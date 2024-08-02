<?php
// LLAMAR AL CONTROLADOR DE LA SESIÓN
require_once '../../controllers/session_controller.php';

session_start();
// VERIFICAR SI EL USUARIO A INICIADO SESIÓN
if (isset($_SESSION['user']) && checkSessionTimeout()) {
    // Se  almacena los datos que son obtenidos  del usuario logueado por medio de un arreglo
    $user = $_SESSION['user'];
    $user_id = $user->userid;
    $rol_user = $user->tipo_user;

    $id_curso = $_GET['id'];
    $id_rea = $_GET['cat'];

    // LLAMAR AL HEADER 
    require_once '../../header.php';
    // LLAMAR A LA BASE DE DATOS ZAJUNA 
    require_once '../../config/db_config.php';
    // LLAMAR A LA BASE DE DATOS INTEGRACION 
    require '../../config/sofia_config.php';
    // LLAMAR AL CONTROLADOR DE CONSULTAS 
    require_once '../../controllers/actiap_controller.php';

    //SE OBTIENE ID DE REDIRECCION A LETRAS DE CALIFICACION
    foreach ($esca as $esc) {
        $id_esca = $esc->id;
    }

    //SE OBTIENE NOMBRE DEL CURSO EN CUESTION
    $name = nombre_ficha($id_curso);
    foreach ($name as $nam) {
        $nombre_ficha = ucfirst(mb_strtolower(($nam->fullname)));
        $ficha = $nam->idnumber;
    }

    //SE OBTIENE EL ID DE LA PERSONA INGRESADA Y QUE PERTENEZCA AL CURSO
    $ingre = ingreso($id_curso);
    //VERIFICA SI EL USUARIO PERTENECE AL CURSO EN CUESTION
    $encontrado = false;
    foreach ($ingre as $ingr) {
        if ($ingr['id'] == $user_id) {
            $encontrado = true;
            break;
        }
    }

    // SI EL USUARIO PERTENECE AL CURSO PUEDE VISUALIZAR LA VISTA
    if ($encontrado) {
?>
        <main>
            <!--ESTILO PARA LA VENTANA EMERGENTE DE CARGANDO... -->
            <style>
                .loader {
                    width: fit-content;
                    font-weight: bold;
                    font-family: sans-serif;
                    font-size: 30px;
                    padding-bottom: 8px;
                    background: linear-gradient(currentColor 0 0) 0 100%/0% 3px no-repeat;
                    animation: l2 2s linear infinite;
                }

                .loader:before {
                    content: "Cargando..."
                }

                @keyframes l2 {
                    to {
                        background-size: 100% 3px
                    }
                }
            </style>

            <div class="history-container my-2 " style="display: flex; justify-content: center;">
                <?php
                mostrar_historial();
                ?>
            </div>
            <div class="container-fluid px-4">
                <div class="card p-3 p-md-5">
                    <div class="container-fluid container-hearder">
                        <div class="row">
                            <div class="col-sm-2">
                                <!-- boton regresar  -->
                                <h6>
                                    <img src="../../public/assets/img/icno-de-regresar.svg" id="back-button" alt="Ícono de regresar" style="margin-right: 5px;" onclick="redirectToActividad('<?= $id_curso; ?>')">
                                    <u id="titulo-regresar" onclick="redirectToActividad('<?= $id_curso; ?>')">Regresar a
                                        Actividades Generales</u>
                                </h6>
                            </div>
                            <div class="col-sm-10 d-flex">
                                <h3 style="color: white;" class="my-2 text-start"><img id="titulo-img" src="../../public/assets/img/documento.svg" alt="?">
                                    Ficha: <span id="color-titulo-ficha"><?php echo ($ficha); ?> </span>
                                    Nombre: <span title="<?php echo $nombre_ficha; ?>" id="color-titulo-nombre"><?php echo substr($nombre_ficha, 0, 64) . '...'; ?></span></h3>
                            </div>
                        </div>
                    </div>
                    <!-- Imagen referencia banner inicio de vista centro de calificaciones -->
                    <div class="my-4">
                        <img src="../../public/assets/banners/acti_ap.svg" id="img-banner">
                    </div>

                    <ol class="breadcrumb m-2">
                        <!-- Se accede al arreglo y se imprime el dato requerido, en este caso hacemos el llamado del campo apellido  -->
                        <li class="m-2"><strong>Bienvenido/a</strong> <?php echo $user->firstname . ' ' . $user->lastname; ?>
                        </li>
                    </ol>

                    <div class="card p-3 p-md-5">
                        <div class="d-flex justify-content-between flex-wrap gap-3">
                            <div>
                                <button class="icono-con-texto ml-2" name="id_curso" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    <img src="../../public/assets/img/codigoColor.svg" class="mr-2" alt="Ícono de evaluación" width="52" height="52" id="icono-evaluacion">
                                    <p>Código de colores</p>
                                </button>
                            </div>
                        </div>


                        <div class="modal fade " id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Código de colores</h5>
                                    </div>
                                    <!-- Cuerpo del modal -->
                                    <div class="modal-body">
                                        <hr />
                                        <p>
                                        <p>Este Código de colores esta establecido para la facilidad de lectura de las calificaciones del centro de calificaciones, por favor tenga en cuenta los siguientes codigos de colores:</p>
                                        <table class="table table-hover mt-2">
                                            <thead>
                                                <tr id="vistaap-thead">
                                                    <th class="text-center">Color</th>
                                                    <th class="text-center">Nota</th>
                                                    <th class="text-center">Estado</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><span class="color-box mr-2" style="background-color: #BCE2A8;"></span>Color Verde </td>
                                                    <td>A</td>
                                                    <td>APROBADO</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="color-box mr-2" style="background-color: #DF5C73;"></span>Color Rojo</td>
                                                    <td>D</td>
                                                    <td>NO APROBADO </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="color-box mr-2" style="background-color: #b9b9b9;"></span>Color Amarillo</td>
                                                    <td>X</td>
                                                    <td>PENDIENTE DE REALIZAR</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        </p>
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-modal" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card-body" id="actividades-card">
                            <div>
                                <h5 class="my-2">
                                    Categoria:&nbsp; <span id="color-titulo-categoria"> <?php echo ($id_rea); ?></span>
                                    <span id="color-titulo-categoria"></span>
                                </h5>
                                <?php
                                //INICION SESION ROL INSTRUCTOR (ROL)
                                if ($rol_user == 3) {
                                    $redireccion = "acti_ap.php";
                                ?>
                                    <!--VENTANA QUE INDICA CARGANDO MIENTRAS SE REESTRUCTURAN LOS DATOS DE LA TABLA -->
                                    <div id="spinner-check" class="loader" role="status" style="display: none; margin: 0 auto;">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>

                                    <form id="emailForm" action="../../controllers/enviarEmail.php" method="POST">

                                        <table id="tabla-act-check" class="display" style="width:100%; display: none;">
                                            <thead>
                                                <meta charset="UTF-8">
                                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                                <title>Icono de Información</title>
                                                <link rel="stylesheet" href="styles.css">
                                                <tr id="actividades-thead">
                                                    <th>
                                                        <input type="hidden" name="redireccion" value="<?php echo $redireccion; ?>">
                                                        <input type="hidden" name="id_curso" value="<?php echo $id_curso; ?>">
                                                        <input type="hidden" name="id_rea" value="<?php echo $id_rea; ?>">
                                                        <input type="checkbox" id="select_all">
                                                    </th>
                                                    <th>Documento</th>
                                                    <th>Nombre Completo</th>
                                                    <?php foreach ($actividades as $actividad) : ?>
                                                        <th tittle=<?= $actividad->itemname ?>>
                                                            <div class="text-center"><?= htmlspecialchars($actividad->itemname) ?></div>
                                                        </th>
                                                    <?php endforeach; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($users as $index => $user) : ?>
                                                    <tr>
                                                        <td><input type="checkbox" name="correo[]" class="CheckedAK" value="<?= htmlspecialchars($user->email) ?>" /></td>
                                                        <td id="text-align-document"><?= htmlspecialchars($user->username) ?></td>
                                                        <td id="text-align-name">
                                                            <?= htmlspecialchars($user->firstname . ' ' . $user->lastname) ?>
                                                        </td>
                                                        <?php foreach ($actividades as $actividad) : ?>
                                                            <td>
                                                                <?php
                                                                $params = obtenerParametros($conn, $user->id, $id_curso, $actividad->idacti);
                                                                $param = reset($params);
                                                                $q_grades = obtenerNotas($conn, $user->id, $actividad->idacti);
                                                                if (!empty($q_grades)) {
                                                                    $grade = reset($q_grades)['grade'];
                                                                    $gradeLetter = $grade >= 7.00000 ? 'A' : 'D';
                                                                    $bgColor = $grade >= 7.00000 ? '#BCE2A8' : '#DF5C73';
                                                                    $activityLink = "http://localhost/zajuna/mod/quiz/review.php?attempt={$param['idattemp']}&cmid={$param['id']}";
                                                                } else {
                                                                    $gradeLetter = 'X';
                                                                    $bgColor = '#b9b9b9';
                                                                    $paramsPen = obtenerParametrosPendientes($conn, $actividad->idacti);
                                                                    $paramPen = reset($paramsPen);
                                                                    $activityLink = "http://localhost/zajuna/mod/quiz/grade.php?id={$paramPen['id']}&itemid&itemnumber&gradeid&userid";
                                                                }
                                                                ?>
                                                                <div class="d-flex" style="background-color: <?= $bgColor ?>; padding: 10px; border-radius: 10px;">
                                                                    <div class="col-8 mx-auto">
                                                                        <h6><?= $gradeLetter ?></h6>
                                                                    </div>
                                                                    <div class="action-menu" data-collapse="menu">
                                                                        <div class="dropdown show">
                                                                            <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                                <span class="" aria-hidden="true"></span>
                                                                            </button>
                                                                            <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                                <a class="dropdown-item" href="<?= $activityLink ?>">Análisis de
                                                                                    Actividad</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                            </div>
                            </form>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>

<?php
        // LLAMADA AL FOOTER 
        include '../../footer.php';
    } else {
        echo "<script>
    window.location.href = 'http://localhost/lmsActividades/error/error_ingre.php';
    </script>";
    }
    // SI EL USUARIO TIENE MAS DE 30 MINUTOS DE INACTIVIDAD ENTRARA POR AQUI Y SE REDIRIGUE A LA PAGINA INICIAL DE ZAJUNA 
} else {
    // ALERTA DE SESION VENCIDA  
    $mensaje = "Ha caducado su sesión. Por favor ingrese nuevamente ";
    echo "<script>
    window.location.href = 'http://localhost/lmsActividades/error/error.php';
    </script>";
}
?>