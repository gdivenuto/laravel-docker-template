
function eliminarDocumentoFirmado(documento) {

    let url  = $('#url_abms').val();
        url += '?controlador='+$('#controlador').val();
        url += '&accion=eliminarDocumentoFirmado';
        url += '&documento='+documento;
        url += '&id='+$('#id').val();
        url += '&pagina='+$('#pagina').val();

    if (confirm('¿Desea eliminar el documento firmado?')) {
        redireccionar(url);
    }
}

jQuery(document).ready(function() {

    $('#documento').on('change',function(){
        let fileName = $(this).val();
        $(this).next('.custom-file-label').html(fileName);
    });

	$('#btUpload').click(function(){
        let documento = document.getElementById('documento').value;
		if (documento.length > 0) {
            $('#formUpload').submit();
        } else {
            mostrarCartel("No has seleccionado ning"+u_acentuada+"n documento", 3);
        }
    });

    mostrarModal();

    if ($('#perfil_usuario').val() == 14)
        $("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
        $("#item_comisiones").addClass("text-info");
});