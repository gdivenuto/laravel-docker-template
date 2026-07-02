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
        <div class="col-md-8">
            <embed id="vista_previa_documento" src="" width="850" height="330" type="application/pdf">
        </div>
        <div class="col-md-4">
            <h3><a href="http://www.concejomdp.gov.ar/biblioteca/legislacion/ACCESO%20A%20LA%20INFORMACION%20HCD%20-%20D-1404.pdf?v=<?= $rnd; ?>" target="_blank">Art. 11 Decreto 1404</a></h3>
            <p id="paso_ayuda" class="margen_sup_10"></p>
            
            <h3>¿El documento es alcanzado por el <a href="http://www.concejomdp.gov.ar/biblioteca/legislacion/ACCESO%20A%20LA%20INFORMACION%20HCD%20-%20D-1404.pdf?v=<?= $rnd; ?>" target="_blank">Art. 11 Decreto 1404</a>?</h3>
            <div class="radio">
                <label>
                    <input type="radio" id="op_decreto_si" name="f_op_decreto" class="op_decreto" value="1">&nbsp;S&iacute;, el documento es alcanzado por el <a href="http://www.concejomdp.gov.ar/biblioteca/legislacion/ACCESO%20A%20LA%20INFORMACION%20HCD%20-%20D-1404.pdf?v=<?= $rnd; ?>" target="_blank">Art. 11 Decreto 1404</a>.
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" id="op_decreto_no" name="f_op_decreto" class="op_decreto" value="0">&nbsp;No, el documento no es alcanzado por el <a href="http://www.concejomdp.gov.ar/biblioteca/legislacion/ACCESO%20A%20LA%20INFORMACION%20HCD%20-%20D-1404.pdf?v=<?= $rnd; ?>" target="_blank">Art. 11 Decreto 1404</a>.
                </label>
            </div>
        </div>
        
    </div>

</form>