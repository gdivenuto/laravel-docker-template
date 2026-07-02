jQuery(document).ready(function() {

    function validarPresentante() {
        let mensaje = '';
		let error = false;
		
		if ( $('#nombre').val() == '' ) {
            mensaje += "Debe ingresar un <b>Nombre</b>.<br>";
            $('#nombre').focus();
            error = true;
        }
        if ( $('#apellido').val() == '' ) {
            mensaje += "Debe ingresar un <b>Apellido</b>.<br>";
            $('#apellido').focus();
            error = true;
        }
        if ( $('#provincia_id').val() == '0' ) {
            mensaje += "Debe seleccionar una <b>Provincia</b>.<br>";
            $('#provincia_id').focus();
            error = true;
        }
        if ( $('#mail').val() != '' ) {
            if ( esEmailValido($('#mail').val()) === false ) {       
                mensaje += "Debe ingresar un <b>Mail v"+a_acentuada+"lido</b>.<br>";
                $('#mail').focus();
                error = true;
            }  
        } else {
            mensaje += "Debe ingresar un <b>Mail</b>.<br>";
            $('#mail').focus();
            error = true;
        }
        if ( $('#dni').val() == '' ) {
            mensaje += "Debe ingresar un <b>DNI</b>.<br>";
            $('#dni').focus();
            error = true;
        }

		if ( error ) {
			mostrarCartel(mensaje, 2);
		}
		else {
			$('#formEdicion').submit();
	    }
    }

	$('#fecha_alta').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    $('#archivo').on('change',function(){
        $(this).next('.custom-file-label').html($(this).val());
    });

	$('#btGuardar').click(function(){
		validarPresentante();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val()+'&id_editado='+$('#id').val());
	});

    mostrarModal();

    $("#item_presentante").addClass("color_resaltado");
});