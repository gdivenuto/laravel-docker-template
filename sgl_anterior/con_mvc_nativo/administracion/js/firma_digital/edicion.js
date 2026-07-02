jQuery(document).ready(function() {

	// Se tildan o destildan TODOS los checkbox
	$("#check_todos").on("click", function() {  
		$(".elegido").prop("checked", this.checked);  
	});  

	// Si todos los checkbox están seleccionados, se tilda o destilda el checkbox "check_todos"
	$(".elegido").on("click", function() {  
	  	if ($(".elegido").length == $(".elegido:checked").length) {  
	    	$("#check_todos").prop("checked", true);  
	  	} else {  
	    	$("#check_todos").prop("checked", false);  
	  	}  
	});

	// Se verifica que por lo menos un checkbox esté tildado
	function verificarCheckbox() {
	    return ($(".elegido:checked").length > 0);
	}

	// Para procesar la Firma de los documentos seleccionados
	$('#btEnviar').click(function(){

		if ($("#password").val() != '') {
	    	// Si NO se ha seleccionado ningún documento
			if ( !verificarCheckbox() ) {
				mostrarCartel("Debes seleccionar por lo menos un Documento.", 3);
			} else {
				//$('#formEdicion').submit();
				mostrarCartel("A FUTURO se firmar"+a_acentuada+"n los documentos seleccionados.", 1);
			}
		} else {
			mostrarCartel("Debes ingresar tu contrase"+enie+"a.", 3);
		}
    });

    $("#item_informatica").addClass("text-info");
});