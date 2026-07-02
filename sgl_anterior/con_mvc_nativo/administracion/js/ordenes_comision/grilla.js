jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_comision='+$('#f_comision').val();
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

    // Para buscar por Comisión
    $('#f_comision').change( function() {
        if ( $('#f_comision').val() != '0' )
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

    if ($('#perfil_usuario').val() == 14)
    	$("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
    	$("#item_comisiones").addClass("text-info");
});