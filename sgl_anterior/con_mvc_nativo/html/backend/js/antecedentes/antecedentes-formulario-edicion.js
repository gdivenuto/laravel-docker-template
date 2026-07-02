/**
 * Se vuelve al listado de Antecedentes
 */
var listenerBotonCancelar = function () {
    irA('antecedentes', $('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val());
};

/**
 * Se envían los datos para guardar el Antecedente
 */
var listenerBotonGuardar = function () {
    // Tomo valores por defecto
    defaultFromPlaceholder('#f_digito_a');
    defaultFromPlaceholder('#f_anexo_a');
    defaultFromPlaceholder('#f_cuerpoalcance_a');
    defaultFromPlaceholder('#f_anexoalcance_a');
    defaultFromPlaceholder('#f_cuerpoanexoalcance_a');
    defaultFromPlaceholder('#f_cuerpoanexo_a');

    // Primero verifico el validador
    if (formEdicionAntecedente.valid()) {
        
        // Paso los valores del formulario al antecedente
        asignarValoresAntecedente();
        
        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=antecedentes&a=save",
            data: JSON.stringify(antecedente) // envio los parametros como JSON
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null)
                    irA('antecedentes', respuesta.data.anio, respuesta.data.tipo, respuesta.data.numero, respuesta.data.cuerpo, respuesta.data.alcance);
                else
                    showModal('Error', 'Se esperaba un antecedente y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    } 
};

/**
 * Listener que ejecuta la logica de interfase de carga de numero de 
 * expediente desde el GDE.
 */
var listenerCambioExpedienteGDE = function () {
    var nro_gde = $('#f_expediente_gde').val();
    nro_gde = nro_gde.trim().toUpperCase();
    if (nro_gde == '') {
        $('#f_expediente_gde_status').html('?');
        $('#form_group_expediente_gde').removeClass('has-success');
        $('#form_group_expediente_gde').removeClass('has-error');
    } else {
        // Por lo analizado, los numeros de expediente del GDE pueden tener estas formas:
        //    IF-2022-00003073- -MUNIMDP-DDSG#SG
        //    EX-2023-00000303-MUNIMDP-DA#SG
        // Nótese el 'campo' vacío en medio "- -" del primer caso. En la expresión regular,
        // es un grupo de captura opcional: (-[^-]+)?
        var regexp_gde = new RegExp('^[A-Z]{1,10}-([0-9]{4})-0{1,7}([0-9]{1,8})(-[^-]+)?-MUNIMDP-[A-Z#]+$');
        var data_gde = nro_gde.match(regexp_gde);
        if (data_gde) {
            $('#f_anio_a').val(data_gde[1]);
            $('#f_tipo_a').val('G');
            $('#f_numero_a').val(data_gde[2]);
            $('#f_observaciones_antecedentes').val(nro_gde);
            $('#f_expediente_gde_status').html('Ok!');
            $('#form_group_expediente_gde').addClass('has-success');
            $('#form_group_expediente_gde').removeClass('has-error');
            $('#f_tipo_a').change();
        } else {
            $('#f_expediente_gde_status').html('Error');
            $('#form_group_expediente_gde').removeClass('has-success');
            $('#form_group_expediente_gde').addClass('has-error');
        }
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'antecedente') con cada campo del formulario de edición
 */
function asignarValoresAntecedente()
{
    antecedente.anio = $('#f_anio').val();
    antecedente.tipo = $('#f_tipo').val();
    antecedente.numero = $('#f_numero').val();
    antecedente.cuerpo = $('#f_cuerpo').val();
    antecedente.alcance = $('#f_alcance').val();

    antecedente.anio_a = ($('#f_anio_a').val() != '') ? $('#f_anio_a').val() : '';
    antecedente.tipo_a = $('#f_tipo_a').val();
    antecedente.numero_a = ($('#f_numero_a').val() != '') ? $('#f_numero_a').val() : '';
    antecedente.digito_a = ($('#f_digito_a').val() != '') ? $('#f_digito_a').val() : '0';
    antecedente.cuerpo_a = ($('#f_cuerpo_a').val() != '') ? $('#f_cuerpo_a').val() : 0;
    antecedente.alcance_a = ($('#f_alcance_a').val() != '') ? $('#f_alcance_a').val() : 0;
    
    antecedente.anexo_a = ($('#f_anexo_a').val() != '') ? $('#f_anexo_a').val() : 0;
    antecedente.cuerpoalcance_a = ($('#f_cuerpoalcance_a').val() != '') ? $('#f_cuerpoalcance_a').val() : 0;
    antecedente.anexoalcance_a = ($('#f_anexoalcance_a').val() != '') ? $('#f_anexoalcance_a').val() : 0;
    antecedente.cuerpoanexoalcance_a = ($('#f_cuerpoanexoalcance_a').val() != '') ? $('#f_cuerpoanexoalcance_a').val() : 0;
    antecedente.cuerpoanexo_a = ($('#f_cuerpoanexo_a').val() != '') ? $('#f_cuerpoanexo_a').val() : 0;
    
    antecedente.observaciones_antecedentes = ($('#f_observaciones_antecedentes').val()) ? $('#f_observaciones_antecedentes').val(): null;
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object antecedente Objeto Antecedente deserializado
 */
function asignarAntecedenteAFormulario(antecedente)
{
    $('#f_anio').val(decodeEntities(antecedente.anio));
    $('#f_tipo').val(decodeEntities(antecedente.tipo));
    $('#f_numero').val(decodeEntities(antecedente.numero));
    $('#f_cuerpo').val(decodeEntities(antecedente.cuerpo));
    $('#f_alcance').val(decodeEntities(antecedente.alcance));

    $('#f_anio_a').val(decodeEntities(antecedente.anio_a));
    $('#f_tipo_a').val(decodeEntities(antecedente.tipo_a));
    $('#f_numero_a').val(decodeEntities(antecedente.numero_a));
    $('#f_digito_a').val((antecedente.digito_a == 0) ? '' : decodeEntities(antecedente.digito_a));
    $('#f_cuerpo_a').val(decodeEntities(antecedente.cuerpo_a));
    $('#f_alcance_a').val(decodeEntities(antecedente.alcance_a));

    $('#f_anexo_a').val((antecedente.anexo_a == 0) ? '' : decodeEntities(antecedente.anexo_a));
    $('#f_cuerpoalcance_a').val((antecedente.cuerpoalcance_a == 0) ? '' : decodeEntities(antecedente.cuerpoalcance_a));
    $('#f_anexoalcance_a').val((antecedente.anexoalcance_a == 0) ? '' : decodeEntities(antecedente.anexoalcance_a));
    $('#f_cuerpoanexoalcance_a').val((antecedente.cuerpoanexoalcance_a == 0) ? '' : decodeEntities(antecedente.cuerpoanexoalcance_a));
    $('#f_cuerpoanexo_a').val((antecedente.cuerpoanexo_a == 0) ? '' : decodeEntities(antecedente.cuerpoanexo_a));

    $('#f_observaciones_antecedentes').val(decodeEntities(antecedente.observaciones_antecedentes));
}

/**
 * Variables globales
 */
var formEdicionAntecedente; // Referencia al formulario de edición del Antecedente después de aplicarle validate().

/**
 * Entry Point de jQuery
 */
$(document).ready(function () {
    // form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
    fixValidatorBootstrap();

    // Agrego las expresiones regulares extra
    asignarValidatorExpresionesRegulares();

    $('#f_tipo_a').ready(function(){
        if ( $('#f_tipo_a').val() == 'E' || $('#f_tipo_a').val() == 'N' || $('#f_tipo_a').val() == 'G' ) {
            $('#f_digito_a').prop('disabled', true);
            $('#f_anexo_a').prop('disabled', true);
            $('#f_cuerpoalcance_a').prop('disabled', true);
            $('#f_anexoalcance_a').prop('disabled', true);
            $('#f_cuerpoanexoalcance_a').prop('disabled', true);
            $('#f_cuerpoanexo_a').prop('disabled', true);
        } else {
            $('#f_digito_a').prop('disabled', false);
            $('#f_anexo_a').prop('disabled', false);
            $('#f_cuerpoalcance_a').prop('disabled', false);
            $('#f_anexoalcance_a').prop('disabled', false);
            $('#f_cuerpoanexoalcance_a').prop('disabled', false);
            $('#f_cuerpoanexo_a').prop('disabled', false);
        }
    });

    $('#f_tipo_a').change(function(){
        if ( $('#f_tipo_a').val() == 'E' || $('#f_tipo_a').val() == 'N' || $('#f_tipo_a').val() == 'G' ) {
            $('#f_digito_a').prop('disabled', true);
            $('#f_anexo_a').prop('disabled', true);
            $('#f_cuerpoalcance_a').prop('disabled', true);
            $('#f_anexoalcance_a').prop('disabled', true);
            $('#f_cuerpoanexoalcance_a').prop('disabled', true);
            $('#f_cuerpoanexo_a').prop('disabled', true);
        } else {
            $('#f_digito_a').prop('disabled', false);
            $('#f_anexo_a').prop('disabled', false);
            $('#f_cuerpoalcance_a').prop('disabled', false);
            $('#f_anexoalcance_a').prop('disabled', false);
            $('#f_cuerpoanexoalcance_a').prop('disabled', false);
            $('#f_cuerpoanexo_a').prop('disabled', false);
        }
    });

    // Se agrega la funcionalidad al 'input' de #f_expediente_gde para que tome
    // los cambios de tipeo, 'ctrl+v' y pegado con 'boton derecho->pegar'
    $('#form_group_expediente_gde').on('input', '#f_expediente_gde', listenerCambioExpedienteGDE);

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);
    
    // Se utiliza el objeto 'antecedente' para asignar los valores en cada campo del formulario de edición
    asignarAntecedenteAFormulario(antecedente);

    // Validacion del formulario
    var year = moment().year(); // año actual
    var year_from = 1900;

    // -- Validación del formulario "form_edicion_antecedente" ------------------
    formEdicionAntecedente = $("#form_edicion_antecedente");
    formEdicionAntecedente.validate({
        rules: {
            f_anio_a: { required: true, digits: true, range: [year_from, year] },
            f_tipo_a: { required: true, seleccionNoCero: true, seleccionNoVacio: true },
            f_numero_a: { required: true, digits: true },
            f_digito_a: { required: true, regexAlphaNum: true },
            f_cuerpo_a: { required: true, digits: true },
            f_alcance_a: { required: true, digits: true },
            f_anexo_a: { required: true, digits: true },
            f_cuerpoalcance_a: { required: true, digits: true },
            f_anexoalcance_a: { required: true, digits: true },
            f_cuerpoanexoalcance_a: { required: true, digits: true },
            f_cuerpoanexo_a: { required: true, digits: true }
        },
        messages: {
            f_anio_a: {
                required: "Por favor ingrese el a&ntilde;o del antecedente.",
                digits: "Por favor ingrese un a&ntilde;o de antecedente v&aacute;lido.",
                range: "El a&ntilde;o del antecedente debe ser un valor entre {1} y {0}".format(year, year_from)
            },
            f_tipo_a: { 
                required: "Por favor seleccione un tipo de expediente.", 
                seleccionNoCero: "Por favor seleccione un tipo de expediente.", 
                seleccionNoVacio: "Por favor seleccione un tipo de expediente." 
            },
            f_numero_a: { 
                required: "Por favor ingrese el n&uacute;mero del antecedente.",
                digits: "Por favor ingrese un n&uacute;mero de antecedente v&aacute;lido." 
            },
            f_digito_a: {
                required: "Por favor ingrese el d&iacute;gito del antecedente.",
                regexAlphaNum: "Por favor ingrese un d&iacute;gito de antecedente v&aacute;lido. Solamente se permiten letras y n&uacute;meros."
            },
            f_cuerpo_a: { 
                required: "Por favor ingrese el cuerpo del antecedente.",
                digits: "Por favor ingrese un cuerpo de antecedente v&aacute;lido." 
            },
            f_alcance_a: { 
                required: "Por favor ingrese el alcance del antecedente.",
                digits: "Por favor ingrese un alcance de antecedente v&aacute;lido." 
            },
            f_anexo_a: { 
                required: "Por favor ingrese el anexo del antecedente.", 
                digits: "Por favor ingrese un anexo de antecedente v&aacute;lido."  
            },
            f_cuerpoalcance_a: { 
                required: "Por favor ingrese el cuerpo alcance del antecedente.", 
                digits: "Por favor ingrese un cuerpo alcance de antecedente v&aacute;lido."  
            },
            f_anexoalcance_a: { 
                required: "Por favor ingrese el anexo alcance del antecedente.", 
                digits: "Por favor ingrese un anexo alcance de antecedente v&aacute;lido."  
            },
            f_cuerpoanexoalcance_a: { 
                required: "Por favor ingrese el cuerpo anexo alcance del antecedente.", 
                digits: "Por favor ingrese un cuerpo anexo alcance de antecedente v&aacute;lido."  
            },
            f_cuerpoanexo_a: { 
                required: "Por favor ingrese el cuerpo anexo del antecedente.", 
                digits: "Por favor ingrese un cuerpo anexo de antecedente v&aacute;lido." 
            }
        }
    });

    // Sólo se permite el ingreso de valores numéricos en aquellos campos que posean la clase CSS 'solo-numero'
    $('.solo-numero').keyup(function (){
        this.value = (this.value + '').replace(/[^0-9]/g, '');
    });
});