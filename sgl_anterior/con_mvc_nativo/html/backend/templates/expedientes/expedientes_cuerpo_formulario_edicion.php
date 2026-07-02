<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 *
 *  $this->vista->data              Array asociativo que contiene todos los parámetros de la vista para ser
 *                                  utilizados en la plantilla.
 *
 * Además:
 *
 *  $this->vista->dataTitulo        Titulo de la vista
 *  $this->vista->dataSubtitulo     Subtitulo de la vista
 *  $this->vista->dataTexto         Texto introductorio de la vista
 *  $this->vista->dataUsuario       Instancia del usuario actual.
 *  $this->vista->dataMensajeOk     Mensaje de confirmación que debe mostrarse en la vista.
 *  $this->vista->dataMensajeError  Mensaje de error que debe mostrarse en la vista.
 */

// Información del expediente
$expediente = $this->vista->data['expediente'];

// Transformación de expediente a JSON para tenerlo disponible en la vista como expediente.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonExpediente = JsonHelper::get()->serializar($expediente, JSON_HEX_APOS);

// Listados para la carga de los combos
$listado_iniciadores = $this->vista->data['listado_iniciadores'];
$listado_categorias = $this->vista->data['listado_categorias'];
$listado_codautores = $this->vista->data['listado_codautores'];
$listado_codtemas = $this->vista->data['listado_codtemas'];

if ($expediente->tipo == 'E') {
	$titulo_segun_tipo = 'del Expediente';
} elseif ($expediente->tipo == 'N') {
	$titulo_segun_tipo = 'de la Nota';
} else {
	$titulo_segun_tipo = 'de la Recomendaci&oacute;n';
}

// Para mostrar en el Calendario de la Fecha de Entrada del Expediente
$v_fecha_entrada_expe = (!is_null($expediente->fecha_entrada_expe)) ? Validator::get()->convertirAFechaVista($expediente->fecha_entrada_expe) : '';

// Marca para saber si estamos Editando o Insertando
// para deshabilitar la clave del expediente o NO
$solo_lectura = ($this->vista->data['bloquear_clave_expediente']) ? 'readonly' : '';

$this->generarModalDialog();
?>
<div class="row espacio_interno_5 borde-inferior">
    <div class="col-md-12"><span class="glyphicon glyphicon-edit"></span>&nbsp;Edici&oacute;n <?php echo $titulo_segun_tipo; ?></div>
</div>
<form id="form_edicion_expediente" class="form-horizontal">
    <div class="row borde-inferior">
        <!-- Campos clave -->
        <div class="<?php echo ($solo_lectura == 'readonly') ? 'col-xs-12' : 'col-xs-11'; ?> col-sm-10 col-md-8 col-lg-6 col-md-offset-1 form-control-without-padding">
            <div class="form-inline">
                <div class="form-group col-xs-2 form-group-inline">
                    <label for="f_anio" class="form-group-inline">A&ntilde;o:</label>
                    <input id="f_anio" name="f_anio" type="text" class="form-control form-control-width-extra-small form-control-padding-5 input-sm solo-numero" maxlength="4" value="" <?php echo $solo_lectura; ?> >
                </div>
                <div class="form-group <?php echo ($solo_lectura == 'readonly') ? 'col-xs-2' : 'col-xs-3'; ?> form-group-inline">
                    <label for="f_tipo" class="form-group-inline">Tipo:</label>
                <?php if ($solo_lectura == 'readonly') {?>
                    <input id="f_tipo" name="f_tipo" type="text" value="" readonly="true" class="form-control form-control-width-extra-small form-control-padding-5 input-sm" />
                <?php } else {?>
                    <select id="f_tipo" name="f_tipo" class="form-control form-control-width-small input-sm">
                        <option value="E">E</option>
                        <option value="N">N</option>
                        <option value="R">R</option>
                    </select>
                <?php }?>
                </div>
                <div class="form-group col-xs-3 form-group-inline">
                    <label for="f_numero" class="form-group-inline">N&uacute;mero:</label>
                    <input id="f_numero" name="f_numero" type="text" class="form-control form-control-width-small form-control-padding-5 input-sm solo-numero" value="" placeholder="Auto." <?php echo $solo_lectura; ?> >
                </div>
                <div class="form-group col-xs-2 form-group-inline">
                    <label for="f_cuerpo" class="form-group-inline">Cpo:</label>
                    <input id="f_cuerpo" name="f_cuerpo" type="text" class="form-control form-control-width-extra-small input-sm solo-numero" value="" <?php echo $solo_lectura; ?> >
                </div>
                <div class="form-group col-xs-2 form-group-inline">
                    <label for="f_alcance" class="form-group-inline">Alc:</label>
                    <input id="f_alcance" name="f_alcance" type="text" class="form-control form-control-width-extra-small input-sm solo-numero" value="" <?php echo $solo_lectura; ?> >
                </div>
            </div>
        </div>
    </div>
    <!-- Datos restantes -->
    <div class="row">
        <!-- Campos a Izquierda -->
        <div class="col-md-6">

            <div class="form-group">
                <label for="v_fecha_entrada_expe" class="col-xs-2 col-sm-2 col-md-4 control-label">Fecha:</label>
                <div class="col-xs-2 col-sm-2 col-md-2">
                    <input id="v_fecha_entrada_expe" name="v_fecha_entrada_expe" class="form-control input-sm criterio_busqueda_campo_fecha espacio_interno_izq_5" type="text" placeholder="dd/mm/aaaa" value="">
                </div>
                <input type="hidden" id="f_fecha_entrada_expe" name="f_fecha_entrada_expe" value="" />
            </div>
            <div class="form-group">
                <label for="v_iniciador_tipo" class="col-xs-2 col-sm-2 col-md-4 control-label">Iniciador:</label>
                <div class="col-xs-10 col-sm-10 col-md-8">
                    <div class="input-group ancho_100_porciento">
                        <select id="v_iniciador_tipo" name="v_iniciador_tipo" class="form-control input-sm ">
                            <option value="G">Grupo</option>
                            <option value="V">Varios</option>
                        </select>
                        <span class="input-group-btn"></span>
                        <select id="f_iniciador" name="f_iniciador" class="form-control input-sm form-control-width-nombre-iniciador">
                            <option value="0">---</option>

                            <?php $cantidad_iniciadores = (isset($listado_iniciadores)) ? count($listado_iniciadores) : 0; ?>

                            <?php for ($i=0; $i < $cantidad_iniciadores; $i++) { $ini = &$listado_iniciadores[$i]; ?>
                                <option value="<?=$ini->tipo_grp;?>|<?=$ini->codigo_grp;?>">
                                    <?=$ini->tipo_grp;?> - <?=$ini->codigo_grp;?> - <?=$ini->descripcion_grp;?>
                                </option>
                            <?php } ?>
<?php
// $this->renderOptionList(
// 	$listado_iniciadores, // coleccion
// 	array('tipo_grp', 'codigo_grp'), // valor del combo
// 	'descripcion_grp', // descripcion
// 	array($expediente->iniciador_tipo, $expediente->iniciador_codigo)); // default selected
?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="f_categoria" class="col-xs-2 col-sm-2 col-md-4 control-label">Categor&iacute;a:</label>
                <div class="col-xs-10 col-sm-10 col-md-8">
                    <select id="f_categoria" name="f_categoria" class="form-control input-sm">
                        <option value="0">---</option><!-- Se muestra aquí 'ro_descripcion_categoria' -->
                        <?php
$this->renderOptionList(
	$listado_categorias, // coleccion
	'id_codcategoria', // valor del combo
	'descripcion_categoria', // descripcion
	$expediente->id_codcategoria); // default selected
?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="f_caratula" class="col-xs-2 col-sm-2 col-md-4 control-label">Car&aacute;tula:</label>
                <div class="col-xs-10 col-sm-10 col-md-8">
                    <input  id="f_caratula" name="f_caratula" type="text"
                            class="form-control input-sm" value=""
                            onKeyDown="limitarCantidadcaracteres(this, 60);"
                            onKeyUp="limitarCantidadcaracteres(this, 60);"
                            placeholder="No m&aacute;s de 60 caracteres">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-2 col-sm-2 col-md-4 control-label">Agregado a:</label>
                <div class="col-xs-10 col-sm-10 col-md-8">
                    <div class="input-group">
                        <input id="f_agregado_anio" name="f_agregado_anio" type="text" class="form-control input-sm grupo-agregado-a solo-numero" maxlength="4" value="" placeholder="A&ntilde;o">
                        <span class="input-group-btn"></span>
                        <select id="f_agregado_tipo" name="f_agregado_tipo" class="form-control input-sm form-control-width-small grupo-agregado-a">
                            <option value="">-</option>
                            <option value="E">E</option>
                            <option value="N">N</option>
                            <option value="R">R</option>
                        </select>
                        <span class="input-group-btn"></span>
                        <input id="f_agregado_numero" name="f_agregado_numero" type="text" class="form-control input-sm grupo-agregado-a solo-numero" value="" placeholder="N&uacute;mero">
                        <span class="input-group-btn"></span>
                        <input id="f_agregado_cuerpo" name="f_agregado_cuerpo" type="text" class="form-control input-sm grupo-agregado-a solo-numero" value="" placeholder="Cpo">
                        <span class="input-group-btn"></span>
                        <input id="f_agregado_alcance" name="f_agregado_alcance" type="text" class="form-control input-sm grupo-agregado-a solo-numero" value="" placeholder="Alc">
                        <!-- <span class="input-group-addon"><span id="v_icono_agregado_valido" class="glyphicon glyphicon-ok-circle"></span></span> -->
                    </div>
                </div>
                <div class="col-md-4">
                </div>
                <div class="col-md-8">
                    <small id="v_msg_agregado_valido" class="forzar-texto-color-negro"></small>
                </div>
            </div>
            <div class="form-group">
                <label for="f_observaciones_expe" class="col-xs-3 col-sm-2 col-md-4 control-label">Observaciones:</label>
                <div class="col-xs-12 col-sm-10 col-md-8">
                    <textarea id="f_observaciones_expe" name="f_observaciones_expe" class="form-control" rows="9"></textarea>
                </div>
            </div>
            <?php
// 03/06/2020 XXXX
// Se setea de Digitalización Parcial a Digitalización Completa
// siempre y cuando se encuentre cargada la Digitalización
//if ($expediente->estado_digitalizacion == 'DC') {
?>
            <div class="form-group">
                <label for="f_digi_completa" class="col-xs-5 col-sm-3 col-md-4 control-label">¿Digitalizaci&oacute;n Completa?</label>
                <div class="col-xs-6 col-sm-2 col-md-3 col-lg-5">
                    <label>
                        <input type="checkbox" id="f_digi_completa" name="f_digi_completa" value="" <?php echo ($expediente->estado_digitalizacion != 'DC') ? 'disabled' : ''; ?> />
                    </label>
                </div>
            </div>
            <?php
//}
?>
        </div>
        <!-- Tablas -->
        <div class="col-md-6">
            <div class="row">
                <!-- Temas -->
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="v_temas" class="col-sm-3 col-md-4 control-label">Temas del Expediente</label>
                                <div class="col-sm-9 col-md-8">
                                    <div class="input-group">
                                        <select id="v_temas" name="v_temas" class="form-control input-sm">
                                            <option value="0"></option>
                                            <?php
$this->renderOptionList(
	$listado_codtemas, // coleccion
	'id_codtema', // valor del combo
	'descripcion_tema', // descripcion
	null); // default selected
?>
                                        </select>
                                        <span class="input-group-btn">
                                            <button id="btn_agregar_tema" type="button" class="btn btn-default btn-sm" title="Agregar el Tema al Expediente">
                                                <span class="glyphicon glyphicon-plus"></span>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="contenedor-grilla contenedor-grilla-en-formularios">
                                <table id="grillaTemas" class="table">
                                    <thead class="color-fondo-menu">
                                        <tr>
                                            <th>Acci&oacute;n</th>
                                            <th>C&oacute;digo</th>
                                            <th>Descripci&oacute;n</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Autores -->
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-sm-3 col-md-4 col-lg-4 control-label">Autores del Expediente</label>
                                <div class="col-sm-9 col-md-8 col-lg-8">
                                    <div class="input-group">
                                        <select id="v_tipo_autor" name="v_tipo_autor" class="form-control input-sm form-control-width-normal">
                                            <option value="V">Varios</option>
                                            <option value="C">Comisi&oacute;n</option>
                                            <option value="G">Grupo</option>
                                        </select>
                                        <span class="input-group-btn"></span>
                                        <select id="v_autores" name="v_autores" class="form-control input-sm">
                                            <option value="0"></option>

                                    <?php $cantidad_autores = (isset($listado_codautores)) ? count($listado_codautores) : 0; ?>

                                    <?php for ($i=0; $i < $cantidad_autores; $i++) { $autor = &$listado_codautores[$i]; ?>
                                        <option value="<?=$autor->tipo_grp;?>|<?=$autor->codigo_grp;?>|<?=$autor->bloque_tipo;?>|<?=$autor->bloque_codigo;?>">
                                            <?=$autor->tipo_grp;?> - <?=$autor->codigo_grp;?> - <?=$autor->descripcion_grp;?>
                                        </option>
                                    <?php } ?>
<?php
// $this->renderOptionList(
// 	$listado_codautores, // coleccion
// 	array('tipo_grp', 'codigo_grp'), // valor del combo
// 	'descripcion_grp', // descripcion
// 	null); // valor preseleccionado
?>
                                        </select>
                                        <span class="input-group-btn">
                                            <button id="btn_agregar_autor" type="button" class="btn btn-default btn-sm" title="Agregar el Autor al Expediente">
                                                <span class="glyphicon glyphicon-plus"></span>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="contenedor-grilla contenedor-grilla-en-formularios">
                                <table id="grillaAutores" class="table">
                                    <thead class="color-fondo-menu">
                                        <tr>
                                            <th>Acci&oacute;n</th>
                                            <th>Grupo</th>
                                            <th>C&oacute;digo</th>
                                            <th>Descripci&oacute;n</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Botones -->
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-xs-9 col-xs-offset-3 col-sm-10 col-sm-offset-2 col-md-10 col-md-offset-2">
                    <button id="btn_guardar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
                    <button id="btn_cancelar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    // Volcado de datos JSON
    var expediente = <?php echo $jsonExpediente; ?>;
</script>
