<?php
/**
 * Clase de controlador del Antecedente perteneciente a un expediente específico.
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_ANTECEDENTE');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_ANTECEDENTE');

class BEAntecedentesController extends BaseController
{
	private $id_conexion;
	private $resultado_login;
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
		$this->accionesPermitidas['edit'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['add'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['save'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['delete'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['mostrarDocumentosDE'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['uploaddocumentosexpeddeptoejecutivo'] = NIVEL_ACCESO_OPERADOR;
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
			$vista = new BEAntecedentesView($paramVista);
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

					$antecedentes = NG::expedientes()->obtenerAntecedentes(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						null, // anio_a
						null, // tipo_a
						null, // numero_a
						null, // digito_a
						null, // cuerpo_a
						null, // alcance_a
						null, // cuerpoalcance_a
						null, // anexoalcance_a
						null, // cuerpoanexoalcance_a
						null, // anexo_a
						null, // cuerpoanexo_a
						null, // observaciones_antecedentes
						null, // id_usuario
						// Control de consulta
						array('anio', 'tipo', 'numero', 'cuerpo', 'alcance', 'anio_a', 'tipo_a', 'numero_a', 'digito_a', 'cuerpo_a', 'alcance_a', 'cuerpoalcance_a', 'anexoalcance_a', 'cuerpoanexoalcance_a', 'anexo_a', 'cuerpoanexo_a'), // criterio y sentido de orden (FIJO)
						$p_limitLength, // cantidad de registros (paginación)
						$p_limitStart); // corrimiento de registros (paginación)

						// Consulta de cantidad de Antecedentes del expediente respectivo (total)
					$cantidadTotalAntecedentes = NG::expedientes()->obtenerAntecedentesCantidad(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						null, // anio_a
						null, // tipo_a
						null, // numero_a
						null, // digito_a
						null, // cuerpo_a
						null, // alcance_a
						null, // cuerpoalcance_a
						null, // anexoalcance_a
						null, // cuerpoanexoalcance_a
						null, // anexo_a
						null, // cuerpoanexo_a
						null, // observaciones_antecedentes
						null  // id_usuario
					);

					// Para cada Antecedente, en caso que sea del D.E., se verifica si existe su directorio respectivo
					foreach ($antecedentes as $ant)
						$ant->existe_directorio_expe_depto_ejecutivo = $this->existeDirectorioDE($ant);

					// finalmente, verifico si existe el expediente
					$cantidadExpedientes = NG::expedientes()->obtenerExpedientesCantidad($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);
					$resultado['existeExpediente'] = $cantidadExpedientes == 1;

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = $cantidadTotalAntecedentes;
					$resultado['recordsFiltered'] = $cantidadTotalAntecedentes;
					$resultado['data'] = $antecedentes;
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
	 * Se verifica si existe el directorio del Depto. Ejecutivo para tomar los documentos del antecedente respectivo
	 * Ruta: /var/www/sgl/expedientes/expe-de/ 	(de la versión 1 del SGL)
	 * @param  [Antecedente] $ant 	Instancia de Antecedente
	 * @return [boolean]      		true o false
	 */
	public function existeDirectorioDE($ant)
	{
		if ( is_null($ant) )
			return false;
		else {
			// Se arma el nombre del directorio donde se tomarán los documentos del ejecutivo
			// Se rellena el número, del antecedente, con ceros a la izquierda
			$ruta_documentos_ejecutivo_para_cargar = PATH_KRAKEN_EXPEDIENTES_DEPTO_EJECUTIVO.$ant->anio_a."/".$ant->anio_a."-".substr(1000000+$ant->numero_a, -6)."-".$ant->digito_a;

			// Devuelve true si es del Depto. Ejecutivo y el directorio existe
			return ( $ant->tipo_a == 'D' && is_dir($ruta_documentos_ejecutivo_para_cargar) );
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

			// Obtengo el expediente a editar
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, false);
			if (is_null($expediente)) {
				throw new Exception('Error: No existe el expediente al que se desea agregarle un antecedente.');
			}

			// Si el expediente se encuentra agregado a otro expediente
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite agregar un Antecedente, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			// Para determinar si estoy agregando o modificando un Antecedente, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'agregar');

			// Preparo una instancia de Antecedente para la vista
			$antecedente = new Antecedente();
			$antecedente->anio = $f_anio;
			$antecedente->tipo = $f_tipo;
			$antecedente->numero = $f_numero;
			$antecedente->cuerpo = $f_cuerpo;
			$antecedente->alcance = $f_alcance;

			$antecedente->anio_a = $f_anio;
			$antecedente->tipo_a = 'E';
			$antecedente->numero_a = 0;
			$antecedente->digito_a = '0';
			$antecedente->cuerpo_a = 0;
			$antecedente->alcance_a = 0;
			$antecedente->cuerpoalcance_a = 0;
			$antecedente->anexoalcance_a = 0;
			$antecedente->cuerpoanexoalcance_a = 0;
			$antecedente->anexo_a = 0;
			$antecedente->cuerpoanexo_a = 0;

			$paramVista['antecedente'] = $antecedente;
			$paramVista['bloquear_clave_antecedente'] = false; // voy a permitir editar la clave del antecedente

			// Instancio la vista y la muestro
			$vista = new BEAntecedentesView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			// Se vuelve a la grilla con el expediente respectivo
			$this->redireccionar(
				'antecedentes',
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
	public function edit($requestParams)
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

		$f_anio_a = Validator::get()->obtenerDefault($requestParams['f_anio_a']);
		$f_tipo_a = Validator::get()->obtenerDefault($requestParams['f_tipo_a']);
		$f_numero_a = Validator::get()->obtenerDefault($requestParams['f_numero_a']);
		$f_digito_a = Validator::get()->obtenerDefault($requestParams['f_digito_a']);
		$f_cuerpo_a = Validator::get()->obtenerDefault($requestParams['f_cuerpo_a']);
		$f_alcance_a = Validator::get()->obtenerDefault($requestParams['f_alcance_a']);

		$f_cuerpoalcance_a = Validator::get()->obtenerDefault($requestParams['f_cuerpoalcance_a']);
		$f_anexoalcance_a = Validator::get()->obtenerDefault($requestParams['f_anexoalcance_a']);
		$f_cuerpoanexoalcance_a = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexoalcance_a']);
		$f_anexo_a = Validator::get()->obtenerDefault($requestParams['f_anexo_a']);
		$f_cuerpoanexo_a = Validator::get()->obtenerDefault($requestParams['f_cuerpoanexo_a']);

		try
		{
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

			$f_anio_a = Validator::get()->validar($f_anio_a, PATRON_NUMEROS, false, 'A&ntilde;o del expediente agregado');
			$f_tipo_a = Validator::get()->validar($f_tipo_a, PATRON_TIPO_EXPEDIENTE_ANTECEDENTE, false, 'Tipo de expediente agregado');
			$f_numero_a = Validator::get()->validar($f_numero_a, PATRON_NUMEROS, false, 'N&uacute;mero de expediente agregado');
			$f_digito_a = Validator::get()->validar($f_digito_a, PATRON_ALFANUM, false, 'D&iacute;gito de expediente agregado');
			$f_cuerpo_a = Validator::get()->validar($f_cuerpo_a, PATRON_NUMEROS, false, 'Cuerpo de expediente agregado');
			$f_alcance_a = Validator::get()->validar($f_alcance_a, PATRON_NUMEROS, false, 'Alcance de expediente agregado');

			$f_cuerpoalcance_a = Validator::get()->validar($f_cuerpoalcance_a, PATRON_NUMEROS, false, 'Cuerpo Alcance del expediente agregado');
			$f_anexoalcance_a = Validator::get()->validar($f_anexoalcance_a, PATRON_NUMEROS, false, 'Anexo Alcance del expediente agregado');
			$f_cuerpoanexoalcance_a = Validator::get()->validar($f_cuerpoanexoalcance_a, PATRON_NUMEROS, false, 'Cuerpo Anexo Alcance del expediente agregado');
			$f_anexo_a = Validator::get()->validar($f_anexo_a, PATRON_NUMEROS, false, 'Anexo del expediente agregado');
			$f_cuerpoanexo_a = Validator::get()->validar($f_cuerpoanexo_a, PATRON_NUMEROS, false, 'Cuerpo Anexo del expediente agregado');

			// Obtengo el expediente a editar su antecedente
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, false);
			if (is_null($expediente)) {
				throw new Exception('Error: No existe el expediente al cual se desea editar el antecedente.');
			}
			// Si el expediente se encuentra agregado a otro expediente
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite editar el antecedente, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			// Se obtiene el Antecedente a editar
			$antecedente = NG::expedientes()->obtenerAntecedente(
				$f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance,
				$f_anio_a, $f_tipo_a, $f_numero_a, $f_digito_a, $f_cuerpo_a, $f_alcance_a,
				$f_cuerpoalcance_a, $f_anexoalcance_a, $f_cuerpoanexoalcance_a, $f_anexo_a, $f_cuerpoanexo_a);

			if (is_null($antecedente))
				throw new Exception('Error: inconsistencia de datos al obtener el Antecedente.');
			else
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $antecedente->generarChecksum());

			// Para determinar si estoy agregando o modificando un Antecedente, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Preparo los datos que necesita la vista
			$paramVista['antecedente'] = $antecedente;
			$paramVista['bloquear_clave_antecedente'] = true; // NO voy a permitir editar la clave del antecedente

			// Instancio la vista y la muestro
			$vista = new BEAntecedentesView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			// Se vuelve a la grilla con el expediente respectivo
			$this->redireccionar(
				'antecedentes',
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
				$antecedente = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($antecedente) == 'Antecedente'))
					throw new Exception('Se esperaba un objeto de tipo Antecedente.');

				// Si estoy agregando, el antecedente no debe existir
				$antecedenteActual = NG::expedientes()->obtenerAntecedente($antecedente->anio, $antecedente->tipo, $antecedente->numero, $antecedente->cuerpo, $antecedente->alcance,
					$antecedente->anio_a, $antecedente->tipo_a, $antecedente->numero_a, $antecedente->digito_a, $antecedente->cuerpo_a, $antecedente->alcance_a,
					$antecedente->cuerpoalcance_a, $antecedente->anexoalcance_a, $antecedente->cuerpoanexoalcance_a, $antecedente->anexo_a, $antecedente->cuerpoanexo_a);
				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					if (!is_null($antecedenteActual))
						throw new Exception('No se puede agregar un antecedente que ya se encuentre ingresado. Verifique los datos ingresados.');
				}
				// Si estoy editando una sanción...
				else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
					// ... la sanción debe existir
					if (is_null($antecedenteActual))
						throw new Exception('No se puede editar un antecedente inexistente.');
					// ... el checksum no tiene que haber variado
					if ( ! $antecedenteActual->verificarChecksum(SessionController::get()->obtener(CHECKSUM)))
						throw new Exception('El antecedente editado ya ha sido modificado desde otra terminal.');
				}

				// Si el antecedente es (E)xpediente o (N)ota, ademas debe existir.
				if (($antecedente->tipo_a == 'E') || ($antecedente->tipo_a == 'N')) {
					$cantidadExpedientes = NG::expedientes()->obtenerExpedientesCantidad($antecedente->anio_a,	$antecedente->tipo_a, $antecedente->numero_a, $antecedente->digito_a, $antecedente->cuerpo_a);
					if ($cantidadExpedientes == 0)
						throw new Exception('El antecedente no existe. Verifique los datos ingresados.');
				}

				// ***********************************************************
				//	Validación de atributos
				// ***********************************************************
				$antecedente->anio_a                     = Validator::get()->sanear($antecedente->anio_a);
				$antecedente->tipo_a                     = Validator::get()->sanear($antecedente->tipo_a);
				$antecedente->numero_a                   = Validator::get()->sanear($antecedente->numero_a);
				$antecedente->digito_a                   = Validator::get()->sanear($antecedente->digito_a);
				$antecedente->cuerpo_a                   = Validator::get()->sanear($antecedente->cuerpo_a);
				$antecedente->alcance_a                  = Validator::get()->sanear($antecedente->alcance_a);
				$antecedente->cuerpoalcance_a            = Validator::get()->sanear($antecedente->cuerpoalcance_a);
				$antecedente->anexoalcance_a             = Validator::get()->sanear($antecedente->anexoalcance_a);
				$antecedente->cuerpoanexoalcance_a 		 = Validator::get()->sanear($antecedente->cuerpoanexoalcance_a);
				$antecedente->anexo_a 			 		 = Validator::get()->sanear($antecedente->anexo_a);
				$antecedente->cuerpoanexo_a 			 = Validator::get()->sanear($antecedente->cuerpoanexo_a);
				$antecedente->observaciones_antecedentes = Validator::get()->sanear($antecedente->observaciones_antecedentes);

				// Actualizo datos de usuario (quien realizó la modificación)
				$usuario = $this->obtenerUsuarioActual();
				$antecedente->id_usuario = $usuario->id_usuario;

				// Guardo la sanción
				$antecedente = NG::expedientes()->guardarAntecedente($antecedente, true); // guardo y recargo

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Antecedente guardado con &eacute;xito.';
				$resultado['data'] = $antecedente;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo guardar el antecedente. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina el Antecedente determinado por su clave
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
				$antecedente = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($antecedente) == 'Antecedente'))
					throw new Exception('Se esperaba un objeto de tipo Antecedente.');

				// Obtengo el expediente antes de eliminar su antecedente
				$expediente = NG::expedientes()->obtenerExpediente(
					$antecedente->anio,
					$antecedente->tipo,
					$antecedente->numero,
					$antecedente->cuerpo,
					$antecedente->alcance,
					false
				);
				if (is_null($expediente)) {
					throw new Exception('Error: No existe el expediente al cual se desea eliminar el antecedente.');
				}
				// Si el expediente se encuentra agregado a otro expediente
				if ( NG::expedientes()->estaAgregadoA($expediente) ) {
					throw new Exception('No se permite eliminar el antecedente, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
				}

				// Se elimina el Antecedente respectivo
				if (! NG::expedientes()->eliminarAntecedente($antecedente) ) {
					$resultado['estado'] = 'ERROR';
					$resultado['mensaje'] = sprintf('No se pudo eliminar el Antecedente del expediente %s-%s-%s-%s-%s.',
						$antecedente->anio,
						$antecedente->tipo,
						$antecedente->numero,
						$antecedente->cuerpo,
						$antecedente->alcance);
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				} else {
					$resultado['estado'] = 'OK';
					$resultado['mensaje'] = sprintf('Antecedente del expediente %s-%s-%s-%s-%s eliminado con &eacute;xito.',
						$antecedente->anio,
						$antecedente->tipo,
						$antecedente->numero,
						$antecedente->cuerpo,
						$antecedente->alcance);
					$resultado['data']['antecedente'] = $antecedente;
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar el Proyecto. Causa: '.$e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se muestran los documentos del directorio de un expediente del Depto. Ejecutivo, para cargarlos en el sistema.
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function mostrarDocumentosDE($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$info = $this->sanearConjuntoParametros($requestParams);

		// Clave del expediente
		$info['f_anio']     = Validator::get()->obtenerDefault($info['f_anio']);
		$info['f_tipo']     = Validator::get()->obtenerDefault($info['f_tipo']);
		$info['f_numero']   = Validator::get()->obtenerDefault($info['f_numero']);
		$info['f_cuerpo']   = Validator::get()->obtenerDefault($info['f_cuerpo']);
		$info['f_alcance']  = Validator::get()->obtenerDefault($info['f_alcance']);
		// Clave del expediente que lo antecede
		$info['f_anio_a']   = Validator::get()->obtenerDefault($info['f_anio_a']);
		$info['f_tipo_a']   = Validator::get()->obtenerDefault($info['f_tipo_a']);
		$info['f_numero_a'] = Validator::get()->obtenerDefault($info['f_numero_a']);
		$info['f_digito_a'] = Validator::get()->obtenerDefault($info['f_digito_a']);

	    // Directorio donde se toman los documentos del Ejecutivo
	    $ruta_documentos_ejecutivo_para_cargar = PATH_KRAKEN_EXPEDIENTES_DEPTO_EJECUTIVO.$info['f_anio_a']."/".$info['f_anio_a']."-".substr(1000000+$info['f_numero_a'], -6)."-".$info['f_digito_a']."/";

		// Se obtienen los documentos del expediente del D.E., que lo antecede
		$documentos_depto_ejecutivo = NG::expedientes()->obtenerDocumentosACargarDE($info, $ruta_documentos_ejecutivo_para_cargar);

		$paramVista['info'] = $info;
		$paramVista['documentos_depto_ejecutivo'] = $documentos_depto_ejecutivo;

		// Se instancia la vista y se muestran los documentos obtenidos, para su carga
		$vista = new BEAntecedentesView($paramVista);
		$vista->vistaMostrarDocumentosDE();
	}

	/**
	 * Se reciben los documentos elegidos para cargarlos en el directorio del expediente respectivo
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function uploaddocumentosexpeddeptoejecutivo($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Extensiones que NO se permiten cargar
		$extensiones_no_permitidas = array(".exe", ".pif", ".inf");

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Se recibe la clave del expediente
		$f_anio    = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo    = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero  = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo  = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		// Se recibe la clave del expediente que antecede
		$f_anio_a   = Validator::get()->obtenerDefault($requestParams['f_anio_a']);
		$f_tipo_a   = Validator::get()->obtenerDefault($requestParams['f_tipo_a']);
		$f_numero_a = Validator::get()->obtenerDefault($requestParams['f_numero_a']);
		$f_digito_a = Validator::get()->obtenerDefault($requestParams['f_digito_a']);

		// Ejecuto la vista
		try {
			// Validación de parámetros de búsqueda
			$f_anio    = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
			$f_tipo    = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
			$f_numero  = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
			$f_cuerpo  = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance de expediente');

			// Se verifica que el expediente exista
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, true);
			if (is_null($expediente))
				throw new Exception('Error: El expediente, al cual se desea cargar documentos de un expediente del D.E., no existe.');

			// Los nombres de cada documento elegido para su carga
			$documentos_a_cargar = $_REQUEST['documento_de'];
			//Logger::get()->Log("documentos_a_cargar", $documentos_a_cargar, false);

			// Se prepara la ruta completa a dicho documento
			// Ejemplo: "/var/www/sgl/expedientes/expe-de/2016/2016-013577-8/"
			$ruta_documento_ejecutivo = PATH_KRAKEN_EXPEDIENTES_DEPTO_EJECUTIVO.$f_anio_a."/".$f_anio_a."-".substr(1000000+$f_numero_a, -6)."-".$f_digito_a."/";
			//Logger::get()->Log("ruta_documento_ejecutivo", $ruta_documento_ejecutivo, false);

			$pos = 0;
			// Por cada documento elegido
		    foreach ($documentos_a_cargar as $documento) {

		    	// Si es un documento y su extensión está permitida
				if ( is_file($ruta_documento_ejecutivo.$documento) && ( ! in_array(strtolower(substr($documento, -4)), $extensiones_no_permitidas) ) ) {

					// Nombre codificado AATNNNNN, para el directorio de proyectos del expediente respectivo
					$nombre_codificado_proyectos = sprintf("%02d%s%05d", $f_anio % 100, $f_tipo, $f_numero);

					// Ruta del directorio destino, /proyectos/AAAA/AATNNNNN/
					// determinado por la clave del expediente respectivo
					// donde se cargarán los documentos
					// tomados del directorio del expediente del Depto. Ejecutivo
					$ruta_directorio_destino = PATH_KRAKEN_RESOURCES_PROYECTOS.$f_anio."/".$nombre_codificado_proyectos."/";
					//Logger::get()->Log("ruta_directorio_destino", $ruta_directorio_destino, false);

					// Se abre el directorio del expediente del ejecutivo para tomar sus documentos
					$dir_abierto = opendir($ruta_documento_ejecutivo);

					// Se abre una conexión FTP en el Servidor respectivo
					$this->id_conexion = ftp_connect(FTP_LOCAL_SERVIDOR);

					// Se inicia una sesión FTP con Usuario y Password respectivos
					$this->resultado_login = ftp_login($this->id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

					// Se verifica la conexión
					if ( ( !$this->id_conexion ) || ( !$this->resultado_login ) ) {
						throw new Exception("Error al intentar conectarse o autenticarse en el Servidor FTP.");
					} else {
						// Se cambia al directorio donde se desean cargar los documentos
						// con formato: /var/www/sgl/expedientes/proyectos/AAAA/AATNNNNN/
						ftp_chdir($this->id_conexion, $ruta_directorio_destino);

						// Se toma la ruta del directorio actual
						$dir_actual = ftp_pwd($this->id_conexion);
						// Se retira de la ruta "/home/recursos"
						// para utilizarse a partir de "/var/www/sgl/expedientes/proyectos/..."
						$dir_actual = str_replace("/home/recursos", "", $dir_actual);

						$archivo_remoto = $dir_actual."/".$documento;

						// Se carga el archivo
						if ( ftp_put($this->id_conexion, $archivo_remoto, $ruta_documento_ejecutivo."/".$documento, FTP_BINARY) ) {
							$mensaje = "Se han cargado satisfactoriamente los documentos!";
							$estado = 'OK';
						} else {
							$mensaje = "La transferencia de los documentos ha fallado!";
							$estado = 'Error';
						}

						// Se cierra la conexión FTP
						ftp_close($this->id_conexion);
					}
					// Se cierra el directorio
					closedir($dir_abierto);
				}
				// Se incrementa la posición, para continuar con el siguiente documento, en caso de haber uno
				$pos++;
		    }

			$paramVista['f_anio']    = $f_anio;
			$paramVista['f_tipo']    = $f_tipo;
			$paramVista['f_numero']  = $f_numero;
			$paramVista['f_cuerpo']  = $f_cuerpo;
			$paramVista['f_alcance'] = $f_alcance;

			$paramVista['mensaje']   = $mensaje;
			$paramVista['estado']    = $estado;

			// Regresamos a la grilla de Antecedentes del expediente respectivo
			$vista = new BEAntecedentesView($paramVista);
			$vista->vistaListado();
		}
		catch (Exception $ex) {
			// Mensaje de error (redirecciono para mantener el error de sesion y mostrarlo)
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar(
				'antecedentes',
				'mostrarDocumentosDE',
				array('f_anio'     => $f_anio,
					  'f_tipo' 	   => $f_tipo,
					  'f_numero'   => $f_numero,
					  'f_cuerpo'   => $f_cuerpo,
					  'f_alcance'  => $f_alcance,
					  'f_anio_a'   => $f_anio_a,
					  'f_tipo_a'   => $f_tipo_a,
					  'f_numero_a' => $f_numero_a,
					  'f_digito_a' => $f_digito_a)
			);
		}
	}
}
?>
