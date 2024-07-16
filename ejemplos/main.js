// Grid API: Access to Grid API methods
let gridApi;

// Row Data Interface
const gridOptions = {
  // Data to be displayed
  rowData: [],
  defaultColDef: {
    resizable: false,
  },

  // Columns to be displayed (Should match rowData properties)
  // Aqui se define cuantas columnas se desea mostrar. Esta deben hacer match con la data consultada en fetchData.php
  columnDefs: [
    { field: "RGA_ID", headerName: "Id de Usuario" },
    { field: "FIC_ID", headerName: "Nombre" },
    { field: "USR_NOMBRE", headerName: "Apellido" },
    { field: "USR_APELLIDO", headerName: "Resultado Aprendizaje" },
    { field: "ADR_EVALUACION_COMPETENCIA", headerName: "Observación" },
    { field: "ADR_EVALUACION_RESULTADO", headerName: "Nombre del Resultado de Aprendizaje" },
  ],

  // Configuraciones Aplicadas a todas las columnas
  defaultColDef: {
    filter: true,
    editable: true,
    filter: "agTextColumnFilter",
    floatingFilter: true,
  },

  // Opciones Grid  & Callbacks
  pagination: true,
  rowSelection: "multiple",
  suppressRowClickSelection: true,
  pagination: true,
  paginationPageSize: 10,
  paginationPageSizeSelector: [10, 25, 50],
  onSelectionChanged: (event) => {
    console.log("Row Selection Event!");
  },
};
function setAutoHeight() {
  gridApi.setGridOption("domLayout", "autoHeight");
  // auto height will get the grid to fill the height of the contents,
  // so the grid div should have no height set, the height is dynamic.
  document.querySelector("#myGrid").style.height = "";
}
// Crear cuadrícula: Crear nueva fila dentro del div #myGrid, utilizando el objeto Opciones de fila.
gridApi = agGrid.createGrid(document.querySelector("#myGrid"), gridOptions);

// Fetch Remote Data
fetch("http://localhost/lms/queries/dataVistaap.php")
  .then((response) => response.json())
  .then((data) => gridApi.setGridOption("rowData", data));



