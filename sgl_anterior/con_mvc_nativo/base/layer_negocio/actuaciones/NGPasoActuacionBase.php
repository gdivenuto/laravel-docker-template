<?php
/**
 * Capa de negocio base para funcionalidad de PasoActuaciones.
 *
 * @author XXXX
 *
 */
class NGPasoActuacionBase extends NGBaseClass {

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
	// ---- Helpers para PasoActuaciones --------------------------------------
	// ------------------------------------------------------------------------
	/**
	 */

	/**
	 * Obtiene el contenido de un directorio, filtrando por una expresión regular.
	 * @param  string $directorio Directorio del cual obtener el contenido. Debe terminar con una barra.
	 * @param  string $regexp     Expresión regular de filtrado. Por defecto, solo archivos terminados en .pdf
	 * @param  boolean $ruta_completa True: la ruta se devuelve completa, False: solo el nombre del archivo.
	 * @return Array              Nombre de archivos en el directorio.
	 */
	public function obtenerContenidoDirectorio($directorio, $regexp = '/\.pdf$/i', $ruta_completa = true)
	{
		$contenido = [];

		if (! is_dir($directorio)) return [];

		if ($handle = opendir($directorio)) {
			while (false !== ($file = readdir($handle)))
				if ($file != "." && $file != ".." && preg_match($regexp, $file))
					$contenido[] = ($ruta_completa)
						? $directorio.$file
						: $file;

			closedir($handle);
		}
		sort($contenido);

		return $contenido;
	}

	/**
	 * Verifica la existencia de una lista de parametros en el contenedor de
	 * datos. En caso de que existan parametros faltantes, devuelve un listado
	 * de errores.
	 * @param  Array $parametros Lista de parametros a verificar
	 * @param  Array $datos      Conjunto de datos donde se verificará la existencia de los parametros.
	 * @return Array             Lista de errores (parametros faltantes).
	 */
	protected function verificarExistenciaParametros($parametros, $datos) {
		$faltantes = [];
		foreach ($parametros as $p) {
			if (!array_key_exists($p, $datos))
				$faltantes[] = "El parámetro '$p' es inexistente.";
		}
		return $faltantes;
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
		return [];
	}
}
?>
