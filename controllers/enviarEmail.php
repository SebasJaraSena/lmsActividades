<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../config/db_config.php';
require '../vendor/autoload.php';

// Recibe la información del formulario
/* $encoded_ficha = $_POST['id_ficha'] ?? '';*/
$id_curso = $_POST['id_curso'] ?? '';
$id_rea = $_POST['id_rea'] ?? '';
$redireccion = $_POST['redireccion'] ?? ''; 
$infoCorreos = $_POST['correosSeleccionados'] ?? [];
$correosSeleccionados = json_decode($infoCorreos, true); 

/* var_dump($id_curso);
var_dump($id_rea);
var_dump($redireccion);
var_dump($infoCorreos.'<br>');
var_dump($correosSeleccionados);
exit(); */

// Crea una instancia de PHPMailer
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host       = 'smtp-mail.outlook.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'senacorreoprueba@outlook.es';
$mail->Password   = 'Sena12345@';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = 587;
$mail->setFrom('senacorreoprueba@outlook.es', 'Recordatorio');
$mail->isHTML(true);

// Construye el cuerpo del correo con todos los destinatarios
$destinatarios = implode(', ', $correosSeleccionados);
$asunto = "Zajuna recordatorio";
$cuerpo = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
    <title>Recordatorio</title>
    </head>
    <body>
        <div class="contenedor">
        <table style="max-width: 600px; padding: 10px; margin:0 auto; border-collapse: collapse;">
            <tr>
                <td style="background-color: #ffffff;">
                    <div class="misection">
                        <p style="margin: 2px; font-size: 18px">Practicante recuerde realizar sus actividades</p>
                        <p>Destinatarios: ' . $destinatarios . '</p>
                    </div>
                </td>
            </tr>
        </table>
        </div>
    </body>
    </html>';

// Agrega todos los destinatarios al correo
foreach ($correosSeleccionados as $infocorreo) {
    $mail->addAddress(trim($infocorreo));
}

// Configura el asunto y el cuerpo del correo
$mail->Subject = $asunto;
$mail->Body = $cuerpo;

// Envía el correo
try {
    $mail->send();
    $respuesta = "Correos enviados exitosamente";
} catch (Exception $e) {
    $respuesta = "Error al enviar correo: {$mail->ErrorInfo}";
}

// Guarda la respuesta en la sesión
$_SESSION['respuesta'] = $respuesta;

// Redirecciona después de enviar el correo
echo "<script>
// Función para codificar en Base64
 function encodeBase64(str) {
     return btoa(str);
 }

 // Función para decodificar en Base64
 function decodeBase64(str) {
     return atob(str);
 }
 window.location.href = 'http://localhost/lmsActividades/views/actividades/$redireccion?id=$id_curso&cat=$id_rea';
</script>";
/* var_dump("http://localhost/lmsActividades/views/actividades/$redireccion?id=$id_curso&cat=$id_rea"); */
exit();

/*  const urlParams = 'id=$id_curso&cat=$encoded_rea';
 const encodedParams = encodeBase64(urlParams);
 window.location.href = 'http://localhost/lmsActividades/views/actividades/$redireccion?id=$id_curso&cat=$id_rea';
  */