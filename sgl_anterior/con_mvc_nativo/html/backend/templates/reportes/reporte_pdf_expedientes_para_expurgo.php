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
						<td class="listados_pdf_ficha_clave_expediente"> A&ntilde;o Tipo Nro. Cpo. Alc. Car&aacute;tula</td>
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
						<td class="listados_pdf_ficha_clave_expediente">
							<strong><?php echo sprintf('%d&nbsp;%s&nbsp;%d&nbsp;%d&nbsp;%d&nbsp;&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;&nbsp;%s', $e->anio, $e->tipo, $e->numero, $e->cuerpo, $e->alcance, $e->iniciador_codigo, $e->caratula); ?></strong>
						</td>
						<td class="listados_pdf_ficha_fecha_ingreso_expediente">
							<strong><?php echo sprintf('%s', Validator::get()->convertirAFechaVista($e->fecha_entrada_expe)); ?></strong>
						</td>
					</tr>
				</table>
				<table>
<?php
	foreach ($e->proyectos as $p) {
		
		echo '<tr><td class="listados_pdf_ficha_info_extracto_e_informes">';
		echo $p->orden_proyecto.'&nbsp;'.$p->ro_descripcion_proyecto;
		
		echo '&nbsp;&nbsp;&nbsp;<b>Promulgado:</b>';
		// Si posee Fecha y Número de Promulgación, se muestran
		echo ( !empty($p->ro_fecha_promulga) && !empty($p->ro_numero_promulga) ) ? Validator::get()->convertirAFechaVista($p->ro_fecha_promulga).'&nbsp;&nbsp;&nbsp;'.$p->ro_numero_promulga : '---';
		
		echo '&nbsp;&nbsp;&nbsp;<b>Decretado:</b>';
		// Si posee Decreto de Promulgación, se muestra
		echo ( !empty($p->ro_decreto_promulga) ) ? $p->ro_decreto_promulga : '---';
		
		echo '&nbsp;&nbsp;&nbsp;<b>Sancionado:</b>';
		// Si posee Fecha y Número de Sanción, se muestran
		echo Validator::get()->convertirAFechaVista($p->ro_fecha_sancion).'&nbsp;'.$p->ro_numero_sancion;
		//echo ( !empty($p->ro_fecha_sancion) && !empty($p->ro_numero_sancion) ) ? Validator::get()->convertirAFechaVista($p->ro_fecha_sancion).'&nbsp;'.$p->ro_numero_sancion : '---';
		echo '</td></tr>';

		if ( !is_null($p->extracto) && $p->extracto != '' )
				echo '<tr><td class="listados_pdf_ficha_info_extracto_e_informes">'.$p->extracto.'</td></tr>';
	}
?>
				</table>
			</td>
		</tr>
<?php
} // fin del foreach de expedientes
?>
	</tbody>
</table>