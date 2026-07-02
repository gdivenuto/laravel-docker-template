<?php
/**
 * autoload_class.php
 *
 * Este script contiene la definición del método para la funcion spl_autoload_register,
 * el cual permite automatizar la inclusión de las clases que se utilicen en la aplicación,
 * evitando una tormenta de 'includes' y 'requires'.
 *
 * TODO: La función registrada en autoload respeta una cierta nomenclatura y lógica para 
 * optimizar el proceso de inclusión automatica de clases.
 */

function autoloadCallback($nombreClase) {
	// Rutas con orden de busqueda específico
	$rutas = array (
		PATH_KRAKEN_LAYER_NEGOCIO,
		PATH_KRAKEN_LAYER_NEGOCIO_ACTUACIONES,
		PATH_KRAKEN_LAYER_MODELO,
		PATH_KRAKEN_LAYER_MODELO_ACTUACIONES,
		PATH_KRAKEN_LAYER_DATOS,
		PATH_KRAKEN_LIBRERIAS_QUERYBUILDER,
		PATH_KRAKEN_LIBRERIAS_TEMPLATOR,
		PATH_KRAKEN_LIBRERIAS,
		PATH_KRAKEN_HTML_BASE,
		PATH_KRAKEN_HTML_BACKEND_CONTROLLERS,
		PATH_KRAKEN_HTML_BACKEND_CONTROLLERS_ACTUACIONES,
		PATH_KRAKEN_HTML_BACKEND_VIEWS,
		PATH_KRAKEN_HTML_BACKEND_VIEWS_ACTUACIONES,
		PATH_KRAKEN_HTML_FRONTEND_CONTROLLERS,
		PATH_KRAKEN_HTML_FRONTEND_VIEWS,
		PATH_KRAKEN_WEBSERVICE);

    // Itero todas las rutas buscando el archivo de la clase a incluir
    foreach ($rutas as $r) {
    	$script = $r.$nombreClase.'.php';
    	if (file_exists($script)) {
  		    require_once($script);
  		    break; // si lo encontre, no sigo buscando
    	}
    }
}

// Registro el callback del autoload
spl_autoload_register('autoloadCallback');
?>