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
$expedientes = ( isset($this->vista->data['expedientes']) && $this->vista->data['expedientes'] != '' ) ? $this->vista->data['expedientes'] : null;

$cantidad_expedientes = ( ! is_null($expedientes) ) ? count($expedientes) : '&nbsp;';

// Se reduce el nombre de la variable 'data' correspondiente a los criterios de la búsqueda
$f_fecha_desde    = $this->vista->data['parametros_marca_comision']['f_fecha_desde'];
$f_fecha_hasta    = $this->vista->data['parametros_marca_comision']['f_fecha_hasta'];
$f_fecha_listado  = $this->vista->data['parametros_marca_comision']['f_fecha_listado'];
$f_comision       = $this->vista->data['parametros_marca_comision']['f_comision'];

// Para mostrar en los Calendarios de fecha Desde, fecha Hasta, fecha de Listado y fecha de Comisión
$v_fecha_desde    = ( !is_null($f_fecha_desde) ) ? Validator::get()->convertirAFechaVista($f_fecha_desde) : '';
$v_fecha_hasta    = ( !is_null($f_fecha_hasta) ) ? Validator::get()->convertirAFechaVista($f_fecha_hasta) : '';
$v_fecha_listado  = ( !is_null($f_fecha_listado) ) ? Validator::get()->convertirAFechaVista($f_fecha_listado) : '';

// Listados para cargar los combos del buscador
$listado_comisiones = $this->vista->data['listado_comisiones'];

$comisiones_sugeridas = '';
$cantidad_comisiones = count($listado_comisiones);
for ($i=0; $i < $cantidad_comisiones; $i++)
    // Si posee la descripción, se utilizará como sugerencia
    $comisiones_sugeridas .= ($listado_comisiones[$i]->descripcion_grp != '') ? '"'.$listado_comisiones[$i]->codigo_grp.'-'.$listado_comisiones[$i]->descripcion_grp.'",' : '';

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12 titulo-codificadora">
        <span class="glyphicon glyphicon-file"></span>&nbsp;Marca de Expedientes en Comisi&oacute;n
    </div>
</div>
<div class="row">
    <!-- Formulario para el criterio de búsqueda -->
    <form id="form_marca_comision" name="form_marca_comision" class="form-horizontal" action="index.php?c=listadoexpedientesencomision&a=view" method="POST">
        
        <!-- Cantidad de expedientes devueltos -->
        <input type="hidden" name="cantidad_expedientes" value="<?php echo $cantidad_expedientes; ?>" />

        <!-- Criterio de búsqueda + Grilla -->
        <div class="col-md-12 responsive">
            <div class="row borde-inferior">
                
                <!-- Rango de fechas + Comisión -->
                <div class="col-sm-5 col-md-4 col-lg-4">
                    <div class="form-group">
                        <!-- Fecha Desde -->
                        <label class="col-xs-2 col-sm-2 col-md-4 control-label">Desde:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <input id="v_fecha_desde" name="v_fecha_desde" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_desde; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_desde" name="f_fecha_desde" value="<?php echo $f_fecha_desde; ?>" />
                        
                        <!-- Fecha Hasta -->
                        <label class="col-xs-2 col-sm-2 col-md-2 col-lg-2 col-xs-offset-1 col-sm-offset-2 col-md-offset-2 control-label">Hasta:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <input id="v_fecha_hasta" name="v_fecha_hasta" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_hasta; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_hasta" name="f_fecha_hasta" value="<?php echo $f_fecha_hasta; ?>" />
                    </div>
                    <!-- Fecha del Listado para el cálculo de días de expedientes en Comisión -->
                    <div class="form-group">
                        <label class="col-xs-7 col-sm-8 col-md-8 col-lg-10 control-label">Calcular d&iacute;as en comisi&oacute;n hasta:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <input id="v_fecha_listado" name="v_fecha_listado" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_listado; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_listado" name="f_fecha_listado" value="<?php echo $f_fecha_listado; ?>" />
                    </div>
                </div>
                <!-- Fecha del listado + Cantidad de expedientes devueltos -->
                <div class="col-sm-7 col-md-8 col-lg-5">
                    <!-- Comisión -->
                    <div class="form-group">
                        <label class="col-xs-2 col-sm-2 col-md-4 control-label">Comisi&oacute;n:</label>
                        <div class="col-xs-10 col-sm-10 col-md-8">
                            <div class="input-group">
                                <select id="f_comision" name="f_comision" class="form-control input-sm">
                                    <option value="0">Seleccione</option>
                                    <?php
                                    $this->renderOptionList(
                                        $listado_comisiones, // coleccion
                                        'codigo_grp',        // valor del combo
                                        array('tipo_grp', 'codigo_grp', 'descripcion_grp'), // información a elegir
                                        $f_comision);        // default selected
                                    ?>
                                </select>
                                <span class="input-group-addon input-sm">
                                    <a href="javascript:mostrarModalComisionAutosugerido();"><span class="glyphicon glyphicon-search"></span></a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Cantidad de Expedientes en la Comisión -->
                    <div class="form-group text-center" style="padding-top: 5px">
                        <span class="glyphicon glyphicon-info-sign"></span>&nbsp;Expedientes en Comisi&oacute;n: <span id="mc_cantidad_resultados"><?php echo $cantidad_expedientes; ?></span>
                    </div>
                </div>
                <!-- Botones: Buscar + Restablecer + Imprimir + Exportar a Texto -->
                <div class="col-sm-12 col-md-12 col-lg-3 text-center">
                    <button id="btn_buscar" name="btn_buscar" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-search"></span>&nbsp;Buscar</button>
                    <button id="btn_volver" name="btn_volver" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes</button>
                    <button id="btn_guardar" name="btn_guardar" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo <?php echo (isset($cantidad_expedientes) && $cantidad_expedientes > 0) ? '' : 'disabled'; ?>"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
                    <button id="btn_limpiar_marcas" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo <?php echo (isset($cantidad_expedientes) && $cantidad_expedientes > 0) ? '' : 'disabled'; ?>"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Limpiar Marcas</button>
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
            </div>
            <div id="msg_error_form"></div>
            <div class="row">
                <div id="tablaExpedientesParaMarcarContainer" class="col-xs-12 col-md-12 responsive">
                    <?php
                    if ( ! is_null($expedientes) ) {
                    ?>
                        <table class="table" id="grillaExpedientesAMarcar">
                            <thead class="color-fondo-menu">
                                <th>A&ntilde;o</th>
                                <th>Tipo</th>
                                <th>N&uacute;mero</th>
                                <th>Cuerpo</th>
                                <th>Alcance</th>
                                <th>D&iacute;as</th>
                                <th class="mc_nombre_marca">Tipo marca</th>
                                <th class="mc_valor_marca">S</th>
                                <th class="mc_valor_marca">T</th>
                                <th class="mc_valor_marca">C</th>
                                <th class="mc_valor_marca">A</th>
                                <th class="mc_valor_marca">P</th>
                                <th class="mc_valor_marca">CV</th>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;// Nro. de fila

                                // Se muestran los expedientes obtenidos para definir su marca en la Comisión respectiva
                                foreach ($expedientes as $exp) {
                                    // Descripción de la marca según su valor
                                    switch ($exp->marca_comision) {
                                        case '0':
                                            $nombre_marca = "Sin marca";
                                            break;
                                        case '1':
                                            $nombre_marca = "Para tratar";
                                            break;
                                        case '2':
                                            $nombre_marca = "Para su conocimiento";
                                            break;
                                        case '3':
                                            $nombre_marca = "Para archivo";
                                            break;  
                                        case '4':
                                            $nombre_marca = "Para pr&oacute;rroga";
                                            break;
                                        case '5':
                                            $nombre_marca = "Para convalidar";
                                            break;      
                                        default:
                                            $nombre_marca = "Sin marca";
                                            break;
                                    }

                                    if ( $exp->cantidad_dias_en_comision >= 0 && $exp->cantidad_dias_en_comision <= 105 )
                                        $css_color = 'resaltado-ok';
                                    else {
                                        if ( $exp->cantidad_dias_en_comision >= 106 && $exp->cantidad_dias_en_comision <= 119 )
                                            $css_color = 'resaltado-advertencia';
                                        else
                                            $css_color =  'resaltado-alerta';
                                    }
                                ?>
                                    <!-- Indica si se ha modificado la Marca del expediente -->
                                    <input type="hidden" id="marca_modificada<?php echo $i; ?>" name="marca_modificada<?php echo $i; ?>" value="false" />
                                    <!-- Clave del expediente, la cual se podrá utilizar para guardar la marca, en caso que haya sido modificada -->
                                    <input type="hidden" id="clave_expediente<?php echo $i; ?>" name="clave_expediente<?php echo $i; ?>" value="<?php echo $exp->anio.'-'.$exp->tipo.'-'.$exp->numero.'-'.$exp->cuerpo.'-'.$exp->alcance; ?>" disabled >
                                                                
                                    <tr>
                                        <!-- Clave del expediente a mostrar por separado -->
                                        <td id="anio_marca_comision<?php echo $i; ?>" class="text-muted"><?php echo $exp->anio; ?></td>
                                        <td id="tipo_marca_comision<?php echo $i; ?>" class="text-muted"><?php echo $exp->tipo; ?></td>
                                        <td id="numero_marca_comision<?php echo $i; ?>" class="text-muted"><?php echo $exp->numero; ?></td>
                                        <td id="cuerpo_marca_comision<?php echo $i; ?>" class="text-muted"><?php echo $exp->cuerpo; ?></td>
                                        <td id="alcance_marca_comision<?php echo $i; ?>" class="text-muted"><?php echo $exp->alcance; ?></td>
                                        
                                        <!-- Cantidad de días en la Comisión -->
                                        <td class="<?php echo $css_color; ?> text-right"><?php echo $exp->cantidad_dias_en_comision; ?></td>
                                        
                                        <!-- Nombre de la Marca -->
                                        <td id="i_nombre_marca<?php echo $i; ?>" class="text-muted mc_nombre_marca"><?php echo $nombre_marca; ?></td>
                                        
                                        <!-- Radios para definir la Marca en la Comisión -->
                                        <td class="mc_valor_marca">
                                            <input type="radio" name="i_tipo_marca<?php echo $i; ?>" id="i_sin_marca<?php echo $i; ?>" value="0" onclick="javascript:habilitarClaveExpedienteComision(<?php echo $i; ?>);$('#i_nombre_marca<?php echo $i; ?>').html('Sin marca');" onchange="javascript:$('#marca_modificada<?php echo $i; ?>').val(true);" checked >
                                        </td>
                                        <td class="mc_valor_marca">
                                            <input type="radio" name="i_tipo_marca<?php echo $i; ?>" id="i_para_tratar<?php echo $i; ?>" value="1" onclick="javascript:habilitarClaveExpedienteComision(<?php echo $i; ?>);$('#i_nombre_marca<?php echo $i; ?>').html('Para tratar');" onchange="javascript:$('#marca_modificada<?php echo $i; ?>').val(true);" <?php echo ($exp->marca_comision == 1) ? 'checked' : ''; ?> >
                                        </td>
                                        <td class="mc_valor_marca">
                                            <input type="radio" name="i_tipo_marca<?php echo $i; ?>" id="i_para_su_conoc<?php echo $i; ?>" value="2" onclick="javascript:habilitarClaveExpedienteComision(<?php echo $i; ?>);$('#i_nombre_marca<?php echo $i; ?>').html('Para su conocimiento');" onchange="javascript:$('#marca_modificada<?php echo $i; ?>').val(true);" <?php echo ($exp->marca_comision == 2) ? 'checked' : ''; ?> >
                                        </td>
                                        <td class="mc_valor_marca">
                                            <input type="radio" name="i_tipo_marca<?php echo $i; ?>" id="i_para_archivo<?php echo $i; ?>" value="3" onclick="javascript:habilitarClaveExpedienteComision(<?php echo $i; ?>);$('#i_nombre_marca<?php echo $i; ?>').html('Para archivo');" onchange="javascript:$('#marca_modificada<?php echo $i; ?>').val(true);" <?php echo ($exp->marca_comision == 3) ? 'checked' : ''; ?> >
                                        </td>
                                        <td class="mc_valor_marca">
                                            <input type="radio" name="i_tipo_marca<?php echo $i; ?>" id="i_para_prorroga<?php echo $i; ?>" value="4" onclick="javascript:habilitarClaveExpedienteComision(<?php echo $i; ?>);$('#i_nombre_marca<?php echo $i; ?>').html('Para pr&oacute;rroga');" onchange="javascript:$('#marca_modificada<?php echo $i; ?>').val(true);" <?php echo ($exp->marca_comision == 4) ? 'checked' : ''; ?> >
                                        </td>
                                        <td class="mc_valor_marca">
                                            <input type="radio" name="i_tipo_marca<?php echo $i; ?>" id="i_para_convalidar<?php echo $i; ?>" value="5" onclick="javascript:habilitarClaveExpedienteComision(<?php echo $i; ?>);$('#i_nombre_marca<?php echo $i; ?>').html('Para convalidar');" onchange="javascript:$('#marca_modificada<?php echo $i; ?>').val(true);" <?php echo ($exp->marca_comision == 5) ? 'checked' : ''; ?> >
                                        </td>
                                    </tr>
                                <?php
                                    $i++; // Se incrementa el Nro. de fila
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $( function() {
        var comisiones_sugeridas = [<?php echo $comisiones_sugeridas; ?>];
        
        $('#modal_comision_sugerida').autocomplete({
            source: comisiones_sugeridas
        });
    });
</script>
