/**
 * Se vuelve al listado de Sanciones
 */
var listenerBotonCancelar = function () {
    irA('sanciones', $('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val());
};

/**
 * Se envían los datos para guardar la Sanción
 */
var listenerBotonGuardar = function () {
    // Primero se verifica el validador
    if (formEdicionSancion.valid()) {
        // Paso los valores del formulario a la sancion
        asignarValoresSancion();
        
        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=sanciones&a=save",
            data: JSON.stringify(sancion) // envio los parametros como JSON
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null)
                    irA('sanciones', respuesta.data.anio, respuesta.data.tipo, respuesta.data.numero, respuesta.data.cuerpo, respuesta.data.alcance);
                else
                    showModal('Error', 'Se esperaba una sanci&oacute;n y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Activa/desactiva controles
 */
var listenerBotonPromulgado = function () {
    $('#btn_promulgado').removeClass('btn-default').addClass('btn-primary active');
    $('#btn_vetado').removeClass('btn-primary active').addClass('btn-default');
    $('.controles-promulgado').show();
    $('.controles-vetado').hide();

    $('#f_numero_promulga').prop('disabled', false);
    $('#v_fecha_promulga').prop('disabled', false);
    $('#f_decreto_promulga').prop('disabled', false);

    $('#v_fecha_veto').prop('disabled', true);
};

/**
 * Activa/desactiva controles
 */
var listenerBotonVetado = function () {
    $('#btn_vetado').removeClass('btn-default').addClass('btn-primary active');
    $('#btn_promulgado').removeClass('btn-primary active').addClass('btn-default');
    $('.controles-promulgado').hide();
    $('.controles-vetado').show();

    $('#f_numero_promulga').prop('disabled', true);
    $('#v_fecha_promulga').prop('disabled', true);
    $('#f_decreto_promulga').prop('disabled', true);

    $('#v_fecha_veto').prop('disabled', false);

};

/**
 * Se cargan los atributos (propiedades del objeto 'sancion') con cada campo del formulario de edición
 */
function asignarValoresSancion()
{
    sancion.anio = $('#f_anio').val();
    sancion.tipo = $('#f_tipo').val();
    sancion.numero = $('#f_numero').val();
    sancion.cuerpo = $('#f_cuerpo').val();
    sancion.alcance = $('#f_alcance').val();

    sancion.orden_proyecto = $('#f_orden_proyecto').val();
    sancion.numero_sancion = $('#f_numero_sancion').val();
    sancion.fecha_sancion = $('#f_fecha_sancion').val();

    if ($('#btn_promulgado').hasClass('active')) {
        sancion.numero_promulga = ($('#f_numero_promulga').val()) ? $('#f_numero_promulga').val(): null;
        sancion.fecha_promulga = ($('#f_fecha_promulga').val()) ? $('#f_fecha_promulga').val(): null;
        sancion.decreto_promulga = ($('#f_decreto_promulga').val()) ? $('#f_decreto_promulga').val(): null;
        sancion.fecha_veto = null;
    } else {
        sancion.numero_promulga = null;
        sancion.fecha_promulga = null;
        sancion.decreto_promulga = null;
        sancion.fecha_veto = ($('#f_fecha_veto').val()) ? $('#f_fecha_veto').val(): null;
    }

    sancion.observaciones_sancion = ($('#f_observaciones_sancion').val()) ? $('#f_observaciones_sancion').val(): null;
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object giro Objeto Giro deserializado
 */
function asignarSancionAFormulario(sancion)
{
    $('#f_anio').val(decodeEntities(sancion.anio));
    $('#f_tipo').val(decodeEntities(sancion.tipo));
    $('#f_numero').val(decodeEntities(sancion.numero));
    $('#f_cuerpo').val(decodeEntities(sancion.cuerpo));
    $('#f_alcance').val(decodeEntities(sancion.alcance));

    $('#f_orden_proyecto').val(decodeEntities(sancion.orden_proyecto));
    $('#f_numero_sancion').val(decodeEntities(sancion.numero_sancion));
    $('#f_fecha_sancion').val(decodeEntities(sancion.fecha_sancion));

    if ((sancion.fecha_veto == null) || (sancion.fecha_veto == '')) {
        $('#f_numero_promulga').val(decodeEntities(sancion.numero_promulga));
        $('#f_decreto_promulga').val(decodeEntities(sancion.decreto_promulga));

        $('#v_fecha_promulga').val(decodeEntities(formatearFechaConBarras(sancion.fecha_promulga)));
        $('#f_fecha_promulga').val(decodeEntities(sancion.fecha_promulga));// es el campo hidden con la fecha en formato yyyy-mm-dd

        $('#v_fecha_veto').val('');
        $('#f_fecha_veto').val('');// es el campo hidden con la fecha en formato yyyy-mm-dd
        
        $('#btn_promulgado').trigger('click');

    } else {
        $('#f_numero_promulga').val('');
        $('#f_decreto_promulga').val('');

        $('#v_fecha_promulga').val('');
        $('#f_fecha_promulga').val('');// es el campo hidden con la fecha en formato yyyy-mm-dd

        $('#v_fecha_veto').val(decodeEntities(formatearFechaConBarras(sancion.fecha_veto)));
        $('#f_fecha_veto').val(decodeEntities(sancion.fecha_veto));// es el campo hidden con la fecha en formato yyyy-mm-dd

        $('#btn_vetado').trigger('click');
    }

    $('#f_observaciones_sancion').val(decodeEntities(sancion.observaciones_sancion));
}

/**
 * Variables globales
 */
var formEdicionSancion; // Referencia al formulario de edición de la Sancion después de aplicarle validate().

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

    // Componentes de fecha
    // Se define la fecha de Sanción
    $('#v_fecha_sancion').datepicker({  altField: '#f_fecha_sancion',
                                        onSelect: function(fecha_elegida) {
                                            // Se establece como valor mínimo de la fecha Promulga y de Veto
                                            $("#v_fecha_promulga").datepicker( "option", "minDate", fecha_elegida);
                                            $("#v_fecha_veto").datepicker( "option", "minDate", fecha_elegida);
                                        },
                                        onClose: function(fecha_sancion_elegida) {
                                            $("#f_fecha_sancion").val(formatearFechaConGuion(fecha_sancion_elegida));
                                        }
                                     });
    // Se define la fecha Promulga, donde su fecha mínima es la fecha de Sanción
    $('#v_fecha_promulga').datepicker({ altField: '#f_fecha_promulga', 
                                        minDate: $('#v_fecha_sancion').val(),
                                        onClose: function(fecha_promulga_elegida) {
                                            $("#f_fecha_promulga").val(formatearFechaConGuion(fecha_promulga_elegida));
                                        }
                                      });
    // Se define la fecha de Veto, donde su fecha mínima es la fecha de Sanción
    $('#v_fecha_veto').datepicker({ altField: '#f_fecha_veto', 
                                    minDate: $('#v_fecha_sancion').val(),
                                    onClose: function(fecha_veto_elegida) {
                                        $("#f_fecha_veto").val(formatearFechaConGuion(fecha_veto_elegida));
                                    }
                                  });

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // -- Botones Promulgado/Vetado --------------------------------------------
    $('#btn_promulgado').click(listenerBotonPromulgado);
    $('#btn_vetado').click(listenerBotonVetado);

    // Se utiliza el objeto sancion para asignar los valores en cada campo del formulario de edición
    asignarSancionAFormulario(sancion);

    // -- Validación del formulario "form_edicion_sancion" ------------------
    formEdicionSancion = $("#form_edicion_sancion");
    formEdicionSancion.validate({
        rules: {
                f_orden_proyecto: { required: true, digits: true, seleccionNoCero: true },
                f_numero_sancion: { required: true },
                v_fecha_sancion: { required: true, regexDate: true },
                v_fecha_promulga: { regexDate: true }, // Puede ser nula
                v_fecha_veto: { regexDate: true } // Puede ser nula
               },
        messages: {
                f_orden_proyecto: { 
                    required: "Por favor seleccione el Proyecto.",
                    digits: "Por favor ingrese un n&uacute;mero de Orden de Proyecto v&aacute;lido.",
                    seleccionNoCero: "Por favor ingrese un n&uacute;mero de Orden de Proyecto v&aacute;lido."
                },
                f_numero_sancion: { 
                    required: "Por favor ingrese un n&uacute;mero de sanci&oacute;n v&aacute;lido."
                },
                v_fecha_sancion: { 
                    required: 'Debe especificar la fecha de Sanci&oacute;n.'
                }
            }
    });

});