<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionReport o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 	Array asociativo que contiene todos los parámetros de la vista para ser utilizados en la plantilla.
 */
header("Content-Type: application/msword; charset=UTF-8");
header('Content-Disposition: inline; filename=expedientes_sin_documento_cargado.doc');

//  SE COMIENZA A ARMAR EL DOCUMENTO 
echo "<html>";
echo "\n<body>";

echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Municipalidad de General Pueyrredon</p>";
echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Sistema de Expedientes</p>";
echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Honorable Concejo Deliberante</p>";
echo "\n<hr>";
echo "\n<p style='margin:0cm;margin-bottom:.0001pt;'><b>Expedientes sin documento cargado</b></p>";