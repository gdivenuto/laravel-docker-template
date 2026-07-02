jQuery(document).ready(function() {

    function validarUsuario()
    {
        let mensaje = '';
		let error = false;
		
        if ( $('#codigo_usuario').val() == '' ) {
            mensaje += "Debe ingresar un <b>Usuario</b>.<br>";
            $('#codigo_usuario').focus();
            error = true;
        }
		
        if ( $('#nombre_usuario').val() == '' ) {
            mensaje += "Debe ingresar un <b>Nombre</b>.<br>";
            $('#nombre_usuario').focus();
            error = true;
        }
        
        if ( ( $('#password_usuario').val() != '' ) && ( $('#password_usuario').val() != $('#confirmar_password_usuario').val() ) ) {
            mensaje += "Deben coincidir las Contrase"+enie+"as.\n";
            $('#confirmar_password_usuario').focus();
            error = true;
        }

        // Si el perfil para Expedientes es 1, 2 o 3, se verifica que se haya ingresado un Legajo
        //if ( $.inArray($('input[name=perfil_exped]:checked').val(), [1,2,3]) != -1 )
        /*if ( $('input[name=perfil_exped]:checked').val() != 4 )
        {
            if ( $('#u_legajo').val() == '' ) {
                mensaje += "Debe ingresar un <b>Legajo</b> para el perfil de Expedientes elegido.<br>";
                $('#u_legajo').focus();
                error = true;
            }
        }*/
        // Si Confirma Giros está tildado, se verifica que se haya ingresado un Legajo
        if ( $('#confirma_giros').is(":checked") ) {
            if ( $('#u_legajo').val() == '' ) {
                mensaje += "Debe ingresar un <b>Legajo</b> para confirmar giros.<br>";
                $('#u_legajo').focus();
                error = true;
            }
        }

		if ( error ) {
			mostrarCartel(mensaje, 2);
		}
		else {
			$('#formEdicion').submit();
	    }
    }

    $('#codigo_usuario').change(function(){

        if ($('#codigo_usuario').val() != '') {
            // Se verifica la existencia del nombre de usuario ingresado
            $.ajax({
                data: {
                    "controlador" : $('#controlador').val(), 
                    "accion" : "estaDisponibleNombreUsuario",
                    "codigo_usuario" : $('#codigo_usuario').val()
                },
                type: "GET",
                //dataType: "json",// Formato de datos que se espera en la respuesta
                url: $('#url_abms').val() // URL a la que se enviará la solicitud Ajax
            })
            .done(function( data, textStatus, jqXHR ) {
                //if ( console && console.log )
                    //console.log(data);
                // Si no se encontró, está disponible
                if (data == null || data == '') {
                    $('#password_usuario').focus();
                } else {
                    $('#codigo_usuario').val('');
                    mostrarCartel("El nombre de usuario <strong>"+data+"</strong> no se encuentra disponible. Ingrese uno diferente, gracias.", 2);
                }
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                if ( console && console.log )
                    console.log( "La solicitud a fallado: "+textStatus);
            });
        }
    });

    // 2026-01-29
    // Se ha comentado para el registro de Periodistas (ya que no existen en SGL/Personal)
    /*$('#nombre_usuario').change(function(){

        if ($('#nombre_usuario').val() != '') {
            $.ajax({
                data: {
                    "controlador" : $('#controlador').val(), 
                    "accion" : "buscarLegajoPoNombreUsuario",
                    "nombre_usuario" : $('#nombre_usuario').val()
                },
                type: "GET",
                url: $('#url_abms').val()
            })
            .done(function( data, textStatus, jqXHR ) {
                if (data == null || data == '') {
                    $('#u_legajo').focus();
                    mostrarCartel("El legajo <strong>"+data+"</strong> no se ha encontrado.<br>Verifique en Personal.", 2);
                } else {
                    $('#u_legajo').val(data);
                }
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                if ( console && console.log )
                    console.log( "La solicitud a fallado: "+textStatus);
            });
        }
    });*/

    $('#u_legajo').change(function()
    {
        if ($('#u_legajo').val() != '') {
            // Se verifica la existencia del legajo ingresado
            $.ajax({
                data: {
                    "controlador" : $('#controlador').val(), 
                    "accion" : "existeLegajo",
                    "u_legajo" : $('#u_legajo').val()
                },
                type: "GET",
                dataType: "json",// Formato de datos que se espera en la respuesta
                url: $('#url_abms').val() // URL a la que se enviará la solicitud Ajax
            })
            .done(function( data, textStatus, jqXHR ) {
                //if ( console && console.log ) console.log(data);
                // Si no se encontró, está disponible
                if (data == null || data == '') {
                    $('#nombre_apellido_legajo').html('<span style="background-color:#d24741;color:white;margin-top:5px;padding:5px;">NO EXISTE EL LEGAJO</span>');
                    $('#btGuardar').css('display', 'none');
                    mostrarCartel("El legajo <strong>"+$('#u_legajo').val()+"</strong> no se encuentra registrado en el sistema.", 2);                    
                } else {
                    // Se muestra su nombre y apellido para constatar con el del formulario
                    $('#nombre_apellido_legajo').html(data.p_apellido+', '+data.p_nombre);
                    $('#btGuardar').css('display', 'inline-block');
                }
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                if ( console && console.log )
                    console.log( "La solicitud a fallado: "+textStatus);
            });
        } else {
            $('#nombre_apellido_legajo').html('');
            $('#btGuardar').css('display', 'inline-block');
        }
    });

	$('#btGuardar').click(function(){
		validarUsuario();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val()+'&id_editado='+$('#a_id').val());
	});

    $("#item_informatica").addClass("text-info");
});