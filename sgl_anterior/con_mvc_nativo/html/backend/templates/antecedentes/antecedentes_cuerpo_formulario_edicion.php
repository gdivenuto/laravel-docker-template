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

// Información del Antecedente
$antecedente = $this->vista->data['antecedente'];

// Transformación del antecedente a JSON para tenerlo disponible en la vista como antecedente.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
$jsonAntecedente = JsonHelper::get()->serializar($antecedente);

$this->generarModalDialog();
?>
<div class="row espacio_interno_5 borde-inferior">
    <div class="col-md-12"><span class="glyphicon glyphicon-edit"></span>&nbsp;Edici&oacute;n del Antecedente para <?php echo ($antecedente->tipo == 'E') ? 'el Expediente' : 'la Nota'; ?>:</div>
</div>
<form id="form_edicion_antecedente" name="form_edicion_antecedente" class="form-horizontal" role="form">
    
    <?php $this->incluirPlantilla('expedientes/expedientes_cabecera_formulario_edicion.php'); ?>
    
    <div class="row">
        <div class="col-md-12">
            <!-- Datos restantes -->
            <div class="form-inline tamanio-texto-small">
                <div class="form-group form-group-inline">
                    <label for="f_anio_a">A&ntilde;o:</label>
                    <input id="f_anio_a" name="f_anio_a" type="text" class="form-control form-control-width-small solo-numero input-sm" value="" >
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_tipo_a">Tipo:</label>
                    <select id="f_tipo_a" name="f_tipo_a" class="form-control input-sm" >
                        <option value="E">Expediente</option>
                        <option value="N">Nota</option>
                        <option value="D">Depto. Ejecutivo</option>
                        <option value="G">Depto. Ejecutivo GDE</option>
                    </select>
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_numero_a">N&uacute;mero:</label>
                    <input id="f_numero_a" name="f_numero_a" type="text" class="form-control form-control-width-small solo-numero input-sm" value="" >
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_digito_a">D&iacute;gito:</label>
                    <input id="f_digito_a" name="f_digito_a" type="text" class="form-control form-control-width-extra-small input-sm" value="" placeholder="0" >
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_cuerpo_a">Cuerpo:</label>
                    <input id="f_cuerpo_a" name="f_cuerpo_a" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" >
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_alcance_a">Alcance:</label>
                    <input id="f_alcance_a" name="f_alcance_a" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" placeholder="0" >
                </div>

                <div class="form-group form-group-inline">
                    <label for="f_cuerpoalcance_a">Cpo. Alc.:</label>
                    <input id="f_cuerpoalcance_a" name="f_cuerpoalcance_a" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" placeholder="0" >
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_anexoalcance_a">Anexo Alc.:</label>
                    <input id="f_anexoalcance_a" name="f_anexoalcance_a" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" placeholder="0" >
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_cuerpoanexoalcance_a">Cpo. Anexo Alc.:</label>
                    <input id="f_cuerpoanexoalcance_a" name="f_cuerpoanexoalcance_a" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" placeholder="0" >
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_anexo_a">Anexo:</label>
                    <input id="f_anexo_a" name="f_anexo_a" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" placeholder="0" >
                </div>
                <div class="form-group form-group-inline">
                    <label for="f_cuerpoanexo_a">Cpo. Anexo:</label>
                    <input id="f_cuerpoanexo_a" name="f_cuerpoanexo_a" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" placeholder="0" >
                </div>
            </div>
        </div>
    </div>
    <br>
    <!-- <div class="row">
        <div class="col-md-12">
            <div class="form-inline tamanio-texto-small">
                <div class="form-group">
                    <label for="f_cuerpoalcance_a">Cuerpo Alcance:</label>
                    <input id="f_cuerpoalcance_a" name="f_cuerpoalcance_a" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" placeholder="0" >
                </div>
                <div class="form-group">
                    <label for="f_anexoalcance_a">Anexo Alcance:</label>
                    <input id="f_anexoalcance_a" name="f_anexoalcance_a" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" placeholder="0" >
                </div>
                <div class="form-group">
                    <label for="f_cuerpoanexoalcance_a">Cuerpo Anexo Alcance:</label>
                    <input id="f_cuerpoanexoalcance_a" name="f_cuerpoanexoalcance_a" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" placeholder="0" >
                </div>
                <div class="form-group">
                    <label for="f_anexo_a">Anexo:</label>
                    <input id="f_anexo_a" name="f_anexo_a" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" placeholder="0" >
                </div>
                <div class="form-group">
                    <label for="f_cuerpoanexo_a">Cuerpo Anexo:</label>
                    <input id="f_cuerpoanexo_a" name="f_cuerpoanexo_a" type="text" class="form-control form-control-width-extra-small solo-numero input-sm" value="" placeholder="0" >
                </div>
            </div>
        </div>
    </div>
    <br> -->

    <div class="form-group">
        <label for="f_observaciones_antecedentes" class="col-md-2 control-label">Observaciones:</label>
        <div class="col-md-8">
            <textarea id="f_observaciones_antecedentes" name="f_observaciones_antecedentes" class="form-control" rows="9"></textarea>
        </div>
    </div>
    <br>
    <div id="form_group_expediente_gde" class="form-group">
        <label for="f_expediente_gde" class="col-md-2 control-label">Expediente GDE:</label>
        <div class="col-md-8">
            <div class="input-group">
                <span id="f_expediente_gde_status" class="input-group-addon">?</span>
                <input id="f_expediente_gde" type="text" class="form-control input-sm" placeholder="Pegue aquí el número completo del expediente en GDE, por ejemplo: EX-2023-00000582- -MUNIMDP-DTASG#SG" value=""></input>
            </div>
        </div>
    </div>
    <br>
    <div class="form-group">
        <div class="col-md-10 col-md-offset-2">
            <button id="btn_guardar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
            <button id="btn_cancelar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Cancelar</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    // Volcado de datos JSON
    var antecedente = <?php echo $jsonAntecedente; ?>;
</script>