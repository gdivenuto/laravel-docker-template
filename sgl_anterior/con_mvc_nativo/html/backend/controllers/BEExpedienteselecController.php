<?php
/**
 * Clase de controlador de Expedientes Electrónicos pertenecientes a un expediente específico.
 *
 * @author XXXX y XXXX
 */

class BEExpedienteselecController extends BaseController {
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
		$this->accionesPermitidas['verfirmas'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['datagrid'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['vercertificados'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['obtenercaratula'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['verdocumento'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['listarembebidos'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['verembebido'] = NIVEL_ACCESO_PERIODISTA;
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

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Verifico si me han pasado parametros por url, para un acceso directo a una posición del paginador.
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		$f_archivo_descarga = Validator::get()->obtenerDefault($requestParams['f_archivo_descarga'], '');

		// Ejecuto la vista
		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance de expediente');

			// Valido el archivo de descarga, si existe
			if ($f_archivo_descarga != '') {
				$f_archivo_descarga = Validator::get()->validar(
					$f_archivo_descarga,
					"/^[0-9]{4,4}[a-z]{1,1}[0-9]{9,9}_[0-9]{20,20}\.(zip|pdf)$/i",
					false,
					'Archivo a descargar'
				);
				$paramVista['f_archivo_descarga'] = URL_SGL_DOC_TEMPORALES.$f_archivo_descarga;
			} else {
				$paramVista['f_archivo_descarga'] = '';
			}

			$paramVista['f_anio'] = $f_anio;
			$paramVista['f_tipo'] = $f_tipo;
			$paramVista['f_numero'] = $f_numero;
			$paramVista['f_cuerpo'] = $f_cuerpo;
			$paramVista['f_alcance'] = $f_alcance;

		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}

		// Instancio la vista y la muestro
		$vista = new BEExpedientesElecView($paramVista);
		$vista->vistaListado();
	}

	/**
	 * Esta acción del controlador devuelve un JSON con toda la información de las
	 * firmas del documento asociado al expediente electronico.
 	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function verfirmas($requestParams)
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
			$f_orden = Validator::get()->validar($requestParams['orden'], PATRON_NUMEROS, false, 'Orden de la entrada del documento electrónico');

			$firmas = NG::firmasExpedienteElec()->obtenerFirmasExpedienteElec($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden);
			if (count($firmas) == 0)
				throw new Exception('No se han encontrado un registro de firmas para documento del expediente electrónico.');

			$resultado['estado'] = 'OK';
			$resultado['mensaje'] = 'Registro de firmas para documento del expediente electrónico obtenido con éxito.';
			$resultado['data'] = $firmas;

		} catch (Exception $ex)	{
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = 'No se puedo obtener el registro de firmas para documento del expediente electrónico. Causa: '.$ex->getMessage();
			$resultado['data'] = null;
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Esta acción del controlador devuelve un JSON con toda la información de las
	 * firmas aplicadas al documento asociado al expediente electronico.
 	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function vercertificados($requestParams)
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
			$f_orden = Validator::get()->validar($requestParams['orden'], PATRON_NUMEROS, false, 'Orden de la entrada del documento electrónico');

			$expe_elec = NG::expedientesElec()->obtenerExpedienteElec($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden);
			if (is_null($expe_elec))
				throw new Exception('Entrada de expediente electrónico inexistente.');

			$documento = PATH_KRAKEN_RESOURCES_PROYECTOS.$expe_elec->documento;

			if (! file_exists($documento))
				throw new Exception('No se encuentra el documento asociado al expediente electrónico.');

			$firmas = NG::firmas()->obtenerFirmasPDF($documento);
			$resultado['estado'] = 'OK';
			$resultado['mensaje'] = 'Firmas obtenidas con éxito.';
			$resultado['data'] = $firmas;

		} catch (Exception $ex)	{
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = 'No se puedo obtener información de firmas. Causa: '.$ex->getMessage();
			$resultado['data'] = null;
		}

		echo JsonHelper::get()->serializar($resultado);
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

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {

			$resultado['recordsTotal'] = 0;
			$resultado['recordsFiltered'] = 0;
			$resultado['data'] = array();
			$resultado['error'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['numeroError'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {

			// Parametros de filtro
			$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
			$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
			$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
			$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

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
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
				$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo de expediente');
				$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance de expediente');

				// La clave del expediente es "necesaria". Si no me la proveen, simulo un resultado vacío.
				if ( $f_anio == null || $f_anio == '' ||
					 $f_tipo == null || $f_tipo == '' ||
					 $f_numero == null || $f_numero == '' ||
					 $f_cuerpo == null || $f_cuerpo == '' ||
					 $f_alcance == null || $f_alcance == '' )
				{
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array();  // Es un array vacio!!! No NULL.
				}
				else
				{
					$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					$estados = NG::expedientesElec()->obtenerExpedientesElec(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						null, // orden
						null, // tipo_actuacion
						null, // detalle
						null, // documento
						null, // documento_hash
						null, // texto_original
						null, // dec1404
						null, // embebido
						null, // es_caratula
						null, // fecha_hora
						null, // id_usuario
						null, // observaciones
						// Control de consulta
						array('anio', 'tipo', 'numero', 'cuerpo', 'alcance', 'orden'), // criterio y sentido de orden (FIJO)
						$p_limitLength, // cantidad de registros (paginación)
						$p_limitStart); // corrimiento de registros (paginación)

					// Consulta de cantidad de Estados del expediente respectivo (total)
					$cantidadTotalEstados = NG::expedientesElec()->obtenerExpedientesElecCantidad(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						null, // orden
						null, // tipo_actuacion
						null, // detalle
						null, // documento
						null, // documento_hash
						null, // texto_original
						null, // dec1404
						null, // embebido
						null, // es_caratula
						null, // fecha_hora
						null, // id_usuario
						null  // observaciones
					);

					// finalmente, verifico si existe el expediente
					$cantidadExpedientes = NG::expedientes()->obtenerExpedientesCantidad($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);
					$resultado['existeExpediente'] = $cantidadExpedientes == 1;

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = $cantidadTotalEstados;
					$resultado['recordsFiltered'] = $cantidadTotalEstados;
					$resultado['data'] = $estados;
				}
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
	 * Se obtiene el documento de la carátula actual del expediente electrónico
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function obtenercaratula($requestParams)
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

			$caratulas = NG::expedientesElec()->obtenerExpedientesElec(
				$f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance,
				null, null, null, null, null, null, null, null,
				true
			);

			if (count($caratulas) == 0) {
				$resultado['estado'] = 'WARNING';
				$resultado['mensaje'] = 'No se ha encontrado una carátula para este expediente electrónico.';
				$resultado['data'] = null;
			} else {
				// Lógica para la caratula en relacion al decreto 1404. Casos:
				// 1. Si el doc no esta alcazado por el decreto 1404, lo muestro.
				// 2. Si esta alcanzado por el dec 1404 pero el documento existe en disco,
				//    lo muestro porque estoy en el entorno 'interno' del HCD.
				// 3. Si esta alcanzado por el dec 1404 pero el documento NO existe,
				//    muestro la plantilla (estoy en la version publica).
				$caratula = $caratulas[0];
				$documento = PATH_KRAKEN_RESOURCES_PROYECTOS . $caratula->documento;
				if (! $caratula->dec1404) {
					if (file_exists($documento)) {
						// Caso 1
						$resultado['estado'] = 'OK';
						$resultado['mensaje'] = 'Carátula del expediente electrónico obtenida con éxito.';
						$resultado['data'] = URL_KRAKEN_RESOURCES_PROYECTOS.$caratula->documento;
					} else {
						// Caso Especial: ERROR... el documento deberia existir y no esta...
						$resultado['estado'] = 'ERROR';
						$resultado['mensaje'] = 'No se ha encontrado una carátula para este expediente electrónico.';
						$resultado['data'] = null;
					}
				} else {
					$resultado['estado'] = 'OK';
					$resultado['mensaje'] = 'Carátula del expediente electrónico obtenida con éxito.';
					$resultado['data'] = (file_exists($documento))
						? URL_KRAKEN_RESOURCES_PROYECTOS.$caratula->documento // Caso 2
						: URL_SGL_DOC_FALTANTE_DEC1404;                       // Caso 3
				}
			}

		} catch (Exception $ex)	{
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = 'No se pudo obtener la carátula del expediente electrónico. Causa: '.$ex->getMessage();
			$resultado['data'] = null;
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Devuelve un documento del expediente electrónico.
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

			// Busco el documento del expediente electrónico
			$expe_elec = NG::expedientesElec()->obtenerExpedienteElec($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden);

			if ($expe_elec) {
				// Ahora verifico la existencia del documento. Casos:
				// 1. Si el doc no esta alcazado por el decreto 1404, lo muestro.
				// 2. Si esta alcanzado por el dec 1404 pero el documento existe en disco,
				//    lo muestro porque estoy en el entorno 'interno' del HCD.
				// 3. Si esta alcanzado por el dec 1404 pero el documento NO existe,
				//    muestro la plantilla (estoy en la version publica).
				$documento = PATH_KRAKEN_RESOURCES_PROYECTOS . $expe_elec->documento;
				if (! $expe_elec->dec1404) {
					if (file_exists($documento)) {
						// Caso 1
						$url = URL_KRAKEN_RESOURCES_PROYECTOS.$expe_elec->documento;
					} else {
						// Caso Especial: ERROR... el documento deberia existir y no esta...
						SessionController::get()->guardarError('El documento solicitado no existe! [1]', ERROR_CONTROLLER_GENERICO);
						$this->redireccionar('home', 'view');
					}
				} else {
					$url = (file_exists($documento))
						? URL_KRAKEN_RESOURCES_PROYECTOS.$expe_elec->documento // Caso 2
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

			// Busco el documento del expediente electrónico
			$expe_elec = NG::expedientesElec()->obtenerExpedienteElec($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden);

			if ($expe_elec) {
				// Ahora verifico la existencia del documento. Casos:
				// 1. Si el doc no esta alcazado por el decreto 1404, lo muestro.
				// 2. Si esta alcanzado por el dec 1404 pero el documento existe en disco,
				//    lo muestro porque estoy en el entorno 'interno' del HCD.
				// 3. Si esta alcanzado por el dec 1404 pero el documento NO existe,
				//    advierto de la situacion y no muestro nada (estoy en la version publica).
				$documento = PATH_KRAKEN_RESOURCES_PROYECTOS . $expe_elec->documento;
				if (! $expe_elec->dec1404) {
					if (file_exists($documento)) {
						// Caso 1
						$paramVista['expe_elec'] = $expe_elec;
						$vista = new BEExpedientesElecView($paramVista);
						$vista->vistaListadoEmbebidos();
					} else {
						// Caso Especial: ERROR... el documento deberia existir y no esta...
						SessionController::get()->guardarError('El documento solicitado no existe! [1]', ERROR_CONTROLLER_GENERICO);
						$this->redireccionar('home', 'view');
					}
				} else {
					if (file_exists($documento)) {
						// Caso 2
						$paramVista['expe_elec'] = $expe_elec;
						$vista = new BEExpedientesElecView($paramVista);
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
			$expe_elec = NG::expedientesElec()->obtenerExpedienteElec($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden);

			if ($expe_elec) {
				// Ahora verifico la existencia del documento. Casos:
				// 1. Si el doc no esta alcazado por el decreto 1404, lo muestro.
				// 2. Si esta alcanzado por el dec 1404 pero el documento existe en disco,
				//    lo muestro porque estoy en el entorno 'interno' del HCD.
				// 3. Si esta alcanzado por el dec 1404 pero el documento NO existe,
				//    muestro la plantilla (estoy en la version publica).
				$documento = PATH_KRAKEN_RESOURCES_PROYECTOS . $expe_elec->documento;
				if (! $expe_elec->dec1404) {
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
