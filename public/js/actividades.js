//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE ACTIVIDADES
function redirectToActividad(id_curso) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/actividades.php?id=${encodeURIComponent(id_curso)}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE FOROS 
function redirectToForos(id_curso) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/foros.php?id=${encodeURIComponent(id_curso)}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE EVIDENCIAS
function redirectToEvidencias(id_curso) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/evidencias.php?id=${encodeURIComponent(id_curso)}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE FOROS_AP 
function redirectToForosAp(id_curso, id_rea) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/for_ap.php?id=${encodeURIComponent(id_curso)}&cat=${encodeURIComponent(id_rea)}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE EVIDENCIAS_AP
function redirectToEvidenciasAp(id_curso, id_rea) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/evi_ap.php?id=${encodeURIComponent(id_curso)}&cat=${encodeURIComponent(id_rea)}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE ACTIVIDADES_AP
function redirectToActividadAp(id_curso, id_rea) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/acti_ap.php?id=${encodeURIComponent(id_curso)}&cat=${encodeURIComponent(id_rea)}`;
}

// FUNCION PARA MOSTRAR EL SPINNER Y OCULTAR LA TABLA 
function mostrarSpinner() {
  document.getElementById('spinner').style.display = 'block';
  document.getElementById('tabla-act').style.display = 'none';
}
// FUNCION PARA MOSTRAR LA TABLA Y OCULTAR EL SPINNER 
function mostrarTabla() {
  document.getElementById('spinner').style.display = 'none';
  document.getElementById('tabla-act').style.display = 'table';
}
// Simula una llamada asíncrona para obtener los datos
function obtenerDatosAsync() {
  return new Promise(resolve => {
    // Simula una demora de  1 segundos
    setTimeout(() => {
      resolve();
    }, 1000);
  });
}

// Inicia el proceso de carga de datos
document.addEventListener('DOMContentLoaded', async () => {
  mostrarSpinner(); // Muestra el spinner inmediatamente
  await obtenerDatosAsync(); // Obtener datos (simulado)
  mostrarTabla(); // Mostrar la tabla cuando los datos estén listos

  $(document).ready(function () {
    var table = new DataTable("#tabla-act", {
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

          exportOptions: {
            columns: ':visible',
            format: {
              body: function (data, row, column, node) {
                return extractTextFromNode(node);
              }
            }
          }
        },
        {
          extend: "csvHtml5",
          text: '<i class="fas fa-file-csv"></i> &nbsp;Exportar CSV',

          exportOptions: {
            columns: ':visible',
            format: {
              body: function (data, row, column, node) {
                return extractTextFromNode(node);
              }
            }
          }
        },
        {
          extend: "pdfHtml5",
          text: '<i class="fas fa-file-pdf"></i> &nbsp;Exportar PDF',

          orientation: 'landscape',
          pageSize: 'A4',
          exportOptions: {
            columns: ':visible',
            format: {
              body: function (data, row, column, node) {
                return extractTextFromNode(node);
              }
            }
          }
        },
      ],

      ordering: false,
      paging: true,
      pageLength: 10,
      select: true,
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
              span = $('<span class="btn  p-2" id="ojito"></span>');
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
      }
    });

    // Función para extraer texto del nodo, eliminando elementos HTML y excluyendo dropdown-menu
    function extractTextFromNode(node) {
      var dropdownMenu = $(node).find('.dropdown-menu');
      if (dropdownMenu.length > 0) {
        return $(node).find('h6').text(); // Extrae solo el texto de la calificación
      }
      return $(node).text().replace(/<\/?[^>]+(>|$)/g, ""); // Remueve etiquetas HTML y obtiene el texto
    }
  });
});


function blockNavigation() {
  // Estado inicial
  var initialState = window.history.state;

  // Función para bloquear la navegación
  function preventNavigation() {
    window.history.pushState(initialState, null, window.location.href);
  }

  // Escuchar el evento popstate para detectar cambios en el historial
  window.addEventListener('popstate', function (event) {
    // Si hay un cambio en el historial, volvemos al estado inicial
    preventNavigation();
  });

  // Bloquear la navegación al cargar la página
  preventNavigation();
}

// Llamar a blockNavigation cuando la página haya cargado
window.onload = function () {
  blockNavigation();
};
