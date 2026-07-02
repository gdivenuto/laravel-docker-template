<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 	Array asociativo que contiene todos los parámetros de la vista para ser	utilizados en la plantilla.
 */
$this->generarModalDialog();
?>
<div class="row">
    <!-- Criterio búsqueda -->
    <div class="col-md-12 responsive">
        <div class="row borde-inferior">
            <div class="col-md-12 titulo-codificadora">
                <span class="glyphicon glyphicon-search"></span>&nbsp;Verificar <b>Digitalizaci&oacute;n</b> de Expedientes del D.E.
            </div>
        </div>
        <div class="row borde-inferior">
            <!-- Formulario para el criterio de Búsqueda -->
            <form id="form_busqueda" name="form_busqueda" class="form-inline" action="index.php?c=verificardigitalizacion&a=view" method="POST">
                
                <div class="col-sm-5 col-md-6 col-lg-6">
                    <div class="form-group form-group-inline">
                        <label for="f_anio">A&ntilde;o:</label>
                        <input id="f_anio" type="text" class="form-control input-sm criterio_busqueda_campo_numerico" name="f_anio" maxlength="4" value="">
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="f_numero">&nbsp;N&uacute;mero:</label>
                        <input id="f_numero" type="text" class="form-control input-sm criterio_busqueda_campo_numerico" name="f_numero" value="">
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="f_digito">&nbsp;D&iacute;gito:</label>
                        <input id="f_digito" name="f_digito" type="text" class="form-control input-sm criterio_busqueda_campo_numerico" value="">
                    </div>
                </div>
                <!-- Botones: Buscar + Restablecer + Ir a expedientes -->
                <div class="col-sm-7 col-md-6 col-lg-6">
                    <button id="btn_buscar" name="btn_buscar" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-search"></span>&nbsp;Buscar</button>
                    <button id="btn_refrescar" name="btn_refrescar" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Restablecer</button>
                    <button id="btn_busqueda_simple" type="button" class="btn btn-primary btn-sm btn_listado_ancho_fijo"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes</button>
                </div>
            </form>
            <div id="msg_error_form"></div>
        </div>
        <div class="row">
            <div id="grillaDocumentosContainer" class="col-md-12 responsive contenedor-grilla">
                <!-- El contenido se carga dinámicamente -->
            </div>
        </div>
    </div>
</div>