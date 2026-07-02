jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
            url += '&f_apellido_nombre='+$('#f_apellido_nombre').val().replace(patron_espacio_blanco_global, "%20");
            url += '&f_bloque='+$('#f_bloque').val().replace(patron_espacio_blanco_global, "%20");
            url += '&f_desde='+$('#f_desde').val();
			url += '&f_hasta='+$('#f_hasta').val();
            url += '&f_cargo='+$('#f_cargo').val();

		redireccionar(url);
	}

    $('#f_apellido_nombre').change( function(e) {
        if ( $('#f_apellido_nombre').val() != '' )
            buscar();
    });
    $('#f_apellido_nombre').keypress( function(e) {
        if ( $('#f_apellido_nombre').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });

    // Para buscar por Contenido
    $('#f_bloque').change( function(e) {
        if ( $('#f_bloque').val() != '' )
            buscar();
    });
    $('#f_bloque').keypress( function(e) {
        if ( $('#f_bloque').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });

    $('#f_desde').change( function(e) {
        if ( $('#f_desde').val() != '' )
            buscar();
    });
    $('#f_desde').keypress( function(e) {
        if ( $('#f_desde').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });
   
    $('#f_hasta').change( function(e) {
        if ( $('#f_hasta').val() != '' )
            buscar();
    });
    $('#f_hasta').keypress( function(e) {
        if ( $('#f_hasta').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });
   
    $('#f_cargo').change( function(e) {
        if ( $('#f_cargo').val() != '' )
            buscar();
    });
    $('#f_cargo').keypress( function(e) {
        if ( $('#f_cargo').val() != '' && e.which == 13 ) {
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

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
    	$("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 11)
    	$("#item_biblioteca").addClass("text-info");
});