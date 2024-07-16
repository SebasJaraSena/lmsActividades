<?php
session_start();

// Verificar si el usuario está autenticado
if (isset($_SESSION['user'])){

    // Almacenar datos en variables, uno de la sesión, otro de la URL
    $user = $_SESSION['user'];
    $curso = base64_decode($_GET['id_ficha']);
    $encoded_curso = $_GET['id_ficha'];
    $id_competencia = base64_decode($_GET['id_competencia']);
    $encoded_competencia = ($_GET['id_competencia']);
    $rol_user = $user->tipo_user;
    $user_id = $user->userid;
    
    //llamada header
    include '../header.php';
    // llamar conexion a bases de datos
    require_once '../config/sofia_config.php';

    echo 'entrando a data result';

}
    ?>