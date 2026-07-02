jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_descripcion='+$('#f_descripcion').val().replace(patron_espacio_blanco_global, "%20");

		redireccionar(url);
	}

    // Para buscar por Título
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
    else if ($('#perfil_usuario').val() == 10)
        $("#item_administracion").addClass("text-info");
});