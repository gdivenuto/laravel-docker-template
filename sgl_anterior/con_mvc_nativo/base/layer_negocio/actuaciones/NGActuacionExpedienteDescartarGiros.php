<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpedienteDescartarGiros.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpedienteDescartarGiros extends NGActuacionBase {

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
		// Se obtiene el expediente para verificar su existencia (por las dudas)
		$expediente = $this->obtenerExpediente($actuacion);

		// Si el expediente no existe
		if (is_null($expediente))
			return ['No existe el expediente que se desea girar a comisiones.'];

		// Obtengo el giro pendiente y verifico su estado
		try {
			$giro_pendiente = NG::girosPendientes()->obtenerGiroPendiente(
				$actuacion->parametros['anio'],
				$actuacion->parametros['tipo'],
				$actuacion->parametros['numero'],
				$actuacion->parametros['cuerpo'],
				$actuacion->parametros['alcance'],
				$actuacion->parametros['id_pendiente']);

			if (is_null($giro_pendiente))
				throw new Exception("No se encuentra el giro pendiente.");
		}
		catch(Exception $e) {
			return [sprintf("Error al obtener giros pendientes: %s", $e->getMessage())];
		}

		if ($giro_pendiente->estado != 'pendiente')
			return ["El lote de giros pendiente se encuentra finalizado o rechazado. Imposible continuar."];

		// Si no es un supervisor, reviso si es solicitante o firmante
		if (! in_array($pUsuario->id_usuario, SGL_ID_USUARIO_SUPERVISORES_MESA_ENTRADA))
			if (($giro_pendiente->id_usuario_solicitante != $pUsuario->id_usuario) && ($giro_pendiente->id_usuario_firmante != $pUsuario->id_usuario))
				return ["El usuario actual no posee permisos para descartar los giros del expediente."];

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
		// Obtengo las transacciones de los pasos de la actuacion
		$trans = NG::transacActuaciones()->obtenerTransacActuaciones($actuacion->id_transaccion);

		// Debo tener la misma cantidad de transacciones que de pasos
		if (count($trans) != count($actuacion->pasos))
			return [sprintf('Error en transacción %s: la cantidad de pasos difiere.', $actuacion->id_transaccion)];

		// Paso 0: Observaciones
		$data = json_decode($trans[0]->data);
		$observaciones = $data->f_texto;

		// Obtengo el giro pendiente y verifico su estado
		try {
			$giro_pendiente = NG::girosPendientes()->obtenerGiroPendiente(
				$actuacion->parametros['anio'],
				$actuacion->parametros['tipo'],
				$actuacion->parametros['numero'],
				$actuacion->parametros['cuerpo'],
				$actuacion->parametros['alcance'],
				$actuacion->parametros['id_pendiente']);

			if (is_null($giro_pendiente))
				throw new Exception("No se encuentra el giro pendiente.");
		}
		catch(Exception $e) {
			return [sprintf("Error al obtener giros pendientes: %s", $e->getMessage())];
		}

		if ($giro_pendiente->estado != 'pendiente')
			return ["El lote de giros pendiente se encuentra finalizado o rechazado. Imposible continuar."];

		// Si no es un supervisor, reviso si es solicitante o firmante
		if (! in_array($pUsuario->id_usuario, SGL_ID_USUARIO_SUPERVISORES_MESA_ENTRADA))
			if (($giro_pendiente->id_usuario_solicitante != $pUsuario->id_usuario) && ($giro_pendiente->id_usuario_firmante != $pUsuario->id_usuario))
				return ["El usuario actual no posee permisos para descartar los giros del expediente."];

		// Actualizamos los giros pendientes del expediente
		try {
			$giro_pendiente->fecha_hora_salida = DateTimeHelper::get()->timestampStr('Y-m-d H:i:s');
			$giro_pendiente->estado = 'rechazado';
			$giro_pendiente->observaciones = $observaciones;
			$giro_pendiente = NG::girosPendientes()->guardarGiroPendiente($giro_pendiente);
		}
		catch(Exception $e) {
			return [sprintf('Ha ocurrido un error al actualizar giros pendientes: %s', $e->getMessage())];
		}

		// Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Observaciones' => $observaciones // la de la Actuación
		];

		// Notifico via correo electrónico a quien mando a confirmar los giros,
		// si el usuario solicitante tiene mail.
		if ( (($giro_pendiente->mail_usuario_solicitante) ?? '') != '' ) {
			$body = sprintf('<p>Por medio del presente se le informa que se han descartado los giros a comisiones propuestos para el <strong>%s</strong>.',
				$giro_pendiente->obtenerEtiqueta()
			);
			try {
				MailHelper::get()->sendMail([
		           'sender' => [
		                'reply' => ($pUsuario->u_mail) ?? '',
		                'reply_name' => $pUsuario->nombre_usuario
		            ],
		            'recipients' => ['address' => [$giro_pendiente->mail_usuario_solicitante]],
		            'message' => [
		                'subject' => sprintf('[Giros a Comisiones Descartados] %s', $giro_pendiente->obtenerEtiqueta()),
		                'body' => $body,
		                'body_alt' => strip_tags($body),
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
