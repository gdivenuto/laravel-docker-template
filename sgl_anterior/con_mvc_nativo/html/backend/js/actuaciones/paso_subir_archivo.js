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
            errores.push('Debe ingresar un detalle para el archivo.');
        } else {
            // Verifico cantidad minima de caracteres (-1 deshabilita el control)
            if ((paso.opciones['titulo_longitud_min'] >= 0) && ($('#f_titulo').val().length < paso.opciones['titulo_longitud_min']))
                errores.push(`Debe ingresar un detalle para el archivo de al menos ${paso.opciones['titulo_longitud_min']} caracteres.`);

            // Verifico cantidad maxima de caracteres (-1 deshabilita el control)
            if ((paso.opciones['titulo_longitud_max'] >= 0) && ($('#f_titulo').val().length > paso.opciones['titulo_longitud_max']))
                errores.push(`Debe ingresar un detalle para el archivo con menos de ${paso.opciones['titulo_longitud_max']} caracteres.`);
        }

    // Debe tener un archivo seleccionado y no debe superar el tamaño máximo
    let archivos = $('#f_archivo')[0].files; 
    if (archivos.length == 0)
        errores.push("Debe seleccionar un archivo para subir.");
    else {
        if (archivos[0].size > paso.opciones['tamano_mb_max'])
            errores.push(`El archivo no debe superar los ${paso.opciones['tamano_mb_max'] / (1024 * 1024)} MB.`);
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

    // Tipos de archivos y filtro de subida
    var mimetypes = [];
    var extensiones = [];

    for (k in paso.opciones['mimetype_permitidos']) {
        mimetypes.push(`.${k}`); // Fix para Chrome... debe tener la extension
        mimetypes.push(paso.opciones['mimetype_permitidos'][k]);
        extensiones.push(k);
    }

    $('#f_archivo').prop('accept', mimetypes.join(','));
    $('#extension_archivo').html(extensiones.join(', '));

    // Tamaño de archivo maximo
    $('#tamanio_archivo').html(paso.opciones['tamano_mb_max'] / (1024 * 1024));
};

/**
 * Lógica de recuperación de transacción previa para el paso.
 */
recuperarTransaccion = function () {
    // Si no hay transacción, aborto el proceso.
    if (transaccion === null) return false;

    // Recupero valores y actualizo UI
    $('#f_titulo').val(transaccion.f_titulo);

    // La Subida de archivos no puede recuperar el nombre del archivo de la transacción 
    // porque la ruta del mismo no se encuentra disponible.
    
    return true;
};

// ----------------------------------------------------------------------------
// ---- Document Ready --------------------------------------------------------
// ----------------------------------------------------------------------------
$(document).ready(function () {

    // Inicializo la interfase (ver js/actuaciones/paso_common.js)
    inicializarInterfase();
});