<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpedienteConvalidarGiros.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpedienteConvalidarGiros extends NGActuacionBase {

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
		try {
			// Se los giros del expediente
			$giros = NG::expedientes()->obtenerGiros(
				$actuacion->parametros['anio'],
				$actuacion->parametros['tipo'],
				$actuacion->parametros['numero'],
				$actuacion->parametros['cuerpo'],
				$actuacion->parametros['alcance']
			);
		}
		catch(Exception $e) {
			return [sprintf('Ha ocurrido un error al obtener los giros del expediente: %s', $e->getMessage())];
		}

		// Si NO hay giros
		if (count($giros) == 0) {
			return ["No se pueden convalidar los giros porque no hay giros cargados en el expediente."];
		}

		// Obtengo el primer Paso (Selección de comisiones) de la actuación y
		// piso su contenido con el conjunto de giros preexistentes
		try {
			$comisiones = [];
			$observaciones = [];

			foreach ($giros as $g) {
				if (! in_array($g->comision_codigo, $comisiones)) {
					$comisiones[] = $g->comision_codigo;
					$observaciones[] = '';
				}
			}

			// Rearmo la transaccion del paso de seleccion de comisiones.
			$trans = NG::transacActuaciones()->obtenerTransacActuaciones($actuacion->id_transaccion, 0);

			if (count($trans) == 0)
				throw new Exception('No se encuentra las transacciones.');

			$trans[0]->data = json_encode([
				'c' => 'actuaciones',
				'a' => 'siguiente',
				'actuacion' => $actuacion->obtenerTipoDeClaseActuacion(),
				'f_comisiones' => $comisiones,
				'f_observaciones' => $observaciones,
				'request_files' => []
			]);

			NG::transacActuaciones()->guardarTransacActuacion($trans[0]);
		}
		catch(Exception $e) {
			return [sprintf("Error al actualizar las transacciones de comisiones pendientes: %s", $e->getMessage())];
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
		$archivo_generado = $data->archivo_generado;

		// Paso 1: Confirmar firma de providencia
		$data = json_decode($trans[1]->data);
		$firma_confirmada = $data->f_op_confirmacion == 1;
		$conservar_original = $actuacion->pasos[1]->opciones['conservar_documento_original'];

		// Paso 2: Selección de Revisores
		$data = json_decode($trans[2]->data);
		$requiere_revision = $data->f_requiere_revision == 1;
		$revisores = $data->f_revisores;

		// Paso 3: Firmantes adicionales
		$data = json_decode($trans[3]->data);
		$firmantes = $data->f_firmantes;

		// Paso 4: Observaciones
		$data = json_decode($trans[4]->data);
		$observaciones = $data->f_texto;

		// ----- Verifico que todo este bien ----------------------------------
		// Se obtiene el expediente para verificar su existencia (por las dudas)
		$expediente = $this->obtenerExpediente($actuacion);
		if (is_null($expediente))
			return ['No existe el expediente del que se desea convalidar los giros.'];

		if (!file_exists($archivo_generado)) return ['No se encuentra el archivo a firmar.'];
		if (!$firma_confirmada) return ['Firma de documento no aceptada.'];

		// ----- Agrego el documento (final o pendiente) al expediente --------
		// Determino la ruta del archivo de salida
		$archivo_firmado = $this->obtenerArchivoSalida($expediente, $requiere_revision, false);
		if ($archivo_firmado == '') return ['No se pudo obtener el nombre del archivo de salida.'];

		// Firmo el documento
		$errores = $this->firmarPDF($archivo_generado, $archivo_firmado, $pUsuario);
		if (count($errores) > 0) return $errores;

		// Si requiere revision, lo envio a revisar; sino lo agrego directamente
		if ($requiere_revision) {
			// Agrego una entrada a documentos pendientes de revision
			try {
				$expe_elec_pend = $this->agregarAExpedienteElectronicoPendiente(
					$expediente,
					$actuacion,
					$pUsuario,
					$archivo_firmado,
					'', // El texto original es vacío para providencias generadas automaticamente
					false, // No es alcanzado por dec 1404
					false, // No es caratula
					'Convalidación de Giros',
					$observaciones,
					$revisores,
					$firmantes
				);
			} catch (Exception $e) {
				return [sprintf('Error al agregar entrada pendiente de revisión al expediente electrónico: %s.', $e->getMessage())];
			}

			if (is_null($expe_elec_pend)) return ['Error al agregar entrada pendiente de revisión al expediente electrónico: entrada nula.'];

		} else {
			// Agrego una entrada al expediente electronico
			try {
				$expe_elec = $this->agregarAExpedienteElectronico(
					$expediente,
					$actuacion,
					$pUsuario,
					$archivo_firmado,
					'', // El texto original es vacío para providencias generadas automaticamente
					false, // No es alcanzado por dec 1404
					false, // No es caratula
					'Convalidación de Giros',
					$observaciones,
					$firmantes
				);
			} catch (Exception $e) {
				return [sprintf('Error al agregar entrada al expediente electrónico: %s.', $e->getMessage())];
			}

			if (is_null($expe_elec)) return ['Error al agregar entrada al expediente electrónico: entrada nula.'];
		}

		// Elimino el original
		if (!$conservar_original) {
			FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
			FTPHelper::get()->delete($archivo_generado);
			FTPHelper::get()->disconnect();
		}

		/*
		if ($giro_pendiente->estado != 'pendiente')
			return ["El lote de giros pendiente se encuentra finalizado o rechazado. Imposible continuar."];

		// ---- Se cargan los Giros en el expediente
		$errores = NG::expedientes()->cargarGiros(
			$expediente,
			$comisiones,
			$observaciones_comisiones,
			$pUsuario,
			$para_ppc);

		// Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Comisiones' => join(', ', $comisiones),
			'Firmante' => sprintf('%s-%s (%s)', $giro_pendiente->id_usuario_firmante, $giro_pendiente->codigo_usuario_firmante, $giro_pendiente->nombre_usuario_firmante),
			'Observaciones' => $observaciones // la de la Actuación
		];

		/* --- REVISAR EL ENVIO DEL MAIL A LA REDACTORA O AREA DE COMISIONES ---- *

		// Se obtiene el mail de notificación para una determinada comisión
		$mail_comision = NG::expedientesParam()->obtenerMailComision($comisiones[0]);
		Logger::get()->Log("mail_comision", $mail_comision, false);

		// Si hay un mail al cual notificarle
		if ( (($mail_comision) ?? '') != '' ) {
			$body = sprintf('<p>Por medio del presente se le informa que se han confirmado los giros a comisiones para el <strong>%s</strong>.',
				$giro_pendiente->obtenerEtiqueta()
			);
			try {
				MailHelper::get()->sendMail([
		           'sender' => [
		                'reply' => ($pUsuario->u_mail) ?? '',
		                'reply_name' => $pUsuario->nombre_usuario
		            ],
		            'recipients' => ['address' => [$mail_comision]],
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
		/* ------------------------------------------------------------------- */

		return [];
	}

}
?>
