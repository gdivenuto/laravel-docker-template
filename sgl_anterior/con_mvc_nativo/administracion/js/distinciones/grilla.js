jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
            url += '&f_distincion='+$('#f_distincion').val();
			url += '&f_acto='+$('#f_acto').val().replace(patron_espacio_blanco_global, "%20");
			url += '&f_expediente='+$('#f_expediente').val().replace(patron_espacio_blanco_global, "%20");
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

    // Para buscar por Año
    $('#f_distincion').change( function(e) {
        if ( $('#f_distincion').val() != '' )
            buscar();
    });
   
    // Para buscar por Título
    $('#f_acto').change( function(e) {
        if ( $('#f_acto').val() != '' )
            buscar();
    });
    $('#f_acto').keypress( function(e) {
        if ( $('#f_acto').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });

    // Para buscar por Contenido
    $('#f_expediente').change( function(e) {
        if ( $('#f_expediente').val() != '' )
            buscar();
    });
    $('#f_expediente').keypress( function(e) {
        if ( $('#f_expediente').val() != '' && e.which == 13 ) {
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