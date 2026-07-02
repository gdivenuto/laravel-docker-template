jQuery(document).ready(function() {

	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
            url += '&f_anio='+$('#f_anio').val();
            url += '&f_tipo='+$('#f_tipo').val();
            url += '&f_numero='+$('#f_numero').val();
            url += '&f_cuerpo='+$('#f_cuerpo').val();
            url += '&f_alcance='+$('#f_alcance').val();
			url += '&f_fecha_desde='+$('#f_fecha_desde').val();
            url += '&f_fecha_hasta='+$('#f_fecha_hasta').val();
            url += '&f_usuario='+$('#f_usuario').val().replace(patron_espacio_blanco_global, "%20");
            url += '&f_criterio='+$('#f_criterio').val();

		redireccionar(url);
	}

    function verificarCriteriosBusqueda() {

        // Si se busca por Clave
        if ( $('#f_criterio').val() == '1' )
        {
            $('.campo_busqueda_por_clave').css('display', 'block');
            $('.campo_busqueda_por_fechas').css('display', 'none');
            $('.campo_busqueda_por_usuario').css('display', 'none');
            $('#f_anio').focus();
        }
        else if ( $('#f_criterio').val() == '2' ) // Por Rango de fechas
        {
            $('.campo_busqueda_por_clave').css('display', 'none');
            $('.campo_busqueda_por_fechas').css('display', 'block');
            $('.campo_busqueda_por_usuario').css('display', 'none');
            //$('#f_fecha_desde').focus();
        }
        else // Por usuario
        {
            $('.campo_busqueda_por_clave').css('display', 'none');
            $('.campo_busqueda_por_fechas').css('display', 'none');
            $('.campo_busqueda_por_usuario').css('display', 'block');
            $('#f_usuario').focus();
        }
    }

    $('#f_criterio').ready(function() {
        verificarCriteriosBusqueda();
    });

    $('#f_criterio').change(function() {
        verificarCriteriosBusqueda();
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
