<?php
/**
 * Clase de controlador de la Búsqueda Avanzada.
 *
 * @author XXXX, XXXX
 */
class BEExpedientesbusquedaavanzadaController extends BaseController {
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	private $defaultOffsetAnios = -10; // Cantidad de años por defecto con los que se setean los filtros de fecha Desde

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
		$this->accionesPermitidas['busquedaavanzadadatagrid'] = NIVEL_ACCESO_PERIODISTA;
	}

	/**
	 * Se inicializan los parámetros, de la búsqueda avanzada, con valores por defecto
	 *
	 * @return array Conjunto de parámetros con sus valores por defecto
	 */
	private function setearValoresPorDefectoParaVista() {
		$resultado = array();

		$resultado['f_fecha_desde'] = $this->obtenerFechaAniosAtrasConDiaInicial($this->defaultOffsetAnios); // 10 años atrás
		$resultado['f_fecha_hasta'] = date("Y-m-d");
		$resultado['f_opcion_sancionado_promulgado'] = 0;
		$resultado['f_iniciador'] = 0;
		$resultado['f_categoria'] = 0;
		$resultado['f_comision'] = 0;
		$resultado['f_estado'] = 0;
		$resultado['f_autor'] = 0;
		$resultado['f_tema'] = 0;
		$resultado['f_caratula'] = '';

		return $resultado;
	}

	/**
	 * Se setean los parámetros, para ser utilizados en la Capa de Negocio
	 *
	 * @return array $parametros Array con los parámetros seteados para la NG
	 */
	private function setearValoresParaNG($parametros) {
		if ($parametros != null) {
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$parametros['f_opcion_sancionado_promulgado'] = ($parametros['f_opcion_sancionado_promulgado'] != null) ? Validator::get()->validar($parametros['f_opcion_sancionado_promulgado'], PATRON_NUMEROS, true, 'Opci&oacute;n para determinar c&oacute;mo utilizar las fechas') : 0;

			$parametros['piniciador_tipo'] = ($parametros['piniciador_tipo'] != 0) ? Validator::get()->validar($parametros['piniciador_tipo'], PATRON_LETRAS, true, 'Tipo del Iniciador') : null;

			$parametros['piniciador_codigo'] = ($parametros['piniciador_codigo']) ? Validator::get()->validar($parametros['piniciador_codigo'], PATRON_ALFANUM_EXT, true, 'C&oacute;digo del Iniciador') : null;

			$parametros['f_categoria'] = ($parametros['f_categoria'] != 0) ? Validator::get()->validar($parametros['f_categoria'], PATRON_NUMEROS, true, 'Categor&iacute;a del Expediente') : null;

			$parametros['f_comision'] = ($parametros['f_comision']) ? Validator::get()->validar($parametros['f_comision'], PATRON_ALFANUM_EXT, true, 'C&oacute;digo de la Comisi&oacute;n') : null;

			$parametros['f_estado'] = ($parametros['f_estado'] != 0) ? Validator::get()->validar($parametros['f_estado'], PATRON_NUMEROS, true, 'Estado del Expediente') : null;

			$parametros['pautor_tipo'] = ($parametros['pautor_tipo'] != 0) ? Validator::get()->validar($parametros['pautor_tipo'], PATRON_LETRAS, true, 'Tipo del Autor') : null;

			$parametros['pautor_codigo'] = ($parametros['pautor_codigo']) ? Validator::get()->validar($parametros['pautor_codigo'], PATRON_ALFANUM_EXT, true, 'C&oacute;digo del Autor') : null;

			$parametros['f_tema'] = ($parametros['f_tema'] != 0) ? Validator::get()->validar($parametros['f_tema'], PATRON_NUMEROS, true, 'Tema del Expediente') : null;

			$parametros['f_caratula'] = ($parametros['f_caratula'] != '') ? Validator::get()->validar($parametros['f_caratula'], PATRON_ALFANUM_EXT, true, 'Car&aacute;tula del Expediente o Extracto de un Proyecto') : null;

			// Si se eligió una Comisión, no se busca por Estado
			if ($parametros['f_comision'] != null) {
				$parametros['f_estado'] = null;
			}

			// Si se eligió un Estado, no se busca por Comisión
			if ($parametros['f_estado'] != null) {
				$parametros['f_comision'] = null;
			}

		}

		return $parametros;
	}

	/**
	 * Verifica que los parámetros, de la búsqueda Avanzada, posean un valor, sino se les asigna un valor por defecto
	 *
	 * @param  array $requestParams Parámetros a verificar
	 * @return array                Parámetros verificados
	 */
	private function obtenerParametrosBusquedaAvanzada($requestParams) {
		$resultado = array();

		// la fecha Desde por defecto es 10 años menor que la fecha actual
		$resultado['f_fecha_desde'] = (isset($requestParams['f_fecha_desde'])) ? $requestParams['f_fecha_desde'] : $this->obtenerFechaAniosAtrasConDiaInicial($this->defaultOffsetAnios);
		// la fecha Hasta por defecto es la fecha actual
		$resultado['f_fecha_hasta'] = (isset($requestParams['f_fecha_hasta'])) ? $requestParams['f_fecha_hasta'] : date("Y-m-d");

		// Si se desea buscar por: fecha de Entrada | fecha sde Sanción | fecha de Promulgación
		$resultado['pfecha_entrada_expe'] = null;
		$resultado['pfecha_sancion'] = null;
		$resultado['pfecha_promulga'] = null;

		// Se recibe la opción para buscar por fechas
		$resultado['f_opcion_sancionado_promulgado'] = (isset($requestParams['f_opcion_sancionado_promulgado'])) ? $requestParams['f_opcion_sancionado_promulgado'] : 0;
		switch ($resultado['f_opcion_sancionado_promulgado']) {
		case 0:
			// Para buscar por Fecha de Entrada
			$resultado['pfecha_entrada_expe'] = array($resultado['f_fecha_desde'], $resultado['f_fecha_hasta']);
			break;
		case 1:
			// Para buscar por Fecha de Sanción
			$resultado['pfecha_sancion'] = array($resultado['f_fecha_desde'], $resultado['f_fecha_hasta']);
			break;
		case 2:
			// Para buscar por Fecha de Promulgación
			$resultado['pfecha_promulga'] = array($resultado['f_fecha_desde'], $resultado['f_fecha_hasta']);
			break;
		default: // Si se recibe un parámetro inválido por defecto se busca por Fecha de Entrada
			$resultado['pfecha_entrada_expe'] = array($resultado['f_fecha_desde'], $resultado['f_fecha_hasta']);
			break;
		}

		// Si se filtra por Categoría
		$resultado['f_categoria'] = (isset($requestParams['f_categoria'])) ? $requestParams['f_categoria'] : null;

		// Si se filtra por Iniciador
		$resultado['f_iniciador'] = (isset($requestParams['f_iniciador'])) ? explode("|", $requestParams['f_iniciador']) : null;
		// Se separa en tipo y código
		$resultado['piniciador_tipo'] = (isset($resultado['f_iniciador'][0])) ? $resultado['f_iniciador'][0] : null;
		$resultado['piniciador_codigo'] = (isset($resultado['f_iniciador'][1])) ? $resultado['f_iniciador'][1] : null;

		// Si se recibe el texto para filtrar por Carátula o por Extracto
		$resultado['f_caratula'] = (isset($requestParams['f_caratula'])) ? $requestParams['f_caratula'] : null;

		// Si se filtra por Tema
		$resultado['f_tema'] = (isset($requestParams['f_tema'])) ? $requestParams['f_tema'] : null;

		// Si se filtra por Autor
		$resultado['f_autor'] = (isset($requestParams['f_autor'])) ? explode("|", $requestParams['f_autor']) : null;
		// Se separa en tipo y código
		$resultado['pautor_tipo'] = (isset($resultado['f_autor'][0])) ? $resultado['f_autor'][0] : null;
		$resultado['pautor_codigo'] = (isset($resultado['f_autor'][1])) ? $resultado['f_autor'][1] : null;

		// Se recibe una Comisión
		$resultado['f_comision'] = (isset($requestParams['f_comision'])) ? $requestParams['f_comision'] : null;

		// Se recibe un Estado
		$resultado['f_estado'] = (isset($requestParams['f_estado'])) ? $requestParams['f_estado'] : null;

		// Si se busca por Estado, se compara con cero porque es un combobox
		if ((!is_null($resultado['f_estado'])) && ($resultado['f_estado'] != 0)) {
			// No interesa ya que NO se busca por Comisión
			$resultado['ptratamiento_comision'] = false;
		}
		// Si se busca por Comisión, se compara con cero porque es un combobox
		elseif ((!is_null($resultado['f_comision'])) && ($resultado['f_comision'] != 0)) {
			// Aquí interesan los estados utilizados en el tratamiento de la Comisión
			$resultado['ptratamiento_comision'] = true;
		} else {
			// No interesa ya que NO se busca por Comisión NI por Estado
			$resultado['ptratamiento_comision'] = null;
		}

		return $resultado;
	}

	/**
	 * Se obtienen todos los listados necesarios para cargar los combos de la búsqueda avanzada
	 * @return array Conjunto de listados (array de arrays)
	 */
	private function cargarCombosCodificadoras() {
		$resultado = array();

		// Se obtienen todos los Iniciadores
		$resultado['listado_iniciadores'] = NG::expedientesParam()->obtenerLugares(
			array('G', 'V'), null, null, null, null, null, null, null, null,
			null,
			null,
			// 1ro las Habilitadas, 2do las Deshabilitadas, por su Descripción
			array('habilitado_grp desc', 'descripcion_grp'),
			null, null);

		//Se obtienen todos los Autores
		$resultado['listado_codautores'] = NG::expedientesParam()->obtenerLugares(
			null, null, null, null, null, null, null, null, null,
			null,
			null,
			// 1ro las Habilitadas, 2do las Deshabilitadas, por su Descripción
			array('habilitado_grp desc', 'descripcion_grp'),
			null, null);

		// Se obtienen todas las Categorias
		$resultado['listado_categorias'] = NG::expedientesParam()->obtenerCodcategorias(
			null,
			//null, (al retirarse codigo_categoria 07/01/2022 XXXX)
			null,
			null,
			null,
			null, // Todas: habilitadas y deshabilitadas
			null,
			array('habilitado_categoria desc', 'id_codcategoria'),
			null, null);

		// Se obtienen todas las Comisiones
		$resultado['listado_comisiones'] = NG::expedientesParam()->obtenerLugares(
			'C', null, null, null, null, null, null, null, null,
			null, // Todas: habilitadas y deshabilitadas
			null,
			// 1ro las Habilitadas, 2do las Deshabilitadas, por su Descripción
			array('habilitado_grp desc', 'descripcion_grp'),
			null, null);

		// Se obtienen todos los Estados
		$resultado['listado_codestados'] = NG::expedientesParam()->obtenerCodestados(
			null,
			//null, (al retirarse codigo_estado 07/01/2022 XXXX)
			null, null, null, null,
			null, // Todos: habilitados y deshabilitados
			null, null,
			array('habilitado_codestado desc, id_codestado'),
			null, null);

		// Se obtienen todos los Temas
		$resultado['listado_codtemas'] = NG::expedientesParam()->obtenerCodtemas(
			null,
			//null, (al retirarse codigo_tema 07/01/2022 XXXX)
			null, null, null,
			null, // Todos: habilitados y deshabilitados
			null,
			// 1ro los Habilitados, 2do los Deshabilitados, por su Id
			array('habilitado_tema desc', 'id_codtema'),
			null, null);

		return $resultado;
	}

	/**
	 * Invoca a la vista 'view' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function view($requestParams) {
		// Antes que nada verifico el nivel de acceso
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Se sanean los parámetros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// La búsqueda se inicializa con valores por defecto
		$parametros_busqueda_avanzada = $this->setearValoresPorDefectoParaVista();

		// Se recibe (o no) un parámetro para verificar si se pulsó el botón Restablecer
		$f_restablecer = (isset($requestParams['f_restablecer'])) ? $requestParams['f_restablecer'] : '';

		// Si se desea restablecer el criterio de búsqueda
		if ($f_restablecer != '') {
			try {
				// Se limpian los parámetros en la sesión
				SessionController::get()->eliminar('parametros_busqueda_avanzada');
			} catch (Exception $ex) {
				// Si falla, vuelve al home y elimina la busqueda en sesión
				SessionController::get()->eliminar('parametros_busqueda_avanzada');
				SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
				$this->redireccionar('home', 'view');
			}
		} else // si NO se desea restablecer
		{
			// Si por lo menos se recibe la fecha desde y la fecha hasta
			if (isset($requestParams['f_fecha_desde']) && $requestParams['f_fecha_desde'] != '' &&
				isset($requestParams['f_fecha_hasta']) && $requestParams['f_fecha_hasta'] != '') {
				try {
					// Se limpian los parámetros en la sesión
					SessionController::get()->eliminar('parametros_busqueda_avanzada');

					// Se obtienen los parámetros recibidos
					$parametros_busqueda_avanzada = $this->obtenerParametrosBusquedaAvanzada($requestParams);
				} catch (Exception $ex) {
					// Si falla, vuelve al home y elimina la busqueda en sesión
					SessionController::get()->eliminar('parametros_busqueda_avanzada');
					SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
					$this->redireccionar('home', 'view');
				}
			} else // Si NO se recibieron las fechas desde y hasta
			{
				try {
					// Si existen los parámetros en la sesión
					if (SessionController::get()->existe('parametros_busqueda_avanzada')) {
						// Se obtienen desde la sesión
						$parametros_busqueda_avanzada = SessionController::get()->obtener('parametros_busqueda_avanzada');
					}
				} catch (Exception $ex) {
					// Si falla, vuelve al home y elimina la busqueda en sesión
					SessionController::get()->eliminar('parametros_busqueda_avanzada');
					SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
					$this->redireccionar('home', 'view');
				}
			}
		}

		//******* Se obtienen los listados necesarios para cargar los combos en la Vista ******************************
		$paramVista = array_merge($paramVista, $this->cargarCombosCodificadoras());

		// Se setea el conjunto de parámetros para la vista
		// Si hubo un error, nunca se llega a ejecutar este código porque se sale por los "catch".
		$paramVista['parametros_busqueda_avanzada'] = $parametros_busqueda_avanzada;

		// Se guardan en sesion (siempre... en un caso es redundante)
		SessionController::get()->guardar('parametros_busqueda_avanzada', $parametros_busqueda_avanzada);

		// Se ejecuta la Vista
		// Instancio la vista y la muestro
		$vista = new BEExpedientesView($paramVista);
		$vista->vistaBusquedaAvanzada();
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function busquedaavanzadadatagrid($requestParams) {
		// Version customizada del "serverside processing" para datatables
		// https://www.datatables.net/manual/server-side
		//
		// En este caso particular, no se utilizan las columnas estándar.

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
			// Parámetros de filtros recibidos
			// El método 'obtenerParametrosBusquedaAvanzada' internamente verifica el valor de cada filtro recibido
			// de esta manera: $f_xxx = (isset($requestParams['f_xxx'])) ? $requestParams['f_xxx'] : null;
			// agrupando cada filtro verificado, en el array '$parametros'
			$parametros = $this->obtenerParametrosBusquedaAvanzada($requestParams);

			// Seteo el valor de control "draw"
			$p_draw = $requestParams['draw']; // es un entero

			// Obtengo datos para la paginación
			$p_limitStart = (trim($requestParams['start']) == '') ? null : trim($requestParams['start']);
			$p_limitLength = (trim($requestParams['length']) == '') ? 10 : trim($requestParams['length']);

			// Saneo todos los parametros
			$p_draw = $this->sanearParametro($p_draw);
			$p_limitStart = $this->sanearParametro($p_limitStart);
			$p_limitLength = $this->sanearParametro($p_limitLength);

			// Realizo la consulta y preparo el resultado
			$resultado = array();
			$resultado['draw'] = $p_draw;
			try {
				// La fecha Desde y Hasta son "necesarias". Si no me las proveen, simulo un resultado vacío.
				if (($parametros['f_fecha_desde'] == null || $parametros['f_fecha_desde'] == '') &&
					($parametros['f_fecha_hasta'] == null || $parametros['f_fecha_hasta'] == '')) {
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array(); // Es un array vacio!!! No NULL.
					$resultado['error'] = "Se requiere el filtro por fecha desde y fecha hasta.";
					// Se limpian los parámetros en la sesión por si arrastro un parametro erróneo
					SessionController::get()->eliminar('parametros_busqueda_avanzada');
				} else {
					// Se verifican previamente los parámetros para ser utilizados en la Capa de Negocio
					// aquí se utiliza la clase Validator para validar mediante expresiones regulares
					$parametros = $this->setearValoresParaNG($parametros);

					$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					// Consulta de expedientes con el criterio respectivo
					$expedientes = NG::reportes()->obtenerExpedientesAvanzado(
						// Parametros
						$parametros['pfecha_entrada_expe'],
						$parametros['pfecha_promulga'],
						$parametros['pfecha_sancion'],
						$parametros['f_categoria'],
						$parametros['piniciador_tipo'],
						$parametros['piniciador_codigo'],
						$parametros['f_caratula'],
						$parametros['f_tema'],
						$parametros['pautor_tipo'],
						$parametros['pautor_codigo'],
						$parametros['ptratamiento_comision'], /* boolean */
						$parametros['f_comision'],
						$parametros['f_estado'],
						true, // instancias completas!
						// Control de consulta
						array('anio', 'tipo', 'numero', 'cuerpo', 'alcance'), // criterio y sentido de orden (FIJO)
						$p_limitLength, // cantidad de registros (paginación)
						$p_limitStart); // corrimiento de registros (paginación)

					// Consulta de cantidad de expedientes (total)
					$cantidadTotalExpedientes = NG::reportes()->obtenerExpedientesAvanzadoCantidad(
						// Parametros
						$parametros['pfecha_entrada_expe'],
						$parametros['pfecha_promulga'],
						$parametros['pfecha_sancion'],
						$parametros['f_categoria'],
						$parametros['piniciador_tipo'],
						$parametros['piniciador_codigo'],
						$parametros['f_caratula'],
						$parametros['f_tema'],
						$parametros['pautor_tipo'],
						$parametros['pautor_codigo'],
						$parametros['ptratamiento_comision'], /* boolean */
						$parametros['f_comision'],
						$parametros['f_estado']);

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = $cantidadTotalExpedientes;
					$resultado['recordsFiltered'] = $cantidadTotalExpedientes;
					$resultado['data'] = $expedientes;
				}
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
				// Se limpian los parámetros en la sesión por si arrastro un parametro erróneo
				SessionController::get()->eliminar('parametros_busqueda_avanzada');
			}
		}

		// Si llegamos hasta aqui es que la consulta se ejecuto con éxito; entonces guardamos
		// los parametros en sesion
		SessionController::get()->guardar('parametros_busqueda_avanzada', $parametros);

		// Retorno resultados
		echo JsonHelper::get()->serializar($resultado);
	}
}
?>
