<?php
/**
 * Este script esta diseñado para ser incluído desde las páginas que requieran el buscador de expedientes.
 */
?>
<!-- Cabecera formulario edicion -->
<div class="row borde-inferior">
    <div class="form-group col-md-12">
        <div class="col-md-12">
            <div class="form-inline tamanio-texto-small">
                <div class="form-group form-group-inline">
                    <label for="f_anio">A&ntilde;o:</label>
                    <input type="text" id="f_anio" name="f_anio" class="form-control form-control-width-small input-sm texto_solo_lectura" value="" readonly="true">
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_tipo">Tipo:</label>
                    <input type="text" id="f_tipo" name="f_tipo" class="form-control form-control-width-extra-small input-sm texto_solo_lectura" value="" readonly="true">
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_numero">N&uacute;mero:</label>
                    <input type="text" id="f_numero" name="f_numero" class="form-control form-control-width-small input-sm texto_solo_lectura" value="" readonly="true" placeholder="Autom&aacute;tico">
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_cuerpo">Cuerpo:</label>
                    <input type="text" id="f_cuerpo" name="f_cuerpo" class="form-control form-control-width-extra-small input-sm texto_solo_lectura" value="" readonly="true">
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_alcance">Alcance:</label>
                    <input type="text" id="f_alcance" name="f_alcance" class="form-control form-control-width-extra-small input-sm texto_solo_lectura" value="" readonly="true">
                </div>
            </div>
        </div>
    </div>
</div>
