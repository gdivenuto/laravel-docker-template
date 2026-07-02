/**
 * [listenerBotonCancelar description]
 * @return {[type]} [description]
 */
var listenerBotonCancelar = function () {
    // Si se viene de la grilla general
    if ( $('#f_grilla').val() == 'general')
        // Se muestra la grilla general de Préstamos
        $(location).attr('href','index.php?c=prestamos&a=viewgeneral');
    else
        // Se vuelve a la grilla de préstamos del expediente respectivo
        irA('prestamos', prestamo.anio, prestamo.tipo, prestamo.numero, prestamo.cuerpo, prestamo.alcance);
};

/**
 * [listenerBotonGuardar description]
 * @return {[type]} [description]
 */
var listenerBotonGuardar = function () {

    // Primero se verifica el validador
    if (formEdicionPrestamo.valid()) {
        
        // Paso los valores del formulario al expediente
        asignarValoresPrestamo();

        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=prestamos&a=save&f_grilla="+$('#f_grilla').val()+"&f_nuevo_estado=A&f_nueva_fecha_hora="+$('#f_nueva_fecha').val()+'%20'+$('#f_nueva_hora').val(),
            dataType: 'json', 
            data: JSON.stringify(prestamo)
        })
        .done(function( respuesta ) {
            console.log('Respuesta: '+respuesta);

            if (respuesta.estado == 'OK') {
                if (respuesta.data != null) {
                    // Si se viene de la grilla general
                    if (respuesta.grilla != null && respuesta.grilla == 'general')
                        // Se muestra la grilla general de Préstamos
                        $(location).attr('href','index.php?c=prestamos&a=viewgeneral&f_anio='+respuesta.data.anio+'&f_tipo='+respuesta.data.tipo+'&f_numero='+respuesta.data.numero+'&f_cuerpo='+respuesta.data.cuerpo+'&f_alcance='+respuesta.data.alcance+'&f_digito='+respuesta.data.digito);
                    else
                        // Se muestra la grilla de préstamos del expediente respectivo
                        irA('prestamos', respuesta.data.anio, respuesta.data.tipo, respuesta.data.numero, respuesta.data.cuerpo, respuesta.data.alcance);
                } else
                    showModal('Error', 'Se esperaba un pr'+e_acentuada+'stamo y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
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

    $('#v_nueva_fecha').focus();

    // Componente de fecha
    $('#v_nueva_fecha').datepicker({ altField: '#f_nueva_fecha' });
    
    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Se utiliza el objeto prestamo para asignar los valores en cada campo del formulario de edición
    asignarPrestamoAFormulario();

    formEdicionPrestamo = $("#formEdicionPrestamo");
    formEdicionPrestamo.validate({
        rules: {
                v_nueva_fecha: { required: true, regexDate: true }
               },
        messages: {
                f_v_nueva_fecha: { 
                    required: "Debe especificar la fecha que se anula el pr&eacute;stamo."
                }
            },
        errorLabelContainer: '#msg_error_form'
    });

});
