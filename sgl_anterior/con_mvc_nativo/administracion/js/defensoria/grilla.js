jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_texto='+$('#f_texto').val().replace(patron_espacio_blanco_global, "%20");
            url += '&f_fecha='+$('#f_fecha').val();
            url += '&f_habilitados='+$('#f_habilitados').val();

		redireccionar(url);
	}

	$('#f_fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });
    
    // Al utilizar el chekbox de Activos
    $('#chk_habilitados').change( function() {
        $('#f_habilitados').val( ( $('#chk_habilitados').prop('checked') ) ? 1 : 0);
        buscar();
    });
    
    // Para buscar por Título
    $('#f_texto').keypress( function(e) {
        if ( $('#f_texto').val() != '' && e.which == 13 ) {
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

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
    	$("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 26)
    	$("#item_modernizacion").addClass("text-info");
    else if ($('#perfil_usuario').val() == 25)
        $("#item_presidencia").addClass("text-info");
});