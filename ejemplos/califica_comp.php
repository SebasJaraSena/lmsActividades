<?php
session_start();

// Verificar si el usuario está autenticado
if (isset($_SESSION['user'])) {

    // Almacenar datos en variables, uno de la sesión, otro de la URL
    $user = $_SESSION['user'];
    $curso = base64_decode($_GET['id_ficha']);
    $encoded_ficha = $_GET['id_ficha'];
    $id_competencia = base64_decode($_GET['id_competencia']);
    $encoded_competencia = $_GET['id_competencia'];
    $rol_user = $user->tipo_user;
    $user_id = $user->userid;
    //llamada header
    include '../../header.php';
    // llamar conexion a bases de datos
    require_once '../../config/sofia_config.php';
?>
    <main>
        <style>
            .loader {
                width: fit-content;
                font-weight: lighter;
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
        <h5 class="p-2 text-center bg-primary text-white">Centro de calificaciones </h5>
        <div class="container-fluid px-4">

            <!-- Boton de reidrección a la pagina anterior  -->
            <div class="container-fluid inline-flex">
                <img src="../../public/assets/img/icno-de-regresar.svg" alt="Ícono de regresar" onclick="redirectToResultados('<?= $encoded_ficha; ?>','<?= $encoded_competencia; ?>')">
                <p>Regresar</p>
            </div>

            <!-- Botón de reidrección a la pagina Actividades  -->
            <div class="container-icono-con-texto d-flex">
                <button type="submit" class="icono-con-texto" name="id_curso" onclick="redirectToActividad('<?= $encoded_ficha; ?>','<?= $encoded_competencia; ?>')">
                    <img src="../../public/assets/img/evaluaciones.svg" alt="Ícono de evaluación" id="icono-evaluacion">
                    <p>Actividades</p>
                </button>
                <button class="icono-con-texto" id="resultadosbutton" onclick="showAlert()">
                    <img src="../../public/assets/img/resultados.svg" alt="Ícono de resultados" id="icono-resultados">
                    <p>Enviar a SOFIA</p>
                </button>
            </div>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">COMPETENCIA DE APRENDIZAJE Codigo: <?php echo ($curso); ?></li>
            </ol>
            <!-- BOTONES PARA DIRECCIONAR A OTRAS PÁGINAS -->

            <div class="card m-4">

                <div class="card-body" id="resultadoap-card" style="display: none;">
                    <form action="../../controllers/actualizar_comp.php" method="POST" id="actualizar_calif">
                        <!-- METODO POST A TRAVES DE FORM PARA UPDATE DE LA DATA -->

                        <table id="resultados_table" class="display" style="width:100%">
                            <thead>
                                <tr id="vistaap-thead">
                                    <th><input type="checkbox" id="selectAllCheckbox"></th>
                                    <th>Documento Identidad</th>
                                    <th>Aprendiz</th>
                                    <th>Calificación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Consulta para unificacion de tablas y muestra de usuarios
                                $sentencia = $replica->query("SELECT * FROM \"INTEGRACION\".historico_competencia WHERE \"FIC_ID\"= $curso  and \"CMP_ID\" = $id_competencia;");
                                $courses = $sentencia->fetchAll(PDO::FETCH_OBJ);

                                // Recorrido de los datos obtenidos
                                foreach ($courses as $course) {
                                    $documento_user = $course->USR_NUM_DOC;
                                    $fullname = $course->USR_NOMBRE . ' ' . $course->USR_APELLIDO;
                                    $rea_evaluacion = $course->ADR_EVALUACION_COMPETENCIA;
                                    $estado_adr = $course->ESTADO_INI;
                                ?>
                                    <tr>
                                        <td><input type="checkbox" class="rowCheckbox" name="usuario[]" value="<?php echo $documento_user; ?>"></td>
                                        <td style="text-align: center;"><?php echo $documento_user ?></td>
                                        <td><?php echo $fullname; ?></td>
                                        <?php
                                        if ($rea_evaluacion == 'A' && $estado_adr == 2) {
                                            $colorStyle = 'background-color:#BCE2A8; pointer-events: none;';
                                           
                                        } elseif ($rea_evaluacion == 'D' && $estado_adr == 2) {
                                            $colorStyle = 'background-color: #DF5C73; pointer-events: none;';

                                        } elseif ($rea_evaluacion == 'A') {
                                            $colorStyle = 'background-color: #BCE2A8;';

                                        } elseif ($rea_evaluacion == 'D') {
                                            $colorStyle = 'background-color: #DF5C73;';

                                        } else {
                                            $colorStyle = 'background-color:#FCE059;';
                                        }
                                        ?>
                                        <td style="<?php echo $colorStyle ?>">
                                        <select id="selectResultado" class="form-select" name="resultado[]" aria-label="Default select example">
                                            <option selected><?php echo $rea_evaluacion?></option>
                                            <option value="A">A</option>
                                            <option value="D">D</option>
                                        </select>
                                        </td>
                                        <!-- pointer-events: none; -->
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <!-- Rest of the form -->
                        <div style="text-align:left;">
                            <!-- BOTON PARA MANEJO DE DATA A POSTGRES -->
                            <button type="button" onclick="submitForm()" class="btn btn-success">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
                <div id="spinner" class="loader" role="status" style="display: none; margin: 0 auto;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        </div>
    </main>

    <script>
        //Funcion del boton que regresa a la vista resultadosap.php y retorna los datos de la url
        function redirectToResultados(encoded_ficha, encoded_competencia) {
            const urlParams = `id_ficha=${encoded_ficha}&id_competencia=${encoded_competencia}`;
            window.location.href = `resultadoap.php?${urlParams}`;
        }
        //Funcion del boton que redirige a la vista actividades.php y retorna los datos de la url
        function redirectToActividad(encoded_ficha, encoded_competencia) {
            window.location.href = `../actividades/actividades.php?id_ficha=${encoded_ficha}&id_competencia=${encoded_competencia}`;
        }

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
        // Función para mostrar el spinner y ocultar la tabla
        function mostrarSpinner() {
            document.getElementById('spinner').style.display = 'block';
            document.getElementById('resultadoap-card').style.display = 'none';
        }

        // Función para mostrar la tabla y ocultar el spinner
        function mostrarTabla() {
            document.getElementById('spinner').style.display = 'none';
            document.getElementById('resultadoap-card').style.display = 'table';
        }

        // Simula una llamada asíncrona para obtener los datos
        function obtenerDatosAsync() {
            return new Promise(resolve => {
                // Simula una demora de  1 segundos
                setTimeout(() => {
                    // Aquí puedes agregar la lógica para obtener tus datos
                    resolve();
                }, 1000);
            });
        }

        // Inicia el proceso de carga de datos
        document.addEventListener('DOMContentLoaded', async () => {
            mostrarSpinner(); // Mostrar spinner mientras se cargan los datos
            await obtenerDatosAsync(); // Obtener datos (simulado)
            mostrarTabla(); // Mostrar la tabla cuando los datos estén listos
            // Inicialización de DataTable
            var table = $('#resultados_table').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.3/i18n/es-ES.json',
                },
                colReorder: true,
                dom: 'Bfrtip',
                buttons: [

                    {
                        extend: 'excelHtml5',
                        text: 'Exportar Excel'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'Exportar Csv'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Exportar Pdf'
                    },
                ],
                order: [[2, 'asc']],
                paging: true,
                pageLength: 15,
            });
            // Función para seleccionar/deseleccionar todas las filas
            $('#selectAllCheckbox').on('change', function() {
                // Obtener todas las filas de la tabla, sin importar la paginación
                var rows = table.rows().nodes();
                // Marcar o desmarcar todos los checkboxes según el estado del checkbox "Seleccionar todo"
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });

            // Manejo de la paginación
            $('#resultados_table').on('page.dt', function() {
                // Deseleccionar el checkbox "Seleccionar todo" cuando cambia la página
                $('#selectAllCheckbox').prop('checked', false);
            });
        });

        // Función para enviar el formulario
        function submitForm() {
            document.getElementById('actualizar_calif').submit();
        }

        function submitForm() {
        // Desactivar la paginación temporalmente
        var table = $('#resultados_table').DataTable();
        var currentPage = table.page();
        table.page('first').draw('page');

        // Recopilar manualmente todas las filas seleccionadas, incluso las que están en otras páginas
        var selectedUsers = [];
        var selectedResults = [];
        $('#resultados_table').DataTable().$('input.rowCheckbox:checked').each(function() {
            selectedUsers.push($(this).val());
            // Obtener el resultado correspondiente a este usuario
            var $row = $(this).closest('tr');
            // Obtener el valor seleccionado del select
            var result = $row.find('#selectResultado').val();
            selectedResults.push(result);
        });

        // Agregar los datos al formulario antes de enviarlo
        $('#actualizar_calif').append('<input type="hidden" name="selected_users" value="' + selectedUsers.join(',') + '">');
        $('#actualizar_calif').append('<input type="hidden" name="selected_results" value="' + selectedResults.join(',') + '">');

        // Reactivar la paginación y volver a la página original
        table.page(currentPage).draw('page');

        // Enviar el formulario
        document.getElementById("actualizar_calif").submit();
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