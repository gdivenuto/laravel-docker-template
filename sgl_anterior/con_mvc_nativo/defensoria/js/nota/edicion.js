jQuery(document).ready(function() {

    $('#documento').on('change',function(){
        $(this).next('.custom-file-label').html($(this).val());
    });

    $('#btGuardar').click(function(){
        let documento = document.getElementById('documento').value;
        if (documento.length > 0) {
            $('#formEdicion').submit();
        } else {
            mostrarCartel("No has seleccionado ning"+u_acentuada+"n documento", 3);
        }
    });

    $('#btCancelar').click(function(){
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
    });

    mostrarModal();

    $("#item_nota").addClass("color_resaltado");
});