<?php
/**
 * Clase helper para la manipulación de archivos subidos mediante un form (POST).
 *
 * Dependencias con herramientas del sistema operativo:
 *   - 'file'
 *   - 'cut'
 *
 * @author kaleb
 */
class FileHelper {
	private $archivos;
	public $rutaUpload; //!< Ruta física local del servidor donde se almacenarán los archivos.
	public $mimeTypePorExtension; //!< Array asociativo de los MimeType de archivos permitidos por defecto.

	/**
	 * Constructor de clase. Requiere que se le pase como referencia el array $_FILES.
	 * @param array &$filesReference Referencia al array $_FILES.
	 * @param string $rutaUpload     Ruta física local del servidor donde se almacenarán los archivos.
	 */
	public function __construct(&$filesReference, $rutaUpload) {
		$this->archivos = $filesReference;
		$this->rutaUpload = $rutaUpload;

		// MimeType por defecto.
		$this->mimeTypePorExtension = array(
			'jpg' => 'image/jpeg',
			'png' => 'image/png',
			'gif' => 'image/gif');
	}

	/**
	 * Una llamada al comando 'file' de linux para determinar el mime-type de un archivo.
	 * Se utiliza como método 'fallback' si el mime-type de 'finfo()' de PHP no nos gusta.
	 * @param  [string] $archivo Ruta completa del archivo a obtener el mime-type.
	 * @return [string]          Mime-Type del archivo.
	 */
	protected function cmdGetMimeType($archivo) {
		if (! file_exists($archivo))
			throw new RuntimeException('FileHelper.cmdGetMimeType: no se encuentra el archivo ' . $archivo);

        $cmd = sprintf('file --mime-type %s | cut -d":" -f2', $archivo);

        // ---- Fix para evitar que los acentos salgan rotos ------------------
        $locale = 'es_AR.UTF-8';
        setlocale(LC_ALL, $locale);
        putenv('LC_ALL='.$locale);
        // --------------------------------------------------------------------

        $output = shell_exec("( $cmd ) 2>&1");

        return trim($output);
	}

	/**
	 * Guarda el archivo subido por petición al servidor.
	 * @param  int $idArchivo Identificador del archivo a subir del array $_FILES.
	 * @param  boolean $autoName  Si es true, genera automáticamente el nombre del archivo subido. Si es false, preserva el nombre del archivo original.
	 * @param  string  $newName   Si $autoName es false, es posible renombrar el archivo subido como '$newName'.
	 * @return string            Nombre del archivo fisico generado en el servidor despues de ser subido.
	 */
	public function subirArchivo($idArchivo, $autoName = false, $newName = null) {
		// Undefined | Multiple Files | $_FILES Corruption Attack
		// Si el request cae dentro de alguno de los ataques mencionados, se vuelve inválida la petición.
		if ((!isset($this->archivos[$idArchivo]['error'])) || is_array($this->archivos[$idArchivo]['error'])) {
			throw new RuntimeException('FileHelper.subirArchivo: Par&aacute;metros inv&aacute;lidos.');
		}

		// Verifico el valor de $this->archivos[$idArchivo]['error']
		switch ($this->archivos[$idArchivo]['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
			throw new RuntimeException('FileHelper.subirArchivo: no se ha indicado un archivo para subir.');
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			throw new RuntimeException('FileHelper.subirArchivo: (1) el tama&ntilde;o de archivo excede el l&iacute;mite permitido.');
		default:
			throw new RuntimeException('FileHelper.subirArchivo: error desconocido.');
		}

		// ---- Vuelvo a chequear el tamaño del archivo
		if ($this->archivos[$idArchivo]['size'] > KRAKEN_UPLOAD_MAX_SIZE) {
			throw new RuntimeException('FileHelper.subirArchivo: (2) el tama&ntilde;o de archivo excede el l&iacute;mite permitido.');
		}

		// ---- Verifico contenido (mime-type)
		// NO CONFIAMOS EN EL VALOR DE $this->archivos[$idArchivo]['mime']
		// Hay que chequearlo por nuestra cuenta.

		// 2023-02-22
		// Debido a que los archivos .docx y sus parientes de Microsoft pueden ser (no siempre)
		// una suerte de archivos .zip enmascarados, el 'finfo()' de PHP no los detecta con su
		// mimetype correspondiente. Es posible determinar su mime-type haciendo un maneje con
		// la definición del archivo /etc/magic, pero en la práctica no ha funcionado.
		// A tal efecto, si el 'finfo()' devuelve un mime-type no válido, hacemos un 'fallback'
		// a la herramienta 'file' del sistema operativo, que tiene una tasa de acierto mejor.
		$fileInfo = new finfo(FILEINFO_MIME_TYPE);
		$mime_type_archivo = $fileInfo->file($this->archivos[$idArchivo]['tmp_name']);

		if ( false === $ext = array_search($mime_type_archivo, $this->mimeTypePorExtension, true) )
		{
			// Hacemos el fallback si el 'finfo(...)' no nos gusta a la verificacion por
			// la herramienta 'file' del sistema operativo...
			$mime_type_archivo = $this->cmdGetMimeType($this->archivos[$idArchivo]['tmp_name']);
			if ( false === $ext = array_search($mime_type_archivo, $this->mimeTypePorExtension, true) )
				throw new RuntimeException('FileHelper.subirArchivo: Formato de archivo inv&aacute;lido.');
		}

		//TODO: NO UTILIZAR $this->archivos[$idArchivo]['name'] SIN VALIDACION!!!
		if ($autoName)
		// Se le debe dar nombre al archivo de forma única.
		// En este ejemplo, obtenemos un nombre unico seguro en base a su información binaria.
		{
			$nombreArchivoDestino = sprintf('%s.%s', sha1_file($this->archivos[$idArchivo]['tmp_name']), $ext);
		} else {
			$nombreArchivoDestino = (is_null($newName))
				? $this->archivos[$idArchivo]['name']
				: $newName;
		}

		if (!move_uploaded_file($this->archivos[$idArchivo]['tmp_name'], sprintf('%s%s', $this->rutaUpload, $nombreArchivoDestino))) {
			throw new RuntimeException('FileHelper.subirArchivo: Se produjo un fallo al mover el archivo subido.');
		}

		return $nombreArchivoDestino;
	}

	/**
	 * Guarda el archivo subido por petición al servidor, con funcionalidad extra.
	 * @param  [int]     $idArchivo             Identificador del archivo a subir del array $_FILES.
	 * @param  [string]  $nombreArchivoDestino  Nombre con el que se va a guardar.
	 * @param  [string]  $host                  Host a conectarse por FTP.
	 * @param  [string]  $usuario               Usuario para el FTP.
	 * @param  [string]  $password              Password para el FTP.
	 * @param  [integer] $permisos              Permisos para el FTP.
	 *
	 * @return [string] 	Nombre del archivo fisico generado en el servidor despues de ser subido.
	 */
	public function subirArchivoComo($idArchivo, $nombreArchivoDestino, $host, $usuario, $password, $permisos = 0666) {

		// Undefined | Multiple Files | $_FILES Corruption Attack
		// Si el request cae dentro de alguno de los ataques mencionados, se vuelve inválida la petición.
		if ((!isset($this->archivos[$idArchivo]['error'])) || is_array($this->archivos[$idArchivo]['error'])) {
			throw new RuntimeException('FileHelper.subirArchivoComo: Par&aacute;metros inv&aacute;lidos.');
		}

		// Verifico el valor de $this->archivos[$idArchivo]['error']
		switch ($this->archivos[$idArchivo]['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
			throw new RuntimeException('FileHelper.subirArchivoComo: no se ha indicado un archivo para subir.');
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			throw new RuntimeException('FileHelper.subirArchivoComo: (1) el tama&ntilde;o de archivo excede el l&iacute;mite permitido.');
		default:
			throw new RuntimeException('FileHelper.subirArchivoComo: error desconocido.');
		}

		// ---- Vuelvo a chequear el tamaño del archivo
		if ($this->archivos[$idArchivo]['size'] > KRAKEN_UPLOAD_MAX_SIZE) {
			throw new RuntimeException('FileHelper.subirArchivoComo: (2) el tama&ntilde;o de archivo excede el l&iacute;mite permitido.');
		}

		// ---- Verifico extensión
		$extensionesPermitidas = array_keys($this->mimeTypePorExtension);
		$extensionArchivo = pathinfo($this->archivos[$idArchivo]['name'], PATHINFO_EXTENSION);
		if (array_search($extensionArchivo, $extensionesPermitidas) === false)
		{
			throw new RuntimeException('FileHelper.subirArchivoComo: Se permiten solamente las extensiones: ' . join(', ', $extensionesPermitidas));
		}

		// ---- Verifico contenido (mime-type)
		// No confiamos en el valor de $this->archivos[$idArchivo]['type']
		// Hay que chequearlo por nuestra cuenta.

		// 2023-02-22
		// Debido a que los archivos .docx y sus parientes de Microsoft pueden ser (no siempre)
		// una suerte de archivos .zip enmascarados, el 'finfo()' de PHP no los detecta con su
		// mimetype correspondiente. Es posible determinar su mime-type haciendo un maneje con
		// la definición del archivo /etc/magic, pero en la práctica no ha funcionado.
		// A tal efecto, si el 'finfo()' devuelve un mime-type no válido, hacemos un 'fallback'
		// a la herramienta 'file' del sistema operativo, que tiene una tasa de acierto mejor.
		$fileInfo = new finfo(FILEINFO_MIME_TYPE);
		$mime_type_archivo = $fileInfo->file($this->archivos[$idArchivo]['tmp_name']);

		if ( false === $ext = array_search($mime_type_archivo, $this->mimeTypePorExtension, true) )
		{
			// Hacemos el fallback si el 'finfo(...)' no nos gusta a la verificacion por
			// la herramienta 'file' del sistema operativo...
			$mime_type_archivo = $this->cmdGetMimeType($this->archivos[$idArchivo]['tmp_name']);
			if ( false === $ext = array_search($mime_type_archivo, $this->mimeTypePorExtension, true) )
				throw new RuntimeException('FileHelper.subirArchivoComo: Formato de archivo inv&aacute;lido.');
		}

		//}
		// 06/07/2020 XXXX: Se implementa la funcionalidad de la versión 1
		// --------------------------------------------------------------------
		// Se establece una conexión FTP
		$id_conexion = ftp_connect($host);

		// Se establece el inicio de sesión FTP con usuario y password
		$resultado_login = ftp_login($id_conexion, $usuario, $password);

		// Se cambia al directorio donde se quiere subir el archivo (/var/www/sgl/expedientes/proyectos/temporal/)
		ftp_chdir($id_conexion, $this->rutaUpload);

		$dir_actual = ftp_pwd($id_conexion);

		$archivoOrigen = $this->archivos[$idArchivo]['tmp_name'];

		$archivoDestino = sprintf('%s%s', $this->rutaUpload, $nombreArchivoDestino);

		ftp_put($id_conexion, $archivoDestino, $archivoOrigen, FTP_BINARY);

		ftp_close($id_conexion);

		return $nombreArchivoDestino;
	}

}
?>
