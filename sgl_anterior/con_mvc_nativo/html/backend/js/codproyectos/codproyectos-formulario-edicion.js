/**
 * Se vuelve al listado de Codificadora de Proyectos
 */
var listenerBotonCancelar = function () {
    $(location).attr('href', 'index.php?c=codproyectos&a=view');
};

/**
 * Se envían los datos para guardar la Codificadora de Proyectos
 */
var listenerBotonGuardar = function () {
    // Primero verifico el validador
    if (formEdicionCodproyecto.valid()) {
        // Paso los valores del formulario a la codificadora
        asignarValoresCodproyecto();

        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=codproyectos&a=save",
            data: JSON.stringify(codproyecto) // envio los parametros como JSON
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null)
                    $(location).attr('href', 'index.php?c=codproyectos&a=view');
                else
                    showModal('Error', 'Se esperaba una codificadora de proyecto y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'codproyecto') con cada campo del formulario de edición
 */
function asignarValoresCodproyecto()
{
    codproyecto.id_codproyecto = ($('#f_id_codproyecto').val() != '') ? $('#f_id_codproyecto').val() : 0;
    codproyecto.descripcion_proyecto = $('#f_descripcion_proyecto').val();
    codproyecto.vigencia_desde_codproy = ($('#f_vigencia_desde_codproy').val() != '') ? $('#f_vigencia_desde_codproy').val() : null;
    codproyecto.vigencia_hasta_codproy = ($('#f_vigencia_hasta_codproy').val() != '') ? $('#f_vigencia_hasta_codproy').val() : null;
    codproyecto.habilitado_codproy = $('#f_habilitado_codproy').val();
 }

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object codproyecto Objeto Codproyecto deserializado
 */
function asignarCodproyectoAFormulario(codproyecto)
{
    $('#f_id_codproyecto').val((codproyecto.id_codproyecto == 0) ? '' : decodeEntities(codproyecto.id_codproyecto));
    $('#f_descripcion_proyecto').val(decodeEntities(codproyecto.descripcion_proyecto));
    $('#f_vigencia_desde_codproy').val(decodeEntities(codproyecto.vigencia_desde_codproy));
    $('#f_vigencia_hasta_codproy').val(decodeEntities(codproyecto.vigencia_hasta_codproy));
    $('#f_habilitado_codproy').val(codproyecto.habilitado_codproy);
}

/**
 * Variables globales
 */
var formEdicionCodproyecto; // Referencia al formulario de edición de la Codificadora de Proyectos después de aplicarle validate().

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
    $('#v_vigencia_desde_codproy').datepicker({ altField: '#f_vigencia_desde_codproy',
                                                  onSelect: function(fecha_elegida) {
                                                    // Se establece como valor mínimo de la fecha hasta 
                                                    $("#v_vigencia_hasta_codproy").datepicker( "option", "minDate", fecha_elegida);
                                                  }
                                              });
    // Se define la fecha Hasta, donde su fecha mínima es la fecha Desde
    $('#v_vigencia_hasta_codproy').datepicker({ altField: '#f_vigencia_hasta_codproy', 
                                                  minDate: $('#v_vigencia_desde_codproy').val()
                                              });
    
    // Al renderizarse el checkbox
    $('#f_habilitado_codproy').ready(function () {
        if ( $('#f_habilitado_codproy').val() == 1 ) // si está seteado en 1
            $('#f_habilitado_codproy').prop("checked", true); // se tilda
        else
            $('#f_habilitado_codproy').prop("checked", false); // se destilda
    });
    // Al modificarse el checkbox
    $('#f_habilitado_codproy').change(function () {
        if ( $('#f_habilitado_codproy').val() == 1 ) // si está seteado en 1
            $('#f_habilitado_codproy').prop("checked", true); // se tilda
        else
            $('#f_habilitado_codproy').prop("checked", false); // se destilda
    });
    // Al usar el checkbox
    $('#f_habilitado_codproy').click(function () {
        if ( $('#f_habilitado_codproy').prop('checked') ) // si se tilda
            $('#f_habilitado_codproy').val(1); // se setea en 1
        else
            $('#f_habilitado_codproy').val(0); // se setea en 0
    });

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Se utiliza el objeto codproyecto para asignar los valores en cada campo del formulario de edición
    asignarCodproyectoAFormulario(codproyecto);

    // -- Validación del formulario "form_edicion_codproyecto" ------------------
    formEdicionCodproyecto = $("#form_edicion_codproyecto");
    formEdicionCodproyecto.validate({
        rules: {
                f_descripcion_proyecto: { required: true },
                v_vigencia_desde_codproy: { regexDate: true },
                v_vigencia_hasta_codproy: { regexDate: true }
               },
        messages: {
                f_descripcion_proyecto: {
                    required: "Por favor ingrese una descripci&oacute;n."
                }
            }
    });
    
    // Sólo se permite el ingreso de valores numéricos en aquellos campos que posean la clase CSS 'solo-numero'
    $('.solo-numero').keyup(function (){
        this.value = (this.value + '').replace(/[^0-9]/g, '');
    });
});