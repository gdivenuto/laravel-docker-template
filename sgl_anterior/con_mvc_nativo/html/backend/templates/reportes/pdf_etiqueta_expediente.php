<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionReport o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 *
 *  $this->vista->data 	Array asociativo que contiene todos los parámetros de la vista para ser utilizados en la plantilla.
 */
// Información del expediente
$e = $this->vista->data['info_expediente'];

if ($e->tipo == 'E') $nombre_segun_tipo = 'Expediente';
if ($e->tipo == 'N') $nombre_segun_tipo = 'Nota';
if ($e->tipo == 'R') $nombre_segun_tipo = 'Recomendaci&oacute;n';
?>
<div class="etiqueta_general">
	<!-- Clave y Fecha de Ingreso -->
	<div class="etiqueta_fila">
		<span class="etiqueta_subrayado"><?php echo $nombre_segun_tipo.' N&deg;';?></span>
		<strong><?= sprintf('%d-%s-%d', $e->numero, $e->iniciador_codigo, $e->anio);?></strong>
		<?php
		// 01/12/2020 XXXX
		// Ahora se muestra el Cuerpo y el Alcance, si alguno de ellos es mayor a cero
		if ($e->cuerpo > 0 || $e->alcance > 0) {
			echo '&nbsp;&nbsp;<span class="etiqueta_subrayado">Cpo.</span>&nbsp;<strong>'.$e->cuerpo.'</strong>&nbsp;';
			echo '&nbsp;<span class="etiqueta_subrayado">Alc.</span>&nbsp;<strong>'.$e->alcance.'</strong>&nbsp;';

			$espacio = ($e->tipo == 'N') ? '50px' : '10px';
			// Se abrevia para que quepen en la misma fila
			echo '<span class="etiqueta_subrayado" style="margin-left:'.$espacio.'">F. Ingreso</span>';
		} else {
			$espacio = ($e->tipo == 'N') ? '120px' : '70px';
			echo '<span class="etiqueta_subrayado" style="margin-left:'.$espacio.'">Fecha Ingreso</span>';
		}
		?>
		<strong><?php echo Validator::get()->convertirAFechaVista($e->fecha_entrada_expe); ?></strong>
	</div>

	<!-- Carátula -->
	<div class="etiqueta_fila"><strong><?php echo $e->caratula; ?></strong></div>

	<!-- Iniciador -->
	<div class="etiqueta_fila"><span class="etiqueta_subrayado">Iniciador</span>:&nbsp;&nbsp;&nbsp;<?php echo $e->iniciador_codigo.'&nbsp;'.$e->ro_iniciador_descripcion_grp; ?></div>

	<!-- Autores -->
	<div class="etiqueta_fila"><span class="etiqueta_subrayado">Autores</span>:&nbsp;&nbsp;&nbsp;
		<?php
		foreach ($e->autores as $a) {
			//$separador_autores = (count($e->autores) > 1) ? '; ' : '';
			$separador_autores = (count($e->autores) > 1) ? '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '';
			echo $a->autor_codigo.'&nbsp;'.$a->ro_descripcion_grp.$separador_autores;
		}
		?>
	</div>

	<!-- Categoría -->
	<div class="etiqueta_fila"><span class="etiqueta_subrayado">Categor&iacute;a</span>:&nbsp;<?php echo $e->id_codcategoria.'&nbsp;'.$e->ro_descripcion_categoria; ?></div>

	<?php
	// Por cada Proyecto
	foreach ($e->proyectos as $p) {
	?>
		<div class="etiqueta_fila"><span class="etiqueta_subrayado">Proyecto</span> <?php echo '<strong>'.$p->orden_proyecto.'&nbsp;'.$p->ro_descripcion_proyecto.'</strong>'; ?></div>
		<div class="etiqueta_fila" style="text-align: justify;"><strong><?php echo $p->extracto; ?></strong></div>
	<?php
	}
	?>
	<br>
	<div class="etiqueta_fila"><span class="etiqueta_subrayado">Antecedentes</span></div>
	<?php if ( count($e->antecedentes) > 0 ) { ?>
		<div class="etiqueta_fila etiqueta_fila_antecedente">
			<span>N&uacute;mero|</span>
			<span>Tipo|</span>
			<span>A&ntilde;o&nbsp;&nbsp;&nbsp;|</span>
			<span>Dig.|</span>
			<span>Cpo.|</span>
			<span>Alc.|</span>
			<span>Cpo.Alc.|</span>
			<span>An.Alc.|</span>
			<span>Cpo.An.Alc.|</span>
			<span>An.|</span>
			<span>Cpo.An.</span>
		</div>
		<?php
		// Por cada Antecedente
		foreach ($e->antecedentes as $ant) {
		?>
			<div class="etiqueta_fila etiqueta_fila_antecedente">
				<span><?php echo str_pad($ant->numero_a, 5, "0", STR_PAD_LEFT).'&nbsp;&nbsp;&nbsp;|'; ?></span>
				<span><?php echo $ant->tipo_a.'&nbsp;&nbsp;&nbsp;&nbsp;|'; ?></span>
				<span><?php echo $ant->anio_a.' |'; ?></span>
				<span><?php echo $ant->digito_a.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|'; ?></span>
				<span><?php echo $ant->cuerpo_a.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|'; ?></span>
				<span><?php echo $ant->alcance_a.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|'; ?></span>
				<span><?php echo $ant->cuerpoalcance_a.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|'; ?></span>
				<span><?php echo $ant->anexoalcance_a.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|'; ?></span>
				<span><?php echo $ant->cuerpoanexoalcance_a.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|'; ?></span>
				<span><?php echo $ant->anexo_a.'&nbsp;&nbsp;&nbsp;|'; ?></span>
				<span><?php echo $ant->cuerpoanexo_a; ?></span>
			</div>
		<?php
		}
	}
	?>
	<br>
	<div><span class="etiqueta_subrayado">Observaciones</span>: <?php echo $e->observaciones_expe; ?></div>
</div>
