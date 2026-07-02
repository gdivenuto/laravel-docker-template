jQuery(document).ready(function() {

	$('#btVolver').click(function() {
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

	$('#btnConfirmarPublicacion').click( function() {
		if (confirm('¿Desea publicar la Orden del D'+i_acentuada+'a de Comisi'+o_acentuada+'n?')) {
			redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=confirmarPublicacion&id='+$('#id').val()+'&pagina='+$('#pagina').val());
		}
	});

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
    	$("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
    	$("#item_comisiones").addClass("text-info");
});