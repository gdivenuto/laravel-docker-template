<?php
/**
 * Clase de controlador de Gestion de Documentos Pendientes del Expediente Electronico
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_FIRMAS');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_FIRMAS');

class BEExpedienteselecpendController extends BaseController
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
		// ...

		// Se sobreescriben permisos, si aplica:
		$this->accionesPermitidas['verfirmantes'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['verrevision'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['datagrid'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['verdocumento'] = NIVEL_ACCESO_CONCEJAL; // los pendientes sólo el Concejal
		$this->accionesPermitidas['listarembebidos'] = NIVEL_ACCESO_CONCEJAL; // los pendientes sólo el Concejal
		$this->accionesPermitidas['verembebido'] = NIVEL_ACCESO_CONCEJAL; // los pendientes sólo el Concejal
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

		// Instancio la vista y la muestro
		$vista = new BEExpedientesElecPendView($paramVista);
		$vista->vistaPendientesRevision();
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function datagrid($requestParams)
	{
		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {

			$resultado['recordsTotal'] = 0;
			$resultado['recordsFiltered'] = 0;
			$resultado['data'] = array();
			$resultado['error'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['numeroError'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {

			// Parametros de filtro
			//$f_detalle = Validator::get()->obtenerDefault($requestParams['f_detalle']);

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
			$resultado['draw'] = $p_draw; // Es un valor entero para control interno del DataTable
			try
			{
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				//$f_detalle = Validator::get()->validar($f_detalle, PATRON_NUMEROS, true, 'Detalle del documento');

				$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
				$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

				$revisiones = NG::revExpedienteElecPend()->obtenerRevisionesPendientesUsuario(
					$this->obtenerUsuarioActual(),
					// Control de consulta
					['T.`fecha_hora_entrada` ASC'], // La firma pendiente mas vieja primero
					$p_limitLength, // cantidad de registros (paginación)
					$p_limitStart); // corrimiento de registros (paginación)

				// Consulta de cantidad de revisiones para el usuario
				$cantidadTotalRevisiones = NG::revExpedienteElecPend()->obtenerRevisionesPendientesUsuarioCantidad(
					$this->obtenerUsuarioActual()
				);

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = $cantidadTotalRevisiones;
				$resultado['recordsFiltered'] = $cantidadTotalRevisiones;
				$resultado['data'] = $revisiones;
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
	 * Esta acción del controlador devuelve un JSON con toda la información de los
	 * firmantes del documento pendiente.
 	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function verfirmantes($requestParams)
	{
		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = [];

		// Validación de parámetros de búsqueda
		try {
			$f_anio = Validator::get()->validar($requestParams['anio'], PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($requestParams['tipo'], PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($requestParams['numero'], PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($requestParams['cuerpo'], PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($requestParams['alcance'], PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden = Validator::get()->validar($requestParams['orden'], PATRON_NUMEROS, false, 'Orden del documento electronico pendiente');

			$firmas_pend = NG::firmasExpedienteElecPend()->obtenerFirmasExpedienteElecPend($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden);
			if (count($firmas_pend) == 0)
				throw new Exception('No se han encontrado un registro de firmas para documento pendiente del expediente electrónico.');

			$resultado['estado'] = 'OK';
			$resultado['mensaje'] = 'Firmas pendientes para el documento a revisar obtenidas con éxito.';
			$resultado['data'] = $firmas_pend;

		} catch (Exception $ex)	{
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = 'No se puedo obtener las firmas pendientes para el documento a revisar obtenidas con éxito. Causa: '.$ex->getMessage();
			$resultado['data'] = null;
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Esta acción del controlador devuelve un JSON con toda la información de las
	 * revisiones del documento pendiente.
 	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function verrevision($requestParams)
	{
		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = [];

		// Validación de parámetros de búsqueda
		try {
			$f_anio = Validator::get()->validar($requestParams['anio'], PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($requestParams['tipo'], PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($requestParams['numero'], PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($requestParams['cuerpo'], PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($requestParams['alcance'], PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden = Validator::get()->validar($requestParams['orden'], PATRON_NUMEROS, false, 'Orden del documento electronico pendiente');

			$revisiones = NG::revExpedienteElecPend()->obtenerRevsExpedienteElecPend($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden);
			if (count($revisiones) == 0)
				throw new Exception('No se han encontrado un registro de revisiones para documento pendiente del expediente electrónico.');

			$resultado['estado'] = 'OK';
			$resultado['mensaje'] = 'Revisiones para el documento a revisar obtenidas con éxito.';
			$resultado['data'] = $revisiones;

		} catch (Exception $ex)	{
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = 'No se puedo obtener las revisiones para el documento a revisar obtenidas con éxito. Causa: '.$ex->getMessage();
			$resultado['data'] = null;
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Devuelve un documento pendiente del expediente electrónico.
	 * Posee la logica de que si un documento es alcanzado por el Art 11 Dec 1404,
	 * lo reemplaza por una plantilla (si no existe en disco, en version web publica).
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function verdocumento($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		$url = '';

		// Ejecuto la vista
		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($requestParams['f_anio'], PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($requestParams['f_tipo'], PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($requestParams['f_numero'], PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($requestParams['f_cuerpo'], PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($requestParams['f_alcance'], PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden = Validator::get()->validar($requestParams['f_orden'], PATRON_NUMEROS, false, 'Orden de la entrada del documento electrónico');

			// Busco el documento pendiente del expediente electrónico
			$expe_elec_pend = NG::expedientesElecPend()->obtenerExpedienteElecPend($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden);

			if ($expe_elec_pend) {
				// Ahora verifico la existencia del documento pendiente. Casos:
				// 1. Si el doc no esta alcazado por el decreto 1404, lo muestro.
				// 2. Si esta alcanzado por el dec 1404 pero el documento existe en disco,
				//    lo muestro porque estoy en el entorno 'interno' del HCD.
				// 3. Si esta alcanzado por el dec 1404 pero el documento NO existe,
				//    muestro la plantilla (estoy en la version publica).
				$documento = PATH_KRAKEN_RESOURCES_PROYECTOS . $expe_elec_pend->documento;
				if (! $expe_elec_pend->dec1404) {
					if (file_exists($documento)) {
						// Caso 1
						$url = URL_KRAKEN_RESOURCES_PROYECTOS.$expe_elec_pend->documento;
					} else {
						// Caso Especial: ERROR... el documento deberia existir y no esta...
						SessionController::get()->guardarError('El documento solicitado no existe! [1]', ERROR_CONTROLLER_GENERICO);
						$this->redireccionar('home', 'view');
					}
				} else {
					$url = (file_exists($documento))
						? URL_KRAKEN_RESOURCES_PROYECTOS.$expe_elec_pend->documento // Caso 2
						: URL_SGL_DOC_FALTANTE_DEC1404;                        // Caso 3
				}

				// Agrego un random al final, para evitar caches
				$url .= sprintf('?v=%s', rand());
			} else {
				// Caso Especial: ERROR... los parametros pasados al controlador
				// son de un documento que no existe en la DB
				SessionController::get()->guardarError('El documento solicitado no existe! [2]', ERROR_CONTROLLER_GENERICO);
				$this->redireccionar('home', 'view');
			}


		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}

		if ($url != '')
			header('Location: ' . $url);
		else
			$this->redireccionar('home', 'view'); // por las dudas lo mando al home
	}

	/**
	 * Devuelve la vista de listado de documentos embebidos de un archivo PDF.
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function listarembebidos($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Ejecuto la vista
		try {
			// Preparo los parámetros de la vista
			$paramVista = $this->generarParametrosVista();

			// Saneo parametros
			$requestParams = $this->sanearConjuntoParametros($requestParams);

			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($requestParams['f_anio'], PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($requestParams['f_tipo'], PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($requestParams['f_numero'], PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($requestParams['f_cuerpo'], PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($requestParams['f_alcance'], PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden = Validator::get()->validar($requestParams['f_orden'], PATRON_NUMEROS, false, 'Orden de la entrada del documento electrónico');

			// Busco el documento del expediente electrónico pendiente
			$expe_elec_pend = NG::expedientesElecPend()->obtenerExpedienteElecPend($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden);

			if ($expe_elec_pend) {
				// Ahora verifico la existencia del documento. Casos:
				// 1. Si el doc no esta alcazado por el decreto 1404, lo muestro.
				// 2. Si esta alcanzado por el dec 1404 pero el documento existe en disco,
				//    lo muestro porque estoy en el entorno 'interno' del HCD.
				// 3. Si esta alcanzado por el dec 1404 pero el documento NO existe,
				//    advierto de la situacion y no muestro nada (estoy en la version publica).
				$documento = PATH_KRAKEN_RESOURCES_PROYECTOS . $expe_elec_pend->documento;
				if (! $expe_elec_pend->dec1404) {
					if (file_exists($documento)) {
						// Caso 1
						$paramVista['expe_elec_pend'] = $expe_elec_pend;
						$vista = new BEExpedientesElecPendView($paramVista);
						$vista->vistaListadoEmbebidos();
					} else {
						// Caso Especial: ERROR... el documento deberia existir y no esta...
						SessionController::get()->guardarError('El documento solicitado no existe! [1]', ERROR_CONTROLLER_GENERICO);
						$this->redireccionar('home', 'view');
					}
				} else {
					if (file_exists($documento)) {
						// Caso 2
						$paramVista['expe_elec_pend'] = $expe_elec_pend;
						$vista = new BEExpedientesElecPendView($paramVista);
						$vista->vistaListadoEmbebidos();
					} else {
						// Caso 3
						SessionController::get()->guardarError('El documento solicitado se encuentra alcanzado por el Artículo 11 del Decreto 1.404. [2]', ERROR_CONTROLLER_GENERICO);
						$this->redireccionar('home', 'view');
					}
				}
			} else {
				// Caso Especial: ERROR... los parametros pasados al controlador
				// son de un documento que no existe en la DB
				SessionController::get()->guardarError('El documento solicitado no existe! [2]', ERROR_CONTROLLER_GENERICO);
				$this->redireccionar('home', 'view');
			}

		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}

	/**
	 * Devuelve un documento embebido de un archivo PDF.
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function verembebido($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		$url = '';

		// Ejecuto la vista
		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($requestParams['f_anio'], PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($requestParams['f_tipo'], PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($requestParams['f_numero'], PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($requestParams['f_cuerpo'], PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($requestParams['f_alcance'], PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden = Validator::get()->validar($requestParams['f_orden'], PATRON_NUMEROS, false, 'Orden de la entrada del documento electrónico');
			$f_id_embebido = Validator::get()->validar($requestParams['f_id_embebido'], PATRON_NUMEROS, false, 'ID de archivo embebido');

			// Busco el documento del expediente electrónico
			$expe_elec_pend = NG::expedientesElecPend()->obtenerExpedienteElecPend($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden);

			if ($expe_elec_pend) {
				// Ahora verifico la existencia del documento. Casos:
				// 1. Si el doc no esta alcazado por el decreto 1404, lo muestro.
				// 2. Si esta alcanzado por el dec 1404 pero el documento existe en disco,
				//    lo muestro porque estoy en el entorno 'interno' del HCD.
				// 3. Si esta alcanzado por el dec 1404 pero el documento NO existe,
				//    muestro la plantilla (estoy en la version publica).
				$documento = PATH_KRAKEN_RESOURCES_PROYECTOS . $expe_elec_pend->documento;
				if (! $expe_elec_pend->dec1404) {
					if (file_exists($documento)) {
						// Caso 1
						$embebido = PDFExtractor::get()->extractFile($documento, $f_id_embebido, PATH_SGL_DOC_TEMPORALES);
						$url = URL_SGL_DOC_TEMPORALES.basename($embebido);
					} else {
						// Caso Especial: ERROR... el documento deberia existir y no esta...
						SessionController::get()->guardarError('El documento solicitado no existe! [1]', ERROR_CONTROLLER_GENERICO);
						$this->redireccionar('home', 'view');
					}
				} else {
					$embebido = PDFExtractor::get()->extractFile($documento, $f_id_embebido, PATH_SGL_DOC_TEMPORALES);
					$url = (file_exists($documento))
						? URL_SGL_DOC_TEMPORALES.basename($embebido) // Caso 2
						: URL_SGL_DOC_FALTANTE_DEC1404;              // Caso 3
				}

				// Agrego un random al final, para evitar caches
				$url .= sprintf('?v=%s', rand());
			} else {
				// Caso Especial: ERROR... los parametros pasados al controlador
				// son de un documento que no existe en la DB
				SessionController::get()->guardarError('El documento solicitado no existe! [2]', ERROR_CONTROLLER_GENERICO);
				$this->redireccionar('home', 'view');
			}


		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}

		if ($url != '')
			header('Location: ' . $url);
		else
			$this->redireccionar('home', 'view'); // por las dudas lo mando al home
	}

}
?>
