<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionDocumentoTexto o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data Array asociativo que contiene todos los parámetros de la vista para ser utilizados en la plantilla.
 */
$expedientes = $this->vista->data['resultados']; // Resultado completo de expedientes
$parametros_codificados = $this->vista->data['parametros_codificados']; // Criterio de búsqueda utilizado en el listado

echo "\n<hr>";
echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Letra".chr(9)."Fecha de Entrada</b></p>";
echo "\n<hr>";

// Por cada Expediente
foreach ($expedientes as $e) {
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$e->anio." ".$e->tipo." ".$e->numero." ".$e->iniciador_codigo."</b> ".Validator::get()->convertirAFechaVista($e->fecha_entrada_expe)."</p>";
	
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Car&aacute;tula: </b>".FormatText::get()->reemplazarPorHTML($e->caratula)."</p>";
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Iniciador: </b>".FormatText::get()->reemplazarPorHTML($e->ro_iniciador_descripcion_grp)."</p>";
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Categor&iacute;a: </b>".FormatText::get()->reemplazarPorHTML($e->ro_descripcion_categoria)."</p>";
		
	foreach ($e->proyectos as $p) {
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Proyecto de ".FormatText::get()->reemplazarPorHTML($p->ro_descripcion_proyecto)."</b></p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'><b>Extracto: </b>".chr(9).FormatText::get()->reemplazarPorHTML($p->extracto)."</p>";
	}
	echo "\n<hr>";
}
?>