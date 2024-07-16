<?php
include "ruta_error.php";

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

function obtenerResultados($curso, $id_competencia, $id_rea)
{
    global $conn, $errorPage; // $conn es la conexión a la base de datos
    try {
        // Iniciar una transacción
        $conn->beginTransaction();
        /* Consulta para unificacion de tablas y muestra de usuarios
         Evitar inyección SQL utilizando consultas preparadas */
        $sentencia = $conn->prepare('SELECT obtenerResultadosRea(:curso, :id_competencia, :id_rea)');
        $sentencia->bindParam(':curso', $curso, PDO::PARAM_STR);
        $sentencia->bindParam(':id_competencia', $id_competencia, PDO::PARAM_STR);
        $sentencia->bindParam(':id_rea', $id_rea, PDO::PARAM_STR);
        $sentencia->execute();
        // Consultar la vista creada por la función
        $resul_query = 'SELECT * FROM vista_result';
        $sentencia = $conn->prepare($resul_query);
        $sentencia->execute();
        // Obtener los resultados
        $courses = $sentencia->fetchAll(PDO::FETCH_OBJ);
        // Confirmar la transacción
        $conn->commit();
        return $courses;
    } catch (Exception $e) {
        // En caso de error, revertir la transacción
        $conn->rollBack();
        echo "Error al ejecutar la consulta de resultados de aprendizaje: " . $e->getMessage() . "\n";
        log_error($conn, get_class($e), $e->getCode(), $e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
        exit();
    }
}
