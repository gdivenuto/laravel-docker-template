<?php
/**
 * Clase que implementa el patrón singleton para centralización de la lógica de
 * manipulación de archivos desde PHP con FTP.
 *
 */
class FTPHelper {
	private static $instance;
	private $host;
	private $user;
	private $password;
	private $ftpConn;

	/**
	 * Constructor privado, parte funcional del patron Singleton.
	 */
	private function __construct() {
		$this->host = 'localhost';
		$this->user = 'anonymous';
		$this->password = '';

		$this->ftpConn = null;
	}

	/**
	 * Realiza un login contra localhost para la impesonalización de acciones de archivos.
	 * @return resource Link a la conexion FTP.
	 */
	public function connect($pHost = 'localhost', $pUser = 'anonymous', $pPassword = '') {
		$this->host = $pHost;
		$this->user = $pUser;
		$this->password = $pPassword;

		$this->ftpConn = ftp_connect($this->host);

		if (!$this->ftpConn) {
			throw new Exception('Error al establecer conexi&oacute;n al servicio de manipulación de archivos.');
		}

		// Login
		if (!ftp_login($this->ftpConn, $this->user, $this->password)) {
			throw new Exception('Error de validaci&oacute;n de credenciales al acceder al servicio de manipulación de archivos.');
		}

		return $this->ftpConn;
	}

	/**
	 * Crea un directorio recursivamente.
	 * @param  string  $dirName Nombre del directorio a crear.
	 * @param  integer $dirPerm Permisos de acceso al directorio (chmod)
	 * @return [type]           [description]
	 */
	public function mkDir($dirBase, $subDirName, $subDirPerm = 0777) {
		if (is_null($this->ftpConn)) {
			throw new Exception('No conectado al servicio de manipulación de archivos.');
		}

		@ftp_chdir($this->ftpConn, $dirBase); // /var/www/uploads

		$dirs = explode('/', $subDirName);

		foreach ($dirs as $dir)
		{
			if ($dir != '') {
				// Si no existe el directorio, lo creo.
				if (!@ftp_chdir($this->ftpConn, $dir)) {
					ftp_mkdir($this->ftpConn, $dir);
					ftp_chmod($this->ftpConn, $subDirPerm, $dir);
					ftp_chdir($this->ftpConn, $dir);
				}
			}
		}

		if (!is_dir($dirBase.$subDirName))
			throw new Exception("Error al crear directorio; base: $dirBase subdir: $subDirName");
	}

	/**
	 * Cambia los permisos sobre un archivo/directorio.
	 * @param  [type]  $archivo [description]
	 * @param  integer $perm    [description]
	 * @return [type]           [description]
	 */
	public function chmod($archivo, $perm = 0777) {
		if (is_null($this->ftpConn)) {
			throw new Exception('No conectado al servicio de manipulación de archivos.');
		}

		ftp_chmod($this->ftpConn, $perm, $archivo);
	}

	/**
	 * Crea un directorio para albergar archivos de proyecto.
	 * @param  string $dirName Nombre del directorio a crear dentro de PATH_KRAKEN_RESOURCES_PROYECTOS (sin barra final)
	 * @param  integer $dirPerm Permisos de acceso al directorio (chmod)
	 * @return [type]           [description]
	 */
	public function mkDirProyecto($dirName, $dirPerm = 0777) {
		if (is_null($this->ftpConn)) {
			throw new Exception('No conectado al servicio de manipulación de archivos.');
		}

		if (!@ftp_chdir($this->ftpConn, PATH_KRAKEN_RESOURCES_PROYECTOS)) // ej: /var/www/sgl2/resources/proyectos/
		{
			throw new Exception('No existe el directorio base de proyectos.');
		}

		$dirs = explode('/', $dirName); // ej: 2017/17E01050

		foreach ($dirs as $dir)
		// Si no existe el directorio, lo creo.
		{
			if (!@ftp_chdir($this->ftpConn, $dir)) {
				if (!ftp_mkdir($this->ftpConn, $dir)) {
					throw new Exception('No se puede crear el directorio ' . $dir);
				} else {
					ftp_chdir($this->ftpConn, $dir);
				}
			}
		}
	}

	/**
	 * Mueve un archivo de un directorio a otro.
	 * @param  string  $origen   Archivo origen
	 * @param  string  $destino  Archivo destino
	 * @param  integer $permisos Permisos del archivo destino
	 * @return [type]            [description]
	 */
	public function moveFile($origen, $destino, $permisos = 0666) {
		if (is_null($this->ftpConn)) {
			throw new Exception('No conectado al servicio de manipulación de archivos.');
		}
		if (!ftp_rename($this->ftpConn, $origen, $destino)) {
			throw new Exception('No se puede mover el archivo.');
		}
	}

	/**
	 * 15/07/2020 XXXX
	 * Crea un directorio para albergar archivos de proyecto.
	 * @param  string $dirName Nombre del directorio a crear dentro de PATH_KRAKEN_RESOURCES_PROYECTOS (sin barra final)
	 * @param  integer $dirPerm Permisos de acceso al directorio (chmod)
	 * @return [type]           [description]
	 */
	public function crearDirectorioProyecto($dirName, $dirPerm = 0777) {
		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		if (is_null($id_conexion) || is_null($resultado_login)) {
			throw new Exception('No conectado al servicio de manipuilación de archivos.');
		}

		if (!@ftp_chdir($id_conexion, PATH_KRAKEN_RESOURCES_PROYECTOS)) // ej: /var/www/sgl2/resources/proyectos/
		{
			throw new Exception('No existe el directorio base de proyectos.');
		}

		$dirs = explode('/', $dirName); // ej: 2017/17E01050

		foreach ($dirs as $dir)
		// Si no existe el directorio, lo creo.
		{
			if (!@ftp_chdir($id_conexion, $dir)) {
				if (!ftp_mkdir($id_conexion, $dir)) {
					throw new Exception('No se puede crear el directorio ' . $dir);
				} else {
					ftp_chdir($id_conexion, $dir);
				}
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);
	}

	/**
	 * 15/07/2020 XXXX
	 * Se carga el archivo temporal en el directorio del expediente respectivo
	 * @param  [type]  $origen   [description]
	 * @param  [type]  $destino  [description]
	 * @param  integer $permisos [description]
	 */
	public function moveFileTemporal($origen, $destino, $permisos = 0666) {
		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		if (is_null($id_conexion) || is_null($resultado_login)) {
			throw new Exception('No conectado al servicio de manipulación de archivos.');
		}

		// Se carga el archivo origen (el temporal) en el directorio destino
		if (!ftp_put($id_conexion, $destino, $origen, FTP_BINARY)) {
			throw new Exception('No se puede cargar el archivo.');
		} else {
			// Se elimina el temporal del directorio "proyectos/temporal/" del expediente respectivo
			if (file_exists($origen)) {
				ftp_delete($id_conexion, $origen);
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);
	}

	/**
	 * Elimina un archivo
	 * @param  [type] $archivo [description]
	 * @return [type]          [description]
	 */
	public function delete($archivo) {
		if (is_null($this->ftpConn)) {
			throw new Exception('No conectado al servicio de manipulación de archivos.');
		}
		if (!ftp_delete($this->ftpConn, $archivo)) {
			throw new Exception('No se puede eliminar el archivo.');
		}
	}

	/**
	 * Desconecta del servicio FTP
	 * @return [type] [description]
	 */
	public function disconnect() {
		ftp_close($this->ftpConn);
		$this->ftpConn = null;
	}

	/**
	 * Se implementa el patrón Singleton para mantener una única instancia y poder acceder a sus
	 * valores desde cualquier script.
	 * @return Logger Instancia de la clase.
	 */
	public static function GetInstance() {
		// Si la instancia no esta definida la creo, sino devuelvo la existente
		if (!isset(self::$instance)) {
			$claseActual = __CLASS__; // Obtengo la clase actual
			self::$instance = new $claseActual; // Creo una instancia
		}

		// Devuelvo la instancia existente.
		return self::$instance;
	}

	/**
	 * Alias de GetInstance()
	 * @return Logger Instancia de la clase.
	 */
	public static function get() {
		return self::GetInstance();
	}

	/**
	 * Es invocado cuando se clona un instancia.
	 * Con este método podemos emitir un mensaje de error y proceder a detener la ejecución del
	 * script por operación inválida al intentar clonar una instancia de Singleton.
	 *
	 * E_USER_ERROR: constante que contiene el mensaje de error generado por el usuario
	 */
	public function __clone() {
		trigger_error("Operación Inválida: No se puede clonar una instancia de " . get_class($this) . ".", E_USER_ERROR);
	}

	/**
	 * __sleep es invocado cuando un objeto es serializado se evita serializar una instancia de
	 * Singleton
	 */
	public function __sleep() {
		trigger_error("No se puede serializar una instancia de " . get_class($this) . ".");
	}

	/**
	 * __wakeup es invocado cuando un objeto es deserializado se evita deserializar una instancia
	 * de Singleton
	 */
	public function __wakeup() {
		trigger_error("No se puede deserializar una instancia de " . get_class($this) . ".");
	}
}
?>
