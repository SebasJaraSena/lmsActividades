<?php
// Iniciar sesión si no está iniciada (puedes implementar tu propio sistema de sesión)
session_start();
   
// Verificar si el usuario está autenticado
if (isset($_SESSION['user'])) {
// Se  almacena los datos que son obtenidos por medio de un arreglo
    $user = $_SESSION['user'];
    $id_user = $user->id;
    $username = $user->username;
    $curso = $_GET['id_curso'];

// llamada header
    include 'header.php';
// llamar conexion a bases de datos
    require_once 'config/db_config.php';
    ?>

<main>

    <
 <h5 class="p-2 text-center bg-primary text-white">Centro de calificaciones</h5>

    <div class="container-fluid px-4">

        <div class="container-fluid inline-flex">
            <img src="public/assets/img/icno-de-regresar.svg" alt="Ícono de regresar" id="back-button">
            <p>Regresar</p>
        </div>
        <!-- <div class="container-icono-con-texto d-flex">
        <button class="icono-con-texto" onclick="miFuncion()">
            <img src="public/assets/img/resultados.svg" alt="Ícono de resultados" id="icono-resultados">
            <p>Resultados</p>
        </button>
        <button class="icono-con-texto" onclick="miFuncion()">
            <img src="public/assets/img/blogs.svg" alt="Ícono de blogs" id="icono-blogs">
            <p>Blogs</p>
        </button>
        <button class="icono-con-texto" onclick="miFuncion()">
            <img src="public/assets/img/evaluaciones.svg" alt="Ícono de evaluación" id="icono-evaluacion">
            <p>Evaluaciones</p>
        </button>
        <button class="icono-con-texto" onclick="miFuncion()">
            <img src="public/assets/img/foros.svg" alt="Ícono de foros" id="icono-foros">
            <p>Foros</p>
        </button>
        <button class="icono-con-texto" onclick="miFuncion()">
            <img src="public/assets/img/evidencias.svg" alt="Ícono de evidencias" id="icono-evidencias">
            <p>Evidencias</p>
        </button>
        <button class="icono-con-texto" onclick="miFuncion()">
            <img src="public/assets/img/wikis.svg" alt="Ícono de wikis" id="icono-wikis">
            <p>Wikis</p>
        </button>
    </div>  -->

        <ol class="breadcrumb mb-4">

            <li class="breadcrumb-item active">Bienvenido
                <?php echo $user->firstname . ' ' . $user->lastname; // Se accede al arreglo y se imprime el dato requerido, en este caso hacemos el llamado del campo apellido  ?>
            </li>

        </ol>


        <div class="card mb-4">
            <div class="card-body">
                <?php
                    $rol_user = $user->shortname;
                        if ($rol_user == 'editingteacher' && $id_user != 2) {?>
                <form class="col" method="POST" name="edit_id" id="edit_id">
                    <?php
                                // Consulta para unificacion de tablas y muestra de usuarios
                                $sentencia = $conn->query("SELECT distinct u.id, u.firstname, u.lastname, u.email,e.courseid, mc.fullname, r.shortname, mc.id
                                                                FROM mdl_user u
                                                                JOIN mdl_user_enrolments ue ON ue.userid = u.id
                                                                JOIN mdl_enrol e ON e.id = ue.enrolid
                                                                JOIN mdl_role_assignments ra ON ra.userid = u.id
                                                                JOIN mdl_course mc ON mc.id = e.courseid
                                                                JOIN mdl_role r ON r.id = ra.roleid
                                                                WHERE r.shortname = 'editingteacher' AND u.id = $id_user AND e.courseid = $curso
                                                                ORDER BY u.lastname, u.firstname");
                                            $courses = $sentencia->fetchAll(PDO::FETCH_OBJ);

                                            // Recorrido de los datos obtenidos
                                            foreach ($courses as $course) {
                                                $name_course = $course->fullname;
                                                $id_course = $course->id;
                                                $firstname = $course->firstname;
                                                $lastname = $course->lastname;
                                                $id_curso = $course->courseid;

                                                ?>
                    <div class="card-group" style="margin: 10px;">
                        <div class="card" id="index-card">
                            <div class="card-body">

                                <h5 class="card-title"> <?=$name_course;?></h5>
                                <br>
                                <p class="card-text"> <input type="hidden" name="id_curso" value="<?= $id_curso; ?>">
                                    <?='ID CURSO: '.$id_course;?></p>
                                <p class="card-text"><?='NOMBRE INSTRUCTOR: '.$firstname . ' ' . $lastname;?> </p>
                            </div>
                        </div>
                    </div>
                    <?php }?>
                </form>

                <?php }
                            if ($rol_user == 'student'  && $id_user != 2) {?>
                <form class="col" method="POST" name="edit_id" id="edit_id">
                    <?php
                                // Consulta para unificacion de tablas y muestra de usuarios
                                $sentencia = $conn->query("SELECT distinct u.id, u.firstname, u.lastname, u.email,e.courseid, mc.fullname, r.shortname, mc.id
                                                                FROM mdl_user u
                                                                JOIN mdl_user_enrolments ue ON ue.userid = u.id
                                                                JOIN mdl_enrol e ON e.id = ue.enrolid
                                                                JOIN mdl_role_assignments ra ON ra.userid = u.id
                                                                JOIN mdl_course mc ON mc.id = e.courseid
                                                                JOIN mdl_role r ON r.id = ra.roleid
                                                                WHERE r.shortname = 'student' AND u.id = $id_user AND e.courseid = $curso
                                                                ORDER BY u.lastname, u.firstname");
                                            $courses = $sentencia->fetchAll(PDO::FETCH_OBJ);

                                            // Recorrido de los datos obtenidos
                                            foreach ($courses as $course) {
                                                $name_course = $course->fullname;
                                                $id_course = $course->id;
                                                $firstname = $course->firstname;
                                                $lastname = $course->lastname;
                                                $id_curso = $course->courseid;

                                                ?>
                    <div class="card-group" style="margin: 10px;">
                        <div class="card" id="index-card">
                            <div class="card-body">
                                <h5 class="card-title">Curso en proceso</h5>
                                <h5 class="card-title"> <?='NOMBRE DEL CURSO: '.$name_course;?></h5>
                                <p class="card-text"><?='NOMBRE APRENDIZ: '.$firstname . ' ' . $lastname;?></p>
                                <button type="button" style="display:flexbox;" class="btn btn-success"
                                    onclick="redirectToCompetencias('<?= $id_curso; ?>')">Ver Competencias</button>
                            </div>
                        </div>
                    </div>
                    <?php }?>
                </form>
                <?php }?>
            </div>
        </div>
    </div>

</main>
<script>
function redirectToCompetencias(idCurso) {
    window.location.href = `competencias.php?id_curso=${idCurso}`;
}
</script>

<!-- llamada Footer -->
<?php include 'footer.php';
} else {
    // Si no hay una sesión iniciada o datos del usuario, redirigir al usuario a otra página
    header("Location: http://localhost/zajuna/");
    exit();
}
?>