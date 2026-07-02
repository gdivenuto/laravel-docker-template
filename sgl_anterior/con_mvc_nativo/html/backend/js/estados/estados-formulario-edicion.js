/**
 * Se vuelve al listado de Estados
 */
var listenerBotonCancelar = function () {
    irA('estados', $('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val());
};

/**
 * Se envían los datos para guardar el Estado
 */
var listenerBotonGuardar = function () {
    // Primero verifico el validador
    if (formEdicionEstado.valid()) {
        // Paso los valores del formulario al estado
        asignarValoresEstado();
        
        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=estados&a=save",
            data: JSON.stringify(estado) // envio los parametros como JSON
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null)
                    irA('estados', respuesta.data.anio, respuesta.data.tipo, respuesta.data.numero, respuesta.data.cuerpo, respuesta.data.alcance);
                else
                    showModal('Error', 'Se esperaba un estado y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'estado') con cada campo del formulario de edición
 */
function asignarValoresEstado()
{
    estado.anio = $('#f_anio').val();
    estado.tipo = $('#f_tipo').val();
    estado.numero = $('#f_numero').val();
    estado.cuerpo = $('#f_cuerpo').val();
    estado.alcance = $('#f_alcance').val();
    
    estado.orden_estado = $('#f_orden_estado').val();
    estado.fecha_estado = $('#f_fecha_estado').val();
    estado.id_codestado = ($('#f_id_codestado').val()) ? $('#f_id_codestado').val(): null;
    estado.observaciones_estado = ($('#f_observaciones_estado').val()) ? $('#f_observaciones_estado').val(): null;
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object estado Objeto Estado deserializado
 */
function asignarEstadoAFormulario(estado)
{
    $('#f_anio').val(decodeEntities(estado.anio));
    $('#f_tipo').val(decodeEntities(estado.tipo));
    $('#f_numero').val(decodeEntities(estado.numero));
    $('#f_cuerpo').val(decodeEntities(estado.cuerpo));
    $('#f_alcance').val(decodeEntities(estado.alcance));

    $('#f_orden_estado').val((estado.orden_estado == 0) ? '' : decodeEntities(estado.orden_estado));
    $('#f_fecha_estado').val(decodeEntities(estado.fecha_estado));
    $('#f_id_codestado').val(decodeEntities(estado.id_codestado));
    $('#f_observaciones_estado').val(decodeEntities(estado.observaciones_estado));
}

/**
 * Se visualiza una modal para buscar un Estado, utilizando un autosugerido
 */
function mostrarModalEstadoAutosugerido() {
    // Se muestra una modal para buscar un Estado mediante un autosugerido
    $('#modalEstadoAutosugerido').modal('show');
    // Se limpia el campo de búsqueda
    $('#modal_estado_sugerido').val('');
    // Se selecciona y se le da el foco al campo de búsqueda por autosugerido
    setfocus('#modal_estado_sugerido');
}

/**
 * Variables globales
 */
var formEdicionEstado; // Referencia al formulario de edición del Estado después de aplicarle validate().

/**
 * Entry Point de jQuery
 */
$(document).ready(function () {
    // form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
    fixValidatorBootstrap();

    // Agrego las expresiones regulares extra
    asignarValidatorExpresionesRegulares();

    // Inicialización de los DatePicker
    inicializarDatePicker();

    // Componente de fecha
    $('#v_fecha_estado').datepicker({
        altField: '#f_fecha_estado',
        onClose: function(fecha_estado_elegida) {
            $("#f_fecha_estado").val(formatearFechaConGuion(fecha_estado_elegida));
        }
    });
    
    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    $('#modal_estado_sugerido').keypress( function(e) {
        if ( $('#modal_estado_sugerido').val() != '' && e.which == 13 )
            $('#btCargarEstadoSugerido').focus();
    });

    $('#btCargarEstadoSugerido').click(function() {
        if ( $('#modal_estado_sugerido').val() != '' ) {
            // Se toma el estado sugerido y elegido
            var estado_sugerido = $('#modal_estado_sugerido').val();
            // Se separa el código y el nombre
            var aux_estado = estado_sugerido.split('-');
            // Se toma el código
            var codigo_estado = aux_estado[0];
            // Se selecciona el Estado en el combo
            $('#f_id_codestado').val(codigo_estado);
            // Se oculta la modal
            $('#modalEstadoAutosugerido').modal('hide');
        } else
            setfocus('#modal_estado_sugerido');
    });

    // Se utiliza el objeto 'estado' para asignar los valores en cada campo del formulario de edición
    asignarEstadoAFormulario(estado);

    // -- Validación del formulario "form_edicion_estado" ------------------
    formEdicionEstado = $("#form_edicion_estado");
    formEdicionEstado.validate({
        rules: {
                v_fecha_estado: { required: true, regexDate: true },
                f_id_codestado: { required: true, seleccionNoCero: true, seleccionNoVacio: true }
               },
        messages: {
                v_fecha_estado: { 
                    required: 'Debe especificar la fecha del Estado.'
                },
                f_id_codestado: {
                    required: 'Por favor seleccione un Estado.',
                    seleccionNoCero: 'Por favor seleccione un Estado.', 
                    seleccionNoVacio: 'Por favor seleccione un Estado.'
                }                
            }
    });

});