<?php
/**
 * Clase de controlador para la Carga de Digitalizaciones
 *
 * @author XXXX y XXXX
 */
class BECargaDigitalizacionesController extends BaseController
{
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct()
	{
		// Llamada al constructor del padre
		parent::__construct();

		// Seteo de ruta base de la interfaz
		$this->baseUrl = URL_KRAKEN_HTML_BACKEND;

		// Nombre del módulo al que corresponde el controlador
		$this->nombreModulo = 'EXPEDIENTES';

		// Determino las acciones válidas y su nivel de acceso mínimo requerido
		$this->accionesPermitidas['view']                           = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['datagriddocall']                 = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['cargardigitalizaciones']         = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['agregardigitalizacion']          = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['sobreescribirdigitalizacion']    = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['eliminardigitalizaciontemporal'] = NIVEL_ACCESO_OPERADOR;
	}

	/**
	 * Invoca a la vista 'view' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function view($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parámetros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Instancio la vista y la muestro
		$vista = new BECargaDigitalizacionesView($paramVista);
		$vista->vistaListado();
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function datagriddocall($requestParams)
	{
		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {

			$resultado['recordsTotal'] = 0;
			$resultado['recordsFiltered'] = 0;
			$resultado['data'] = array();
			$resultado['error'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['numeroError'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {
			// Seteo el valor de control "draw"
			$p_draw = $requestParams['draw'];

			// Obtengo datos para la paginación
			$p_limitStart = (trim($requestParams['start']) == '') ? null : trim($requestParams['start']);
			$p_limitLength = (trim($requestParams['length']) == '') ? null : trim($requestParams['length']);

			// Saneo todos los parametros
			$p_draw = $this->sanearParametro($p_draw);
			$p_limitStart = $this->sanearParametro($p_limitStart);
			$p_limitLength = $this->sanearParametro($p_limitLength);

			// Realizo la consulta y preparo el resultado
			$resultado = array();
			$resultado['draw'] = $p_draw; // Es un valor entero para control interno del DataTable
			try
			{
				$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
				$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

				$documentos = NG::expedientes()->obtenerDigitalizacionesACargar();

				// Elimino el dato de las rutas locales para evitar un 'information leak' (fuga de información)
				foreach ($documentos as $d)
					$d['ruta_completa'] = '';

				// Simulo la paginación
				if (count($documentos) > 0) {
					$paginas = array_chunk($documentos, $p_limitLength);
					// Para obtener la pagina, hago la division entera entre el start y el length
					$resultado['data'] = $paginas[floor($p_limitStart / $p_limitLength)];
				} else
					$resultado['data'] = array();

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = count($documentos);
				$resultado['recordsFiltered'] = count($documentos);
			}
			catch (Exception $ex)
			{
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array();  // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Invoca a la accion 'cargardigitalizaciones' del controlador.
	 */
	public function cargardigitalizaciones($requestParams) {

		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');
		} else {

			try {
				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				// No utilizo el JsonHelper porque es un json con un array "crudo".
				$jsonData = file_get_contents('php://input');
				$parametros = json_decode($jsonData);

				if ( ! array_key_exists('archivos', $parametros) )
					throw new Exception('Par&aacute;metro inv&aacute;lido. Se requiere el par&aacute;metro "archivos".');

				$f_archivos = $parametros->archivos;

				if (!(is_array($f_archivos)))
					throw new Exception('Tipo de par&aacute;metro inv&aacute;lido: (array)archivos.');

				// el resultado del proceso incluye:
				// 		el nombre del archivo, o un grupo de nombres
				// 		el estado: OK, WARNING (cuando ya existe el documento) o ERROR
				// 		el mensaje que describe el resultado del movimiento
				$resultadoProceso = NG::expedientes()->cargarDigitalizaciones($f_archivos);

				// Genero respuesta
				$resultado['estado']  = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data']    = $resultadoProceso;

			} catch (Exception $e) {
				$resultado['estado']  = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: '.$e->getMessage();
				$resultado['data']    = ERROR_CONTROLLER_GENERICO;
			}

			// Para que retorne el JSON
			header('Content-Type: application/json');
			echo JsonHelper::get()->serializar($resultado);
		}
	}

	/**
	 * Se agrega una digitalización
	 */
	public function agregardigitalizacion() {
		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');
		} else {

			try {
				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				// No utilizo el JsonHelper porque es un json con un array "crudo".
				$jsonData = file_get_contents('php://input');

				$parametros = json_decode($jsonData);

				if (!(array_key_exists('digitalizacion', $parametros)))
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "digitalizacion".');

				$f_digitalizacion = $parametros->digitalizacion;

				$resultadoProceso = NG::expedientes()->agregardigitalizacion($f_digitalizacion);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se sobreescribe una digitalización
	 */
	public function sobreescribirdigitalizacion() {
		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');
		} else {

			try {
				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				// No utilizo el JsonHelper porque es un json con un array "crudo".
				$jsonData = file_get_contents('php://input');

				$parametros = json_decode($jsonData);

				if (!(array_key_exists('digitalizacion', $parametros)))
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "digitalizacion".');

				$f_digitalizacion = $parametros->digitalizacion;

				$resultadoProceso = NG::expedientes()->sobreescribirdigitalizacion($f_digitalizacion);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * 21/07/2020 XXXX
	 * Se elimina una digitalización temporal de un expediente determinado
	 * @param [type] $requestParams [description]
	 */
	public function eliminardigitalizaciontemporal($requestParams)
	{
		// Antes que nada verifico el nivel de acceso. No redirecciono al home en caso de error.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {
			// Parametro
			$f_archivo = Validator::get()->obtenerDefault($requestParams['archivo']);

			try {
				// Se elimina la digitalización temporal del expediente respectivo
				NG::expedientes()->eliminarDigitalizacionTemporal($f_archivo);

				// Se obtienen todas las digitalizaciones temporales
				$documentos = NG::expedientes()->obtenerDigitalizacionesACargar();

				// Elimino el dato de las rutas locales para evitar un 'information leak'
				foreach ($documentos as $d)
					$d['ruta_completa'] = '';

				// Simulo la paginación
				if (count($documentos) > 0) {
					$paginas = array_chunk($documentos, 16);
					// Para obtener la pagina, hago la division entera entre el start y el length
					$resultado['data'] = $paginas[floor(0 / 16)];
				} else
					$resultado['data'] = array();

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = count($documentos);
				$resultado['recordsFiltered'] = count($documentos);
			}
			catch (Exception $ex)
			{
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array();  // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}
}
?>
