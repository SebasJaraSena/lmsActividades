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
    // LLAMAR AL CONTROLADOR DE CONSULTAS 
    require_once '../../controllers/eviap_controller.php';


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
        <div class="history-container  " style="display: flex; justify-content: center;">
            <?php
            mostrar_historial();
            ?>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-2">
                    <!-- BOTON PARA REGRESAR A LA VISTA DE RESULTSADOS  -->
                    <img src="http://localhost/lmsActividades/public/assets/img/icno-de-regresar.svg" alt="Ícono de regresar" onclick="redirectToEvidencias('<?= $id_curso; ?>')">
                    <p>Regresar</p>
                </div>
                <div class="col-sm-8 d-flex justify-content-center">
                    <h3 style="text-align: center;"><img class="ml-2" src="../../public/assets/img/documento.svg" alt="icono">
                        EVIDENCIAS / RESULTADO DE APRENDIZAJE: <span id="color-titulo"><?php echo ($id_rea); ?></span></h3>
                </div>
            </div>
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
                    <nav class="tertiary-navigation-selector">
                        <div class="dropdown">
                            <!--BOTON PARA REDIRECCIONAR AL APARTADO DE LETRAS DE CALIFICACION DE ZAJUNA -->
                            <button class="icono-con-texto" type="button" data-toggle="dropdown" aria-expanded="false">
                                <img src="http://localhost/lmsActividades/public/assets/img/blogs.svg" alt="Ícono de blogs" id="icono-blogs">
                                Informe Calificador
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="http://localhost/zajuna/grade/edit/letter/index.php?id=<?= $id_esca ?>">Letras
                                        calificación</a></li>
                            </ul>
                        </div>
                    </nav>
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
                            <p>Este Código de colores esta establecido para la facilidad de lectura de las calificaciones
                                del centro de calificaciones, por favor tenga en cuenta los siguientes codigos de colores:
                            </p>
                            <span class="color-box" style="background-color: #BCE2A8;"></span> Color Verde: APROBADO <br>
                            <span class="color-box" style="background-color: #DF5C73;"></span> Color Rojo: DESAPROBADO <br>
                            <span class="color-box" style="background-color: #FCE059;"></span> Color Amarillo: PENDIENTE <br>
                            <span class="color-box" style="background-color: #b9b9b9;"></span> Color Gris: PENDIENTE DE CALIFICACIÓN
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

                <div class="table-responsive">
                    <?php
                    //INICIO SESION DE APRENDIZ (ROL 3)
                    if ($rol_user == 3) {
                        $redireccion = "evi_ap.php";
                    ?>
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
                                        $courseid = $actividad->courseid;
                                        $itemid = $actividad->id;
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
                                    $email = $user->email;
                                    echo
                                    '<tr>
                                        <td id=text-align-document>' . $id_user . '</td>
                                        <td id="text-align-name">' . $firstname . ' ' . $lastname . '</td>';

                                    // ITERAMOS NUEVAMENTE LA CONSULTA DE ACTIVIDADES PARA RELACIONAR ACTIVIDADES CON LA NUEVA CONSULTA DE NOTAS Y ASI ORDENAR ACTIVIDADES POR NOTA DE CADA ESTUDIANTE EN LA TABLA.        
                                    foreach ($actividades as $actividad) {
                                        echo '<td>';
                                        $itemnumber = 0;
                                        $id_evi = $actividad->idacti;
                                        $courseid = $actividad->courseid;
                                        $itemid = $actividad->id;

                                        // LLAMADA A LA FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCIÓN
                                        $params = obtenerParametros($conn, $id_curso, $id_evi);
                                        foreach ($params as $param) {
                                            $id = $param['id'];
                                        }

                                        $parti = obtenerParticipacionEvi($conn, $id_evi, $id_user);
                                        $participacion = null;
                                        foreach ($parti as $part) {
                                            if (!empty($part['status'])) {
                                                $participacion = $part['status'];
                                                break;
                                            }
                                        }

                                        // LLAMADA A LA FUNCION PARA OBTENER LAS NOTAS DE LOS APRENDICES 
                                        $q_grades = obtenerNotas($conn, $id_user, $id_evi);
                                        foreach ($q_grades as $q_grade) {
                                            $grad = $q_grade['rawgrade'];

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
                                                                            <a class="dropdown-item" href="http://localhost/zajuna/grade/report/singleview/index.php?id=' . $courseid . '&item=grade&itemid=' . $itemid . '&gpr_type=report&gpr_plugin=grader&gpr_courseid=' . $courseid . '">Retroalimentación</a>
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
                                                                            <a class="dropdown-item" href="http://localhost/zajuna/grade/report/singleview/index.php?id=' . $courseid . '&item=grade&itemid=' . $itemid . '&gpr_type=report&gpr_plugin=grader&gpr_courseid=' . $courseid . '">Retroalimentación</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>';
                                                }
                                                //ESTUDIANTE CON NOTA / PENDIENTE
                                            } elseif (!empty($participacion)) {
                                                echo '<div class="d-flex" style="background-color: #b9b9b9; padding: 10px; border-radius: 10px;">
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
                                                                    <a class="dropdown-item" href="http://localhost/zajuna/mod/assign/view.php?id=' . $id . '&rownum=0&action=grader&userid=' . $id_user . '">Calificar Evidencia</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>';
                                                //ESTUDIANTE SIN NOTA / PENDIENTE
                                                // SI LA COLUMNA GRADE NO CONTIENE VALOR ENTRARÁ POR LA CONDICION QUE IMPRIME UNA NOTA X (PENDIENTE), INDICANDO UNA CASILLA AMARILLA.
                                            } else {
                                                $id_evi = $actividad->idacti;
                                                $courseid = $actividad->courseid;
                                                $itemid = $actividad->id;
                                                $correos[] = $email;

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
                                                                        <a class="dropdown-item" href="http://localhost/zajuna/grade/report/singleview/index.php?id=' . $courseid . '&item=grade&itemid=' . $itemid . '&gpr_type=report&gpr_plugin=grader&gpr_courseid=' . $courseid . '">Retroalimentación</a>
                                                                        <form method="POST" name="emailForm" id="emailForm"  action="http://localhost/lmsActividades/controllers/enviarEmail.php">
                                                                            <input type="hidden" name="correo[]" class="CheckedAK " value="' . $email . '">
                                                                            <input type="hidden" name="id_ficha" value="' . $id_curso . '">
                                                                            <input type="hidden" name="actividades" value="' . $redireccion . '">
                                                                            <input type="hidden" name="rea_id" value="' . $id_rea . '">
                                                                            <button type="submit" class="btn" >Enviar Recordatorio</button>
                                                                        </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> ';
                                            }
                                        }

                                        if (empty($q_grades)) {
                                            $id_evi = $actividad->idacti;
                                            $courseid = $actividad->courseid;
                                            $itemid = $actividad->id;
                                            $correos[] = $email;

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
                                                                        <a class="dropdown-item" href="http://localhost/zajuna/grade/report/singleview/index.php?id=' . $courseid . '&item=grade&itemid=' . $itemid . '&gpr_type=report&gpr_plugin=grader&gpr_courseid=' . $courseid . '">Retroalimentación</a>
                                                                        <form method="POST" name="emailForm" id="emailForm"  action="http://localhost/lmsActividades/controllers/enviarEmail.php">
                                                                            <input type="hidden" name="correo[]" class="CheckedAK " value="' . $email . '">
                                                                            <input type="hidden" name="id_ficha" value="' . $id_curso . '">
                                                                            <input type="hidden" name="actividades" value="' . $redireccion . '">
                                                                            <input type="hidden" name="rea_id" value="' . $id_rea . '">
                                                                            <button type="submit" class="btn" >Enviar Recordatorio</button>
                                                                        </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> ';
                                        }
                                        echo '</td>';

                                        echo '</tr>';
                                    }
                                } ?>
                            </tbody>
                        </table>
                        <?php
                        echo '<form method="POST" name="emailForm" id="emailForm"  action="http://localhost/lmsActividades/controllers/enviarEmail.php">
                                    ';
                        $correos[] = $email;
                        $uniqueEmails = array_unique($correos);
                        foreach ($uniqueEmails as $correo) {
                            echo '
                                        <input type="hidden" name="correo[]" class="CheckedAK " value="' . $correo . '">
                                       ';
                        }
                        echo '
                                       <input type="hidden" name="id_ficha" value="' . $id_curso . '">
                                       <input type="hidden" name="actividades" value="' . $redireccion . '">
                                       <input type="hidden" name="rea_id" value="' . $id_rea . '">
                                       <button type="submit" class="btn icono-con-texto  my-3">Enviar Recordatorio a todos</button>
                                   </form>
                                   ';
                        ?>
                </div>
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
    echo "<script>
    window.location.href = 'http://localhost/lmsActividades/error/error.php';
    </script>";
}
?>