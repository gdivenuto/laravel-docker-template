jQuery(document).ready(function() {

    function validarActividad() {

        var mensaje = '';
		var error = false;
		
		if ( $('#a_fecha').val() == '' ) {
            mensaje += "Debe elegir una <b>Fecha</b>.<br>";
            $('#a_fecha').focus();
            error = true;
        }
        if ( $('#a_hora').val() == '' ) {
            mensaje += "Debe ingresar un <b>Horario</b>.<br>";
            $('#a_hora').focus();
            error = true;
        }
        if ( $('#a_titulo').val() == '' ) {
            mensaje += "Debe ingresar un <b>T"+i_acentuada+"tulo</b>.<br>";
            $('#a_titulo').focus();
            error = true;
        }

		if ( error ) {
			mostrarCartel(mensaje, 2);
		}
		else {
			$('#formEdicion').submit();
	    }
    }

    $('#a_fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    $('#a_hora').blur(function(){
        // Se formatea el valor ingresado para la hora
        formatearHora($('#a_hora'));
    });

	$('#btGuardar').click(function(){
		validarActividad();
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