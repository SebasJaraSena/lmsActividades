<?php
/* Configuración de seguridad para conexión a la Base de Datos Replica Integración */
$password = "12345";
$usuario = "postgres";
$nombreBaseDeDatos = "integracion_replica-v3";
/* Puede ser 127.0.0.1 o el nombre de equipo; o la IP de un servidor remoto */
$rutaServidor = "localhost";
$puerto = 5432;
/* Validación de la conexión */
try {
    /* Validación de la conexión */
    $replica = new PDO("pgsql:host=$rutaServidor;port=$puerto;dbname=$nombreBaseDeDatos", $usuario, $password);
    $replica->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    /* PDO es un controlador que implementa la interfaz de Objetos de Datos de PHP (PDO), permitir el acceso desde PHP a bases de datos de PostgreSQL */
} catch (Exception $e) {
    /* Si la conexión falla, se muestra el error */
    echo "Ocurrió un error con la base de datos: " . $e->getMessage();
}
