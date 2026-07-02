jQuery(document).ready(function() {

	function buscar() {

		let url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&valor_buscado='+$('#valor_buscado').val().replace(patron_espacio_blanco_global, "%20");

		redireccionar(url);
	}

    $('#valor_buscado').change( function(e) {
        if ( $('#valor_buscado').val() != '' )
            buscar();
    });
    $('#valor_buscado').keypress( function(e) {
        if ( $('#valor_buscado').val() != '' && e.which == 13 )
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

    $("#item_modelo_escrito").addClass("color_resaltado");
});