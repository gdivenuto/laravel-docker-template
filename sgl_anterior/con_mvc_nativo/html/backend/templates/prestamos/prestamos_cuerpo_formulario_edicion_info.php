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

// Solo se muestran el libro numero y folio cuando el prestamo ya fue prestado.
$esconderLibro = is_null($prestamo->fecha_prestado);

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12"><span class="glyphicon glyphicon-edit"></span>&nbsp;<b>Editar</b> Pr&eacute;stamo del Expediente <?php echo $prestamo->anio.'-'.$prestamo->tipo.'-'.$prestamo->numero.'-'.$prestamo->cuerpo.'-'.$prestamo->alcance; ?></div>
</div>
<form id="formEdicionPrestamo" name="formEdicionPrestamo" class="form-horizontal" role="form">

    <input type="hidden" id="f_grilla" name="f_grilla" value="<?php echo $grilla; ?>" />

    <div class="container-fluid">
        <div class="form-group" <?php echo ($esconderLibro) ? 'style="display:none"' : ''; ?>>
            <label for="f_libro_numero" class="col-lg-2 control-label">Libro Nro.</label>
            <div class="col-lg-1">
                <input type="text" id="f_libro_numero" name="f_libro_numero" value="" size="5" class="form-control">
            </div>
            <label for="f_libro_folio" class="col-lg-2 control-label">Libro Folio</label>
            <div class="col-lg-1">
                <input type="text" id="f_libro_folio" name="f_libro_folio" value="" size="5" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label for="f_observaciones_prestamo" class="col-lg-2 control-label">Observaciones</label>
            <div class="col-lg-5">
                <textarea id="f_observaciones_prestamo" name="f_observaciones_prestamo" class="form-control" rows="12"></textarea>
             </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-3 col-lg-offset-2">
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