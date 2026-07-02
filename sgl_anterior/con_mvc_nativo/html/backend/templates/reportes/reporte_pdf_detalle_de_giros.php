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

/**
 * 13/01/2022 XXXX
 * Se toma la Comisión actual
 * No debe ser la última, sino la que NO posea fecha de salida
 *
 * @param  [type] $giros [description]
 * @return [type]        [description]
 */
function obtenerComisionActual($giros) {
	$posicion = count($giros)-1;// Empieza en la última Comisión
    $encontrado = false;

    // Mientras no se encuentre la comisión vigente
    while ($encontrado === false && $posicion >= 0) {
        // Si la comisión posee fecha de entrada y NO posee fecha de salida
        if ($giros[$posicion]->fecha_entrada_giro != null && $giros[$posicion]->fecha_salida_giro === null){
            return $giros[$posicion]; // Devuelve la comision para mostrar su información
        } else {
        	// Se actualiza la posición para volver a corroborar con la comisión anterior
            $posicion--;
        }
    }
    if ($encontrado === false) // Si NO se encontró una Comisión con fecha de entrada
        return null; // No hay Comisión que mostrar
}

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
						<td class="listados_pdf_ficha_clave_expediente"> A&ntilde;o Tipo Nro. Cpo. Alc.</td>
						<td class="listados_pdf_ficha_fecha_ingreso_expediente">Fecha de Entrada</td>
					</tr>
				</table>
			</td>
		</tr>
<?php
$cantidad_expedientes = 0;
$cantidad_notas = 0;
foreach ($expedientes as $e) {
	// Se define su título según su tipo
	if ($e->tipo == 'E') $cantidad_expedientes++;
	if ($e->tipo == 'N') $cantidad_notas++;
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
					<tr>
						<td class="pdf_detalle_giros_ficha_titulo">Temas</td>
						<td class="pdf_detalle_giros_ficha_valor">
							<?php
							foreach ($e->temas as $t)
								echo $t->ro_descripcion_tema.'<br>';
							?>
						</td>
						<td class="pdf_detalle_giros_ficha_fechas">&nbsp;</td>
					</tr>
					<tr>
						<td class="pdf_detalle_giros_ficha_titulo">Autores</td>
						<td class="pdf_detalle_giros_ficha_valor">
							<?php
							foreach ($e->autores as $a)
								echo $a->ro_descripcion_grp.'<br>';
							?>
						</td>
						<td class="pdf_detalle_giros_ficha_fechas">&nbsp;</td>
					</tr>
					<?php
					foreach ($e->proyectos as $p) {
						echo '<tr>';
							echo '<td class="pdf_detalle_giros_ficha_titulo">Proyecto de </td>';
							echo '<td class="pdf_detalle_giros_ficha_valor">'.$p->ro_descripcion_proyecto.'</td>';
							echo '<td class="pdf_detalle_giros_ficha_fechas">&nbsp;</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<td class="pdf_detalle_giros_ficha_titulo pdf_texto_arriba">Extracto</td>';
							echo '<td class="pdf_detalle_giros_ficha_valor">';
								echo (!is_null($p->extracto)) ? $p->extracto : 'Sin extracto';
							echo '</td>';
							echo '<td class="pdf_detalle_giros_ficha_fechas">&nbsp;</td>';
						echo '</tr>';
					}
					// Se toma el Estado actual
					$estado_actual = $e->estados[count($e->estados)-1];
					if ( !is_null($estado_actual) ) {
						echo '<tr>';
							echo '<td class="pdf_detalle_giros_ficha_titulo">Estado</td>';
							echo '<td class="pdf_detalle_giros_ficha_valor">'.$estado_actual->ro_nombre_estado.'</td>';
							echo '<td class="pdf_detalle_giros_ficha_fechas">';
							echo (!is_null($estado_actual->fecha_estado)) ? 'Desde el '.Validator::get()->convertirAFechaVista($estado_actual->fecha_estado) : '&nbsp;';
							echo '</td>';
						echo '</tr>';
					}
					// Se toma la Comisión actual
					$comision_actual = obtenerComisionActual($e->giros);
					if ( !is_null($comision_actual) ) {
						echo '<tr>';
							echo '<td class="pdf_detalle_giros_ficha_titulo">Comisi&oacute;n</td>';
							echo '<td class="pdf_detalle_giros_ficha_valor">'.$comision_actual->ro_descripcion_grp.'</td>';
							echo '<td class="pdf_detalle_giros_ficha_fechas">';
							echo (!is_null($comision_actual->fecha_entrada_giro)) ? 'Desde el '.Validator::get()->convertirAFechaVista($comision_actual->fecha_entrada_giro) : '&nbsp;';
							echo '</td>';
						echo '</tr>';
					}
					// Se muestran los giros a cada comisión
					foreach ($e->giros as $g) {
						echo '<tr>';
							echo '<td class="pdf_detalle_giros_ficha_informacion_giro" colspan="3">';
								echo $g->comision_codigo.'&nbsp;&nbsp;';
								echo $g->ro_descripcion_grp.'&nbsp;&nbsp;';
								echo Validator::get()->convertirAFechaVista($g->fecha_entrada_giro);
								echo ( !is_null($g->fecha_salida_giro) ) ? '&nbsp;&nbsp;'.Validator::get()->convertirAFechaVista($g->fecha_entrada_giro) : '';
								echo ( !is_null($g->dictamen_giro) ) ? '&nbsp;&nbsp;'.$g->dictamen_giro : '';
							echo '</td>';
						echo '</tr>';
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
