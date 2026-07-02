jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_codigo='+$('#f_codigo').val();
			url += '&f_nombre='+$('#f_nombre').val().replace(patron_espacio_blanco_global, "%20");

		redireccionar(url);
	}

    $('#f_codigo').keypress( function(e) {
        if ( $('#f_codigo').val() != '' && e.which == 13 ) {
        	e.preventDefault();
            buscar();
        }
    });

    // Para buscar por Nombre
    $('#f_nombre').change( function(e) {
        if ( $('#f_nombre').val() != '' )
            buscar();
    });
    $('#f_nombre').keypress( function(e) {
        if ( $('#f_nombre').val() != '' && e.which == 13 ) {
        	e.preventDefault();
            buscar();
        }
    });

	$('#btBuscar').click( function() {
		buscar();
	});

	$('#btLimpiar').click( function() {
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar');
	});

	$('#btNuevo').click( function() {
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editar');
	});

	$('#btSetearMantenimiento').click( function() {
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=setearMantenimiento&m='+$('#estado_mantenimiento').val());
	});

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
    	$("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
    	$("#item_comisiones").addClass("text-info");
});