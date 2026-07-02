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
        <!-- Solapas + Botón Habilitar -->
        <div class="row">
            <!-- Solapas -->
            <div class="col-sm-11 responsive contenedor-solapas">
                <?php $this->incluirPlantilla('expedientes/expedientes_solapas.php');?>
            </div>
            <?php
            // Sólo Perfil 1 ó 2 puede habilitar la Participación
            if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) {
                
                // Sólo Informática o Mesa de Entrada pueden verlo 
                // (perfil1 es del Subsistema de Administración)    
                if ($_SESSION['perfil1'] == 14 || $_SESSION['perfil1'] == 24) { 
            ?>
                    <!-- Botón para habilitar el expediente en la participación -->
                    <div class="col-sm-1 alineacion margen_sup_5">
                        <button id="btn_habilitar_participacion" type="button" class="btn btn-primary btn-sm boton-adaptado">
                            <span class="glyphicon glyphicon-plus"></span>&nbsp;Habilitar Participaci&oacute;n
                        </button>
                    </div>
            <?php
                }
            }
            ?>
        </div>
        <?php
        // Sólo Perfil 1 ó 2 puede habilitar la Participación
        if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) {
        ?>
        <div class="row">
            <!-- Botones: Moderar + Cargar Propuesta + Ver Propuesta + Imprimir incorporadas -->
            <div class="col-sm-11 alineacion">

                <?php // Sólo Informática o Mesa de Entrada pueden verlo
                // (perfil1 es del Subsistema de Administración) 
                if ($_SESSION['perfil1'] == 14 || $_SESSION['perfil1'] == 24) {
                ?>
                    <form id="form_moderacion"
                          name="form_moderacion"
                          action="<?=URL_KRAKEN_BASE;?>administracion/abms/index.php?controlador=participaciones&accion=listarParticipaciones"
                          method="POST"
                          class="form-horizontal"
                          role="form">

                        <input type="hidden" id="moderacion_anio" name="anio" value="" />
                        <input type="hidden" id="moderacion_tipo" name="tipo" value="" />
                        <input type="hidden" id="moderacion_numero" name="numero" value="" />
                        <input type="hidden" id="moderacion_cuerpo" name="cuerpo" value="" />
                        <input type="hidden" id="moderacion_alcance" name="alcance" value="" />

                        <!-- Botón para MODERAR las participaciones -->
                        <a id="btn_moderar" name="btn_moderar" class="btn btn-primary btn-sm boton-adaptado" title="Moderar">
                            <span class="glyphicon glyphicon-edit"></span>&nbsp;Moderar
                        </a>
                    </form>
                <?php }?>

                <!-- Formulario para la carga de la PROPUESTA -->
                <form id="form_upload_propuesta"
                      name="form_upload_propuesta"
                      action="index.php?c=participaciones&a=uploadpropuesta"
                      method="POST"
                      class="form-horizontal"
                      enctype="multipart/form-data"
                      role="form">

                    <input type="hidden" id="propuesta_anio" name="propuesta_anio" value="" />
                    <input type="hidden" id="propuesta_tipo" name="propuesta_tipo" value="" />
                    <input type="hidden" id="propuesta_numero" name="propuesta_numero" value="" />
                    <input type="hidden" id="propuesta_cuerpo" name="propuesta_cuerpo" value="" />
                    <input type="hidden" id="propuesta_alcance" name="propuesta_alcance" value="" />

                    <!-- Se oculta el input 'file' con el que se carga la Propuesta -->
                    <input type="file" accept="application/pdf" id="f_propuesta" name="f_propuesta" value="" style="display:none;" />

                    <!-- Botón para buscar y seleccionar el documento Público -->
                    <button id="btn_examinar_propuesta" type="button" class="btn btn-primary btn-sm pull-right" title="Cargar Propuesta">
                        <span class="glyphicon glyphicon-open"></span>&nbsp;Cargar Propuesta
                    </button>
                </form>

                <!-- Enlace a la Propuesta (pdf) -->
                <a id="enlace_propuesta" href="" target="_blank" class="btn btn-primary btn-sm boton-adaptado">
                    <span class="glyphicon glyphicon-file"></span>&nbsp;Ver Propuesta
                </a>

                <!-- Botón para IMPRIMIR las participaciones incorporadas -->
                <a id="btn_informe_participaciones" href="" target="_blank" name="btn_informe_participaciones" class="btn btn-primary btn-sm boton-adaptado" title="Imprimir">
                    <span class="glyphicon glyphicon-print"></span>&nbsp;Imprimir incorporadas
                </a>
            </div>
        </div>
        <?php } ?>
        
        <!-- Zona secundaria de errores -->
        <div id="row_error_grilla" class="row">
            <div class="col-md-12 responsive contenedor-grilla"><span id="msg_error_grilla" class="help-block">&nbsp;</span></div>
        </div>
        <!-- Grilla principal de las Participaciones -->
        <div class="row">
            <div id="grillaParticipacionesContainer" class="col-md-12 responsive contenedor-grilla">
                <!-- La grilla se genera dinamicamente -->
            </div>
        </div>

    </div>
    <?php $this->incluirPlantilla('expedientes/expedientes_vista_previa_expediente.php');?>
</div>
<script type="text/javascript">
    var perfil_usuario_actual = <?=(isset($_SESSION['perfil2'])) ? $_SESSION['perfil2'] : 0;?>;

    var url_base = '<?=URL_KRAKEN_BASE;?>';
</script>