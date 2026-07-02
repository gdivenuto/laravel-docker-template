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
$f_fecha_desde = $this->vista->data['parametros_expedientes_para_expurgo']['f_fecha_desde'];
$f_fecha_hasta = $this->vista->data['parametros_expedientes_para_expurgo']['f_fecha_hasta'];
$f_estado      = $this->vista->data['parametros_expedientes_para_expurgo']['f_estado'];

// Para mostrar en los Calendarios de fecha Desde, fecha Hasta, fecha de Listado y fecha de Comisión
$v_fecha_desde = ( !is_null($f_fecha_desde) ) ? Validator::get()->convertirAFechaVista($f_fecha_desde) : '';
$v_fecha_hasta = ( !is_null($f_fecha_hasta) ) ? Validator::get()->convertirAFechaVista($f_fecha_hasta) : '';

// Listado para cargar el combo de Estados
$listado_codestados = $this->vista->data['listado_codestados'];

$estados_sugeridos = '';
$cantidad_estados = (isset($listado_codestados)) ? count($listado_codestados) : 0;
for ($i=0; $i < $cantidad_estados; $i++)
    // Si posee el nombre, se utilizará como sugerencia
    $estados_sugeridos .= ($listado_codestados[$i]->nombre_estado != '') ? '"'.$listado_codestados[$i]->id_codestado.'-'.$listado_codestados[$i]->nombre_estado.'",' : '';

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12 titulo-codificadora">
        <span class="glyphicon glyphicon-file"></span><strong>Expedientes para Expurgo</strong>
    </div>
</div>
<div class="row">
    <!-- Criterio de búsqueda + Grilla -->
    <div class="col-md-12 responsive">
        <div class="row borde-inferior">

            <!-- Formulario para el criterio de búsqueda -->
            <form id="form_expedientes_para_expurgo" name="form_expedientes_para_expurgo" class="form-horizontal" action="index.php?c=listadoexpedientesparaexpurgo&a=view" method="POST">
                
                 <div class="col-sm-5 col-md-4 col-lg-3">
                    <!-- Fechas Desde y Hasta -->
                    <div class="form-group">
                        <label for="v_fecha_desde" class="col-xs-2 col-sm-2 col-md-4 control-label">Desde:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <input type="text" id="v_fecha_desde" name="v_fecha_desde" class="form-control criterio_busqueda_campo_fecha input-sm" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_desde; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_desde" name="f_fecha_desde" value="<?php echo $f_fecha_desde; ?>" />
                        
                        <label for="v_fecha_hasta" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 col-xs-offset-1 col-sm-offset-2 col-md-offset-2 control-label">Hasta:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <input type="text" id="v_fecha_hasta" name="v_fecha_hasta" class="form-control criterio_busqueda_campo_fecha input-sm" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_hasta; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_hasta" name="f_fecha_hasta" value="<?php echo $f_fecha_hasta; ?>" />
                    </div>
                </div>
                <div class="col-sm-7 col-md-8 col-lg-8">
                    <label for="f_estado" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 control-label">Estado:</label>
                    <div class="col-xs-10 col-sm-10 col-md-8">
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
                <!-- Botones: Buscar + Restablecer + Imprimir + Exportar a Texto -->
                <div class="col-sm-12 col-md-12 col-lg-12 text-center">
                    <button id="btn_buscar" name="btn_buscar" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-search"></span>&nbsp;Buscar</button>
                    <button id="btn_refrescar" name="btn_refrescar" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Restablecer</button>
                    <button id="btn_imprimir" name="btn_imprimir" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-print"></span>&nbsp;Imprimir</button>
                    <button id="btn_exportar_texto" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-export"></span>&nbsp;Exportar a Texto</button>
                    <button id="btn_exportar_planilla" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo btn_listado_ancho_fijo_mas_largo"><span class="glyphicon glyphicon-export"></span>&nbsp;Exportar a Planilla C&aacute;lculo</button>
                    <button id="btn_volver" name="btn_volver" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes</button>
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
        <div class="row">
            <div id="tablaFichasContainer" class="col-md-12 responsive">
                <!-- El listado se genera dinámicamente -->
            </div>
        </div>
    </div>
</div>
<script>
    $( function() {
        var estados_sugeridos = [<?php echo $estados_sugeridos; ?>];
        $('#modal_estado_sugerido').autocomplete({
            source: estados_sugeridos
        });
    });
</script>