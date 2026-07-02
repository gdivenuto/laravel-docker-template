jQuery(document).ready(function() {

	function buscar() {

		let url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
            url += '&f_numero='+$('#f_numero').val();
			url += '&f_presentante='+$('#f_presentante').val();
            url += '&f_tipo_proceso='+$('#f_tipo_proceso').val();
            url += '&f_fecha='+$('#f_fecha').val();
            url += '&f_estado='+$('#f_estado').val();

		redireccionar(url);
	}

    $('#f_numero').change( function(e) {
        if ( $('#f_numero').val() != '' )
            buscar();
    });
    $('#f_presentante').change( function(e) {
        if ( $('#f_presentante').val() != '0' )
            buscar();
    });
    $('#f_tipo_proceso').change( function(e) {
        if ( $('#f_tipo_proceso').val() != '0' )
            buscar();
    });
    $('#f_estado').change( function(e) {
        if ( $('#f_estado').val() != '0' )
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

    $("#item_expediente").addClass("color_resaltado");
});