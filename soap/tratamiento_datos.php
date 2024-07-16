<?php
// CONDICIONAL PARA RECIBIR DATA POR METODO POST - FORMULARIO, DE LO CONTRARIO NO EJECUTAR
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // RECIBIR VALORES (EN ESTE CASO NOTAS DEL CENTRO CALIFICACIONES)
    $num1 = $_POST["num1"];
    $num2 = $_POST["num2"];

    try {
        // CONEXION AL ENDPOINT WSDL DE SOFIA - EN ESTE CASO SE USA ENDPOINT PUBLICO PARA TEST
        $client = new SoapClient("http://www.dneonline.com/calculator.asmx?WSDL");
        // PARAMETROS EN FORMA DE ARREGLO QUE SE RECIBEN CON LOS VALORES DE LAS VARIABLES PASADAS POR EL FORMULARIO
        $params = array(
            "intA" => $num1,
            "intB" => $num2
        );
        // METODO soapCall propio de php para llamar cada metodo/traductor, donde se ejecuta la lógica
        $response = $client->__soapCall("Add", array($params)); // AQUI LLAMAMOS LOS TRADUCTORES O METODOS - Add, Subtract, Multiply, Divide
        $response2 = $client->__soapCall("Subtract", array($params)); // AQUI LLAMAMOS LOS TRADUCTORES O METODOS - Add, Subtract, Multiply, Divide
        $response3 = $client->__soapCall("Multiply", array($params)); // AQUI LLAMAMOS LOS TRADUCTORES O METODOS - Add, Subtract, Multiply, Divide
        $response4 = $client->__soapCall("Divide", array($params)); // AQUI LLAMAMOS LOS TRADUCTORES O METODOS - Add, Subtract, Multiply, Divide
        // echos para imprimir respuestas consumo SOAP
        echo "La suma es: " . $response->AddResult . "<br/>";
        echo "La resta es: " . $response2->SubtractResult . "<br/>";
        echo "La multiplicación es: " . $response3->MultiplyResult . "<br/>";
        echo "La división es: " . $response4->DivideResult;
    } catch (SoapFault $fault) {
        // print error si no se recibe post
        echo "Hubo un error al conectar con el servicio web: {$fault->faultstring}";
    }
}
// // Crear el cliente SOAP - EJEMPLO ENTENDIMIENTO SOAP
// $soap_url = 'http://example.com/triangle?wsdl'; // URL del servicio web SOAP
// $cliente = new SoapClient($soap_url);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {
//     // Obtiene el nombre ingresado en el formulario
//     $base = $_POST['base']; // Obtener el valor de la base desde el formulario
//     $height = $_POST['height']; // Obtener el valor de la altura desde el formulario

//     try {
//         // Llama a la OBTENER AREA
//         $response = $cliente->CalculateArea(['base' => $base, 'height' => $height]);

//         // La respuesta exitosa del servidor SOAP
//         echo "Respuesta del servidor: " . $respuesta;
//     } catch (SoapFault $e) {
//         // Manejo de errores en caso de falla
//         echo "Error al llamar al servicio: " . $e->getMessage();
//     }
// }
// 
?>
