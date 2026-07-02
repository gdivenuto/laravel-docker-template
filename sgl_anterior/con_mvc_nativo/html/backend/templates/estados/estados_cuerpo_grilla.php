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
    <!-- Criterio de búsqueda simple + Solapas + Botón NUEVO + Grilla -->
    <div class="col-md-9 responsive">
        <div class="row borde-inferior">
            <div class="col-md-12 responsive">
                <?php $this->incluirPlantilla('expedientes/expedientes_buscador_expediente.php'); ?>
            </div>
        </div>
        <!-- Solapas + Botón NUEVO -->
        <div class="row">
            <!-- Solapas -->
            <div class="col-sm-11 responsive contenedor-solapas">
                <?php $this->incluirPlantilla('expedientes/expedientes_solapas.php'); ?>
            </div>
        <?php 
        // Sólo Perfil 1 ó 2 puede agregar un Nuevo registro
        if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 ) {
        ?>
            <!-- Botón Nuevo -->
            <div class="col-sm-1 responsive margen_sup_5">
                <div class="row text-right">
                    <button id="btn_nuevo_estado" type="button" class="btn btn-primary btn-sm boton-adaptado"><span class="glyphicon glyphicon-plus"></span>&nbsp;Nuevo Estado</button>
                </div>
            </div>
        <?php
        }
        ?>
        </div>
        <!-- Zona secundaria de errores -->
        <div id="row_error_grilla" class="row">
            <div class="col-md-12 responsive contenedor-grilla"><span id="msg_error_grilla" class="help-block">&nbsp;</span></div>
        </div>
        <!-- Grilla -->
        <div class="row">
            <div id="grillaEstadosContainer" class="col-md-12 responsive contenedor-grilla">
                <!-- La grilla se genera dinamicamente -->
            </div>
        </div>
    </div>
    <?php $this->incluirPlantilla('expedientes/expedientes_vista_previa_expediente.php'); ?>
</div>
<script type="text/javascript">
    var perfil_usuario_actual = <?php echo (isset($_SESSION['perfil2'])) ? $_SESSION['perfil2'] : 0; ?>;
</script>