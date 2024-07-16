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

function obtenerResultadosxCompetencia($curso, $id_competencia)
{
    global $conn, $errorPage; // $replica es la conexión a la base de datos

    try {
        // Iniciar una transacción
        $conn->beginTransaction();
        // Llamar a la función PL/pgSQL con los parámetros
        $query = $conn->prepare('SELECT obtenerResultados(:curso, :competencia)');
        $query->bindParam(':curso', $curso, PDO::PARAM_STR); // Se usa PARAM_STR porque los parámetros se pasan como VARCHAR
        $query->bindParam(':competencia', $id_competencia, PDO::PARAM_STR); // Se usa PARAM_STR porque los parámetros se pasan como VARCHAR
        $query->execute();
        // Consultar la vista creada por la función
        $resul_query = 'SELECT * FROM vista_result';
        $query = $conn->prepare($resul_query);
        $query->execute();
        // Obtener los resultados
        $resultados = $query->fetchAll(PDO::FETCH_OBJ);
        // Confirmar la transacción
        $conn->commit();
        return $resultados;
    } catch (Exception $e) {
        // En caso de error, revertir la transacción
        $conn->rollBack();
        echo "Error al ejecutar la consulta de resultados de aprendizaje: " . $e->getMessage() . "\n";
        log_error($conn, get_class($e), $e->getCode(), $e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
        exit();
    }
}

function obtenerResultadosxCompetenciaxApr($curso, $id_competencia, $user_id)
{
    global $conn, $errorPage; // $replica es la conexión a la base de datos

    try {
        // Iniciar una transacción
        $conn->beginTransaction();
        // Llamar a la función PL/pgSQL con los parámetros
        $query = $conn->prepare('SELECT obtenerResultadosAprendiz(:curso, :competencia, :id)');
        $query->bindParam(':curso', $curso, PDO::PARAM_STR); // Se usa PARAM_STR porque los parámetros se pasan como VARCHAR
        $query->bindParam(':competencia', $id_competencia, PDO::PARAM_STR); // Se usa PARAM_STR porque los parámetros se pasan como VARCHAR
        $query->bindParam(':id', $user_id, PDO::PARAM_INT);
        $query->execute();
        // Consultar la vista creada por la función
        $resul_query = 'SELECT * FROM vista_result';
        $query = $conn->prepare($resul_query);
        $query->execute();
        // Obtener los resultados
        $resultadosApren = $query->fetchAll(PDO::FETCH_OBJ);
        // Confirmar la transacción
        $conn->commit();
        return $resultadosApren;
    } catch (Exception $e) {
        // En caso de error, revertir la transacción
        $conn->rollBack();
        echo "Error al ejecutar la consulta de resultados de aprendizaje: " . $e->getMessage() . "\n";
        log_error($conn, get_class($e), $e->getCode(), $e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
        exit();
    }
}

