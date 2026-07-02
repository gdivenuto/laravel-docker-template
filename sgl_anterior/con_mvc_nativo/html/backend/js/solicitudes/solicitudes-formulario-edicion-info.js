/**
 * [listenerBotonCancelar description]
 * @return {[type]} [description]
 */
var listenerBotonCancelar = function () {
    $(location).attr('href','index.php?c=solicitudes&a=view');
};

/**
 * [listenerBotonGuardar description]
 * @return {[type]} [description]
 */
var listenerBotonGuardar = function () {
    // Paso los valores del formulario al expediente
    asignarValoresSolicitud();

    // Peticion asíncrona
    $.ajax({
        method: "POST",
        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
        url: "index.php?c=solicitudes&a=save&f_editarsoloinfo=1",
        dataType: 'json', 
        data: JSON.stringify(solicitud)
    })
    .done(function( respuesta ) {
        if (respuesta.estado == 'OK') {
            if (respuesta.data != null) {
                // Se muestra la grilla general de Préstamos
                $(location).attr('href','index.php?c=solicitudes&a=view');
            } else
                showModal('Error', 'Se esperaba una solicitud y no se recibieron resultados.');
        } else 
            showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {
        showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
    });
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

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Se utiliza el objeto solicitud para asignar los valores en cada campo del formulario de edición
    asignarSolicitudAFormulario();
});