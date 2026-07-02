<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionDocumentoTexto o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 *
 *  $this->vista->data 	Array asociativo que contiene todos los parámetros de la vista para ser utilizados en la plantilla.
 */
$expedientes = $this->vista->data['resultados']; // Resultado completo de expedientes

echo "\n<hr>";
echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o" . chr(9) . "Tipo" . chr(9) . "Nro." . chr(9) . "Letra" . chr(9) . "Car&aacute;tula</b></p>";
echo "\n<hr>";

// Por cada Expediente
foreach ($expedientes as $e) {
	// Se arma el texto informativo con los días en comisión del expediente
	$dias_en_comision = ($e->cantidad_dias_en_comision != '' && $e->cantidad_dias_en_comision != '-1') ? "( " . $e->cantidad_dias_en_comision . " d&iacute;as en Comisi&oacute;n )" : "";

	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
	echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>" . $e->anio . " " . $e->tipo . " " . $e->numero . " " . $e->iniciador_codigo . " " . FormatText::get()->reemplazarPorHTML($e->caratula) . "</b> " . $dias_en_comision . "</p>";

	// Por cada proyecto del expediente
	foreach ($e->proyectos as $p)
	// Si posee Extracto
	{
		if (!is_null($p->extracto) && $p->extracto != '') {
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>" . FormatText::get()->reemplazarPorHTML($p->extracto) . "</p>";
		}
	}

	// En caso que el expediente respectivo posea por lo menos un Informe
	if (count($e->informes) > 0) {
		// Se toma el último Informe
		$ultimo_informe = $e->informes[count($e->informes) - 1];

		$fecha_pedido_informe = Validator::get()->convertirAFechaVista($ultimo_informe->fecha_pedido_informe);
		$observaciones_informe = (!is_null($ultimo_informe->observaciones_informe)) ? $ultimo_informe->observaciones_informe : 'no posee observaciones';

		// Si se venció el plazo de 120 días en la comisión y posee un Informe abierto
		//if ( ($e->cantidad_dias_en_comision > 120) && (!is_null($e->ultimo_informe)) ) {
		// 15/06/2021 XXXX
		// Si no se ha devuelto el informe
		if (isset($ultimo_informe) && $ultimo_informe->fecha_vuelta_informe == '') {
			// SE MUESTRA EL DETALLE DEL INFORME PENDIENTE
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'><b>Informe pendiente</b> desde el " . $fecha_pedido_informe . ": " . FormatText::get()->reemplazarPorHTML($observaciones_informe) . ".</p>";
		}
	}
}
?>
