/*!
    * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2023 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
// 
// Scripts

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

    document.getElementById('back-button-zajuna').addEventListener('click', function () {
        window.location.href = 'http://localhost/zajuna/my/courses.php';
    });


    // -----------------------------FIN AJUSTES TABLAS-----------------------------------------------------------

    /*cambiará el color del texto a verde cuando el cursor esté sobre los enladdEventListeneraces y 
        volverá al color predeterminado cuando se retire el cursor. 
        ubicacion en header de navbar-brand ps-4.*/
    const links = document.querySelectorAll('.navbar-brand a');


    links.forEach(link => {
        link.addEventListener('mouseenter', () => {
            link.style.color = '#39a900';
        });


        link.addEventListener('mouseleave', () => {
            link.style.color = ''; // Revertir al color predeterminado
        });
    });
    /*cambiará el color del texto a verde cuando el cursor esté sobre los enlaces y 
    volverá al color predeterminado cuando se retire el cursor. 
    ubicacion en id="zajuna-link" class="navbar-brand ps-5"*/
    const zajunaLink = document.getElementById('zajuna-link');

    zajunaLink.addEventListener('mouseenter', () => {
        zajunaLink.style.color = '#39a900';
    });


    zajunaLink.addEventListener('mouseleave', () => {
        zajunaLink.style.color = ''; // Revertir al color predeterminado
    });
});

// FUNCION QUE EVALUA SI EL NAVEGADOR ES FIREFOX NO IMPRIME MENSAJE DE RECOMENDACION
$(document).ready(function () {
    var isFirefox = typeof InstallTrigger !== 'undefined';

    if (!isFirefox) {
        $('#browser-alert').show();
    }
});


$(document).ready(function () {
    var table = new DataTable("#tabla_ap", {
      language: {
        url: "https://cdn.datatables.net/plug-ins/2.0.3/i18n/es-ES.json",
      },
      colReorder: true,
      scrollX: true,
      dom: "Bfrtip",
      buttons: [
       
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
  
     // Ajustar y redibujar la _ap después de la reordenación
     table.columns.adjust().draw();
   });
  
   $("#container").css("display", "block");
   table.columns.adjust().draw();
  },
      
    });
    
  });
