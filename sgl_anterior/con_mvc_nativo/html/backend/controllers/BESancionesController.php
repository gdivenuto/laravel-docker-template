<?php
/**
 * Clase de controlador de la Sanción perteneciente a un expediente específico.
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_SANCION');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_SANCION');

class BESancionesController extends BaseController
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
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['datagrid'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['edit'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['add'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['save'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['delete'] = NIVEL_ACCESO_OPERADOR;
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

			// Instancio la vista y la muestro
			$vista = new BESancionesView($paramVista);
			$vista->vistaListado();

		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
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

					$sanciones = NG::expedientes()->obtenerSanciones(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						null, // orden_proyecto
						null, // fecha_sancion
						null, // numero_sancion
						null, // fecha_promulga
						null, // numero_promulga
						null, // decreto_promulga
						null, // fecha_veto
						null, // decreto_veto
						null, // decreto_presidencia
						null, // fecha_remision_de_comunicacion
						null, // fecha_1er_vto_comunicacion
						null, // fecha_2do_vto_comunicacion
						null, // fecha_rta_comunicacion
						null, //observaciones_sancion
						null, // id_usuario
						// Control de consulta
						array('anio', 'tipo', 'numero', 'cuerpo', 'alcance', 'orden_proyecto', 'fecha_sancion'), // criterio y sentido de orden (FIJO)
						$p_limitLength, // cantidad de registros (paginación)
						$p_limitStart); // corrimiento de registros (paginación)

					// Consulta de cantidad de Sanciones del expediente respectivo (total)
					$cantidadTotalSanciones = NG::expedientes()->obtenerSancionesCantidad(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						null, // orden_proyecto
						null, // fecha_sancion
						null, // numero_sancion
						null, // fecha_promulga
						null, // numero_promulga
						null, // decreto_promulga
						null, // fecha_veto
						null, // decreto_veto
						null, // decreto_presidencia
						null, // fecha_remision_de_comunicacion
						null, // fecha_1er_vto_comunicacion
						null, // fecha_2do_vto_comunicacion
						null, // fecha_rta_comunicacion
						null, // observaciones_sancion
						null  // id_usuario
					);

					// finalmente, verifico si existe el expediente
					$cantidadExpedientes = NG::expedientes()->obtenerExpedientesCantidad($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);
					$resultado['existeExpediente'] = $cantidadExpedientes == 1;

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = $cantidadTotalSanciones;
					$resultado['recordsFiltered'] = $cantidadTotalSanciones;
					$resultado['data'] = $sanciones;
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
	 * Invoca a la vista 'add' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function add($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		try {
			// Se recibe la clave del expediente
			$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
			$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
			$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
			$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

			// Obtengo el expediente a editar
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, false);
			if (is_null($expediente)) {
				throw new Exception('Error: No existe el expediente al que se desea agregarle una sanción.');
			}

			// Si el expediente se encuentra agregado a otro expediente, no se permite su edición
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite agregar una Sanción, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			// Para determinar si estoy agregando o modificando una Sanción, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'agregar');

			// Preparo una instancia de Sanción para la vista
			$sancion = new Sancion();
			$sancion->anio = $f_anio;
			$sancion->tipo = $f_tipo;
			$sancion->numero = $f_numero;
			$sancion->cuerpo = $f_cuerpo;
			$sancion->alcance = $f_alcance;

			$paramVista['sancion'] = $sancion;
			$paramVista['bloquear_clave_sancion'] = false; // voy a permitir editar la clave de la sancion

			// Se obtienen los Proyectos del expediente respectivo, para elegir uno para la Sanción
			$paramVista['listado_proyectos'] = NG::expedientes()->obtenerProyectos($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);

			// Instancio la vista y la muestro
			$vista = new BESancionesView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			// Se vuelve a la grilla con el expediente respectivo
			$this->redireccionar(
				'sanciones',
				'view',
				[
					'f_anio' => $f_anio,
					'f_tipo' => $f_tipo,
					'f_numero' => $f_numero,
					'f_cuerpo' => $f_cuerpo,
					'f_alcance' => $f_alcance
				]
			);
		}
	}

    /**
	 * Invoca a la vista 'edit' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function edit($requestParams)
	{
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
		$f_orden_proyecto = Validator::get()->obtenerDefault($requestParams['f_orden_proyecto']);
		$f_fecha_sancion = Validator::get()->obtenerDefault($requestParams['f_fecha_sancion']);

		try
		{
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden_proyecto = Validator::get()->validar($f_orden_proyecto, PATRON_NUMEROS, false, 'Orden del proyecto');

			// Obtengo el expediente a editar su sanción
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, false);
			if (is_null($expediente)) {
				throw new Exception('Error: No existe el expediente al cual se desea editar la sanción.');
			}
			// Si el expediente se encuentra agregado a otro expediente
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite editar la sanción, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			// Se obtiene la sancion a editar
			$sancion = NG::expedientes()->obtenerSancion($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden_proyecto, $f_fecha_sancion);
			if (is_null($sancion))
				throw new Exception('Error: inconsistencia de datos al obtener la Sanci&oacute;n.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $sancion->generarChecksum());

			// Para determinar si estoy agregando o modificando una sancion, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Preparo los datos que necesita la vista
			$paramVista['sancion'] = $sancion;
			$paramVista['bloquear_clave_sancion'] = true; // NO voy a permitir editar la clave de la sancion

			// Se obtienen los Proyectos del expediente respectivo, para elegir uno para la Sanción
			$paramVista['listado_proyectos'] = NG::expedientes()->obtenerProyectos($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);

			// Instancio la vista y la muestro
			$vista = new BESancionesView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			// Se vuelve a la grilla con el expediente respectivo
			$this->redireccionar(
				'sanciones',
				'view',
				[
					'f_anio' => $f_anio,
					'f_tipo' => $f_tipo,
					'f_numero' => $f_numero,
					'f_cuerpo' => $f_cuerpo,
					'f_alcance' => $f_alcance
				]
			);
		}
	}

	/**
	 * Invoca a la vista 'save' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function save($requestParams)
	{
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
				// Verifico acción de guardado
				if (!SessionController::get()->existe(SAVE_ACTION))
					throw new Exception('No se puede determinar la acci&oacute;n de guardado.');

				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				$jsonData = file_get_contents('php://input');
				$sancion = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($sancion) == 'Sancion'))
					throw new Exception('Se esperaba un objeto de tipo Sancion.');

				// Si estoy agregando, la sancion no debe existir
				$sancionActual = NG::expedientes()->obtenerSancion($sancion->anio, $sancion->tipo, $sancion->numero, $sancion->cuerpo, $sancion->alcance, $sancion->orden_proyecto, $sancion->fecha_sancion);
				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					if (!is_null($sancionActual))
						throw new Exception('No se puede agregar una sanci&oacute;n que ya se encuentre ingresada. Verifique la clave del expediente, el n&uacute;mero de orden del proyecto y la fecha de sanci&oacuten.');
				}
				// Si estoy editando una sanción...
				else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
					// ... la sanción debe existir
					if (is_null($sancionActual))
						throw new Exception('No se puede editar una sanci&oacute;n inexistente.');
					// ... el checksum no tiene que haber variado
					if ( ! $sancionActual->verificarChecksum(SessionController::get()->obtener(CHECKSUM)))
						throw new Exception('La sanci&oacute;n editada ya ha sido modificada desde otra terminal.');
				}

				// ***********************************************************
				// Validación de atributos
				// ***********************************************************
				$sancion->orden_proyecto 				 = Validator::get()->sanear($sancion->orden_proyecto);
				$sancion->fecha_sancion 				 = Validator::get()->sanear($sancion->fecha_sancion);
				$sancion->numero_sancion 				 = Validator::get()->sanear($sancion->numero_sancion);
				$sancion->fecha_promulga 				 = Validator::get()->sanear($sancion->fecha_promulga);
				$sancion->numero_promulga 				 = Validator::get()->sanear($sancion->numero_promulga);
				$sancion->decreto_promulga 			  	 = Validator::get()->sanear($sancion->decreto_promulga);
				$sancion->fecha_veto 					 = Validator::get()->sanear($sancion->fecha_veto);
				$sancion->decreto_veto 	   			  	 = Validator::get()->sanear($sancion->decreto_veto);
				$sancion->decreto_presidencia 			 = Validator::get()->sanear($sancion->decreto_presidencia);
				$sancion->fecha_remision_de_comunicacion = Validator::get()->sanear($sancion->fecha_remision_de_comunicacion);
				$sancion->fecha_1er_vto_comunicacion     = Validator::get()->sanear($sancion->fecha_1er_vto_comunicacion);
				$sancion->fecha_2do_vto_comunicacion     = Validator::get()->sanear($sancion->fecha_2do_vto_comunicacion);
				$sancion->fecha_rta_comunicacion         = Validator::get()->sanear($sancion->fecha_rta_comunicacion);
				$sancion->observaciones_sancion 		 = Validator::get()->sanear($sancion->observaciones_sancion);

				// Actualizo datos de usuario (quien realizó la modificación)
				$usuario = $this->obtenerUsuarioActual();
				$sancion->id_usuario = $usuario->id_usuario;

				// Guardo la sanción
				$sancion = NG::expedientes()->guardarSancion($sancion, true); // guardo y recargo

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Sanci&oacute;n guardada con &eacute;xito.';
				$resultado['data'] = $sancion;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo guardar la sanción. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina la Sanción determinada por su clave
	 * @param [type] $requestParams [description]
	 */
	public function delete($requestParams)
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
			try {
				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				$jsonData = file_get_contents('php://input');
				$sancion = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($sancion) == 'Sancion'))
					throw new Exception('Se esperaba un objeto de tipo Sancion.');

				// Obtengo el expediente antes de eliminar su sancion
				$expediente = NG::expedientes()->obtenerExpediente(
					$sancion->anio,
					$sancion->tipo,
					$sancion->numero,
					$sancion->cuerpo,
					$sancion->alcance,
					false
				);
				if (is_null($expediente)) {
					throw new Exception('Error: No existe el expediente al cual se desea eliminar la sancion.');
				}
				// Si el expediente se encuentra agregado a otro expediente
				if ( NG::expedientes()->estaAgregadoA($expediente) ) {
					throw new Exception('No se permite eliminar la sancion, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
				}

				// Se elimina el sancion respectivo
				if (! NG::expedientes()->eliminarSancion($sancion) ) {
					$resultado['estado'] = 'ERROR';
					$resultado['mensaje'] = sprintf('No se pudo eliminar la Sanci&oacute;n del expediente %s-%s-%s-%s-%s.',
						$sancion->anio,
						$sancion->tipo,
						$sancion->numero,
						$sancion->cuerpo,
						$sancion->alcance);
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				} else {
					$resultado['estado'] = 'OK';
					$resultado['mensaje'] = sprintf('Sanci&oacute;n del expediente %s-%s-%s-%s-%s eliminada con &eacute;xito.',
						$sancion->anio,
						$sancion->tipo,
						$sancion->numero,
						$sancion->cuerpo,
						$sancion->alcance);
					$resultado['data']['sancion'] = $sancion;
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar la Sanci&oacute;n. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}
}
?>
