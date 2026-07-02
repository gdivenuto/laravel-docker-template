<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionReport o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 				Array asociativo que contiene todos los parámetros de la vista para ser
*/
// Resultado completo de expedientes
$expedientes = $this->vista->data['resultados'];
?>
<!-- Aquí comienza a mostrarse el resultado completo -->
<table class="pdf_tabla_cuerpo_reporte">
	<tbody>
		<tr>
			<td class="pdf_ficha_expediente pdf_texto_gris">
				<table>
					<tr>
						<td class="listados_pdf_ficha_clave_expediente"> A&ntilde;o&nbsp;Tipo&nbsp;Nro.&nbsp;Cpo.&nbsp;Alc.</td>
						<td class="listados_pdf_ficha_fecha_ingreso_expediente">Fecha de Entrada</td>
					</tr>
				</table>
			</td>
		</tr>
	<?php
	foreach ($expedientes as $e) {
	?>
		<tr>
			<td class="pdf_ficha_expediente">
				<table>
					<tr>
						<td class="pdf_detalle_giros_ficha_titulo">
							<strong><?php echo sprintf('%d&nbsp;%s&nbsp;%d&nbsp;%d&nbsp;%d', $e->anio, $e->tipo, $e->numero, $e->cuerpo, $e->alcance); ?></strong>
						</td>
						<td class="pdf_detalle_giros_ficha_valor">&nbsp;</td>
						<td class="pdf_detalle_giros_ficha_fechas">
							<strong><?php echo Validator::get()->convertirAFechaVista($e->fecha_entrada_expe); ?></strong>
						</td>
					</tr>
					<tr>
						<td class="pdf_detalle_giros_ficha_titulo">Car&aacute;tula</td>
						<td class="pdf_detalle_giros_ficha_valor"><?php echo $e->caratula; ?></td>
						<td class="pdf_detalle_giros_ficha_fechas">&nbsp;</td>
					</tr>
					<tr>
						<td class="pdf_detalle_giros_ficha_titulo">Iniciador</td>
						<td class="pdf_detalle_giros_ficha_valor"><?php echo $e->ro_iniciador_descripcion_grp; ?></td>
						<td class="pdf_detalle_giros_ficha_fechas">&nbsp;</td>
					</tr>
					<tr>
						<td class="pdf_detalle_giros_ficha_titulo">Categor&iacute;a</td>
						<td class="pdf_detalle_giros_ficha_valor"><?php echo $e->ro_descripcion_categoria; ?></td>
						<td class="pdf_detalle_giros_ficha_fechas">&nbsp;</td>
					</tr>
					<?php foreach ($e->proyectos as $p) { ?>
						<tr>
							<td class="pdf_detalle_giros_ficha_titulo">Proyecto de </td>
							<td colspan="2" class="pdf_detalle_giros_ficha_valor"><?php echo $p->ro_descripcion_proyecto; ?></td>
						</tr>
						<tr>
							<td colspan="3" class="pdf_detalle_giros_ficha_valor"><?php echo $p->extracto; ?></td>
						</tr>
					<?php } ?>
				</table>
			</td>
		</tr>
	<?php
	} // fin del foreach de expedientes
	?>
	</tbody>
</table>