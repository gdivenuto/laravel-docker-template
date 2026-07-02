jQuery(document).ready(function() {

	$('#btAgregarSuscriptor').click(function(){
		
		var mensaje = "";
	    var error = false;

		// Si se ha ingresado un Email para un nuevo suscriptor
	    if ( $('#nuevo_mail').val() != '' ) {
	    	// Si el mail NO es válido
	        if ( esEmailValido($('#nuevo_mail').val()) === false ) {
	            error = true;
	    		mensaje += "Debe ingresar un Email v"+a_acentuada+"lido.";
	            $('#nuevo_mail').focus();
	        }
	    } else { // si NO se ha ingresado
		    error = true;
		    mensaje += "Debe ingresar un mail.";
			$('#nuevo_mail').focus();
		}

	    if (error) {
			mostrarCartel(mensaje, 2);// Se muestra el mensaje de error
	    } else {
	    	// Se envía la info para crear y asignar el nuevo Suscriptor
			redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=asignarNuevoSuscriptor&id_lista='+$('#id').val()+'&email='+$('#nuevo_mail').val());
		}
	});

	function buscarSuscriptores() {

		// Si se ha ingresado un Email para un nuevo Suscriptor
	    if ( $('#criterio_a_buscar').val() == '' ) {
	    	mostrarCartel("Debe ingresar un mail o parte de "+e_acentuada+"l", 2);
		} else {
			var url  = $('#url_abms').val();
				url += '?controlador='+$('#controlador').val();
				url += '&accion=editarSuscriptoresLista';
				url += '&id_lista='+$('#id').val();
				url += '&criterio_a_buscar='+$('#criterio_a_buscar').val().replace(patron_espacio_blanco_global, "%20");

			redireccionar(url);
		}
	}

    // Para buscar por Título
    $('#criterio_a_buscar').change( function(e) {
        if ( $('#criterio_a_buscar').val() != '' )
            buscarSuscriptores();
    });
    $('#criterio_a_buscar').keypress( function(e) {
        if ( $('#criterio_a_buscar').val() != '' && e.which == 13 )
            buscarSuscriptores();
    });

	$('#btBuscar').click( function() {
		buscarSuscriptores();
	});

	$('#btLimpiar').click( function() {
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editarSuscriptoresLista&id='+$('#id').val());
	});

	$('#btGuardar').click(function(){
		// Se obliga a elegir por lo menos uno ya existente
		if (! verificarCheckbox('.suscriptores')) {
		    mostrarCartel("Debe elegir por lo menos un mail para suscribir.", 2);
		} else {
			$('#formEdicion').submit();
		}
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editar&id='+$('#id').val()+'&pagina='+$('#pagina').val());
	});

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
    	$("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 10)
        $("#item_administracion").addClass("text-info");
});