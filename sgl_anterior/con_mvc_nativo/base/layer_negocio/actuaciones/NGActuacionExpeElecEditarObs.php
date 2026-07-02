<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpeElecEditarObs.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpeElecEditarObs extends NGActuacionBase {

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct() {
		parent::__construct();
	}

	// ------------------------------------------------------------------------
	// ---- Inicialización de Actuaciones -------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Tomar una actuacion y ejecuta su funcion de inicialización.
	 * @param  Actuacion    $actuacion Actuacion a procesar
	 * @param  Usuario|null $pUsuario  Usuario asociado a la ejecución de la actuacion.
	 * @return Array                Lista de errores encontrados durante el procesamiento.
	 */
	public function inicializarActuacion(Actuacion $actuacion, Usuario $pUsuario = null)
	{
		// En la inicialización, obtengo la observación a partir
		// de la entrada del expediente electronico.
		try {
			$expe_elec = NG::expedientesElec()->obtenerExpedienteElec(
				$actuacion->parametros['anio'],
				$actuacion->parametros['tipo'],
				$actuacion->parametros['numero'],
				$actuacion->parametros['cuerpo'],
				$actuacion->parametros['alcance'],
				$actuacion->parametros['orden']
			);
		} catch (Exception $e) {
			return [sprintf('Error al inicializar actuación: %s', $e->getMessage())];
		}

		if (is_null($expe_elec))
			return ['No se encuentran las observaciones en la entrada del expediente electrónico.'];

		// Preseteo la trasanccion de la observación con el valor de la entrada del expediente electronico
		try {
			$trans = NG::transacActuaciones()->obtenerTransacActuacion($actuacion->id_transaccion, 0);
			$trans->data = json_encode(['f_texto' => $expe_elec->observaciones]);
			NG::transacActuaciones()->guardarTransacActuacion($trans);
		} catch (Exception $e) {
			return ['Error al actualizar transacción para la edición de las observaciones.'];
		}

		return [];
	}

	// ------------------------------------------------------------------------
	// ---- Verificación de Actuaciones ---------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Tomar una actuacion y ejecuta su funcion de verificación.
	 * @param  Actuacion    $actuacion Actuacion a verificar.
	 * @param  Usuario|null $pUsuario  Usuario asociado a la verificación de la actuacion.
	 * @return Array                   Lista de errores encontrados durante la verificación.
	 */
	public function verificarActuacion(Actuacion $actuacion, Usuario $pUsuario = null)
	{
		return parent::verificarActuacion($actuacion, $pUsuario);
	}

	// ------------------------------------------------------------------------
	// ---- Procesamiento de Actuaciones --------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Toma una actuacion y ejecuta su funcion de validacion y procesamiento en base
	 * a las transacciones generadas durante el wizard.
	 * @param  Actuacion    $actuacion Actuacion a procesar
	 * @param  Usuario|null $pUsuario  Usuario asociado a la ejecución de la actuacion.
	 * @return Array                Lista de errores encontrados durante el procesamiento.
	 */
	public function procesarActuacion(Actuacion $actuacion, Usuario $pUsuario = null)
	{
		// Extra: obtengo el documento del expediente electronico
		$expe_elec = NG::expedientesElec()->obtenerExpedienteElec(
			$actuacion->parametros['anio'],
			$actuacion->parametros['tipo'],
			$actuacion->parametros['numero'],
			$actuacion->parametros['cuerpo'],
			$actuacion->parametros['alcance'],
			$actuacion->parametros['orden']
		);
		if (is_null($expe_elec))
			return [sprintf("El documento del expediente electronico '%s-%s-%s cpo. %s alc. %s orden %s' no existe.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'], $actuacion->parametros['orden'])];

		// Obtengo las transacciones de los pasos de la actuacion
		$trans = NG::transacActuaciones()->obtenerTransacActuaciones($actuacion->id_transaccion);

		// Debo tener la misma cantidad de transacciones que de pasos
		if (count($trans) != count($actuacion->pasos))
			return [sprintf('Error en transacción %s: la cantidad de pasos difiere.', $actuacion->id_transaccion)];

		// Paso 0: Observaciones
		$data = json_decode($trans[0]->data);
		$observaciones = $data->f_texto;

		// Ejecuto la logica de cambio de las observaciones del expediente electronico
		try {
			NG::expedientesElec()->editarObservaciones($expe_elec, $observaciones);
		} catch (Exception $e) {
			return [$e->getMessage()];
		}

		// Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Observaciones' => $observaciones
		];

		return [];
	}
}
?>
