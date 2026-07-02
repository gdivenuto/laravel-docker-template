<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionDocumentoTexto o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data Array asociativo que contiene todos los parámetros de la vista para ser utilizados en la plantilla.
 */
$informes = $this->vista->data['resultados']; // Resultado completo de expedientes
$parametros_codificados = $this->vista->data['parametros_codificados']; // Criterio de búsqueda utilizado en el listado

echo "\n<hr>";
echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Letra".chr(9)."Car&aacute;tula</b></p>";
echo "\n<hr>";

// Por cada Informe
foreach ($informes as $inf) {
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$inf->anio." ".$inf->tipo." ".$inf->numero." ".FormatText::get()->reemplazarPorHTML($inf->caratula)."</b></p>";//$inf->iniciador_codigo
	
	// Detalle y Fecha del pedido del Informe
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Detalle: </b>".chr(9).FormatText::get()->reemplazarPorHTML($inf->detalle_informe).chr(9).chr(9).chr(9)." <b>Pedido el</b> ".Validator::get()->convertirAFechaVista($inf->fecha_pedido_informe)."</p>";

	// Por cada proyecto			
	foreach ($inf->proyectos as $p)
		// Si posee Extracto
		if ( !is_null($p->extracto) && $p->extracto != '' )
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>".FormatText::get()->reemplazarPorHTML($p->extracto)."</p>";

	// Comisión
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>En Comisi&oacute;n desde el </b>".chr(9).Validator::get()->convertirAFechaVista($inf->ro_fecha_comision).chr(9).chr(9).chr(9)." <b>Cantidad de d&iacute;as</b>: ".$inf->cantidad_dias_del_informe."</p>";
	// Se separa cada Informe con una línea
	echo "\n<hr>";
}
?>