jQuery(document).ready(function() {

    function validarTipoProceso() {
        let mensaje = '';
		let error = false;
		
		if ( $('#nombre').val() == '' ) {
            mensaje += "Debe ingresar un <b>Nombre</b>.<br>";
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
		validarTipoProceso();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val()+'&id_editado='+$('#id').val());
	});
    
    // Se comienza editando el nombre
    $('#nombre').focus();

    // Para tabular con la tecla Enter
    $('#nombre').keypress( function(e) {
        // Sólo si se ingresó un valor
        if ( $('#nombre').val() != '' && e.which == 13 )
            $('#btGuardar').focus();
    });

    $("#item_tipo_proceso").addClass("color_resaltado");
});