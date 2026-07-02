<?php
/**
 * Clase de controlador del listado de Expedientes sin digitalizar.
 *
 * @author XXXX
 */
class BEListadoexpedientessindigitalizarController extends BaseController
{
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	private $defaultOffsetMeses = -4;// Cantidad de años por defecto con los que se setean los filtros de fecha Desde

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
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['expedientessindigitalizardatagrid'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['generarpdfsindigitalizar'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['generardocumentotextosindigitalizar'] = NIVEL_ACCESO_OPERADOR;

		// Parámetros a excluir del criterio de búsqueda
		$this->parametros_excluidos = array('ptratamiento_comision');

		// Conjunto de etiquetas a mostrar en el criterio de búsqueda en los reportes
		$this->etiquetas = array();

		// Etiquetas automáticas para criterios de búsqueda
		$this->etiquetas['f_fecha_desde'] = 'Desde';
		$this->etiquetas['f_fecha_hasta'] = 'Hasta';
	}

	/**
	 * Se inicializan los parámetros del criterio de búsqueda, con valores por defecto
	 *
	 * @return array Conjunto de parámetros con sus valores por defecto
	 */
	private function setearValoresPorDefectoParaVista()
	{
		$resultado = array();
		$resultado['f_fecha_desde'] = $this->getFechaOffsetMeses($this->defaultOffsetMeses);
		$resultado['f_fecha_hasta'] = date("Y-m-d");

		return $resultado;
	}

	/**
	 * Verifica que los parámetros posean un valor, sino se les asigna un valor por defecto
	 *
	 * @param  array $requestParams Parámetros a verificar
	 * @return array                Parámetros verificados
	 */
	private function obtenerParametros($requestParams)
	{
		$resultado = array();

		// la fecha Desde, por defecto es 10 años menor que la fecha actual
		$resultado['f_fecha_desde'] = (isset($requestParams['f_fecha_desde'])) ? $requestParams['f_fecha_desde'] : $this->getFechaOffsetMeses($this->defaultOffsetMeses);
		// la fecha Hasta, por defecto es la fecha actual
		$resultado['f_fecha_hasta'] = (isset($requestParams['f_fecha_hasta'])) ? $requestParams['f_fecha_hasta'] : date("Y-m-d");

		// No interesa ya que NO se busca por Comisión NI por Estado
		$resultado['ptratamiento_comision'] = null;

		return $resultado;
	}

	/**
	 * Se obtienen las etiquetas de cada criterio utilizado en el listado
	 * @param  array $parametros Conjunto de parámetros con el criterio de búsqueda
	 * @return array $parametros El mismo conjunto con los nombres agregados
	 */
	public function prepararParametrosParaReporte($parametros)
	{
		if ( isset($parametros['f_fecha_desde']) && !is_null($parametros['f_fecha_desde']) )
			$parametros['f_fecha_desde'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_desde']);

		if ( isset($parametros['f_fecha_hasta']) && !is_null($parametros['f_fecha_hasta']) )
			$parametros['f_fecha_hasta'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_hasta']);

		return $parametros;
	}

	/**
	 * Invoca a la vista 'view' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function view($requestParams)
	{
		// Antes que nada verifico el nivel de acceso
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista (HEREDADO)
		$paramVista = $this->generarParametrosVista();

		// Se sanean los parámetros (HEREDADO)
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// La búsqueda se inicializa con valores por defecto
		$parametros_listado_sin_digitalizacion = $this->setearValoresPorDefectoParaVista();

		// Se recibe (o no) un parámetro para verificar si se pulsó el botón Restablecer
		$f_restablecer = (isset($requestParams['f_restablecer'])) ? $requestParams['f_restablecer'] : '';

		// Si se desea restablecer el criterio de búsqueda
		if ($f_restablecer != '')
		{
			try {
				// Se limpian los parámetros en la sesión
				SessionController::get()->eliminar('parametros_listado_sin_digitalizacion');
			}
			catch (Exception $ex) {
				// Si falla, vuelve al home y elimina la busqueda en sesión
				SessionController::get()->eliminar('parametros_listado_sin_digitalizacion');
				SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
				$this->redireccionar('home', 'view');
			}
		}
		else // si NO se desea restablecer
		{
			// Si por lo menos se recibe la fecha desde y la fecha hasta
			if (isset($requestParams['f_fecha_desde']) && $requestParams['f_fecha_desde'] != '' &&
				isset($requestParams['f_fecha_hasta']) && $requestParams['f_fecha_hasta'] != '' )
			{
				try	{
					// Se limpian los parámetros en la sesión
					SessionController::get()->eliminar('parametros_listado_sin_digitalizacion');

					// Se obtienen los parámetros recibidos, verificando que posean un valor
					$parametros_listado_sin_digitalizacion = $this->obtenerParametros($requestParams);
				}
				catch (Exception $ex) {
					// Si falla, vuelve al home y elimina la busqueda en sesión
					SessionController::get()->eliminar('parametros_listado_sin_digitalizacion');
					SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
					$this->redireccionar('home', 'view');
				}
			}
			else // Si NO se recibieron las fechas desde y hasta
			{
				try {
					// Si existen los parámetros en la sesión
					if (SessionController::get()->existe('parametros_listado_sin_digitalizacion')) {
						// Se obtienen desde la sesión
						$parametros_listado_sin_digitalizacion = SessionController::get()->obtener('parametros_listado_sin_digitalizacion');
					}
				} catch (Exception $ex) {
					// Si falla, vuelve al home y elimina la busqueda en sesión
					SessionController::get()->eliminar('parametros_listado_sin_digitalizacion');
					SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
					$this->redireccionar('home', 'view');
				}
			}
		}

		// Se setea el conjunto de parámetros para la vista
		// Si hubo un error, nunca se llega a ejecutar este código porque se sale por los "catch".
		$paramVista['parametros_listado_sin_digitalizacion'] = $parametros_listado_sin_digitalizacion;

		// Se guardan en sesion (siempre... en un caso es redundante)
		SessionController::get()->guardar('parametros_listado_sin_digitalizacion', $parametros_listado_sin_digitalizacion);

		// Se ejecuta la Vista
		// Instancio la vista y la muestro
		$vista = new BEListadosView($paramVista);
		$vista->vistaListadoExpedientesSinDigitalizar();
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function expedientessindigitalizardatagrid($requestParams)
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
			// Parámetros de filtros recibidos
			// El método 'obtenerParametros' internamente verifica el valor de cada filtro recibido
			// de esta manera: $f_xxx = (isset($requestParams['f_xxx'])) ? $requestParams['f_xxx'] : null;
			// agrupando cada filtro verificado, en el array '$parametros'
			$parametros = $this->obtenerParametros($requestParams);

			// Seteo el valor de control "draw"
			$p_draw = $requestParams['draw'];// es un entero

			// Obtengo datos para la paginación
			$p_limitStart = (trim($requestParams['start']) == '') ? null : trim($requestParams['start']);
			$p_limitLength = (trim($requestParams['length']) == '') ? null : trim($requestParams['length']);

			// Saneo todos los parametros
			$p_draw = $this->sanearParametro($p_draw);
			$p_limitStart = $this->sanearParametro($p_limitStart);
			$p_limitLength = $this->sanearParametro($p_limitLength);

			// Realizo la consulta y preparo el resultado
			$expedientes_sin_digitalizar = array();
			$resultado = array();
			$resultado['draw'] = $p_draw;
			try {
				// La fecha Desde y Hasta son "necesarias". Si no me las proveen, simulo un resultado vacío.
				if ( ($parametros['f_fecha_desde'] == null || $parametros['f_fecha_desde'] == '') &&
					 ($parametros['f_fecha_hasta'] == null || $parametros['f_fecha_hasta'] == '') )
				{
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array();  // Es un array vacio!!! No NULL.
					$resultado['error'] = "Se requiere el filtro por fecha desde y fecha hasta.";
					// Se limpian los parámetros en la sesión por si arrastro un parametro erróneo
					SessionController::get()->eliminar('parametros_expedientes_sin_digitalizar');
				}
				else
				{
					$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					// Consulta de expedientes en un rango de fechas determinado
					$expedientes = NG::reportes()->obtenerSoloExpedientes(
						// Parametros
				        $parametros['f_fecha_desde'],
						$parametros['f_fecha_hasta'],
						null, // f_fecha_comision 				No utilizado aquí
						null, // f_comision 					No utilizado aquí
						null, // f_estado 						No utilizado aquí
						null, // f_comisiones_elegidas_en_modal No utilizado aquí
						null, // ptratamiento_comision 			No utilizado aquí
						false, // SIN instancias completas!
						// Control de consulta
						array('anio', 'tipo', 'numero', 'cuerpo', 'alcance') // criterio y sentido de orden (FIJO)
					);

					// Por cada expediente devuelto
					$pos = 0;
					foreach ($expedientes as $e) {
						// Obtenemos el estado de la digitalizacion del expediente
						NG::expedientes()->determinarEstadoDigitalizacion($e);

						// Solamente nos quedamos con aquellos que NO poseen una digitalizacion
						if ( $e->estado_digitalizacion === ESTADO_DIGITALIZACION_SIN_CARGAR ) {
							$expedientes_sin_digitalizar[$pos] = $e;
							$pos++;
						}
					}

					// De los que quedaron, obtenemos los Proyectos, en caso que posea
					foreach ($expedientes_sin_digitalizar as $exp)
						$exp = NG::expedientes()->completarInstanciaSoloConProyectos($exp);

					// Resultado a mostrar en la Vista
					$paramVista['resultados'] = $expedientes_sin_digitalizar;

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = count($expedientes_sin_digitalizar);
					$resultado['recordsFiltered'] = count($expedientes_sin_digitalizar);
					// Se asignan los expedientes obtenidos a la Vista
					$resultado['data'] = $expedientes_sin_digitalizar;
				}
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array();  // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
				// Se limpian los parámetros en la sesión por si arrastro un parametro erróneo
				SessionController::get()->eliminar('parametros_expedientes_sin_digitalizar');
			}
		}

		// Si llegamos hasta aqui es que la consulta se ejecuto con éxito; entonces guardamos
		// los parametros en sesion
		SessionController::get()->guardar('parametros_expedientes_sin_digitalizar', $parametros);

		// Retorno resultados
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se obtienen los expedientes que NO posean una digitalizacion
	 * @param  [array] $parametros Array con la fecha Desde y la fecha Hasta
	 * @return [array]             Array con los Expedientes
	 */
	public function obtenerExpedientesSinDigitalizar($parametros)
	{
		$listado_completo = array();
		$rango_del_limite = 100; // Se obtienen grupos de 100 registros

		// Primero obtenemos la cantidad total de expedientes, en dicho rango de fechas
		$cantidad_auxiliar = NG::reportes()->obtenerCantidadSoloExpedientes(
			// Parametros
	        $parametros['f_fecha_desde'],
			$parametros['f_fecha_hasta'],
			null, // f_fecha_comision 				No utilizado aquí
			null, // f_comision 					No utilizado aquí
			null, // f_estado 						No utilizado aquí
			null, // f_comisiones_elegidas_en_modal No utilizado aquí
			null);// ptratamiento_comision 			No utilizado aquí

		// Si se obtuvieron expedientes en dicho rango de fechas
		if ( $cantidad_auxiliar > 0 )
		{
			$corte = false;
			$inicio = 0; // Inicio del límite para la query

			// Mientras existan expedientes
			while ( ($inicio < $cantidad_auxiliar) && !$corte )
			{
				// Se calcula el inicio del límite a pedir
				$inicio_del_limite = $inicio;

				// Se obtienen los expedientes ingresados en un rango de fechas determinado
				$expedientes_aux = NG::reportes()->obtenerSoloExpedientes(
					// Parametros
			        $parametros['f_fecha_desde'],
					$parametros['f_fecha_hasta'],
					null, // f_fecha_comision 				No utilizado aquí
					null, // f_comision 	  				No utilizado aquí
					null, // f_estado 		  				No utilizado aquí
					null, // f_comisiones_elegidas_en_modal No utilizado aquí
					null, // ptratamiento_comision 			No utilizado aquí
					false, // SIN instancias completas!
					// Control de consulta
					array('anio', 'tipo', 'numero', 'cuerpo', 'alcance'),
					$rango_del_limite, // cantidad de registros (paginación)
					$inicio_del_limite   // corrimiento de registros (paginación)
				);
				// Total obtenidos en el ciclo de 100
				$total_devueltos = count($expedientes_aux);

				// Si devuelve algún expediente
				if ( $total_devueltos != 0 ) {
					// Si se llegó al último ciclo del recorrido
					if ( $total_devueltos < $rango_del_limite )
						$corte = true;// Se corta el bucle del while

					// Obtengo el resto de la información si se solicita la instancia completa
					foreach ($expedientes_aux as $exp)
						$exp = NG::expedientes()->completarInstanciaSoloConProyectos($exp);

					// Se unen los resultados parciales
					$listado_completo = array_merge($listado_completo, $expedientes_aux);

					$expedientes_aux = null;
				}
				// Se incrementa el contador de a 100, que es el valor del rango
				$inicio += $rango_del_limite;
			}

			// Por cada expediente obtenido, del listado completo
			 $posicion = 0;
			 foreach ($listado_completo as $e) {
				// Obtenemos el estado de la digitalizacion del expediente
				NG::expedientes()->determinarEstadoDigitalizacion($e);

				// Solamente nos quedamos con aquellos que NO poseen una digitalizacion
				if ( $e->estado_digitalizacion === ESTADO_DIGITALIZACION_SIN_CARGAR ) {
					$expedientes_sin_digitalizar[$posicion] = $e;
					$posicion++;
				}
			}
		} else
			$expedientes_sin_digitalizar = array(); // Es un array vacio!!! No NULL

		return $expedientes_sin_digitalizar;
	}

	/**
	 * Se genera el reporte en formato PDF
	 * @param  array $requestParams Conjunto de parámetros
	 */
	public function generarpdfsindigitalizar($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros iniciales de la vista (títulos. mensajes y verificaciones del usuario)
		$paramVista = $this->generarParametrosVista(); // (HEREDADO)

		// Saneo parametros (HEREDADO)
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		$parametros = $this->obtenerParametros($requestParams);

		try {
			// Se obtienen los expedientes que NO posean una digitalizacion
			$expedientes_sin_digitalizar = $this->obtenerExpedientesSinDigitalizar($parametros);

			// Resultado a mostrar en la Vista
			$paramVista['resultados'] = $expedientes_sin_digitalizar;

			// Se obtienen las descripciones de los parámetros utilizados
			$parametros_codificados = $this->prepararParametrosParaReporte($parametros);
			// Se le pasa a la Vista el texto correspondiente al criterio de búsqueda utilizado, con sus nombres respectivos
			$paramVista['criterio_busqueda'] = $this->obtenerTextoCriterioBusqueda($parametros_codificados);

			// Instancio la vista y la muestro
			$vista = new BEReportesView($paramVista);
			$vista->vistaReportePdfSinDigitalizar();
		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}

	/**
	 * Se genera el reporte en formato Texto
	 * @param  array $requestParams Conjunto de parámetros
	 */
	public function generardocumentotextosindigitalizar($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros iniciales de la vista (títulos. mensajes y verificaciones del usuario)
		$paramVista = $this->generarParametrosVista(); // (HEREDADO)

		// Saneo parametros (HEREDADO)
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		$parametros = $this->obtenerParametros($requestParams);

		try {
			// Se obtienen los expedientes que NO posean una digitalizacion
			$expedientes_sin_digitalizar = $this->obtenerExpedientesSinDigitalizar($parametros);

			// Resultado a mostrar en la Vista
			$paramVista['resultados'] = $expedientes_sin_digitalizar;

			// Se obtienen las descripciones de los parámetros utilizados
			$parametros_codificados = $this->prepararParametrosParaReporte($parametros);
			// Se le pasa a la Vista el texto correspondiente al criterio de búsqueda utilizado, con sus nombres respectivos
			$paramVista['criterio_busqueda'] = $this->obtenerTextoCriterioBusqueda($parametros_codificados);

			// Instancio la vista y la muestro
			$vista = new BEDocumentosTextoView($paramVista);
			$vista->vistaDocumentoTextosExpedientesSinDigitalizar();
		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}
}
?>
