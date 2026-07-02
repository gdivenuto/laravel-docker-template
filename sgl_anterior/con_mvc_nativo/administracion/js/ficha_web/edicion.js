function eliminarTemporal(nombre_temporal) {

    var url  = $('#url_abms').val()+'?controlador='+$('#controlador').val();
        url += '&accion=eliminarTemporal';
        url += '&nombre_temporal='+nombre_temporal;
        
        // Resto de la info del registro
        url += '&fw_legajo='+$('#fw_legajo').val();
        url += '&fw_funcion='+$('#fw_funcion').val();
        url += '&fw_es_presidente_bloque='+$('#fw_es_presidente_bloque').val();
        url += '&fw_anio_inicio='+$('#fw_anio_inicio').val();
        url += '&fw_anio_fin='+$('#fw_anio_fin').val();
        url += '&fw_foto='+$('#fw_foto').val();
        url += '&fw_profesion='+$('#fw_profesion').val();
        url += '&fw_mail='+$('#fw_mail').val();
        url += '&fw_telefono='+$('#fw_telefono').val();
        url += '&fw_facebook='+$('#fw_facebook').val();
        url += '&fw_instagram='+$('#fw_instagram').val();
        url += '&fw_twitter='+$('#fw_twitter').val();
        url += '&fw_sitio_web='+$('#fw_sitio_web').val();
        url += '&fw_autor_codigo='+$('#fw_autor_codigo').val();
        url += '&pagina='+$('#pagina').val();

    if (confirm('¿Desea eliminar la foto previamente elegida?')) {
        redireccionar(url);
    }
}

function eliminarFoto() {
    if ( confirm('Desea eliminar la foto?') ) {
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=eliminarFoto&id='+$('#fw_legajo').val());
    }
}

function validarFichaWeb() {

    var mensaje = '';
	var error = false;
	
	// Si se ingresó el mail y NO es válido
	if ( $('#fw_mail').val() != '' && esEmailValido($('#fw_mail').val()) == false )  { 		
		error = true;
		mensaje += "Debe ingresar un mail v"+a_acentuada+"lido.\n";
		$('fw_mail').focus();
	}
   
	if ( error ) {
		mostrarCartel(mensaje, 2);
	} else {
		$('#formEdicion').submit();
    }
}

jQuery(document).ready(function() {
    
    $('#fw_es_presidente_bloque').on('change',function(){
        // Se establece el valor, si está chequeado o no
        $('#fw_es_presidente_bloque').val( ( $('#fw_es_presidente_bloque').prop('checked') ) ? 1 : 0);
    });

	$('#btGuardar').click(function(){
		validarFichaWeb();
	});

	$('#btVolver').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val()+'&id_editado='+$('#id').val());
	});
    
    $('#fw_funcion').focus();

    mostrarModal();

    $("#item_informatica").addClass("text-info");
});