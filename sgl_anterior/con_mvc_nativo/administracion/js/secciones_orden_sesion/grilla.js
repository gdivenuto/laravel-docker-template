jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_codigo='+$('#f_codigo').val();
			url += '&f_nombre='+$('#f_nombre').val().replace(patron_espacio_blanco_global, "%20");
			url += '&f_seccion_padre='+$('#f_seccion_padre').val();

		redireccionar(url);
	}

    // Para buscar por Código
    $('#f_codigo').change( function() {
        if ( $('#f_codigo').val() != '' )
            buscar();
    });
    $('#f_codigo').keypress( function(e) {
        if ( $('#f_codigo').val() != '' && e.which == 13 ) {
        	e.preventDefault();
            buscar();
        }
    });

    // Para buscar por Nombre
    $('#f_nombre').change( function() {
        if ( $('#f_nombre').val() != '' )
            buscar();
    });
    $('#f_nombre').keypress( function(e) {
        if ( $('#f_nombre').val() != '' && e.which == 13 ) {
        	e.preventDefault();
            buscar();
        }
    });

	// Al seleccionarse una Sección padre
	$('#f_seccion_padre').change( function() {
		buscar();
	});
	
	$('#btBuscar').click( function() {
		buscar();
	});

	$('#btLimpiar').click( function() {
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&f_limpiar=1&pagina='+$('#pagina').val());
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