jQuery(document).ready(function() {

    function validarContenidoWeb() {
        
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

	$('#btGuardar').click(function(){
        // Se asigna el contenido
        $('#contenido').val($('#editor_contenido').summernote('code'));
        
        if ( $('#contenido').val() != '<p><br></p>' && $('#contenido').val() != '' ) {
            mostrarCartel("Debe ingresar un <b>Contenido</b>.", 2);
        }

        // Se valida y envia el formulario
		validarContenidoWeb();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador=usuarios&accion=listar');
	});

    $('#editor_contenido').summernote({
        placeholder: 'Ingrese el contenido...',
        tabsize: 2,
        height: 270,
        toolbar: [
            // [nombre del grupo, [lista de botones]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            //['color', ['color']],
            //['table', ['table']],
            ['insert', ['picture']],
            ['para', ['ul', 'ol', 'paragraph']]
        ]
    });

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 11)
        $("#item_biblioteca").addClass("text-info");
});