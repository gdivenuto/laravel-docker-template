<?php
// Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
$this->generarModalDialog();

// Genero la cabecera del wizard
$actuacion = $this->vista->data['actuacion'];

$paso = $actuacion->obtenerPasoActual();
$paso_nombre = $paso->nombre;
if (array_key_exists('paso_nombre', $paso->opciones))
	if ($paso->opciones['paso_nombre'] != '')
		$paso_nombre = $paso->opciones['paso_nombre'];

?>
<div class="row">
	<div class="col-md-6">
		<h2><?= $actuacion->nombre; ?></h2>
	</div>
	<div class="col-md-6 margen_sup_10">
		<h3><?= $actuacion->descripcion; ?></h3>
	</div>
</div>

<div class="row borde-inferior">
	<div class="col-md-6">
		<!-- Boton Cancelar -->
		<button class="btn btn-default btn-cancelar">
			Cancelar <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
		</button>
		&nbsp;
		
		<!-- Boton Anterior -->
		<button class="btn btn-default btn-anterior <?= ($actuacion->paso_actual > 0) ? '' : 'disabled'; ?>">
			<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Anterior
		</button>

		<!-- Boton Siguiente/Finalizar -->
		<button class="btn btn-primary btn-siguiente">
		<?php if ($actuacion->enUltimoPaso()) { ?>
			Finalizar <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
		<?php } else { ?>
			Siguiente <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>	
		<?php } ?>
		</button>
	</div>

	<div class="col-md-6 margen_sup_10">
		<p><strong>Paso <?= $actuacion->paso_actual + 1; ?> de <?= count($actuacion->pasos); ?>: <?= $paso_nombre; ?></strong></p>
	</div>
</div>

<!-- Control de errores -->
<?php if (SessionController::get()->existe('MENSAJE_ERROR')) { ?>
	<div class="alert alert-warning alert-dismissible show" role="alert">
		<strong>{SessionController::get()->obtener('MENSAJE_ERROR')}</strong>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
<?php } ?>

<!-- Texto informativo de la actuacion -->
<div id="actuacion_texto_informativo" style="display: none"></div>