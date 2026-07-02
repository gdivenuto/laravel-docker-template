<?php
/**
 * Clase de controlador para la Carga de Giros,
 * desde la opción Tareas del menú principal
 * utilizando la clave del Expediente ó Nota actual.
 *
 * @author XXXX y XXXX
 */
DEFINE('SAVE_ACTION', 'SAVE_ACTION_GIRO');

class BECargaGirosController extends BaseController {
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
		$this->accionesPermitidas['verificarexistencia'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['save'] = NIVEL_ACCESO_OPERADOR;
	}

	/**
	 * Se verifica la existencia de Giros para el Expediente ó Nota actual
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function verificarexistencia($requestParams) {
		// Antes que nada verifico el nivel de acceso
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parámetros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

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
					// Se obtienen todas las Comisiones
					$paramVista['listado_comisiones'] = NG::expedientesParam()->obtenerLugares(
						'C', null, null, null, null, null, null, null, null, '1', null, array('descripcion_grp'), null, null);

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

					// Si posee Giros, se muestra el formulario para agregar un nuevo Giro
					if ($cantidadTotalGiros > 0) {
						// Para determinar si estoy agregando o modificando un Giro, guardo una variable de sesion
						SessionController::get()->guardar(SAVE_ACTION, 'agregar');

						// Preparo una instancia de Giro para la vista
						$giro = new Giro();
						$giro->anio = $f_anio;
						$giro->tipo = $f_tipo;
						$giro->numero = $f_numero;
						$giro->cuerpo = $f_cuerpo;
						$giro->alcance = $f_alcance;
						$giro->fecha_entrada_giro = date('Y-m-d');

						$paramVista['giro'] = $giro;

						// Instancio la vista y la muestro
						$vista = new BEGirosView($paramVista);
						$vista->vistaEdicion();
					} else {
						// si NO posee Giros, se realiza la carga grupal (no más de seis)

						// Se pasa la clave del expediente/nota a la vista
						$paramVista['f_anio'] = $f_anio;
						$paramVista['f_tipo'] = $f_tipo;
						$paramVista['f_numero'] = $f_numero;
						$paramVista['f_cuerpo'] = $f_cuerpo;
						$paramVista['f_alcance'] = $f_alcance;

						// Instanciamos la vista y mostramos el formulario de edición para la carga grupal
						$vista = new BECargaGirosView($paramVista);
						$vista->vistaEdicion();
					}
				}
			} catch (Exception $ex) {
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array(); // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}
	}

	/**
	 * Se guardan los giros elegidos para el expediente ó nota respectivo
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function save($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);
		//Logger::get()->Log("requestParams", $requestParams, false);

		// Se recibe la clave
		$f_anio = Validator::get()->obtenerDefault($requestParams['f_anio']);
		$f_tipo = Validator::get()->obtenerDefault($requestParams['f_tipo']);
		$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
		$f_cuerpo = Validator::get()->obtenerDefault($requestParams['f_cuerpo']);
		$f_alcance = Validator::get()->obtenerDefault($requestParams['f_alcance']);

		// 28/07/2021 XXXX
		// Se recibe la marca si se considera la PPC o no
		$f_ppc = Validator::get()->obtenerDefault($requestParams['f_ppc'], 0);

		try
		{
			// Validación de la clave del expediente/nota
			$f_anio = Validator::get()->validar($f_anio, PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($f_tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($f_cuerpo, PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($f_alcance, PATRON_NUMEROS, false, 'Alcance de expediente');
			// 28/07/2021 XXXX
			// Se valida si la marca de PPC es numérica o no
			$f_ppc = Validator::get()->validar($f_ppc, PATRON_NUMEROS, false, 'Consideraci&oacute;n de PPC');
			//Logger::get()->Log("f_ppc", $f_ppc, false);

			$orden_giro = 1;
			// Se recorren los giros seleccionados, por lo menos uno
			for ($nro_giro = 1; $nro_giro < 7; $nro_giro++) {

				// Si se eligió un giro determinado para cargar
				if ($requestParams['f_comision_' . $nro_giro] != '0') {

					$comision_tipo = 'C';
					$comision_codigo = $requestParams['f_comision_' . $nro_giro];

					// 28/07/2021 XXXX
					// Si es una Nota
					if ($f_tipo == 'N') {
						// Si es el primer giro, se asigna la fecha elegida, sino un valor nulo
						$fecha_entrada_giro = ($nro_giro == 1) ? $this->formatearFechaNegocio($requestParams['v_fecha_primer_giro']) : null;
					} else {
						// Si es el primer giro
						if ($nro_giro == 1) {

							$estados = NG::expedientes()->obtenerEstados($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance);
							// Nos quedamos con el último Estado
							$ultimo_estado = $estados[count($estados) - 1];
							//Logger::get()->Log("ultimo_estado", $ultimo_estado, false);

							// Si el Estado actual es 90 (EN PPC)
							if ($ultimo_estado->id_codestado == 90) {
								// NO se le asigna la fecha aún
								$fecha_entrada_giro = null;
							} else {
								// Se asigna la fecha elegida
								$fecha_entrada_giro = $this->formatearFechaNegocio($requestParams['v_fecha_primer_giro']);
							}
						} else {
							$fecha_entrada_giro = null; // NO se le asigna la fecha aún
						}
					}

					// Se asigna la observación, sino un valor nulo
					$observaciones_giro = Validator::get()->sanear($requestParams['f_observaciones_giro_' . $nro_giro]);

					// Preparo una instancia de Giro con la info recibida
					$giro = new Giro();
					$giro->anio = $f_anio;
					$giro->tipo = $f_tipo;
					$giro->numero = $f_numero;
					$giro->cuerpo = $f_cuerpo;
					$giro->alcance = $f_alcance;
					$giro->orden_giro = $orden_giro;
					$giro->comision_tipo = $comision_tipo;
					$giro->comision_codigo = $comision_codigo;
					$giro->fecha_entrada_giro = $fecha_entrada_giro;
					$giro->fecha_salida_giro = null;
					$giro->dictamen_giro = null;
					$giro->observaciones_giro = $observaciones_giro;

					// Actualizo datos de usuario (quien realizó la modificación)
					$usuario = $this->obtenerUsuarioActual();
					$giro->id_usuario = $usuario->id_usuario;

					// 28/07/2021 XXXX
					// Se define la marca, para modificar la Observación del Estado, del 1er Giro
					// sólo si es una Nota y se considera para PPC
					$para_ppc = ($f_tipo == 'N' && $f_ppc == 1) ? '1' : '0';

					// Guardo el giro
					// si es el primero para dicho expediente, se registra el estado 3 "Girado a comisión"
					// (está definido dentro del método guardarGiro)
					$giro = NG::expedientes()->guardarGiro(
						$giro,
						true,
						$para_ppc,
						$this->formatearFechaNegocio($requestParams['v_fecha_primer_giro']) // antes se utilizaba la fecha del Giro creado
					); // guardo y recargo

					$orden_giro++; // Se incrementa para un siguiente giro
				}
			}

			$paramVista['f_anio'] = $f_anio;
			$paramVista['f_tipo'] = $f_tipo;
			$paramVista['f_numero'] = $f_numero;
			$paramVista['f_cuerpo'] = $f_cuerpo;
			$paramVista['f_alcance'] = $f_alcance;

		} catch (Exception $e) {
			// Mensaje de error
			SessionController::get()->guardarError('No se ha realizado la carga inicial de giros. Causa: ' . $e->getMessage(), ERROR_CONTROLLER_GENERICO);
		}

		// Instancio la vista y la muestro
		$vista = new BEGirosView($paramVista);
		$vista->vistaListado();
	}
}
?>
