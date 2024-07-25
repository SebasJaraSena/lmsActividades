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
 
// FUNCION PARA PERMITIR EL INGRESO DEL USUARIO A UN CURSO EN CUESTION
function ingreso($id_curso)
{
    global $replica, $errorPage, $conn;
    try {
        // Llamada a la función para obtener los parámetros de redirección a letras de calificación de la ficha en cuestión
        $ingreso = $conn->prepare("SELECT obtenerIngreso(:curso)");
        $ingreso->bindParam(':curso', $id_curso, PDO::PARAM_INT);
        $ingreso->execute();
        $ingre_query = "SELECT * FROM vista_ing";
        $ingreso = $conn->prepare($ingre_query);
        $ingreso->execute();
        $ingre = $ingreso->fetchAll(PDO::FETCH_ASSOC);
        return $ingre;
    } catch (PDOException $e) {
        echo "Error al ejecutar la consulta para obtener la escala de calificación : " . $e->getMessage() . "\n";
        log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
        exit();
    }
}

// FUNCION PARA OBTENER EL NOMBRE DEL CURSO EN CUESTION 
function nombre_ficha($id_curso)
{
    global $replica, $errorPage, $conn;
    try {
        $query = $conn->prepare("SELECT fullname FROM mdl_course WHERE id = :curso");
        $query->execute(['curso' => $id_curso]);
        $name = $query->fetchAll(PDO::FETCH_OBJ);
        return $name;
    } catch (PDOException $e) {
        echo "Error al obtener el nombre de la ficha: " . $e->getMessage() . "\n";
        log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
        exit();
    }
}

try {
    // Llamada a la función para obtener los parámetros de redirección a letras de calificación de la ficha en cuestión
    $escala = $conn->prepare("SELECT obtenerEscala(:curso)");
    $escala->bindParam(':curso', $id_curso, PDO::PARAM_STR);
    $escala->execute();
    $esca_query = "SELECT * FROM vista_esca";
    $escala = $conn->prepare($esca_query);
    $escala->execute();
    $esca = $escala->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo "Error al ejecutar la consulta para obtener la escala de calificación : " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
    echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
    exit();
}

try {
    // LLAMADA A LA FUNCION PARA OBTENER LAS CATEGORIAS DE EVALUACION DE ACTIVIDADES REALIZADAS EN ZAJUNA
    $titulosCat = $conn->prepare("SELECT obtenerCategorias(:curso)");
    $titulosCat->bindParam(':curso', $id_curso, PDO::PARAM_INT);
    $titulosCat->execute();
    $cat_query = "SELECT * FROM vista_cat ORDER BY fullname ASC";
    $titulosCat = $conn->prepare($cat_query);
    $titulosCat->execute();
    $categorias = $titulosCat->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo "Error al ejecutar la consulta para obtener evaluaciones de zajuna : " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
    echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
    exit();
}

try {
    // LLAMADA A LA FUNCION PARA OBTENER LAS ACTIVIDADES REALIZADAS EN ZAJUNA
    $titulos = $conn->prepare("SELECT obtenerActividades(:curso)");
    $titulos->bindParam(':curso', $id_curso, PDO::PARAM_INT);
    $titulos->execute();
    $acti_query = "SELECT * FROM vista_actividades ORDER BY itemname ASC";
    $titulos = $conn->prepare($acti_query);
    $titulos->execute();
    $actividades = $titulos->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo "Error al ejecutar la consulta para obtener evaluaciones de zajuna : " . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
    echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
    exit();
}

try {
    // LLAMADA A LA FUNCION PARA OBTENER LOS USUARIOS MATRICULADOS EN LA FICHA EN CUESTION
    $user_query = $conn->prepare("SELECT obtenerUsuarios(:curso)");
    $user_query->bindParam(':curso', $id_curso, PDO::PARAM_STR);
    $user_query->execute();
    $apren_query = "SELECT * FROM vista_usuarios ORDER BY firstname ASC";
    $user_query = $conn->prepare($apren_query);
    $user_query->execute();
    $users = $user_query->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo 'Error al obtener los usuarios: ' . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
    echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
    exit();
}

try {
    // LLAMADA A LA FUNCION PARA OBTENER EL APRENDIZ MATRICULADO EN LA FICHA EN CUESTION
    $user_query = $conn->prepare("SELECT obtenerUsuariosApren(:curso, :user)");
    $user_query->bindParam(':curso', $id_curso, PDO::PARAM_STR);
    $user_query->bindParam(':user', $user_id, PDO::PARAM_INT);
    $user_query->execute();
    $apren_query = "SELECT * FROM vista_usuarios_apren ORDER BY firstname ASC";
    $user_query = $conn->prepare($apren_query);
    $user_query->execute();
    $userApr = $user_query->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo 'Error al obtener el aprendiz matriculado: ' . $e->getMessage() . "\n";
    log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
    echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
    exit();
}

// LLAMADA A LA FUNCION PARA OBTENER LAS NOTAS DEL APRENDIZ EN MULTIPLES ACTIIDADES
function obtenerNotas($id_user, $acti)
{
    $errorPage = 'http://localhost/lmsActividades/error_acti.php';
    global $replica, $conn;
    try {
        $q_gradess = $conn->prepare("SELECT * FROM obtenerNotasActi(ARRAY[:acti]::BIGINT[], ARRAY[:id_user]::BIGINT[])");
        $q_gradess->bindParam(':acti', $acti, PDO::PARAM_INT);
        $q_gradess->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $q_gradess->execute();
        $q_grades = $q_gradess->fetchAll(PDO::FETCH_ASSOC);
        return $q_grades;
    } catch (PDOException $e) {
        echo 'Error al obtener las notas de las actividades: ' . $e->getMessage() . "\n";
        log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
        exit();
        return [];
    }
}

// LLAMADA A LA FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCION A REVISION DE ACTIVIDADES
function obtenerParametros($id_user, $id_curso, $acti)
{
    $errorPage = 'http://localhost/lmsActividades/error_acti.php';
    global $replica, $conn;
    try {
        $stmt = $conn->prepare("SELECT * FROM obtenerParametros(ARRAY[:id_user]::BIGINT[], :curso, ARRAY[:acti]::BIGINT[])");
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->bindParam(':curso', $id_curso, PDO::PARAM_STR);
        $stmt->bindParam(':acti', $acti, PDO::PARAM_INT);
        $stmt->execute();
        $params = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $params;
    } catch (PDOException $e) {
        echo 'Error al obtener los parámetros de redirección: ' . $e->getMessage() . "\n";
        log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
        exit();
        return [];
    }
}

// LLAMADA A LA FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCION A REVISION DE APRENDICES PENDIENTES
function obtenerParametrosPendientes($acti)
{
    $errorPage = 'http://localhost/lmsActividades/error_acti.php';
    global $conn, $replica;
    try {
        $stmt = $conn->prepare("SELECT * FROM obtenerParametrosPend(ARRAY[:acti]::BIGINT[])");
        $stmt->bindParam(':acti', $acti, PDO::PARAM_STR);
        $stmt->execute();
        $paramsPen = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $paramsPen;
    } catch (PDOException $e) {
        echo 'Error al obtener los parámetros de redirección de aprendices pendientes: ' . $e->getMessage() . "\n";
        log_error($replica, get_class($e), $e->getCode(), $e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=$errorPage'>";
        exit();
        return [];
    }
}
