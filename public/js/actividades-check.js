//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE ACTIVIDADES
function redirectToActividad(id_curso) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/actividades.php?id=${encodeURIComponent(
    id_curso
  )}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE FOROS
function redirectToForos(id_curso) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/foros.php?id=${encodeURIComponent(
    id_curso
  )}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE EVIDENCIAS
function redirectToEvidencias(id_curso) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/evidencias.php?id=${encodeURIComponent(
    id_curso
  )}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE WIKIS
function redirectToWikis(id_curso) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/wikis.php?id=${encodeURIComponent(
    id_curso
  )}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE FOROS_AP
function redirectToForosAp(id_curso, id_rea) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/for_ap.php?id=${encodeURIComponent(
    id_curso
  )}&cat=${encodeURIComponent(id_rea)}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE EVIDENCIAS_AP
function redirectToEvidenciasAp(id_curso, id_rea) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/evi_ap.php?id=${encodeURIComponent(
    id_curso
  )}&cat=${encodeURIComponent(id_rea)}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE ACTIVIDADES_AP
function redirectToActividadAp(id_curso, id_rea) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/acti_ap.php?id=${encodeURIComponent(
    id_curso
  )}&cat=${encodeURIComponent(id_rea)}`;
}

// FUNCION PARA MOSTRAR EL SPINNER Y OCULTAR LA TABLA
function mostrarSpinnerCheck() {
  document.getElementById("spinner-check").style.display = "block";
  document.getElementById("tabla-act-check").style.display = "none";
}
// FUNCION PARA MOSTRAR LA TABLA Y OCULTAR EL SPINNER
function mostrarTablaCheck() {
  document.getElementById("spinner-check").style.display = "none";
  document.getElementById("tabla-act-check").style.display = "table";
}
// Simula una llamada asíncrona para obtener los datos
function obtenerDatosAsyncCheck() {
  return new Promise((resolve) => {
    // Simula una demora de  1 segundos
    setTimeout(() => {
      resolve();
    }, 1000);
  });
}
//***************************************************
// Inicia el proceso de carga de datos
document.addEventListener("DOMContentLoaded", async () => {
  // Muestra el spinner y obtiene los datos
  mostrarSpinnerCheck();
  await obtenerDatosAsyncCheck();
  mostrarTablaCheck();

  $(document).ready(function () {
    var table = new DataTable("#tabla-act-check", {
      language: {
        url: "https://cdn.datatables.net/plug-ins/2.0.3/i18n/es-ES.json",
      },
      colReorder: false,
      scrollX: true,
      dom: "Bfrtip",
      colReorder: true,
      ordering: false,
      paging: true,
      pageLength: 10,
      lengthMenu: [
        [10, 15, 25, 50, -1],
        ["10", "15", "25", "50", "Mostrar todo"],
      ], // Opciones de número de filas a mostrar
      buttons: [
        //  Boton para exportar archivos en formato Excel
        {
          extend: "excelHtml5",
          text: '<i class="fas fa-file-excel"></i> &nbsp;Exportar Excel',
          title: 'CENTRO DE ACTIVIDADES',
          filename: function () {
            var d = new Date();
            var date =
              d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
            return 'Centro de Actividades ' + date;
          },
          exportOptions: {
            columns: ":visible",
            format: {
              body: function (data, row, column, node) {
                return extractTextFromNode(node);
              }
            },
          },
          customize: function (xlsx) {
            var sheet = xlsx.xl.worksheets["sheet1.xml"];
            var d = new Date();
            var date = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
            var time = d.toLocaleString('es-CO', {
              hour: 'numeric',
              minute: 'numeric',
              second: 'numeric',
              hour12: true
            });
            var dateTime = date + ' ' + time;
            var formattedDate = "Documento Generado: " + dateTime;
            var additionalData = "Calificaciones Centro de Actividades";

            // Crear una nueva fila con la fecha y el dato adicional
            var newRow = '<row r="1"><c t="inlineStr" r="A1"><is><t>' + formattedDate + ' - ' + additionalData + '</t></is></c></row>';


            // Ajustar los índices de las filas existentes
            $('row', sheet).each(function () {
              var r = parseInt($(this).attr('r'));
              $(this).attr('r', r + 1);
              $('c', this).each(function () {
                var ref = $(this).attr('r');
                var col = ref.substring(0, 1);
                var row = parseInt(ref.substring(1)) + 1;
                $(this).attr('r', col + row);
              });
            });

            // Insertar la nueva fila al principio del archivo Excel
            sheet.childNodes[0].childNodes[1].innerHTML = newRow + sheet.childNodes[0].childNodes[1].innerHTML;

            // Añadir estilo (negrilla) a las celdas (s="1" referencia al estilo en la hoja de estilos)
            var styleSheet = xlsx.xl['styles.xml'];
            var cellXfs = $('cellXfs', styleSheet);
            cellXfs.append('<xf xfId="0" applyFont="1" fontId="1"/>');
            var fonts = $('fonts', styleSheet);
            fonts.append('<font><b/><sz val="11"/><color rgb="000000"/><name val="Calibri"/></font>');
          },
        },
        //  Boton para exportar archivos en formato CSV
        {
          extend: "csvHtml5",
          text: '<i class="fas fa-file-csv"></i> &nbsp;Exportar Csv',
          title: 'CENTRO DE ACTIVIDADES',
          filename: function () {
            var d = new Date();
            var date =
              d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
            return 'Centro de Actividades ' + date;
          },
          exportOptions: {
            columns: ":visible",
            format: {
              body: function (data, row, column, node) {
                return extractTextFromNode(node);
              }
            },
          },
          customize: function (csv) {
            var d = new Date();
            var date = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
            var time = d.toLocaleString('es-CO', {
              hour: 'numeric',
              minute: 'numeric',
              second: 'numeric',
              hour12: true
            });
            var dateTime = date + ' ' + time;
            var formattedDate = "Documento Generado: " + dateTime;
            var additionalData = "Calificaciones Centro de Actividades";

            // Agregar la fecha y el dato adicional como filas en el contenido CSV
            var newCsv = formattedDate + "\n" + additionalData + "\n" + csv;
            return newCsv;
          },
        },
        //  Boton para exportar archivos en formato PDF
        {
          extend: "pdfHtml5",
          text: '<i class="fas fa-file-pdf"></i> &nbsp;Exportar Pdf',
          title: 'CENTRO DE ACTIVIDADES',
          filename: function () {
            var d = new Date();
            var date =
              d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
            return 'Centro de Actividades' + date;
          },
          exportOptions: {
            columns: ":visible",
            format: {
              body: function (data, row, column, node) {
                return extractTextFromNode(node);
              }
            },
          },
          customize: function (doc) {
            var d = new Date();
            var date = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
            var time = d.toLocaleString('es-CO', {
              hour: 'numeric',
              minute: 'numeric',
              second: 'numeric',
              hour12: true
            });
            var dateTime = date + ' ' + time;
            var formattedDate = "Documento Generado: " + dateTime;
            var additionalData = "Calificaciones Centro de Actividades";

            // Agregar la fecha y el dato adicional como una fila en el contenido del PDF
            doc.content.splice(1, 0, {
              text: formattedDate + "\n" + additionalData,
              margin: [0, 0, 0, 12],
              bold: true
            });
          },
        },
        //  Boton para envio de correos seleccionados
        {
          extend: "pdfHtml5",
          text: '<i class="fas fa-paper-plane"></i> &nbsp;Enviar Emails',
          action: function (e, dt, node, config) {
            enviarCorreosSeleccionados();
          },
        },
      ],

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
        api.on("column-reorder", function () {
          // Limpiar y reconstruir los botones de visibilidad
          $(".btn").remove();
          updateVisibilityButtons();

          // Ajustar y redibujar la tabla después de la reordenación
          table.columns.adjust().draw();
        });

        $("#container").css("display", "block");
        table.columns.adjust().draw();

        // Seleccionar/Deseleccionar todos los checkboxes
        $("#select_all").on("click", function () {
          var allRows = table.rows().nodes(); // Selecciona todas las filas, incluidas las no visibles
          $('input[type="checkbox"]', allRows).prop("checked", this.checked);
          actualizarContador();
        });

        // Actualizar "Seleccionar todos" si se selecciona/desselecciona un checkbox
        $("#tabla-act-check tbody").on("change", 'input[type="checkbox"]', function () {
          if (!this.checked) {
            var el = $("#select_all").get(0);
            if (el && el.checked && "indeterminate" in el) {
              el.indeterminate = true;
            }
          }
          actualizarContador();
        });
      },
    });

    // Función para actualizar el contador de elementos seleccionados
    function actualizarContador() {
      var totalSeleccionados = table
        .rows()
        .nodes()
        .to$()
        .find('input[type="checkbox"]:checked').length;
      $("#contador").text(totalSeleccionados);
    }

    // Función para enviar correos seleccionados
    function enviarCorreosSeleccionados() {
      var correosSeleccionados = [];

      // Recorremos todas las filas de la tabla, no solo las visibles
      table.rows().nodes().to$().each(function () {
        var checkbox = $(this).find('input[type="checkbox"]');
        if (checkbox.prop("checked")) {
          correosSeleccionados.push(checkbox.val());
        }
      });

      // Mostrar advertencia antes de enviar correos
      Swal.fire({
        title: '¿Estás seguro?',
        text: `Se enviarán correos a ${correosSeleccionados.length} destinatarios.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          var emailForm = document.getElementById("emailForm");
          
           // Añadir los correos seleccionados a un campo oculto del formulario
           var inputCorreos = document.createElement("input");
           inputCorreos.type = "hidden";
           inputCorreos.name = "correosSeleccionados";
           inputCorreos.value = JSON.stringify(correosSeleccionados);
           emailForm.appendChild(inputCorreos);
 
           // Capturar los valores ocultos de redireccion, id_curso, id_rea
           var redireccion = document.querySelector('input[name="redireccion"]').value;
           var id_curso = document.querySelector('input[name="id_curso"]').value;
           var id_rea = document.querySelector('input[name="id_rea"]').value;
 
           var inputRedireccion = document.createElement("input");
           inputRedireccion.type = "hidden";
           inputRedireccion.name = "redireccion";
           inputRedireccion.value = redireccion;
           emailForm.appendChild(inputRedireccion);
 
           var inputIdCurso = document.createElement("input");
           inputIdCurso.type = "hidden";
           inputIdCurso.name = "id_curso";
           inputIdCurso.value = id_curso;
           emailForm.appendChild(inputIdCurso);
 
           var inputIdRea = document.createElement("input");
           inputIdRea.type = "hidden";
           inputIdRea.name = "id_rea";
           inputIdRea.value = id_rea;
           emailForm.appendChild(inputIdRea);

          // Enviar el formulario
          emailForm.submit();
        }
      });
    }

    // Inicialización de la tabla y otros eventos...
    function updateVisibilityButtons() {
      var api = table.api();
      var columnVisibility = {};

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

    // Función para extraer texto del nodo, eliminando elementos HTML y excluyendo dropdown-menu
    function extractTextFromNode(node) {
      var dropdownMenu = $(node).find(".dropdown-menu");
      if (dropdownMenu.length > 0) {
        return $(node).find("h6").text(); // Extrae solo el texto de la calificación
      }
      return $(node)
        .text()
        .replace(/<\/?[^>]+(>|$)/g, ""); // Remueve etiquetas HTML y obtiene el texto
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
  window.addEventListener("popstate", function (event) {
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
