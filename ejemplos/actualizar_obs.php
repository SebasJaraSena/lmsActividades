<?php 
require_once 'db_config.php'; // llamar conexion base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET["id_user"])) {
    // Capturar los datos enviados por el formulario
    $id_user=$_GET["id"];
    $observacion = $_POST['edit_observacion'];

    if ($observacion) // si variable observacion existe
    {

        // Construir la consulta de actualización
        $update_query = $conn->query("UPDATE mdl_block_califica SET descripcionra = '$observacion' WHERE userid = '$id_user' AND enrolid=13" ); // COMPARAR -> ID DE USUARIO -> ID DE ENROLAMIENTO CORRESPONDAN AL CURSO
        
        $mensaje = "EL RESULTADO DE APRENDIZAJE HA SIDO CARGADO CON EXITO";
        echo "<script type='text/javascript'>
        alert('$mensaje');
        window.location.href = 'index.php';
        </script>";
    }
    else // alert para indicar NO actualización 
    {
        $mensaje = "ERROR AL ACTUALIZAR LA DATA";
        echo "<script type='text/javascript'>
        alert('$mensaje');
        window.location.href = 'index.php';
        </script>";

        }

    
}
?>













