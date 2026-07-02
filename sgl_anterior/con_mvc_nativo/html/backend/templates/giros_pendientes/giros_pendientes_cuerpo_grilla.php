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
 */
$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-6">
        <h2>Giros a comisiones pendientes de confirmaci&oacute;n</h2>
    </div>
    <div class="col-md-6 margen_sup_10">
        <h3>Estos giros a comisiones requieren atenci&oacute;n por estar pendientes de confirmaci&oacute;n.</h3>
    </div>
</div>
<div class="row borde-inferior">
    <div class="col-md-12 responsive">
        <!-- Grilla -->
        <div class="row">
            <div id="grillaGirosPendientesContainer" class="col-md-12 responsive contenedor-grilla">
                <!-- La grilla se genera dinamicamente -->
            </div>
        </div>
    </div>
    <div class="col-md-3 responsive">
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        <a class="btn btn-primary" href="<?= $this->vista->baseUrl;?>index.php?c=expedientes&a=view" role="button">
            <span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a expedientes
        </a>
    </div>
</div>
<script type="text/javascript">
    // Parametros
    var perfil_usuario_actual = <?= (isset($_SESSION['perfil2'])) ? $_SESSION['perfil2'] : 0; ?>;
    var url_raiz = '<?=URL_KRAKEN_BASE;?>';
    var base_url = '<?= $this->vista->baseUrl; ?>';
    var id_usuario = '<?= $this->vista->dataUsuario->id_usuario; ?>';
</script>