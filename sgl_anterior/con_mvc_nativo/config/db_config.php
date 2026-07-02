<?php
/* ****************************************************************************
Configuracion de la base de datos
**************************************************************************** */
// Las configuraciones base se obtienen a partir del nombre del equipo donde se encuentra
// la aplicacion.

// PHP >= 5.3.0
$config_hostname = strtolower(gethostname());
// PHP < 5.3.0
//$config_hostname = strtolower(php_uname("n"));

// Entorno de Test (Servidor: hcd06)
if ($config_hostname == strtolower('hcd06')) {
	define("DB_SERVER", "localhost");
	define("DB_USERNAME", "XXXXXXXXXX");
	define("DB_PASSWORD", "XXXXXXXXXX");
	define("DB_DATABASE", "hcd");
}
// Entorno de Prod (Servidor: hcd02)
else if ($config_hostname == strtolower('hcd02')) {
	define("DB_SERVER", "localhost");
	define("DB_USERNAME", "XXXXXXXXXX");
	define("DB_PASSWORD", "XXXXXXXXXX");
	define("DB_DATABASE", "hcd");
}
// Entorno de Consulta Web (Servidor: lobo3)
else if ($config_hostname == strtolower('lobo3')) {
	define("DB_SERVER", "localhost");
	define("DB_USERNAME", "XXXXXXXXXX");
	define("DB_PASSWORD", "XXXXXXXXXX");
	define("DB_DATABASE", "hcd");
}
// Entorno de Desarrollo (Estación de trabajo: informatica3)
else if ($config_hostname == 'informatica3') {
	define("DB_SERVER", "localhost");
	define("DB_USERNAME", "XXXXXXXXXX");
	define("DB_PASSWORD", "XXXXXXXXXX");
	define("DB_DATABASE", "hcd");
}
// Por defecto
else {
	define("DB_SERVER", "localhost");
	define("DB_USERNAME", "XXXXXXXXXX");
	define("DB_PASSWORD", "XXXXXXXXXX");
	define("DB_DATABASE", "hcd");
}
?>
