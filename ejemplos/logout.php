<?php
// Iniciar sesión
session_start();
// Destruir la sesión
session_destroy();
echo json_encode(["success" => true]);
// Redirigir al usuario a la página de inicio de sesión
header("Location: http://localhost/zajuna/login");
exit();
