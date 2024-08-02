//FUNCION PARA REGRESAR A ZAJUNA
function redirectToZajuna(id_curso) {
  window.location.href = `http://localhost/zajuna/course/view.php?id=${encodeURIComponent(id_curso)}`;
}
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
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE WIKIS
function redirectToWikis(id_curso) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/wikis.php?id=${encodeURIComponent(id_curso)}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE FOROS_AP 
function redirectToForosAp(id_curso, categoria) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/for_ap.php?id=${encodeURIComponent(id_curso)}&cat=${encodeURIComponent(categoria)}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE EVIDENCIAS_AP
function redirectToEvidenciasAp(id_curso, categoria) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/evi_ap.php?id=${encodeURIComponent(id_curso)}&cat=${encodeURIComponent(categoria)}`;
}
//FUNCION PARA ENVIAR PARAMETROS POR URL A LA VISTA DE ACTIVIDADES_AP
function redirectToActividadAp(id_curso, categoria) {
  window.location.href = `http://localhost/lmsActividades/views/actividades/acti_ap.php?id=${encodeURIComponent(id_curso)}&cat=${encodeURIComponent(categoria)}`;
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

// función para mostrar el nombre completo del resultado de aprendizaje
function showFullName(element, fullName, shortName) {
  // Encuentra el <th> contenedor
  const th = element.parentElement;
  // Encuentra el <p> con el nombre corto
  const p = th.querySelector('.nombre_acti');
  // Mostrar el nombre completo
  p.textContent = fullName;
  // Ocultar el botón "Leer más" y mostrar "Leer menos"
  element.style.display = 'none';
  th.querySelector('.readless-btn').style.display = 'inline';
  // Agregar la clase que ajusta el tamaño
  th.classList.add('expanded');
}

// función para ocultar el nombre completo y mostrar el nombre corto
function showShortName(element, shortName, fullName) {
  // Encuentra el <th> contenedor
  const th = element.parentElement;
  // Encuentra el <p> con el nombre corto
  const p = th.querySelector('.nombre_acti');
  // Mostrar el nombre corto con puntos suspensivos
  p.textContent = shortName + '...';
  // Ocultar el botón "Leer menos" y mostrar "Leer más"
  element.style.display = 'none';
  th.querySelector('.readmore-btn').style.display = 'inline';
  // Remover la clase que ajusta el tamaño
  th.classList.remove('expanded');
}

// Inicia el proceso de carga de datos
document.addEventListener('DOMContentLoaded', async () => {
  mostrarSpinner(); // Muestra el spinner inmediatamente
  await obtenerDatosAsync(); // Obtener datos (simulado)
  mostrarTabla(); // Mostrar la tabla cuando los datos estén listos

  // Obtener el codigo de la competencia
  var elemento = document.getElementById("color-titulo-ficha");
  var cod_compentencia = elemento.textContent || elemento.innerText;
  // Obtener el nombre de la competencia
  var name_comp = document.getElementById("color-titulo-nombre");
  var name_compentencia = name_comp.textContent || name_comp.innerText;

  $(document).ready(function () {
    var table = new DataTable("#tabla-act", {
      language: {
        url: "https://cdn.datatables.net/plug-ins/2.0.3/i18n/es-ES.json",
      },
      colReorder: true,
      scrollX: true,
      dom: '<"row"<"col-md-7"B><"col-md-3"f><"col-md-2"l>rtip',
      buttons: [
        //  Boton para exportar archivos en formato Excel
        {
          extend: "excelHtml5",
          text: '<i class="fas fa-file-excel"></i>',
          titleAttr: 'Exportar Excel',
          title: 'CENTRO DE ACTIVIDADES',
          filename: function () {
            var d = new Date();
            var date =
              d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
            return 'Centro de Actividades - FICHA NRO: ' + cod_compentencia + + date;
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
            var date = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " /";
            var time = d.toLocaleString('es-CO', {
              hour: 'numeric',
              minute: 'numeric',
              second: 'numeric',
              hour12: true
            });
            var dateTime = date + ' ' + time;
            var formattedDate = "Documento Generado: " + dateTime;
            var additionalData = "Calificaciones Centro de Actividades - FICHA NRO: " + cod_compentencia + '- NOMBRE FICHA: ' + name_compentencia;

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
          text: '<i class="fas fa-file-csv"></i>',
          titleAttr: 'Exportar Csv',
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
            var date = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " /";
            var time = d.toLocaleString('es-CO', {
              hour: 'numeric',
              minute: 'numeric',
              second: 'numeric',
              hour12: true
            });
            var dateTime = date + ' ' + time;
            var formattedDate = "Documento Generado: " + dateTime;
            var additionalData = "Calificaciones Centro de Actividades - FICHA NRO: " + cod_compentencia + '- NOMBRE FICHA: ' + name_compentencia;

            // Agregar la fecha y el dato adicional como filas en el contenido CSV
            var newCsv = formattedDate + "\n" + additionalData + "\n" + csv;
            return newCsv;
          },
        },
        // Boton para exportar archivos en formato PDF
        {
          extend: "pdfHtml5",
          text: '<i class="fas fa-file-pdf"></i>',
          titleAttr: 'Exportar Pdf',
          title: 'CENTRO DE ACTIVIDADES',
          filename: function () {
            var d = new Date();
            var date =
              d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
            return 'Centro de Actividades' + date;
          },
          // ajuste de pag pdf
          orientation: 'landscape', // Orientación horizontal
          pageSize: 'A2', // Tamaño de la página
          autoWidth: true, // Ajustar automáticamente el ancho de las columnas
          exportOptions: {
            columns: ":visible",
            row: ':all',
            format: {
              header: function (data, columnIndex) {
                // Limpiar etiquetas HTML y limitar a 25 caracteres
                var maxLength = 25;
                var cleanData = data.replace(/<\/?[^>]+(>|$)/g, ""); // Remueve etiquetas HTML
                return cleanData.length > maxLength ? cleanData.substr(0, maxLength) + '...' : cleanData;
              },
              body: function (data, row, column, node) {
                return extractTextFromNode(node);
              }
            },
          },
          customize: function (doc) {
            var d = new Date();
            var date = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " /";
            var time = d.toLocaleString('es-CO', {
              hour: 'numeric',
              minute: 'numeric',
              second: 'numeric',
              hour12: true
            });
            var dateTime = date + ' ' + time;
            var formattedDate = "Documento Generado: " + dateTime;
            var additionalData = "Calificaciones Centro de Actividades - FICHA NRO: " + cod_compentencia + '- NOMBRE FICHA: ' + name_compentencia;

            // Agregar la fecha y el dato adicional como una fila en el contenido del PDF
            doc.content.splice(1, 0, {
              text: formattedDate + "\n" + additionalData,
              margin: [0, 0, 0, 12],
              bold: true
            });
            // Ajustar el tamaño de las celdas si hay problemas de ancho
            doc.styles.tableHeader.fontSize = 10; // Tamaño de fuente más pequeño para el encabezado
            doc.defaultStyle.fontSize = 9; // Tamaño de fuente más pequeño para el cuerpo
            doc.pageMargins = [20, 60, 20, 30]; // Ajusta los márgenes de la página
          },
        },


        {
          extend: "excelHtml5",
          text: '<i class="fas fa-eye"></i> &nbsp;Restaurar Columnas',
          action: function (e, dt, node, config) {
            restoreAllColumns(dt);
          }
        },
      ],

      ordering: false,
      paging: true,
      pageLength: 10,
      lengthMenu: [[10, 15, 25, 50, -1],
      ['10', '15', '25', '50', 'Mostrar todo']],// Opciones de número de filas a mostrar

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
            var span = header.find(".visibility-toggle");

            if (span.length === 0) {
              span = $('<span class="btn ml-2 visibility-toggle" id="ojito"></span>');
              span.on("click", function () {
                var visible = !column.visible();
                column.visible(visible);
                // Cambiar estilo basado en visibilidad
                if (visible) {
                  span.removeClass("column-hidden").addClass("column-visible");
                } else {
                  span.removeClass("column-visible").addClass("column-hidden");
                }
              });

              // Aplicar clase inicial basada en la visibilidad actual de la columna
              if (column.visible()) {
                span.addClass("column-visible");
              } else {
                span.addClass("column-hidden");
              }

              header.append(span);
            }
          });
        }

        // Inicializar los botones de visibilidad
        updateVisibilityButtons();

        // Actualizar los botones de visibilidad después de la reordenación
        api.on('column-reorder', function () {
          // Limpiar y reconstruir los botones de visibilidad
          $(".visibility-toggle").remove();
          updateVisibilityButtons();

          // Ajustar y redibujar la tabla después de la reordenación
          table.columns.adjust().draw();
        });

        // Botón para restaurar todas las columnas
        $('#restore-columns').on('click', function () {
          restoreAllColumns(api);
        });

        $("#container").css("display", "block");
        table.columns.adjust().draw();
      },
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

function restoreAllColumns(tableApi) {
  tableApi.columns().visible(true);

  // Re-inicializar los botones de visibilidad
  updateVisibilityButtons(tableApi);
}

function updateVisibilityButtons(tableApi) {
  // Limpiar y reconstruir los botones de visibilidad
  $(".visibility-toggle").remove(); // Remueve botones antiguos

  tableApi.columns().every(function () {
    var column = this;
    var columnIndex = column.index();

    var header = $(column.header());
    var span = $('<span class="btn ml-2 visibility-toggle" id="ojito"></span>')
      .on("click", function () {
        var visible = !column.visible();
        column.visible(visible);
        // Cambiar estilo basado en visibilidad
        if (visible) {
          span.removeClass("column-hidden").addClass("column-visible");
        } else {
          span.removeClass("column-visible").addClass("column-hidden");
        }
      });

    // Aplicar clase inicial basada en la visibilidad actual de la columna
    if (column.visible()) {
      span.addClass("column-visible");
    } else {
      span.addClass("column-hidden");
    }

    header.append(span);
  });
}

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
