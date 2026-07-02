<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionDocumentoTexto 
 * o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data Array asociativo que contiene todos los parámetros de la vista para ser
 *  utilizados en la plantilla.
  */
// Criterio de búsqueda utilizado
$criterios = $this->vista->data['criterio_busqueda'];
if ( count ($criterios) > 0 ) {
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt;'><b>Criterio de b&uacute;squeda utilizado.</b></p>";
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>".implode('<br>', $criterios)."</p>";
}