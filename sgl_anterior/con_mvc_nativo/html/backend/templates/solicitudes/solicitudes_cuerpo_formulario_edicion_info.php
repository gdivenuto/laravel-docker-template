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

// Transformación de solicitud a JSON para tenerla disponible en la vista como solicitud.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonSolicitud = JsonHelper::get()->serializar($solicitud, JSON_HEX_APOS);

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12"><span class="glyphicon glyphicon-edit"></span>&nbsp;<b>Editar</b> Solicitud del Expediente <?php echo $solicitud->anio.'-'.$solicitud->tipo.'-'.$solicitud->numero.'-'.$solicitud->cuerpo.'-'.$solicitud->alcance; ?></div>
</div>
<form id="formEdicionSolicitud" name="formEdicionSolicitud" class="form-horizontal" role="form">

    <div class="container-fluid">
        <div class="form-group">
            <label for="f_observaciones" class="col-lg-2 control-label">Observaciones</label>
            <div class="col-lg-5">
                <textarea id="f_observaciones" name="f_observaciones" class="form-control" rows="12"></textarea>
             </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-3 col-lg-offset-2">
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