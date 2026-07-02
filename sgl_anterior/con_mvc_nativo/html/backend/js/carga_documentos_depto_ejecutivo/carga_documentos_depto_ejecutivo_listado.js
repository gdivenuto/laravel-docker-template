/**
 * Se vuelve al listado de Proyectos
 */
var listenerBotonCancelar = function () {
    irA('antecedentes', $('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val());
};

/**
 * Entry Point de jQuery
 */
$(document).ready(function () {
    // Se envía el formulario
    $('#btn_cargar').click(function () { 
    	// Se verifica si está tildado por lo menos un documento para su carga
    	if (verificarCheckbox('.chk_documento_DE')) {
    		$('#form_upload_documentos_ejecutivo').submit();
    	} else {
    		showModal('Atenci&oacute;n', 'Debe seleccionar por lo menos un documento.');
    	}
    });

    // Se vuelve al listado de Antecedentes del expediente respectivo
    $('#btn_cancelar').click(listenerBotonCancelar);
});