jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listarParticipaciones';
            url += '&anio='+$('#anio').val();
            url += '&tipo='+$('#tipo').val();
            url += '&numero='+$('#numero').val();
            url += '&cuerpo='+$('#cuerpo').val();
            url += '&alcance='+$('#alcance').val();
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

    $('#btBuscar').click( function() {

        if ($('#f_fecha_desde').val() != '' && $('#f_fecha_hasta').val() != '') {
            buscar();
        } else {
            mostrarCartel("Debe ingresar la fecha Desde y Hasta.", 3);
        }
    });

    $('#btLimpiar').click( function() {

        var url  = $('#url_abms').val();
            url += '?controlador='+$('#controlador').val();
            url += '&accion=listarParticipaciones';
            url += '&anio='+$('#anio').val();
            url += '&tipo='+$('#tipo').val();
            url += '&numero='+$('#numero').val();
            url += '&cuerpo='+$('#cuerpo').val();
            url += '&alcance='+$('#alcance').val();
            url += '&pagina='+$('#pagina').val();

        redireccionar(url);
    });

    $('#btVolver').click( function() {
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar');
    });


    mostrarModal();

    $("#item_informatica").addClass("text-info");
});