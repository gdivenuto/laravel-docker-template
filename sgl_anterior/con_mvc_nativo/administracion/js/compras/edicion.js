jQuery(document).ready(function() {

    function validarCompra() {

        var mensaje = '';
		var error = false;
		
		if ( $('#comp_proveedor').val() == '' ) {
            mensaje += "Debe ingresar un <b>Proveedor</b>.<br>";
            $('#comp_proveedor').focus();
            error = true;
        }
        if ( $('#comp_concepto').val() == '' ) {
            mensaje += "Debe ingresar un <b>Concepto de Compra</b>.<br>";
            $('#comp_concepto').focus();
            error = true;
        }

		if ( error ) {
			mostrarCartel(mensaje, 2);
		}
		else {
			$('#formEdicion').submit();
	    }
    }

    $('#comp_fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

	$('#btGuardar').click(function(){
		validarCompra();
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