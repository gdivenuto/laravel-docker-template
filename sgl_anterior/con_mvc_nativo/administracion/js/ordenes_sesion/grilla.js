jQuery(document).ready(function() {

	function buscar() {

		let url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_periodo='+$('#f_periodo').val();
			url += '&f_reunion='+$('#f_reunion').val();
			url += '&f_sesion='+$('#f_sesion').val().replace(patron_espacio_blanco_global, "%20");
            url += '&f_fecha='+$('#f_fecha').val();

		redireccionar(url);
	}

    $('#f_fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    // Para buscar por Código
    $('#f_periodo').change( function() {
        if ( $('#f_periodo').val() != '' )
            buscar();
    });
    $('#f_periodo').keypress( function(e) {
        if ( $('#f_periodo').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });

    // Para buscar por Nombre
    $('#f_reunion').change( function() {
        if ( $('#f_reunion').val() != '' )
            buscar();
    });
    $('#f_reunion').keypress( function(e) {
        if ( $('#f_reunion').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });

    // Para buscar por Nombre
    $('#f_sesion').change( function() {
        if ( $('#f_sesion').val() != '' )
            buscar();
    });
    $('#f_sesion').keypress( function(e) {
        if ( $('#f_sesion').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });

	$('#btBuscar').click( function() {
		buscar();
	});

	$('#btLimpiar').click( function() {
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

	$('#btNuevo').click( function() {
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editar');
	});

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
    	$("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 10)
    	$("#item_administracion").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
    	$("#item_comisiones").addClass("text-info");
});