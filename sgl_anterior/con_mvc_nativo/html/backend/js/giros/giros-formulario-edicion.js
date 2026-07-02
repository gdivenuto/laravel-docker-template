/**
 * Se vuelve al listado de Giros
 */
var listenerBotonCancelar = function () {
    irA('giros', $('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val());
};

/**
 * Se envían los datos para guardar el Giro
 */
var listenerBotonGuardar = function () {

    // Primero verifico el validador
    if (formEdicionGiro.valid()) {

        //alert($("#f_fecha_entrada_giro").val());

        // Paso los valores del formulario al giro
        asignarValoresGiro();
        
        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=giros&a=save",
            data: JSON.stringify(giro) // envio los parametros como JSON
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null)
                    irA('giros', respuesta.data.anio, respuesta.data.tipo, respuesta.data.numero, respuesta.data.cuerpo, respuesta.data.alcance);
                else
                    showModal('Error', 'Se esperaba un giro y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'giro') con cada campo del formulario de edición
 */
function asignarValoresGiro()
{
    giro.anio = $('#f_anio').val();
    giro.tipo = $('#f_tipo').val();
    giro.numero = $('#f_numero').val();
    giro.cuerpo = $('#f_cuerpo').val();
    giro.alcance = $('#f_alcance').val();

    giro.orden_giro = $('#f_orden_giro').val();
    giro.comision_tipo = 'C';
    giro.comision_codigo = $('#f_comision').val();
    giro.fecha_entrada_giro = ($('#f_fecha_entrada_giro').val() != '') ? $('#f_fecha_entrada_giro').val(): null;
    giro.fecha_salida_giro = ($('#f_fecha_salida_giro').val() != '') ? $('#f_fecha_salida_giro').val(): null;
    giro.dictamen_giro = ($('#f_dictamen_giro').val() != '') ? $('#f_dictamen_giro').val(): null;
    giro.observaciones_giro = ($('#f_observaciones_giro').val() != '') ? $('#f_observaciones_giro').val(): null;
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object giro Objeto Giro deserializado
 */
function asignarGiroAFormulario(giro)
{
    $('#f_anio').val(decodeEntities(giro.anio));
    $('#f_tipo').val(decodeEntities(giro.tipo));
    $('#f_numero').val(decodeEntities(giro.numero));
    $('#f_cuerpo').val(decodeEntities(giro.cuerpo));
    $('#f_alcance').val(decodeEntities(giro.alcance));

    $('#f_orden_giro').val((giro.orden_giro == 0) ? '' : decodeEntities(giro.orden_giro));
    $('#f_comision').val(decodeEntities(giro.comision_codigo));
    $('#f_fecha_entrada_giro').val(decodeEntities(giro.fecha_entrada_giro));
    $('#f_fecha_salida_giro').val(decodeEntities(giro.fecha_salida_giro));
    $('#f_dictamen_giro').val(decodeEntities(giro.dictamen_giro));
    $('#f_observaciones_giro').val(decodeEntities(giro.observaciones_giro));
}

/**
 * Se visualiza una modal para buscar una Comisión, utilizando un autosugerido
 */
function mostrarModalComisionAutosugerido() {
    // Se muestra una modal para buscar una Comisión mediante un autosugerido
    $('#modalComisionAutosugerido').modal('show');
    // Se limpia el campo de búsqueda
    $('#modal_comision_sugerida').val('');
    // Se selecciona y se le da el foco al campo de búsqueda por autosugerido
    setfocus('#modal_comision_sugerida');
}

/**
 * Variables globales
 */
var formEdicionGiro; // Referencia al formulario de edición del Giro después de aplicarle validate().

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
    // Se define la fecha de entrada
    $('#v_fecha_entrada_giro').datepicker({ altField: '#f_fecha_entrada_giro',
                                            onSelect: function(fecha_entrada_elegida) {
                                                // Se establece como valor mínimo de la fecha de salida 
                                                $("#v_fecha_salida_giro").datepicker( "option", "minDate", fecha_entrada_elegida);
                                            },
                                            onClose: function(fecha_entrada_elegida) {
                                                $("#f_fecha_entrada_giro").val(formatearFechaConGuion(fecha_entrada_elegida));
                                            }
                                          });
    // Se define la fecha de salida, donde su fecha mínima es la fecha de entrada elegida
    $('#v_fecha_salida_giro').datepicker({ altField: '#f_fecha_salida_giro', 
                                           minDate: $('#v_fecha_entrada_giro').val(),
                                           onClose: function(fecha_salida_elegida) {
                                                $("#f_fecha_salida_giro").val(formatearFechaConGuion(fecha_salida_elegida));
                                           }
                                         });

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    $('#modal_comision_sugerida').keypress( function(e) {
        if ( $('#modal_comision_sugerida').val() != '' && e.which == 13 )
            $('#btCargarComisionSugerida').focus();
    });

    $('#btCargarComisionSugerida').click(function() {
        if ( $('#modal_comision_sugerida').val() != '' ) {
            // Se toma la comisión sugerida y elegida
            var comision_sugerida = $('#modal_comision_sugerida').val();
            // Se separa el código y la descripción
            var aux_comision = comision_sugerida.split('-');
            // Se toma el código
            var codigo_comision = aux_comision[0];
            // Se selecciona la Comisión en el combo
            $('#f_comision').val(codigo_comision);
            // Se oculta la modal
            $('#modalComisionAutosugerido').modal('hide');
        } else
            setfocus('#modal_comision_sugerida');
    });

    // Se utiliza el objeto 'giro' para asignar los valores en cada campo del formulario de edición
    asignarGiroAFormulario(giro);

    $('#v_fecha_salida_giro').change(function() {
        // Si está vacía
        if ($('#v_fecha_salida_giro').val() == '')
            $('#f_fecha_salida_giro').val(''); // Se vacía el valor del campo oculto
    });

    // -- Validación del formulario "form_edicion_giro" ------------------
    formEdicionGiro = $("#form_edicion_giro");
    formEdicionGiro.validate({
        rules: {
                f_comision: { required: true, seleccionNoCero: true },
                v_fecha_entrada_giro: { 
                    required: false, // Puede ser nula
                    regexDate: true 
                },
                v_fecha_salida_giro: { 
                    required: false, // Puede ser nula
                    regexDate: true
                }
               },
        messages: {
                f_comision: {
                    required: "Por favor seleccione una Comisi&oacute;n.",
                    seleccionNoCero: "Por favor seleccione una Comisi&oacute;n."
                }
            } 
    });
});