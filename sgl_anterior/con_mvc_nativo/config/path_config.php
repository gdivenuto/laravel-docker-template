<?php
/* ****************************************************************************
 Configuración de rutas del proyecto.

 Esta configuración debe incluise SIEMPRE con una ruta absoluta, por ejemplo:

 <?php require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/path_config.php'); ?>
 **************************************************************************** */

// ****************************************************************************
// Ruta Base ******************************************************************
// ****************************************************************************
// Las rutas base se obtienen a partir del nombre del equipo donde se encuentra
// la aplicacion.

// PHP >= 5.3.0
$config_hostname = strtolower(gethostname());
// PHP < 5.3.0
//$config_hostname = strtolower(php_uname("n"));

// Entorno de Desarrollo (Estación de trabajo: informatica3)
if ($config_hostname == 'informatica3')
	define('PATH_BASE', '/var/www/html/');
// Entorno de Test (Servidor: hcd06)
else if ($config_hostname == 'hcd06') {
	// Como tenemos dos configuraciones de test, aplicamos la que corresponda
	switch (strtolower($_SERVER['SERVER_NAME'])) {
		case 'hcd06.concejomdp.gov.ar':
			define('PATH_BASE', '/var/www/');
			break;
		case 'sglexdi.concejomdp.gov.ar':
			define('PATH_BASE', '/var/www-exdi/');
			break;
	}
}
// Entorno de Produccion (Servidor: hcd02 / www)
else if ($config_hostname == 'hcd02')
	define('PATH_BASE', '/var/www/');
// Entorno de Produccion (Servidor: lobo3 / www)
else if ($config_hostname == 'lobo3')
	define('PATH_BASE', '/var/www/home/');
// Entorno por defecto
else
	define('PATH_BASE', '/var/www/');

// ****************************************************************************
// Rutas Base de los proyectos ************************************************
// ****************************************************************************
define('PATH_SGL', PATH_BASE.'sgl/');
define('PATH_WSSGL', PATH_BASE.'wssgl/');

// ****************************************************************************
// Rutas Base de las librerias de SGL *****************************************
// ****************************************************************************
define('PATH_SGL_LIBRERIAS', PATH_SGL.'librerias/');
define('PATH_SGL_LAYER_NEGOCIO_PRESTAMOS', PATH_SGL.'expedientes/prestamos/layer_negocio/');
define('PATH_SGL_LAYER_DATOS_PRESTAMOS', PATH_SGL.'expedientes/prestamos/layer_datos/');
define('PATH_SGL_LAYER_MODELO_PRESTAMOS', PATH_SGL.'expedientes/prestamos/layer_modelo/');

// ****************************************************************************
// Rutas Base de las librerias de WSSGL ***************************************
// ****************************************************************************
define('PATH_WSSGL_LAYER_PRESENTACION', PATH_WSSGL.'layer_presentacion/');
define('PATH_WSSGL_LAYER_NEGOCIO', PATH_WSSGL.'layer_negocio/');
define('PATH_WSSGL_LAYER_DATOS', PATH_WSSGL.'layer_datos/');
define('PATH_WSSGL_LAYER_MODELO', PATH_WSSGL.'layer_modelo/');
define('PATH_WSSGL_LIBRERIAS', PATH_WSSGL.'librerias/');

// ****************************************************************************
// Rutas Base de la documentacion asociada ************************************
// ****************************************************************************
define('PATH_SGL_DOCUMENTOS_ASOCIADOS', PATH_SGL.'expedientes/proyectos/');

// ****************************************************************************
// Rutas de Logs **************************************************************
// ****************************************************************************
define('PATH_SGL_LOG', PATH_SGL.'log/');
define('PATH_WSSGL_LOGS', PATH_WSSGL.'logs/');

?>
