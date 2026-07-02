<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 				Array asociativo que contiene todos los parámetros de la vista para ser
 *  								utilizados en la plantilla.
 *
 * Además:
 * 
 *  $this->vista->dataTitulo		Titulo de la vista
 *  $this->vista->dataSubtitulo		Subtitulo de la vista
 *  $this->vista->dataTexto			Texto introductorio de la vista
 *  $this->vista->dataUsuario		Instancia del usuario actual.
 *  $this->vista->dataMensajeOk		Mensaje de confirmación que debe mostrarse en la vista.
 *  $this->vista->dataMensajeError	Mensaje de error que debe mostrarse en la vista.
 */
$this->generarModalDialog();
?>
<!-- Este contenedor se mueve con JS dentro del Datatable, en el evento drawCallback -->
<div id="contenedor-botones-dt" class="dataTables_filter">
    <button id="btn_nueva_categoria" type="button" class="btn btn-primary btn-sm boton-adaptado"><span class="glyphicon glyphicon-plus"></span>&nbsp;Nueva</button>
    <button id="btn_volver" type="button" class="btn btn-primary btn-sm boton-adaptado"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes</button>
</div>
<div class="row">
    <div class="col-md-12 responsive">
        <div class="row">
            <div id="grillaCodcategoriasContainer" class="col-md-12 responsive contenedor-grilla contenedora-grilla-codificadora">
                <!-- La grilla se genera dinamicamente -->
            </div>
        </div>
    </div>
</div>