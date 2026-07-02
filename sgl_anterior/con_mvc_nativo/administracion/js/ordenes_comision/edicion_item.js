jQuery(document).ready(function() {

    function validarItemOrdenDiaComision() {
        
        var mensaje = '';
		var error = false;
		
        if ( $('#extracto').val() == '' ) {
            mensaje += "Debe ingresar el texto del <b>extracto</b>.<br>";
            $('#extracto').focus();
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
        validarItemOrdenDiaComision();
    });

    $('#btCancelar').click(function(){
        // Se vuelve a la edición de la Orden del día de la Comisión
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editar&id='+$('#id_orden_comision').val()+'&pagina='+$('#pagina').val());
    });
    
    mostrarModal();
    
    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
        $("#item_comisiones").addClass("text-info");
});