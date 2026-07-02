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

// Información de la Codificadora de Temas
$codtema = $this->vista->data['codtema'];

// Transformación de la Codificadora de Temas a JSON para tenerla disponible en la vista como 'codtema'.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonCodtema = JsonHelper::get()->serializar($codtema);

// Para mostrar en el Calendario de la Fecha Desde y Hasta de la Codificadora de Temas 
$v_vigencia_desde_tema = ( !is_null($codtema->vigencia_desde_tema) ) ? Validator::get()->convertirAFechaVista($codtema->vigencia_desde_tema) : '';
$v_vigencia_hasta_tema = ( !is_null($codtema->vigencia_hasta_tema) ) ? Validator::get()->convertirAFechaVista($codtema->vigencia_hasta_tema) : '';

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12 titulo-codificadora">
        <span class="glyphicon glyphicon-edit"></span>&nbsp;Edici&oacute;n de la Codificadora de Temas
    </div>
</div>
<form id="form_edicion_codtema" name="form_edicion_codtema" class="form-horizontal">
    
    <input id="f_id_codtema" name="f_id_codtema" type="hidden" value="" />
    
    <div class="row">
        <div class="col-md-10">
            <div class="form-group">
                <label class="col-xs-3 col-sm-2 col-md-2 control-label">C&oacute;digo:</label>
                <div class="col-xs-1 col-sm-1 col-md-1" style="padding-top: 5px;">
                    <small class="form-text">
                        <?=($codtema->id_codtema != 0) ? $codtema->id_codtema : '---';?>
                    </small>
                </div>
            </div>
            <div class="form-group">
                <label for="f_descripcion_tema" class="col-xs-3 col-sm-2 col-md-2 control-label">Descripci&oacute;n:</label>
                <div class="col-xs-9 col-sm-10 col-md-10">
                    <input id="f_descripcion_tema" name="f_descripcion_tema" type="text" class="form-control input-sm" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="v_vigencia_desde_tema" class="col-xs-3 col-sm-2 col-md-2 control-label">Vigente desde:</label>
                <div class="col-xs-2 col-sm-2 col-md-1">
                     <input id="v_vigencia_desde_tema" name="v_vigencia_desde_tema" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_vigencia_desde_tema; ?>">
                </div>
                <input type="hidden" id="f_vigencia_desde_tema" name="f_vigencia_desde_tema" value="" />
            </div>
            <div class="form-group">
                <label for="v_vigencia_hasta_tema" class="col-xs-3 col-sm-2 col-md-2 control-label">Vigente hasta:</label>
                <div class="col-xs-2 col-sm-2 col-md-1">
                    <input id="v_vigencia_hasta_tema" name="v_vigencia_hasta_tema" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_vigencia_hasta_tema; ?>">
                </div>
                <input type="hidden" id="f_vigencia_hasta_tema" name="f_vigencia_hasta_tema" value="" />
            </div>
            <div class="form-group">
                <label for="f_habilitado_tema" class="col-xs-3 col-sm-2 col-md-2 control-label">Habilitado:</label>
                <div class="col-xs-9 col-sm-4 col-md-4">
                    <div class="checkbox-circle">
                        <input type="checkbox" id="f_habilitado_tema" name="f_habilitado_tema" value="">
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
    var codtema = <?php echo $jsonCodtema; ?>;
</script>