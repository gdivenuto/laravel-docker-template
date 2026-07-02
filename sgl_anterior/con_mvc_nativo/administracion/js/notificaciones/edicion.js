
function subirEnTemporal() {
    
    $('#formEdicion').attr("action", $('#url_abms').val()+"?controlador="+$('#controlador').val()+"&accion=subirEnTemporal");

    $('#formEdicion').submit();
}

function eliminarTemporal(nombre_temporal) {

    var partes = nombre_temporal.split('__');
    var nombre_a_mostrar = partes[1];

    var url  = $('#url_abms').val()+'?controlador='+$('#controlador').val();
        url += '&accion=eliminarTemporal';
        url += '&n_id='+$('#n_id').val();
        url += '&prefijo='+$('#prefijo').val();
        url += '&nombre_temporal='+nombre_temporal;
        url += '&n_fecha='+$('#n_fecha').val();
        url += '&n_asunto='+$('#n_asunto').val();
        url += '&n_id_grupo_destino='+$('#n_id_grupo_destino').val();
        url += '&n_mensaje='+$('#n_mensaje').val();
        url += '&n_id_mail='+$('#n_id_mail').val();
        url += '&es_fe_erratas='+$('es_fe_erratas').val();
        url += '&pagina='+$('#pagina').val();

    if (confirm('¿Desea eliminar el archivo '+nombre_a_mostrar+' elegido?')) {
        redireccionar(url);
    }
}

function validarNotificacion() {
    
    var mensaje = '';
    var error = false;
    
    if ( $('#n_fecha').val() == '' ) {
        error = true;
        mensaje += " Debe ingresar una Fecha.\n";
        $('#n_fecha').focus();
    }
    
    if ( $('#n_asunto').val() == '' ) {
        error = true;
        mensaje += "Debe ingresar un <b>Asunto</b>.<br>";
        $('#n_asunto').focus();
    }

    // Si NO se eligió un Grupo y NO se eligió por lo menos una Lista
    if (($('#n_id_grupo_destino').val() == '0') && (! verificarCheckbox('.listas_destino'))) {
        error = true;
        mensaje += " Debe elegir un <b>Grupo</b> o una <b>Lista</b>.\n";
    }

    // Si no se ha ingresado el Mensaje
    if ( (! error) && $('#n_fecha').val() != '' && $('#n_asunto').val() != '' && $('#n_mensaje').val() == '' ) {
        // Si se confirma el guardado sin texto
        if ( confirm('¿Desea guardar la Notificacion sin mensaje?') ) {
            // Se envía el formulario
            $('#formEdicion').submit();
        } else {
            // Se le da el foco para permitir su ingreso
            $('#n_mensaje').focus();
        }
    } else {
        if ( error ) {
            mostrarCartel(mensaje, 2);
        } else {
            $('#formEdicion').submit();
        }
    }
}

jQuery(document).ready(function() {

    $('#n_fecha').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });
    
    // Si se trata de una NUEVA Notificación y NO es una Fe de Erratas
    if ($('#n_id').val() == '' && $('#es_fe_erratas').val() == '0') {
        // Se limpian los checkbox de las Listas
        desmarcarTodosCheckbox('formEdicion');//sin el # por ser JS vanilla
    }

    $('#btGuardar').click(function(){
        validarNotificacion();
    });

    $('#btCancelar').click(function(){

        var url  = $('#url_abms').val()+'?controlador='+$('#controlador').val();
            url += '&accion=cancelarEdicion';
            url += '&prefijo='+$('#prefijo').val();
            url += '&pagina='+$('#pagina').val();

        redireccionar(url);
    });

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 23)
        $("#item_actas").addClass("text-info");
    else if ($('#perfil_usuario').val() == 10)
        $("#item_administracion").addClass("text-info");
    else if ($('#perfil_usuario').val() == 11)
        $("#item_biblioteca").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
        $("#item_comisiones").addClass("text-info");
    else if ($('#perfil_usuario').val() == 24)
        $("#item_mesa_entradas").addClass("text-info");
    else if ($('#perfil_usuario').val() == 26)
        $("#item_modernizacion").addClass("text-info");
    else if ($('#perfil_usuario').val() == 15)
        $("#item_prensa").addClass("text-info");
    else if ($('#perfil_usuario').val() == 25)
        $("#item_presidencia").addClass("text-info");
});