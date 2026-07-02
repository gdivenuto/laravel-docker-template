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
// Información del prestamo
$prestamo = $this->vista->data['prestamo'];
// Para saber a qué grilla volver
$grilla = ($this->vista->data['f_grilla'] != '') ? $this->vista->data['f_grilla'] : 'solapa';

// Transformación de prestamo a JSON para tenerlo disponible en la vista como prestamo.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonPrestamo = JsonHelper::get()->serializar($prestamo, JSON_HEX_APOS);

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12"><span class="glyphicon glyphicon-edit"></span>&nbsp;<b>Prestar</b> Expediente <?php echo $prestamo->anio.'-'.$prestamo->tipo.'-'.$prestamo->numero.'-'.$prestamo->cuerpo.'-'.$prestamo->alcance; ?></div>
</div>
<form id="formEdicionPrestamo" name="formEdicionPrestamo" class="form-horizontal" role="form">
    
    <input type="hidden" id="f_grilla" name="f_grilla" value="<?php echo $grilla; ?>" />

    <div class="container-fluid">
        <div class="form-group">
            <label for="v_nueva_fecha" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 control-label">Fecha:</label>
            <div class="col-xs-3 col-sm-2 col-md-1 col-lg-1">
                <input id="v_nueva_fecha" name="v_nueva_fecha" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo date("d/m/Y"); ?>" >
            </div>
            <input type="hidden" id="f_nueva_fecha" name="f_nueva_fecha" value="<?php echo date("Y-m-d"); ?>" />

            <label for="f_nueva_hora" class="col-xs-3 col-sm-2 col-md-2 col-lg-2 control-label">Hora</label>
            <div class="col-xs-3 col-sm-2 col-md-1 col-lg-1">
                <input type="text" id="f_nueva_hora" name="f_nueva_hora" value="<?php echo date("H:i:s"); ?>" size="6" maxlength="8" class="form-control input-sm criterio_busqueda_campo_fecha">
            </div>
        </div>
        <div class="form-group">
            <label for="f_libro_numero" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 control-label">Libro Nro.</label>
            <div class="col-xs-3 col-sm-2 col-md-1 col-lg-1">
                <input type="text" id="f_libro_numero" name="f_libro_numero" value="" size="5" class="form-control input-sm criterio_busqueda_campo_fecha">
            </div>
            <label for="f_libro_folio" class="col-xs-3 col-sm-2 col-md-2 col-lg-2 control-label">Libro Folio</label>
            <div class="col-xs-3 col-sm-2 col-md-1 col-lg-1">
                <input type="text" id="f_libro_folio" name="f_libro_folio" value="" size="5" class="form-control input-sm criterio_busqueda_campo_fecha">
            </div>
        </div>
        <div class="form-group">
            <label for="f_observaciones_prestamo" class="col-sm-2 col-md-2 col-lg-2 control-label">Observaciones</label>
            <div class="col-sm-8 col-md-8 col-lg-8">
                <textarea id="f_observaciones_prestamo" name="f_observaciones_prestamo" class="form-control" rows="12"></textarea>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-10 col-sm-offset-2 col-md-10 col-md-offset-2">
                <!-- Botón Guardar -->
                <button id="btn_guardar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
                <button id="btn_cancelar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Cancelar</button>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    // Volcado de datos JSON
    var prestamo = <?php echo $jsonPrestamo; ?>;
</script>