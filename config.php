<?php

ini_set( "log_errors", true );
ini_set( "error_reporting", E_ALL );
ini_set( "error_log", "error_log" );

date_default_timezone_set( "Europe/Madrid" );  // http://www.php.net/manual/en/timezones.php
define( "APP_URL", "index.php" );

$trabajandoEnLocal = false;
if (!$trabajandoEnLocal) {
    define( "DB_DSN", "mysql:host=jquerymobile.db.6581652.hostedresource.com;dbname=jquerymobile" );
    define( "DB_USERNAME", "jquerymobile" );
    define( "DB_PASSWORD", "jqueryPass1" );
}else{
     define( "DB_DSN", "mysql:host=localhost;dbname=tasktango" );
    define( "DB_USERNAME", "root" );
    define( "DB_PASSWORD", "" );
}
define( "CLASS_PATH", "classes" );
define( "TEMPLATE_PATH", "templates" );
define( "PASSWORD_EMAIL_FROM_NAME", "Task Tango" );
define( "PASSWORD_EMAIL_FROM_ADDRESS", "javi.delahaza@gmail.com" );
define( "PASSWORD_EMAIL_SUBJECT", "Your New Task Tango Password" );
define( "PASSWORD_EMAIL_APP_URL", "http://your-site-url/task-tango/" );
require( CLASS_PATH . "/User.php" );
require( CLASS_PATH . "/Todo.php" );

/*
 * CONSTANTES
 */
define( "NOMBRE_APP", "Asignación de Tareas" );
define( "ELIMINAR_TAREA", "Eliminar Tarea" );

// HEADER
define( "HEADER_REGISTRO", "Registro" );
// BOTONES
define( "BOTON_CANCELAR", "Cancelar" );
define( "BOTON_NUEVA", "Nueva" );
define( "BOTON_OPCIONES", "Opciones" );
define( "BOTON_REGISTRAR", "Registrese" );
define( "BOTON_VOLVER", "Volver" );
define( "BOTON_GUARDAR", "Guardar" );
?>
