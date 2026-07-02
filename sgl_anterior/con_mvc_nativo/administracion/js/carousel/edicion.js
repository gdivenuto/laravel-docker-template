
function subirEnTemporal() {
    
    $('#formEdicion').attr("action", $('#url_abms').val()+"?controlador="+$('#controlador').val()+"&accion=subirEnTemporal");

    $('#formEdicion').submit();
}

function eliminarTemporal(nombre_temporal) {

    var url  = $('#url_abms').val()+'?controlador='+$('#controlador').val();
        url += '&accion=eliminarTemporal';
        url += '&nombre_temporal='+nombre_temporal;
        
        // Resto de la info del registro
        url += '&id='+$('#id').val();
        url += '&recurso='+$('#recurso').val();
        url += '&enlace='+$('#enlace').val();
        url += '&pagina='+$('#pagina').val();

    if (confirm('¿Desea eliminar la foto previamente elegida?')) {
        redireccionar(url);
    }
}

function eliminarFoto() {
    if ( confirm('Desea eliminar la foto?') ) {
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=eliminarFoto&id='+$('#id').val());
    }
}

function validarRecursoCarousel() {
    var mensaje = '';
	var error = false;
	
	if ( error ) {
		mostrarCartel(mensaje, 2);
	}
	else {
		$('#formEdicion').submit();
    }
}

jQuery(document).ready(function() {

    $('#recurso').on('change',function(){
        // Se obtiene el nombre
        var fileName = $(this).val();
        // Se reemplaza la etiqueta por defecto "Choose a file"
        $(this).next('.custom-file-label').html(fileName);
    });

	$('#btGuardar').click(function(){
		validarRecursoCarousel();
	});

	$('#btVolver').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

    mostrarModal();

    $("#item_informatica").addClass("text-info");
});