jQuery(document).ready(function() {

    function validarDistincion() {

        var mensaje = '';
		var error = false;
		
		if ( $('#d_fecha').val() == '' ) {
            mensaje += "Debe elegir una <b>Fecha</b>.<br>";
            $('#d_fecha').focus();
            error = true;
        }
        if ( $('#d_tipo').val() == '' ) {
            mensaje += "Debe elegir un <b>Tipo de distinci"+o_acentuada+"n</b>.<br>";
            $('#d_tipo').focus();
            error = true;
        }
        if ( $('#d_acto').val() == '' ) {
            mensaje += "Debe ingresar un <b>N"+u_acentuada+"mero de Acto</b>.<br>";
            $('#d_acto').focus();
            error = true;
        }

		if ( error ) {
			mostrarCartel(mensaje, 2);
		}
		else {
			$('#formEdicion').submit();
	    }
    }

    $('#d_fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

	$('#btGuardar').click(function(){
		validarDistincion();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 15)
        $("#item_prensa").addClass("text-info");
});