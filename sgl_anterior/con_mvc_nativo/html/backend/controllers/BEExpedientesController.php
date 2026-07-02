<?php
/**
 * Clase de controlador del Home del subsitio.
 *
 * @author Kaleb
 */
DEFINE('CHECKSUM', 'CHECKSUM_EXPEDIENTE');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_EXPEDIENTE');

class BEExpedientesController extends BaseController {
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

		$this->registros_por_pagina = 16;

		// Nombre del módulo al que corresponde el controlador
		$this->nombreModulo = 'EXPEDIENTES';

		// 05/06/2020 XXXX  Se modificó los accesos para Periodista (Consulta Web del SGL)
		// --------------------------------------------------------------------------------------
		// Determino las acciones válidas y su nivel de acceso mínimo requerido
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_PERIODISTA; //NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['edit'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['add'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['save'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['verificarexpedientesenppcvencidos'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['obtenerexpediente'] = NIVEL_ACCESO_PERIODISTA; //NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['busquedasimpledatagrid'] = NIVEL_ACCESO_PERIODISTA; //NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['generarpdffichaexpediente'] = NIVEL_ACCESO_PERIODISTA; //NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['generarpdfetiquetaexpediente'] = NIVEL_ACCESO_PERIODISTA; //NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['obtenersiguienteexpediente'] = NIVEL_ACCESO_PERIODISTA; //NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['obtenerexpedienteanterior'] = NIVEL_ACCESO_PERIODISTA; //NIVEL_ACCESO_OPERADOR;

		$this->accionesPermitidas['delete'] = NIVEL_ACCESO_ADMINISTRADOR;
	}

	/**
	 * Se obtienen todos los listados necesarios para cargar los combos de la búsqueda avanzada
	 * @return array Conjunto de listados (array de arrays)
	 */
	private function cargarCombosCodificadoras() {
		$resultado = array();

		// Se obtienen todos los Iniciadores
		$resultado['listado_iniciadores'] = NG::expedientesParam()->obtenerLugares(
			null, null, null, null, null, null, null, null, null, '1', null, array('descripcion_grp'), null, null);

		// 26/02/2021 XXXX
		// se reemplaza: array('G', 'V')
		// por: null
		// en el 1er parámetro, para que devuelva TODOS los lugares
		// para utilizar el Tipo 'C' además de 'V' y 'G'
		//Se obtienen todos los Autores
		$resultado['listado_codautores'] = NG::expedientesParam()->obtenerLugares(
			null, null, null, null, null, null, null, null, null, '1', null, array('descripcion_grp'), null, null);

		// Se obtienen todas las Categorias
		$resultado['listado_categorias'] = NG::expedientesParam()->obtenerCodcategorias(
			null,
			//null, (al retirarse codigo_categoria 07/01/2022 XXXX)
			null, null, null, '1', null, array('descripcion_categoria'), null, null);

		// Se obtienen todas las Comisiones
		$resultado['listado_comisiones'] = NG::expedientesParam()->obtenerLugares(
			'C', null, null, null, null, null, null, null, null, '1', null, array('descripcion_grp'), null, null);

		// Se obtienen todos los Estados
		$resultado['listado_codestados'] = NG::expedientesParam()->obtenerCodestados(
			null,
			//null, (al retirarse codigo_estado 07/01/2022 XXXX)
			null, null, null, null, '1', null, null, array('nombre_estado'), null, null);

		// Se obtienen todos los Temas
		$resultado['listado_codtemas'] = NG::expedientesParam()->obtenerCodtemas(
			null,
			//null, (al retirarse codigo_tema 07/01/2022 XXXX)
			null, null, null, '1', null, array('descripcion_tema'), null, null);

		return $resultado;
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

		try {
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

			// Obtengo el expediente a editar (instancia completa)
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, true);
			if (is_null($expediente)) {
				throw new Exception('Error: El expediente a editar no existe.');
			} else {
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $expediente->generarChecksum());
			}

			// Para determinar si estoy agregando o modificando una expediente, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Preparo los datos que necesita la vista
			$paramVista['bloquear_clave_expediente'] = true; // no voy a permitir editar la clave del expediente

			// 03/06/2020 por XXXX
			NG::expedientes()->determinarEstadoDigitalizacion($expediente);

			$paramVista['expediente'] = $expediente;
			$paramVista = array_merge($paramVista, $this->cargarCombosCodificadoras());

			// Instancio la vista y la muestro
			$vista = new BEExpedientesView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			// Se vuelve a la grilla con el expediente respectivo
			$this->redireccionar(
				'expedientes',
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
	public function add($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Para determinar si estoy agregando o modificando una expediente, guardo una variable de sesion
		SessionController::get()->guardar(SAVE_ACTION, 'agregar');

		// Preparo una instancia de expediente para la vista
		$expediente = new Expediente();
		$expediente->anio = date("Y");
		$expediente->tipo = 'E';
		$expediente->numero = 0;
		$expediente->cuerpo = 0;
		$expediente->alcance = 0;
		$expediente->fecha_entrada_expe = date('Y-m-d');

		$paramVista['bloquear_clave_expediente'] = false; // Voy a permitir editar la clave del expediente
		$paramVista['expediente'] = $expediente;
		$paramVista = array_merge($paramVista, $this->cargarCombosCodificadoras());

		// Instancio la vista y la muestro
		$vista = new BEExpedientesView($paramVista);
		$vista->vistaEdicion();
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
				$expediente = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($expediente) == 'Expediente')) {
					throw new Exception('Se esperaba un objeto de tipo Expediente.');
				}

				// Si es un alta, se calcula el numero automáticamente (si corresponde)
				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					// En este caso particular si el número es un -1, obtengo el valor automáticamente
					if ($expediente->numero == -1) {
						$expediente->numero = NG::expedientes()->obtenerNumeroSiguienteExpediente($expediente->anio, $expediente->tipo);
					}

					//else
					//	$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
				} else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
					//	$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
				} else {
					throw new Exception('Acci&oacute;n de guardado inv&aacute;lida.');
				}

				// Si estoy agregando, el expediente no debe existir
				$expedienteActual = NG::expedientes()->obtenerExpediente($expediente->anio, $expediente->tipo, $expediente->numero, $expediente->cuerpo, $expediente->alcance, true);
				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					if (!is_null($expedienteActual)) {
						throw new Exception('No se puede agregar un expediente que ya se encuentre ingresado. Verifique el a&ntilde;o, tipo, n&uacute;mero, cuerpo y alcance del expediente.');
					}

				}
				// Si estoy editando un expediente...
				else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
					// ... el expediente debe existir
					if (is_null($expedienteActual)) {
						throw new Exception('No se puede editar un expediente inexistente.');
					}

					// ... el checksum no tiene que haber variado
					if (!$expedienteActual->verificarChecksum(SessionController::get()->obtener(CHECKSUM))) {
						throw new Exception('El expediente editado ya ha sido modificado desde otra terminal.');
					}

				}

				// El expediente debe tener al menos un tema y un autor
				if ((count($expediente->getTemas()) == 0) || (count($expediente->getAutores()) == 0)) {
					throw new Exception('El expediente debe poseer al menos un Tema y un Autor.');
				}

				// Actualizo datos de usuario (quien realizó la modificación)
				$usuario = $this->obtenerUsuarioActual();
				$expediente->id_usuario = $usuario->id_usuario;

				// Si el expediente tiene asignado todos los campos de "agregado a", el expediente debe existir.
				// Si alguno de los campos de "Agregado a..." ha sido editado, entonces verifico la existencia
				if (!is_null($expediente->agregado_anio) &&
					!is_null($expediente->agregado_tipo) &&
					!is_null($expediente->agregado_numero) &&
					!is_null($expediente->agregado_cuerpo) &&
					!is_null($expediente->agregado_alcance)) {

					$expAgregadoA = NG::expedientes()->obtenerExpediente($expediente->agregado_anio, $expediente->agregado_tipo, $expediente->agregado_numero, $expediente->agregado_cuerpo, $expediente->agregado_alcance);
					if (is_null($expAgregadoA)) {
						throw new Exception("El expediente 'Agregado a' no existe; Verifique el a&ntilde;o, tipo, n&uacute;mero, cuerpo y alcance del expediente 'Agregado a'.");
					}

					// Si hay un expediente agregado, a dicho expediente tengo que asignarle el expediente actual como antecedente.
					// Esto puedo hacerlo antes de guardar el expediente debido a que la tabla de antecedentes tiene una relacion
					// opcional con la de expedientes (puedo hacer la relación aunque el expediente no exista todavía).
					$antecedente = new Antecedente();
					$antecedente->anio = $expediente->agregado_anio;
					$antecedente->tipo = $expediente->agregado_tipo;
					$antecedente->numero = $expediente->agregado_numero;
					$antecedente->cuerpo = $expediente->agregado_cuerpo;
					$antecedente->alcance = $expediente->agregado_alcance;

					$antecedente->anio_a = $expediente->anio;
					$antecedente->tipo_a = $expediente->tipo;
					$antecedente->numero_a = $expediente->numero;
					$antecedente->cuerpo_a = $expediente->cuerpo;
					$antecedente->alcance_a = $expediente->alcance;
					$antecedente->digito_a = '0';

					$antecedente->observaciones_antecedentes = 'AUTOMATICO';
					$antecedente->id_usuario = $usuario->id_usuario;

					NG::expedientes()->guardarAntecedente($antecedente);
				} else {
					// Para asegurar la integridad de datos, si no esta 'agregado a' completo, seteo todo en null.
					$expediente->agregado_anio = null;
					$expediente->agregado_tipo = null;
					$expediente->agregado_numero = null;
					$expediente->agregado_cuerpo = null;
					$expediente->agregado_alcance = null;
				}

				// ***********************************************************
				// Validación de atributos
				// ***********************************************************
				$expediente->anio = Validator::get()->sanear($expediente->anio);
				$expediente->tipo = Validator::get()->sanear($expediente->tipo);
				$expediente->numero = Validator::get()->sanear($expediente->numero);
				$expediente->cuerpo = Validator::get()->sanear($expediente->cuerpo);
				$expediente->alcance = Validator::get()->sanear($expediente->alcance);
				$expediente->iniciador_tipo = Validator::get()->sanear($expediente->iniciador_tipo);
				$expediente->iniciador_codigo = Validator::get()->sanear($expediente->iniciador_codigo);
				$expediente->iniciador_bloque_tipo = Validator::get()->sanear($expediente->iniciador_bloque_tipo);
				$expediente->iniciador_bloque_codigo = Validator::get()->sanear($expediente->iniciador_bloque_codigo);
				$expediente->agregado_anio = Validator::get()->sanear($expediente->agregado_anio);
				$expediente->agregado_tipo = Validator::get()->sanear($expediente->agregado_tipo);
				$expediente->agregado_numero = Validator::get()->sanear($expediente->agregado_numero);
				$expediente->agregado_cuerpo = Validator::get()->sanear($expediente->agregado_cuerpo);
				$expediente->agregado_alcance = Validator::get()->sanear($expediente->agregado_alcance);
				$expediente->id_codcategoria = Validator::get()->sanear($expediente->id_codcategoria);
				$expediente->fecha_entrada_expe = Validator::get()->sanear($expediente->fecha_entrada_expe);
				$expediente->caratula = Validator::get()->sanear($expediente->caratula);
				$expediente->observaciones_expe = Validator::get()->sanear($expediente->observaciones_expe);
				$expediente->marca_comision = Validator::get()->sanear($expediente->marca_comision);
				// Agregado el 03/06/2020 XXXX
				$expediente->digi_completa = Validator::get()->sanear($expediente->digi_completa);

				foreach ($expediente->getAntecedentes() as $item) {
					if ($item->getInstanceState() != IS_STABLE) {
						$item->id_usuario = $usuario->id_usuario;
					}
				}

				foreach ($expediente->getAutores() as $item) {
					if ($item->getInstanceState() != IS_STABLE) {
						$item->id_usuario = $usuario->id_usuario;
					}
				}

				foreach ($expediente->getEstados() as $item) {
					if ($item->getInstanceState() != IS_STABLE) {
						$item->id_usuario = $usuario->id_usuario;
					}
				}

				foreach ($expediente->getGiros() as $item) {
					if ($item->getInstanceState() != IS_STABLE) {
						$item->id_usuario = $usuario->id_usuario;
					}
				}

				foreach ($expediente->getProyectos() as $item) {
					if ($item->getInstanceState() != IS_STABLE) {
						$item->id_usuario = $usuario->id_usuario;
					}
				}

				foreach ($expediente->getSanciones() as $item) {
					if ($item->getInstanceState() != IS_STABLE) {
						$item->id_usuario = $usuario->id_usuario;
					}
				}

				foreach ($expediente->getTemas() as $item) {
					if ($item->getInstanceState() != IS_STABLE) {
						$item->id_usuario = $usuario->id_usuario;
					}
				}

				// Guardo el expediente
				$expediente = NG::expedientes()->guardarExpediente($expediente, true); // guardo y recargo

				// En caso de un expediente nuevo
				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					// Se genera el estado "Registrado" automáticamente
					$estado = new Estado();
					$estado->id_codestado = ID_CODESTADO_NUEVO_EXPEDIENTE; // @ config.php
					$estado->anio = $expediente->anio;
					$estado->tipo = $expediente->tipo;
					$estado->numero = $expediente->numero;
					$estado->cuerpo = $expediente->cuerpo;
					$estado->alcance = $expediente->alcance;
					$estado->fecha_estado = $expediente->fecha_entrada_expe;
					$estado->orden_estado = 1;
					$estado->observaciones_estado = 'AUTOMATICO';
					$estado->id_usuario = $usuario->id_usuario;

					NG::expedientes()->guardarEstado($estado);

					// 23/07/2020 XXXX
					// Una vez guardado el Expediente y registrado el estado Registrado.
					// Si el expediente tiene asignado todos los campos de "agregado a"
					if (!is_null($expediente->agregado_anio) &&
						!is_null($expediente->agregado_tipo) &&
						!is_null($expediente->agregado_numero) &&
						!is_null($expediente->agregado_cuerpo) &&
						!is_null($expediente->agregado_alcance)) {

						// Se genera el estado "Agregado a..." automáticamente
						$estado = new Estado();
						$estado->id_codestado = ID_CODESTADO_AGREGADO_A; // @ config.php
						$estado->anio = $expediente->anio;
						$estado->tipo = $expediente->tipo;
						$estado->numero = $expediente->numero;
						$estado->cuerpo = $expediente->cuerpo;
						$estado->alcance = $expediente->alcance;
						$estado->fecha_estado = $expediente->fecha_entrada_expe;
						$estado->orden_estado = 2; // siguiente estado ya que el orden 1 es para el estado Registrado
						$estado->observaciones_estado = 'AUTOMATICO';
						$estado->id_usuario = $usuario->id_usuario;

						NG::expedientes()->guardarEstado($estado);
					}
				}
				// 23/07/2020 XXXX
				// En caso de la edición de un expediente
				elseif (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {

					// Si el expediente tiene asignado todos los campos de "agregado a"
					if (!is_null($expediente->agregado_anio) &&
						!is_null($expediente->agregado_tipo) &&
						!is_null($expediente->agregado_numero) &&
						!is_null($expediente->agregado_cuerpo) &&
						!is_null($expediente->agregado_alcance)) {

						// Se verifica primero si ya está registrado el estado "Agregado a..." para dicho expediente
						$estado_agregado_a = NG::expedientes()->obtenerEstados(
							$expediente->anio,
							$expediente->tipo,
							$expediente->numero,
							$expediente->cuerpo,
							$expediente->alcance,
							null, // fecha_estado
							null, // orden_estado
							ID_CODESTADO_AGREGADO_A);

						// Si NO está registrado el Estado "Agregado a..."
						if (count($estado_agregado_a) == 0) {

							// Se obtiene el siguiente número de Orden de estado del expediente
							$siguiente_orden = NG::expedientes()->obtenerNumeroSiguienteEstado(
								$expediente->anio,
								$expediente->tipo,
								$expediente->numero,
								$expediente->cuerpo,
								$expediente->alcance,
								null// no desconoce la fecha del último estado registrado
							);

							// Se genera el estado "Agregado a..." automáticamente
							$estado = new Estado();
							$estado->id_codestado = ID_CODESTADO_AGREGADO_A; // @ config.php
							$estado->anio = $expediente->anio;
							$estado->tipo = $expediente->tipo;
							$estado->numero = $expediente->numero;
							$estado->cuerpo = $expediente->cuerpo;
							$estado->alcance = $expediente->alcance;
							$estado->fecha_estado = date("Y-m-d");
							$estado->orden_estado = $siguiente_orden;
							$estado->observaciones_estado = 'AUTOMATICO';
							$estado->id_usuario = $usuario->id_usuario;

							NG::expedientes()->guardarEstado($estado);
						}
					}
				}

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Expediente guardado con &eacute;xito.';
				$resultado['data'] = $expediente;

				// Una ultima validación: el 'agregado a' debe ser distinto del expediente cargado (referencia circular)
				// Lo dejo guardar, pero le aviso...
				if (($expediente->anio == $expediente->agregado_anio) &&
					($expediente->tipo == $expediente->agregado_tipo) &&
					($expediente->numero == $expediente->agregado_numero) &&
					($expediente->cuerpo == $expediente->agregado_cuerpo) &&
					($expediente->alcance == $expediente->agregado_alcance)) {

					$resultado['estado'] = 'WARNING';
					$resultado['mensaje'] = "ADVERTENCIA: el expediente 'Agregado a' es id&eacute;ntico al n&uacute;mero de expediente guardado; esto hace que el expediente se agregue a s&iacute; mismo, posiblemente provocando errores a futuro. De todas formas, el expediente se ha guardado con &eacute;xito.";
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo guardar el expediente. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}
		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Esta acción del controlador permite obtener los datos completos de un único expediente.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function obtenerexpediente($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = "ERROR";
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');
		} else {
			// Parametros de filtro
			$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
			$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
			$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
			$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

			try {
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
				$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
				$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

				$expediente = NG::expedientes()->obtenerExpediente(
					$f_anio, // anio
					$f_tipo, // tipo
					$f_numero, // numero
					$f_cuerpo, // cuerpo
					$f_alcance, // alcance
					true); // Instancias completas

				if (!is_null($expediente)) {
					NG::expedientes()->determinarEstadoProyecto($expediente);
					// Agregado el 27/02/2019 por XXXX
					NG::expedientes()->determinarEstadoDigitalizacion($expediente);

					// 21/09/2020 XXXX
					// Sólo los Perfiles 1 y 2 pueden ver los extractos cuando el Tema es el 36 (OFICIO JUDICIAL)
					// Si el perfil es 3 o 4 los extractos NO se muestran (se limpian)
					// --------------------------------------------------------------------------------------------
					if (isset($_SESSION['perfil2']) && ($_SESSION['perfil2'] == 3 || $_SESSION['perfil2'] == 4)) {
						$tiene_tema_36 = 0;
						// Se recorren los Temas del expediente/nota
						foreach ($expediente->getTemas() as $item) {
							// Si posee el Tema 36
							if ($item->id_codtema == 36) {
								$tiene_tema_36 = 1; // Se marca que lo tiene
								break;
							}
						}
						// Si posee un Tema 36
						if ($tiene_tema_36) {
							// Se "limpian" los Extractos de cada Proyecto del expediente
							foreach ($expediente->getProyectos() as $item) {
								$item->extracto = '';
							}
						}
					}

					// preparo el resultado (wrap en un array con los resultados de la consulta)
					$resultado['estado'] = "OK";
					$resultado['mensaje'] = "Expediente consultado con &eacute;xito.";
					$resultado['data'] = $expediente;
				} else {
					$resultado['estado'] = "WARNING";
					$resultado['mensaje'] = "Expediente no encontrado.";
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				}
			} catch (Exception $ex) {
				$resultado['estado'] = "ERROR";
				$resultado['mensaje'] = $ex->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}
		// Devuelvo el resultado serializado en JSON, segun las especificaciones de DataTables
		echo JsonHelper::get()->serializar($resultado);
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

		// Si me falta algun parámetro, voy a buscar el expediente por defecto.
		if (is_null($f_anio) || is_null($f_tipo) || is_null($f_numero) || is_null($f_cuerpo) || is_null($f_alcance)) {
			$expedienteUltimo = NG::expedientes()->obtenerExpedienteUltimo();
			$f_anio = $expedienteUltimo->anio;
			$f_tipo = $expedienteUltimo->tipo;
			$f_numero = $expedienteUltimo->numero;
			$f_cuerpo = $expedienteUltimo->cuerpo;
			$f_alcance = $expedienteUltimo->alcance;
		}

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
			$vista = new BEExpedientesView($paramVista);
			$vista->vistaBusquedaSimple();

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
	public function busquedasimpledatagrid($requestParams) {
		// Version customizada del "serverside processing" para datatables
		// https://www.datatables.net/manual/server-side
		//
		// En este caso particular, no se utiliza la paginación estándar.

		// Antes que nada verifico el nivel de acceso, pero sin redireccional al home en caso de error.
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

			$f_instancias_completas = (isset($requestParams['f_instancias_completas'])) ? $requestParams['f_instancias_completas'] : false;

			// Seteo el valor de control "draw"
			$p_draw = $requestParams['draw'];

			// Obtengo datos para la paginación
			$p_limitStart = (trim($requestParams['start']) == '') ? null : trim($requestParams['start']);

			// lo seteamos para que siempre muestre 15 registros  por página
			$p_limitLength = $this->registros_por_pagina; //(trim($requestParams['length']) == '') ? null : trim($requestParams['length']);

			// Saneo todos los parametros
			$p_draw = $this->sanearParametro($p_draw);
			$p_limitStart = $this->sanearParametro($p_limitStart);
			$p_limitLength = $this->sanearParametro($p_limitLength);

			// Realizo la consulta y preparo el resultado
			$resultado = array();
			$resultado['draw'] = $p_draw;
			try
			{
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, true, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
				$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, true, 'Cuerpo de expediente');
				$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, true, 'Alcance de expediente');

				$f_instancias_completas = false; // ($f_instancias_completas == 1); fuerzo solo la cabecera del expediente

				// Inicio
				$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
				// Cantidad maxima de resultados permitidos
				$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

				// Obtenemos los expedientes de la página Actual
				$expedientes = NG::expedientes()->obtenerExpedientesPagina(
					$f_anio, // anio
					$f_tipo, // tipo
					$f_numero, // numero
					$f_cuerpo, // cuerpo
					$f_alcance, // alcance
					MENOR_IGUAL_A,
					$f_instancias_completas,
					// Control de consulta
					array('full_key desc'), // criterio y sentido de orden (FIJO)
					$p_limitLength, // Cantidad de registros a obtener (10, 25, 50, 100 por ejemplo)
					0// A partir de qué registro se obtendrá el resultado
				);

				// Para los resultados devueltos, obtengo el estado del proyecto y de la digitalización del expediente
				foreach ($expedientes as $e) {
					NG::expedientes()->determinarEstadoProyecto($e);
					// Agregado el 27/02/2019 por XXXX
					NG::expedientes()->determinarEstadoDigitalizacion($e);
				}

				$primer_expediente = null;
				$expediente_anterior = null;
				$expediente_siguiente = null;
				$ultimo_expediente = null;

				// Si hay expedientes que mostrar
				if (count($expedientes) > 0) {

					// *** 09/08/2017 XXXX
					// agregamos y modificamos esta parte para obtener
					// la Primera, Anterior, Siguiente y Última página de resultados

					$expedientes_primeros = NG::expedientes()->obtenerExpedientes(
						// Parametros
						null, null, null, null, null, // la clave no es necesaria
						null, // piniciador_tipo
						null, // piniciador_codigo
						null, // piniciador_bloque_tipo
						null, // piniciador_bloque_codigo
						null, // pagregado_anio
						null, // pagregado_tipo
						null, // pagregado_numero
						null, // pagregado_cuerpo
						null, // pagregado_alcance
						null, // pid_codcategoria
						null, // pfecha_entrada_expe
						null, // pcaratula
						null, // pobservaciones_expe
						null, // pmarca_comision
						null, // pid_usuario
						$f_instancias_completas,
						// Control de consulta
						null, // criterio y sentido de orden (FIJO)
						$p_limitLength, // Cantidad de registros a obtener (10, 25, 50, 100 por ejemplo)
						0// A partir de qué registro se obtendrá el resultado
					);
					// Se toma el último expediente (cantidad deseada por página menos uno), de la primer página
					$expediente_primero = $expedientes_primeros[$p_limitLength - 1];

					// Obtenemos el Último expediente de la página Anterior
					// utilizando la clave del PRIMER expediente de la página actual
					$expedientes_anteriores = NG::expedientes()->obtenerExpedientesPagina(
						$expedientes[0]->anio, // anio
						$expedientes[0]->tipo, // tipo
						$expedientes[0]->numero, // numero
						$expedientes[0]->cuerpo, // cuerpo
						$expedientes[0]->alcance, // alcance
						MENOR_IGUAL_A, // MAYOR_A
						$f_instancias_completas,
						// Control de consulta
						array('full_key desc'), // criterio y sentido de orden (FIJO)
						1, // para obtener el primero
						$p_limitLength
					);
					// Nos quedamos con el último elemento encontrado, de los "$p_limitLength" (10, 25, 50, 100) encontrados de la página anterior
					// EN REALIDAD SIEMPRE DEVUELVE UNO SOLO
					$expediente_anterior = (count($expedientes_anteriores) > 0) ? $expedientes_anteriores[count($expedientes_anteriores) - 1] : null;

					// Obtenemos el primer expediente de la página Siguiente
					// utilizando la clave del ÚLTIMO expediente de la página actual
					$expedientes_siguientes = NG::expedientes()->obtenerExpedientesPagina(
						$expedientes[0]->anio, // anio
						$expedientes[0]->tipo, // tipo
						$expedientes[0]->numero, // numero
						$expedientes[0]->cuerpo, // cuerpo
						$expedientes[0]->alcance, // alcance
						MAYOR_A, // MENOR_IGUAL_A,
						$f_instancias_completas,
						// Control de consulta
						array('full_key asc'), // criterio y sentido de orden (FIJO)
						$p_limitLength,
						$p_limitLength - 1
					);
					$expediente_siguiente = (count($expedientes_siguientes) > 0) ? $expedientes_siguientes[0] : null;

					// Se obtiene el ULTIMO expediente
					$expediente_ultimo = NG::expedientes()->obtenerExpedienteUltimo();
				}

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = count($expedientes);
				$resultado['recordsFiltered'] = count($expedientes);
				// agrego más datos custom
				$resultado['expedientePrimerPagina'] = $expediente_primero;
				$resultado['expedientePaginaAnterior'] = $expediente_anterior;
				$resultado['expedientePaginaActual'] = (count($expedientes) > 0) ? $expedientes[0] : null;
				$resultado['expedientePaginaSiguiente'] = $expediente_siguiente;
				$resultado['expedienteUltimaPagina'] = $expediente_ultimo;

				// *** 07/08/2017, XXXX *******
				// se invierte el resultado de expedientes, para mostrar el último cargado en la vista
				$resultado['data'] = array_reverse($expedientes);
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array();
				$resultado['expedientePrimerPagina'] = null;
				$resultado['expedientePaginaAnterior'] = null;
				$resultado['expedientePaginaActual'] = null;
				$resultado['expedientePaginaSiguiente'] = null;
				$resultado['expedienteUltimaPagina'] = null;
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * 15/09/2020 XXXX
	 * Se obtiene el siguiente expediente de uno determinado por su clave
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function obtenersiguienteexpediente($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = "ERROR";
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');
		} else {
			// Parametros de filtro
			$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
			$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
			$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
			$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

			try {
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
				$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
				$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

				$expediente_siguiente = NG::expedientes()->obtenerExpedientesPagina(
					$f_anio, // anio
					$f_tipo, // tipo
					$f_numero, // numero
					$f_cuerpo, // cuerpo
					$f_alcance, // alcance
					MAYOR_A, // mayor a
					false,
					// Control de consulta
					array('full_key asc'), // criterio y sentido de orden (FIJO)
					1,
					0
				);

				if (!is_null($expediente_siguiente)) {
					// preparo el resultado (wrap en un array con los resultados de la consulta)
					$resultado['estado'] = "OK";
					$resultado['mensaje'] = "Siguiente expediente obtenido con &eacute;xito.";
					$resultado['data'] = $expediente_siguiente;
				} else {
					$resultado['estado'] = "WARNING";
					$resultado['mensaje'] = "Expediente no encontrado.";
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				}
			} catch (Exception $ex) {
				$resultado['estado'] = "ERROR";
				$resultado['mensaje'] = $ex->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}
		// Devuelvo el resultado serializado en JSON, segun las especificaciones de DataTables
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * 15/09/2020 XXXX
	 * Se obtiene el expediente anterior de uno determinado por su clave
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function obtenerexpedienteanterior($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = "ERROR";
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');
		} else {
			// Parametros de filtro
			$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
			$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
			$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
			$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

			try {
				// Verifico aquellos parametros que puedan ser inyectables con SQL
				$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
				$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
				$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
				$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

				$expediente_anterior = NG::expedientes()->obtenerExpedientesPagina(
					$f_anio, // anio
					$f_tipo, // tipo
					$f_numero, // numero
					$f_cuerpo, // cuerpo
					$f_alcance, // alcance
					MENOR_IGUAL_A, // menor igual a
					false,
					// Control de consulta
					array('full_key desc'), // criterio y sentido de orden (FIJO)
					1,
					1
				);

				if (!is_null($expediente_anterior)) {
					// preparo el resultado (wrap en un array con los resultados de la consulta)
					$resultado['estado'] = "OK";
					$resultado['mensaje'] = "Expediente anterior obtenido con &eacute;xito.";
					$resultado['data'] = $expediente_anterior;
				} else {
					$resultado['estado'] = "WARNING";
					$resultado['mensaje'] = "Expediente no encontrado.";
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				}
			} catch (Exception $ex) {
				$resultado['estado'] = "ERROR";
				$resultado['mensaje'] = $ex->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}
		// Devuelvo el resultado serializado en JSON, segun las especificaciones de DataTables
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina el Expediente determinado por su clave
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
				$expediente = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($expediente) == 'Expediente')) {
					throw new Exception('Se esperaba un objeto de tipo Expediente.');
				}

				// 13/01/2023 XXXX
				// Si el expediente se encuentra agregado a otro expediente, no se permite su eliminación
				if ( NG::expedientes()->estaAgregadoA($expediente) ) {
					throw new Exception('El expediente se encuentra bloqueado para su eliminación, debido a que se encuentra agregado a otro expediente.');
				}

				// Antes de eliminar, recargo el expediente para obtenerlo completo
				// (Desde la interfase solo recibo la cabecera del expediente, sin sus datos "hijos")
				$expediente = NG::expedientes()->obtenerExpediente(
					$expediente->anio,
					$expediente->tipo,
					$expediente->numero,
					$expediente->cuerpo,
					$expediente->alcance,
					true// instancia completa
				);

				// Se elimina el Expediente respectivo (internamente se eliminan sus dependencias previamente)
				if (!NG::expedientes()->eliminarExpediente($expediente)) {
					$resultado['estado'] = 'ERROR';
					$resultado['mensaje'] = sprintf('No se pudo eliminar el expediente %s-%s-%s-%s-%s.',
						$expediente->anio,
						$expediente->tipo,
						$expediente->numero,
						$expediente->cuerpo,
						$expediente->alcance);
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				} else {
					$resultado['estado'] = 'OK';
					$resultado['mensaje'] = sprintf('Expediente %s-%s-%s-%s-%s eliminado con &eacute;xito.',
						$expediente->anio,
						$expediente->tipo,
						$expediente->numero,
						$expediente->cuerpo,
						$expediente->alcance);
					$resultado['data']['expediente'] = $expediente;

					// Ademas, obtengo el ultimo expediente ingresado para saber donde posicionar la grilla
					$resultado['data']['expediente_siguiente'] = NG::expedientes()->obtenerExpedienteUltimo(true);
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar el expediente. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Esta acción del controlador permite generar la ficha de un expediente, determinado por su clave, en formato PDF.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function generarpdffichaexpediente($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// Parametros de filtro
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

		try {
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

			$expediente = NG::expedientes()->obtenerExpediente(
				$f_anio, // anio
				$f_tipo, // tipo
				$f_numero, // numero
				$f_cuerpo, // cuerpo
				$f_alcance, // alcance
				true); // Instancias completas

			// 21/09/2020 XXXX
			// NO se muestran cuando el Tema es el 36 (OFICIO JUDICIAL)
			// -----------------------------------------------------------------
			$tiene_tema_36 = 0;
			// Se recorren los Temas del expediente
			foreach ($expediente->getTemas() as $item) {
				// Si posee el Tema 36
				if ($item->id_codtema == 36) {
					$tiene_tema_36 = 1; // Se marca que lo tiene
					break;
				}
			}
			// Si posee un Tema 36
			if ($tiene_tema_36) {
				// Se "limpian" los Extractos de cada Proyecto del expediente
				foreach ($expediente->getProyectos() as $item) {
					$item->extracto = '';
				}
			}

			$paramVista['info_expediente'] = $expediente;

			// Instancio la vista y la muestro
			$vista = new BEReportesView($paramVista);
			$vista->vistaReportePdfFichaExpediente();
		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}

	/**
	 * Esta acción del controlador permite generar la etiqueta de un expediente, determinado por su clave, en formato PDF.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function generarpdfetiquetaexpediente($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// Parametros de filtro
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

		try {
			// Verifico aquellos parametros que puedan ser inyectables con SQL
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');

			$expediente = NG::expedientes()->obtenerExpediente(
				$f_anio, // anio
				$f_tipo, // tipo
				$f_numero, // numero
				$f_cuerpo, // cuerpo
				$f_alcance, // alcance
				true); // Instancias completas

			// 21/09/2020 XXXX
			// NO se muestran cuando el Tema es el 36 (OFICIO JUDICIAL)
			// -----------------------------------------------------------------
			$tiene_tema_36 = 0;
			// Se recorren los Temas del expediente
			foreach ($expediente->getTemas() as $item) {
				// Si posee el Tema 36
				if ($item->id_codtema == 36) {
					$tiene_tema_36 = 1; // Se marca que lo tiene
					break;
				}
			}
			// Si posee un Tema 36
			if ($tiene_tema_36) {
				// Se "limpian" los Extractos de cada Proyecto del expediente
				foreach ($expediente->getProyectos() as $item) {
					$item->extracto = '';
				}
			}

			$paramVista['info_expediente'] = $expediente;

			// Instancio la vista y la muestro
			$vista = new BEReportesView($paramVista);
			$vista->vistaReportePdfEtiquetaExpediente();

		} catch (Exception $ex) {
			// Si falla, vuelve al home y elimina la busqueda en sesión
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
	}

	/**
	 * Esta acción del controlador permite obtener los expedientes con estado 90 (en PPC) cuya fecha haya superado los 30 días.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 */
	public function verificarexpedientesenppcvencidos() {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['estado'] = "ERROR";
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = SessionController::get()->obtener('NUMERO_ERROR');
		} else {

			try {
				$expedientes = NG::expedientes()->obtenerExpedientesEnPpcVencidos();
				//Logger::get()->Log("expedientes_ppc_vencidos", $expedientes, false);

				if (!is_null($expedientes)) {
					// Se prepara el resultado (wrap en un array con los resultados de la consulta)
					$resultado['estado'] = "OK";
					$resultado['mensaje'] = "Expedientes verificados con &eacute;xito.";
					$resultado['data'] = $expedientes;
				} else {
					$resultado['estado'] = "WARNING";
					$resultado['mensaje'] = "Expedientes no encontrados.";
					$resultado['data'] = ERROR_CONTROLLER_GENERICO;
				}
			} catch (Exception $ex) {
				$resultado['estado'] = "ERROR";
				$resultado['mensaje'] = $ex->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}
		// Devuelvo el resultado serializado en JSON, segun las especificaciones de DataTables
		echo JsonHelper::get()->serializar($resultado);
	}

}
?>
