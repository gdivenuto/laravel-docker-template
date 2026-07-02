// ----------------------------------------------------------------------------
// ---- Funciones y Callbacks -------------------------------------------------
// ----------------------------------------------------------------------------

/**
 * Lógica de validación de datos del paso.
 */
confirmarPasoActual = function () {
    // Reset de errores
    errores = [];

    $('.campo-plantilla').each(function () {
        var regex_str = $(this).data('regex');
        if (regex_str !== '') {
            regex = eval(atob(regex_str));
            if (!regex.test($(this).val()))
                errores.push(`Ingrese un valor válido para el campo '${$(this).data('nombre')}'`);
        }

        if ($(this).data('obligatorio') == 'si')
            if ($(this).val() == '')
                errores.push(`El campo '${$(this).data('nombre')}' es obligatorio.`);
    });

    return (errores.length == 0);
};

callbackGenerarSelectorPlantilla = function (k, v) {
    var data_row = '';
    var hint = (v.descripcion == '') ? 'Seleccionar Plantilla' : v.descripcion;
    data_row += '<div class="col-md-12">';
        data_row += `<button class="btn btn-sm btn-default btn-seleccionar-plantilla" title="${hint}" data-plantilla="${v.plantilla}"><i class="glyphicon glyphicon-ok"></i></button>&nbsp;`;
        data_row += `<i>${v.categoria}</i> - ${v.nombre}`;
    data_row += '</div>';
    $('#lista_plantillas_disponibles').append(data_row);        
};

/**
 * Lógica de inicialización de la vista en base a los parametros del paso.
 */
inicializarFormulario = function () {
    if (paso.opciones['paso_ayuda'] != '')
        $('#paso_ayuda').html(paso.opciones['paso_ayuda']);

    // Setup de lista de plantillas
    $.each(paso.datos['plantillas'], callbackGenerarSelectorPlantilla);
};

/**
 * Lógica de recuperación de transacción previa para el paso.
 */
recuperarTransaccion = function () {
    // Si no hay transacción, aborto el proceso.
    if (transaccion === null) return false;

    // Si la transacción posee una plantilla (si se ha elegido una específica)
    if (transaccion.f_plantilla) {
        // Recupero valores y actualizo UI 
        // (cuando termina de cargar el ajax de seleccionarPlantilla)
        $.when(seleccionarPlantilla(transaccion.f_plantilla))
            .done(function(s) {
                // Me quedo con todos los campos 'f_*' (sin f_plantilla)
                var campos_form = Object.keys(transaccion).filter(x => x.startsWith('f_') && x != 'f_plantilla');
                $.each(campos_form, function (k, v) {
                    
                    // Tratamiento especial para los radio-button
                    if ($(`#${v}[type="radio-falso"]`).length == 0)
                        $(`#${v}`).val(transaccion[v]);
                    else {
                        $(`#${v} input[type="radio"]`).each(function (k, i) { 
                            $(i).prop("checked", $(i).val() == transaccion[v]);
                        });
                    }    
                });
            });

        return true;
    } else {
        return false;
    }
};

callbackBtnSeleccionarPlantilla = function (e) {
    e.preventDefault();
    var id_plantilla = $(this).data('plantilla');
    seleccionarPlantilla(id_plantilla);
}

callbackBtnQuitarPlantilla = function (e) {
    e.preventDefault();
    quitarPlantilla();
}

seleccionarPlantilla = function (id_plantilla) {

    // Peticion asíncrona
    // ATENCION: devuelvo una instancia del ajax para poder ejecutar codigo
    // al finalizar el request (ver recuperarTransaccion)
    return $.ajax({
            method: "GET",
            url: `${base_url}index.php?c=plantillas&a=getform&f_plantilla=${id_plantilla}`,
            dataType: 'json'
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                $('#plantilla_seleccionada').html(respuesta.data.plantilla.nombre);
                $('#plantilla_formulario').html(respuesta.data.formulario);
                $('#btn_quitar_plantilla').removeClass('display_none');
            } else {
                showModal('Error', `Error al obtener plantilla: ${respuesta.mensaje}`);
            }
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
}

quitarPlantilla = function () {
    $('#plantilla_seleccionada').html('Ninguna');
    $('#plantilla_formulario').html('<input type="hidden" id="f_plantilla" name="f_plantilla" value="">');
    $('#btn_quitar_plantilla').addClass('display_none');

    return true;
}

var callbackBuscar = function () {
    var str = $('#f_busqueda').val().trim().toLowerCase();
    var filtrado = (str != '')
        ? paso.datos['plantillas'].filter(
            x => x.nombre.toLowerCase().includes(str) || 
                 x.categoria.toLowerCase().includes(str) || 
                 x.descripcion.toLowerCase().includes(str)
          )
        : paso.datos['plantillas'];
    
    $('#lista_plantillas_disponibles').empty();

    $.each(filtrado, callbackGenerarSelectorPlantilla);
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

    // Inicializo botones
    $('#btn_quitar_plantilla').click(callbackBtnQuitarPlantilla);
    $('#lista_plantillas_disponibles').on('click', '.btn-seleccionar-plantilla', callbackBtnSeleccionarPlantilla);
});