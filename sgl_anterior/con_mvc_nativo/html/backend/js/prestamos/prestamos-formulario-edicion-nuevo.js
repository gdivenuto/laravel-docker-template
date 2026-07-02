/**
 * [listenerBotonCancelar description]
 * @return {[type]} [description]
 */
var listenerBotonCancelar = function () {
    // Se vuelve a la grilla de préstamos del expediente respectivo
    irA('prestamos', prestamo.anio, prestamo.tipo, prestamo.numero, prestamo.cuerpo, prestamo.alcance);
};

/**
 * [listenerBotonGuardar description]
 * @return {[type]} [description]
 */
var listenerBotonGuardar = function () {

    // Primero se verifica el validador
    if ( formEdicionPrestamo.valid() ) {

        // Paso los valores del formulario al expediente
        asignarValoresPrestamo();

        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=prestamos&a=save&f_grilla=solapa&f_editarsoloinfo=1",
            dataType: 'json', 
            data: JSON.stringify(prestamo)
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null)
                    // Se muestra la grilla de préstamos del expediente respectivo
                    irA('prestamos', respuesta.data.anio, respuesta.data.tipo, respuesta.data.numero, respuesta.data.cuerpo, respuesta.data.alcance);
                else
                    showModal('Error', 'Se esperaba un pr'+e_acentuada+'stamo y no se recibieron resultados.');
            } else 
                showModal('Atenci&oacute;n', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'prestamo') con cada campo del formulario de edición
 */
function asignarValoresPrestamo()
{
    prestamo.fecha_solicitud = $('#f_solo_fecha_solicitud').val()+' '+$('#f_solo_hora_solicitud').val();
    
    // Se separa el tipo y código del Solicitante elegido
    var solicitante_aux = $('#f_solicitante').val().split('|');
    prestamo.solicitante_tipo   = solicitante_aux[0];
    prestamo.solicitante_codigo = solicitante_aux[1];
    
    prestamo.observaciones_prestamo = ($('#f_observaciones_prestamo').val()) ? $('#f_observaciones_prestamo').val(): null;
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object prestamo Objeto Prestamo deserializado
 */
function asignarPrestamoAFormulario()
{
    $('#f_observaciones_prestamo').val(decodeEntities(prestamo.observaciones_prestamo));
}

/**
 * Variables globales
 */
var formEdicionPrestamo; // Referencia al formulario de edición de expediente después de aplicarle validate().

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

    $('#v_fecha_solicitud').focus();

    // Componente de fecha
    $('#v_fecha_solicitud').datepicker({
        altField: '#f_solo_fecha_solicitud',
        onClose: function(fecha_solicitud_elegida) {
            $("#f_fecha_solicitud").val(formatearFechaConGuion(fecha_solicitud_elegida));
        }
    });
    
    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Se utiliza el objeto prestamo para asignar los valores en cada campo del formulario de edición
    asignarPrestamoAFormulario();

    formEdicionPrestamo = $("#formEdicionPrestamo");
    formEdicionPrestamo.validate({
        rules: {
                v_fecha_solicitud: { required: true, regexDate: true }, // Fecha de solicitud
                f_solo_hora_solicitud: { required: true, regexTime: true }, // Hora de solicitud
                f_solicitante: { required: true, seleccionNoCero: true } // Solicitante
               },
        messages: {
                v_fecha_solicitud: {
                    required: "Debe especificar la fecha que se solicita el pr"+e_acentuada+"stamo."
                },
                f_solo_hora_solicitud: {
                    required: "Por favor ingrese la hora que se solicita el pr&eacute;stamo.",
                },
                f_solicitante: {
                    seleccionNoCero: "Por favor seleccione un Solicitante para el pr&eacute;stamo."
                }
            },
        errorLabelContainer: '#msg_error_form'
    });
});