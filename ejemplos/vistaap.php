<?php
session_start();

// Verificar si el usuario está autenticado
if (isset($_SESSION['user'])) {

    // Almacenar datos en variables, uno de la sesión, otro de la URL
    $user = $_SESSION['user'];
    $curso = base64_decode($_GET['id_ficha']);
    $encoded_curso = $_GET['id_ficha'];
    $id_competencia = base64_decode($_GET['id_competencia']);
    $encoded_competencia = ($_GET['id_competencia']);
    $rol_user = $user->tipo_user;
    $user_id = $user->userid;
    
    //llamada header
    include '../../header.php';
    // llamar conexion a bases de datos
    require_once '../../config/sofia_config.php';

   ?>
<main>



<h5 class="p-2 text-center bg-primary text-white">Centro de calificaciones</h5>
<!-- boton regresar  -->

<head>
    <style>
        .hidden-div {
            display: none;
        }

        table.dataTable.dataTable_width_auto {
            width: auto;
        }
     
        .dt-paging {
        display: none;
        }

    </style>
</head>

<div class="container-fluid px-4">
    <div class=" hidden-div container-fluid inline-flex">
        <img src="../../public/assets/img/icno-de-regresar.svg" alt="Ícono de regresar" id="back-button">
    </div>

    <div class="container-fluid inline-flex">
        <img src="../../public/assets/img/icno-de-regresar.svg" alt="Ícono de regresar" onclick="redirectToCompetencias('<?= $encoded_curso; ?>')">
        <p>Regresar</p>
        <script>
            function redirectToCompetencias(encoded_curso) {
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
    </div>

    <div class="card m-4">
        <div class="card-body" id="resultadoap-card">
            <p class="card-text">
            </p><?= 'CATEGORIA COMPETENCIA: ' . $id_competencia;  ?></p>
            <?php
            $rol_user = $user->tipo_user;
            // PERMISO PARA ROL DE INSTRUCTOR (TIPO DE USUARIO = 3 EN REPLICA)  
            if ($rol_user == 3) {
                // Número de resultados por página
                $resultados_por_pagina = 10;

                // Página actual
                if (isset($_GET['pagina'])) {
                    $pagina_actual = $_GET['pagina'];
                } else {
                    $pagina_actual = 1;
                }

                // Calcular el offset
                $offset = ($pagina_actual - 1) * $resultados_por_pagina;

                // Consulta para obtener el total de registros
                $total_registros = $replica->query("SELECT COUNT(*) FROM \"INTEGRACION\".vista_vistaap
                    WHERE \"FIC_ID\"= $curso  and \"CMP_ID\" = $id_competencia ")->fetchColumn();

                // Consulta para obtener los registros de la página actual
                $query = $replica->prepare("SELECT * FROM \"INTEGRACION\".vista_vistaap WHERE \"FIC_ID\"= $curso  and \"CMP_ID\" = $id_competencia ORDER BY \"USR_NOMBRE\" ASC  LIMIT :limit OFFSET :offset");
                $query->bindParam(':limit', $resultados_por_pagina, PDO::PARAM_INT);
                $query->bindParam(':offset', $offset, PDO::PARAM_INT);
                $query->execute();
                $registros = $query->fetchAll(PDO::FETCH_ASSOC);

                // Mostrar los resultados
                // Mostrar los datos de cada registro
                echo "
                    <table class='table' id='mitabla'>
                    <thead>
                        <tr id='vistaap-thead'>
                            <th>DOCUMENTO</th>
                            <th>Nombre</th>
                            <th>RESULTADO</th>
                        </tr>
                    </thead>
                    <tbody>";

                    foreach ($registros as $registro) {
                    echo "<tr>
                    <td class='text-center'>" . $registro['USR_NUM_DOC'] . "</td>
                    <td class='text-center'>" . $registro['USR_NOMBRE'] . "</td>
                    <td class='text-center'>" . $registro['ADR_EVALUACION_RESULTADO'] . "</td>
                    </tr>";
                    }
                    echo "</tbody>
                    </table>";

                // Mostrar la paginación
                $total_paginas = ceil($total_registros / $resultados_por_pagina);
                echo "<br>Páginas: ";
                for ($i = 1; $i <= $total_paginas; $i++) {
                    echo "<a class='btn btn-success' href='?id_ficha=$encoded_curso&id_competencia=$encoded_competencia&pagina=" . $i . "'>" . $i . "</a>";
                }; ?>

            <?php }
            // PERMISO PARA ROL DE APRENDIZ (TIPO DE USUARIO = 5 EN REPLICA)
            if ($rol_user ==  5) { ?>

                <table id="tabla-act" class="table table-striped">
                    <thead>
                        <tr id="resultados-thead2">

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
                    WHERE \"ADR\".\"CMP_ID\" = $id_competencia AND \"U\".\"FIC_ID\"= $curso AND \"U\".\"ID_USUARIO_LMS\" = $user_id;");
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
                                <td><?= $rea_evaluacion ?></td>
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
    $(document).ready(function() {
        var table = new DataTable('#mitabla', {
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.0.3/i18n/es-ES.json',
        },
        colReorder: true,
        responsive: true,
        dom: 'Bfrtip',
            buttons: [
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ],
        paging: false,
        select: true, // Habilita la selección de filas
        
        initComplete: function () {
            var api = this.api();
            api.columns().every(function () {
                var column = this;
                var columnIndex = column.index();
    
                var span = document.createElement('span'); // Crea un botón
                span.innerHTML = 'Ocultar'; // Texto del botón
                span.className = 'btn btn-success'; // Clases CSS para estilo
    
                // Agregar evento click al botón
                span.addEventListener('click', function () {
                    column.visible(!column.visible());
                });
    
                // Añadir el botón al encabezado de la columna
                $(column.header()).append(span);
            });
          
            $('#container').css('display', 'block'); // Asegúrate de que el contenedor esté visible
            table.columns.adjust().draw()
        }
    });
         
    });
</script>

    <!-- llamada Footer -->
<?php include '../../footer.php';
} else {
    // Si no hay una sesión iniciada o datos del usuario, redirigir al usuario a otra página
    header("Location: http://localhost/zajuna/");
    exit();
}
?>