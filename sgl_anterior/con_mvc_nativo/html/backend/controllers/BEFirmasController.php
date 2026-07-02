<?php
/**
 * Clase de controlador de Gestion de Firmas
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_FIRMAS');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_FIRMAS');

class BEFirmasController extends BaseController
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

		// Nombre del módulo al que corresponde el controlador
		$this->nombreModulo = 'EXPEDIENTES';

		// Determino las acciones válidas y su nivel de acceso mínimo requerido
		$this->accionesPermitidas['firmadordescarga'] = NIVEL_ACCESO_CONCEJAL;//NIVEL_ACCESO_OPERADOR;
		// 2023-06-07: Se modificó el nivel de acceso para Concejales, para que puedan descargar el pdf una vez firmado.

		// Se sobreescriben permisos, si aplica:
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['datagrid'] = NIVEL_ACCESO_CONCEJAL;
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

		// Instancio la vista y la muestro
		$vista = new BEFirmasView($paramVista);
		$vista->vistaPendientesFirma();
	}

	/**
	 * Esta acción del controlador se utiliza cuando se descarga un archivo
	 * del firmador online. Muestra una vista que descarga el archivo y redirecciona
	 * a la vista general de expedientes.
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function firmadordescarga($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);
		$f_archivo_descarga = Validator::get()->obtenerDefault($requestParams['f_archivo_descarga'], '');

		// Ejecuto la vista
		try {
			// Valido el archivo de descarga, si existe
			if ($f_archivo_descarga != '') {
				$f_archivo_descarga = Validator::get()->validar(
					$f_archivo_descarga,
					"/^documento_[0-9]{1,5}_[0-9]{20,20}\.pdf$/i",
					false,
					'Archivo a descargar'
				);
				$paramVista['f_archivo_descarga'] = (file_exists(PATH_SGL_DOC_TEMPORALES.$f_archivo_descarga))
					? URL_SGL_DOC_TEMPORALES.$f_archivo_descarga
					: '';
			} else {
				$paramVista['f_archivo_descarga'] = '';
			}

		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}

		// Instancio la vista y la muestro
		$vista = new BEFirmasView($paramVista);
		$vista->vistaDescargarArchivo();
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

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {

			$resultado['recordsTotal'] = 0;
			$resultado['recordsFiltered'] = 0;
			$resultado['data'] = array();
			$resultado['error'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['numeroError'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {

			// Parametros de filtro
			//$f_detalle = Validator::get()->obtenerDefault($requestParams['f_detalle']);

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
			$resultado['draw'] = $p_draw; // Es un valor entero para control interno del DataTable
			try
			{
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				//$f_detalle = Validator::get()->validar($f_detalle, PATRON_NUMEROS, true, 'Detalle del documento');

				$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
				$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

				$firmas = NG::firmasExpedienteElec()->obtenerFirmasPendientesUsuario(
					$this->obtenerUsuarioActual(),
					// Control de consulta
					['T.`fecha_hora_entrada` ASC'], // La firma pendiente mas vieja primero
					$p_limitLength, // cantidad de registros (paginación)
					$p_limitStart); // corrimiento de registros (paginación)

				// Consulta de cantidad de Firmas pendientes
				$cantidadTotalFirmas = NG::firmasExpedienteElec()->obtenerFirmasPendientesUsuarioCantidad(
					$this->obtenerUsuarioActual()
				);

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = $cantidadTotalFirmas;
				$resultado['recordsFiltered'] = $cantidadTotalFirmas;
				$resultado['data'] = $firmas;
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

}
?>
