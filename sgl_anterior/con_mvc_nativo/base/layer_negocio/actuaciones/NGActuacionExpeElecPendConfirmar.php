<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpeElecPendConfirmar.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpeElecPendConfirmar extends NGActuacionBase {

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
	// ---- Métodos únicos de esta Actuación ----------------------------------
	// ------------------------------------------------------------------------
	public function esUnicoRevisorFirmante(ExpedienteElecPend $pexpe_elec_pend, Usuario $pUsuario = null) {
		// Obtengo la cantidad de revisores
		// Podría usar $pexpe_elec_pend->total_revisiones, pero necesito
		// consultar los datos de las revisiones de todas formas.
		try {
			$revisiones = NG::revExpedienteElecPend()->obtenerRevsExpedienteElecPend(
				$pexpe_elec_pend->anio,
				$pexpe_elec_pend->tipo,
				$pexpe_elec_pend->numero,
				$pexpe_elec_pend->cuerpo,
				$pexpe_elec_pend->alcance,
				$pexpe_elec_pend->orden,
				null, // cualquier revision
				null, // cualquier usuario
				null  // cualquier estado (no me importa lo que sea)
			);
		} catch (Exception $e) {
			throw new Exception(sprintf('esUnicoRevisorFirmante: error al obtener revisiones (%s)', $e->getMessage()));
		}

		// Si es el único revisor...
		if (count($revisiones) == 1) {
			// Si el usuario de la revision es el usuario actuante...
			if ($revisiones[0]->id_usuario == $pUsuario->id_usuario) {
				// Reviso si ademas es el único firmante...
				try {
					$firmas = NG::firmasExpedienteElecPend()->obtenerFirmasExpedienteElecPend(
						$pexpe_elec_pend->anio,
						$pexpe_elec_pend->tipo,
						$pexpe_elec_pend->numero,
						$pexpe_elec_pend->cuerpo,
						$pexpe_elec_pend->alcance,
						$pexpe_elec_pend->orden
					);
				} catch (Exception $e) {
					throw new Exception(sprintf('esUnicoRevisorFirmante: error al obtener firmas (%s)', $e->getMessage()));
				}

				// Si es la unica firma ademas de la del solicitante...
				if (count($firmas) == 2) {
					// El usuario firmante es el usuario actuante...
					// El primer usuario (la firma 0) es el usuario solicitante.
					if ($firmas[1]->id_usuario == $pUsuario->id_usuario)
						return true;
					else
						// El usuario firmante NO ES el usuario actuante
						return false;
				} else
					// Mas de un firmante... entonces no.
					return false;

			} else
				// El usuario de la revision NO ES el usuario actuante...
				return false;
		} else
			// No es el unico revisor...
			return false;
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
		// Es expediente Agregado?
		$errores = $this->esExpedienteAgregado($actuacion);
		if (count($errores) > 0)
			return $errores;

		// En la inicialización, obtengo el documento del preview a partir
		// de la entrada del expediente electronico.
		try {
			$expe_elec_pend = NG::expedientesElecPend()->obtenerExpedienteElecPend(
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

		if (is_null($expe_elec_pend))
			return ['No se encuentra el documento pendiente asociado al expediente electrónico.'];

		// Actualizo el parámetro del paso 0: Confirmar Revision
		$actuacion->getPaso(0)->opciones['documento_a_revisar']['ruta_documento_a_revisar'] = $expe_elec_pend->documento;

		// Actualizo el parámetro del paso 0: Confirmar Revision
		// Si el usuario es el unico revisor y firmante, entonces este paso "revisa y firma"
		$actuacion->getPaso(0)->opciones['revisar_y_firmar'] = $this->esUnicoRevisorFirmante($expe_elec_pend, $pUsuario);

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
		// Verificacion extra: si justo el usuario quiere confirmar algo que durante
		// el proceso de confirmacion fue descartado, devuelvo un error.
		$cant_descartados = NG::revExpedienteElecPend()->obtenerRevsExpedienteElecPendCantidad(
			$actuacion->parametros['anio'],
			$actuacion->parametros['tipo'],
			$actuacion->parametros['numero'],
			$actuacion->parametros['cuerpo'],
			$actuacion->parametros['alcance'],
			$actuacion->parametros['orden'],
			$actuacion->parametros['id_revision'], // verificar esto ---> tendria que ser null
			$pUsuario->id_usuario, // verificar esto ---> tendria que ser null
			'descartado'
		);
		if ($cant_descartados > 0)
			return ["El documento que usted quiere revisar ha sido descartado. Cancele esta actuación para poder continuar."];

		// Obtengo la revisión del documento pendiente
		$revision = NG::revExpedienteElecPend()->obtenerRevExpedienteElecPend(
			$actuacion->parametros['anio'],
			$actuacion->parametros['tipo'],
			$actuacion->parametros['numero'],
			$actuacion->parametros['cuerpo'],
			$actuacion->parametros['alcance'],
			$actuacion->parametros['orden'],
			$actuacion->parametros['id_revision'],
			$pUsuario->id_usuario,
			'pendiente'
		);
		if (is_null($revision))
			return ["La revision del documento pendiente no existe, no pertenece al usuario actual o no se encuentra pendiente."];

		// Obtengo las transacciones de los pasos de la actuacion
		$trans = NG::transacActuaciones()->obtenerTransacActuaciones($actuacion->id_transaccion);

		// Debo tener la misma cantidad de transacciones que de pasos
		if (count($trans) != count($actuacion->pasos))
			return [sprintf('Error en transacción %s: la cantidad de pasos difiere.', $actuacion->id_transaccion)];

		// Paso 0: Confirma Revision
		$data = json_decode($trans[0]->data);
		$doc_confirmado = $data->f_op_revision == 1;

		// Paso 1: Observaciones
		$data = json_decode($trans[1]->data);
		$observaciones = $data->f_texto;

		// Actualizo el estado de la revision
		try {
			$revision->estado = ($doc_confirmado) ? 'confirmado' : 'rechazado';
			$revision->fecha_hora_salida = DateTimeHelper::get()->timestampStr('Y-m-d H:i:s');
			$revision->observaciones = $observaciones;
			$revision = NG::revExpedienteElecPend()->guardarRevExpedienteElecPend($revision, true);
		} catch (Exception $e) {
			return [sprintf("ERROR: no se pudo actualizar el estado de la revisión. Causa: %s", $e->getMessage())];
		}

		// Extra: obtengo el documento pendiente del expediente electronico
		// Se hace despues de actualizar la revision para que los contadores
		// de pendientes, finalizadas y rechazadas me den actualizados.
		$expe_elec_pend = NG::expedientesElecPend()->obtenerExpedienteElecPend(
			$actuacion->parametros['anio'],
			$actuacion->parametros['tipo'],
			$actuacion->parametros['numero'],
			$actuacion->parametros['cuerpo'],
			$actuacion->parametros['alcance'],
			$actuacion->parametros['orden']
		);
		if (is_null($expe_elec_pend))
			return [sprintf("El documento pendiente de revision '%s-%s-%s cpo. %s alc. %s orden %s' no existe.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'], $actuacion->parametros['orden'])];

		$mail_body = ''; // Si esta variable se mantiene vacía, no hay que enviar nada por correo.
		$mail_subject = '';

		// Si no tengo revisiones pendientes...
		if ($expe_elec_pend->cant_pendientes == 0)
		{
			// Si todas las revisiones son 'confirmadas', agrego el pendiente al expediente electronico
			if ($expe_elec_pend->total_revisiones == $expe_elec_pend->cant_confirmados)
			{
				try {
					$expe_elec = NG::expedientesElec()->agregarDocumentoElectronicoDesdePendiente($expe_elec_pend);
				} catch (Exception $e) {
					return [$e->getMessage()];
				}

				// REVISAR Y FIRMAR: Si ademas es el unico revisor y firmante, firmo digitalmente el documento
				if ($this->esUnicoRevisorFirmante($expe_elec_pend, $pUsuario)) {
					// El expediente electronico ya lo tengo actualizado, y ya sabemos por la lógica
					// de 'esUnicoRevisorFirmante' que la segunda firma es la del usuario actual.
					try {
						$firma_expe_elec = NG::firmasExpedienteElec()->obtenerFirmaExpedienteElec(
							$expe_elec->anio,
							$expe_elec->tipo,
							$expe_elec->numero,
							$expe_elec->cuerpo,
							$expe_elec->alcance,
							$expe_elec->orden,
							2 // --> la segunda firma es la del usuario actual
						);
					} catch (Exception $e) {
						return [$e->getMessage()];
					}

					// Verifico que todo este bien
					$archivo_para_firma = PATH_KRAKEN_RESOURCES_PROYECTOS.$expe_elec->documento;
					if (!file_exists($archivo_para_firma)) return ['No se encuentra el archivo a firmar.'];
					if (!$doc_confirmado) return ['Firma de documento no aceptada.'];

					// Determino la ruta del archivo de salida
					try {
						$archivo_firmado = $this->obtenerArchivoSalida(
							NG::expedientesElec()->obtenerExpedienteDeExpedienteElec($expe_elec),
							false,
							$expe_elec->dec1404
						);
						if ($archivo_firmado == '') return ['No se pudo obtener el nombre del archivo de salida.'];
					} catch (Exception $e) {
						return [sprintf('No se pudo obtener el nombre del archivo de salida: %s.', $e->getMessage())];
					}

					// Firmo el documento
					$errores = $this->firmarPDF($archivo_para_firma, $archivo_firmado, $pUsuario);
					if (count($errores) > 0) return $errores;

					// Actualizo la entrada del expediente electronico y el estado de la firma
					try {
						$firma_expe_elec->estado = 'firmado';
						$firma_expe_elec->fecha_hora_salida = date('Y-m-d H:i:s');
						$firma_expe_elec->observaciones = 'Firmado automáticamente desde la actuación de revisión (revisar y firmar).';
						$firma_expe_elec = NG::firmasExpedienteElec()->guardarFirmaExpedienteElec($firma_expe_elec);

						$expe_elec = NG::expedientesElec()->actualizarDocumentoExpedienteElec($expe_elec, $archivo_firmado);
					} catch (Exception $e) {
						return [sprintf('Error al actualizar la firma del expediente electrónico: %s.', $e->getMessage())];
					}

					// Elimino el original
					FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
					FTPHelper::get()->delete($archivo_para_firma);
					FTPHelper::get()->disconnect();

					// Notifico de que se paso la revision y firma con exito
					$mail_subject = sprintf('[Revisión y Firma finalizada: Aceptada] %s', $expe_elec_pend->obtenerEtiqueta());
					$mail_body = sprintf('<p>Por medio del presente se le informa que el documento <strong>%s</strong> enviado a revisión ha sido confirmado, firmado y aceptado por todos los revisores.</p><p>El documento ha sido agregado al expediente electrónico con la denominación <strong>%s</strong>.</p>',
						$expe_elec_pend->obtenerEtiqueta(true),
						$expe_elec->obtenerEtiqueta(true)
					);

					// Completo la info para auditoria para la actualizacion de la revision
					// y el "cierre" de todas las revisiones
					$actuacion->info_auditoria = [
						'Genera documento electronico' => $expe_elec->obtenerEtiqueta(true),
						'ID Revision' => $revision->id_revision,
						'Revision finalizada' => 'si',
						'Estado' => ($doc_confirmado) ? 'confirmado/firmado' : 'rechazado',
						'Observaciones' => '[REVISION Y FIRMA] ' . $observaciones
					];

				} else {
					// ------ Notificacion y auditoria "normal" de una revision comun

					// Notifico de que se paso la revision con exito
					$mail_subject = sprintf('[Revisión finalizada: Aceptada] %s', $expe_elec_pend->obtenerEtiqueta());
					$mail_body = sprintf('<p>Por medio del presente se le informa que el documento <strong>%s</strong> enviado a revisión ha sido confirmado y aceptado por todos los revisores.</p><p>El documento ha sido agregado al expediente electrónico con la denominación <strong>%s</strong>.</p>',
						$expe_elec_pend->obtenerEtiqueta(true),
						$expe_elec->obtenerEtiqueta(true)
					);

					// Completo la info para auditoria para la actualizacion de la revision
					// y el "cierre" de todas las revisiones
					$actuacion->info_auditoria = [
						'Genera documento electronico' => $expe_elec->obtenerEtiqueta(true),
						'ID Revision' => $revision->id_revision,
						'Revision finalizada' => 'si',
						'Estado' => ($doc_confirmado) ? 'confirmado' : 'rechazado',
						'Observaciones' => $observaciones
					];
				}

			} else {
				// Si se da este caso, es que al menos 1 revisor ha rechazado el documento.
				// Elimino el pendiente
				try {
					FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
	  				FTPHelper::get()->delete(PATH_KRAKEN_RESOURCES_PROYECTOS.$expe_elec_pend->documento);
	  				FTPHelper::get()->disconnect();
				} catch (Exception $e) {
					// Si no puedo borrar, no hago nada (por ahora)
				}

				// Si el ultimo revisor ha rechazado, notifico con el motivo
				if (!$doc_confirmado) {
					$mail_subject = sprintf('[Revisión finalizada: Rechazada] %s', $expe_elec_pend->obtenerEtiqueta());
					$mail_body = sprintf('<p>Por medio del presente se le informa que el documento <strong>%s</strong> enviado a revisión ha sido rechazado por el revisor <strong>%s</strong>.</p>%s',
						$expe_elec_pend->obtenerEtiqueta(true),
						$revision->nombre_usuario,
						($observaciones != '')
							? sprintf('<p>Las observaciones del rechazo son: <i>%s</i></p>', $observaciones)
							: ''
					);
				} else {
	  				// Notifico de la finalizacion rechazada
					$mail_subject = sprintf('[Revisión finalizada: Rechazada] %s', $expe_elec_pend->obtenerEtiqueta());
					$mail_body = sprintf('<p>Por medio del presente se le informa que el documento <strong>%s</strong> enviado a revisión ha sido rechazado por al menos uno de sus revisores.</p>',
						$expe_elec_pend->obtenerEtiqueta(true)
					);
				}


				// Completo la info para auditoria para la actualizacion de la revision
				// y el "cierre" de todas las revisiones
				$actuacion->info_auditoria = [
					'Genera documento electronico' => 'no',
					'ID Revision' => $revision->id_revision,
					'Revision finalizada' => 'si',
					'Estado' => ($doc_confirmado) ? 'confirmado' : 'rechazado',
					'Observaciones' => $observaciones
				];
			}
		} else {
			// Si el revisor ha rechazado el documento, notifico al solicitante con el motivo
			if (!$doc_confirmado) {
				$mail_subject = sprintf('[Revisión Rechazada] %s', $expe_elec_pend->obtenerEtiqueta());
				$mail_body = sprintf('<p>Por medio del presente se le informa que el documento <strong>%s</strong> enviado a revisión ha sido rechazado por el revisor <strong>%s</strong>.</p>%s',
					$expe_elec_pend->obtenerEtiqueta(true),
					$revision->nombre_usuario,
					($observaciones != '')
						? sprintf('<p>Las observaciones del rechazo son: <i>%s</i></p>', $observaciones)
						: ''
				);
			}

			// Completo la info para auditoria para la actualizacion de una revision solamente
			$actuacion->info_auditoria = [
				'Genera documento electronico' => 'restan confirmaciones',
				'ID Revision' => $revision->id_revision,
				'Revision finalizada' => 'no',
				'Estado' => ($doc_confirmado) ? 'confirmado' : 'rechazado',
				'Observaciones' => $observaciones
			];
		}

		// Notifico via correo electrónico al solicitante de la revisión, si aplica
		if (($mail_body != '') && (!is_null($revision->mail_usuario_solicitante))) {
			try {
				MailHelper::get()->sendMail([
		           'sender' => [
		                'reply' => ($revision->mail_usuario) ?? '',
		                'reply_name' => $revision->nombre_usuario
		            ],
		            'recipients' => ['address' => [$revision->mail_usuario_solicitante]],
		            'message' => [
		                'subject' => $mail_subject,
		                'body' => $mail_body,
		                'body_alt' => strip_tags($mail_body)
		            ]
		        ]);
			} catch (Exception $e) {
				return [sprintf('Error al enviar correo electrónico: %s', $e->getMessage())];
			}
		}

		return [];
	}

}
?>
