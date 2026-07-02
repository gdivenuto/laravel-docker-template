<?php
/**
 * Clase de controlador del listado de Informes.
 *
 * @author XXXX, XXXX
 */
class BEListadoinformesController extends BaseController {
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
		$this->accionesPermitidas['informesdatagrid'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['generarpdfinformes'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['generardocumentotextoinformes'] = NIVEL_ACCESO_PERIODISTA;

		$this->limite_para_vencidos = 30; // A partir de los 30 días los Informes están vencidos

		// Parámetros a excluir del criterio de búsqueda
		$this->parametros_excluidos = array('ptratamiento_comision', 'f_vencidos');

		// Conjunto de etiquetas a mostrar en el criterio de búsqueda en los reportes
		$this->etiquetas = array();

		// Etiquetas automáticas para criterios de búsqueda
		$this->etiquetas['f_fecha_desde'] = '&nbsp;&nbsp;&nbsp;Desde';
		$this->etiquetas['f_fecha_hasta'] = '&nbsp;&nbsp;&nbsp;Hasta';
		$this->etiquetas['f_fecha_listado'] = '&nbsp;&nbsp;&nbsp;Fecha del listado';
		$this->etiquetas['f_comision'] = '&nbsp;&nbsp;&nbsp;Comisi&oacute;n';
		$this->etiquetas['f_vencidos'] = '&nbsp;&nbsp;&nbsp;S&oacute;lo vencidos';
	}

	/**
	 * Se inicializan los parámetros del criterio de búsqueda, con valores por defecto
	 *
	 * @return array Conjunto de parámetros con sus valores por defecto
	 */
	private function setearValoresPorDefectoParaVista() {
		$resultado = array();

		$resultado['f_fecha_desde'] = $this->obtenerFechaAniosAtrasConDiaInicial($this->defaultOffsetAnios); // 10 años atrás
		$resultado['f_fecha_hasta'] = date("Y-m-d");
		$resultado['f_fecha_listado'] = date("Y-m-d"); // Utilizada para calcular los días en comisión
		$resultado['f_comision'] = 0;
		$resultado['f_vencidos'] = 0;

		return $resultado;
	}

	/**
	 * Verifica que los parámetros posean un valor, sino se les asigna un valor por defecto
	 *
	 * @param  array $requestParams Parámetros a verificar
	 * @return array                Parámetros verificados
	 */
	private function obtenerParametros($requestParams) {
		$resultado = array();

		// la fecha Desde, por defecto es 10 años menor que la fecha actual
		$resultado['f_fecha_desde'] = (isset($requestParams['f_fecha_desde'])) ? $requestParams['f_fecha_desde'] : $this->obtenerFechaAniosAtrasConDiaInicial($this->defaultOffsetAnios);
		// la fecha Hasta, por defecto es la fecha actual
		$resultado['f_fecha_hasta'] = (isset($requestParams['f_fecha_hasta'])) ? $requestParams['f_fecha_hasta'] : date("Y-m-d");
		// la fecha del Listado, por defecto es la fecha actual
		$resultado['f_fecha_listado'] = (isset($requestParams['f_fecha_listado'])) ? $requestParams['f_fecha_listado'] : date("Y-m-d");

		// Se recibe una Comisión
		$resultado['f_comision'] = (isset($requestParams['f_comision'])) ? $requestParams['f_comision'] : null;

		// Se recibe la opción 'Sólo Vencidos'
		$resultado['f_vencidos'] = (isset($requestParams['f_vencidos'])) ? $requestParams['f_vencidos'] : 0;

		// Aquí interesan los estados utilizados en el tratamiento de la Comisión
		$resultado['ptratamiento_comision'] = true;

		return $resultado;
	}

	/**
	 * Se obtienen las etiquetas de cada criterio utilizado en el listado
	 * @param  array $parametros Conjunto de parámetros con el criterio de búsqueda
	 * @return array $parametros El mismo conjunto con los nombres agregados
	 */
	public function prepararParametrosParaReporte($parametros) {
		if (isset($parametros['f_fecha_desde']) && !is_null($parametros['f_fecha_desde'])) {
			$parametros['f_fecha_desde'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_desde']);
		}

		if (isset($parametros['f_fecha_hasta']) && !is_null($parametros['f_fecha_hasta'])) {
			$parametros['f_fecha_hasta'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_hasta']);
		}

		if (isset($parametros['f_fecha_listado']) && !is_null($parametros['f_fecha_listado'])) {
			$parametros['f_fecha_listado'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_listado']);
		}

		// Se obtiene el nombre de la Comisión elegida (por ello la verificación con cero)
		if (array_key_exists('f_comision', $parametros)) {
			$nuevoValor = (isset($parametros['f_comision'][0]) && $parametros['f_comision'][0] != '0') ? NG::expedientesParam()->obtenerLugar($parametros['f_comision'][0], $parametros['f_comision'][1]) : null;
			$parametros['f_comision'] = (!is_null($nuevoValor)) ? $nuevoValor->descripcion_grp : null;
		}

		return $parametros;
	}

	/**
	 * Invoca a la vista 'view' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function view($requestParams) {
		// Antes que nada verifico el nivel de acceso
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista (HEREDADO)
		$paramVista = $this->generarParametrosVista();

		// Se sanean los parámetros (HEREDADO)
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// La búsqueda se inicializa con valores por defecto
		$parametros_listado_informes = $this->setearValoresPorDefectoParaVista();

		// Se recibe (o no) un parámetro para verificar si se pulsó el botón Restablecer
		$f_restablecer = (isset($requestParams['f_restablecer'])) ? $requestParams['f_restablecer'] : '';

		// Si se desea restablecer el criterio de búsqueda
		if ($f_restablecer != '') {
			try {
				// Se limpian los parámetros en la sesión
				SessionController::get()->eliminar('parametros_listado_informes');
			} catch (Exception $ex) {
				// Si falla, vuelve al home y elimina la busqueda en sesión
				SessionController::get()->eliminar('parametros_listado_informes');
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
					SessionController::get()->eliminar('parametros_listado_informes');

					// Se obtienen los parámetros recibidos, verificando que posean un valor
					$parametros_listado_informes = $this->obtenerParametros($requestParams);
				} catch (Exception $ex) {
					// Si falla, vuelve al home y elimina la busqueda en sesión
					SessionController::get()->eliminar('parametros_listado_informes');
					SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
					$this->redireccionar('home', 'view');
				}
			} else // Si NO se recibieron las fechas desde y hasta
			{
				try {
					// Si existen los parámetros en la sesión
					if (SessionController::get()->existe('parametros_listado_informes')) {
						// Se obtienen desde la sesión
						$parametros_listado_informes = SessionController::get()->obtener('parametros_listado_informes');
					}
				} catch (Exception $ex) {
					// Si falla, vuelve al home y elimina la busqueda en sesión
					SessionController::get()->eliminar('parametros_listado_informes');
					SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
					$this->redireccionar('home', 'view');
				}
			}
		}

		// 25/02/2021 XXXX
		// Se obtienen todas las Comisiones
		$paramVista['listado_comisiones'] = NG::expedientesParam()->obtenerLugares(
			'C', null, null, null, null, null, null, null, null,
			null, // Todas: habilitadas y deshabilitadas
			null,
			array('habilitado_grp desc', 'descripcion_grp'), // 1ro las Habilitadas, 2do las Deshabilitadas, por su Descripción
			null, null);

		// Se setea el conjunto de parámetros para la vista
		// Si hubo un error, nunca se llega a ejecutar este código porque se sale por los "catch".
		$paramVista['parametros_listado_informes'] = $parametros_listado_informes;

		// Se guardan en sesion (siempre... en un caso es redundante)
		SessionController::get()->guardar('parametros_listado_informes', $parametros_listado_informes);

		// Se ejecuta la Vista
		// Instancio la vista y la muestro
		$vista = new BEListadosView($paramVista);
		$vista->vistaListadoInformes();
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function informesdatagrid($requestParams) {
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
			// El método 'obtenerParametros' internamente verifica el valor de cada filtro recibido
			// de esta manera: $f_xxx = (isset($requestParams['f_xxx'])) ? $requestParams['f_xxx'] : null;
			// agrupando cada filtro verificado, en el array '$parametros'
			$parametros = $this->obtenerParametros($requestParams);

			// Seteo el valor de control "draw"
			$p_draw = $requestParams['draw']; // es un entero

			// Obtengo datos para la paginación
			$p_limitStart = (trim($requestParams['start']) == '') ? null : trim($requestParams['start']);
			$p_limitLength = (trim($requestParams['length']) == '') ? null : trim($requestParams['length']);

			// Saneo todos los parametros
			$p_draw = $this->sanearParametro($p_draw);
			$p_limitStart = $this->sanearParametro($p_limitStart);
			$p_limitLength = $this->sanearParametro($p_limitLength);

			// Realizo la consulta y preparo el resultado
			$resultado = array();
			$resultado['draw'] = $p_draw;
			try
			{
				// La fecha Desde y Hasta son "necesarias". Si no me las proveen, simulo un resultado vacío.
				if (($parametros['f_fecha_desde'] == null || $parametros['f_fecha_desde'] == '') &&
					($parametros['f_fecha_hasta'] == null || $parametros['f_fecha_hasta'] == '')) {
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array(); // Es un array vacio!!! No NULL.
					$resultado['error'] = "Se requiere el filtro por fecha desde y fecha hasta.";
					// Se limpian los parámetros en la sesión por si arrastro un parametro erróneo
					SessionController::get()->eliminar('parametros_listado_informes');
				} else {
					// Verifico aquellos parametros que puedan ser inyectables con SQL
					$parametros['f_comision'] = ($parametros['f_comision']) ? Validator::get()->validar($parametros['f_comision'], PATRON_ALFANUM_EXT, true, 'C&oacute;digo de la Comisi&oacute;n') : null;

					$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					// Consulta de cantidad de informes (total)
					$cantidadTotalInformes = NG::reportes()->obtenerInformesCantidad(
						// Parametros
						$parametros['f_fecha_listado'],
						$parametros['f_fecha_desde'],
						$parametros['f_fecha_hasta'],
						$parametros['f_comision'],
						$parametros['f_vencidos'] == 1// flag para saber si muestro solo los vencidos
					);

					// Consulta de informes con el criterio respectivo
					$informes = NG::reportes()->obtenerInformes(
						// Parametros
						$parametros['f_fecha_listado'],
						$parametros['f_fecha_desde'],
						$parametros['f_fecha_hasta'],
						$parametros['f_comision'],
						$parametros['f_vencidos'] == 1, // flag para saber si muestro solo los vencidos
						true, // instancias completas!
						// Control de consulta
						array('anio', 'tipo', 'numero', 'cuerpo', 'alcance'), // criterio y sentido de orden (FIJO)
						$p_limitLength, // cantidad de registros (paginación)
						$p_limitStart // corrimiento de registros (paginación)
					);

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = $cantidadTotalInformes;
					$resultado['recordsFiltered'] = $cantidadTotalInformes;
					// Se asignan los informes obtenidos a la Vista
					$resultado['data'] = $informes;
				}
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;

				// Se limpian los parámetros en la sesión por si arrastro un parametro erróneo
				SessionController::get()->eliminar('parametros_listado_informes');
			}
		}

		// Si llegamos hasta aqui es que la consulta se ejecuto con éxito; entonces guardamos
		// los parametros en sesion
		SessionController::get()->guardar('parametros_listado_informes', $parametros);

		// Retorno resultados
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se genera el reporte en formato PDF
	 * @param  array $requestParams Conjunto de parámetros recibidos
	 */
	public function generarpdfinformes($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros iniciales de la vista (títulos. mensajes y verificaciones del usuario)
		$paramVista = $this->generarParametrosVista(); // (HEREDADO)

		// Saneo parametros (HEREDADO)
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		$parametros = $this->obtenerParametros($requestParams);

		try {
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$parametros['f_comision'] = ($parametros['f_comision']) ? Validator::get()->validar($parametros['f_comision'], PATRON_ALFANUM_EXT, true, 'C&oacute;digo de la Comisi&oacute;n') : null;

			// Obtenemos la cantidad de Informes, según el criterio de búsqueda utilizado
			$cantidadResultados = NG::reportes()->obtenerInformesCantidad(
				// Parametros
				$parametros['f_fecha_listado'],
				$parametros['f_fecha_desde'],
				$parametros['f_fecha_hasta'],
				$parametros['f_comision'],
				$parametros['f_vencidos'] == 1// flag para saber si muestro solo los vencidos
			);

			// Si la cantidad obtenida supera la cantidad máxima permitida para generar el listado
			if ($cantidadResultados > KRAKEN_REPORT_MAX_RESULT_COUNT) {
				throw new Exception(sprintf('Error al generar reporte: No ha podido generarse el reporte porque posee %d resultados y supera el l&iacute;mite de %d permitidos. Por favor, ajuste los par&aacute;metros del mismo y vuelva a intentarlo.',
					$cantidadResultados, KRAKEN_REPORT_MAX_RESULT_COUNT));
			}

			// Si la cantidad obtenida es menor al límite, obtenemos los Informes,
			// según el criterio de búsqueda utilizado
			$informes = NG::reportes()->obtenerInformes(
				// Parametros
				$parametros['f_fecha_listado'],
				$parametros['f_fecha_desde'],
				$parametros['f_fecha_hasta'],
				$parametros['f_comision'],
				$parametros['f_vencidos'] == 1, // flag para saber si muestro solo los vencidos
				true, // instancias completas!
				// Control de consulta
				array('anio desc', 'tipo', 'numero desc', 'cuerpo', 'alcance'));

			$paramVista['resultados'] = $informes;

			// Se obtienen las descripciones de los parámetros utilizados
			$parametros_codificados = $this->prepararParametrosParaReporte($parametros);
			// Se le pasa a la Vista el texto correspondiente al criterio de búsqueda utilizado, con sus nombres respectivos
			$paramVista['criterio_busqueda'] = $this->obtenerTextoCriterioBusqueda($parametros_codificados);

			// Instancio la vista y la muestro
			$vista = new BEReportesView($paramVista);
			$vista->vistaReportePdfInformes();
		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}

	/**
	 * Se genera el reporte en formato de documento de texto
	 * @param  array $requestParams Conjunto de parámetros recibidos
	 */
	public function generardocumentotextoinformes($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros iniciales de la vista (títulos. mensajes y verificaciones del usuario)
		$paramVista = $this->generarParametrosVista(); // (HEREDADO)

		// Saneo parametros (HEREDADO)
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		$parametros = $this->obtenerParametros($requestParams);

		try {
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$parametros['f_comision'] = ($parametros['f_comision']) ? Validator::get()->validar($parametros['f_comision'], PATRON_ALFANUM_EXT, true, 'C&oacute;digo de la Comisi&oacute;n') : null;

			// Obtenemos la cantidad de Informes, según el criterio de búsqueda utilizado
			$cantidadResultados = NG::reportes()->obtenerInformesCantidad(
				// Parametros
				$parametros['f_fecha_listado'],
				$parametros['f_fecha_desde'],
				$parametros['f_fecha_hasta'],
				$parametros['f_comision'],
				$parametros['f_vencidos'] == 1// flag para saber si muestro solo los vencidos
			);

			// Si la cantidad obtenida supera la cantidad máxima permitida para generar el listado
			if ($cantidadResultados > KRAKEN_REPORT_MAX_RESULT_COUNT) {
				throw new Exception(sprintf('Error al generar reporte: No ha podido generarse el reporte porque posee %d resultados y supera el l&iacute;mite de %d permitidos. Por favor, ajuste los par&aacute;metros del mismo y vuelva a intentarlo.',
					$cantidadResultados, KRAKEN_REPORT_MAX_RESULT_COUNT));
			}

			// Si la cantidad obtenida es menor al límite, obtenemos los Informes,
			// según el criterio de búsqueda utilizado
			$informes = NG::reportes()->obtenerInformes(
				// Parametros
				$parametros['f_fecha_listado'],
				$parametros['f_fecha_desde'],
				$parametros['f_fecha_hasta'],
				$parametros['f_comision'],
				$parametros['f_vencidos'] == 1, // flag para saber si muestro solo los vencidos
				true, // instancias completas!
				// Control de consulta
				array('anio desc', 'tipo', 'numero desc', 'cuerpo', 'alcance'));

			$paramVista['resultados'] = $informes;

			// Se obtienen las descripciones de los parámetros utilizados
			$parametros_codificados = $this->prepararParametrosParaReporte($parametros);
			// Se le pasa a la Vista el texto correspondiente al criterio de búsqueda utilizado, con sus nombres respectivos
			$paramVista['criterio_busqueda'] = $this->obtenerTextoCriterioBusqueda($parametros_codificados);

			// Instancio la vista y la muestro
			$vista = new BEDocumentosTextoView($paramVista);
			$vista->vistaDocumentoTextoInformes();
		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}
}
?>
