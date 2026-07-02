jQuery(document).ready(function() {

	// El usuario (empleado) modifica su password
	function modificarPasswordPropio() {
		var texto = "";
		var error = false;

		// Si NO ha ingresado su password actual
		if ($("#mp_password_actual").val() == "" ) {
			texto += "Ingrese su Contrase"+enie+"a actual. <br>";
			$('#mp_password_actual').focus();
			error = true;
		}

		// Si NO ha ingresado su nuevo password
		if ($("#mp_password_nuevo").val() == "" ) {
			texto += "Ingrese una nueva Contrase"+enie+"a. <br>";
			$('#mp_password_nuevo').focus();
			error = true;
		} else {
			// Si el password posee menos de 8 caracteres
			if ( $("#mp_password_nuevo").val().length < 8 ) {
				texto += "Su nueva Contrase"+enie+"a debe poseer m"+a_acentuada+"s de 8 caracteres. <br>";
				$('#mp_password_nuevo').focus();
				error = true;
			} else {
				// Si los passwords NO coinciden
				if ( $("#mp_password_a_confirmar").val() != $("#mp_password_nuevo").val() ) {
					texto += "Las Contrase"+enie+"as no coinciden (la nueva y su confirmaci"+o_acentuada+"n). <br>";
					$('#mp_password_a_confirmar').focus();
					error = true;
				}
			}
		}

		if (error) {
			mostrarCartel(texto, 2);
		} else {
			$('#formEdicion').submit();
	    }
	}

	$('#btGuardar').click(function(){
		modificarPasswordPropio();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador=usuarios&accion=listar');
	});

	$('#mp_password_actual').focus();

    $("#item_administracion").addClass("text-warning");
});