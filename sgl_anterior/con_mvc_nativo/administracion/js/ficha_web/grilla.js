jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_legajo='+$('#f_legajo').val().replace(patron_espacio_blanco_global, "%20");
			url += '&f_apellido_y_nombre='+$('#f_apellido_y_nombre').val().replace(patron_espacio_blanco_global, "%20");
			url += '&f_activos='+$('#f_activos').val();

		redireccionar(url);
	}

    // Para buscar por Legajo
     $('#f_legajo').keypress( function(e) {
        if ( $('#f_legajo').val() != '' && e.which == 13 ) {
        	e.preventDefault();
            buscar();
        }
    });

    // Para buscar por Apellidos y/o Nombres
 	$('#f_apellido_y_nombre').keypress( function(e) {
        if ( $('#f_apellido_y_nombre').val() != '' && e.which == 13 ) {
        	e.preventDefault();
            buscar();
        }
    });

	// Al utilizar el chekbox de Activos
	$('#chk_activos').change( function() {
		$('#f_activos').val( ( $('#chk_activos').prop('checked') ) ? 1 : 0);
		buscar();
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

    $("#item_informatica").addClass("text-info");
});