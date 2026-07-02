<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionReport o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 *
 *  $this->vista->data 				Array asociativo que contiene todos los parámetros de la vista para ser
 */
// Resultado completo de participaciones
$participaciones = $this->vista->data['resultados'];
//Logger::get()->Log("participaciones_informe", $participaciones, false);

if ($participaciones[0]->tipo == 'E') {
	$documento = 'el Expediente';
} elseif ($participaciones->tipo == 'N') {
	$documento = 'la Nota';
} elseif ($participaciones->tipo == 'R') {
	$documento = 'la Recomendaci&oacute;n';
}
?>
<table class="pdf_tabla_cuerpo_reporte">
	<tbody>
		<tr>
			<td class="pdf_ficha_expediente pdf_texto_gris">
				<table>
					<tr>
						<td class="listados_pdf_ficha_clave_expediente">
							Participaciones en <?=$documento;?>&nbsp;
							<strong><?=sprintf('%d&nbsp;%s&nbsp;%d&nbsp;%d&nbsp;%d', $participaciones[0]->anio, $participaciones[0]->tipo, $participaciones[0]->numero, $participaciones[0]->cuerpo, $participaciones[0]->alcance);?></strong>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="pdf_ficha_expediente">
				<table>
					<tr>
						<td><strong>Nro</strong></td>
						<td><strong>Fecha</strong></td>
						<td><strong>Nombre</strong></td>
						<td><strong>Documento</strong></td>
						<td><strong>Domicilio</strong></td>
						<td><strong>Tel&eacute;fono</strong></td>
						<td><strong>Mail</strong></td>
					</tr>
					<?php foreach ($participaciones as $pa) {?>
						<tr><td colspan="7"><hr></td></tr>
						<tr>
							<td style="width:20px;"><?=$pa->numero_participacion;?></td>
							<td style="width:60px;"><?=Validator::get()->convertirAFechaVista($pa->fecha);?></td>
							<td style="width:150px;"><?=$pa->apellidoynombre;?></td>
							<td style="width:80px;"><?=$pa->tipodoc . ':&nbsp;' . $pa->nrodoc;?></td>
							<td style="width:150px;"><?=$pa->domicilio;?></td>
							<td style="width:80px;"><?=$pa->telefono;?></td>
							<td style="width:150px;"><?=$pa->mail;?></td>
						</tr>
						<?php if (isset($pa->institucion_nombre) && $pa->institucion_nombre != null) {?>
							<tr>
								<td colspan="7">Instituci&oacute;n: <?=$pa->institucion_nombre;?> - <?=$pa->institucion_domicilio;?></td>
							</tr>
						<?php }?>
						<tr>
							<td colspan="7" style="padding-top:10px;">
								<strong>Texto:</strong> <?=html_entity_decode($pa->texto);?>
							</td>
						</tr>
					<?php }?>
				</table>
			</td>
		</tr>
	</tbody>
</table>