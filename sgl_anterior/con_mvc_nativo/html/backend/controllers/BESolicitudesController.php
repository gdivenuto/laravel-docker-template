<?php
/**
 * Clase de controlador de la Solicitud perteneciente a un préstamo específico.
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_SOLICITUD');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_SOLICITUD');

class BESolicitudesController extends BaseController
{
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	// Cantidad de años por defecto con los que se setean los filtros de fecha Desde
	private $defaultOffsetAnios = -1;

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
		$this->accionesPermitidas['view']     = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['datagrid'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['editinfo'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['add']      = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['save']     = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['delete']   = NIVEL_ACCESO_OPERADOR;
		// Para los cambios de Estado de la Solicitud
		$this->accionesPermitidas['editsolicitadoee'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['editingresado']    = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['editdevueltoee']   = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['editanulado']      = NIVEL_ACCESO_OPERADOR;

		$this->accionesPermitidas['verificarprestamopendiente'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['generarpdfsolicitudes'] = NIVEL_ACCESO_OPERADOR;

		// Conjunto de etiquetas a mostrar en el criterio de búsqueda en los reportes
		$this->etiquetas = array();

		// Etiquetas automáticas para criterios de búsqueda
		$this->etiquetas['f_anio']    = '&nbsp;&nbsp;&nbsp;A&ntilde;o';
		$this->etiquetas['f_tipo']    = '&nbsp;&nbsp;&nbsp;Tipo';
		$this->etiquetas['f_numero']  = '&nbsp;&nbsp;&nbsp;N&uacute;mero';
		$this->etiquetas['f_cuerpo']  = '&nbsp;&nbsp;&nbsp;Cuerpo';
		$this->etiquetas['f_alcance'] = '&nbsp;&nbsp;&nbsp;Alcance';

		$this->etiquetas['f_digito']    		 = '&nbsp;&nbsp;&nbsp;D&iacute;gito';
		$this->etiquetas['f_cuerpoalcance']    	 = '&nbsp;&nbsp;&nbsp;Cuerpo Alcance';
		$this->etiquetas['f_anexoalcance']  	 = '&nbsp;&nbsp;&nbsp;Anexo Alcance';
		$this->etiquetas['f_cuerpoanexoalcance'] = '&nbsp;&nbsp;&nbsp;Cuerpo Anexo Alcance';
		$this->etiquetas['f_anexo'] 			 = '&nbsp;&nbsp;&nbsp;Anexo';
		$this->etiquetas['f_cuerpoanexo'] 		 = '&nbsp;&nbsp;&nbsp;Cuerpo Anexo';

		$this->etiquetas['f_fecha_solicitud_hcd_desde'] = '&nbsp;&nbsp;&nbsp;Desde';
		$this->etiquetas['f_fecha_solicitud_hcd_hasta'] = '&nbsp;&nbsp;&nbsp;Hasta';

		// Para los Estados de la Solicitud
		$this->etiquetas['f_estado_solicitado_hcd'] = '&nbsp;&nbsp;&nbsp;Estado Solicitado al HCD';
		$this->etiquetas['f_estado_solicitado_ee']  = '&nbsp;&nbsp;&nbsp;Estado Solicitado al E.E.';
		$this->etiquetas['f_estado_ingresado_ee']   = '&nbsp;&nbsp;&nbsp;Estado Ingresado al E.E.';
		$this->etiquetas['f_estado_devuelto_ee']    = '&nbsp;&nbsp;&nbsp;Estado Devuelto al E.E.';
		$this->etiquetas['f_estado_anulado']        = '&nbsp;&nbsp;&nbsp;Estado Anulado';
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
		$f_anio    = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo    = Validator::get()->obtenerDefault($requestParams['f_tipo'], '0');
		$f_numero  = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo  = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		$f_digito  = Validator::get()->obtenerDefault($requestParams['f_digito']);

		// Ejecuto la vista
		try {
			// Validación de parámetros de búsqueda
			$f_anio    = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
			if ( $f_tipo != '0' )
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
			$f_numero  = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
			$f_cuerpo  = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance de expediente');

			$f_digito 			  = Validator::get()->validar($f_digito, PATRON_ALFANUM, true, 'D&iacute;gito del pr&eacute;stamo');
			$f_cuerpoalcance 	  = Validator::get()->validar($f_cuerpoalcance, PATRON_NUMEROS, true, 'Cuerpo Alcance del pr&eacute;stamo');
			$f_anexoalcance 	  = Validator::get()->validar($f_anexoalcance, PATRON_NUMEROS, true, 'Anexo Alcance del pr&eacute;stamo');
			$f_cuerpoanexoalcance = Validator::get()->validar($f_cuerpoanexoalcance, PATRON_NUMEROS, true, 'Cuerpo Anexo Alcance del pr&eacute;stamo');
			$f_anexo 			  = Validator::get()->validar($f_anexo, PATRON_NUMEROS, true, 'Anexo del pr&eacute;stamo');
			$f_cuerpoanexo 		  = Validator::get()->validar($f_cuerpoanexo, PATRON_NUMEROS, true, 'Cuerpo Anexo del pr&eacute;stamo');

			$paramVista['f_anio']    = $f_anio;
			$paramVista['f_tipo']    = $f_tipo;
			$paramVista['f_numero']  = $f_numero;
			$paramVista['f_cuerpo']  = $f_cuerpo;
			$paramVista['f_alcance'] = $f_alcance;

			$paramVista['f_digito']    			= $f_digito;
			$paramVista['f_cuerpoalcance']    	= $f_cuerpoalcance;
			$paramVista['f_anexoalcance']  		= $f_anexoalcance;
			$paramVista['f_cuerpoanexoalcance'] = $f_cuerpoanexoalcance;
			$paramVista['f_anexo'] 				= $f_anexo;
			$paramVista['f_cuerpoanexo'] 		= $f_cuerpoanexo;

			$paramVista['f_fecha_desde'] = $f_fecha_desde;
			$paramVista['f_fecha_hasta'] = $f_fecha_hasta;

		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}

		// Instancio la vista y la muestro
		$vista = new BESolicitudesView($paramVista);
		$vista->vistaListado();
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
			// *** Parametros de filtro, por defecto null
			//
			// Para buscar por la clave del préstamo
			$f_anio    = Validator::get()->obtenerDefault($requestParams['f_anio']);
			$f_tipo    = Validator::get()->obtenerDefault($requestParams['f_tipo']);
			$f_numero  = Validator::get()->obtenerDefault($requestParams['f_numero']);
			$f_cuerpo  = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
			$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
			// Resto de la clave
			$f_digito    		  = Validator::get()->obtenerDefault($requestParams['f_digito']);
			$f_cuerpoalcance      = Validator::get()->obtenerDefault($requestParams['f_cuerpoalcance']);
			$f_anexoalcance  	  = Validator::get()->obtenerDefault($requestParams['f_anexoalcance']);
			$f_cuerpoanexoalcance = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexoalcance']);
			$f_anexo 			  = Validator::get()->obtenerDefault($requestParams['f_anexo']);
			$f_cuerpoanexo 		  = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexo']);

			// la fecha Desde, por defecto es 1 año menor que la fecha actual
			$f_fecha_solicitud_hcd_desde = (isset($requestParams['f_fecha_desde']) && $requestParams['f_fecha_desde'] != '') ? $requestParams['f_fecha_desde'] : null;//$this->getFechaOffsetAnios($this->defaultOffsetAnios)
			// la fecha Hasta, por defecto es la fecha actual
			$f_fecha_solicitud_hcd_hasta = (isset($requestParams['f_fecha_hasta']) && $requestParams['f_fecha_desde'] != '') ? $requestParams['f_fecha_hasta'] : null;//date("Y-m-d")

			// Para filtrar por los Estados elegidos
			$f_estado_solicitado_hcd = Validator::get()->obtenerDefault($requestParams['f_estado_solicitado_hcd']);
			$f_estado_solicitado_ee  = Validator::get()->obtenerDefault($requestParams['f_estado_solicitado_ee']);
			$f_estado_ingresado_ee   = Validator::get()->obtenerDefault($requestParams['f_estado_ingresado_ee']);
			$f_estado_devuelto_ee    = Validator::get()->obtenerDefault($requestParams['f_estado_devuelto_ee']);
			$f_estado_anulado        = Validator::get()->obtenerDefault($requestParams['f_estado_anulado']);

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
				$p_limitStart  = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
				$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

				// Verifico aquellos parámetros que puedan ser inyectables con SQL
				$f_anio    = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del pr&eacute;stamo');
				$f_tipo    = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo del pr&eacute;stamo');
				$f_numero  = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero del pr&eacute;stamo');
				$f_cuerpo  = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo del pr&eacute;stamo');
				$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance del pr&eacute;stamo');

				$f_digito 			  = Validator::get()->validar($f_digito, PATRON_ALFANUM, true, 'D&iacute;gito del pr&eacute;stamo');
				$f_cuerpoalcance 	  = Validator::get()->validar($f_cuerpoalcance, PATRON_NUMEROS, true, 'Cuerpo Alcance del pr&eacute;stamo');
				$f_anexoalcance 	  = Validator::get()->validar($f_anexoalcance, PATRON_NUMEROS, true, 'Anexo Alcance del pr&eacute;stamo');
				$f_cuerpoanexoalcance = Validator::get()->validar($f_cuerpoanexoalcance, PATRON_NUMEROS, true, 'Cuerpo Anexo Alcance del pr&eacute;stamo');
				$f_anexo 			  = Validator::get()->validar($f_anexo, PATRON_NUMEROS, true, 'Anexo del pr&eacute;stamo');
				$f_cuerpoanexo 		  = Validator::get()->validar($f_cuerpoanexo, PATRON_NUMEROS, true, 'Cuerpo Anexo del pr&eacute;stamo');

				// NO IMPLEMENTADOS LOS PERFILES AÚN EN LA VERSIÓN 2
				// para perfiles 1 y 2 se muestran todos los Préstamos (NO se filtra por ningún estado)
				// para perfiles 3 y 4 sólo los Prestados, Devueltos y/o Anulados (PARA QUE LOS BLOQUES NO SEPAN QUIÉN LOS PIDIÓ ANTES DE PRESTARSE)
				//$estados_a_obtener = ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) ? null : array(Prestamo::E_PRESTADO, Prestamo::E_DEVUELTO, Prestamo::E_ANULADO);
				//$estados_a_obtener = null;
				$estados_a_obtener = Array();
				// Para estado Solicitado al HCD
				if ( !is_null($f_estado_solicitado_hcd) && $f_estado_solicitado_hcd != '')
					$estados_a_obtener[] = $f_estado_solicitado_hcd;

				// Para estado Solicitado al Ente Externo
				if ( !is_null($f_estado_solicitado_ee) && $f_estado_solicitado_ee != '')
					$estados_a_obtener[] = $f_estado_solicitado_ee;

				// Para estado Ingresado al Ente Externo
				if ( !is_null($f_estado_ingresado_ee) && $f_estado_ingresado_ee != '')
					$estados_a_obtener[] = $f_estado_ingresado_ee;

				// Para estado Devuelto al Ente Externo
				if ( !is_null($f_estado_devuelto_ee) && $f_estado_devuelto_ee != '')
					$estados_a_obtener[] = $f_estado_devuelto_ee;

				// Para estado Anulado
				if ( !is_null($f_estado_anulado) && $f_estado_anulado != '')
					$estados_a_obtener[] = $f_estado_anulado;

				$solicitudes = null;
				$cantidadTotalSolicitudes = 0;

				$solicitudes = NG::prestamos()->obtenerSolicitudesExpedientesExternos(
					$f_anio, // anio
					$f_tipo, // tipo
					$f_numero, // numero
					$f_cuerpo, // cuerpo
					$f_alcance, // alcance
					$f_digito, // digito
					$f_cuerpoalcance, // cuerpoalcance
					$f_anexoalcance, // anexoalcance
					$f_cuerpoanexoalcance, // cuerpoanexoalcance
					$f_anexo, // anexo
					$f_cuerpoanexo, // cuerpoanexo
					$f_fecha_solicitud_hcd_desde, // Fecha desde
					$f_fecha_solicitud_hcd_hasta, // Fecha hasta
					$estados_a_obtener, // Estados
					// Control de consulta
					true, // Instancias completas, para obtener posibles estados siguientes
					array('anio desc', 'tipo', 'numero desc', 'cuerpo', 'alcance', 'digito', 'cuerpoalcance', 'anexoalcance', 'cuerpoanexoalcance', 'anexo', 'cuerpoanexo', 'fecha_solicitud_hcd'),
					$p_limitLength, // cantidad de registros (paginación)
					$p_limitStart // corrimiento de registros (paginación)
				);

				// Consulta de cantidad de Prestamos del expediente respectivo (total)
				$cantidadTotalSolicitudes = NG::prestamos()->obtenerSolicitudesExpedientesExternosCantidad(
					$f_anio, // anio
					$f_tipo, // tipo
					$f_numero, // numero
					$f_cuerpo, // cuerpo
					$f_alcance, // alcance
					$f_digito, // digito
					$f_cuerpoalcance, // cuerpoalcance
					$f_anexoalcance, // anexoalcance
					$f_cuerpoanexoalcance, // cuerpoanexoalcance
					$f_anexo, // anexo
					$f_cuerpoanexo, // cuerpoanexo
					$f_fecha_solicitud_hcd_desde, // Fecha desde
					$f_fecha_solicitud_hcd_hasta, // Fecha hasta
					$estados_a_obtener // Estados
				);

				// Por cada solicitud
				foreach ($solicitudes as $s)
					// Verificamos si existe por lo menos un Préstamo pendiente (Solicitado y NO Prestado aún)
        			$s->ro_existe_prestamo_pendiente = NG::prestamos()->existePrestamoPendienteParaSolicitud($s);

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = $cantidadTotalSolicitudes;
				$resultado['recordsFiltered'] = $cantidadTotalSolicitudes;
				$resultado['data'] = $solicitudes;
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
	 * Este método nos envía al formulario para editar sólo las Observaciones de la Solicitud
	 */
	public function editinfo($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Se recibe la clave del expediente
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		$f_digito = Validator::get()->obtenerDefault($requestParams['f_digito']);
		$f_cuerpoalcance = Validator::get()->obtenerDefault($requestParams['f_cuerpoalcance']);
		$f_anexoalcance = Validator::get()->obtenerDefault($requestParams['f_anexoalcance']);
		$f_cuerpoanexoalcance = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexoalcance']);
		$f_anexo = Validator::get()->obtenerDefault($requestParams['f_anexo']);
		$f_cuerpoanexo = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexo']);
		$f_fecha_solicitud_hcd = Validator::get()->obtenerDefault($requestParams['f_fecha_solicitud_hcd']);

		try {
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_digito = Validator::get()->validar($f_digito, PATRON_ALFANUM, false, 'D&iacute;gito de expediente');
			$f_cuerpoalcance = Validator::get()->validar($f_cuerpoalcance, PATRON_NUMEROS, false, 'Cuerpo Alcance del expediente');
			$f_anexoalcance = Validator::get()->validar($f_anexoalcance, PATRON_NUMEROS, false, 'Anexo Alcance del expediente');
			$f_cuerpoanexoalcance = Validator::get()->validar($f_cuerpoanexoalcance, PATRON_NUMEROS, false, 'Cuerpo Anexo Alcance del expediente');
			$f_anexo = Validator::get()->validar($f_anexo, PATRON_NUMEROS, false, 'Anexo del expediente');
			$f_cuerpoanexo = Validator::get()->validar($f_cuerpoanexo, PATRON_NUMEROS, false, 'Cuerpo Anexo del expediente');

			$solicitud = NG::prestamos()->obtenerSolicitudExpedienteExterno(
				$f_anio, // anio
				$f_tipo, // tipo
				$f_numero, // numero
				$f_cuerpo, // cuerpo
				$f_alcance, // alcance
				$f_digito,
				$f_cuerpoalcance,
				$f_anexoalcance,
				$f_cuerpoanexoalcance,
				$f_anexo,
				$f_cuerpoanexo,
				$f_fecha_solicitud_hcd,
				true); // Instancias completas

			if (is_null($solicitud))
				throw new Exception('Error: La solicitud a editar no existe.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $solicitud->generarChecksum());

			// Para determinar si estoy agregando o modificando un solicitud, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			$paramVista['solicitud'] = $solicitud;

			// Instancio la vista y la muestro
			$vista = new BESolicitudesView($paramVista);
			$vista->vistaEdicionInfo();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BESolicitudesView($paramVista);
			$vista->vistaEdicionInfo();
		}
	}

	/**
	 * Este método nos envía al formulario de edición de cambio de estado a Solicitado al E.E.
	 */
	public function editsolicitadoee($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Se recibe la clave del expediente
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		$f_digito = Validator::get()->obtenerDefault($requestParams['f_digito']);
		$f_cuerpoalcance = Validator::get()->obtenerDefault($requestParams['f_cuerpoalcance']);
		$f_anexoalcance = Validator::get()->obtenerDefault($requestParams['f_anexoalcance']);
		$f_cuerpoanexoalcance = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexoalcance']);
		$f_anexo = Validator::get()->obtenerDefault($requestParams['f_anexo']);
		$f_cuerpoanexo = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexo']);
		$f_fecha_solicitud_hcd = Validator::get()->obtenerDefault($requestParams['f_fecha_solicitud_hcd']);

		try {
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_digito = Validator::get()->validar($f_digito, PATRON_ALFANUM, false, 'D&iacute;gito de expediente');
			$f_cuerpoalcance = Validator::get()->validar($f_cuerpoalcance, PATRON_NUMEROS, false, 'Cuerpo Alcance del expediente');
			$f_anexoalcance = Validator::get()->validar($f_anexoalcance, PATRON_NUMEROS, false, 'Anexo Alcance del expediente');
			$f_cuerpoanexoalcance = Validator::get()->validar($f_cuerpoanexoalcance, PATRON_NUMEROS, false, 'Cuerpo Anexo Alcance del expediente');
			$f_anexo = Validator::get()->validar($f_anexo, PATRON_NUMEROS, false, 'Anexo del expediente');
			$f_cuerpoanexo = Validator::get()->validar($f_cuerpoanexo, PATRON_NUMEROS, false, 'Cuerpo Anexo del expediente');

			$solicitud = NG::prestamos()->obtenerSolicitudExpedienteExterno(
				$f_anio, // anio
				$f_tipo, // tipo
				$f_numero, // numero
				$f_cuerpo, // cuerpo
				$f_alcance, // alcance
				$f_digito,
				$f_cuerpoalcance,
				$f_anexoalcance,
				$f_cuerpoanexoalcance,
				$f_anexo,
				$f_cuerpoanexo,
				$f_fecha_solicitud_hcd,
				true); // Instancias completas

			if (is_null($solicitud))
				throw new Exception('Error: La Solicitud a editar no existe.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $solicitud->generarChecksum());

			// Para determinar si estoy agregando o modificando un solicitud, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			$paramVista['solicitud'] = $solicitud;

			// Instancio la vista y la muestro
			$vista = new BESolicitudesView($paramVista);
			$vista->vistaEdicionEstadoSolicitadoEE();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BESolicitudesView($paramVista);
			$vista->vistaEdicionEstadoSolicitadoEE();
		}
	}

	/**
	 * Este método nos envía al formulario de edición de cambio de estado a Ingresado del E.E.
	 */
	public function editingresado($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Se recibe la clave del expediente
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		$f_digito = Validator::get()->obtenerDefault($requestParams['f_digito']);
		$f_cuerpoalcance = Validator::get()->obtenerDefault($requestParams['f_cuerpoalcance']);
		$f_anexoalcance = Validator::get()->obtenerDefault($requestParams['f_anexoalcance']);
		$f_cuerpoanexoalcance = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexoalcance']);
		$f_anexo = Validator::get()->obtenerDefault($requestParams['f_anexo']);
		$f_cuerpoanexo = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexo']);
		$f_fecha_solicitud_hcd = Validator::get()->obtenerDefault($requestParams['f_fecha_solicitud_hcd']);

		try {
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_digito = Validator::get()->validar($f_digito, PATRON_ALFANUM, false, 'D&iacute;gito de expediente');
			$f_cuerpoalcance = Validator::get()->validar($f_cuerpoalcance, PATRON_NUMEROS, false, 'Cuerpo Alcance del expediente');
			$f_anexoalcance = Validator::get()->validar($f_anexoalcance, PATRON_NUMEROS, false, 'Anexo Alcance del expediente');
			$f_cuerpoanexoalcance = Validator::get()->validar($f_cuerpoanexoalcance, PATRON_NUMEROS, false, 'Cuerpo Anexo Alcance del expediente');
			$f_anexo = Validator::get()->validar($f_anexo, PATRON_NUMEROS, false, 'Anexo del expediente');
			$f_cuerpoanexo = Validator::get()->validar($f_cuerpoanexo, PATRON_NUMEROS, false, 'Cuerpo Anexo del expediente');

			$solicitud = NG::prestamos()->obtenerSolicitudExpedienteExterno(
				$f_anio, // anio
				$f_tipo, // tipo
				$f_numero, // numero
				$f_cuerpo, // cuerpo
				$f_alcance, // alcance
				$f_digito,
				$f_cuerpoalcance,
				$f_anexoalcance,
				$f_cuerpoanexoalcance,
				$f_anexo,
				$f_cuerpoanexo,
				$f_fecha_solicitud_hcd,
				true); // Instancias completas

			if (is_null($solicitud))
				throw new Exception('Error: La solicitud a editar no existe.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $solicitud->generarChecksum());

			// Para determinar si estoy agregando o modificando una solicitud, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			$paramVista['solicitud'] = $solicitud;

			// Instancio la vista y la muestro
			$vista = new BESolicitudesView($paramVista);
			$vista->vistaEdicionEstadoIngresadoEE();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BESolicitudesView($paramVista);
			$vista->vistaEdicionEstadoIngresadoEE();
		}
	}

	/**
	 * Este método nos envía al formulario de edición de cambio de estado a Devuelto al E.E.
	 */
	public function editdevueltoee($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Se recibe la clave del expediente
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		$f_digito = Validator::get()->obtenerDefault($requestParams['f_digito']);
		$f_cuerpoalcance = Validator::get()->obtenerDefault($requestParams['f_cuerpoalcance']);
		$f_anexoalcance = Validator::get()->obtenerDefault($requestParams['f_anexoalcance']);
		$f_cuerpoanexoalcance = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexoalcance']);
		$f_anexo = Validator::get()->obtenerDefault($requestParams['f_anexo']);
		$f_cuerpoanexo = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexo']);
		$f_fecha_solicitud_hcd = Validator::get()->obtenerDefault($requestParams['f_fecha_solicitud_hcd']);

		try {
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_digito = Validator::get()->validar($f_digito, PATRON_ALFANUM, false, 'D&iacute;gito de expediente');
			$f_cuerpoalcance = Validator::get()->validar($f_cuerpoalcance, PATRON_NUMEROS, false, 'Cuerpo Alcance del expediente');
			$f_anexoalcance = Validator::get()->validar($f_anexoalcance, PATRON_NUMEROS, false, 'Anexo Alcance del expediente');
			$f_cuerpoanexoalcance = Validator::get()->validar($f_cuerpoanexoalcance, PATRON_NUMEROS, false, 'Cuerpo Anexo Alcance del expediente');
			$f_anexo = Validator::get()->validar($f_anexo, PATRON_NUMEROS, false, 'Anexo del expediente');
			$f_cuerpoanexo = Validator::get()->validar($f_cuerpoanexo, PATRON_NUMEROS, false, 'Cuerpo Anexo del expediente');

			$solicitud = NG::prestamos()->obtenerSolicitudExpedienteExterno(
				$f_anio, // anio
				$f_tipo, // tipo
				$f_numero, // numero
				$f_cuerpo, // cuerpo
				$f_alcance, // alcance
				$f_digito,
				$f_cuerpoalcance,
				$f_anexoalcance,
				$f_cuerpoanexoalcance,
				$f_anexo,
				$f_cuerpoanexo,
				$f_fecha_solicitud_hcd
			);

			if (is_null($solicitud))
				throw new Exception('Error: La solicitud a editar no existe.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $solicitud->generarChecksum());

			// Para determinar si estoy agregando o modificando una solicitud, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Para generar una nueva Solicitud a partir de la Solicitud devuelta
			$paramVista['f_generar_nueva_solicitud'] = $requestParams['f_generar_nueva_solicitud'];

			$paramVista['solicitud'] = $solicitud;

			// Instancio la vista y la muestro
			$vista = new BESolicitudesView($paramVista);
			$vista->vistaEdicionEstadoDevueltoEE();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BESolicitudesView($paramVista);
			$vista->vistaEdicionEstadoDevueltoEE();
		}
	}

	/**
	 * Este método nos envía al formulario de edición de cambio de estado a Prestado
	 */
	public function editanulado($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Se recibe la clave del expediente
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		$f_digito = Validator::get()->obtenerDefault($requestParams['f_digito']);
		$f_cuerpoalcance = Validator::get()->obtenerDefault($requestParams['f_cuerpoalcance']);
		$f_anexoalcance = Validator::get()->obtenerDefault($requestParams['f_anexoalcance']);
		$f_cuerpoanexoalcance = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexoalcance']);
		$f_anexo = Validator::get()->obtenerDefault($requestParams['f_anexo']);
		$f_cuerpoanexo = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexo']);
		$f_fecha_solicitud_hcd = Validator::get()->obtenerDefault($requestParams['f_fecha_solicitud_hcd']);

		try {
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_digito = Validator::get()->validar($f_digito, PATRON_ALFANUM, false, 'D&iacute;gito de expediente');
			$f_cuerpoalcance = Validator::get()->validar($f_cuerpoalcance, PATRON_NUMEROS, false, 'Cuerpo Alcance del expediente');
			$f_anexoalcance = Validator::get()->validar($f_anexoalcance, PATRON_NUMEROS, false, 'Anexo Alcance del expediente');
			$f_cuerpoanexoalcance = Validator::get()->validar($f_cuerpoanexoalcance, PATRON_NUMEROS, false, 'Cuerpo Anexo Alcance del expediente');
			$f_anexo = Validator::get()->validar($f_anexo, PATRON_NUMEROS, false, 'Anexo del expediente');
			$f_cuerpoanexo = Validator::get()->validar($f_cuerpoanexo, PATRON_NUMEROS, false, 'Cuerpo Anexo del expediente');

			$solicitud = NG::prestamos()->obtenerSolicitudExpedienteExterno(
				$f_anio, // anio
				$f_tipo, // tipo
				$f_numero, // numero
				$f_cuerpo, // cuerpo
				$f_alcance, // alcance
				$f_digito,
				$f_cuerpoalcance,
				$f_anexoalcance,
				$f_cuerpoanexoalcance,
				$f_anexo,
				$f_cuerpoanexo,
				$f_fecha_solicitud_hcd,
				true); // Instancias completas

			if (is_null($solicitud))
				throw new Exception('Error: La solicitud a editar no existe.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $solicitud->generarChecksum());

			// Para determinar si estoy agregando o modificando un solicitud, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			$paramVista['solicitud'] = $solicitud;

			// Instancio la vista y la muestro
			$vista = new BESolicitudesView($paramVista);
			$vista->vistaEdicionEstadoAnulado();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BESolicitudesView($paramVista);
			$vista->vistaEdicionEstadoAnulado();
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
				// Utilizado para modificar la información
				// NO para cambiar su estado
				$f_editarsoloinfo = $requestParams['f_editarsoloinfo'] == '1';

				// Utilizada para generar una nueva solicitud luego de devolver la respectiva
				$f_generar_nueva_solicitud = $requestParams['f_generar_nueva_solicitud'] == '1';

				// Si se realiza un cambio de Estado de la Solicitud
				if ( ! $f_editarsoloinfo ) {
					// Verifico que el estado sea válido
					$f_nuevo_estado = Validator::get()->validar($requestParams['f_nuevo_estado'], PATRON_ESTADO_SOLICITUD_EXPEDIENTE_EXTERNO, false, 'Pr&oacute;ximo estado');
					$f_nueva_fecha_hora = Validator::get()->validar($requestParams['f_nueva_fecha_hora'], PATRON_FECHA_HORA, false, "Fecha y hora del cambio de estado");
				}

				// Verifico acción de guardado
				if (!SessionController::get()->existe(SAVE_ACTION))
					throw new Exception('No se puede determinar la acci&oacute;n de guardado.');

				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				$jsonData = file_get_contents('php://input');
				$solicitud = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($solicitud) == 'SolicitudExpedienteExterno'))
					throw new Exception('Se esperaba un objeto de tipo SolicitudExpedienteExterno.');

				// ***********************************************************
				//	Validación de atributos (según próximo estado)
				// ***********************************************************
				// Actualizo datos generales y de usuario (quien realizó la modificación)
				$solicitud->observaciones = Validator::get()->sanear($solicitud->observaciones);
				$usuario = $this->obtenerUsuarioActual();
				$solicitud->id_usuario = $usuario->id_usuario;

				if ( ! $f_editarsoloinfo )
					// Cambio de estado (lanza excepcion si falla)
					$solicitud = NG::prestamos()->cambiarEstadoExpedienteExterno($solicitud, $f_nuevo_estado, $f_nueva_fecha_hora);

				// Guardo la solicitud
				$solicitud = NG::prestamos()->guardarSolicitudExpedienteExterno($solicitud, true); // guardo y recargo

				// Si se desea generar una nueva solicitud luego de devolver una anterior
				$mensaje_aux = '';
				if ($f_generar_nueva_solicitud) {
					// Se genera una nueva Solicitud a partir de la Solicitud devuelta
					$solicitud_nueva = NG::prestamos()->generarNuevaSolicitudExpedienteExterno($solicitud);
					// Guardo la solicitud
					$solicitud_nueva_guardada = NG::prestamos()->guardarSolicitudExpedienteExterno($solicitud_nueva, true); // guardo y recargo

					$mensaje_aux = 'Se ha devuelto la solicitud y se ha generado una nueva para los pr&eacute;stamos pendientes.';
				}

				// Genero respuesta
				$resultado['estado']  = 'OK';
				$resultado['mensaje'] = ($mensaje_aux != '') ? $mensaje_aux : 'Solicitud guardada con &eacute;xito.';
				$resultado['data']    = $solicitud;

			} catch (Exception $e) {
				$resultado['estado']  = 'ATENCI&Oacute;N';
				$resultado['mensaje'] = 'No se pudo guardar la Solicitud. Causa: '.$e->getMessage();
				$resultado['data']    = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina el Prestamo determinado por su clave
	 * @param [type] $requestParams [description]
	 */
	public function delete($requestParams)
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
				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				$jsonData = file_get_contents('php://input');
				$solicitud = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($solicitud) == 'SolicitudExpedienteExterno'))
					throw new Exception('Se esperaba un objeto de tipo SolicitudExpedienteExterno.');

				// Antes de eliminar, recargo el Préstamo para obtenerlo completo
				$solicitud = NG::prestamos()->obtenerSolicitudExpedienteExterno(
					$solicitud->anio,
					$solicitud->tipo,
					$solicitud->numero,
					$solicitud->cuerpo,
					$solicitud->alcance,
					$solicitud->digito,
					$solicitud->cuerpoalcance,
					$solicitud->anexoalcance,
					$solicitud->cuerpoanexoalcance,
					$solicitud->anexo,
					$solicitud->cuerpoanexo,
					$solicitud->fecha_solicitud_hcd,
					true
				);

				$solicitud = NG::prestamos()->eliminarSolicitudExpedienteExterno($solicitud);

				// Genero respuesta
				$resultado['estado']  = 'OK';
				$resultado['mensaje'] = 'Solicitud eliminada con &eacute;xito.';
				$resultado['data']    = $solicitud;

			} catch (Exception $e) {
				$resultado['estado']  = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar la Solicitud. Causa: '.$e->getMessage();
				$resultado['data']    = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se obtienen las etiquetas de cada criterio utilizado en el listado
	 * @param  array $parametros Conjunto de parámetros con el criterio de búsqueda
	 * @return array $parametros El mismo conjunto con los nombres agregados
	 */
	public function prepararParametrosParaReporte($parametros)
	{
		if ( isset($parametros['f_fecha_solicitud_hcd_desde']) && !is_null($parametros['f_fecha_solicitud_hcd_desde']) )
			$parametros['f_fecha_solicitud_hcd_desde'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_solicitud_hcd_desde']);

		if ( isset($parametros['f_fecha_solicitud_hcd_hasta']) && !is_null($parametros['f_fecha_solicitud_hcd_hasta']) )
			$parametros['f_fecha_solicitud_hcd_hasta'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_solicitud_hcd_hasta']);

		return $parametros;
	}

	/**
	 * Se genera el listado de Solicitudes en formato PDF
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function generarpdfsolicitudes($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros iniciales de la vista (títulos. mensajes y verificaciones del usuario)
		$paramVista = $this->generarParametrosVista(); // (HEREDADO)

		// Saneo parametros (HEREDADO)
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Filtrado por la clave del préstamo
		$f_anio    = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo    = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero  = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo  = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		// Resto de la clave
		$f_digito    		  = Validator::get()->obtenerDefault($requestParams['f_digito']);
		$f_cuerpoalcance      = Validator::get()->obtenerDefault($requestParams['f_cuerpoalcance']);
		$f_anexoalcance  	  = Validator::get()->obtenerDefault($requestParams['f_anexoalcance']);
		$f_cuerpoanexoalcance = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexoalcance']);
		$f_anexo 			  = Validator::get()->obtenerDefault($requestParams['f_anexo']);
		$f_cuerpoanexo 		  = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexo']);

		// la fecha Desde, por defecto es 1 año menor que la fecha actual
		$f_fecha_solicitud_hcd_desde = (isset($requestParams['f_fecha_solicitud_hcd_desde']) && $requestParams['f_fecha_solicitud_hcd_desde'] != '') ? $requestParams['f_fecha_solicitud_hcd_desde'] : null;
		// la fecha Hasta, por defecto es la fecha actual
		$f_fecha_solicitud_hcd_hasta = (isset($requestParams['f_fecha_solicitud_hcd_hasta']) && $requestParams['f_fecha_solicitud_hcd_hasta'] != '') ? $requestParams['f_fecha_solicitud_hcd_hasta'] : null;

		// Para filtrar por los Estados elegidos
		$f_estado_solicitado_hcd = Validator::get()->obtenerDefault($requestParams['f_estado_solicitado_hcd']);
		$f_estado_solicitado_ee  = Validator::get()->obtenerDefault($requestParams['f_estado_solicitado_ee']);
		$f_estado_ingresado_ee   = Validator::get()->obtenerDefault($requestParams['f_estado_ingresado_ee']);
		$f_estado_devuelto_ee    = Validator::get()->obtenerDefault($requestParams['f_estado_devuelto_ee']);
		$f_estado_anulado        = Validator::get()->obtenerDefault($requestParams['f_estado_anulado']);

		try {
			// Verifico aquellos parámetros que puedan ser inyectables con SQL
			$f_anio    = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del pr&eacute;stamo');
			$f_tipo    = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo del pr&eacute;stamo');
			$f_numero  = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero del pr&eacute;stamo');
			$f_cuerpo  = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo del pr&eacute;stamo');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance del pr&eacute;stamo');

			$f_digito  		      = Validator::get()->validar($f_digito, PATRON_ALFANUM, true, 'D&iacute;gito del pr&eacute;stamo');
			$f_cuerpoalcance 	  = Validator::get()->validar($f_cuerpoalcance, PATRON_NUMEROS, true, 'Cuerpo Alcance del pr&eacute;stamo');
			$f_anexoalcance 	  = Validator::get()->validar($f_anexoalcance, PATRON_NUMEROS, true, 'Anexo Alcance del pr&eacute;stamo');
			$f_cuerpoanexoalcance = Validator::get()->validar($f_cuerpoanexoalcance, PATRON_NUMEROS, true, 'Cuerpo Anexo Alcance del pr&eacute;stamo');
			$f_anexo 			  = Validator::get()->validar($f_anexo, PATRON_NUMEROS, true, 'Anexo del pr&eacute;stamo');
			$f_cuerpoanexo 		  = Validator::get()->validar($f_cuerpoanexo, PATRON_NUMEROS, true, 'Cuerpo Anexo del pr&eacute;stamo');

			$estados_a_obtener = Array();
			// Para estado Solicitado al HCD
			if ( !is_null($f_estado_solicitado_hcd) && $f_estado_solicitado_hcd != '')
				$estados_a_obtener[] = $f_estado_solicitado_hcd;

			// Para estado Solicitado al Ente Externo
			if ( !is_null($f_estado_solicitado_ee) && $f_estado_solicitado_ee != '')
				$estados_a_obtener[] = $f_estado_solicitado_ee;

			// Para estado Ingresado al Ente Externo
			if ( !is_null($f_estado_ingresado_ee) && $f_estado_ingresado_ee != '')
				$estados_a_obtener[] = $f_estado_ingresado_ee;

			// Para estado Devuelto al Ente Externo
			if ( !is_null($f_estado_devuelto_ee) && $f_estado_devuelto_ee != '')
				$estados_a_obtener[] = $f_estado_devuelto_ee;

			// Para estado Anulado
			if ( !is_null($f_estado_anulado) && $f_estado_anulado != '')
				$estados_a_obtener[] = $f_estado_anulado;

			// Consulta de cantidad de Prestamos del expediente respectivo (total)
			$cantidadTotalSolicitudes = NG::prestamos()->obtenerSolicitudesExpedientesExternosCantidad(
				$f_anio, // anio
				$f_tipo, // tipo
				$f_numero, // numero
				$f_cuerpo, // cuerpo
				$f_alcance, // alcance
				$f_digito, // digito
				$f_cuerpoalcance, // cuerpoalcance
				$f_anexoalcance, // anexoalcance
				$f_cuerpoanexoalcance, // cuerpoanexoalcance
				$f_anexo, // anexo
				$f_cuerpoanexo, // cuerpoanexo
				$f_fecha_solicitud_hcd_desde, // Fecha desde
				$f_fecha_solicitud_hcd_hasta, // Fecha hasta
				$estados_a_obtener // Estados
			);

			// Verifico la cantidad máxima de resultados
			if ($cantidadTotalSolicitudes > KRAKEN_REPORT_MAX_RESULT_COUNT)
				throw new Exception(sprintf('Error al generar reporte: No ha podido generarse el reporte porque posee %d resultados y supera el l&iacute;mite de %d permitidos. Por favor, ajuste los par&aacute;metros del mismo y vuelva a intentarlo.',
					$cantidadTotalSolicitudes, KRAKEN_REPORT_MAX_RESULT_COUNT));

			$solicitudes = NG::prestamos()->obtenerSolicitudesExpedientesExternos(
				$f_anio, // anio
				$f_tipo, // tipo
				$f_numero, // numero
				$f_cuerpo, // cuerpo
				$f_alcance, // alcance
				$f_digito, // digito
				$f_cuerpoalcance, // cuerpoalcance
				$f_anexoalcance, // anexoalcance
				$f_cuerpoanexoalcance, // cuerpoanexoalcance
				$f_anexo, // anexo
				$f_cuerpoanexo, // cuerpoanexo
				$f_fecha_solicitud_hcd_desde, // Fecha desde
				$f_fecha_solicitud_hcd_hasta, // Fecha hasta
				$estados_a_obtener, // Estados
				// Control de consulta
				true, // Instancias completas, para obtener posibles estados siguientes
				array('anio desc', 'tipo', 'numero desc', 'cuerpo', 'alcance', 'digito', 'cuerpoalcance', 'anexoalcance', 'cuerpoanexoalcance', 'anexo', 'cuerpoanexo', 'fecha_solicitud_hcd')
			);

			if ($f_anio != '') $parametros['f_anio'] = $f_anio;
			if ($f_tipo != '') $parametros['f_tipo'] = $f_tipo;
			if ($f_numero != '') $parametros['f_numero'] = $f_numero;
			if ($f_cuerpo != '') $parametros['f_cuerpo'] = $f_cuerpo;
			if ($f_alcance != '') $parametros['f_alcance'] = $f_alcance;
			if ($f_digito != '') $parametros['f_digito'] = $f_digito;
			if ($f_cuerpoalcance != '') $parametros['f_cuerpoalcance'] = $f_cuerpoalcance;
			if ($f_anexoalcance != '') $parametros['f_anexoalcance'] = $f_anexoalcance;
			if ($f_cuerpoanexoalcance != '') $parametros['f_cuerpoanexoalcance'] = $f_cuerpoanexoalcance;
			if ($f_anexo != '') $parametros['f_anexo'] = $f_anexo;
			if ($f_cuerpoanexo != '') $parametros['f_cuerpoanexo'] = $f_cuerpoanexo;
			if ($f_fecha_solicitud_hcd_desde != '') $parametros['f_fecha_solicitud_hcd_desde'] = $f_fecha_solicitud_hcd_desde;
			if ($f_fecha_solicitud_hcd_hasta != '') $parametros['f_fecha_solicitud_hcd_hasta'] = $f_fecha_solicitud_hcd_hasta;
			if ($f_estado_solicitado_hcd != '') $parametros['f_estado_solicitado_hcd'] = $f_estado_solicitado_hcd;
			if ($f_estado_solicitado_ee != '') $parametros['f_estado_solicitado_ee'] = $f_estado_solicitado_ee;
			if ($f_estado_ingresado_ee != '') $parametros['f_estado_ingresado_ee'] = $f_estado_ingresado_ee;
			if ($f_estado_devuelto_ee != '') $parametros['f_estado_devuelto_ee'] = $f_estado_devuelto_ee;
			if ($f_estado_anulado != '') $parametros['f_estado_anulado'] = $f_estado_anulado;

			// Se obtienen las descripciones de los parámetros utilizados
			$parametros_codificados = $this->prepararParametrosParaReporte($parametros);
			// Se le pasa a la Vista el texto correspondiente al criterio de búsqueda utilizado, con sus nombres respectivos
			$paramVista['criterio_busqueda'] = ( ! is_null($parametros_codificados) ) ? $this->obtenerTextoCriterioBusqueda($parametros_codificados) : '';

			// Se pasa a la Vista los Préstamos obtenidos
			$paramVista['resultados'] = $solicitudes;

			// Instancio la vista y la muestro
			$vista = new BEReportesView($paramVista);
			$vista->vistaReportePdfSolicitudes();

		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}
}
?>
