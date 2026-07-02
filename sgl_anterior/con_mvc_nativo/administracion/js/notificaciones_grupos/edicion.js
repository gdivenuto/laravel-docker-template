
function validarGrupo() {
    
    var mensaje = '';
    var error = false;
    
    if ( $('#id').val() == '' ) {
        error = true;
        mensaje += " Debe ingresar el <b>C"+o_acentuada+"digo</b>.<br>";
        $('#id').focus();
    }
    
    if ( $('#descripcion').val() == '' ) {
        error = true;
        mensaje += "Debe ingresar la <b>Descripci"+o_acentuada+"n</b>.<br>";
        $('#descripcion').focus();
    }
    
    // Si NO se eligió por lo menos una Lista
    if (! verificarCheckbox('.listas_destino')) {
        error = true;
        mensaje += " Debe elegir por lo menos una <b>Lista</b> de distribuci"+o_acentuada+"n.";
    }

    if ( error ) {
        mostrarCartel(mensaje, 2);
    } else {
        $('#formEdicion').submit();
    }
}

jQuery(document).ready(function() {

    // Si se trata de un nuevo Grupo
    if ($('#id').val() == '') {
        // Se limpian los checkbox de las Listas
        desmarcarTodosCheckbox('formEdicion');//sin el # por ser JS vanilla
    }

    $('#btGuardar').click(function(){
        validarGrupo();
    });

    $('#btCancelar').click(function(){
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
    });

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 10)
        $("#item_administracion").addClass("text-info");
    
});