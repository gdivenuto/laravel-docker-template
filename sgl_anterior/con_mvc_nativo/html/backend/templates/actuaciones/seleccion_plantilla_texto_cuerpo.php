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
<form id="formPasoActuacion" name="formPasoActuacion" method="POST"
      action="index.php?c=actuaciones&a=siguiente&actuacion=<?= $actuacion->obtenerTipoDeClaseActuacion(); ?>">
    
    <div class="row">
        <div class="col-md-12">
            <p id="paso_ayuda" class="margen_sup_10"></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 margen_x_10">
            <h3><strong>Plantillas de documento disponibles</strong></h3>
            <div class="input-group">
                <input  id="f_busqueda" type="text" value="" class="form-control input-sm"
                        placeholder="Ingrese un término de búsqueda.">
                <div class="input-group-btn">
                    <button id="btnBuscarPlantilla" class="btn btn-default btn-sm" type="button">
                        <i class="glyphicon glyphicon-search"></i>
                    </button>
                </div>
            </div>
            <br>
            <div id="lista_plantillas_disponibles" class="row scroll_vertical_auto max_h_250 listas_plantillas">
            </div>
        </div>
        <div class="col-md-5 margen_x_10">
            <h3 class="margen_sup_23">
                <button id="btn_quitar_plantilla" class="btn btn-xs btn-default display_none" title="No utilizar plantilla"><i class="glyphicon glyphicon-remove"></i></button>&nbsp;
                <strong>Plantilla seleccionada:&nbsp;</strong>
                <span id="plantilla_seleccionada">Ninguna</span>
            </h3>
            <div id="plantilla_formulario" class="scroll_vertical_auto listas_plantillas margen_sup_20">
                <input type="hidden" id="f_plantilla" name="f_plantilla" value="">
            </div>
        </div>
    </div>
</form>
