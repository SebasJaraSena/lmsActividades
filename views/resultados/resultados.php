<?php
// llamar al controlador de sesion
require_once '../../controllers/session_controller.php';

session_start();
// Verificar si el usuario está autenticado
if (isset($_SESSION['user']) && checkSessionTimeout()) {

    // Se  almacena los datos que son obtenidos  del usuario logueado por medio de un arreglo
    $user = $_SESSION['user'];
    $id_user = $user->userid;

    if (isset($_GET['params'])) {
        $encodedParams = $_GET['params'];
        $decodedParams = base64_decode($encodedParams);

        parse_str($decodedParams, $params);

        // Decodificar parámetros recibidos por GET
        $curso = base64_decode($params['curso']);
        $id_competencia = base64_decode($params['id_competencia']);
        $id_rea = base64_decode($params['rea_id']);
        $encoded_curso = $params['curso'];
        $encoded_competencia = $params['id_competencia'];

        // Codificar parámetros para su posterior uso en URLs
        $encoded_rea = base64_encode($id_rea);
        $encoded_ficha = base64_encode($curso);
        $encoded_competencia = base64_encode($id_competencia);
    }

    // Incluir el archivo de cabecera
    include '../../header.php';
    // llamar conexion a bases de datos
    require_once '../../config/sofia_config.php';
    // llamar conexion a bases de datos de zajuna
    require_once '../../config/db_config.php';
    // llamar al controlador
    require_once '../../controllers/resp_controller.php';
    $courses = obtenerResultados($curso, $id_competencia, $id_rea);
    foreach ($courses as $course) {
        $nombre = $course->REA_NOMBRE;
    }
?>

    <main>
        <h5 class="p-2 text-center bg-primary text-white">Centro de calificaciones </h5>
        <div class="container-fluid px-4">
            <div class="history-container my-2" style="display: flex; justify-content: center;">
                <?php mostrar_historial(); ?>
            </div>

            <div class="container-fluid mt-2">
                <div class="row ">
                    <div class="col-sm-2">
                        <!-- Botón para redireccionar a la pagina anterior -->
                        <img src="../../public/assets/img/icno-de-regresar.svg" alt="Ícono de regresar" onclick="redirectResulToResultadoAP('<?= $encoded_ficha; ?>','<?= $encoded_competencia; ?>')">
                        <p>Regresar</p>
                    </div>
                    <div class="col-sm-8 d-flex justify-content-center">
                        <h3><img class="ml-2" src="../../public/assets/img/documento.svg" alt="icono">RESULTADOS DE
                            APRENDIZAJE CODIGO:&nbsp; <span id="color-titulo"><?php echo ($id_rea); ?></span></h3>
                    </div>
                </div>
            </div>
            <div class="card p-3 p-md-5">
                <div class="d-flex justify-content-between flex-wrap gap-3 mb-2">
                    <div>
                        <button class="icono-con-texto ml-2" name="id_curso" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <img src="../../public/assets/img/codigoColor.svg" class="mr-2" alt="Ícono de evaluación" width="52" height="52" id="icono-evaluacion">
                            <p>Código de colores</p>
                        </button>
                    </div>
                    <div class="d-flex flex-wrap gap-3">
                        <!-- Botón para redireccionar a las actividades del resultado -->

                        <button type="submit" class="icono-con-texto" name="id_curso" onclick="redirectToActividadApContro('<?= $encoded_ficha; ?>','<?= $encoded_competencia; ?>','<?= $encoded_rea; ?>')">
                            <img src="../../public/assets/img/evaluaciones.svg" alt="Ícono de evaluación" id="icono-evaluacion">
                            <p>Actividades</p>
                        </button>
                        <!--  <button type="submit" class="icono-con-texto" id="resultadosofia" onclick="redirectToSOFIA()">
                                <img src="../../public/assets/img/resultados.svg" alt="Ícono de resultados" id="icono-resultados">
                                <p>EJEMPLO SOFIA SOAP</p>
                            </button> -->
                        <button class="icono-con-texto" id="updateSofiaButton" onclick="submitForm('updateSofia')">
                            <img src="../../public/assets/img/resultados.svg" alt="Ícono de resultados" id="icono-resultados">
                            <p>Enviar a SOFIA</p>
                        </button>

                        <!-- BOTONES PARA DIRECCIONAR A OTRAS PÁGINAS -->
                    </div>
                </div>

                <div class="modal fade " id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <img src="../../public/assets/img/color.svg" height="30" width="30">
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
                                <span class="color-box" style="background-color: #BCE2A8;"></span> Color Verde: APROBADO
                                <br>
                                <span class="color-box" style="background-color: #DF5C73;"></span> Color Rojo: DESAPROBADO
                                <br>
                                <span class="color-box" style="background-color: #FCE059;"></span> Color Amarillo: PENDIENTE
                                </br>
                                <span class="color-box" style="background-color: #aa80ff;"></span> Color Morado: ENVIADO A
                                SOFIA </br>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-modal" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body" id="resultadoap-card">
                <h5><?php echo ($nombre); ?></h5>
                <br>
                    <input type="hidden" id="ficha" value="<?php echo $encoded_ficha ?>">
                    <input type="hidden" id="competencia" value="<?php echo $encoded_competencia; ?>">
                    <input type="hidden" id="rea" value="<?php echo $encoded_rea; ?>">

                    <form method="POST" id="myForm">
                        <!-- METODO POST A TRAVES DE FORM PARA UPDATE DE LA DATA -->
                        <table id="resultados_table" class="display" style="width:100%">
                            <thead>
                                <tr id="vistaap-thead">
                                    <th><input type="checkbox" id="selectAllCheckbox"></th>
                                    <th title="Información adicional sobre el código">Codigo</th>
                                    <th>Aprendiz</th>
                                    <th>Calificación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                foreach ($courses as $course) {
                                    $id_resultado = $course->USR_NUM_DOC;
                                    $documento_user = $course->ADR_ID;
                                    $fullname = $course->USR_NOMBRE . ' ' . $course->USR_APELLIDO;
                                    $rea_evaluacion = $course->ADR_EVALUACION_RESULTADO;
                                    $estado_adr = $course->ESTADO_SINCRONIZACION;
                                ?>
                                    <tr>
                                        <td><input type="checkbox" class="rowCheckbox" name="usuario[]" value="<?php echo $documento_user; ?>"></td>
                                        <td id="codigo" style="text-align: center;"><?php echo $documento_user ?></td>
                                        <td id="aprendiz"><?php echo $fullname; ?></td>

                                        <?php
                                        $valorA = base64_encode('A');
                                        $valorD = base64_encode('D');
                                        $colors = [
                                            'A' => '#BCE2A8',
                                            'D' => '#DF5C73',
                                            'X' => '#FCE059',
                                        ];

                                        if ($estado_adr == 1) {
                                            $colorStyle = 'background-color:' . $colors[$rea_evaluacion] . ';';
                                            if ($rea_evaluacion == 'X') {
                                                echo '<td style="' . $colorStyle . '"><input type="hidden" id="estado" value="'.$estado_adr.'">
                                                <select id="selectResultado" class="form-select" name="resultado[]" aria-label="Default select example" onchange="updateHiddenField(this)">
                                                    <option selected id="result" disabled style="background-color:darkgray;">' . $rea_evaluacion . '</option>
                                                    <option value="' . $valorA . '">A</option>
                                                    <option value="' . $valorD . '">D</option>
                                                </select>
                                                <input type="hidden" class="selected-resultado" name="selected_resultado[]" value="' . $rea_evaluacion . '">
                                            </td>';
                                            } else {
                                                echo '<td id="result" style="' . $colorStyle . '">' . $rea_evaluacion . '</td>';
                                            }
                                        } elseif ($estado_adr == 2) {
                                            $colorStyle = 'background-color:' . $colors[$rea_evaluacion] . ';';
                                            echo '<td style="' . $colorStyle . '"><input type="hidden" id="estado" value="'.$estado_adr.'">
                                                <select id="selectResultado" class="form-select" name="resultado[]" aria-label="Default select example" onchange="updateHiddenField(this)">
                                                    <option selected id="result" disabled style="background-color:darkgray;">' . $rea_evaluacion . '</option>
                                                    <option value="' . $valorA . '">A</option>
                                                    <option value="' . $valorD . '">D</option>
                                                </select>
                                                <input type="hidden" class="selected-resultado" name="selected_resultado[]" value="' . $rea_evaluacion . '">
                                            </td>';
                                        } elseif ($estado_adr == 3) {
                                            $colorStyle = 'background-color:#aa80ff;';
                                            echo '<td id="result" class="fw-bold" style="' . $colorStyle . '"><input type="hidden" id="estado" value="'.$estado_adr.'">' . $rea_evaluacion . '</td>';
                                        } else {
                                            $colorStyle = 'background-color:#FCE059;';
                                            echo '<td id="result"><input type="hidden" id="estado" value="'.$estado_adr.'"> ' . $rea_evaluacion . '</td>';
                                        }
                                        ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <!-- Rest of the form -->
                        <div style="text-align:left;">
                            <!-- BOTON PARA MANEJO DE DATA A POSTGRES -->
                            <button type="button" id="updateButton" class="btn btn-success" onclick="submitForm('update')">Guardar Cambios</button>
                        </div>
                    </form>
                    <!-- Modal -->
                    <div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="dataModalLabel">Datos Seleccionados</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="post" id="formulario_sofia">
                                    <div class="modal-body">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr id="vistaap-thead">
                                                    <th scope="col">Aprendiz</th>
                                                    <th scope="col">Calificaciòn</th>
                                                </tr>
                                            </thead>
                                            <tbody id="selectedData">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary" id="confirmUpdateSofia">Confirmar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <!-- llamada Footer -->
<?php include '../../footer.php';
} else {

    $mensaje = "Ha caducado su sesión. Por favor ingrese nuevamente ";
    echo "<script>
    window.location.href = 'http://localhost/lms/error/error.php';
    </script>";
}
?>