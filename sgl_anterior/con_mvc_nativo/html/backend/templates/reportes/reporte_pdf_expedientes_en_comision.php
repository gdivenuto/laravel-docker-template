<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionReport o alguno de sus descendientes.
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
?>
<!-- Aquí comienza a mostrarse el resultado completo -->
<table class="pdf_tabla_cuerpo_reporte">
	<tbody>
		<tr>
			<td class="pdf_ficha_expediente pdf_texto_gris">
				<table>
					<tr>
						<td class="listados_pdf_ficha_clave_expediente"> A&ntilde;o&nbsp;Tipo&nbsp;Nro.&nbsp;Cpo.&nbsp;Alc.&nbsp;&nbsp;&nbsp;Car&aacute;tula</td>
						<td class="listados_pdf_ficha_fecha_ingreso_expediente">
							<?php echo (empty($parametros_codificados['f_estado'])) ? 'D&iacute;as en Comisi&oacute;n&nbsp;&nbsp;&nbsp;Fecha de Entrada' : 'Fecha de Entrada'; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
<?php
$cantidad_expedientes = 0;
$cantidad_notas = 0;
foreach ($expedientes as $e) {
	// Se contabilizan los Expedientes y Notas
	if ($e->tipo == 'E') {
		$cantidad_expedientes++;
	}

	if ($e->tipo == 'N') {
		$cantidad_notas++;
	}

	?>
		<tr>
			<td class="pdf_ficha_expediente">
				<table>
					<tr>
						<td class="listados_pdf_ficha_clave_expediente">
							<strong><?php echo sprintf('%d&nbsp;%s&nbsp;%d&nbsp;%d&nbsp;%d&nbsp;&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;&nbsp;%s', $e->anio, $e->tipo, $e->numero, $e->cuerpo, $e->alcance, $e->iniciador_codigo, $e->caratula); ?></strong>
						</td>
						<td class="listados_pdf_ficha_fecha_ingreso_expediente">
							<?php if ($e->cantidad_dias_en_comision != '' && $e->cantidad_dias_en_comision != '-1') { // Si se buscó por una Comisión y se calcularon sus días en ella ?>
								<strong><?php echo sprintf('%s&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $e->cantidad_dias_en_comision . ' d&iacute;as'); ?></strong>
							<?php }?>
							<strong><?php echo sprintf('%s&nbsp;&nbsp;&nbsp;', Validator::get()->convertirAFechaVista($e->fecha_entrada_expe)); ?></strong>
						</td>
					</tr>
				</table>
				<table>
					<?php
// Por cada proyecto que posee
	foreach ($e->proyectos as $p)
	// Si posee extracto se muestra
	{
		if (!is_null($p->extracto) && $p->extracto != '') {
			echo '<tr><td class="listados_pdf_ficha_info_extracto_e_informes">' . $p->extracto . '</td></tr>';
		}
	}

	// En caso que el expediente respectivo posea por lo menos un Informe
	if (count($e->informes) > 0) {
		// Se toma el último Informe
		$ultimo_informe = $e->informes[count($e->informes) - 1];

		$fecha_pedido_informe = Validator::get()->convertirAFechaVista($ultimo_informe->fecha_pedido_informe);
		$observaciones_informe = (!is_null($ultimo_informe->observaciones_informe)) ? $ultimo_informe->observaciones_informe : 'no posee';

		// Si se venció el plazo de 120 días en la comisión y posee un Informe abierto
		//if ( ($e->cantidad_dias_en_comision > 120) && (!is_null($e->ultimo_informe)) ) {
		// 15/06/2021 XXXX
		// Si no se ha devuelto el informe
		if (isset($ultimo_informe) && $ultimo_informe->fecha_vuelta_informe == '') {
			echo '<tr><td class="listados_pdf_ficha_info_extracto_e_informes">';
			echo sprintf('<strong>Informe pendiente</strong> desde el %s&nbsp;, %s <strong>Observaciones: </strong>%s', $fecha_pedido_informe, $ultimo_informe->detalle_informe, $observaciones_informe);
			echo '</td></tr>';
		}
	}
	?>
				</table>
			</td>
		</tr>
<?php
} // fin del foreach de expedientes
?>
		<tr>
			<td>
				<table>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td class="listado_pdf_titulo_tipo_expe pdf_texto_gris">Cantidad de Expedientes:</td>
						<td class="listado_pdf_valor_contador_exped_en_comision"><?php echo $cantidad_expedientes; ?></td>
					</tr>
					<tr>
						<td class="listado_pdf_titulo_tipo_expe pdf_texto_gris">Cantidad de Notas:</td>
						<td class="listado_pdf_valor_contador_exped_en_comision"><?php echo $cantidad_notas; ?></td>
					</tr>
					<tr>
						<td class="listado_pdf_titulo_tipo_expe pdf_texto_gris">Cantidad Total:</td>
						<td class="listado_pdf_valor_contador_exped_en_comision"><strong><?php echo ($cantidad_expedientes + $cantidad_notas); ?></strong></td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>
