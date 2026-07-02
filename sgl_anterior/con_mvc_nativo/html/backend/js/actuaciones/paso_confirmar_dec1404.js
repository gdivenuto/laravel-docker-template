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
    if ( ! verificarCheckbox('.op_decreto') )
        errores.push('Debe definir si el documento es alcanzado por el Art. 11 Decreto 1404.');
    
    // Hago una verificacion forzada de SI y NO para asegurarme que
    // solamente haya una única opcion seleccionada.
    if ( ($('#op_decreto_si').prop('checked') && $('#op_decreto_no').prop('checked')) ||
         (!$('#op_decreto_si').prop('checked') && !$('#op_decreto_no').prop('checked')))
        errores.push('Debe seleccionar una única opción.');

    return (errores.length == 0);
};

/**
 * Lógica de inicialización de la vista en base a los parametros del paso.
 */
inicializarFormulario = function () {
    
    if (paso.opciones['paso_ayuda'] != '')
        $('#paso_ayuda').html(paso.opciones['paso_ayuda']);

    // Si permite preview y tiene un documento asignado, se setea su vista previa
    if (paso.opciones['permite_preview_documento'] && paso.datos['archivo_preview'] != '')
        $('#vista_previa_documento').prop('src', paso.datos['archivo_preview']);
    else
        $('#vista_previa_documento').parent().closest('div').hide(); 
};

/**
 * Lógica de recuperación de transacción previa para el paso.
 */
recuperarTransaccion = function () {
    // Si no hay transacción, aborto el proceso.
    if (transaccion === null) return false;

    // Recupero valores y actualizo UI
    
    // Si se había asignado al Artículo 11 del Decreto 1404
    if (transaccion.f_op_decreto == 1) {
        $('#op_decreto_si').prop('checked', true);
        $('#op_decreto_no').prop('checked', false);
    }
    else
        // Si NO se había confirmado la firma
        if (transaccion.f_op_decreto == 0) {
            $('#op_decreto_si').prop('checked', false);
            $('#op_decreto_no').prop('checked', true);
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