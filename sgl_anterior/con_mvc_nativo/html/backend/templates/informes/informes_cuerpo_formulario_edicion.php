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

// Información del informe
$informe = $this->vista->data['informe'];

// Transformación del informe a JSON para tenerlo disponible en la vista como informe.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonInforme = JsonHelper::get()->serializar($informe);

// Para mostrar correctamente las fechas del Informe 
$v_fecha_pedido_informe = ( !is_null($informe->fecha_pedido_informe) ) ? Validator::get()->convertirAFechaVista($informe->fecha_pedido_informe) : '';
$v_fecha_vuelta_informe = ( !is_null($informe->fecha_vuelta_informe) ) ? Validator::get()->convertirAFechaVista($informe->fecha_vuelta_informe) : '';

$nombre_segun_tipo = ($informe->tipo == 'E') ? ' el Expediente ' : ' la Nota ';

// Para poder mostrar o no el botón "Nuevo Informe" al Cancelar la edición
$f_fecha_salida_giro = $this->vista->data['f_fecha_salida_giro'];

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12">
        <span class="glyphicon glyphicon-edit"></span>
        &nbsp;Informe de <?php echo $informe->ro_nombre_comision; ?>, para
        &nbsp;<?php echo $nombre_segun_tipo.' '.$informe->anio.' '.$informe->tipo.' '.$informe->numero.' '.$informe->cuerpo.' '.$informe->alcance; ?>
    </div>
</div>
<form id="form_edicion_informe" name="form_edicion_informe" class="form-horizontal" role="form">

    <!-- Clave del Expediente -->
    <input type="hidden" id="f_anio" name="f_anio" value="<?php echo $informe->anio; ?>" />
    <input type="hidden" id="f_tipo" name="f_tipo" value="<?php echo $informe->tipo; ?>" />
    <input type="hidden" id="f_numero" name="f_numero" value="<?php echo $informe->numero; ?>" />
    <input type="hidden" id="f_cuerpo" name="f_cuerpo" value="<?php echo $informe->cuerpo; ?>" />
    <input type="hidden" id="f_alcance" name="f_alcance" value="<?php echo $informe->alcance; ?>" />
    <!-- Se mantiene el valor de la fecha de salida del giro -->
    <input type="hidden" id="f_fecha_salida_giro" name="f_fecha_salida_giro" value="<?php echo $f_fecha_salida_giro; ?>" />
    
    <div class="row">
        <div class="col-md-12">    
            <div class="form-group">
                <label for="f_orden_giro" class="col-sm-2 col-md-2 control-label">Orden Giro:</label>
                <div class="col-sm-2 col-md-1">
                    <input id="f_orden_giro" name="f_orden_giro" type="text" class="form-control input-sm" readonly placeholder="Autom&aacute;tico" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="f_orden_informe" class="col-sm-2 col-md-2 control-label">Orden Informe:</label>
                <div class="col-sm-2 col-md-2">
                    <input id="f_orden_informe" name="f_orden_informe" type="text" class="form-control input-sm" readonly placeholder="Autom&aacute;tico" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="v_fecha_pedido_informe" class="col-sm-2 col-md-2 control-label">Fecha de Pedido:</label>
                <div class="col-sm-2 col-md-2">
                    <input id="v_fecha_pedido_informe" name="v_fecha_pedido_informe" class="form-control input-sm criterio_busqueda_campo_fecha espacio_interno_izq_5" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_pedido_informe; ?>">
                </div>
                <input type="hidden" id="f_fecha_pedido_informe" name="f_fecha_pedido_informe" value="" />
            </div>
            <div class="form-group">
                <label for="v_fecha_vuelta_informe" class="col-sm-2 col-md-2 control-label">Fecha de Vuelta:</label>
                <div class="col-sm-2 col-md-2">
                    <input id="v_fecha_vuelta_informe" name="v_fecha_vuelta_informe" class="form-control input-sm criterio_busqueda_campo_fecha espacio_interno_izq_5" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_vuelta_informe; ?>">
                </div>
                <input type="hidden" id="f_fecha_vuelta_informe" name="f_fecha_vuelta_informe" value="" />
            </div>
            <div class="form-group">
                <label for="f_detalle_informe" class="col-sm-2 col-md-2 control-label">Detalle:</label>
                <div class="col-sm-2 col-md-2">
                    <input id="f_detalle_informe" name="f_detalle_informe" type="text" class="form-control input-sm" value="" maxlength="35">
                </div>
            </div>
            <div class="form-group">
                <label for="f_observaciones_informe" class="col-sm-2 col-md-2 control-label">Observaciones:</label>
                <div class="col-sm-10 col-md-8">
                    <textarea id="f_observaciones_informe" name="f_observaciones_informe" class="form-control" rows="9"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2 col-md-10 col-md-offset-2">
                    <button id="btn_guardar" class="btn btn-primary btn-sm" type="button"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
                    <button id="btn_cancelar" class="btn btn-primary btn-sm" type="button"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    // Volcado de datos JSON
    var informe = <?php echo $jsonInforme; ?>;
</script>