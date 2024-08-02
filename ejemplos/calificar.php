 <?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: http://localhost/zajuna/");
    }
    // llamada header
    include 'header.php';
    require_once 'db_config.php'; // llamar conexion base de datos


    ?>
 <main>
     <div class="container-fluid px-4">
         <h1 class="mt-4">Evaluar Aprendices</h1>
         <div class="card mb-4">
             <div class="card-header">
                 <i class="fas fa-table me-1"></i>
             </div>

             <div class="card-body">
                 <!-- METODO POST A TRAVES DE FORM PARA UPDATE DE LA DATA -->
                 <form method="POST" name="edit_id" id="edit_id" action="actualizar_list.php">
                     <table id="datatablesSimple">
                         <thead>
                             <tr>
                                 <th>DOCUMENTO</th>
                                 <th>USUARIO</th>
                                 <th>RESULTADO APRENDIZAJE</th>
                                 <th>DESCRIPCIÓN</th>
                             </tr>
                         </thead>

                         <tbody>
                             <?php
                                $documento = 0; //campo para el documento del usuario
                                $dato = 6; // id curso, simulacion de ejemplo toma el curso #6 

                                // Consulta para unificacion de tablas y muestra de usuarios
                                $sentencia = $conn->query("select distinct u.id as id_user,u.firstname, u.lastname, mc.id, mc.fullname, mbc.reaprendizaje, mbc.descripcionra
                            FROM mdl_block_califica mbc
                            JOIN mdl_user u on u.id = mbc.userid
                            JOIN mdl_user_enrolments ue ON ue.userid = u.id
                            JOIN mdl_enrol e ON e.id = ue.enrolid
                            JOIN mdl_role_assignments ra ON ra.userid = u.id 
                            JOIN mdl_role r ON r.id = ra.roleid AND r.shortname = 'student'
                            JOIN mdl_course mc ON mc.id = e.courseid
                            WHERE mbc.enrolid = 13 AND mc.id =" . $dato);
                                $courses = $sentencia->fetchAll(PDO::FETCH_OBJ);

                                // Recorrido de los datos obtenidos
                                foreach ($courses as $course) {
                                    $documento = $documento + 1; // SIMULACION VALOR = DOCUMENTO DEL USUARIO
                                    $id_user = $course->id_user;
                                    $name_course = $course->fullname;
                                    $firstname = $course->firstname;
                                    $lastname = $course->lastname;
                                    $reaprendizaje = $course->reaprendizaje;
                                    $observacion = $course->descripcionra;
                                ?>

                                 <tr>

                                     <td><?= $documento; ?></td>
                                     <td><?= $firstname . ' ' . $lastname; ?></td>
                                     <td>
                                         <!-- HABILITAR CAMPO RESULTADO DE APRENDIZAJE -->
                                         <input type="text" name="edit_calificacion[]" id="edit_calificacion" value="<?= $reaprendizaje; ?>">
                                         <input type="hidden" name="user_id[]" value="<?= $id_user; ?>">
                                     </td>
                                     <td>
                                         <!-- HABILITAR CAMPO OBSERVACIÓN -->
                                         <input type="text" name="edit_observacion[]" id="edit_observacion" value="<?= $observacion; ?>">

                                     </td>
                                 </tr>
                             <?php }  ?>
                         </tbody>
                     </table>

                     <div style="text-align:left;">
                         <!-- BOTON PARA MANEJO DE DATA A POSTGRES -->
                         <button type="submit" style="display:flexbox;" class="btn btn-success">Guardar Cambios</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </main>
 <!-- llamada Footer -->
 <?php include 'footer.php';
    ?>