<?php
/**
 * Clase de controlador para la generación de los reportes.
 *
 * @author XXXX y XXXX
 */
class BEReportesController extends BaseController
{
	protected $etiquetas;
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

		// Nombre del módulo (subsistema) al que corresponde el controlador
		$this->nombreModulo = 'EXPEDIENTES';

		// Determino las acciones válidas y su nivel de acceso mínimo requerido
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_PERIODISTA;// NIVEL_ACCESO_CONCEJAL
		$this->accionesPermitidas['busquedaavanzada'] = NIVEL_ACCESO_PERIODISTA;// NIVEL_ACCESO_CONCEJAL
		$this->accionesPermitidas['busquedaavanzadadocumentotexto'] = NIVEL_ACCESO_PERIODISTA;// NIVEL_ACCESO_CONCEJAL
		$this->accionesPermitidas['busquedaavanzadaplanillacalculo'] = NIVEL_ACCESO_PERIODISTA;// NIVEL_ACCESO_CONCEJAL
		$this->accionesPermitidas['busquedaporantecedente'] = NIVEL_ACCESO_PERIODISTA;// NIVEL_ACCESO_CONCEJAL

		// Parámetros a excluir del criterio de búsqueda
		$this->parametros_excluidos = array('pfecha_entrada_expe');

		// Conjunto de etiquetas a mostrar en el criterio de búsqueda en los reportes
		$this->etiquetas = array();

		// Etiquetas automáticas para criterios de búsqueda
		$this->etiquetas['f_fecha_desde']   = 'Desde';
		$this->etiquetas['f_fecha_hasta']   = 'Hasta';
		$this->etiquetas['f_opcion_fechas'] = 'Por';
		$this->etiquetas['f_caratula'] 		= 'Car&aacute;tula';
		$this->etiquetas['f_iniciador'] 	= 'Iniciador';
		$this->etiquetas['f_categoria'] 	= 'Categor&iacute;a';
		$this->etiquetas['f_comision'] 		= 'Comisi&oacute;n';
		$this->etiquetas['f_estado'] 		= 'Estado';
		$this->etiquetas['f_autor'] 		= 'Autor';
		$this->etiquetas['f_tema'] 			= 'Tema';
		$this->etiquetas['f_numero'] 		= 'N&uacute;mero';
		$this->etiquetas['f_anio'] 			= 'A&ntilde;o';
	}

	/**
	 * Verifica que los parámetros, de la búsqueda Avanzada, posean un valor, sino se les asigna un valor por defecto
	 *
	 * @param  array $requestParams Parámetros a verificar
	 * @return array                Parámetros verificados
	 */
	private function obtenerParametrosBusquedaAvanzada($requestParams)
	{
		$resultado = array();

		// la fecha Desde por defecto es 10 años menor que la fecha actual
		$resultado['f_fecha_desde'] = (isset($requestParams['f_fecha_desde'])) ? $requestParams['f_fecha_desde'] : $this->getFechaOffsetAnios($this->defaultOffsetAnios);
		// la fecha Hasta por defecto es la fecha actual
		$resultado['f_fecha_hasta'] = (isset($requestParams['f_fecha_hasta'])) ? $requestParams['f_fecha_hasta'] : date("Y-m-d");

		// Si se desea buscar por: fecha de Entrada | fecha de Sanción | fecha de Promulgación
		$resultado['pfecha_entrada_expe'] = null;
		$resultado['pfecha_sancion'] = null;
		$resultado['pfecha_promulga'] = null;

		// Se recibe la opción para buscar por fechas
		$resultado['f_opcion_fechas'] = (isset($requestParams['f_opcion_fechas'])) ? $requestParams['f_opcion_fechas'] : 0;
		switch ($resultado['f_opcion_fechas']) {
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
		// aquí no se separa ya que se sabe que el tipo es 'C', sólo se utiliza el codigo_grp
		$resultado['f_comision'] = (isset($requestParams['f_comision'])) ? $requestParams['f_comision'] : null;

		// Se recibe un Estado
		$resultado['f_estado'] = (isset($requestParams['f_estado'])) ? $requestParams['f_estado'] : null;

		// Si se busca por Estado
		if ( !is_null($resultado['f_estado']) )
			$resultado['ptratamiento_comision'] = false;// No interesa ya que NO se busca por Comisión
		// Si se busca por Comisión
		elseif( !is_null($resultado['f_comision']) )
			$resultado['ptratamiento_comision'] = true;// Aquí interesan los estados utilizados en el tratamiento de la Comisión
		else
			$resultado['ptratamiento_comision'] = null;// No interesa ya que NO se busca por Comisión NI por Estado

		return $resultado;
	}

	/**
	 * Se setean los parámetros, para ser utilizados en la Capa de Negocio
	 *
	 * @return array $parametros Array con los parámetros seteados para la NG
	 */
	private function setearValoresParaNG($parametros)
	{
		if ($parametros != null) {
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$parametros['f_opcion_fechas']   = ($parametros['f_opcion_fechas'] != null) ? Validator::get()->validar($parametros['f_opcion_fechas'], PATRON_NUMEROS, true, 'Opci&oacute;n para determinar c&oacute;mo utilizar las fechas') : 0;
			$parametros['piniciador_tipo']   = ($parametros['piniciador_tipo'] != 0) ? Validator::get()->validar($parametros['piniciador_tipo'], PATRON_LETRAS, true, 'Tipo del Iniciador') : null;
			$parametros['piniciador_codigo'] = ($parametros['piniciador_codigo']) ? Validator::get()->validar($parametros['piniciador_codigo'], PATRON_ALFANUM_EXT, true, 'C&oacute;digo del Iniciador') : null;
			$parametros['f_categoria'] 	     = ($parametros['f_categoria'] != 0) ? Validator::get()->validar($parametros['f_categoria'], PATRON_NUMEROS, true, 'Categor&iacute;a del Expediente') : null;
			$parametros['f_comision']  	     = ($parametros['f_comision']) ? Validator::get()->validar($parametros['f_comision'], PATRON_ALFANUM_EXT, true, 'C&oacute;digo del Iniciador') : null;
			$parametros['f_estado']    	     = ($parametros['f_estado'] != 0) ? Validator::get()->validar($parametros['f_estado'], PATRON_NUMEROS, true, 'Estado del Expediente') : null;
			$parametros['pautor_tipo'] 	     = ($parametros['pautor_tipo'] != 0) ? Validator::get()->validar($parametros['pautor_tipo'], PATRON_LETRAS, true, 'Tipo del Autor') : null;
			$parametros['pautor_codigo'] 	 = ($parametros['pautor_codigo']) ? Validator::get()->validar($parametros['pautor_codigo'], PATRON_ALFANUM_EXT, true, 'C&oacute;digo del Autor') : null;
			$parametros['f_tema'] 	   	     = ($parametros['f_tema'] != 0) ? Validator::get()->validar($parametros['f_tema'], PATRON_NUMEROS, true, 'Tema del Expediente') : null;
			$parametros['f_caratula']  	     = ($parametros['f_caratula'] != '') ? Validator::get()->validar($parametros['f_caratula'], PATRON_ALFANUM_EXT, true, 'Car&aacute;tula del Expediente o Extracto de un Proyecto') : null;

			// Si se eligió una Comisión, no se busca por Estado
			if ($parametros['f_comision'] != null)
				$parametros['f_estado'] = null;

			// Si se eligió un Estado, no se busca por Comisión
			if ($parametros['f_estado'] != null)
				$parametros['f_comision'] = null;
		}

		return $parametros;
	}

	/**
	 * Se obtienen los valores parametricos de cada criterio utilizado en la búsqueda
	 * @param  array $parametros Conjunto de parámetros con el criterio de búsqueda
	 * @return array $parametros El mismo conjunto con los nombres agregados
	 */
	public function obtenerParametrosCodificadosCriterio($parametros)
	{
		$parametros['pfecha_entrada_expe'] = null;// Se anula el Rango de fechas ya que no se muestra en el reporte
		$parametros['pautor_codigo']	   = null;// Se anula el código del Autor ya que no se muestra en el reporte
		$parametros['piniciador_codigo']   = null;// Se anula el código del Iniciador ya que no se muestra en el reporte

		if ( isset($parametros['f_fecha_desde']) && !is_null($parametros['f_fecha_desde']) )
			$parametros['f_fecha_desde'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_desde']);
		if ( isset($parametros['f_fecha_hasta']) && !is_null($parametros['f_fecha_hasta']) )
			$parametros['f_fecha_hasta'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_hasta']);

		if (isset($parametros['f_opcion_fechas']) && $parametros['f_opcion_fechas'] == '0') $parametros['f_opcion_fechas'] = 'todos los expedientes';
		if (isset($parametros['f_opcion_fechas']) && $parametros['f_opcion_fechas'] == '1') $parametros['f_opcion_fechas'] = 's&oacute;lo Sancionados';
		if (isset($parametros['f_opcion_fechas']) && $parametros['f_opcion_fechas'] == '2') $parametros['f_opcion_fechas'] = 's&oacute;lo Promulgados';

		// Se obtiene el nombre del Iniciador elegido (por ello la verificación con cero)
		if (array_key_exists('f_iniciador', $parametros)) {
			$nuevoValor = (isset($parametros['f_iniciador'][0]) && $parametros['f_iniciador'][0] != '0') ? NG::expedientesParam()->obtenerLugar($parametros['f_iniciador'][0], $parametros['f_iniciador'][1]) : null;
			$parametros['f_iniciador'] = (!is_null($nuevoValor))
				? $nuevoValor->descripcion_grp
				: null;
		}

		// Se obtiene el nombre de la Categoría elegida (por ello la verificación con cero)
		if (array_key_exists('f_categoria', $parametros)) {
			$nuevoValor = (isset($parametros['f_categoria']) && $parametros['f_categoria'] != '0') ? NG::expedientesParam()->obtenerCodcategoria($parametros['f_categoria']) : null;
			$parametros['f_categoria'] = (!is_null($nuevoValor))
				? $nuevoValor->descripcion_categoria
				: null;
		}

		// Se obtiene el nombre de la Comisión elegida (por ello la verificación con cero)
		if (array_key_exists('f_comision', $parametros)) {
			$nuevoValor = (isset($parametros['f_comision']) && $parametros['f_comision'] != '') ? NG::expedientesParam()->obtenerLugar('C', $parametros['f_comision']) : null;
			$parametros['f_comision'] = (!is_null($nuevoValor))
				? $nuevoValor->descripcion_grp
				: null;
		}

		// Se obtiene el nombre del Estado elegido (por ello la verificación con cero)
		if (array_key_exists('f_estado', $parametros)) {
			$nuevoValor = (isset($parametros['f_estado']) && $parametros['f_estado'] != '0' ) ? NG::expedientesParam()->obtenerCodestado($parametros['f_estado']) : null;
			$parametros['f_estado'] = (!is_null($nuevoValor))
				? $nuevoValor->nombre_estado
				: null;
		}

		// Se obtiene el nombre del Autor elegido (por ello la verificación con cero)
		if (array_key_exists('f_autor', $parametros)) {
			$nuevoValor = (isset($parametros['f_autor'][0]) && $parametros['f_autor'][0] != '0') ? NG::expedientesParam()->obtenerLugar($parametros['f_autor'][0], $parametros['f_autor'][1]) : null;
			$parametros['f_autor'] = (!is_null($nuevoValor))
				? $nuevoValor->descripcion_grp
				: null;
		}

		// Se obtiene el nombre del Tema elegido (por ello la verificación con cero)
		if (array_key_exists('f_tema', $parametros)) {
			$nuevoValor = (isset($parametros['f_tema']) && $parametros['f_tema'] != '0' ) ? NG::expedientesParam()->obtenerCodtema($parametros['f_tema']) : null;
			$parametros['f_tema'] = (!is_null($nuevoValor))
				? $nuevoValor->descripcion_tema
				: null;
		}

		return $parametros;
	}

	/**
	 * [busquedaavanzada description]
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function busquedaavanzada($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros iniciales de la vista (títulos. mensajes y verificaciones del usuario)
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		$parametros = $this->obtenerParametrosBusquedaAvanzada($requestParams);

		try {
			// Se verifican previamente los parámetros para ser utilizados en la Capa de Negocio
			// aquí se utiliza la clase Validator para validar mediante expresiones regulares
			$parametros = $this->setearValoresParaNG($parametros);

			$cantidadResultados = NG::reportes()->obtenerExpedientesAvanzadoCantidad(
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

			// Verifico la cantidad máxima de resultados
			if ($cantidadResultados > KRAKEN_REPORT_MAX_RESULT_COUNT)
				throw new Exception(sprintf('Error al generar reporte: No ha podido generarse el reporte porque posee %d resultados y supera el l&iacute;mite de %d permitidos. Por favor, ajuste los par&aacute;metros del mismo y vuelva a intentarlo.',
					$cantidadResultados, KRAKEN_REPORT_MAX_RESULT_COUNT));

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
				true, // instancias completas
				// Control de consulta
				array('anio', 'tipo', 'numero', 'cuerpo', 'alcance'));
				//array('anio desc', 'tipo', 'numero desc', 'cuerpo', 'alcance'));

			$paramVista['resultados'] = $expedientes;

			// Se obtienen las descripciones de los parámetros utilizados
			$parametros_codificados = $this->obtenerParametrosCodificadosCriterio($parametros);
			// Se le pasa a la Vista el texto correspondiente al criterio de búsqueda utilizado, con sus nombres respectivos
			$paramVista['criterio_busqueda'] = $this->obtenerTextoCriterioBusqueda($parametros_codificados);

			// Instancio la vista y la muestro
			$vista = new BEReportesView($paramVista);
			$vista->vistaReporteBusquedaAvanzada();
		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}

	/**
	 * Se genera el reporte en formato de documento de texto
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function busquedaavanzadadocumentotexto($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros iniciales de la vista (títulos. mensajes y verificaciones del usuario)
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		$parametros = $this->obtenerParametrosBusquedaAvanzada($requestParams);

		try {
			// Se verifican previamente los parámetros para ser utilizados en la Capa de Negocio
			// aquí se utiliza la clase Validator para validar mediante expresiones regulares
			$parametros = $this->setearValoresParaNG($parametros);

			$cantidadResultados = NG::reportes()->obtenerExpedientesAvanzadoCantidad(
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

			// Verifico la cantidad máxima de resultados
			if ($cantidadResultados > KRAKEN_REPORT_MAX_RESULT_COUNT)
				throw new Exception(sprintf('Error al generar reporte: No ha podido generarse el reporte porque posee %d resultados y supera el l&iacute;mite de %d permitidos. Por favor, ajuste los par&aacute;metros del mismo y vuelva a intentarlo.',
					$cantidadResultados, KRAKEN_REPORT_MAX_RESULT_COUNT));

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
				true, // instancias completas
				// Control de consulta
				array('anio', 'tipo', 'numero', 'cuerpo', 'alcance'));

			$paramVista['resultados'] = $expedientes;

			// Se obtienen las descripciones de los parámetros utilizados
			$parametros_codificados = $this->obtenerParametrosCodificadosCriterio($parametros);
			// Se le pasa a la Vista el texto correspondiente al criterio de búsqueda utilizado, con sus nombres respectivos
			$paramVista['criterio_busqueda'] = $this->obtenerTextoCriterioBusqueda($parametros_codificados);

			// Instancio la vista y la muestro
			$vista = new BEDocumentosTextoView($paramVista);
			$vista->vistaDocumentoTextoBusquedaAvanzada();
		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}

	/**
	 * Se genera la planilla de cálculo del resultado de la búsqueda avanzada
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function busquedaavanzadaplanillacalculo($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros iniciales de la vista (títulos. mensajes y verificaciones del usuario)
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		$parametros = $this->obtenerParametrosBusquedaAvanzada($requestParams);

		try {
			// Se verifican previamente los parámetros para ser utilizados en la Capa de Negocio
			// aquí se utiliza la clase Validator para validar mediante expresiones regulares
			$parametros = $this->setearValoresParaNG($parametros);

			$cantidadResultados = NG::reportes()->obtenerExpedientesAvanzadoCantidad(
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

			// Verifico la cantidad máxima de resultados
			if ($cantidadResultados > KRAKEN_REPORT_MAX_RESULT_COUNT)
				throw new Exception(sprintf('Error al generar reporte: No ha podido generarse el reporte porque posee %d resultados y supera el l&iacute;mite de %d permitidos. Por favor, ajuste los par&aacute;metros del mismo y vuelva a intentarlo.',
					$cantidadResultados, KRAKEN_REPORT_MAX_RESULT_COUNT));

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
				true, // instancias completas
				// Control de consulta
				array('anio', 'tipo', 'numero', 'cuerpo', 'alcance'));

			// Se le pasa a la Vista el listado de Expedientes
			$paramVista['resultados'] = $expedientes;
			// Se le pasa a la Vista las descripciones de los parámetros utilizados
			$paramVista['criterio_busqueda'] = $this->obtenerParametrosCodificadosCriterio($parametros);

			// Instancio la vista y la muestro
			$vista = new BEPlanillasCalculoView($paramVista);
			$vista->vistaPlanillaCalculoBusquedaAvanzada();
		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}

	/**
	 * [busquedaporantecedente description]
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function busquedaporantecedente($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Parametros de filtro
		$parametros['f_numero'] = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$parametros['f_anio']   = Validator::get()->obtenerDefault($requestParams['f_anio']);

		try
		{
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$parametros['f_numero'] = Validator::get()->validar($parametros['f_numero'], PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
			$parametros['f_anio']   = Validator::get()->validar($parametros['f_anio'], PATRON_NUMEROS, true, 'A&ntilde;o del expediente');

			$cantidadResultados = NG::reportes()->obtenerExpedientesPorAntecedenteCantidad(
				// Parametros
		        $parametros['f_anio'], // anio_a
				null, // tipo_a
				$parametros['f_numero'], // numero_a
				null, // digito_a
				null, // cuerpo_a
				null, // alcance_a
				null, // cuerpoalcance_a
				null, // anexoalcance_a
				null, // cuerpoanexoalcance_a
				null, // anexo_a
				null // cuerpoanexo_a
			);

			// Verifico la cantidad máxima de resultados
			if ($cantidadResultados > KRAKEN_REPORT_MAX_RESULT_COUNT)
				throw new Exception(sprintf('Error al generar reporte: No ha podido generarse el reporte porque posee %d resultados y supera el l&iacute;mite de %d permitidos. Por favor, ajuste los par&aacute;metros del mismo y vuelva a intentarlo.',
					$cantidadResultados, KRAKEN_REPORT_MAX_RESULT_COUNT));

			$expedientes = NG::reportes()->obtenerExpedientesPorAntecedente(
				$parametros['f_anio'], // anio_a
				null, // tipo_a
				$parametros['f_numero'], // numero_a
				null, // digito_a
				null, // cuerpo_a
				null, // alcance_a
				null, // cuerpoalcance_a
				null, // anexoalcance_a
				null, // cuerpoanexoalcance_a
				null, // anexo_a
				null, // cuerpoanexo_a
				true, // instancias completas
				// Control de consulta
				array('anio', 'tipo', 'numero', 'cuerpo', 'alcance'));
				//array('anio desc', 'tipo', 'numero desc', 'cuerpo', 'alcance'));

			$paramVista['resultados'] = $expedientes;

			// Se obtienen las descripciones de los parámetros utilizados
			$parametros_codificados = $this->obtenerParametrosCodificadosCriterio($parametros);
			// Se le pasa a la Vista el texto correspondiente al criterio de búsqueda utilizado, con sus nombres respectivos
			$paramVista['criterio_busqueda'] = $this->obtenerTextoCriterioBusqueda($parametros_codificados);

			// Instancio la vista y la muestro
			$vista = new BEReportesView($paramVista);
			$vista->vistaReporteBusquedaPorAntecedente();
		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}
}
?>
