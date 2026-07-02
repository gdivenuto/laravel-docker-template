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

// Información de la Categoría
$codcategoria = $this->vista->data['categoria'];

// Transformación de la Categoria a JSON para tenerla disponible en la vista como 'categoria'.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonCodcategoria = JsonHelper::get()->serializar($codcategoria);

// Para mostrar en el Calendario de la Fecha Desde y Hasta de la Categoría 
$v_vigencia_desde_categoria = ( !is_null($codcategoria->vigencia_desde_categoria) ) ? Validator::get()->convertirAFechaVista($codcategoria->vigencia_desde_categoria) : '';
$v_vigencia_hasta_categoria = ( !is_null($codcategoria->vigencia_hasta_categoria) ) ? Validator::get()->convertirAFechaVista($codcategoria->vigencia_hasta_categoria) : '';

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12 titulo-codificadora">
        <span class="glyphicon glyphicon-edit"></span>&nbsp;Edici&oacute;n de la Categor&iacute;a
    </div>
</div>
<form id="form_edicion_categoria" name="form_edicion_categoria" class="form-horizontal" role="form">

    <input id="f_id_codcategoria" name="f_id_codcategoria" type="hidden" value="">
    
    <div class="row">
        <div class="col-md-10">
            <div class="form-group">
                <label class="col-xs-3 col-sm-2 col-md-2 control-label">C&oacute;digo:</label>
                <div class="col-xs-1 col-sm-1 col-md-1" style="padding-top: 5px;">
                    <small class="form-text">
                        <?=($codcategoria->id_codcategoria != 0) ? $codcategoria->id_codcategoria : '---';?>
                    </small>
                </div>
            </div>
            <div class="form-group">
                <label for="f_descripcion_categoria" class="col-xs-3 col-sm-2 col-md-2 control-label">Descripci&oacute;n:</label>
                <div class="col-xs-9 col-sm-10 col-md-10">
                    <input id="f_descripcion_categoria" name="f_descripcion_categoria" type="text" class="form-control input-sm" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="v_vigencia_desde_categoria" class="col-xs-3 col-sm-2 col-md-2 control-label">Vigente desde:</label>
                <div class="col-xs-2 col-sm-2 col-md-1">
                    <input id="v_vigencia_desde_categoria" name="v_vigencia_desde_categoria" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_vigencia_desde_categoria; ?>">
                </div>
                <input type="hidden" id="f_vigencia_desde_categoria" name="f_vigencia_desde_categoria" value="" />
            </div>
            <div class="form-group">
                <label for="v_vigencia_hasta_categoria" class="col-xs-3 col-sm-2 col-md-2 control-label">Vigente hasta:</label>
                <div class="col-xs-2 col-sm-2 col-md-1">
                    <input id="v_vigencia_hasta_categoria" name="v_vigencia_hasta_categoria" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_vigencia_hasta_categoria; ?>">
                </div>
                <input type="hidden" id="f_vigencia_hasta_categoria" name="f_vigencia_hasta_categoria" value="" />
            </div>
            <div class="form-group">
                <label for="f_habilitado_categoria" class="col-xs-3 col-sm-2 col-md-2 control-label">Habilitado:</label>
                <div class="col-xs-9 col-sm-4 col-md-4">
                    <div class="checkbox-circle">
                        <input type="checkbox" id="f_habilitado_categoria" name="f_habilitado_categoria" value="">
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
    var codcategoria = <?php echo $jsonCodcategoria; ?>;
</script>