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

foreach ($expedientes as $e) {
	// Se define su título según su tipo
	if ($e->tipo == 'E') {
		$nombre_tipo = 'Expediente: ';
	}

	if ($e->tipo == 'N') {
		$nombre_tipo = 'Nota: ';
	}

	if ($e->tipo == 'R') {
		$nombre_tipo = 'Recomendaci&oacute;n: ';
	}
	?>
	<div class="pdf_avanzada_info_expediente">
		<div class="pdf_avanzada_ficha_titulo"><strong><?php echo $nombre_tipo; ?></strong></div>
		<div class="pdf_avanzada_ficha_valor">
			<strong><?php echo sprintf('%d&nbsp;-&nbsp;%s&nbsp;-&nbsp;%d&nbsp;-&nbsp;%d&nbsp;-&nbsp;%d', $e->anio, $e->tipo, $e->numero, $e->cuerpo, $e->alcance); ?></strong>
		</div>
	</div>
	<div class="pdf_avanzada_info_expediente">
		<div class="pdf_avanzada_ficha_titulo"><strong>Fecha Ingreso:</strong></div>
		<div class="pdf_avanzada_ficha_valor"><?php echo Validator::get()->convertirAFechaVista($e->fecha_entrada_expe); ?></div>
	</div>
	<div class="pdf_avanzada_info_expediente">
		<div class="pdf_avanzada_ficha_titulo"><strong>Car&aacute;tula:</strong></div>
		<div class="pdf_avanzada_ficha_valor"><?php echo ($e->caratula != '') ? FormatText::reemplazarPorHTML($e->caratula) : 'no posee'; ?></div>
	</div>
	<div class="pdf_avanzada_info_expediente">
		<div class="pdf_avanzada_ficha_titulo"><strong>Iniciador:</strong></div>
		<div class="pdf_avanzada_ficha_valor"><?php echo FormatText::reemplazarPorHTML($e->ro_iniciador_descripcion_grp); ?></div>
	</div>
	<div class="pdf_avanzada_info_expediente">
		<div class="pdf_avanzada_ficha_titulo"><strong>Categor&iacute;a:</strong></div>
		<div class="pdf_avanzada_ficha_valor"><?php echo FormatText::reemplazarPorHTML($e->ro_descripcion_categoria); ?></div>
	</div>
	<div class="pdf_avanzada_info_expediente">
		<div class="pdf_avanzada_ficha_titulo"><strong>Temas:</strong></div>
		<div class="pdf_avanzada_ficha_valor"> <?php foreach ($e->temas as $t) {echo FormatText::reemplazarPorHTML($t->ro_descripcion_tema) . ' <br>';}?></div>
	</div>
	<div class="pdf_avanzada_info_expediente">
		<div class="pdf_avanzada_ficha_titulo"><strong>Autores:</strong></div>
		<div class="pdf_avanzada_ficha_valor"><?php foreach ($e->autores as $a) {echo FormatText::reemplazarPorHTML($a->ro_descripcion_grp) . ' <br>';}?></div>
	</div>
<?php foreach ($e->proyectos as $p) {?>
	<div class="pdf_avanzada_info_expediente">
		<div class="pdf_avanzada_ficha_titulo"><strong>Proyecto de</strong></div>
		<div class="pdf_avanzada_ficha_valor"><?php echo FormatText::reemplazarPorHTML($p->ro_descripcion_proyecto); ?></div>
	</div>
	<div class="pdf_avanzada_info_expediente">
		<div valign="top" class="pdf_avanzada_ficha_titulo"><strong>Extracto:</strong></div>
		<div class="pdf_avanzada_ficha_valor"><?php echo ($p->extracto != '') ? FormatText::reemplazarPorHTML($p->extracto) : ''; ?></div>
	</div>
<?php }?>
	<div class="pdf_avanzada_info_expediente">
		<div class="pdf_avanzada_ficha_titulo"><strong>Estado:</strong></div>
		<div class="pdf_avanzada_ficha_valor">
<?php
$estado_actual = $e->estados[count($e->estados) - 1]; // Se muestra el estado_actual
	$fecha_estado_actual = Validator::get()->convertirAFechaVista($estado_actual->fecha_estado);

	echo FormatText::reemplazarPorHTML($estado_actual->ro_nombre_estado) . '&nbsp;&nbsp;&nbsp;&nbsp;<strong>Desde:</strong> ' . $fecha_estado_actual;
	?>
		</div>
	</div>
<?php
// Si posee un Estado
	if ($estado_actual != null) {
		// Si este estado requiere tratamiento en Comisión
		if ($estado_actual->ro_tratamiento_comision == 1) {
			$posicion = count($e->giros) - 1; // Empieza en la última Comisión
			$encontrado = false;

			// Mientras no se encuentre la comisión vigente
			while ($encontrado === false && $posicion >= 0) {
				// Si la comisión posee fecha de entrada y NO posee fecha de salida
				if ($e->giros[$posicion]->fecha_entrada_giro != null && $e->giros[$posicion]->fecha_salida_giro === null) {
					$comision_actual = $e->giros[$posicion]; // Devuelve la comision para mostrar su información
					break; // dejamos de buscar
				} else {
					// Se actualiza la posición para volver a corroborar con la comisión anterior
					$posicion--;
				}
			}
			// Si NO se encontró una Comisión con fecha de entrada
			if ($encontrado === false) {
				// No hay Comisión que mostrar
				$comision_actual = null;
			}

			// Si se encuentra en Comisión se muestra
			if ($comision_actual != null) {?>
			<div class="pdf_avanzada_info_expediente">
				<div class="pdf_avanzada_ficha_titulo"><strong>Comisi&oacute;n:</strong></div>
				<div class="pdf_avanzada_ficha_valor">
					<?php echo $comision_actual->comision_codigo . ' - ' . FormatText::reemplazarPorHTML($comision_actual->ro_descripcion_grp) . '&nbsp;&nbsp;&nbsp;&nbsp;<strong>Desde:</strong> ' . Validator::get()->convertirAFechaVista($comision_actual->fecha_entrada_giro); ?>
				</div>
			</div>
		<?php }
		}
	}
	echo '<br><hr class="pdf_avanzada_linea">';
}
?>