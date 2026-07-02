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

$expe_elec = $this->vista->data['expe_elec'];
try {
    $embebidos = PDFExtractor::get()->getAttachments(PATH_KRAKEN_RESOURCES_PROYECTOS.$expe_elec->documento);
} catch (Exception $e) {
    die(sprintf("ERROR: no se encuentra el documento electrónico, causa %s", $e->getMessage()));
}

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12">
        <h2>Archivos embebidos en el Documento Electr&oacute;nico</h2>
        <h3><?= $expe_elec->obtenerEtiqueta(true); ?></h3>
        <a class="btn btn-primary" href="<?= $this->vista->baseUrl; ?>index.php?c=expedienteselec&a=view&f_anio=<?= $expe_elec->anio; ?>&f_tipo=<?= $expe_elec->tipo; ?>&f_numero=<?= $expe_elec->numero; ?>&f_cuerpo=<?= $expe_elec->cuerpo; ?>&f_alcance=<?= $expe_elec->alcance; ?>&f_orden=<?= $expe_elec->orden; ?>">
            <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;Volver al Expediente
        </a>
    </div>
</div>

<div class="row borde-inferior">
    <div class="col-md-6 responsive">
        <h2>PDF Embebidos: <?= count($embebidos['pdf']); ?> documento(s)</h2>
        <!-- Grilla de PDF-->
        <div id="lista_embebidos_pdf" class="row scroll_vertical_auto listas_firmantes">
<?php foreach ($embebidos['pdf'] as $emb) { ?>
            <div class="col-md-12">
                <a role="button" class="btn btn-sm btn-default" href="<?= $this->vista->baseUrl; ?>index.php?c=expedienteselec&a=verembebido&f_anio=<?= $expe_elec->anio; ?>&f_tipo=<?= $expe_elec->tipo; ?>&f_numero=<?= $expe_elec->numero; ?>&f_cuerpo=<?= $expe_elec->cuerpo; ?>&f_alcance=<?= $expe_elec->alcance; ?>&f_orden=<?= $expe_elec->orden; ?>&f_id_embebido=<?= $emb['id']; ?>" download >
                    <span class="glyphicon glyphicon-save"></span>
                </a>
                <a href="<?= $this->vista->baseUrl; ?>index.php?c=expedienteselec&a=verembebido&f_anio=<?= $expe_elec->anio; ?>&f_tipo=<?= $expe_elec->tipo; ?>&f_numero=<?= $expe_elec->numero; ?>&f_cuerpo=<?= $expe_elec->cuerpo; ?>&f_alcance=<?= $expe_elec->alcance; ?>&f_orden=<?= $expe_elec->orden; ?>&f_id_embebido=<?= $emb['id']; ?>" target="_blank">
                    <?= $emb['file']; ?>
                </a>
            </div>
<?php } ?>
        </div>
    </div>

    <div class="col-md-6 responsive">
        <h2>Otros Embebidos: <?= count($embebidos['other']); ?> documento(s)</h2>
        <!-- Grilla de Otros documentos-->
        <div id="lista_embebidos_otros" class="row scroll_vertical_auto listas_firmantes">
<?php foreach ($embebidos['other'] as $emb) { ?>
            <div class="col-md-12 ">
                <a role="button" class="btn btn-sm btn-default" href="<?= $this->vista->baseUrl; ?>index.php?c=expedienteselec&a=verembebido&f_anio=<?= $expe_elec->anio; ?>&f_tipo=<?= $expe_elec->tipo; ?>&f_numero=<?= $expe_elec->numero; ?>&f_cuerpo=<?= $expe_elec->cuerpo; ?>&f_alcance=<?= $expe_elec->alcance; ?>&f_orden=<?= $expe_elec->orden; ?>&f_id_embebido=<?= $emb['id']; ?>" download >
                    <span class="glyphicon glyphicon-save"></span>
                </a>
                <a href="<?= $this->vista->baseUrl; ?>index.php?c=expedienteselec&a=verembebido&f_anio=<?= $expe_elec->anio; ?>&f_tipo=<?= $expe_elec->tipo; ?>&f_numero=<?= $expe_elec->numero; ?>&f_cuerpo=<?= $expe_elec->cuerpo; ?>&f_alcance=<?= $expe_elec->alcance; ?>&f_orden=<?= $expe_elec->orden; ?>&f_id_embebido=<?= $emb['id']; ?>" target="_blank">
                    <?= $emb['file']; ?>
                </a>
            </div>
<?php } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    // Parametros
    var perfil_usuario_actual = <?= (isset($_SESSION['perfil2'])) ? $_SESSION['perfil2'] : 0; ?>;
    var url_raiz = '<?=URL_KRAKEN_BASE;?>';
    var base_url = '<?= $this->vista->baseUrl; ?>';
</script>