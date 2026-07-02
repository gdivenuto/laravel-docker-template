jQuery(document).ready(function() {

    function validarComisionInterna() {

        var mensaje = '';
		var error = false;
		
		if ( $('#ci_codigo').val() == '0' ) {
            mensaje += "Debe seleccionar una <b>Comisi"+o_acentuada+"n Interna</b>.<br>";
            $('#comp_proveedor').focus();
            error = true;
        }
        
		if ( error ) {
			mostrarCartel(mensaje, 2);
		}
		else {
			$('#formEdicion').submit();
	    }
    }

    $('#ci_horario').blur(function() {
        // Se formatea el valor ingresado a la hora
        formatearHora($('#ci_horario'));
    });
            
	$('#btGuardar').click(function(){
		validarComisionInterna();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
        $("#item_comisiones").addClass("text-info");
});