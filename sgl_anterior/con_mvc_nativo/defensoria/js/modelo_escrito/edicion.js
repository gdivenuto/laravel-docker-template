jQuery(document).ready(function() {

    function validarModeloEscrito() {
        let mensaje = '';
		let error = false;
		
		if ( $('#nombre').val() == '' ) {
            mensaje += "Debe ingresar un <b>Nombre</b>.<br>";
            $('#nombre').focus();
            error = true;
        }
        if ( $('#descripcion').val() == '' ) {
            mensaje += "Debe ingresar una <b>Descripci"+o_acentuada+"n</b>.<br>";
            $('#descripcion').focus();
            error = true;
        }
       
		if ( error ) {
			mostrarCartel(mensaje, 2);
		} else {
			$('#formEdicion').submit();
	    }
    }

	$('#btGuardar').click(function(){
		validarModeloEscrito();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val()+'&id_editado='+$('#id').val());
	});
    
    $('#nombre').focus();

    $('#nombre').keypress( function(e) {
        if ( $('#nombre').val() != '' && e.which == 13 )
            $('#btGuardar').focus();
    });

    $("#item_modelo_escrito").addClass("color_resaltado");
});