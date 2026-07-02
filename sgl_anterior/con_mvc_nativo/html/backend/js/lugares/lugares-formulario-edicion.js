/**
 * Se vuelve al listado de Lugares (Codificadora de Iniciadores, Comisiones y Autores)
 */
var listenerBotonCancelar = function () {
    $(location).attr('href', 'index.php?c=lugares&a=view');
};

/**
 * Se envían los datos para guardar el Lugar (Codificadora de Iniciadores, Comisiones y Autores)
 */
var listenerBotonGuardar = function () {
    // Primero verifico el validador
    if (formEdicionLugares.valid()) {
        // Paso los valores del formulario a la codificadora
        asignarValoresLugar();

        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=lugares&a=save",
            data: JSON.stringify(lugar) // envio los parametros como JSON
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null)
                    $(location).attr('href', 'index.php?c=lugares&a=view');
                else
                    showModal('Error', 'Se esperaba un Lugar (codificadora de Iniciadores, Comisiones y Autores) y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'lugar') con cada campo del formulario de edición
 */
function asignarValoresLugar()
{
    lugar.tipo_grp          = $('#f_tipo_grp').val();
    lugar.codigo_grp        = $('#f_codigo_grp').val();
    lugar.descripcion_grp   = $('#f_descripcion_grp').val();
    lugar.abreviatura_grp   = ($('#f_abreviatura_grp').val() != '') ? $('#f_abreviatura_grp').val() : null;
    lugar.bloque_tipo       = ($('#f_bloque_tipo').val() != '') ? $('#f_bloque_tipo').val() : null;
    lugar.bloque_codigo     = ($('#f_bloque_codigo').val() != '') ? $('#f_bloque_codigo').val() : null;
    lugar.vigente_Desde_grp = ($('#f_vigente_Desde_grp').val() != '') ? $('#f_vigente_Desde_grp').val() : null;
    lugar.vigente_Hasta_grp = ($('#f_vigente_Hasta_grp').val() != '') ? $('#f_vigente_Hasta_grp').val() : null;
    lugar.observaciones_grp = ($('#f_observaciones_grp').val() != '') ? $('#f_observaciones_grp').val() : null;
    lugar.habilitado_grp    =  $('#f_habilitado_grp').val();
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object lugar Objeto Lugar deserializado
 */
function asignarLugarAFormulario(lugar)
{
    $('#f_tipo_grp').val(decodeEntities(lugar.tipo_grp));
    $('#f_codigo_grp').val(decodeEntities(lugar.codigo_grp));
    $('#f_descripcion_grp').val(decodeEntities(lugar.descripcion_grp));
    $('#f_abreviatura_grp').val(decodeEntities(lugar.abreviatura_grp));
    $('#f_bloque_tipo').val(decodeEntities(lugar.bloque_tipo));
    $('#f_bloque_codigo').val(decodeEntities(lugar.bloque_codigo));
    $('#f_vigente_Desde_grp').val(decodeEntities(lugar.vigente_Desde_grp));
    $('#f_vigente_Hasta_grp').val(decodeEntities(lugar.vigente_Hasta_grp));
    $('#f_observaciones_grp').val(decodeEntities(lugar.observaciones_grp));
    $('#f_habilitado_grp').val(lugar.habilitado_grp);
}

/**
 * Variables globales
 */
var formEdicionLugares; // Referencia al formulario de edición de Lugares (Codificadora de Iniciadores, Comisiones y Autores) después de aplicarle validate().

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
    $('#v_vigente_Desde_grp').datepicker({ altField: '#f_vigente_Desde_grp',
                                           onSelect: function(fecha_elegida) {
                                                // Se establece como valor mínimo de la fecha hasta 
                                                $("#v_vigente_Hasta_grp").datepicker( "option", "minDate", fecha_elegida);
                                           }
                                        });
    // Se define la fecha Hasta, donde su fecha mínima es la fecha Desde
    $('#v_vigente_Hasta_grp').datepicker({ altField: '#f_vigente_Hasta_grp', 
                                           minDate: $('#v_vigente_Desde_grp').val()
                                         });

    // Al renderizarse el checkbox
    $('#f_habilitado_grp').ready(function () {
        if ( $('#f_habilitado_grp').val() == 1 ) // si está seteado en 1
            $('#f_habilitado_grp').prop("checked", true); // se tilda
        else
            $('#f_habilitado_grp').prop("checked", false); // se destilda
    });
    // Al modificarse el checkbox
    $('#f_habilitado_grp').change(function () {
        if ( $('#f_habilitado_grp').val() == 1 ) // si está seteado en 1
            $('#f_habilitado_grp').prop("checked", true); // se tilda
        else
            $('#f_habilitado_grp').prop("checked", false); // se destilda
    });
    // Al usar el checkbox
    $('#f_habilitado_grp').click(function () {
        if ( $('#f_habilitado_grp').prop('checked') ) // si se tilda
            $('#f_habilitado_grp').val(1); // se setea en 1
        else
            $('#f_habilitado_grp').val(0); // se setea en 0
    });

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Se utiliza el objeto 'lugar' para asignar los valores en cada campo del formulario de edición
    asignarLugarAFormulario(lugar);

    // -- Validación del formulario "form_edicion_lugares" ------------------
    formEdicionLugares = $("#form_edicion_lugares");
    formEdicionLugares.validate({
        rules: {
                f_tipo_grp: { required: true },
                f_codigo_grp: { required: true },
                f_descripcion_grp: { required: true },
                v_vigente_Desde_grp: { regexDate: true }
               },
        messages: {
                f_tipo_grp: { 
                    required: "Por favor ingrese el Tipo."
                },
                f_codigo_grp: { 
                    required: "Por favor ingrese el C&oacute;digo."
                },
                f_descripcion_grp: {
                    required: "Por favor ingrese la Descripci&oacute;n."
                }
            }
    });
});