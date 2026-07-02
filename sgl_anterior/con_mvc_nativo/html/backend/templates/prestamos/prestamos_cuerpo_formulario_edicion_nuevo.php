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

// Transformación del préstamo a JSON para tenerlo disponible en la vista como prestamo.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonPrestamo = JsonHelper::get()->serializar($prestamo);

// Listado para la carga del combo de posibles Solicitantes
$listado_solicitantes = $this->vista->data['listado_solicitantes'];

$this->generarModalDialog();
?>
<div class="row espacio_interno_5 borde-inferior">
    <div class="col-md-12">
        <span class="glyphicon glyphicon-edit"></span>
        &nbsp;Edici&oacute;n de <b>nuevo</b> Pr&eacute;stamo para <?php echo ($prestamo->tipo == 'E') ? 'el Expediente' : 'la Nota'; ?> 
        <b><?php echo $prestamo->anio.'-'.$prestamo->tipo.'-'.$prestamo->numero.'-'.$prestamo->cuerpo.'-'.$prestamo->alcance; ?></b>
        &nbsp;&nbsp;&nbsp;
        <b>Estado: <span class="resaltado-advertencia resaltado_espacio">Solicitado</span></b>
    </div>
</div>
<form id="formEdicionPrestamo" name="formEdicionPrestamo" class="form-horizontal" role="form">
    <div class="row">
        <div class="col-md-12">
            <!-- Datos restantes -->
            <div class="form-group">
                <label for="v_fecha_solicitud" class="col-xs-3 col-sm-2 col-md-2 control-label">Fecha:</label>
                <div class="col-xs-3 col-sm-2 col-md-1">
                    <input id="v_fecha_solicitud" name="v_fecha_solicitud" class="form-control input-sm criterio_busqueda_campo_fecha espacio_interno_izq_5" type="text" placeholder="dd/mm/aaaa" value="<?php echo date("d/m/Y"); ?>">
                </div>
                <input type="hidden" id="f_solo_fecha_solicitud" name="f_solo_fecha_solicitud" value="<?php echo date("Y-m-d"); ?>" />
            </div>   
            <div class="form-group">
                <label for="f_solo_hora_solicitud" class="col-xs-3 col-sm-2 col-md-2 control-label">Hora:</label>
                <div class="col-xs-3 col-sm-2 col-md-1">
                    <input type="text" id="f_solo_hora_solicitud" name="f_solo_hora_solicitud" value="<?php echo date("H:i:s"); ?>" size="6" maxlength="8" class="form-control input-sm criterio_busqueda_campo_fecha">
                </div>
            </div>
            <div class="form-group">
                <label for="f_solicitante" class="col-xs-3 col-sm-2 col-md-2 control-label">Solicitado por:</label>
                <div class="col-xs-9 col-sm-8 col-md-8">
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
                </div>
            </div>
            <div class="form-group">
                <label for="f_observaciones_prestamo" class="col-xs-2 col-sm-2 col-md-2 control-label">Observaciones:</label>
                <div class="col-xs-12 col-sm-8 col-md-8">
                    <textarea id="f_observaciones_prestamo" name="f_observaciones_prestamo" class="form-control" rows="12"></textarea>
                </div>
            </div>
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
    var prestamo = <?php echo $jsonPrestamo; ?>;
</script>