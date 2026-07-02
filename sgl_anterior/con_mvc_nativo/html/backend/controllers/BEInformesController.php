<?php
/**
 * Clase de controlador del Informe perteneciente a un Giro específico.
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_INFORME');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_INFORME');

class BEInformesController extends BaseController
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
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['datagrid'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['edit'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['add'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['save'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['delete'] = NIVEL_ACCESO_OPERADOR;
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
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		$f_orden_giro = Validator::get()->obtenerDefault($requestParams['f_orden_giro']);
		$f_fecha_salida_giro = Validator::get()->obtenerDefault($requestParams['f_fecha_salida_giro']);

		// Ejecuto la vista
		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance de expediente');
			$f_orden_giro = Validator::get()->validar($f_orden_giro, PATRON_NUMEROS, true, 'Orden del Giro');

			$paramVista['f_anio'] = $f_anio;
			$paramVista['f_tipo'] = $f_tipo;
			$paramVista['f_numero'] = $f_numero;
			$paramVista['f_cuerpo'] = $f_cuerpo;
			$paramVista['f_alcance'] = $f_alcance;
			$paramVista['f_orden_giro'] = $f_orden_giro;
			$paramVista['f_fecha_salida_giro'] = $f_fecha_salida_giro;

		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}

		// Instancio la vista y la muestro
		$vista = new BEInformesView($paramVista);
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

			// Parametros de filtro
			$f_anio 	  = Validator::get()->obtenerDefault($requestParams['f_anio']);
			$f_tipo 	  = Validator::get()->obtenerDefault($requestParams['f_tipo']);
			$f_numero 	  = Validator::get()->obtenerDefault($requestParams['f_numero']);
			$f_cuerpo 	  = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
			$f_alcance 	  = Validator::get()->obtenerDefault($requestParams['f_alcance']);
			$f_orden_giro = Validator::get()->obtenerDefault($requestParams['f_orden_giro']);

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
				$f_orden_giro = Validator::get()->validar($f_orden_giro, PATRON_NUMEROS, true, 'Orden del Giro');

				// La clave del informe es "necesaria". Si no me la proveen, simulo un resultado vacío.
				if ( $f_anio == null || $f_anio == '' ||
					 $f_tipo == null || $f_tipo == '' ||
					 $f_numero == null || $f_numero == '' ||
					 $f_cuerpo == null || $f_cuerpo == '' ||
					 $f_alcance == null || $f_alcance == '' ||
					 $f_orden_giro == null || $f_orden_giro == '')
				{
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array();  // Es un array vacio!!! No NULL.
				}
				else
				{
					$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					$informes = NG::expedientes()->obtenerInformes(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, 	// digito
						$f_alcance, 	// cuerpo
						$f_orden_giro, // orden_giro
						null, // orden_informe
						null, // fecha_pedido_informe
						null, // fecha_vuelta_informe
						null, // detalle_informe
						null, // observaciones_informe
						null, // id_usuario
						// Control de consulta
						array('anio', 'tipo', 'numero', 'cuerpo', 'alcance', 'orden_giro', 'orden_informe'), // criterio y sentido de orden (FIJO)
						$p_limitLength, // cantidad de registros (paginación)
						$p_limitStart); // corrimiento de registros (paginación)

					// Consulta de cantidad de Informes del giro respectivo (total)
					$cantidadTotalInformes = NG::expedientes()->obtenerInformesCantidad(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						$f_orden_giro, // orden_giro
						null, // orden_informe
						null, // fecha_pedido_informe
						null, // fecha_vuelta_informe
						null, // detalle_informe
						null, // observaciones_informe
						null  // id_usuario
					);

					// finalmente, verifico si existe el expediente
					$cantidadExpedientes = NG::expedientes()->obtenerExpedientesCantidad($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);
					$resultado['existeExpediente'] = $cantidadExpedientes == 1;

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = $cantidadTotalInformes;
					$resultado['recordsFiltered'] = $cantidadTotalInformes;
					$resultado['data'] = $informes;
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

		// Se recibe la clave del expediente
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		// Se recibe el orden y la fecha de salida del giro, ésta última puede que no la posea aún
		$f_orden_giro = Validator::get()->obtenerDefault($requestParams['f_orden_giro']);
		$f_fecha_salida_giro = Validator::get()->obtenerDefault($requestParams['f_fecha_salida_giro']);

		// Para determinar si estoy agregando o modificando un Informe, guardo una variable de sesion
		SessionController::get()->guardar(SAVE_ACTION, 'agregar');

		// Preparo una instancia de Informe para la vista
		$informe = new Informe();
		$informe->anio = $f_anio;
		$informe->tipo = $f_tipo;
		$informe->numero = $f_numero;
		$informe->cuerpo = $f_cuerpo;
		$informe->alcance = $f_alcance;
		$informe->orden_giro = $f_orden_giro;
		$informe->fecha_pedido_informe = date('Y-m-d');// Por defecto la actual

		$paramVista['informe'] = $informe;

		// Se agrega como parámetro para la vista, la fecha de salida del giro, puede que tenga un valor definido o no aún
		$paramVista['f_fecha_salida_giro'] = $f_fecha_salida_giro;

		// Instancio la vista y la muestro
		$vista = new BEInformesView($paramVista);
		$vista->vistaEdicion();
	}

    /**
	 * Invoca a la vista 'edit' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function edit($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Verifico si me han pasado parametros por url, para un acceso directo a una posición del paginador.
		$f_anio 		 = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo 		 = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero 		 = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo 		 = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance 		 = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		$f_orden_giro 	 = Validator::get()->obtenerDefault($requestParams['f_orden_giro']);
		$f_orden_informe = Validator::get()->obtenerDefault($requestParams['f_orden_informe']);

		// Se mantiene el valor de la fecha de salida del giro, en caso que posea
		$f_fecha_salida_giro = Validator::get()->obtenerDefault($requestParams['f_fecha_salida_giro']);

		try
		{
			// Validación de parámetros de búsqueda
			$f_anio 		 = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo 		 = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero 		 = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo 		 = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance 		 = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden_giro 	 = Validator::get()->validar($f_orden_giro, PATRON_NUMEROS, false, 'Orden del giro');
			$f_orden_informe = Validator::get()->validar($f_orden_informe, PATRON_NUMEROS, false, 'Orden del informe');

			// Se obtiene el informe a editar
			$informe = NG::expedientes()->obtenerInforme($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden_giro, $f_orden_informe);

			if (is_null($informe))
				throw new Exception('Error: inconsistencia de datos al obtener el Informe.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $informe->generarChecksum());

			// Para determinar si estoy agregando o modificando un informe, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Preparo los datos que necesita la vista
			$paramVista['informe'] = $informe;

			$paramVista['f_fecha_salida_giro'] = $f_fecha_salida_giro;

			// Instancio la vista y la muestro
			$vista = new BEInformesView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e)
		{
			// Mensaje de error
			SessionController::get()->guardar($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			$vista = new BEInformesView($paramVista);
			$vista->vistaEdicion();
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
				// Verifico acción de guardado
				if (!SessionController::get()->existe(SAVE_ACTION))
					throw new Exception('No se puede determinar la acci&oacute;n de guardado.');

				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				$jsonData = file_get_contents('php://input');
				$informe = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($informe) == 'Informe'))
					throw new Exception('Se esperaba un objeto de tipo Informe.');

				// Si estoy agregando, el informe no debe existir
				$informeActual = NG::expedientes()->obtenerInforme($informe->anio, $informe->tipo, $informe->numero, $informe->cuerpo, $informe->alcance, $informe->orden_giro, $informe->orden_informe);
				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					if (!is_null($informeActual))
						throw new Exception('No se puede agregar un informe que ya se encuentre ingresado. Verifique la clave del expediente y el n&uacute;mero de orden del giro y del informe.');

					// Si estoy agregando un informe, calculo su orden
					$informe->orden_informe = NG::expedientes()->obtenerNumeroSiguienteInforme($informe->anio, $informe->tipo, $informe->numero, $informe->cuerpo, $informe->alcance, $informe->orden_giro);
				}
				// Si estoy editando un informe...
				else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
					// ... el informe debe existir
					if (is_null($informeActual))
						throw new Exception('No se puede editar un informe inexistente.');
					// ... el checksum no tiene que haber variado
					if ( ! $informeActual->verificarChecksum(SessionController::get()->obtener(CHECKSUM)))
						throw new Exception('El informe editado ya ha sido modificado desde otra terminal.');
				}

				// ***********************************************************
				//  Validación de atributos
				// ***********************************************************
				$informe->orden_informe 		= Validator::get()->sanear($informe->orden_informe);
				$informe->fecha_pedido_informe  = Validator::get()->sanear($informe->fecha_pedido_informe);
				$informe->fecha_vuelta_informe  = Validator::get()->sanear($informe->fecha_vuelta_informe);
				$informe->detalle_informe 		= Validator::get()->sanear($informe->detalle_informe);
				$informe->observaciones_informe = Validator::get()->sanear($informe->observaciones_informe);

				// Actualizo datos de usuario (quien realizó la modificación)
				$usuario = $this->obtenerUsuarioActual();
				$informe->id_usuario = $usuario->id_usuario;

				// Guardo el informe
				$informe = NG::expedientes()->guardarInforme($informe, true); // guardo y recargo

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Informe guardado con &eacute;xito.';
				$resultado['data'] = $informe;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo guardar el informe. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina el Informe determinado por su clave
	 * @param [type] $requestParams [description]
	 */
	public function delete($requestParams)
	{
		// Antes que nada verifico el nivel de acceso. No redirecciono al home en caso de error.
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
				$informe = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($informe) == 'Informe'))
					throw new Exception('Se esperaba un objeto de tipo Informe.');

				// Se elimina el Informe respectivo
				if (! NG::expedientes()->eliminarInforme($informe) ) {
					$resultado['estado'] = 'ERROR';
					$resultado['mensaje'] = sprintf('No se pudo eliminar el Informe del expediente %s-%s-%s-%s-%s.',
						$informe->anio,
						$informe->tipo,
						$informe->numero,
						$informe->cuerpo,
						$informe->alcance);
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				} else {
					$resultado['estado'] = 'OK';
					$resultado['mensaje'] = sprintf('Informe del expediente %s-%s-%s-%s-%s eliminado con &eacute;xito.',
						$informe->anio,
						$informe->tipo,
						$informe->numero,
						$informe->cuerpo,
						$informe->alcance);
					$resultado['data']['informe'] = $informe;
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar el Informe. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}
}
?>
