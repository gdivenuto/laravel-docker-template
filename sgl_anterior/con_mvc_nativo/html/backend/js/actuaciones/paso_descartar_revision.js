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
    if ( ! verificarCheckbox('.op_descartar') )
        errores.push('Debe definir si el documento pendiente de revisión es descartado o no.');
    
    // Hago una verificacion forzada de SI y NO para asegurarme que
    // solamente haya una única opcion seleccionada.
    // Hago una verificacion forzada de SI(false) y NO(true) para asegurarme que
    // solamente haya una opcion seleccionada.
    if (!$('#op_descartar_si').prop('checked') && $('#op_descartar_no').prop('checked')) 
        errores.push('Debe descartar el documento para poder continuar.');
    
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

    // Si el documento posee archivos embebidos, muestro una alerta
    if (paso.datos['es_embebido'])
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
    
    // Si se había asignado al Artículo 11 del Decreto 1404
    if (transaccion.f_op_descartar == 1) {
        $('#op_descartar_si').prop('checked', true);
        $('#op_descartar_no').prop('checked', false);
    }
    else
        // Si NO se había confirmado la firma
        if (transaccion.f_op_descartar == 0) {
            $('#op_descartar_si').prop('checked', false);
            $('#op_descartar_no').prop('checked', true);
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