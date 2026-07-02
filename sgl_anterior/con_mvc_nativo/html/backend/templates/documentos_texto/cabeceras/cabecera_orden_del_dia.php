<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionReport o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 				Array asociativo que contiene todos los parámetros de la vista para ser
 *  								utilizados en la plantilla.
 */
header("Content-Type: application/msword; charset=UTF-8");
header('Content-Disposition: inline; filename=orden_del_dia.doc');

//  SE COMIENZA A ARMAR EL DOCUMENTO 
echo "<html>";
echo "\n<body>";
echo "\n<p style='margin:0cm;margin-bottom:.0001pt;'><b>Orden del D&iacute;a</b></p>";
echo "\n<hr>";