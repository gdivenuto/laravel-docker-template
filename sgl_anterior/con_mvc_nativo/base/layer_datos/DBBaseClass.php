<?php
require_once PATH_KRAKEN_LAYER_DATOS_CONFIG . 'db_config.php';

// Constantes de manipulacion de datos
define("ORDEN_ASCENDENTE", "ASC");
define("ORDEN_DESCENDENTE", "DESC");

// Constantes de uso para DBBaseClass
define("LEVEL_UP", 1);
define("LEVEL_DOWN", -1);

/**
 * Clase base (abstracta) para toda las capas de acceso a datos (persistencia).
 * Posee los métodos primitivos comunes a todas las capas de acceso a datos específicas.
 * Esta clase implementa el acceso a datos utilizando MySQLi con los drivers básicos
 * de PHP, llamados MySQL Client Library, el cual es menos performante y no permite
 * el uso de funciones mejoradas. Funciona por defecto en PHP 5.3.x
 */
abstract class DBBaseClass {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	// Referencia a la conexion mediante las funciones de mysqli
	protected $mysqli_conn;

	// Flag para determinar si la conexion esta abierta, y el nivel de anidamiento de las conexiones
	protected $db_connection_level;

	// Flag para determinar el nivel de anidamiento de las transacciones
	protected $db_transaction_level;

	// Se utilizan los privilegios de la DB, como en la versión 1 de SGL
	protected $db_privilegios;

	// Parametros de conexion a la db por defecto
	protected $dbServer;
	protected $dbUsername;
	protected $dbPassword;
	protected $dbDatabase;

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase. Inicializa, entre otras cosas, los parametros de conexión a la base de datos y las
	 * constantes de nombres de tablas.
	 */
	function __construct() {
		// Parametros de conexion por defecto.
		// Si una clase hija tuviera que utilizar otros, solo debe reescribirlos en su constructor.
		$this->dbServer = DB_SERVER;
		$this->dbUsername = DB_USERNAME;
		$this->dbPassword = DB_PASSWORD;
		$this->dbDatabase = DB_DATABASE;

		// Flags
		$this->db_connection_level = 0;
		$this->db_transaction_level = 0;

		// El privilegio a utilizar se determina en base al perfil del usuario según el sistema elegido.
		// Un usuario puede poseer uno o más perfiles, según los sistemas al que tenga permiso de acceso.
		$this->db_privilegios = Array(
			"0_user" => "hcd_user", // Se utiliza en el Login para verificar existencia del usuario y sus perfiles en cada sistema
			"0_pass" => "user7654",
			"1_user" => "hcd_nivel1", // Perfil 1: ADMINISTRADOR
			"1_pass" => "nivel1_3478",
			"2_user" => "hcd_nivel2", // Perfil 2: ALTAS-MODIFICACIONES
			"2_pass" => "nivel2_4590",
			"3_user" => "hcd_nivel3", // Perfil 3: CONSULTAS
			"3_pass" => "nivel3_2367",
			"4_user" => "hcd_nivel4", // Perfil 4: CONSULTAS WEB
			"4_pass" => "nivel4_6917",
			"5_user" => "hcd_nivel5", // Perfil 5: GESTION GIROS (SECRETARIO HCD)
			"5_pass" => "nivel5_8130",
			"10_user" => "hcd_nivel1", // Perfil 10: AREA ADMINISTRACION
			"10_pass" => "nivel1_3478",
			"11_user" => "hcd_nivel1", // Perfil 11: AREA BIBLIOTECA
			"11_pass" => "nivel1_3478",
			"12_user" => "hcd_nivel1", // Perfil 12: AREA COMISIONES
			"12_pass" => "nivel1_3478",
			"14_user" => "hcd_nivel1", // Perfil 14: AREA INFORMATICA
			"14_pass" => "nivel1_3478",
			"15_user" => "hcd_nivel1", // Perfil 15: AREA PRENSA
			"15_pass" => "nivel1_3478"
		);
	}

	/**
	 * Determina si hay una conexión establecida al motor de base de datos.
	 * @return boolean TRUE si la conexión se encuentra establecida, FALSE en caso contrario.
	 */
	protected function isDbConnected() {
		return $this->db_connection_level > 0;
	}

	/**
	 * Para soportar conexiones 'anidadas', este método se invoca al 'aumentar' o 'disminuir' el nivel de anidamiento
	 * en las conexiones.
	 * @param integer $sentido Parámetro que indica si se 'aumenta' o 'dismuye' el nivel de anidamiento. Pueden utilizarse las constantes LEVEL_UP o LEVEL_DOWN repectivamente.
	 */
	protected function setDbConnectionLevel($sentido = LEVEL_UP) {
		if ($sentido == LEVEL_UP)
			$this->db_connection_level++;
		else
			$this->db_connection_level = ($this->db_connection_level > 0) ? $this->db_connection_level - 1 : 0;
	}

	/**
	 * Para soportar transacciones 'anidadas', este método se invoca al 'aumentar' o 'disminuir' el nivel de anidamiento
	 * en las transacciones.
	 * @param integer $sentido Parámetro que indica si se 'aumenta' o 'dismuye' el nivel de anidamiento. Pueden utilizarse las constantes LEVEL_UP o LEVEL_DOWN repectivamente.
	 */
	protected function setDbTransactionLevel($sentido = LEVEL_UP) {
		if ($sentido == LEVEL_UP)
			$this->db_transaction_level++;
		else
			$this->db_transaction_level = ($this->db_transaction_level > 0) ? $this->db_transaction_level - 1 : 0;
	}

	/**
	 * Conecta al motor de persistencia.
	 *
	 * Se utiliza la funcionalidad de la versión 1 de SGL,
	 * para conectarse con el usuario del Perfil determinado para el Sistema de Expedientes.
	 *
	 * En el Login del usuario, se utilizan las credenciales definidas en el archivo de configuración db_config.php
	 *
	 * Una vez que haya elegido un sistema el usuario, se conectará según el perfil que posea en dicho sistema.
	 *
	 * @param  boolean $modoAutocommit Activa o desactiva el modo de autocommit para transacciones.
	 * @throws RuntimeException
	 */
	public function conectar($modoAutocommit = true) {
		// Conecto solamente en el nivel 0
		if ($this->db_connection_level == 0) {
			// Si se conoce el perfil para el sistema de Expedientes del usuario autenticado
			if (isset($_SESSION['perfil2'])) {
				// Se reasigna el usuario y su password en base a dicho perfil
				$this->dbUsername = $this->db_privilegios[$_SESSION['perfil2'] . "_user"];
				$this->dbPassword = $this->db_privilegios[$_SESSION['perfil2'] . "_pass"];
			}
			//fputs(fopen("dbUsername.txt", 'w'), print_r($this->dbUsername, true));
			//fputs(fopen("dbPassword.txt", 'w'), print_r($this->dbPassword, true));

			$this->mysqli_conn = new mysqli($this->dbServer, $this->dbUsername, $this->dbPassword, $this->dbDatabase);

			// Cambiamos el juego de caracteres, para que acepte caracteres especiales.
			$this->mysqli_conn->set_charset("utf8");

			/* comprobar la conexión */
			if ($this->mysqli_conn->connect_errno)
				throw new RuntimeException("Error en layer_datos:" . $this->mysqli_conn->connect_error);

			$this->setDbConnectionLevel(LEVEL_UP); // Aumento el nivel de anidamiento de conexion

			// Aplico el modo de autocommit, si la version de MySQL y PHP lo soportan
			$this->autoCommit($modoAutocommit);

		} else
			$this->setDbConnectionLevel(LEVEL_UP); // Aumento el nivel de anidamiento de conexion
	}

	/**
	 * Desconecta del motor de persistencia.
	 */
	public function desconectar() {
		// Desconecto solamente en el nivel 1
		if ($this->db_connection_level == 1)
			$this->mysqli_conn->close();

		$this->setDbConnectionLevel(LEVEL_DOWN); // Disminuyo el nivel de anidamiento de conexion
	}

	/**
	 * Indica si la configuración actual del servidor permite el uso de transacciones con MySQL.
	 * Requiere estar conectado al motor de base de datos.
	 * @return boolean TRUE si la version de PHP es superior a la 5.5.0, y la de MySQL es superior a la 5.6.x. FALSE en caso contrario.
	 */
	private function transaccionesPermitidas() {
		return (version_compare(PHP_VERSION, '5.5.0') >= 0) && ($this->mysqli_conn->server_version >= 50600);
	}

	/**
	 * Activa o desactiva el modo de autocommit para transacciones. Aplica solamente cuando la capa de datos
	 * se encuentra conectada. http://php.net/manual/es/mysqli.autocommit.php
	 * @param  boolean $modo Modo de autocommit.
	 */
	public function autoCommit($modo = true) {
		if ($this->isDbConnected()) {
			if ($this->transaccionesPermitidas())
				$this->mysqli_conn->autocommit($modo);
		} else
			throw new Exception(sprintf("Error en %s.autoCommit: Debe conectar al motor de persistencia antes de establecer el modo de transacci&oacute;n.", get_class($this)));
	}

	/**
	 * Inicializa una transacción en el motor de persistencia. Aplica solamente cuando la capa de datos
	 * se encuentra conectada. http://php.net/manual/es/mysqli.begin-transaction.php
	 * @param  boolean $soloLectura Si es verdadero, aplica MYSQLI_TRANS_START_READ_ONLY, sino MYSQLI_TRANS_START_READ_WRITE
	 */
	public function iniciarTransaccion($soloLectura = true) {
		if ($this->isDbConnected()) {

			if ($this->transaccionesPermitidas()) {
				// Las transacciones se inician solamente en el nivel 0
				if ($this->db_transaction_level == 0) {

					$modoTransaccion = ($soloLectura) ? MYSQLI_TRANS_START_READ_ONLY : MYSQLI_TRANS_START_READ_WRITE;

					if (!$this->mysqli_conn->begin_transaction($modoTransaccion))
						throw new Exception(sprintf("Error grave en %s.iniciarTransaccion: No pudo iniciarse la transacci&oacute;n.", get_class($this)));
				}

				$this->setDbTransactionLevel(LEVEL_UP); // Aumento un nivel de transacción
			}

		} else
			throw new Exception(sprintf("Error en %s.iniciarTransaccion: Debe conectar al motor de persistencia antes de iniciar la transacci&oacute;n.", get_class($this)));
	}

	/**
	 * Guarda los cambios realizados de la transacción actual en el motor de persistencia. Aplica solamente cuando la capa de datos
	 * se encuentra conectada. http://php.net/manual/es/mysqli.commit.php
	 */
	public function guardarTransaccion() {
		if ($this->isDbConnected()) {

			if ($this->transaccionesPermitidas()) {
				// Las transacciones se guardan solamente en el nivel 1
				if ($this->db_transaction_level == 1) {
					if (!$this->mysqli_conn->commit()) {
						throw new Exception(sprintf("Error en %s.guardarTransaccion: No pudo guardarse la transacci&oacute;n.", get_class($this)));
					}
				}

				$this->setDbTransactionLevel(LEVEL_DOWN); // Disminuyo un nivel de transacción
			}
		} else {
			throw new Exception(sprintf("Error en %s.guardarTransaccion: Debe conectar al motor de persistencia antes de guardar la transacci&oacute;n.", get_class($this)));
		}

	}

	/**
	 * Cancela los cambios realizados de la transacción actual en el motor de persistencia. Aplica solamente cuando la capa de datos
	 * se encuentra conectada. http://php.net/manual/es/mysqli.rollback.php
	 */
	public function cancelarTransaccion() {
		if ($this->isDbConnected()) {

			if ($this->transaccionesPermitidas()) {
				// Las transacciones se cancelan solamente en el nivel 1
				if ($this->db_transaction_level == 1) {
					if (!$this->mysqli_conn->rollback()) {
						throw new Exception(sprintf("Error en %s.cancelarTransaccion: No pudo cancelarse la transacci&oacute;n.", get_class($this)));
					}
				}

				$this->setDbTransactionLevel(LEVEL_DOWN); // Disminuyo un nivel de transacción
			}

		} else {
			throw new Exception(sprintf("Error en %s.cancelarTransaccion: Debe conectar al motor de persistencia antes de cancelar la transacci&oacute;n.", get_class($this)));
		}

	}

	/**
	 * Transforma un array a codificación de caracteres utf8 de manera recursiva.
	 * @param array $dat Array a convertir a codificación de caracteres utf8.
	 * @return array El mismo array enviado como parámetro, con la codificación de caracteres actualizada a utf8.
	 */
	protected function arrayUTF8EncodeRecursive(&$dat) {
		/**/
		array_walk_recursive($dat, function (&$entry) {
			// Si no es utf8, lo convierto
			if (is_string($entry)) {
				if (!mb_detect_encoding($entry, 'UTF-8', true)) {
					$entry = mb_convert_encoding($entry, 'UTF-8');
				}

				// $entry = html_entity_decode($entry);
			}
		});
		return $dat;
		/*
			if (is_string($dat)) {
				if (mb_detect_encoding($dat, 'UTF-8', true))
					return $dat;
				else
					return utf8_encode($dat);
			}

			if (is_object($dat)) {
				$ovs= get_object_vars($dat);
				$new=$dat;
				foreach ($ovs as $k =>$v)    {
					$new->$k=$this->arrayUTF8EncodeRecursive($new->$k);
				}
				return $new;
			}
			return $new;
		}

			if (!is_array($dat)) return $dat;
			$ret = array();
			foreach($dat as $i=>$d) $ret[$i] = $this->arrayUTF8EncodeRecursive($d);
			return $ret;
		*/
	}

	/**
	 * Convierte un valor booleano (que permita nulos) a entero, para poder ser guardado en MySQL.
	 * @param  boolean $boolValue Valor booleano a convertir a entero.
	 * @return int            Valor entero correspondiente al booleano indicado como parámetro.
	 */
	protected function boolToInt($boolValue) {
		return (is_null($boolValue)) ? null : intval($boolValue);
	}

	/**
	 * Toma una instancia de SelectQueryBuilder y la ejecuta utilizando la conexión especificada.
	 * @param  SelectQueryBuilder $builder   Instancia de SelectQueryBuilder de la cual se espera este configurada la consulta a realizar contra el motor de base de datos.
	 * @param  mysqli             $conexion  Instancia de conexión a base de datos. Si fuera 'null', utiliza la conexión por defecto de la clase ($mysqli_conn).
	 * @return array              Array (codificado en utf8) que contiene el resultado de la consulta.
	 */
	protected function obtenerResultadosConsulta(SelectQueryBuilder $builder, $conexion = null) {
		// antes que nada, seteo la conexion por defecto
		if ($conexion === null) {
			$conexion = $this->mysqli_conn;
		}

		// Genero el query
		$query = $builder->getQuery(); // parametros con AND

		// preparo y ejecuto la consulta
		$filas = array();
		if ($sentencia = $conexion->prepare($query)) {

			// obtengo los parametros para el bind_params
			$parametrosBind = $builder->criteriosWhere->generarBindParams();

			// Uso "call_user_func_array", dado que $sentencia->bind_param('s', $param); no soporta un array de parametros.
			// Ademas, solamente lo invoco si es que tengo parametros...
			$bindparam_result = true;
			if (count($parametrosBind) > 0) {
				$bindparam_result = call_user_func_array(array($sentencia, 'bind_param'), $parametrosBind);
			}

			if (!$bindparam_result) {
				throw new Exception(sprintf("Error grave en %s.obtenerResultadosConsulta: asociaci&oacute;n de par&aacute;metro inv&aacute;lida.", get_class($this)));
			}

			// ejecutar la sentencia
			$sentencia->execute();

			if ($sentencia->error != '') {
				throw new Exception(sprintf("Error grave en %s.obtenerResultadosConsulta: %s", get_class($this), $conexion->error));
			}

			// Obtengo los metadatos de la consulta, para posteriormente hacer un bind_result "dinámico" sin necesidad
			// de tener instalado el driver nativo de mysqli
			// Referencia: http://stackoverflow.com/questions/994041/how-can-i-put-the-results-of-a-mysqli-prepared-statement-into-an-associative-arr
			$metadata = $sentencia->result_metadata();
			while ($campoMetadata = $metadata->fetch_field()) {
				// creo la variable que va a contener el valor del campo en el fetch
				$columnas[$campoMetadata->name] = null;
				// referencio la variable para poder pasarlas por referencia al "call_user_func_array"
				$columnasRef[] = &$columnas[$campoMetadata->name];
			}

			// uso "call_user_func_array", dado que $sentencia->bind_result(...); no soporta un array de parametros
			call_user_func_array(array($sentencia, 'bind_result'), $columnasRef);

			// obtener los valores de los campos para la consulta
			$auxArray = array(); // array auxiliar para "copiar" resultados a la salida
			while ($sentencia->fetch()) {
				// Copio cada variable al array auxiliar
				foreach ($columnas as $campo => $valor) {
					$auxArray[$campo] = $valor;
				}

				$filas[] = $auxArray; // inserto el array en la coleccion de filas resultante
			}

			$sentencia->close();
		} else {
			throw new Exception(sprintf("Error grave en %s.obtenerResultadosConsulta: %s", get_class($this), $conexion->error));
		}

		return $this->arrayUTF8EncodeRecursive($filas);
	}

	/**
	 * Toma un SaveQueryBuilder y ejecuta un query del cual no se espera resultados.
	 * @param  SaveQueryBuilder $builder  Instancia de SaveQueryBuilder de la cual se espera este configurada la consulta a realizar contra el motor de base de datos.
	 * @param  mysqli           $conexion Instancia de conexión a base de datos. Si fuera 'null', utiliza la conexión por defecto de la clase ($mysqli_conn).
	 * @return int           En caso de consultas INSERT/UPDATE que poseen autoincrementales, devuelve el valor del registro insertado. En caso de DELETE, devuelve la cantidad de filas afectadas.
	 */
	protected function ejecutarNoConsulta(BaseCRUDQueryBuilder $builder, $conexion = null) {
		// antes que nada, seteo la conexion por defecto
		if ($conexion === null) {
			$conexion = $this->mysqli_conn;
		}

		// Genero el query
		$query = $builder->getQuery();

		$resultadoEjecucion = 0;

		// preparo y ejecuto la consulta
		if ($sentencia = $conexion->prepare($query)) {

			// obtengo los parametros para el bind_params
			$parametrosBind = $builder->generarBindParams();

			// Uso "call_user_func_array", dado que $sentencia->bind_param('s', $param); no soporta un array de parametros.
			// Ademas, solamente lo invoco si es que tengo parametros...
			$bindparam_result = true;
			if (count($parametrosBind) > 0) {
				$bindparam_result = call_user_func_array(array($sentencia, 'bind_param'), $parametrosBind);
			}

			if (!$bindparam_result) {
				throw new Exception(sprintf("Error grave en %s.ejecutarNoConsulta: asociaci&oacute;n de par&aacute;metro inv&aacute;lida.", get_class($this)));
			}

			// ejecutar la sentencia
			$sentencia->execute();

			if ($sentencia->error != '') {
				throw new Exception(sprintf("Error grave en %s.ejecutarNoConsulta: %s", get_class($this), $conexion->error));
			}

			// Depende del tipo de query que estoy ejecutando, devuelvo un resultado.
			if ($builder instanceof SaveQueryBuilder) {
				$resultadoEjecucion = $conexion->insert_id;
			}
			// obtengo el autoincremental generado, si corresponde
			else if ($builder instanceof DeleteQueryBuilder) {
				// devuelvo la cantidad de filas afectadas
				$resultadoEjecucion = $conexion->affected_rows;
			}

			$sentencia->close();

		} else {
			throw new Exception(sprintf("Error grave en %s.ejecutarNoConsulta: %s", get_class($this), $conexion->error));
		}

		return $resultadoEjecucion;
	}
}
?>
