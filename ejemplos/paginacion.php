<?php
// header
include '../header.php';
include_once '../config/sofia_config.php';

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
                                WHERE \"FIC_ID\"= 2966656 and \"CMP_ID\" = 37714 ")->fetchColumn();

// Consulta para obtener los registros de la página actual
$query = $replica->prepare("SELECT * FROM \"INTEGRACION\".vista_vistaap WHERE \"FIC_ID\"= 2966656 and \"CMP_ID\" = 37714 ORDER BY \"USR_NOMBRE\" ASC  LIMIT :limit OFFSET :offset");
$query->bindParam(':limit', $resultados_por_pagina, PDO::PARAM_INT);
$query->bindParam(':offset', $offset, PDO::PARAM_INT);
$query->execute();
$registros = $query->fetchAll(PDO::FETCH_ASSOC);

// Mostrar los resultados
// Mostrar los datos de cada registro
echo "<div>
<div class='card-body' id='resultadoap-card'>
<table class='table'>
<thead>
<tr>
<td>DOCUMENTO</td>
<td>NOMBRE</td>
<td>RESULTADO</td>
</tr>
</thead>
<tbody>";

foreach ($registros as $registro) {
    echo "<tr>
       <td id='numero'>" . $registro['USR_NUM_DOC'] . "</td>
       <td>" . $registro['USR_NOMBRE'] . "</td>
       <td>" . $registro['ADR_EVALUACION_RESULTADO'] . "</td>
     </tr>";
}

echo "</tbody>
</table>";

// Mostrar la paginación
$total_paginas = ceil($total_registros / $resultados_por_pagina);
echo "<br>Páginas: ";
for ($i = 1; $i <= $total_paginas; $i++) {
    echo "<button class='btn btn-success pagina-btn' data-pagina='" . $i . "'>" . $i . "</button>";
}

// footer
include '../footer.php';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $(".pagina-btn").click(function(){
        var pagina = $(this).data("pagina");
        window.location.href = "?pagina=" + pagina;
    });
    
    document.addEventListener("keydown", function(event) {
        if (event.key === "F5") {
            window.location.href = "?pagina=1";
        }
    });
});
</script>
