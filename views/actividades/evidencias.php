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

    // Se valida que los datos de la url sean los correctos
    if (isset($_GET['params'])) {
        $encodedParams = $_GET['params'];
        $decodedParams = base64_decode($encodedParams);

        parse_str($decodedParams, $params);
        // Decodificar parámetros recibidos por GET
        $id_curso = base64_decode($params['id_ficha']);
        $id_competencia = base64_decode($params['id_competencia']);

        // Codificar parámetros para su posterior uso en URLs
        $encoded_curso = $params['id_ficha'];
        $encoded_competencia = $params['id_competencia'];
    }

    // LLAMAR AL HEADER 
    require_once '../../header.php';
    // LLAMAR A LA BASE DE DATOS ZAJUNA 
    require_once '../../config/db_config.php';
    // LLAMAR A LA BASE DE DATOS INTEGRACION 
    require '../../config/sofia_config.php';
    // LLAMAR AL CONTROLADOR DE CONSULTAS 
    require_once '../../controllers/evi_controller.php';

    foreach ($esca as $esc) {
        $id_esca = $esc->id;
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

        <h5 class="p-2 text-center bg-primary text-white">Centro de Calificaciones: Evidencias</h5>
        <div class="container-fluid px-4">
            <div class="history-container " style="display: flex; justify-content: center;">
                <?php
                mostrar_historial();
                ?>
            </div>

            <h3 style="text-align: center;"><img class="ml-2" src="../../public/assets/img/documento.svg" alt="icono">EVIDENCIAS</h3>
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
                        if ($rol_user == 3) {
                        ?>
                            <nav class="tertiary-navigation-selector">
                                <div class="dropdown">
                                    <!--BOTON PARA REDIRECCIONAR AL APARTADO DE LETRAS DE CALIFICACION DE ZAJUNA -->
                                    <button class="icono-con-texto" type="button" data-toggle="dropdown" aria-expanded="false">
                                        <img src="http://localhost/lms/public/assets/img/blogs.svg" alt="Ícono de blogs" id="icono-blogs">&nbsp;
                                        Informe Calificador
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="http://localhost/zajuna/grade/edit/letter/index.php?id=<?= $id_esca ?>">Letras calificación</a></li>
                                    </ul>
                                </div>
                            </nav>
                        <?php
                        }
                        ?>

                        <!--BOTONES PARA REDIRECCIONAR A LAS DEMAS VISTAS DE ACTIVIDADES -->
                        <button class="icono-con-texto" onclick="redirectToActividad('<?= $encoded_curso; ?>','<?= $encoded_competencia; ?>')">
                            <img src="http://localhost/lms/public/assets/img/evaluaciones.svg" alt="Ícono de evaluación" id="icono-evaluacion" class="mr-2">

                            <p>Actividades</p>
                        </button>
                        <button class="icono-con-texto" onclick="redirectToForos('<?= $encoded_curso; ?>','<?= $encoded_competencia; ?>')">
                            <img src="http://localhost/lms/public/assets/img/foros.svg" alt="Ícono de foros" id="icono-foros" class="mr-2">

                            <p>Foros</p>
                        </button>
                        <button class="icono-con-texto" onclick="miFuncion()">
                            <img src="http://localhost/lms/public/assets/img/foros.svg" alt="Ícono de foros" id="icono-foros" class="mr-2">

                            <p>Blogs</p>
                        </button>
                        <button class="icono-con-texto" onclick="miFuncion()">
                            <img src="http://localhost/lms/public/assets/img/wikis.svg" alt="Ícono de wikis" id="icono-wikis" class="mr-2">

                            <p>Wikis</p>
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
                                <p>Este Código de colores esta establecido para la facilidad de lectura de las calificaciones del centro de calificaciones, por favor tenga en cuenta los siguientes codigos de colores:</p>
                                <span class="color-box" style="background-color: #BCE2A8;"></span> Color Verde: APROBADO <br>
                                <span class="color-box" style="background-color: #DF5C73;"></span> Color Rojo: DESAPROBADO <br>
                                <span class="color-box" style="background-color: #FCE059;"></span> Color Amarillo: PENDIENTE </br>
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
                                                $itemname = $actividad->itemname;
                                                $id_evi = $actividad->idacti;
                                                // SE IMPRIMEN LAS ACTIVIDADES EN LA CABECERA DE LA TABLA
                                                echo
                                                '<th>
                                                <div class="text-center">' . $itemname . '</div>
                                            </th>';
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
                                                $id_evi = $actividad->idacti;

                                                // LLAMADA A LA FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCIÓN
                                                $params = obtenerParametros($conn, $id_curso, $id_evi);
                                                foreach ($params as $param) {
                                                    $id = $param['id'];
                                                }

                                                // LLAMADA A LA FUNCION PARA OBTENER LAS NOTAS DE LOS APRENDICES 
                                                $q_grades = obtenerNotas($conn, $id_user, $id_evi);
                                                foreach ($q_grades as $q_grade) {
                                                    $grad = $q_grade['rawgrade'];
                                                    $id_evi = $actividad->idacti;

                                                    // SE REALIZA UNA CONDICION QUE VALIDE SI ESTA CONSULTA Q_GRADES TIENE VALORES EN LA BD.
                                                    if (!empty($grad)) {
                                                        // SI LA COLUMNA GRADE ES MAYOR A 70 ENTRARA POR LA CONDICION QUE IMPRIME UNA NOTA A (APROBADO), INDICANDO UNA CASILLA VERDE.
                                                        if ($grad >= 70.00000) {
                                                            echo
                                                            '<div class="d-flex" style="background-color: #BCE2A8; padding: 10px; border-radius: 10px;">
                                                                <div class="d-gitd gap-2 col-8 mx-auto">
                                                                    <h6>A</h6>
                                                                </div>
                                                                <div>
                                                                    <div class="action-manu" data-collapse="menu">
                                                                        <div class="dropdown show">
                                                                            <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                                <span class="" tittle ="Acciones de la celda" aria-hidden="true">
                                                                                </span>
                                                                                <span class="sr-only">Acciones de la celda</span>
                                                                            </button>
                                                                            <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                                <a class="dropdown-item" href="http://localhost/zajuna/mod/assign/view.php?id=' . $id . '&rownum=0&action=grader&userid=' . $id_user . '">Analisis de la Evidencia</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>';
                                                            // SI LA COLUMNA GRADE ES MANOR A 70 ENTRARA POR LA CONDICION QUE IMPRIME UNA NOTA N (NO APROBADO), INDICANDO UNA CASILLA ROJA.
                                                        } else {
                                                            echo
                                                            '<div class="d-flex" style="background-color: #DF5C73; padding: 10px; border-radius: 10px;">
                                                                <div class="d-gitd gap-2 col-8 mx-auto">
                                                                    <h6>D<h6>
                                                                </div>
                                                                <div>
                                                                    <div class="action-manu" data-collapse="menu">
                                                                        <div class="dropdown show">
                                                                            <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                                <span class="" tittle ="Acciones de la celda" aria-hidden="true">
                                                                                </span>
                                                                                <span class="sr-only">Acciones de la celda</span>
                                                                            </button>
                                                                            <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                                <a class="dropdown-item" href="http://localhost/zajuna/mod/assign/view.php?id=' . $id . '&rownum=0&action=grader&userid=' . $id_user . '">Analisis de la Evidencia</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>';
                                                        }
                                                        //ESTUDIANTE SIN NOTA / PENDIENTE
                                                        // SI LA COLUMNA GRADE NO CONTIENE VALOR ENTRARÁ POR LA CONDICION QUE IMPRIME UNA NOTA X (PENDIENTE), INDICANDO UNA CASILLA AMARILLA.
                                                    } else {
                                                        $id_evi = $actividad->idacti;

                                                        // LLAMADA A LA FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCIÓN DE LAS ACTIVIDADES PENDIENTES POR LOS APRENDICES 
                                                        $params = obtenerParametros($conn, $id_curso, $id_evi);
                                                        foreach ($params as $param) {
                                                            $id = $param['id'];
                                                        }

                                                        echo
                                                        '<div class="d-flex" style="background-color: #FCE059; padding: 10px; border-radius: 10px;">
                                                            <div class="d-gitd gap-2 col-8 mx-auto">
                                                                <h6>X<h6>
                                                            </div>

                                                            <div>
                                                                <div class="action-manu" data-collapse="menu">
                                                                    <div class="dropdown show">
                                                                        <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                            <span class="" tittle ="Acciones de la celda" aria-hidden="true">
                                                                            </span>
                                                                            <span class="sr-only">Acciones de la celda</span>
                                                                        </button>
                                                                        <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                                <a class="dropdown-item" href="http://localhost/zajuna/mod/assign/view.php?id=' . $id . '">Analisis de la Evidencia</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> ';
                                                    }
                                                }
                                                // USUARIO NO REGISTRADO EN NINGUNA EVIDENCIA
                                                if (empty($q_grades)) {
                                                    $id_evi = $actividad->idacti;

                                                    // LLAMADA A LA FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCIÓN DE LAS ACTIVIDADES PENDIENTES POR LOS APRENDICES 
                                                    $params = obtenerParametros($conn, $id_curso, $id_evi);
                                                    foreach ($params as $param) {
                                                        $id = $param['id'];
                                                    }

                                                    echo
                                                    '<div class="d-flex" style="background-color: #FCE059; padding: 10px; border-radius: 10px;">
                                                        <div class="d-gitd gap-2 col-8 mx-auto">
                                                            <h6>X<h6>
                                                        </div>

                                                        <div>
                                                            <div class="action-manu" data-collapse="menu">
                                                                <div class="dropdown show">
                                                                    <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                        <span class="" tittle ="Acciones de la celda" aria-hidden="true">
                                                                        </span>
                                                                        <span class="sr-only">Acciones de la celda</span>
                                                                    </button>
                                                                    <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                            <a class="dropdown-item" href="http://localhost/zajuna/mod/assign/view.php?id=' . $id . '">Analisis de la Evidencia</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> ';
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
                                //INICIO SESION DE APRENDIZ (rol 5)
                            } else if ($rol_user == 5) { ?>
                    <table id="tabla_ap" class="display" style="width:100%">
                        <!--CABECERA DE LA TABLA CON LAS ACTIVIDADES OBTENIDAS DE ZAJUNA -->
                        <thead>
                            <tr id="actividades-thead">
                                <th>ID</th>
                                <th>EVIDENCIAS</th>
                                <th>NOTA</th>
                                <th>RETROALIMENTACIÓN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // SE RECORRE LA CONSULTA DE ACTIVIDADES PARA ALMACENAR EN VARIABLES EL ID DE LA ACTIVIDAD Y EL NOMBRE.
                                foreach ($actividades as $actividad) {
                                    $itemname = $actividad->itemname;
                                    $id_evi = $actividad->idacti;

                                    // SE IMPRIMEN EL ID Y NOMBRE DE LAS ACTIVIDADES
                                    echo
                                    '<tr>
                                        <td id = "text-align-document">' . $id_evi . '</td>
                                        <td id = "text-align-name">' . $itemname . '</td>';

                                    // SE RECORRE LA CONSULTA DE USUARIO POR APRENDIZ PARA TRAER AL USUARIO LOGUEADO
                                    foreach ($userApr as $user) {
                                        echo '<td>';
                                        $id_user = $user->id;
                                        $itemnumber = 0;
                                        $id_evi = $actividad->idacti;

                                        // LLAMADA A LA FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCIÓN
                                        $params = obtenerParametros($conn, $id_curso, $id_evi);
                                        foreach ($params as $param) {
                                            $id = $param['id'];
                                        }

                                        // LLAMADA A LA FUNCION PARA OBTENER LAS NOTAS DE LOS APRENDICES 
                                        $q_grades = obtenerNotas($conn, $id_user, $id_evi);
                                        foreach ($q_grades as $q_grade) {
                                            $grad = $q_grade['rawgrade'];
                                            $retro = $q_grade['feedback'];

                                            // SE REALIZA UNA CONDICION QUE VALIDE SI $GRAD TIENE VALORES EN LA BD.
                                            if (!empty($grad)) {
                                                // SI LA COLUMNA GRADE ES MAYOR A 70 ENTRARA POR LA CONDICION QUE IMPRIME UNA NOTA A (APROBADO), INDICANDO UNA CASILLA VERDE.
                                                if ($grad >= 70.00000) {
                                                    echo
                                                    '<div class="d-flex" style="background-color: #BCE2A8; padding: 10px; border-radius: 10px;"">
                                                        <div class="col-8 mx-auto">
                                                            <h6>A</h6>
                                                        </div>
                                                        <div>
                                                            <div class="action-manu" data-collapse="menu">
                                                                <div class="dropdown show">
                                                                    <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                        <span class="" tittle ="Acciones de la celda" aria-hidden="true">
                                                                        </span>
                                                                        <span class="sr-only">Acciones de la celda</span>
                                                                    </button>
                                                                    <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                        <a class="dropdown-item" href="http://localhost/zajuna/mod/assign/view.php?id=' . $id . '">Revisión de Evidencia</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>';
                                                    // SI LA COLUMNA GRADE ES MANOR A 70 ENTRARA POR LA CONDICION QUE IMPRIME UNA NOTA N (NO APROBADO), INDICANDO UNA CASILLA ROJA.
                                                } else if ($grad <= 70.00000) {
                                                    echo
                                                    '<div class="d-flex" style="background-color: #DF5C73; padding: 10px; border-radius: 10px;">
                                                        <div class="d-gitd gap-2 col-8 mx-auto">
                                                            <h6>D</h6>
                                                        </div>
                                                        <div class="action-manu" data-collapse="menu">
                                                            <div class="dropdown show">
                                                                <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                    <span class="" tittle ="Acciones de la celda" aria-hidden="true">
                                                                    </span>
                                                                    <span class="sr-only">Acciones de la celda</span>
                                                                </button>
                                                                <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                    <a class="dropdown-item" href="http://localhost/zajuna/mod/assign/view.php?id=' . $id . '">Revisión de Evidencia</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>';
                                                }
                                                // SI LA COLUMNA GRADE NO CONTIENE VALOR ENTRARÁ POR LA CONDICION QUE IMPRIME UNA NOTA X (PENDIENTE), INDICANDO UNA CASILLA AMARILLA.
                                            } else if (empty($grad)) {
                                                echo
                                                '<div class="d-flex" style="background-color: #FCE059; padding: 10px; border-radius: 10px;">
                                                    <div class="d-gitd gap-2 col-8 mx-auto">
                                                        <h6>X</h6>
                                                    </div>
                                                    <div class="action-manu" data-collapse="menu">
                                                        <div class="dropdown show">
                                                            <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                <span class="" tittle ="Acciones de la celda" aria-hidden="true">
                                                                </span>
                                                                <span class="sr-only">Acciones de la celda</span>
                                                            </button>
                                                            <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                <a class="dropdown-item" href="http://localhost/zajuna/mod/assign/view.php?id=' . $id . '">Realizar Evidencia</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                                            }
                                            echo '</td>';

                                            echo
                                            '<td>' . $retro . '</td>';
                                        }
                                    }
                                    echo '</tr>';
                                } ?>
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
    echo  "<script>
    window.location.href = 'http://localhost/lms/error/error.php';
    </script>";
}
?>