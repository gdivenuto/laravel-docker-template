jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_titulo='+$('#f_titulo').val();
			url += '&f_descripcion='+$('#f_descripcion').val().replace(patron_espacio_blanco_global, "%20");
			url += '&f_fecha='+$('#f_fecha').val();

		redireccionar(url);
	}

    // Para buscar por Código
    $('#f_titulo').change( function() {
        if ( $('#f_titulo').val() != '' )
            buscar();
    });
    $('#f_titulo').keypress( function(e) {
        if ( $('#f_titulo').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });

    // Para buscar por Nombre
    $('#f_descripcion').change( function() {
        if ( $('#f_descripcion').val() != '' )
            buscar();
    });
    $('#f_descripcion').keypress( function(e) {
        if ( $('#f_descripcion').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });

    $('#f_fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
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
    else if ($('#perfil_usuario').val() == 26)
    	$("#item_modernizacion").addClass("text-info");
});