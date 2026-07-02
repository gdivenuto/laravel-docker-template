/**
 * Se vuelve al listado de Participaciones
 */
var listenerBotonCancelar = function () {
    irA('participaciones', $('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val());
};

/**
 * Se envían los datos para guardar la Participacion
 */
var listenerBotonGuardar = function () {

    // Primero verifico el validador
    if (formEdicionExpedEnParticipacion.valid()) {

        //alert($("#f_fecha_inicio").val());

        // Paso los valores del formulario al objeto Exepediente en Participacion
        asignarValoresExpedienteEnParticipacion();
        
        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=participaciones&a=habilitarExpedienteAParticipar",
            data: JSON.stringify(exped_en_participacion) // envio los parametros como JSON
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null)
                    irA('participaciones', respuesta.data.anio, respuesta.data.tipo, respuesta.data.numero, respuesta.data.cuerpo, respuesta.data.alcance);
                else
                    showModal('Error', 'Se esperaba un expediente en participacion y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'exped_en_participacion') con cada campo del formulario de edición
 */
function asignarValoresExpedienteEnParticipacion()
{
    exped_en_participacion.anio = $('#f_anio').val();
    exped_en_participacion.tipo = $('#f_tipo').val();
    exped_en_participacion.numero = $('#f_numero').val();
    exped_en_participacion.cuerpo = $('#f_cuerpo').val();
    exped_en_participacion.alcance = $('#f_alcance').val();

    exped_en_participacion.fecha_inicio = ($('#f_fecha_inicio').val() != '') ? $('#f_fecha_inicio').val(): null;
    exped_en_participacion.fecha_fin = ($('#f_fecha_fin').val() != '') ? $('#f_fecha_fin').val(): null;
    exped_en_participacion.extracto = ($('#f_extracto').val()) ? $('#f_extracto').val(): null;
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object exped_en_participacion Objeto ExpedienteEnParticipacion deserializado
 */
function asignarExpedEnParticipacionAFormulario(exped_en_participacion)
{
    $('#f_anio').val(decodeEntities(exped_en_participacion.anio));
    $('#f_tipo').val(decodeEntities(exped_en_participacion.tipo));
    $('#f_numero').val(decodeEntities(exped_en_participacion.numero));
    $('#f_cuerpo').val(decodeEntities(exped_en_participacion.cuerpo));
    $('#f_alcance').val(decodeEntities(exped_en_participacion.alcance));

    $('#f_fecha_inicio').val(decodeEntities(exped_en_participacion.fecha_inicio));
    $('#f_fecha_fin').val(decodeEntities(exped_en_participacion.fecha_fin));
    $('#f_extracto').val(decodeEntities(exped_en_participacion.extracto));
}

/**
 * Variables globales
 */
var formEdicionExpedEnParticipacion; // Referencia al formulario de edición de la Participacion después de aplicarle validate().

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

    // Componentes de fecha, se establecen los campos del formulario que se actualizan con la fecha seleccionada de cada datepicker
    // Se define la fecha de inicio
    $('#v_fecha_inicio').datepicker({ altField: '#f_fecha_inicio',
                                            // onSelect: function(fecha_inicio_elegida) {
                                            //     // Se establece como valor mínimo de la fecha de salida 
                                            //     $("#v_fecha_fin").datepicker( "option", "minDate", fecha_inicio_elegida);
                                            // },
                                            onClose: function(fecha_inicio_elegida) {
                                                $("#f_fecha_inicio").val(formatearFechaConGuion(fecha_inicio_elegida));
                                            }
                                          });
    // // Se define la fecha de finalizacion, donde su fecha mínima es la fecha de inicio elegida
    // $('#v_fecha_fin').datepicker({ altField: '#f_fecha_fin', 
    //                                        minDate: $('#v_fecha_inicio').val(),
    //                                        onClose: function(fecha_fin_elegida) {
    //                                             $("#f_fecha_fin").val(formatearFechaConGuion(fecha_fin_elegida));
    //                                        }
    //                                      });

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Se utiliza el objeto 'exped_en_participacion' para asignar los valores en cada campo del formulario de edición
    asignarExpedEnParticipacionAFormulario(exped_en_participacion);

    // $('#v_fecha_fin').change(function() {
    //     // Si está vacía
    //     if ($('#v_fecha_fin').val() == '')
    //         $('#f_fecha_fin').val(''); // Se vacía el valor del campo oculto
    // });

    // -- Validación del formulario "form_edicion_exped_en_participacion" ------------------
    formEdicionExpedEnParticipacion = $("#form_edicion_exped_en_participacion");
    formEdicionExpedEnParticipacion.validate({
        rules: {
                f_extracto: { required: true },
                v_fecha_inicio: { 
                    required: false, // Puede ser nula
                    regexDate: true 
                }
               },
        messages: {
                f_extracto: {
                    required: "Por favor ingrese un extracto."
                }
            } 
    });
});