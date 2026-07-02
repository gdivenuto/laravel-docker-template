jQuery(document).ready(function() {

    function validarEquipo() {

        var mensaje = '';
        var error = false;
        
        if ( $('#nombre_netbios').val() == '' ) {
            mensaje += "Debe ingresar un <strong>Nombre</strong> para el equipo.<br>";
            $('#nombre_netbios').focus();
            error = true;
        }
        if ( $('#fecha_alta').val() == '' ) {
            mensaje += "Debe ingresar una <strong>Fecha de Alta</strong>.<br>";
            $('#fecha_alta').focus();
            error = true;
        }

        if ( $('#fecha_alta').val() != '' && $('#fecha_caducidad').val() != '' )
        {
            if ( esLaFechaMayor($('#fecha_alta').val(), $('#fecha_caducidad').val()) )
            {
                mensaje += "La Fecha de Alta <b>debe ser menor</b> a la Fecha de Caducidad.<br>";
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

    $('#nombre_netbios').change(function(){

        if ($('#nombre_netbios').val() != '') {
            // Se verifica la existencia del nombre de usuario ingresado
            $.ajax({
                data: {
                    "controlador" : $('#controlador').val(), 
                    "accion" : "estaDisponibleNombreNetbios",
                    "nombre_netbios" : $('#nombre_netbios').val()
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
                    $('#parte_direccion_mac_0').focus();
                } else {
                    $('#nombre_netbios').val('');
                    mostrarCartel("El nombre del equipo <strong>"+data+"</strong> no se encuentra disponible. Ingrese uno diferente, gracias.", 2);
                }
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                if ( console && console.log )
                    console.log( "La solicitud a fallado: "+textStatus);
            });
        }
    });

    $('#fecha_alta').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    $('#fecha_caducidad').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    function refrezcarComboResponsables() {

        if (siEstaDefinido('#cod_area') && $('#cod_area').val() != '0') {

            let valor_cod_area = $('#cod_area').val();

            if (valor_cod_area !== null) {
                let tipo_area = valor_cod_area.substring(0, 2);
                
                if ( tipo_area == '02' ) {
                    var url = $('#url_abms').val()+'?controlador='+$('#controlador').val();
                    url += '&accion=refrescarComboResponsables';
                    url += '&cod_area='+$('#cod_area').val();
                    url += '&cod_responsable='+$('#cod_responsable').val();
                    url += '&se_edita=1';

                    // Se muestran los responsables del area determinada,
                    // que se encuentren ACTIVOS en el Sistema de Personal
                    refrescar(url, '#cod_responsable');
                }
            }
        }
    }
    
    $('#parte_direccion_mac_0').keyup(function() {
        if ( $('#parte_direccion_mac_0').val() != '' && // Si posee un valor ingresado
             $('#parte_direccion_mac_0').val().length == 2 && // si ya tiene dos caracteres
             $('#parte_direccion_mac_0').val() != $('#parte_direccion_mac_0_actual').val()) // si se ha cambiado su valor (en caso de una edición)
        {
            setTimeout("$('#parte_direccion_mac_1').select()",75);
        }
    });
    $('#parte_direccion_mac_0').blur(function() {
        // Si el valor ingresado no es válido
        if ( ! esRangoMAC_Valido($('#parte_direccion_mac_0').val()) ){
            mostrarCartel("Debe ingresar un car"+a_acentuada+"cter v"+a_acentuada+"lido para la direcci"+o_acentuada+"n MAC!", 3);
            $('#parte_direccion_mac_0').focus();
        }
    });
    
    $('#parte_direccion_mac_1').keyup(function() {
        if ( $('#parte_direccion_mac_1').val() != '' && // Si posee un valor ingresado
             $('#parte_direccion_mac_1').val().length == 2 && // si ya tiene dos caracteres
             $('#parte_direccion_mac_1').val() != $('#parte_direccion_mac_1_actual').val()) // si se ha cambiado su valor (en caso de una edición)
        {
            setTimeout("$('#parte_direccion_mac_2').select()",75);
        }
    });
    $('#parte_direccion_mac_1').blur(function() {
        // Si el valor ingresado no es válido
        if ( ! esRangoMAC_Valido($('#parte_direccion_mac_1').val()) ){
            mostrarCartel("Debe ingresar un car"+a_acentuada+"cter v"+a_acentuada+"lido para la direcci"+o_acentuada+"n MAC!", 3);
            $('#parte_direccion_mac_1').focus();
        }
    });
    
    $('#parte_direccion_mac_2').keyup(function() {
        if ( $('#parte_direccion_mac_2').val() != '' && // Si posee un valor ingresado
             $('#parte_direccion_mac_2').val().length == 2 && // si ya tiene dos caracteres
             $('#parte_direccion_mac_2').val() != $('#parte_direccion_mac_2_actual').val()) // si se ha cambiado su valor (en caso de una edición)
        {
            setTimeout("$('#parte_direccion_mac_3').select()",75);
        }
    });
    $('#parte_direccion_mac_2').blur(function() {
        // Si el valor ingresado no es válido
        if ( ! esRangoMAC_Valido($('#parte_direccion_mac_2').val()) ){
            mostrarCartel("Debe ingresar un car"+a_acentuada+"cter v"+a_acentuada+"lido para la direcci"+o_acentuada+"n MAC!", 3);
            $('#parte_direccion_mac_2').focus();
        }
    });
    
    $('#parte_direccion_mac_3').keyup(function() {
        if ( $('#parte_direccion_mac_3').val() != '' && // Si posee un valor ingresado
             $('#parte_direccion_mac_3').val().length == 2 && // si ya tiene dos caracteres
             $('#parte_direccion_mac_3').val() != $('#parte_direccion_mac_3_actual').val()) // si se ha cambiado su valor (en caso de una edición)
        {
            setTimeout("$('#parte_direccion_mac_4').select()",75);
        }
    });
    $('#parte_direccion_mac_3').blur(function() {
        // Si el valor ingresado no es válido
        if ( ! esRangoMAC_Valido($('#parte_direccion_mac_3').val()) ){
            mostrarCartel("Debe ingresar un car"+a_acentuada+"cter v"+a_acentuada+"lido para la direcci"+o_acentuada+"n MAC!", 3);
            $('#parte_direccion_mac_3').focus();
        }
    });
        
    $('#parte_direccion_mac_4').keyup(function() {
        if ( $('#parte_direccion_mac_4').val() != '' && // Si posee un valor ingresado
             $('#parte_direccion_mac_4').val().length == 2 && // si ya tiene dos caracteres
             $('#parte_direccion_mac_4').val() != $('#parte_direccion_mac_4_actual').val()) // si se ha cambiado su valor (en caso de una edición)
        {
            setTimeout("$('#parte_direccion_mac_5').select()",75);
        }
    });
    $('#parte_direccion_mac_4').blur(function() {
        // Si el valor ingresado no es válido
        if ( ! esRangoMAC_Valido($('#parte_direccion_mac_4').val()) ){
            mostrarCartel("Debe ingresar un car"+a_acentuada+"cter v"+a_acentuada+"lido para la direcci"+o_acentuada+"n MAC!", 3);
            $('#parte_direccion_mac_4').focus();
        }
    });
    
    $('#parte_direccion_mac_5').keyup(function() {
        if ( $('#parte_direccion_mac_5').val() != '' && // Si posee un valor ingresado
             $('#parte_direccion_mac_5').val().length == 2 && // si ya tiene dos caracteres
             $('#parte_direccion_mac_5').val() != $('#parte_direccion_mac_5_actual').val()) // si se ha cambiado su valor (en caso de una edición)
        {
            setTimeout("$('#parte_ip_0').select()",75);
        }
    });
    $('#parte_direccion_mac_5').blur(function() {
        // Si el valor ingresado no es válido
        if ( ! esRangoMAC_Valido($('#parte_direccion_mac_5').val()) ){
            mostrarCartel("Debe ingresar un car"+a_acentuada+"cter v"+a_acentuada+"lido para la direcci"+o_acentuada+"n MAC!", 3);
            $('#parte_direccion_mac_5').focus();
        }
    });
    
    // Al renderizarse el combo de Areas
    $('#cod_area').ready(function() {
        refrezcarComboResponsables();
    });
    
    // Al producirse un cambio en el combo de Areas
    $('#cod_area').change(function() {
        refrezcarComboResponsables();
    });
    
    $('#btGuardar').click(function(){
        validarEquipo();
    });

    $('#btCancelar').click(function(){
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
    });

    mostrarModal();

    $("#item_informatica").addClass("text-info");
});