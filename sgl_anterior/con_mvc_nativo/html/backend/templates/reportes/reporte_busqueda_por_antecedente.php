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
<?php
foreach ($expedientes as $e) {
	// Se define su título según su tipo
	if ($e->tipo == 'E') $nombre_tipo = 'Expediente: ';
	if ($e->tipo == 'N') $nombre_tipo = 'Nota: ';
	if ($e->tipo == 'R') $nombre_tipo = 'Recomendaci&oacute;n: ';
?>
		<tr>
			<td class="pdf_ficha_expediente">
				<table>
					<tr>
						<td id="pdf_ficha_clave_expediente"><strong><?php echo sprintf('%s&nbsp;%d&nbsp;-&nbsp;%s&nbsp;-&nbsp;%d&nbsp;-&nbsp;%d&nbsp;-&nbsp;%d', $nombre_tipo, $e->anio, $e->tipo, $e->numero, $e->cuerpo, $e->alcance); ?></strong></td>
						<td id="pdf_ficha_fecha_ingreso_expediente"><strong>Fecha Ingreso:</strong> <?php echo Validator::get()->convertirAFechaVista($e->fecha_entrada_expe); ?></td>
					</tr>
				</table>
				<table>
					<tr>
						<td class="pdf_ficha_titulo">&nbsp;&nbsp;<strong>Car&aacute;tula:</strong></td>
						<td class="pdf_ficha_valor"><?php echo ($e->caratula != '') ? $e->caratula : 'no posee'; ?></td>
					</tr>
					<tr>
						<td class="pdf_ficha_titulo">&nbsp;&nbsp;<strong>Iniciador:</strong></td>
						<td class="pdf_ficha_valor"><?php echo $e->ro_iniciador_descripcion_grp; ?></td>
					</tr>
					<tr>
						<td class="pdf_ficha_titulo">&nbsp;&nbsp;<strong>Categor&iacute;a:</strong></td>
						<td class="pdf_ficha_valor"><?php echo $e->ro_descripcion_categoria; ?></td>
					</tr>
					<tr>
						<td class="pdf_ficha_titulo">&nbsp;&nbsp;<strong>Temas:</strong></td>
						<td class="pdf_ficha_valor">
							<?php
							foreach ($e->temas as $t)
								echo $t->ro_descripcion_tema.' <br>';
							?>
						</td>
					</tr>
					<tr>
						<td class="pdf_ficha_titulo">&nbsp;&nbsp;<strong>Autores:</strong></td>
						<td class="pdf_ficha_valor">
							<?php
							foreach ($e->autores as $a)
								echo $a->ro_descripcion_grp.' <br>';
							?>
						</td>
					</tr>
				<?php
				foreach ($e->proyectos as $p) {
				?>
					<tr>
						<td class="pdf_ficha_titulo">&nbsp;&nbsp;<strong>Proyecto de</strong></td>
						<td class="pdf_ficha_valor"><?php echo $p->ro_descripcion_proyecto; ?></td>
					</tr>
					<tr>
						<td valign="top" class="pdf_ficha_titulo">&nbsp;&nbsp;<strong>Extracto:</strong></td>
						<td class="pdf_ficha_valor"><?php echo ($p->extracto != '') ? $p->extracto : 'no posee'; ?></td>
					</tr>
				<?php
				}
				?>
					<tr>
						<td class="pdf_ficha_titulo">&nbsp;&nbsp;<strong>Estado:</strong></td>
						<td class="pdf_ficha_valor">
							<?php
							$estado_actual = $e->estados[count($e->estados)-1]; // Se muestra el estado_actual 
							$fecha_estado_actual = Validator::get()->convertirAFechaVista($estado_actual->fecha_estado);
							echo $estado_actual->ro_nombre_estado.'&nbsp;&nbsp;&nbsp;&nbsp;<strong>Desde</strong> '.$fecha_estado_actual;
							?>
						</td>
					</tr>
				<?php
				// Si posee un Estado
				if ($estado_actual != null) {
					// Si este estado requiere tratamiento en Comisión
					if ($estado_actual->ro_tratamiento_comision == 1) {
						// La buscamos y se muestra la última Comisión
						if ($e->giros[count($e->giros)] > 0) {
							$comision_actual = $e->giros[$e->giros[count($e->giros)-1]];
				?>
							<tr>
								<td class="pdf_ficha_titulo">&nbsp;&nbsp;<strong>Comisi&oacute;n:</strong></td>
								<td class="pdf_ficha_valor">
									<?php echo $comision_actual->comision_codigo.' - '.$comision_actual->ro_descripcion_grp.'&nbsp;&nbsp;&nbsp;&nbsp;<strong>Desde</strong> '.Validator::get()->convertirAFechaVista($comision_actual->fecha_entrada_giro); ?>
								</td>
							</tr>
				<?php
						}
					}
				}
				?>
					<tr>
						<td class="pdf_ficha_titulo">&nbsp;&nbsp;<strong>Anteced.:</strong></td>
						<td class="pdf_ficha_valor">
							<?php echo $e->antecedentes[0]->tipo_a.'&nbsp;&nbsp;'.$e->antecedentes[0]->numero_a.'&nbsp;&nbsp;'.$e->antecedentes[0]->digito_a.'&nbsp;&nbsp;'.$e->antecedentes[0]->anio_a.'&nbsp;&nbsp;'.$e->antecedentes[0]->cuerpo_a.'&nbsp;&nbsp;'.$e->antecedentes[0]->alcance_a; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
<?php
}
?>
	</tbody>
</table>