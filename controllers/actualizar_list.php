<?php
session_start();
require '../config/sofia_config.php';

// Obtener el usuario autenticado
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $inst_califica = $user->userid;

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

    // Verificar si se recibió una solicitud POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validar los datos de entrada
        $selectedUsers = isset($_POST['selected_users']) ? explode(',', $_POST['selected_users']) : [];
        $selectedResults = isset($_POST['selected_results']) ? explode(',', $_POST['selected_results']) : [];

        if (empty($selectedUsers) || empty($selectedResults)) {
            echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
            exit;
        }
        // Iterar sobre los usuarios seleccionados
        for ($i = 0; $i < count($selectedUsers); $i++) {
            $usuario = $selectedUsers[$i];
            $resultado = base64_decode($selectedResults[$i]);

            // Preparar la consulta SQL para actualizar los resultados
            $sql = "UPDATE \"INTEGRACION\".\"TABLA_NOTASXRA_CC\"  SET \"ADR_EVALUACION_RESULTADO\" = :resultado, \"NIS_FUN_EVALUO\" = :inst_califica, \"FECHA_ACTUALIZACION\" = CURRENT_TIMESTAMP, \"ESTADO_SINCRONIZACION\" = 2 WHERE \"ADR_ID\" = :id_rea";
            $stmt = $replica->prepare($sql);

            // Ejecutar la consulta SQL con parámetros
            try {
                $stmt->execute([
                    'resultado' => $resultado,
                    'inst_califica' => $inst_califica,
                    'id_rea' => $usuario
                ]);
            } catch (Exception $e) {
                // Registra el error en un log
                error_log($e->getMessage());
                // Muestra un mensaje de error al usuario
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar datos']);
                log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
                exit;
            }
        }
        // Retornar una respuesta adecuada
        echo json_encode(['status' => 'success', 'message' => 'Datos actualizados correctamente']);
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
