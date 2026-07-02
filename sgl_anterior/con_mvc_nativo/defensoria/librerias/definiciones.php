<?php
$config_hostname = strtolower(gethostname());

// Entorno de Desarrollo (Estación de trabajo: informatica3)
if ($config_hostname == 'informatica3')
	define('RUTA_BASE', '/var/www/html/');
// Entorno de Test (Servidor: hcd06)
else if ($config_hostname == 'hcd06')
	define('RUTA_BASE', '/var/www/');
// Entorno de Produccion (Servidor: hcd02 / www)
else if ($config_hostname == 'hcd02')
	define('RUTA_BASE', '/var/www/');
// Entorno de Produccion (Servidor: lobo3 / www)
else if ($config_hostname == 'lobo3')
	define('RUTA_BASE', '/var/www/home/');
// Entorno por defecto
else
	define('RUTA_BASE', '/var/www/');

define("RUTA_SGL", RUTA_BASE."sgl/");
define("RUTA_RAIZ", RUTA_SGL."defensoria/");

define("URL_RAIZ_SGL", "http://".$_SERVER['HTTP_HOST']."/sgl/");
define("URL_RAIZ", URL_RAIZ_SGL . "defensoria/");

define("RUTA_LIBRERIAS_SGL", RUTA_SGL."librerias/");

define("TITULO_SISTEMA", "SGL Defensoria");

define('DB_SERVIDOR', "localhost");

define('RUTA_LIBRERIAS', RUTA_RAIZ . 'librerias/');
define('RUTA_ABMS', RUTA_RAIZ . 'abms/');
define('RUTA_DIRECTORIO_TEMPORAL', RUTA_ABMS . 'temporal/');
define('RUTA_CONTROLADORES', RUTA_ABMS . 'controladores/');
define('RUTA_MODELOS', RUTA_ABMS . 'modelos/');
define('RUTA_VISTAS', RUTA_ABMS . 'vistas/');
define('RUTA_CSS', RUTA_RAIZ . 'css/');

define('RUTA_PUBLICO', RUTA_RAIZ.'publico/');
define('RUTA_DOCUMENTOS_MOVIMIENTOS', RUTA_PUBLICO.'movimientos/');
define('RUTA_DOCUMENTOS_NOTAS', RUTA_PUBLICO.'notas/');

define('URL_PUBLICO', URL_RAIZ.'publico/');
define('URL_DOCUMENTOS_MOVIMIENTOS', URL_PUBLICO.'movimientos/');
define('URL_DOCUMENTOS_NOTAS', URL_PUBLICO.'notas/');

define('URL_LIBRERIAS', URL_RAIZ . 'librerias/');
define('URL_ABMS', URL_RAIZ . 'abms/index.php');
define('URL_DIRECTORIO_TEMPORAL', URL_RAIZ . 'abms/temporal/');
define('URL_JS', URL_RAIZ . 'js/');
define('URL_JS_LIBRERIAS', URL_JS . 'librerias/');
define('URL_CSS', URL_RAIZ . 'css/');
define('URL_IMAGENES', URL_RAIZ . 'imagenes/');

define('TAMANIO_MAXIMO_ARCHIVO', '41943040');// 40 MB = 40*1024*1024
define('CONTROLADOR_POR_DEFECTO', 'Presentador');
define('CANT_POR_PAGINA', 12);

?>
