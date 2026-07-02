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

// Información del Lugar (Codificadora de Iniciadores, Comisiones y Autores)
$lugar = $this->vista->data['lugar'];

// Transformación de la Codificadora de Estados a JSON para tenerla disponible en la vista como 'codestado'.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonLugar = JsonHelper::get()->serializar($lugar);

// Para mostrar en el Calendario de la Fecha Desde y Hasta del Lugar (Codificadora de Iniciadores, Comisiones y Autores)
$v_vigente_Desde_grp = ( !is_null($lugar->vigente_Desde_grp) ) ? Validator::get()->convertirAFechaVista($lugar->vigente_Desde_grp) : '';
$v_vigente_Hasta_grp = ( !is_null($lugar->vigente_Hasta_grp) ) ? Validator::get()->convertirAFechaVista($lugar->vigente_Hasta_grp) : '';

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12 titulo-codificadora">
        <span class="glyphicon glyphicon-edit"></span>&nbsp;Edici&oacute;n del Lugar (Codificadora de Iniciadores, Comisiones y Autores)
    </div>
</div>
<form id="form_edicion_lugares" name="form_edicion_lugares" class="form-horizontal">
    <div class="row">
        <div class="col-md-10">
            <div class="form-group">
                <label for="f_tipo_grp" class="col-xs-3 col-sm-4 col-md-3 control-label">Tipo:</label>
                <div class="col-xs-1 col-sm-1 col-md-1">
                    <input id="f_tipo_grp" name="f_tipo_grp" type="text" class="form-control input-sm ancho_80" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="f_codigo_grp" class="col-xs-3 col-sm-4 col-md-3 control-label">C&oacute;digo:</label>
                <div class="col-xs-1 col-sm-1 col-md-1">
                    <input id="f_codigo_grp" name="f_codigo_grp" type="text" class="form-control input-sm ancho_80" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="f_descripcion_grp" class="col-xs-3 col-sm-4 col-md-3 control-label">Descripci&oacute;n:</label>
                <div class="col-xs-9 col-sm-8 col-md-9">
                    <input id="f_descripcion_grp" name="f_descripcion_grp" type="text" class="form-control input-sm" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="f_abreviatura_grp" class="col-xs-3 col-sm-4 col-md-3 control-label">Abreviatura p/ Orden del D&iacute;a:</label>
                <div class="col-xs-9 col-sm-8 col-md-9">
                    <input id="f_abreviatura_grp" name="f_abreviatura_grp" type="text" class="form-control input-sm" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="f_bloque_tipo" class="col-xs-3 col-sm-4 col-md-3 control-label">Bloque Tipo:</label>
                <div class="col-xs-1 col-sm-1 col-md-1">
                    <input id="f_bloque_tipo" name="f_bloque_tipo" type="text" class="form-control input-sm ancho_80" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="f_bloque_codigo" class="col-xs-3 col-sm-4 col-md-3 control-label">Bloque C&oacute;digo:</label>
                <div class="col-xs-1 col-sm-1 col-md-1">
                    <input id="f_bloque_codigo" name="f_bloque_codigo" type="text" class="form-control input-sm ancho_80" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="v_vigente_Desde_grp" class="col-xs-3 col-sm-4 col-md-3 control-label">Vigencia desde:</label>
                <div class="col-xs-2 col-sm-2 col-md-1">
                    <input id="v_vigente_Desde_grp" name="v_vigente_Desde_grp" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_vigente_Desde_grp; ?>">
                </div>
                <input type="hidden" id="f_vigente_Desde_grp" name="f_vigente_Desde_grp" value="" />
            </div>
            <div class="form-group">
                <label for="v_vigente_Hasta_grp" class="col-xs-3 col-sm-4 col-md-3 control-label">Vigencia hasta:</label>
                <div class="col-xs-2 col-sm-2 col-md-1">
                    <input id="v_vigente_Hasta_grp" name="v_vigente_Hasta_grp" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_vigente_Hasta_grp; ?>">
                 </div>
                <input type="hidden" id="f_vigente_Hasta_grp" name="f_vigente_Hasta_grp" value="" />
            </div>
            <div class="form-group">
                <label for="f_observaciones_grp" class="col-xs-3 col-sm-4 col-md-3 control-label">Observaciones:</label>
                <div class="col-xs-9 col-sm-8 col-md-9">
                    <textarea id="f_observaciones_grp" name="f_observaciones_grp" class="form-control" rows="5"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="f_habilitado_grp" class="col-xs-3 col-sm-4 col-md-3 control-label">Habilitado:</label>
                <div class="col-xs-9 col-sm-4 col-md-4">
                    <div class="checkbox-circle">
                        <input type="checkbox" id="f_habilitado_grp" name="f_habilitado_grp" value="">
                    </div>
                </div>
            </div>
            <br>
            <div class="form-group">
                <div class="col-xs-9 col-xs-offset-3 col-sm-8 col-sm-offset-4 col-md-10 col-md-offset-3">
                    <button id="btn_guardar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
                    <button id="btn_cancelar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    // Volcado de datos JSON
    var lugar = <?php echo $jsonLugar; ?>;
</script>