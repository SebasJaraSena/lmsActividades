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

function obtenerCompetenciasPorCurso($curso)
{
    global $replica, $errorPage; // $replica es la conexión a la base de datos,$errorPage ruta redirecciòn vista error
    try {
        // Evitar inyección SQL utilizando consultas preparadas
        $sentencia = $replica->prepare('SELECT "INTEGRACION".obtenerCompetencias(:curso)');
        $sentencia->bindParam(':curso', $curso, PDO::PARAM_STR);
        $sentencia->execute();
        $resul_query = 'SELECT * FROM vista_com';
        $sentencia = $replica->prepare($resul_query);
        $sentencia->execute();
        $courses = $sentencia->fetchAll(PDO::FETCH_OBJ);
        return $courses;
    } catch (PDOException $e) {
        echo "Error al obtener las competencias por ficha : " . $e->getMessage() . "\n";
        log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
        exit();
    }
}
