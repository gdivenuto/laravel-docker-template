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
 *
 * Extras:
 *
 *  $actuacion     Actuacion en curso ($this->vista->data['actuacion'])
 *  $paso          Paso actual para la Actuacion ($actuacion->obtenerPasoActual())
 */

$actuacion = $this->vista->data['actuacion'];
$paso = $actuacion->obtenerPasoActual();
$rnd = str_pad(rand(0, 99999999), 8, "0", STR_PAD_LEFT);
?>
<form   id="formPasoActuacion" name="formPasoActuacion" 
        action="index.php?c=actuaciones&a=siguiente&actuacion=<?= $actuacion->obtenerTipoDeClaseActuacion(); ?>" 
        method="POST">

    <div id="alerta_archivo_embebido" class="row display_none">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>
                <strong>ATENCI&Oacute;N:</strong> este documento posee archivos embebidos!
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <embed id="vista_previa_documento" src="" width="850" height="330" type="application/pdf">
        </div>
        <div class="col-md-4">
            <p id="paso_ayuda" class="margen_sup_10"></p>
            
            <h3>¿Desea descartar la revisión del documento pendiente?</h3>
            <div class="radio">
                <label>
                    <input type="radio" id="op_descartar_si" name="f_op_descartar" class="op_descartar" value="1">&nbsp;Si, deseo <strong>descartar</strong> la revisión del documento pendiente.
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" id="op_descartar_no" name="f_op_descartar" class="op_descartar" value="0">&nbsp;No, no deseo <strong>descartar</strong> la revisión del documento pendiente.
                </label>
            </div>
        </div>
        
    </div>

</form>