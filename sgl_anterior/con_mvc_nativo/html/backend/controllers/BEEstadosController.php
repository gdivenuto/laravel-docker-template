<?php
/**
 * Clase de controlador del Estado perteneciente a un expediente específico.
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_ESTADO');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_ESTADO');

class BEEstadosController extends BaseController
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
			$vista = new BEEstadosView($paramVista);
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

					$estados = NG::expedientes()->obtenerEstados(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						null, // fecha_estado
						null, // orden_estado
						null, // id_codestado
						null, // observaciones_estado
						null, // id_usuario
						// Control de consulta
						array('anio', 'tipo', 'numero', 'cuerpo', 'alcance', 'fecha_estado', 'orden_estado'), // criterio y sentido de orden (FIJO)
						$p_limitLength, // cantidad de registros (paginación)
						$p_limitStart); // corrimiento de registros (paginación)

					// Consulta de cantidad de Estados del expediente respectivo (total)
					$cantidadTotalEstados = NG::expedientes()->obtenerEstadosCantidad(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						null, // fecha_estado
						null, // orden_estado
						null, // id_codestado
						null, // observaciones_estado
						null  // id_usuario
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
				throw new Exception('Error: No existe el expediente al que se desea agregarle un estado.');
			}

			// Si el expediente se encuentra agregado a otro expediente, no se permite su edición
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite agregar un Estado, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			// Para determinar si estoy agregando o modificando un Estado, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'agregar');

			// Preparo una instancia de Estado para la vista
			$estado = new Estado();
			$estado->anio = $f_anio;
			$estado->tipo = $f_tipo;
			$estado->numero = $f_numero;
			$estado->cuerpo = $f_cuerpo;
			$estado->alcance = $f_alcance;
			$estado->fecha_estado = date("Y-m-d");

			$paramVista['estado'] = $estado;

			// Se obtienen todos los Estados
			$paramVista['listado_codestados'] = NG::expedientesParam()->obtenerCodestados(
				null,
				//null, 10/01/2022 XXXX, se retira codigo_estado
				null, null, null, null, '1', null, null, array('id_codestado'), null, null);

			// Instancio la vista y la muestro
			$vista = new BEEstadosView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			// Se vuelve a la grilla con el expediente respectivo
			$this->redireccionar(
				'estados',
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
		$f_fecha_estado = Validator::get()->obtenerDefault($requestParams['f_fecha_estado']);
		$f_orden_estado = Validator::get()->obtenerDefault($requestParams['f_orden_estado']);

		try
		{
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden_estado = Validator::get()->validar($f_orden_estado, PATRON_NUMEROS, false, 'Orden del Estado');

			// Obtengo el expediente a editar su estado
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, false);
			if (is_null($expediente)) {
				throw new Exception('Error: No existe el expediente al cual se desea editar el estado.');
			}
			// Si el expediente se encuentra agregado a otro expediente
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite editar el estado, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			// Se obtiene el Estado a editar
			$estado = NG::expedientes()->obtenerEstado($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_fecha_estado, $f_orden_estado);
			if (is_null($estado))
				throw new Exception('Error: inconsistencia de datos al obtener la Sanci&oacute;n.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $estado->generarChecksum());

			// Para determinar si estoy agregando o modificando un Estado, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Preparo los datos que necesita la vista
			$paramVista['estado'] = $estado;

			// Se obtienen todos los Estados
			$paramVista['listado_codestados'] = NG::expedientesParam()->obtenerCodestados(
				null,
				//null, 10/01/2022 XXXX, se retira codigo_estado
				null, null, null, null, '1', null, null, array('id_codestado'), null, null);

			// Instancio la vista y la muestro
			$vista = new BEEstadosView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			// Se vuelve a la grilla con el expediente respectivo
			$this->redireccionar(
				'estados',
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
				$estado = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($estado) == 'Estado'))
					throw new Exception('Se esperaba un objeto de tipo Estado.');

				// Si estoy agregando, el estado no debe existir
				$estadoActual = NG::expedientes()->obtenerEstado($estado->anio, $estado->tipo, $estado->numero, $estado->cuerpo, $estado->alcance, $estado->fecha_estado, $estado->orden_estado);
				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					if (!is_null($estadoActual))
						throw new Exception('No se puede agregar un estado que ya se encuentre ingresado. Verifique la clave del expediente y el n&uacute;mero de orden del estado.');

					// Si estoy agregando un estado, calculo su orden siguiente
					$estado->orden_estado = NG::expedientes()->obtenerNumeroSiguienteEstado($estado->anio, $estado->tipo, $estado->numero, $estado->cuerpo, $estado->alcance, null);
				}
				// Si estoy editando un estado...
				else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
					// ... el estado debe existir
					if (is_null($estadoActual))
						throw new Exception('No se puede editar un estado inexistente.');
					// ... el checksum no tiene que haber variado
					if ( ! $estadoActual->verificarChecksum(SessionController::get()->obtener(CHECKSUM)))
						throw new Exception('El estado editado ya ha sido modificado desde otra terminal.');
				}

				// ***********************************************************
				// Validación de atributos
				// ***********************************************************
				$estado->fecha_estado 		  = Validator::get()->sanear($estado->fecha_estado);
				$estado->orden_estado 		  = Validator::get()->sanear($estado->orden_estado);
				$estado->id_codestado 		  = Validator::get()->sanear($estado->id_codestado);
				$estado->observaciones_estado = Validator::get()->sanear($estado->observaciones_estado);

				// Actualizo datos de usuario (quien realizó la modificación)
				$usuario = $this->obtenerUsuarioActual();
				$estado->id_usuario = $usuario->id_usuario;

				// Guardo el estado
				$estado = NG::expedientes()->guardarEstado($estado, true); // guardo y recargo

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Estado guardado con &eacute;xito.';
				$resultado['data'] = $estado;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo guardar el estado. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina el Estado determinado por su clave
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
				$estado = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($estado) == 'Estado'))
					throw new Exception('Se esperaba un objeto de tipo Estado.');

				// Obtengo el expediente antes de eliminar su estado
				$expediente = NG::expedientes()->obtenerExpediente(
					$estado->anio,
					$estado->tipo,
					$estado->numero,
					$estado->cuerpo,
					$estado->alcance,
					false
				);
				if (is_null($expediente)) {
					throw new Exception('Error: No existe el expediente al cual se desea eliminar el estado.');
				}
				// Si el expediente se encuentra agregado a otro expediente
				if ( NG::expedientes()->estaAgregadoA($expediente) ) {
					throw new Exception('No se permite eliminar el estado, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
				}

				// Se elimina el Estado respectivo
				if (! NG::expedientes()->eliminarEstado($estado) ) {
					$resultado['estado'] = 'ERROR';
					$resultado['mensaje'] = sprintf('No se pudo eliminar el Estado del expediente %s-%s-%s-%s-%s.',
						$estado->anio,
						$estado->tipo,
						$estado->numero,
						$estado->cuerpo,
						$estado->alcance);
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				} else {
					$resultado['estado'] = 'OK';
					$resultado['mensaje'] = sprintf('Estado del expediente %s-%s-%s-%s-%s eliminado con &eacute;xito.',
						$estado->anio,
						$estado->tipo,
						$estado->numero,
						$estado->cuerpo,
						$estado->alcance);
					$resultado['data']['estado'] = $estado;
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar el Estado. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}
}
?>
