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

    <div class="row">
        <div class="col-md-6">
            <p id="paso_ayuda" class="margen_sup_10"></p>
            
            <h3>¿En que formato desea exportar el expediente electr&oacute;nico?</h3>
            <div class="radio">
                <label>
                    <input type="radio" id="op_formato_zip" name="f_op_formato" class="op_formato" value="zip">&nbsp;Agrupar toda la documentaci&oacute;n en <strong>un archivo ZIP comprimido</strong>, manteniendo las firmas digitales originales.
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" id="op_formato_pdf" name="f_op_formato" class="op_formato" value="pdf">&nbsp;Agrupar toda la documentaci&oacute;n en <strong>un &uacute;nico archivo PDF</strong> firmado por el sistema, en calidad de copia fiel.
                </label>
            </div>
            <?php /**/ ?>
            <div class="radio">
                <label>
                    <input type="radio" id="op_formato_pdf_publico" name="f_op_formato" class="op_formato" value="pdf_publico">&nbsp;Agrupar toda la documentaci&oacute;n <strong>p&uacute;blica</strong> en <strong>un &uacute;nico archivo PDF</strong> firmado por el sistema, en calidad de copia fiel.<br><strong>(Los documentos alcanzados por el Art. 11 del Decreto 1404 ser&aacute;n reemplazados por una hoja con su referencia)</strong>.
                </label>
            </div>
            <?php /**/ ?>
        </div>
        
    </div>

</form>