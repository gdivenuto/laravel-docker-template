<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionPlanillaCalculo o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 	Array asociativo que contiene todos los parámetros de la vista para ser utilizados en la plantilla.
 */
header("Cache-Control: must-revalidate");
header("Pragma: must-revalidate");
header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename=resultado_busqueda_avanzada.csv');

//  Se comienza a armar la plantilla
echo "Municipalidad de General Pueyrredon";
echo "\nSistema de Expedientes";
echo "\nHonorable Concejo Deliberante";
echo "\n";
echo "\nBúsqueda Avanzada.";
echo "\n";