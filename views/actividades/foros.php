<?php
// LLAMAR AL CONTROLADOR DE LA SESIÓN
require_once '../../controllers/session_controller.php';

session_start();
// Verificar si el usuario está autenticado
if (isset($_SESSION['user']) && checkSessionTimeout()) {
    // Se  almacena los datos que son obtenidos  del usuario logueado por medio de un arreglo
    $user = $_SESSION['user'];
    $user_id = $user->userid;
    $rol_user = $user->tipo_user;

    $id_curso = ($_GET['id']);

    // LLAMAR AL HEADER 
    require_once '../../header.php';
    // LLAMAR A LA BASE DE DATOS ZAJUNA 
    require_once '../../config/db_config.php';
    // LLAMAR AL CONTROLADOR DE CONSULTAS 
    require_once '../../controllers/for_controller.php';

    foreach ($esca as $esc) {
        $id_esca = $esc->id;
    }

    $name = nombre_ficha($id_curso);
    foreach ($name as $nam) {
        $nombre_ficha = $nam->fullname;
    }
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
        <div class="container-fluid px-4">
            <div class="history-container" style="display: flex; justify-content: center;">
                <?php mostrar_historial(); ?>
            </div>
            <div class="container-fluid">

                <div class="container-fluid container-hearder">
                    <div class="row">
                        <div class="col-sm-2">
                        </div>
                        <div class="col-sm-8 d-flex justify-content-center">
                            <!-- Mostrar ID de la competencia -->
                            <h3 style="color: white;" class="my-2"><img id="titulo-img" src="../../public/assets/img/documento.svg" alt="icono"><span id="color-titulo"></span> Ficha:<span id="color-titulo"> <?php echo ($nombre_ficha); ?></span></h3>
                        </div>
                    </div>
                </div>
                <!-- Imagen referencia banner inicio de vista centro de calificaciones -->
                <div class="my-4">
                    <img src="../../public/assets/banners/foros.svg" id="img-banner">
                </div>

                <ol class="breadcrumb m-2">
                    <!-- Se accede al arreglo y se imprime el dato requerido, en este caso hacemos el llamado del campo apellido  -->
                    <li class="m-2"><strong>Bienvenido/a</strong> <?php echo $user->firstname . ' ' . $user->lastname; ?></li>
                </ol>
            </div>
            <div class="card p-3 p-md-5">
                <div class="d-flex justify-content-between flex-wrap gap-3">

                    <div>
                        <button class="icono-con-texto ml-2" name="id_curso" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <img src="../../public/assets/img/codigoColor.svg" class="mr-2" alt="Ícono de evaluación" width="52" height="52" id="icono-evaluacion">
                            <p>Código de colores</p>
                        </button>
                    </div>

                    <div class="d-flex flex-wrap gap-3">
                        <?php if ($rol_user == 3) { ?>
                            <nav class="tertiary-navigation-selector">
                                <div class="dropdown">
                                    <!--BOTON PARA REDIRECCIONAR AL APARTADO DE LETRAS DE CALIFICACION DE ZAJUNA -->
                                    <button class="icono-con-texto" type="button" data-toggle="dropdown" aria-expanded="false">
                                        <img src="http://localhost/lmsActividades/public/assets/img/blogs.svg" alt="Ícono de blogs" id="icono-blogs">
                                        &nbsp; Informe Calificador
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="http://localhost/zajuna/grade/edit/letter/index.php?id=<?= $id_esca ?>">Letras de Calificación</a></li>
                                        <li><a class="dropdown-item" href="http://localhost/zajuna/grade/edit/tree/index.php?id=<?= $id_curso ?>">
                                                Categorias</a></li>
                                    </ul>
                                </div>
                            </nav>
                        <?php } ?>

                        <!--BOTONES PARA REDIRECCIONAR A LAS DEMAS VISTAS DE ACTIVIDADES -->
                        <button class="icono-con-texto" onclick="redirectToActividad('<?= $id_curso; ?>')">
                            <img src="http://localhost/lmsActividades/public/assets/img/evaluaciones.svg" alt="Ícono de evaluación" id="icono-evaluacion" class="mr-2">
                            <p>Actividades</p>
                        </button>
                        <button class="icono-con-texto" onclick="redirectToEvidencias('<?= $id_curso; ?>')">
                            <img src="http://localhost/lmsActividades/public/assets/img/evidencias.svg" alt="Ícono de evidencias" id="icono-evidencias" class="mr-2">
                            <p>Evidencias</p>
                        </button>
                        <button class="icono-con-texto" onclick="miFuncion()">
                            <img src="http://localhost/lmsActividades/public/assets/img/foros.svg" alt="Ícono de foros" id="icono-foros" class="mr-2">
                            <p>Blogs</p>
                        </button>
                        <button class="icono-con-texto" onclick="redirectToWikis('<?= $id_curso; ?>')">
                            <img src="http://localhost/lmsActividades/public/assets/img/wikis.svg" alt="Ícono de wikis" id="icono-wikis" class="mr-2">
                            <p>Wikis</p>
                        </button>
                    </div>
                </div>

                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Código de colores</h5>
                            </div>
                            <div class="modal-body">
                                <hr />
                                <p>Este Código de colores esta establecido para la facilidad de lectura de las calificaciones del centro de calificaciones, por favor tenga en cuenta los siguientes códigos de colores:</p>
                                <span class="color-box" style="background-color: #BCE2A8;"></span> Color Verde: APROBADO<br>
                                <span class="color-box" style="background-color: #DF5C73;"></span> Color Rojo: DESAPROBADO<br>
                                <span class="color-box" style="background-color: #FCE059;"></span> Color Amarillo: PENDIENTE DE CALIFICACIÓN<br>
                                <span class="color-box" style="background-color: #b9b9b9;"></span> Color Gris: PENDIENTE DE REALIZAR FORO
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-modal" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="actividades-card">

                    <?php if ($rol_user == 3) { ?>
                        <nav class="tertiary-navigation-selector">
                            <div class="dropdown">
                                <!--BOTÓN PARA REDIRECCIONAR AL APARTADO DE CATEGORÍAS DE CALIFICACIÓN DE ZAJUNA -->
                                <button class="icono-con-texto" type="button" data-toggle="dropdown" aria-expanded="false">
                                    &nbsp;Categorías
                                </button>
                                <ul class="dropdown-menu">
                                    <?php foreach ($categorias as $categoria) {
                                        $id_categoria =  $categoria->id;
                                        $id_rea = $categoria->fullname; ?>
                                        <li>
                                            <a class="dropdown-item" onclick="redirectToForosAp(<?php echo $id_curso; ?>, <?php echo $id_rea; ?>)">
                                                <?php echo $categoria->fullname; ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </nav>
                    <?php } ?>

                    <form method="POST" name="edit_id" id="edit_id" action="actualizar_acti.php">
                        <div class="table-responsive">
                            <?php
                            //INICIO SESION DE APRENDIZ (ROL 3) 
                            if ($rol_user == 3) { ?>
                                <!--VENTANA QUE INDICA CARGANDO MIENTRAS SE REESTRUCTURAN LOS DATOS DE LA TABLA -->
                                <div id="spinner" class="loader" role="status" style="display: none; margin: 0 auto;">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>

                                <table id="tabla-act" class="display" style="width:100%; display: none;">
                                    <!--CABECERA DE LA TABLA CON LAS ACTIVIDADES OBTENIDAS DE ZAJUNA -->
                                    <thead>
                                        <tr id="actividades-thead">
                                            <th>Documento</th>
                                            <th>Nombre Completo</th>
                                            <?php
                                            // SE RECORRE LA CONSULTA DE ACTIVIDADES PARA ALMACENAR EN VARIABLES EL ID DE LA ACTIVIDAD Y EL NOMBRE.
                                            foreach ($actividades as $actividad) {
                                                $id_for = $actividad->idacti;
                                                $name_for = $actividad->itemname;
                                                // SE IMPRIMEN LAS ACTIVIDADES EN LA CABECERA DE LA TABLA
                                            ?>
                                                <th>
                                                    <div class="text-justify"><?php echo $name_for; ?></div>
                                                </th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // SE RECORRE LA CONSULTA DE USERS PARA ALMACENAR EN VARIABLES EL ID, EL DOCUMENTO, EL NOMBRE Y APELLIDO DE LOS APRENDICES MATRRICULADOS EN LA FICHA EN CUESTION.
                                        foreach ($users as $user) {
                                            $id_user = $user->id;
                                            $doc_user = $user->username;
                                            $firstname = $user->firstname;
                                            $lastname = $user->lastname;
                                        ?>
                                            <tr>
                                                <td id="text-align-document"><?php echo $doc_user; ?></td>
                                                <td id="text-align-name"><?php echo $firstname . ' ' . $lastname; ?></td>

                                                <?php
                                                // ITERAMOS NUEVAMENTE LA CONSULTA DE ACTIVIDADES PARA RELACIONAR ACTIVIDADES CON LA NUEVA CONSULTA DE NOTAS Y ASI ORDENAR ACTIVIDADES POR NOTA DE CADA ESTUDIANTE EN LA TABLA.        
                                                foreach ($actividades as $actividad) {
                                                    $id_for = $actividad->idacti;
                                                    echo '<td>';

                                                    // LLAMADA A LA FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCIÓN
                                                    $params = obtenerParametros($conn, $id_for);
                                                    foreach ($params as $param) {
                                                        $id = $param['id'];
                                                    }

                                                    // LLAMADA A LA FUNCION PARA OBTENER LAS PARTICIPACIONES DE UN FORO
                                                    $parti = obtenerParticipacion($conn, $id_for, $id_user);
                                                    $participacion = null;
                                                    foreach ($parti as $part) {
                                                        if (!empty($part['mensaje'])) {
                                                            $participacion = $part['mensaje'];
                                                            break;
                                                        }
                                                    }

                                                    // LLAMADA A LA FUNCION PARA OBTENER LAS NOTAS DE LOS APRENDICES 
                                                    $q_grades = obtenerNotas($conn, $id_user, $id_curso, $id_for);
                                                    $grad = null;
                                                    foreach ($q_grades as $q_grade) {
                                                        $grad = $q_grade['rawgrade'];
                                                        $id_for = $actividad->idacti;
                                                    }

                                                    // SE REALIZA UNA CONDICION QUE VALIDE SI ESTA CONSULTA Q_GRADES TIENE VALORES EN LA BD.
                                                    if (!empty($grad)) {
                                                        // SI LA COLUMNA GRADE ES MAYOR A 70 ENTRARA POR LA CONDICION QUE IMPRIME UNA NOTA A (APROBADO), INDICANDO UNA CASILLA VERDE.
                                                        if ($grad >= 70.00000) {
                                                            echo '<div class="d-flex" style="background-color: #BCE2A8; padding: 10px; border-radius: 10px;">
                                                                    <div class="d-gitd gap-2 col-8 mx-auto">
                                                                        <h6>A</h6>
                                                                    </div>
                                                                    <div>
                                                                        <div class="action-manu" data-collapse="menu">
                                                                            <div class="dropdown show">
                                                                                <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                                    <span class="" tittle="Acciones de la celda" aria-hidden="true"></span>
                                                                                    <span class="sr-only">Acciones de la celda</span>
                                                                                </button>
                                                                                <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                                    <a class="dropdown-item" href="http://localhost/zajuna/mod/forum/discuss.php?d=' . $id . '">Analisis del Foro</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>';
                                                            // SI LA COLUMNA GRADE ES MANOR A 70 ENTRARA POR LA CONDICION QUE IMPRIME UNA NOTA N (NO APROBADO), INDICANDO UNA CASILLA ROJA.
                                                        } else {
                                                            echo '<div class="d-flex" style="background-color: #DF5C73; padding: 10px; border-radius: 10px;">
                                                                    <div class="d-gitd gap-2 col-8 mx-auto">
                                                                        <h6>D</h6>
                                                                    </div>
                                                                    <div class="action-manu" data-collapse="menu">
                                                                        <div class="dropdown show">
                                                                            <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                                <span class="" tittle="Acciones de la celda" aria-hidden="true"></span>
                                                                                <span class="sr-only">Acciones de la celda</span>
                                                                            </button>
                                                                            <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                                <a class="dropdown-item" href="http://localhost/zajuna/mod/forum/discuss.php?d=' . $id . '">Analisis del Foro</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>';
                                                        }
                                                        //ESTUDIANTE CON NOTA / PENDIENTE
                                                    } elseif (!empty($participacion)) {
                                                        echo '<div class="d-flex" style="background-color: #FCE059; padding: 10px; border-radius: 10px;">
                                                                <div class="d-gitd gap-2 col-8 mx-auto">
                                                                    <h6>P</h6>
                                                                </div>
                                                                <div class="action-manu" data-collapse="menu">
                                                                    <div class="dropdown show">
                                                                        <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                            <span class="" title="Acciones de la celda" aria-hidden="true"></span>
                                                                            <span class="sr-only">Acciones de la celda</span>
                                                                        </button>
                                                                        <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                            <a class="dropdown-item" href="http://localhost/zajuna/mod/forum/discuss.php?d=' . $id . '">Calificar del Foro</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>';
                                                        //ESTUDIANTE SIN NOTA / PENDIENTE
                                                        // SI LA COLUMNA GRADE NO CONTIENE VALOR ENTRARÁ POR LA CONDICION QUE IMPRIME UNA NOTA X (PENDIENTE), INDICANDO UNA CASILLA AMARILLA.
                                                    } else {
                                                        $id_for = $actividad->idacti;
                                                        // LLAMADA A LA FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCIÓN DE LAS ACTIVIDADES PENDIENTES POR LOS APRENDICES 
                                                        $paramsPen = obtenerParametrosPendientes($conn, $id_for);
                                                        foreach ($paramsPen as $param) {
                                                            $id = $param['id'];
                                                        }
                                                        echo '<div class="d-flex" style="background-color: #b9b9b9; padding: 10px; border-radius: 10px;">
                                                                <div class="d-gitd gap-2 col-8 mx-auto">
                                                                    <h6>X</h6>
                                                                </div>
                                                                <div class="action-manu" data-collapse="menu">
                                                                    <div class="dropdown show">
                                                                        <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                            <span class="" tittle="Acciones de la celda" aria-hidden="true"></span>
                                                                            <span class="sr-only">Acciones de la celda</span>
                                                                        </button>
                                                                        <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                            <a class="dropdown-item" href="http://localhost/zajuna/mod/forum/view.php?id=' . $id . '">Analisis del Foro</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>';
                                                    }
                                                    echo '</td>';
                                                }
                                                ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                        </div>
                    </form>
                <?php
                                //INICIO SESION DE APRENDIZ (rol 5)
                            } else if ($rol_user == 5) { ?>
                    <table id="tabla_ap" class="display" style="width:100%">
                        <thead>
                            <tr id="actividades-thead">
                                <th>ID</th>
                                <th>FOROS</th>
                                <th>NOTA</th>
                                <th>RETROALIMENTACIÓN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($actividades as $actividad) {
                                    $id_for = $actividad->idacti;
                                    $name_for = $actividad->itemname;
                                    echo '<tr>
                                                <td id="text-align-document">' . $id_for . '</td>
                                                <td id="text-align-name">' . $name_for . '</td>';

                                    // SE RECORRE LA CONSULTA DE USUARIO POR APRENDIZ PARA TRAER AL USUARIO LOGUEADO                                                
                                    foreach ($userApr as $user) {
                                        $id_user = $user->id;

                                        // LLAMADA A LA FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCIÓN
                                        $params = obtenerParametros($conn, $id_for);
                                        $id = $params[0]['id'] ?? null;

                                        // LLAMADA A LA FUNCION PARA OBTENER LAS PARTICIPACIONES DE UN FORO
                                        $parti = obtenerParticipacion($conn, $id_for, $id_user);
                                        $participacion = null;
                                        foreach ($parti as $part) {
                                            if (!empty($part['mensaje'])) {
                                                $participacion = $part['mensaje'];
                                                break;
                                            }
                                        }

                                        // LLAMADA A LA FUNCION PARA OBTENER LAS NOTAS DE LOS APRENDICES 
                                        $q_grades = obtenerNotas($conn, $id_user, $id_curso, $id_for);
                                        $grad = $q_grades[0]['rawgrade'] ?? null;
                                        $retro = $q_grades[0]['feedback'] ?? '';

                                        // Determinar clase y color de la nota
                                        if (!empty($grad)) {
                                            if ($grad >= 70.00000) {
                                                $nota_class = 'A';
                                                $nota_color = '#BCE2A8';
                                            } else {
                                                $nota_class = 'D';
                                                $nota_color = '#DF5C73';
                                            }
                                        } elseif (!empty($participacion)) {
                                            $nota_class = 'P';
                                            $nota_color = '#b9b9b9';
                                        } else {
                                            $nota_class = 'X';
                                            $nota_color = '#FCE059';
                                        }
                                        //ACCIONES DE LAS CELDAS PARA CADA NOTA
                                        echo '<td>
                                                    <div class="d-flex" style="background-color: ' . $nota_color . '; padding: 10px; border-radius: 10px;">
                                                        <div class="col-8 mx-auto">
                                                            <h6>' . $nota_class . '</h6>
                                                        </div>
                                                        <div class="action-menu" data-collapse="menu">
                                                            <div class="dropdown show">
                                                                <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                    <span class="" title="Acciones de la celda" aria-hidden="true"></span>
                                                                    <span class="sr-only">Acciones de la celda</span>
                                                                </button>
                                                                <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                    <a class="dropdown-item" href="http://localhost/zajuna/mod/forum/discuss.php?d=' . $id . '">Revisión del Foro</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>' . $retro . '</td>
                                            </tr>';
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                <?php } ?>
                </div>
            </div>
        </div>
    </main>
<?php
    // LLAMADA AL FOOTER 
    include '../../footer.php';
    // SI EL USUARIO TIENE MAS DE 30 MINUTOS DE INACTIVIDAD ENTRARA POR AQUI Y SE REDIRIGUE A LA PAGINA INICIAL DE ZAJUNA 
} else {
    // ALERTA DE SESION VENCIDA  
    $mensaje = "Ha caducado su sesión. Por favor ingrese nuevamente ";
    echo "<script>window.location.href = 'http://localhost/lmsActividades/error/error.php';</script>";
}
?>