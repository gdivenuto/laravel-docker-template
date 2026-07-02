jQuery(document).ready(function() {

    function validarResolucion() {

        let mensaje = '';
		let error = false;
		
        if ( $('#remitente_id').val() == '0' ) {
            mensaje += "Debe elegir un <b>Remitente</b>.<br>";
            $('#remitente_id').focus();
            error = true;
        }
        if ( $('#fecha').val() == '' ) {
            mensaje += "Debe elegir una <b>Fecha</b>.<br>";
            $('#fecha').focus();
            error = true;
        }

		if ( error ) {
			mostrarCartel(mensaje, 2);
		}
		else {
			$('#formEdicion').submit();
	    }
    }

    $('#fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

	$('#btGuardar').click(function(){
		validarResolucion();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

    mostrarModal();

    $("#item_resolucion").addClass("color_resaltado");
});