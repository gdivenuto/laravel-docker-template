<?php
/**
 * Capa de negocio para funcionalidad de ActuacionDocumentoSubirFirmarPdf.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionDocumentoSubirFirmarPdf extends NGActuacionBase {

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
		return parent::inicializarActuacion($actuacion, $pUsuario);
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

		// Paso 0: Subir Archivo
		$data = json_decode($trans[0]->data);
		$detalle_texto = $data->f_titulo;
		$archivo_subido = $data->archivo_subido;

		// Paso 1: Confirmar Firma
		$data = json_decode($trans[1]->data);
		$firma_confirmada = $data->f_op_confirmacion == 1;
		$conservar_original = $actuacion->pasos[1]->opciones['conservar_documento_original'];

		// Paso 2: Destinatarios del Documento
		$data = json_decode($trans[2]->data);
		$destinatarios = $data->f_destinatarios;

		// Paso 3: Observaciones
		$data = json_decode($trans[3]->data);
		$observaciones = $data->f_texto;

		// Verifico que todo este bien
		if (!file_exists($archivo_subido)) return ['No se encuentra el archivo a firmar.'];
		if (!$firma_confirmada) return ['Firma de documento no aceptada.'];

		// Determino la ruta del archivo de salida
		$archivo_firmado = sprintf('%sdocumento_%s_%s.pdf',
			$actuacion->pasos[1]->opciones['path_documento_firmado'],
			$actuacion->id_transaccion,
			DateTimeHelper::get()->timestampStr('YmdHisu')
		);
		if ($archivo_firmado == '') return ['No se pudo obtener el nombre del archivo de salida.'];

		// Firmo el documento
		$errores = $this->firmarPDF($archivo_subido, $archivo_firmado, $pUsuario);
		if (count($errores) > 0) return $errores;

		// Elimino el original
		if (!$conservar_original) {
			FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
			FTPHelper::get()->delete($archivo_subido);
			FTPHelper::get()->disconnect();
		}

		// Preparo el cuerpo del mensaje
		$body = sprintf('<p>Por medio del presente, <strong>%s</strong> le remite una copia del documento <strong>"%s"</strong> firmado electrónicamente a través del Sistema de Gestión Legislativa.</p><p>Motiva este envío: %s</p>',
			$pUsuario->nombre_usuario,
			$detalle_texto,
			($observaciones != '') ? $observaciones : 'sin motivos particulares.'
		);

		// Envío del correo electrónico
		try {
			MailHelper::get()->sendMail([
	           'sender' => [
	                'reply' => ($pUsuario->u_mail) ?? '',
	                'reply_name' => $pUsuario->nombre_usuario
	            ],
	            'recipients' => [
	            	'address' => $destinatarios,
	            	'bcc' => (!is_null($pUsuario->u_mail))
	            		? [$pUsuario->u_mail]
	            		: []
	            ],
	            'message' => [
	                'subject' => '[Envío de Documentación]',
	                'body' => $body,
	                'body_alt' => strip_tags($body),
	            ],
	            'attachments' => [$archivo_firmado]
	        ]);
		} catch (Exception $e) {
			return [sprintf('Error al enviar correo electrónico: %s', $e->getMessage())];
		}

		// Guardo como dato de actuación, el archivo generado
		// En el método 'obtenerRutaRetorno' de la actuación,
		// se redirecciona a la descarga.
		$actuacion->datos['archivo_descarga'] = basename($archivo_firmado);

		// Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Detalle' => $detalle_texto,
			'Firma confirmada' => ($firma_confirmada) ? 'si' : 'no',
			'Destinatarios' => (isset($destinatarios)) ? join(', ', $destinatarios) : '---',
			'Archivo firmado' => $archivo_firmado,
			'Observaciones' => $observaciones
		];

		return [];
	}

}
?>
