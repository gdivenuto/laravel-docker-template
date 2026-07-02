/**
 * Se vuelve al listado de Proyectos
 */
var listenerBotonCancelar = function () {
    irA('proyectos', $('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val());
};

/**
 * Se envían los datos para guardar el Proyecto
 */
var listenerBotonGuardar = function () {
    // Primero se verifica el validador
    if (formEdicionProyecto.valid()) {
        // Paso los valores del formulario al proyecto
        asignarValoresProyecto();
        
        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=proyectos&a=save",
            data: JSON.stringify(proyecto) // envio los parametros como JSON
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null)
                    irA('proyectos', respuesta.data.anio, respuesta.data.tipo, respuesta.data.numero, respuesta.data.cuerpo, respuesta.data.alcance);
                else
                    showModal('Error', 'Se esperaba un proyecto y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'proyecto') con cada campo del formulario de edición
 */
function asignarValoresProyecto()
{
    proyecto.anio = $('#f_anio').val();
    proyecto.tipo = $('#f_tipo').val();
    proyecto.numero = $('#f_numero').val();
    proyecto.cuerpo = $('#f_cuerpo').val();
    proyecto.alcance = $('#f_alcance').val();
    
    proyecto.id_codproyecto = $('#f_id_codproyecto').val();
    proyecto.orden_proyecto = $('#f_orden_proyecto').val();
    proyecto.extracto = ($('#f_extracto').val()) ? $('#f_extracto').val(): null;
    proyecto.observaciones_proyecto = ($('#f_observaciones_proyecto').val()) ? $('#f_observaciones_proyecto').val(): null;
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object proyecto Objeto Proyecto deserializado
 */
function asignarProyectoAFormulario(proyecto)
{
    $('#f_anio').val(decodeEntities(proyecto.anio));
    $('#f_tipo').val(decodeEntities(proyecto.tipo));
    $('#f_numero').val(decodeEntities(proyecto.numero));
    $('#f_cuerpo').val(decodeEntities(proyecto.cuerpo));
    $('#f_alcance').val(decodeEntities(proyecto.alcance));

    $('#f_id_codproyecto').val(decodeEntities(proyecto.id_codproyecto));
    $('#f_orden_proyecto').val((proyecto.orden_proyecto == 0) ? '' : decodeEntities(proyecto.orden_proyecto));
    $('#f_extracto').val(decodeEntities(proyecto.extracto));
    $('#f_observaciones_proyecto').val(decodeEntities(proyecto.observaciones_proyecto));
}

/**
 * Variables globales
 */
var formEdicionProyecto; // Referencia al formulario de edición del proyecto después de aplicarle validate().

/**
 * Entry Point de jQuery
 */
$(document).ready(function () {
    // form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
    fixValidatorBootstrap();

    // Agrego las expresiones regulares extra
    asignarValidatorExpresionesRegulares();

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Se utiliza el objeto proyecto para asignar los valores en cada campo del formulario de edición
    asignarProyectoAFormulario(proyecto);

    // -- Validación del formulario "form_edicion_proyecto" ------------------
    formEdicionProyecto = $("#form_edicion_proyecto");
    formEdicionProyecto.validate({
        rules: {
                f_id_codproyecto: { required: true, seleccionNoCero: true },
                f_orden_proyecto: { digits: true }
               },
        messages: {
                f_id_codproyecto: {
                    required: "Por favor ingrese el Tipo de Proyecto.",
                    seleccionNoCero: "Por favor ingrese el Tipo de Proyecto."
                },
                f_orden_proyecto: { 
                    digits: "Por favor ingrese un n&uacute;mero de Orden v&aacute;lido." 
                }
            }
    });

});