// ----------------------------------------------------------------------------
// ---- Funciones y Callbacks -------------------------------------------------
// ----------------------------------------------------------------------------

/**
 * Lógica de validación de datos del paso.
 */
confirmarPasoActual = function () {
    // Reset de errores
    errores = [];

    // Firmantes Seleccionados
    var selected = obtenerFirmantesSeleccionados();

    // Verifico cantidad mínima de firmantes
    if (selected.length < paso.opciones['cantidad_minima'])
        errores.push(`Debe elegir al menos ${paso.opciones['cantidad_minima']} firmante(s).`);

    // Verifico cantidad máxima de firmantes
    if ((paso.opciones['cantidad_maxima'] >= 0) && (selected.length > paso.opciones['cantidad_maxima']))
        errores.push(`Debe elegir no más de ${paso.opciones['cantidad_maxima']} firmante(s).`);

    return (errores.length == 0);
};

/**
 * Lógica de inicialización de la vista en base a los parametros del paso.
 */
inicializarFormulario = function () {
    if (paso.opciones['paso_ayuda'] != '')
        $('#paso_ayuda').html(paso.opciones['paso_ayuda']);

    // Setup de lista de firmantes
    $.each(paso.datos['firmantes'], function (k, v) {
        var data_row = '';
        data_row += '<div class="col-md-12">';
            data_row += `<button class="btn btn-sm btn-default btn-agregar-firmante" title="Agregar como firmante" data-id_usuario="${v.id_usuario}"><i class="glyphicon glyphicon-plus"></i></button>&nbsp;`;
            data_row += v.nombre;
        data_row += '</div>';
        $('#lista_firmantes_disponibles').append(data_row);
    });
};

/**
 * Lógica de recuperación de transacción previa para el paso.
 */
recuperarTransaccion = function () {
    // Si no hay transacción, selecciono los firmantes por defecto.
    var firmantes = (transaccion === null)
        ? paso.opciones['firmantes_default']
        : transaccion.f_firmantes;

    $.each(firmantes, function (k, v) {
        agregarFirmante(v);
    });
    
    return (!transaccion === null);
};

obtenerFirmantesSeleccionados = function () {
    return $('div.data-row').map(
        function (i) { 
            return $(this).data('id_usuario'); 
        }).get();
};

callbackFormSubmit = function (e) {
    // Como este callback se apila con los callbacks anteriores del form 'pasoActuacion'
    // (los que estan definidos en paso_common.js), debo 'reverificar' la validacion del
    // paso para que la lógica no se ejecute cuando hay errores.
    if (confirmarPasoActual()) {
        // Convertimos los seleccionados a un campo 'select multiple escondido' del form
        var firmantes = obtenerFirmantesSeleccionados();
        if (firmantes.length > 0) {
            $.each(obtenerFirmantesSeleccionados(), function (k, v) {
                $('#f_firmantes').append(`<option value="${v}" selected></option>`);
            });
        } else {
            // Como necesito enviar el campo (aunque este vacío), cambio el 
            // 'fake' multiselect por un input
            $('#f_firmantes').remove();
            $('#formPasoActuacion').append('<input type="hidden" name="f_firmantes" value="">');
        }
    }
};

callbackBtnAgregarFirmante = function (e) {
    e.preventDefault();
    agregarFirmante($(this).data('id_usuario'));
    mostrarCantidadSeleccionados();
}

callbackBtnQuitarFirmante = function (e) {
    e.preventDefault();
    quitarFirmante($(this).data('id_usuario'));
    mostrarCantidadSeleccionados();
}

mostrarCantidadSeleccionados = function () {
    let cant = $(`div.data-row[data-id_usuario]`).length; 
    $('#cant_seleccionados').html((cant > 0) 
        ? `Seleccionados: ${cant} signatario(s).`
        : 'Ningún signatario seleccionado.'
    );
}

agregarFirmante = function (id_usuario) {
    // Verifico si ya se encuentra agregado
    if ($(`div.data-row[data-id_usuario=${id_usuario}]`).length > 0) 
        return false;

    // Verifico si ya llegue a la cantidad máxima
    var selected = obtenerFirmantesSeleccionados();
    if ((paso.opciones['cantidad_maxima'] >= 0) && (selected.length >= paso.opciones['cantidad_maxima'])) {
        alert(`No se pueden agregar más de ${paso.opciones['cantidad_maxima']} firmantes.`);
        return false;
    }

    // Si no existe, lo agrego
    var data_row = '';
    data_row += `<div class="col-md-12 data-row" data-id_usuario="${id_usuario}">`;
        data_row += `<button class="btn btn-sm btn-default btn-quitar-firmante" title="Remover firmante" data-id_usuario="${id_usuario}"><i class="glyphicon glyphicon-remove"></i></button>&nbsp;`;
        data_row += paso.datos['firmantes'].find(x => x.id_usuario == id_usuario).nombre;
    data_row += '</div>';
    $('#lista_firmantes').append(data_row);

    return true;
}

quitarFirmante = function (id_usuario) {
    // Elimino el data row seleccionado
    $(`div.data-row[data-id_usuario=${id_usuario}]`).remove();
    return true;
}

var callbackBuscar = function () {
    var str = $('#f_busqueda').val().trim().toLowerCase();
    var filtrado = (str != '')
        ? paso.datos['firmantes'].filter(x => x.nombre.toLowerCase().includes(str))
        : paso.datos['firmantes'];
    
    $('#lista_firmantes_disponibles').empty();

    $.each(filtrado, function (k, v) {
        var data_row = '';
        data_row += '<div class="col-md-12">';
            data_row += `<button class="btn btn-sm btn-default btn-agregar-firmante" title="Agregar como firmante" data-id_usuario="${v.id_usuario}"><i class="glyphicon glyphicon-plus"></i></button>&nbsp;`;
            data_row += v.nombre;
        data_row += '</div>';
        $('#lista_firmantes_disponibles').append(data_row);
    });
};

var buscarTimeout = 400;

var handlerBuscar = null;
// ----------------------------------------------------------------------------
// ---- Document Ready --------------------------------------------------------
// ----------------------------------------------------------------------------
$(document).ready(function () {
    // Inicializo la interfase (ver js/actuaciones/paso_common.js)
    inicializarInterfase();

    $('#btnBuscarDisponible').click(function(e){
        e.preventDefault();
    });

    // Inicializa el hander con una ejecucion "forzada" del metodo
    handlerBuscar = setTimeout(callbackBuscar, buscarTimeout);
    
    // Desde el boton
    $('#f_busqueda').keydown(function (e) {
        if (e.keyCode == 13)
            e.preventDefault();
        else {
            clearTimeout(handlerBuscar);
            handlerBuscar = setTimeout(callbackBuscar, buscarTimeout);
        }
    });

    mostrarCantidadSeleccionados();

    // Inicializo botones
    $('#lista_firmantes_disponibles').on('click', '.btn-agregar-firmante', callbackBtnAgregarFirmante);
    $('#lista_firmantes').on('click', '.btn-quitar-firmante', callbackBtnQuitarFirmante);

    // Fix al form para que seleccione todos los firmantes antes del submit
    $('#formPasoActuacion').submit(callbackFormSubmit);
});