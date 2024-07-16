<?php
require '../config/sofia_config.php';

function log_error($replica, $type, $code, $description) {
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

function test_exceptions($type) {
    switch ($type) {
        case 1:
            throw new Exception("Excepción General", 1);
        case 2:
            throw new TypeError("Error de Tipo", 2);
        case 3:
            throw new InvalidArgumentException("Error de Argumento Inválido", 3);
        case 4:
            throw new LengthException("Error de Longitud Inválida", 4);
        case 5:
            throw new RangeException("Error de Rango", 5);
        case 6:
            throw new OutOfRangeException("Error de Fuera de Rango", 6);
        case 7:
            throw new DomainException("Error de Dominio", 7);
        case 8:
            throw new LogicException("Error de Lógica", 8);
        case 9:
            throw new RuntimeException("Error de Tiempo de Ejecución", 9);
        case 10:
            throw new DivisionByZeroError("Error de División por Cero", 10);
        default:
            throw new Exception("Tipo de error no manejado", 0);
    }
}


try {
    // Cambia el valor para probar diferentes excepciones
  
    test_exceptions(9);
} catch (TypeError $e) {
    echo "Capturado TypeError: " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
} catch (InvalidArgumentException $e) {
    echo "Capturado InvalidArgumentException: " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
} catch (LengthException $e) {
    echo "Capturado LengthException: " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
} catch (RangeException $e) {
    echo "Capturado RangeException: " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
} catch (OutOfRangeException $e) {
    echo "Capturado OutOfRangeException: " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
} catch (DomainException $e) {
    echo "Capturado DomainException: " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
} catch (LogicException $e) {
    echo "Capturado LogicException: " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
} catch (RuntimeException $e) {
    echo "Capturado RuntimeException: " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
} catch (DivisionByZeroError $e) {
    echo "Capturado DivisionByZeroError: " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
} catch (Exception $e) {
    echo "Capturado Exception: " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
}


