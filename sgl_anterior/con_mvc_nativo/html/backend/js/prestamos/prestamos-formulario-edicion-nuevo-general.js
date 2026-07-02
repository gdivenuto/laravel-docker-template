/**
 * [listenerBotonCancelar description]
 * @return {[type]} [description]
 */
var listenerBotonCancelar = function () {
    // Se vuelve a la grilla de préstamos del expediente respectivo
    $(location).attr('href', 'index.php?c=prestamos&a=viewgeneral');
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
            url: "index.php?c=prestamos&a=save&f_grilla=general&f_editarsoloinfo=1",
            dataType: 'json', 
            data: JSON.stringify(prestamo)
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null) {
                    // Si el préstamo es de un expediente del Depto. Ejecutivo u Otro Ente
                    if (respuesta.data.tipo == 'D' || respuesta.data.tipo == 'O')
                        // Se muestra la Solicitud generada para dicho Préstamo
                        $(location).attr('href', 'index.php?c=solicitudes&a=view&f_anio='+respuesta.data.anio+'&f_tipo='+respuesta.data.tipo+'&f_numero='+respuesta.data.numero+'&f_cuerpo='+respuesta.data.cuerpo+'&f_alcance='+respuesta.data.alcance+'&f_digito='+respuesta.data.digito);
                    else
                        // Se muestra la Solicitud generada para dicho Préstamo
                        $(location).attr('href', 'index.php?c=prestamos&a=viewgeneral&f_anio='+respuesta.data.anio+'&f_tipo='+respuesta.data.tipo+'&f_numero='+respuesta.data.numero+'&f_cuerpo='+respuesta.data.cuerpo+'&f_alcance='+respuesta.data.alcance);
                } else
                    showModal('Error', 'Se esperaba un pr'+e_acentuada+'stamo y no se recibieron resultados.');
            } else 
                showModal(respuesta.estado, respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
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
    prestamo.anio               = $('#f_anio').val();
    prestamo.tipo               = $('#f_tipo').val();
    prestamo.numero             = $('#f_numero').val();
    prestamo.cuerpo             = $('#f_cuerpo').val();
    prestamo.alcance            = $('#f_alcance').val();
    prestamo.digito             = ($('#f_digito').val() != '') ? $('#f_digito').val() : '0';
    prestamo.cuerpoalcance      = ($('#f_cuerpoalcance').val() != '') ? $('#f_cuerpoalcance').val() : 0;
    prestamo.anexoalcance       = ($('#f_anexoalcance').val() != '') ? $('#f_anexoalcance').val() : 0;
    prestamo.cuerpoanexoalcance = ($('#f_cuerpoanexoalcance').val() != '') ? $('#f_cuerpoanexoalcance').val() : 0;
    prestamo.anexo              = ($('#f_anexo').val() != '') ? $('#f_anexo').val() : 0;
    prestamo.cuerpoanexo        = ($('#f_cuerpoanexo').val() != '') ? $('#f_cuerpoanexo').val() : 0;
    prestamo.fecha_solicitud    = $('#f_solo_fecha_solicitud').val()+' '+$('#f_solo_hora_solicitud').val();
    
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
    $('#f_anio').val(decodeEntities(prestamo.anio));
    $('#f_tipo').val(decodeEntities(prestamo.tipo));
    $('#f_numero').val('');//decodeEntities(prestamo.numero)
    $('#f_cuerpo').val(decodeEntities(prestamo.cuerpo));
    $('#f_alcance').val(decodeEntities(prestamo.alcance));
    $('#f_digito').val(decodeEntities(prestamo.digito));
    $('#f_cuerpoalcance').val(decodeEntities(prestamo.cuerpoalcance));
    $('#f_anexoalcance').val(decodeEntities(prestamo.anexoalcance));
    $('#f_cuerpoanexoalcance').val(decodeEntities(prestamo.cuerpoanexoalcance));
    $('#f_anexo').val(decodeEntities(prestamo.anexo));
    $('#f_cuerpoanexo').val(decodeEntities(prestamo.cuerpoanexo));
    $('#f_observaciones_prestamo').val(decodeEntities(prestamo.observaciones_prestamo));
}

/**
 * Se visualiza una modal para buscar un Solicitante, utilizando un autosugerido
 */
function mostrarModalSolicitanteAutosugerido() {
    // Se muestra una modal para buscar un Solicitante mediante un autosugerido
    $('#modalSolicitanteAutosugerido').modal('show');
    // Se limpia el campo de búsqueda
    $('#modal_solicitante_sugerido').val('');
    // Se selecciona y se le da el foco al campo de búsqueda por autosugerido
    setfocus('#modal_solicitante_sugerido');
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

    $('#f_anio').focus();

    // Componente de fecha
    $('#v_fecha_solicitud').datepicker({ altField: '#f_solo_fecha_solicitud' });
    
    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    $('#modal_solicitante_sugerido').keypress( function(e) {
        if ( $('#modal_solicitante_sugerido').val() != '' && e.which == 13 )
            $('#btCargarSolicitanteSugerido').focus();
    });
    
    $('#btCargarSolicitanteSugerido').click(function() {
        if ( $('#modal_solicitante_sugerido').val() != '' ) {
            // Se toma el solicitante sugerido y elegido
            var solicitante_sugerido = $('#modal_solicitante_sugerido').val();
            // Se separa el tipo, el código y la descripción
            var aux_solicitante = solicitante_sugerido.split('-');
            // Se toma el código
            var codigo_solicitante = aux_solicitante[0]+'|'+aux_solicitante[1];
            // Se selecciona el Iniciador en el combo
            $('#f_solicitante').val(codigo_solicitante);
            // Se oculta la modal
            $('#modalSolicitanteAutosugerido').modal('hide');
        } else
            setfocus('#modal_solicitante_sugerido');
    });

    // Se utiliza el objeto prestamo para asignar los valores en cada campo del formulario de edición
    asignarPrestamoAFormulario();

    // Validacion del formulario
    var year = moment().year(); // año actual

    formEdicionPrestamo = $("#formEdicionPrestamo");
    formEdicionPrestamo.validate({
        rules: {
                f_anio: { required: true, digits: true, range: [1983, year] },
                f_tipo: { required: true, seleccionNoCero: true, seleccionNoVacio: true },
                f_numero: { required: true, digits: true, seleccionNoCero: true },
                f_cuerpo: { required: true, digits: true },
                f_alcance: { required: true, digits: true },
                f_digito: { required: true, regexAlphaNum: true },
                f_cuerpoalcance: { required: true, digits: true },
                f_anexoalcance: { required: true, digits: true },
                f_cuerpoanexoalcance: { required: true, digits: true },
                f_anexo: { required: true, digits: true },
                f_cuerpoanexo: { required: true, digits: true },
                v_fecha_solicitud: { required: true, regexDate: true }, // Fecha de solicitud
                f_solo_hora_solicitud: { required: true, regexTime: true }, // Hora de solicitud
                f_solicitante: { required: true, seleccionNoCero: true } // Solicitante
               },
        messages: {
                f_anio: {
                    required: "Por favor ingrese el a&ntilde;o del pr&eacute;stamo.",
                    digits: "Por favor ingrese un a&ntilde;o de pr&eacute;stamo v&aacute;lido.",
                    range: "El a&ntilde;o del expediente debe ser un valor entre 1983 y {0}".format(year)
                },
                f_tipo: { 
                    required: "Por favor seleccione un tipo de expediente.", 
                    seleccionNoCero: "Por favor seleccione un tipo de expediente.", 
                    seleccionNoVacio: "Por favor seleccione un tipo de expediente." 
                },
                f_numero: { 
                    required: "Por favor ingrese el n&uacute;mero del pr&eacute;stamo.",
                    digits: "Por favor ingrese un n&uacute;mero de pr&eacute;stamo v&aacute;lido.",
                    seleccionNoCero: "Por favor ingrese un n&uacute;mero de expediente.", 
                },
                f_cuerpo: { 
                    required: "Por favor ingrese el cuerpo del pr&eacute;stamo.",
                    digits: "Por favor ingrese un cuerpo de pr&eacute;stamo v&aacute;lido." 
                },
                f_alcance: { 
                    required: "Por favor ingrese el alcance del pr&eacute;stamo.",
                    digits: "Por favor ingrese un alcance de pr&eacute;stamo v&aacute;lido." 
                },
                f_digito: {
                    required: "Por favor ingrese el d&iacute;gito del pr&eacute;stamo.",
                    regexAlphaNum: "Por favor ingrese un d&iacute;gito de pr&eacute;stamo v&aacute;lido. Solamente se permiten letras y n&uacute;meros."
                },
                f_anexo: { 
                    required: "Por favor ingrese el anexo del pr&eacute;stamo.", 
                    digits: "Por favor ingrese un anexo de pr&eacute;stamo v&aacute;lido."  
                },
                f_cuerpoalcance: { 
                    required: "Por favor ingrese el cuerpo alcance del pr&eacute;stamo.", 
                    digits: "Por favor ingrese un cuerpo alcance de pr&eacute;stamo v&aacute;lido."  
                },
                f_anexoalcance: { 
                    required: "Por favor ingrese el anexo alcance del pr&eacute;stamo.", 
                    digits: "Por favor ingrese un anexo alcance de pr&eacute;stamo v&aacute;lido."  
                },
                f_cuerpoanexoalcance: { 
                    required: "Por favor ingrese el cuerpo anexo alcance del pr&eacute;stamo.", 
                    digits: "Por favor ingrese un cuerpo anexo alcance de pr&eacute;stamo v&aacute;lido."  
                },
                f_cuerpoanexo: { 
                    required: "Por favor ingrese el cuerpo anexo del pr&eacute;stamo.", 
                    digits: "Por favor ingrese un cuerpo anexo de pr&eacute;stamo v&aacute;lido." 
                },
                v_fecha_solicitud: {
                    required: "Debe especificar la fecha que se solicita el pr&eacute;stamo."
                },
                f_solo_hora_solicitud: {
                    required: "Por favor ingrese la hora que se solicita el pr&eacute;stamo."
                },
                f_solicitante: {
                    seleccionNoCero: "Por favor seleccione un Solicitante para el pr&eacute;stamo."
                }
            },
        errorLabelContainer: '#msg_error_form'
    });
});