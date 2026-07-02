jQuery(document).ready(function() {

	function validarLista() {
        
	    var mensaje = '';
	    var error = false;
	    
	    if ( $('#name').val() == '' ) {
	        error = true;
	        mensaje += " Debe ingresar una Fecha.\n";
	        $('#name').focus();
	    }
	    
	    if ( $('#description').val() == '' ) {
	        error = true;
	        mensaje += "Debe ingresar una <b>Descripci"+o_acentuada+"n</b>.<br>";
	        $('#description').focus();
	    }

	    if ( error ) {
            mostrarCartel(mensaje, 2);
        } else {
            $('#formEdicion').submit();
        }
    }

	$('#btGuardar').click(function(){
		validarLista();
	});

	$('#btAgregarSuscriptores').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editarSuscriptoresLista&id_lista='+$('#id').val()+'&pagina='+$('#pagina').val());
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
    	$("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 10)
        $("#item_administracion").addClass("text-info");
});