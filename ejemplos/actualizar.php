<?php 
require_once 'db_config.php'; // llamar conexion base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET["id_user"])) {
    // Capturar los datos enviados por el formulario
    $id_user=$_GET["id"];
    $calificacion = $_POST['edit_calificacion'];

    if (strlen($calificacion) == 1) // si el campo de resultado aprendizaje es mas de 1 digito, se mostrará error y no se actualizará la tabla
    {

        // Construir la consulta de actualización
        $update_query = $conn->query("UPDATE mdl_block_califica SET reaprendizaje = '$calificacion' WHERE userid = '$id_user' AND enrolid=13");

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



