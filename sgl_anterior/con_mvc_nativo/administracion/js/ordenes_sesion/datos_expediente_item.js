jQuery(document).ready(function() {

	$('#btActualizarExtracto').click(function() {
        
        if ( $('#anio').val() != '' && ($('#tipo').val() == 'E' || $('#tipo').val() == 'N') && $('#numero').val() != '' ) {

            var url = $('#url_abms').val()+'?controlador='+$('#controlador').val();
                url += '&accion=actualizarExtracto';
                url += '&anio='+$('#anio').val();
                url += '&tipo='+$('#tipo').val()
                url += '&numero='+$('#numero').val();

            $.ajax({
                type: "POST",
                url: url,
                success: function (respuesta) {
                    $('#extracto').val(respuesta);
                }
            });
        } else {
            mostrarCartel("Debe ingresar previamente la clave (A"+enie+"o-Tipo-N"+u_acentuada+"mero) de un Expediente o Nota.", 2);
        }
    });

	$('#btCopiarIniciador').click(function() {
        
        if ( $('#anio').val() != '' && ($('#tipo').val() == 'E' || $('#tipo').val() == 'N') && $('#numero').val() != '' ) {
            // Si se posee la descripción del Iniciador
            if ( $('#descripcion_iniciador').val() != '' )
                // Se asigna en el textarea de Iniciador/Autor
                $('#autor').val($('#descripcion_iniciador').val());
            else
                mostrarCartel("No se posee una descripci"+o_acentuada+"n del Iniciador.", 2);
                
        } else
            mostrarCartel("Debe ingresar previamente la clave (A"+enie+"o-Tipo-N"+u_acentuada+"mero) de un Expediente o Nota.", 2);
    });

    $('#btCopiarAutor').click(function() {
        
        if ( $('#anio').val() != '' && ($('#tipo').val() == 'E' || $('#tipo').val() == 'N') && $('#numero').val() != '' ) {
            // Si se posee la descripción del Autor
            if ( $('#descripcion_autor').val() != '' )
                // Se asigna en el textarea de Iniciador/Autor
                $('#autor').val($('#descripcion_autor').val());
            else
                mostrarCartel("No se posee una descripci"+o_acentuada+"n del Autor.", 2);
                
        } else
            mostrarCartel("Debe ingresar previamente la clave (A"+enie+"o-Tipo-N"+u_acentuada+"mero) de un Expediente o Nota.", 2);
    });

	if ( puede_registrarse_item ) { // Si puede registrarse el ítem
		$('#btGuardar').css('display', 'inline-block');
		
		$('#mensaje_clave_documento_orden_sesion').html('');
		
		if ( $('#tipo').val() == 'E' || $('#tipo').val() == 'N' ) {
			$('#caratula').focus();
		} else {
			$('#extracto').focus();
		}
	} else {
		$('#btGuardar').css('display', 'none');
		
		$('#mensaje_clave_documento_orden_sesion').css({
			'float':'left', 
			'font-size':'14px', 
			'color':'red', 
			'padding':'7px 0 0 10px'
		});

		$('#mensaje_clave_documento_orden_sesion').html('<?= $mensaje; ?>');
		
		$('#numero').focus();
	}
});