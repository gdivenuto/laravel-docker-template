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

// Información del giro
$giro = $this->vista->data['giro'];

// Transformación del giro a JSON para tenerlo disponible en la vista como giro.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonGiro = JsonHelper::get()->serializar($giro);

// Listado para la carga del combo de Comisiones
$listado_comisiones = $this->vista->data['listado_comisiones'];

// Para mostrar correctamente las fechas del Giro 
$v_fecha_entrada_giro = ( !is_null($giro->fecha_entrada_giro) ) ? Validator::get()->convertirAFechaVista($giro->fecha_entrada_giro) : '';
$v_fecha_salida_giro = ( !is_null($giro->fecha_salida_giro) ) ? Validator::get()->convertirAFechaVista($giro->fecha_salida_giro) : '';

$comisiones_sugeridas = '';
$cantidad_comisiones = count($listado_comisiones);
for ($i=0; $i < $cantidad_comisiones; $i++)
    // Si posee la descripción, se utilizará como sugerencia
    $comisiones_sugeridas .= ($listado_comisiones[$i]->descripcion_grp != '') ? '"'.$listado_comisiones[$i]->codigo_grp.'-'.$listado_comisiones[$i]->descripcion_grp.'",' : '';

$this->generarModalDialog();
?>
<div class="row espacio_interno_5 borde-inferior">
    <div class="col-md-12"><span class="glyphicon glyphicon-edit"></span>&nbsp;Edici&oacute;n del Giro para <?php echo ($giro->tipo == 'E') ? 'el Expediente' : 'la Nota'; ?>:</div>
</div>

<form id="form_edicion_giro" name="form_edicion_giro" class="form-horizontal" role="form">

    <?php $this->incluirPlantilla('expedientes/expedientes_cabecera_formulario_edicion.php'); ?>

    <!-- Datos restantes -->
    <div class="row">
        <div class="col-md-12">    
            <div class="form-group">
                <label for="f_orden_giro" class="col-xs-3 col-sm-2 col-md-2 control-label">Orden:</label>
                <div class="col-xs-4 col-sm-2 col-md-2">
                    <input id="f_orden_giro" name="f_orden_giro" type="text" class="form-control input-sm" readonly placeholder="Autom&aacute;tico" value="">
                </div>
            </div>
            
            <div class="form-group">
                <label for="f_comision" class="col-xs-3 col-sm-2 col-md-2 control-label">Comisi&oacute;n:</label>
                <div class="col-xs-9 col-sm-10 col-md-8">
                    <div class="input-group">
                        <select id="f_comision" name="f_comision" class="form-control input-sm">
                            <option value="0">---</option><!-- Se muestra aquí 'descripcion_grp' -->
                            <?php
                            $this->renderOptionList(
                                $listado_comisiones,     // coleccion
                                'codigo_grp',            // valor del combo
                                array('tipo_grp', 'codigo_grp', 'descripcion_grp'), // información a elegir //'descripcion_grp',       // descripcion
                                $giro->comision_codigo); // default selected
                            ?>
                        </select>
                        <span class="input-group-addon input-sm">
                            <a href="javascript:mostrarModalComisionAutosugerido();"><span class="glyphicon glyphicon-search"></span></a>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="v_fecha_entrada_giro" class="col-xs-3 col-sm-2 col-md-2 control-label">Fecha entrada:</label>
                <div class="col-xs-3 col-sm-1 col-md-1">
                    <input id="v_fecha_entrada_giro" name="v_fecha_entrada_giro" class="form-control input-sm criterio_busqueda_campo_fecha espacio_interno_izq_5" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_entrada_giro; ?>">
                </div>
                <input type="hidden" id="f_fecha_entrada_giro" name="f_fecha_entrada_giro" value="" />

                <label for="v_fecha_salida_giro" class="col-xs-3 col-sm-2 col-md-2 col-lg-2 control-label espacio_interno_der_5">Fecha salida:</label>
                <div class="col-xs-2 col-sm-1 col-md-1 sin_padding_izq">
                    <input id="v_fecha_salida_giro" name="v_fecha_salida_giro" class="form-control input-sm criterio_busqueda_campo_fecha espacio_interno_izq_5" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_salida_giro; ?>">
                </div>
                <input type="hidden" id="f_fecha_salida_giro" name="f_fecha_salida_giro" value="" />
            </div>
            <div class="form-group">
                <label for="f_dictamen_giro" class="col-xs-3 col-sm-2 col-md-2 control-label">Dictamen:</label>
                <div class="col-xs-9 col-sm-10 col-md-8">
                    <input id="f_dictamen_giro" name="f_dictamen_giro" type="text" class="form-control input-sm" value="">
                </div>
            </div>
            
            <div class="form-group">
                <label for="f_observaciones_giro" class="col-xs-3 col-sm-2 col-md-2 control-label">Observaciones:</label>
                <div class="col-xs-12 col-sm-10 col-md-8">
                    <textarea id="f_observaciones_giro" name="f_observaciones_giro" class="form-control" rows="9"></textarea>
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

    <!-- Modal para el autosugerido de Comisiones -->
    <div class="modal fade" id="modalComisionAutosugerido" tabindex="-8" role="dialog" aria-labelledby="modalComisionAutosugerido" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Buscar Comisi&oacute;n</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group">
                            <div class="col-xs-8 col-sm-10 col-md-10 col-lg-10">
                                <input type="text" id="modal_comision_sugerida" name="modal_comision_sugerida" value="" class="form-control input-sm">
                            </div>
                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                <button type="button" id="btCargarComisionSugerida" class="btn btn-primary btn-sm" title="Asignar">
                                    <span class="glyphicon glyphicon-list-alt"></span>&nbsp;Asignar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    // Volcado de datos JSON
    var giro = <?php echo $jsonGiro; ?>;

    $( function() {
        var comisiones_sugeridas = [<?php echo $comisiones_sugeridas; ?>];

        $('#modal_comision_sugerida').autocomplete({
            source: comisiones_sugeridas
        });
    });
</script>