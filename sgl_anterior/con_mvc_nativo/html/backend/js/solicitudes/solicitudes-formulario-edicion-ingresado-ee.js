/**
 * [listenerBotonCancelar description]
 * @return {[type]} [description]
 */
var listenerBotonCancelar = function () {
    // Se muestra la grilla general de Préstamos
    $(location).attr('href','index.php?c=solicitudes&a=view');
};

/**
 * [listenerBotonGuardar description]
 * @return {[type]} [description]
 */
var listenerBotonGuardar = function () {

    // Primero se verifica el validador
    if (formEdicionSolicitud.valid()) {
        
        // Paso los valores del formulario al expediente
        asignarValoresSolicitud();

        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=solicitudes&a=save&f_nuevo_estado=IEE&f_nueva_fecha_hora="+$('#f_nueva_fecha').val()+'%20'+$('#f_nueva_hora').val(),
            dataType: 'json', 
            data: JSON.stringify(solicitud)
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null) {
                    // Se muestra la grilla general de Préstamos, para Prestar
                    $(location).attr('href','index.php?c=prestamos&a=viewgeneral&f_anio='+respuesta.data.anio+'&f_tipo='+respuesta.data.tipo+'&f_numero='+respuesta.data.numero+'&f_cuerpo='+respuesta.data.cuerpo+'&f_alcance='+respuesta.data.alcance+'&f_digito='+respuesta.data.digito);
                } else
                    showModal('Error', 'Se esperaba una solicitud y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'solicitud') con cada campo del formulario de edición
 */
function asignarValoresSolicitud()
{
    solicitud.observaciones = ($('#f_observaciones').val()) ? $('#f_observaciones').val(): null;
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object solicitud Objeto solicitud deserializado
 */
function asignarSolicitudAFormulario()
{
    $('#f_observaciones').val(decodeEntities(solicitud.observaciones));
}

/**
 * Variables globales
 */
var formEdicionSolicitud; // Referencia al formulario de edición de expediente después de aplicarle validate().

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

    $('#v_nueva_fecha').focus();

    // Componente de fecha
    $('#v_nueva_fecha').datepicker({ altField: '#f_nueva_fecha' });
    
    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Se utiliza el objeto solicitud para asignar los valores en cada campo del formulario de edición
    asignarSolicitudAFormulario();

    formEdicionSolicitud = $("#formEdicionSolicitud");
    formEdicionSolicitud.validate({
        rules: {
                v_nueva_fecha: { required: true, regexDate: true }
               },
        messages: {
                f_v_nueva_fecha: { 
                    required: "Debe especificar la fecha que se ingresa la solicitud."
                }
            },
        errorLabelContainer: '#msg_error_form'
    });

});
