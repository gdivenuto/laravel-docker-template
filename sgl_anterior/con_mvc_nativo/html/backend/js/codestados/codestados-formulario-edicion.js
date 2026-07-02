/**
 * Se vuelve al listado de Codificadora de Estados
 */
var listenerBotonCancelar = function () {
    $(location).attr('href', 'index.php?c=codestados&a=view');
};

/**
 * Se envían los datos para guardar la Codificadora de Estados
 */
var listenerBotonGuardar = function () {
    // Primero verifico el validador
    if (formEdicionCodestados.valid()) {
        // Paso los valores del formulario a la codificadora
        asignarValoresCodestado();

        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=codestados&a=save",
            data: JSON.stringify(codestado) // envio los parametros como JSON
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null)
                    $(location).attr('href', 'index.php?c=codestados&a=view');
                else
                    showModal('Error', 'Se esperaba una codificadora de estados y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'codestado') con cada campo del formulario de edición
 */
function asignarValoresCodestado()
{
    codestado.id_codestado = ($('#f_id_codestado').val() != '') ? $('#f_id_codestado').val() : 0;
    codestado.nombre_estado = $('#f_nombre_estado').val();
    codestado.vigencia_desde_codestado = ($('#f_vigencia_desde_codestado').val() != '') ? $('#f_vigencia_desde_codestado').val() : null;
    codestado.vigencia_hasta_codestado = ($('#f_vigencia_hasta_codestado').val() != '') ? $('#f_vigencia_hasta_codestado').val() : null;
    codestado.observaciones_codestado = ($('#f_observaciones_codestado').val() != '') ? $('#f_observaciones_codestado').val() : null;
    codestado.habilitado_codestado = $('#f_habilitado_codestado').val();
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object codestado Objeto Codestado deserializado
 */
function asignarCodestadoAFormulario(codestado)
{
    $('#f_id_codestado').val((codestado.id_codestado == 0) ? '' : decodeEntities(codestado.id_codestado));
    $('#f_nombre_estado').val(decodeEntities(codestado.nombre_estado));
    $('#f_vigencia_desde_codestado').val(decodeEntities(codestado.vigencia_desde_codestado));
    $('#f_vigencia_hasta_codestado').val(decodeEntities(codestado.vigencia_hasta_codestado));
    $('#f_observaciones_codestado').val(decodeEntities(codestado.observaciones_codestado));
    $('#f_habilitado_codestado').val(codestado.habilitado_codestado);
}

/**
 * Variables globales
 */
var formEdicionCodestados; // Referencia al formulario de edición de la Codificadora de Estados después de aplicarle validate().

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

    // Componentes de fecha
    // Se define la fecha Desde
    $('#v_vigencia_desde_codestado').datepicker({ altField: '#f_vigencia_desde_codestado',
                                                  onSelect: function(fecha_elegida) {
                                                    // Se establece como valor mínimo de la fecha hasta 
                                                    $("#v_vigencia_hasta_codestado").datepicker( "option", "minDate", fecha_elegida);
                                                  }
                                                });
    // Se define la fecha Hasta, donde su fecha mínima es la fecha Desde
    $('#v_vigencia_hasta_codestado').datepicker({ altField: '#f_vigencia_hasta_codestado', 
                                                  minDate: $('#v_vigencia_desde_codestado').val()
                                                });

    // Al renderizarse el checkbox
    $('#f_habilitado_codestado').ready(function () {
        if ( $('#f_habilitado_codestado').val() == 1 ) // si está seteado en 1
            $('#f_habilitado_codestado').prop("checked", true); // se tilda
        else
            $('#f_habilitado_codestado').prop("checked", false); // se destilda
    });
    // Al modificarse el checkbox
    $('#f_habilitado_codestado').change(function () {
        if ( $('#f_habilitado_codestado').val() == 1 ) // si está seteado en 1
            $('#f_habilitado_codestado').prop("checked", true); // se tilda
        else
            $('#f_habilitado_codestado').prop("checked", false); // se destilda
    });
    // Al usar el checkbox
    $('#f_habilitado_codestado').click(function () {
        if ( $('#f_habilitado_codestado').prop('checked') ) // si se tilda
            $('#f_habilitado_codestado').val(1); // se setea en 1
        else
            $('#f_habilitado_codestado').val(0); // se setea en 0
    });

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Se utiliza el objeto codestado para asignar los valores en cada campo del formulario de edición
    asignarCodestadoAFormulario(codestado);

    // -- Validación del formulario "form_edicion_codestado" ------------------
    formEdicionCodestados = $("#form_edicion_codestado");
    formEdicionCodestados.validate({
        rules: {
                f_nombre_estado: { required: true },
                v_vigencia_desde_codestado: { regexDate: true },
                v_vigencia_hasta_codestado: { regexDate: true }
               },
        messages: {
                f_nombre_estado: {
                    required: "Por favor ingrese un nombre."
                }
            }
    });
    
    // Sólo se permite el ingreso de valores numéricos en aquellos campos que posean la clase CSS 'solo-numero'
    $('.solo-numero').keyup(function (){
        this.value = (this.value + '').replace(/[^0-9]/g, '');
    });
});