// ----------------------------------------------------------------------------
// ---- Funciones y Callbacks -------------------------------------------------
// ----------------------------------------------------------------------------

/**
 * Lógica de validación de datos del paso.
 */
confirmarPasoActual = function () {
    // Reset de errores
    errores = [];

    // Revisores Seleccionados
    var selected = obtenerRevisoresSeleccionados();

    // Verifico cantidad mínima de revisores solamente cuando se requiere revision.
    if ($('#f_requiere_revision').is(":checked")) {
        if (selected.length < paso.opciones['cantidad_minima'])
            errores.push(`Debe elegir al menos ${paso.opciones['cantidad_minima']} revisor(es).`);

        // Verifico cantidad máxima de revisores
        if ((paso.opciones['cantidad_maxima'] >= 0) && (selected.length > paso.opciones['cantidad_maxima']))
            errores.push(`Debe elegir no más de ${paso.opciones['cantidad_maxima']} revisor(es).`);
    }

    return (errores.length == 0);
};

/**
 * Lógica de inicialización de la vista en base a los parametros del paso.
 */
inicializarFormulario = function () {
    if (paso.opciones['paso_ayuda'] != '')
        $('#paso_ayuda').html(paso.opciones['paso_ayuda']);

    $('#f_requiere_revision').prop('checked', paso.opciones['requiere_revision_por_defecto']);

    // Setup de lista de revisores
    $.each(paso.datos['revisores'], function (k, v) {
        var data_row = '';
        data_row += '<div class="col-md-12">';
            data_row += `<button class="btn btn-sm btn-default btn-agregar-revisor" title="Agregar como revisor" data-id_usuario="${v.id_usuario}"><i class="glyphicon glyphicon-plus"></i></button>&nbsp;`;
            data_row += v.nombre;
        data_row += '</div>';
        $('#lista_revisores_disponibles').append(data_row);
    });
};

/**
 * Lógica de recuperación de transacción previa para el paso.
 */
recuperarTransaccion = function () {
    // Si no hay transacción, selecciono los revisores por defecto.
    if (transaccion === null) {
        var revisores = paso.opciones['revisores_default'];
        var requiere_rev = paso.opciones['requiere_revision_por_defecto'];
    } else {
        var revisores = transaccion.f_revisores;
        var requiere_rev = transaccion.f_requiere_revision;
    }

    $.each(revisores, function (k, v) {
        agregarRevisor(v);
    });

    $('#f_requiere_revision').prop('checked', requiere_rev); 
    mostrarSelectorRevisores(requiere_rev);

    return (!transaccion === null);
};

obtenerRevisoresSeleccionados = function () {
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
        var revisores = obtenerRevisoresSeleccionados();
        if (revisores.length > 0) {
            $.each(obtenerRevisoresSeleccionados(), function (k, v) {
                $('#f_revisores').append(`<option value="${v}" selected></option>`);
            });
        } else {
            // Como necesito enviar el campo (aunque este vacío), cambio el 
            // 'fake' multiselect por un input
            $('#f_revisores').remove();
            $('#formPasoActuacion').append('<input type="hidden" name="f_revisores" value="">');
        }
    }
};

callbackBtnAgregarRevisor = function (e) {
    e.preventDefault();
    agregarRevisor($(this).data('id_usuario'));
    mostrarCantidadSeleccionados();
}

callbackBtnQuitarRevisor = function (e) {
    e.preventDefault();
    quitarRevisor($(this).data('id_usuario'));
    mostrarCantidadSeleccionados();
}

callbackChkRequiereRevision = function (e) {
    mostrarSelectorRevisores($('#f_requiere_revision').is(":checked"));
};

mostrarSelectorRevisores = function (mostrar) {
    if (mostrar)
        $('#fila_seleccion_revisores').show();
    else
        $('#fila_seleccion_revisores').hide();
}

mostrarCantidadSeleccionados = function () {
    let cant = $(`div.data-row[data-id_usuario]`).length; 
    $('#cant_seleccionados').html((cant > 0) 
        ? `Seleccionados: ${cant} signatario(s).`
        : 'Ningún signatario seleccionado.'
    );
}

agregarRevisor = function (id_usuario) {
    // Verifico si ya se encuentra agregado
    if ($(`div.data-row[data-id_usuario=${id_usuario}]`).length > 0) 
        return false;

    // Verifico si ya llegue a la cantidad máxima
    var selected = obtenerRevisoresSeleccionados();
    if ((paso.opciones['cantidad_maxima'] >= 0) && (selected.length >= paso.opciones['cantidad_maxima'])) {
        alert(`No se pueden agregar más de ${paso.opciones['cantidad_maxima']} revisores.`);
        return false;
    }

    // Si no existe, lo agrego
    var data_row = '';
    data_row += `<div class="col-md-12 data-row" data-id_usuario="${id_usuario}">`;
        data_row += `<button class="btn btn-sm btn-default btn-quitar-revisor" title="Remover revisor" data-id_usuario="${id_usuario}"><i class="glyphicon glyphicon-remove"></i></button>&nbsp;`;
        data_row += paso.datos['revisores'].find(x => x.id_usuario == id_usuario).nombre;
    data_row += '</div>';
    $('#lista_revisores').append(data_row);

    return true;
}

quitarRevisor = function (id_usuario) {
    // Elimino el data row seleccionado
    $(`div.data-row[data-id_usuario=${id_usuario}]`).remove();
    return true;
}

var callbackBuscar = function () {
    var str = $('#f_busqueda').val().trim().toLowerCase();
    var filtrado = (str != '')
        ? paso.datos['revisores'].filter(x => x.nombre.toLowerCase().includes(str))
        : paso.datos['revisores'];
    
    $('#lista_revisores_disponibles').empty();

    $.each(filtrado, function (k, v) {
        var data_row = '';
        data_row += '<div class="col-md-12">';
            data_row += `<button class="btn btn-sm btn-default btn-agregar-revisor" title="Agregar como revisor" data-id_usuario="${v.id_usuario}"><i class="glyphicon glyphicon-plus"></i></button>&nbsp;`;
            data_row += v.nombre;
        data_row += '</div>';
        $('#lista_revisores_disponibles').append(data_row);
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
    $('#f_requiere_revision').change(callbackChkRequiereRevision);
    $('#lista_revisores_disponibles').on('click', '.btn-agregar-revisor', callbackBtnAgregarRevisor);
    $('#lista_revisores').on('click', '.btn-quitar-revisor', callbackBtnQuitarRevisor);

    // Fix al form para que seleccione todos los revisores antes del submit
    $('#formPasoActuacion').submit(callbackFormSubmit);
});