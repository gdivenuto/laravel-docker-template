<?php
/**
 * Clase de controlador del Home del subsitio.
 * 
 * @author Kaleb
 */
class BEExpedientesbusquedaantecedenteController extends BaseController
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
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['busquedaporantecedentedatagrid'] = NIVEL_ACCESO_PERIODISTA;
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

		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio'], date("Y"));
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		
		// Ejecuto la vista
		try {
			// Validación de parámetros de búsqueda
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
			
			$paramVista['f_numero'] = $f_numero;
			$paramVista['f_anio'] = $f_anio;

		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}

		// Instancio la vista y la muestro
		$vista = new BEExpedientesView($paramVista);
		$vista->vistaBusquedaPorAntecedente();
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables. 
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function busquedaporantecedentedatagrid($requestParams)
	{
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

			// Parametros de filtro
			$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);

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
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
				
				// El numero de expediente es "necesario". Si no me lo proveen, simulo un resultado vacío.
				if ($f_numero == null || $f_numero == '')
				{
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array();  // Es un array vacio!!! No NULL.
				}
				else
				{
					$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					$expedientes = NG::reportes()->obtenerExpedientesPorAntecedente(
						$f_anio, // anio_a
						null, // tipo_a
						$f_numero, // numero_a
						null, // digito_a
						null, // cuerpo_a
						null, // alcance_a
						null, // cuerpoalcance_a
						null, // anexoalcance_a
						null, // cuerpoanexoalcance_a
						null, // anexo_a
						null, // cuerpoanexo_a
						true, // instancias completas!
						// Control de consulta
						array('anio', 'tipo', 'numero', 'cuerpo', 'alcance'), // criterio y sentido de orden (FIJO)
						$p_limitLength, // cantidad de registros (paginación)
						$p_limitStart); // corrimiento de registros (paginación)	

					// Consulta de cantidad de expedientes por antecedente (total)
					$cantidadTotalExpedientes = NG::reportes()->obtenerExpedientesPorAntecedenteCantidad(
						$f_anio, // anio_a
						null, // tipo_a
						$f_numero, // numero_a
						null, // digito_a
						null, // cuerpo_a
						null, // alcance_a
						null, // cuerpoalcance_a
						null, // anexoalcance_a
						null, // cuerpoanexoalcance_a
						null, // anexo_a
						null  // cuerpoanexo_a
					);

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = $cantidadTotalExpedientes;
					$resultado['recordsFiltered'] = $cantidadTotalExpedientes;
					$resultado['data'] = $expedientes;	
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
}
?>