<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
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

$c_anio = Validator::get()->obtenerDefault($this->vista->data['f_anio'], '');
$c_tipo = Validator::get()->obtenerDefault($this->vista->data['f_tipo'], '-');
$c_numero = Validator::get()->obtenerDefault($this->vista->data['f_numero'], '');
$c_cuerpo = Validator::get()->obtenerDefault($this->vista->data['f_cuerpo'], '');
$c_alcance = Validator::get()->obtenerDefault($this->vista->data['f_alcance'], '');
$c_orden_giro = Validator::get()->obtenerDefault($this->vista->data['f_orden_giro'], '');
$c_fecha_salida_giro = Validator::get()->obtenerDefault($this->vista->data['f_fecha_salida_giro']);

$nombre_segun_tipo = ($c_tipo == 'E') ? ' del Expediente ' : ' de la Nota ';

$this->generarModalDialog();
?>
<input type="hidden" id="f_anio" name="f_anio" value="<?php echo $c_anio; ?>" />
<input type="hidden" id="f_tipo" name="f_tipo" value="<?php echo $c_tipo; ?>" />
<input type="hidden" id="f_numero" name="f_numero" value="<?php echo $c_numero; ?>" />
<input type="hidden" id="f_cuerpo" name="f_cuerpo" value="<?php echo $c_cuerpo; ?>" />
<input type="hidden" id="f_alcance" name="f_alcance" value="<?php echo $c_alcance; ?>" />
<input type="hidden" id="f_orden_giro" name="f_orden_giro" value="<?php echo $c_orden_giro; ?>" />
<input type="hidden" id="f_fecha_salida_giro" name="f_fecha_salida_giro" value="<?php echo $c_fecha_salida_giro; ?>" />

<div class="row botonera-codificadora">
    <div class="col-md-9">
        <span class="glyphicon glyphicon-file"></span>
        &nbsp;Informes <?php echo $nombre_segun_tipo . ' ' . $c_anio . ' ' . $c_tipo . ' ' . $c_numero . ' ' . $c_cuerpo . ' ' . $c_alcance; ?>
    </div>
    <div class="col-md-3 text-right">
        <?php
// Sólo Perfil 1 ó 2 puede agregar un Nuevo registro
if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) {
	// Si se encuentra abierto el Giro
	if ($c_fecha_salida_giro === 'null') {
		?>
                <!-- Botón Nuevo -->
                <button id="btn_nuevo_informe" type="button" class="btn btn-primary btn-sm boton-adaptado"><span class="glyphicon glyphicon-plus"></span>&nbsp;Nuevo Informe</button>
        <?php
}
}
?>
        <!-- Botón Volver, a la grilla de Giros -->
        <button id="btn_volver" type="button" class="btn btn-primary btn-sm boton-adaptado" title="Volver a la solapa de Giros"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Giros</button>
    </div>
</div>
<!-- Grilla -->
<div class="row">
    <div id="grillaInformesContainer" class="col-md-12 responsive contenedor-grilla">
        <!-- La grilla se genera dinamicamente -->
    </div>
</div>
<script type="text/javascript">
    var perfil_usuario_actual = <?php echo (isset($_SESSION['perfil2'])) ? $_SESSION['perfil2'] : 0; ?>;
</script>