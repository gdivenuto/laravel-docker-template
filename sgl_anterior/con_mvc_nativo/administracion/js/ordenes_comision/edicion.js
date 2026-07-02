
/**
 * Se crea una Orden del Día de una Comisión
 */
crearOrdenDiaComision = function () {
    
    let mensaje = '';
    let error = false;
    let coincide_principal = false;
    
    // Si es una comisión conjunta
    if ($('#es_conjunta').prop('checked'))
    {
        // Si NO se han seleccionado las comisiones conjuntas
        if ( ! verificarCheckbox('.conjuntas') ) {
            mensaje += "Debe elegir las Comisiones <b>conjuntas</b>.<br>";
            error = true;
        }

        // Si se ha seleccionado solo una comisión
        if ($('.conjuntas:checked').length == 1) {
            mensaje += "Debe elegir por lo menos dos Comisiones <b>conjuntas</b>.<br>";
            error = true;   
        }

        // Si NO se ha seleccionado ninguna comisión como principal
        if ( ! verificarCheckbox('.principal') ) {
            mensaje += "Debe elegir una Comisi"+o_acentuada+"n como <b>principal</b>.<br>";
            error = true;
        } else {
            // Para cada comisión seleccionada
            $('.conjuntas:checked').each(function() {
                // Se verifica si la principal es una de las comisiones seleccionadas
                // hasta encontrar por lo menos la coincidencia
                if ( ($('.principal:checked').val() == $(this).val()) && ( ! coincide_principal) ) {
                    coincide_principal = true;
                }
            });
            // Si la principal NO coincide con alguna comisión conjunta
            if ( ! coincide_principal) {
                mensaje += "La principal <b>NO</b> coincide con alguna comisi"+o_acentuada+"n conjunta</b>. Verifique.<br>";
                error = true;
            }
        }
    } else {
        if ( $('#codigo_comision').val() == '0' ) {
            mensaje += "Debe elegir una <b>Comisi"+o_acentuada+"n</b>.<br>";
            $('#codigo_comision').focus();
            error = true;
        }
    }
    
    if ( $('#fecha').val() == '' ) {
        mensaje += "Debe elegir una <b>Fecha</b>.<br>";
        $('#fecha').focus();
        error = true;
    }
    
    if ( $('#hora').val() == '' ) {
        mensaje += "Debe ingresar una <b>Hora</b>.<br>";
        $('#hora').focus();
        error = true;
    }
   
    if ( error ) {
        mostrarCartel(mensaje, 2);
    }
    else {
        $('#formEdicion').submit();
    }
}

/**
 * Se guarda la Orden del Día de una Comisión
*/
guardarOrdenDiaComision = function () {
    
    let mensaje = '';
    let error = false;
    
    if ( $('#fecha').val() == '' ) {
        mensaje += "Debe elegir una <b>Fecha</b>.<br>";
        $('#fecha').focus();
        error = true;
    }
    
    if ( $('#hora').val() == '' ) {
        mensaje += "Debe ingresar una <b>Hora</b>.<br>";
        $('#hora').focus();
        error = true;
    }

    if ( error ) {
        mostrarCartel(mensaje, 2);
    }
    else {
        $('#formEdicion').submit();
    }
}

agregarItem = function (id_orden_comision, marca_comision) {
    redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=agregarItem&id='+id_orden_comision+'&marca='+marca_comision);
}

editarItem = function (id_item) {
    redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editarItem&id='+id_item);
}

eliminarItem = function (id_item) {
    if (confirm("¿Desea eliminar el "+i_acentuada+"tem de la Orden?")) {
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=eliminarItem&id='+id_item);
    }
}

editarEncabezado = function (id) {
    redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editarEncabezado&id='+id);
}

editarPie = function (id) {
    redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editarPie&id='+id);
}

editarCabecera = function (id) {
    redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editarCabecera&id='+id);
}

jQuery(document).ready(function() {

    $('#es_conjunta').change(function() {
        // Si es una comisión conjunta
        if ($('#es_conjunta').prop('checked')) {
            // Se oculta el combo y se muestra el listado de checkbox (de comisiones)
            $('#lb_comision').removeClass('d-inline').addClass('d-none');
            $('#contenedor_combo_comisiones').removeClass('d-inline').addClass('d-none');
            $('#contenedor_comisiones_conjuntas').removeClass('d-none').addClass('d-inline');
        } else {
            // Se oculta el listado de checkbox y se muestra el combo (de comisiones)
            $('#lb_comision').removeClass('d-none').addClass('d-inline');
            $('#contenedor_combo_comisiones').removeClass('d-none').addClass('d-inline');
            $('#contenedor_comisiones_conjuntas').removeClass('d-inline').addClass('d-none');
        }
    });
    
    $('#fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    $('#hora').blur(function() {
        formatearHora($('#hora'));
    });

	$('#btCrear').click(function() {
		crearOrdenDiaComision();
	});

	$('#btCancelar').click(function() {
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

    $('#btGuardar').click(function() {
        guardarOrdenDiaComision();
    });

    $('#btVolver').click(function() {
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
    });
    
    mostrarModal();
    
    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
        $("#item_comisiones").addClass("text-info");
});