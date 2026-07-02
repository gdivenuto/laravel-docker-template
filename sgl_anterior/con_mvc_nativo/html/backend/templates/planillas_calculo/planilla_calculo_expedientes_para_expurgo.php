<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionPlanillaCalculo o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 	Array asociativo que contiene todos los parámetros de la vista para ser utilizados en la plantilla.
 */
$expedientes 	   = $this->vista->data['resultados']; // Resultado completo de expedientes
$criterio_busqueda = $this->vista->data['criterio_busqueda']; // Criterio de búsqueda utilizado en el listado

echo "\nCriterio de Búsqueda.";

if ( $criterio_busqueda['f_fecha_desde'] != '' && $criterio_busqueda['f_fecha_hasta'] != '' )
	echo "\nDesde:,".$criterio_busqueda['f_fecha_desde'];
	echo "\nHasta:,".$criterio_busqueda['f_fecha_hasta'];

if ( $criterio_busqueda['f_estado'] != '' )
	echo "\nEstado:,\"".str_replace('"', "", $criterio_busqueda['f_estado'])."\"";

echo "\n";
// Títulos de cada columna, separados por coma
echo "\nAño,Tipo,Número,Cuerpo,Alcance,Fecha Ingreso,Orden Proyecto,Proyecto,Fecha Prom.,Nro. Prom.,Decreto Prom.,Fecha Sanc.,Nro. Sanc.,Extracto";

// Por cada Expediente
foreach ($expedientes as $e) {
	// Por cada proyecto del expediente
	foreach ($e->proyectos as $p) {
		// Se va armando la fila de valores separados por coma, formato CSV
		$fila_en_csv = "\n";
		$fila_en_csv .= $e->anio.",".$e->tipo.",".$e->numero.",".$e->cuerpo.",".$e->alcance;

		$fila_en_csv .= ",".Validator::get()->convertirAFechaVista($e->fecha_entrada_expe);
		
		$fila_en_csv .= ",".$p->orden_proyecto.",".$p->ro_descripcion_proyecto;

		// Si posee Fecha y Número de Promulgación, se muestran
		$fila_en_csv .= ( $p->ro_fecha_promulga != '' && $p->ro_numero_promulga != '' ) ? ",".Validator::get()->convertirAFechaVista($p->ro_fecha_promulga).",".$p->ro_numero_promulga : ',,';

		// Si posee decreto de promulga
		$fila_en_csv .= ( $p->ro_decreto_promulga != '' ) ? ",".$p->ro_decreto_promulga : ',';

		// Si posee fecha y número de Sanción
		$fila_en_csv .= ( $p->ro_fecha_sancion != '' && $p->ro_numero_sancion != '' ) ? ",".Validator::get()->convertirAFechaVista($p->ro_fecha_sancion).",".$p->ro_numero_sancion : ',,';

		// Si posee Extracto
		$fila_en_csv .= ( !is_null($p->extracto) && $p->extracto != '' ) ? ",\"".str_replace('"',"",$p->extracto)."\"" : ',';

		echo $fila_en_csv;
	}
}