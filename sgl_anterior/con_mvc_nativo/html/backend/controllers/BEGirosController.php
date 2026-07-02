<?php
/**
 * Clase de controlador del Giro perteneciente a un expediente específico.
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_GIRO');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_GIRO');

class BEGirosController extends BaseController {
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
		$this->accionesPermitidas['edit'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['add'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['save'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['delete'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['subirordengiro'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['bajarordengiro'] = NIVEL_ACCESO_OPERADOR;
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
			$vista = new BEGirosView($paramVista);
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

				// La clave del expediente es "necesaria". Si no me la proveen, simulo un resultado vacío.
				if ($f_anio == null || $f_anio == '' ||
					$f_tipo == null || $f_tipo == '' ||
					$f_numero == null || $f_numero == '' ||
					$f_cuerpo == null || $f_cuerpo == '' ||
					$f_alcance == null || $f_alcance == '') {
					$resultado['recordsTotal'] = 0;
					$resultado['recordsFiltered'] = 0;
					$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				} else {
					// 26/02/2021 XXXX
					// --------------------
					// Se debe retirar la paginación, ya que para reordenan los Giros deben estar todos en la solapa
					$p_limitStart = 0; // Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
					$p_limitLength = 100; // Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

					$giros = NG::expedientes()->obtenerGiros(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						null, // orden_giro
						null, // comision_tipo
						null, // comision_codigo
						null, // fecha_entrada_giro
						null, // fecha_salida_giro
						null, // dictamen_giro
						null, // observaciones_giro
						null, // id_usuario
						// Control de consulta, criterio y sentido de orden (FIJO)
						array('anio', 'tipo', 'numero', 'cuerpo', 'alcance', 'orden_giro'),
						$p_limitLength, // cantidad de registros (paginación)
						$p_limitStart // corrimiento de registros (paginación)
					);

					// Consulta de cantidad de Giros del expediente respectivo (total)
					$cantidadTotalGiros = NG::expedientes()->obtenerGirosCantidad(
						$f_anio, // anio
						$f_tipo, // tipo
						$f_numero, // numero
						$f_cuerpo, // digito
						$f_alcance, // cuerpo
						null, // orden_giro
						null, // comision_tipo
						null, // comision_codigo
						null, // fecha_entrada_giro
						null, // fecha_salida_giro
						null, // dictamen_giro
						null, // observaciones_giro
						null// id_usuario
					);

					// finalmente, verifico si existe el expediente
					$cantidadExpedientes = NG::expedientes()->obtenerExpedientesCantidad($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);
					$resultado['existeExpediente'] = $cantidadExpedientes == 1;

					// Obtengo, si existe, el último Giro del expediente
					$ultimo_giro = ($cantidadTotalGiros > 0) ? $giros[$cantidadTotalGiros-1] : null;

					// Por cada giro
					foreach ($giros as $p => $g) {
						// Si posee fecha de entrada y NO fecha de salida
						if (!is_null($g->fecha_entrada_giro) && is_null($g->fecha_salida_giro)) {

							$fecha_entrada_ultimo_giro = $this->formatearFecha($g->fecha_entrada_giro);
							$fecha_del_listado = date("d/m/Y");

							// Se calcula el número de días en comisión
							$g->cantidad_dias_en_comision = $this->calcularDiasEnComision(
								$fecha_entrada_ultimo_giro,
								$fecha_del_listado,
								$g->anio,
								$g->tipo,
								$g->numero,
								$g->cuerpo,
								$g->alcance,
								$g->orden_giro);
						} else {
							$g->cantidad_dias_en_comision = -1;
						}

						// Se toma el Giro anterior y el siguiente (si posee)
						// para evaluar si puede reordenarse el Giro actual con ellos
						// -------------------------------------------------------------
						// Si existe el giro anterior
						$giro_anterior = ($p-1 > 0) ? $giros[$p-1] : null;
						// Si existe el giro posterior
						$giro_posterior = (isset($giros[$p+1])) ? $giros[$p+1] : null;

						// Si el Giro no posee fecha de entrada ni de salida
						if (is_null($g->fecha_entrada_giro) && is_null($g->fecha_salida_giro)) {

							// Si es el primer Giro
							if ($g->orden_giro == 1) {

								// No tiene un giro Anterior para intercambiar orden
								$g->ro_puede_reordenarse_con_giro_anterior = 0;

								// Se verifica si puede intercambiar el orden con el giro Siguiente
								if (isset($giro_posterior) &&
									is_null($giro_posterior->fecha_entrada_giro) &&
									is_null($giro_posterior->fecha_salida_giro)) {

									$g->ro_puede_reordenarse_con_giro_siguiente = 1;
								} else {
									$g->ro_puede_reordenarse_con_giro_siguiente = 0;
								}
						 	} // Si es el último Giro
							elseif ( (!is_null($ultimo_giro)) && ($g->orden_giro == $ultimo_giro->orden_giro) ) {

								// Se verifica si puede intercambiar el orden con el giro Anterior
								if (isset($giro_anterior) &&
									is_null($giro_anterior->fecha_entrada_giro) &&
									is_null($giro_anterior->fecha_salida_giro)) {

								 	$g->ro_puede_reordenarse_con_giro_anterior = 1;
								} else {
									$g->ro_puede_reordenarse_con_giro_anterior = 0;
								}

								// No tiene Siguiente giro para intercambiar orden
								$g->ro_puede_reordenarse_con_giro_siguiente = 0;
							}
							else {
								// Se verifica si puede intercambiar el orden con el giro Anterior
								if (isset($giro_anterior) &&
									is_null($giro_anterior->fecha_entrada_giro) &&
									is_null($giro_anterior->fecha_salida_giro)) {

									$g->ro_puede_reordenarse_con_giro_anterior = 1;
								} else {
									$g->ro_puede_reordenarse_con_giro_anterior = 0;
								}

								// Se verifica si puede intercambiar el orden con el giro Siguiente
								if (isset($giro_posterior) &&
									is_null($giro_posterior->fecha_entrada_giro) &&
									is_null($giro_posterior->fecha_salida_giro)) {

									$g->ro_puede_reordenarse_con_giro_siguiente = 1;
								} else {
									$g->ro_puede_reordenarse_con_giro_siguiente = 0;
								}
							}
						}
					}

					// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
					$resultado['recordsTotal'] = $cantidadTotalGiros;
					$resultado['recordsFiltered'] = $cantidadTotalGiros;
					$resultado['data'] = $giros;
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
				throw new Exception('Error: No existe el expediente al que se desea agregarle un giro.');
			}

			// Si el expediente se encuentra agregado a otro expediente, no se permite agregar un giro
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite agregar un Giro, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			// Para determinar si estoy agregando o modificando un Giroo, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'agregar');

			// Preparo una instancia de Giro para la vista
			$giro = new Giro();
			$giro->anio = $f_anio;
			$giro->tipo = $f_tipo;
			$giro->numero = $f_numero;
			$giro->cuerpo = $f_cuerpo;
			$giro->alcance = $f_alcance;
			$giro->fecha_entrada_giro = ''; //date('Y-m-d');

			$paramVista['giro'] = $giro;

			// Se obtienen todas las Comisiones
			$paramVista['listado_comisiones'] = NG::expedientesParam()->obtenerLugares(
				'C', null, null, null, null, null, null, null, null, '1', null,
				array('descripcion_grp'), // 25/02/2021 XXXX
				null, null);

			// Instancio la vista y la muestro
			$vista = new BEGirosView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			// Se vuelve a la grilla con el expediente respectivo
			$this->redireccionar(
				'giros',
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
		$f_orden_giro = Validator::get()->obtenerDefault($requestParams['f_orden_giro']);

		try
		{
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden_giro = Validator::get()->validar($f_orden_giro, PATRON_NUMEROS, false, 'Orden del giro');

			// Obtengo el expediente a verificar
			$expediente = NG::expedientes()->obtenerExpediente($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, false);
			if (is_null($expediente)) {
				throw new Exception('Error: No existe el expediente al cual se desea editar el giro.');
			}
			// Si el expediente se encuentra agregado a otro expediente
			if ( NG::expedientes()->estaAgregadoA($expediente) ) {
				throw new Exception('No se permite editar el giro, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
			}

			// Se obtiene el giro a editar
			$giro = NG::expedientes()->obtenerGiro($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden_giro);
			if (is_null($giro)) {
				throw new Exception('Error: inconsistencia de datos al obtener el Giro.');
			} else {
				// Guardo en sesion su checksum para evitar ediciones simultáneas
				SessionController::get()->guardar(CHECKSUM, $giro->generarChecksum());
			}

			// Para determinar si estoy agregando o modificando una giro, guardo una variable de sesion
			SessionController::get()->guardar(SAVE_ACTION, 'editar');

			// Preparo los datos que necesita la vista
			$paramVista['giro'] = $giro;

			// Se obtienen todas las Comisiones
			$paramVista['listado_comisiones'] = NG::expedientesParam()->obtenerLugares(
				'C', null, null, null, null, null, null, null, null, '1', null,
				array('descripcion_grp'), // 25/02/2021 XXXX
				null, null);

			// Instancio la vista y la muestro
			$vista = new BEGirosView($paramVista);
			$vista->vistaEdicion();
		}
		catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError($e->getMessage(), ERROR_CONTROLLER_GENERICO);
			// Se vuelve a la grilla con el expediente respectivo
			$this->redireccionar(
				'giros',
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
				$giro = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($giro) == 'Giro')) {
					throw new Exception('Se esperaba un objeto de tipo Giro.');
				}

				// Si estoy agregando, el giro no debe existir
				$giroActual = NG::expedientes()->obtenerGiro($giro->anio, $giro->tipo, $giro->numero, $giro->cuerpo, $giro->alcance, $giro->orden_giro);
				if (SessionController::get()->obtener(SAVE_ACTION) == 'agregar') {
					if (!is_null($giroActual)) {
						throw new Exception('No se puede agregar un giro que ya se encuentre ingresado. Verifique la clave del expediente y el n&uacute;mero de orden del giro.');
					}

					// Si estoy agregando un giro, calculo su orden
					$giro->orden_giro = NG::expedientes()->obtenerNumeroSiguienteGiro($giro->anio, $giro->tipo, $giro->numero, $giro->cuerpo, $giro->alcance);
				}
				// Si estoy editando un giro...
				else if (SessionController::get()->obtener(SAVE_ACTION) == 'editar') {
					// ... el giro debe existir
					if (is_null($giroActual)) {
						throw new Exception('No se puede editar un giro inexistente.');
					}

					// ... el checksum no tiene que haber variado
					if (!$giroActual->verificarChecksum(SessionController::get()->obtener(CHECKSUM))) {
						throw new Exception('El giro editado ya ha sido modificado desde otra terminal.');
					}

				}

				// ***********************************************************
				//  Validación de atributos
				// ***********************************************************
				$giro->orden_giro = Validator::get()->sanear($giro->orden_giro);
				$giro->comision_tipo = Validator::get()->sanear($giro->comision_tipo);
				$giro->comision_codigo = Validator::get()->sanear($giro->comision_codigo);
				$giro->fecha_entrada_giro = Validator::get()->sanear($giro->fecha_entrada_giro);
				$giro->fecha_salida_giro = Validator::get()->sanear($giro->fecha_salida_giro);
				$giro->dictamen_giro = Validator::get()->sanear($giro->dictamen_giro);
				$giro->observaciones_giro = Validator::get()->sanear($giro->observaciones_giro);

				// Actualizo datos de usuario (quien realizó la modificación)
				$usuario = $this->obtenerUsuarioActual();
				$giro->id_usuario = $usuario->id_usuario;

				// Si se trata del primer Giro
				if ($giro->orden_giro == 1) {
					// Si no se eligió una fecha en el 1er Giro, se carga la actual
					$giro->fecha_entrada_giro = (is_null($giro->fecha_entrada_giro)) ? date("Y-m-d") : $giro->fecha_entrada_giro;
					// Se guarda
					$giro = NG::expedientes()->guardarGiro($giro, true); // guardo y recargo
				} else {

					// Si el Giro NO tiene fecha de Entrada
					if (is_null($giro->fecha_entrada_giro) && $giro->fecha_entrada_giro == '') {

						// 22/08/2022 XXXX, se optimizó la obtención del Giro anterior
						// ----------------------------------------------------------------
						// Se obtiene el Giro anterior
						$giro_anterior = NG::expedientes()->obtenerGiroAnterior(
							$giro->anio, $giro->tipo, $giro->numero, $giro->cuerpo, $giro->alcance, $giro->orden_giro);

						// Si existe el Giro anterior
						if (!is_null($giro_anterior)) {
							// Se utiliza su fecha de Salida para la fecha de Entrada del Giro a guardar
							$giro->fecha_entrada_giro = $giro_anterior->fecha_salida_giro;
						}
					}

					// Se guarda el giro
					$giro = NG::expedientes()->guardarGiro($giro, true); // guardo y recargo
				}

				// Se verifica el seteo en la fecha de entrada del Giro siguiente
				// (siempre y cuando exista)
				// --------------------------------------------------------------

				// Si el Giro tiene fecha de Salida
				if (!is_null($giro->fecha_salida_giro) && $giro->fecha_salida_giro != '') {

					// 22/08/2022 XXXX, se optimizó la obtención del Giro siguiente
					// -----------------------------------------------------------------
					// Se intenta obtener el Giro siguiente
					$giro_siguiente = NG::expedientes()->obtenerGiroSiguiente(
							$giro->anio, $giro->tipo, $giro->numero, $giro->cuerpo, $giro->alcance, $giro->orden_giro);

					// Si existe el siguiente Giro
					if (!is_null($giro_siguiente)) {
						// Se le asigna a su fecha de Entrada, la fecha de Salida del giro recibido
						$giro_siguiente->fecha_entrada_giro = $giro->fecha_salida_giro;
						// Se guarda
						$giro_siguiente = NG::expedientes()->guardarGiro($giro_siguiente, false);
					}
				}

				// Genero respuesta
				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Giro guardado con &eacute;xito.';
				$resultado['data'] = $giro;

			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo guardar el giro. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se elimina el Giro determinado por su clave
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
				$giro = JsonHelper::get()->deserializar($jsonData);

				if (!(get_class($giro) == 'Giro')) {
					throw new Exception('Se esperaba un objeto de tipo Giro.');
				}

				// Obtengo el expediente antes de eliminar su giro
				$expediente = NG::expedientes()->obtenerExpediente(
					$giro->anio,
					$giro->tipo,
					$giro->numero,
					$giro->cuerpo,
					$giro->alcance,
					false
				);
				if (is_null($expediente)) {
					throw new Exception('Error: No existe el expediente al cual se desea eliminar el giro.');
				}
				// Si el expediente se encuentra agregado a otro expediente
				if ( NG::expedientes()->estaAgregadoA($expediente) ) {
					throw new Exception('No se permite eliminar el giro, el expediente se encuentra bloqueado para su edición, debido a que se encuentra agregado a otro expediente.');
				}

				// 08/02/2022 XXXX, como en la versión anterior del sistema
				// Si el Giro posee Informes
				if (NG::expedientes()->obtenerInformesCantidad(
					$giro->anio, $giro->tipo, $giro->numero, $giro->cuerpo, $giro->alcance, $giro->orden_giro) > 0) {

					$resultado['estado'] = 'WARNING';
					$resultado['mensaje'] = "El Giro posee Informes pendientes.";
					$resultado['data']['giro'] = $giro;
				} else {
					// Se elimina el Giro respectivo
					if (!NG::expedientes()->eliminarGiro($giro)) {
						$resultado['estado'] = 'ERROR';
						$resultado['mensaje'] = sprintf('No se pudo eliminar el Giro del expediente %s-%s-%s-%s-%s.',
							$giro->anio,
							$giro->tipo,
							$giro->numero,
							$giro->cuerpo,
							$giro->alcance);
						$resultado['data'] = ERROR_CONTROLLER_GENERICO;
					} else {
						$resultado['estado'] = 'OK';
						$resultado['mensaje'] = sprintf('Giro del expediente %s-%s-%s-%s-%s eliminado con &eacute;xito.',
							$giro->anio,
							$giro->tipo,
							$giro->numero,
							$giro->cuerpo,
							$giro->alcance);
						$resultado['data']['giro'] = $giro;
					}
				}
			} catch (Exception $e) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = 'No se pudo eliminar el Giro. Causa: ' . $e->getMessage();
				$resultado['data'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		// Para que retorne el JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se sube un Giro determinado, por su Orden
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function subirordengiro($requestParams) {
		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Verifico si me han pasado parametros por url, para un acceso directo a una posición del paginador.
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		$f_orden_giro = Validator::get()->obtenerDefault($requestParams['f_orden_giro']);

		try
		{
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden_giro = Validator::get()->validar($f_orden_giro, PATRON_NUMEROS, false, 'Orden del giro');

			// Se obtiene el giro a subir, para verificar su existencia
			$giro = NG::expedientes()->obtenerGiro($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden_giro);

			if (is_null($giro)) {
				throw new Exception('Error: inconsistencia de datos al obtener el Giro.');
			} else {
				// 22/08/2022 XXXX, se optimizó la obtención del Giro anterior
				// ----------------------------------------------------------------
				// Se obtiene el giro ANTERIOR, para verificar su existencia y modificarle su orden de Giro
				$giro_anterior = NG::expedientes()->obtenerGiroAnterior(
					$giro->anio, $giro->tipo, $giro->numero, $giro->cuerpo, $giro->alcance, $giro->orden_giro);

				if (is_null($giro_anterior)) {
					throw new Exception('Error: inconsistencia de datos al obtener el Giro anterior.');
				} else {
					// Se toman los Ordenes de Giro respectivos para intercambiarlos
					$orden_giro_a_subir = $giro->orden_giro;
					$orden_giro_a_bajar = $giro_anterior->orden_giro;

					// Se crea un Giro Auxiliar, con un Orden de Giro 98
					$giro_anterior->orden_giro = 98;
					$giro_auxiliar_98 = NG::expedientes()->guardarGiro($giro_anterior, true); // guardo y recargo

					// Se crea un Giro Auxiliar, con un Orden de Giro 99
					$giro->orden_giro = 99;
					$giro_auxiliar_99 = NG::expedientes()->guardarGiro($giro, true); // guardo y recargo

					// Se elimina el Giro a subir (ya pasado al giro_auxiliar_99)
					if (NG::expedientes()->eliminarGiro($giro)) {
						// Se le asigna el Orden de giro que se sube
						$giro_auxiliar_98->orden_giro = $orden_giro_a_subir;
						// Se simula el intercambio, utilizando los giros auxiliares
						$giro_arriba_que_quedo = NG::expedientes()->guardarGiro($giro_auxiliar_98, true); // guardo y recargo
					}

					// Se elimina el Giro a bajar (ya pasado al giro_auxiliar_98)
					if (NG::expedientes()->eliminarGiro($giro_anterior)) {
						// Se le asigna el Orden de giro que se baja
						$giro_auxiliar_99->orden_giro = $orden_giro_a_bajar;
						// Se simula el intercambio, utilizando los giros auxiliares
						$giro_abajo_que_quedo = NG::expedientes()->guardarGiro($giro_auxiliar_99, true); // guardo y recargo
					}
				}
			}
		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}
		echo true;
	}

	/**
	 * Se baja un Giro determinado, por su Orden
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function bajarordengiro($requestParams) {
		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Verifico si me han pasado parametros por url, para un acceso directo a una posición del paginador.
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);
		$f_orden_giro = Validator::get()->obtenerDefault($requestParams['f_orden_giro']);

		try
		{
			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden_giro = Validator::get()->validar($f_orden_giro, PATRON_NUMEROS, false, 'Orden del giro');

			// Se obtiene el giro a bajar, para verificar su existencia
			$giro = NG::expedientes()->obtenerGiro($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden_giro);

			if (is_null($giro)) {
				throw new Exception('Error: inconsistencia de datos al obtener el Giro.');
			} else {
				// 22/08/2022 XXXX, se optimizó la obtención del Giro siguiente
				// -----------------------------------------------------------------
				// Se obtiene el giro SIGUIENTE, para verificar su existencia y modificarle su orden de Giro
				$giro_siguiente = NG::expedientes()->obtenerGiroSiguiente(
						$giro->anio, $giro->tipo, $giro->numero, $giro->cuerpo, $giro->alcance, $giro->orden_giro);

				if (is_null($giro_siguiente)) {
					throw new Exception('Error: inconsistencia de datos al obtener el Giro anterior.');
				} else {
					// Se toman los Ordenes de Giro respectivos para intercambiarlos
					$orden_giro_a_bajar = $giro->orden_giro;
					$orden_giro_a_subir = $giro_siguiente->orden_giro;

					// Se crea un Giro Auxiliar, con un Orden de Giro 98
					$giro_siguiente->orden_giro = 98;
					$giro_auxiliar_98 = NG::expedientes()->guardarGiro($giro_siguiente, true); // guardo y recargo

					// Se crea un Giro Auxiliar, con un Orden de Giro 99
					$giro->orden_giro = 99;
					$giro_auxiliar_99 = NG::expedientes()->guardarGiro($giro, true); // guardo y recargo

					// Se elimina el Giro a bajar (ya pasado al giro_auxiliar_99)
					if (NG::expedientes()->eliminarGiro($giro)) {
						// Se le asigna el Orden de giro que se sube
						$giro_auxiliar_98->orden_giro = $orden_giro_a_bajar;
						// Se simula el intercambio, utilizando los giros auxiliares
						$giro_arriba_que_quedo = NG::expedientes()->guardarGiro($giro_auxiliar_98, true); // guardo y recargo
					}

					// Se elimina el Giro a subir (ya pasado al giro_auxiliar_98)
					if (NG::expedientes()->eliminarGiro($giro_siguiente)) {
						// Se le asigna el Orden de giro que se baja
						$giro_auxiliar_99->orden_giro = $orden_giro_a_subir;
						// Se simula el intercambio, utilizando los giros auxiliares
						$giro_abajo_que_quedo = NG::expedientes()->guardarGiro($giro_auxiliar_99, true); // guardo y recargo
					}
				}
			}
		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('home', 'view');
		}

		echo true;
	}

}
?>
