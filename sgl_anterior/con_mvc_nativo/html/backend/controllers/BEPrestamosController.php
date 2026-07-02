<?php
/**
 * Clase de controlador del Préstamo perteneciente a un expediente específico.
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_PRESTAMO');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_PRESTAMO');

class BEPrestamosController extends BaseController
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
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['datagrid'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['editprestado'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['editdevuelto'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['editanulado'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['editinfo'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['add'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['save'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['delete'] = NIVEL_ACCESO_OPERADOR;
		// Para la grilla general de Préstamos
		$this->accionesPermitidas['viewgeneral'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['datagridgeneral'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['addgeneral'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['generarpdfprestamos'] = NIVEL_ACCESO_OPERADOR;

		// Parámetros a excluir del criterio de búsqueda
		$this->parametros_excluidos = array('f_solicitante_tipo', 'f_solicitante_codigo');

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

		$this->etiquetas['f_fecha_solicitud_desde'] = '&nbsp;&nbsp;&nbsp;Desde';
		$this->etiquetas['f_fecha_solicitud_hasta'] = '&nbsp;&nbsp;&nbsp;Hasta';
		$this->etiquetas['f_solicitante'] 	  = '&nbsp;&nbsp;&nbsp;Solicitante';

		// Para los Estados del Préstamo
		$this->etiquetas['f_estado_solicitado'] = '&nbsp;&nbsp;&nbsp;Estado Solicitado';
		$this->etiquetas['f_estado_prestado']   = '&nbsp;&nbsp;&nbsp;Estado Prestado';
		$this->etiquetas['f_estado_devuelto']   = '&nbsp;&nbsp;&nbsp;Estado Devuelto';
		$this->etiquetas['f_estado_anulado']    = '&nbsp;&nbsp;&nbsp;Estado Anulado';
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
		$f_tipo    = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero  = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo  = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

		// Ejecuto la vista
		try {
			// Validación de parámetros de búsqueda
			$f_anio    = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
			$f_tipo    = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
			$f_numero  = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
			$f_cuerpo  = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance de expediente');

			$paramVista['f_anio']    = $f_anio;
			$paramVista['f_tipo']    = $f_tipo;
			$paramVista['f_numero']  = $f_numero;
			$paramVista['f_cuerpo']  = $f_cuerpo;
			$paramVista['f_alcance'] = $f_alcance;

			// Instancio la vista y la muestro
			$vista = new BEPrestamosView($paramVista);
			$vista->vistaListado();

		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
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

			// Parametros de filtro
			$f_anio    = Validator::get()->obtenerDefault($requestParams['f_anio']);
			$f_tipo    = Validator::get()->obtenerDefault($requestParams['f_tipo']);
			$f_numero  = Validator::get()->obtenerDefault($requestParams['f_numero']);
			$f_cuerpo  = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
			$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

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
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
				$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo de expediente');
				$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance de expediente');

				// La clave del expediente es "necesaria". Si no me la proveen, simulo un resultado vacío.
				if ( $f_anio == null || $f_anio == '' ||
					 $f_tipo == null || $f_tipo == '' ||
					 $f_numero == null || $f_numero == '' ||
					 $f_cuerpo == null || $f_cuerpo == '' ||
					 $f_alcance == null || $f_alcance == '' )
				{
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array();  // Es un array vacio!!! No NULL.
				}
				else
				{
					$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					// NO IMPLEMENTADOS LOS PERFILES AÚN EN LA VERSIÓN 2
					// para perfiles 1 y 2 se muestran todos los Préstamos (NO se filtra por ningún estado)
					// para perfiles 3 y 4 sólo los Prestados, Devueltos y/o Anulados (PARA QUE LOS BLOQUES NO SEPAN QUIÉN LOS PIDIÓ ANTES DE PRESTARSE)
					//$estados_a_obtener = ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) ? null : array(Prestamo::E_PRESTADO, Prestamo::E_DEVUELTO, Prestamo::E_ANULADO);
					$estados_a_obtener = null;

					$prestamos = null;
					$cantidadTotalPrestamos = 0;

					$prestamos = NG::prestamos()->obtenerPrestamos(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // cuerpo
						$f_alcance, // alcance
						null, // digito
						null, // cuerpoalcance
						null, // anexoalcance
						null, // cuerpoanexoalcance
						null, // anexo
						null, // cuerpoanexo
						null, null, // Fecha desde y Fecha hasta
						null, null, // Solicitante tipo y código
						$estados_a_obtener, // Estados
						// Control de consulta
						true, // Instancias completas, para obtener posibles estados siguientes
						array('anio desc', 'tipo', 'numero desc', 'cuerpo', 'alcance', 'digito', 'cuerpoalcance', 'anexoalcance', 'cuerpoanexoalcance', 'anexo', 'cuerpoanexo', 'fecha_solicitud'),
						$p_limitLength, // cantidad de registros (paginación)
						$p_limitStart); // corrimiento de registros (paginación)

					// Consulta de cantidad de Prestamos del expediente respectivo (total)
					$cantidadTotalPrestamos = NG::prestamos()->obtenerPrestamosCantidad(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // cuerpo
						$f_alcance, // alcance
						null, // digito
						null, // cuerpoalcance
						null, // anexoalcance
						null, // cuerpoanexoalcance
						null, // anexo
						null, // cuerpoanexo
						null, null, // Fecha desde y Fecha hasta
						null, null, // Solicitante tipo y código
						$estados_a_obtener // Estados
					);

					// finalmente, verifico si existe el expediente
					$cantidadExpedientes = NG::expedientes()->obtenerExpedientesCantidad($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);
					$resultado['existeExpediente'] = $cantidadExpedientes == 1;

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = $cantidadTotalPrestamos;
					$resultado['recordsFiltered'] = $cantidadTotalPrestamos;
					$resultado['data'] = $prestamos;
				}
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
	 * Este método nos envía al formulario de edición de cambio de estado a Prestado
	 */
	public function editprestado($requestParams)
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
		$f_fecha_solicitud = Validator::get()->obtenerDefault($requestParams['f_fecha_solicitud']);

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

			// Obtengo el expediente a editar su prestamo
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, false);
			if (is_null($expediente)) {
				throw new Exception('Error: No existe el expediente al cual se desea editar el prestamo.');
			}
			// Si el expediente se encuentra agregado a otro expediente
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite editar el prestamo, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			$prestamo = NG::prestamos()->obtenerPrestamo(
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
				$f_fecha_solicitud,
				true); // Instancias completas

			if (is_null($prestamo))
				throw new Exception('Error: El prestamo a editar no existe.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $prestamo->generarChecksum());

			// Para determinar si estoy agregando o modificando un prestamo, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			$paramVista['prestamo'] = $prestamo;
			// Para volver a la grilla general o la solapa respectiva
			$paramVista['f_grilla']	= $requestParams['f_grilla'];

			// Instancio la vista y la muestro
			$vista = new BEPrestamosView($paramVista);
			$vista->vistaEdicionEstadoPrestado();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);

			// Se vuelve a la grilla de prestamos del expediente respectivo
			$this->redireccionar(
				'prestamos',
				'view',
				[
					'f_anio' => $f_anio,
					'f_tipo' => $f_tipo,
					'f_numero' => $f_numero,
					'f_cuerpo' => $f_cuerpo,
					'f_alcance' => $f_alcance
				]
			);
		}
	}

	/**
	 * Este método nos envía al formulario de edición de cambio de estado a Devuelto
	 */
	public function editdevuelto($requestParams)
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
		$f_fecha_solicitud = Validator::get()->obtenerDefault($requestParams['f_fecha_solicitud']);

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

			// Obtengo el expediente a editar su prestamo
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, false);
			if (is_null($expediente)) {
				throw new Exception('Error: No existe el expediente al cual se desea editar el prestamo.');
			}
			// Si el expediente se encuentra agregado a otro expediente
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite editar el prestamo, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			$prestamo = NG::prestamos()->obtenerPrestamo(
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
				$f_fecha_solicitud,
				true); // Instancias completas

			if (is_null($prestamo))
				throw new Exception('Error: El prestamo a editar no existe.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $prestamo->generarChecksum());

			// Para determinar si estoy agregando o modificando un prestamo, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			$paramVista['prestamo'] = $prestamo;
			// Para volver a la grilla general o la solapa respectiva
			$paramVista['f_grilla']	= $requestParams['f_grilla'];

			// Instancio la vista y la muestro
			$vista = new BEPrestamosView($paramVista);
			$vista->vistaEdicionEstadoDevuelto();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);

			// Se vuelve a la grilla de prestamos del expediente respectivo
			$this->redireccionar(
				'prestamos',
				'view',
				[
					'f_anio' => $f_anio,
					'f_tipo' => $f_tipo,
					'f_numero' => $f_numero,
					'f_cuerpo' => $f_cuerpo,
					'f_alcance' => $f_alcance
				]
			);
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
		$f_fecha_solicitud = Validator::get()->obtenerDefault($requestParams['f_fecha_solicitud']);

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

			// Obtengo el expediente a editar su prestamo
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, false);
			if (is_null($expediente)) {
				throw new Exception('Error: No existe el expediente al cual se desea editar el prestamo.');
			}
			// Si el expediente se encuentra agregado a otro expediente
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite editar el prestamo, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			$prestamo = NG::prestamos()->obtenerPrestamo(
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
				$f_fecha_solicitud,
				true); // Instancias completas

			if (is_null($prestamo))
				throw new Exception('Error: El prestamo a editar no existe.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $prestamo->generarChecksum());

			// Para determinar si estoy agregando o modificando un prestamo, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			$paramVista['prestamo'] = $prestamo;
			// Para volver a la grilla general o la solapa respectiva
			$paramVista['f_grilla']	= $requestParams['f_grilla'];

			// Instancio la vista y la muestro
			$vista = new BEPrestamosView($paramVista);
			$vista->vistaEdicionEstadoAnulado();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);

			// Se vuelve a la grilla de prestamos del expediente respectivo
			$this->redireccionar(
				'prestamos',
				'view',
				[
					'f_anio' => $f_anio,
					'f_tipo' => $f_tipo,
					'f_numero' => $f_numero,
					'f_cuerpo' => $f_cuerpo,
					'f_alcance' => $f_alcance
				]
			);
		}
	}

	/**
	 * Este método nos envía al formulario de edición de la información del préstamo
	 * Para editar: Número del Libro, Folio del Libro y Observaciones
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
		$f_fecha_solicitud = Validator::get()->obtenerDefault($requestParams['f_fecha_solicitud']);

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

			// Obtengo el expediente a editar su prestamo
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, false);
			if (is_null($expediente)) {
				throw new Exception('Error: No existe el expediente al cual se desea editar el prestamo.');
			}
			// Si el expediente se encuentra agregado a otro expediente
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite editar el prestamo, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			$prestamo = NG::prestamos()->obtenerPrestamo(
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
				$f_fecha_solicitud,
				true); // Instancias completas

			if (is_null($prestamo))
				throw new Exception('Error: El prestamo a editar no existe.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $prestamo->generarChecksum());

			// Para determinar si estoy agregando o modificando un prestamo, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			$paramVista['prestamo'] = $prestamo;
			// Para volver a la grilla general o la solapa respectiva
			$paramVista['f_grilla']	= $requestParams['f_grilla'];

			// Instancio la vista y la muestro
			$vista = new BEPrestamosView($paramVista);
			$vista->vistaEdicionInfo();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);

			// Se vuelve a la grilla de prestamos del expediente respectivo
			$this->redireccionar(
				'prestamos',
				'view',
				[
					'f_anio' => $f_anio,
					'f_tipo' => $f_tipo,
					'f_numero' => $f_numero,
					'f_cuerpo' => $f_cuerpo,
					'f_alcance' => $f_alcance
				]
			);
		}
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

		try {
			// Se recibe la clave del expediente
			$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
			$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
			$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
			$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

			// Obtengo el expediente a verificar
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, false);
			if (is_null($expediente)) {
				throw new Exception('Error: No existe el expediente al que se desea agregarle un préstamo.');
			}

			// Si el expediente se encuentra agregado a otro expediente
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite agregar un Préstamo, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			// Para determinar si estoy agregando o modificando un Prestamo, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'agregar');

			// Preparo una instancia de Prestamo para la vista
			$prestamo = new Prestamo();
			$prestamo->anio = $f_anio;
			$prestamo->tipo = $f_tipo;
			$prestamo->numero = $f_numero;
			$prestamo->cuerpo = $f_cuerpo;
			$prestamo->alcance = $f_alcance;
			$prestamo->digito = '0';
			$prestamo->cuerpoalcance = 0;
			$prestamo->anexoalcance = 0;
			$prestamo->cuerpoanexoalcance = 0;
			$prestamo->anexo = 0;
			$prestamo->cuerpoanexo = 0;
			$prestamo->fecha_solicitud = date("Y-m-d");

			$paramVista['prestamo'] = $prestamo;

			// 25/02/2021 XXXX
			// Se obtienen todos los posibles Solicitantes
			$paramVista['listado_solicitantes'] = NG::expedientesParam()->obtenerLugares(
				null, null, null, null, null, null, null, null, null, '1', null, array('descripcion_grp'), null, null);

			// Se muestra el formulario para editar un Nuevo Préstamo
			$vista = new BEPrestamosView($paramVista);
			$vista->vistaEdicionNuevo();
		}
		catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			// Se vuelve a la grilla con el expediente respectivo
			$this->redireccionar(
				'prestamos',
				'view',
				[
					'f_anio' => $f_anio,
					'f_tipo' => $f_tipo,
					'f_numero' => $f_numero,
					'f_cuerpo' => $f_cuerpo,
					'f_alcance' => $f_alcance
				]
			);
		}
	}

	/**
	 * Invoca a la vista 'viewgeneral' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function viewgeneral($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Verifico si me han pasado parametros por url, para un acceso directo a una posición del paginador.
		$f_anio    = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo    = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero  = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo  = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		$f_digito  = Validator::get()->obtenerDefault($requestParams['f_digito']);

		// Ejecuto la vista
		try {
			// Validación de parámetros de búsqueda
			$f_anio    = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
			$f_tipo    = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
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

		// 25/02/2021 XXXX
		// Se obtienen todos los posibles Solicitantes
		$paramVista['listado_solicitantes'] = NG::expedientesParam()->obtenerLugares(
			null, null, null, null, null, null, null, null, null, '1', null, array('descripcion_grp'), null, null);

		// Instancio la vista y la muestro
		$vista = new BEPrestamosView($paramVista);
		$vista->vistaListadoGeneral();
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function datagridgeneral($requestParams)
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

			// Para filtrar por un Solicitante determinado
			$f_solicitante = Validator::get()->obtenerDefault($requestParams['f_solicitante']);

			// la fecha Desde, por defecto es 1 año menor que la fecha actual
			$f_fecha_solicitud_desde = (isset($requestParams['f_fecha_desde']) && $requestParams['f_fecha_desde'] != '') ? $requestParams['f_fecha_desde'] : null;//$this->getFechaOffsetAnios($this->defaultOffsetAnios)
			// la fecha Hasta, por defecto es la fecha actual
			$f_fecha_solicitud_hasta = (isset($requestParams['f_fecha_hasta']) && $requestParams['f_fecha_hasta'] != '') ? $requestParams['f_fecha_hasta'] : null;//date("Y-m-d")

			// Para filtrar por los Estados elegidos
			$f_estado_solicitado = Validator::get()->obtenerDefault($requestParams['f_estado_solicitado']);
			$f_estado_prestado   = Validator::get()->obtenerDefault($requestParams['f_estado_prestado']);
			$f_estado_devuelto   = Validator::get()->obtenerDefault($requestParams['f_estado_devuelto']);
			$f_estado_anulado    = Validator::get()->obtenerDefault($requestParams['f_estado_anulado']);

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

				// Si se recibió un Solicitante
				if ( !is_null($f_solicitante) && $f_solicitante != '0' )
					// Se separa por el guión medio
					$partes_solicitante = explode('|', $f_solicitante);

				// Se asignan el Tipo y el Código del solicitante
				$f_solicitante_tipo   = ($partes_solicitante[0]) ? $partes_solicitante[0] : null;
				$f_solicitante_codigo = ($partes_solicitante[1]) ? $partes_solicitante[1] : null;

				// NO IMPLEMENTADOS LOS PERFILES AÚN EN LA VERSIÓN 2
				// para perfiles 1 y 2 se muestran todos los Préstamos (NO se filtra por ningún estado)
				// para perfiles 3 y 4 sólo los Prestados, Devueltos y/o Anulados (PARA QUE LOS BLOQUES NO SEPAN QUIÉN LOS PIDIÓ ANTES DE PRESTARSE)
				//$estados_a_obtener = ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) ? null : array(Prestamo::E_PRESTADO, Prestamo::E_DEVUELTO, Prestamo::E_ANULADO);

				$estados_a_obtener = Array();
				// Para estado Solicitado
				if ( !is_null($f_estado_solicitado) && $f_estado_solicitado != '')
					$estados_a_obtener[] = $f_estado_solicitado;

				// Para estado Prestado
				if ( !is_null($f_estado_prestado) && $f_estado_prestado != '')
					$estados_a_obtener[] = $f_estado_prestado;

				// Para estado Devuelto
				if ( !is_null($f_estado_devuelto) && $f_estado_devuelto != '')
					$estados_a_obtener[] = $f_estado_devuelto;

				// Para estado Anulado
				if ( !is_null($f_estado_anulado) && $f_estado_anulado != '')
					$estados_a_obtener[] = $f_estado_anulado;

				$prestamos = null;
				$cantidadTotalPrestamos = 0;

				$prestamos = NG::prestamos()->obtenerPrestamos(
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
					$f_fecha_solicitud_desde, $f_fecha_solicitud_hasta, // Fecha desde y Fecha hasta
					$f_solicitante_tipo, $f_solicitante_codigo, // Solicitante tipo y código
					$estados_a_obtener, // Estados
					// Control de consulta
					true, // Instancias completas, para obtener posibles estados siguientes
					array('anio desc', 'tipo', 'numero desc', 'cuerpo', 'alcance', 'digito', 'cuerpoalcance', 'anexoalcance', 'cuerpoanexoalcance', 'anexo', 'cuerpoanexo', 'fecha_solicitud'),
					$p_limitLength, // cantidad de registros (paginación)
					$p_limitStart // corrimiento de registros (paginación)
				);

				// Consulta de cantidad de Prestamos del expediente respectivo (total)
				$cantidadTotalPrestamos = NG::prestamos()->obtenerPrestamosCantidad(
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
					$f_fecha_solicitud_desde, $f_fecha_solicitud_hasta, // Fecha desde y Fecha hasta
					$f_solicitante_tipo, $f_solicitante_codigo, // Solicitante tipo y código
					$estados_a_obtener // Estados
				);

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = $cantidadTotalPrestamos;
				$resultado['recordsFiltered'] = $cantidadTotalPrestamos;
				$resultado['data'] = $prestamos;
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
	 * Invoca a la vista 'addgeneral' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function addgeneral($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Para determinar si estoy agregando o modificando un Prestamo, guardo una variable de sesion
		SessionController::get()->guardar(SAVE_ACTION, 'agregar');

		// Preparo una instancia de Prestamo para la vista
		$prestamo = new Prestamo();
		$prestamo->anio = date("Y");
		$prestamo->tipo = 'D';
		$prestamo->numero = 0.0;
		$prestamo->cuerpo = 0;
		$prestamo->alcance = 0;
		$prestamo->digito = '0';
		$prestamo->cuerpoalcance = 0;
		$prestamo->anexoalcance = 0;
		$prestamo->cuerpoanexoalcance = 0;
		$prestamo->anexo = 0;
		$prestamo->cuerpoanexo = 0;
		$prestamo->fecha_solicitud = date("Y-m-d");
		$prestamo->estado = 'S';

		$paramVista['prestamo'] = $prestamo;

		// 25/02/2021 XXXX
		// Se obtienen todos los posibles Solicitantes
		$paramVista['listado_solicitantes'] = NG::expedientesParam()->obtenerLugares(
			null, null, null, null, null, null, null, null, null, '1', null, array('descripcion_grp'), null, null);

		// Se muestra el formulario para editar un Nuevo Préstamo
		$vista = new BEPrestamosView($paramVista);
		$vista->vistaEdicionNuevoGeneral();
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

				// Si se realiza un cambio de Estado del Préstamo
				if ( ! $f_editarsoloinfo ) {
					// Verifico que el estado sea válido
					$f_nuevo_estado = Validator::get()->validar($requestParams['f_nuevo_estado'], PATRON_ESTADO_PRESTAMO_EXPEDIENTE, false, 'Pr&oacute;ximo estado');
					$f_nueva_fecha_hora = Validator::get()->validar($requestParams['f_nueva_fecha_hora'], PATRON_FECHA_HORA, false, "Fecha y hora del cambio de estado");
				}

				// Verifico acción de guardado
				if (!SessionController::get()->existe(SAVE_ACTION))
					throw new Exception('No se puede determinar la acci&oacute;n de guardado.');

				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				$jsonData = file_get_contents('php://input');
				$prestamo = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($prestamo) == 'Prestamo'))
					throw new Exception('Se esperaba un objeto de tipo Prestamo.');

				// Si se quiere prestar un Expediente del HCD
				if ( $prestamo->tipo === 'E' || $prestamo->tipo === 'N' || $prestamo->tipo === 'R' )
					// Se define si existe o no
					$existe_expediente = NG::prestamos()->existeExpedienteHCD($prestamo->anio, $prestamo->tipo, $prestamo->numero, $prestamo->cuerpo, $prestamo->alcance);
				elseif ( $prestamo->tipo === 'D' || $prestamo->tipo === 'O' )
					// Se supone que existe el expediente del D.E. u Otro Ente Externo
					$existe_expediente = true;

				// Si existe el expediente
				if ( $existe_expediente ) {

					// Si estoy agregando, el Préstamo no debe existir
					$prestamoActual = NG::prestamos()->obtenerPrestamo(
						$prestamo->anio,
						$prestamo->tipo,
						$prestamo->numero,
						$prestamo->cuerpo,
						$prestamo->alcance,
						$prestamo->digito,
						$prestamo->cuerpoalcance,
						$prestamo->anexoalcance,
						$prestamo->cuerpoanexoalcance,
						$prestamo->anexo,
						$prestamo->cuerpoanexo,
						$prestamo->fecha_solicitud,
						true
					);

					if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
						if (!is_null($prestamoActual))
							throw new Exception('No se puede agregar un Pr&eacute;stamo que ya se encuentre ingresado. Verifique la clave del prestamo.');
					}
					// Si estoy editando un préstamo...
					else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
						// ... el préstamo debe existir
						if (is_null($prestamoActual))
							throw new Exception('No se puede editar un Pr&eacute;stamo inexistente.');

						// ... el checksum no tiene que haber variado
						if ( ! $prestamoActual->verificarChecksum(SessionController::get()->obtener(CHECKSUM)))
							throw new Exception('El Pr&eacute;stamo editado ya ha sido modificado desde otra terminal.');
					}

					// ***********************************************************
					//	Validación de atributos (según próximo estado)
					// ***********************************************************
					// Actualizo datos generales y de usuario (quien realizó la modificación)
					$prestamo->observaciones_prestamo = Validator::get()->sanear($prestamo->observaciones_prestamo);
					$usuario = $this->obtenerUsuarioActual();
					$prestamo->id_usuario = $usuario->id_usuario;

					// Si sólo se desea modificar la información del Préstamo
					if ($f_editarsoloinfo) {
						// Si la fecha de Prestado no es nula, es que ya se prestó, por lo cual permito editar folio y numero del libro,
						// los cuales se suponen ya estan cargados
						if ( !is_null($prestamo->fecha_prestado) ) {
							$prestamo->libro_numero = Validator::get()->sanear($prestamo->libro_numero);
							$prestamo->libro_folio  = Validator::get()->sanear($prestamo->libro_folio);
						} elseif ( !is_null($prestamoActual) ) {
							// Por las dudas mantengo los valores anteriores (por las dudas que me quieran hacer una cagada)
							$prestamo->libro_numero = $prestamoActual->libro_numero;
							$prestamo->libro_folio  = $prestamoActual->libro_folio;
						}
					}
					else // Si se desea realizar un cambio de estado
					{
						if ( $f_nuevo_estado === Prestamo::E_PRESTADO ) {
							$prestamo->libro_numero = Validator::get()->sanear($prestamo->libro_numero);
							$prestamo->libro_folio  = Validator::get()->sanear($prestamo->libro_folio);
						} else {
							// Por las dudas mantengo los valores anteriores (por las dudas que me quieran hacer una cagada)
							$prestamo->libro_numero = $prestamoActual->libro_numero;
							$prestamo->libro_folio  = $prestamoActual->libro_folio;
						}

						// Cambio de estado (lanza excepcion si falla)
						$prestamo = NG::prestamos()->cambiarEstado($prestamo, $f_nuevo_estado, $f_nueva_fecha_hora);
					}

					// Guardo el prestamo
					$prestamo = NG::prestamos()->guardarPrestamo($prestamo, true); // guardo y recargo

					// Se verifica si requiere una Solicitud de Expediente Externo.
					// Se considera que requiere una Solicitud cuando NO existe al menos una en estado:
	 				// 		SHCD = "solicitada al hcd",
	 				// 		SEE  = "solicitada al ente externo" o
	 				// 		IEE  = "ingresada desde el Ente Externo al hcd".
	 				// Además, el préstamo debe ser de un expediente externo (tipo = "D" ó tipo = "O"),
	 				// y debe estar en un estado QUE NO SEA ESTADO FINAL
	 				// (porque los estados finales ya no requieren solicitud).
					if ( NG::prestamos()->requiereSolicitudExpedienteExterno($prestamo) ) {
						// Se obtiene un objeto de Solicitud de Expediente Externo
						$solicitud = NG::prestamos()->obtenerInstanciaSolicitudExpedienteExterno($prestamo);

						// Se guardan los datos de la Solicitud del expediente externo
						$solicitud = NG::prestamos()->guardarSolicitudExpedienteExterno($solicitud);
					}

					// Genero respuesta
					$resultado['estado']  = 'OK';
					$resultado['mensaje'] = 'Pr&eacute;stamo guardado con &eacute;xito.';
					$resultado['data']    = $prestamo;
					$resultado['grilla']  = $requestParams['f_grilla'];// Para volver a la grilla general o la solapa respectiva
				} else {
					// Se muestra el mensaje de expediente inexistente en el mismo formulario
					$resultado['estado']  = 'ATENCI&Oacute;N';
					$resultado['mensaje'] = "El expediente interno ".$prestamo->anio."-".$prestamo->tipo."-".$prestamo->numero."-".$prestamo->cuerpo."-".$prestamo->alcance." no existe en el sistema.";
					$resultado['data']    = null;
				}

			} catch (Exception $e) {
				$resultado['estado']  = 'ATENCI&Oacute;N';
				$resultado['mensaje'] = 'No se pudo guardar el Pr&eacute;stamo. Causa: '.$e->getMessage();
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
				$prestamo = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($prestamo) == 'Prestamo'))
					throw new Exception('Se esperaba un objeto de tipo Prestamo.');

				// Obtengo el expediente antes de eliminar su prestamo
				$expediente = NG::expedientes()->obtenerExpediente(
					$prestamo->anio,
					$prestamo->tipo,
					$prestamo->numero,
					$prestamo->cuerpo,
					$prestamo->alcance,
					false
				);
				if (is_null($expediente)) {
					throw new Exception('Error: No existe el expediente al cual se desea eliminar el préstamo.');
				}
				// Si el expediente se encuentra agregado a otro expediente
				if ( NG::expedientes()->estaAgregadoA($expediente) ) {
					throw new Exception('No se permite eliminar el préstamo, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
				}

				// Antes de eliminar, recargo el Préstamo para obtenerlo completo
				$prestamo = NG::prestamos()->obtenerPrestamo(
					$prestamo->anio,
					$prestamo->tipo,
					$prestamo->numero,
					$prestamo->cuerpo,
					$prestamo->alcance,
					$prestamo->digito,
					$prestamo->cuerpoalcance,
					$prestamo->anexoalcance,
					$prestamo->cuerpoanexoalcance,
					$prestamo->anexo,
					$prestamo->cuerpoanexo,
					$prestamo->fecha_solicitud,
					true
				);

				$prestamo = NG::prestamos()->eliminarPrestamo($prestamo);

				// Genero respuesta
				$resultado['estado']  = 'OK';
				$resultado['mensaje'] = 'Pr&eacute;stamo eliminado con &eacute;xito.';
				$resultado['data']    = $prestamo;

			} catch (Exception $e) {
				$resultado['estado']  = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar el Pr&eacute;stamo. Causa: '.$e->getMessage();
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
		if ( isset($parametros['f_fecha_solicitud_desde']) && !is_null($parametros['f_fecha_solicitud_desde']) )
			$parametros['f_fecha_solicitud_desde'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_solicitud_desde']);

		if ( isset($parametros['f_fecha_solicitud_hasta']) && !is_null($parametros['f_fecha_solicitud_hasta']) )
			$parametros['f_fecha_solicitud_hasta'] = Validator::get()->convertirAFechaVista($parametros['f_fecha_solicitud_hasta']);

		// Si existe como clave del vector de Parámetros
		if ( isset($parametros['f_solicitante_tipo']) && array_key_exists('f_solicitante_tipo', $parametros) && array_key_exists('f_solicitante_codigo', $parametros)) {
			// Se obtiene el nombre de la Comisión elegida (por ello la verificación con cero)
			$nuevoValor = (isset($parametros['f_solicitante_tipo']) && $parametros['f_solicitante_tipo'] != '0') ? NG::expedientesParam()->obtenerLugar($parametros['f_solicitante_tipo'], $parametros['f_solicitante_codigo']) : null;
			$parametros['f_solicitante'] = (!is_null($nuevoValor)) ? $nuevoValor->descripcion_grp : null;
		}

		return $parametros;
	}

	/**
	 * Se genera el listado de Préstamos en formato PDF
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function generarpdfprestamos($requestParams)
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

		// Filtrado por un Solicitante determinado
		$f_solicitante = Validator::get()->obtenerDefault($requestParams['f_solicitante']);

		// Filtrado por un rango de fechas
		$f_fecha_solicitud_desde = (isset($requestParams['f_fecha_solicitud_desde']) && $requestParams['f_fecha_solicitud_desde'] != '') ? $requestParams['f_fecha_solicitud_desde'] : null;
		$f_fecha_solicitud_hasta = (isset($requestParams['f_fecha_solicitud_hasta']) && $requestParams['f_fecha_solicitud_hasta'] != '') ? $requestParams['f_fecha_solicitud_hasta'] : null;

		// Filtrado por Estados
		$f_estado_solicitado = Validator::get()->obtenerDefault($requestParams['f_estado_solicitado']);
		$f_estado_prestado   = Validator::get()->obtenerDefault($requestParams['f_estado_prestado']);
		$f_estado_devuelto   = Validator::get()->obtenerDefault($requestParams['f_estado_devuelto']);
		$f_estado_anulado    = Validator::get()->obtenerDefault($requestParams['f_estado_anulado']);

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

			// Si se recibió un Solicitante
			if ( !is_null($f_solicitante) && $f_solicitante != '0' )
				// Se separa por el guión medio
				$partes_solicitante = explode('|', $f_solicitante);

			// Se asignan el Tipo y el Código del solicitante
			$f_solicitante_tipo   = ($partes_solicitante[0]) ? $partes_solicitante[0] : null;
			$f_solicitante_codigo = ($partes_solicitante[1]) ? $partes_solicitante[1] : null;

			$estados_a_obtener = Array();
			// Para estado Solicitado
			if ( !is_null($f_estado_solicitado) && $f_estado_solicitado != '')
				$estados_a_obtener[] = $f_estado_solicitado;

			// Para estado Prestado
			if ( !is_null($f_estado_prestado) && $f_estado_prestado != '')
				$estados_a_obtener[] = $f_estado_prestado;

			// Para estado Devuelto
			if ( !is_null($f_estado_devuelto) && $f_estado_devuelto != '')
				$estados_a_obtener[] = $f_estado_devuelto;

			// Para estado Anulado
			if ( !is_null($f_estado_anulado) && $f_estado_anulado != '')
				$estados_a_obtener[] = $f_estado_anulado;

			// Consulta de cantidad de Prestamos
			$cantidadResultados = NG::prestamos()->obtenerPrestamosCantidad(
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
				$f_fecha_solicitud_desde, $f_fecha_solicitud_hasta, // Fecha desde y Fecha hasta
				$f_solicitante_tipo, $f_solicitante_codigo, // Solicitante tipo y código
				$estados_a_obtener // Estados
			);

			// Verifico la cantidad máxima de resultados
			if ($cantidadResultados > KRAKEN_REPORT_MAX_RESULT_COUNT)
				throw new Exception(sprintf('Error al generar reporte: No ha podido generarse el reporte porque posee %d resultados y supera el l&iacute;mite de %d permitidos. Por favor, ajuste los par&aacute;metros del mismo y vuelva a intentarlo.',
					$cantidadResultados, KRAKEN_REPORT_MAX_RESULT_COUNT));

			$prestamos = NG::prestamos()->obtenerPrestamos(
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
				$f_fecha_solicitud_desde, $f_fecha_solicitud_hasta, // Fecha desde y Fecha hasta
				$f_solicitante_tipo, $f_solicitante_codigo, // Solicitante tipo y código
				$estados_a_obtener, // Estados
				// Control de consulta
				true, // Instancias completas, para obtener posibles estados siguientes
				array('anio desc', 'tipo', 'numero desc', 'cuerpo', 'alcance', 'digito', 'cuerpoalcance', 'anexoalcance', 'cuerpoanexoalcance', 'anexo', 'cuerpoanexo', 'fecha_solicitud')
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
			if ($f_fecha_solicitud_desde != '') $parametros['f_fecha_solicitud_desde'] = $f_fecha_solicitud_desde;
			if ($f_fecha_solicitud_hasta != '') $parametros['f_fecha_solicitud_hasta'] = $f_fecha_solicitud_hasta;
			if ($f_solicitante_tipo != '') $parametros['f_solicitante_tipo'] = $f_solicitante_tipo;
			if ($f_solicitante_codigo != '') $parametros['f_solicitante_codigo'] = $f_solicitante_codigo;
			if ($f_estado_solicitado != '') $parametros['f_estado_solicitado'] = $f_estado_solicitado;
			if ($f_estado_prestado != '') $parametros['f_estado_prestado'] = $f_estado_prestado;
			if ($f_estado_devuelto != '') $parametros['f_estado_devuelto'] = $f_estado_devuelto;
			if ($f_estado_anulado != '') $parametros['f_estado_anulado'] = $f_estado_anulado;

			// Se obtienen las descripciones de los parámetros utilizados
			$parametros_codificados = $this->prepararParametrosParaReporte($parametros);
			// Se le pasa a la Vista el texto correspondiente al criterio de búsqueda utilizado, con sus nombres respectivos
			$paramVista['criterio_busqueda'] = ( ! is_null($parametros_codificados) ) ? $this->obtenerTextoCriterioBusqueda($parametros_codificados) : '';

			// Se pasa a la Vista los Préstamos obtenidos
			$paramVista['resultados'] = $prestamos;

			// Instancio la vista y la muestro
			$vista = new BEReportesView($paramVista);
			$vista->vistaReportePdfPrestamos();

		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}
}
?>
