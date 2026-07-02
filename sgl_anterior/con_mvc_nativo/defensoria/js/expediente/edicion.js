jQuery(document).ready(function() {

    function validarExpediente() {

        let mensaje = '';
		let error = false;
		
        if ( $('#presentante_id').val() == '0' ) {
            mensaje += "Debe elegir un <b>Presentante</b>.<br>";
            $('#presentante_id').focus();
            error = true;
        }
        if ( $('#tipo_proceso_id').val() == '0' ) {
            mensaje += "Debe elegir un <b>Tipo de Proceso</b>.<br>";
            $('#tipo_proceso_id').focus();
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
		validarExpediente();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

    mostrarModal();

    $("#item_expediente").addClass("color_resaltado");
});