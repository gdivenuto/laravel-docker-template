<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpedienteConfirmarGiros.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpedienteConfirmarGiros extends NGActuacionBase {

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

		// Es expediente Agregado?
		$errores = $this->esExpedienteAgregado($actuacion, $expediente);
		if (count($errores) > 0)
			return $errores;

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

		if ($giro_pendiente->id_usuario_firmante != $pUsuario->id_usuario)
			return ["El usuario actual no posee permisos para confirmar los giros del expediente."];

		// Obtengo el primer Paso (Selección de comisiones) de la actuación y
		// piso su contenido con lo guardado como pendiente de confirmación
		try {
			$trans = NG::transacActuaciones()->obtenerTransacActuaciones($actuacion->id_transaccion, 0);

			if (count($trans) == 0)
				throw new Exception('No se encuentra las transacciones.');

			$trans[0]->data = $giro_pendiente->giros_pendientes;

			NG::transacActuaciones()->guardarTransacActuacion($trans[0]);
		}
		catch(Exception $e) {
			return [sprintf("Error al actualizar las transacciones de comisiones pendientes: %s", $e->getMessage())];
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
		$archivo_generado = $data->archivo_generado;

		// Paso 1: Confirmar firma de providencia
		$data = json_decode($trans[1]->data);
		$firma_confirmada = $data->f_op_confirmacion == 1;
		$conservar_original = $actuacion->pasos[1]->opciones['conservar_documento_original'];

		// Paso 2: Observaciones
		$data = json_decode($trans[2]->data);
		$observaciones = $data->f_texto;

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

		if ($giro_pendiente->id_usuario_firmante != $pUsuario->id_usuario)
			return ["El usuario actual no posee permisos para confirmar los giros del expediente."];

		// Actualizamos los giros pendientes del expediente
		try {
			$giro_pendiente->fecha_hora_salida = DateTimeHelper::get()->timestampStr('Y-m-d H:i:s');
			$giro_pendiente->estado = 'confirmado';
			$giro_pendiente->observaciones = $observaciones;
			$giro_pendiente = NG::girosPendientes()->guardarGiroPendiente($giro_pendiente);
		}
		catch(Exception $e) {
			return [sprintf('Ha ocurrido un error al guardar giros pendientes: %s', $e->getMessage())];
		}

		// ---- Se cargan los Giros en el expediente
		$errores = NG::expedientes()->cargarGiros(
			$expediente,
			$comisiones,
			$observaciones_comisiones,
			$pUsuario,
			$para_ppc);

		// ---- Componer providencia en PDF y adjuntar al expediente electronico
		// Verifico que todo este bien
		if (!file_exists($archivo_generado)) return ['No se encuentra el archivo a firmar.'];
		if (!$firma_confirmada) return ['Firma de documento no aceptada.'];

		// Determino la ruta del archivo de salida (por defecto no es alcanzado por el Art 11 Dec 1404)
		$archivo_firmado = $this->obtenerArchivoSalida($expediente, false, false);
		if ($archivo_firmado == '') return ['No se pudo obtener el nombre del archivo de salida.'];

		// Firmo el documento
		$errores = $this->firmarPDF($archivo_generado, $archivo_firmado, $pUsuario);
		if (count($errores) > 0) return $errores;

		// Agrego una entrada al expediente electronico con sus firmas asociadas
		try {
			// Agrego una entrada al expediente electronico
			$expe_elec = $this->agregarAExpedienteElectronico(
				$expediente,
				$actuacion,
				$pUsuario,
				$archivo_firmado,
				'',            // Sin texto original
				false,         // No es alcanzado por el Art 11 Dec 1404
				false,         // No es caratula
				'Giro a Comisiones',
				$observaciones,
				[]             // Sin firmantes extra
			);
		} catch (Exception $e) {
			return [sprintf('Error al agregar entrada al expediente electrónico: %s.', $e->getMessage())];
		}

		if (is_null($expe_elec)) return ['Error al agregar entrada al expediente electrónico: entrada nula.'];

		// Elimino el original
		if (!$conservar_original) {
			FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
			FTPHelper::get()->delete($archivo_generado);
			FTPHelper::get()->disconnect();
		}

		// Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Comisiones' => join(', ', $comisiones),
			'Firmante' => sprintf('%s-%s (%s)', $giro_pendiente->id_usuario_firmante, $giro_pendiente->codigo_usuario_firmante, $giro_pendiente->nombre_usuario_firmante),
			'Observaciones' => $observaciones // la de la Actuación
		];

		// Notifico vía correo electrónico a quien mandó a confirmar los giros,
		// si el usuario solicitante tiene mail.
		// Además, se envía una copia al mail de comisiones.
		if ( (($giro_pendiente->mail_usuario_solicitante) ?? '') != '' ) {
			$body = sprintf('<p>Por medio del presente se le informa que se han confirmado los giros a comisiones para el <strong>%s</strong>.',
				$giro_pendiente->obtenerEtiqueta()
			);
			try {
				// 16/02/2023 XXXX
				// Comisiones NO desea que le llegue la Copia a comisiones@concejomdp.gov.ar
		        // 'cc' => [SGL_MAIL_AREA_COMISIONES]

				MailHelper::get()->sendMail([
		           'sender' => [
		                'reply' => ($pUsuario->u_mail) ?? '',
		                'reply_name' => $pUsuario->nombre_usuario
		            ],
		            'recipients' => [
		            	'address' => [$giro_pendiente->mail_usuario_solicitante]
		            ],
		            'message' => [
		                'subject' => sprintf('[Giros a Comisiones Confirmados] %s', $giro_pendiente->obtenerEtiqueta()),
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
