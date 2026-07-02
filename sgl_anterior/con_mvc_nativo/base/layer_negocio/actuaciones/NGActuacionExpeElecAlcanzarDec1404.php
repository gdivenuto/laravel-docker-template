<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpeElecAlcanzarDec1404.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpeElecAlcanzarDec1404 extends NGActuacionBase {

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
		// En la inicialización, obtengo el documento del preview a partir
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
			return ['No se encuentra el documento asociado al expediente electrónico.'];

		// Actualizo el parámetro del paso 0: Confirmar Dec 1404
		$actuacion->getPaso(0)->opciones['documento_a_confirmar']['ruta_documento_a_confirmar'] = $expe_elec->documento;

		// Preseteo la trasanccion del decreto 1404 con el valor de la entrada del expediente electronico
		try {
			$trans = NG::transacActuaciones()->obtenerTransacActuacion($actuacion->id_transaccion, 0);
			$trans->data = json_encode(['f_op_decreto' => $expe_elec->dec1404]);
			NG::transacActuaciones()->guardarTransacActuacion($trans);
		} catch (Exception $e) {
			return ['Error al actualizar transacción para confirmación de alcance de Art. 11 Decreto 1.404.'];
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

		// Paso 0: Alcanzado por Dec. 1404
		$data = json_decode($trans[0]->data);
		$alcanza_dec1404 = $data->f_op_decreto == 1;

		// Paso 1: Observaciones
		$data = json_decode($trans[1]->data);
		$observaciones = $data->f_texto;

		// Ejecuto la logica de cambio de alcance de expediente electronico
		try {
			NG::expedientesElec()->alcanzarDec1404($expe_elec, $alcanza_dec1404);
		} catch (Exception $e) {
			return [$e->getMessage()];
		}

		// Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Alcanza dec1404' => ($alcanza_dec1404) ? 'si' : 'no',
			'Observaciones' => $observaciones
		];

		return [];
	}

}
?>
