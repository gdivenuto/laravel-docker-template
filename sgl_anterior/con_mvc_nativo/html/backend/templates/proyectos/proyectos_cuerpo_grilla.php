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
                <?php $this->incluirPlantilla('expedientes/expedientes_buscador_expediente.php');?>
            </div>
        </div>
        <!-- Solapas + Botón NUEVO -->
        <div class="row">
            <!-- Solapas -->
            <div class="col-sm-11 responsive contenedor-solapas">
                <?php $this->incluirPlantilla('expedientes/expedientes_solapas.php');?>
            </div>
        <?php
        // Sólo Perfil 1 ó 2 puede agregar un Nuevo registro
        if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) {
        ?>
            <div class="col-sm-1 responsive">
                <div class="row margen_sup_5">
                    <button id="btn_nuevo_proyecto" type="button" class="btn btn-primary btn-sm boton-adaptado">
                        <span class="glyphicon glyphicon-plus"></span>&nbsp;Nuevo proyecto
                    </button>
                </div>
            </div>
        <?php } ?>
        </div>
        <!-- Zona secundaria de errores -->
        <div id="row_error_grilla" class="row">
            <div class="col-md-12 responsive contenedor-grilla"><span id="msg_error_grilla" class="help-block">&nbsp;</span></div>
        </div>
        <!-- Grilla principal de los Proyectos -->
        <div class="row">
            <div id="grillaProyectosContainer" class="col-md-12 responsive contenedor-grilla">
                <!-- La grilla se genera dinamicamente -->
            </div>
        </div>

        <!-- Grilla de los documentos pertenecientes al expediente actual + los reservados -->
        <?php
        // Sólo Perfil 1 ó 2 puede cargar un documento público
        if (isset($_SESSION['perfil2']) && ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2)) {?>
            <div class="row margen_y_5">
                <div class="col-12">
                    <!-- Botón para buscar y seleccionar el documento Público -->
                    <button id="btn_examinar_documento_unificado" type="button" class="btn btn-primary btn-sm" title="Cargar Documento">
                        <span class="glyphicon glyphicon-open"></span>&nbsp;Cargar Documento de Trabajo
                    </button>
                </div>
            </div>
        <?php } ?>
        <div class="row">
             <div class="col-md-6">
            <?php
            // Sólo Perfil 1 ó 2 puede cargar un documento público
            if (isset($_SESSION['perfil2']) && ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2)) {?>
                <div style="border-bottom:1px solid #c0c0c0">
                    <strong>Documentos</strong>
                    <!-- Formulario para la carga de la digitalización -->
                    <form id="form_upload_digi_desde_solapa"
                          name="form_upload_digi_desde_solapa"
                          action="index.php?c=cargaproyectos&a=uploaddigitalizacionesdeproyectos"
                          method="POST"
                          class="form-horizontal"
                          enctype="multipart/form-data"
                          role="form">

                        <input type="hidden" id="digi_anio" name="digi_anio" value="" />
                        <input type="hidden" id="digi_tipo" name="digi_tipo" value="" />
                        <input type="hidden" id="digi_numero" name="digi_numero" value="" />
                        <input type="hidden" id="digi_cuerpo" name="digi_cuerpo" value="" />
                        <input type="hidden" id="digi_alcance" name="digi_alcance" value="" />

                        <!-- Se oculta el input 'file' con el que se carga la Digitalización -->
                        <input type="file" accept="application/pdf" id="f_digitalizacion" name="f_digitalizacion" value="" style="display:none;" />

                        <!-- Botón para buscar y seleccionar el documento Público -->
                        <button id="btn_examinar_digitalizacion" type="button" class="btn btn-primary btn-sm pull-right" title="Cargar Digitalizaci&oacute;n">
                            <span class="glyphicon glyphicon-open"></span>&nbsp;Cargar Digitalizaci&oacute;n
                        </button>
                    </form>
                </div>
            <?php } else {?>
                    <br><h5><strong>Documentos</strong><h5>
            <?php }?>
                <br><br>
                <div id="grillaDocumentosContainer" class="col-md-12 responsive contenedor-grilla">
                <!-- La grilla de los documentos se genera dinamicamente -->
                </div>
            </div>
            <?php
            // El perfil 4 NO debe ver los documentos reservados
            if (isset($_SESSION['perfil2']) && $_SESSION['perfil2'] != 4) {?>
            <div class="col-md-6">
                <?php
                // Sólo Perfil 1 ó 2 puede cargar un documento reservado
            	if (isset($_SESSION['perfil2']) && ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2)) {?>
                    <div style="border-bottom:1px solid #c0c0c0">
                        <strong>Documentos&nbsp;<a href="http://www.concejomdp.gov.ar/biblioteca/legislacion/ACCESO%20A%20LA%20INFORMACION%20HCD%20-%20D-1404.pdf?v=<?php echo mt_rand(); ?>" target="_blank"> (Art.11 Decreto 1404)</a></strong>
                       <!-- Formulario para la carga de la digitalización reservada -->
                        <form id="form_upload_digi_reservada_desde_solapa"
                              name="form_upload_digi_reservada_desde_solapa"
                              action="index.php?c=cargaproyectos&a=uploaddigitalizacionesreservadasdeproyectos"
                              method="POST"
                              class="form-horizontal"
                              enctype="multipart/form-data"
                              role="form">

                            <input type="hidden" id="digi_reservada_anio" name="digi_reservada_anio" value="" />
                            <input type="hidden" id="digi_reservada_tipo" name="digi_reservada_tipo" value="" />
                            <input type="hidden" id="digi_reservada_numero" name="digi_reservada_numero" value="" />
                            <input type="hidden" id="digi_reservada_cuerpo" name="digi_reservada_cuerpo" value="" />
                            <input type="hidden" id="digi_reservada_alcance" name="digi_reservada_alcance" value="" />

                            <!-- Se oculta el input 'file' con el que se carga la Digitalización -->
                            <input type="file" accept="application/pdf" id="f_digitalizacion_reservada" name="f_digitalizacion" value="" style="display:none;" />

                            <!-- Botón para buscar y seleccionar el documento Público -->
                            <button id="btn_examinar_digitalizacion_reservada" type="button" class="btn btn-primary btn-sm pull-right" title="Cargar Digitalizaci&oacute;n">
                                <span class="glyphicon glyphicon-open"></span>&nbsp;Cargar Digitalizaci&oacute;n
                            </button>
                        </form>
                    </div>
                <?php } else {?>
                    <br><h5><strong>Documentos&nbsp;<a href="http://www.concejomdp.gov.ar/biblioteca/legislacion/ACCESO%20A%20LA%20INFORMACION%20HCD%20-%20D-1404.pdf?v=<?php echo mt_rand(); ?>" target="_blank"> (Art.11 Decreto 1404)</a></strong><h5>
                <?php }?>
                <br><br>
                <div id="grillaReservadosContainer" class="col-md-12 responsive contenedor-grilla pull-right">
                    <!-- La grilla de los documentos Reservados se genera dinamicamente -->
                </div>
            </div>
        <?php }?>
        </div>
    </div>
    <?php $this->incluirPlantilla('expedientes/expedientes_vista_previa_expediente.php');?>
</div>
<script type="text/javascript">
    var perfil_usuario_actual = <?php echo (isset($_SESSION['perfil2'])) ? $_SESSION['perfil2'] : 0; ?>;
    var digitalizacion_existente = '<?php echo (isset($_SESSION['digitalizacion_existente'])) ? $_SESSION['digitalizacion_existente'] : 0; ?>';
    var doc_publico_existente = '<?php echo (isset($_SESSION['doc_publico_existente'])) ? $_SESSION['doc_publico_existente'] : 0; ?>';
    var digitalizacion_reservada_existente = '<?php echo (isset($_SESSION['digitalizacion_reservada_existente'])) ? $_SESSION['digitalizacion_reservada_existente'] : 0; ?>';
    var doc_reservado_existente = '<?php echo (isset($_SESSION['doc_reservado_existente'])) ? $_SESSION['doc_reservado_existente'] : 0; ?>';
</script>
<?php
// Se eliminan de la sesión
$_SESSION['digitalizacion_existente'] = '';
$_SESSION['doc_publico_existente'] = '';
$_SESSION['digitalizacion_reservada_existente'] = '';
$_SESSION['doc_reservado_existente'] = '';
?>