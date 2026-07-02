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
//********** Se reciben los parámetros del Controlador ******************************************
// Se reduce el nombre de la variable 'data' correspondiente a los criterios de la búsqueda
$f_fecha_desde    = $this->vista->data['parametros_expedientes_en_comision']['f_fecha_desde'];
$f_fecha_hasta    = $this->vista->data['parametros_expedientes_en_comision']['f_fecha_hasta'];
$f_fecha_comision = $this->vista->data['parametros_expedientes_en_comision']['f_fecha_comision'];
$f_fecha_listado  = $this->vista->data['parametros_expedientes_en_comision']['f_fecha_listado'];
$f_comision       = $this->vista->data['parametros_expedientes_en_comision']['f_comision'];
$f_estado         = $this->vista->data['parametros_expedientes_en_comision']['f_estado'];
$f_vencidos       = $this->vista->data['parametros_expedientes_en_comision']['f_vencidos'];

// Para mostrar en los Calendarios de fecha Desde, fecha Hasta, fecha de Listado y fecha de Comisión
$v_fecha_desde    = ( !is_null($f_fecha_desde) ) ? Validator::get()->convertirAFechaVista($f_fecha_desde) : '';
$v_fecha_hasta    = ( !is_null($f_fecha_hasta) ) ? Validator::get()->convertirAFechaVista($f_fecha_hasta) : '';
$v_fecha_listado  = ( !is_null($f_fecha_listado) ) ? Validator::get()->convertirAFechaVista($f_fecha_listado) : '';
$v_fecha_comision = ( !is_null($f_fecha_comision) ) ? Validator::get()->convertirAFechaVista($f_fecha_comision) : '';

// Listados para cargar los combos del buscador
$listado_comisiones = $this->vista->data['listado_comisiones'];
$listado_codestados = $this->vista->data['listado_codestados'];

$comisiones_sugeridas = '';
$cantidad_comisiones = (isset($listado_comisiones)) ? count($listado_comisiones) : 0;
for ($i=0; $i < $cantidad_comisiones; $i++)
    // Si posee la descripción, se utilizará como sugerencia
    $comisiones_sugeridas .= ($listado_comisiones[$i]->descripcion_grp != '') ? '"'.$listado_comisiones[$i]->codigo_grp.'-'.$listado_comisiones[$i]->descripcion_grp.'",' : '';

$estados_sugeridos = '';
$cantidad_estados = (isset($listado_codestados)) ? count($listado_codestados) : 0;
for ($i=0; $i < $cantidad_estados; $i++)
    // Si posee el nombre, se utilizará como sugerencia
    $estados_sugeridos .= ($listado_codestados[$i]->nombre_estado != '') ? '"'.$listado_codestados[$i]->id_codestado.'-'.$listado_codestados[$i]->nombre_estado.'",' : '';

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12 titulo-codificadora">
        <span class="glyphicon glyphicon-file"></span>Expedientes en Comisi&oacute;n
    </div>
</div>
<div class="row">
    <!-- Criterio de búsqueda + Grilla -->
    <div class="col-md-12 col-lg-12 responsive">
        <div class="row borde-inferior">
            <!-- Formulario para el criterio de búsqueda -->
            <form id="form_expedientes_en_comision" name="form_expedientes_en_comision" class="form-horizontal" action="index.php?c=listadoexpedientesencomision&a=view" method="POST">
                
                <!-- Campo oculto que contendrá las comisiones elegidas en la modal -->
                <input type="hidden" id="f_comisiones_elegidas_en_modal" name="f_comisiones_elegidas_en_modal" value="" />
                
                <div class="col-sm-5 col-md-2 col-lg-3">
                    <!-- Fechas Desde y Hasta -->
                    <div class="form-group">
                        <!-- Calendario Desde -->
                        <label for="v_fecha_desde" class="col-xs-2 col-sm-2 col-md-4 col-lg-5 control-label">Desde:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-7">
                            <input type="text" id="v_fecha_desde" name="v_fecha_desde" class="form-control criterio_busqueda_campo_fecha input-sm" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_desde; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_desde" name="f_fecha_desde" value="<?php echo $f_fecha_desde; ?>" />
                    </div>
                    <div class="form-group">
                        <!-- Calendario Hasta -->
                        <label for="v_fecha_hasta" class="col-xs-2 col-sm-2 col-md-4 col-lg-5 control-label">Hasta:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-7">
                            <input type="text" id="v_fecha_hasta" name="v_fecha_hasta" class="form-control criterio_busqueda_campo_fecha input-sm" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_hasta; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_hasta" name="f_fecha_hasta" value="<?php echo $f_fecha_hasta; ?>" />
                    </div>
                </div>
                <div class="col-sm-6 col-md-5 col-lg-3">
                    <!-- Ingresados en la Comisión antes del... -->
                    <div class="form-group">
                        <label for="v_fecha_comision" class="col-xs-7 col-sm-8 col-md-8 col-lg-9 control-label">Ingresados en la Comisi&oacute;n antes del:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <input type="text" id="v_fecha_comision" name="v_fecha_comision" class="form-control criterio_busqueda_campo_fecha input-sm" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_comision; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_comision" name="f_fecha_comision" value="<?php echo $f_fecha_comision; ?>" />
                    </div>
                    <!-- Fecha utilizada para el cálculo de días de expedientes en Comisión -->
                    <div class="form-group">
                        <label for="v_fecha_listado" class="col-xs-7 col-sm-8 col-md-8 col-lg-9 control-label">Calcular d&iacute;as en comisi&oacute;n hasta el:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <input type="text" id="v_fecha_listado" name="v_fecha_listado" class="form-control criterio_busqueda_campo_fecha input-sm" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_listado; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_listado" name="f_fecha_listado" value="<?php echo $f_fecha_listado; ?>" />
                    </div>
                </div>
                <div class="col-sm-12 col-md-5 col-lg-6">
                    <!-- Comisión -->
                    <div class="form-group">
                        <label for="f_comision" class="col-xs-2 col-sm-1 col-md-2 col-lg-2 control-label">Comisi&oacute;n:</label>
                        <div class="col-xs-10 col-sm-11 col-md-10 col-lg-10">
                            <div class="input-group">
                                <select id="f_comision" name="f_comision" class="form-control input-sm">
                                    <option value="0">Seleccione</option>
                                    <?php for ($i=0; $i < $cantidad_comisiones; $i++) { $comision = &$listado_comisiones[$i]; ?>
                                        <option value="<?=$comision->codigo_grp;?>">
                                            <?=($comision->habilitado_grp == '1') ? '* ' : '';?><?=$comision->tipo_grp;?> - <?=$comision->codigo_grp;?> - <?=$comision->descripcion_grp;?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="input-group-addon input-sm">
                                    <a href="javascript:mostrarModalComisionAutosugerido();"><span class="glyphicon glyphicon-search"></span></a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Estado -->
                    <div class="form-group">
                        <label for="f_estado" class="col-xs-2 col-sm-1 col-md-2 col-lg-2 control-label">Estado:</label>
                        <div class="col-xs-10 col-sm-11 col-md-10 col-lg-10">
                            <div class="input-group">
                                <select id="f_estado" name="f_estado" class="form-control input-sm">
                                    <option value="0">Seleccione</option>
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
                 </div>
                
                <!-- Checkbox sólo vencidos -->
                <div class="col-sm-12 col-md-3 col-lg-5">
                    <label>
                        <strong>S&oacute;lo expedientes vencidos</strong>&nbsp;<input type="checkbox" id="f_vencidos" name="f_vencidos" value="0">
                    </label>
                </div>

                <!-- Botones: Buscar + Restablecer + Imprimir + Exportar a Texto + Volver a Expedientes -->
                <div class="col-sm-12 col-md-9 col-lg-7 text-center">
                    <button id="btn_buscar" name="btn_buscar" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-search"></span>&nbsp;Buscar</button>
                    <button id="btn_refrescar" name="btn_refrescar" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Restablecer</button>
                    <button id="btn_imprimir" name="btn_imprimir" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-print"></span>&nbsp;Imprimir</button>
                    <button id="btn_exportar_texto" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-export"></span>&nbsp;Exportar a Texto</button>
                    <button id="btn_volver" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes</button>
                </div>
                
                <!-- Modal de Comisiones para elegir varias y utilizarlas como criterio de búsqueda -->
                <div class="modal fade" id="modalComisiones" tabindex="-7" role="dialog" aria-labelledby="modalComisiones" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Seleccione las Comisiones que desea en la b&uacute;squeda.</h3>
                            </div>
                            <div class="modal-body" style="height: 410px;overflow-y: auto">
                                <div class="container-fluid">
                                    <label><input type="checkbox" id="modal_todas_comisiones" name="modal_todas_comisiones" value="" checked >&nbsp;Todas</label>
                                    <?php
                                    foreach ($listado_comisiones as $c) {
                                    ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="checkbox">
                                                    <label><input type="checkbox" class="class_modal_comisiones_elegidas" name="modal_comisiones_elegidas[]" value="<?php echo $c->codigo_grp; ?>" checked >&nbsp;<?php echo $c->tipo_grp.', '.$c->codigo_grp.', '.$c->descripcion_grp; ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-primary btn-sm" id="btn_siguiente">Siguiente</button>
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
        </div>
        <div id="msg_error_form"></div>
        <div class="row">
            <div id="tablaFichasContainer" class="col-md-12 responsive">
                <!-- El listado se genera dinámicamente -->
            </div>
        </div>
    </div>
</div>
<script>
    $( function() {
        var comisiones_sugeridas = [<?php echo $comisiones_sugeridas; ?>];
        var estados_sugeridos = [<?php echo $estados_sugeridos; ?>];

        $('#modal_comision_sugerida').autocomplete({
            source: comisiones_sugeridas
        });

        $('#modal_estado_sugerido').autocomplete({
            source: estados_sugeridos
        });
    });
</script>