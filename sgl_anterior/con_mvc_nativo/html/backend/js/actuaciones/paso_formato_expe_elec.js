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
    if ( ! verificarCheckbox('.op_formato') )
        errores.push('Debe seleccionar un formato de exportación.');
    
    // Hago una verificacion forzada de SI y NO para asegurarme que
    // solamente haya una única opcion seleccionada.
    if ( ($('#op_formato_zip').prop('checked') && $('#op_formato_pdf').prop('checked') && $('#op_formato_pdf_publico').prop('checked')) ||
         (!$('#op_formato_zip').prop('checked') && !$('#op_formato_pdf').prop('checked') && !$('#op_formato_pdf_publico').prop('checked'))
       )
        errores.push('Debe seleccionar una única opción.');

    return (errores.length == 0);
};

/**
 * Lógica de inicialización de la vista en base a los parametros del paso.
 */
inicializarFormulario = function () {
    
    if (paso.opciones['paso_ayuda'] != '')
        $('#paso_ayuda').html(paso.opciones['paso_ayuda']);

};

/**
 * Lógica de recuperación de transacción previa para el paso.
 */
recuperarTransaccion = function () {
    // Si no hay transacción, aborto el proceso.
    if (transaccion === null) return false;

    // Recupero valores y actualizo UI
    
    // Si se había seleccionado zip
    if (transaccion.f_op_formato == 'zip') {
        $('#op_formato_zip').prop('checked', true);
        $('#op_formato_pdf').prop('checked', false);
        $('#op_formato_pdf_publico').prop('checked', false);
    }
    else {
        // Si se había seleccionado pdf
        if (transaccion.f_op_formato == 'pdf') {
            $('#op_formato_zip').prop('checked', false);
            $('#op_formato_pdf').prop('checked', true);
            $('#op_formato_pdf_publico').prop('checked', false);
        }
        else {
            // Si se había seleccionado pdf público
            if (transaccion.f_op_formato == 'pdf_publico') {
                $('#op_formato_zip').prop('checked', false);
                $('#op_formato_pdf').prop('checked', false);
                $('#op_formato_pdf_publico').prop('checked', true);
            }
        }
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