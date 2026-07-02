<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionDocumentoTexto o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 				Array asociativo que contiene todos los parámetros de la vista para ser
 *  								utilizados en la plantilla.
 *
 * Además:
 * 
 *  $this->vista->dataTitulo		Titulo de la vista
 *  $this->vista->dataSubtitulo		Subtitulo de la vista
 *  $this->vista->dataTexto			Texto introductorio de la vista
 *  $this->vista->dataUsuario		Instancia del usuario actual.
 *  $this->vista->dataMensajeOk		Mensaje de confirmación que debe mostrarse en la vista.
 *  $this->vista->dataMensajeError	Mensaje de error que debe mostrarse en la vista.
 */
$expedientes = $this->vista->data['resultados']; // Resultado completo de expedientes
$parametros_codificados = $this->vista->data['parametros_codificados']; // Criterio de búsqueda utilizado en el listado

echo "\n<hr>";
echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Letra".chr(9)."Car&aacute;tula</b></p>";
echo "\n<hr>";

// Por cada Expediente
foreach ($expedientes as $e) {
	if ($e->tipo == 'E') $titulo_segun_tipo = 'Expediente: ';
	if ($e->tipo == 'N') $titulo_segun_tipo = 'Nota: ';
	if ($e->tipo == 'R') $titulo_segun_tipo = 'Recomendaci&oacute;n: ';

	// Si el iniciador es un Concejal
    if ( $e->iniciador_codigo == 'CJA' ) {
    	foreach ($e->autores as $autor) {
			if ( !is_null($autor->ro_descripcion_grp) && $autor->ro_descripcion_grp != '' ) {
				$nombre_iniciador = FormatText::get()->reemplazarPorHTML($autor->ro_descripcion_grp);// Se muestra su descripción
				break;// Se sale del foreach de autores
			}
		}
	} else
        // sino, se muestra la descripción del iniciador
        $nombre_iniciador = FormatText::get()->reemplazarPorHTML($e->ro_iniciador_descripcion_grp);

	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$titulo_segun_tipo." ".$e->numero." - ".$e->iniciador_codigo." - ".substr($e->anio, -2).": ".$nombre_iniciador.": ".FormatText::get()->reemplazarPorHTML($e->caratula)."</b></p>";
	
	// Por cada proyecto			
	foreach ($e->proyectos as $p)
		// Si posee Extracto, se muestra
		if ( !is_null($p->extracto) && $p->extracto != '' )
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>".FormatText::get()->reemplazarPorHTML($p->extracto)."</p>";

	echo "\n<hr>";
}
?>