<?php
/**
 * Clase de controlador para la Carga de Proyectos
 *
 * @author XXXX y XXXX
 */
class BECargaProyectosController extends BaseController {
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct() {
		// Llamada al constructor del padre
		parent::__construct();

		// Seteo de ruta base de la interfaz
		$this->baseUrl = URL_KRAKEN_HTML_BACKEND;

		// Nombre del módulo al que corresponde el controlador
		$this->nombreModulo = 'EXPEDIENTES';

		// Determino las acciones válidas y su nivel de acceso mínimo requerido
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['datagriddocall'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['movetemporal'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['movealltemporal'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['seleccionardocumento'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['uploadtemporal'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['eliminardocumentotemporal'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['eliminardocumentooriginal'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['eliminardigitalizacion'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['eliminardocumentopublico'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['eliminarreservado'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['uploadpublico'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['uploadreservado'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['moverdocumentopublicoareservado'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['moverdocumentoreservadoapublico'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['sobreescribirdocpublico'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['sobreescribirdocreservado'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['eliminardocumentoauxiliar'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['agregardocumentopublicopdf'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['agregardocumentoreservadopdf'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['sobreescribirdocpublicoenmovimiento'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['sobreescribirdocreservadoenmovimiento'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['agregardocumentopublicopdfenmovimiento'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['agregardocumentoreservadopdfenmovimiento'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['uploaddigitalizacionesdeproyectos'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['sobreescribirdigitalizacion'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['agregardigitalizacion'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['uploaddigitalizacionesreservadasdeproyectos'] = NIVEL_ACCESO_OPERADOR;
	}

	/**
	 * Invoca a la vista 'view' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function view($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parámetros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Instancio la vista y la muestro
		$vista = new BECargaProyectosView($paramVista);
		$vista->vistaListado();
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function datagriddocall($requestParams) {
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

				$documentos = NG::expedientes()->obtenerArchivosACargar();

				// Elimino el dato de las rutas locales para evitar un 'information leak' (fuga de información)
				foreach ($documentos as $d) {
					$d['ruta_completa'] = '';
				}

				// Simulo la paginación
				if (count($documentos) > 0) {
					$paginas = array_chunk($documentos, $p_limitLength);
					// Para obtener la pagina, hago la division entera entre el start y el length
					$resultado['data'] = $paginas[floor($p_limitStart / $p_limitLength)];
				} else {
					$resultado['data'] = array();
				}

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = count($documentos);
				$resultado['recordsFiltered'] = count($documentos);
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Invoca a la accion 'movetemporal' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function movetemporal($requestParams) {
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

				if (!(array_key_exists('forzar', $parametros) && array_key_exists('archivos', $parametros))) {
					throw new Exception('Parametros inv&aacute;lidos. Se requieren los par&aacute;metros "forzar" y "archivos".');
				}

				$f_forzar = $parametros->forzar;
				$f_archivos = $parametros->archivos;

				if (!(is_bool($f_forzar) && is_array($f_archivos))) {
					throw new Exception('Tipos de par&aacute;metros inv&aacute;lidos: (bool)forzar y (array)archivos.');
				}

				// el resultado del proceso incluye:
				// 		el nombre del archivo, o un grupo de nombres
				// 		el estado: OK, WARNING (cuando ya existe el documento) o ERROR
				// 		el mensaje que describe el resultado del movimiento
				$resultadoProceso = NG::expedientes()->moverArchivosTemporales($f_archivos, $f_forzar);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Invoca a la accion 'movealltemporal' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function movealltemporal($requestParams) {
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

				if (!array_key_exists('forzar', $parametros)) {
					throw new Exception('Parametros inv&aacute;lidos. Se requiere el par&aacute;metro "forzar".');
				}

				$f_forzar = $parametros->forzar;

				if (!is_bool($f_forzar)) {
					throw new Exception('Tipo de par&aacute;metro inv&aacute;lido: (bool)forzar.');
				}

				// el resultado del proceso incluye:
				// 		el nombre del archivo, o un grupo de nombres
				// 		el estado: OK, WARNING (cuando ya existe el documento) o ERROR
				// 		el mensaje que describe el resultado del movimiento
				$documentos = NG::expedientes()->obtenerArchivosACargar();

				$f_archivos = array();
				foreach ($documentos as $value) {
					$f_archivos[] = $value['archivo'];
				}

				$resultadoProceso = NG::expedientes()->moverArchivosTemporales($f_archivos, $f_forzar);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se muestra el formulario para la carga del documento temporal
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function seleccionardocumento($requestParams) {
		// Antes que nada verificamos el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparamos los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneamos parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Verificamos si nos han pasado parametros por url, para saber durante la carga, a qué expediente, nota o recomendación pertenecerá el documento.
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

		// Ejecutamos la vista
		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance de expediente');

			$paramVista['f_anio'] = $f_anio;
			$paramVista['f_tipo'] = $f_tipo;
			$paramVista['f_numero'] = $f_numero;
			$paramVista['f_cuerpo'] = $f_cuerpo;
			$paramVista['f_alcance'] = $f_alcance;

		} catch (Exception $ex) {
			// Si falla, volvemos al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}

		// Instanciamos la vista y la mostramos
		$vista = new BECargaProyectosView($paramVista);
		$vista->vistaUploadTemporal();
	}

	/**
	 * Se carga un documento como temporal
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function uploadtemporal($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Verifico si me han pasado parametros por url, para un acceso directo a una posición del paginador.
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

			// Se verifica que el expediente exista
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, true);
			if (is_null($expediente)) {
				throw new Exception('Error: El expediente, al cual se desea cargar proyectos, no existe.');
			}

			// Se intenta subir el archivo temporal
			$nombre_temporal = NG::expedientes()->subirArchivoTemporal($expediente, $requestParams['request_files'], 'f_archivo_temporal');

			// Si se ha podido subir el archivo temporal
			if (isset($nombre_temporal) && $nombre_temporal != null) {
				// Se vuelve a la grilla con el expediente respectivo
				$this->redireccionar(
					'expedientes',
					'view',
					array('f_anio' => $f_anio,
						'f_tipo' => $f_tipo,
						'f_numero' => $f_numero,
						'f_cuerpo' => $f_cuerpo,
						'f_alcance' => $f_alcance));
			}

		} catch (Exception $e) {
			// Mensaje de error (redirecciono para mantener el error de sesion y mostrarlo)
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('cargaproyectos', 'seleccionardocumento',
				array('f_anio' => $f_anio,
					'f_tipo' => $f_tipo,
					'f_numero' => $f_numero,
					'f_cuerpo' => $f_cuerpo,
					'f_alcance' => $f_alcance));
		}
	}

	/**
	 * 21/07/2020 XXXX
	 * Se elimina el documento temporal de un expediente determinado
	 * @param [type] $requestParams [description]
	 */
	public function eliminardocumentotemporal($requestParams) {
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
				// Se elimina el documento temporal del expediente respectivo
				NG::expedientes()->eliminarDocumentoTemporal($f_archivo);

				$documentos = NG::expedientes()->obtenerArchivosACargar();

				// Elimino el dato de las rutas locales para evitar un 'information leak'
				foreach ($documentos as $d) {
					$d['ruta_completa'] = '';
				}

				// Simulo la paginación
				if (count($documentos) > 0) {
					$paginas = array_chunk($documentos, 16);
					// Para obtener la pagina, hago la division entera entre el start y el length
					$resultado['data'] = $paginas[floor(0 / 16)];
				} else {
					$resultado['data'] = array();
				}

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = count($documentos);
				$resultado['recordsFiltered'] = count($documentos);
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina el documento original de un expediente determinado
	 * @param [type] $requestParams [description]
	 */
	public function eliminardocumentooriginal($requestParams) {
		// Antes que nada verifico el nivel de acceso. No redirecciono al home en caso de error.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {
			// Parametros
			$f_anio = Validator::get()->obtenerDefault($requestParams['anio']);
			$f_tipo = Validator::get()->obtenerDefault($requestParams['tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['numero']);
			$f_archivo = Validator::get()->obtenerDefault($requestParams['archivo']);

			try {
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				// La clave del expediente es "obligatoria". Si no me la proveen, simulo un resultado vacío.
				if (is_null($f_anio) || $f_anio == '' || is_null($f_tipo) || $f_tipo == '' || is_null($f_numero) || $f_numero == '') {
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				} else {
					// Se elimina el documento original del expediente respectivo
					NG::expedientes()->eliminarDocumentoOriginal($f_anio, $f_tipo, $f_numero, $f_archivo);

					$expedientes = NG::expedientes()->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
					if (count($expedientes) == 0) {
						throw new Exception('Expediente inexistente.');
					}

					$documentos = NG::expedientes()->obtenerArchivosProyecto($expedientes[0]);

					// Elimino el dato de las rutas locales para evitar un 'information leak'
					foreach ($documentos as $d) {
						$d['ruta_completa'] = '';
					}

					// Simulo la paginación
					if (count($documentos) > 0) {
						$paginas = array_chunk($documentos, 8);
						// Para obtener la pagina, hago la division entera entre el start y el length
						$resultado['data'] = $paginas[floor(0 / 8)];
					} else {
						$resultado['data'] = array();
					}

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = count($documentos);
					$resultado['recordsFiltered'] = count($documentos);
				}
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina la digitalización de un expediente determinado
	 * @param [type] $requestParams [description]
	 */
	public function eliminardigitalizacion($requestParams) {
		// Antes que nada verifico el nivel de acceso. No redirecciono al home en caso de error.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {
			// Parametros
			$f_anio = Validator::get()->obtenerDefault($requestParams['anio']);
			$f_tipo = Validator::get()->obtenerDefault($requestParams['tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['numero']);
			$f_archivo = Validator::get()->obtenerDefault($requestParams['archivo']);

			try {
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				// La clave del expediente es "obligatoria". Si no me la proveen, simulo un resultado vacío.
				if (is_null($f_anio) || $f_anio == '' || is_null($f_tipo) || $f_tipo == '' || is_null($f_numero) || $f_numero == '') {
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				} else {
					// Se elimina el documento original del expediente respectivo
					NG::expedientes()->eliminarDigitalizacion($f_anio, $f_tipo, $f_numero, $f_archivo);

					$expedientes = NG::expedientes()->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
					if (count($expedientes) == 0) {
						throw new Exception('Expediente inexistente.');
					}

					$documentos = NG::expedientes()->obtenerArchivosProyecto($expedientes[0]);

					$digitalizaciones = NG::expedientes()->obtenerArchivosDigitalizacion($expedientes[0]);

					// Eliminamos el dato de las rutas locales para evitar un 'information leak'
					foreach ($documentos as $d) {
						$d['ruta_completa'] = '';
					}

					foreach ($digitalizaciones as $digi) {
						$digi['ruta_completa'] = '';
						// Si se trata de la Digitalización Cargada
						if ($digi['tipo'] == 'digitalizada') {
							// Se agrega como documento
							$documentos[] = $digi;
						}
					}

					// Simulo la paginación
					if (count($documentos) > 0) {
						$paginas = array_chunk($documentos, 8);
						// Para obtener la pagina, hago la division entera entre el start y el length
						$resultado['data'] = $paginas[floor(0 / 8)];
					} else {
						$resultado['data'] = array();
					}

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = count($documentos);
					$resultado['recordsFiltered'] = count($documentos);
				}
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina el documento público de un expediente determinado
	 * @param [type] $requestParams [description]
	 */
	public function eliminardocumentopublico($requestParams) {
		// Antes que nada verifico el nivel de acceso. No redirecciono al home en caso de error.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {
			// Parametros
			$f_anio = Validator::get()->obtenerDefault($requestParams['anio']);
			$f_tipo = Validator::get()->obtenerDefault($requestParams['tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['numero']);
			$f_archivo = Validator::get()->obtenerDefault($requestParams['archivo']);

			try {
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				// La clave del expediente es "obligatoria". Si no me la proveen, simulo un resultado vacío.
				if (is_null($f_anio) || $f_anio == '' || is_null($f_tipo) || $f_tipo == '' || is_null($f_numero) || $f_numero == '') {
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				} else {
					// Se elimina el documento original del expediente respectivo
					NG::expedientes()->eliminarDocumentoOriginal($f_anio, $f_tipo, $f_numero, $f_archivo);

					$expedientes = NG::expedientes()->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
					if (count($expedientes) == 0) {
						throw new Exception('Expediente inexistente.');
					}

					$documentos = NG::expedientes()->obtenerArchivosProyecto($expedientes[0]);

					// Elimino el dato de las rutas locales para evitar un 'information leak'
					foreach ($documentos as $d) {
						$d['ruta_completa'] = '';
					}

					// Simulo la paginación
					if (count($documentos) > 0) {
						$paginas = array_chunk($documentos, 8);
						// Para obtener la pagina, hago la division entera entre el start y el length
						$resultado['data'] = $paginas[floor(0 / 8)];
					} else {
						$resultado['data'] = array();
					}

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = count($documentos);
					$resultado['recordsFiltered'] = count($documentos);
				}
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina un documento reservado de un expediente determinado
	 * @param [type] $requestParams [description]
	 */
	public function eliminarreservado($requestParams) {
		// Antes que nada verifico el nivel de acceso. No redirecciono al home en caso de error.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {
			// Parametros
			$f_anio = Validator::get()->obtenerDefault($requestParams['anio']);
			$f_tipo = Validator::get()->obtenerDefault($requestParams['tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['numero']);
			$f_archivo = Validator::get()->obtenerDefault($requestParams['archivo']);

			try {
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				// La clave del expediente es "obligatoria". Si no me la proveen, simulo un resultado vacío.
				if (is_null($f_anio) || $f_anio == '' || is_null($f_tipo) || $f_tipo == '' || is_null($f_numero) || $f_numero == '') {
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				} else {
					// Se elimina el documento reservado del expediente respectivo
					NG::expedientes()->eliminarReservado($f_anio, $f_tipo, $f_numero, $f_archivo);

					$expedientes = NG::expedientes()->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
					if (count($expedientes) == 0) {
						throw new Exception('Expediente inexistente.');
					}

					$reservados = NG::expedientes()->obtenerArchivosReservados($expedientes[0]);

					// Eliminamos el dato de las rutas locales para evitar un 'information leak'
					foreach ($reservados as $r) {
						$r['ruta_completa'] = '';
					}

					// Si posee documentos reservados
					if (count($reservados) > 0) {
						// Se ordena nuevamente el listado de documentos, por fecha de forma descendente
						foreach ($reservados as $key => $row) {
							$aux[$key] = substr($row['fecha'], 6, 4) . substr($row['fecha'], 3, 2) . substr($row['fecha'], 0, 2);
						}
						array_multisort($aux, SORT_DESC, $reservados);
					}

					// Simulo la paginación
					if (count($reservados) > 0) {
						$paginas = array_chunk($reservados, $p_limitLength);
						// Para obtener la pagina, hago la division entera entre el start y el length
						$resultado['data'] = $paginas[floor($p_limitStart / $p_limitLength)];
					} else {
						$resultado['data'] = array();
					}

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = count($reservados);
					$resultado['recordsFiltered'] = count($reservados);
				}
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	public function verificarExistenciaReservado($anio, $tipo, $numero, $nombre_archivo) {
		// Nombre codificado del directorio del expediente respectivo
		$nombre_codificado = sprintf("%02d%s%05d", $anio % 100, $tipo, $numero);
		// Ruta del directorio remoto de públicos, determinado por la clave del expediente respectivo
		$directorio_reservados = PATH_KRAKEN_RESOURCES_PROYECTOS . $anio . "/" . $nombre_codificado . "/reservados";

		return (file_exists($directorio_reservados . '/' . $nombre_archivo));
	}

	/**
	 * Se carga un documento Reservado
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function uploadreservado($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Verifico si me han pasado parametros por url, para un acceso directo a una posición del paginador.
		$f_anio = Validator::get()->obtenerDefault($requestParams['dr_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['dr_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['dr_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['dr_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['dr_alcance']);

		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

			// Se verifica que el expediente exista
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, true);
			if (is_null($expediente)) {
				throw new Exception('Error: El expediente, al cual se desea cargar el documento, no existe.');
			}

			if ($requestParams['request_files']['f_archivo_reservado']['type'] == 'application/x-ms-dos-executable' ||
				$requestParams['request_files']['f_archivo_reservado']['type'] == 'application/x-php' ||
				$requestParams['request_files']['f_archivo_reservado']['type'] == 'application/x-javascript') {

				throw new Exception('Atención: El tipo de documento no es permitido.');
			}

			// Nombre del documento Reservado
			$nombre_archivo = $requestParams['request_files']['f_archivo_reservado']['name'];

			// Si ya existe
			if ($this->verificarExistenciaReservado($f_anio, $f_tipo, $f_numero, $nombre_archivo)) {
				// Se almacena en sesión el nombre del documento Reservado existente
				$_SESSION['doc_reservado_existente'] = $nombre_archivo;
				// Se sube como un documento auxiliar, para mantenerlo mientras se le consulta al usuario
				NG::expedientes()->subirDocumentoReservadoAuxiliar($expediente, $requestParams['request_files']);
			} else {
				$_SESSION['doc_reservado_existente'] = 0;
				// Sino, se sube al directorio respectivo
				NG::expedientes()->subirDocumentoReservado($expediente, $requestParams['request_files']);
			}

			$paramVista['f_anio'] = $f_anio;
			$paramVista['f_tipo'] = $f_tipo;
			$paramVista['f_numero'] = $f_numero;
			$paramVista['f_cuerpo'] = $f_cuerpo;
			$paramVista['f_alcance'] = $f_alcance;

			// Instancio la vista y la muestro
			$vista = new BEProyectosView($paramVista);
			$vista->vistaListado();

		} catch (Exception $e) {
			// Mensaje de error (redirecciono para mantener el error de sesion y mostrarlo)
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('proyectos', 'view',
				array(
					'f_anio' => $f_anio,
					'f_tipo' => $f_tipo,
					'f_numero' => $f_numero,
					'f_cuerpo' => $f_cuerpo,
					'f_alcance' => $f_alcance));
		}
	}

	public function verificarExistenciaPublico($anio, $tipo, $numero, $nombre_archivo) {
		// Nombre codificado del directorio del expediente respectivo
		$nombre_codificado = sprintf("%02d%s%05d", $anio % 100, $tipo, $numero);
		// Ruta del directorio remoto de públicos, determinado por la clave del expediente respectivo
		$directorio_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $anio . "/" . $nombre_codificado;

		return (file_exists($directorio_expediente . '/' . $nombre_archivo));
	}

	/**
	 * Se carga un documento Público
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function uploadpublico($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Verifico si me han pasado parametros por url, para un acceso directo a una posición del paginador.
		$f_anio = Validator::get()->obtenerDefault($requestParams['dp_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['dp_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['dp_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['dp_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['dp_alcance']);

		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

			// Se verifica que el expediente exista
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, true);
			if (is_null($expediente)) {
				throw new Exception('Error: El expediente, al cual se desea cargar el documento, no existe.');
			}

			if ($requestParams['request_files']['f_archivo_publico']['type'] == 'application/x-ms-dos-executable' ||
				$requestParams['request_files']['f_archivo_publico']['type'] == 'application/x-php' ||
				$requestParams['request_files']['f_archivo_publico']['type'] == 'application/x-javascript') {

				throw new Exception('Atención: El tipo de documento no es permitido.');
			}

			// Nombre del documento público
			$nombre_archivo = $requestParams['request_files']['f_archivo_publico']['name'];

			// Si ya existe
			if ($this->verificarExistenciaPublico($f_anio, $f_tipo, $f_numero, $nombre_archivo)) {
				// Se almacena en sesión el nombre del documento público existente
				$_SESSION['doc_publico_existente'] = $nombre_archivo;
				// Se sube como un documento auxiliar, para mantenerlo mientras se le consulta al usuario
				NG::expedientes()->subirDocumentoAuxiliar($expediente, $requestParams['request_files']);
			} else {
				$_SESSION['doc_publico_existente'] = 0;
				// Sino, se lo sube al directorio respectivo
				NG::expedientes()->subirDocumentoPublico($expediente, $requestParams['request_files']);
			}

			$paramVista['f_anio'] = $f_anio;
			$paramVista['f_tipo'] = $f_tipo;
			$paramVista['f_numero'] = $f_numero;
			$paramVista['f_cuerpo'] = $f_cuerpo;
			$paramVista['f_alcance'] = $f_alcance;

			// Instancio la vista y la muestro
			$vista = new BEProyectosView($paramVista);
			$vista->vistaListado();

		} catch (Exception $e) {
			// Mensaje de error (redirecciono para mantener el error de sesion y mostrarlo)
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('proyectos', 'view',
				array(
					'f_anio' => $f_anio,
					'f_tipo' => $f_tipo,
					'f_numero' => $f_numero,
					'f_cuerpo' => $f_cuerpo,
					'f_alcance' => $f_alcance));
		}
	}

	/**
	 * Se mueve un documento al directorio de los reservados
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function moverdocumentopublicoareservado($requestParams) {
		// Antes que nada verifico el nivel de acceso. No redirecciono al home en caso de error.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {
			// Parametros
			$f_anio = Validator::get()->obtenerDefault($requestParams['anio']);
			$f_tipo = Validator::get()->obtenerDefault($requestParams['tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['numero']);
			$f_archivo = Validator::get()->obtenerDefault($requestParams['archivo']);

			try {
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				// La clave del expediente es "obligatoria". Si no me la proveen, simulo un resultado vacío.
				if (is_null($f_anio) || $f_anio == '' || is_null($f_tipo) || $f_tipo == '' || is_null($f_numero) || $f_numero == '') {
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				} else {
					// Se verifica que el expediente exista
					$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, 0, 0, true);
					if (is_null($expediente)) {
						throw new Exception('Error: El expediente, al cual se desea mover el documento, no existe.');
					}

					// Si ya existe el documento público
					if ($this->verificarExistenciaReservado($f_anio, $f_tipo, $f_numero, $f_archivo)) {
						$resultado['estado'] = 'WARNING';
						$resultado['mensaje'] = sprintf('El documento ya existe para el expediente %d-%s-%d.', $f_anio, $f_tipo, $f_numero);
						$resultado['data'] = $f_archivo;
					} else {
						// Se mueve el documento público al directorio reservados, del expediente respectivo
						$resultadoProceso = NG::expedientes()->moverDocumentoPublicoToReservado($f_anio, $f_tipo, $f_numero, $f_archivo);

						// Genero respuesta
						$resultado['estado'] = 'OK';
						$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
						$resultado['data'] = $resultadoProceso;
					}
				}
			} catch (Exception $ex) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se mueve un documento reservado al directorio AATNNNNN (se retira de reservados/)
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function moverdocumentoreservadoapublico($requestParams) {
		// Antes que nada verifico el nivel de acceso. No redirecciono al home en caso de error.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {
			// Parametros
			$f_anio = Validator::get()->obtenerDefault($requestParams['anio']);
			$f_tipo = Validator::get()->obtenerDefault($requestParams['tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['numero']);
			$f_archivo = Validator::get()->obtenerDefault($requestParams['archivo']);

			try {
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				// La clave del expediente es "obligatoria". Si no me la proveen, simulo un resultado vacío.
				if (is_null($f_anio) || $f_anio == '' || is_null($f_tipo) || $f_tipo == '' || is_null($f_numero) || $f_numero == '') {
					$resultado['estado'] = 'ERROR';
					$resultado['mensaje'] = 'No se han recibido los datos del expediente';
					$resultado['data'] = array();
				} else {
					// Se verifica que el expediente exista
					$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, 0, 0, true);
					if (is_null($expediente)) {
						throw new Exception('Error: El expediente, al cual se desea mover el documento, no existe.');
					}

					// Si ya existe el documento público
					if ($this->verificarExistenciaPublico($f_anio, $f_tipo, $f_numero, $f_archivo)) {
						$resultado['estado'] = 'WARNING';
						$resultado['mensaje'] = sprintf('El documento ya existe para el expediente %d-%s-%d.', $f_anio, $f_tipo, $f_numero);
						$resultado['data'] = $f_archivo;
					} else {
						// Se mueve un documento reservado al directorio AATNNNNN (se retira de reservados/)
						$resultadoProceso = NG::expedientes()->moverDocumentoReservadoToPublico($f_anio, $f_tipo, $f_numero, $f_archivo);

						// Genero respuesta
						$resultado['estado'] = 'OK';
						$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
						$resultado['data'] = $resultadoProceso;
					}
				}
			} catch (Exception $ex) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se sobreescribe un documento público
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function sobreescribirdocpublico($requestParams) {
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

				if (!array_key_exists('anio', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "anio".');
				}
				if (!array_key_exists('tipo', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "tipo".');
				}
				if (!array_key_exists('numero', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "numero".');
				}
				if (!array_key_exists('documento_existente', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "documento_existente".');
				}

				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($parametros->anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($parametros->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($parametros->numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				$documento_existente = $parametros->documento_existente;

				$resultadoProceso = NG::expedientes()->sobreescribirDocPublico($f_anio, $f_tipo, $f_numero, $documento_existente);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $ex) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $ex->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}
		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se agrega un documento público pdf a uno existente
	 */
	public function agregardocumentopublicopdf() {
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

				if (!array_key_exists('anio', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "anio".');
				}
				if (!array_key_exists('tipo', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "tipo".');
				}
				if (!array_key_exists('numero', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "numero".');
				}
				if (!array_key_exists('documento_existente', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "documento_existente".');
				}

				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($parametros->anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($parametros->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($parametros->numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				$documento_existente = $parametros->documento_existente;

				$resultadoProceso = NG::expedientes()->agregarDocumentoPublicoPDF($f_anio, $f_tipo, $f_numero, $documento_existente);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se sobreescribe un documento Reservado
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function sobreescribirdocreservado($requestParams) {
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

				if (!array_key_exists('anio', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "anio".');
				}
				if (!array_key_exists('tipo', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "tipo".');
				}
				if (!array_key_exists('numero', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "numero".');
				}
				if (!array_key_exists('documento_existente', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "documento_existente".');
				}

				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($parametros->anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($parametros->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($parametros->numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				$documento_existente = $parametros->documento_existente;

				$resultadoProceso = NG::expedientes()->sobreescribirDocReservado($f_anio, $f_tipo, $f_numero, $documento_existente);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $ex) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}
		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se agrega un documento reservado pdf a uno existente
	 */
	public function agregardocumentoreservadopdf() {
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

				if (!array_key_exists('anio', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "anio".');
				}
				if (!array_key_exists('tipo', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "tipo".');
				}
				if (!array_key_exists('numero', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "numero".');
				}
				if (!array_key_exists('documento_existente', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "documento_existente".');
				}

				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($parametros->anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($parametros->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($parametros->numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				$documento_existente = $parametros->documento_existente;

				$resultadoProceso = NG::expedientes()->agregarDocumentoReservadoPDF($f_anio, $f_tipo, $f_numero, $documento_existente);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina un documento reservado de un expediente determinado
	 * @param [type] $requestParams [description]
	 */
	public function eliminardocumentoauxiliar($requestParams) {
		// Antes que nada verifico el nivel de acceso. No redirecciono al home en caso de error.
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

				if (!array_key_exists('anio', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "anio".');
				}
				if (!array_key_exists('tipo', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "tipo".');
				}
				if (!array_key_exists('numero', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "numero".');
				}
				if (!array_key_exists('documento_auxiliar', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "documento_auxiliar".');
				}

				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($parametros->anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($parametros->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($parametros->numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				$f_archivo = $parametros->documento_auxiliar;

				// Se elimina el documento auxiliar
				NG::expedientes()->eliminarDocumentoAuxiliar($f_anio, $f_tipo, $f_numero, $f_archivo);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = true;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se sobreescribe un documento público que se desea mover desde los reservados
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function sobreescribirdocpublicoenmovimiento($requestParams) {
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

				if (!array_key_exists('anio', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "anio".');
				}
				if (!array_key_exists('tipo', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "tipo".');
				}
				if (!array_key_exists('numero', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "numero".');
				}
				if (!array_key_exists('documento_existente', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "documento_existente".');
				}

				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($parametros->anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($parametros->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($parametros->numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				$documento_existente = $parametros->documento_existente;

				$resultadoProceso = NG::expedientes()->sobreescribirDocPublicoEnMovimiento($f_anio, $f_tipo, $f_numero, $documento_existente);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $ex) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}
		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se sobreescribe un documento reservado que se desea mover desde los públicos
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function sobreescribirdocreservadoenmovimiento($requestParams) {
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

				if (!array_key_exists('anio', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "anio".');
				}
				if (!array_key_exists('tipo', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "tipo".');
				}
				if (!array_key_exists('numero', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "numero".');
				}
				if (!array_key_exists('documento_existente', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "documento_existente".');
				}

				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($parametros->anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($parametros->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($parametros->numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				$documento_existente = $parametros->documento_existente;

				$resultadoProceso = NG::expedientes()->sobreescribirDocReservadoEnMovimiento($f_anio, $f_tipo, $f_numero, $documento_existente);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $ex) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}
		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se agrega un documento público pdf a uno existente, durante un movimiento
	 */
	public function agregardocumentopublicopdfenmovimiento() {
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

				if (!array_key_exists('anio', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "anio".');
				}
				if (!array_key_exists('tipo', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "tipo".');
				}
				if (!array_key_exists('numero', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "numero".');
				}
				if (!array_key_exists('documento_existente', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "documento_existente".');
				}

				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($parametros->anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($parametros->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($parametros->numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				$documento_existente = $parametros->documento_existente;

				$resultadoProceso = NG::expedientes()->agregarDocumentoPublicoPdfEnMovimiento($f_anio, $f_tipo, $f_numero, $documento_existente);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se agrega un documento público pdf a uno existente, durante un movimiento
	 */
	public function agregardocumentoreservadopdfenmovimiento() {
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

				if (!array_key_exists('anio', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "anio".');
				}
				if (!array_key_exists('tipo', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "tipo".');
				}
				if (!array_key_exists('numero', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "numero".');
				}
				if (!array_key_exists('documento_existente', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "documento_existente".');
				}

				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($parametros->anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($parametros->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($parametros->numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				$documento_existente = $parametros->documento_existente;

				$resultadoProceso = NG::expedientes()->agregarDocumentoReservadoPdfEnMovimiento($f_anio, $f_tipo, $f_numero, $documento_existente);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se verifica la existencia de una Digitalización
	 * @param  integer $anio              Año del expediente
	 * @param  String  $tipo              Tipo del expediente
	 * @param  integer $numero            Número del expediente
	 * @param  string  $nombre_codificado Nombre de la digitalización
	 * @param  integer $es_reservada      Si es reservada o no
	 * @return boolean                    True|False
	 */
	public function verificarExistenciaDigitalizacion($anio, $tipo, $numero, $nombre_codificado, $es_reservada = false) {

		// Ruta del directorio remoto de públicos, determinado por la clave del expediente respectivo
		$directorio_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $anio . "/" . $nombre_codificado;
		// Se define la ruta correcta, tomando en cuenta si es reservada o no
		$ruta = ($es_reservada) ? $directorio_expediente . '/reservados/' : $directorio_expediente . '/';

		return (file_exists($ruta . $nombre_codificado . '.pdf'));
	}

	/**
	 * Se carga una Digitalización (desde la solapa de Proyectos)
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function uploaddigitalizacionesdeproyectos($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Verifico si me han pasado parametros por url, para un acceso directo a una posición del paginador.
		$f_anio = Validator::get()->obtenerDefault($requestParams['digi_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['digi_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['digi_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['digi_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['digi_alcance']);

		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

			// Se verifica que el expediente exista
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, true);
			if (is_null($expediente)) {
				throw new Exception('Error: El expediente, al cual se desea cargar el documento, no existe.');
			}

			if ($requestParams['request_files']['f_digitalizacion']['type'] != 'application/pdf') {
				throw new Exception('Atención: El documento no es un PDF.');
			}

			// Nombre codificado del directorio del expediente respectivo
			$nombre_codificado = sprintf("%02d%s%05d", $f_anio % 100, $f_tipo, $f_numero);

			// Si ya existe
			if ($this->verificarExistenciaDigitalizacion($f_anio, $f_tipo, $f_numero, $nombre_codificado, false)) {
				// Se almacena en sesión el nombre de la digitalización existente
				$_SESSION['digitalizacion_existente'] = $nombre_codificado . '.pdf';
				// Se sube como un documento auxiliar, para mantenerlo mientras se le consulta al usuario
				NG::expedientes()->subirDigitalizacionAuxiliar($expediente, $requestParams['request_files']);
			} else {
				$_SESSION['digitalizacion_existente'] = 0;
				// Sino, se sube al directorio respectivo
				NG::expedientes()->subirDigitalizacionDirectamente($expediente, $requestParams['request_files']);
			}

			$paramVista['f_anio'] = $f_anio;
			$paramVista['f_tipo'] = $f_tipo;
			$paramVista['f_numero'] = $f_numero;
			$paramVista['f_cuerpo'] = $f_cuerpo;
			$paramVista['f_alcance'] = $f_alcance;

			// Instancio la vista y la muestro
			$vista = new BEProyectosView($paramVista);
			$vista->vistaListado();

		} catch (Exception $e) {
			// Mensaje de error (redirecciono para mantener el error de sesion y mostrarlo)
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('proyectos', 'view',
				array(
					'f_anio' => $f_anio,
					'f_tipo' => $f_tipo,
					'f_numero' => $f_numero,
					'f_cuerpo' => $f_cuerpo,
					'f_alcance' => $f_alcance));
		}
	}

	/**
	 * Se carga una Digitalización (desde la solapa de Proyectos)
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function uploaddigitalizacionesreservadasdeproyectos($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Verifico si me han pasado parametros por url, para un acceso directo a una posición del paginador.
		$f_anio = Validator::get()->obtenerDefault($requestParams['digi_reservada_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['digi_reservada_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['digi_reservada_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['digi_reservada_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['digi_reservada_alcance']);

		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

			// Se verifica que el expediente exista
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, true);
			if (is_null($expediente)) {
				throw new Exception('Error: El expediente, al cual se desea cargar el documento, no existe.');
			}

			if ($requestParams['request_files']['f_digitalizacion']['type'] != 'application/pdf') {
				throw new Exception('Atención: El documento no es un PDF.');
			}

			// Nombre codificado del directorio del expediente respectivo
			$nombre_codificado = sprintf("%02d%s%05d", $f_anio % 100, $f_tipo, $f_numero);

			// Si ya existe
			if ($this->verificarExistenciaDigitalizacion($f_anio, $f_tipo, $f_numero, $nombre_codificado, true)) {
				$_SESSION['digitalizacion_reservada_existente'] = $nombre_codificado . '.pdf';
				// Se sube como un documento auxiliar, para mantenerlo mientras se le consulta al usuario
				NG::expedientes()->subirDigitalizacionAuxiliar($expediente, $requestParams['request_files']);
			} else {
				$_SESSION['digitalizacion_reservada_existente'] = 0;
				// Sino, se sube al directorio respectivo
				NG::expedientes()->subirDigitalizacionReservadaDirectamente($expediente, $requestParams['request_files']);
			}

			$paramVista['f_anio'] = $f_anio;
			$paramVista['f_tipo'] = $f_tipo;
			$paramVista['f_numero'] = $f_numero;
			$paramVista['f_cuerpo'] = $f_cuerpo;
			$paramVista['f_alcance'] = $f_alcance;

			// Instancio la vista y la muestro
			$vista = new BEProyectosView($paramVista);
			$vista->vistaListado();

		} catch (Exception $e) {
			// Mensaje de error (redirecciono para mantener el error de sesion y mostrarlo)
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('proyectos', 'view',
				array(
					'f_anio' => $f_anio,
					'f_tipo' => $f_tipo,
					'f_numero' => $f_numero,
					'f_cuerpo' => $f_cuerpo,
					'f_alcance' => $f_alcance));
		}
	}

	/**
	 * Se sobreescribe una digitalizacion
	 */
	public function sobreescribirdigitalizacion($requestParams) {
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

				if (!array_key_exists('anio', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "anio".');
				}
				if (!array_key_exists('tipo', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "tipo".');
				}
				if (!array_key_exists('numero', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "numero".');
				}
				if (!array_key_exists('digitalizacion_existente', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "digitalizacion_existente".');
				}
				if (!array_key_exists('es_reservada', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "es_reservada".');
				}

				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($parametros->anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($parametros->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($parametros->numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				$digitalizacion_existente = $parametros->digitalizacion_existente;
				$es_reservada = $parametros->es_reservada;

				// Se utiliza el MISMO método que los documentos públicos
				$resultadoProceso = NG::expedientes()->sobreescribirDigitalizacionDirectamente($f_anio, $f_tipo, $f_numero, $digitalizacion_existente, $es_reservada);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $ex) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $ex->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}
		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se agrega una digitalizacion a una existente
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

				if (!array_key_exists('anio', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "anio".');
				}
				if (!array_key_exists('tipo', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "tipo".');
				}
				if (!array_key_exists('numero', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "numero".');
				}
				if (!array_key_exists('digitalizacion_existente', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "digitalizacion_existente".');
				}
				if (!array_key_exists('es_reservada', $parametros)) {
					throw new Exception('Parametro inv&aacute;lido. Se requiere el par&aacute;metro "es_reservada".');
				}

				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($parametros->anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($parametros->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($parametros->numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');

				$digitalizacion_existente = $parametros->digitalizacion_existente;
				$es_reservada = $parametros->es_reservada;

				$resultadoProceso = NG::expedientes()->agregarDigitalizacionDirectamente($digitalizacion_existente, $es_reservada);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Operaci&oacute;n realizada con &eacute;xito.';
				$resultado['data'] = $resultadoProceso;

			} catch (Exception $ex) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'Fallo en operaci&oacute;n. Causa: ' . $ex->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

}
?>
