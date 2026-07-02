<?php
/**
 * Clase de controlador de Gestion de Firmas
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_GIROS_PENDIENTES');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_GIROS_PENDIENTES');

class BEGirospendientesController extends BaseController
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

		// Determino las acciones válidas y su nivel de acceso mínimo requerido.
		// Este es un caso especial: solo aquellos usuarios que tengan 'confirmar_giros = 1'
		// (o en su defecto el secretario actual) pueden ser los que confirman o descartan giros.
		// Para que el secretario, que posee perfil de concejal, pueda confirmar o rechazar
		// giros, se habilitan las acciones pertinentes. El resto de los concejales, si bien pueden
		// ver los giros, nunca podran confirmarlos por no ser 'confirmadores' posibles.
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_CONCEJAL; // NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['datagrid'] = NIVEL_ACCESO_CONCEJAL; // NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['vergiros'] = NIVEL_ACCESO_CONCEJAL; // NIVEL_ACCESO_OPERADOR;
	}

	/**
	 * Invoca a la vista 'view' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function view($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Instancio la vista y la muestro
		$vista = new BEGirosPendientesView($paramVista);
		$vista->vistaGirosPendientes();
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

				// En caso de que el usuario sea un supervisor de mesa de entradas,
				// puede ver todos los giros. Sino, solamente ve los giros donde es
				// solicitante / firmante.
				$filtro_usuarios = (in_array($this->obtenerUsuarioActual()->id_usuario, SGL_ID_USUARIO_SUPERVISORES_MESA_ENTRADA))
					? null
					: $this->obtenerUsuarioActual();

				$pendientes = NG::girosPendientes()->obtenerGirosPendientesParaUsuario(
					$filtro_usuarios,
					// Control de consulta
					['T.`fecha_hora_entrada` ASC'], // La firma pendiente mas vieja primero
					$p_limitLength, // cantidad de registros (paginación)
					$p_limitStart // corrimiento de registros (paginación)
				);

				$cantidadTotalPendientes = NG::girosPendientes()->obtenerGirosPendientesParaUsuarioCantidad(
					$filtro_usuarios
				);

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = $cantidadTotalPendientes;
				$resultado['recordsFiltered'] = $cantidadTotalPendientes;
				$resultado['data'] = $pendientes;
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
	 * Esta acción del controlador devuelve un JSON con toda la información de los
	 * giros a comisiones pendientes.
 	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function vergiros($requestParams)
	{
		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = [];

		// Validación de parámetros de búsqueda
		try {
			$f_anio = Validator::get()->validar($requestParams['anio'], PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($requestParams['tipo'], PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($requestParams['numero'], PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($requestParams['cuerpo'], PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($requestParams['alcance'], PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_id_pendiente = Validator::get()->validar($requestParams['id_pendiente'], PATRON_NUMEROS, false, 'ID de lote de giros a comisiones pendientes');

			$giro_pendiente = NG::girosPendientes()->obtenerGiroPendiente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_id_pendiente);
			if (is_null($giro_pendiente))
				throw new Exception('No se han encontrado un lote de giros pendientes para el expediente.');

			// Se obtienen las Comisiones (Lugares de tipo C y habilitadas)
			$lugares = NG::expedientesParam()->obtenerLugares(
				'C',
				null, null, null, null, null, null, null, null,
				'1',
				null,
				array('descripcion_grp'),
				null,
				null);

			// Se genera la info de comisiones seleccionadas
			$comisiones = [];

			$data_giros = json_decode($giro_pendiente->giros_pendientes);

			foreach ($data_giros->f_comisiones as $k => $v) {
				$descripcion_grp = '?';
				foreach ($lugares as $l) {
					if ($l->codigo_grp == $v) {
						$descripcion_grp = $l->descripcion_grp;
						break;
					}
				}

				$comisiones[] = [
					'codigo_grp' => $v,
					'descripcion_grp' => $descripcion_grp,
					'observaciones' => $data_giros->f_observaciones[$k]
				];
			}

			$resultado['estado'] = 'OK';
			$resultado['mensaje'] = 'Giros pendientes para el expediente electrónico obtenidos con éxito.';
			$resultado['data'] = $comisiones;

		} catch (Exception $ex)	{
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = 'No se puedo obtener el lote de giros pendientes para el expediente electrónico. Causa: '.$ex->getMessage();
			$resultado['data'] = null;
		}

		echo json_encode($resultado);
	}

}
?>
