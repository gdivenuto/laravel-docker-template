jQuery(document).ready(function() {

    function validarRemitente() {
        let mensaje = '';
		let error = false;
		
		if ( $('#nombre').val() == '' ) {
            mensaje += "Debe ingresar un <b>Nombre</b>.<br>";
            $('#nombre').focus();
            error = true;
        }
        if ( $('#provincia_id').val() == '0' ) {
            mensaje += "Debe seleccionar una <b>Provincia</b>.<br>";
            $('#provincia_id').focus();
            error = true;
        }
        /*if ( $('#mail').val() != '' ) {
            if ( esEmailValido($('#mail').val()) === false ) {       
                mensaje += "Debe ingresar un <b>Mail v"+a_acentuada+"lido</b>.<br>";
                $('#mail').focus();
                error = true;
            }  
        } else {
            mensaje += "Debe ingresar un <b>Mail</b>.<br>";
            $('#mail').focus();
            error = true;
        }*/

		if ( error ) {
			mostrarCartel(mensaje, 2);
		}
		else {
			$('#formEdicion').submit();
	    }
    }

	$('#fecha_alta').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

	$('#btGuardar').click(function(){
		validarRemitente();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val()+'&id_editado='+$('#id').val());
	});

    mostrarModal();

    $("#item_remitente").addClass("color_resaltado");
});