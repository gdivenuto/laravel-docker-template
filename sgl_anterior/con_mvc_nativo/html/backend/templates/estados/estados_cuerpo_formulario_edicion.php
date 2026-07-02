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

// Información del Estado
$estado = $this->vista->data['estado'];

// Transformación del estado a JSON para tenerlo disponible en la vista como estado.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonEstado = JsonHelper::get()->serializar($estado);

// Listado para la carga del combo de Codificadora de Estados
$listado_codestados = $this->vista->data['listado_codestados'];

// Para mostrar correctamente la fecha del Estado 
$v_fecha_estado = ( !is_null($estado->fecha_estado) ) ? Validator::get()->convertirAFechaVista($estado->fecha_estado) : '';

$estados_sugeridos = '';
$cantidad_estados = (isset($listado_codestados)) ? count($listado_codestados) : 0;
for ($i=0; $i < $cantidad_estados; $i++)
    // Si posee el nombre, se utilizará como sugerencia
    $estados_sugeridos .= ($listado_codestados[$i]->nombre_estado != '') ? '"'.$listado_codestados[$i]->id_codestado.'-'.$listado_codestados[$i]->nombre_estado.'",' : '';

$this->generarModalDialog();
?>
<div class="row espacio_interno_5 borde-inferior">
    <div class="col-md-12"><span class="glyphicon glyphicon-edit"></span>&nbsp;Edici&oacute;n del Estado para <?php echo ($estado->tipo == 'E') ? 'el Expediente' : 'la Nota'; ?>:</div>
</div>
<form id="form_edicion_estado" name="form_edicion_estado" class="form-horizontal" role="form">
    
    <?php $this->incluirPlantilla('expedientes/expedientes_cabecera_formulario_edicion.php'); ?>

    <div class="row">
        <div class="col-md-12">
            <!-- Datos restantes -->
            <div class="form-group">
                <label for="f_orden_proyecto" class="col-xs-2 col-sm-2 col-md-2 control-label">Orden:</label>
                <div class="col-xs-4 col-sm-2 col-md-2">
                    <input id="f_orden_estado" name="f_orden_estado" type="text" class="form-control input-sm" readonly placeholder="Autom&aacute;tico" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="v_fecha_estado" class="col-xs-2 col-sm-2 col-md-2 control-label">Fecha:</label>
                <div class="col-xs-2 col-sm-2 col-md-1">
                    <input  id="v_fecha_estado" name="v_fecha_estado" 
                            class="form-control input-sm criterio_busqueda_campo_fecha espacio_interno_izq_5" type="text" placeholder="dd/mm/aaaa" 
                            <?=(SessionController::get()->obtener(SAVE_ACTION) == 'editar') ? 'readonly' : '';?>
                            value="<?php echo $v_fecha_estado; ?>">
                </div>
                <input type="hidden" id="f_fecha_estado" name="f_fecha_estado" value="" />
            </div>   
            <div class="form-group">
                <label for="f_id_codestado" class="col-xs-2 col-sm-2 col-md-2 control-label">Estado:</label>
                <div class="col-xs-10 col-sm-10 col-md-8">
                    <div class="input-group">
                        <select id="f_id_codestado" name="f_id_codestado" class="form-control input-sm">
                            <option value="0">---</option><!-- Se muestra aquí 'nombre_estado' -->
                            <?php for ($i=0; $i < $cantidad_estados; $i++) { 
                                $estado = &$listado_codestados[$i]; ?>
                                <option value="<?=$estado->id_codestado;?>">
                                    <?=($estado->habilitado_codestado == '1') ? '* ' : '';?>
                                    <?=$estado->id_codestado;?> - <?=$estado->nombre_estado;?>
                                </option>
                            <?php } ?>
                        </select>
                        <span class="input-group-addon input-sm">
                            <a href="javascript:mostrarModalEstadoAutosugerido();"><span class="glyphicon glyphicon-search"></span></a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="f_observaciones_estado" class="col-xs-3 col-sm-2 col-md-2 control-label">Observaciones:</label>
                <div class="col-xs-12 col-sm-8 col-md-8">
                    <textarea id="f_observaciones_estado" name="f_observaciones_estado" class="form-control" rows="11"></textarea>
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

    <!-- Modal para el autosugerido de Estados -->
    <div class="modal fade" id="modalEstadoAutosugerido" tabindex="-9" role="dialog" aria-labelledby="modalEstadoAutosugerido" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Buscar Estado</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group">
                            <div class="col-xs-8 col-sm-10 col-md-10 col-lg-10">
                                <input type="text" id="modal_estado_sugerido" name="modal_estado_sugerido" value="" class="form-control input-sm">
                            </div>
                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                <button type="button" id="btCargarEstadoSugerido" class="btn btn-primary btn-sm" title="Asignar">
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
    var estado = <?php echo $jsonEstado; ?>;

    $( function() {
        var estados_sugeridos = [<?php echo $estados_sugeridos; ?>];

        $('#modal_estado_sugerido').autocomplete({
            source: estados_sugeridos
        });
    });
</script>