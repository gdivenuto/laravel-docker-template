<?php
/**
 * Este script esta diseñado para ser incluído desde las páginas que requieran el buscador de expedientes.
 */
$c_anio = Validator::get()->obtenerDefault($this->vista->data['f_anio'], '');
$c_tipo = Validator::get()->obtenerDefault($this->vista->data['f_tipo'], '-');
$c_numero = Validator::get()->obtenerDefault($this->vista->data['f_numero'], '');
$c_cuerpo = Validator::get()->obtenerDefault($this->vista->data['f_cuerpo'], '');
$c_alcance = Validator::get()->obtenerDefault($this->vista->data['f_alcance'], '');
?>
<!-- Buscador de expedientes por clave -->
<form id="form_busqueda_expediente" class="form-inline" action="index.php?c=expedientes&a=view" method="POST">
    <div class="row espacio_interno_sup_3">

        <!-- Campos del buscador -->
        <div class="form-group col-xs-2 col-sm-2 col-md-1 form-group-inline sin_padding_der">
            <label for="f_anio" class="form-group-inline tamanio-texto-muy-chico">A&ntilde;o:</label>
            <input id="f_anio" name="f_anio" type="text" class="form-control input-ancho-40 input-sm" maxlength="4" value="<?= $c_anio; ?>">
        </div>
        <div class="form-group col-xs-2 col-sm-2 col-md-2 form-group-inline sin_padding_izq sin_padding_der" style="width:84px">
            <label for="f_tipo" class="form-group-inline tamanio-texto-muy-chico">Tipo:</label>
            <select id="f_tipo" name="f_tipo" class="form-control input-sm">
                <option value="E" <?= ($c_tipo == "E") ? "selected" : ""; ?> >E</option>
                <option value="N" <?= ($c_tipo == "N") ? "selected" : ""; ?> >N</option>
                <option value="R" <?= ($c_tipo == "R") ? "selected" : ""; ?> >R</option>
            </select>
        </div>
        <div class="form-group col-xs-2 col-sm-2 col-md-1 form-group-inline sin_padding_izq sin_padding_der">
            <label for="f_numero" class="form-group-inline tamanio-texto-muy-chico">N&uacute;mero:</label>
            <input id="f_numero" name="f_numero" type="text" class="form-control input-ancho-35 input-sm" value="<?= $c_numero; ?>">
        </div>
        <div class="form-group col-xs-2 col-sm-2 col-md-1 form-group-inline sin_padding_izq sin_padding_der">
            <label for="f_cuerpo" class="form-group-inline tamanio-texto-muy-chico">Cpo.:</label>
            <input id="f_cuerpo" name="f_cuerpo" type="text" class="form-control form-control-width-extra-small input-sm" value="<?= $c_cuerpo; ?>">
        </div>
        <div class="form-group col-xs-2 col-sm-2 col-md-1 form-group-inline sin_padding_izq sin_padding_der">
            <label for="f_alcance" class="form-group-inline tamanio-texto-muy-chico">Alc.:</label>
            <input id="f_alcance" name="f_alcance" type="text" class="form-control form-control-width-extra-small input-sm" value="<?= $c_alcance; ?>">
        </div>

        <!-- Botón Buscar -->
        <button id="btn_buscar_expediente" name="btn_buscar_expediente" type="button" class="btn btn-primary btn-sm col-xs-12 col-md-1">
            <span class="glyphicon glyphicon-search"></span>&nbsp;Buscar
        </button>

        <?php if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) { // Sólo para Perfil 1 ó 2 ?>

            <!-- Búsqueda Avanzada -->
            <button id="btn_busqueda_avanzada" class="btn btn-primary btn-sm col-xs-6 col-md-2" name="btn_busqueda_avanzada" type="button" title="B&uacute;squeda Avanzada">
                <span class="glyphicon glyphicon-search"></span>&nbsp;B&uacute;squeda Avanzada
            </button>
            <!-- Búsqueda por Antecedente -->
            <button id="btn_busqueda_por_antecedente" class="btn btn-primary btn-sm col-xs-6 col-md-2" name="btn_busqueda_por_antecedente" type="button" title="B&uacute;squeda por Antecedente">
                <span class="glyphicon glyphicon-search"></span>&nbsp;Por Antecedente
            </button>
            <!-- Verificar Digitalización -->
            <button id="btn_verificar_digitalizacion" class="btn btn-primary btn-sm col-xs-12 col-md-2" name="btn_verificar_digitalizacion" type="button" title="Verificar Digitalizaci&oacute;n D.E.">
                <span class="glyphicon glyphicon-search"></span>&nbsp;Verificar Digitalizaci&oacute;n
            </button>

        <?php } else { // Perfil de Usuario de Bloque y Consulta Web ?>

            <!-- Búsqueda Avanzada -->
            <button id="btn_busqueda_avanzada" class="btn btn-primary btn-sm col-xs-6 col-md-2" name="btn_busqueda_avanzada" type="button" title="B&uacute;squeda Avanzada">
                <span class="glyphicon glyphicon-search"></span>&nbsp;B&uacute;squeda Avanzada
            </button>
            <!-- Búsqueda por Antecedente -->
            <button id="btn_busqueda_por_antecedente" class="btn btn-primary btn-sm col-xs-6 col-md-2" name="btn_busqueda_por_antecedente" type="button" title="B&uacute;squeda por Antecedente">
                <span class="glyphicon glyphicon-search"></span>&nbsp;Por Antecedente
            </button>

        <?php }?>
    </div>
    <div class="row">
        <!-- Botones del Paginador, sólo para la solapa Expedientes (deben estar en el class el nombre de dicho botón para el JS) -->

        <!-- Se muestra en este lugar en la versión de Escritorio -->
        <button id="btn_primer_pagina" class="btn_primer_pagina btn btn-primary btn-sm col-xs-6 col-md-2 col-md-offset-2" type="button">
            <span class="glyphicon glyphicon-fast-backward"></span>&nbsp;Primero
        </button>

        <button id="btn_pagina_anterior" class="btn_pagina_anterior btn btn-primary btn-sm col-xs-6 col-md-2" type="button">
            <span class="glyphicon glyphicon-step-backward"></span>&nbsp;Anterior
        </button>

        <button id="btn_pagina_siguiente" class="btn_pagina_siguiente btn btn-primary btn-sm col-xs-6 col-md-2" type="button">
            Siguiente&nbsp;<span class="glyphicon glyphicon-step-forward"></span>
        </button>

        <!-- Se muestra en este lugar en la versión Móvil -->
        <button id="btn_primer_pagina_movil" class="btn_primer_pagina btn btn-primary btn-sm col-xs-6 col-md-2" type="button">
            <span class="glyphicon glyphicon-fast-backward"></span>&nbsp;Primero
        </button>

        <button id="btn_ultima_pagina" class="btn_ultima_pagina btn btn-primary btn-sm col-xs-6 col-md-2" type="button">
            &Uacute;ltimo&nbsp;<span class="glyphicon glyphicon-fast-forward"></span>
        </button>

        <?php if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) { // Sólo para Perfil 1 ó 2 ?>
            <!-- Botón Nuevo -->
            <button id="btn_nuevo_expediente" class="btn btn-primary btn-sm col-md-2" type="button">
                <span class="glyphicon glyphicon-plus"></span>&nbsp;Nuevo Expediente
            </button>
        <?php }?>
    </div>
    <div class="row">
        <!-- Botones Anterior y Siguiente, para el resto de las solapas (Expedientes tiene los propios)-->
        <button id="btn_expediente_anterior" class="btn btn-primary btn-sm col-xs-6 col-md-2 col-md-offset-8" title="Ver del Expediente Anterior" type="button">
            <span class="glyphicon glyphicon-step-backward"></span>&nbsp;Anterior
        </button>
        <button id="btn_expediente_siguiente" class="btn btn-primary btn-sm col-xs-6 col-md-2" title="Ver del Expediente Siguiente" type="button">
            Siguiente&nbsp;<span class="glyphicon glyphicon-step-forward"></span>
        </button>
    </div>
</form>
<div id="msg_error_form"></div>
