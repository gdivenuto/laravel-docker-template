<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionDocumentoTexto o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 	Array asociativo que contiene todos los parámetros de la vista para ser utilizados en la plantilla.
 */
$expedientes = $this->vista->data['resultados']; // Resultado completo de expedientes

echo "\n<hr>";
echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."Tipo".chr(9)."Nro.".chr(9)."Letra".chr(9)."Car&aacute;tula</b></p>";
echo "\n<hr>";

// Por cada Expediente
foreach ($expedientes as $e) {
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br><b>".$e->anio." ".$e->tipo." ".$e->numero." ".$e->iniciador_codigo." ".FormatText::get()->reemplazarPorHTML($e->caratula)."</b></p>";
	
	// Por cada proyecto del expediente			
	foreach ($e->proyectos as $p)
		// Si posee Extracto
		if ( !is_null($p->extracto) && $p->extracto != '' )
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>".FormatText::get()->reemplazarPorHTML($p->extracto)."</p>";

	// Se toma el Estado actual
	$estado_actual = $e->estados[count($e->estados)-1];
	if ( !is_null($estado_actual) ) {
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Estado: </b>".FormatText::get()->reemplazarPorHTML($estado_actual->ro_nombre_estado)."</p>";

		if ( !is_null($estado_actual->fecha_estado) )
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Desde el: </b>".Validator::get()->convertirAFechaVista($estado_actual->fecha_estado)."</p>";
		
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Observaciones: </b>".FormatText::get()->reemplazarPorHTML($estado_actual->observaciones_estado)."</p>";
	}
}
?>