<?php
/**
 * Capa de negocio base para funcionalidad de PasoActuacionSubirArchivo.
 *
 * @author XXXX
 *
 */
class NGPasoActuacionSubirArchivo extends NGPasoActuacionBase {

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
	// ---- Asignacion de datos de Pasos --------------------------------------
	// ------------------------------------------------------------------------
	/**
	 * Delega la lógica de obtencion de datos para que un paso determinado
	 * disponga de todo lo necesario para generar su vista, por ejemplo, una consulta
	 * a la BD con los posibles firmantes.
	 * @param  Actuacion     $actuacion [description]
	 * @param  PasoActuacion $paso   [description]
	 * @param  Usuario       $pUsuario  [description]
	 */
	public function asignarDatosAPasoActuacion(Actuacion $actuacion, PasoActuacion $paso, Usuario $pUsuario)
	{
		parent::asignarDatosAPasoActuacion($actuacion, $paso, $pUsuario);
	}

	// ------------------------------------------------------------------------
	// ---- Procesamiento de Pasos --------------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Toma un paso en particular y ejecutar su funcion de validacion y
	 * procesamiento (guardar transaccion) en base a los parametros recopilados.
	 * @param  Actuacion     $actuacion [description]
	 * @param  PasoActuacion $paso   [description]
	 * @param  Usuario       $pUsuario  [description]
	 * @param  Array         $params Parámetros del paso, por referencia (esto permite modificar los parametros desde el procesamiento del paso).
	 * @return Array                 Array de errores detectados; si es '[]', no hay errores.
	 */
	public function procesarPasoActuacion(Actuacion $actuacion, PasoActuacion $paso, Usuario $pUsuario, &$params)
	{
		$ops = $paso->opciones;

		$errores = [];
		// ---- Verifico la existencia obligatoria de los parametros
		// En caso de un archivo, se verifica el array 'request_files', y dentro de ese array, el campo del archivo buscado
		$errores = $this->verificarExistenciaParametros(['f_titulo', 'request_files'], $params);
		if (count($errores) > 0) return $errores;

		$archivos = $params['request_files'];
		$errores = $this->verificarExistenciaParametros(['f_archivo'], $archivos);
		if (count($errores) > 0) return $errores;

		// Solo confirmo el titulo si esta permitido y es obligatorio...
	    if ($ops['titulo_permitido'] && $ops['titulo_obligatorio']) {
    	    if (trim($params['f_titulo']) == '') {
        	    $errores[] = 'Debe ingresar un título.';
    	    } else {
	            // Verifico cantidad minima de caracteres (-1 deshabilita el control)
	            if (($ops['titulo_longitud_min'] >= 0) && (strlen($params['f_titulo']) < $ops['titulo_longitud_min']))
	                $errores[] = sprintf('Debe ingresar un título de al menos %d caracteres.', $ops['titulo_longitud_min']);

	            // Verifico cantidad maxima de caracteres (-1 deshabilita el control)
	            if (($ops['titulo_longitud_max'] >= 0) && (strlen($params['f_titulo']) > $ops['titulo_longitud_max']))
	                $errores[] = sprintf('Debe ingresar un título con menos de %d caracteres.', $ops['titulo_longitud_max']);
        	}
	    }

	    // Devolucion parcial de errores
	    if (count($errores) > 0) return $errores;

	    $archivo_extension = '';

		// ---- Intento guardar el archivo seleccionado
		if ($archivos['f_archivo']['error'] != UPLOAD_ERR_NO_FILE) {
			$fileHelper = new FileHelper($archivos, $paso->opciones['directorio_destino']);
			$fileHelper->mimeTypePorExtension = $paso->opciones['mimetype_permitidos'];

			try {
				$archivo_extension = strtolower(pathinfo($archivos['f_archivo']['name'], PATHINFO_EXTENSION));

				// Si NO es una extensión permitida
				if (!in_array($archivo_extension, array_keys($paso->opciones['mimetype_permitidos']))) {
					$errores[] = ['La extensión del archivo es inválida.'];
					return $errores;
				}

				// Se genera el nombre con el que se va a guardar dicho temporal:
				// AATNNNNN_fechahora_sha1.extension
				$nombre_archivo_nuevo = sprintf('%d_%d_%s_%s.%s',
					$paso->id_transaccion,
					$paso->id_paso,
					DateTimeHelper::get()->timestampStr('YmdHisu'),
					sha1_file($archivos['f_archivo']['tmp_name']),
					$archivo_extension
				);

				// Se sube el archivo.
				// Se pasa como primer parametro 'f_archivo': el nombre del elemento dentro del array de archivos (el nombre del input)
				$archivo = $fileHelper->subirArchivoComo(
					'f_archivo',
					$nombre_archivo_nuevo,
					'localhost',
					FTP_LOCAL_USER,
					FTP_LOCAL_PASSWORD,
					0664
				);

				// Actualizo el nombre del archivo en la transaccion para que se refleje
				// el nuevo nombre.
				$params['archivo_subido'] = $paso->opciones['directorio_destino'].$archivo;

			} catch (Exception $exUpload) {
				$errores[] = 'No se pudo cargar el documento. Causa: ' . str_replace('FileHelper.subirArchivoComo: ', '', $exUpload->getMessage());
			}
		} else {
			$errores[] = sprintf("Error genérico al subir el archivo (%s).", $archivos['f_archivo']['error']);
		}

		// Inicializo el lote de archivos subidos como vacio
		// (solamente se utiliza cuando se sube un zip)
		$params['lote_archivos_subidos'] = [];

		// Si el archivo es un .ZIP, ademas lo descompacto.
		// La referencia a $params['archivo_subido'] se mantiene intacta.
		// Se agrega la referencia $params['lote_archivos_subidos'] por si se desea
		// trabajar con el contenido del archivo zip.
		if ($archivo_extension == 'zip') {
			$zip = new ZipArchive();
			if ($zip->open($params['archivo_subido']) === true) {

				// Obtengo solo los archivos .pdf contenidos en el .zip y de paso obtengo la version saneada.
				$archivos_pdf = [];
				$archivos_saneados_pdf = [];
				for ($i = 0; $i < $zip->numFiles; $i++) {
					$zip_file = $zip->getNameIndex($i);
					if (preg_match('/\.pdf$/i', $zip_file)) {
						$archivos_pdf[] = $zip_file;
						$archivos_saneados_pdf[] = sprintf('%s.pdf', preg_replace('/[^A-Za-z0-9\-]/', '_', pathinfo($zip_file, PATHINFO_FILENAME)));
					}
				}

				// Si hay archivos válidos dentro del zip...
				if (count($archivos_pdf) > 0) {
					try {
						$subdir_zip = pathinfo($archivo, PATHINFO_FILENAME);

						FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
						FTPHelper::get()->mkDir($paso->opciones['directorio_destino'], $subdir_zip, 0777);
						FTPHelper::get()->disconnect();

						$directorio_destino = sprintf('%s%s/', $paso->opciones['directorio_destino'], $subdir_zip);
						if ($zip->extractTo($directorio_destino, $archivos_pdf) ) {
							// Por cuestiones de seguridad, filtro los caracteres de los archivos extraidos
							FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
							for ($i = 0; $i < count($archivos_pdf); $i++) {
								FTPHelper::get()->moveFile(
									$directorio_destino.$archivos_pdf[$i],
									$directorio_destino.$archivos_saneados_pdf[$i]
								);
							}
							FTPHelper::get()->disconnect();

							// Agrego la referencia al lote de archivos subidos
							$params['lote_archivos_subidos'] = $this->obtenerContenidoDirectorio($directorio_destino);
						} else
							$errores[] = 'No se pudo cargar el archivo: no se pudo extraer el contenido del archivo zip.';

						$zip->close();

					} catch (Exception $e) {
						$errores[] = sprintf('No se pudo cargar el archivo: no se puede crear la carpeta contenedora para el archivo zip: %s', $e->getMessage());
					}
				} else
					$errores[] = 'El archivo zip no contiene documentos válidos.';
			} else
				$errores[] = 'No se pudo cargar el archivo: no se puede abrir el archivo zip.';
		}

		return $errores;
	}
}
?>
