<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpeElecEnviarMail.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpeElecEnviarMail extends NGActuacionBase {

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

		// Paso 0: Destinatarios del Expediente Electronico
		$data = json_decode($trans[0]->data);
		$destinatarios = $data->f_destinatarios;

		// Paso 1: Formato de Expediente Electronico
		$data = json_decode($trans[1]->data);
		$formato = $data->f_op_formato;

		// Paso 2: Observaciones
		$data = json_decode($trans[2]->data);
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
			}
		} catch (Exception $e) {
			return [$e->getMessage()];
		}

		if ($archivo == '')
			return [sprintf('Error en transacción %s: no se pudo generar el archivo para descarga.', $actuacion->id_transaccion)];

		// Preparo el cuerpo del mensaje
		$body = sprintf('<p>Por medio del presente se remite una copia de <strong>%s</strong> a través del Sistema de Gestión Legislativa.</p>',
			$this->obtenerEtiquetaExpediente($actuacion)
		);

		// Envío del correo electrónico
		try {
			MailHelper::get()->sendMail([
	           'sender' => [
	                'reply' => ($pUsuario->u_mail) ?? '',
	                'reply_name' => $pUsuario->nombre_usuario
	            ],
	            'recipients' => ['address' => $destinatarios],
	            'message' => [
	                'subject' => sprintf('[Envío de Documentación] %s', $this->obtenerEtiquetaExpediente($actuacion)),
	                'body' => $body,
	                'body_alt' => strip_tags($body),
	            ],
	            'attachments' => [$archivo]
	        ]);
		} catch (Exception $e) {
			return [sprintf('Error al enviar correo electrónico: %s', $e->getMessage())];
		}

		// Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Destinatarios' => join(', ', $destinatarios),
			'Formato' => $formato,
			'Salida' => $archivo,
			'Observaciones' => $observaciones
		];

		return [];
	}
}
?>
