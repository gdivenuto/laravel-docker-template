<?php
/**
 * Capa de negocio base para funcionalidad de Actuaciones.
 *
 * @author XXXX
 *
 */
class NGActuacionBase extends NGBaseClass {

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
	// ---- Helpers para Actuaciones ------------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * A partir de un expediente, obtiene el subdirectorio correspondiente
	 * para archivos del mismo.
	 *
	 * ATENCION: no devuelve la ruta completa, solo la ruta parcial del expediente
	 * a partir del directorio del año.
	 * @param  Expediente $expediente [description]
	 * @return [type]                 [description]
	 */
	public function obtenerSubDirectorioExpediente(Expediente $expediente) {
		// Determino el directorio del expediente (expedientes/proyectos/AAAA/AATNNNNN)
		$ruta_parcial_expe = sprintf('%d/%02d%s%05d/',
			$expediente->anio,
			$expediente->anio % 100,
			$expediente->tipo,
			$expediente->numero
		);

		// Si hace falta, creo la ruta para que "$ruta_parcial_expe" exista
		if (! is_dir(PATH_KRAKEN_RESOURCES_PROYECTOS.$ruta_parcial_expe) ) {
			try {
				FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
				FTPHelper::get()->mkDir(PATH_KRAKEN_RESOURCES_PROYECTOS, $ruta_parcial_expe, 0777);
				FTPHelper::get()->chmod(PATH_KRAKEN_RESOURCES_PROYECTOS.$ruta_parcial_expe, 0755);
				FTPHelper::get()->disconnect();
			} catch (Exception $e) {
				return '';
			}
		}

		// Devuelvo solo la ruta parcial
		return $ruta_parcial_expe;
	}

	/**
	 * A partir de un expediente, genera los directorios necesarios y determina
	 * el nombre del archivo de salida para almacenar un documento firmado.
	 *
	 * NOTA: los métodos se hacen públicos porque temporalmente se requieren su lógica en otras capas de negocio (que no heredan directamente de NGActuacionBase)
	 * @param  Expediente $expediente          [description]
	 * @param  boolean    $documento_pendiente [description]
	 * @param  boolean    $alcanza_dec1404     [description]
	 * @return [type]                          [description]
	 */
	public function obtenerArchivoSalida(
		Expediente $expediente,
		$documento_pendiente = false,
		$alcanza_dec1404 = false)
	{
		// Determino la ruta del archivo de salida
		$sub_path_destino = sprintf('%selectronico/',
			$this->obtenerSubDirectorioExpediente($expediente)
		);

		// Creo la ruta del archivo de salida (si hace falta)
		if (! is_dir(PATH_KRAKEN_RESOURCES_PROYECTOS.$sub_path_destino) ) {
			try {
				FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
				FTPHelper::get()->mkDir(PATH_KRAKEN_RESOURCES_PROYECTOS, $sub_path_destino, 0777);
				FTPHelper::get()->chmod(PATH_KRAKEN_RESOURCES_PROYECTOS.$sub_path_destino, 0755);
				FTPHelper::get()->disconnect();
			} catch (Exception $e) {
				return '';
			}
		}

		// Determino el nombre del archivo de salida
		return sprintf('%s%s%s%02d%s%05d_%s%s.pdf',
			PATH_KRAKEN_RESOURCES_PROYECTOS,
			$sub_path_destino,
			($documento_pendiente) ? 'pendiente_' : '',
			$expediente->anio % 100,
			$expediente->tipo,
			$expediente->numero,
			date('YmdHis'),
			($alcanza_dec1404) ? '_dec1404' : ''
		);
	}

	/**
	 * Wrapper para firmar documento en actuaciones.
	 * Una vez firmado el documento, lo mueve a la carpeta final ajustando los permisos.
	 *
	 * NOTA: los métodos se hacen públicos porque temporalmente se requieren su lógica en otras capas de negocio (que no heredan directamente de NGActuacionBase)
	 * @param  [type]       $doc_origen  [description]
	 * @param  [type]       $doc_destino [description]
	 * @param  Usuario|null $pUsuario    [description]
	 * @return [type]                    [description]
	 */
	public function firmarPDF($doc_origen, $doc_destino, Usuario $pUsuario = null)
	{
		$pre_destino = PATH_SGL_DOC_TEMPORALES . basename($doc_destino);
		$errores = NG::firmas()->firmarPDF($doc_origen, $pre_destino, $pUsuario, 0644);
		if (count($errores) == 0) {
			try {
				FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
				FTPHelper::get()->moveFile($pre_destino, $doc_destino, 0644);
				FTPHelper::get()->disconnect();
			} catch (Exception $e) {
				$errores = [sprintf('Error al mover archivo: %s', $e->getMessage())];
			}
		}
		return $errores;
	}

	/**
	 * Agrega una entrada al expediente electronico.
	 * @param  Expediente $pexpediente      [description]
	 * @param  Actuacion  $pactuacion       [description]
	 * @param  Usuario    $pusuario         [description]
	 * @param  string     $parchivo         [description]
	 * @param  string     $ptexto_original  [description]
	 * @param  boolean    $palcanza_dec1404 [description]
	 * @param  boolean    $pes_caratula 	[description]
	 * @param  string     $pdetalle         [description]
	 * @param  string     $pobservaciones   [description]
	 * @param  array      $pfirmantes_extra [description]
	 * @return [type]                       [description]
	 */
	protected function agregarAExpedienteElectronico(
		Expediente $pexpediente,
		Actuacion $pactuacion,
		Usuario $pusuario,
		$parchivo = '',
		$ptexto_original = null,
		$palcanza_dec1404 = false,
		$pes_caratula = false,
		$pdetalle = '',
		$pobservaciones = '',
		$pfirmantes_extra = [])
	{
		// Extraigo la ruta base del archivo
		$archivo_ref = preg_replace(sprintf('|^%s|',PATH_KRAKEN_RESOURCES_PROYECTOS), '', $parchivo);

		// Verifico si el archivo tiene documentos embebidos
		try {
			$tiene_embebidos = PDFExtractor::get()->hasAttachments($parchivo);
		} catch (Exception $e) {
			return null; // TODO: hacer algo aca...
		}

		// Agrego una entrada al expediente electrónico
		// (el orden y la fecha/hora se recalculan)
		$expe_elec = NG::expedientesElec()->agregarDocumentoElectronico(
			new ExpedienteElec(
				$pexpediente->anio,
				$pexpediente->tipo,
				$pexpediente->numero,
				$pexpediente->cuerpo,
				$pexpediente->alcance,
				null,
				$pactuacion->obtenerTipoDeClaseActuacion(),
				($pdetalle != '') ? $pdetalle : $pactuacion->nombre,
				$archivo_ref,
				($parchivo != '') ? hash_file('sha256', $parchivo) : '',
				$ptexto_original,
				$palcanza_dec1404,
				$tiene_embebidos,
				$pes_caratula,
				null,
				$pusuario->id_usuario,
				($pobservaciones) ?? ''
			)
		);

		// Agrego la 'firma inicial' del usuario que anexa el documento
		NG::firmasExpedienteElec()->agregarFirmasDocumentoElectronico($expe_elec, $pusuario, [$pusuario], 'firmado');

		// Agrego el resto de las firmas como pendientes, si aplica
		if (count($pfirmantes_extra) > 0) {
			$firmantes = NG::seguridad()->obtenerUsuarios($pfirmantes_extra);
			NG::firmasExpedienteElec()->agregarFirmasDocumentoElectronico($expe_elec, $pusuario, $firmantes, 'pendiente');

			// Además, notifico a los firmantes de que tienen un documento pendiente
			$destinatarios = [];
			foreach ($firmantes as $f) {
				if (!is_null($f->u_mail))
					$destinatarios[] = $f->u_mail;
			}
			if (count($destinatarios) > 0) {
				// Preparo el cuerpo del mensaje
				$body = sprintf('<p>Por medio del presente se le informa que el funcionario <strong>%s</strong> lo ha designado como signatario del documento <strong>%s</strong> y se requiere de su atención para la firma en el Sistema de Gestión Legislativa.</p>',
					$pusuario->nombre_usuario,
					$expe_elec->obtenerEtiqueta(true)
				);

				try {
					MailHelper::get()->sendMail([
			           'sender' => [
			                'reply' => ($pusuario->u_mail) ?? '',
			                'reply_name' => $pusuario->nombre_usuario
			            ],
			            'recipients' => ['address' => $destinatarios],
			            'message' => [
			                'subject' => sprintf('[Firma Pendiente] %s', $expe_elec->obtenerEtiqueta()),
			                'body' => $body,
			                'body_alt' => strip_tags($body),
			            ]
			        ]);
				} catch (Exception $e) {
					// TODO: hacer algo aca...
				}
			}
		}

		return $expe_elec;
	}

	/**
	 * Agrega una entrada como documento electronico pendiente de revision.
	 * @param  Expediente $pexpediente          [description]
	 * @param  Actuacion  $pactuacion           [description]
	 * @param  Usuario    $pusuario             [description]
	 * @param  string     $parchivo             [description]
	 * @param  [type]     $ptexto_original      [description]
	 * @param  boolean    $palcanza_dec1404     [description]
	 * @param  string     $pdetalle             [description]
	 * @param  string     $pobservaciones       [description]
	 * @param  array      $previsores_documento [description]
	 * @param  array      $pfirmantes_extra     [description]
	 * @return [type]                           [description]
	 */
	protected function agregarAExpedienteElectronicoPendiente(
		Expediente $pexpediente,
		Actuacion $pactuacion,
		Usuario $pusuario,
		$parchivo = '',
		$ptexto_original = null,
		$palcanza_dec1404 = false,
		$pes_caratula = false,
		$pdetalle = '',
		$pobservaciones = '',
		$previsores_documento = [],
		$pfirmantes_extra = [])
	{
		// Extraigo la ruta base del archivo
		$archivo_ref = preg_replace(sprintf('|^%s|',PATH_KRAKEN_RESOURCES_PROYECTOS), '', $parchivo);

		// Verifico si el archivo tiene documentos embebidos
		try {
			$tiene_embebidos = PDFExtractor::get()->hasAttachments($parchivo);
		} catch (Exception $e) {
			return null; // TODO: hacer algo aca...
		}

		// Agrego una entrada al expediente electrónico pendiente
		// (el orden y la fecha/hora se recalculan)
		$expe_elec_pend = NG::expedientesElecPend()->agregarDocumentoElectronicoPend(
			new ExpedienteElecPend(
				$pexpediente->anio,
				$pexpediente->tipo,
				$pexpediente->numero,
				$pexpediente->cuerpo,
				$pexpediente->alcance,
				null,
				$pactuacion->obtenerTipoDeClaseActuacion(),
				($pdetalle != '') ? $pdetalle : $pactuacion->nombre,
				$archivo_ref,
				($parchivo != '') ? hash_file('sha256', $parchivo) : '',
				$ptexto_original,
				$palcanza_dec1404,
				$tiene_embebidos,
				$pes_caratula,
				null,
				$pusuario->id_usuario,
				($pobservaciones) ?? ''
			)
		);

		// ---- Agrego los firmantes
		// El primer firmante es el funcionario que envía a revisar el documento.
		// Para este usuario se considerara que el documento ya esta firmado
		// (si se confirma positivamente el documento por todos los revisores).
		NG::firmasExpedienteElecPend()->agregarFirmasDocumentoElectronicoPend($expe_elec_pend, $pusuario, [$pusuario]);

		// Agrego el resto de las firmas, si aplica
		if (count($pfirmantes_extra) > 0) {
			$firmantes = NG::seguridad()->obtenerUsuarios($pfirmantes_extra);
			NG::firmasExpedienteElecPend()->agregarFirmasDocumentoElectronicoPend($expe_elec_pend, $pusuario, $firmantes);
		}

		// ---- Agrego los revisores del documento
		if (count($previsores_documento) > 0) {
			$revisores = NG::seguridad()->obtenerUsuarios($previsores_documento);
			NG::revExpedienteElecPend()->agregarRevsDocumentoElectronicoPend($expe_elec_pend, $pusuario, $revisores, 'pendiente');

			// Además, notifico a los revisores de que tienen un documento pendiente de revision
			$destinatarios = [];
			foreach ($revisores as $r) {
				if (!is_null($r->u_mail))
					$destinatarios[] = $r->u_mail;
			}
			if (count($destinatarios) > 0) {
				// Preparo el cuerpo del mensaje
				$body = sprintf('<p>Por medio del presente se le informa que el funcionario <strong>%s</strong> lo ha designado como revisor del documento <strong>%s</strong> y se requiere de su atención para la revisión en el Sistema de Gestión Legislativa.</p>',
					$pusuario->nombre_usuario,
					$expe_elec_pend->obtenerEtiqueta(true)
				);

				try {
					MailHelper::get()->sendMail([
			           'sender' => [
			                'reply' => ($pusuario->u_mail) ?? '',
			                'reply_name' => $pusuario->nombre_usuario
			            ],
			            'recipients' => ['address' => $destinatarios],
			            'message' => [
			                'subject' => sprintf('[Revisión Pendiente] %s', $expe_elec_pend->obtenerEtiqueta()),
			                'body' => $body,
			                'body_alt' => strip_tags($body),
			            ]
			        ]);
				} catch (Exception $e) {
					// TODO: hacer algo aca...
				}
			}
		}

		return $expe_elec_pend;
	}

	/**
	 * Obtiene la ultima entrada de expediente electronico y la almacena como
	 * dato en la actuacion.
	 * @param  Actuacion    $actuacion [description]
	 * @param  Usuario|null $pUsuario  [description]
	 * @return [type]                  [description]
	 */
	protected function guardarUltimoOrdenExpedienteElec(Actuacion $actuacion, Usuario $pUsuario = null)
	{
		// Para evitar que dos usuarios actualizen el mismo expediente, se almacena el último nro. de orden
		// de los documentos del expediente electrónico.
		try {
			$expe_elec = NG::expedientesElec()->obtenerExpedienteElecUltimo(
				$actuacion->parametros['anio'],
				$actuacion->parametros['tipo'],
				$actuacion->parametros['numero'],
				$actuacion->parametros['cuerpo'],
				$actuacion->parametros['alcance']
			);
		} catch (Exception $e) {
			return [$e->getMessage()];
		}

		// Obtengo el ultimo nro de orden y lo guardo en la actuacion
		$actuacion->datos['expe_elec_ultimo'] = $expe_elec;

		return [];
	}

	/**
	 * Determina si hubo cambios en el expediente electronico mientras
	 * se estaba completando la actuacion
	 * @param  Actuacion $actuacion [description]
	 * @return [type]               [description]
	 */
	protected function huboCambiosEnExpedienteElec(Actuacion $actuacion)
	{
		// Obtengo "la foto" de la ultima entrada del expediente electronico
		$ult_ee = $actuacion->datos['expe_elec_ultimo'];

		// Obtengo el estado de la ultima entrada actual del expediente electronico
		$act_ee = NG::expedientesElec()->obtenerExpedienteElecUltimo(
			$actuacion->parametros['anio'],
			$actuacion->parametros['tipo'],
			$actuacion->parametros['numero'],
			$actuacion->parametros['cuerpo'],
			$actuacion->parametros['alcance']
		);

		// Comparo nros de orden
		if (is_null($ult_ee) && is_null($act_ee))
			return false;
		else {
			if (!is_null($ult_ee) && !is_null($act_ee))
				return ($ult_ee->orden != $act_ee->orden);
			else
				return true;
		}
	}

	/**
	 * Verifica que la actuación posea como parametros los campos clave de un
	 * expediente y los devuelve como una 'etiqueta' imprimible.
	 * @param  Actuacion $actuacion [description]
	 * @return [type]               [description]
	 */
	protected function obtenerEtiquetaExpediente(Actuacion $actuacion)
	{
		if (is_null($actuacion)) return '';

		if (array_key_exists('anio', $actuacion->parametros) &&
			array_key_exists('tipo', $actuacion->parametros) &&
			array_key_exists('numero', $actuacion->parametros) &&
			array_key_exists('cuerpo', $actuacion->parametros) &&
			array_key_exists('alcance', $actuacion->parametros))
		{
			$tipo_str = '';

			switch ($actuacion->parametros['tipo']) {
				case 'E':	$tipo_str = 'Expediente'; break;
				case 'N':	$tipo_str = 'Nota'; break;
				case 'R':	$tipo_str = 'Recomendación'; break;
			}

			return sprintf('%s %s-%s-%s cpo %s alc %s',
	        	$tipo_str,
	        	$actuacion->parametros['anio'],
				$actuacion->parametros['tipo'],
				$actuacion->parametros['numero'],
				$actuacion->parametros['cuerpo'],
				$actuacion->parametros['alcance']
			);
		} else {
			return '';
		}
	}

	/**
	 * [obtenerExpediente description]
	 * @param  [type]  $panio            [description]
	 * @param  [type]  $ptipo            [description]
	 * @param  [type]  $pnumero          [description]
	 * @param  [type]  $pcuerpo          [description]
	 * @param  [type]  $palcance         [description]
	 * @param  boolean $lanzar_excepcion [description]
	 * @return [type]                    [description]
	 */
	protected function obtenerExpediente($actuacion, $lanzar_excepcion = false)
	{

		// Se obtiene el expediente para verificar su existencia (por las dudas)
		try {
			$expediente = NG::expedientes()->obtenerExpediente(
				$actuacion->parametros['anio'],
				$actuacion->parametros['tipo'],
				$actuacion->parametros['numero'],
				$actuacion->parametros['cuerpo'],
				$actuacion->parametros['alcance']
			);
		}
		catch (Exception $e){
			$expediente = null;
			if ($lanzar_excepcion)
				throw $e;
		}

		// Si el expediente no existe
		if (is_null($expediente) && $lanzar_excepcion)
			throw new Exception('No existe el expediente.');
		else
			return $expediente;
	}

	/**
	 * Si la actuación dispone de los parametros propios para buscar un expediente,
	 * verifica si el expediente esta o no agregado a otro.
	 * Para optimizar el acceso a la db, es posible pasar un expediente ya obtenido
	 * desde la DB como parametro.
	 * @param  [type] $actuacion  Actuacion de la que se desea buscar el expediente
	 * @param  [type] $expediente Si expediente es != null, se utiliza esa instancia para verificar.
	 * @return [type]             [description]
	 */
	protected function esExpedienteAgregado($actuacion, $expediente = null) {
		// Obtengo el expediente a verificar
		$expediente_a_verificar = (is_null($expediente))
			? $this->obtenerExpediente($actuacion, true)
			: $expediente;

		// Si el expediente se encuentra agregado a otro expediente, no se permite su edición
		if ( NG::expedientes()->estaAgregadoA($expediente_a_verificar) )
			return ['No se permite modificar el expediente electrónico porque se encuentra agregado a otro expediente.'];

		return [];
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
		return [];
	}



}
?>
