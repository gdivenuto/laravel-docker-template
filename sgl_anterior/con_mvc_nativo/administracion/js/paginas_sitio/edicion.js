jQuery(document).ready(function() {

	$('#btGuardar').click(function(){
		$('#formEdicion').submit();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador=usuarios&accion=listar');
	});

    mostrarModal();

    $("#item_informatica").addClass("text-info");
});