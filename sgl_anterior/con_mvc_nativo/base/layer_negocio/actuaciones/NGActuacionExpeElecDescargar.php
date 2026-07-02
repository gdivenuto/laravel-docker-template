<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpeElecDescargar.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpeElecDescargar extends NGActuacionBase {

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
		return $this->guardarUltimoOrdenExpedienteElec($actuacion, $pUsuario);
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
		// Verifico que no hayan habido cambios en el expediente electronico
		// mientras se estaba realizando esta actuación.
		if ($this->huboCambiosEnExpedienteElec($actuacion))
			return [sprintf("El expediente electrónico '%s-%s-%s cpo. %s alc. %s' ha sufrido modificaciones mientras se estaba llevando a cabo esta actuación. Por favor, verifique.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'])];

		return [];
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
		// Obtengo las transacciones de los pasos de la actuacion
		$trans = NG::transacActuaciones()->obtenerTransacActuaciones($actuacion->id_transaccion);

		// Debo tener la misma cantidad de transacciones que de pasos
		if (count($trans) != count($actuacion->pasos))
			return [sprintf('Error en transacción %s: la cantidad de pasos difiere.', $actuacion->id_transaccion)];

		// Paso 0: Formato de Expediente Electronico
		$data = json_decode($trans[0]->data);
		$formato = $data->f_op_formato;

		// Paso 1: Observaciones
		$data = json_decode($trans[1]->data);
		$observaciones = $data->f_texto;

		// Ejecuto la logica de exportación del de expediente electronico
		try {
			switch ($formato) {
				case 'zip':
					$archivo = NG::expedientesElec()->generarExpedienteElecZip(
						$actuacion->parametros['anio'],
						$actuacion->parametros['tipo'],
						$actuacion->parametros['numero'],
						$actuacion->parametros['cuerpo'],
						$actuacion->parametros['alcance']
					);
					break;
				case 'pdf':
					$archivo = NG::expedientesElec()->generarExpedienteElecPdf(
						$actuacion->parametros['anio'],
						$actuacion->parametros['tipo'],
						$actuacion->parametros['numero'],
						$actuacion->parametros['cuerpo'],
						$actuacion->parametros['alcance']
					);
					break;
				case 'pdf_publico':
					$archivo = NG::expedientesElec()->generarExpedientePublicoElecPdf(
						$actuacion->parametros['anio'],
						$actuacion->parametros['tipo'],
						$actuacion->parametros['numero'],
						$actuacion->parametros['cuerpo'],
						$actuacion->parametros['alcance']
					);
					break;
			}
		} catch (Exception $e) {
			return [$e->getMessage()];
		}

		if ($archivo == '')
			return [sprintf('Error en transacción %s: no se pudo generar el archivo para descarga.', $actuacion->id_transaccion)];

		// Guardo como dato de actuación, el archivo generado
		// En el método 'obtenerRutaRetorno' de la actuación,
		// se redirecciona a la descarga.
		$actuacion->datos['archivo_descarga'] = basename($archivo);

		// Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Formato' => $formato,
			'Salida' => $archivo,
			'Observaciones' => $observaciones
		];

		return [];
	}
}
?>
