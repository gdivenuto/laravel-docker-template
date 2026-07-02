jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
            url += '&f_fecha='+$('#f_fecha').val();
			url += '&f_asunto='+$('#f_asunto').val().replace(patron_espacio_blanco_global, "%20");

		redireccionar(url);
	}

	$('#f_fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    // Para buscar por Título
    $('#f_asunto').change( function() {
        if ( $('#f_asunto').val() != '' )
            buscar();
    });
    $('#f_asunto').keypress( function(e) {
        if ( $('#f_asunto').val() != '' && e.which == 13 ) {
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
    else if ($('#perfil_usuario').val() == 23)
    	$("#item_actas").addClass("text-info");
    else if ($('#perfil_usuario').val() == 10)
        $("#item_administracion").addClass("text-info");
    else if ($('#perfil_usuario').val() == 11)
        $("#item_biblioteca").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
        $("#item_comisiones").addClass("text-info");
    else if ($('#perfil_usuario').val() == 24)
        $("#item_mesa_entradas").addClass("text-info");
    else if ($('#perfil_usuario').val() == 26)
        $("#item_modernizacion").addClass("text-info");
    else if ($('#perfil_usuario').val() == 15)
        $("#item_prensa").addClass("text-info");
    else if ($('#perfil_usuario').val() == 25)
        $("#item_presidencia").addClass("text-info");
});