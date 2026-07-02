<?php
/**
 * Clase encargada del ruteo a los distintos controladores, utilizada en 'index.php' del raíz del subsitio html.
 */

define('ERROR_ROUTER_GENERICO', 100);
define('ERROR_CONTROLADOR_INVALIDO', 101);
define('ERROR_ACCION_INVALIDA', 102);

class ControllerRouter {
 	/**
 	 * Realiza el ruteo (redirección) al controlador y acción correspondiente.
 	 * @param  array $requestParams      Referencia a parámetros del request. Equivalente a $_REQUEST (o $_POST + $_GET).
 	 * @param  array $requestFiles       Referencia a parámetros files del request. Equivalente a $_FILES.
 	 * @param  string $baseControllerPrefix Prefijo de nombre de todos los controladores del subsitio.
 	 */
    static function route($requestParams = null, $requestFiles = null, $baseControllerPrefix = '')
    {
		// Inicio de sesion.
		SessionController::get()->iniciarSesion();

		// Si los parametros de request son nulos, tomo la variable global $_REQUEST
		if (is_null($requestParams))
			$requestParams = $_REQUEST;

		// Si dispongo del parametro $_FILES, lo agrego como parte de $requestParams
		if (is_null($requestFiles))
			$requestParams['request_files'] = array();
		else
			$requestParams['request_files'] = $requestFiles;

		// Es obligatorio tener como parametro el nombre del controlador y la accion.
		if (!isset($requestParams['c'])) 
			$requestParams['c'] = 'home';

		if (!isset($requestParams['a'])) 
			$requestParams['a'] = 'view';

		try {
			$claseControlador = Validator::get()->validar($requestParams['c'], PATRON_ALFANUM, false, 'Controlador');
			$claseControlador = $baseControllerPrefix.ucfirst(strtolower($claseControlador)).'Controller';
			$accion = Validator::get()->validar($requestParams['a'], PATRON_ALFANUM, false, 'Acci&oacute;n');
		} catch (Exception $e) {
			$claseControlador = $baseControllerPrefix.'HomeController';
			$accion = 'view';
			SessionController::get()->guardarError($e->getMessage(), ERROR_ROUTER_GENERICO);
		}
		
		// Si no existe la clase, se incluye la clase del controlador 'home'
		if (!class_exists($claseControlador)) {
			$claseControlador = $baseControllerPrefix.'HomeController';
			$accion = 'view';
			SessionController::get()->guardarError("Controlador inv&aacute;lido.", ERROR_CONTROLADOR_INVALIDO);
		}

		// Si no existe la clase del controlador y su accion correspondiente, vuelvo al home
		if (is_callable(array($claseControlador, $accion)) === false) {
			// Lo llevo al home, y muestro un error
			$claseControlador = $baseControllerPrefix.'HomeController';
			$accion = 'view';
			SessionController::get()->guardarError("Acci&oacute;n indefinida.", ERROR_ACCION_INVALIDA);
		}

		// Se crea una instancia del controlador solicitado
		$instanciaControlador = new $claseControlador();
		
		// Verifico que la acción sea ejecutable
		if ($instanciaControlador->validarAccion($accion)) {
			// Se ejecuta la accion para el controlador solicitado
			$instanciaControlador->$accion($requestParams);
		}
		else {
			// Lo llevo al home, y muestro un error
			$claseControlador = $baseControllerPrefix.'HomeController';
			$accion = 'view';
			SessionController::get()->guardarError("Acci&oacute;n inv&aacute;lida.", ERROR_ACCION_INVALIDA);

			$instanciaControlador = new $claseControlador();
			$instanciaControlador->$accion($requestParams);
		}
    }
}
?>