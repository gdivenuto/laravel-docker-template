<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionDocumentoTexto o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 *
 *  $this->vista->data Array asociativo que contiene todos los parámetros de la vista para ser
 */
$expedientes = $this->vista->data['resultados']; // Resultado completo de expedientes
$marca_auxiliar = 99;
// Por cada Expediente
foreach ($expedientes as $e) {
	// Si posee una marca en comisión
	if ( !is_null($e->marca_comision) ) {
		// Si es diferente a la ya mostrada
		if ( $e->marca_comision != $marca_auxiliar ) {
			$marca_auxiliar = $e->marca_comision;
			switch ($marca_auxiliar) {
				case '0':
					$nombre_marca = "Sin marca";
					break;
				case '1':
					$nombre_marca = "Para tratar";
					break;
				case '2':
					$nombre_marca = "Para su conocimiento";
					break;
				case '3':
					$nombre_marca = "Para archivo";
					break;
				// 07/12/2021 XXXX, se retiró esta Marca.
				// case '4':
				// 	$nombre_marca = "Para pr&oacute;rroga";
				// 	break;
				case '5':
					$nombre_marca = "Para convalidar";
					break;
			}
			// Se muestra el nombre de la marca en comisión respectiva antes de cada listado
			echo "\n<hr><p style='margin:0cm;margin-bottom:.0001pt'><b>".$nombre_marca."</b></p>";
		}
		// Ya no se muestran los Días en Comisión !!!
		// Se arma el texto informativo con los días en comisión del expediente
		//$dias_en_comision = ( $e->cantidad_dias_en_comision != '-1' ) ? "( ".$e->cantidad_dias_en_comision." d&iacute;as en Comisi&oacute;n )" : "";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$e->anio." ".$e->tipo." ".$e->numero." ".$e->iniciador_codigo." ".FormatText::get()->reemplazarPorHTML($e->caratula)."</b></p>";

		// Por cada proyecto del expediente
		foreach ($e->proyectos as $p)
			// Si posee Extracto
			if ( !is_null($p->extracto) && $p->extracto != '' )
				echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>".FormatText::get()->reemplazarPorHTML($p->extracto)."</p>";
	}
}
?>
