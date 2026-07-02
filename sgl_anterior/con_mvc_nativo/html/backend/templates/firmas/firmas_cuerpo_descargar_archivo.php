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
?>
<div class="row">
    <div class="col-md-12">
        <h2>Firmador Online - Documento generado</h2>
        <a class="btn btn-primary" href="<?= $this->vista->baseUrl;?>index.php?c=expedientes&a=view" role="button">
            <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;Volver
        </a>
    </div>
    <div class="col-md-12">
        <embed id="vista_previa_documento" src="<?= $this->vista->data['f_archivo_descarga']; ?>" width="100%" height="512" type="application/pdf">
    </div>
</div>

<script type="text/javascript">
    // Parametros
    var perfil_usuario_actual = <?= (isset($_SESSION['perfil2'])) ? $_SESSION['perfil2'] : 0; ?>;
    var url_raiz = '<?=URL_KRAKEN_BASE;?>';
    var base_url = '<?= $this->vista->baseUrl; ?>';
</script>