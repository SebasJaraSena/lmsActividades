<?php

session_start();
$id_url_curso = $_GET['idnumber'];
$encrypted_curso_id = base64_encode($id_url_curso);

/* select distinct inf.* from "INTEGRACION"."V_INSTRUCTORXFICHA_B" INF inner join "INTEGRACION"."V_REGISTRO_ACADEMICO_B" RGA on RGA."FIC_ID" = inf."FIC_ID" 
inner join "INTEGRACION"."V_APRENDIZXDETALLE_RUTA_B" RESUL on RESUL."RGA_ID" = RGA."RGA_ID" 
where resul."CMP_ID" = 37714 and RGA."FIC_ID" = 2964949; */

header("Location: ../views/competencias.php?idnumber=$id_url_curso");