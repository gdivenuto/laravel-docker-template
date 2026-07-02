jQuery(document).ready(function() {

	function buscar() {

		let url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
            url += '&f_numero='+$('#f_numero').val();
			url += '&f_remitente='+$('#f_remitente').val();
            url += '&f_fecha='+$('#f_fecha').val();

		redireccionar(url);
	}

    $('#f_numero').change( function(e) {
        if ( $('#f_numero').val() != '' )
            buscar();
    });
    $('#f_remitente').change( function(e) {
        if ( $('#f_remitente').val() != '0' )
            buscar();
    });

    $('#f_fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
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

    $("#item_resolucion").addClass("color_resaltado");
});