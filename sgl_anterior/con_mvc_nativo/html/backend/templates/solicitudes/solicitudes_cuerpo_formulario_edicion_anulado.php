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
// Información de la solicitud
$solicitud = $this->vista->data['solicitud'];

// Transformación de solicitud a JSON para tenerlo disponible en la vista como solicitud.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonSolicitud = JsonHelper::get()->serializar($solicitud, JSON_HEX_APOS);

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12">
        <h4><span class="glyphicon glyphicon-edit"></span>&nbsp;<b>Anular</b> solicitud del Expediente <?php echo $solicitud->anio.'-'.$solicitud->tipo.'-'.$solicitud->numero.'-'.$solicitud->cuerpo.'-'.$solicitud->alcance; ?></h4>
    </div>
</div>
<form id="formEdicionSolicitud" name="formEdicionSolicitud" class="form-horizontal" role="form">
  
    <div class="container-fluid">
        <div class="form-group">
            <label for="v_nueva_fecha" class="col-sm-2 col-md-2 control-label">Fecha:</label>
            <div class="col-sm-2 col-md-1 col-lg-1">
                <input id="v_nueva_fecha" name="v_nueva_fecha" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo date("d/m/Y"); ?>" >
            </div>
            <input type="hidden" id="f_nueva_fecha" name="f_nueva_fecha" value="<?php echo date("Y-m-d"); ?>" />

            <label for="f_nueva_hora" class="col-sm-2 col-md-2 col-lg-1 control-label">Hora</label>
            <div class="col-sm-2 col-md-1 col-lg-1">
                <input type="text" id="f_nueva_hora" name="f_nueva_hora" value="<?php echo date("H:i:s"); ?>" size="6" maxlength="8" class="form-control input-sm criterio_busqueda_campo_fecha">
            </div>
        </div>
        <div class="form-group">
            <label for="f_observaciones" class="col-sm-2 col-md-2 col-lg-2 control-label">Observaciones</label>
            <div class="col-sm-8 col-md-8 col-lg-8">
                <textarea id="f_observaciones" name="f_observaciones" class="form-control" rows="12"></textarea>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-10 col-sm-offset-2 col-md-10 col-md-offset-2">
                <!-- Botón Guardar -->
                <button id="btn_guardar" class="btn btn-primary btn-sm" type="button"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
                <button id="btn_cancelar" class="btn btn-primary btn-sm" type="button"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Cancelar</button>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    // Volcado de datos JSON
    var solicitud = <?php echo $jsonSolicitud; ?>;
</script>