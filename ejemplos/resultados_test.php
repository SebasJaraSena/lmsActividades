<?php
session_start();


// Verificar si el usuario está autenticado
if (isset($_SESSION['user'])) {


    // Almacenar datos en variables, uno de la sesión, otro de la URL
    $user = $_SESSION['user'];
    $rol_user = $user->tipo_user;
    $user_id = $user->userid;
    $encoded_curso = $_GET['id_ficha'];
    $encoded_competencia = ($_GET['id_competencia']);
    $curso = base64_decode($_GET['id_ficha']);
    $id_competencia = base64_decode($_GET['id_competencia']);


    //llamada header
    include '../../header.php';
    // llamar conexion a bases de datos
    require_once '../../config/sofia_config.php';

?>
    <main>
        <h5 class="p-2 text-center bg-primary text-white">Centro de calificaciones Resultados</h5>
        <!-- boton regresar  -->
        <div class="container-fluid px-4">

            <div class="container-fluid inline-flex">
                <img src="../../public/assets/img/icno-de-regresar.svg" id="back-button" alt="Ícono de regresar" onclick="redirectToCompetencias('<?= $encoded_curso; ?>')">
                <p>Regresar</p>
                <script>
                    function redirectToCompetencias(encoded_curso, encoded_competencia) {
                        window.location.href = `../competencias.php?idnumber=${encoded_curso}`;
                    }
                </script>
            </div>

            <div class="container-icono-con-texto d-flex">
                <button type="submit" class="icono-con-texto" name="id_curso" onclick="redirectToActividad('<?= $encoded_curso; ?>','<?= $encoded_competencia; ?>')">
                    <img src="../../public/assets/img/evaluaciones.svg" alt="Ícono de evaluación" id="icono-evaluacion">
                    <p>Actividades</p>
                </button>
                <script>
                    function redirectToActividad(encoded_curso, encoded_competencia) {
                        window.location.href = `../actividades/actividades.php?id_ficha=${encoded_curso}&id_competencia=${encoded_competencia}`;
                    }
                </script>
                <?php

                if ($rol_user == 3) { ?>


                    <button class="icono-con-texto" id="resultadosbutton" onclick="showAlert()">
                        <img src="../../public/assets/img/resultados.svg" alt="Ícono de resultados" id="icono-resultados">
                        <p>Enviar a SOFIA</p>
                    </button>
                <?php } ?>
            </div>

            <div class="card m-4">
                <div class="card-body" id="resultadoap-card">
                    <p class="card-text"><?= 'CATEGORIA COMPETENCIA: ' . $id_competencia; ?></p>
                    <?php
                    if ($rol_user == 3) {
                        // CONSULTA PARA OBTENER RESULTADOS POR COMPETENCIA DEL PROGRAMA - INSTRUCTOR
                        /*  $con_resultado = $replica->query("SELECT * from \"INTEGRACION\".\"vista_vistaap\"
                           where \"FIC_ID\" = $curso and \"CMP_ID\" = $id_competencia"); */


                        $con_resultado = $replica->query("SELECT \"FIC_ID\",\"USR_NOMBRE\",\"USR_APELLIDO\",\"USR_NUM_DOC\",\"CMP_ID\",\"REA_ID\",\"ADR_EVALUACION_COMPETENCIA\",\"ADR_EVALUACION_RESULTADO\" FROM \"INTEGRACION\".vista_resultados
                       where \"FIC_ID\" = 2966656 and \"CMP_ID\" = 37714
                       order by \"USR_NOMBRE\" asc;");
                        $resultados = $con_resultado->fetchAll(PDO::FETCH_OBJ);

                        echo '
                       <table id="example" class="display" style="width:100%">
                           <thead>
                               <tr id="resultados-thead">
                                   <th>Documento</th>
                                   <th>Nombre Completo</th>';
                        // Suponiendo que tiene una matriz de ID de resultados ($resultados)
                        foreach ($resultados as $resultado) {
                            $id_resultado = $resultado->REA_ID;
                            $documento_user = $resultado->USR_NUM_DOC;
                            $fullname = $resultado->USR_NOMBRE . ' ' . $resultado->USR_APELLIDO;
                            echo '
                                   <th>
                                       <form action="resultados.php" Method="POST">
                                           <input type="hidden" value="' . $curso . '" name="curso" id="curso">
                                           <input type="hidden" value="' . $id_competencia . '" name="id_competencia" id="id_competencia">
                                           <button name="id_rea" id="id_rea" class="btn btn-success m-2" type="submit" value="' . $id_resultado . '">
                                               ' . $id_resultado . '
                                           </button>
                                       </form>
                                   </th>';
                            echo '
                               </tr>
                           </thead>
                           <tbody>';
                            // Consulta para unificacion de tablas y muestra de usuarios
                            /* $sentencia = $replica->query("SELECT * FROM \"INTEGRACION\".\"vista_resultados\" WHERE \"CMP_ID\" = $id_competencia AND \"vista_resultados\".\"FIC_ID\" =$curso ;  ");
                       $courses = $sentencia->fetchAll(PDO::FETCH_OBJ); */

                            // Inicializar un array para almacenar los resultados de evaluación de cada usuario
                            $evaluaciones_por_usuario = [];
                            // Inicializar un array para almacenar los nombres completos de cada usuario
                            $nombres_completos = [];
                            $nombres_completos[$documento_user] = $fullname;

                            // Añadir el resultado de evaluación al array correspondiente al usuario
                            if (!isset($evaluaciones_por_usuario[$documento_user])) {
                                $evaluaciones_por_usuario[$documento_user] = [];
                            }
                            // Asumiendo que $course->REA_ID y $course->ADR_EVALUACION_RESULTADO son los datos correctos
                            $evaluaciones_por_usuario[$documento_user][$course->REA_ID] = $course->ADR_EVALUACION_RESULTADO;
                            // Mostrar los resultados
                            foreach ($evaluaciones_por_usuario as $documento_user => $evaluaciones) {
                                echo "<tr>";
                                echo "<td>" . $documento_user . "</td>";
                                echo "<td>" .  $nombres_completos[$documento_user] . "</td>";
                                // Mostrar resultados de evaluación
                                foreach ($resultados as $resultado) {
                                    if ($evaluaciones[$id_resultado]  == 'A') {
                                        $colorStyle = 'background-color:#BCE2A8;';
                                    } elseif ($evaluaciones[$id_resultado]  == 'D') {
                                        $colorStyle = 'background-color: #DF5C73;';
                                    } else {
                                        $colorStyle = 'background-color:#FCE059;';
                                    }
                                    echo "<td style='" . $colorStyle . "'>" . $evaluaciones[$id_resultado] . "</td>";
                                }
                            }
                        }
                    ?>
                        </tbody>
                        <!-- Rest of the table -->
                        </table>
                    <?php }
                    if ($rol_user ==  5) { ?>
                        <table id="example" class="display" style="width:100%">
                            <thead>
                                <tr id="resultados-thead">


                                    <th>Nombre Completo</th>
                                    <th>Resultado de Aprendizaje</th>
                                    <th>Evaluación Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Consulta para unificacion de tablas y muestra de usuarios
                                $sentencia = $replica->query("SELECT \"U\".\"ID_USUARIO_LMS\", \"U\".\"RGA_ID\", \"U\".\"FIC_ID\",\"U\".\"USR_NOMBRE\",\"U\".\"USR_APELLIDO\",\"U\".\"USR_NUM_DOC\", \"ADR\".\"CMP_ID\", \"ADR\".\"RGA_ID\",\"RA\".\"REA_ID\",\"RA\".\"REA_NOMBRE\", \"ADR\".\"ADR_EVALUACION_COMPETENCIA\",\"ADR\".\"ADR_EVALUACION_RESULTADO\"
                           FROM \"INTEGRACION\".\"USUARIO_LMS\" AS \"U\"
                           JOIN \"INTEGRACION\".\"V_APRENDIZXDETALLE_RUTA_B\" AS \"ADR\" ON \"U\".\"RGA_ID\" = \"ADR\".\"RGA_ID\"
                           JOIN \"INTEGRACION\".\"RESULTADO_APRENDIZAJE\" AS \"RA\" ON \"ADR\".\"REA_ID\" = \"RA\".\"REA_ID\"
                           WHERE \"ADR\".\"CMP_ID\" = $id_competencia AND \"U\".\"FIC_ID\"= $curso AND \"U\".\"ID_USUARIO_LMS\" $user_id ;");
                                $courses = $sentencia->fetchAll(PDO::FETCH_OBJ);

                                // Recorrido de los datos obtenidos
                                foreach ($courses as $course) {
                                    $documento_user = $course->USR_NUM_DOC;
                                    $fullname = $course->USR_NOMBRE . '' . $course->USR_APELLIDO;
                                    $rea_nombre = $course->REA_NOMBRE;
                                    $rea_evaluacion = $course->ADR_EVALUACION_RESULTADO;
                                ?>
                                    <tr>
                                        <td><?= $fullname; ?></td>
                                        <td><?= $rea_nombre ?></td>
                                        <?php if ($rea_evaluacion == 'A') {
                                            $colorStyle = 'background-color:#BCE2A8;';
                                        } elseif ($rea_evaluacion == 'D') {
                                            $colorStyle = 'background-color: #DF5C73;';
                                        } else {
                                            $colorStyle = 'background-color:#FCE059;';
                                        }
                                        echo "<td style='" . $colorStyle . "'>" . $rea_evaluacion . "</td>"; ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        function showAlert() {
            Swal.fire({
                title: "Esta seguro de querer enviar los datos a SOFIA?",
                footer: 'Nota: Una vez enviada la información, usted NO podra realizar ningun cambio, si desea relizar cambios posterior al envio, favor comunicarse al Soporte para ser atendido',
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: "Enviar",
                denyButtonText: `No enviar cambios`
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire("Enviado!", "", "success");
                } else if (result.isDenied) {
                    Swal.fire("No se realiza cambios en los resultados de aprendizaje", "", "info");
                }
            });
        }
    </script>
    <!-- llamada Footer -->
<?php include '../../footer.php';
} else {
    // Si no hay una sesión iniciada o datos del usuario, redirigir al usuario a otra página
    header("Location: http://localhost/zajuna/");
    exit();
}
?>