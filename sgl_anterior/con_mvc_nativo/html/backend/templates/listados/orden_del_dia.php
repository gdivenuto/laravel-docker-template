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
$f_fecha_desde = $this->vista->data['parametros_listado_orden_del_dia']['f_fecha_desde'];
$f_fecha_hasta = $this->vista->data['parametros_listado_orden_del_dia']['f_fecha_hasta'];
$f_comision    = $this->vista->data['parametros_listado_orden_del_dia']['f_comision'];

// Para mostrar en los Calendarios de fecha Desde, fecha Hasta, fecha de Listado y fecha de Comisión
$v_fecha_desde = ( !is_null($f_fecha_desde) ) ? Validator::get()->convertirAFechaVista($f_fecha_desde) : '';
$v_fecha_hasta = ( !is_null($f_fecha_hasta) ) ? Validator::get()->convertirAFechaVista($f_fecha_hasta) : '';

// Listado para cargar el combo de Comisiones del criterio de búsqueda
$listado_comisiones  = $this->vista->data['listado_comisiones'];
$comisiones_sugeridas = '';
$cantidad_comisiones = count($listado_comisiones);
for ($i=0; $i < $cantidad_comisiones; $i++)
    // Si posee la descripción, se utilizará como sugerencia
    $comisiones_sugeridas .= ($listado_comisiones[$i]->descripcion_grp != '') ? '"'.$listado_comisiones[$i]->codigo_grp.'-'.$listado_comisiones[$i]->descripcion_grp.'",' : '';

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12 titulo-codificadora">
        <span class="glyphicon glyphicon-file"></span><strong>Orden del D&iacute;a de Comisi&oacute;n</strong>
    </div>
</div>
<div class="row borde-inferior">
    <!-- Formulario para el criterio de búsqueda -->
    <form id="form_orden_del_dia" name="form_orden_del_dia" class="form-horizontal" action="index.php?c=listadoordendeldia&a=view" method="POST">
        <!-- Fechas Desde y Hasta -->
        <div class="col-sm-5 col-md-4 col-lg-3">
            <div class="form-group">
                <!-- Calendario Desde -->
                <label for="v_fecha_desde" class="col-xs-2 col-sm-2 col-md-4 control-label">Desde:</label>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                    <input type="text" id="v_fecha_desde" name="v_fecha_desde" class="form-control criterio_busqueda_campo_fecha input-sm" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_desde; ?>">
                </div>
                <input type="hidden" id="f_fecha_desde" name="f_fecha_desde" value="<?php echo $f_fecha_desde; ?>" />
                <!-- Calendario Hasta -->
                <label for="v_fecha_hasta" class="col-xs-2 col-sm-2 col-md-2 col-lg-2 col-xs-offset-1 col-sm-offset-2 col-md-offset-2 control-label">Hasta:</label>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                    <input type="text" id="v_fecha_hasta" name="v_fecha_hasta" class="form-control criterio_busqueda_campo_fecha input-sm" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_hasta; ?>">
                </div>
                <input type="hidden" id="f_fecha_hasta" name="f_fecha_hasta" value="<?php echo $f_fecha_hasta; ?>" />
            </div>
        </div>
        <!-- Comisión -->
        <div class="col-sm-7 col-md-8 col-lg-9">
            <div class="form-group">
                <label for="f_comision" class="col-xs-2 col-sm-2 col-md-4 control-label">Comisi&oacute;n:</label>
                <div class="col-xs-10 col-sm-10 col-md-8">
                    <div class="input-group">
                        <select id="f_comision" name="f_comision" class="form-control input-sm">
                            <option value="0">Seleccione</option>
                            <?php for ($i=0; $i < $cantidad_comisiones; $i++) { $comision = &$listado_comisiones[$i]; ?>
                                <option value="<?=$comision->codigo_grp;?>">
                                    <?=($comision->habilitado_grp == '1') ? '* ' : '';?><?=$comision->tipo_grp;?> - <?=$comision->codigo_grp;?> - <?=$comision->descripcion_grp;?>
                                </option>
                            <?php } ?>
                            <?php
                            // $this->renderOptionList(
                            //     $listado_comisiones, // coleccion
                            //     'codigo_grp',        // valor del combo
                            //     array('tipo_grp', 'codigo_grp', 'descripcion_grp'), // información para elegir
                            //     $f_comision);        // default selected
                            ?>
                        </select>
                        <span class="input-group-addon input-sm">
                            <a href="javascript:mostrarModalComisionAutosugerido();"><span class="glyphicon glyphicon-search"></span></a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones: Buscar + Restablecer + Imprimir + Exportar a Texto + Volver a Expedientes -->
        <div class="col-sm-12 col-md-12 col-lg-12 text-center">
            <button id="btn_buscar" name="btn_buscar" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-search"></span>&nbsp;Buscar</button>
            <button id="btn_refrescar" name="btn_refrescar" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Restablecer</button>
            <button id="btn_imprimir" name="btn_imprimir" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-print"></span>&nbsp;Imprimir</button>
            <button id="btn_exportar_texto" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-export"></span>&nbsp;Exportar a Texto</button>
            <button id="btn_volver" name="btn_volver" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes</button>
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
</div>
<div class="row">
    <div id="tablaFichasContainer" class="col-md-12 responsive">
        <!-- El listado se genera dinámicamente -->
    </div>
</div>
<script>
    $( function() {
        var comisiones_sugeridas = [<?php echo $comisiones_sugeridas; ?>];
        $('#modal_comision_sugerida').autocomplete({
            source: comisiones_sugeridas
        });
    });
</script>