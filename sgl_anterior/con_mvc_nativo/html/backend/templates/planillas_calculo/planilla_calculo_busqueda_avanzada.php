<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionPlanillaCalculo o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 	Array asociativo que contiene todos los parámetros de la vista para ser utilizados en la plantilla.
 */
$expedientes = $this->vista->data['resultados']; // Resultado completo de expedientes
$criterio_busqueda = $this->vista->data['criterio_busqueda']; // Criterio de búsqueda utilizado en el listado

echo "\nCriterio de Búsqueda.";

if ( $criterio_busqueda['f_fecha_desde'] != '' && $criterio_busqueda['f_fecha_hasta'] != '' )
	echo "\nDesde:,".$criterio_busqueda['f_fecha_desde'];
	echo "\nHasta:,".$criterio_busqueda['f_fecha_hasta'];

if ( $criterio_busqueda['f_opcion_fechas'] != '' )
	echo "\nPor:,\"".str_replace('"', "", $criterio_busqueda['f_opcion_fechas'])."\"";

if ( $criterio_busqueda['f_categoria'] != '' )
	echo "\nCategoría:,\"".str_replace('"', "", $criterio_busqueda['f_categoria'])."\"";

if ( $criterio_busqueda['f_iniciador'] != '' )
	echo "\nIniciador:,\"".str_replace('"', "", $criterio_busqueda['f_iniciador'])."\"";

if ( $criterio_busqueda['f_caratula'] != '' )
	echo "\nCarátula:,\"".str_replace('"', "", $criterio_busqueda['f_caratula'])."\"";

if ( $criterio_busqueda['f_tema'] != '' )
	echo "\nTema:,\"".str_replace('"', "", $criterio_busqueda['f_tema'])."\"";

if ( $criterio_busqueda['f_autor'] != '' )
	echo "\nAutor:,\"".str_replace('"', "", $criterio_busqueda['f_autor'])."\"";

if ( $criterio_busqueda['f_estado'] != '' )
	echo "\nEstado:,\"".str_replace('"', "", $criterio_busqueda['f_estado'])."\"";

if ( $criterio_busqueda['f_comision'] != '' )
	echo "\nComisión:,\"".str_replace('"', "", $criterio_busqueda['f_comision'])."\"";

echo "\n";
// Títulos de cada columna, separados por coma
echo "\nAño,Tipo,Número,Cuerpo,Alcance,Fecha Ingreso,Carátula,Iniciador,Categoría,Temas,Autores,Proyectos,Extracto,Estado,Desde,Comisión,Desde";

// Por cada Expediente
foreach ($expedientes as $e) {
	// Por cada proyecto del expediente
	foreach ($e->proyectos as $p) {
		// Se va armando la fila de valores separados por coma, formato CSV
		$fila_en_csv = "\n";
		$fila_en_csv .= $e->anio.",".$e->tipo.",".$e->numero.",".$e->cuerpo.",".$e->alcance;

		$fila_en_csv .= ",".Validator::get()->convertirAFechaVista($e->fecha_entrada_expe);

		$fila_en_csv .= ",\"".$e->caratula."\"";
		$fila_en_csv .= ",\"".$e->ro_iniciador_descripcion_grp."\"";
		$fila_en_csv .= ",\"".$e->ro_descripcion_categoria."\"";

		$cantidad_temas = count($e->temas);
		for ($t=0; $t < $cantidad_temas; $t++) {
			$tema = &$e->temas[$t];
			// Si es el primer (ó único) Tema se pasa a otra celda con la coma y se abre comilla doble
			if ($t == 0)
				$fila_en_csv .= ",\"";
			// Si NO es el primero ni el último, se agrega la coma para separarlos
			elseif ( $t < $cantidad_temas )
				$fila_en_csv .= ",";
			
			// Se agrega la descripción del Tema
			$fila_en_csv .= str_replace('"',"",str_replace(','," ",$tema->descripcion_tema));

			// Si es el último Tema (ó el único) se cierra la comilla doble
			if ($t == ($cantidad_temas-1) )
				$fila_en_csv .= "\"";
		}

		$cantidad_autores = count($e->autores);
		for ($a=0; $a < $cantidad_autores; $a++) {
			$autor = &$e->autores[$a];
			// Si es el primer (ó único) Autor se pasa a otra celda con la coma y se abre comilla doble
			if ($a == 0)
				$fila_en_csv .= ",\"";
			// Si NO es el primero ni el último, se agrega la coma para separarlos
			elseif ( $a < $cantidad_autores )
				$fila_en_csv .= ",";
			
			// Se agrega la descripción del Autor
			$fila_en_csv .= str_replace('"',"",str_replace(','," ",$autor->descripcion_grp));
			
			// Si es el último Autor (ó el único) se cierra la comilla doble
			if ($a == ($cantidad_autores-1) )
				$fila_en_csv .= "\"";
		}
		
		$fila_en_csv .= ",".$p->orden_proyecto." - ".$p->ro_descripcion_proyecto;

		// Si posee Extracto
		$fila_en_csv .= ( !is_null($p->extracto) && $p->extracto != '' ) ? ",\"".str_replace('"',"",$p->extracto)."\"" : ',';

		// Se toma el Estado actual
		$estado_actual = $e->estados[count($e->estados)-1];
		if ( !is_null($estado_actual) ) {
			$fila_en_csv .= ",\"".str_replace('"',"",$estado_actual->ro_nombre_estado)."\"";

			$fila_en_csv .= ( $estado_actual->fecha_estado != '' ) ? ",".Validator::get()->convertirAFechaVista($estado_actual->fecha_estado) : ",";
		}

		// Se toma la Comisión actual
		$comision_actual = $e->giros[count($e->giros)-1];
		if ( !is_null($comision_actual) ) {
			$fila_en_csv .= ",\"".str_replace('"',"",$comision_actual->ro_descripcion_grp)."\"";
			
			$fila_en_csv .= ( $comision_actual->fecha_entrada_giro != '' ) ? ",".Validator::get()->convertirAFechaVista($comision_actual->fecha_entrada_giro) : ",";
		}

		echo $fila_en_csv;
	}
}