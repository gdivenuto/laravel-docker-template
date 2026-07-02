<?php
/**
 * Clase de controlador de Actuaciones
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_ACTUACIONES');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_ACTUACIONES');

class BEActuacionesController extends BaseController
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
		$this->accionesPermitidas['wizard'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['anterior'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['siguiente'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['cancelar'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['retomar'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['descartar'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['actuacionpendiente'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['verificaractuacion'] = NIVEL_ACCESO_CONCEJAL;

		// Estas acciones se verifican de forma adicional, para evitar tener
		// que crear un metodo por cada una (y centralizarlas en una única llamada).
		// por defecto, el nivel de acceso requiere NIVEL_ACCESO_OPERADOR
		$this->addAccionesPermitidasActuacion();

		// Se sobreescriben permisos, si aplica:
		$this->accionesPermitidas['expe_elec_pend_confirmar'] = NIVEL_ACCESO_CONCEJAL; // los Concejales pueden confirmar/rechazar documentos Pendientes
		$this->accionesPermitidas['expe_elec_firmar'] = NIVEL_ACCESO_CONCEJAL;// los Concejales pueden Firmar documentos
		$this->accionesPermitidas['expe_elec_rechazar_firma'] = NIVEL_ACCESO_CONCEJAL;// los Concejales pueden Rechazar Firmas de documentos
		$this->accionesPermitidas['expe_elec_enviar_mail'] = NIVEL_ACCESO_CONCEJAL;// los Concejales pueden Enviar documentos
		$this->accionesPermitidas['expe_elec_descargar'] = NIVEL_ACCESO_CONCEJAL;

		// Este es un caso especial: solo aquellos usuarios que tengan 'confirmar_giros = 1'
		// (o en su defecto el secretario actual) pueden ser los que confirman o descartan giros.
		// Para que el secretario, que posee perfil de concejal, pueda confirmar o rechazar
		// giros, se habilitan las acciones pertinentes. El resto de los concejales, si bien pueden
		// ver los giros, nunca podran confirmarlos por no ser 'confirmadores' posibles.
		$this->accionesPermitidas['expediente_confirmar_giros'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['expediente_descartar_giros'] = NIVEL_ACCESO_CONCEJAL;

		// Para deshabilitar temporalmente las convalidaciones de los giros,
		// se le da un nivel ficticio superior al de ADMINISTRADOR
		$this->accionesPermitidas['expediente_convalidar_giros'] = NIVEL_ACCESO_ADMINISTRADOR + 1000;
	}

	/**
	 * Toma los archivos de clases desde el subdirectorio de actuaciones y
	 * genera las 'accionesPermitidas' con permisos de NIVEL_ACCESO_OPERADOR.
	 * Posteriormente es posible cambiar el permiso por defecto.
	 * @param  [type] $file_clase_actuacion [description]
	 * @return [type]                       [description]
	 */
	private function addAccionesPermitidasActuacion()
	{
		foreach (glob(PATH_KRAKEN_LAYER_MODELO_ACTUACIONES.'Actuacion*.php') as $file) {
	        $tipo_actuacion = preg_replace('/^Actuacion([^\.]*)\.php$/i', '$1', basename($file));

	        if ($tipo_actuacion != '') {
	        	$tipo_actuacion = preg_replace('/([A-Z])/', '_$1', $tipo_actuacion);
        		$tipo_actuacion = preg_replace('/^_/', '', $tipo_actuacion);

        		$this->accionesPermitidas[strtolower($tipo_actuacion)] = NIVEL_ACCESO_CONCEJAL;
        		//NIVEL_ACCESO_OPERADOR;
        		// 2023-06-07: Se modificó el nivel de acceso para Concejales, para que puedan utilizar el FIRMADOR ONLINE (subir/componer un pdf para su firma, independiente de un E.E.)
	        }
	    }
	}

	/**
	 * Obtiene la cadena correspondiente a la vista de un determinado paso en
	 * base a su nombre de clase.
	 * @param  PasoActuacion $paso Paso del que se desea obtener la vista.
	 * @return String              Nombre de la clase de la vista asociada.
	 */
	private function obtenerClaseVistaDePaso(PasoActuacion $paso) {
		// El nombre de la vista se genera en base al nombre de clase
		return sprintf('BEPA%sView', preg_replace('/^PasoActuacion(\w+)/', '$1', get_class($paso)));
	}

	/**
	 * Obtiene la actuación almacenada en sesion, llevando a cabo todas las
	 * validaciones pertinentes.
	 * @param  Array     $requestParams Parametros del Request.
	 * @param  boolean   $crear_si_no_existe Si es True, crea la actuación en sesion cuando ésta no existe.
	 * @return Actuacion                Actuacion almacenada en sesion.
	 */
	public function obtenerActuacionSesion($requestParams, $crear_si_no_existe = false)
	{
		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);
		$accion_actuacion = $requestParams['actuacion'];

		$actuacion = null;

		// En base al parametro 'actuacion', verifico que la 'sub-accion' del controlador exista.
		if (! $this->validarAccion($accion_actuacion)) {
			SessionController::get()->guardarError('Tipo de actuación inválido (1).', ERROR_CONTROLLER_GENERICO);
		} else {
			// Verifica los permisos de usuario para dicha accion.
			$this->verificarNivelAccesoUsuario($accion_actuacion);

			// Si no existe la actuación en sesión, la creo
			if (! SessionController::get()->existe('actuacion')) {
				try {
					// Intentamos crear la instancia de actuacion...
					if ($crear_si_no_existe) {
						$actuacion = NG::actuaciones()->nuevaActuacion($accion_actuacion, $requestParams, $this->obtenerUsuarioActual()->id_usuario);

						// Verifico los parametros de la actuacion
						$errores = $actuacion->verificarParametros();
						if (count($errores) > 0) {
							$actuacion = null;
							SessionController::get()->guardarError(join('<br>', $errores), ERROR_CONTROLLER_GENERICO);
						} else {
							// Inicializo la actuacion
							$errores = NG::actuaciones()->inicializarActuacion($actuacion, $this->obtenerUsuarioActual());
							if (count($errores) > 0) {
								$actuacion = null;
								SessionController::get()->guardarError(join('<br>', $errores), ERROR_CONTROLLER_GENERICO);
							}
						}
					}
				} catch (Exception $e) {
					SessionController::get()->guardarError('Tipo de actuación inválido (2): '.$e->getMessage(), ERROR_CONTROLLER_GENERICO);
				}
			} else {
				// Si ya existe, la recupero
				$actuacion = SessionController::get()->obtenerSerializado('actuacion', new Actuacion());

				// Verifico que el tipo de actuacion en sesion sea el mismo solicitado del wizard
				if (get_class($actuacion) != NG::actuaciones()->obtenerClaseDeTipoActuacion($accion_actuacion)) {
					SessionController::get()->guardarError('Tipo de actuación incompatible con la actuación en curso.', ERROR_CONTROLLER_GENERICO);
					$actuacion = null;
				}
			}
		}

		return $actuacion;
	}

	/**
	 * Inicia el 'wizard' para un determinado tipo de actuación.
	 * Es método que conociendo la actuacion y el paso actual, renderiza
	 * la vista correspondiente.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function wizard($requestParams)
	{
		// Antes que nada verifico el nivel de acceso al método general.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Obtengo la actuacion de sesion, y si no existe, la creo.
		// Si falla, vuelvo a la solapa de Expedientes Electrónicos con el error almacenado en sesion.
		$actuacion = $this->obtenerActuacionSesion($requestParams, true);
		if (is_null($actuacion)) {

			//$this->redireccionar('expedientes', 'view');

			// 14/02/2023 XXXX
			// Se retorna a la solapa de Expedientes Electrónicos
			$this->redireccionar(
				'expedienteselec',
				'view',
				[
					'f_anio' => $requestParams['anio'],
					'f_tipo' => $requestParams['tipo'],
					'f_numero' => $requestParams['numero'],
					'f_cuerpo' => $requestParams['cuerpo'],
					'f_alcance' => $requestParams['alcance']
				]
			);
		}

		SessionController::get()->guardarSerializado('actuacion', $actuacion);

		// Obtengo el paso actual
		$paso_actual = $actuacion->obtenerPasoActual();

		// Preparo los parametros de la vista
		$paramVista = $this->generarParametrosVista();
		$paramVista['actuacion'] = $actuacion;
		$paramVista['transac_actuacion'] = NG::transacActuaciones()->obtenerTransacActuacion(
			$paso_actual->id_transaccion,
			$paso_actual->id_paso
		);

		// Lleno el paso con todos los datos que necesita
		NG::pasoActuaciones()->asignarDatosAPasoActuacion($actuacion, $paso_actual, $this->obtenerUsuarioActual());

		// Invoco la vista
		$paso_clase_vista = $this->obtenerClaseVistaDePaso($paso_actual);
		$vista = new $paso_clase_vista($paramVista);
		$vista->vistaPasoActuacion();
	}

	/**
	 * [anterior description]
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function anterior($requestParams)
	{
		// Antes que nada verifico el nivel de acceso al método general.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Obtengo la actuacion de sesion, y si no existe, tengo un error.
		// Si falla, vuelvo al home con el error almacenado en sesion.
		$actuacion = $this->obtenerActuacionSesion($requestParams, false);
		if (is_null($actuacion)) $this->redireccionar('expedientes', 'view');

		// Si ya existe, la recupero/guardo y voy al paso anterior
		$actuacion->pasoAnterior();
		SessionController::get()->guardarSerializado('actuacion', $actuacion);

		$this->redireccionar('actuaciones', 'wizard', ['actuacion' => $actuacion->obtenerTipoDeClaseActuacion()]);
	}

	/**
	 * [siguiente description]
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function siguiente($requestParams)
	{
		// Antes que nada verifico el nivel de acceso al método general.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Obtengo la actuacion de sesion, y si no existe, tengo un error.
		// Si falla, vuelvo al home con el error almacenado en sesion.
		$actuacion = $this->obtenerActuacionSesion($requestParams, false);
		if (is_null($actuacion)) $this->redireccionar('expedientes', 'view');

		// Proceso el paso, porque 'siguiente' significa haber pasado
		// correctamente las validaciones del paso.
		$errores = NG::PasoActuaciones()->procesarPasoActuacion(
			$actuacion,
			$actuacion->obtenerPasoActual(),
			$this->obtenerUsuarioActual(),
			$requestParams
		);

		// Si no hubo errores, verifico si estoy en el ultimo paso
		if (count($errores) == 0) {

			// Si NO estaba en el ultimo paso, hago un 'SIGUIENTE'
			if (!$actuacion->enUltimoPaso()) {

				$actuacion->pasoSiguiente();
				SessionController::get()->guardarSerializado('actuacion', $actuacion);
				$this->redireccionar('actuaciones', 'wizard', ['actuacion' => $actuacion->obtenerTipoDeClaseActuacion()]);

			} else { // Si estoy en el ultimo paso, hago un 'FINALIZAR'

				$errores = NG::actuaciones()->procesarActuacion($actuacion, $this->obtenerUsuarioActual());

				if (count($errores) == 0) {
					// Elimino las transacciones
					NG::transacActuaciones()->eliminarTransacActuaciones($actuacion->id_transaccion);

					$ret = $actuacion->obtenerRutaRetorno();

					// Elimino la actuación en sesion
					SessionController::get()->eliminar('actuacion');

					// Redirecciono
					$this->redireccionar($ret['controlador'], $ret['accion'], $ret['parametros']);
				} else {
					// Si hubo errores, los guardo en sesion y me mantengo en el paso actual.
					SessionController::get()->guardarError(join($errores, '<br>'), ERROR_CONTROLLER_GENERICO);
					$this->redireccionar('actuaciones', 'wizard', ['actuacion' => $actuacion->obtenerTipoDeClaseActuacion()]);
				}

			}
		} else {
			// Si hubo errores, los guardo en sesion y me mantengo en el paso actual.
			SessionController::get()->guardarError(join($errores, '<br>'), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('actuaciones', 'wizard', ['actuacion' => $actuacion->obtenerTipoDeClaseActuacion()]);
		}
	}

	/**
	 * [cancelar description]
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function cancelar($requestParams)
	{
		// Antes que nada verifico el nivel de acceso al método general.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Obtengo la actuacion de sesion, y si no existe, tengo un error.
		// Si falla, vuelvo al home con el error almacenado en sesion.
		$actuacion = $this->obtenerActuacionSesion($requestParams, false);
		if (is_null($actuacion)) $this->redireccionar('expedientes', 'view');

		// Guardo URL de retorno
		$ret = $actuacion->obtenerRutaRetorno();

		// Elimino las transacciones
		NG::transacActuaciones()->eliminarTransacActuaciones($actuacion->id_transaccion);

		// Elimino la actuación en sesion
		SessionController::get()->eliminar('actuacion');

		// Redirecciono
		$this->redireccionar($ret['controlador'], $ret['accion'], $ret['parametros']);
	}

	/**
	 * [retomar description]
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function retomar($requestParams)
	{
		// Antes que nada verifico el nivel de acceso al método general.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Obtengo la actuacion de sesion, y si no existe, vuelvo al home.
		$actuacion = (SessionController::get()->existe('actuacion'))
			? SessionController::get()->obtenerSerializado('actuacion', new Actuacion())
			: null;
		if (is_null($actuacion)) $this->redireccionar('expedientes', 'view');

		// Recupero la actuacion para volver al wizard.
		$this->redireccionar('actuaciones', 'wizard', ['actuacion' => $actuacion->obtenerTipoDeClaseActuacion()]);
	}

	/**
	 * Elimina la actuacion actual (si existe) y la reemplaza con una nueva.
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function descartar($requestParams)
	{
		// Antes que nada verifico el nivel de acceso al método general.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Descarto la actuación actual
		if (SessionController::get()->existe('actuacion')) {
			$actuacion = SessionController::get()->obtenerSerializado('actuacion', new Actuacion());
			NG::transacActuaciones()->eliminarTransacActuaciones($actuacion->id_transaccion);
			SessionController::get()->eliminar('actuacion');
		}

		// Ejecuto la lógica del wizard
		$requestParams['a'] = 'wizard'; // piso la accion
		$this->wizard($requestParams);
	}

	/**
	 * [actuacionpendiente description]
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function actuacionpendiente($requestParams) {
		// Antes que nada verifico el nivel de acceso, pero sin redireccional al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// Obtengo la actuacion actual, si existe.
		$actuacion = (SessionController::get()->existe('actuacion'))
			? SessionController::get()->obtenerSerializado('actuacion', new Actuacion())
			: null;

		$resultado = (is_null($actuacion))
			? ['estado' => 'OK', 'mensaje' => 'No hay actuaciones pendientes.', 'data' => null]
			: ['estado' => 'OK', 'mensaje' => 'Hay una actuación pendiente.', 'data' => [
					'tipo_actuacion' => $actuacion->obtenerTipoDeClaseActuacion(),
					'nombre' => $actuacion->nombre,
					'texto_informativo' => $actuacion->generarTextoInformativo()
				]
			];

		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * [verificaractuacion description]
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function verificaractuacion($requestParams) {
		// Antes que nada verifico el nivel de acceso, pero sin redireccional al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		// Obtengo la actuacion actual, si existe.
		$actuacion = (SessionController::get()->existe('actuacion'))
			? SessionController::get()->obtenerSerializado('actuacion', new Actuacion())
			: null;

		if (is_null($actuacion)) {
			$resultado = ['estado' => 'ERROR', 'mensaje' => 'No hay actuaciones verificables.', 'data' => null];
		} else {
			$errores = NG::actuaciones()->verificarActuacion($actuacion, $this->obtenerUsuarioActual());

			$resultado = (count($errores) == 0)
				? ['estado' => 'OK', 'mensaje' => 'Actuación sin errores.', 'data' => $errores]
				: ['estado' => 'ERROR', 'mensaje' => 'Actuación con errores.', 'data' => $errores];
		}

		echo JsonHelper::get()->serializar($resultado);
	}
}
?>
