jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
            url += '&f_fecha='+$('#f_fecha').val();
			url += '&f_titulo='+$('#f_titulo').val().replace(patron_espacio_blanco_global, "%20");
			url += '&f_contenido='+$('#f_contenido').val().replace(patron_espacio_blanco_global, "%20");
            
		redireccionar(url);
	}

	$('#f_fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    // Para buscar por Título
    $('#f_titulo').change( function(e) {
        if ( $('#f_titulo').val() != '' )
            buscar();
    });
    $('#f_titulo').keypress( function(e) {
        if ( $('#f_titulo').val() != '' && e.which == 13 ){
            e.preventDefault();
            buscar();
        }
    });

    // Para buscar por Contenido
    $('#f_contenido').change( function(e) {
        if ( $('#f_contenido').val() != '' )
            buscar();
    });
    $('#f_contenido').keypress( function(e) {
        if ( $('#f_contenido').val() != '' && e.which == 13 ) {
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
    else if ($('#perfil_usuario').val() == 15)
    	$("#item_prensa").addClass("text-info");
});