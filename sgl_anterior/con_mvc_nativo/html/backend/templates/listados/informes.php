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
$f_fecha_desde    = $this->vista->data['parametros_listado_informes']['f_fecha_desde'];
$f_fecha_hasta    = $this->vista->data['parametros_listado_informes']['f_fecha_hasta'];
$f_fecha_listado  = $this->vista->data['parametros_listado_informes']['f_fecha_listado'];
$f_comision       = $this->vista->data['parametros_listado_informes']['f_comision'];
$f_vencidos       = $this->vista->data['parametros_listado_informes']['f_vencidos'];

// Para mostrar en los Calendarios de fecha Desde, fecha Hasta y fecha de Listado
$v_fecha_desde    = ( !is_null($f_fecha_desde) ) ? Validator::get()->convertirAFechaVista($f_fecha_desde) : '';
$v_fecha_hasta    = ( !is_null($f_fecha_hasta) ) ? Validator::get()->convertirAFechaVista($f_fecha_hasta) : '';
$v_fecha_listado  = ( !is_null($f_fecha_listado) ) ? Validator::get()->convertirAFechaVista($f_fecha_listado) : '';

// Listados para cargar el combo del buscador
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
        <span class="glyphicon glyphicon-file"></span><strong>Informes</strong>
    </div>
</div>
<div class="row">
    <!-- Criterio de búsqueda + Grilla -->
    <div class="col-md-12 responsive">
        <div class="row borde-inferior">
            <!-- Formulario para el criterio de búsqueda -->
            <form id="form_listado_informes" name="form_listado_informes" class="form-horizontal" action="index.php?c=listadoinformes&a=view" method="POST">
                
                <div class="col-sm-5 col-md-5 col-lg-3">
                    <!-- Fechas Desde y Hasta -->
                    <div class="form-group">
                        <label for="v_fecha_desde" class="col-xs-2 col-sm-4 col-md-3 control-label">Desde:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 sin_padding_izq">
                            <input type="text" id="v_fecha_desde" name="v_fecha_desde" class="form-control criterio_busqueda_campo_fecha input-sm" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_desde; ?>">
                        </div>
                        <label for="v_fecha_hasta" class="col-xs-2 col-sm-2 col-md-2 col-xs-offset-1 col-sm-offset-2 col-md-offset-1 control-label sin_padding_izq">Hasta:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 sin_padding_izq">
                            <input type="text" id="v_fecha_hasta" name="v_fecha_hasta" class="form-control criterio_busqueda_campo_fecha input-sm" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_hasta; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_desde" name="f_fecha_desde" value="<?php echo $f_fecha_desde; ?>" />
                        <input type="hidden" id="f_fecha_hasta" name="f_fecha_hasta" value="<?php echo $f_fecha_hasta; ?>" />
                    </div>
                </div>
                <div class="col-sm-7 col-md-4 col-lg-3">
                    <!-- Fecha del Listado para el cálculo de días de expedientes en Comisión -->
                    <div class="form-group">
                        <label for="v_fecha_listado" class="col-xs-7 col-sm-8 col-md-8 sin_padding_izq control-label">Calcular d&iacute;as en comisi&oacute;n hasta:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 sin_padding_izq">
                            <input type="text" id="v_fecha_listado" name="v_fecha_listado" class="form-control criterio_busqueda_campo_fecha input-sm" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_listado; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_listado" name="f_fecha_listado" value="<?php echo $f_fecha_listado; ?>" />
                    </div>
                </div>
                <div class="col-sm-10 col-md-10 col-lg-5">
                    <!-- Comisión -->
                    <div class="form-group">
                        <label for="f_comision" class="col-xs-2 col-sm-2 col-md-2 control-label">Comisi&oacute;n:</label>
                        <div class="col-xs-10 col-sm-10 col-md-10 sin_padding_izq">
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
                <div class="col-sm-2 col-md-2 col-lg-1 col-xs-offset-2 col-lg-offset-0">
                    <!-- Checkbox sólo vencidos -->
                    <div class="form-group">
                        <div class="checkbox">
                            <label class="control-label">
                                <input type="checkbox" id="f_vencidos" name="f_vencidos" value="0">&nbsp;<strong>S&oacute;lo vencidos</strong>
                            </label>
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