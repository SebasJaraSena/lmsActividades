<?php
// llamar al controlador de sesion
require_once '../controllers/session_controller.php';

session_start();
// Verificar si el usuario está autenticado
if (isset($_SESSION['user']) && checkSessionTimeout()) {

    // Se  almacena los datos que son obtenidos  del usuario logueado por medio de un arreglo
    $user = $_SESSION['user'];
    $tipo_user = $user->tipo_user;
    $curso = base64_decode($_GET['idnumber']);
    // llamada header
    include '../header.php';
    // llamar conexion a bases de datos
    require_once '../config/db_config.php';
    // llamar conexion a bases de datos
    require_once '../config/sofia_config.php';
    // llamar al controlador
    require_once '../controllers/comp_controller.php';
?>
    <main>
        <h5 class="p-2 text-center bg-primary text-white">Centro de calificaciones Competencias</h5>
        <div class="container-fluid px-4" style="display: flex; justify-content: center;">
            <?php
            mostrar_historial();
            ?>
        </div>

        <div class="container-fluid px-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="../public/assets/img/icno-de-regresar.svg" alt="Ícono de regresar" id="back-button-zajuna">
                        <p>Regresar a ZAJUNA</p>
                    </div>
                    <div class="col-sm-8 d-flex justify-content-center">
                        <h3 class="my-4"><img class="ml-2" src="../public/assets/img/documento.svg" alt=""> Competencias N° Ficha:&nbsp; <span class="my-4"  id="color-titulo"><?php echo $curso; ?></span></h3>
                    </div>
                </div>
            </div>
            <!-- Boton de regresar a la pagina anterior (ZAJUNA) -->

            <div class="card m-4">
                <ol class="breadcrumb m-2">
                    <!-- Se accede al arreglo y se imprime el dato requerido, en este caso hacemos el llamado del campo apellido  -->
                    <li class="m-2"><strong>Bienvenido</strong> <?php echo $user->firstname . ' ' . $user->lastname; ?></li>
                </ol>
                <div class="card-body">
                    <?php
                    // Recuperar los cursos de los parámetros GET de la URL 
                    $courses = obtenerCompetenciasPorCurso($curso);
                    // Imprimir los datos de los cursos
                    foreach ($courses as $course) {
                        // Puedes acceder a cada atributo del curso según sea necesario
                        $codigo_ficha = $course->FIC_ID;
                        $encoded_ficha = base64_encode($codigo_ficha); // FIC ID encriptado
                        $id_competencia = $course->CMP_ID;
                        $encoded_competencia = base64_encode($id_competencia); // CMP ID encriptado
                        $firstname = $course->CMP_NOMBRE;
                    ?>
                        <div class="card-group" style="margin: 10px;">
                            <div class="card" id="index-card">
                                <div class="card-body">
                                    <h5 class="card-title m-2">ID competencia <?= $id_competencia; ?></h5>
                                    <p class="card-text m-4"><?= 'NOMBRE COMPETENCIA: ' . $firstname; ?> </p>
                                    <button type="button" class="btn btn-success" onclick="redirectComToResultados('<?= $encoded_ficha; ?>', '<?= $encoded_competencia; ?>')">
                                        <?php if ($tipo_user == 3) : ?>
                                            Resultados de Aprendizaje
                                        <?php else : ?>
                                            Resultados de Aprendizaje
                                        <?php endif; ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>
    <!-- llamada Footer -->
<?php include '../footer.php';
} else {
    $mensaje = "Ha caducado su sesión. Por favor ingrese nuevamente ";
    echo "<script>
    window.location.href = 'http://localhost/lms/error/error.php';
    </script>";
}
?>