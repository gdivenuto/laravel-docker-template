<?php
/**
 * Helper para los QueryBuilders.
 * Pone a disposición funcionalidad común a todos los mecanismos generadores de query.
 */
class BuilderHelper {
	
   	/**
	 * Agrega las comillas invertidas a un nombre de columna o tabla.
	 * @param  string $argumento Nombre de columna o tabla, con la forma: tabla.columna
	 * @return string            El nombre de la columna con las comillas invertidas aplicadas, por ejemplo: `tabla`.`columna`
	 */
	 public static function agregarBackquote($argumento) {
		// separo por el punto
		$partes = explode('.', $argumento);

		// agrego los backquotes a cada parte
		$salida = array();
		foreach ($partes as $p) {
			$salida[] = sprintf("`%s`", $p);
		}

		// devuelvo lo modificado
		return implode('.', $salida);
	}

}
?>