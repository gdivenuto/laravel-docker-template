<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpeElecRechazarFirma.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpeElecRechazarFirma extends NGActuacionBase {

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
		// El rechazo de firma no requiere validación de expediente Agregado
		// debido a que no se modifica el contenido del expediente electronico.
		//$errores = $this->esExpedienteAgregado($actuacion, $expediente);
		//if (count($errores) > 0)
		// 	return $errores;

		// Hago todas las verificaciones de la solicitud de firma del expediente electronico
		$firma_expe_elec = NG::firmasExpedienteElec()->obtenerFirmaExpedienteElec(
			$actuacion->parametros['anio'],
			$actuacion->parametros['tipo'],
			$actuacion->parametros['numero'],
			$actuacion->parametros['cuerpo'],
			$actuacion->parametros['alcance'],
			$actuacion->parametros['orden'],
			$actuacion->parametros['id_firma']
		);
		if (is_null($firma_expe_elec))
			return [sprintf("La solicitud de firma para el documento del expediente electronico '%s-%s-%s cpo. %s alc. %s orden %s' no existe.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'], $actuacion->parametros['orden'])];
		if ($firma_expe_elec->estado != 'pendiente')
			return [sprintf("La solicitud de firma para el documento del expediente electronico '%s-%s-%s cpo. %s alc. %s orden %s' no se encuentra pendiente.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'], $actuacion->parametros['orden'])];
		if ($firma_expe_elec->id_usuario != $pUsuario->id_usuario)
			return [sprintf("El usuario actual no tiene permisos para rechazar la firma del documento del expediente electronico '%s-%s-%s cpo. %s alc. %s orden %s'.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'], $actuacion->parametros['orden'])];

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

		// Actualizo el parámetro del paso 0: Rechazar Firma
		$actuacion->getPaso(0)->opciones['documento_a_rechazar']['ruta_documento_a_rechazar'] = $expe_elec->documento;

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
		// Extra: obtengo la solicitud de firma del expediente electronico
		$firma_expe_elec = NG::firmasExpedienteElec()->obtenerFirmaExpedienteElec(
			$actuacion->parametros['anio'],
			$actuacion->parametros['tipo'],
			$actuacion->parametros['numero'],
			$actuacion->parametros['cuerpo'],
			$actuacion->parametros['alcance'],
			$actuacion->parametros['orden'],
			$actuacion->parametros['id_firma']
		);
		if (is_null($firma_expe_elec))
			return [sprintf("La solicitud de firma para el documento del expediente electronico '%s-%s-%s cpo. %s alc. %s orden %s' no existe.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'], $actuacion->parametros['orden'])];
		if ($firma_expe_elec->estado != 'pendiente')
			return [sprintf("La solicitud de firma para el documento del expediente electronico '%s-%s-%s cpo. %s alc. %s orden %s' no se encuentra pendiente.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'], $actuacion->parametros['orden'])];
		if ($firma_expe_elec->id_usuario != $pUsuario->id_usuario)
			return [sprintf("El usuario actual no tiene permisos para rechazar la firma del documento del expediente electronico '%s-%s-%s cpo. %s alc. %s orden %s'.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'], $actuacion->parametros['orden'])];

		// Obtengo las transacciones de los pasos de la actuacion
		$trans = NG::transacActuaciones()->obtenerTransacActuaciones($actuacion->id_transaccion);

		// Debo tener la misma cantidad de transacciones que de pasos
		if (count($trans) != count($actuacion->pasos))
			return [sprintf('Error en transacción %s: la cantidad de pasos difiere.', $actuacion->id_transaccion)];

		// Paso 0: Rechazar Firma
		$data = json_decode($trans[0]->data);
		$firma_rechazada = $data->f_op_confirmacion == 1;

		// Paso 1: Observaciones
		$data = json_decode($trans[1]->data);
		$observaciones = $data->f_texto;

		// Actualizo la entrada del expediente electronico y el estado de la firma
		try {
			$firma_expe_elec->estado = 'cancelado';
			$firma_expe_elec->fecha_hora_salida = date('Y-m-d H:i:s');
			$firma_expe_elec->observaciones = $observaciones;
			$firma_expe_elec = NG::firmasExpedienteElec()->guardarFirmaExpedienteElec($firma_expe_elec);
		} catch (Exception $e) {
			return [sprintf('Error al actualizar la firma del expediente electrónico: %s.', $e->getMessage())];
		}

		// Extra: obtengo el documento del expediente electronico
		// despues de modificar el estado de las firmas, para obtener
		// los totales correctos.
		$expe_elec = NG::expedientesElec()->obtenerExpedienteElec(
			$actuacion->parametros['anio'],
			$actuacion->parametros['tipo'],
			$actuacion->parametros['numero'],
			$actuacion->parametros['cuerpo'],
			$actuacion->parametros['alcance'],
			$actuacion->parametros['orden']
		);

		// Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Orden' => $expe_elec->orden,
			'Id Firma' => $firma_expe_elec->id_firma,
			'Firma rechazada' => ($firma_rechazada) ? 'si' : 'no',
			'Observaciones' => $observaciones
		];

		if (is_null($expe_elec))
			return [sprintf("El documento del expediente electronico '%s-%s-%s cpo. %s alc. %s orden %s' no existe.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'], $actuacion->parametros['orden'])];

		// Notifico via correo electrónico al solicitante de la firma (si aplica)
		if (! is_null($firma_expe_elec->mail_usuario_solicitante)) {
			$body = sprintf('<p>Por medio del presente se le informa que el signatario <strong>%s</strong> ha rechazado la solicitud de firma de un documento a través del Sistema de Gestión Legislativa:</p><ul><li>%s</li></ul>',
				$firma_expe_elec->nombre_usuario,
				$expe_elec->obtenerEtiqueta(true)
			);
			try {
				MailHelper::get()->sendMail([
		           'sender' => [
		                'reply' => ($firma_expe_elec->mail_usuario) ?? '',
		                'reply_name' => $firma_expe_elec->nombre_usuario
		            ],
		            'recipients' => ['address' => [$firma_expe_elec->mail_usuario_solicitante]],
		            'message' => [
		                'subject' => sprintf('[Notificación de Rechazo de Firma] %s', $expe_elec->obtenerEtiqueta()),
		                'body' => $body,
		                'body_alt' => strip_tags($body),
		            ]
		        ]);
			} catch (Exception $e) {
				return [sprintf('Error al enviar correo electrónico: %s', $e->getMessage())];
			}
		}

		// Si todos los firmantes han completado su firma (firmado o rechazado),
		// se le informa todos los firmantes y el solicitante.
		if ($expe_elec->sinFirmasPendientes()) {

			// Recopilo todos los destinatarios (y de paso el listado de firmas)
			$firmas = NG::firmasExpedienteElec()->obtenerFirmasExpedienteElec($expe_elec->anio, $expe_elec->tipo, $expe_elec->numero, $expe_elec->cuerpo, $expe_elec->alcance, $expe_elec->orden);
			$destinatarios = [];
			$detalle_firmas = [];

			foreach ($firmas as $f) {

				if (!is_null($f->mail_usuario))
					if (! in_array($f->mail_usuario, $destinatarios))
						$destinatarios[] = $f->mail_usuario;

				if (!is_null($f->mail_usuario_solicitante))
					if (! in_array($f->mail_usuario_solicitante, $destinatarios))
						$destinatarios[] = $f->mail_usuario_solicitante;

				$detalle_firmas[] = sprintf('<li>Estado: <strong>%s</strong><ul><li>Signatario: <strong>%s</strong> ; Solicitante: <strong>%s</strong></li><li>Solicitado el: <strong>%s</strong> ; Firmado el: <strong>%s</strong></li></ul></li>',
					ucfirst($f->estado),
					$f->nombre_usuario,
					$f->nombre_usuario_solicitante,
					$f->fecha_hora_entrada,
					$f->fecha_hora_salida
				);
			}

			// Solo envío el correo si tengo destinatarios posibles
			if (count($destinatarios) > 0) {

				// Preparo el cuerpo del mensaje
				$body = sprintf('<p>Por medio del presente se le informa que todos los signatarios designados han <strong>%s</strong> el documento <strong>%s</strong> a través del Sistema de Gestión Legislativa.</p><p>A continuación se detalla el resumen de firmas:</p><ul>%s</ul>',
					($expe_elec->hayFirmasCanceladas()) ? 'firmado/rechazado' : 'firmado',
					$expe_elec->obtenerEtiqueta(true),
					join('', $detalle_firmas)
				);

				try {
					MailHelper::get()->sendMail([
			            'recipients' => ['address' => $destinatarios],
			            'message' => [
			                'subject' => sprintf('[Notificación de Firma Completa] %s', $expe_elec->obtenerEtiqueta()),
			                'body' => $body,
			                'body_alt' => strip_tags($body),
			            ]
			        ]);
				} catch (Exception $e) {
					return [sprintf('Error al enviar correo electrónico: %s', $e->getMessage())];
				}
			}
		}

		return [];
	}
}
?>
