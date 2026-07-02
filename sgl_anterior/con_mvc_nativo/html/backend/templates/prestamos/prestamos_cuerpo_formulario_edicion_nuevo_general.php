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

// Información del Préstamo
$prestamo = $this->vista->data['prestamo'];
// Para saber a qué grilla volver
$grilla = ($this->vista->data['f_grilla'] != '') ? $this->vista->data['f_grilla'] : 'solapa';

// Transformación del préstamo a JSON para tenerlo disponible en la vista como prestamo.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonPrestamo = JsonHelper::get()->serializar($prestamo);

// Listado para la carga del combo de posibles Solicitantes
$listado_solicitantes = $this->vista->data['listado_solicitantes'];

$solicitantes_sugeridos = '';
$cantidad_solicitantes = count($listado_solicitantes);
for ($i=0; $i < $cantidad_solicitantes; $i++)
    // Si posee la descripción, se utilizará como sugerencia
    $solicitantes_sugeridos .= ($listado_solicitantes[$i]->descripcion_grp != '') ? '"'.$listado_solicitantes[$i]->tipo_grp.'-'.$listado_solicitantes[$i]->codigo_grp.'-'.$listado_solicitantes[$i]->descripcion_grp.'",' : '';

$this->generarModalDialog();
?>
<div class="row espacio_interno_5 borde-inferior">
    <div class="col-md-12">
        <span class="glyphicon glyphicon-edit"></span>
        &nbsp;Edici&oacute;n de <b>nuevo</b> Pr&eacute;stamo.&nbsp;&nbsp;&nbsp;<b>Estado: <span class="resaltado-advertencia resaltado_espacio">Solicitado</span></b>
    </div>
</div>
<form id="formEdicionPrestamo" name="formEdicionPrestamo" class="form-horizontal" role="form">
    <div class="row">
        <!-- Clave del Préstamo -->
        <div class="col-md-12 xs_texto_centrado">
            <div class="form-inline tamanio-texto-small">
                <div class="form-group form-group-inline">
                    <label for="f_anio">A&ntilde;o:</label>
                    <input id="f_anio" name="f_anio" type="text" class="form-control form-control-width-small solo-numero input-sm" value="">
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_tipo">Tipo:</label>
                    <select id="f_tipo" name="f_tipo" class="form-control input-sm">
                        <option value="E">Expediente</option>
                        <option value="N">Nota</option>
                        <option value="R">Recomendaci&oacute;n</option>
                        <option value="D">Depto. Ejecutivo</option>
                        <option value="O">Otro Ente</option>
                    </select>
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_numero">N&uacute;mero:</label>
                    <input id="f_numero" name="f_numero" type="text" class="form-control form-control-width-small solo-numero input-sm" value="">
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_cuerpo">Cuerpo:</label>
                    <input id="f_cuerpo" name="f_cuerpo" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="">
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_alcance">Alcance:</label>
                    <input id="f_alcance" name="f_alcance" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" placeholder="0">
                </div>
               
            </div>
        </div>
    </div>
    <br>
    <div class="form-group">
        <label for="f_digito" class="col-xs-2 col-sm-2 col-md-2 control-label">D&iacute;gito:</label>
        <div class="col-xs-2 col-sm-2 col-md-1">    
            <input type="text" id="f_digito" name="f_digito" value="" placeholder="0" class="form-control input-sm lg_ancho_75">
        </div>
        <label for="f_cuerpoalcance" class="col-xs-2 col-sm-2 col-md-2 control-label">Cuerpo alcance:</label>
        <div class="col-xs-2 col-sm-2 col-md-1">    
            <input type="text" id="f_cuerpoalcance" name="f_cuerpoalcance" value="" placeholder="0" class="form-control input-sm lg_ancho_75">
        </div>
        <label for="f_anexoalcance" class="col-xs-2 col-sm-2 col-md-2 control-label">Anexo alcance:</label>
        <div class="col-xs-2 col-sm-2 col-md-1">    
            <input type="text" id="f_anexoalcance" name="f_anexoalcance" value="" placeholder="0" class="form-control input-sm lg_ancho_75">
        </div>
    </div>
    <div class="form-group">
        <label for="f_cuerpoanexoalcance" class="col-xs-2 col-sm-2 col-md-2 control-label">Cuerpo anexo alcance:</label>
        <div class="col-xs-2 col-sm-2 col-md-1">    
            <input type="text" id="f_cuerpoanexoalcance" name="f_cuerpoanexoalcance" value="" placeholder="0" class="form-control input-sm lg_ancho_75">
        </div>
        <label for="f_anexo" class="col-xs-2 col-sm-2 col-md-2 control-label">Anexo:</label>
        <div class="col-xs-2 col-sm-2 col-md-1">    
            <input type="text" id="f_anexo" name="f_anexo" value="" placeholder="0" class="form-control input-sm lg_ancho_75">
        </div>
        <label for="f_cuerpoanexo" class="col-xs-2 col-sm-2 col-md-2 control-label">Cuerpo Anexo:</label>
        <div class="col-xs-2 col-sm-2 col-md-1">    
            <input type="text" id="f_cuerpoanexo" name="f_cuerpoanexo" value="" placeholder="0" class="form-control input-sm lg_ancho_75">
        </div>
    </div>
    <div class="form-group">
        <label for="v_fecha_solicitud" class="col-xs-3 col-sm-2 col-md-2 control-label">Fecha:</label>
        <div class="col-xs-2 col-sm-2 col-md-2">
            <input id="v_fecha_solicitud" name="v_fecha_solicitud" class="form-control input-sm criterio_busqueda_campo_fecha" type="text" placeholder="dd/mm/aaaa" value="<?php echo date("d/m/Y"); ?>">
        </div>
        <input type="hidden" id="f_solo_fecha_solicitud" name="f_solo_fecha_solicitud" value="<?php echo date("Y-m-d"); ?>" />

        <label for="f_solo_hora_solicitud" class="col-xs-3 col-sm-2 col-md-2 col-lg-1 control-label">Hora:</label>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">    
            <input type="text" id="f_solo_hora_solicitud" name="f_solo_hora_solicitud" value="<?php echo date("H:i:s"); ?>" size="6" maxlength="8" class="form-control input-sm criterio_busqueda_campo_fecha">
        </div>
    </div>
    <div class="form-group">
        <label for="f_solicitante" class="col-xs-3 col-sm-2 col-md-2 control-label">Solicitado por:</label>
        <div class="col-xs-9 col-sm-8 col-md-8">
            <div class="input-group">
                <select id="f_solicitante" name="f_solicitante" class="form-control input-sm">
                    <option value="0">---</option>
                    <?php
                    $this->renderOptionList(
                        $listado_solicitantes,            // coleccion
                        array('tipo_grp', 'codigo_grp'), // valor del combo
                        array('tipo_grp', 'codigo_grp', 'descripcion_grp') // información a elegir
                    );
                    ?>
                </select>
                <span class="input-group-addon input-sm">
                    <a href="javascript:mostrarModalSolicitanteAutosugerido();"><span class="glyphicon glyphicon-search"></span></a>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="f_observaciones_prestamo" class="col-xs-3 col-sm-2 col-md-2 control-label">Observaciones:</label>
        <div class="col-xs-12 col-sm-8 col-md-8">
            <textarea id="f_observaciones_prestamo" name="f_observaciones_prestamo" class="form-control xs_textarea" rows="12"></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-9 col-xs-offset-3 col-sm-10 col-sm-offset-2 col-md-10 col-md-offset-2">
            <button id="btn_guardar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
            <button id="btn_cancelar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Cancelar</button>
        </div>
    </div>
    <!-- Modal para el autosugerido de Solicitantes -->
    <div class="modal fade" id="modalSolicitanteAutosugerido" tabindex="-8" role="dialog" aria-labelledby="modalSolicitanteAutosugerido" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Buscar Solicitante</h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group">
                            <div class="col-xs-8 col-sm-10 col-md-10 col-lg-10">
                                <input type="text" id="modal_solicitante_sugerido" name="modal_solicitante_sugerido" value="" class="form-control input-sm">
                            </div>
                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                <button type="button" id="btCargarSolicitanteSugerido" class="btn btn-primary btn-sm" title="Asignar">
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
<script type="text/javascript">
    // Volcado de datos JSON
    var prestamo = <?php echo $jsonPrestamo; ?>;

    $( function() {
        var solicitantes_sugeridos = [<?php echo $solicitantes_sugeridos; ?>];

        $('#modal_solicitante_sugerido').autocomplete({
            source: solicitantes_sugeridos
        });
    });
</script>