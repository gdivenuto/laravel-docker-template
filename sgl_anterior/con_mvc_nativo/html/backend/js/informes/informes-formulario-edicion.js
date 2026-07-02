/**
 * Se vuelve al listado de Informes
 */
var listenerBotonCancelar = function () {
    $(location).attr('href', 'index.php?c={0}&a=view&f_anio={1}&f_tipo={2}&f_numero={3}&f_cuerpo={4}&f_alcance={5}&f_orden_giro={6}&f_fecha_salida_giro={7}'.format(
        'informes', 
        $('#f_anio').val(), 
        $('#f_tipo').val(), 
        $('#f_numero').val(), 
        $('#f_cuerpo').val(), 
        $('#f_alcance').val(),
        $('#f_orden_giro').val(),
        $('#f_fecha_salida_giro').val()
    ));
};

/**
 * Se envían los datos para guardar el Informe
 */
var listenerBotonGuardar = function () {

    // Primero verifico el validador
    if (formEdicionInforme.valid()) {

        // Paso los valores del formulario al informe
        asignarValoresInforme();
        
        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=informes&a=save",
            data: JSON.stringify(informe) // envio los parametros como JSON
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null) {
                    $(location).attr('href', 'index.php?c={0}&a=view&f_anio={1}&f_tipo={2}&f_numero={3}&f_cuerpo={4}&f_alcance={5}&f_orden_giro={6}&f_fecha_salida_giro={7}'.format(
                        'informes', 
                        respuesta.data.anio, 
                        respuesta.data.tipo, 
                        respuesta.data.numero, 
                        respuesta.data.cuerpo, 
                        respuesta.data.alcance, 
                        respuesta.data.orden_giro,
                        $('#f_fecha_salida_giro').val()
                    ));
                }
                else
                    showModal('Error', 'Se esperaba un informe y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'informe') con cada campo del formulario de edición
 */
function asignarValoresInforme()
{
    informe.anio = $('#f_anio').val();
    informe.tipo = $('#f_tipo').val();
    informe.numero = $('#f_numero').val();
    informe.cuerpo = $('#f_cuerpo').val();
    informe.alcance = $('#f_alcance').val();

    informe.orden_giro = $('#f_orden_giro').val();
    informe.orden_informe = $('#f_orden_informe').val();
    informe.fecha_pedido_informe = ($('#f_fecha_pedido_informe').val() != '') ? $('#f_fecha_pedido_informe').val(): null;
    informe.fecha_vuelta_informe = ($('#f_fecha_vuelta_informe').val() != '') ? $('#f_fecha_vuelta_informe').val(): null;
    informe.detalle_informe = ($('#f_detalle_informe').val() != '') ? $('#f_detalle_informe').val(): null;
    informe.observaciones_informe = ($('#f_observaciones_informe').val() != '') ? $('#f_observaciones_informe').val(): null;
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object informe Objeto Informe deserializado
 */
function asignarInformeAFormulario(informe)
{
    $('#f_anio').val(decodeEntities(informe.anio));
    $('#f_tipo').val(decodeEntities(informe.tipo));
    $('#f_numero').val(decodeEntities(informe.numero));
    $('#f_cuerpo').val(decodeEntities(informe.cuerpo));
    $('#f_alcance').val(decodeEntities(informe.alcance));

    $('#f_orden_giro').val((informe.orden_giro == 0) ? '' : decodeEntities(informe.orden_giro));
    $('#f_orden_informe').val((informe.orden_informe == 0) ? '' : decodeEntities(informe.orden_informe));
    $('#f_fecha_pedido_informe').val(decodeEntities(informe.fecha_pedido_informe));
    $('#f_fecha_vuelta_informe').val(decodeEntities(informe.fecha_vuelta_informe));
    $('#f_detalle_informe').val(decodeEntities(informe.detalle_informe));
    $('#f_observaciones_informe').val(decodeEntities(informe.observaciones_informe));
}

/**
 * Variables globales
 */
var formEdicionInforme; // Referencia al formulario de edición del Informe después de aplicarle validate().

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
    // Se define la fecha de pedido
    $('#v_fecha_pedido_informe').datepicker({
        altField: '#f_fecha_pedido_informe',
        onSelect: function(fecha_elegida) {
            // Se establece como valor mínimo de la fecha de vuelta 
            $("#v_fecha_vuelta_informe").datepicker( "option", "minDate", fecha_elegida);
        }
    });
    // Se define la fecha de vuelta, donde su fecha mínima es la fecha de pedido elegida
    $('#v_fecha_vuelta_informe').datepicker({
        altField: '#f_fecha_vuelta_informe', 
        minDate: $('#v_fecha_pedido_informe').val()
    });

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Se utiliza el objeto 'informe' para asignar los valores en cada campo del formulario de edición
    asignarInformeAFormulario(informe);

    // -- Validación del formulario "form_edicion_informe" ------------------
    formEdicionInforme = $("#form_edicion_informe");
    formEdicionInforme.validate({
        rules: {
                v_fecha_pedido_informe: { required: true, regexDate: true },
                v_fecha_vuelta_informe: { required: false, // Puede ser nula
                                          regexDate: true }
               },
        messages: {
                v_fecha_pedido_informe: { 
                    required: 'Debe especificar la fecha de pedido del informe.'
                }
            } 
    });
});