jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_anio='+$('#f_anio').val();
			url += '&f_concepto='+$('#f_concepto').val().replace(patron_espacio_blanco_global, "%20");
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
    $('#f_anio').change( function(e) {
        if ( $('#f_anio').val() != '' )
            buscar();
    });
   
    // Para buscar por Concepto
    $('#f_concepto').change( function(e) {
        if ( $('#f_concepto').val() != '' )
            buscar();
    });
    $('#f_concepto').keypress( function(e) {
        if ( $('#f_concepto').val() != '' && e.which == 13 ) {
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
    else if ($('#perfil_usuario').val() == 26)
    	$("#item_modernizacion").addClass("text-info");
});