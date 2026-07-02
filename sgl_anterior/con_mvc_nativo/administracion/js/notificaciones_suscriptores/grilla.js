jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&criterio_a_buscar='+$('#criterio_a_buscar').val().replace(patron_espacio_blanco_global, "%20");

		redireccionar(url);
	}

    // Para buscar por Título
    $('#criterio_a_buscar').change( function() {
        if ( $('#criterio_a_buscar').val() != '' )
            buscar();
    });
    $('#criterio_a_buscar').keypress( function(e) {
        if ( $('#criterio_a_buscar').val() != '' && e.which == 13 ) {
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