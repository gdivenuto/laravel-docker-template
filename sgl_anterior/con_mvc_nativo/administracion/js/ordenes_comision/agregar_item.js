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

    // Para procesar el envío de los expedientes seleccionados
    $('#btGuardar').click(function(){
        // Si NO se ha seleccionado ningún Expediente
        if ( !verificarCheckbox() )
            mostrarCartel("Debes seleccionar por lo menos un Expediente.", 3);
        else
            $('#formEdicion').submit();
    });

    $('#btCancelar').click(function(){
        // Se vuelve a la edición de la Orden del día de la Comisión
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editar&id='+$('#id_orden_comision').val()+'&pagina='+$('#pagina').val());
    });
    
    mostrarModal();
    
    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
        $("#item_comisiones").addClass("text-info");
});