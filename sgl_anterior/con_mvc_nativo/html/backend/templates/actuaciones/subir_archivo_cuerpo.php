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
?>
<form   id="formPasoActuacion" name="formPasoActuacion" 
        action="index.php?c=actuaciones&a=siguiente&actuacion=<?= $actuacion->obtenerTipoDeClaseActuacion(); ?>" 
        method="POST"
        enctype="multipart/form-data">

    <div class="form-group">
        <label for="f_titulo">Detalle del archivo:</label>
        <input id="f_titulo" name="f_titulo" type="text" class="form-control input-sm" value="">
    </div>

    <div class="form-group">
        <label for="f_archivo">Seleccione un archivo:</label>
        <input  type="file" id="f_archivo" name="f_archivo" value="" accept="" class="form-control form-control-sm" />
        <small>Se permiten archivos con extensi&oacute;n <strong><span id="extension_archivo"></span></strong> de tama&ntilde;o hasta <strong><span id="tamanio_archivo"></span> MB</strong>.</small>
    </div>    

</form>