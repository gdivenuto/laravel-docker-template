<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionReport o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 	Array asociativo que contiene todos los parámetros de la vista para ser
*/
// Resultado completo de Solicitudes
$solicitudes = $this->vista->data['resultados'];
?>
<!-- Aquí comienza a mostrarse el resultado completo -->
<table class="pdf_tabla_cuerpo_reporte pdf_tabla_borde">
	<thead>
		<tr>
			<th class="pdf_prestamos_identificador">EXPEDIENTE</th>
			<th class="pdf_prestamos_fecha">FECHA SOLICITUD HCD</th>
			<th class="pdf_prestamos_fecha">FECHA SOLICITUD E.E.</th>
			<th class="pdf_prestamos_fecha">FECHA INGRESO HCD</th>
			<th class="pdf_prestamos_fecha">FECHA DEVOLUCI&Oacute;N E.E.</th>
			<th class="pdf_prestamos_fecha">FECHA ANULADO</th>
			<th class="pdf_prestamos_estado">ESTADO</th>
			<th class="pdf_prestamos_observaciones">OBSERVACIONES</th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ($solicitudes as $s) {
	?>
		<tr>
			<td class="pdf_solicitudes_identificador">
				<?php echo $s->toStringDescription(); ?>
			</td>
			<td class="pdf_prestamos_fecha">
				<?php echo Validator::get()->convertirAFechaVista($s->fecha_solicitud_hcd); ?>
			</td>
			<td class="pdf_prestamos_fecha">
				<?php echo ($s->fecha_solicitud_ee != '') ? Validator::get()->convertirAFechaVista($s->fecha_solicitud_ee) : '&nbsp;'; ?>
			</td>
			<td class="pdf_prestamos_fecha">
				<?php echo ($s->fecha_ingresado_ee != '') ? Validator::get()->convertirAFechaVista($s->fecha_ingresado_ee) : '&nbsp;'; ?>
			</td>
			<td class="pdf_prestamos_fecha">
				<?php echo ($s->fecha_devuelto_ee != '') ? Validator::get()->convertirAFechaVista($s->fecha_devuelto_ee) : '&nbsp;'; ?>
			</td>
			<td class="pdf_prestamos_fecha">
				<?php echo ($s->fecha_anulado_ee != '') ? Validator::get()->convertirAFechaVista($s->fecha_anulado_ee) : '&nbsp;'; ?>
			</td>
			<td class="pdf_solicitudes_estado">
				<?php echo $s->estadoToString(); ?>
			</td>
			<td class="pdf_solicitudes_observaciones">
				<?php echo ($s->observaciones != '') ? $s->obtenerResumenObservacion() : '&nbsp;'; ?>
			</td>
		</tr>
	<?php
	} // fin del foreach de Solicitudes
	?>
	</tbody>
</table>