<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpedienteSubirArchivoTrabajo.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpedienteSubirArchivoTrabajo extends NGActuacionBase {

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
		// Se obtiene el expediente para verificar su existencia (por las dudas)
		$expediente = $this->obtenerExpediente($actuacion);

		// Si el expediente no existe
		if (is_null($expediente))
			return ['No existe el expediente donde desea agregar un documento de trabajo.'];

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
		// Extra: obtengo el expediente de trabajo
		$expediente = NG::expedientes()->obtenerExpediente(
			$actuacion->parametros['anio'],
			$actuacion->parametros['tipo'],
			$actuacion->parametros['numero'],
			$actuacion->parametros['cuerpo'],
			$actuacion->parametros['alcance']
		);
		if (is_null($expediente))
			return [sprintf("El expediente '%s-%s-%s cpo. %s alc. %s' no existe.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'])];

		// Obtengo las transacciones de los pasos de la actuacion
		$trans = NG::transacActuaciones()->obtenerTransacActuaciones($actuacion->id_transaccion);

		// Debo tener la misma cantidad de transacciones que de pasos
		if (count($trans) != count($actuacion->pasos))
			return [sprintf('Error en transacción %s: la cantidad de pasos difiere.', $actuacion->id_transaccion)];

		// Paso 0: Subir Archivo (lote de archivos)
		$data = json_decode($trans[0]->data);
		$archivo_subido = $data->archivo_subido;
		$nombre_archivo_original = $data->request_files->f_archivo->name;
		$lote_archivos_subidos = $data->lote_archivos_subidos; // en caso de un zip, tiene elementos

		// Paso 1: Alcanzado por Dec. 1404
		$data = json_decode($trans[1]->data);
		$alcanza_dec1404 = $data->f_op_decreto == 1;

		// Paso 2: Observaciones
		$data = json_decode($trans[2]->data);
		$observaciones = $data->f_texto;

		// ---- Verifico que todo este bien -----------------------------------

		// ---- Procesamiento del archivo subido ------------------------------
		// Si el archivo subido es un '.zip'
		if (preg_match('/\.zip$/i', $archivo_subido)) {
			// ---- Validaciones de Lote
			if (count($lote_archivos_subidos) == 0)
				return ['No hay archivos en el lote de documentos para firma.'];

			foreach ($lote_archivos_subidos as $archivo_lote)
				if (! file_exists($archivo_lote))
					return [sprintf('El documento "%s" perteneciente al lote no existe.', basename($archivo_lote))];

			// ---- Procesamiento del lote ----------------------------------------
			$timestamp = DateTimeHelper::get()->timestampInstance();

			// Nombre del contenedor temporal del .pdf "no firmado"
			$pdf_temp_filename = sprintf('%stemp_%d%s%05d%02d%02d_%s.pdf',
				PATH_SGL_DOC_TEMPORALES,
				$expediente->anio,
				$expediente->tipo,
				$expediente->numero,
				$expediente->cuerpo,
				$expediente->alcance,
				$timestamp->format('YmdHisu')
			);

			// Tomo todos los archivos y preparo la lista de documentos a concatenar.
			$documentos = [];

			foreach ($lote_archivos_subidos as $archivo_lote) {
				$documentos[] = sprintf("'%s'", $archivo_lote);

				// Ademas, si el documento posee otros PDFs embebidos, los extraigo y los agrego a la lista.
				$embebidos = PDFExtractor::get()->getAttachments($archivo_lote);
				if (count($embebidos['pdf']) > 0)
					foreach ($embebidos['pdf'] as $emb) {
						$emb_tmp_file = PDFExtractor::get()->extractFile($archivo_lote, $emb['id']);
						$documentos[] = sprintf("'%s'", $emb_tmp_file);
					}
			}

			// Se define el comando para unir las digitalizaciones, utilizando el comando gs (ghostscript)
			$cmd = sprintf("gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile='%s' %s",
				$pdf_temp_filename,
				join(' ', $documentos)
			);

			// Se ejecuta el comando
			$cmd_result = shell_exec($cmd);

			// Verifico errores
			if (! file_exists($pdf_temp_filename))
				return [sprintf('No se encuentra el contenedor pdf temporal: %s', $pdf_temp_filename)];

			// Si todo esta bien, muevo el archivo a su destino final y vuelvo a verificar
			try {
				$file_info = pathinfo(basename($nombre_archivo_original));
				$subdir_expediente = sprintf('%s%s',
					$this->obtenerSubDirectorioExpediente($expediente),
					($alcanza_dec1404) ? 'reservados/' : ''
				);
				$doc_destino = sprintf('%s%s%s.pdf',
					PATH_KRAKEN_RESOURCES_PROYECTOS,
					$subdir_expediente,
					preg_replace('/[^A-Za-z0-9\-\.]/', '_', $file_info['filename'])
				);
				FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
				if ($alcanza_dec1404 && !is_dir(PATH_KRAKEN_RESOURCES_PROYECTOS.$subdir_expediente) ) {
					FTPHelper::get()->mkDir(PATH_KRAKEN_RESOURCES_PROYECTOS, $subdir_expediente, 0777);
					FTPHelper::get()->chmod(PATH_KRAKEN_RESOURCES_PROYECTOS.$subdir_expediente, 0755);
				}
				FTPHelper::get()->moveFile($pdf_temp_filename, $doc_destino, 0644);
				FTPHelper::get()->disconnect();
			} catch (Exception $e) {
				return [sprintf('Error al mover archivo: %s', $e->getMessage())];
			}

		} else { // Si el archivo subido NO ES un '.zip'
			try {
				$subdir_expediente = sprintf('%s%s',
					$this->obtenerSubDirectorioExpediente($expediente),
					($alcanza_dec1404) ? 'reservados/' : ''
				);
				$doc_destino = sprintf('%s%s%s',
					PATH_KRAKEN_RESOURCES_PROYECTOS,
					$subdir_expediente,
					preg_replace('/[^A-Za-z0-9\-\.]/', '_', basename($nombre_archivo_original))
				);
				FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
				if ($alcanza_dec1404 && !is_dir(PATH_KRAKEN_RESOURCES_PROYECTOS.$subdir_expediente) ) {
					FTPHelper::get()->mkDir(PATH_KRAKEN_RESOURCES_PROYECTOS, $subdir_expediente, 0777);
					FTPHelper::get()->chmod(PATH_KRAKEN_RESOURCES_PROYECTOS.$subdir_expediente, 0755);
				}
				FTPHelper::get()->moveFile($archivo_subido, $doc_destino, 0644);
				FTPHelper::get()->disconnect();
			} catch (Exception $e) {
				return [sprintf('Error al mover archivo: %s', $e->getMessage())];
			}
		}

		//Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Doc destino' => $doc_destino,
			'Alcanzado Dec1404' => ($alcanza_dec1404) ? 'si' : 'no',
			'Observaciones' => $observaciones
		];

		return [];
	}

}
?>
