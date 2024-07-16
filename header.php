<?php

if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = array();
}

// Variable para detectar recarga
if (!isset($_SESSION['last_page'])) {
    $_SESSION['last_page'] = '';
}

// Define la URL de la página especial que causa el reseteo del historial
$reset_page_url = 'http://localhost/lmsActividades/views/actividades.php?idnumber=Mjk2NjY1Ng=='; // Ajusta esto según tu configuración

// Resetea el historial si se vuelve a la página especial
$current_page = $_SERVER['PHP_SELF'];
$url_info = parse_url($_SERVER['REQUEST_URI']);
$url = $url_info['path'] . (!empty($url_info['query']) ? '?' . $url_info['query'] : '');

if ($current_page === $reset_page_url) {
    $_SESSION['history'] = array();
    $_SESSION['last_page'] = $current_page;
} else {
    // Agrega la página actual al historial solo si no está ya presente o si no es una recarga
    if ($_SESSION['last_page'] != $url) {
        // Obtiene el nombre del archivo sin la extensión ".php"
        $page_name = basename($current_page, '.php');

        // Agrega la página al principio del historial
        array_unshift($_SESSION['history'], array('url' => $url, 'name' => $page_name));

        // Limita el historial a 3 elementos
        if (count($_SESSION['history']) > 3) {
            // Solo conserva los primeros 3 elementos del historial
            $_SESSION['history'] = array_slice($_SESSION['history'], 0, 3);
        }
    }
    // Actualiza la variable de sesión para la siguiente carga
    $_SESSION['last_page'] = $url;
}
function mostrar_historial()
{
    if (isset($_SESSION['history']) && !empty($_SESSION['history'])) {
        // Mostrar el historial en orden inverso para tener el más reciente a la derecha
        $history_count = count($_SESSION['history']);
        for ($i = $history_count - 1; $i >= 0; $i--) {
            echo "&nbsp;<a style='color:#04324d; text-decoration: none; font-weight: bold; text-transform: capitalize;' href='{$_SESSION['history'][$i]['url']}' data-url='{$_SESSION['history'][$i]['url']}'>{$_SESSION['history'][$i]['name']}</a> / ";
        }
    } else {
        echo "No hay historial disponible.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <!-- Font Awesome Icon -->
    <link rel="icon" type="image/png" href="http://localhost/lmsActividades/public/assets/img/head-sena.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link href="http://localhost/lmsActividades/public/css/styles.css" rel="stylesheet" />
    <link href="http://localhost/lmsActividades/public/css/style.min.css" rel="stylesheet" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="http://localhost/lmsActividades/public/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.1/css/responsive.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/2.0.1/css/colReorder.dataTables.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/select/2.0.0/js/dataTables.select.js">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css">
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.12.0/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="http://localhost/lmsActividades/public/js/all.js" crossorigin="anonymous"></script>

    <!-- Buttons for DataTables -->
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

    <title>Calificaciones</title>
</head>

<body class="sb-nav-fixed">

    <!--MENSAJE DE ALERTA RECOMENDANDO USO DE MOZILLA FIREFOX -->
    <div id="browser-alert" class="alert alert-warning alert-dismissible fade show" role="alert" style="display:none;">
        <strong>Se recomienda el uso de Mozilla Firefox para una correcta visualización.</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <nav class="d-flex flex justify-content-between flex-wrap flex-md-nowrap sticky-top px-4 py-2 navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a id="zajuna-link" class="navbar-brand ps-5 fs-5" href="http://localhost/zajuna/"><img class="my-2" src="http://localhost/lmsActividades/public/assets/img/zajuna.svg" alt=""></a>
        <div class="d-inline-flex justify-content-center justify-content-md-start text-white w-100 order-3 order-md-0">
            <a class="nav-link ps-4 fs-6" href="http://localhost/zajuna/my/">Área personal</a>
            <a class="nav-link ps-4 fs-6" href="http://localhost/zajuna/my/courses.php">Mis cursos</a>
            <a class="nav-link ps-4 fs-6" href="https://oferta.senasofiaplus.edu.co/sofia-oferta/">Accede a SOFIA</a>
        </div>
        <div class="d-flex">
            <nav class="logo-sena">
                <img src="http://localhost/lmsActividades/public/assets/img/head-sena.svg" alt="Ícono de sena" id="logo-sena-img">
            </nav>

            <!-- Navbar / Rutas de navegacion o acciones rapidas del Boque lmsActividades a Zajuna -->
            <ul class=" navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="http://localhost/zajuna/user/profile.php">Perfil</a></li>
                        <li><a class="dropdown-item" href="http://localhost/zajuna/grade/report/overview/index.php">Calificaciones</a></li>
                        <li><a class="dropdown-item" href="http://localhost/zajuna/calendar/view.php?view=month">Calendario</a></li>
                        <li><a class="dropdown-item" href="http://localhost/zajuna/user/files.php">Archivos Privados</a></li>
                        <li><a class="dropdown-item" href="http://localhost/zajuna/reportbuilder/index.php">Informes</a></li>
                        <li><a class="dropdown-item" href="http://localhost/zajuna/user/preferences.php">Preferencias</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div id="layoutSidenav_content">