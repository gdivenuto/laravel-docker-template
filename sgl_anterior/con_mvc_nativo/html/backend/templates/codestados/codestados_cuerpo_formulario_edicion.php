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

// Información de la Codificadora de Estados
$codestado = $this->vista->data['codestado'];

// Transformación de la Codificadora de Estados a JSON para tenerla disponible en la vista como 'codestado'.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonCodestado = JsonHelper::get()->serializar($codestado);

// Para mostrar en el Calendario de la Fecha Desde y Hasta de la Codificadora del Estado 
$v_vigencia_desde_codestado = ( !is_null($codestado->vigencia_desde_codestado) ) ? Validator::get()->convertirAFechaVista($codestado->vigencia_desde_codestado) : '';
$v_vigencia_hasta_codestado = ( !is_null($codestado->vigencia_hasta_codestado) ) ? Validator::get()->convertirAFechaVista($codestado->vigencia_hasta_codestado) : '';

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12 titulo-codificadora">
        <span class="glyphicon glyphicon-edit"></span>&nbsp;Edici&oacute;n de la Codificadora de Estados
    </div>
</div>
<form id="form_edicion_codestado" name="form_edicion_codestado" class="form-horizontal">

    <input id="f_id_codestado" name="f_id_codestado" type="hidden" value="">

    <div class="row">
        <div class="col-md-10">
            <div class="form-group">
                <label  class="col-xs-3 col-sm-2 col-md-2 control-label">C&oacute;digo:</label>
                <div class="col-xs-1 col-sm-1 col-md-1" style="padding-top: 5px;">
                    <small class="form-text">
                        <?=($codestado->id_codestado != 0) ? $codestado->id_codestado : '---';?>
                    </small>
                </div>
            </div>
            <div class="form-group">
                <label for="f_nombre_estado" class="col-xs-3 col-sm-2 col-md-2 control-label">Nombre:</label>
                <div class="col-xs-9 col-sm-10 col-md-10">
                    <input id="f_nombre_estado" name="f_nombre_estado" type="text" class="form-control input-sm" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="v_vigencia_desde_codestado" class="col-xs-3 col-sm-2 col-md-2 control-label">Vigente desde:</label>
                <div class="col-xs-2 col-sm-2 col-md-1">
                    <input id="v_vigencia_desde_codestado" name="v_vigencia_desde_codestado" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_vigencia_desde_codestado; ?>">
                </div>
                <input type="hidden" id="f_vigencia_desde_codestado" name="f_vigencia_desde_codestado" value="" />
            </div>
            <div class="form-group">
                <label for="v_vigencia_hasta_codestado" class="col-xs-3 col-sm-2 col-md-2 control-label">Vigente hasta:</label>
                <div class="col-xs-2 col-sm-2 col-md-1">
                    <input id="v_vigencia_hasta_codestado" name="v_vigencia_hasta_codestado" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_vigencia_hasta_codestado; ?>">
                </div>
                <input type="hidden" id="f_vigencia_hasta_codestado" name="f_vigencia_hasta_codestado" value="" />
            </div>
            <div class="form-group">
                <label for="f_observaciones_codestado" class="col-xs-3 col-sm-2 col-md-2 control-label">Observaciones:</label>
                <div class="col-xs-9 col-sm-10 col-md-10">
                    <textarea id="f_observaciones_codestado" name="f_observaciones_codestado" class="form-control xs_textarea" rows="10"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="f_habilitado_codestado" class="col-xs-3 col-sm-2 col-md-2 control-label">Habilitado:</label>
                <div class="col-xs-9 col-sm-4 col-md-4">
                    <div class="checkbox-circle">
                        <input type="checkbox" id="f_habilitado_codestado" name="f_habilitado_codestado" value="">
                    </div>
                </div>
            </div>
            <br>
            <div class="form-group">
                <div class="col-xs-9 col-xs-offset-3 col-sm-10 col-sm-offset-2 col-md-10 col-md-offset-2">
                    <button id="btn_guardar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
                    <button id="btn_cancelar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    // Volcado de datos JSON
    var codestado = <?php echo $jsonCodestado; ?>;
</script>