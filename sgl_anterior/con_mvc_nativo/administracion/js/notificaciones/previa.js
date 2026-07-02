jQuery(document).ready(function() {
	
	$('#btVolver').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

	$('#btEnviarMail').click(function(){

		if (confirm('¿Desea enviar la Notificacion por mail?')) {

			redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=enviarNotificacionPorMail&id='+$('#n_id').val());
		}
	});
});