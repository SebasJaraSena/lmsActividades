<?php
// llamar al controlador de sesion
require_once '../../controllers/session_controller.php';

session_start();
//llamada header
include '../../header.php';
// Verificar si el usuario está autenticado
if (isset($_SESSION['user']) && checkSessionTimeout()) {

    // Almacenar datos en variables, uno de la sesión, otro de la URL
    $user = $_SESSION['user'];
    $rol_user = $user->tipo_user;
    $user_id = $user->userid;

    if (isset($_GET['params'])) {
        $encodedParams = $_GET['params'];
        $decodedParams = base64_decode($encodedParams);

        parse_str($decodedParams, $params);

        // Ahora puedes usar $id_ficha y $id_competencia como necesites
        $curso = base64_decode($params['id_ficha']);
        $id_competencia = base64_decode($params['id_competencia']);

        $encoded_curso = $params['id_ficha'];
        $encoded_competencia = $params['id_competencia'];
    }

    // llamar conexion a bases de datos de integracion
    require_once '../../config/sofia_config.php';
    // llamar conexion a bases de datos de zajuna
    require_once '../../config/db_config.php';
    // llamar al controlador
    require_once '../../controllers/results_controller.php';
?>
    <main>
        <h5 class="p-2 text-center bg-primary text-white">Centro de calificaciones Resultados</h5>
        <!-- boton regresar  -->
        <div class="history-container my-2 " style="display: flex; justify-content: center;">
            <?php
            mostrar_historial();
            ?>
        </div>
        <div class="container-fluid px-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="../../public/assets/img/icno-de-regresar.svg" id="back-button" alt="Ícono de regresar" onclick="redirectToCompetencias('<?= $encoded_curso; ?>')">
                        <p>Regresar</p>
                    </div>
                    <div class="col-sm-8 d-flex justify-content-center">
                        <h3><img class="ml-2" id="titulo-img" src="../../public/assets/img/documento.svg" alt="icono"> ID
                            COMPETENCIA:&nbsp; <span id="color-titulo"> <?php echo ($id_competencia); ?></span></h3>
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
                    <!-- Botón para redireccionar a las actividades del resultado -->
                    <div class="d-flex flex-wrap gap-3">
                        <button type="submit" class="icono-con-texto" name="id_curso" onclick="redirectToActi('<?= $encoded_curso; ?>','<?= $encoded_competencia; ?>')">
                            <img src="../../public/assets/img/evaluaciones.svg" class="mr-2" alt="Ícono de evaluación" id="icono-evaluacion">
                            <p>Actividades Generales</p>
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
                    <?php
                    if ($rol_user == 3) {
                        // CONSULTA PARA OBTENER RESULTADOS POR COMPETENCIA DEL PROGRAMA 
                        $resultados = obtenerResultadosxCompetencia($curso, $id_competencia);
                    ?>

                        <table id="tabla" class="display" style="width:100%">
                            <thead>
                                <tr id="resultados-thead">
                                    <th>Documento </th>
                                    <th>Nombre Completo </th>
                                    <?php
                                    // Inicializar un array para almacenar los REA_ID y REA_NOMBRE únicos con resultados
                                    $unique_reas = [];

                                    foreach ($resultados as $resultado) {
                                        // Verificar si el REA_ID tiene resultados
                                        if (!empty($resultado->ADR_EVALUACION_RESULTADO)) {
                                            $unique_reas[$resultado->REA_ID] = $resultado->REA_NOMBRE;
                                        }
                                    }
                                    // Ordenar el array por REA_ID antes de imprimir en la cabecera de la tabla
                                    ksort($unique_reas);

                                    // Imprimir los REA_ID y REA_NOMBRE únicos con resultados en la cabecera de la tabla
                                    foreach ($unique_reas as $rea_id => $rea_nombre) {
                                        $encode_rea = base64_encode($rea_id);
                                        $encoded_curso = base64_encode($curso); // Asegúrate de tener estas variables definidas
                                        $encoded_competencia = base64_encode($id_competencia); // Asegúrate de tener estas variables definidas

                                        echo '<th title="' . $rea_nombre . '"><button class="icono-con-texto btn-success" onclick="redirectToResultado(\'' . $encoded_curso . '\', \'' . $encoded_competencia . '\', \'' . $encode_rea . '\')">';
                                        echo '<p class="ml-2">' . $rea_id . '</p>';
                                        echo '</button></th>';
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Inicializar un array para almacenar los resultados de evaluación de cada usuario
                                $evaluaciones_por_usuario = [];
                                // Inicializar un array para almacenar los nombres completos de cada usuario
                                $nombres_completos = [];
                                $estados_por_usuario = [];

                                // Añadir el resultado de evaluación al array correspondiente al usuario
                                foreach ($resultados as $resultado) {
                                    $documento_user = $resultado->USR_NUM_DOC;
                                    $fullname = $resultado->USR_NOMBRE . ' ' . $resultado->USR_APELLIDO;
                                    $estado_adr = $resultado->ESTADO_SINCRONIZACION;
                                    $nombres_completos[$documento_user] = $fullname;

                                    if (!isset($evaluaciones_por_usuario[$documento_user])) {
                                        $evaluaciones_por_usuario[$documento_user] = [];
                                    }

                                    $evaluaciones_por_usuario[$documento_user][$resultado->REA_ID] = $resultado->ADR_EVALUACION_RESULTADO;
                                    $estados_por_usuario[$documento_user][$resultado->REA_ID] = $estado_adr;
                                }

                                // Mostrar los resultados
                                foreach ($evaluaciones_por_usuario as $documento_user => $evaluaciones) {
                                    echo "<tr >";
                                    echo "<td id='text-align-document'>" . $documento_user . "</td>";
                                    echo "<td id='text-align-name'>" . $nombres_completos[$documento_user] . "</td>";

                                    // Mostrar resultados de evaluación
                                    foreach ($unique_reas as $rea_id => $_) {
                                        // Verificar si el usuario tiene una evaluación para este REA_ID
                                        if (isset($evaluaciones[$rea_id])) {
                                            $evaluacion = $evaluaciones[$rea_id];
                                            /* Relaciona la calififcacion del resultado de aprendizaje y el estado que posee en la base de datos */
                                            $estado = isset($estados_por_usuario[$documento_user][$rea_id]) ? $estados_por_usuario[$documento_user][$rea_id] : '';
                                            $evaluacion_estado = $evaluacion;
                                            $estilos = [
                                                'A' => 'background-color:#BCE2A8; padding: 8px; border-radius: 8px;',
                                                'D' => 'background-color: #DF5C73; padding: 8px; border-radius: 8px;',
                                                'X' => 'background-color: #FCE059; padding: 8px; border-radius: 8px;',
                                                '3' => 'background-color: #aa80ff; padding: 8px; border-radius: 8px;',
                                                'default' => 'background-color:#FCE059; padding: 8px; border-radius: 8px;'
                                            ];

                                            if (in_array($estado, [1, 2])) {
                                                $colorStyle = $estilos[$evaluacion] ?? $estilos['default'];
                                            } elseif ($estado == 3 && in_array($evaluacion, ['A', 'D', 'X'])) {
                                                $colorStyle = $estilos['3'];
                                            } else {
                                                $colorStyle = $estilos['default'];
                                            }
                                            echo "<td style='" . $colorStyle . "'>" . $evaluacion_estado . "</td>";
                                        } else {
                                            // Si no hay evaluación para este REA_ID, imprimir una celda vacía
                                            echo "<td></td>";
                                        }
                                    }
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                    <?php
                    }
                    // USUARIO APRENDIZ 
                    if ($rol_user == 5) {
                    ?>
                        <table id="tabla_ap" class="display" style="width:100%">
                            <thead>
                                <tr id="resultados-thead">
                                    <th>ID</th>
                                    <th>RESULTADOS DE APRENDIZAJE</th>
                                    <th>NOTA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $resultadosApren = obtenerResultadosxCompetenciaxApr($curso, $id_competencia, $user_id);
                                foreach ($resultadosApren as $resultado) {
                                    $rea_id = $resultado->REA_ID;
                                    $nomRes = $resultado->REA_NOMBRE;
                                    $nota = $resultado->ADR_EVALUACION_RESULTADO;

                                    echo "<tr>";
                                    echo "<td id='text-align-document'>" . $rea_id . "</td>";
                                    echo "<td id='text-align-name'>" . $nomRes . "</td>";
                                    echo '<td>';
                                    if ($nota == 'A') {
                                        echo
                                        '<div class="d-flex" style="background-color: #BCE2A8; padding: 10px; border-radius: 10px;">
                                                    <div class="col-8 mx-auto">
                                                        <p>A</p>
                                                    </div>
                                                </div>';
                                    } elseif ($nota == 'D') {
                                        echo
                                        '<div class="d-flex" style="background-color: #DF5C73; padding: 10px; border-radius: 10px;">
                                                    <div class="d-gitd gap-2 col-8 mx-auto">
                                                        <p>D</p>
                                                    </div>
                                                </div>';
                                    } elseif ($nota == 'X') {
                                        echo
                                        '<div class="d-flex" style="background-color: #FCE059; padding: 10px; border-radius: 10px;">
                                                    <div class="d-gitd gap-2 col-8 mx-auto">
                                                        <p>X</p>
                                                    </div>
                                                </div>';
                                    }
                                    echo '</td>';
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php
                    }
                    ?>
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