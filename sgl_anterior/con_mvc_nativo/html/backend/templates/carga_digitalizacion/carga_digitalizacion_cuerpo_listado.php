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
<div class="row">
    <!-- Botón Volver + Grilla -->
    <div class="col-md-9 responsive">
        <div class="row espacio_interno_sup_3">
            <div class="col-md-4 titulo-codificadora">
                <span class="glyphicon glyphicon-open"></span>&nbsp;Carga de Digitalizaciones
            </div>
            <!-- Botón para volver al listado de Expedientes -->
            <div class="col-md-8 responsive">
                <div class="row text-right">
                    <!-- Botón para volver al listado de expedientes -->
                    <button id="btn_busqueda_simple" type="button" class="btn btn-primary btn-sm boton-adaptado">
                        <span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes
                    </button>
                </div>
            </div>
        </div>
        <div class="row borde-inferior"></div>
        <!-- Grilla -->
        <div class="row">
            <div id="grillaContenidoTemporalContainer" class="col-md-12 responsive contenedor-grilla">
                <!-- La grilla se genera dinamicamente -->
            </div>
        </div>
    </div>
    <?php $this->incluirPlantilla('expedientes/expedientes_vista_previa_expediente.php'); ?>
</div>