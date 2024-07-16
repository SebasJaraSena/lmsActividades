// Función para codificar en Base64
function encodeBase64(str) {
  return btoa(str);
}

// Función para decodificar en Base64
function decodeBase64(str) {
  return atob(str);
}

/* Funciòn de redirecciòn la pagina de resultadoap -> compotencias */
function redirectToCompetencias(encoded_curso) {
  window.location.href = `../competencias.php?idnumber=${encoded_curso}`;
}

/* Funciòn de redirecciòn la pagina de resultadoap -> actividades */
function redirectToActi(encoded_curso, encoded_competencia) {
  const urlParams = `id_ficha=${encoded_curso}&id_competencia=${encoded_competencia}`;
  const encodedParams = encodeBase64(urlParams);
  window.location.href = `../actividades/actividades.php?params=${encodedParams}`;
}

/* Funciòn de redirecciòn la pagina de Competencias -> resultadoap */
function redirectComToResultados(encoded_ficha, encoded_competencia) {
  const urlParams = `id_ficha=${encoded_ficha}&id_competencia=${encoded_competencia}`;
  const encodedParams = encodeBase64(urlParams);
  window.location.href = `../views/resultados/resultadoap.php?params=${encodedParams}`;
}

/* Funciòn de redirecciòn la pagina de resultadoap -> resultado */
function redirectToResultado(encoded_curso, encoded_competencia, encode_rea) {
  const urlParams = `curso=${encoded_curso}&id_competencia=${encoded_competencia}&rea_id=${encode_rea}`;
  const encodedParams = encodeBase64(urlParams);
  window.location.href = `../../views/resultados/resultados.php?params=${encodedParams}`;

}

$(document).ready(function () {
  var table = new DataTable("#tabla", {
    language: {
      url: "https://cdn.datatables.net/plug-ins/2.0.3/i18n/es-ES.json",
    },
    colReorder: true,
    scrollX: true,
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> &nbsp;Exportar Excel',

      },
      {
        extend: "csvHtml5",
        text: '<i class="fas fa-file-csv"></i> &nbsp;Exportar Csv',

      },
      {
        extend: "pdfHtml5",
        text: '<i class="fas fa-file-pdf"></i> &nbsp;Exportar PDF',

      },
    ],
    ordering: false,
    paging: true,
    pageLength: 10, // # de datos por pagina
    select: true, // Habilita la selección de filas

    initComplete: function () {
      var api = this.api();
      var columnVisibility = {};

      // Función para actualizar los botones de visibilidad
      function updateVisibilityButtons() {
        api.columns().every(function () {
          var column = this;
          var columnIndex = column.index();

          if (!columnVisibility.hasOwnProperty(columnIndex)) {
            columnVisibility[columnIndex] = column.visible();
          }

          var header = $(column.header());
          var span = header.find(".btn");

          if (span.length === 0) {
            span = $('<span class="btn p-2" id="ojito"></span>');
            span.on("click", function () {
              columnVisibility[columnIndex] = !columnVisibility[columnIndex];
              api.column(columnIndex).visible(columnVisibility[columnIndex]);
            });
            header.append(span);
          }
        });
      }
      // Inicializar los botones de visibilidad
      updateVisibilityButtons();

      // Actualizar los botones de visibilidad después de la reordenación
      api.on('column-reorder', function () {
        // Limpiar y reconstruir los botones de visibilidad
        $(".btn").remove();
        updateVisibilityButtons();

        // Ajustar y redibujar la tabla después de la reordenación
        table.columns.adjust().draw();
      });

      $("#container").css("display", "block");
      table.columns.adjust().draw();
    },

  });

});




