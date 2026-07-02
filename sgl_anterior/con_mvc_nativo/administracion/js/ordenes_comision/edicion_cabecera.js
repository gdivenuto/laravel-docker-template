jQuery(document).ready(function() {

    function validarCabeceraOrdenDiaComision() {
        
        let mensaje = '';
		let error = false;
		
        if ( $('#asunto').val() == '' ) {
            mensaje += "Debe ingresar el texto del <b>T"+i_acentuada+"tulo de la Comisi"+o_acentuada+"n</b>.<br>";
            $('#asunto').focus();
            error = true;
        }
        if ( $('#fecha').val() == '' ) {
            mensaje += "Debe elegir una <b>Fecha</b>.<br>";
            $('#fecha').focus();
            error = true;
        }
        
        if ( $('#hora').val() == '' ) {
            mensaje += "Debe ingresar una <b>Hora</b>.<br>";
            $('#hora').focus();
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

    $('#hora').blur(function() {
        formatearHora($('#hora'));
    });

    $('#btGuardar').click(function(){
        validarCabeceraOrdenDiaComision();
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