<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data Array asociativo que contiene todos los parámetros de la vista para ser
 *  				   utilizados en la plantilla.
 */
//********** Se reciben los parámetros del Controlador ******************************************
// Se reduce el nombre de la variable 'data' correspondiente a los criterios de la búsqueda
$f_fecha_desde = $this->vista->data['parametros_listado_sin_digitalizacion']['f_fecha_desde'];
$f_fecha_hasta = $this->vista->data['parametros_listado_sin_digitalizacion']['f_fecha_hasta'];

// Para mostrar en los Calendarios de fecha Desde y fecha Hasta
$v_fecha_desde = ( !is_null($f_fecha_desde) ) ? Validator::get()->convertirAFechaVista($f_fecha_desde) : '';
$v_fecha_hasta = ( !is_null($f_fecha_hasta) ) ? Validator::get()->convertirAFechaVista($f_fecha_hasta) : '';

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12 titulo-codificadora">
        <span class="glyphicon glyphicon-file"></span><strong>Expedientes sin Digitalizar</strong>
    </div>
</div>
<div class="row">
    <!-- Criterio de búsqueda -->
    <div class="col-md-12 responsive">
        <div class="row borde-inferior">
            <!-- Formulario para el criterio de búsqueda -->
            <form id="form_sin_digitalizar" name="form_sin_digitalizar" class="form-horizontal" action="index.php?c=listadoexpedientessindigitalizar&a=view" method="POST">
                <!-- Fecha Desde y Hasta -->
                <div class="col-sm-6 col-md-3 col-lg-3">
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
                
                <!-- Total de resultados -->
                <div class="col-sm-6 col-md-2 col-lg-2 label-sin-padding-lateral text-center">
                    <span class="glyphicon glyphicon-info-sign"></span>&nbsp;Total de Expedientes: <span id="lb_cantidad_resultados"></span>
                </div>

                <!-- Botones: Buscar + Restablecer + Imprimir + Exportar a Texto -->
                <div class="col-sm-12 col-md-7 col-lg-7 text-center">
                    <button id="btn_buscar" name="btn_buscar" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-search"></span>&nbsp;Buscar</button>
                    <button id="btn_refrescar" name="btn_refrescar" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Restablecer</button>
                    <button id="btn_imprimir" name="btn_imprimir" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-print"></span>&nbsp;Imprimir</button>
                    <button id="btn_exportar_texto" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-export"></span>&nbsp;Exportar a Texto</button>
                    <button id="btn_volver" name="btn_volver" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes</button>
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