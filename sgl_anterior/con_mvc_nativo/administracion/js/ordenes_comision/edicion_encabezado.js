jQuery(document).ready(function() {

    function validarEncabezadoOrdenDiaComision() {
        
        let mensaje = '';
		let error = false;
		
        if ( $('#editor_encabezado').val() == '' ) {
            mensaje += "Debe ingresar el texto del <b>encabezado</b>.<br>";
            $('#editor_encabezado').focus();
            error = true;
        }
       
		if ( error ) {
			mostrarCartel(mensaje, 2);
		}
		else {
			$('#formEdicion').submit();
	    }
    }

    $('#editor_encabezado').summernote({
        placeholder: 'Ingrese el texto del encabezado...',
        tabsize: 2,
        width: '100%',
        height: 300,
        toolbar: [
            // --- [nombre del grupo, [lista de botones]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']]
        ]
    });

    $('#btGuardar').click(function(){
        validarEncabezadoOrdenDiaComision();
    });

    $('#btCancelar').click(function(){
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editar&id='+$('#id').val());
    });
    
    mostrarModal();
    
    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
        $("#item_comisiones").addClass("text-info");
});