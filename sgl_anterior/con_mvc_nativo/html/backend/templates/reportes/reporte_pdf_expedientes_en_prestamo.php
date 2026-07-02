<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionReport o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data Array asociativo que contiene todos los parámetros de la vista para ser utilizados en la plantilla.
 */
// Resultado completo de expedientes
$expedientes = $this->vista->data['resultados'];
?>
<!-- Aquí comienza a mostrarse el resultado completo -->
<table class="pdf_tabla_cuerpo_reporte">
	<tbody>
<?php
foreach ($expedientes as $e) {
	if ($e->tipo == 'E') $titulo_segun_tipo = 'Expediente: ';
	if ($e->tipo == 'N') $titulo_segun_tipo = 'Nota: ';
	if ($e->tipo == 'R') $titulo_segun_tipo = 'Recomendaci&oacute;n: ';
?>
		<tr>
			<td class="pdf_ficha_expediente">
				<!-- Título según su Tipo + Número + Código del Iniciador + Año corto + Iniciador + Carátula + Fecha de entrada -->
				<table>
					<tr>
						<td class="pdf_exped_en_prestamo_ficha_clave_expediente">
							<strong><?php echo sprintf('%s&nbsp;%d&nbsp;%s&nbsp;%d&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;&nbsp;%s', $titulo_segun_tipo, $e->numero, $e->iniciador_codigo, substr($e->anio, -2), $e->ro_iniciador_descripcion_grp, $e->caratula); ?></strong>
						</td>
						<td class="pdf_exped_en_prestamo_ficha_fecha_ingreso_expediente">
							<strong><?php echo sprintf('%s', Validator::get()->convertirAFechaVista($e->fecha_entrada_expe)); ?></strong>
						</td>
					</tr>
				</table>
				<!-- Extracto -->
				<table>
					<?php
					foreach ($e->proyectos as $p)
						if ( !is_null($p->extracto) && $p->extracto != '' )
							echo '<tr><td class="listados_pdf_ficha_info_extracto_e_informes">'.$p->extracto.'</td></tr>';
					?>
				</table>
				<!-- Estado y su observación -->
				<table>
					<?php
					// Se toma el Estado actual
					$estado_actual = $e->estados[count($e->estados)-1];
					if ( !is_null($estado_actual) ) {
						echo '<tr>';
							echo '<td class="pdf_exped_en_prestamo_ficha_titulo">Estado:</td>';
							echo '<td class="pdf_exped_en_prestamo_ficha_valor">'.$estado_actual->ro_nombre_estado.'</td>';
							echo '<td class="pdf_exped_en_prestamo_ficha_fechas">';
							echo (!is_null($estado_actual->fecha_estado)) ? 'Desde el '.Validator::get()->convertirAFechaVista($estado_actual->fecha_estado) : '&nbsp;';
							echo '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td class="pdf_exped_en_prestamo_ficha_titulo">Observaciones</td>';
							echo '<td class="pdf_exped_en_prestamo_ficha_valor">'.$estado_actual->observaciones_estado.'</td>';
						echo '</tr>';
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