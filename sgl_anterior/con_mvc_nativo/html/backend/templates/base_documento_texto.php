<?php
/**
 * Este script esta diseñado para ser incluído como plantilla desde BaseViewActionDocumentoTexto 
 * o alguno de sus descendientes.
 * A continuación se detallan los métodos de generación de la plantilla, implementados en 
 * BaseViewActionDocumentoTexto o alguno de sus descendientes.
 *
 * 	$this->generarHtmlHeader();		--> No utilizado
 * 	$this->generarHtmlHeaderCSS();	--> No utilizado
 * 	$this->generarHtmlHeaderJS();	--> No utilizado
 * 	$this->generarMenu();			--> No utilizado
 * 	$this->generarCabecera();
 * 	$this->generarCriterioBusqueda();
 * 	$this->generarCuerpo();
 * 	$this->generarPie();
 */

// Incluyo la cabecera
$this->generarCabecera();

// Incluyo el criterio de búsqueda utilizado
$this->generarCriterioBusqueda();

// Incluyo el cuerpo de la vista
$this->generarCuerpo();

// Incluyo el pie de pagina 
$this->generarPie();
?>