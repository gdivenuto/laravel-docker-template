jQuery(document).ready(function() {

	$('#btNuevo').click( function() {
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editar&numero='+$('#numero').val());
	});

    $('#btVolver').click(function(){
        redireccionar($('#url_abms').val()+'?controlador=expediente&accion=listar');
    });
    
    mostrarModal();

    $("#item_expediente").addClass("color_resaltado");
});