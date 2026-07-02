jQuery(document).ready(function() {

	function validarSuscriptor() {
        
        if ( $('#email').val() == '' ) {
            mostrarCartel("Debe ingresar un <b>Email</b>.", 2);
            $('#email').focus();
        } else {
	        // Si el mail NO es válido
	        if ( esEmailValido($('#email').val()) === false ) {
	            mostrarCartel("Debe ingresar un Email v"+a_acentuada+"lido.", 2);
	            $('#email').focus();
	        } else {
				$('#formEdicion').submit();
			}
		}
    }

	$('#btGuardar').click(function(){
		validarSuscriptor();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar');
	});

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
    	$("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 10)
        $("#item_administracion").addClass("text-info");
});