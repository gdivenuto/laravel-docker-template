<?php
/**
 * Clase de controlador del Proyecto perteneciente a un expediente específico.
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_PROYECTO');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_PROYECTO');

class BEProyectosController extends BaseController {
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************

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
		$this->accionesPermitidas['datagrid'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['datagriddocexp'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['datagridreservadosexp'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['edit'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['add'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['save'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['delete'] = NIVEL_ACCESO_OPERADOR;
	}

	/**
	 * Invoca a la vista 'view' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function view($requestParams) {
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

		// Ejecuto la vista
		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance de expediente');

			$paramVista['f_anio'] = $f_anio;
			$paramVista['f_tipo'] = $f_tipo;
			$paramVista['f_numero'] = $f_numero;
			$paramVista['f_cuerpo'] = $f_cuerpo;
			$paramVista['f_alcance'] = $f_alcance;

			// Instancio la vista y la muestro
			$vista = new BEProyectosView($paramVista);
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
	public function datagrid($requestParams) {
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
			$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
			$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
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

				// La clave del expediente es "obligatoria". Si no me la proveen, simulo un resultado vacío.
				if ($f_anio == null || $f_anio == '' ||
					$f_tipo == null || $f_tipo == '' ||
					$f_numero == null || $f_numero == '' ||
					$f_cuerpo == null || $f_cuerpo == '' ||
					$f_alcance == null || $f_alcance == '') {
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				} else {
					$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					$proyectos = NG::expedientes()->obtenerProyectos(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						null, // orden_proyecto
						null, // id_codproyecto
						null, // extracto
						null, // observaciones_proyecto
						null, // id_usuario
						// Control de consulta
						array('anio', 'tipo', 'numero', 'cuerpo', 'alcance', 'orden_proyecto'), // criterio y sentido de orden (FIJO)
						$p_limitLength, // cantidad de registros (paginación)
						$p_limitStart); // corrimiento de registros (paginación)

					// Consulta de cantidad de proyectos del expediente respectivo (total)
					$cantidadTotalProyectos = NG::expedientes()->obtenerProyectosCantidad(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						null, // orden_proyecto
						null, // id_codproyecto
						null, // extracto
						null, // observaciones_proyecto
						null// id_usuario
					);

					// finalmente, verifico si existe el expediente
					$cantidadExpedientes = NG::expedientes()->obtenerExpedientesCantidad($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);
					$resultado['existeExpediente'] = $cantidadExpedientes == 1;

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = $cantidadTotalProyectos;
					$resultado['recordsFiltered'] = $cantidadTotalProyectos;
					$resultado['data'] = $proyectos;
				}
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function datagriddocexp($requestParams) {
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
			$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
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
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');

				// La clave del expediente es "obligatoria". Si no me la proveen, simulo un resultado vacío.
				if (is_null($f_anio) || $f_anio == '' ||
					is_null($f_tipo) || $f_tipo == '' ||
					is_null($f_numero) || $f_numero == '') {
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				} else {
					$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					$expedientes = NG::expedientes()->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
					if (count($expedientes) == 0) {
						throw new Exception('Expediente inexistente.');
					}

					$documentos = NG::expedientes()->obtenerArchivosProyecto($expedientes[0]);

					$digitalizaciones = NG::expedientes()->obtenerArchivosDigitalizacion($expedientes[0]);

					// Si posee documentos
					if (count($documentos) > 0) {
						$auxiliar = [];
						foreach ($documentos as $e) {
							// Si NO es un directorio
							if (!is_dir($e['ruta_completa'] . '/')) {
								// Si no es un auxiliar (utilizado en la sobreescritura)
								if (strpos($e['archivo'], 'temp_') === false) {
									// Así nos quedamos solamente con los archivos
									$auxiliar[] = $e;
								}
							}
						}

						$documentos = $auxiliar;
						// Si quedaron sólo archivos (luego de la asignación previa)
						if (count($documentos) > 0) {
							// Eliminamos el dato de las rutas locales para evitar un 'information leak'
							foreach ($documentos as $d) {
								$d['ruta_completa'] = '';
							}
						}
					}

					// Si posee digitalizaciones
					if (count($digitalizaciones) > 0) {
						foreach ($digitalizaciones as $digi) {
							$digi['ruta_completa'] = '';
							// Si se trata de la Digitalización Cargada
							if ($digi['tipo'] == 'digitalizada') {
								// Se agrega como documento
								$documentos[] = $digi;
							}
						}
					}

					// Si hay documentos
					if (count($documentos) > 0) {
						// Se ordena nuevamente el listado de documentos, por fecha de forma descendente
						foreach ($documentos as $key => $row) {
							$aux[$key] = substr($row['fecha'], 6, 4) . substr($row['fecha'], 3, 2) . substr($row['fecha'], 0, 2);
						}
						array_multisort($aux, SORT_DESC, $documentos);
					}

					// Simulo la paginación
					if (count($documentos) > 0) {
						$paginas = array_chunk($documentos, $p_limitLength);
						// Para obtener la pagina, hago la division entera entre el start y el length
						$resultado['data'] = $paginas[floor($p_limitStart / $p_limitLength)];
					} else {
						$resultado['data'] = array();
					}

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = count($documentos);
					$resultado['recordsFiltered'] = count($documentos);
				}
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * 03/08/2020 XXXX
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function datagridreservadosexp($requestParams) {
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
			$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
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
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');

				// La clave del expediente es "obligatoria". Si no me la proveen, simulo un resultado vacío.
				if (is_null($f_anio) || $f_anio == '' ||
					is_null($f_tipo) || $f_tipo == '' ||
					is_null($f_numero) || $f_numero == '') {
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				} else {
					$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					$expedientes = NG::expedientes()->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
					if (count($expedientes) == 0) {
						throw new Exception('Expediente inexistente.');
					}

					$reservados = NG::expedientes()->obtenerArchivosReservados($expedientes[0]);

					// Eliminamos el dato de las rutas locales para evitar un 'information leak'
					foreach ($reservados as $r) {
						$r['ruta_completa'] = '';
					}

					// Si posee documentos reservados
					if (count($reservados) > 0) {
						// Se ordena nuevamente el listado de documentos, por fecha de forma descendente
						foreach ($reservados as $key => $row) {
							$aux[$key] = substr($row['fecha'], 6, 4) . substr($row['fecha'], 3, 2) . substr($row['fecha'], 0, 2);
						}
						array_multisort($aux, SORT_DESC, $reservados);
					}

					// Simulo la paginación
					if (count($reservados) > 0) {
						$paginas = array_chunk($reservados, $p_limitLength);
						// Para obtener la pagina, hago la division entera entre el start y el length
						$resultado['data'] = $paginas[floor($p_limitStart / $p_limitLength)];
					} else {
						$resultado['data'] = array();
					}

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = count($reservados);
					$resultado['recordsFiltered'] = count($reservados);
				}
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array(); // Es un array vacio!!! No NULL.
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
	public function add($requestParams) {
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
				throw new Exception('Error: No existe el expediente al que se desea agregarle un proyecto.');
			}

			// Si el expediente se encuentra agregado a otro expediente, no se permite agregar un proyecto
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite agregar un Proyecto, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			// Para determinar si estoy agregando o modificando una Proyecto, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'agregar');

			// Preparo una instancia de proyecto para la vista
			$proyecto = new Proyecto();
			$proyecto->anio = $f_anio;
			$proyecto->tipo = $f_tipo;
			$proyecto->numero = $f_numero;
			$proyecto->cuerpo = $f_cuerpo;
			$proyecto->alcance = $f_alcance;

			$paramVista['proyecto'] = $proyecto;

			// Se obtienen los tipos de Proyectos (Codificadoras de Proyectos)
			$paramVista['listado_codproyectos'] = NG::expedientesParam()->obtenerCodproyectos(
				null,
				//null, 10/01/2022 XXXX se retira codigo_proyecto
				null, null, null, '1', null, array('descripcion_proyecto'), null, null);

			// Instancio la vista y la muestro
			$vista = new BEProyectosView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			// Se vuelve a la grilla con el expediente respectivo
			$this->redireccionar(
				'proyectos',
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
	 * Invoca a la vista 'edit' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function edit($requestParams) {
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
		$f_orden_proyecto = Validator::get()->obtenerDefault($requestParams['f_orden_proyecto']);

		try
		{
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden_proyecto = Validator::get()->validar($f_orden_proyecto, PATRON_NUMEROS, false, 'Orden del proyecto');

			// Obtengo el expediente a editar su proyecto
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, false);
			if (is_null($expediente)) {
				throw new Exception('Error: No existe el expediente al cual se desea editar el proyecto.');
			}
			// Si el expediente se encuentra agregado a otro expediente
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite editar el proyecto, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			// Se obtiene el proyecto a editar
			$proyecto = NG::expedientes()->obtenerProyecto($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden_proyecto);
			if (is_null($proyecto)) {
				throw new Exception('Error: inconsistencia de datos al obtener el Proyecto.');
			} else
			// Guardo en sesion su checksum para evitar ediciones simultáneas
			{
				SessionController::get()->guardar(CHECKSUM, $proyecto->generarChecksum());
			}

			// Para determinar si estoy agregando o modificando una proyecto, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Preparo los datos que necesita la vista
			$paramVista['proyecto'] = $proyecto;

			// Se obtienen los tipos de Proyectos (Codificadoras de Proyectos)
			$paramVista['listado_codproyectos'] = NG::expedientesParam()->obtenerCodproyectos(
				null,
				//null, 10/01/2022 XXXX, se retira codigo_proyecto
				null, null, null, '1', null, array('descripcion_proyecto'), null, null);

			// Instancio la vista y la muestro
			$vista = new BEProyectosView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			// Se vuelve a la grilla de proyectos del expediente respectivo
			$this->redireccionar(
				'proyectos',
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
	 * Invoca a la vista 'save' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function save($requestParams) {
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
				if (!SessionController::get()->existe(SAVE_ACTION)) {
					throw new Exception('No se puede determinar la acci&oacute;n de guardado.');
				}

				// Obtengo los datos leyendo directamente el cuerpo de la peticion, porque es un JSON
				$jsonData = file_get_contents('php://input');
				$proyecto = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($proyecto) == 'Proyecto')) {
					throw new Exception('Se esperaba un objeto de tipo Proyecto.');
				}

				// Si estoy agregando, el proyecto no debe existir
				$proyectoActual = NG::expedientes()->obtenerProyecto($proyecto->anio, $proyecto->tipo, $proyecto->numero, $proyecto->cuerpo, $proyecto->alcance, $proyecto->orden_proyecto);
				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					if (!is_null($proyectoActual)) {
						throw new Exception('No se puede agregar un proyecto que ya se encuentre ingresado. Verifique la clave del expediente y el n&uacute;mero de orden del proyecto.');
					}

					// Si estoy agreagando un proyecto, calculo su orden
					$proyecto->orden_proyecto = NG::expedientes()->obtenerNumeroSiguienteProyecto($proyecto->anio, $proyecto->tipo, $proyecto->numero, $proyecto->cuerpo, $proyecto->alcance);
				}
				// Si estoy editando un proyecto...
				else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
					// ... el proyecto debe existir
					if (is_null($proyectoActual)) {
						throw new Exception('No se puede editar un proyecto inexistente.');
					}

					// ... el checksum no tiene que haber variado
					if (!$proyectoActual->verificarChecksum(SessionController::get()->obtener(CHECKSUM))) {
						throw new Exception('El proyecto editado ya ha sido modificado desde otra terminal.');
					}

				}

				// ***********************************************************
				// Validación de atributos
				// ***********************************************************
				$proyecto->orden_proyecto = Validator::get()->sanear($proyecto->orden_proyecto);
				$proyecto->id_codproyecto = Validator::get()->sanear($proyecto->id_codproyecto);

				// 26/08/2020 XXXX
				// Se eliminan los espacios externos (no vacíos ni saltos) del valor del Extracto, posiblemente generados por error de tipeo
				$proyecto->extracto = FormatText::limpiarEspaciosExternos(Validator::get()->sanear($proyecto->extracto));

				$proyecto->observaciones_proyecto = Validator::get()->sanear($proyecto->observaciones_proyecto);

				// Actualizo datos de usuario (quien realizó la modificación)
				$usuario = $this->obtenerUsuarioActual();
				$proyecto->id_usuario = $usuario->id_usuario;

				// Guardo el proyecto
				$proyecto = NG::expedientes()->guardarProyecto($proyecto, true); // guardo y recargo

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Proyecto guardado con &eacute;xito.';
				$resultado['data'] = $proyecto;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo guardar el proyecto. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina el Proyecto determinado por su clave
	 * @param [type] $requestParams [description]
	 */
	public function delete($requestParams) {
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
				$proyecto = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($proyecto) == 'Proyecto')) {
					throw new Exception('Se esperaba un objeto de tipo Proyecto.');
				}

				// Obtengo el expediente antes de eliminar su proyecto
				$expediente = NG::expedientes()->obtenerExpediente(
					$proyecto->anio,
					$proyecto->tipo,
					$proyecto->numero,
					$proyecto->cuerpo,
					$proyecto->alcance,
					false
				);
				if (is_null($expediente)) {
					throw new Exception('Error: No existe el expediente al cual se desea eliminar el proyecto.');
				}
				// Si el expediente se encuentra agregado a otro expediente
				if ( NG::expedientes()->estaAgregadoA($expediente) ) {
					throw new Exception('No se permite eliminar el proyecto, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
				}

				// Se elimina el Proyecto respectivo
				if (!NG::expedientes()->eliminarProyecto($proyecto)) {
					$resultado['estado'] = 'ERROR';
					$resultado['mensaje'] = sprintf('No se pudo eliminar el Proyecto del expediente %s-%s-%s-%s-%s.',
						$proyecto->anio,
						$proyecto->tipo,
						$proyecto->numero,
						$proyecto->cuerpo,
						$proyecto->alcance);
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				} else {
					$resultado['estado'] = 'OK';
					$resultado['mensaje'] = sprintf('Proyecto del expediente %s-%s-%s-%s-%s eliminado con &eacute;xito.',
						$proyecto->anio,
						$proyecto->tipo,
						$proyecto->numero,
						$proyecto->cuerpo,
						$proyecto->alcance);
					$resultado['data']['proyecto'] = $proyecto;
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar el Proyecto. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

}
?>
