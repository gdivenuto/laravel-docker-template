
function subirEnTemporal() {
    
    $('#formEdicion').attr("action", $('#url_abms').val()+"?controlador="+$('#controlador').val()+"&accion=subirEnTemporal");

    $('#formEdicion').submit();
}

function eliminarTemporal(nombre_temporal) {

    var partes = nombre_temporal.split('__');
    var nombre_a_mostrar = partes[1];

    var url  = $('#url_abms').val()+'?controlador='+$('#controlador').val();
        url += '&accion=eliminarTemporal';
        url += '&id_dataset='+$('#id').val();
        url += '&prefijo='+$('#prefijo').val();
        url += '&nombre_temporal='+nombre_temporal;
        url += '&titulo='+$('#titulo').val();
        url += '&fecha_emitido='+$('#fecha_emitido').val();
        url += '&fecha_modificado='+$('#fecha_modificado').val();
        url += '&id_catalogo='+$('#id_catalogo').val();
        url += '&id_publicador='+$('#id_publicador').val();
        url += '&identificador='+$('#identificador').val();
        url += '&palabras_clave='+$('#palabras_clave').val();
        url += '&lenguaje='+$('#lenguaje').val();
        url += '&frecuencia='+$('#frecuencia').val();
        url += '&url='+$('#url').val();
        url += '&licencia='+$('#licencia').val();
        url += '&fuente='+$('#fuente').val();
        url += '&nivel_acceso='+$('#nivel_acceso').val();
        url += '&descripcion='+$('#descripcion').val();
        url += '&pagina='+$('#pagina').val();

    if (confirm('¿Desea eliminar el archivo '+nombre_a_mostrar+' elegido?')) {
        redireccionar(url);
    }
}

function validarDataset() {
    
    var mensaje = '';
    var error = false;
    
    if ( $('#titulo').val() == '' ) {
        error = true;
        mensaje += "Debe ingresar un <b>T"+i_acentuada+"tulo</b>.<br>";
        $('#titulo').focus();
    }
    
    if ( $('#fecha_emitido').val() == '' ) {
        error = true;
        mensaje += " Debe ingresar una Fecha de Emisi"+o_acentuada+"n.\n";
        $('#fecha_emitido').focus();
    }
    
    if ( error ) {
        mostrarCartel(mensaje, 2);
    }
    else {
        $('#formEdicion').submit();
    }
}

jQuery(document).ready(function() {

    $('#fecha_emitido').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });
    $('#fecha_modificado').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    $('#btGuardar').click(function(){
        validarDataset();
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
    else if ($('#perfil_usuario').val() == 26)
        $("#item_modernizacion").addClass("text-info");
});