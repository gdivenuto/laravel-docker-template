jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_texto='+$('#f_texto').val().replace(patron_espacio_blanco_global, "%20");
            url += '&f_fecha_desde='+$('#f_fecha_desde').val();
            url += '&f_fecha_hasta='+$('#f_fecha_hasta').val();
            url += '&f_tipo='+$('#f_tipo').val();

		redireccionar(url);
	}

    $('#f_texto').change( function(e) {
        if ( $('#f_texto').val() != '' )
            buscar();
    });
    $('#f_texto').keypress( function(e) {
        if ( $('#f_texto').val() != '' && e.which == 13 ){
            e.preventDefault();
            buscar();
        }
    });
    
    $('#f_fecha_desde').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    $('#f_fecha_hasta').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    $('#f_tipo').change( function(e) {
        buscar();
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
    else if ($('#perfil_usuario').val() == 10)
        $("#item_administracion").addClass("text-info");
});