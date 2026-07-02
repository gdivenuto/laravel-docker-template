<?php
/* CLASE GENERAL DE FUNCIONES PHP PARA LA APLICACION
---------------------------------------------------- */
class LibreriaGeneral {

	private static $instancia;

	/**
	 * Se implementa el patrón Singleton para mantener una única instancia y poder acceder a sus
	 * valores desde cualquier script.
	 */
	public static function ObtenerInstancia() {
		// Si la instancia no esta definida la creo, sino devuelvo la existente
		if (!isset(self::$instancia)) {
			$claseActual = __CLASS__; // Obtengo la clase actual
			self::$instancia = new $claseActual; // Creo una instancia
		}
		// Devuelvo la instancia existente.
		return self::$instancia;
	}

	/**
	 * Es invocado cuando se clona un instancia.
	 * Con este método podemos emitir un mensaje de error y
	 * proceder a detener la ejecución del script por operación inválida
	 * al intentar clonar una instancia de Singleton
	 *
	 * E_USER_ERROR: constante que contiene el mensaje de error generado por el usuario
	 */
	public function __clone() {
		trigger_error("Operación Inválida: No se puede clonar una instancia de " . get_class($this) . ".", E_USER_ERROR);
	}

	/**
	 * __sleep es invocado cuando un objeto es serializado
	 * se evita serializar una instancia de Singleton
	 */
	public function __sleep() {
		trigger_error("No se puede serializar una instancia de " . get_class($this) . ".");
	}

	/**
	 * __wakeup es invocado cuando un objeto es deserializado
	 * se evita deserializar una instancia de Singleton
	 */
	public function __wakeup() {
		trigger_error("No se puede deserializar una instancia de " . get_class($this) . ".");
	}

	/**
	 * Recibe un valor determinado, se eliminan las etiquetas HTML y PHP (con strip_tags),
	 * se reemplaza algunos caracteres por su equivalente en HTML
	 *
	 * @param string|integer|array $valor_recibido
	 * @param string $valor_por_defecto
	 * @return string|integer|array
	 */
	public static function recoge($valor_recibido, $valor_por_defecto = '') {
		$valor_a_devolver = (isset($_REQUEST[$valor_recibido]) && ($_REQUEST[$valor_recibido] != '')) ? trim(strip_tags($_REQUEST[$valor_recibido])) : trim(strip_tags($valor_por_defecto));
		$valor_a_devolver = stripslashes($valor_a_devolver);
		$valor_a_devolver = str_replace('&', '&amp;', $valor_a_devolver);
		$valor_a_devolver = str_replace('"', '&quot;', $valor_a_devolver);

		return $valor_a_devolver;
	}

	/**
	 * Para recortar un texto según un límite determinado
	 * @param string $string
	 * @param integer $charlimit
	 * @return string, cadena recortada
	 */
	public static function cortar_string($string, $charlimit) {
		if (substr($string, $charlimit - 1, 1) != ' ') {
			$string = substr($string, 0, $charlimit);
			$array = explode(' ', $string);
			array_pop($array);
			$new_string = implode(' ', $array);

			return $new_string . ' ...';
		} else {
			return substr($string, 0, $charlimit - 1) . ' ...';
		}

	}

	/**
	 * Para convertir los saltos de linea y las tabulaciones en su respectiva etiqueta u operador html
	 * @param string $textohtml
	 * @return mixed
	 */
	public static function convertir_salto_linea($textohtml) {
		$textohtml = str_replace("\n", "<br>", $textohtml);
		$textohtml = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $textohtml);

		return $textohtml;
	}

	/**
	 * Devuelve un array con datos de un archivo determinado
	 * @param string $path
	 * @return array $datos, información del archivo
	 */
	public static function obtenerDatosArchivoRecibido($path) {
		// Vaciamos la caché de lectura de disco
		clearstatcache();

		// Comprobamos si el fichero existe
		$datos["exists"] = is_file($path);

		// Comprobamos si el fichero es escribible
		$datos["writable"] = is_writable($path);

		// Leemos los permisos del fichero
		$datos["chmod"] = ($datos["exists"] ? substr(sprintf("%o", fileperms($path)), -4) : FALSE);

		// Extraemos la extensión, un sólo paso
		$datos["ext"] = substr(strrchr($path, "."), 1);

		// Primer paso de lectura de ruta
		$datos["path"] = array_shift(explode("." . $datos["ext"], $path));

		// Primer paso de lectura de nombre
		$datos["name"] = array_pop(explode("/", $datos["path"]));

		// Ajustamos nombre a FALSE si está vacio
		$datos["name"] = ($datos["name"] ? $datos["name"] : FALSE);

		// Ajustamos la ruta a FALSE si está vacia
		$datos["path"] = ($datos["exists"] ? ($datos["name"] ? realpath(array_shift(explode($datos["name"], $datos["path"]))) : realpath(array_shift(explode($datos["ext"], $datos["path"])))) : ($datos["name"] ? array_shift(explode($datos["name"], $datos["path"])) : ($datos["ext"] ? array_shift(explode($datos["ext"], $datos["path"])) : rtrim($datos["path"], "/"))));

		// Ajustamos el nombre a FALSE si está vacio o a su valor en caso contrario
		$datos["filename"] = (($datos["name"] OR $datos["ext"]) ? $datos["name"] . ($datos["ext"] ? "." : "") . $datos["ext"] : FALSE);

		// Devolvemos los resultados
		return $datos;
	}

	/**
	 * Se quitan las virgulillas a la cadena
	 * (tildes, diéresis y virgulillas)
	 * @param  string $cadena Cadena a limpiar
	 * @return string         Cadena limpia
	 */
	public function quitarVirgulillas($cadena) {
		// En minúsculas
		$cadena = str_replace('á', 'a', $cadena);
		$cadena = str_replace('é', 'e', $cadena);
		$cadena = str_replace('í', 'i', $cadena);
		$cadena = str_replace('ó', 'o', $cadena);
		$cadena = str_replace('ú', 'u', $cadena);
		$cadena = str_replace('ñ', 'n', $cadena);
		$cadena = str_replace('ü', 'u', $cadena);
		// En mayúsculas
		$cadena = str_replace('Á', 'A', $cadena);
		$cadena = str_replace('É', 'E', $cadena);
		$cadena = str_replace('Í', 'I', $cadena);
		$cadena = str_replace('Ó', 'O', $cadena);
		$cadena = str_replace('Ú', 'U', $cadena);
		$cadena = str_replace('Ñ', 'N', $cadena);
		$cadena = str_replace('Ü', 'U', $cadena);

		return $cadena;
	}

	/**
	 * Reemplaza las vocales acentuadas por su mayúscula respectiva
	 * @param string $cadena
	 * @return string $cadena, la cadena con vocales reemplazadas
	 */
	public static function reemplazarPorMayusculaAcentuada($cadena) {
		$cadena = str_replace('á', 'Á', $cadena);
		$cadena = str_replace('é', 'É', $cadena);
		$cadena = str_replace('í', 'Í', $cadena);
		$cadena = str_replace('ó', 'Ó', $cadena);
		$cadena = str_replace('ú', 'Ú', $cadena);
		$cadena = str_replace('ñ', 'Ñ', $cadena);

		return $cadena;
	}

	/**
	 * Convierte una cadena a mayúsculas, incluyendo los acentos y la eñe
	 * @param  string $cadena Cadena a convertir
	 * @return string         Cadena convertida
	 */
	public static function aMayuscula($cadena) {
		return self::reemplazarPorMayusculaAcentuada(strtoupper($cadena));
	}

	/**
	 * Serializa una colección de datos
	 * @param Array $coleccion
	 * @return string, devuelve los datos serializados en una cadena
	 */
	public static function serializarColeccion($coleccion) {
		return base64_encode(json_encode($coleccion));
	}

	/**
	 * Deserializa una cadena de datos
	 * @param string $cadena_serializada
	 * @return mixed
	 */
	public static function deserializarColeccion($cadena_serializada) {
		return json_decode(base64_decode($cadena_serializada), true);
	}

	/**
	 * Se guarda en un archivo txt el contenido de un elemento determinado
	 * @param string $nombre_archivo
	 * @param mixed $elemento_a_verificar
	 */
	public static function registrarLog($identificador, $elemento_a_verificar, $extension = ".txt") {
		fputs(fopen($identificador . $extension, 'w'), print_r($elemento_a_verificar, true));
	}

	/**
	 * Se eliminan los espacios vacíos en cualquier posición de la cadena
	 * @param string $cadena
	 * @return string cadena sin espacios vacíos
	 */
	public static function eliminarEspacios($cadena) {
		return str_replace(' ', '', $cadena);
	}

	/**
	 * Se elimina un directorio determinado,
	 * previamente se elimina su contenido recursivamente
	 * @param string $directorio
	 */
	public static function eliminarDirectorio($directorio) {
		// Si se puede abrir el directorio respectivo
		if ($dir_abierto = opendir($directorio)) {
			// Mientras encuentre un archivo
			while (false !== ($archivo = readdir($dir_abierto))) {
				// Se descartan . y ..
				if ($archivo != '..' && $archivo != '.') {
					// Se elimina el archivo
					if (!unlink($directorio . '/' . $archivo)) {
						self::eliminarDirectorio($directorio . '/' . $archivo);
					}
				}
			}
			// Se cierra el directorio
			closedir($dir_abierto);
			// Se elimina el directorio, el cual ya se encuentra vacío
			rmdir($directorio);
		}
	}

	/**
	 * Devuelve el número del día que le corresponde en la semana
	 * @param  integer $anio [description]
	 * @param  integer $mes  [description]
	 * @param  integer $dia  [description]
	 * @return integer       Número del día que le corresponde en la semana
	 */
	public static function obtenerNumeroDia($anio, $mes, $dia) {
		return date("w", mktime(0, 0, 0, $mes, $dia, $anio));
	}

	/**
	 * Devuelve el nombre del día en la semana
	 * @param  string $fecha 		Fecha en formato yyyy-mm-dd
	 * @return string $nombre_dia   Nombre del día
	 */
	public static function obtenerNombreDia($fecha) {
		// Nombres de días de la semana (0 = domingo, 6 = sabado)
		$nombres_dias = array("Domingo", "Lunes", "Martes", "Mi&eacute;rcoles", "Jueves", "Viernes", "S&aacute;bado");

		// Se separa la fecha por su guión
		$partes = explode('-', $fecha);

		$anio = $partes[0];
		$mes = $partes[1];
		$dia = $partes[2];

		// Se obtiene el número del día en la semana
		$numero_dia_en_semana = self::obtenerNumeroDia($anio, $mes, $dia);

		// Se obtiene el nombre del día, según su número en la semana
		$nombre_dia = $nombres_dias[$numero_dia_en_semana];

		return $nombre_dia;
	}

	public static function mostrarNombreNumeroDiaActual() {
		return self::obtenerNombreDia(date("Y-m-d")) . ' ' . date("d");
	}

	public static function obtenerNombreMes($numero_mes) {
		// Nombres de Meses
		$nombres_meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

		// Devuelve el nombre del Mes
		return $nombres_meses[$numero_mes - 1];
	}

	public static function mostrarAnioConMiles($fecha) {
		return number_format(date("Y"), 0, '', '.');
	}

	public static function mostrarFechaActualConLetras() {
		return self::mostrarNombreNumeroDiaActual() . ' de ' . self::obtenerNombreMes(date("m")) . ' de ' . date("Y");
	}

	/**
	 * SE LE DA EL FORMATO #dia de nombre_mes de anio completo
	 * @param  [type] $fecha [description]
	 * @return [type]        [description]
	 */
	public static function mostrarFormatoGregorianoAnio($fecha) {
		$meses = Array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		$dias = Array("Domingo", "Lunes", "Martes", "Mi&eacute;rcoles", "Jueves", "Viernes", "S&aacute;bado");

		if ($fecha && $fecha != '0000-00-00') {
			// SE TOMAN LAS PARTES DE LA FECHA [0]:anio_completo, [1]:mes, [2]:dia
			$fec_partes = explode("-", $fecha);

			$nro_dia = abs($fec_partes[2]);
			$nro_mes = abs($fec_partes[1]);
			$nro_anio = $fec_partes[0];

			// 0 = domingo | 6 = sabado
			$dia_semana = date("w", mktime(0, 0, 0, $nro_mes, $nro_dia, $nro_anio));

			// FORMATO: #dia de nombre_mes de anio_completo
			$fecha_a_ver = $dias[$dia_semana] . ' ' . $nro_dia . ' de ' . $meses[$nro_mes - 1] . ' de ' . $nro_anio;

			return $fecha_a_ver;
		} else {
			return '';
		}
	}

	/**
	 * Devuelve el nombre abreviado del mes
	 * @param string $fecha Fecha en formato yyyy-mm-dd
	 */
	public static function obtenerNombreAbreviadoMes($fecha) {
		//$meses = Array ("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
		// Modificadas las abreviaturas
		$meses = Array("En", "Febr", "Mzo", "Abr", "My", "Jun", "Jul", "Ag", "Sep", "Oct", "Nov", "Dic");

		if ($fecha) {
			if ($fecha != '0000-00-00') {
				$fec_partes = explode("-", $fecha);

				return $meses[$fec_partes[1] - 1];
			} else {
				return '';
			}
		} else {
			return '';
		}
	}

	/**
	 * Se obtiene el formato de fecha: [nombre mes abreviado] [nro del dia], [anio completo]
	 * Ejemplo: Mar 29, 2020
	 * @param  string $fecha [description]
	 * @return string        [description]
	 */
	public static function mostrarFechaFormatoBlog($fecha) {

		if (isset($fecha) && $fecha != '0000-00-00') {

			$mes_abreviado = self::obtenerNombreAbreviadoMes($fecha);

			// Se divide la fecha [0]:anio_completo, [1]:mes, [2]:dia
			$fec_partes = explode("-", $fecha);
			$nro_dia = abs($fec_partes[2]);
			$nro_anio = $fec_partes[0];

			// [nombre mes abreviado] [nro del dia], [anio completo]
			return $mes_abreviado . ' ' . $nro_dia . ', ' . $nro_anio;
		} else {
			return '';
		}
	}

	/**
	 * Devuelve el Texto correctamente formateado para una UI
	 * @param  [type] $texto [description]
	 * @return [type]        [description]
	 */
	public static function mostrarTexto($texto) {
		return nl2br(stripslashes(html_entity_decode($texto)));
	}

	/**
	 * Se comprueba si es un valor Numérico
	 * @param  [integer] $numero Valor a verificar
	 * @return [boolean] True|False
	 */
	public static function comprobarValorNumerico($numero) {
		return (preg_match('/^[0-9]+$/', $numero));
	}

	/**
	 * Se comprueba si el código del libro es válido
	 * - valor numérico de 9 o 10 repeticiones 
	 * - finalizando o no con una X
	 * @param  string $codigo Valor a verificar
	 * @return boolean
	 */
	public static function esCodigoValido($codigo) {
	    return (preg_match('/^[0-9]{9,10}X?$/', $codigo));
	}

	/**
	 * Se comprueba si es valor permitido para un Nombre de Usuario
	 * @param  [string]  $nombre_usuario Valor a verificar
	 * @return [boolean] True|False
	 */
	public static function comprobarValorNombreUsuario($nombre_usuario) {
		return (preg_match('/^[a-zA-Z0-9\-_]{3,20}$/', $nombre_usuario));
	}

	public static function seEncuentra($coleccion, $pnombre_campo, $pvalor_buscado) {
		foreach ($coleccion as $nro => $contenido) {
			foreach ($contenido as $clave => $valor_a_comparar) {
				// Si la clave es justamente el nombre del campo por el cual buscar
				if ($clave == $pnombre_campo) {
					// Si se encontró el valor buscado
					if ($valor_a_comparar == $pvalor_buscado) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Se genera un password aleatorio
	 *
	 * @return string $password
	 */
	public static function generarPasswordAleatorio() {
		
		//Se define una cadena de caracteres.
		$cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";

		// Se obtiene la longitud de la cadena de caracteres
		$longitud_cadena = strlen($cadena);

		// Se define la variable que va a contener el password
		$password = "";

		// Se define la longitud del password
		$longitud_password = 10;

		// Se crea el password
		for ($i = 1; $i <= $longitud_password; $i++) {
			// Se define un numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
			$posicion = rand(0, $longitud_cadena - 1);

			// Se va formando el password en cada iteracion del bucle, añadiendo a la cadena $password la letra correspondiente a la posicion $posicion en la cadena de caracteres definida.
			$password .= substr($cadena, $posicion, 1);
		}

		return $password;
	}

	/**
	 * Devuelve una fecha en formato dia/mes/año_completo
	 * @param string $fecha
	 * @return null|string $fecha_con_barra
	 */
	public function formatearFechaConBarra($fecha) {
		if ($fecha == null) {
			return null;
		} else {
			$fec_partes = explode("-", $fecha);

			return $fec_partes[0] . '/' . $fec_partes[1] . '/' . $fec_partes[2];
		}
	}

	/**
	 * Se genera un Código de Barras
	 */
	public static function generarCodigoBarras(
		$filepath = "",
		$text = "0",
		$size = "20",
		$orientation = "horizontal",
		$code_type = "code128",
		$print = false,
		$SizeFactor = 1) {
		$code_string = "";
		// Se traduce el texto de $text en el correcto código de barras $code_type
		if (in_array(strtolower($code_type), array("code128", "code128b"))) {
			$chksum = 104;
			// No debe cambiar el orden de los elementos de la matriz
			// ya que la suma de comprobación depende de la clave de la matriz para validar el código final
			$code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "\`" => "111422", "a" => "121124", "b" => "121421", "c" => "141122", "d" => "141221", "e" => "112214", "f" => "112412", "g" => "122114", "h" => "122411", "i" => "142112", "j" => "142211", "k" => "241211", "l" => "221114", "m" => "413111", "n" => "241112", "o" => "134111", "p" => "111242", "q" => "121142", "r" => "121241", "s" => "114212", "t" => "124112", "u" => "124211", "v" => "411212", "w" => "421112", "x" => "421211", "y" => "212141", "z" => "214121", "{" => "412121", "|" => "111143", "}" => "111341", "~" => "131141", "DEL" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "FNC 4" => "114131", "CODE A" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
			$code_keys = array_keys($code_array);
			$code_values = array_flip($code_keys);
			for ($X = 1; $X <= strlen($text); $X++) {
				$activeKey = substr($text, ($X - 1), 1);
				$code_string .= $code_array[$activeKey];
				$chksum = ($chksum + ($code_values[$activeKey] * $X));
			}
			$code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

			$code_string = "211214" . $code_string . "2331112";
		} elseif (strtolower($code_type) == "code128a") {
			$chksum = 103;
			$text = strtoupper($text); // Code 128A doesn't support lower case
			// No debe cambiar el orden de los elementos de la matriz
			// ya que la suma de comprobación depende de la clave de la matriz para validar el código final
			$code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "NUL" => "111422", "SOH" => "121124", "STX" => "121421", "ETX" => "141122", "EOT" => "141221", "ENQ" => "112214", "ACK" => "112412", "BEL" => "122114", "BS" => "122411", "HT" => "142112", "LF" => "142211", "VT" => "241211", "FF" => "221114", "CR" => "413111", "SO" => "241112", "SI" => "134111", "DLE" => "111242", "DC1" => "121142", "DC2" => "121241", "DC3" => "114212", "DC4" => "124112", "NAK" => "124211", "SYN" => "411212", "ETB" => "421112", "CAN" => "421211", "EM" => "212141", "SUB" => "214121", "ESC" => "412121", "FS" => "111143", "GS" => "111341", "RS" => "131141", "US" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "CODE B" => "114131", "FNC 4" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
			$code_keys = array_keys($code_array);
			$code_values = array_flip($code_keys);
			for ($X = 1; $X <= strlen($text); $X++) {
				$activeKey = substr($text, ($X - 1), 1);
				$code_string .= $code_array[$activeKey];
				$chksum = ($chksum + ($code_values[$activeKey] * $X));
			}
			$code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

			$code_string = "211412" . $code_string . "2331112";
		} elseif (strtolower($code_type) == "code39") {
			$code_array = array("0" => "111221211", "1" => "211211112", "2" => "112211112", "3" => "212211111", "4" => "111221112", "5" => "211221111", "6" => "112221111", "7" => "111211212", "8" => "211211211", "9" => "112211211", "A" => "211112112", "B" => "112112112", "C" => "212112111", "D" => "111122112", "E" => "211122111", "F" => "112122111", "G" => "111112212", "H" => "211112211", "I" => "112112211", "J" => "111122211", "K" => "211111122", "L" => "112111122", "M" => "212111121", "N" => "111121122", "O" => "211121121", "P" => "112121121", "Q" => "111111222", "R" => "211111221", "S" => "112111221", "T" => "111121221", "U" => "221111112", "V" => "122111112", "W" => "222111111", "X" => "121121112", "Y" => "221121111", "Z" => "122121111", "-" => "121111212", "." => "221111211", " " => "122111211", "$" => "121212111", "/" => "121211121", "+" => "121112121", "%" => "111212121", "*" => "121121211");

			// Se convierte a mayúsculas
			$upper_text = strtoupper($text);

			for ($X = 1; $X <= strlen($upper_text); $X++) {
				$code_string .= $code_array[substr($upper_text, ($X - 1), 1)] . "1";
			}

			$code_string = "1211212111" . $code_string . "121121211";
		} elseif (strtolower($code_type) == "code25") {
			$code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
			$code_array2 = array("3-1-1-1-3", "1-3-1-1-3", "3-3-1-1-1", "1-1-3-1-3", "3-1-3-1-1", "1-3-3-1-1", "1-1-1-3-3", "3-1-1-3-1", "1-3-1-3-1", "1-1-3-3-1");

			for ($X = 1; $X <= strlen($text); $X++) {
				for ($Y = 0; $Y < count($code_array1); $Y++) {
					if (substr($text, ($X - 1), 1) == $code_array1[$Y]) {
						$temp[$X] = $code_array2[$Y];
					}
				}
			}

			for ($X = 1; $X <= strlen($text); $X += 2) {
				if (isset($temp[$X]) && isset($temp[($X + 1)])) {
					$temp1 = explode("-", $temp[$X]);
					$temp2 = explode("-", $temp[($X + 1)]);
					for ($Y = 0; $Y < count($temp1); $Y++) {
						$code_string .= $temp1[$Y] . $temp2[$Y];
					}
				}
			}

			$code_string = "1111" . $code_string . "311";
		} elseif (strtolower($code_type) == "codabar") {
			$code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "$", ":", "/", ".", "+", "A", "B", "C", "D");
			$code_array2 = array("1111221", "1112112", "2211111", "1121121", "2111121", "1211112", "1211211", "1221111", "2112111", "1111122", "1112211", "1122111", "2111212", "2121112", "2121211", "1121212", "1122121", "1212112", "1112122", "1112221");

			// Se convierte a mayúsculas
			$upper_text = strtoupper($text);

			for ($X = 1; $X <= strlen($upper_text); $X++) {
				for ($Y = 0; $Y < count($code_array1); $Y++) {
					if (substr($upper_text, ($X - 1), 1) == $code_array1[$Y]) {
						$code_string .= $code_array2[$Y] . "1";
					}
				}
			}
			$code_string = "11221211" . $code_string . "1122121";
		}

		// Se rellenan los bordes del código de barras
		$code_length = 20;
		if ($print) {
			$text_height = 30;
		} else {
			$text_height = 0;
		}

		for ($i = 1; $i <= strlen($code_string); $i++) {
			$code_length = $code_length + (integer) (substr($code_string, ($i - 1), 1));
		}

		if (strtolower($orientation) == "horizontal") {
			$img_width = $code_length * $SizeFactor;
			$img_height = $size;
		} else {
			$img_width = $size;
			$img_height = $code_length * $SizeFactor;
		}

		$image = imagecreate($img_width, $img_height + $text_height);
		$black = imagecolorallocate($image, 0, 0, 0);
		$white = imagecolorallocate($image, 255, 255, 255);

		imagefill($image, 0, 0, $white);
		if ($print) {
			imagestring($image, 5, 31, $img_height, $text, $black);
		}

		$location = 10;
		for ($position = 1; $position <= strlen($code_string); $position++) {
			$cur_size = $location + (substr($code_string, ($position - 1), 1));
			if (strtolower($orientation) == "horizontal") {
				imagefilledrectangle($image, $location * $SizeFactor, 0, $cur_size * $SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black));
			} else {
				imagefilledrectangle($image, 0, $location * $SizeFactor, $img_width, $cur_size * $SizeFactor, ($position % 2 == 0 ? $white : $black));
			}

			$location = $cur_size;
		}

		// Se dibuja el código de barras en la pantalla o se guarda en un archivo
		if ($filepath == "") {
			header('Content-type: image/png');
			imagepng($image);
			imagedestroy($image);
		} else {
			imagepng($image, $filepath);
			imagedestroy($image);
		}
	}

	public static function rellenarConCerosIzquierda($valor, $longitud) {
		return str_pad($valor, $longitud, '0', STR_PAD_LEFT);
	}

	public static function esMailValido($valor) {
		//filter_var() depura el correo usando FILTER_SANITIZE_EMAIL
		$valor = filter_var($valor, FILTER_SANITIZE_EMAIL);

		//filter_var() valida el correo usando FILTER_VALIDATE_EMAIL
		return (filter_var($valor, FILTER_VALIDATE_EMAIL));
	}

	public static function obtenerTextoCodigoBarras(
		$v_cuit,
		$v_tipo_comprobante,
		$v_punto_venta,
		$v_cae,
		$v_vencimiento_cae) {

		$suma_ubicacion_par = 0;
		$suma_ubicacion_impar = 0;

		$base_calculo = $v_cuit . $v_tipo_comprobante . $v_punto_venta . $v_cae . $v_vencimiento_cae;

		$largo = strlen(trim($base_calculo));

		// Se suman los digitos de ubicación Par
		for ($i = 0; $i < $largo + 1; $i = $i + 2) {
			$suma_ubicacion_par = $suma_ubicacion_par + substr($base_calculo, $i, 1);
		}

		// Se suman los digitos de ubicación Impar
		for ($i = 1; $i < $largo + 1; $i = $i + 2) {
			$suma_ubicacion_impar = $suma_ubicacion_impar + substr($base_calculo, $i, 1);
		}

		// Se multiplica por 3 la suma de ubicación Par
		$suma_ubicacion_parx3 = $suma_ubicacion_par * 3;
		// A dicho valor calculado se lo incrementa con la suma de Impares
		$suma_paresx3_mas_suma_impares = $suma_ubicacion_parx3 + $suma_ubicacion_impar;

		$largo_final = strlen(trim($suma_paresx3_mas_suma_impares));
		// Se define el digito
		$digito = 10 - substr($suma_paresx3_mas_suma_impares, $largo_final - 1, 1);

		// Se retorna la base con el digito concatenado al final
		return $base_calculo . $digito;
	}

	public static function existeFoto($ruta_foto) {

		return is_file($ruta_foto);
	}

	public static function formatearMoneda($precio) {

		return str_replace(",", ".", str_replace(".", "", $precio));
	}

	/**
	 * Se define el color según el nivel de Stock
	 * @param  integer $cantidad        	Número de existencias del producto
	 * @param  integer $cantidad_minima 	Valor mínimo aceptado del producto
	 * @return string  						Clase CSS
	 */
	public static function definirColorSegunStock($cantidad = 0, $cantidad_minima = 0) {

		if ($cantidad == 0) {
			return 'bg-danger text-white';
		} elseif ($cantidad <= $cantidad_minima) {
			return 'bg-warning';
		} else {
			return 'bg-success text-white';
		}
	}

	public static function quitarAcentos($cadena) {
		// En minúsculas
		$cadena = str_replace('á', 'a', $cadena);
		$cadena = str_replace('é', 'e', $cadena);
		$cadena = str_replace('í', 'i', $cadena);
		$cadena = str_replace('ó', 'o', $cadena);
		$cadena = str_replace('ú', 'u', $cadena);
		// En mayúsculas
		$cadena = str_replace('Á', 'A', $cadena);
		$cadena = str_replace('É', 'E', $cadena);
		$cadena = str_replace('Í', 'I', $cadena);
		$cadena = str_replace('Ó', 'O', $cadena);
		$cadena = str_replace('Ú', 'U', $cadena);

		return $cadena;
	}

	public static function aMayusculas($cadena) {
		// Primero se quitan los Acentos
		$cadena = str_replace('á', 'A', $cadena);
		$cadena = str_replace('é', 'E', $cadena);
		$cadena = str_replace('í', 'I', $cadena);
		$cadena = str_replace('ó', 'O', $cadena);
		$cadena = str_replace('ú', 'U', $cadena);
		$cadena = str_replace('Á', 'A', $cadena);
		$cadena = str_replace('É', 'E', $cadena);
		$cadena = str_replace('Í', 'I', $cadena);
		$cadena = str_replace('Ó', 'O', $cadena);
		$cadena = str_replace('Ú', 'U', $cadena);
		// Se convierte la ñ
		$cadena = str_replace('ñ', 'Ñ', $cadena);

		return strtoupper($cadena);
	}

	public static function quitarAcentosMayusculas($cadena) {
		$cadena = str_replace('Á', 'A', $cadena);
		$cadena = str_replace('É', 'E', $cadena);
		$cadena = str_replace('Í', 'I', $cadena);
		$cadena = str_replace('Ó', 'O', $cadena);
		$cadena = str_replace('Ú', 'U', $cadena);

		return $cadena;
	}

	/**
	 * Se reemplazan los espacios vacíos en cualquier posición de la cadena por un guión bajo
	 * @param string $cadena
	 * @return string cadena, con espacios bajos en lugar de espacios vacíos
	 */
	public static function reemplazarEspaciosPorGuionesBajos($cadena) {
		return str_replace(' ', '_', $cadena);
	}

	/**
	 * Se eliminan los comillas simples en cualquier posición de la cadena
	 * @param string $cadena
	 * @return string cadena, sin comillas simples
	 */
	public static function eliminarComillaSimple($cadena) {
		return str_replace("'", "", $cadena);
	}

	/**
	 * Devuelve una fecha en formato año_completo-mes-dia
	 * @param string $fecha
	 * @return null|string $fecha_con_guiones
	 */
	public static function formatearFechaConGuiones($fecha) {
		if ($fecha == null) {
			return null;
		} else {
			$fec_partes = explode("/", $fecha);

			return $fec_partes[2] . '-' . $fec_partes[1] . '-' . $fec_partes[0];
		}
	}

	public static function esAdjuntoDe($id, $nombre_archivo) {
		return (!(strpos($nombre_archivo, $id . '__') === FALSE));
	}

	public static function mostrarVideoLocal($url) {

		return '<video width="320" height="240" controls class="bg-dark"><source src="' . $url . '" type="video/mp4"></video>';
	}

	public static function mostrarVideoYoutube($url) {

		parse_str(parse_url($url, PHP_URL_QUERY), $output);

		$id_video = !empty($output['v']) ? $output['v'] : $url;

		$video  = '<div class="ratio ratio-16x9">';
		$video .= '<iframe src="https://www.youtube.com/embed/' . $id_video . '"';
		$video .= ' frameborder="0"';
		$video .= ' allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"';
		$video .= ' allowfullscreen></iframe>';
		$video .= '</div>';

		return $video;
	}

	public static function mostrarShortYoutube($url) {
		$url = str_replace("?feature=shared", "", $url);
		$url = str_replace("shorts", "embed", $url);

		$video  = '<div class="ratio ratio-16x9">';
		$video .= '<iframe src="'.$url.'"';
		$video .= ' frameborder="0"';
		$video .= ' allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"';
		$video .= ' allowfullscreen';
		$video .= ' sandbox="allow-same-origin allow-scripts allow-popups allow-presentation">';
		$video .= '</iframe>';
		$video .= '</div>';
		return $video;
	}

	public static function mostrarShortYoutubeBackend($url) {
		$video  = '<div class="embed-responsive embed-responsive-21by9">';
		$video .= '<iframe src="'.str_replace("shorts", "embed", $url).'"';
		$video .= ' class="embed-responsive-item"';
		$video .= ' frameborder="0"';
		$video .= ' allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"';
		$video .= ' allowfullscreen></iframe>';
		$video .= '</div>';
		return $video;
	}
	
	/**
	 * Se genera y almacena el json de la venta
	 * @param  array $info_pedido
	 * @param  array $info_cliente
	 * @param  array $detalle_pedido
	 * @return void
	 */
	public static function generarJson($info_pedido, $info_cliente, $detalle_pedido) {

		$cantidad_detalle = (isset($detalle_pedido)) ? count($detalle_pedido) : 0;

		$data = [
		    "pedido_id" => $info_pedido['id'],
		    "cliente" => [
		        "id" => $info_cliente['id'],
		        "nombre" => $info_cliente['nombre'],
		        "apellido" => $info_cliente['apellido'],
		        "dni" => $info_cliente['dni']
		    ],
		    "fecha" => $info_pedido['fecha'],
		    "detalle" => []
		];

		for ($i = 0; $i < $cantidad_detalle; $i++) {
		    $data["detalle"][] = [
		        "codigo" => $detalle_pedido[$i]['codigo'],
		        "isbn" => $detalle_pedido[$i]['isbn'],
		        "titulo" => $detalle_pedido[$i]['titulo'],
		        "cantidad" => $detalle_pedido[$i]['cantidad'],
		        "precio_unitario" => $detalle_pedido[$i]['precio_unitario']
		    ];
		}

		$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

		if ($json === false) {
		    self::registrarLog("error_json_venta_".$info_pedido['id'], "Error al codificar JSON: " . json_last_error_msg());
		} else {
		    fputs(fopen(RUTA_JSON_VENTAS.$info_pedido['id'].'.json', 'w'), print_r($json, true));
		}
	}

}
?>
