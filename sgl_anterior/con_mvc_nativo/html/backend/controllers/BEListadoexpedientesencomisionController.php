<?php
/**
 * Clase de controlador del listado de Expedientes en Comisión.
 *
 * @author XXXX, XXXX
 */
class BEListadoexpedientesencomisionController extends BaseController
{
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	private $defaultOffsetAnios = -10;// Cantidad de años por defecto con los que se setean los filtros de fecha Desde

	private $limite_para_vencidos;
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
		$this->accionesPermitidas['expedientesencomisiondatagrid'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['generarpdfexpedientesencomision'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['generardocumentotextoexpedientesencomision'] = NIVEL_ACCESO_PERIODISTA;

		$this->limite_para_vencidos = LIMITE_EXPEDIENTES_VENCIDOS;// A partir de los X días los Expedientes están vencidos

		// Parámetros a excluir del criterio de búsqueda
		$this->parametros_excluidos = array('ptratamiento_comision', 'f_vencidos', 'f_comisiones_elegidas_en_modal');

		// Conjunto de etiquetas a mostrar en el criterio de búsqueda en los reportes
		$this->etiquetas = array();

		// Etiquetas automáticas para criterios de búsqueda
		$this->etiquetas['f_fecha_desde']    = '&nbsp;&nbsp;&nbsp;Desde';
		$this->etiquetas['f_fecha_hasta']    = '&nbsp;&nbsp;&nbsp;Hasta';
		$this->etiquetas['f_fecha_listado']  = '&nbsp;&nbsp;&nbsp;Fecha del listado';
		$this->etiquetas['f_fecha_comision'] = '&nbsp;&nbsp;&nbsp;Ingresados en la Comisi&oacute;n antes del';
		$this->etiquetas['f_comision'] 		 = '&nbsp;&nbsp;&nbsp;Comisi&oacute;n';
		$this->etiquetas['f_estado'] 		 = '&nbsp;&nbsp;&nbsp;Estado';
	}

	/**
	 * Se inicializan los parámetros del criterio de búsqueda, con valores por defecto
	 *
	 * @return array Conjunto de parámetros con sus valores por defecto
	 */
	private function setearValoresPorDefectoParaVista()
	{
		$resultado = array();
		$resultado['f_fecha_desde']    = $this->obtenerFechaAniosAtrasConDiaInicial($this->defaultOffsetAnios);//getFechaOffsetAnios
		$resultado['f_fecha_hasta']    = date("Y-m-d");
		$resultado['f_fecha_listado']  = date("Y-m-d");// Utilizada para calcular los días en comisión
		$resultado['f_fecha_comision'] = date("Y-m-d");
		$resultado['f_comision']       = 0;
		$resultado['f_estado'] 	       = 0;
		$resultado['f_vencidos'] 	   = 0;

		return $resultado;
	}

	/**
	 * Se setean los parámetros, para ser utilizados en la Capa de Negocio
	 *
	 * @return array $parametros Array con los parámetros seteados para la NG
	 */
	private function setearValoresParaNG($parametros)
	{
		if ($parametros != null)
		{
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$parametros['f_comision'] = ($parametros['f_comision']) ? Validator::get()->validar($parametros['f_comision'], PATRON_ALFANUM_EXT, true, 'C&oacute;digo de la Comisi&oacute;n') : null;
			$parametros['f_estado']   = ($parametros['f_estado'] != 0) ? Validator::get()->validar($parametros['f_estado'], PATRON_NUMEROS, true, 'Estado del Expediente') : null;

			// Si se eligió una Comisión, no se busca por Estado ni por el grupo de Comisiones de la modal
			if ($parametros['f_comision'] != null){
				$parametros['f_estado'] = null;
				$parametros['f_comisiones_elegidas_en_modal'] = null;
			}

			// Si se eligió un Estado, no se busca por Comisión ni por el grupo de Comisiones de la modal
			if ($parametros['f_estado'] != null){
				$parametros['f_comision'] = null;
				$parametros['f_comisiones_elegidas_en_modal'] = null;
			}
		}

		return $parametros;
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
		$resultado['f_fecha_desde'] = (isset($requestParams['f_fecha_desde'])) ? $requestParams['f_fecha_desde'] : $this->getFechaOffsetAnios($this->defaultOffsetAnios);
		// la fecha Hasta, por defecto es la fecha actual
		$resultado['f_fecha_hasta'] = (isset($requestParams['f_fecha_hasta'])) ? $requestParams['f_fecha_hasta'] : date("Y-m-d");
		// la fecha del Listado, por defecto es la fecha actual
		$resultado['f_fecha_listado'] = (isset($requestParams['f_fecha_listado'])) ? $requestParams['f_fecha_listado'] : date("Y-m-d");
		// la fecha de Comisión, por defecto es la fecha actual
		$resultado['f_fecha_comision'] = (isset($requestParams['f_fecha_comision'])) ? $requestParams['f_fecha_comision'] : date("Y-m-d");

		// Se recibe una Comisión
		$resultado['f_comision'] = (isset($requestParams['f_comision'])) ? $requestParams['f_comision'] : null;

		// Se recibe un Estado
		$resultado['f_estado'] = (isset($requestParams['f_estado'])) ? $requestParams['f_estado'] : null;

		// Se recibe la opción 'Sólo Vencidos'
		$resultado['f_vencidos'] = (isset($requestParams['f_vencidos'])) ? $requestParams['f_vencidos'] : 0;

		// Si se busca por Estado
		if ( (!is_null($resultado['f_estado'])) && ($resultado['f_estado'] != 0) )
			$resultado['ptratamiento_comision'] = false;// No interesa ya que NO se busca por Comisión
		// Si se busca por Comisión
		elseif( (!is_null($resultado['f_comision'])) && ($resultado['f_comision'] != 0) )
			// Aquí interesan los estados utilizados en el tratamiento de la Comisión
			$resultado['ptratamiento_comision'] = true;
		// Si NO se busca ni por Estado ni por Comisión, y se eligieron Comisiones de la modal
		elseif ( (isset($requestParams['f_comisiones_elegidas_en_modal']) && $requestParams['f_comisiones_elegidas_en_modal'] != '' ) ){
			// Se arma el array de Comisiones elegidas separadas por coma
			$resultado['f_comisiones_elegidas_en_modal'] = explode(',', $requestParams['f_comisiones_elegidas_en_modal']);
			$resultado['ptratamiento_comision'] = null;// No interesa ya que NO se busca por Comisión NI por Estado
		}

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

		if ( isset($parametros['f_fecha_listado']) && !is_null($parametros['f_fecha_listado']) )
			$parametros['f_fecha_listado'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_listado']);

		if ( isset($parametros['f_fecha_comision']) && !is_null($parametros['f_fecha_comision']) )
			$parametros['f_fecha_comision'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_comision']);

		// Si existe como clave del vector de Parámetros
		if (array_key_exists('f_comision', $parametros)) {
			// Se obtiene el nombre de la Comisión elegida (por ello la verificación con cero)
			$nuevoValor = (isset($parametros['f_comision']) && $parametros['f_comision'] != '0') ? NG::expedientesParam()->obtenerLugar('C', $parametros['f_comision']) : null;
			$parametros['f_comision'] = (!is_null($nuevoValor)) ? $nuevoValor->descripcion_grp : null;
		}

		// Si existe como clave del vector de Parámetros
		if (array_key_exists('f_estado', $parametros)) {
			// Se obtiene el nombre del Estado elegido (por ello la verificación con cero)
			$nuevoValor = (isset($parametros['f_estado']) && $parametros['f_estado'] != '0' ) ? NG::expedientesParam()->obtenerCodestado($parametros['f_estado']) : null;
			$parametros['f_estado'] = (!is_null($nuevoValor)) ? $nuevoValor->nombre_estado: null;
		}

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
		$parametros_expedientes_en_comision = $this->setearValoresPorDefectoParaVista();

		// Se recibe (o no) un parámetro para verificar si se pulsó el botón Restablecer
		$f_restablecer = (isset($requestParams['f_restablecer'])) ? $requestParams['f_restablecer'] : '';

		// Si se desea restablecer el criterio de búsqueda
		if ($f_restablecer != '')
		{
			try {
				// Se limpian los parámetros en la sesión
				SessionController::get()->eliminar('parametros_expedientes_en_comision');
			}
			catch (Exception $ex) {
				// Si falla, vuelve al home y elimina la busqueda en sesión
				SessionController::get()->eliminar('parametros_expedientes_en_comision');
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
					SessionController::get()->eliminar('parametros_expedientes_en_comision');

					// Se obtienen los parámetros recibidos, verificando que posean un valor
					$parametros_expedientes_en_comision = $this->obtenerParametros($requestParams);
				}
				catch (Exception $ex) {
					// Si falla, vuelve al home y elimina la busqueda en sesión
					SessionController::get()->eliminar('parametros_expedientes_en_comision');
					SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
					$this->redireccionar('home', 'view');
				}
			}
			else // Si NO se recibieron las fechas desde y hasta
			{
				try {
					// Si existen los parámetros en la sesión
					if (SessionController::get()->existe('parametros_expedientes_en_comision')) {
						// Se obtienen desde la sesión
						$parametros_expedientes_en_comision = SessionController::get()->obtener('parametros_expedientes_en_comision');
					}
				} catch (Exception $ex) {
					// Si falla, vuelve al home y elimina la busqueda en sesión
					SessionController::get()->eliminar('parametros_expedientes_en_comision');
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

		// Se obtienen todos los Estados
		$paramVista['listado_codestados'] = NG::expedientesParam()->obtenerCodestados(
			null,
			//null, (al retirarse codigo_estado 07/01/2022 XXXX)
			null, null, null, null,
			null, // Todos: habilitados y deshabilitados
			null, null,
			array('habilitado_codestado desc, id_codestado'),
			null, null);

		// Se setea el conjunto de parámetros para la vista
		// Si hubo un error, nunca se llega a ejecutar este código porque se sale por los "catch".
		$paramVista['parametros_expedientes_en_comision'] = $parametros_expedientes_en_comision;

		// Se guardan en sesion (siempre... en un caso es redundante)
		SessionController::get()->guardar('parametros_expedientes_en_comision', $parametros_expedientes_en_comision);

		// Se ejecuta la Vista
		// Instancio la vista y la muestro
		$vista = new BEListadosView($paramVista);
		$vista->vistaListadoExpedientesEnComision();
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function expedientesencomisiondatagrid($requestParams)
	{
		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['recordsTotal']    = 0;
			$resultado['recordsFiltered'] = 0;
			$resultado['data']            = array();
			$resultado['error']           = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['numeroError']     = SessionController::get()->obtener('NUMERO_ERROR');
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
			$resultado = array();
			$resultado['draw'] = $p_draw;
			try {
				// La fecha Desde y Hasta son "necesarias". Si no me las proveen, simulo un resultado vacío.
				if ( ($parametros['f_fecha_desde'] == null || $parametros['f_fecha_desde'] == '') &&
					 ($parametros['f_fecha_hasta'] == null || $parametros['f_fecha_hasta'] == '') )
				{
					$resultado['recordsTotal']    = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data']            = array();  // Es un array vacio!!! No NULL.
					$resultado['error']           = "Se requiere el filtro por fecha desde y fecha hasta.";
					// Se limpian los parámetros en la sesión por si arrastro un parametro erróneo
					SessionController::get()->eliminar('parametros_expedientes_en_comision');
				}
				else
				{
					// Se verifican previamente los parámetros para ser utilizados en la Capa de Negocio
					// aquí se utiliza la clase Validator para validar mediante expresiones regulares
					$parametros = $this->setearValoresParaNG($parametros);

					$p_limitStart  = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					// Si se desean listar sólo los Expedientes vencidos
					if ( $parametros['f_vencidos'] == 1 ) {

						// Consulta de expedientes Vencidos con el criterio respectivo
						$expedientes = NG::reportes()->obtenerExpedientesEnComisionVencidos(
							// Parametros
							$parametros['f_fecha_listado'],
					        $parametros['f_fecha_desde'],
							$parametros['f_fecha_hasta'],
							$parametros['f_fecha_comision'],
							$parametros['f_comision'],
							$parametros['f_estado'],
							$parametros['f_comisiones_elegidas_en_modal'],
							$parametros['ptratamiento_comision'],
							true, // instancias completas!
							// Control de consulta
							array('anio', 'tipo', 'numero', 'cuerpo', 'alcance') // criterio y sentido de orden (FIJO)
						);

						// Se asigna la cantidad de expedientes Vencidos
						$cantidadTotalExpedientes = (isset($expedientes)) ? count($expedientes) : 0;

					} else {
						// Consulta de cantidad de expedientes
						$cantidadTotalExpedientes = NG::reportes()->obtenerExpedientesEnComisionCantidad(
							// Parametros
					        $parametros['f_fecha_desde'],
							$parametros['f_fecha_hasta'],
							$parametros['f_fecha_comision'],
							$parametros['f_comision'],
							$parametros['f_estado'],
							$parametros['f_comisiones_elegidas_en_modal'],
							$parametros['ptratamiento_comision']
						);

						// Consulta de expedientes con el criterio respectivo
						$expedientes = NG::reportes()->obtenerExpedientesEnComision(
							// Parametros
							$parametros['f_fecha_listado'],
					        $parametros['f_fecha_desde'],
							$parametros['f_fecha_hasta'],
							$parametros['f_fecha_comision'],
							$parametros['f_comision'],
							$parametros['f_estado'],
							$parametros['f_comisiones_elegidas_en_modal'],
							$parametros['ptratamiento_comision'],
							true, // instancias completas!
							// Control de consulta
							array('anio', 'tipo', 'numero', 'cuerpo', 'alcance'), // criterio y sentido de orden (FIJO)
							$p_limitLength, // cantidad de registros (paginación)
							$p_limitStart // corrimiento de registros (paginación)
						);

					}

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal']    = $cantidadTotalExpedientes;
					$resultado['recordsFiltered'] = $cantidadTotalExpedientes;
					// Se asignan los expedientes obtenidos a la Vista
					$resultado['data'] 			  = $expedientes;

						//Logger::get()->Log("expedientes", $expedientes, false);
				}
			} catch (Exception $ex) {
				$resultado['recordsTotal']    = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data']            = array();  // Es un array vacio!!! No NULL.
				$resultado['error']           = $ex->getMessage();
				$resultado['numeroError']     = ERROR_CONTROLLER_GENERICO;
				// Se limpian los parámetros en la sesión por si arrastro un parametro erróneo
				SessionController::get()->eliminar('parametros_expedientes_en_comision');
			}
		}

		// Si llegamos hasta aqui es que la consulta se ejecuto con éxito; entonces guardamos
		// los parametros en sesion
		SessionController::get()->guardar('parametros_expedientes_en_comision', $parametros);

		// Retorno resultados
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se genera el reporte en formato PDF
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function generarpdfexpedientesencomision($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros iniciales de la vista (títulos. mensajes y verificaciones del usuario)
		$paramVista = $this->generarParametrosVista(); // (HEREDADO)

		// Saneo parametros (HEREDADO)
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		$parametros = $this->obtenerParametros($requestParams);

		try {
			// Se verifican previamente los parámetros para ser utilizados en la Capa de Negocio
			// aquí se utiliza la clase Validator para validar mediante expresiones regulares
			$parametros = $this->setearValoresParaNG($parametros);

			// Si se desean listar sólo los Expedientes vencidos
			if ( $parametros['f_vencidos'] == 1 ) {

				// Consulta de expedientes Vencidos con el criterio respectivo
				$expedientes = NG::reportes()->obtenerExpedientesEnComisionVencidos(
					// Parametros
					$parametros['f_fecha_listado'],
			        $parametros['f_fecha_desde'],
					$parametros['f_fecha_hasta'],
					$parametros['f_fecha_comision'],
					$parametros['f_comision'],
					$parametros['f_estado'],
					$parametros['f_comisiones_elegidas_en_modal'],
					$parametros['ptratamiento_comision'],
					true, // instancias completas!
					// Control de consulta
					array('anio', 'tipo', 'numero', 'cuerpo', 'alcance') // criterio y sentido de orden (FIJO)
				);
			} else {
				// Consulta de expedientes con el criterio respectivo
				$expedientes = NG::reportes()->obtenerExpedientesEnComision(
					// Parametros
					$parametros['f_fecha_listado'],
			        $parametros['f_fecha_desde'],
					$parametros['f_fecha_hasta'],
					$parametros['f_fecha_comision'],
					$parametros['f_comision'],
					$parametros['f_estado'],
					$parametros['f_comisiones_elegidas_en_modal'],
					$parametros['ptratamiento_comision'],
					true, // instancias completas!
					// Control de consulta
					array('anio', 'tipo', 'numero', 'cuerpo', 'alcance') // criterio y sentido de orden (FIJO)
				);
			}

			// Se asigna la cantidad de expedientes Vencidos
			$cantidadResultados = count($expedientes);

			// Si la cantidad obtenida supera la cantidad máxima permitida para generar el listado
			if ($cantidadResultados > KRAKEN_REPORT_MAX_RESULT_COUNT)
				throw new Exception(sprintf('Error al generar reporte: No ha podido generarse el reporte porque posee %d resultados y supera el l&iacute;mite de %d permitidos. Por favor, ajuste los par&aacute;metros del mismo y vuelva a intentarlo.',
					$cantidadResultados, KRAKEN_REPORT_MAX_RESULT_COUNT));

			// Se asignan los expedientes obtenidos a la Vista
			$paramVista['resultados'] = $expedientes;

			// Se obtienen las descripciones de los parámetros utilizados
			$parametros_codificados = $this->prepararParametrosParaReporte($parametros);
			// Se le pasa a la Vista los parámetros codificados
			$paramVista['parametros_codificados'] = $parametros_codificados;
			// Se le pasa a la Vista el texto correspondiente al criterio de búsqueda utilizado, con sus nombres respectivos
			$paramVista['criterio_busqueda'] = $this->obtenerTextoCriterioBusqueda($parametros_codificados);

			// Instancio la vista y la muestro
			$vista = new BEReportesView($paramVista);
			$vista->vistaReportePdfExpedientesEnComision();

		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}

	/**
	 * Se genera el documento de texto
	 * @param  array $requestParams Conjunto de parámetros
	 */
	public function generardocumentotextoexpedientesencomision($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros iniciales de la vista (títulos. mensajes y verificaciones del usuario)
		$paramVista = $this->generarParametrosVista(); // (HEREDADO)

		// Saneo parametros (HEREDADO)
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		$parametros = $this->obtenerParametros($requestParams);

		try {
			// Se verifican previamente los parámetros para ser utilizados en la Capa de Negocio
			// aquí se utiliza la clase Validator para validar mediante expresiones regulares
			$parametros = $this->setearValoresParaNG($parametros);

			// Si se desean listar sólo los Expedientes vencidos
			if ( $parametros['f_vencidos'] == 1 ) {

				// Consulta de expedientes Vencidos con el criterio respectivo
				$expedientes = NG::reportes()->obtenerExpedientesEnComisionVencidos(
					// Parametros
					$parametros['f_fecha_listado'],
			        $parametros['f_fecha_desde'],
					$parametros['f_fecha_hasta'],
					$parametros['f_fecha_comision'],
					$parametros['f_comision'],
					$parametros['f_estado'],
					$parametros['f_comisiones_elegidas_en_modal'],
					$parametros['ptratamiento_comision'],
					true, // instancias completas!
					// Control de consulta
					array('anio', 'tipo', 'numero', 'cuerpo', 'alcance') // criterio y sentido de orden (FIJO)
				);
			} else {
				// Consulta de expedientes con el criterio respectivo
				$expedientes = NG::reportes()->obtenerExpedientesEnComision(
					// Parametros
					$parametros['f_fecha_listado'],
			        $parametros['f_fecha_desde'],
					$parametros['f_fecha_hasta'],
					$parametros['f_fecha_comision'],
					$parametros['f_comision'],
					$parametros['f_estado'],
					$parametros['f_comisiones_elegidas_en_modal'],
					$parametros['ptratamiento_comision'],
					true, // instancias completas!
					// Control de consulta
					array('anio', 'tipo', 'numero', 'cuerpo', 'alcance') // criterio y sentido de orden (FIJO)
				);
			}

			// Se asigna la cantidad de expedientes Vencidos
			$cantidadResultados = count($expedientes);

			// Si la cantidad obtenida supera la cantidad máxima permitida para generar el listado
			if ($cantidadResultados > KRAKEN_REPORT_MAX_RESULT_COUNT)
				throw new Exception(sprintf('Error al generar reporte: No ha podido generarse el reporte porque posee %d resultados y supera el l&iacute;mite de %d permitidos. Por favor, ajuste los par&aacute;metros del mismo y vuelva a intentarlo.',
					$cantidadResultados, KRAKEN_REPORT_MAX_RESULT_COUNT));

			// Se asignan los expedientes obtenidos a la Vista
			$paramVista['resultados'] = $expedientes;

			// Se obtienen las descripciones de los parámetros utilizados
			$parametros_codificados = $this->prepararParametrosParaReporte($parametros);
			// Se le pasa a la Vista los parámetros codificados
			$paramVista['parametros_codificados'] = $parametros_codificados;
			// Se le pasa a la Vista el texto correspondiente al criterio de búsqueda utilizado, con sus nombres respectivos
			$paramVista['criterio_busqueda'] = $this->obtenerTextoCriterioBusqueda($parametros_codificados);

			// Instancio la vista y la muestro
			$vista = new BEDocumentosTextoView($paramVista);
			$vista->vistaDocumentoTextoExpedientesEnComision();
		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}
}
?>
