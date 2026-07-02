jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_tipo='+$('#f_tipo').val();
            url += '&f_acto='+$('#f_acto').val();
			url += '&f_titulo='+$('#f_titulo').val().replace(patron_espacio_blanco_global, "%20");
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

    // Para buscar por Tipo
    $('#f_tipo').change( function() {
        if ( $('#f_tipo').val() != '0' )
            buscar();
    });
    // Para buscar por Acto
    $('#f_acto').change( function() {
        if ( $('#f_acto').val() != '0' )
            buscar();
    });
    // Para buscar por Título
    $('#f_titulo').keypress( function(e) {
        if ( $('#f_titulo').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });

	$('#btBuscar').click( function() {
		buscar();
	});

	$('#btLimpiar').click( function() {
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&limpiar=si&pagina='+$('#pagina').val());
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