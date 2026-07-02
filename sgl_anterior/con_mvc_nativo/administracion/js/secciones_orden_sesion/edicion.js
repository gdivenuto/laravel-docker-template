jQuery(document).ready(function() {

    function validarSeccion() {
        var mensaje = '';
		var error = false;
		
        if ( $('#codigo').val() == '' ) {
            mensaje += "Debe ingresar un <b>C"+o_acentuada+"digo</b>.<br>";
            $('#codigo').focus();
            error = true;
        }

		if ( $('#nombre').val() == '' ) {
            mensaje += "Debe ingresar una <b>Descripci"+o_acentuada+"n</b>.<br>";
            $('#nombre').focus();
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
		validarSeccion();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});
    
    mostrarModal();
    
    if ($('#perfil_usuario').val() == 14)
    	$("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 10)
    	$("#item_administracion").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
    	$("#item_comisiones").addClass("text-info");
});