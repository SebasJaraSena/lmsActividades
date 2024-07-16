<?php
session_start();
// Verificar si el usuario está autenticado
if (isset($_SESSION['user'])) {
    // Se  almacena los datos que son obtenidos por medio de un arreglo
    $user = $_SESSION['user'];
    $user_nis = $user->user_nis;
// Conexion a la base de datos Instegracion_replica
require '../config/sofia_config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
   // Verificar si se recibieron los datos esperados
   if (isset($_POST["selected_users"]) && isset($_POST["selected_results"])) {

       // Obtener los usuarios seleccionados y los resultados
       $selectedUsers = explode(',', $_POST["selected_users"]);
       $selectedResults = explode(',', $_POST["selected_results"]);
       $fun_evaluo = $user_nis;
       // usuarios seleccionados
       for ($i = 0; $i < count($selectedUsers); $i++) {
           $usuario = $selectedUsers[$i];
           $resultado = $selectedResults[$i];
           
        // echo "Usuario: " . $usuario . ", Resultado: " . $resultado . ", instructor: " . $fun_evaluo . "<br>";
           
           $sql = "UPDATE \"INTEGRACION\".\"HISTORICO_CC\"  SET \"ADR_EVALUACION_COMPETENCIA\" = :resultado, \"NIS_FUN_EVALUO\" = $fun_evaluo, \"FECHA_ACTUALIZACION\" = CURRENT_TIMESTAMP, \"ESTADO_INI\" = 2 WHERE \"USR_NUM_DOC\" = :id_rea";

           
           $stmt = $replica->prepare($sql);
           $stmt->execute(['resultado' => $resultado, 'id_rea' => $usuario]);
           // Verificar si la actualización fue exitosa

           if ($stmt->rowCount() > 0) {
               echo "Actualización exitosa para el usuario: " . $usuario . ", Resultado: " . $resultado . ", instructor: " . $fun_evaluo . "<br>";
           } else {
               echo "No se pudo actualizar el usuario: " . $usuario . "<br>";
           }

          
       }

   } else {
       echo "No se recibieron los datos.";
   }
} else {
   echo "Acceso denegado.";
}
}

?>



