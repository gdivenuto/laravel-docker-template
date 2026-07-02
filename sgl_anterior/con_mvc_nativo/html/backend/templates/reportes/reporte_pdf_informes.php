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
// Resultado completo de informes
$informes = $this->vista->data['resultados'];
?>
<!-- Aquí comienza a mostrarse el resultado completo -->
<table class="pdf_tabla_cuerpo_reporte">
	<tbody>
		<tr>
			<td class="pdf_ficha_expediente pdf_texto_gris">
				<table>
					<tr>
						<td class="listados_pdf_ficha_clave_expediente"> A&ntilde;o Tipo Nro. Cpo. Alc. Car&aacute;tula</td>
						<td class="listados_pdf_ficha_fecha_ingreso_expediente">Fecha de Entrada</td>
					</tr>
				</table>
			</td>
		</tr>
	<?php
	foreach ($informes as $inf) {
	?>
		<tr>
			<td class="pdf_ficha_expediente">
				<!-- Clave + Carátula + Fecha de Entrada del Expediente -->
				<table>
					<tr>
						<td class="listados_pdf_ficha_clave_expediente">
							<strong><?php echo sprintf('%d&nbsp;%s&nbsp;%d&nbsp;%d&nbsp;%d&nbsp;&nbsp;&nbsp;&nbsp;%s', $inf->anio, $inf->tipo, $inf->numero, $inf->cuerpo, $inf->alcance, $inf->ro_caratula); ?></strong>
						</td>
						<td class="listados_pdf_ficha_fecha_ingreso_expediente">
							<strong><?php echo Validator::get()->convertirAFechaVista($inf->ro_fecha_entrada_expe); ?></strong>
						</td>
					</tr>
				</table>
				<!-- Detalle y Fecha del pedido del Informe -->
				<table>
					<tr>
						<td class="pdf_informes_ficha_titulo">Detalle: <?php echo $inf->detalle_informe; ?></td>
						<td class="pdf_informes_ficha_fechas">Pedido el <?php echo Validator::get()->convertirAFechaVista($inf->fecha_pedido_informe); ?></td>
					</tr>
				</table>
				<!-- Extracto del proyecto -->
				<table>
					<?php
					foreach ($inf->proyectos as $p)
						if ( !is_null($p->extracto) && $p->extracto != '' )
							echo '<tr><td class="listados_pdf_ficha_info_extracto_e_informes">'.$p->extracto.'</td></tr>';
					?>
				</table>
				<!-- Comisión -->
				<table>
					<tr>
						<td class="pdf_informes_ficha_titulo">
							En Comisi&oacute;n desde el <?php echo Validator::get()->convertirAFechaVista($inf->ro_fecha_comision); ?>
						</td>
						<td class="pdf_informes_ficha_fechas">
							Cantidad de d&iacute;as: <?php echo $inf->cantidad_dias_del_informe; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	<?php
	} // fin del foreach de informes
	?>
	</tbody>
</table>