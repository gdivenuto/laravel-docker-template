jQuery(document).ready(function() {

    function validarHistoricoConcejal() {

        var mensaje = '';
		var error = false;
		
		if ( $('#ch_apellido_nombre').val() == '' ) {
            mensaje += "Debe ingresar un <b>Apellido y Nombre</b>.<br>";
            $('#ch_apellido_nombre').focus();
            error = true;
        }
        if ( $('#ch_desde').val() == '' ) {
            mensaje += "Debe ingresar un <b>A"+enie+"o de inicio</b>.<br>";
            $('#ch_desde').focus();
            error = true;
        }
                
		if ( error ) {
			mostrarCartel(mensaje, 2);
		}
		else {
			$('#formEdicion').submit();
	    }
    }

	$('#btGuardar').click(function(){
		validarHistoricoConcejal();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 11)
        $("#item_biblioteca").addClass("text-info");
});