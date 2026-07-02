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

		// si estan habilitadas las comillas magicas
		if (get_magic_quotes_gpc()) {
			// se eliminan las barras invertidas de $valor_a_devolver
			$valor_a_devolver = stripslashes($valor_a_devolver);
		}

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
	 * Arma un árbol de subdirectorios y archivos
	 * de un directorio determinado
	 * @param string $dir
	 * @param integer $tab
	 */
	public static function armarArbol($dir, $tab = 0) {
		$directorios_excluidos = Array("imagenes", "backups", "sql");

		$directorio = dir($dir);

		if (!$tab) {
			echo "<pre>";
		}

		if (!in_array(basename($directorio->path), $directorios_excluidos)) {
			echo "\n" . str_pad("", ($tab * 3), " ", STR_PAD_LEFT) . "<img src='imagenes/directorio.jpg' width='14' height='14' align='top' >&nbsp;<strong>" . basename($directorio->path) . "</strong>";

			while ($df = $directorio->read()) {
				if ($df == "." || $df == "..") {
					continue;
				}

				if (is_file($directorio->path . $df)) {
					echo "\n" . str_pad("", ($tab * 3), " ") . "  " . basename($df);
				} else {
					armarArbol($directorio->path . $df . "/", $tab + 1);
				}
			}
		}

		$directorio->close();

		if (!$tab) {
			echo "\n</pre>";
		}

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
	 * @param string $identificador
	 * @param mixed  $elemento_a_verificar
	 * @param string $extension
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
	 * Se retiran las comillas simples en los elementos de un conjunto determinado
	 * @param  [array] $conjunto Con comillas
	 * @return [array] $conjunto Sin comillas
	 */
	public static function retirarComillaSimpleArray($conjunto) {
		foreach ($conjunto as $valor) {
			str_replace("'", "", $valor);
		}

		return $conjunto;
	}

	public static function esAdjuntoDe($id, $nombre_archivo) {

		return ( (!(strpos($nombre_archivo, $id . '__', 0) === FALSE)) && (strpos($nombre_archivo, $id . '__') === 0) );
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

	/**
	 * Devuelve el nombre del mes según su número
	 * @param integer $nro_mes
	 * @return Ambigous <string>
	 */
	public static function mostrarNombreMes($nro_mes) {
		$meses = Array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

		return $meses[$nro_mes - 1];
	}

	/**
	 * Muestra la fecha en letras
	 * @param string $fecha
	 */
	public static function mostrarFechaLetras($fecha) {
		// Se divide la fecha por cada guión
		$partes_fecha = explode('-', $fecha);

		// Se establece el número del día
		$dia = ($partes_fecha[2] < 10) ? substr($partes_fecha[2], 1, 1) : $partes_fecha[2];

		// Devuelve la fecha en formato [nro del día] de [nombre del mes] de [nro del año]
		return $dia . " de " . self::mostrarNombreMes($partes_fecha[1]) . " de " . $partes_fecha[0];
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
		// Abreviaturas
		$meses = Array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");

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
		return (preg_match('/^[0-9][0-9]*$/', $numero));
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

		// Obtenemos la longitud de la cadena de caracteres
		$longitud_cadena = strlen($cadena);

		// Se define la variable que va a contener el password
		$password = "";

		// Se define la longitud del password
		$longitud_password = 10;

		// Creamos el password
		for ($i = 1; $i <= $longitud_password; $i++) {
			//Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
			$posicion = rand(0, $longitud_cadena - 1);

			//Vamos formando el password en cada iteracion del bucle, añadiendo a la cadena $password la letra correspondiente a la posicion $posicion en la cadena de caracteres definida.
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

	public static function rellenarConCerosIzquierda($valor, $longitud) {
		return str_pad($valor, $longitud, '0', STR_PAD_LEFT);
	}

	public static function esMailValido($valor) {
		//filter_var() depura el correo usando FILTER_SANITIZE_EMAIL
		$valor = filter_var($valor, FILTER_SANITIZE_EMAIL);

		//filter_var() valida el correo usando FILTER_VALIDATE_EMAIL
		return (filter_var($valor, FILTER_VALIDATE_EMAIL));
	}

	public static function convertir_a_numero_romano($num) {
		if ($num < 0 || $num > 9999) {
			return -1;
		}
		$r_ones = array(1 => "I", 2 => "II", 3 => "III", 4 => "IV", 5 => "V", 6 => "VI", 7 => "VII", 8 => "VIII", 9 => "IX");
		$r_tens = array(1 => "X", 2 => "XX", 3 => "XXX", 4 => "XL", 5 => "L", 6 => "LX", 7 => "LXX", 8 => "LXXX", 9 => "XC");
		$r_hund = array(1 => "C", 2 => "CC", 3 => "CCC", 4 => "CD", 5 => "D", 6 => "DC", 7 => "DCC", 8 => "DCCC", 9 => "CM");
		$r_thou = array(1 => "M", 2 => "MM", 3 => "MMM", 4 => "MMMM", 5 => "MMMMM", 6 => "MMMMMM", 7 => "MMMMMMM", 8 => "MMMMMMMM", 9 => "MMMMMMMMM");

		$ones = $num % 10;
		$tens = ($num - $ones) % 100;
		$hundreds = ($num - $tens - $ones) % 1000;
		$thou = ($num - $hundreds - $tens - $ones) % 10000;
		$tens = $tens / 10;
		$hundreds = $hundreds / 100;
		$thou = $thou / 1000;

		$rnum = '';

		if ($thou) {$rnum .= $r_thou[$thou];}
		if ($hundreds) {$rnum .= $r_hund[$hundreds];}
		if ($tens) {$rnum .= $r_tens[$tens];}
		if ($ones) {$rnum .= $r_ones[$ones];}

		return $rnum;
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

		$cadena = str_replace('á', 'Á', $cadena);
		$cadena = str_replace('é', 'É', $cadena);
		$cadena = str_replace('í', 'Í', $cadena);
		$cadena = str_replace('ó', 'Ó', $cadena);
		$cadena = str_replace('ú', 'Ú', $cadena);
		$cadena = str_replace('ñ', 'Ñ', $cadena);

		return strtoupper($cadena);
	}

	public function aMinusculas($cadena) {

		$cadena = str_replace('Á','á',$cadena);
		$cadena = str_replace('É','é',$cadena);
		$cadena = str_replace('Í','í',$cadena);
		$cadena = str_replace('Ó','ó',$cadena);
		$cadena = str_replace('Ú','ú',$cadena);
		$cadena = str_replace('Ñ','ñ',$cadena);

		return strtolower($cadena);
	}

	public static function quitarAcentosMayusculas($cadena) {
		$cadena = str_replace('Á', 'A', $cadena);
		$cadena = str_replace('É', 'E', $cadena);
		$cadena = str_replace('Í', 'I', $cadena);
		$cadena = str_replace('Ó', 'O', $cadena);
		$cadena = str_replace('Ú', 'U', $cadena);

		return $cadena;
	}

	public static function unificarSimboloGrado($cadena) {
		$cadena = str_replace("°", "º", $cadena);
		$cadena = str_replace("ª", "º", $cadena);

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
	 * Se muestra la descripción de la Frecuencia del Dataset
	 * @param  [integer] $frecuencia
	 * @return [string]  $descripcion
	 */
	public static function mostrarFrecuenciaDataset($frecuencia) {

		switch ($frecuencia) {
		case 1:
			$descripcion = 'Semanal';
			break;
		case 2:
			$descripcion = 'Quincenal';
			break;
		case 3:
			$descripcion = 'Mensual';
			break;
		case 4:
			$descripcion = 'Bimestral';
			break;
		case 5:
			$descripcion = 'Trimestral';
			break;
		case 6:
			$descripcion = 'Semestral';
			break;
		case 7:
			$descripcion = 'Anual';
			break;
		}
		return $descripcion;
	}

	/**
	 * Se define el Tipo de Medio según la extensión
	 * @param  [string] 	$extension  	Extensión del archivo
	 * @return [integer]    $tipo_medio     Valor entero
	 */
	public static function definirTipoMedio($extension) {

		switch ($extension) {
		case 'pdf':
			$tipo_medio = 1;
			break;
		case 'ods':
			$tipo_medio = 2;
			break;
		case 'csv':
			$tipo_medio = 3;
			break;
		case 'jpeg':
		case 'jpg':
		case 'png':
		case 'gif':
			$tipo_medio = 4;
			break;
		case 'txt':
			$tipo_medio = 5;
			break;
		case 'html':
			$tipo_medio = 6;
			break;
		case 'odt':
			$tipo_medio = 7;
			break;
		}
		return $tipo_medio;
	}
	
	public static function existeFoto($ruta_foto) {

		return is_file($ruta_foto);
	}

	public static function existeArchivo($ruta_archivo) {

		return is_file($ruta_archivo);
	}
	
	public static function mostrarVideoYoutube($url) {

		parse_str(parse_url($url, PHP_URL_QUERY));

		$id_video = !empty($v) ? $v : $url;

		$video  = '<div class="embed-responsive embed-responsive-16by9">';
		$video .= '<iframe src="https://www.youtube.com/embed/' . $id_video . '"';
		$video .= ' frameborder="0"';
		$video .= ' allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"';
		$video .= ' allowfullscreen></iframe>';
		$video .= '</div>';

		return $video;
	}

	public static function restarDiasFechaActual($cantidad) {
		
		$fecha = date('Y-m-j');
		
		$fecha_anterior = strtotime ('-'.$cantidad.' day', strtotime($fecha));
		$fecha_anterior = date('Y-m-j', $fecha_anterior);

		return $fecha_anterior;
	}

	/**
	 * @param  [type] $image
	 * @param  [type] $width
	 * @param  [type] $height
	 * @param  [type] $escala
	 * @return [type]
	 */
	public static function resizeImage($image, $width, $height, $escala)
	{
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);

		$imageType      = image_type_to_mime_type($imageType);
		$newImageWidth  = ceil($width * $escala);
		$newImageHeight = ceil($height * $escala);
		$newImage       = imagecreatetruecolor($newImageWidth,$newImageHeight);

		switch ($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image); 
				break;
		    case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image); 
				break;
		    case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image); 
				break;
	  	}

		imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
		
		switch ($imageType) {
			case "image/gif":
		  		imagegif($newImage,$image); 
				break;
	      	case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
		  		imagejpeg($newImage,$image,90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage,$image);  
				break;
	    }
		
		chmod($image, 0777);

		return $image;
	}

	/**
	 * Se genera la foto recortada en base a la imagen original y la información del recorte
	 * 
	 * @param  [type] $nombre_imagen_miniatura 	Nombre de la imagen recortada
	 * @param  [type] $image            [description]
	 * @param  [type] $width            [description]
	 * @param  [type] $height           [description]
	 * @param  [type] $start_width      [description]
	 * @param  [type] $start_height     [description]
	 * @param  [type] $escala            [description]
	 * @return [type]                   [description]
	 */
	public static function resizeThumbnailImage($nombre_imagen_miniatura, $image, $width, $height, $start_width, $start_height, $escala)
	{
		list($imagewidth, $imageheight, $imageType) = getimagesize($image);
		
		$imageType = image_type_to_mime_type($imageType);
		
		$newImageWidth  = ceil($width * $escala);
		$newImageHeight = ceil($height * $escala);
		$newImage       = imagecreatetruecolor($newImageWidth,$newImageHeight);

		switch ($imageType) {
			case "image/gif":
				$source=imagecreatefromgif($image); 
				break;
		    case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
				$source=imagecreatefromjpeg($image); 
				break;
		    case "image/png":
			case "image/x-png":
				$source=imagecreatefrompng($image); 
				break;
	  	}

		imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);

		switch($imageType) {
			case "image/gif":
		  		imagegif($newImage, $nombre_imagen_miniatura); 
				break;
	      	case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":
		  		imagejpeg($newImage, $nombre_imagen_miniatura, 90); 
				break;
			case "image/png":
			case "image/x-png":
				imagepng($newImage, $nombre_imagen_miniatura);  
				break;
	    }

		chmod($nombre_imagen_miniatura, 0777);

		return $nombre_imagen_miniatura;
	}

	/**
	 * Devuelve el alto de la foto
	 * @param  [type] $image Imagen para obtener su alto
	 * @return int Devuelve el alto de la imagen
	 */
	public static function getHeight($image)
	{
		$size = getimagesize($image);// Se obtiene el tamaño y dimensiones de la imagen
		$height = $size[1]; // Se toma su Alto

		return $height;
	}

	/**
	 * Devuelve el ancho de la foto
	 * @param  [type] $image Imagen para obtener su ancho
	 * @return int Devuelve el ancho de la imagen
	 */
	public static function getWidth($image)
	{
		$size = getimagesize($image);// Se obtiene el tamaño y dimensiones de la imagen
		$width = $size[0]; // Se toma su Ancho

		return $width;
	}

	/**
	 * Se filtran (reemplazan por vacío) caracteres de una cadena
	 * Los caracteres filtrados son: ~ : / ? # [ ] & ( ) * + =
	 * @param  [string] $nombre_archivo
	 * @return [string]                
	 */
	public static function filtrarCaracteresNoDeseados($nombre_archivo)
	{
		return preg_replace('/[~:\/\?#\[\]&\(\)\*\+=]+/', '', $nombre_archivo);
	}

}
?>