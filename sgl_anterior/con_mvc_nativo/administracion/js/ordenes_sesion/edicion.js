jQuery(document).ready(function() {

    /**
     * Se verifica si posee texto un editor determinado por su Id
     * @param  {string}     id_editor   Identificador ('#nombre', por ejemplo)
     * @return {boolean}                True|False
     */
    poseeTexto = function(id_editor) {
        return ($(id_editor).val() != '<p><br></p>' && $(id_editor).val() != '' && $(id_editor).val() != undefined);
    }

    function validarOrdenDiaSesion() {
        
        let mensaje = '';
		let error = false;
		
        if ( $('#periodo').val() == '' ) {
            mensaje += "Debe ingresar un <b>Per"+i_acentuada+"odo</b>.<br>";
            $('#periodo').focus();
            error = true;
        }

		if ( $('#sesion').val() == '' ) {
            mensaje += "Debe ingresar un texto para la <b>Sesi"+o_acentuada+"n</b>.<br>";
            $('#sesion').focus();
            error = true;
        }
        
        if ( $('#fecha').val() == '' ) {
            mensaje += "Debe elegir una <b>Fecha</b>.<br>";
            $('#fecha').focus();
            error = true;
        }
        
        if ( $('#hora').val() == '' ) {
            mensaje += "Debe ingresar una <b>Hora</b>.<br>";
            $('#hora').focus();
            error = true;
        }

        $('#editor_texto_decreto_previo_anexo').val($('#editor_texto_decreto_previo_anexo').summernote('code'));
        
		if ( error ) {
			mostrarCartel(mensaje, 2);
		}
		else {
			$('#formEdicion').submit();
	    }
    }

    function verificarUsoDecretoConAnexo() {
        if ($('#sin_decreto_y_anexo').prop('checked')) {
            $('#contenedor_texto_decreto_previo_anexo').removeClass('d-inline-block').addClass('d-none');
        }
        if ($('#con_decreto_y_anexo').prop('checked')) {
            $('#contenedor_texto_decreto_previo_anexo').removeClass('d-none').addClass('d-inline-block');
        }
    }

    $('#fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    $('#hora').blur(function(){
        formatearHora($('#hora'));
    });
    
    // Si se modifica
    if ( $('#id').val() != '' ) {
        $('#periodo').focus();
    } else {
        $('#fecha').focus();
    }
    
    jQuery(function($) {
        $('#sin_decreto_y_anexo').on('change', function() {
            verificarUsoDecretoConAnexo();
        }).trigger('change');
    });
    jQuery(function($) {
        $('#con_decreto_y_anexo').on('change', function() {
            verificarUsoDecretoConAnexo();
        }).trigger('change');
    });

    $('#editor_texto_decreto_previo_anexo').summernote({
        placeholder: 'Ingrese el texto del decreto...',
        tabsize: 2,
        height: 225,
        toolbar: [
            // --- [nombre del grupo, [lista de botones]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']]
        ]
    });
    
    $('#btCargarGirosItems').click(function(){
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=cargarGirosOrden&id_sesion='+$('#id').val()+'&pagina='+$('#pagina').val());
    });

    $('#btNuevoItem').click(function(){
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=agregarItem&id_sesion='+$('#id').val()+'&pagina='+$('#pagina').val());
    });

	$('#btGuardar').click(function(){
		validarOrdenDiaSesion();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});
    
    mostrarModal();
    
    if ($('#perfil_usuario').val() == 14)
    	$("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 10)
    	$("#item_administracion").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
    	$("#item_comisiones").addClass("text-info");
});