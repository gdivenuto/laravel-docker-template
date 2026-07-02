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

// Información de la Codificadora de Proyectos
$codproyecto = $this->vista->data['codproyecto'];

// Transformación de la Codificadora de Proyectos a JSON para tenerla disponible en la vista como 'codproyecto'.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonCodproyecto = JsonHelper::get()->serializar($codproyecto);

// Para mostrar en el Calendario de la Fecha Desde y Hasta de la Codificadora de Proyectos
$v_vigencia_desde_codproy = ( !is_null($codproyecto->vigencia_desde_codproy) ) ? Validator::get()->convertirAFechaVista($codproyecto->vigencia_desde_codproy) : '';
$v_vigencia_hasta_codproy = ( !is_null($codproyecto->vigencia_hasta_codproy) ) ? Validator::get()->convertirAFechaVista($codproyecto->vigencia_hasta_codproy) : '';

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12 titulo-codificadora">
        <span class="glyphicon glyphicon-edit"></span>&nbsp;Edici&oacute;n de la Codificadora de Proyectos
    </div>
</div>
<form id="form_edicion_codproyecto" name="form_edicion_codproyecto" class="form-horizontal" role="form">
    
    <input id="f_id_codproyecto" name="f_id_codproyecto" type="hidden" value="" />

    <div class="row">
        <div class="col-md-10">
            <div class="form-group">
                <label class="col-xs-3 col-sm-2 col-md-2 control-label">C&oacute;digo:</label>
                <div class="col-xs-1 col-sm-1 col-md-1" style="padding-top: 5px;">
                    <small class="form-text">
                        <?=($codproyecto->id_codproyecto != 0) ? $codproyecto->id_codproyecto : '---';?>
                    </small>
                </div>
            </div>
            <div class="form-group">
                <label for="f_descripcion_proyecto" class="col-xs-3 col-sm-2 col-md-2 control-label">Descripci&oacute;n:</label>
                <div class="col-xs-9 col-sm-10 col-md-10">
                    <input id="f_descripcion_proyecto" name="f_descripcion_proyecto" type="text" class="form-control input-sm" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="v_vigencia_desde_codproy" class="col-xs-3 col-sm-2 col-md-2 control-label">Vigente desde:</label>
                <div class="col-xs-2 col-sm-2 col-md-1">
                    <input id="v_vigencia_desde_codproy" name="v_vigencia_desde_codproy" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_vigencia_desde_codproy; ?>">
                 </div>
                <input type="hidden" id="f_vigencia_desde_codproy" name="f_vigencia_desde_codproy" value="" />
            </div>
            <div class="form-group">
                <label for="v_vigencia_hasta_codproy" class="col-xs-3 col-sm-2 col-md-2 control-label">Vigente hasta:</label>
                <div class="col-xs-2 col-sm-2 col-md-1">
                    <input id="v_vigencia_hasta_codproy" name="v_vigencia_hasta_codproy" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_vigencia_hasta_codproy; ?>">
                </div>
                <input type="hidden" id="f_vigencia_hasta_codproy" name="f_vigencia_hasta_codproy" value="" />
            </div>
            <div class="form-group">
                <label for="f_habilitado_codproy" class="col-xs-3 col-sm-2 col-md-2 control-label">Habilitado:</label>
                <div class="col-xs-9 col-sm-4 col-md-4">
                    <div class="checkbox-circle">
                        <input type="checkbox" id="f_habilitado_codproy" name="f_habilitado_codproy" value="">
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
    var codproyecto = <?php echo $jsonCodproyecto; ?>;
</script>