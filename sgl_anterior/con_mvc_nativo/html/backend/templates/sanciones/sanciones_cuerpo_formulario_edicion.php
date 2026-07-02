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

// Información de la Sanción
$sancion = $this->vista->data['sancion'];

// Transformación del giro a JSON para tenerlo disponible en la vista como giro.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonSancion = JsonHelper::get()->serializar($sancion);

// Listado para la carga del combo de Proyectos del expediente respectivo
// para seleccionar uno durante la edición de la Sanción. Debido a que se quiere 
// dar un 'hint' del proyecto, se recorta el extracto a 100 caracteres para mostrarlo.
$listado_proyectos = $this->vista->data['listado_proyectos'];
foreach ($listado_proyectos as $p)
    if (!is_null($p->extracto))
        $p->extracto = substr($p->extracto, 0, 100) . '...';

// Para mostrar correctamente las fechas de la Sanción
$v_fecha_sancion = ( !is_null($sancion->fecha_sancion) ) ? Validator::get()->convertirAFechaVista($sancion->fecha_sancion) : '';
$v_fecha_promulga = ( !is_null($sancion->fecha_promulga) ) ? Validator::get()->convertirAFechaVista($sancion->fecha_promulga) : '';
$v_fecha_veto = ( !is_null($sancion->fecha_veto) ) ? Validator::get()->convertirAFechaVista($sancion->fecha_veto) : '';

// Marca para saber si estamos Editando o Insertando
// para deshabilitar la clave de la sanción o NO
$solo_lectura = ($this->vista->data['bloquear_clave_sancion']) ? 'readonly' : '';
$solo_lectura_select = ($this->vista->data['bloquear_clave_sancion']) ? 'disabled' : '';

$this->generarModalDialog();
?>
<div class="row espacio_interno_5 borde-inferior">
    <div class="col-md-12"><span class="glyphicon glyphicon-edit"></span>&nbsp;Edici&oacute;n de la Sanci&oacute;n para <?php echo ($sancion->tipo == 'E') ? 'el Expediente' : 'la Nota'; ?>:</div>
</div>
<form id="form_edicion_sancion" name="form_edicion_sancion" class="form-horizontal">
    
    <?php $this->incluirPlantilla('expedientes/expedientes_cabecera_formulario_edicion.php'); ?>
    
    <!-- Datos restantes -->
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="f_orden_proyecto" class="col-xs-3 col-sm-3 col-md-2 control-label">Proyectos:</label>
                <div class="col-xs-9 col-sm-9 col-md-8">
                    <select id="f_orden_proyecto" name="f_orden_proyecto" class="form-control input-sm" <?php echo $solo_lectura_select; ?>>
                        <option value="0">---</option><!-- Se muestra aquí 'ro_descripcion_proyecto' -->
                        <?php
                        $this->renderOptionList(
                            $listado_proyectos, // coleccion
                            'orden_proyecto',        // valor del combo
                            array('orden_proyecto', 'ro_descripcion_proyecto', 'extracto'),   // descripcion
                            $sancion->orden_proyecto);        // default selected
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="f_numero_sancion" class="col-xs-3 col-sm-3 col-md-2 control-label">Nro. Sanci&oacute;n:</label>
                <div class="col-xs-9 col-sm-1 col-md-1">
                    <input id="f_numero_sancion" name="f_numero_sancion" type="text" class="form-control input-sm criterio_busqueda_campo_fecha" value="" <?php echo $solo_lectura; ?>>
                </div>
            </div>
            <div class="form-group">
                <label for="v_fecha_sancion" class="col-xs-3 col-sm-3 col-md-2 control-label">Fecha de Sanci&oacute;n:</label>
                <div class="col-xs-3 col-sm-1 col-md-1">
                    <input id="v_fecha_sancion" name="v_fecha_sancion" class="form-control input-sm criterio_busqueda_campo_fecha espacio_interno_izq_5" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_sancion; ?>" <?php echo $solo_lectura; ?>>
                </div>
                <input type="hidden" id="f_fecha_sancion" name="f_fecha_sancion" value="" />
            </div>
            <div class="form-group">
                <label for="v_promulgado_vetado" class="col-xs-3 col-sm-3 col-md-2 control-label">Promulgado/Vetado: </label>
                <div class="col-xs-9 col-sm-4 col-md-4">
                    <div class="btn-group">
                        <button id="btn_promulgado" name="btn_promulgado" type="button" class="btn btn-sm btn-primary active">Promulgado</button>
                        <button id="btn_vetado" name="btn_vetado" type="button" class="btn btn-sm btn-default">Vetado</button>
                    </div>                    
                </div>
            </div>
            <div class="form-group controles-promulgado">
                <label for="f_numero_promulga" class="col-xs-3 col-sm-3 col-md-2 control-label">Nro. Promulgaci&oacute;n:</label>
                <div class="col-xs-3 col-sm-2 col-md-1">
                    <input id="f_numero_promulga" name="f_numero_promulga" type="text" class="form-control input-sm criterio_busqueda_campo_fecha" value="">
                </div>
            </div>
            <div class="form-group controles-promulgado">
                <label for="v_fecha_promulga" class="col-xs-3 col-sm-3 col-md-2 control-label">Fecha de Promulgaci&oacute;n:</label>
                <div class="col-xs-3 col-sm-1 col-md-1">
                    <input id="v_fecha_promulga" name="v_fecha_promulga" class="form-control input-sm criterio_busqueda_campo_fecha espacio_interno_izq_5" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_promulga; ?>">
                </div>
                <input type="hidden" id="f_fecha_promulga" name="f_fecha_promulga" value="" />
            </div>
            <div class="form-group controles-promulgado">
                <label for="f_decreto_promulga" class="col-xs-3 col-sm-3 col-md-2 control-label">Dec. Promulgaci&oacute;n:</label>
                <div class="col-xs-3 col-sm-1 col-md-1">
                    <input id="f_decreto_promulga" name="f_decreto_promulga" type="text" class="form-control input-sm criterio_busqueda_campo_fecha" value="">
                </div>
            </div>
            <div class="form-group controles-vetado">
                <label for="v_fecha_veto" class="col-xs-3 col-sm-3 col-md-2 control-label">Fecha de Veto:</label>
                <div class="col-xs-3 col-sm-1 col-md-1">
                    <input id="v_fecha_veto" name="v_fecha_veto" class="form-control input-sm criterio_busqueda_campo_fecha espacio_interno_izq_5" type="text" placeholder="dd/mm/aaaa" value="<?php echo $v_fecha_veto; ?>">
                </div>
                <input type="hidden" id="f_fecha_veto" name="f_fecha_veto" value="" />
            </div>
            <div class="form-group">
                <label for="f_observaciones_sancion" class="col-xs-3 col-sm-3 col-md-2 control-label">Observaciones:</label>
                <div class="col-xs-12 col-sm-9 col-md-8">
                    <textarea id="f_observaciones_sancion" name="f_observaciones_sancion" class="form-control" rows="7"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-9 col-xs-offset-3 col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">
                    <button id="btn_guardar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
                    <button id="btn_cancelar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    // Volcado de datos JSON
    var sancion = <?php echo $jsonSancion; ?>;
</script>