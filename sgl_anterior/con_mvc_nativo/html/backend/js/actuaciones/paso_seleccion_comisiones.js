// --------------------------------------------------------------------------
// ---- Funciones y Callbacks -----------------------------------------------
// --------------------------------------------------------------------------

/**
 * Lógica de validación de datos del paso.
 */
confirmarPasoActual = function () {
    // Reset de errores
    errores = [];

    // Comisiones seleccionadas
    var selected = obtenerComisionesSeleccionados();

    // Verifico cantidad mínima de comisiones
    if (selected.length < paso.opciones['cantidad_minima'])
        errores.push(`Debe elegir al menos ${paso.opciones['cantidad_minima']} comision(s).`);

    return (errores.length == 0);
};

/**
 * Lógica de inicialización de la vista en base a los parametros del paso.
 */
inicializarFormulario = function () {

    if (paso.datos['ocultar_ppc'])
        $('#fila_chk_ppc').addClass('display_none');
    else
        $('#fila_chk_ppc').removeClass('display_none');

    if (paso.opciones['paso_ayuda'] != '')
        $('#paso_ayuda').html(paso.opciones['paso_ayuda']);

    // Setup de lista de comisiones
    $.each(paso.datos['comisiones'], function (k, v) {
        var data_row = '';
        data_row += '<div class="col-md-12">';
            data_row += `<button class="btn btn-sm btn-default btn-agregar-comision" title="Agregar" data-codigo_grp="${v.codigo_grp}"><i class="glyphicon glyphicon-plus"></i></button>&nbsp;`;
            data_row += v.descripcion_grp;
        data_row += '</div>';
        $('#lista_comisiones_disponibles').append(data_row);
    });
};

/**
 * Lógica de recuperación de transacción previa para el paso.
 */
recuperarTransaccion = function () {
    // Si no hay transacción, aborto el proceso.
    if (transaccion === null) return false;

    // --- Recupero valores y actualizo UI -----------
    
    // Por cada comisión en la transacción
    for (i=0; i < transaccion.f_comisiones.length; i++) {
        // Se agrega
        agregarComision(transaccion.f_comisiones[i]);
        // Se asigna su observación
        $(`input[data-obs_comision=${transaccion.f_comisiones[i]}]`).val(transaccion.f_observaciones[i]);
    }

    // Si se marcó la opción Para considerar en Participación Ciudadana
    if (transaccion.f_ppc == 1)
        // Se marca
        $('#f_ppc').prop('checked', true);

    return true;
};

obtenerComisionesSeleccionados = function () {
    return $('div.data-row').map(
        function (i) { 
            return $(this).data('codigo_grp'); 
        }).get();
};

obtenerObservaciones = function () {
    return $('div.data-row input.data-obs').map(
        function (i) { 
            return $(this).val(); 
        }).get();
};

callbackFormSubmit = function (e) {
    // Convertimos los seleccionados a un campo 'select multiple escondido' del form
    var comisiones = obtenerComisionesSeleccionados();
    if (comisiones.length > 0) {
        $.each(obtenerComisionesSeleccionados(), function (k, v) {
            $('#f_comisiones').append(`<option value="${v}" selected></option>`);
        });

        // Incluyo tambien las observaciones
        $.each(obtenerObservaciones(), function (k, v) {
            $('#f_observaciones').append(`<option value="${v}" selected></option>`);
        });

        // Elimino los campos temporales del form
        $('div.data-row input.data-obs').remove();
    } else {
        // Como necesito enviar el campo (aunque este vacío), cambio el 
        // 'fake' multiselect por un input
        $('#f_comisiones').remove();
        $('#f_observaciones').remove();
        $('#formPasoActuacion').append('<input type="hidden" name="f_comisiones" value="">');
        $('#formPasoActuacion').append('<input type="hidden" name="f_observaciones" value="">');
    }
};

callbackBtnAgregarComision = function (e) {
    e.preventDefault();
    agregarComision($(this).data('codigo_grp'));
    mostrarCantidadSeleccionados();
}

callbackBtnMoverArriba = function (e) {
    e.preventDefault();
    var fila = $(this).parent();

    if (fila.prev().length > 0) {
        fila.insertBefore(fila.prev());
        renumerarComisiones();
    }
}

callbackBtnMoverAbajo = function (e) {
    e.preventDefault();
    var fila = $(this).parent();
    
    if (fila.next().length > 0) {
        fila.insertAfter(fila.next());
        renumerarComisiones();
    }
}

callbackBtnQuitarComision = function (e) {
    e.preventDefault();
    quitarComision($(this).data('codigo_grp'));
    mostrarCantidadSeleccionados();
}

mostrarCantidadSeleccionados = function () {
    let cant = $(`div.data-row[data-codigo_grp]`).length; 
    $('#cant_seleccionados').html((cant > 0) 
        ? `${cant} comisiones seleccionada(s).`
        : 'Ninguna comisión seleccionada.'
    );
}

agregarComision = function (codigo_grp) {

    // Verifico si ya se encuentra agregado
    if ($(`div.data-row[data-codigo_grp=${codigo_grp}]`).length > 0) 
        return false;

    // Si no existe, lo agrego
    var data_row = '';
    data_row += `<div class="col-md-12 data-row" data-codigo_grp="${codigo_grp}">`;
        // Botones para subir o bajar las Comisiones
        data_row += `<button class="btn btn-sm btn-default btn-mover-arriba" title="Subir Comisi&oacute;n"><i class="glyphicon glyphicon-arrow-up"></i></button>&nbsp;`;
        data_row += `<button class="btn btn-sm btn-default btn-mover-abajo" title="Bajar Comisi&oacute;n"><i class="glyphicon glyphicon-arrow-down"></i></button>&nbsp;`;
        // Se agrega el botón para remover la Comisión
        data_row += `<button class="btn btn-sm btn-default btn-quitar-comision" title="Remover Comisión" data-codigo_grp="${codigo_grp}"><i class="glyphicon glyphicon-remove"></i></button>&nbsp;`;
        // Nro del orden de la comisión seleccionada
        data_row += `<span class="orden-comision"></span>&nbsp;`;
        // Se agrega la descripción de la Comisión
        data_row += paso.datos['comisiones'].find(x => x.codigo_grp == codigo_grp).descripcion_grp;
        // Se agrega el campo para la Observación
        data_row += `<br><input type="text" name="f_observaciones_tmp[]" class="form-control input-sm data-obs margen_inf_10" data-obs_comision="${codigo_grp}" value="" placeholder="Ingrese una observación..." />`;
    data_row += '</div>';
    $('#lista_comisiones').append(data_row);

    // Se renumeran las comisiones
    renumerarComisiones();

    // Se pone el foco en la observación respectiva
    $(`.data-obs[data-obs_comision="${codigo_grp}"]`).focus();
    // Se desplaza hacia abajo la lista en caso que se llegue a la parte inferior
    $("#lista_comisiones").animate({ scrollTop: $('#lista_comisiones').prop("scrollHeight")}, 1000);

    return true;
}

/**
 * Se renumeran las Comisiones seleccionadas
 */
renumerarComisiones = function() {
    $(".orden-comision").each(function(i){
        $(this).html((i+1)+')');
    });
    
    // Se habilitan todos los botones Subir
    $('.btn-mover-arriba').prop('disabled', false);
    // Se habilitan todos los botones Bajar
    $('.btn-mover-abajo').prop('disabled', false);
    // Se deshabilita el botón Subir de la primer comisión
    $('.btn-mover-arriba').first().prop('disabled', true);
    // Se deshabilita el botón Bajar de la última comisión
    $('.btn-mover-abajo').last().prop('disabled', true);
}

/**
 * Se quita la Comisión de la selección
 * @param  {[type]} codigo_grp Código de la comisión a quitar
 * @return {[type]}            [description]
 */
quitarComision = function (codigo_grp) {
    // Elimino el data row seleccionado
    $(`div.data-row[data-codigo_grp=${codigo_grp}]`).remove();
    renumerarComisiones();

    return true;
}

var callbackBuscar = function () {
    var str = $('#f_busqueda').val().trim().toLowerCase();
    var filtrado = (str != '')
        ? paso.datos['comisiones'].filter(x => x.descripcion_grp.toLowerCase().includes(str))
        : paso.datos['comisiones'];
    
    $('#lista_comisiones_disponibles').empty();

    $.each(filtrado, function (k, v) {
        var data_row = '';
        data_row += '<div class="col-md-12">';
            data_row += `<button class="btn btn-sm btn-default btn-agregar-comision" title="Agregar comisión" data-codigo_grp="${v.codigo_grp}"><i class="glyphicon glyphicon-plus"></i></button>&nbsp;`;
            data_row += v.descripcion_grp;
        data_row += '</div>';
        $('#lista_comisiones_disponibles').append(data_row);
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
    $("#f_busqueda").keydown(function (e) {
        if (e.keyCode == 13)
            e.preventDefault();
        else {
            clearTimeout(handlerBuscar);
            handlerBuscar = setTimeout(callbackBuscar, buscarTimeout);
        }
    });
    
    mostrarCantidadSeleccionados();

    // Inicializo botones
    $('#lista_comisiones_disponibles').on('click', '.btn-agregar-comision', callbackBtnAgregarComision);
    
    $('#lista_comisiones').on('click', '.btn-mover-arriba', callbackBtnMoverArriba);
    $('#lista_comisiones').on('click', '.btn-mover-abajo', callbackBtnMoverAbajo);

    $('#lista_comisiones').on('click', '.btn-quitar-comision', callbackBtnQuitarComision);

    // Fix al form para que seleccione todos las comisiones antes del submit
    $('#formPasoActuacion').submit(callbackFormSubmit);
});