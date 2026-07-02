/**
 * Se vuelve al listado de Expedientes
 */
var listenerBotonCancelar = function () {
    irA('expedientes', $('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val());
};

/**
 * Entry Point de jQuery
 */
$(document).ready(function () {
    // Se visualiza la vista previa del expediente|nota|recomendación al cual se carga el documento temporal
    vistaPreviaExpediente($('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val());

    // -- Botones Examinar/Cancelar
    $('#btn_examinar').click(function () { $('#f_archivo_temporal').click(); });
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Al seleccionar un documento mediante el botón Examinar
    $('#f_archivo_temporal').change(function(){
        // Se sube directamente hasta que se disponga de un preview
        $('#form_upload_temporal').submit();
    });
});