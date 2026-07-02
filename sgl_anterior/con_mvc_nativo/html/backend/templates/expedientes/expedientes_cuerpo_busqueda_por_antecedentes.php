<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 				Array asociativo que contiene todos los parámetros de la vista para ser
 *  								utilizados en la plantilla.
 */
$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12 titulo-codificadora">
        <span class="glyphicon glyphicon-search"></span>&nbsp;B&uacute;squeda por Antecedente
    </div>
</div>
<div class="row">
    <!-- Criterio búsqueda de Por Antecedente + Grilla -->
    <div class="col-md-12 responsive">
        <div class="row borde-inferior">
            <!-- Formulario para el criterio de Búsqueda Por Antecedente-->
            <form id="form_busqueda_por_antecedente" class="form-inline" action="index.php?c=expedientesbusquedaantecente&a=view" method="POST">
                <!-- Número y Año -->
                <div class="col-sm-5 col-md-4">
                    <div class="form-group form-group-inline">
                        <label for="numero">N&uacute;mero:</label>
                        <input id="f_numero" type="text" class="form-control input-sm form-control-width-normal" name="f_numero" value="<?php echo $this->vista->data['f_numero'];?>">
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="anio">A&ntilde;o:</label>
                        <input id="f_anio" type="text" class="form-control input-sm form-control-width-normal" name="f_anio" maxlength="4" value="">
                    </div>
                </div>
                <!-- Botones: Buscar y Restablecer -->
                <div class="col-sm-3 col-md-4 text-left">
                    <button id="btn_buscar_por_antecedente" name="btn_buscar_por_antecedente" type="button" class="btn btn-primary btn-sm boton-adaptado"><span class="glyphicon glyphicon-search"></span>&nbsp;Buscar</button>
                    <button id="btn_refrescar" name="btn_refrescar" type="button" class="btn btn-primary btn-sm boton-adaptado"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Restablecer</button>
                </div>
                <!-- Botones: Imprimir y Volver -->
                <div class="col-sm-4 col-md-4 text-right">
                    <button id="btn_imprimir" name="btn_imprimir" type="button" class="btn btn-primary btn-sm boton-adaptado"><span class="glyphicon glyphicon-print"></span>&nbsp;Imprimir</button>
                    <button id="btn_busqueda_simple" type="button" class="btn btn-primary btn-sm boton-adaptado"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes</button>
                </div>
            </form>
            <div id="msg_error_form"></div>
        </div>
        <div class="row">
            <div id="tablaFichasContainer" class="col-md-12 responsive">
                <!-- El listado se genera dinámicamente -->
            </div>
        </div>
    </div>
</div>