# `CENTRO DE CALIFICACIONES Y ACTIVIDADES ZAJUNA ` 

## DESCRIPCIÓN DEL PROYECTO
Este proyecto es un sistema de calificaciones diseñado para evaluar los resultados de aprendizaje de los aprendices a través de un perfil instructor. Permite a los instructores calificar y gestionar el progreso de los aprendices de ZAJUNA de una manera amigable y simple para el usuario. 
  
## 1. REQUISITOS DE INSTALACIÓN 

  - Lenguaje PHP 8.2. Extensiones php requeridas (desde Zajuna): PDO, cURL, fileinfo, gd, gettext, gmp, intl, imap,mbstring, exif, openssl, pdo_psql, pgsql, soap, sockets, sodium, sqlite3, xsl, zip.  
  - PostgreSQL 16. 
  - Composer 2.7. 
  - Packagist -> PHPMailer 
  - PARA SISTEMA OPERATIVO WINDOWS: Instalar y configurar Wampserverx64 con las extensiones PHP mencionadas anteriormente y el entorno de trabajo (carpeta www dentro del wamp64). 
  - PARA LINUX – UBUNTU 24.04: Validar que el directorio de trabajo en el servicio apache2 se encuentre configurado a la ruta /var/www/html/lms 

## 2. INSTALACION DE PROYECTO CENTRO DE CALIFICACIONES Y ACTIVIDADES:

  #### 1. Clona el repositorio LMS-CENTRO DE CALIFICACIONES Y ACTIVIDADES en tu máquina local: 
  Para windows: Recuerda clonar el repositorio en la carpeta de trabajo localizada dentro de wamp64. La ruta es /wamp64/www/lms. 
  Para Linux: Recuerda clonar el repositorio en la carpeta de trabajo localizada en la ruta /var/www/html/lms. Se recomienda revisar la configuración del Apache2 para que el directorio de trabajo coincida con la ruta identificada    por el servicio web. 
   
  #### 2. Instalación Composer 2.7 y paquete/dependencia PHPMailer: 
  Para la instalación de este paquete, favor dirigirse a la documentación ubicada en el repositorio de archivos Agata - LMS>DOCUMENTOS>EQUIPO CENTRO CALIFICACIONES > MANUALES CC-CA V2 > MANUAL DE DESARROLLADOR CC-CA VERSION 2.0 -

  #### 3. Ajuste de rutas de navegación dentro de proyecto Centro de calificaciones y actividades: 
  A continuación se realiza una lista en donde se debe tener presente las rutas del aplicativo que deben ser modificadas previo a su lanzamiento en servidores para garantizar su correcto funcionamiento. 

| RUTA | DESCRIPCIÓN |
|--------------|--------------|
| lms/controller/ruta_error.php         |Aquí se ajusta la ruta para las vistas de errores.    |
|lms/config/login_config.php            |Aquí se ajusta las rutas según el criterio de sesión.|
|lms/header.php                         |Aquí se ajusta las rutas para iconografia, estilos, archivos de librerias descargados en el proyecto, rutas del menu de navegación, entre otros.|
|lms/public/js/resultado.js             |Rutas que se ejecutan en funciones javascript.|
|lms/public/js/actividades.js           |Rutas que se ejecutan en funciones javascript.|
|lms/public/js/resultadosap.js          |Rutas que se ejecutan en funciones javascript.|
|lms/public/js/scripts.js               |Rutas que se ejecutan en funciones javascript.|
|lms/views/actividades/acti_ap.php      |Rutas que redireccionan a zajuna, iconografìas e imagenes. |
|lms/views/actividades/actividades.php  |Rutas que redireccionan a zajuna, iconografìas e imagenes.|
|lms/views/actividades/evi_ap.php       |Rutas que redireccionan a zajuna, iconografìas e imagenes. |
|lms/views/actividades/evidencias.php   |Rutas que redireccionan a zajuna, iconografìas e imagenes.|
|lms/views/actividades/for_ap.php       |Rutas que redireccionan a zajuna, iconografìas e imagenes. |
|lms/views/actividades/foros.php        |Rutas que redireccionan a zajuna, iconografìas e imagenes. |
|lms/views/competencias.php             |Rutas que redireccionan a zajuna, iconografìas e imagenes. |
|lms/views/resultados/resultadoap.php   |Rutas que redireccionan a zajuna, iconografìas e imagenes.|
|lms/views/resultados/resultados.php    |Rutas que redireccionan a zajuna, iconografìas e imagenes.|
|lms/footer.php                         |Aquí se ajusta las rutas para iconografia, estilos, archivos de librerias descargados en el proyecto, entre otros. |

 #### 4. Ajuste de archivo navigationlib.php dentro del core de Zajuna: 
  Para este punto de la instalación, se debe ubicar el archivo en el core del aplicativo Zajuna, ubicado en la ruta: /zajuna/lib/navigationlib.php. Posteriormente al localizar el archivo se debe realizar lo siguiente: 
      
  - Se busca la funcion add_course_essentials  y se agrega la variable global $USER. 
  - Se busca la condición de la funcion, la cual es la siguiente: 
    
         if ($navoptions->grades) { 
          $url = new moodle_url('/grade/report/index.php',array('id'=>$course->id)); 
            $gradenode = $coursenode->add(get_string('grades'), $url, self::TYPE_SETTING, null, 'grades', new pix_icon('i/grades', '')); 
            // If the page type matches the grade part, then make the nav drawer grade node (incl. all sub pages) active. 
            if ($this->page->context->contextlevel < CONTEXT_MODULE && strpos($this->page->pagetype, 'grade-') === 0) { 
            $gradenode->make_active(); 
            }
          } 
          
   - Se reemplaza la condición de la función, quedando de esta manera:
     
         if ($navoptions->grades) { 
              // Construir la URL externa con placeholders {USERID} para ser reemplazados 
              // Se debe ajustar la url según la ubicación del bloque calificaciones y actividades 
              $external_url = 'http://localhost/lms/config/login_config.php?user=' . $USER->id . '&idnumber=' . urlencode($course->idnumber); 
              
              // Crear el objeto moodle_url con la URL externa construida 
              $url = new moodle_url($external_url); 
              
              // Añadir el enlace modificado a la navegación del curso ($coursenode) 
              $gradenode = $coursenode->add(get_string('grades'), $url,  self::TYPE_SETTING, null, 'grades', new pix_icon('i/grades', '')); 
              
              // Si la página actual es una página relacionada con calificaciones, activar el nodo de calificaciones 
              if ($this->page->context->contextlevel < CONTEXT_MODULE && strpos($this->page->pagetype, 'grade-') === 0) { 
                  $gradenode->make_active(); 
              } 
          } 
   

#### 6. Creación de servidor desde PostgreSQL para conexión entre bases de datos:
Una vez se tiene conexión con las bases de datos de INTEGRACION_REPLICA y ZAJUNA, se debe generar una unión a través de un server remoto generado bajo SQL, que simula una conexión entre ambas bases de datos, lo que nos permitirá el despliegue de funciones importantes en pasos venideros. 
    
  ***Paso 1:*** En ambas bases de datos se debe ejecutar la instalación de la siguiente extensión:
    
    CREATE EXTENSION IF NOT EXISTS postgres_fdw; 
  
  ***Paso 2:*** Crear el servidor remoto para la DB integracion_replica, se instala en la base de datos de ZAJUNA:
 
      CREATE SERVER integracion_server 
      FOREIGN DATA WRAPPER postgres_fdw 
      OPTIONS (host 'localhost', dbname 'integracion_replica-3', port '5432'); 
  
  ***Paso 3:*** Crear el mapeo de usuario:
 
      CREATE USER MAPPING FOR CURRENT_USER 
      SERVER integracion_server 
      OPTIONS (user 'postgres', password '12345'); 
  
  ***Paso 4:*** Importamos Esquema de integración hacia Esquema public Zajuna:
 
      IMPORT FOREIGN SCHEMA "INTEGRACION" 
      LIMIT TO ("USUARIO_LMS", "TABLA_NOTASXRA_CC", "RESULTADO_APRENDIZAJE") 
      FROM SERVER integracion_server 
      INTO public; 
  
#### 7. Migración/creación de funciones SQL en bases de datos: 

Para ejecutar la lógica del centro de calificaciones y actividades, se debe realizar creación de funciones SQL definidas por el equipo de desarrollo. Entre estas se deben ejecutar los Scripts sql que se encuentran en los archivos "funcionesIntegracion.sql" y "funcionesZajuna.sql" ubicados en la ruta del proyecto: lms/queries/funcionesIntegracion.sql y lms/queries/funcionesZajuna.sql. 
        
***NOTA***: para conocer la funcionalidad de estas funciones SQL favor remitirse al manual desarrollador del centro de calificaciones y actividades, ubicado en el repositorio de archivos Agata - LMS>DOCUMENTOS>EQUIPO CENTRO CALIFICACIONES > MANUALES CC-CA V2 > MANUAL DE DESARROLLADOR CC-CA VERSION 2.0 - NUMERAL 4: Funciones SQL. 

## 3. USO DEL APLICATIVO 
  1. Inicia sesión como instructor o aprendiz utilizando credenciales ZAJUNA. 
  2. Visualiza, según la ficha a la cual ingresa el usuario, la lista de aprendices y sus resultados de aprendizaje por cada competencia. 
  3. Califica o visualiza (según el rol del usuario) los resultados de aprendizaje de los aprendices a través de una interfaz simple. 
  4. Guarda los cambios realizados de manera local para actualizar los resultados de aprendizaje cuantas veces sea necesario antes de ser enviados a la plataforma de SOFIA PLUS. 
  5. Visualiza, según la ficha a la cual ingresa el usuario, la lista de actividades según la categoría del curso. Así mismo permite ingresar por actividad (foros, evidencias, pruebas de conocimiento) según se necesite. 
  6. Envía notificaciones por correo electrónico a los usuarios cuyas actividades se encuentren en estado pendiente. Permite enviar tanto de forma global como de manera individual. 
  7. Permite sincronizar el resultado de aprendizaje calificado en el centro de calificaciones y actividades con SOFIA PLUS, mediante el botón de enviar resultados a SOFIA. 

   
## 5. CONTACTO SOPORTE APLICACIÓN CC - CA ##  
Si tienes alguna pregunta o sugerencia, no dudes en ponerte en contacto con nosotros a través de [correo electrónico](mailto:info@test.com). 
