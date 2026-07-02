/**
 * Se vuelve al listado de Giros
 */
var listenerBotonCancelar = function () {
    irA('expedientes', $('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val());
};

/**
 * Se guardan los giros cargados
 */
var listenerBotonGuardar = function () {
    // Primero se verifica el validador
    if (formEdicionCargaGiros.valid()) {
        // Se envía el formulario
        formEdicionCargaGiros.submit();
    }
};

/**
 * Variables globales
 */
var formEdicionCargaGiros; // Referencia al formulario de edición del Giro después de aplicarle validate().

/**
 * Entry Point de jQuery
 */
$(document).ready(function () {
    // form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
    fixValidatorBootstrap();

    // Agrego las expresiones regulares extra
    asignarValidatorExpresionesRegulares();

    // Inicialización de los DatePicker
    inicializarDatePicker();

    // Componente de fecha, se establecen los campos del formulario que se actualizan con la fecha seleccionada de cada datepicker
    // Se define la fecha del primer giro
    $('#v_fecha_primer_giro').datepicker({ altField: '#f_fecha_primer_giro'});
    
    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Por defecto se deshabilitan los campos de Observaciones
    $('#f_observaciones_giro_1').prop('readonly', true);
    $('#f_observaciones_giro_2').prop('readonly', true);
    $('#f_observaciones_giro_3').prop('readonly', true);
    $('#f_observaciones_giro_4').prop('readonly', true);
    $('#f_observaciones_giro_5').prop('readonly', true);
    $('#f_observaciones_giro_6').prop('readonly', true);

    $('#f_comision_1').change(function(){
        if ( $('#f_comision_1').val() != '0' )
            $('#f_observaciones_giro_1').prop('readonly', false);
        else
            $('#f_observaciones_giro_1').prop('readonly', true);
    });

    $('#f_comision_2').change(function(){
        if ( $('#f_comision_2').val() != '0' )
            $('#f_observaciones_giro_2').prop('readonly', false);
        else
            $('#f_observaciones_giro_2').prop('readonly', true);
    });

    $('#f_comision_3').change(function(){
        if ( $('#f_comision_3').val() != '0' )
            $('#f_observaciones_giro_3').prop('readonly', false);
        else
            $('#f_observaciones_giro_3').prop('readonly', true);
    });

    $('#f_comision_4').change(function(){
        if ( $('#f_comision_4').val() != '0' )
            $('#f_observaciones_giro_4').prop('readonly', false);
        else
            $('#f_observaciones_giro_4').prop('readonly', true);
    });

    $('#f_comision_5').change(function(){
        if ( $('#f_comision_5').val() != '0' )
            $('#f_observaciones_giro_5').prop('readonly', false);
        else
            $('#f_observaciones_giro_5').prop('readonly', true);
    });

    $('#f_comision_6').change(function(){
        if ( $('#f_comision_6').val() != '0' )
            $('#f_observaciones_giro_6').prop('readonly', false);
        else
            $('#f_observaciones_giro_6').prop('readonly', true);
    });


    // -- Validación del formulario "form_edicion_cargagiro" ------------------
    formEdicionCargaGiros = $("#form_edicion_cargagiro");
    formEdicionCargaGiros.validate({
        rules: {
                v_fecha_primer_giro: { required: true, regexDate: true },
                f_comision_1: { required: true, seleccionNoCero: true }
               },
        messages: {
                v_fecha_entrada_giro: { 
                    required: 'Debe especificar la fecha de entrada del primer giro.'
                },
                f_comision_1: {
                    required: "Por favor seleccione una Comisi&oacute;n.",
                    seleccionNoCero: "Por favor seleccione una Comisi&oacute;n."
                }
            } 
    });
});