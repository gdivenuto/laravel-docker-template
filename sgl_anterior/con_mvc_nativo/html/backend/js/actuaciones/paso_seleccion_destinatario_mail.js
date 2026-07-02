// ----------------------------------------------------------------------------
// ---- Funciones y Callbacks -------------------------------------------------
// ----------------------------------------------------------------------------

/**
 * Lógica de validación de datos del paso.
 */
confirmarPasoActual = function () {
    // Reset de errores
    errores = [];

    // Destinatarios Seleccionados
    var selected = obtenerDestinatariosSeleccionados();

    // Verifico cantidad mínima de destinatarios
    if (selected.length < paso.opciones['cantidad_minima'])
        errores.push(`Debe elegir al menos ${paso.opciones['cantidad_minima']} destinatarios(s).`);

    // Verifico cantidad máxima de destinatarios
    if ((paso.opciones['cantidad_maxima'] >= 0) && (selected.length > paso.opciones['cantidad_maxima']))
        errores.push(`Debe elegir no más de ${paso.opciones['cantidad_maxima']} destinatario(s).`);

    return (errores.length == 0);
};

/**
 * Lógica de inicialización de la vista en base a los parametros del paso.
 */
inicializarFormulario = function () {
    if (paso.opciones['paso_ayuda'] != '')
        $('#paso_ayuda').html(paso.opciones['paso_ayuda']);

    // Setup de lista de destinatarios
    $.each(paso.datos['destinatarios'], function (k, v) {
        var data_row = '';
        data_row += '<div class="col-md-12">';
            data_row += `<button class="btn btn-sm btn-default btn-agregar-destinatario" title="Agregar como destinatario" data-mail="${v.mail}"><i class="glyphicon glyphicon-plus"></i></button>&nbsp;`;
            data_row += `${v.nombre_completo} &lt;${v.mail}&gt;`;
        data_row += '</div>';
        $('#lista_destinatarios_disponibles').append(data_row);
    });

    // Setup del destinatario manual
    if (paso.opciones['destinatario_manual'])
        $('#f_destinatario_manual').prop('placeholder', paso.opciones['destinatario_manual_placeholder']);
    else 
        $('#f_destinatario_manual').parents('.form-group').hide(); 
};

/**
 * Lógica de recuperación de transacción previa para el paso.
 */
recuperarTransaccion = function () {
    // Si no hay transacción, aborto el proceso.
    if (transaccion === null) return false;

    // Recupero valores y actualizo UI
    $.each(transaccion.f_destinatarios, function (k, v) {
        agregarDestinatario(v);
    });

    return true;
};

obtenerDestinatariosSeleccionados = function () {
    return $('div.data-row').map(
        function (i) { 
            return $(this).data('mail'); 
        }).get();
};

callbackFormSubmit = function (e) {
    // Como este callback se apila con los callbacks anteriores del form 'pasoActuacion'
    // (los que estan definidos en paso_common.js), debo 'reverificar' la validacion del
    // paso para que la lógica no se ejecute cuando hay errores.
    if (confirmarPasoActual()) {
        // Convertimos los seleccionados a un campo 'select multiple escondido' del form
        var destinatarios = obtenerDestinatariosSeleccionados();
        if (destinatarios.length > 0) {
            $.each(obtenerDestinatariosSeleccionados(), function (k, v) {
                $('#f_destinatarios').append(`<option value="${v}" selected></option>`);
            });
        } else {
            // Como necesito enviar el campo (aunque este vacío), cambio el 
            // 'fake' multiselect por un input
            $('#f_destinatarios').remove();
            $('#formPasoActuacion').append('<input type="hidden" name="f_destinatarios" value="">');
        }
    }
};

callbackBtnAgregarDestinatario = function (e) {
    e.preventDefault();
    agregarDestinatario($(this).data('mail'));
    mostrarCantidadSeleccionados();
}

callbackBtnQuitarDestinatario = function (e) {
    e.preventDefault();
    quitarDestinatario($(this).data('mail'));
    mostrarCantidadSeleccionados();
}

callbackAgregarDestinatarioManual = function (e) {
    
    var mail = $('#f_destinatario_manual').val().trim();
    if (mail.match(/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i)) {
        agregarDestinatario(mail);
        $('#f_destinatario_manual').val('');
    }
    mostrarCantidadSeleccionados();
};

mostrarCantidadSeleccionados = function () {
    let cant = $(`div.data-row[data-mail]`).length; 
    $('#cant_seleccionados').html((cant > 0) 
        ? `${cant} destinatario(s) seleccionado(s)`
        : 'Ningún destinatario seleccionado'
    );
}

agregarDestinatario = function (mail) {
    // Verifico si ya se encuentra agregado
    if ($(`div.data-row[data-mail="${mail}"]`).length > 0) 
        return false;

    // Verifico si ya llegue a la cantidad máxima
    var selected = obtenerDestinatariosSeleccionados();
    if ((paso.opciones['cantidad_maxima'] >= 0) && (selected.length >= paso.opciones['cantidad_maxima'])) {
        alert(`No se pueden agregar más de ${paso.opciones['cantidad_maxima']} destinatarios.`);
        return false;
    }

    // Agrego el destinatario, si no existe
    var destinatario = paso.datos['destinatarios'].find(x => x.mail == mail);
    var data_row = '';
    data_row += `<div class="col-md-12 data-row" data-mail="${mail}">`;
        data_row += `<button class="btn btn-sm btn-default btn-quitar-destinatario" title="Remover destinatario" data-mail="${mail}"><i class="glyphicon glyphicon-remove"></i></button>&nbsp;`;
        data_row += (destinatario)
            ? `${destinatario.nombre_completo} &lt;${destinatario.mail}&gt;`
            : `&lt;${mail}&gt;`;
    data_row += '</div>';
    $('#lista_destinatarios').append(data_row);

    return true;
}

quitarDestinatario = function (mail) {
    // Elimino el data row seleccionado
    $(`div.data-row[data-mail="${mail}"]`).remove();
    return true;
}

var callbackBuscar = function () {
    var str = $('#f_busqueda').val().trim().toLowerCase();
    var filtrado = (str != '')
        ? paso.datos['destinatarios'].filter(x => x.mail.toLowerCase().includes(str) || x.nombre_completo.toLowerCase().includes(str))
        : paso.datos['destinatarios'];
    
    $('#lista_destinatarios_disponibles').empty();

    $.each(filtrado, function (k, v) {
        var data_row = '';
        data_row += '<div class="col-md-12">';
            data_row += `<button class="btn btn-sm btn-default btn-agregar-destinatario" title="Agregar como destinatario" data-mail="${v.mail}"><i class="glyphicon glyphicon-plus"></i></button>&nbsp;`;
            data_row += `${v.nombre_completo} &lt;${v.mail}&gt;`;
        data_row += '</div>';
        $('#lista_destinatarios_disponibles').append(data_row);
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

    // Inicializa el hander con una ejecucion "forzada" del metodo
    handlerBuscar = setTimeout(callbackBuscar, buscarTimeout);
    
    // Inicializar botones y controles
    $('#btn_agregar_manual').click(callbackAgregarDestinatarioManual);
    
    $("#f_destinatario_manual").keydown(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            callbackAgregarDestinatarioManual();
        }
    });
    
    $('#btnBuscarDisponible').click(function(e){
        e.preventDefault();
        callbackBuscar();
    });

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
    $('#lista_destinatarios_disponibles').on('click', '.btn-agregar-destinatario', callbackBtnAgregarDestinatario);
    $('#lista_destinatarios').on('click', '.btn-quitar-destinatario', callbackBtnQuitarDestinatario);

    // Fix al form para que seleccione todos los destinatarios antes del submit
    $('#formPasoActuacion').submit(callbackFormSubmit);
});