jQuery(document).ready(function() {
	
	$('#btVolver').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

	$('#btEnviarMail').click(function(){

		if (confirm('¿Desea enviar la Gacetilla por mail?')) {

			redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=enviarGacetillaPorMail&codigo='+$('#g_codigo').val());
		}
	});
});