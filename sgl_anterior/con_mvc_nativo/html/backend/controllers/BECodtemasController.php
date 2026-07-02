<?php
/**
 * Clase de controlador de la Codificadora de Temas
 *
 * @author XXXX y XXXX
 *
 * 07/01/2022 XXXX: se retira el campo codigo_tema
 */
DEFINE('CHECKSUM', 'CHECKSUM_CODTEMA');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_CODTEMA');

class BECodtemasController extends BaseController
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
		$vista = new BECodTemasView($paramVista);
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
			$p_valor_a_buscar = (trim($requestParams['search']['value']) == '') ? null : trim($requestParams['search']['value']);// Buscamos por 'descripcion_tema'

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

				$codtemas = NG::expedientesParam()->obtenerCodtemas(
					null, // id_codtema
					$p_valor_a_buscar, // descripcion_tema
					null, // vigencia_desde_tema
					null, // vigencia_hasta_tema
					null, // habilitado_tema
					null, // id_usuario
					// Control de consulta
					array('id_codtema'), // criterio y sentido de orden (FIJO)
					$p_limitLength, // cantidad de registros (paginación)
					$p_limitStart); // corrimiento de registros (paginación)

				// Consulta de cantidad de Cod. Temas (total)
				$cantidadTotalCodtemas = NG::expedientesParam()->obtenerCodtemasCantidad(
					null, // id_codtema
					$p_valor_a_buscar, // descripcion_tema
					null, // vigencia_desde_tema
					null, // vigencia_hasta_tema
					null, // habilitado_tema
					null  // id_usuario
				);

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = $cantidadTotalCodtemas;
				$resultado['recordsFiltered'] = $cantidadTotalCodtemas;
				$resultado['data'] = $codtemas;
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

		// Para determinar si estoy agregando o modificando una Codificadora de Temas, guardo una variable de sesion
		SessionController::get()->guardar(SAVE_ACTION, 'agregar');

		// Preparo una instancia de Codtema para la vista
		$codtema = new Codtema();
		$codtema->id_codtema = 0;
		$codtema->descripcion_tema = null;
		$codtema->vigencia_desde_tema = null;
		$codtema->vigencia_hasta_tema = null;
		$codtema->habilitado_tema = '1';

		$paramVista['codtema'] = $codtema;

		// Instancio la vista y la muestro
		$vista = new BECodTemasView($paramVista);
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

		// Se obtiene el Id de la Codificadora de Temas
		$f_id_codtema = Validator::get()->obtenerDefault($requestParams['f_id_codtema']);

		try
		{
			// Validación de parámetros de búsqueda
			$f_id_codtema = Validator::get()->validar($f_id_codtema, PATRON_NUMEROS, false, 'Id de la Codificadora de Proyectos');

			// Se obtiene la Codificadora de Temas a editar
			$codtema = NG::expedientesParam()->obtenerCodtema($f_id_codtema);
			if (is_null($codtema))
				throw new Exception('Error: inconsistencia de datos al obtener la Codificadora de Proyectos.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $codtema->generarChecksum());

			// Para determinar si estoy agregando o modificando una Codificadora de Temas, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Preparo los datos que necesita la vista
			$paramVista['codtema'] = $codtema;

			// Instancio la vista y la muestro
			$vista = new BECodTemasView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BECodTemasView($paramVista);
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
				$codtema = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($codtema) == 'Codtema'))
					throw new Exception('Se esperaba un objeto de tipo Codtema.');

				// Si estoy agregando, la codificadora de temas no debe existir
				$codtemaActual = NG::expedientesParam()->obtenerCodtema($codtema->id_codtema);

				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					if (!is_null($codtemaActual))
						throw new Exception('No se puede agregar una codificadora de temas que ya se encuentre ingresada. Verifique el Id de la codificadora de temas.');
				}
				// Si estoy editando una codificadora de temas...
				else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
					// ... la codificadora de temas debe existir
					if (is_null($codtemaActual))
						throw new Exception('No se puede editar una codificadora de temas inexistente.');
					// ... el checksum no tiene que haber variado
					if ( ! $codtemaActual->verificarChecksum(SessionController::get()->obtener(CHECKSUM)))
						throw new Exception('La codificadora de temas editada ya ha sido modificada desde otra terminal.');
				}

				// ***************************************************************************************
				// Validación de atributos
				// ***************************************************************************************
				$codtema->id_codtema 		  = Validator::get()->sanear($codtema->id_codtema);
				$codtema->descripcion_tema 	  = Validator::get()->sanear($codtema->descripcion_tema);
				$codtema->vigencia_desde_tema = Validator::get()->sanear($codtema->vigencia_desde_tema);
				$codtema->vigencia_hasta_tema = Validator::get()->sanear($codtema->vigencia_hasta_tema);

				// Actualizo datos de usuario (quien realizó la modificación)
				$usuario = $this->obtenerUsuarioActual();
				$codtema->id_usuario = $usuario->id_usuario;

				// Guardo la codificadora de temas
				$codtema = NG::expedientesParam()->guardarCodtema($codtema, true); // guardo y recargo

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Codificadora de temas guardada con &eacute;xito.';
				$resultado['data'] = $codtema;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo guardar la codificadora de temas. Causa: '.$e->getMessage();
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
		$f_id_codtema = Validator::get()->obtenerDefault($requestParams['f_id_codtema']);

		try	{
			// Validación del Id de la codificadora
			$f_id_codtema = Validator::get()->validar($f_id_codtema, PATRON_NUMEROS, false, 'Id de la Codificadora');

			// Se obtiene la codificadora para modificar su estado
			$codtema = NG::expedientesParam()->obtenerCodtema($f_id_codtema);

			if (is_null($codtema))
				throw new Exception('Error: inconsistencia de datos al obtener la Codificadora.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $codtema->generarChecksum());

			// Para determinar que se modifica el estado de la codificadora, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Se define el valor opuesto para modificar su estado
			$codtema->habilitado_tema = ($codtema->habilitado_tema == '1') ? '0' : '1';

			// Actualizo datos de usuario (quien realizó la modificación)
			$usuario = $this->obtenerUsuarioActual();
			$codtema->id_usuario = $usuario->id_usuario;

			// Se guarda la codificadora con el estado modificado
			$codtema = NG::expedientesParam()->guardarCodtema($codtema, true); // guardo y recargo

			// No es necesario instanciar la vista y mostrar el listado
			// ya que se recarga el datatable respectivo en el JS
			// con dataTableRef.ajax.reload();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BECodTemasView($paramVista);
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
				$codtema = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($codtema) == 'Codtema'))
					throw new Exception('Se esperaba un objeto de tipo Codtema.');

				// Para determinar que se elimina una codificadora de estado, guardo una variable de sesion
				SessionController::get()->guardar(SAVE_ACTION, 'delete');

				// Actualizo datos de usuario (quien realizó la eliminación)
				$usuario = $this->obtenerUsuarioActual();
				$codtema->id_usuario = $usuario->id_usuario;

				// Se elimina la codificadora
				if (! NG::expedientesParam()->eliminarCodtema($codtema) ) {
					$resultado['estado'] = 'ERROR';
					$resultado['mensaje'] = sprintf('No se pudo eliminar la Codificadora de Tema %s.', $codtema->descripcion_tema);
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				} else {
					$resultado['estado'] = 'OK';
					$resultado['mensaje'] = sprintf('Codificadora de Tema %s eliminada con &eacute;xito.', $codtema->descripcion_tema);
					$resultado['data']['codtema'] = $codtema;
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar la Codificadora de Tema. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}
}
?>
