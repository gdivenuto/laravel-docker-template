jQuery(document).ready(function() {

    function validarPublicador() {
        var mensaje = '';
		var error = false;
		
        if ( $('#titulo').val() == '' ) {
            mensaje += "Debe ingresar un <b>T"+i_acentuada+"tulo</b>.<br>";
            $('#titulo').focus();
            error = true;
        }
		
		if ( error ) {
			mostrarCartel(mensaje, 2);
		}
		else {
			$('#formEdicion').submit();
	    }
    }

	$('#fecha_emitido').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });
    $('#fecha_modificado').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

	$('#btGuardar').click(function(){
		validarPublicador();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 26)
        $("#item_modernizacion").addClass("text-info");
});