<?php
session_start();
require '../config/sofia_config.php';

if (isset($_SESSION['user'])) {
    // Almacenar los datos del usuario en una variable
    $user = $_SESSION['user'];
    $user_nis = $user->userid;

    function log_error($replica, $type, $code, $description)
    {
        // Preparar la declaración SQL para insertar el registro de error
        $query = "INSERT INTO \"LOG\".error_log (error_type, error_code, error_description, error_date) VALUES (:type, :code, :description, NOW())";
        $stmt = $replica->prepare($query);
        // Ejecutar la declaración con los parámetros
        try {
            $stmt->execute([
                ':type' => $type,
                ':code' => $code,
                ':description' => $description
            ]);
        } catch (PDOException $e) {
            echo "Error al insertar el registro: " . $e->getMessage();
        }
    }

    function validate_input($data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                return false;
            }
        }
        return true;
    }

    // Asegúrate de que solo aceptas solicitudes POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtén el cuerpo de la solicitud JSON
        $inputJSON = file_get_contents('php://input');
        // Decodifica el JSON en un array asociativo
        $data = json_decode($inputJSON, true);

        $response = array();
        if (is_array($data)) {
            try {
                $dataCount = count($data);
                for ($index = 0; $index < $dataCount; $index++) {
                    $item = $data[$index];
                    $aprendiz = $item['aprendiz'] ?? '';
                    $calificacion = $item['calificacion'] ?? '';
                    $codigo = $item['codigo'] ?? '';

                    // Validar que los campos no estén vacíos
                    if (!validate_input(['aprendiz' => $aprendiz, 'calificacion' => $calificacion, 'codigo' => $codigo])) {
                        throw new Exception("Todos los campos son obligatorios y no deben estar vacíos.");
                    }

                    // lógica
                    $sql = "UPDATE \"INTEGRACION\".\"TABLA_NOTASXRA_CC\" 
                        SET \"ADR_EVALUACION_RESULTADO\" = :resultado,
                            \"NOTA_ENVIADA_SOFIA\" = :resultado, 
                            \"NIS_FUN_EVALUO\" = :user_nis, 
                            \"FECHA_ENVIO_SOFIA\" = CURRENT_TIMESTAMP, 
                            \"ESTADO_SINCRONIZACION\" = 3 
                        WHERE \"ADR_ID\" = :id_rea";
                    $stmt = $replica->prepare($sql);
                    $stmt->execute(['resultado' => $calificacion, 'user_nis' => $user_nis, 'id_rea' => $codigo]);
                }
                $response['success'] = true;
                $response['message'] = 'Datos recibidos y procesados con éxito.';
            } catch (PDOException $e) {
                // Manejo de errores de base de datos
                $response['success'] = false;
                $response['message'] = 'Error en la base de datos: ' . $e->getMessage();

                echo "Error en la base de datos envio sofia controller" . $e->getMessage() . "\n";
                log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
            } catch (Exception $e) {
                // Manejo de otros errores
                $response['success'] = false;
                $response['message'] = 'Ocurrió un error: ' . $e->getMessage();
                echo "Error Sofia_controller" . $e->getMessage() . "\n";
                log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'Formato de datos inválido.';
        }

        // Devuelve una respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        // Si no es una solicitud POST, devuelve un error
        header('HTTP/1.1 405 Method Not Allowed');
        echo json_encode(array('success' => false, 'message' => 'Método no permitido.'));
    }
} else {
    // Si no hay una sesión de usuario, devuelve un error
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(array('success' => false, 'message' => 'No autorizado.'));
}
