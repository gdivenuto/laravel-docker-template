// ----------------------------------------------------------------------------
// ---- Funciones y Callbacks -------------------------------------------------
// ----------------------------------------------------------------------------

/**
 * Lógica de validación de datos del paso.
 */
confirmarPasoActual = function () {
    // Reset de errores
    errores = [];

    // Solo confirmo el titulo si esta permitido y es obligatorio...
    if (paso.opciones['titulo_permitido'] && paso.opciones['titulo_obligatorio']) 
        if ($('#f_titulo').val().trim() == '') {
            errores.push('Debe ingresar un título.');
        } else {
            // Verifico cantidad minima de caracteres (-1 deshabilita el control)
            if ((paso.opciones['titulo_longitud_min'] >= 0) && ($('#f_titulo').val().length < paso.opciones['titulo_longitud_min']))
                errores.push(`Debe ingresar un título de al menos ${paso.opciones['titulo_longitud_min']} caracteres.`);

            // Verifico cantidad maxima de caracteres (-1 deshabilita el control)
            if ((paso.opciones['titulo_longitud_max'] >= 0) && ($('#f_titulo').val().length > paso.opciones['titulo_longitud_max']))
                errores.push(`Debe ingresar un título con menos de ${paso.opciones['titulo_longitud_max']} caracteres.`);
        }

    // Solo confirmo el texto cuando es obligatorio
    if (paso.opciones['texto_obligatorio']) {

        // Si el texto debe ser enriquecido
        var texto = (paso.opciones['texto_enriquecido']) 
           ? $($('#f_texto').summernote('code')).text() 
           : $('#f_texto').val();

        if (texto.trim() == '') {
            errores.push('Debe ingresar un texto.');
        } else {
            // Verifico cantidad minima de caracteres (-1 deshabilita el control)
            if ((paso.opciones['texto_longitud_min'] >= 0) && (texto.length < paso.opciones['texto_longitud_min']))
                errores.push(`Debe ingresar un texto de al menos ${paso.opciones['texto_longitud_min']} caracteres.`);

            // Verifico cantidad maxima de caracteres (-1 deshabilita el control)
            if ((paso.opciones['texto_longitud_max'] >= 0) && (texto.length > paso.opciones['texto_longitud_max']))
                errores.push(`Debe ingresar un texto con menos de ${paso.opciones['texto_longitud_max']} caracteres.`);
        }
    }

    return (errores.length == 0);
};

/**
 * Lógica de inicialización de la vista en base a los parametros del paso.
 */
inicializarFormulario = function () {
    // Setup de Titulo
    if (paso.opciones['titulo_permitido']) {
        $('#f_titulo').prop('placeholder', paso.opciones['titulo_placeholder']);

        // Limito el máximo de caracteres por HTML, si aplica
        if (paso.opciones['titulo_longitud_max'] >= 0)
            $('#f_titulo').prop('maxlength', paso.opciones['titulo_longitud_max']);
    } else 
        $('#f_titulo').parents('.form-group').hide(); 

    // Si el texto debe ser enriquecido
    if (paso.opciones['texto_enriquecido']) {
        // Configuracion del editor
        $('#f_texto').summernote({
            placeholder: paso.opciones['texto_placeholder'],
            tabsize: 2,
            height: 270,
            toolbar: [
                // ---- [nombre del grupo, [lista de botones]] -----
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['insert', ['picture']],
                ['para', ['ul', 'ol', 'paragraph']]
            ]
        });
    } else {
        $('#f_texto').prop('rows', 12);
        $('#f_texto').prop('placeholder', paso.opciones['texto_placeholder']);
    }
};

/**
 * Lógica de recuperación de transacción previa para el paso.
 */
recuperarTransaccion = function () {
    // Si no hay transacción, aborto el proceso.
    if (transaccion === null) return false;

    // Recupero valores y actualizo UI
    $('#f_titulo').val(transaccion.f_titulo);

    if (paso.opciones['texto_enriquecido'])
        $('#f_texto').summernote('code', transaccion.f_texto);        
    else
        $('#f_texto').val(transaccion.f_texto);

    return true;
};

// ----------------------------------------------------------------------------
// ---- Document Ready --------------------------------------------------------
// ----------------------------------------------------------------------------
$(document).ready(function () {
    // Inicializo la interfase (ver js/actuaciones/paso_common.js)
    inicializarInterfase();
});