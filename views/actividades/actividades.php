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
    // LLAMAR A LA BASE DE DATOS INTEGRACION 
    require '../../config/sofia_config.php';
    // LLAMAR AL CONTROLADOR DE CONSULTAS 
    require_once '../../controllers/acti_controller.php';

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
                                <!-- boton regresar  -->
                                <h6>
                                    <img src="../../public/assets/img/icno-de-regresar.svg" alt="Ícono de regresar" style="margin-right: 5px;" onclick="redirectToZajuna('<?= $id_curso; ?>')">
                                    <u id="titulo-regresar" onclick="redirectToZajuna('<?= $id_curso; ?>')">Regresar al
                                        Curso</u>
                                </h6>
                            </div>
                            <div class="col-sm-7 d-flex">
                                <h3 style="color: white;" class="my-2 text-start">
                                    Nombre: <span title="<?php echo $nombre_ficha; ?>" id="color-titulo-nombre"><?php echo substr($nombre_ficha, 0, 64); ?></span>...</h3>
                                <span id="color-titulo-categoria"></span>
                            </div>
                            <div class="col-sm-3 d-flex">
                                <h3 style="color: white;" class="my-2 text-start"><img id="titulo-img" src="../../public/assets/img/documento.svg" alt="?">
                                    Ficha: <span id="color-titulo-ficha"><?php echo ($ficha); ?> </span>
                            </div>
                        </div>
                    </div>
                    <!-- Imagen referencia banner inicio de vista centro de calificaciones -->
                    <div class="my-4">
                        <img src="../../public/assets/banners/actividades.svg" id="img-banner">
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
                                    <img src="../../public/assets/img/codigoColor.svg" class="mr-3" alt="Ícono de evaluación" width="52" height="52" id="icono-evaluacion">
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
                                                        de calificación</a></li>
                                                <li><a class="dropdown-item" href="http://localhost/zajuna/grade/edit/tree/index.php?id=<?= $id_curso ?>">
                                                        Configuración de calificaciones del curso</a></li>
                                            </ul>
                                        </div>
                                    </nav>
                                <?php
                                }
                                ?>
                                <!--BOTONES PARA REDIRECCIONAR A LAS DEMAS VISTAS DE FOROS Y EVIDENCIAS -->
                                <button class="icono-con-texto" onclick="redirectToForos('<?= $id_curso; ?>')">
                                    <img src="http://localhost/lmsActividades/public/assets/img/foros.svg" alt="Ícono de foros" id="icono-foros" class="mr-2">
                                    <p>Foros</p>
                                </button>
                                <button class="icono-con-texto" onclick="redirectToEvidencias('<?= $id_curso; ?>')">
                                    <img src="http://localhost/lmsActividades/public/assets/img/evidencias.svg" alt="Ícono de evidencias" id="icono-evidencias" class="mr-2">
                                    <p>Evidencias</p>
                                </button>
                                <!-- <button class="icono-con-texto" onclick="miFuncion()">
                                <img src="http://localhost/lmsActividades/public/assets/img/blogs.svg" alt="Ícono de blogs" id="icono-blogs" class="mr-2">
                                <p>Blogs</p>
                            </button> -->
                                <button class="icono-con-texto" onclick="redirectToWikis('<?= $id_curso; ?>')">
                                    <img src="http://localhost/lmsActividades/public/assets/img/wikis.svg" alt="Ícono de wikis" id="icono-wikis" class="mr-2">
                                    <p>Wikis</p>
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
                                <?php
                                //INICION SESION ROL INSTRUCTOR (ROL 3)
                                if ($rol_user == 3) { ?>
                                    <!--VENTANA QUE INDICA CARGANDO MIENTRAS SE REESTRUCTURAN LOS DATOS DE LA TABLA -->
                                    <div id="spinner" class="loader" role="status" style="display: none; margin: 0 auto;">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>

                                    <table id="tabla-act" style="width:100%; display: none;">
                                        <thead>
                                            <tr id="categorias-thead">
                                                <th rowspan="2">Documento</th>
                                                <th rowspan="2">Nombre Completo</th>
                                                <th rowspan="2">Correo</th>
                                                <?php
                                                $actividadesCat = [];
                                                foreach ($actividades as $actividad) {
                                                    $categoria = $actividad->fullname;
                                                    $actividadesCat[$categoria][] = $actividad;
                                                }
                                                // Generar encabezados de columna para categorías
                                                foreach ($actividadesCat as $categoria => $actividades) {
                                                    $colspan = count($actividades);

                                                    echo '<th colspan="' . $colspan . '" tittle="' . $categoria . '">
                                                            <button class="icono-con-texto btn-success" onclick="redirectToActividadAp(\'' . $id_curso . '\', \'' . $categoria . '\')">';
                                                    echo    '<p class="ml-2">' . $categoria . '</p>';
                                                    echo
                                                    '</button>
                                                        </th>';
                                                }
                                                ?>
                                            </tr>
                                            <tr id="actividades-thead">
                                                <?php
                                                // Generar encabezados de columna para actividades
                                                foreach ($actividadesCat as $actividades) {
                                                    foreach ($actividades as $actividad) {
                                                        $nombre_acti = $actividad->itemname;
                                                        // Mostrar solo los primeros 38 caracteres
                                                        $short_name = substr($nombre_acti, 0, 38);
                                                        // Nombre completo para uso en JavaScript
                                                        $full_name = addslashes($nombre_acti);

                                                        echo '<th>';
                                                        echo '<p class="nombre_acti text-center" tittle="' . $short_name . '">' . $short_name . '...</p>';
                                                        echo '<a href="javascript:void(0);" class="readmore-btn" onclick="showFullName(this, \'' . $full_name . '\', \'' . $short_name . '\')"> Leer más</a>';
                                                        echo '<a href="javascript:void(0);" class="readless-btn" onclick="showShortName(this, \'' . $short_name . '\', \'' . $full_name . '\')" style="display:none;"> Leer menos</a>';
                                                        echo '</th>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($users as $user) {
                                                $id_user = $user->id;
                                                $doc_user = $user->username;
                                                $firstname = $user->firstname;
                                                $lastname = $user->lastname;
                                                $correo = $user->email;

                                                echo '<tr>';
                                                echo '<td id="text-align-document">' . $doc_user . '</td>';
                                                echo '<td id="text-align-name">' . $firstname . ' ' . $lastname . '</td>';
                                                echo '<td id="text-align-name">' . $correo . '</td>';

                                                foreach ($actividadesCat as $categoria => $actividades) {
                                                    foreach ($actividades as $actividad) {
                                                        echo '<td>';
                                                        $acti = $actividad->idacti;

                                                        // LLAMADA A LA FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCION
                                                        $params = obtenerParametros($id_user, $id_curso, $acti);
                                                        foreach ($params as $param) {
                                                            $gradeid = $param['gradeid'];
                                                            $itemid = $param['itemid'];
                                                            $id = $param['id'];
                                                            $idAttemp = $param['idattemp'];
                                                        }
                                                        $q_grades = obtenerNotas($id_user, $acti);
                                                        if (!empty($q_grades)) {
                                                            foreach ($q_grades as $q_grade) {
                                                                $grad = $q_grade['grade'];
                                                                if ($grad >= 7.00000) {
                                                                    echo '<div class="d-flex" style="background-color: #BCE2A8; padding: 10px; border-radius: 10px;">
                                                                <div class="col-8 mx-auto">
                                                                    <h6 >A</h6>
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
                                                                                <a class="dropdown-item" href="http://localhost/zajuna/mod/quiz/review.php?attempt=' . $idAttemp . '&cmid=' . $id . '">Analisis de Actividad</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>';
                                                                } else {
                                                                    echo '<div class="d-flex" style="background-color: #DF5C73; padding: 10px; border-radius: 10px;">
                                                                    <div class="d-gitd gap-2 col-8 mx-auto">
                                                                        <h6>D</h6>
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
                                                                                        <a class="dropdown-item" href="http://localhost/zajuna/mod/quiz/review.php?attempt=' . $idAttemp . '&cmid=' . $id . '">Analisis de Actividad</a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>';
                                                                }
                                                            }
                                                        } else {
                                                            $acti = $actividad->idacti;

                                                            // LLAMADA A LA FUNCION PARA OBTENER LOS PARAMETROS DE REDIRECCIÓN DE LAS ACTIVIDADES PENDIENTES POR LOS APRENDICES 
                                                            $paramsPen = obtenerParametrosPendientes($acti);
                                                            foreach ($paramsPen as $param) {
                                                                $id = $param['id'];
                                                            }
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
                                                                                    <a class="dropdown-item" href="http://localhost/zajuna/mod/quiz/grade.php?id=' . $id . '&itemid&itemnumber&gradeid&userid">Analisis de Actividad</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>';
                                                        }

                                                        echo '</td>';
                                                    }
                                                }
                                                echo '</tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                            </div>
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
                                    $actividadesCat = [];
                                    foreach ($actividades as $actividad) {
                                        $categoria = $actividad->fullname;
                                        $actividadesCat[$categoria][] = $actividad;
                                    }

                                    // Print the categories and related activities
                                    foreach ($actividadesCat as $categoria => $actividades) {
                                        // Print the category row
                                        echo '<tr style="background-color: #d9d9d9;">';
                                        echo '<td></td>';
                                        echo '<td style="font-weight: bold;">' . $categoria . '</td>';
                                        echo '<td></td>';
                                        echo '</tr>';

                                        // Iterate over each activity within the category
                                        foreach ($actividades as $actividad) {
                                            $acti = $actividad->idacti;
                                            $name = $actividad->itemname;

                                            // Print the activity ID and name
                                            echo '<tr>';
                                            echo '<td id="text-align-document">' . $acti . '</td>';
                                            echo '<td id="text-align-name">' . $name . '</td>';

                                            // Iterate over each user
                                            foreach ($userApr as $user) {
                                                $id_user = $user->id;
                                                echo '<td>';
                                                $itemnumber = 0;
                                                $acti = $actividad->idacti;

                                                // Get activity parameters
                                                $params = obtenerParametros($id_user, $id_curso, $acti);
                                                $idAttemp = '';
                                                foreach ($params as $param) {
                                                    $idAttemp = $param['idattemp'];
                                                }

                                                // Get pending activity parameters
                                                $paramsPen = obtenerParametrosPendientes($acti);
                                                $id = '';
                                                foreach ($paramsPen as $param) {
                                                    $id = $param['id'];
                                                }

                                                // Get the grades of the users
                                                $q_grades = obtenerNotas($id_user, $acti);
                                                // Check if the query has values in the database
                                                if (!empty($q_grades)) {
                                                    foreach ($q_grades as $q_grade) {
                                                        $grad = $q_grade['grade'];
                                                        // If the grade column is greater than 70, print a note A (approved), indicating a green box
                                                        if ($grad >= 7.00000) {
                                                            echo
                                                            '<div class="d-flex" style="background-color: #BCE2A8; padding: 10px; border-radius: 10px;">
                                                                <div class="col-8 mx-auto">
                                                                    <h6>A</h6>
                                                                </div>
                                                                <div class="action-manu" data-collapse="menu">
                                                                    <div class="dropdown show">
                                                                        <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                            <span class="" title ="Acciones de la celda" aria-hidden="true"></span>
                                                                            <span class="sr-only">Acciones de la celda</span>
                                                                        </button>
                                                                        <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                            <a class="dropdown-item" href="http://localhost/zajuna/mod/quiz/review.php?attempt=' . $idAttemp . '&cmid=' . $id . '">Revisión de Actividad</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>';
                                                            // If the grade column is less than 70, print a note N (not approved), indicating a red box
                                                        } else {
                                                            echo
                                                            '<div class="d-flex" style="background-color: #DF5C73; padding: 10px; border-radius: 10px;">
                                                                <div class="d-gitd gap-2 col-8 mx-auto">
                                                                    <h6>D</h6>
                                                                </div>
                                                                <div class="action-manu" data-collapse="menu">
                                                                    <div class="dropdown show">
                                                                        <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                            <span class="" title ="Acciones de la celda" aria-hidden="true"></span>
                                                                            <span class="sr-only">Acciones de la celda</span>
                                                                        </button>
                                                                        <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                            <a class="dropdown-item" href="http://localhost/zajuna/mod/quiz/review.php?attempt=' . $idAttemp . '&cmid=' . $id . '">Revisión de Actividad</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>';
                                                        }
                                                    }
                                                    // If the grade column does not contain a value, print a note X (pending), indicating a yellow box
                                                } else {
                                                    echo
                                                    '<div class="d-flex" style="background-color: #b9b9b9; padding: 10px; border-radius: 10px;">
                                                        <div class="d-gitd gap-2 col-8 mx-auto">
                                                            <h6>X</h6>
                                                        </div>
                                                        <div class="action-manu" data-collapse="menu">
                                                            <div class="dropdown show">
                                                                <button class="btn btn-link btn-icon icon-size-3 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-type="grade" data-id="">
                                                                    <span class="" title ="Acciones de la celda" aria-hidden="true"></span>
                                                                    <span class="sr-only">Acciones de la celda</span>
                                                                </button>
                                                                <div role="menu" class="dropdown-menu collapse" id="calificaciones-menu" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px;">
                                                                    <a class="dropdown-item" href="http://localhost/zajuna/mod/quiz/view.php?id=' . $id . '">Realizar Prueba de Conocimiento</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>';
                                                }
                                                echo '</td>';
                                            }
                                            echo '</tr>';
                                        }
                                    }
                                    ?>
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
        //SI EL USUARIO NO PERTENECE AL CURSO SE REDIRIJE A UNA VISTA DE ERROR
    } else {
        echo "<script>
        window.location.href = 'http://localhost/lmsActividades/error/error_ingre.php';
        </script>";
    }
} else {
    // SI EL USUARIO TIENE MAS DE 30 MINUTOS DE INACTIVIDAD ENTRARA POR AQUI Y SE REDIRIGUE A LA PAGINA INICIAL DE ZAJUNA 
    // ALERTA DE SESION VENCIDA  
    $mensaje = "Ha caducado su sesión. Por favor ingrese nuevamente ";
    echo "<script>
    window.location.href = 'http://localhost/lmsActividades/error/error.php';
    </script>";
}

?>