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
	if ($e->tipo == 'E') $titulo_segun_tipo = 'Expediente: ';
	if ($e->tipo == 'N') $titulo_segun_tipo = 'Nota: ';
	if ($e->tipo == 'R') $titulo_segun_tipo = 'Recomendaci&oacute;n: ';
?>
		<tr>
			<td class="pdf_ficha_expediente">
				<table>
					<tr>
						<td class="listados_pdf_ficha_clave_expediente">
							<strong>
							<?php
							// Si el iniciador es un Concejal
					        if ( $e->iniciador_codigo == 'CJA' ) {
					        	foreach ($e->autores as $autor) {
									if ( !is_null($autor->ro_descripcion_grp) && $autor->ro_descripcion_grp != '' ) {
										$nombre_iniciador = $autor->ro_descripcion_grp;// Se muestra su descripción
										break;// Se sale del foreach de autores
									}
								}
							} else
					            // sino, se muestra la descripción del iniciador
					            $nombre_iniciador = $e->ro_iniciador_descripcion_grp;

							echo sprintf('%s&nbsp;%d&nbsp;%s&nbsp;%d&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;&nbsp;%s', $titulo_segun_tipo, $e->numero, $e->iniciador_codigo, substr($e->anio, -2), $nombre_iniciador, $e->caratula);
							?>
							</strong>
						</td>
						<td class="listados_pdf_ficha_fecha_ingreso_expediente">
							<strong><?php echo sprintf('%s', Validator::get()->convertirAFechaVista($e->fecha_entrada_expe)); ?></strong>
						</td>
					</tr>
				</table>
				<table>
<?php
	foreach ($e->proyectos as $p)
		if ( !is_null($p->extracto) && $p->extracto != '' )
				echo '<tr><td class="listados_pdf_ficha_info_extracto_e_informes">'.$p->extracto.'</td></tr>';
?>
				</table>
			</td>
		</tr>
<?php
} // fin del foreach de expedientes
?>
	</tbody>
</table>