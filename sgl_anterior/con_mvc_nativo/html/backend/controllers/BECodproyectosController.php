<?php
/**
 * Clase de controlador de la Codificadora de Proyectos
 *
 * @author XXXX y XXXX
 *
 * 07/01/2022 XXXX: se retira el campo codigo_proyecto
 */
DEFINE('CHECKSUM', 'CHECKSUM_CODPROYECTO');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_CODPROYECTO');

class BECodproyectosController extends BaseController
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
		$vista = new BECodproyectosView($paramVista);
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
			$resultado['numeroRrror'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {

			// Seteo el valor de control "draw"
			$p_draw = $requestParams['draw'];

			// Recibimos el valor a buscar, si es vacío, debemos pasarle NULL a la NG para que nos retorne todos los registros
			$p_valor_a_buscar = (trim($requestParams['search']['value']) == '') ? null : trim($requestParams['search']['value']);// Buscamos por 'descripcion_proyecto'

			// Obtengo datos para la paginación
			$p_limitStart = (trim($requestParams['start']) == '') ? null : trim($requestParams['start']);
			$p_limitLength = (trim($requestParams['length']) == '') ? null : trim($requestParams['length']);

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

				$codproyectos = NG::expedientesParam()->obtenerCodproyectos(
					null, // id_codproyecto
					$p_valor_a_buscar, // descripcion_proyecto
					null, // vigencia_desde_codproy
					null, // vigencia_hasta_codproy
					null, // habilitado_codproy
					null, // id_usuario
					// Control de consulta
					array('id_codproyecto'), // criterio y sentido de orden (FIJO)
					$p_limitLength, // cantidad de registros (paginación)
					$p_limitStart); // corrimiento de registros (paginación)

				// Consulta de cantidad de Cod. de Proyectos (total)
				$cantidadTotalCodproyectos = NG::expedientesParam()->obtenerCodproyectosCantidad(
					null, // id_codproyecto
					$p_valor_a_buscar, // descripcion_proyecto
					null, // vigencia_desde_codproy
					null, // vigencia_hasta_codproy
					null, // habilitado_codproy
					null  // id_usuario
				);

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = $cantidadTotalCodproyectos;
				$resultado['recordsFiltered'] = $cantidadTotalCodproyectos;
				$resultado['data'] = $codproyectos;
			}
			catch (Exception $ex)
			{
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array();  // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
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

		// Para determinar si estoy agregando o modificando una Codificadora de Proyectos, guardo una variable de sesion
		SessionController::get()->guardar(SAVE_ACTION, 'agregar');

		// Preparo una instancia de Codproyecto para la vista
		$codproyecto = new Codproyecto();
		$codproyecto->id_codproyecto = 0;
		$codproyecto->descripcion_proyecto = null;
		$codproyecto->vigencia_desde_codproy = null;
		$codproyecto->vigencia_hasta_codproy = null;
		$codproyecto->habilitado_codproy = '1';

		$paramVista['codproyecto'] = $codproyecto;

		// Instancio la vista y la muestro
		$vista = new BECodproyectosView($paramVista);
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

		// Se obtiene el Id de la Codificadora de Proyectos
		$f_id_codproyecto = Validator::get()->obtenerDefault($requestParams['f_id_codproyecto']);

		try
		{
			// Validación de parámetros de búsqueda
			$f_id_codproyecto = Validator::get()->validar($f_id_codproyecto, PATRON_NUMEROS, false, 'Id de la Codificadora de Proyectos');

			// Se obtiene la Codificadora de Proyectos a editar
			$codproyecto = NG::expedientesParam()->obtenerCodproyecto($f_id_codproyecto);
			if (is_null($codproyecto))
				throw new Exception('Error: inconsistencia de datos al obtener la Codificadora de Proyectos.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $codproyecto->generarChecksum());

			// Para determinar si estoy agregando o modificando una codificadora de proyectos, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Preparo los datos que necesita la vista
			$paramVista['codproyecto'] = $codproyecto;

			// Instancio la vista y la muestro
			$vista = new BECodproyectosView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BECodproyectosView($paramVista);
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
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');
		} else {
			try {
				// Verifico acción de guardado
				if (!SessionController::get()->existe(SAVE_ACTION))
					throw new Exception('No se puede determinar la acci&oacute;n de guardado.');

				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				$jsonData = file_get_contents('php://input');
				$codproyecto = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($codproyecto) == 'Codproyecto'))
					throw new Exception('Se esperaba un objeto de tipo Codproyecto.');

				// Si estoy agregando, la codificadora de proyectos no debe existir
				$codproyectoActual = NG::expedientesParam()->obtenerCodproyecto($codproyecto->id_codproyecto);

				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					if (!is_null($codproyectoActual))
						throw new Exception('No se puede agregar una codificadora de proyectos que ya se encuentre ingresada. Verifique el Id de la codificadora de proyectos.');
				}
				// Si estoy editando una codificadora de proyectos...
				else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
					// ... la codificadora de proyectos debe existir
					if (is_null($codproyectoActual))
						throw new Exception('No se puede editar una codificadora de proyectos inexistente.');
					// ... el checksum no tiene que haber variado
					if ( ! $codproyectoActual->verificarChecksum(SessionController::get()->obtener(CHECKSUM)))
						throw new Exception('La codificadora de proyectos editada ya ha sido modificada desde otra terminal.');
				}

				// ***************************************************************************************
				//TODO: Validación de atributos
				// ***************************************************************************************
				$codproyecto->id_codproyecto 		 = Validator::get()->sanear($codproyecto->id_codproyecto);
				$codproyecto->descripcion_proyecto 	 = Validator::get()->sanear($codproyecto->descripcion_proyecto);
				$codproyecto->vigencia_desde_codproy = Validator::get()->sanear($codproyecto->vigencia_desde_codproy);
				$codproyecto->vigencia_hasta_codproy = Validator::get()->sanear($codproyecto->vigencia_hasta_codproy);

				// Actualizo datos de usuario (quien realizó la modificación)
				$usuario = $this->obtenerUsuarioActual();
				$codproyecto->id_usuario = $usuario->id_usuario;

				// Guardo la codificadora de proyectos
				$codproyecto = NG::expedientesParam()->guardarCodproyecto($codproyecto, true); // guardo y recargo

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Codificadora de proyectos guardada con &eacute;xito.';
				$resultado['data'] = $codproyecto;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo guardar la codificadora de proyectos. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se modifica el estado Habilitado/Deshabilitado de la Codificadora determinada por su Id
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

		// Se recibe el Id de la codificadora
		$f_id_codproyecto = Validator::get()->obtenerDefault($requestParams['f_id_codproyecto']);

		try	{
			// Validación del Id de la codificadora
			$f_id_codproyecto = Validator::get()->validar($f_id_codproyecto, PATRON_NUMEROS, false, 'Id de la Codificadora');

			// Se obtiene la codificadora para modificar su estado
			$codproyecto = NG::expedientesParam()->obtenerCodproyecto($f_id_codproyecto);

			if (is_null($codproyecto))
				throw new Exception('Error: inconsistencia de datos al obtener la Codificadora.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $codproyecto->generarChecksum());

			// Para determinar que se modifica el estado de la codificadora, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Se define el valor opuesto para modificar su estado
			$codproyecto->habilitado_codproy = ($codproyecto->habilitado_codproy == '1') ? '0' : '1';

			// Actualizo datos de usuario (quien realizó la modificación)
			$usuario = $this->obtenerUsuarioActual();
			$codproyecto->id_usuario = $usuario->id_usuario;

			// Se guarda la codificadora con el estado modificado
			$codproyecto = NG::expedientesParam()->guardarCodproyecto($codproyecto, true); // guardo y recargo

			// No es necesario instanciar la vista y mostrar el listado
			// ya que se recarga el datatable respectivo en el JS
			// con dataTableRef.ajax.reload();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BECodproyectosView($paramVista);
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
				$codproyecto = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($codproyecto) == 'Codproyecto'))
					throw new Exception('Se esperaba un objeto de tipo Codproyecto.');

				// Para determinar que se elimina una codificadora de estado, guardo una variable de sesion
				SessionController::get()->guardar(SAVE_ACTION, 'delete');

				// Actualizo datos de usuario (quien realizó la eliminación)
				$usuario = $this->obtenerUsuarioActual();
				$codproyecto->id_usuario = $usuario->id_usuario;

				// Se elimina la codificadora
				if (! NG::expedientesParam()->eliminarCodproyecto($codproyecto) ) {
					$resultado['estado'] = 'ERROR';
					$resultado['mensaje'] = sprintf('No se pudo eliminar la Codificadora de Proyecto %s.', $codproyecto->descripcion_proyecto);
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				} else {
					$resultado['estado'] = 'OK';
					$resultado['mensaje'] = sprintf('Codificadora de Proyecto %s eliminada con &eacute;xito.', $codproyecto->descripcion_proyecto);
					$resultado['data']['codproyecto'] = $codproyecto;
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar la Codificadora de Proyecto. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}
}
?>
