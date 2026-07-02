<?php
/**
 * Clase de controlador de la Categoría
 *
 * @author XXXX y XXXX
 *
 * 07/01/2022 XXXX: se retira el campo codigo_categoria
 */
DEFINE('CHECKSUM', 'CHECKSUM_CATEGORIA');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_CATEGORIA');

class BECategoriasController extends BaseController
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
		$vista = new BECategoriasView($paramVista);
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
			$p_valor_a_buscar = (trim($requestParams['search']['value']) == '') ? null : trim($requestParams['search']['value']);// Buscamos por 'descripcion_categoria'

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

				$categorias = NG::expedientesParam()->obtenerCodcategorias(
					null, // id_codcategoria
					//null, // codigo_categoria
					$p_valor_a_buscar, // descripcion_categoria
					null, // vigencia_desde_categoria
					null, // vigencia_hasta_categoria
					null, // habilitado_categoria
					null, // id_usuario
					// Control de consulta
					array('id_codcategoria'), // criterio y sentido de orden (FIJO)
					$p_limitLength, // cantidad de registros (paginación)
					$p_limitStart); // corrimiento de registros (paginación)

				// Consulta de cantidad de Categorías del expediente respectivo (total)
				$cantidadTotalCategorias = NG::expedientesParam()->obtenerCodcategoriasCantidad(
					null, // id_codcategoria
					//null, // codigo_categoria
					$p_valor_a_buscar, // descripcion_categoria
					null, // vigencia_desde_categoria
					null, // vigencia_hasta_categoria
					null, // habilitado_categoria
					null  // id_usuario
				);

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = $cantidadTotalCategorias;
				$resultado['recordsFiltered'] = $cantidadTotalCategorias;
				$resultado['data'] = $categorias;
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

		// Para determinar si estoy agregando o modificando una Categoría, guardo una variable de sesion
		SessionController::get()->guardar(SAVE_ACTION, 'agregar');

		// Preparo una instancia de Codcategoria para la vista
		$categoria = new Codcategoria();
		$categoria->id_codcategoria = 0;
		//$categoria->codigo_categoria = 0.0;
		$categoria->descripcion_categoria = null;
		$categoria->vigencia_desde_categoria = null;
		$categoria->vigencia_hasta_categoria = null;
		$categoria->habilitado_categoria = '1';

		$paramVista['categoria'] = $categoria;

		// Instancio la vista y la muestro
		$vista = new BECategoriasView($paramVista);
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

		// Se obtiene el Id de la Categoría
		$f_id_codcategoria = Validator::get()->obtenerDefault($requestParams['f_id_codcategoria']);

		try
		{
			// Validación de parámetros de búsqueda
			$f_id_codcategoria = Validator::get()->validar($f_id_codcategoria, PATRON_NUMEROS, false, 'Id de la Categor&iacute;a');

			// Se obtiene la categoria a editar
			$categoria = NG::expedientesParam()->obtenerCodcategoria($f_id_codcategoria);
			if (is_null($categoria))
				throw new Exception('Error: inconsistencia de datos al obtener la Categor&iacute;a.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $categoria->generarChecksum());

			// Para determinar si estoy agregando o modificando una categoria, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Preparo los datos que necesita la vista
			$paramVista['categoria'] = $categoria;

			// Instancio la vista y la muestro
			$vista = new BECategoriasView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardar($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BECategoriasView($paramVista);
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
				$codcategoria = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($codcategoria) == 'Codcategoria'))
					throw new Exception('Se esperaba un objeto de tipo Codcategoria.');

				// Si estoy agregando, la categoria no debe existir
				$categoriaActual = NG::expedientesParam()->obtenerCodcategoria($codcategoria->id_codcategoria);

				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					if (!is_null($categoriaActual))
						throw new Exception('No se puede agregar una categor&iacute;a que ya se encuentre ingresada. Verifique el Id de la categor&iacute;a.');
				}
				// Si estoy editando una categoría...
				else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
					// ... la categoría debe existir
					if (is_null($categoriaActual))
						throw new Exception('No se puede editar una categor&iacute;a inexistente.');
					// ... el checksum no tiene que haber variado
					if ( ! $categoriaActual->verificarChecksum(SessionController::get()->obtener(CHECKSUM)))
						throw new Exception('La categor&iacute;a editada ya ha sido modificada desde otra terminal.');
				}

				//	Validación de atributos
				$codcategoria->id_codcategoria 			= Validator::get()->sanear($codcategoria->id_codcategoria);
				//$codcategoria->codigo_categoria 		= Validator::get()->sanear($codcategoria->codigo_categoria);
				$codcategoria->descripcion_categoria 	= Validator::get()->sanear($codcategoria->descripcion_categoria);
				$codcategoria->vigencia_desde_categoria = Validator::get()->sanear($codcategoria->vigencia_desde_categoria);
				$codcategoria->vigencia_hasta_categoria = Validator::get()->sanear($codcategoria->vigencia_hasta_categoria);
				$codcategoria->habilitado_categoria 	= Validator::get()->sanear($codcategoria->habilitado_categoria);

				// Actualizo datos de usuario (quien realizó la modificación)
				$usuario = $this->obtenerUsuarioActual();
				$codcategoria->id_usuario = $usuario->id_usuario;

				// Guardo la categoría
				$codcategoria = NG::expedientesParam()->guardarCodcategoria($codcategoria, true); // guardo y recargo

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Categor&iacute;a guardada con &eacute;xito.';
				$resultado['data'] = $codcategoria;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo guardar la categor&iacute;a. Causa: '.$e->getMessage();
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

		// Se recibe el Id de la Categoría
		$f_id_codcategoria = Validator::get()->obtenerDefault($requestParams['f_id_codcategoria']);

		try	{
			// Validación del Id de la codificadora
			$f_id_codcategoria = Validator::get()->validar($f_id_codcategoria, PATRON_NUMEROS, false, 'Id de la Categor&iacute;a');

			// Se obtiene la categoria para modificar su estado
			$codcategoria = NG::expedientesParam()->obtenerCodcategoria($f_id_codcategoria);

			if (is_null($codcategoria))
				throw new Exception('Error: inconsistencia de datos al obtener la Categor&iacute;a.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $codcategoria->generarChecksum());

			// Para determinar que se modifica el estado de la categoria, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Se define el valor opuesto para modificar su estado
			$codcategoria->habilitado_categoria = ($codcategoria->habilitado_categoria == '1') ? '0' : '1';

			// Actualizo datos de usuario (quien realizó la modificación)
			$usuario = $this->obtenerUsuarioActual();
			$codcategoria->id_usuario = $usuario->id_usuario;

			// Se guarda la categoría con el estado modificado
			$codcategoria = NG::expedientesParam()->guardarCodcategoria($codcategoria, true); // guardo y recargo

			// No es necesario instanciar la vista y mostrar el listado
			// ya que se recarga el datatable respectivo en el JS
			// con dataTableRef.ajax.reload();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BECategoriasView($paramVista);
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
				$codcategoria = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($codcategoria) == 'Codcategoria'))
					throw new Exception('Se esperaba un objeto de tipo Codcategoria.');

				// Para determinar que se elimina una categoria, guardo una variable de sesion
				SessionController::get()->guardar(SAVE_ACTION, 'delete');

				// Actualizo datos de usuario (quien realizó la eliminación)
				$usuario = $this->obtenerUsuarioActual();
				$codcategoria->id_usuario = $usuario->id_usuario;

				// Se elimina la categoría
				if (! NG::expedientesParam()->eliminarCodcategoria($codcategoria) ) {
					$resultado['estado'] = 'ERROR';
					$resultado['mensaje'] = sprintf('No se pudo eliminar la Categoria %s.', $codcategoria->descripcion_categoria);
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				} else {
					$resultado['estado'] = 'OK';
					$resultado['mensaje'] = sprintf('Categoria %s eliminada con &eacute;xito.', $codcategoria->descripcion_categoria);
					$resultado['data']['codcategoria'] = $codcategoria;
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar la Categoria. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}
}
?>
