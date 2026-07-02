<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpeElecPendDescartar.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpeElecPendDescartar extends NGActuacionBase {

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
		$actuacion->getPaso(0)->opciones['documento_a_descartar']['ruta_documento_a_descartar'] = $expe_elec_pend->documento;

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
		// Obtengo todas las revisiónes del documento pendiente
		$revisiones = NG::revExpedienteElecPend()->obtenerRevsExpedienteElecPend(
			$actuacion->parametros['anio'],
			$actuacion->parametros['tipo'],
			$actuacion->parametros['numero'],
			$actuacion->parametros['cuerpo'],
			$actuacion->parametros['alcance'],
			$actuacion->parametros['orden']
			// Si estos parametros no se especifican, obtengo todas las revisiones del
			// documento electronico pendiente. Como cada doc pendiente va acompañado
			// de sus revisiones, no corro riesgo de pisar otras revisiones.
			//$actuacion->parametros['id_revision'],
			//$pUsuario->id_usuario
		);
		if (count($revisiones) == 0)
			return ["Las revisiones del documento pendiente no existen."];

		foreach ($revisiones as $r) {
			if ($r->id_usuario_solicitante != $pUsuario->id_usuario)
				return ["Las revisiones del documento pendiente no pertenecen al usuario actual."];
		}

		// Obtengo las transacciones de los pasos de la actuacion
		$trans = NG::transacActuaciones()->obtenerTransacActuaciones($actuacion->id_transaccion);

		// Debo tener la misma cantidad de transacciones que de pasos
		if (count($trans) != count($actuacion->pasos))
			return [sprintf('Error en transacción %s: la cantidad de pasos difiere.', $actuacion->id_transaccion)];

		// Paso 0: Descarta Revision
		$data = json_decode($trans[0]->data);
		$doc_confirmado = $data->f_op_descartar == 1;

		// Paso 1: Observaciones
		$data = json_decode($trans[1]->data);
		$observaciones = $data->f_texto;

		// Actualizo el estado de TODAS las revisiones como 'descartado'
		try {
			$fh_salida = DateTimeHelper::get()->timestampStr('Y-m-d H:i:s');
			foreach ($revisiones as $r) {
				$r->estado = 'descartado';
				$r->fecha_hora_salida = $fh_salida;
				$r->observaciones = $observaciones;
				NG::revExpedienteElecPend()->guardarRevExpedienteElecPend($r, false);
			}
		} catch (Exception $e) {
			return [sprintf("ERROR: no se pudo actualizar el estado de las revisiones. Causa: %s", $e->getMessage())];
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

		// Elimino el archivo pendiente
		try {
			FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
			FTPHelper::get()->delete(PATH_KRAKEN_RESOURCES_PROYECTOS.$expe_elec_pend->documento);
			FTPHelper::get()->disconnect();
		} catch (Exception $e) {
			// Si no puedo borrar, no hago nada (por ahora)
		}

		// ----- Notifico a todos los participantes del descarte del documento a revisar
		// (uso la primera revision para obtener el solicitante... deberia ser el mismo para todas)
		$mail_recipients = (!is_null($revisiones[0]->mail_usuario_solicitante))
			? [$revisiones[0]->mail_usuario_solicitante]
			: [];
		foreach ($revisiones as $r) {
			if (!is_null($r->mail_usuario))
				$mail_recipients[] = $r->mail_usuario;
		}

		$mail_subject = sprintf('[Revisión Descartada] %s', $expe_elec_pend->obtenerEtiqueta());
		$mail_body = sprintf('<p>Por medio del presente se le informa que el funcionario <strong>%s</strong> ha descartado la revisión del documento <strong>%s</strong>.</p>%s',
			$pUsuario->nombre_usuario,
			$expe_elec_pend->obtenerEtiqueta(true),
			($observaciones != '')
				? sprintf('<p>Las observaciones del funcionario son: <i>%s</i></p>', $observaciones)
				: ''
		);

		if (count($mail_recipients) > 0) {
			try {
				MailHelper::get()->sendMail([
		           'sender' => [
		                'reply' => ($pUsuario->u_mail) ?? '',
		                'reply_name' => $pUsuario->nombre_usuario
		            ],
		            'recipients' => ['address' => $mail_recipients],
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

		// Completo la info para auditoria para la actualizacion de una revision solamente
		$actuacion->info_auditoria = [
			'Genera documento electronico' => 'revisión descartada',
			'ID Revision' => $actuacion->parametros['id_revision'],
			'Revision finalizada' => 'no',
			'Estado' => 'descartado',
			'Observaciones' => $observaciones
		];

		return [];
	}

}
?>
