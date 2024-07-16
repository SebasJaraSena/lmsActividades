function updateHiddenField(selectElement) {
  var hiddenField = selectElement.nextElementSibling;
  hiddenField.value = selectElement.value;
}

$(document).ready(function () {
  var table = $("#resultados_table").DataTable({
    language: {
      url: "https://cdn.datatables.net/plug-ins/2.0.3/i18n/es-ES.json",
    },
    colReorder: true,
    columnDefs: [{ className: "dt-center", targets: [0, 2],
      
     }],
    dom: "Bfrtip",
    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> &nbsp;Exportar Excel',
        exportOptions: {
          columns: ":visible",
          format: {
            body: function (data, row, column, node) {
              var selectElement = $(node).find("select");
              if (selectElement.length > 0) {
                return $(node).find(".selected-resultado").val();
              }
              var checkboxElement = $(node).find('input[type="checkbox"]');
              if (checkboxElement.length > 0) {
                return "";
              }
              return data.replace(/<\/?[^>]+(>|$)/g, ""); // Remueve etiquetas HTML
            },
          },
        },
      },
      {
        extend: "csvHtml5",
        text: '<i class="fas fa-file-csv"></i> &nbsp;Exportar Csv',
        exportOptions: {
          columns: ":visible",
          format: {
            body: function (data, row, column, node) {
              var selectElement = $(node).find("select");
              if (selectElement.length > 0) {
                return $(node).find(".selected-resultado").val();
              }
              var checkboxElement = $(node).find('input[type="checkbox"]');
              if (checkboxElement.length > 0) {
                return "";
              }
              return data.replace(/<\/?[^>]+(>|$)/g, ""); // Remueve etiquetas HTML
            },
          },
        },
      },
      {
        extend: "pdfHtml5",
        text: '<i class="fas fa-file-pdf"></i> &nbsp;Exportar Pdf',
        exportOptions: {
          columns: ":visible",
          format: {
            body: function (data, row, column, node) {
              // Remover los inputs y selects de la exportación
              var selectElement = $(node).find("select");
              if (selectElement.length > 0) {
                return $(node).find(".selected-resultado").val();
              }
              var checkboxElement = $(node).find('input[type="checkbox"]');
              if (checkboxElement.length > 0) {
                return "";
              }
              return data.replace(/<\/?[^>]+(>|$)/g, ""); // Remueve etiquetas HTML
            },
          },
        },
      },
    ],
    ordering: false,
    paging: true,
    pageLength: 15,
    autoWidth: false,
    responsive: true
  });

  // Función para seleccionar/deseleccionar todas las filas
  $("#selectAllCheckbox").on("change", function () {
    var rows = table.rows().nodes();
    $('input[type="checkbox"]', rows).prop("checked", this.checked);
  });

  // Manejo de la paginación
  $("#resultados_table").on("page.dt", function () {
    $("#selectAllCheckbox").prop("checked", false);
  });
});

// Función para codificar en Base64
function encodeBase64(str) {
  return btoa(str);
}
// Función para decodificar en Base64
function decodeBase64(str) {
  return atob(str);
}
/* Función para mostrar la alerta de enviar los reultados calificados a Sofia */

function showAlert() {
  Swal.fire({
    title: "Esta seguro de querer enviar los datos a SOFIA?",
    footer:
      "Nota: Una vez enviada la información, usted NO podra realizar ningun cambio, si desea relizar cambios posterior al envio, favor comunicarse al Soporte para ser atendido",
    showDenyButton: true,
    showCancelButton: false,
    confirmButtonColor: "#39a900",
    confirmButtonText: "Enviar",
    denyButtonText: `No enviar cambios`,
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire("Enviado!", "", "success");
    } else if (result.isDenied) {
      Swal.fire(
        "No se realiza cambios en los resultados de aprendizaje",
        "",
        "info"
      );
    }
  });
}
const encoded_ficha = document.querySelector("#ficha").value;
const encoded_competencia = document.querySelector("#competencia").value;
const encode_rea = document.querySelector("#rea").value;

/* Función para redireccionar a la pagina de resultadoap */
function redirectResulToResultadoAP(encoded_ficha, encoded_competencia) {
  const urlParams = `id_ficha=${encoded_ficha}&id_competencia=${encoded_competencia}`;
  const encodedParams = encodeBase64(urlParams);
  window.location.href = `resultadoap.php?params=${encodedParams}`;
}
/* Función para redireccionar a la pagina de actividades del resultado de aprendizaje */
function redirectToActividadApContro(
  encoded_ficha,
  encoded_competencia,
  encoded_rea
) {
  const urlParams = `id_ficha=${encoded_ficha}&id_competencia=${encoded_competencia}&rea_id=${encoded_rea}`;
  const encodedParams = encodeBase64(urlParams);
  window.location.href = `http://localhost/lms/views/actividades/acti_ap.php?params=${encodedParams}`;
}
/* Función para redireccionar a la pagina de actividades */
function redirectToSOFIA() {
  window.location.href = `http://localhost/lms/soap/ejemplosoap.php`; // ENVES DE EJEMPLO, ENVIAR DATA A TRATAMIENTO.PHP - PASAR DATA ENCRIPTADA O PARAMETROS
}
/**
 * submitForm
 *
 * @return void
 */
function submitForm(action) {
  var table = $("#resultados_table").DataTable();
  var currentPage = table.page();
  table.page("first").draw("page");
  var selectedRows = $("#resultados_table")
    .DataTable()
    .$("input.rowCheckbox:checked")
    .closest("tr");
  /* Condiciòn para asignar la ruta del action del formulario */
  if (action === "update") {
    var selectedUsers = [];
    var selectedResults = [];
    var hasChanges = false;

    table.page(currentPage).draw("page");
    /* Se valida que se seleccione filas de la tabla */
    selectedRows.each(function () {
      var userId = $(this).find("input.rowCheckbox").val();
      var result = $(this).find("#selectResultado").val();

      if (selectedRows.length === 0) {
        Swal.fire({
          title: "Error",
          text: "Debe seleccionar las filas!",
          icon: "question",
          animation: false,
          focusConfirm: false,
          confirmButtonText: "Intentar de nuevo",
          confirmButtonColor: "#ff595e",
        });
        return;
      }
      /* Se valida que la calificaciòn sea diferente a vacio o que nos sea un dato nulo */
      if (result === "" || result == null) {
        Swal.fire({
          title: "Error",
          text: "Todos los resultados deben ser seleccionados!",
          icon: "warning",
          animation: false,
          focusConfirm: false,
          confirmButtonText: "Intentar de nuevo",
          confirmButtonColor: "#ff595e",
        });
        return false;
      }
      /* Verificar que los datos a modificar sean diferente a su calificaciòn original */
      var originalResult = $(this)
        .find("#selectResultado")
        .data("original-value");
      if (result !== originalResult) {
        hasChanges = true;
      }

      selectedUsers.push(userId);
      selectedResults.push(result);
    });

    if (!hasChanges) {
      Swal.fire({
        title: "Error",
        text: "Debe modificar al menos un campo!",
        icon: "warning",
        animation: false,
        focusConfirm: false,
        confirmButtonText: "Intentar de nuevo",
        confirmButtonColor: "#ff595e",
      });
      return;
    }

    // Preparar los datos para enviar
    var data = {
      selected_users: selectedUsers.join(","),
      selected_results: selectedResults.join(","),
    };

    $.ajax({
      url: "../../controllers/actualizar_list.php",
      type: "POST",
      data: data,
      success: function (response) {
        // Manejar la respuesta del servidor
        Swal.fire({
          title: "Éxito",
          text: "Datos actualizados correctamente",
          icon: "success",
          animation: false,
          focusConfirm: false,
          confirmButtonText: "Continuar",
          confirmButtonColor: "#28a745",
          allowOutsideClick: false,
        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
            // Recargar o actualizar la tabla si es necesario
            location.reload();
          }
        });
      },
      error: function (xhr, status, error) {
        // Manejar errores
        Swal.fire({
          title: "Error",
          text: "Ocurrió un error al actualizar los datos",
          icon: "error",
          animation: false,
          focusConfirm: false,
          confirmButtonText: "Intentar de nuevo",
          confirmButtonColor: "#ff595e",
        });
      },
    });
  } else if (action === "updateSofia") {
    /* Condiciòn para obtener todos los datos seleccionados, visualizar los campos en el modal */
    /* este es el bueno */

    Swal.fire({
      title: "¿Está seguro de enviar los datos a SOFIA?",
      footer:
        "Nota: Una vez enviada la información a SOFIA, No podrá realizar ningún cambio. ¿Desea continuar?",
      showDenyButton: true,
      showCancelButton: false,
      confirmButtonColor: "#39a900",
      confirmButtonText: "Continuar",
      denyButtonText: `No enviar cambios`,
      allowOutsideClick: false,
    }).then((result) => {
      if (result.isConfirmed) {
        var selectedData = [];

        if (selectedRows.length === 0) {
          Swal.fire({
            title: "Error",
            text: "No hay datos seleccionados para enviar.",
            icon: "question",
            animation: false,
            focusConfirm: false,
            confirmButtonText: "Intentar de nuevo",
            confirmButtonColor: "#ff595e",
          });
          return;
        }
        selectedRows.each(function () {
          var codigo = $(this).find("#codigo").text();
          var aprendiz = $(this).find("#aprendiz").text();
          var estado = $(this).find("#estado").val();
          console.log(estado);
          var calificacion = $(this).find("#result").text();

          if (estado === "2" && calificacion !== "X") {
            // Validar si el estado es "dos" y sean diferente a X
            selectedData.push({
              codigo: codigo,
              aprendiz: aprendiz,
              calificacion: calificacion,
            });
            var modalBody = document.getElementById("selectedData");
            modalBody.innerHTML = "";

            selectedData.forEach((data) => {
              modalBody.innerHTML += `
            <tr>
                <td>${data.aprendiz}</td>
                <td>${data.calificacion}</td>
            </tr>`;
            });
            $("#dataModal").modal("show");

            document
              .getElementById("formulario_sofia")
              .addEventListener("submit", function (event) {
                event.preventDefault();

                fetch("../../controllers/sofia_controller.php", {
                  // Asegúrate de proporcionar una URL válida
                  method: "POST",
                  headers: {
                    "Content-Type": "application/json",
                  },
                  body: JSON.stringify(selectedData),
                })
                  .then((response) => response.json())
                  .then((data) => {
                    if (data.success) {
                      Swal.fire({
                        title: "¡Éxito!",
                        text: data.message,
                        icon: "success",
                        confirmButtonColor: "#39a900",
                        confirmButtonText: "Continuar",
                        allowOutsideClick: false,
                      }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                          location.reload();
                        }
                      });
                      $("#dataModal").modal("hide");
                    } else {
                      Swal.fire({
                        title: "Error!",
                        text: data.message,
                        icon: "error",
                        confirmButtonColor: "#ff595e",
                        confirmButtonText: "Intentar de Nuevo",
                        allowOutsideClick: false,
                      }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                          location.reload();
                        }
                      });
                    }
                  })
                  .catch((error) => {
                    Swal.fire({
                      title: "Error!",
                      text: "Hubo un problema con la solicitud.",
                      icon: "error",
                      confirmButtonColor: "#ff595e",
                      confirmButtonText: "Intentar de Nuevo",
                      allowOutsideClick: false,
                    }).then((result) => {
                      /* Read more about isConfirmed, isDenied below */
                      if (result.isConfirmed) {
                        location.reload();
                      }
                    });
                  });
              });
          } else {
            Swal.fire({
              title: "Advertencia",
              text: "Solo se visualizaran los aprendices habilitados para el envio a Sofia.",
              icon: "info",
              animation: false,
              focusConfirm: false,
              confirmButtonText: "Continuar",
              allowOutsideClick: false,
            });
            return;
          }
          var currentPage = table.page();
          table.page(currentPage).draw("page");
        });
      } else if (result.isDenied) {
        Swal.fire({
          title: "Advertencia",
          text: "No se realiza cambios en los resultados de aprendizaje.",
          icon: "info",
          animation: false,
          focusConfirm: false,
          confirmButtonText: "Continuar",
          allowOutsideClick: false,
        }).then((result) => {
          if (result.isConfirmed) {
            location.reload();
          }
        });
      }
    });
  }
}
