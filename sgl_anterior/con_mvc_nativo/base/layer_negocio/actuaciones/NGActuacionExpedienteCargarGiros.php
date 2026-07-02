<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpedienteCargarGiros.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpedienteCargarGiros extends NGActuacionBase {

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

		try {
			// Se obtiene la cantidad de giros pendientes de carga, si hubiesen
			$cant_giros_pendientes = NG::girosPendientes()->obtenerGirosPendientesCantidad(
				$actuacion->parametros['anio'],
				$actuacion->parametros['tipo'],
				$actuacion->parametros['numero'],
				$actuacion->parametros['cuerpo'],
				$actuacion->parametros['alcance'],
				null, // id_pendiente
				null, //giros_pendientes
				'pendiente', // estado
				null, // fecha_hora_entrada
				null, // fecha_hora_salida
				null, // id_usuario_firmante
				null, // id_usuario_solicitante
				null // observaciones
			);
		}
		catch(Exception $e) {
			return [sprintf('Ha ocurrido un error al obtener la cantidad de giros pendientes: %s', $e->getMessage())];
		}

		// Si hay giros pendientes
		if ($cant_giros_pendientes > 0) {
			// Retorna el mensaje informando al usuario que no puede cargar giros por estar otros pendientes
			return [sprintf(
				"No se puede hacer una carga de giros, porque hay giros pendientes para %s %s-%s-%s-%s-%s",
				($actuacion->parametros['tipo'] == 'E') ? 'el expediente' : 'la nota',
				$actuacion->parametros['anio'],
				$actuacion->parametros['tipo'],
				$actuacion->parametros['numero'],
				$actuacion->parametros['cuerpo'],
				$actuacion->parametros['alcance']
			)];
		}

		// No retorna nada
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

		// Paso 0: Comisiones seleccionadas para el Expediente Electrónico
		$data = json_decode($trans[0]->data);
		$para_ppc = (isset($data->f_ppc)) ? $data->f_ppc : '';
		$comisiones = $data->f_comisiones;
		$observaciones_comisiones = $data->f_observaciones;

		// Paso 1: Firmantes adicionales
		$data = json_decode($trans[1]->data);
		$firmantes = $data->f_firmantes;

		// Paso 2: Observaciones
		$data = json_decode($trans[2]->data);
		$observaciones = $data->f_texto;

		// Se obtiene el expediente para verificar su existencia (por las dudas)
		$expediente = $this->obtenerExpediente($actuacion);

		// Si el expediente no existe
		if (is_null($expediente))
			return ['No existe el expediente que se desea girar a comisiones.'];

		if (count($firmantes) == 0)
			return ['No ha seleccionado ningún firmante para girar el expediente a comisiones.'];

		if (NG::seguridad()->obtenerUsuariosHabilitadosParaGirosCantidad($firmantes[0]) == 0)
			return ['El firmante seleccionado no posee privilegios para confirmar los giros a comisiones.'];

		try {
			$giro_pendiente = NG::girosPendientes()->agregarGiroPendiente(
				new GiroPendiente(
					$expediente->anio, // $panio
					$expediente->tipo, // $ptipo
					$expediente->numero, // $pnumero
					$expediente->cuerpo, // $pcuerpo
					$expediente->alcance, // $palcance
					null, // $pid_pendiente en nulo para que se calcule automáticamente
					$trans[0]->data, // $pgiros_pendientes
					'pendiente', // $pestado
					'CURRENT_TIMESTAMP', // $pfecha_hora_entrada
					null, // $pfecha_hora_salida
					$firmantes[0], // $pid_usuario_firmante
					$pUsuario->id_usuario, // $pid_usuario_solicitante
					$observaciones // $pobservaciones
				)
			);
		}
		catch(Exception $e) {
			return [sprintf('Ha ocurrido un error al guardar giros pendientes: %s', $e->getMessage())];
		}

		// Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Comisiones' => join(', ', $comisiones),
			'Firmantes' => join(', ', $firmantes),
			'Observaciones' => $observaciones // la de la Actuación
		];

		// Notifico via correo electrónico a quien confirma los giros,
		// si el usuario firmante tiene mail.
		if ( (($giro_pendiente->mail_usuario_firmante) ?? '') != '' ) {
			$body = sprintf('<p>Por medio del presente se le informa que hay giros a comisiones pendientes de su confirmación para el <strong>%s</strong>.',
				$giro_pendiente->obtenerEtiqueta()
			);
			try {
				MailHelper::get()->sendMail([
		           'sender' => [
		                'reply' => ($pUsuario->u_mail) ?? '',
		                'reply_name' => $pUsuario->nombre_usuario
		            ],
		            'recipients' => ['address' => [$giro_pendiente->mail_usuario_firmante]],
		            'message' => [
		                'subject' => sprintf('[Giros a Comisiones Pendientes] %s', $giro_pendiente->obtenerEtiqueta()),
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
