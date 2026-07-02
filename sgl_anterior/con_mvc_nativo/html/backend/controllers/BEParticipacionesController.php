<?php
/**
 * Clase de controlador de la Participacion perteneciente a un expediente específico.
 *
 * @author XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_PARTICIPACION');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_PARTICIPACION');

class BEParticipacionesController extends BaseController {
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
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['datagrid'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['editarhabilitacion'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['uploadpropuesta'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['habilitarExpedienteAParticipar'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['delete'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['informeParticipaciones'] = NIVEL_ACCESO_OPERADOR;
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

		// Ejecuto la vista
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
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}

		// Instancio la vista y la muestro
		$vista = new BEParticipacionesView($paramVista);
		$vista->vistaListado();
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function datagrid($requestParams) {
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

				// La clave del expediente es "obligatoria". Si no me la proveen, simulo un resultado vacío.
				if ($f_anio == null || $f_anio == '' ||
					$f_tipo == null || $f_tipo == '' ||
					$f_numero == null || $f_numero == '' ||
					$f_cuerpo == null || $f_cuerpo == '' ||
					$f_alcance == null || $f_alcance == '') {
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				} else {
					$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					$participaciones = NG::expedientes()->obtenerParticipaciones(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // cuerpo
						$f_alcance, // alcance
						null, // numero_participacion
						null, // texto
						//null, // id_usuario
						// Control de consulta, criterio y sentido de orden (FIJO)
						array('anio', 'tipo', 'numero', 'cuerpo', 'alcance', 'numero_participacion'),
						$p_limitLength, // cantidad de registros (paginación)
						$p_limitStart); // corrimiento de registros (paginación)

					// Consulta de cantidad de participaciones del expediente respectivo (total)
					$cantidadTotalParticipaciones = NG::expedientes()->obtenerParticipacionesCantidad(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // cuerpo
						$f_alcance, // alcance
						null, // numero_participacion
						null//, // texto
						//null// id_usuario
					);

					//Logger::get()->Log("participaciones", $participaciones, false);
					//Logger::get()->Log("cantidadTotalParticipaciones", $cantidadTotalParticipaciones, false);

					// finalmente, verifico si existe el expediente
					$cantidadExpedientes = NG::expedientes()->obtenerExpedientesCantidad(
						$f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);

					$resultado['existeExpediente'] = $cantidadExpedientes == 1;

					// Se verifica si el expediente se encuentra habilitado para su participación ciudadana
					$resultado['estaHabilitado'] = NG::expedientes()->estaHabilitadoParticipacion(
						$f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);

					// Se verifica si el expediente ya posee la Propuesta (pdf) para su participación ciudadana,
					// de ser así, se toma la url completa de dicha propuesta (pdf), sino un valor nulo
					$resultado['urlPropuesta'] = NG::expedientes()->poseePropuestaParticipacion(
						$f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = $cantidadTotalParticipaciones;
					$resultado['recordsFiltered'] = $cantidadTotalParticipaciones;
					$resultado['data'] = $participaciones;
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
	 * Se elimina la participación determinada por su clave
	 * @param [type] $requestParams [description]
	 */
	public function delete($requestParams) {
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
				$jsonData = file_get_contents('php://input');
				$participacion = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($participacion) == 'Participacion')) {
					throw new Exception('Se esperaba un objeto de tipo Participacion.');
				}

				// Se elimina la Participacion respectiva
				if (!NG::expedientes()->eliminarparticipacion($participacion)) {
					$resultado['estado'] = 'ERROR';
					$resultado['mensaje'] = sprintf('No se pudo eliminar la participaci&oacute;n del expediente %s-%s-%s-%s-%s.',
						$participacion->anio,
						$participacion->tipo,
						$participacion->numero,
						$participacion->cuerpo,
						$participacion->alcance);
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				} else {
					$resultado['estado'] = 'OK';
					$resultado['mensaje'] = sprintf('Participaci&oacute;n del expediente %s-%s-%s-%s-%s eliminada con &eacute;xito.',
						$participacion->anio,
						$participacion->tipo,
						$participacion->numero,
						$participacion->cuerpo,
						$participacion->alcance);
					$resultado['data']['participacion'] = $participacion;
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar la participaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	public function editarhabilitacion($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Se recibe la clave del expediente
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

		// Se obtiene el primer proyecto
		$primer_proyecto = NG::expedientes()->obtenerProyecto($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, 1);
		//Logger::get()->Log("primer_proyecto", $primer_proyecto, false);

		// Preparo una instancia de ExpedienteEnParticipacion para la vista
		$exped_en_participacion = new ExpedienteEnParticipacion();
		$exped_en_participacion->anio = $f_anio;
		$exped_en_participacion->tipo = $f_tipo;
		$exped_en_participacion->numero = $f_numero;
		$exped_en_participacion->cuerpo = $f_cuerpo;
		$exped_en_participacion->alcance = $f_alcance;
		$exped_en_participacion->fecha_inicio = ''; //date('Y-m-d');
		$exped_en_participacion->fecha_fin = ''; //date('Y-m-d');
		$exped_en_participacion->extracto = $primer_proyecto->extracto;

		//Logger::get()->Log("exped_en_participacion", $exped_en_participacion, false);

		$paramVista['exped_en_participacion'] = $exped_en_participacion;

		// Instancio la vista y la muestro
		$vista = new BEParticipacionesView($paramVista);
		$vista->vistaEdicion();
	}

	/**
	 * Se carga una propuesta en formato pdf
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function uploadpropuesta($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		//Logger::get()->Log("requestParams_en_uploadpropuesta", $requestParams, false);

		// Verifico si me han pasado parametros por url, para un acceso directo a una posición del paginador.
		$f_anio = Validator::get()->obtenerDefault($requestParams['propuesta_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['propuesta_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['propuesta_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['propuesta_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['propuesta_alcance']);

		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

			// Se verifica que el expediente exista
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, true);
			//Logger::get()->Log("expediente_en_uploadpropuesta", $expediente, false);

			if (is_null($expediente)) {
				throw new Exception('Error: El expediente, al cual se desea cargar la propuesta, no existe.');
			}

			if ($requestParams['request_files']['f_propuesta']['type'] != 'application/pdf') {
				throw new Exception('Atención: El documento no es un PDF.');
			}
			//Logger::get()->Log("requestParams_en_uploadpropuesta", $requestParams, false);

			// Se sube al directorio respectivo
			NG::expedientes()->subirPropuestaParticipacion($expediente, $requestParams['request_files']);

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
		$vista = new BEParticipacionesView($paramVista);
		$vista->vistaListado();
	}

	/**
	 * Se habilita un Expediente para la Participación Ciudadana
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function habilitarExpedienteAParticipar($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {
			try {

				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				$jsonData = file_get_contents('php://input');
				$exped_en_participacion = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($exped_en_participacion) == 'ExpedienteEnParticipacion')) {
					throw new Exception('Se esperaba un objeto de tipo ExpedienteEnParticipacion.');
				}

				if ($exped_en_participacion->fecha_inicio != '') {
					// Se define la fecha fin como el incremento de 30 días a la de inicio
					$mas_30_dias = strtotime('+30 day', strtotime($exped_en_participacion->fecha_inicio));
					$exped_en_participacion->fecha_fin = date('Y-m-d', $mas_30_dias);
				}

				// Actualizo datos de usuario (quien realizó la modificación)
				$usuario = $this->obtenerUsuarioActual();
				$exped_en_participacion->id_usuario = $usuario->id_usuario;

				// Se habilita el expediente en la participación
				$exped_en_participacion = NG::expedientes()->habilitarExpedienteAParticipar($exped_en_participacion, true);

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Expediente habilitado en la participaci&oacute;n con &eacute;xito.';
				$resultado['data'] = $exped_en_participacion;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo habilitar el expediente en la participaci&oacute;n. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	public function informeParticipaciones($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Se recibe la clave del expediente
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

		try
		{
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance de expediente');

			$participaciones = NG::expedientes()->obtenerParticipaciones(
				$f_anio, // anio
				$f_tipo, // tipo
				$f_numero, // numero
				$f_cuerpo, // cuerpo
				$f_alcance, // alcance
				null, // numero_participacion
				null, // texto
				//null, // id_usuario
				// Control de consulta
				array('anio', 'tipo', 'numero', 'cuerpo', 'alcance', 'numero_participacion'));

			$paramVista['resultados'] = $participaciones;

			// Se obtienen las descripciones de los parámetros utilizados
			$parametros_codificados = null; //$this->obtenerParametrosCodificadosCriterio($parametros);
			// Se le pasa a la Vista el texto correspondiente al criterio de búsqueda utilizado, con sus nombres respectivos
			$paramVista['criterio_busqueda'] = null; //$this->obtenerTextoCriterioBusqueda($parametros_codificados);

			// Instancio la vista y la muestro
			$vista = new BEReportesView($paramVista);
			$vista->vistaReportePdfParticipaciones();

		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}

}
?>
