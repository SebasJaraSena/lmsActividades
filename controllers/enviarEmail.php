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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #ffffff; color: #000000; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 20px auto; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden;">
        <div style="text-align: center; background-color: #39a900; color: #ffffff; padding: 2px;display: flex;  align-items: center; justify-content: center; flex-direction: column;">
            <h1 style="margin: 0;">Recordatorio</h1>
            <img src="https://imgs.search.brave.com/-LGD7JvfVu2AbmTsxSTAhB5qGaHl_ocj5dz1kQNn28o/rs:fit:500:0:0:0/g:ce/aHR0cHM6Ly90cmFt/aXRlaW5mb3JtYXRp/dm8uY29tL3dwLWNv/bnRlbnQvdXBsb2Fk/cy8yMDIyLzA4L0xv/Z28tc2VuYS1ibGFu/Y28tc2luLWZvbmRv/LnBuZw" alt="Logo" style="margin-top: 10px; height: 50px;">
        </div>
        <div style="padding: 20px;">
            <p>Estimado aprendiz: ' . $destinatarios . '</p>
            <p>Te recordamos que tienes actividades pendientes en la plataforma Zajuna. Por favor, revisa y completa las tareas a la brevedad.</p>
            <p>Si ya has completado todas tus actividades, puedes ignorar este mensaje.</p>
            <p>Saludos cordiales.</p>
        </div>
        <div style="background-color: #f8f8f8; color: #777777; padding: 10px; text-align: center;">
            <p>Este es un mensaje automatizado, por favor no respondas a este correo.</p>
            <p>&copy; 2024 Zajuna</p>
        </div>
    </div>
</body>
</html>
';

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