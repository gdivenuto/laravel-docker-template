<?php
/**
 * Clase de controlador de Lugares (Codificadora de Iniciadores, Comisiones y Autores)
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_LUGAR');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_LUGAR');

class BELugaresController extends BaseController
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

		// Cantidad de registros visualizados por página
		$this->registros_por_pagina = 14;

		// Nombre del módulo al que corresponde el controlador
		$this->nombreModulo = 'EXPEDIENTES';

		// Determino las acciones válidas y su nivel de acceso mínimo requerido
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['listado'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['edit'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['add'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['save'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['setHabilitado'] = NIVEL_ACCESO_OPERADOR;
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

		// Instancio la vista y la muestro
		$vista = new BELugaresView($paramVista);
		$vista->vistaListado();
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function listado($requestParams)
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

			// Recibimos el valor a buscar, si es vacío, debemos pasarle NULL a la NG para que nos retorne todos los registros
			$p_valor_a_buscar = (trim($requestParams['search']['value']) == '') ? null : trim($requestParams['search']['value']);// Buscamos por 'descripcion_grp'

			// Obtengo datos para la paginación
			$p_limitStart = (trim($requestParams['start']) == '') ? null : trim($requestParams['start']);
			$p_limitLength = $this->registros_por_pagina;//(trim($requestParams['length']) == '') ? null : trim($requestParams['length']);

			// Saneo todos los parametros
			$p_draw = $this->sanearParametro($p_draw);
			$p_valor_a_buscar = $this->sanearParametro($p_valor_a_buscar);
			$p_limitStart = $this->sanearParametro($p_limitStart);
			$p_limitLength = $this->sanearParametro($p_limitLength);

			// Realizo la consulta y preparo el resultado
			$resultado = array();
			$resultado['draw'] = $p_draw; // Es un valor entero para control interno del DataTable
			try
			{
				$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
				$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

				$lugares = NG::expedientesParam()->obtenerLugares(
					null, // tipo_grp
					null, // codigo_grp
					$p_valor_a_buscar, // descripcion_grp
					null, // abreviatura_grp
					null, // bloque_tipo
					null, // bloque_codigo
					null, // observaciones_grp
					null, // vigente_Desde_grp
					null, // vigente_Hasta_grp
					null, // habilitado_grp
					null, // id_usuario
					// Control de consulta
					array('tipo_grp', 'codigo_grp'), // criterio y sentido de orden (FIJO)
					$p_limitLength, // cantidad de registros (paginación)
					$p_limitStart); // corrimiento de registros (paginación)

				// Consulta de cantidad de Lugares (total)
				$cantidadTotalLugares = NG::expedientesParam()->obtenerLugaresCantidad(
					null, // tipo_grp
					null, // codigo_grp
					$p_valor_a_buscar, // descripcion_grp
					null, // abreviatura_grp
					null, // bloque_tipo
					null, // bloque_codigo
					null, // observaciones_grp
					null, // vigente_Desde_grp
					null, // vigente_Hasta_grp
					null, // habilitado_grp
					null  // id_usuario
				);

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = $cantidadTotalLugares;
				$resultado['recordsFiltered'] = $cantidadTotalLugares;
				$resultado['data'] = $lugares;
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

		// Para determinar si estoy agregando o modificando una Codificadora de Iniciadores, Comisiones y Autores, guardo una variable de sesion
		SessionController::get()->guardar(SAVE_ACTION, 'agregar');

		// Preparo una instancia de Lugar para la vista
		$lugar = new Lugar();
		$lugar->tipo_grp = '';
		$lugar->codigo_grp = '';
		$lugar->descripcion_grp = null;
		$lugar->abreviatura_grp = null;
		$lugar->bloque_tipo = null;
		$lugar->bloque_codigo = null;
		$lugar->observaciones_grp = null;
		$lugar->vigente_Desde_grp = null;
		$lugar->vigente_Hasta_grp = null;
		$lugar->habilitado_grp = '0';

		$paramVista['lugar'] = $lugar;

		// Instancio la vista y la muestro
		$vista = new BELugaresView($paramVista);
		$vista->vistaEdicion();
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

		// Se obtiene el Tipo y Código del Lugar (Codificadora de Iniciadores, Comisiones y Autores)
		$f_tipo_grp = Validator::get()->obtenerDefault($requestParams['f_tipo_grp']);
		$f_codigo_grp = Validator::get()->obtenerDefault($requestParams['f_codigo_grp']);

		try
		{
			// Validación de parámetros de búsqueda
			$f_tipo_grp = Validator::get()->validar($f_tipo_grp, PATRON_ALFANUM_EXT, false, 'Tipo de Lugares (Codificadora de Iniciadores, Comisiones y Autores)');
			$f_codigo_grp = Validator::get()->validar($f_codigo_grp, PATRON_ALFANUM_EXT, false, 'C&oacute;digo de Lugares (Codificadora de Iniciadores, Comisiones y Autores)');

			// Se obtiene el Lugar (Codificadora de Iniciadores, Comisiones y Autores) a editar
			$lugar = NG::expedientesParam()->obtenerLugar($f_tipo_grp, $f_codigo_grp);
			if (is_null($lugar))
				throw new Exception('Error: inconsistencia de datos al obtener el Lugar (Codificadora de Iniciadores, Comisiones y Autores).');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $lugar->generarChecksum());

			// Para determinar si estoy agregando o modificando un Lugar, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Preparo los datos que necesita la vista
			$paramVista['lugar'] = $lugar;

			// Instancio la vista y la muestro
			$vista = new BELugaresView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BELugaresView($paramVista);
			$vista->vistaEdicion();
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
			$resultado['data'] =  SessionController::get()->obtener('NUMERO_ERROR');
		} else {

			try {
				// Verifico acción de guardado
				if (!SessionController::get()->existe(SAVE_ACTION))
					throw new Exception('No se puede determinar la acci&oacute;n de guardado.');

				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				$jsonData = file_get_contents('php://input');
				$lugar = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($lugar) == 'Lugar'))
					throw new Exception('Se esperaba un objeto de tipo Lugar.');

				// Si estoy agregando, la codificadora de proyectos no debe existir
				$lugarActual = NG::expedientesParam()->obtenerLugar($lugar->tipo_grp, $lugar->codigo_grp);

				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					if (!is_null($lugarActual))
						throw new Exception('No se puede agregar una codificadora de Lugares (Iniciadores, Comisiones y Autores) que ya se encuentre ingresada. Verifique el Tipo y C&oacute;digo de la codificadora de Lugares (Iniciadores, Comisiones y Autores).');
				}
				// Si estoy editando una codificadora de proyectos...
				else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
					// ... la codificadora de proyectos debe existir
					if (is_null($lugarActual))
						throw new Exception('No se puede editar una codificadora de Lugares (Iniciadores, Comisiones y Autores) inexistente.');
					// ... el checksum no tiene que haber variado
					if ( ! $lugarActual->verificarChecksum(SessionController::get()->obtener(CHECKSUM)))
						throw new Exception('La codificadora de Lugares (Iniciadores, Comisiones y Autores) editada ya ha sido modificada desde otra terminal.');
				}

				// ***************************************************************************************
				// 		Validación de atributos
				// ***************************************************************************************
				$lugar->tipo_grp 		  = Validator::get()->sanear($lugar->tipo_grp);
				$lugar->codigo_grp 		  = Validator::get()->sanear($lugar->codigo_grp);
				$lugar->descripcion_grp   = Validator::get()->sanear($lugar->descripcion_grp);
				$lugar->abreviatura_grp   = Validator::get()->sanear($lugar->abreviatura_grp);
				$lugar->bloque_tipo 	  = Validator::get()->sanear($lugar->bloque_tipo);
				$lugar->bloque_codigo 	  = Validator::get()->sanear($lugar->bloque_codigo);
				$lugar->observaciones_grp = Validator::get()->sanear($lugar->observaciones_grp);
				$lugar->vigente_Desde_grp = Validator::get()->sanear($lugar->vigente_Desde_grp);
				$lugar->vigente_Hasta_grp = Validator::get()->sanear($lugar->vigente_Hasta_grp);

				// Actualizo datos de usuario (quien realizó la modificación)
				$usuario = $this->obtenerUsuarioActual();
				$lugar->id_usuario = $usuario->id_usuario;

				// Guardo la codificadora de Lugares (Iniciadores, Comisiones y Autores)
				$lugar = NG::expedientesParam()->guardarLugar($lugar, true); // guardo y recargo

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Codificadora de Lugares (Iniciadores, Comisiones y Autores) guardada con &eacute;xito.';
				$resultado['data'] = $lugar;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo guardar la codificadora de Lugares (Iniciadores, Comisiones y Autores). Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se modifica el estado Habilitado/Deshabilitado del Lugar (Codificadora de Iniciadores, Comisiones y Autores) determinado por su Tipo y Código
	 * @param [type] $requestParams [description]
	 */
	public function setHabilitado($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();
		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Se obtiene el Tipo y Código del Lugar (Codificadora de Iniciadores, Comisiones y Autores)
		$f_tipo_grp = Validator::get()->obtenerDefault($requestParams['f_tipo_grp']);
		$f_codigo_grp = Validator::get()->obtenerDefault($requestParams['f_codigo_grp']);

		try	{
			// Validación de parámetros de búsqueda
			$f_tipo_grp = Validator::get()->validar($f_tipo_grp, PATRON_ALFANUM_EXT, false, 'Tipo de Lugares (Codificadora de Iniciadores, Comisiones y Autores)');
			$f_codigo_grp = Validator::get()->validar($f_codigo_grp, PATRON_ALFANUM_EXT, false, 'C&oacute;digo de Lugares (Codificadora de Iniciadores, Comisiones y Autores)');

			// Se obtiene el Lugar (Codificadora de Iniciadores, Comisiones y Autores) a editar
			$lugar = NG::expedientesParam()->obtenerLugar($f_tipo_grp, $f_codigo_grp);

			if (is_null($lugar))
				throw new Exception('Error: inconsistencia de datos al obtener la Codificadora.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $lugar->generarChecksum());

			// Para determinar que se modifica el estado de la codificadora, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Se define el valor opuesto para modificar su estado
			$lugar->habilitado_grp = ($lugar->habilitado_grp == '1') ? '0' : '1';

			// Actualizo datos de usuario (quien realizó la modificación)
			$usuario = $this->obtenerUsuarioActual();
			$lugar->id_usuario = $usuario->id_usuario;

			// Se guarda la codificadora con el estado modificado
			$lugar = NG::expedientesParam()->guardarLugar($lugar, true); // guardo y recargo

			// No es necesario instanciar la vista y mostrar el listado
			// ya que se recarga el datatable respectivo en el JS
			// con dataTableRef.ajax.reload();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BELugaresView($paramVista);
			$vista->vistaListado();
		}
	}

	/**
	 * Se elimina la Codificadora determinada por su Id
	 * @param [type] $requestParams [description]
	 */
	public function delete($requestParams)
	{
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
				$lugar = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($lugar) == 'Lugar'))
					throw new Exception('Se esperaba un objeto de tipo Lugar.');

				// Para determinar que se elimina una codificadora de estado, guardo una variable de sesion
				SessionController::get()->guardar(SAVE_ACTION, 'delete');

				// Actualizo datos de usuario (quien realizó la eliminación)
				$usuario = $this->obtenerUsuarioActual();
				$lugar->id_usuario = $usuario->id_usuario;

				// Se elimina la codificadora
				if (! NG::expedientesParam()->eliminarLugar($lugar) ) {
					$resultado['estado'] = 'ERROR';
					$resultado['mensaje'] = sprintf('No se pudo eliminar el Lugar %s.', $lugar->descripcion_grp);
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				} else {
					$resultado['estado'] = 'OK';
					$resultado['mensaje'] = sprintf('Lugar %s eliminado con &eacute;xito.', $lugar->descripcion_grp);
					$resultado['data']['lugar'] = $lugar;
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar el Lugar. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}
}
?>
