<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionDocumentoTexto o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 	Array asociativo que contiene todos los parámetros de la vista para ser utilizados en la plantilla.
 */
$expedientes = $this->vista->data['resultados']; // Resultado completo de expedientes
$parametros_codificados = $this->vista->data['parametros_codificados']; // Criterio de búsqueda utilizado en el listado

echo "\n<hr>";
echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Cpo.".chr(9)."Alc.".chr(9)."Car&aacute;tula".chr(9)."Fecha de Entrada</b></p>";
echo "\n<hr>";

// Por cada Expediente
foreach ($expedientes as $e) {
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$e->anio." ".$e->tipo." ".$e->numero." ".$e->cuerpo." ".$e->alcance." ".$e->caratula." ".Validator::get()->convertirAFechaVista($e->fecha_entrada_expe)."</b></p>";
	
	// Por cada proyecto del expediente
	foreach ($e->proyectos as $p) {

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt;'>".$p->orden_proyecto." ".FormatText::get()->reemplazarPorHTML($p->ro_descripcion_proyecto)."</p>";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt;'><b>Promulgado:</b>";
		// Si posee Fecha y Número de Promulgación, se muestran
		echo ( !empty($p->ro_fecha_promulga) && !empty($p->ro_numero_promulga) ) ? Validator::get()->convertirAFechaVista($p->ro_fecha_promulga)." ".$p->ro_numero_promulga : "---";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt;'><b>Decretado:</b>";
		// Si posee Decreto de Promulgación, se muestra
		echo ( !empty($p->ro_decreto_promulga) ) ? $p->ro_decreto_promulga : "---";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt;'><b>Sancionado:</b>";
		// Si posee Fecha y Número de Sanción, se muestran
		echo Validator::get()->convertirAFechaVista($p->ro_fecha_sancion)." ".$p->ro_numero_sancion;

		// Si posee Extracto
		if ( !is_null($p->extracto) && $p->extracto != '' )
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>".FormatText::get()->reemplazarPorHTML($p->extracto)."</p>";
		
		echo "\n<hr>";
	}
}
?>