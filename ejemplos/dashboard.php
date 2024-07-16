<?php
session_start();

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $id_curso = $_GET['idnumber'];

    include 'header.php';
    require_once 'db_config.php';
    ?>

<main>
    <h5 class="p-2 text-center bg-primary text-white">Centro de calificaciones</h5>

    <div class="container-fluid inline-flex">
        <img src="public/assets/img/icno-de-regresar.svg" alt="Ícono de regresar" id="back-button">
        <p>Regresar</p>
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">
            <strong>Bienvenido</strong>
            <?php echo $user->firstname . ' ' . $user->lastname; ?>
        </li>
    </ol>


    <div class="card-body m col-12 container text-center">

                <div class="card-group row">

                    <!-- GRAFICA DE ACTIVIDADES -->
                        <div class="m col rounded" style=" box-shadow: 0px 0px 30px gray; padding: 2em;">

                             <div class="card-body">
                             <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="icono-con-texto" name ="id_curso" onclick="redirectToActividad('<?=$id_curso;?>')">
                                <img src="public/assets/img/evaluaciones.svg" alt="Ícono de evaluación" id="icono-evaluacion">
                                <p>Actividades</p>
                                </button>

                                <script>function redirectToActividad(curso, categoria) {window.location.href = `actividades.php?id_curso=${curso}&categoria=${categoria}`;}</script>

                                </div>
                                     <h3>Actividades realizadas en Zajuna </h3>
                    <?php
$labels = [];
    $data = [];

    $curso = $id_curso;
    $titulos = $conn->query("SELECT name, id FROM mdl_quiz WHERE course= " . $curso);
    $actividades = $titulos->fetchAll(PDO::FETCH_OBJ);

    foreach ($actividades as $actividad) {
        $consulta = $conn->query("SELECT COUNT(id) as count FROM mdl_quiz_grades WHERE quiz = " . $actividad->id);

        if ($consulta) {
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                $conteo = $resultado['count'];
                $data[] = $conteo;
                $labels[] = $actividad->name . ': Cantidad de aprendices: ' . $conteo;
            } else {
                echo json_encode(array('error' => 'No se encontraron resultados.'));
            }
        } else {
            echo json_encode(array('error' => 'Error al ejecutar la consulta.'));
        }
    }
    ?>
                    <!-- imprime la grafica -->
                    <canvas id="myChart" width="350" height="350"></canvas>
                </div>
            </div>

               <!-- GRAFICA DE EVIDENCIAS -->
               <div class="m col" style=" box-shadow: 0px 0px 30px gray; padding: 2em;">
                                <div class="card-body">

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button class="icono-con-texto" onclick="redirectToEvidencias('<?=$id_curso;?>')">
                                    <img src="public/assets/img/evidencias.svg" alt="Ícono de evidencias" id="icono-evidencias">
                                    <p>Evidencias</p>
                                </button>
</div>
                                <script>function redirectToEvidencias(id_curso) {window.location.href = `evidencias.php?id_curso=${id_curso}`;}</script>

                                    <h3>Evidencias realizadas en Zajuna</h3>
                    <?php
$labelsE = [];
    $dataE = [];

    $curso = $id_curso;
    $titulosE = $conn->query("SELECT name, id FROM mdl_assign WHERE course= " . $curso);
    $actividadesE = $titulosE->fetchAll(PDO::FETCH_OBJ);

    foreach ($actividadesE as $actividadE) {
        $consultaE = $conn->query("SELECT COUNT(id) as count FROM mdl_assign_grades WHERE assignment = " . $actividadE->id);

        if ($consultaE) {
            $resultadoE = $consultaE->fetch(PDO::FETCH_ASSOC);

            if ($resultadoE) {
                $conteo = $resultadoE['count'];
                $dataE[] = $conteo;
                $labelsE[] = $actividadE->name . ': Cantidad de aprendices: ' . $conteo;
            } else {
                echo json_encode(array('error' => 'No se encontraron resultados.'));
            }
        } else {
            echo json_encode(array('error' => 'Error al ejecutar la consulta.'));
        }
    }
    ?>
                    <!-- imprime la grafica -->
                    <canvas id="myChart2" width="350" height="350"></canvas>
                </div>
            </div>

  <!-- GRAFICA DE FOROS -->
  <div class="m col " style=" box-shadow: 0px 0px 30px gray; padding: 2em;">
                                <div class="card-body">


                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                  <button class="icono-con-texto" onclick="redirectToForos('<?=$id_curso;?>')">
                                    <img src="public/assets/img/foros.svg" alt="Ícono de foros" id="icono-foros">
                                     <p>Foros</p>
                                  </button>
                                </div>
                                <script>function redirectToForos(id_curso) {window.location.href = `foros.php?id_curso=${id_curso}`;}</script>

                                    <h3>Foros realizadas en Zajuna</h3>
                    <?php
$labelsF = [];
    $dataF = [];

    $curso = $id_curso;
    $titulosF = $conn->query("SELECT i.id, i.courseid, i.itemname, COUNT(g.itemid) as count_rawgrade
                                    FROM mdl_grade_items i
                                    JOIN mdl_grade_grades g ON g.itemid = i.id
                                    JOIN mdl_forum f ON f.id = i.iteminstance
                                    WHERE i.courseid = $curso
                                    AND i.itemmodule = 'forum'
                                    AND g.rawgrade IS NOT NULL
                                    GROUP BY i.id, i.courseid, i.itemname
                                ");
    $actividadesF = $titulosF->fetchAll(PDO::FETCH_OBJ);

    foreach ($actividadesF as $actividadF) {
        $consultaF = $conn->query("SELECT COUNT(g.itemid) as count_rawgrade
                                        FROM mdl_grade_items i
                                        JOIN mdl_grade_grades g ON g.itemid = i.id
                                        JOIN mdl_forum f ON f.id = i.iteminstance
                                        WHERE i.courseid = $curso
                                        AND i.iteminstance = f.id
                                        AND i.itemmodule = 'forum'
                                        AND g.rawgrade IS NOT NULL
                                        GROUP BY i.id, i.courseid, i.itemname");

        if ($consultaF) {
            $resultadoF = $consultaF->fetch(PDO::FETCH_ASSOC);

            if ($resultadoF) {
                $conteo = $resultadoF['count_rawgrade'];
                $dataF[] = $conteo;
                $labelsF[] = $actividadF->itemname . ': Cantidad de aprendices: ' . $conteo;
            } else {
                echo json_encode(array('error' => 'No se encontraron resultados.'));
            }
        } else {
            echo json_encode(array('error' => 'Error al ejecutar la consulta.'));
        }
    }
    ?>
                    <!-- imprime la grafica -->
                    <canvas id="myChart3" width="350" height="350"></canvas>
                </div>
            </div>

                <!-- LISTA DE aprendices -->
                        <div class="m col rounded" style=" box-shadow: 0px 0px 30px gray; padding: 2em;">
                                <div class="card-body">
                                <h3>LISTA DE APRENDICES</h3>
                <div class="card-body ">
                    <table id="example2" class="display" style="width:100%; font-size: 0.75em;">
                        <thead>
                            <tr id="resultados-thead">
                                <th>Aprendices</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
$user_query = $conn->query("SELECT distinct u.id, u.firstname, u.lastname, e.courseid, r.shortname
                                            FROM  mdl_user u
                                            JOIN mdl_user_enrolments ue ON ue.userid = u.id
                                            JOIN mdl_enrol e ON e.id = ue.enrolid
                                            JOIN mdl_role_assignments ra ON ra.userid = u.id
                                            JOIN mdl_course mc ON mc.id = e.courseid
                                            JOIN mdl_role r ON r.id = ra.roleid
                                            WHERE mc.id = " . $curso . " AND r.shortname = 'student'");
    $users = $user_query->fetchAll(PDO::FETCH_OBJ);

    foreach ($users as $user) {
        $id_user = $user->id;
        $firstname = $user->firstname;
        $lastname = $user->lastname;

        ?>
                                <tr>
                                    <td><?=$firstname . ' ' . $lastname;?></td>
                                </tr>
                            <?php
}?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ALMACENAMIENTO CON LOCALSTORAGE-->
    <script>
                                    $(document).ready(function() {
                                        // Comprobar si hay un orden de columna guardado en el almacenamiento local
                                        var columnOrder = localStorage.getItem('columnOrder');
                                        if (columnOrder) {
                                            // Inicializar la tabla DataTable con el orden de columna guardado
                                            var table = $('#example2').DataTable({
                                            colReorder: true,
                                            colVis: true,
                                            language: {
                                             url: 'https://cdn.datatables.net/plug-ins/2.0.3/i18n/es-ES.json',
                                             },
                                            "columnOrder": JSON.parse(columnOrder)
                                            });
                                        } else {
                                            // Inicializar la tabla DataTable sin un orden de columna guardado
                                            var table = $('#example2').DataTable({
                                            colReorder: true,
                                            language: {
                                            url: 'https://cdn.datatables.net/plug-ins/2.0.3/i18n/es-ES.json',
                                                 },
                                            colVis: true
                                            });
                                        }

                                        var columnBeingDragged = null;


                                        $('#example2 thead').on('mousedown', 'th', function(e) {
                                            if ($(e.target).is('th')) {
                                            // Obtén el índice de la columna arrastrada
                                            columnBeingDragged = table.colReorder.order()[0];
                                            if (table.colReorder) {
                                                table.colReorder.start(columnBeingDragged, e.pageX, e.pageY);
                                            }
                                            }
                                        });

                                        // Restaurar el orden de las columnas desde el almacenamiento local cuando la página se carga
                                        $(document).ready(function() {
                                            var columnOrder = localStorage.getItem('columnOrder');
                                            if (columnOrder) {
                                            table.colReorder.order(JSON.parse(columnOrder));
                                            }
                                        });

                                        // Guardar el orden de las columnas en el almacenamiento local cuando cambia el orden
                                        table.on('order', function(e, settings, details) {
                                            localStorage.setItem('columnOrder', JSON.stringify(table.colReorder.order()));
                                        });
                                    });
                                </script>
    <script>
        function randomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        var backgroundColorsA = [];
        var backgroundColorsE = [];
        var backgroundColorsF = [];
        for (var i = 0; i < <?php echo count($labels); ?>; i++) {
            backgroundColorsA.push(randomColor());
            backgroundColorsE.push(randomColor());
            backgroundColorsF.push(randomColor());
        }

        var labels = <?php echo json_encode($labels); ?>;
        var data = <?php echo json_encode($data); ?>;
        var labelsE = <?php echo json_encode($labelsE); ?>;
        var dataE = <?php echo json_encode($dataE); ?>;
        var labelsF = <?php echo json_encode($labelsF); ?>;
        var dataF = <?php echo json_encode($dataF); ?>;

        var ctx1 = document.getElementById('myChart').getContext('2d');
        var ctx2 = document.getElementById('myChart2').getContext('2d');
        var ctx3 = document.getElementById('myChart3').getContext('2d');

        var myChart = new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'aprendices que ya realizaron la actividad: ',
                    data: data,
                    backgroundColor: backgroundColorsA,
                    borderColor: backgroundColorsA,
                    borderWidth: 1,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: false
            }
        });

        var myChart2 = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: labelsE,
                datasets: [{
                    label: 'aprendices que ya realizaron la evidencia: ',
                    data: dataE,
                    backgroundColor: backgroundColorsE,
                    borderColor: backgroundColorsE,
                    borderWidth: 1,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: false
            }
        });

        var myChart3 = new Chart(ctx3, {
            type: 'pie',
            data: {
                labels: labelsF,
                datasets: [{
                    label: 'aprendices que ya participaron en el foro: ',
                    data: dataF,
                    backgroundColor: backgroundColorsF,
                    borderColor: backgroundColorsF,
                    borderWidth: 1,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: false
            }
        });
    </script>
</main>

<?php include 'footer.php';
} else {
    header("Location: http://localhost/zajuna/");
    exit();
}
?>