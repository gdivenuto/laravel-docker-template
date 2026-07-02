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
    
    <select multiple id="f_destinatarios" name="f_destinatarios[]" class="display_none"></select>

    <div class="row">
        <div class="col-md-12">
            <p id="paso_ayuda" class="margen_sup_10"></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 margen_x_10">
            <h3><strong>Destinatarios disponibles</strong></h3>
            <div class="input-group">
                <input  id="f_busqueda" type="text" value="" class="form-control input-sm"
                        placeholder="Ingrese un término de búsqueda.">
                <div class="input-group-btn">
                    <button id="btnBuscarDisponible" class="btn btn-default btn-sm" type="button">
                        <i class="glyphicon glyphicon-search"></i>
                    </button>
                </div>
            </div>
            <br>
            <div id="lista_destinatarios_disponibles" class="row scroll_vertical_auto listas_destinatarios">
            </div>
            <br>
            <div class="form-group">
                <label for="f_destinatario_manual">Otros:</label>
                <div class="input-group">
                    <input id="f_destinatario_manual" type="email" class="form-control input-sm" value="">
                    <div class="input-group-addon btn btn-primary" id="btn_agregar_manual">Agregar</div>
                </div>
            </div>
        </div>
        <div class="col-md-5 margen_x_10">
            <h3 class="margen_sup_23"><strong>Destinatarios seleccionados</strong></h3>
            <h3 class="margen_sup_10">
                <span id="cant_seleccionados"></span>
                <?= ($paso->opciones['cantidad_minima'] > 0) 
                    ? '&nbsp;(se requieren al menos '.$paso->opciones['cantidad_minima'].').' 
                    : '.';?>
            </h3>
            <div id="lista_destinatarios" class="row scroll_vertical_auto listas_destinatarios margen_sup_20">
            </div>
        </div>
    </div>
</form>
