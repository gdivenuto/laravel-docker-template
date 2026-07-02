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
$f_fecha_desde = $this->vista->data['parametros_busqueda_avanzada']['f_fecha_desde'];
$f_fecha_hasta = $this->vista->data['parametros_busqueda_avanzada']['f_fecha_hasta'];
$f_opcion_sancionado_promulgado = $this->vista->data['parametros_busqueda_avanzada']['f_opcion_sancionado_promulgado'];
$f_iniciador = $this->vista->data['parametros_busqueda_avanzada']['f_iniciador'];
$f_categoria = $this->vista->data['parametros_busqueda_avanzada']['f_categoria'];
$f_caratula = $this->vista->data['parametros_busqueda_avanzada']['f_caratula'];
$f_tema = $this->vista->data['parametros_busqueda_avanzada']['f_tema'];
$f_autor = $this->vista->data['parametros_busqueda_avanzada']['f_autor'];
$f_comision = $this->vista->data['parametros_busqueda_avanzada']['f_comision'];
$f_estado = $this->vista->data['parametros_busqueda_avanzada']['f_estado'];

// Para mostrar en los Calendarios de fecha Desde y fecha Hasta
$v_fecha_desde = (!is_null($f_fecha_desde)) ? Validator::get()->convertirAFechaVista($f_fecha_desde) : '';
$v_fecha_hasta = (!is_null($f_fecha_hasta)) ? Validator::get()->convertirAFechaVista($f_fecha_hasta) : '';

// Listados para cargar los combos del buscador
$listado_iniciadores = $this->vista->data['listado_iniciadores'];

$listado_comisiones = $this->vista->data['listado_comisiones'];
$listado_codestados = $this->vista->data['listado_codestados'];
$listado_categorias = $this->vista->data['listado_categorias'];
$listado_codautores = $this->vista->data['listado_codautores'];
$listado_codtemas = $this->vista->data['listado_codtemas'];

$iniciadores_sugeridos = '';
$cantidad_iniciadores = count($listado_iniciadores);
for ($i = 0; $i < $cantidad_iniciadores; $i++){
    // Si posee la descripción, se utilizará como sugerencia
	$iniciadores_sugeridos .= ($listado_iniciadores[$i]->descripcion_grp != '') ? '"' . $listado_iniciadores[$i]->tipo_grp . '-' . $listado_iniciadores[$i]->codigo_grp . '-' . $listado_iniciadores[$i]->descripcion_grp . '",' : '';
}

$comisiones_sugeridas = '';
$cantidad_comisiones = count($listado_comisiones);
for ($i = 0; $i < $cantidad_comisiones; $i++){
    // Si posee la descripción, se utilizará como sugerencia
	$comisiones_sugeridas .= ($listado_comisiones[$i]->descripcion_grp != '') ? '"' . $listado_comisiones[$i]->codigo_grp . '-' . $listado_comisiones[$i]->descripcion_grp . '",' : '';
}

$estados_sugeridos = '';
$cantidad_estados = count($listado_codestados);
for ($i = 0; $i < $cantidad_estados; $i++){
    // Si posee el nombre, se utilizará como sugerencia
	$estados_sugeridos .= ($listado_codestados[$i]->nombre_estado != '') ? '"' . $listado_codestados[$i]->id_codestado . '-' . $listado_codestados[$i]->nombre_estado . '",' : '';
}

$categorias_sugeridas = '';
$cantidad_categorias = count($listado_categorias);
for ($i = 0; $i < $cantidad_categorias; $i++){
    // Si posee el nombre, se utilizará como sugerencia
	$categorias_sugeridas .= ($listado_categorias[$i]->descripcion_categoria != '') ? '"' . $listado_categorias[$i]->id_codcategoria . '-' . $listado_categorias[$i]->descripcion_categoria . '",' : '';
}

$autores_sugeridos = '';
$cantidad_autores = count($listado_codautores);
for ($i = 0; $i < $cantidad_autores; $i++){
    // Si posee la descripción, se utilizará como sugerencia
	$autores_sugeridos .= ($listado_codautores[$i]->descripcion_grp != '') ? '"' . $listado_codautores[$i]->tipo_grp . '-' . $listado_codautores[$i]->codigo_grp . '-' . $listado_codautores[$i]->descripcion_grp . '",' : '';
}

$temas_sugeridos = '';
$cantidad_temas = count($listado_codtemas);
for ($i = 0; $i < $cantidad_temas; $i++){
    // Si posee la descripción, se utilizará como sugerencia
	$temas_sugeridos .= ($listado_codtemas[$i]->descripcion_tema != '') ? '"' . $listado_codtemas[$i]->id_codtema . '-' . $listado_codtemas[$i]->descripcion_tema . '",' : '';
}

$this->generarModalDialog();
?>
<div class="row borde-inferior">
    <div class="col-md-12 titulo-codificadora">
        <span class="glyphicon glyphicon-search"></span>&nbsp;B&uacute;squeda Avanzada
    </div>
</div>
<div class="row">
    <!-- Criterio búsqueda avanzada + Solapas + Botón NUEVO + Grilla -->
    <div class="col-md-12 responsive">
        <div class="row borde-inferior">

            <!-- Formulario para el criterio de Búsqueda Avanzada-->
            <form id="form_busqueda_avanzada" name="form_busqueda_avanzada" class="form-horizontal" action="index.php?c=expedientesbusquedaavanzada&a=view" method="POST">

                <!-- Fecha Desde + Iniciador + Comisión + Estado -->
                <div class="col-sm-4 col-md-3">
                    <!-- Fecha Desde -->
                    <div class="form-group">
                        <label for="v_fecha_desde" class="col-xs-2 col-sm-2 col-md-4 control-label">Desde:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2">
                            <input id="v_fecha_desde" name="v_fecha_desde" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_desde; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_desde" name="f_fecha_desde" value="<?php echo $f_fecha_desde; ?>" />
                    </div>
                    <!-- Iniciador -->
                    <div class="form-group">
                        <label for="f_iniciador" class="col-xs-2 col-sm-2 col-md-4 control-label">Iniciador:</label>
                        <div class="col-xs-10 col-sm-10 col-md-8">
                            <div class="input-group">
                                <select id="f_iniciador" name="f_iniciador" class="form-control input-sm">
                                    <option value="0">Seleccione</option>
                                    
                                    <?php for ($i=0; $i < $cantidad_iniciadores; $i++) { 
                                        $ini = &$listado_iniciadores[$i]; ?>
                                        <option value="<?=$ini->tipo_grp;?>|<?=$ini->codigo_grp;?>">
                                            <?=($ini->habilitado_grp == '1') ? '* ' : '';?><?=$ini->tipo_grp;?> - <?=$ini->codigo_grp;?> - <?=$ini->descripcion_grp;?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="input-group-addon input-sm">
                                    <a href="javascript:mostrarModalIniciadorAutosugerido();"><span class="glyphicon glyphicon-search"></span></a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Comisión -->
                    <div class="form-group">
                        <label for="f_comision" class="col-xs-2 col-sm-2 col-md-4 control-label">Comisi&oacute;n:</label>
                        <div class="col-xs-10 col-sm-10 col-md-8">
                            <div class="input-group">
                                <select id="f_comision" name="f_comision" class="form-control input-sm">
                                    <option value="0">Seleccione</option>

                                    <?php for ($i=0; $i < $cantidad_comisiones; $i++) { 
                                        $comision = &$listado_comisiones[$i]; ?>
                                        <option value="<?=$comision->codigo_grp;?>">
                                            <?=($comision->habilitado_grp == '1') ? '* ' : '';?><?=$comision->tipo_grp;?> - <?=$comision->codigo_grp;?> - <?=$comision->descripcion_grp;?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="input-group-addon input-sm">
                                    <a href="javascript:mostrarModalComisionAutosugerido();"><span class="glyphicon glyphicon-search"></span></a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Estado -->
                    <div class="form-group">
                        <label for="f_estado" class="col-xs-2 col-sm-2 col-md-4 control-label">Estado:</label>
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
                </div>

                <!-- Fecha Hasta + Categoría + Autor + Tema -->
                <div class="col-sm-4 col-md-3">
                    <!-- Fecha Hasta -->
                    <div class="form-group">
                        <label for="v_fecha_hasta" class="col-xs-2 col-sm-2 col-md-4 control-label">Hasta:</label>
                        <div class="col-xs-2 col-sm-2 col-md-2">
                            <input id="v_fecha_hasta" name="v_fecha_hasta" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_hasta; ?>">
                        </div>
                        <input type="hidden" id="f_fecha_hasta" name="f_fecha_hasta" value="<?php echo $f_fecha_hasta; ?>" />
                    </div>

                    <!-- Categoría -->
                    <div class="form-group">
                        <label for="f_categoria" class="col-xs-2 col-sm-2 col-md-4 control-label">Categor&iacute;a:</label>
                        <div class="col-xs-10 col-sm-10 col-md-8">
                            <div class="input-group">
                                <select id="f_categoria" name="f_categoria" class="form-control input-sm">
                                    <option value="0">Seleccione</option>
                                    <?php for ($i=0; $i < $cantidad_categorias; $i++) { 
                                        $categoria = &$listado_categorias[$i]; ?>
                                        <option value="<?=$categoria->id_codcategoria;?>">
                                            <?=($categoria->habilitado_categoria == '1') ? '* ' : '';?>
                                            <?=$categoria->id_codcategoria;?> - <?=$categoria->descripcion_categoria;?>
                                        </option>
                                    <?php }
                                    //$this->renderOptionList(
                                    //	$listado_categorias, // coleccion
                                    //	'id_codcategoria', // valor del combo
                                    //	array('id_codcategoria', 'descripcion_categoria'), // código + descripcion
                                    //	$f_categoria); // default selected
                                    ?>
                                </select>
                                <span class="input-group-addon input-sm">
                                    <a href="javascript:mostrarModalCategoriaAutosugerida();"><span class="glyphicon glyphicon-search"></span></a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Autor -->
                    <div class="form-group">
                        <label for="f_autor" class="col-xs-2 col-sm-2 col-md-4 control-label">Autor:</label>
                        <div class="col-xs-10 col-sm-10 col-md-8">
                            <div class="input-group">
                                <select id="f_autor" name="f_autor" class="form-control input-sm">
                                    <option value="0">Seleccione</option>

                                    <?php for ($i=0; $i < $cantidad_autores; $i++) { 
                                        $autor = &$listado_codautores[$i]; ?>
                                        <option value="<?=$autor->tipo_grp;?>|<?=$autor->codigo_grp;?>">
                                            <?=($autor->habilitado_grp == '1') ? '* ' : '';?><?=$autor->tipo_grp;?> - <?=$autor->codigo_grp;?> - <?=$autor->descripcion_grp;?>
                                        </option>
                                    <?php }
                                    // $this->renderOptionList(
                                    // 	$listado_codautores, // coleccion
                                    // 	array('tipo_grp', 'codigo_grp'), // valor del combo
                                    // 	array('tipo_grp', 'codigo_grp', 'descripcion_grp'), // información a mostrar para elegir
                                    // 	$f_autor); // valor preseleccionado
                                    ?>
                                </select>
                                <span class="input-group-addon input-sm">
                                    <a href="javascript:mostrarModalAutorAutosugerido();"><span class="glyphicon glyphicon-search"></span></a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Tema -->
                    <div class="form-group">
                        <label for="f_tema" class="col-xs-2 col-sm-2 col-md-4 control-label">Tema:</label>
                        <div class="col-xs-10 col-sm-10 col-md-8">
                            <div class="input-group">
                                <select id="f_tema" name="f_tema" class="form-control input-sm">
                                    <option value="0">Seleccione</option>
                                    
                                    <?php for ($i=0; $i < $cantidad_temas; $i++) { 
                                        $tema = &$listado_codtemas[$i]; ?>
                                        <option value="<?=$tema->id_codtema;?>">
                                            <?=($tema->habilitado_tema == '1') ? '* ' : '';?>
                                            <?=$tema->id_codtema;?> - <?=$tema->descripcion_tema;?>
                                        </option>
                                    <?php }
                                    //$this->renderOptionList(
                                    //	$listado_codtemas, // coleccion
                                    //	'id_codtema', // valor del combo
                                    //	array('id_codtema', 'descripcion_tema'), // código + descripcion
                                    //	$f_tema); // default selected
                                    ?>
                                 </select>
                                 <span class="input-group-addon input-sm">
                                    <a href="javascript:mostrarModalTemaAutosugerido();"><span class="glyphicon glyphicon-search"></span></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- Combo: Todos | sólo Sancionados | sólo Promulgados + Por Extracto o Carátula -->
                <div class="col-sm-4 col-md-2">
                    <div class="form-group">
                        <div class="col-md-12">
                            <!-- Para elegir criterio de fechas: fecha Entrada|fecha Sanción|fecha Promulgación -->
                            <select id="f_opcion_sancionado_promulgado" name="f_opcion_sancionado_promulgado" class="form-control input-sm">
                                <option value="0" <?php echo ($f_opcion_sancionado_promulgado == 0) ? "selected" : ""; ?> >Todos</option>
                                <option value="1" <?php echo ($f_opcion_sancionado_promulgado == 1) ? "selected" : ""; ?> >S&oacute;lo Sancionados</option>
                                <option value="2" <?php echo ($f_opcion_sancionado_promulgado == 2) ? "selected" : ""; ?> >S&oacute;lo Promulgados</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <!-- Por Extracto o Carátula -->
                            <input type="text" id="f_caratula" name="f_caratula" class="form-control input-sm" value="<?php echo $f_caratula; ?>" placeholder="Por Extracto o Car&aacute;tula" />
                        </div>
                    </div>
                    <span class="glyphicon glyphicon-info-sign"></span>&nbsp;Total de Expedientes: <span id="ba_cantidad_resultados"></span>
                </div>

                <!-- Botones: Buscar y Restablecer -->
                <div class="col-sm-4 col-md-2">
                    <button id="btn_buscar" name="btn_buscar" type="button" class="btn btn-primary btn-sm btn-block"><span class="glyphicon glyphicon-search"></span>&nbsp;Buscar</button>

                    <button id="btn_refrescar" name="btn_refrescar" type="button" class="btn btn-primary btn-sm btn-block"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Restablecer</button>

                    <button id="btn_imprimir" name="btn_imprimir" type="button" class="btn btn-primary btn-sm btn-block"><span class="glyphicon glyphicon-print"></span>&nbsp;Imprimir</button>
                </div>

                <!-- Botones: Búsqueda Simple + Por Antecedente + Exportar a Texto + Exportar a Planilla -->
                <div class="col-sm-4 col-md-2 col-xs-separador-superior">
                    <button id="btn_busqueda_simple" type="button" class="btn btn-primary btn-sm btn-block"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes</button>

                    <button id="btn_exportar_texto" type="button" class="btn btn-primary btn-sm btn-block"><span class="glyphicon glyphicon-export"></span>&nbsp;Exportar a Texto</button>

                    <button id="btn_exportar_planilla" type="button" class="btn btn-primary btn-sm btn-block"><span class="glyphicon glyphicon-export"></span>&nbsp;Exportar a Planilla C&aacute;lculo</button>
                </div>

                <!-- Modal para el autosugerido de Iniciadores -->
                <div class="modal fade" id="modalIniciadorAutosugerido" tabindex="-8" role="dialog" aria-labelledby="modalIniciadorAutosugerido" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Buscar Iniciador</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="form-group">
                                        <div class="col-xs-8 col-lg-10">
                                            <input type="text" id="modal_iniciador_sugerido" name="modal_iniciador_sugerido" value="" class="form-control input-sm">
                                        </div>
                                        <div class="col-xs-2 col-lg-2">
                                            <button type="button" id="btCargarIniciadorSugerido" class="btn btn-primary btn-sm" title="Asignar">
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

                <!-- Modal para el autosugerido de Comisiones -->
                <div class="modal fade" id="modalComisionAutosugerido" tabindex="-9" role="dialog" aria-labelledby="modalComisionAutosugerido" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Buscar Comisi&oacute;n</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="form-group">
                                        <div class="col-xs-8 col-lg-10">
                                            <input type="text" id="modal_comision_sugerida" name="modal_comision_sugerida" value="" class="form-control input-sm">
                                        </div>
                                        <div class="col-xs-2 col-lg-2">
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

                <!-- Modal para el autosugerido de Estados -->
                <div class="modal fade" id="modalEstadoAutosugerido" tabindex="-10" role="dialog" aria-labelledby="modalEstadoAutosugerido" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Buscar Estado</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="form-group">
                                        <div class="col-xs-8 col-lg-10">
                                            <input type="text" id="modal_estado_sugerido" name="modal_estado_sugerido" value="" class="form-control input-sm">
                                        </div>
                                        <div class="col-xs-2 col-lg-2">
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

                <!-- Modal para el autosugerido de Categorías -->
                <div class="modal fade" id="modalCategoriaAutosugerida" tabindex="-11" role="dialog" aria-labelledby="modalCategoriaAutosugerida" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Buscar Categor&iacute;a</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="form-group">
                                        <div class="col-xs-8 col-lg-10">
                                            <input type="text" id="modal_categoria_sugerida" name="modal_categoria_sugerida" value="" class="form-control input-sm">
                                        </div>
                                        <div class="col-xs-2 col-lg-2">
                                            <button type="button" id="btCargarCategoriaSugerida" class="btn btn-primary btn-sm" title="Asignar">
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

                <!-- Modal para el autosugerido de Autores -->
                <div class="modal fade" id="modalAutorAutosugerido" tabindex="-12" role="dialog" aria-labelledby="modalAutorAutosugerido" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Buscar Autor</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="form-group">
                                        <div class="col-xs-8 col-lg-10">
                                            <input type="text" id="modal_autor_sugerido" name="modal_autor_sugerido" value="" class="form-control input-sm">
                                        </div>
                                        <div class="col-xs-2 col-lg-2">
                                            <button type="button" id="btCargarAutorSugerido" class="btn btn-primary btn-sm" title="Asignar">
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

                <!-- Modal para el autosugerido de Temas -->
                <div class="modal fade" id="modalTemaAutosugerido" tabindex="-13" role="dialog" aria-labelledby="modalTemaAutosugerido" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Buscar Tema</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="form-group">
                                        <div class="col-xs-8 col-lg-10">
                                            <input type="text" id="modal_tema_sugerido" name="modal_tema_sugerido" value="" class="form-control input-sm">
                                        </div>
                                        <div class="col-xs-2 col-lg-2">
                                            <button type="button" id="btCargarTemaSugerido" class="btn btn-primary btn-sm" title="Asignar">
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
        var iniciadores_sugeridos = [<?php echo $iniciadores_sugeridos; ?>];
        var comisiones_sugeridas = [<?php echo $comisiones_sugeridas; ?>];
        var estados_sugeridos = [<?php echo $estados_sugeridos; ?>];
        var categorias_sugeridas = [<?php echo $categorias_sugeridas; ?>];
        var autores_sugeridos = [<?php echo $autores_sugeridos; ?>];
        var temas_sugeridos = [<?php echo $temas_sugeridos; ?>];

        $('#modal_iniciador_sugerido').autocomplete({
            source: iniciadores_sugeridos
        });

        $('#modal_comision_sugerida').autocomplete({
            source: comisiones_sugeridas
        });

        $('#modal_estado_sugerido').autocomplete({
            source: estados_sugeridos
        });

        $('#modal_categoria_sugerida').autocomplete({
            source: categorias_sugeridas
        });

        $('#modal_autor_sugerido').autocomplete({
            source: autores_sugeridos
        });

        $('#modal_tema_sugerido').autocomplete({
            source: temas_sugeridos
        });
    });
</script>