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

    // LLAMAR AL HEADER 
    require_once '../../header.php';
    // LLAMAR A LA BASE DE DATOS ZAJUNA 
    require '../../config/db_config.php';
    // LLAMAR AL CONTROLADOR DE CONSULTAS 
    require_once '../../controllers/wikis_controller.php';

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
        <!-- HISTORIAL DE NAVEGACIÓN -->
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
                        </div>
                        <div class="col-sm-8 d-flex justify-content-center">
                            <!-- Mostrar ID de la competencia -->
                            <h3 style="color: white;" class="my-2"><img id="titulo-img" src="../../public/assets/img/documento.svg" alt="icono"><span id="color-titulo"></span> Ficha:<span id="color-titulo"> <?php echo ($nombre_ficha); ?></span></h3>
                        </div>
                    </div>
                </div>
                <!-- Imagen referencia banner inicio de vista centro de calificaciones -->
                <div class="my-4">
                    <img src="../../public/assets/banners/wikis.svg" id="img-banner">
                </div>

                <ol class="breadcrumb m-2">
                    <!-- Se accede al arreglo y se imprime el dato requerido, en este caso hacemos el llamado del campo apellido  -->
                    <li class="m-2"><strong>Bienvenido/a</strong> <?php echo $user->firstname . ' ' . $user->lastname; ?></li>
                </ol>
                <div class="card p-3 p-md-5">
                    <div class="d-flex justify-content-between flex-wrap gap-3">

                        <div>
                            <button class="icono-con-texto ml-2" name="id_curso" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <img src="../../public/assets/img/codigoColor.svg" class="mr-2" alt="Ícono de evaluación" width="52" height="52" id="icono-evaluacion">
                                <p>Código de colores</p>
                            </button>
                        </div>

                        <div class="d-flex flex-wrap gap-3">

                            <?php
                            // SI EL USUARIO QUE INGRESA TIENE ROL 3 (INSTRUCTOR) PODRA VISUALIZAR ESTE BOTON
                            if ($rol_user == 3) {
                            ?>
                                <nav class="tertiary-navigation-selector">
                                    <div class="dropdown">
                                        <!--BOTON PARA REDIRECCIONAR AL APARTADO DE LETRAS DE CALIFICACION DE ZAJUNA -->
                                        <button class="icono-con-texto" type="button" data-toggle="dropdown" aria-expanded="false">
                                            <img src="http://localhost/lmsActividades/public/assets/img/blogs.svg" alt="Ícono de blogs" id="icono-blogs">
                                            &nbsp; Informe Calificador
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="http://localhost/zajuna/grade/edit/letter/index.php?id=<?= $id_esca ?>">
                                                    Letras
                                                    calificación</a></li>
                                            <li><a class="dropdown-item" href="http://localhost/zajuna/grade/edit/tree/index.php?id=<?= $id_curso ?>">
                                                    Categorias</a></li>
                                        </ul>
                                    </div>
                                </nav>
                            <?php
                            }
                            ?>

                            <!--BOTONES PARA REDIRECCIONAR A LAS DEMAS VISTAS DE FOROS Y EVIDENCIAS -->
                            <button class="icono-con-texto" onclick="redirectToActividad('<?= $id_curso; ?>')">
                                <img src="http://localhost/lmsActividades/public/assets/img/evaluaciones.svg" alt="Ícono de evaluación" id="icono-evaluacion" class="mr-2">
                                <p>Actividades</p>
                            </button>
                            <button class="icono-con-texto" onclick="redirectToForos('<?= $id_curso; ?>')">
                                <img src="http://localhost/lmsActividades/public/assets/img/foros.svg" alt="Ícono de foros" id="icono-foros" class="mr-2">
                                <p>Foros</p>
                            </button>
                            <button class="icono-con-texto" onclick="redirectToEvidencias('<?= $id_curso; ?>')">
                                <img src="http://localhost/lmsActividades/public/assets/img/evidencias.svg" alt="Ícono de evidencias" id="icono-evidencias" class="mr-2">
                                <p>Evidencias</p>
                            </button>
                            <button class="icono-con-texto" onclick="miFuncion()">
                                <img src="http://localhost/lmsActividades/public/assets/img/blogs.svg" alt="Ícono de blogs" id="icono-blogs" class="mr-2">
                                <p>Blogs</p>
                            </button>
                        </div>
                    </div>


                    <div class="modal fade " id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">

                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Código de colores</h5>
                                </div>
                                <div class="modal-body">
                                    <hr />
                                    <p>
                                    <p>Este Código de colores esta establecido para la facilidad de lectura de las
                                        calificaciones
                                        del centro de calificaciones, por favor tenga en cuenta los siguientes codigos de
                                        colores:
                                    </p>
                                    <span class="color-box" style="background-color: #BCE2A8;"></span> Color Verde: PARTICIPÓ
                                    <br>
                                    <span class="color-box" style="background-color: #b9b9b9;"></span> Color Gris: PENDIENTE DE PARTICIPAR
                                    </br>
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-modal" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="actividades-card">

                        <form method="POST" name="edit_id" id="edit_id" action="actualizar_acti.php">
                            <div class="table-responsive">
                                <?php
                                //INICION SESION ROL INSTRUCTOR (ROL 3)
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
                                                    $acti = $actividad->id;
                                                    $name = $actividad->nombre;
                                                    // SE IMPRIMEN LAS ACTIVIDADES EN LA CABECERA DE LA TABLA
                                                    echo
                                                    '<th>' . $name . ' </th>';
                                                }
                                                ?>
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
                                                echo
                                                '<tr>
                                                <td id="text-align-document">' . $doc_user . '</td>
                                                <td id="text-align-name">' . $firstname . ' ' . $lastname . '</td>';

                                                // ITERAMOS NUEVAMENTE LA CONSULTA DE ACTIVIDADES PARA RELACIONAR ACTIVIDADES CON LA NUEVA CONSULTA DE NOTAS Y ASI ORDENAR ACTIVIDADES POR NOTA DE CADA ESTUDIANTE EN LA TABLA.        
                                                foreach ($actividades as $actividad) {
                                                    echo '<td>';
                                                    $itemnumber = 0;
                                                    $acti = $actividad->id;

                                                    $params = obtenerParametros($conn, $acti, $id_curso);
                                                    foreach ($params as $param) {
                                                        $id = $param['id'];
                                                    }

                                                    // LLAMADA A LA FUNCION PARA OBTENER LA PARTICIPACION DE LOS APRENDICES EN LAS WIKIS
                                                    $parti = obtenerParticipacion($conn, $id_user, $acti, $id_curso);

                                                    // SE REALIZA UNA CONDICION QUE VALIDE SI ESTA CONSULTA PARTI TIENE VALORES EN LA BD.
                                                    if (!empty($parti)) {
                                                        foreach ($parti as $part) {
                                                            $participacion = $part['content'];

                                                            if (!empty($participacion)) {
                                                                echo
                                                                '<div class="d-flex" style="background-color: #BCE2A8; padding: 10px; border-radius: 10px;">
                                                                    <div class="col-8 mx-auto">
                                                                        <h6 >P</h6>
                                                                    </div>
                                                                <div>
                                                                    <div class="action-manu" data-collapse="menu">
                                                                        <div class="dropdown show">
                                                                            <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                                <span class="" tittle="Acciones de la celda" aria-hidden="true">
                                                                                </span>
                                                                                <span class="sr-only">Acciones de la celda</span>
                                                                            </button>
                                                                            <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                                <a class="dropdown-item" href="http://localhost/zajuna/mod/wiki/view.php?id=' . $id . '">Analisis del Wiki</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>';
                                                            }
                                                        }
                                                    } else {
                                                        echo '
                                                    <div class="d-flex" style="background-color: #b9b9b9; padding: 10px; border-radius: 10px;">
                                                        <div class="d-gitd gap-2 col-8 mx-auto">
                                                            <h6>X</h6>
                                                        </div>
                                                        <div>
                                                            <div class="action-manu" data-collapse="menu">
                                                                <div class="dropdown show">
                                                                    <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                        <span class="" tittle="Acciones de la celda" aria-hidden="true">
                                                                        </span>
                                                                        <span class="sr-only">Acciones de la celda</span>
                                                                    </button>
                                                                    <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                         <a class="dropdown-item" href="http://localhost/zajuna/mod/wiki/view.php?id=' . $id . '">Analisis del Wiki</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>';
                                                    }
                                                    echo '</td>';
                                                }
                                                echo '</tr>';
                                            } ?>
                                        </tbody>
                                    </table>
                            </div>
                        </form>
                    <?php
                                    //INICIO SESION DE APRENDIZ (ROL 5) 
                                } else if ($rol_user == 5) { ?>
                        <table id="tabla_ap" class="display" style="width:100%">
                            <!--CABECERA DE LA TABLA CON LAS ACTIVIDADES OBTENIDAS DE ZAJUNA -->
                            <thead>
                                <tr id="actividades-thead">
                                    <th>ID</th>
                                    <th>PRUEBAS DE CONOCIMIENTO</th>
                                    <th>NOTA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // SE RECORRE LA CONSULTA DE ACTIVIDADES PARA ALMACENAR EN VARIABLES EL ID DE LA ACTIVIDAD Y EL NOMBRE.
                                    foreach ($actividades as $actividad) {
                                        $acti = $actividad->id;
                                        $name = $actividad->nombre;

                                        // SE IMPRIMEN EL ID Y NOMBRE DE LAS ACTIVIDADES
                                        echo
                                        '<tr>
                                    <td id = "text-align-document">' . $acti . '</td>
                                    <td id = "text-align-name">' . $name . '</td>';

                                        // SE RECORRE LA CONSULTA DE USUARIO POR APRENDIZ PARA TRAER AL USUARIO LOGUEADO
                                        foreach ($userApr as $user) {
                                            $id_user = $user->id;
                                            echo '<td>';
                                            $itemnumber = 0;
                                            $acti = $actividad->id;

                                            $params = obtenerParametros($conn, $acti, $id_curso);
                                            foreach ($params as $param) {
                                                $id = $param['id'];
                                            }

                                            // LLAMADA A LA FUNCION PARA OBTENER LA PARTICIPACION DE LOS APRENDICES EN LAS WIKIS
                                            $parti = obtenerParticipacion($conn, $id_user, $acti, $id_curso);

                                            // SE REALIZA UNA CONDICION QUE VALIDE SI ESTA CONSULTA PARTI TIENE VALORES EN LA BD.
                                            if (!empty($parti)) {
                                                foreach ($parti as $part) {
                                                    $participacion = $part['content'];

                                                    if (!empty($participacion)) {
                                                        echo
                                                        '<div class="d-flex" style="background-color: #BCE2A8; padding: 10px; border-radius: 10px;">
                                                            <div class="col-8 mx-auto">
                                                                <h6 >P</h6>
                                                            </div>
                                                        <div>
                                                            <div class="action-manu" data-collapse="menu">
                                                                <div class="dropdown show">
                                                                    <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                        <span class="" tittle="Acciones de la celda" aria-hidden="true">
                                                                        </span>
                                                                        <span class="sr-only">Acciones de la celda</span>
                                                                    </button>
                                                                    <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                        <a class="dropdown-item" href="http://localhost/zajuna/mod/wiki/view.php?id=' . $id . '">Analisis del Wiki</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>';
                                                    }
                                                }
                                            } else {
                                                echo '
                                                <div class="d-flex" style="background-color: #FCE059; padding: 10px; border-radius: 10px;">
                                                    <div class="d-gitd gap-2 col-8 mx-auto">
                                                        <h6>X</h6>
                                                    </div>
                                                    <div>
                                                        <div class="action-manu" data-collapse="menu">
                                                            <div class="dropdown show">
                                                                <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                    <span class="" tittle="Acciones de la celda" aria-hidden="true">
                                                                    </span>
                                                                    <span class="sr-only">Acciones de la celda</span>
                                                                </button>
                                                                <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                        <a class="dropdown-item" href="http://localhost/zajuna/mod/wiki/view.php?id=' . $id . '">Realizar Wiki</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                                            }
                                            echo '</td>';
                                        }
                                        echo '</tr>';
                                    } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                    </div>
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
    echo "<script>
    window.location.href = 'http://localhost/lmsActividades/error/error.php';
    </script>";
}
?>