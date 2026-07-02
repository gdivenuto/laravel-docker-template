jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_usuario='+$('#f_usuario').val().replace(patron_espacio_blanco_global, "%20");
			url += '&f_fecha_desde='+$('#f_fecha_desde').val();
            url += '&f_fecha_hasta='+$('#f_fecha_hasta').val();

		redireccionar(url);
	}

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

    // Para buscar por Titulo o Texto
    $('#f_usuario').change( function(e) {
        if ( $('#f_usuario').val() != '' )
            buscar();
    });
    $('#f_usuario').keypress( function(e) {
        if ( $('#f_usuario').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });

    $('#btBuscar').click( function() {
        buscar();
    });

    $('#btLimpiar').click( function() {
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
    });

    mostrarModal();

    $("#item_informatica").addClass("text-info");
});