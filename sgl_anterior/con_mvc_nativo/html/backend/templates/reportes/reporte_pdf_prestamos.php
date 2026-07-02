<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionReport o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 	Array asociativo que contiene todos los parámetros de la vista para ser
*/
// Resultado completo de Préstamos
$prestamos = $this->vista->data['resultados'];
?>
<!-- Aquí comienza a mostrarse el resultado completo -->
<table class="pdf_tabla_cuerpo_reporte pdf_tabla_borde">
	<thead>
		<tr>
			<th class="pdf_prestamos_identificador">EXPEDIENTE</th>
			<th class="pdf_prestamos_solicitante">SOLICITANTE</th>
			<th class="pdf_prestamos_fecha">FECHA SOLICITUD</th>
			<th class="pdf_prestamos_fecha">FECHA PR&Eacute;STAMO</th>
			<th class="pdf_prestamos_fecha">FECHA DEVOLUCI&Oacute;N</th>
			<th class="pdf_prestamos_fecha">FECHA ANULADO</th>
			<th class="pdf_prestamos_estado">ESTADO</th>
			<th class="pdf_prestamos_libro">NRO.</th>
			<th class="pdf_prestamos_libro">FOLIO</th>
			<th class="pdf_prestamos_observaciones">OBSERVACIONES</th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ($prestamos as $p) {
	?>
		<tr>
			<td class="pdf_prestamos_identificador">
				<?php echo $p->toStringDescription(); ?>
			</td>
			<td class="pdf_prestamos_estado">
				<?php echo $p->ro_solicitante_nombre; ?>
			</td>
			<td class="pdf_prestamos_fecha">
				<?php echo Validator::get()->convertirAFechaVista($p->fecha_solicitud); ?>
			</td>
			<td class="pdf_prestamos_fecha">
				<?php echo ($p->fecha_prestado != '') ? Validator::get()->convertirAFechaVista($p->fecha_prestado) : '&nbsp;'; ?>
			</td>
			<td class="pdf_prestamos_fecha">
				<?php echo ($p->fecha_devuelto != '') ? Validator::get()->convertirAFechaVista($p->fecha_devuelto) : '&nbsp;'; ?>
			</td>
			<td class="pdf_prestamos_fecha">
				<?php echo ($p->fecha_anulado != '') ? Validator::get()->convertirAFechaVista($p->fecha_anulado) : '&nbsp;'; ?>
			</td>
			<td class="pdf_prestamos_estado">
				<?php echo $p->estadoToString(); ?>
			</td>
			<td class="pdf_prestamos_libro">
				<?php echo ($p->libro_numero != '') ? $p->libro_numero : '&nbsp;'; ?>
			</td>
			<td class="pdf_prestamos_libro">
				<?php echo ($p->libro_folio != '') ? $p->libro_folio : '&nbsp;'; ?>
			</td>
			<td class="pdf_prestamos_observaciones">
				<?php echo ($p->observaciones_prestamo != '') ? $p->obtenerResumenObservacion() : '&nbsp;'; ?>
			</td>
		</tr>
	<?php
	} // fin del foreach de Préstamos
	?>
	</tbody>
</table>