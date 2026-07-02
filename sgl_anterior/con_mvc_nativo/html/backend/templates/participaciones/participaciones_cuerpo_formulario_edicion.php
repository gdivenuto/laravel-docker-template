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

// Información del exped_en_participacion
$exped_en_participacion = $this->vista->data['exped_en_participacion'];

// Transformación del exped_en_participacion a JSON para tenerlo disponible en la vista como exped_en_participacion.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonExpedEnParticipacion = JsonHelper::get()->serializar($exped_en_participacion);

// Para mostrar correctamente las fechas
$v_fecha_inicio = (!is_null($exped_en_participacion->fecha_inicio)) ? Validator::get()->convertirAFechaVista($exped_en_participacion->fecha_inicio) : '';

//$v_fecha_fin = (!is_null($exped_en_participacion->fecha_fin)) ? Validator::get()->convertirAFechaVista($exped_en_participacion->fecha_fin) : '';

$extracto = (isset($exped_en_participacion->extracto)) ? $exped_en_participacion->extracto : '';

$this->generarModalDialog();
?>
<div class="row espacio_interno_5 borde-inferior">
    <div class="col-md-12"><span class="glyphicon glyphicon-edit"></span>&nbsp;Edici&oacute;n de la Participaci&oacute;n para <?=($exped_en_participacion->tipo == 'E') ? 'el Expediente' : 'la Nota';?>:</div>
</div>

<form id="form_edicion_exped_en_participacion" name="form_edicion_exped_en_participacion" class="form-horizontal" role="form">

    <?php $this->incluirPlantilla('expedientes/expedientes_cabecera_formulario_edicion.php');?>

    <!-- Datos restantes -->
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="v_fecha_inicio" class="col-xs-3 col-sm-2 col-md-2 control-label">Fecha inicio:</label>
                <div class="col-xs-3 col-sm-1 col-md-1">
                    <input id="v_fecha_inicio" name="v_fecha_inicio" class="form-control input-sm criterio_busqueda_campo_fecha espacio_interno_izq_5" type="text" placeholder="dd/mm/aaaa" value="<?=$v_fecha_inicio;?>">
                </div>
                <input type="hidden" id="f_fecha_inicio" name="f_fecha_inicio" value="" />

                <!-- <label for="v_fecha_fin" class="col-xs-3 col-sm-2 col-md-2 col-lg-2 control-label espacio_interno_der_5">Fecha finalizaci&oacute;n:</label>
                <div class="col-xs-2 col-sm-1 col-md-1 sin_padding_izq">
                    <input id="v_fecha_fin" name="v_fecha_fin" class="form-control input-sm criterio_busqueda_campo_fecha espacio_interno_izq_5" type="text" placeholder="" value="<?=$v_fecha_fin;?>" disabled>
                </div> -->
                <input type="hidden" id="f_fecha_fin" name="f_fecha_fin" value="" />
            </div>

            <div class="form-group">
                <label for="f_extracto" class="col-xs-3 col-sm-2 col-md-2 control-label">Extracto:</label>
                <div class="col-xs-12 col-sm-10 col-md-8">
                    <textarea id="f_extracto" name="f_extracto" class="form-control" rows="9"><?=$extracto;?></textarea>
                </div>
            </div>

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
    var exped_en_participacion = <?php echo $jsonExpedEnParticipacion; ?>;
</script>