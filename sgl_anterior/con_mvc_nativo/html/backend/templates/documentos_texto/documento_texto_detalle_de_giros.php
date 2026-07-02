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
echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Letra".chr(9)."Car&aacute;tula".chr(9)."Fecha de Entrada</b></p>";
echo "\n<hr>";

// Por cada Expediente
foreach ($expedientes as $e) {
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$e->anio." ".$e->tipo." ".$e->numero." ".$e->iniciador_codigo." ".FormatText::get()->reemplazarPorHTML($e->caratula)."</b> ".Validator::get()->convertirAFechaVista($e->fecha_entrada_expe)."</p>";
	
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Car&aacute;tula</b>: ".FormatText::get()->reemplazarPorHTML($e->caratula)."</p>";
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Iniciador</b>: ".FormatText::get()->reemplazarPorHTML($e->ro_iniciador_descripcion_grp)."</p>";
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Categor&iacute;a</b>: ".FormatText::get()->reemplazarPorHTML($e->ro_descripcion_categoria)."</p>";
	
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Tema: </b>";
	$cantidad_temas = count($e->temas);
	for ($t=0; $t < $cantidad_temas; $t++) {
		$tema = &$e->temas[$t];
		echo chr(9).chr(9).FormatText::get()->reemplazarPorHTML($tema->descripcion_tema);
		if ( $t != ($cantidad_temas - 1) ) echo '<b>;</b> ';
	}
	echo "\n</p>";
	
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Autor: </b>";
	$cantidad_autores = count($e->autores);
	for ($a=0; $a < $cantidad_autores; $a++) {
		$autor = &$e->autores[$a];
		echo chr(9).chr(9).FormatText::get()->reemplazarPorHTML($autor->descripcion_grp);
		if ( $a != ($cantidad_autores - 1) ) echo '<b>;</b> ';
	}
	echo "\n</p>";
	
	foreach ($e->proyectos as $p) {
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Proyecto de ".FormatText::get()->reemplazarPorHTML($p->ro_descripcion_proyecto)."</b></p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'><b>Extracto: </b>".chr(9).FormatText::get()->reemplazarPorHTML($p->extracto)."</p>";
	}

	// Se toma el Estado actual
	$estado_actual = $e->estados[count($e->estados)-1];
	if ( !is_null($estado_actual) )
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Estado: </b>".chr(9).FormatText::get()->reemplazarPorHTML($estado_actual->ro_nombre_estado).chr(9).chr(9).chr(9)." Desde el ".Validator::get()->convertirAFechaVista($estado_actual->fecha_estado)."</p>";
	
	// Se toma la Comisión actual
	$comision_actual = $e->giros[count($e->giros)-1];
	if ( !is_null($comision_actual) )
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Comisi&oacute;n: </b>".chr(9).FormatText::get()->reemplazarPorHTML($comision_actual->ro_descripcion_grp).chr(9).chr(9).chr(9)." Desde el ".Validator::get()->convertirAFechaVista($comision_actual->fecha_entrada_giro)."</p>";
				
	// Se muestran los giros a cada comisión
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Giros: </b></p>";
	foreach ($e->giros as $g)
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>".$g->comision_codigo.chr(9).FormatText::get()->reemplazarPorHTML($g->ro_descripcion_grp).chr(9).Validator::get()->convertirAFechaVista($g->fecha_entrada_giro).chr(9).Validator::get()->convertirAFechaVista($g->fecha_salida_giro).chr(9).$g->dictamen_giro."</p>";

	echo "\n<hr>";
}
?>