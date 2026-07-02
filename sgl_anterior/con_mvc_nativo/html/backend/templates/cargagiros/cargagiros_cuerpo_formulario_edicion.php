<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 *
 *  $this->vista->data              Array asociativo que contiene todos los parámetros de la vista para ser
 *                                  utilizados en la plantilla.
 *
 * Además:
 *
 *  $this->vista->dataTitulo        Titulo de la vista
 *  $this->vista->dataSubtitulo     Subtitulo de la vista
 *  $this->vista->dataTexto         Texto introductorio de la vista
 *  $this->vista->dataUsuario       Instancia del usuario actual.
 *  $this->vista->dataMensajeOk     Mensaje de confirmación que debe mostrarse en la vista.
 *  $this->vista->dataMensajeError  Mensaje de error que debe mostrarse en la vista.
 */

// Clave del Expediente o Nota
$f_anio = $this->vista->data['f_anio'];
$f_tipo = $this->vista->data['f_tipo'];
$f_numero = $this->vista->data['f_numero'];
$f_cuerpo = $this->vista->data['f_cuerpo'];
$f_alcance = $this->vista->data['f_alcance'];

// Listado para la carga del combo de Comisiones
$listado_comisiones = $this->vista->data['listado_comisiones'];

// Para mostrar correctamente las fechas del Giro
$v_fecha_primer_giro = date("d/m/Y");

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12">
        <span class="glyphicon glyphicon-edit"></span>
        &nbsp;Carga Inicial de Giros a Comisi&oacute;n, <?php echo ($f_tipo == 'E') ? 'del Expediente' : 'de la Nota'; ?>:
        &nbsp;<b><?php echo $f_anio . '-' . $f_tipo . '-' . $f_numero . '-' . $f_cuerpo . '-' . $f_alcance; ?></b>
    </div>
</div>
<br>
<form id="form_edicion_cargagiro" name="form_edicion_cargagiro" action="index.php?c=cargagiros&a=save" method="POST" class="form-horizontal" role="form">

    <!-- Clave del Expediente ó Nota a cargarle el Giro respectivo -->
    <input type="hidden" name="f_anio" id="f_anio" value="<?php echo $f_anio; ?>" />
    <input type="hidden" name="f_tipo" id="f_tipo" value="<?php echo $f_tipo; ?>" />
    <input type="hidden" name="f_numero" id="f_numero" value="<?php echo $f_numero; ?>" />
    <input type="hidden" name="f_cuerpo" id="f_cuerpo" value="<?php echo $f_cuerpo; ?>" />
    <input type="hidden" name="f_alcance" id="f_alcance" value="<?php echo $f_alcance; ?>" />

    <div class="row">
        <div class="col-md-2">
            <div class="row">
                <div class="col-md-10 col-md-offset-2"><b>Fecha 1&deg; Giro:</b></div>
            </div>
            <div class="row">
                <div class="col-md-6 col-md-offset-2">
                    <input id="v_fecha_primer_giro" name="v_fecha_primer_giro" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_primer_giro; ?>">
                </div>
                <input type="hidden" id="f_fecha_primer_giro" name="f_fecha_primer_giro" value="" />
            </div>
        </div>
        <?php if ($f_tipo == 'N') {?>
            <div class="col-md-1">
                <div class="checkbox" style="padding-top: 10px">
                    <label class="control-label">
                        <input type="checkbox" id="f_ppc" name="f_ppc" value="1" title="Para considerar Participaci&oacute;n Ciudadana">&nbsp;<strong>PPC</strong>
                    </label>
                </div>
             </div>
        <?php }?>
        <!-- Comisiones a elegir -->
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12"><b>Comisi&oacute;n</b></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <select id="f_comision_1" name="f_comision_1" class="form-control input-sm">
                        <option value="0">---</option><!-- Se muestra aquí 'descripcion_grp' -->
                        <?php
$this->renderOptionList(
	$listado_comisiones, // coleccion
	'codigo_grp', // valor del combo
	'descripcion_grp'); // descripcion
?>
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <select id="f_comision_2" name="f_comision_2" class="form-control input-sm">
                        <option value="0">---</option><!-- Se muestra aquí 'descripcion_grp' -->
                        <?php
$this->renderOptionList(
	$listado_comisiones, // coleccion
	'codigo_grp', // valor del combo
	'descripcion_grp'); // descripcion
?>
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <select id="f_comision_3" name="f_comision_3" class="form-control input-sm">
                        <option value="0">---</option><!-- Se muestra aquí 'descripcion_grp' -->
                        <?php
$this->renderOptionList(
	$listado_comisiones, // coleccion
	'codigo_grp', // valor del combo
	'descripcion_grp'); // descripcion
?>
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <select id="f_comision_4" name="f_comision_4" class="form-control input-sm">
                        <option value="0">---</option><!-- Se muestra aquí 'descripcion_grp' -->
                        <?php
$this->renderOptionList(
	$listado_comisiones, // coleccion
	'codigo_grp', // valor del combo
	'descripcion_grp'); // descripcion
?>
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <select id="f_comision_5" name="f_comision_5" class="form-control input-sm">
                        <option value="0">---</option><!-- Se muestra aquí 'descripcion_grp' -->
                        <?php
$this->renderOptionList(
	$listado_comisiones, // coleccion
	'codigo_grp', // valor del combo
	'descripcion_grp'); // descripcion
?>
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <select id="f_comision_6" name="f_comision_6" class="form-control input-sm">
                        <option value="0">---</option><!-- Se muestra aquí 'descripcion_grp' -->
                        <?php
$this->renderOptionList(
	$listado_comisiones, // coleccion
	'codigo_grp', // valor del combo
	'descripcion_grp'); // descripcion
?>
                    </select>
                </div>
            </div>
        </div>
        <!-- Observaciones de cada comisión elegida -->
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12"><b>Observaciones</b></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <input type="text" id="f_observaciones_giro_1" name="f_observaciones_giro_1" class="form-control input-sm">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <input type="text" id="f_observaciones_giro_2" name="f_observaciones_giro_2" class="form-control input-sm">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <input type="text" id="f_observaciones_giro_3" name="f_observaciones_giro_3" class="form-control input-sm">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <input type="text" id="f_observaciones_giro_4" name="f_observaciones_giro_4" class="form-control input-sm">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <input type="text" id="f_observaciones_giro_5" name="f_observaciones_giro_5" class="form-control input-sm">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <input type="text" id="f_observaciones_giro_6" name="f_observaciones_giro_6" class="form-control input-sm">
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <button id="btn_guardar" class="btn btn-primary btn-sm" type="button">
                <span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar
            </button>
            <button id="btn_cancelar" class="btn btn-primary btn-sm" type="button">
                <span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes
            </button>
        </div>
    </div>
</form>