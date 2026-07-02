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
<input type="hidden" id="f_archivo_descarga" value="<?= $this->vista->data['f_archivo_descarga']; ?>">

<div class="row">
    <!-- Criterio de búsqueda simple + Solapas + Botones + Grilla -->
    <div class="col-md-9 responsive">
        <div class="row borde-inferior">
            <div class="col-md-12 responsive">
                <?php $this->incluirPlantilla('expedientes/expedientes_buscador_expediente.php');?>
            </div>
        </div>
        <!-- Solapas -->
        <div class="row">
            <div class="col-sm-11 responsive contenedor-solapas">
                <?php $this->incluirPlantilla('expedientes/expedientes_solapas.php');?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-11 responsive">
                <div class="row margen_y_5">
                    <?php
                    // Sólo Perfil Administrador o Supervisor pueden Cargar y Componer documentos
                    if (in_array($_SESSION['perfil2'], [1, 2])) {
                    ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="glyphicon glyphicon-plus"></span>&nbsp;Agregar documento <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a id="btn_cargar_documento" href="#">
                                        <span class="glyphicon glyphicon-open"></span>&nbsp;Cargar documento PDF
                                    </a>
                                </li>
                                <li>
                                    <a id="btn_cargar_archivo_zip" href="#">
                                        <span class="glyphicon glyphicon-folder-open"></span>&nbsp;Cargar archivo ZIP
                                    </a>
                                </li>
                                <li>
                                    <a id="btn_componer_documento" href="#">
                                        <span class="glyphicon glyphicon-edit"></span>&nbsp;Componer documento
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php }

                    // Sólo Perfil Administrador, Supervisor, Concejales y Secretario HCD
                    // pueden Descargar y Enviar documentos
                    if (in_array($_SESSION['perfil2'], [1, 2, 3, 5])) {
                    ?>
                        <button type="button" class="btn btn-default btn-sm" id="btn_descargar_expe_elec">
                            <span class="glyphicon glyphicon-save"></span>&nbsp;Descargar
                        </button>
                        <button type="button" class="btn btn-default btn-sm" id="btn_enviar_expe_elec">
                            <span class="glyphicon glyphicon-envelope"></span>&nbsp;Enviar
                        </button>
                    <?php }

                    // Sólo Perfil Administrador o Supervisor pueden Cargar Giros
                    if (in_array($_SESSION['perfil2'], [1, 2])) {
                    ?>
                        <button type="button" class="btn btn-default btn-sm" id="btn_cargar_giros_expe_elec">
                            <span class="glyphicon glyphicon-open"></span>&nbsp;Cargar Giros
                        </button>
                    <?php } ?>

                    <button type="button" class="btn btn-default btn-sm" id="btn_ver_caratula">
                        <span class="glyphicon glyphicon-file"></span>&nbsp;Ver Car&aacute;tula
                    </button>
                </div>
            </div>
        </div>

        <!-- Zona secundaria de errores -->
        <div id="row_error_grilla" class="row">
            <div class="col-md-12 responsive contenedor-grilla"><span id="msg_error_grilla" class="help-block">&nbsp;</span></div>
        </div>

        <!-- Grilla principal de los Expedientes Electrónicos -->
        <div class="row">
            <div id="grillaExpedientesElecContainer" class="col-md-12 responsive contenedor-grilla">
                <!-- La grilla se genera dinamicamente -->
            </div>
        </div>
    </div>
    <?php $this->incluirPlantilla('expedientes/expedientes_vista_previa_expediente.php');?>
</div>
<script type="text/javascript">
    var perfil_usuario_actual = <?= (isset($_SESSION['perfil2'])) ? $_SESSION['perfil2'] : 0; ?>;
    var url_raiz = '<?=URL_KRAKEN_BASE;?>';
</script>