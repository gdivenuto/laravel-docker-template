function eliminarTemporal(nombre_temporal) {

    var url  = $('#url_abms').val()+'?controlador='+$('#controlador').val();
        url += '&accion=eliminarTemporal';
        url += '&nombre_temporal='+nombre_temporal;
        
        // Resto de la info del registro
        url += '&g_codigo='+$('#g_codigo').val();
        url += '&g_titulo='+$('#g_titulo').val();
        url += '&g_fecha='+$('#g_fecha').val();
        url += '&g_foto='+$('#g_foto').val();
        url += '&g_texto='+$('#g_texto').val();
        url += '&g_tipo='+$('#g_tipo').val();
        url += '&g_acto='+$('#g_acto').val();
        url += '&g_enviar_por_mail='+$('#g_enviar_por_mail').val();
        url += '&pagina='+$('#pagina').val();

    if (confirm('¿Desea eliminar la foto previamente elegida?')) {
        redireccionar(url);
    }
}

function eliminarFoto() {
    if ( confirm('Desea eliminar la foto?') ) {
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=eliminarFoto&id='+$('#g_codigo').val());
    }
}

function eliminarFotoSecundaria(id_imagen) {
    
    if ( confirm('Desea eliminar la foto?') ) {
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=eliminarFotoSecundaria&id_imagen='+id_imagen+'&id_gacetilla='+$('#g_codigo').val());
    }
}

function validarGacetilla() {
    var mensaje = '';
    var error = false;
    
    if ( $('#g_titulo').val() == '' ) {
        mensaje += "Debe ingresar un <b>t"+i_acentuada+"tulo</b>.<br>";
        $('#g_titulo').focus();
        error = true;
    }
    
    if ( $('#g_fecha').val() == '' ) {
        mensaje += "Debe elegir una <b>Fecha</b>.<br>";
        $('#g_fecha').focus();
        error = true;
    }

    if ( error ) {
        mostrarCartel(mensaje, 2);
    }
    else {
        $('#formEdicion').submit();
    }
}

jQuery(document).ready(function() {

	$('#g_fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    $('#fotos').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    });

    // Al renderizarse el Tipo
    $('#g_tipo').ready( function() {
        // Si el Tipo es Anuncio ó Escuela
        if ( $("#g_tipo").val() == 'A' || $("g_tipo").val() == 'E' )
            $('#g_acto').prop('disabled', true);
        else
            $('#g_acto').prop('disabled', false);
    });
    
    // Al seleccionarse un Tipo
    $('#g_tipo').change( function() {
        // Si el Tipo es Anuncio ó Escuela
        if ( $("#g_tipo").val() == 'A' || $("#g_tipo").val() == 'E' )
            $('#g_acto').prop('disabled', true);
        else
            $('#g_acto').prop('disabled', false);
    });
    
    // Si posee una foto
    if ( $('#g_foto').val() != '' ) {
        // Se muestra el botón Borrar
        $('#btBorrarFotoPpal').css('display','block');
    } else  {
        // Se oculta el botón Borrar
        $('#btBorrarFotoPpal').css('display','none');
    }

	$('#btGuardar').click(function(){
		validarGacetilla();
	});

	$('#btVolver').click(function(){
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val()+'&id_editado='+$('#g_codigo').val());
	});

    mostrarModal();

    // Foco en el título
    $('#g_titulo').focus();

    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 15)
        $("#item_prensa").addClass("text-info");
});