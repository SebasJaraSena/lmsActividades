<?php

function checkSessionTimeout()
{
    $timeout_duration = 1800; // duración del tiempo de espera en segundos (60 segundos = 1 minuto)

    if (isset($_SESSION['LAST_ACTIVITY'])) {
        $elapsed_time = time() - $_SESSION['LAST_ACTIVITY'];

        if ($elapsed_time > $timeout_duration) {
            // La última solicitud fue hace más de $timeout_duration segundos
            session_unset();     // unset $_SESSION variable for the run-time 
            session_destroy();   // destroy session data in storage
            echo "<script>
            localStorage.clear();
            </script>";
            return false; // Indica que la sesión ha expirado
        }
    }

    $_SESSION['LAST_ACTIVITY'] = time(); // Actualiza la marca de tiempo de la última actividad
    return true; // Indica que la sesión sigue activa
}
