jQuery(document).ready(function() {

    function validarBanca25() {

        let mensaje = 'Si <b>no descarta</b> la solicitud:<br>';
        let error = false;

		if ( $('#apellidoynombre').val() == '' ) {
            mensaje += "Debe ingresar el <b>Apellido y nombres</b>.<br>";
            $('#apellidoynombre').focus();
            error = true;
        }
        if ( $('#mail').val() != '' && esEmailValido($('#mail').val()) == false )  {      
            error = true;
            mensaje += "Debe ingresar un <b>mail v"+a_acentuada+"lido</b>.<br>";
            $('mail').focus();
        }
        // Si se ingresó la fecha de sesión, se obliga a ingresar la clave de Exped/Nota
        if ( $('#fecha_sesion').val() != '' ) {
            if ( $('#expe_anio').val() == '' ) {
                mensaje += "Debe ingresar el <b>A"+enie+"o</b> de Exped/Nota.<br>";
                $('#expe_anio').focus();
                error = true;
            }
            if ( $('#expe_tipo').val() == '0' ) {
                mensaje += "Debe elegir un <b>Tipo</b> de Exped/Nota.<br>";
                $('#expe_tipo').focus();
                error = true;
            }
            if ( $('#expe_numero').val() == '' ) {
                mensaje += "Debe ingresar el <b>N"+u_acentuada+"mero</b> de Exped/Nota.<br>";
                $('#expe_numero').focus();
                error = true;
            }
        }
        // Si se ingresó la clave de Exped/Nota, se obliga a ingresar la fecha de sesión
        if ( $('#expe_anio').val() != '' || 
             $('#expe_tipo').val() != '0' || 
             $('#expe_numero').val() != ''
           ){
            if ( $('#fecha_sesion').val() == '' ) {
                mensaje += "Debe elegir la <b>Fecha de Sesi"+o_acentuada+"n</b>.<br>";
                $('#fecha_sesion').focus();
                error = true;
            }
        }
        /**
        if ( ! verificarCheckbox('#descartada') ) {
            if ( $('#fecha_sesion').val() == '' ) {
                mensaje += "Debe elegir la <b>Fecha de Sesi"+o_acentuada+"n</b>.<br>";
                $('#fecha_sesion').focus();
                error = true;
            }
            if ( $('#expe_anio').val() == '' ) {
                mensaje += "Debe ingresar el <b>A"+enie+"o</b> de Exped/Nota.<br>";
                $('#expe_anio').focus();
                error = true;
            }
            if ( $('#expe_tipo').val() == '0' ) {
                mensaje += "Debe elegir un <b>Tipo</b> de Exped/Nota.<br>";
                $('#expe_tipo').focus();
                error = true;
            }
            if ( $('#expe_numero').val() == '' ) {
                mensaje += "Debe ingresar el <b>N"+u_acentuada+"mero</b> de Exped/Nota.<br>";
                $('#expe_numero').focus();
                error = true;
            }
        }
        /**/
		if ( error ) {
			mostrarCartel(mensaje, 2);
		} else {
			$('#formEdicion').submit();
	    }
    }

    $('#fecha_sesion').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

	$('#btGuardar').click(function(){
		validarBanca25();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

    $('#btBorrar').click(function(){
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=borrar&solicitud_id='+$('#solicitud_id').val()+'&pagina='+$('#pagina').val());
    });

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 10)
        $("#item_administracion").addClass("text-info");
});