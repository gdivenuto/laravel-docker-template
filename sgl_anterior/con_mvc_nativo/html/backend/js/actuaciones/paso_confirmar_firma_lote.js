// ----------------------------------------------------------------------------
// ---- Funciones y Callbacks -------------------------------------------------
// ----------------------------------------------------------------------------

/**
 * Lógica de validación de datos del paso.
 */
confirmarPasoActual = function () {
    // Reset de errores
    errores = [];

    // Se verifica si se ha elegido uno de los radiobuttons
    if ( ! verificarCheckbox('.op_confirmacion') )
        errores.push('Debe definir si desea firmar o no el documento.');
    
    if (paso.opciones['firma_obligatoria']) {
        // Hago una verificacion forzada de SI(false) y NO(true) para asegurarme que
        // solamente haya una opcion seleccionada.
        if (!$('#op_confirmacion_si').prop('checked') && $('#op_confirmacion_no').prop('checked')) 
            errores.push('Debe firmar el documento para poder continuar.');
    }

    return (errores.length == 0);
};

/**
 * Lógica de inicialización de la vista en base a los parametros del paso.
 */
inicializarFormulario = function () {

    if (paso.opciones['paso_ayuda'] != '')
        $('#paso_ayuda').html(paso.opciones['paso_ayuda']);

    // Levanto el lote de documentos a firmar y lo muestro
    var hay_embebidos = false;
    $.each(paso.datos['lote_documentos'], function (i, v) {
        var archivo = v.link.substring(v.link.lastIndexOf('/')+1);
        var icono_embebido = '';
        if (v.es_embebido) {
            hay_embebidos = true;
            icono_embebido = '<span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>&nbsp;';
        }
        $('#lote_documentos').append(`<li class="list-group-item"><a href="${v.link}" target="_blank">${icono_embebido}<span class="glyphicon glyphicon-file"></span>&nbsp;${archivo}</a></li>`);
    });

    // Si el archivo posee al menos un documento con embebidos, muestro una alerta
    if (hay_embebidos)
        $('#alerta_archivo_embebido').removeClass('display_none');
    else 
        $('#alerta_archivo_embebido').addClass('display_none');
};

/**
 * Lógica de recuperación de transacción previa para el paso.
 */
recuperarTransaccion = function () {
    // Si no hay transacción, aborto el proceso.
    if (transaccion === null) return false;

    // Recupero valores y actualizo UI
    
    // Si se había confirmado la firma
    if (transaccion.f_op_confirmacion == 1) {
        $('#op_confirmacion_si').prop('checked', true);
        $('#op_confirmacion_no').prop('checked', false);
    }
    else
        // Si NO se había confirmado la firma
        if (transaccion.f_op_confirmacion == 0) {
            $('#op_confirmacion_si').prop('checked', false);
            $('#op_confirmacion_no').prop('checked', true);
        }
        
    return true;
};

// ----------------------------------------------------------------------------
// ---- Document Ready --------------------------------------------------------
// ----------------------------------------------------------------------------
$(document).ready(function () {
    // Inicializo la interfase (ver js/actuaciones/paso_common.js)
    inicializarInterfase();
});