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
 *
 * El 03/06/2020 XXXX
 * Se agregó el Id row_solapas para el contenedor de las Solapas
 * Se agregó el Id row_grilla para el contenedor de la Grilla
 * Para ocultarlos en la versión Móvil
 */
$this->generarModalDialog();
?>
<div class="row contenedor_cuerpo">
    <!-- Criterio de búsqueda simple + Botones (Buscar, Nuevo, Paginador, Búsquedas) + Solapas + Grilla -->
    <div class="col-md-9 responsive">
        <div class="row borde-inferior">
            <div class="col-md-12 responsive">
                <?php $this->incluirPlantilla('expedientes/expedientes_buscador_expediente.php');?>
            </div>
        </div>
        <!-- Solapas -->
        <div id="row_solapas" class="row">
            <div class="col-xs-12 responsive contenedor-solapas">
                <?php $this->incluirPlantilla('expedientes/expedientes_solapas.php');?>
            </div>
        </div>
        <!-- Zona secundaria de errores -->
        <div id="row_error_grilla" class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 responsive contenedor-grilla"><span id="msg_error_grilla" class="help-block">&nbsp;</span></div>
        </div>
        <!-- Grilla -->
        <div id="row_grilla" class="row">
            <div id="grillaExpedientesContainer" class="col-md-12 responsive contenedor-grilla">
                <!-- La grilla se genera dinámicamente -->
            </div>
        </div>
    </div>
    <?php $this->incluirPlantilla('expedientes/expedientes_vista_previa_expediente.php');?>
</div>
<script type="text/javascript">
    perfil_usuario_actual = <?=(isset($_SESSION['perfil2'])) ? $_SESSION['perfil2'] : 0;?>;

    if (verificacion_ppc_hecha === undefined) {
        verificacion_ppc_hecha = '<?=(isset($_SESSION['verificacion_ppc_hecha'])) ? $_SESSION['verificacion_ppc_hecha'] : 0;?>';
    }
</script>
<?php $_SESSION['verificacion_ppc_hecha'] = null; // se limpia ?>
