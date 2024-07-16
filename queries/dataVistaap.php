    <?php
require_once '../config/sofia_config.php'; // Asegúrar de tener la conexion de la base de datos

header('Content-Type: application/json');

// Aquí va tu consulta SQL, ajusta según necesita
$sentencia = $replica->query ("SELECT * from \"INTEGRACION\".\"vista_vistaap\" where \"FIC_ID\"  = 2966656 and \"CMP_ID\" = 37714 ORDER by \"USR_NOMBRE\" asc;"); // Asegúrate de completar la consulta según tus necesidades

try {
    $results = $sentencia->fetchAll(PDO::FETCH_OBJ);
    // var_dump($results);
    
    // Con el Json_encode convierte la consulta en json para luego ser recorrido en el pluggin AG-GRID
    echo json_encode($results);
   
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}


?>