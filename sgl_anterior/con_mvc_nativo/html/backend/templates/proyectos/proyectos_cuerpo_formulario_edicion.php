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

// Información del proyecto
$proyecto = $this->vista->data['proyecto'];

// Transformación del proyecto a JSON para tenerlo disponible en la vista como proyecto.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonProyecto = JsonHelper::get()->serializar($proyecto);

// Listados para la carga de los combos
$listado_codproyectos = $this->vista->data['listado_codproyectos'];

$this->generarModalDialog();
?>
<div class="row espacio_interno_5 borde-inferior">
    <div class="col-md-12"><span class="glyphicon glyphicon-edit"></span>&nbsp;Edici&oacute;n del Proyecto para <?php echo ($proyecto->tipo == 'E') ? 'el Expediente' : 'la Nota'; ?>:</div>
</div>
<form id="form_edicion_proyecto" name="form_edicion_proyecto" class="form-horizontal" role="form">

    <?php $this->incluirPlantilla('expedientes/expedientes_cabecera_formulario_edicion.php'); ?>

    <!-- Datos restantes -->
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="f_id_codproyecto" class="col-xs-2 col-sm-2 col-md-2 control-label">Tipo:</label>
                <div class="col-xs-10 col-sm-3 col-md-4">
                    <select id="f_id_codproyecto" name="f_id_codproyecto" class="form-control input-sm">
                        <option value="0">---</option><!-- Se muestra aquí 'descripcion_proyecto' -->
                        <?php
                        $this->renderOptionList(
                            $listado_codproyectos,      // coleccion
                            'id_codproyecto',           // valor del combo
                            'descripcion_proyecto',     // descripcion
                            $proyecto->id_codproyecto); // default selected
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="f_orden_proyecto" class="col-xs-2 col-sm-2 col-md-2 control-label">Orden:</label>
                <div class="col-xs-4 col-sm-2 col-md-2">
                    <input id="f_orden_proyecto" name="f_orden_proyecto" type="text" class="form-control input-sm" readonly value="" placeholder="Autom&aacute;tico">
                </div>
            </div>
            <div class="form-group">
                <label for="f_extracto" class="col-xs-2 col-sm-2 col-md-2 control-label">Extracto:</label>
                <div class="col-xs-12 col-sm-10 col-md-8">
                    <textarea id="f_extracto" name="f_extracto" class="form-control" rows="7"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="f_observaciones_proyecto" class="col-xs-2 col-sm-2 col-md-2 control-label">Observaciones:</label>
                <div class="col-xs-12 col-sm-10 col-md-8">
                    <textarea id="f_observaciones_proyecto" name="f_observaciones_proyecto" class="form-control" rows="7"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-9 col-xs-offset-3 col-sm-10 col-sm-offset-2 col-md-10 col-md-offset-2">
                    <button id="btn_guardar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
                    <button id="btn_cancelar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    // Volcado de datos JSON
    var proyecto = <?php echo $jsonProyecto; ?>;
</script>