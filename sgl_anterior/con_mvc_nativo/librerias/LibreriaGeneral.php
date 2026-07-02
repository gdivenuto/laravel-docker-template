<?php
// 07/05/2015: CLASE ESTATICA PARA VALIDAR PARAMETROS RECIBIDOS EN TODO EL SISTEMA
require_once "validador.php";

/***************************************************************************
CLASE GENERAL DE FUNCIONES PHP PARA LA APLICACION
 ****************************************************************************/
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
	 * Recibe un valor determinado y lo valida utilizando
	 * el método estático validarParametro de la clase Validador
	 * @param string|integer|array $valor
	 * @param string $valor_defecto
	 * @return string|integer|array
	 */
	public static function recoge($valor, $valor_defecto = '') {
		return Validador::validarParametro($valor, $valor_defecto);
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
		$directorios_excluidos = Array("imagenes", "docs", "documentos", "documentacion", "proyectos", "generar_pdf", "backups", "sgl_imagenes", "fonts", "expe-de");

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
	 * Elimina los acentos en una cadena determinada
	 * @param string $cadena
	 * @return string
	 */
	public static function eliminarAcentos($cadena) {
		$a_buscar = "ÀÁÂÄÅàáâäÒÓÔÖòóôöÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
		$reemplazo = "AAAAAaaaaOOOOooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";

		return utf8_encode(strtr(utf8_decode($cadena), utf8_decode($a_buscar), $reemplazo));
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

		return $cadena;
	}

	public function quitarAcentos($cadena) {
		// En minúsculas
		$cadena = str_replace('á', 'a', $cadena);
		$cadena = str_replace('é', 'e', $cadena);
		$cadena = str_replace('í', 'i', $cadena);
		$cadena = str_replace('ó', 'o', $cadena);
		$cadena = str_replace('ú', 'u', $cadena);
		$cadena = str_replace('ñ', 'n', $cadena);
		// En mayúsculas
		$cadena = str_replace('Á', 'A', $cadena);
		$cadena = str_replace('É', 'E', $cadena);
		$cadena = str_replace('Í', 'I', $cadena);
		$cadena = str_replace('Ó', 'O', $cadena);
		$cadena = str_replace('Ú', 'U', $cadena);
		$cadena = str_replace('Ñ', 'N', $cadena);

		return $cadena;
	}

	public function obtenerNombreMes($mes) {
		$meses = Array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

		return $meses[$mes - 1];
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
	public static function guardarEnTxt($identificador, $elemento_a_verificar) {
		// Se obtiene el nombre de la clase hija, utilizando this como parámetro en el método get_class()
		$nombre_clase_hija = get_class($this);

		// Se obtiene un rastreo de PHP
		$backtrace = debug_backtrace();

		// Se toma el nombre del método invocado
		$metodo = $backtrace[1]['function'];

		fputs(fopen("log/" . $nombre_clase_hija . "_" . $metodo . "_" . $identificador . ".txt", 'w'), print_r($elemento_a_verificar, true));
	}

	/**
	 * Se eliminan los espacios vacíos en cualquier posición de la cadena
	 * @param string $cadena
	 * @return string cadena, sin espacios vacíos
	 */
	public static function eliminarEspacios($cadena) {
		return str_replace(' ', '', $cadena);
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
	 * Se elimina un directorio determinado,
	 * previamente se elimina su contenido recursivamente
	 * @param string $directorio
	 */
	public static function eliminarDirectorio($directorio) {
		// Si se puede abrir el directorio respectivo
		if ($dir_abierto = @opendir($directorio)) {
			// Mientras encuentre un archivo
			while (false !== ($archivo = readdir($dir_abierto))) {
				// Se descartan . y ..
				if ($archivo != '..' && $archivo != '.') {
					// Se elimina el archivo
					if (!@unlink($directorio . '/' . $archivo)) {
						eliminarDirectorio($directorio . '/' . $archivo);
					}
				}
			}
			// Se cierra el directorio
			closedir($dir_abierto);

			// Se elimina el directorio, el cual ya se encuentra vacío
			@rmdir($directorio);
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

	public static function mostrarDiaEnLetras($nro_dia = '') {
		$dias_en_letras = Array("uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve",
			"diez", "once", "doce", "trece", "catorce", "quince", "dieciseis", "diecisiete", "dieciocho", "diecinueve",
			"veinte", "veintiuno", "veintidos", "veintitr&eacute;s", "veinticuatro", "veinticinco", "veintiseis", "veintisiete",
			"veintiocho", "veintinueve", "treinta", "treinta y uno");

		if ($nro_dia == '') {
			return '';
		}

		return $dias_en_letras[$nro_dia - 1];
	}

	public static function mostrarAnioEnLetras($nro_anio = '') {
		$anios_en_letras = Array();
		$anios_en_letras['2014'] = "dos mil catorce";
		$anios_en_letras['2015'] = "dos mil quince";
		$anios_en_letras['2016'] = "dos mil dieciseis";
		$anios_en_letras['2017'] = "dos mil diecisiete";
		$anios_en_letras['2018'] = "dos mil dieciocho";
		$anios_en_letras['2019'] = "dos mil diecinueve";
		$anios_en_letras['2020'] = "dos mil veinte";
		$anios_en_letras['2021'] = "dos mil veintiuno";
		$anios_en_letras['2022'] = "dos mil veintidos";
		$anios_en_letras['2023'] = "dos mil veintitres";
		$anios_en_letras['2024'] = "dos mil veinticuatro";
		$anios_en_letras['2025'] = "dos mil veinticinco";
		$anios_en_letras['2026'] = "dos mil veintiseis";
		$anios_en_letras['2027'] = "dos mil veintisiete";
		$anios_en_letras['2028'] = "dos mil veintiocho";
		$anios_en_letras['2029'] = "dos mil veintinueve";
		$anios_en_letras['3000'] = "tres mil";

		if ($nro_anio == '') {
			return '';
		}

		return $anios_en_letras[$nro_anio];
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

	public static function esAdjuntoDe($id, $nombre_archivo) {
		return (!(strpos($nombre_archivo, $id . '__') === FALSE));
	}

	public static function reemplazarPorHTML($cadena) {
		$cadena = str_replace("á", "&aacute;", $cadena);
		$cadena = str_replace("é", "&eacute;", $cadena);
		$cadena = str_replace("í", "&iacute;", $cadena);
		$cadena = str_replace("ó", "&oacute;", $cadena);
		$cadena = str_replace("ú", "&uacute;", $cadena);
		$cadena = str_replace("ñ", "&ntilde;", $cadena);
		$cadena = str_replace("Á", "&Aacute;", $cadena);
		$cadena = str_replace("É", "&Eacute;", $cadena);
		$cadena = str_replace("Í", "&Iacute;", $cadena);
		$cadena = str_replace("Ó", "&Oacute;", $cadena);
		$cadena = str_replace("Ú", "&Uacute;", $cadena);
		$cadena = str_replace("Ñ", "&Ntilde;", $cadena);
		$cadena = str_replace("ü", "&uuml;", $cadena);
		$cadena = str_replace("Ü", "&Uuml;", $cadena);
		$cadena = str_replace("@", "&#64;", $cadena);
		$cadena = str_replace("°", "&deg;", $cadena);
		$cadena = str_replace("º", "&deg;", $cadena);
		$cadena = str_replace("ª", "&deg;", $cadena);
		$cadena = str_replace('"', "&#34;", $cadena);

		return $cadena;
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
}
?>
