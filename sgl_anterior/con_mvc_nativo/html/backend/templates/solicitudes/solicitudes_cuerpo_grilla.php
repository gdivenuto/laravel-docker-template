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
$c_anio    = Validator::get()->obtenerDefault($this->vista->data['f_anio'], '');
$c_tipo    = Validator::get()->obtenerDefault($this->vista->data['f_tipo'], '0');
$c_numero  = Validator::get()->obtenerDefault($this->vista->data['f_numero'], '');
$c_cuerpo  = Validator::get()->obtenerDefault($this->vista->data['f_cuerpo'], 0);
$c_alcance = Validator::get()->obtenerDefault($this->vista->data['f_alcance'], 0);

// Si se busca por la clave
// si alguno de los campos siguientes no tiene valor,
// se inicializan en cero
if ( $c_anio != '' ) {
    $c_digito             = Validator::get()->obtenerDefault($this->vista->data['f_digito'], '0');
    $c_cuerpoalcance      = Validator::get()->obtenerDefault($this->vista->data['f_cuerpoalcance'], 0);
    $c_anexoalcance       = Validator::get()->obtenerDefault($this->vista->data['f_anexoalcance'], 0);
    $c_cuerpoanexoalcance = Validator::get()->obtenerDefault($this->vista->data['f_cuerpoanexoalcance'], 0);
    $c_anexo              = Validator::get()->obtenerDefault($this->vista->data['f_anexo'], 0);
    $c_cuerpoanexo        = Validator::get()->obtenerDefault($this->vista->data['f_cuerpoanexo'], 0);
} else
    // sino, se muestran vacíos los campos respectivos
    $c_cuerpo = $c_alcance = $c_digito = $c_cuerpoalcance = $c_anexoalcance = $c_cuerpoanexoalcance = $c_anexo = $c_cuerpoanexo = '';

$f_fecha_desde = $this->vista->data['f_fecha_desde'];
$f_fecha_hasta = $this->vista->data['f_fecha_hasta'];
// Para mostrar en los Calendarios de fecha Desde y fecha Hasta
$v_fecha_desde = ( !is_null($f_fecha_desde) ) ? Validator::get()->convertirAFechaVista($f_fecha_desde) : '';
$v_fecha_hasta = ( !is_null($f_fecha_hasta) ) ? Validator::get()->convertirAFechaVista($f_fecha_hasta) : '';

$this->generarModalDialog();
?>
<div class="row">
    <div class="col-md-12 responsive">
        <!-- Botón NUEVO -->
        <div class="row borde-inferior">
            <div class="col-sm-5 col-md-6 col-lg-6">
                <h5>LISTADO DE <b>SOLICITUDES A ENTES EXTERNOS</b></h5>
            </div>
            <div class="col-sm-7 col-md-6 col-lg-6 text-right">
                <button id="btn_buscar" type="button" class="btn btn-primary btn-sm boton-adaptado">
                    <span class="glyphicon glyphicon-search"></span>&nbsp;Buscar
                </button>
                <button id="btn_restablecer" type="button" class="btn btn-primary btn-sm boton-adaptado">
                    <span class="glyphicon glyphicon-repeat"></span>&nbsp;Restablecer
                </button>
                <button id="btn_generar_reporte" type="button" class="btn btn-primary btn-sm boton-adaptado">
                    <span class="glyphicon glyphicon-print"></span>&nbsp;Generar reporte
                </button>
                <button id="btn_volver" type="button" class="btn btn-primary btn-sm boton-adaptado">
                    <span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes
                </button>
            </div>
        </div>
        <!-- Clave del Préstamo -->
        <div class="row borde-inferior">
            <div class="col-md-12">
                <div class="form-inline tamanio-texto-small">
                    <div class="form-group form-group-inline">
                        <label for="f_anio">A&ntilde;o:</label>
                        <input id="f_anio" name="f_anio" type="text" class="form-control form-control-width-small solo-numero input-sm" value="<?php echo $c_anio;?>" maxlength="4">
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="f_tipo">Tipo:</label>
                        <select id="f_tipo" name="f_tipo" class="form-control input-sm">
                            <option value="">&nbsp;</option>
                            <option value="E" <?php echo ($c_tipo == "E") ? "selected" : "";?> >E</option>
                            <option value="N" <?php echo ($c_tipo == "N") ? "selected" : "";?> >N</option>
                            <option value="R" <?php echo ($c_tipo == "R") ? "selected" : "";?> >R</option>
                            <option value="D" <?php echo ($c_tipo == "D") ? "selected" : "";?> >D</option>
                            <option value="O" <?php echo ($c_tipo == "O") ? "selected" : "";?> >O</option>
                        </select>
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="f_numero">N&uacute;mero:</label>
                        <input id="f_numero" name="f_numero" type="text" class="form-control form-control-width-small solo-numero input-sm" value="<?php echo $c_numero;?>">
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="f_cuerpo">Cuerpo:</label>
                        <input id="f_cuerpo" name="f_cuerpo" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="<?php echo $c_cuerpo;?>">
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="f_alcance">Alcance:</label>
                        <input id="f_alcance" name="f_alcance" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="<?php echo $c_alcance;?>">
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="f_digito">D&iacute;gito:</label>
                        <input id="f_digito" name="f_digito" type="text" class="form-control form-control-width-extra-small input-sm" value="<?php echo $c_digito;?>">
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="f_cuerpoalcance">Cuerpo alcance:</label>
                        <input id="f_cuerpoalcance" name="f_cuerpoalcance" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="<?php echo $c_cuerpoalcance;?>">
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="f_anexoalcance">Anexo alcance:</label>
                        <input id="f_anexoalcance" name="f_anexoalcance" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="<?php echo $c_anexoalcance;?>">
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="f_cuerpoanexoalcance">Cuerpo anexo alcance:</label>
                        <input id="f_cuerpoanexoalcance" name="f_cuerpoanexoalcance" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="<?php echo $c_cuerpoanexoalcance;?>">
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="f_anexo">Anexo:</label>
                        <input id="f_anexo" name="f_anexo" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="<?php echo $c_anexo;?>">
                    </div>
                    <div class="form-group form-group-inline">
                        <label for="f_cuerpoanexo">Cuerpo anexo:</label>
                        <input id="f_cuerpoanexo" name="f_cuerpoanexo" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="<?php echo $c_cuerpoanexo;?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Solicitante + Rango de Fechas + Estados -->
        <div class="row borde-inferior">
            <div class="col-md-12">
                <div class="form-inline tamanio-texto-small">
                    <!-- Fecha Desde -->
                    <div class="form-group form-group-inline">
                        <label for="v_fecha_desde">Desde:</label>
                        <input id="v_fecha_desde" name="v_fecha_desde" class="ancho_80 form-control input-sm" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_desde; ?>">
                        <input type="hidden" id="f_fecha_desde" name="f_fecha_desde" value="<?php echo $f_fecha_desde; ?>" />
                    </div>
                    <!-- Fecha Hasta -->
                    <div class="form-group form-group-inline">   
                        <label for="v_fecha_hasta">Hasta:</label>
                        <input id="v_fecha_hasta" name="v_fecha_hasta" class="ancho_80 form-control input-sm" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_hasta; ?>">
                        <input type="hidden" id="f_fecha_hasta" name="f_fecha_hasta" value="<?php echo $f_fecha_hasta; ?>" />
                    </div>
                    <!-- Estados -->
                    <div class="form-group form-group-inline">
                        <b>Estados:</b>&nbsp;
                        <label class="checkbox-inline">
                            <input type="checkbox" id="f_estado_solicitado_hcd" name="f_estado_solicitado_hcd" value="">Solicitado al HCD
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" id="f_estado_solicitado_ee" name="f_estado_solicitado_ee" value="">Solicitado al E.E.
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" id="f_estado_ingresado_ee" name="f_estado_ingresado_ee" value="">Ingresado del E.E.
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" id="f_estado_devuelto_ee" name="f_estado_devuelto_ee" value="">Devuelto al E.E.
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" id="f_estado_anulado" name="f_estado_anulado" value="">Anulado
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <!-- Zona secundaria de errores 
        <div id="row_error_grilla" class="row">
            <div class="col-md-12 responsive contenedor-grilla"><span id="msg_error_grilla" class="help-block">&nbsp;</span></div>
        </div>-->
        <!-- Grilla -->
        <div class="row">
            <div id="grillaSolicitudesContainer" class="col-md-12 responsive contenedor-grilla">
               <!-- La grilla se genera dinamicamente -->
            </div>
        </div>
    </div>
</div>