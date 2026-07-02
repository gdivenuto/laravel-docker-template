/**
 * Se vuelve al listado de Codificadora de Temas
 */
var listenerBotonCancelar = function () {
    $(location).attr('href', 'index.php?c=codtemas&a=view');
};

/**
 * Se envían los datos para guardar la Codificadora de Temas
 */
var listenerBotonGuardar = function () {
    // Primero verifico el validador
    if (formEdicionCodtema.valid()) {
        // Paso los valores del formulario a la codificadora
        asignarValoresCodtema();

        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=codtemas&a=save",
            data: JSON.stringify(codtema) // envio los parametros como JSON
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null)
                    $(location).attr('href', 'index.php?c=codtemas&a=view');
                else
                    showModal('Error', 'Se esperaba una codificadora de temas y no se recibieron resultados.');
            } else 
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'codtema') con cada campo del formulario de edición
 */
function asignarValoresCodtema()
{
    codtema.id_codtema = ($('#f_id_codtema').val() != '') ? $('#f_id_codtema').val() : 0;
    codtema.descripcion_tema = $('#f_descripcion_tema').val();
    codtema.vigencia_desde_tema = ($('#f_vigencia_desde_tema').val() != '') ? $('#f_vigencia_desde_tema').val() : null;
    codtema.vigencia_hasta_tema = ($('#f_vigencia_hasta_tema').val()) ? $('#f_vigencia_hasta_tema').val() : null;
    codtema.habilitado_tema = $('#f_habilitado_tema').val();
 }

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object codtema Objeto Codtema deserializado
 */
function asignarCodtemaAFormulario(codtema)
{
    $('#f_id_codtema').val(decodeEntities(codtema.id_codtema));
    $('#f_descripcion_tema').val(decodeEntities(codtema.descripcion_tema));
    $('#f_vigencia_desde_tema').val(decodeEntities(codtema.vigencia_desde_tema));
    $('#f_vigencia_hasta_tema').val(decodeEntities(codtema.vigencia_hasta_tema));
    $('#f_habilitado_tema').val(codtema.habilitado_tema);
}

/**
 * Variables globales
 */
var formEdicionCodtema; // Referencia al formulario de edición de la Codificadora de Temas después de aplicarle validate().

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
    $('#v_vigencia_desde_tema').datepicker({ altField: '#f_vigencia_desde_tema',
                                              onSelect: function(fecha_elegida) {
                                                    // Se establece como valor mínimo de la fecha hasta 
                                                    $("#v_vigencia_hasta_tema").datepicker( "option", "minDate", fecha_elegida);
                                              }
                                           });
    // Se define la fecha Hasta, donde su fecha mínima es la fecha Desde
    $('#v_vigencia_hasta_tema').datepicker({ altField: '#f_vigencia_hasta_tema', 
                                             minDate: $('#v_vigencia_desde_tema').val()
                                           });

    // Al renderizarse el checkbox
    $('#f_habilitado_tema').ready(function () {
        if ( $('#f_habilitado_tema').val() == 1 ) // si está seteado en 1
            $('#f_habilitado_tema').prop("checked", true); // se tilda
        else
            $('#f_habilitado_tema').prop("checked", false); // se destilda
    });
    // Al modificarse el checkbox
    $('#f_habilitado_tema').change(function () {
        if ( $('#f_habilitado_tema').val() == 1 ) // si está seteado en 1
            $('#f_habilitado_tema').prop("checked", true); // se tilda
        else
            $('#f_habilitado_tema').prop("checked", false); // se destilda
    });
    // Al usar el checkbox
    $('#f_habilitado_tema').click(function () {
        if ( $('#f_habilitado_tema').prop('checked') ) // si se tilda
            $('#f_habilitado_tema').val(1); // se setea en 1
        else
            $('#f_habilitado_tema').val(0); // se setea en 0
    });

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Se utiliza el objeto codproyecto para asignar los valores en cada campo del formulario de edición
    asignarCodtemaAFormulario(codtema);

    // -- Validación del formulario "form_edicion_codtema" ------------------
    formEdicionCodtema = $("#form_edicion_codtema");
    formEdicionCodtema.validate({
        rules: {
                f_descripcion_tema: { required: true },
                v_vigencia_desde_tema: { regexDate: true },
                v_vigencia_hasta_tema: { regexDate: true }
               },
        messages: {
                f_descripcion_tema: {
                    required: "Por favor ingrese una descripci&oacute;n."
                }
            }
    });
    
    // Sólo se permite el ingreso de valores numéricos en aquellos campos que posean la clase CSS 'solo-numero'
    $('.solo-numero').keyup(function (){
        this.value = (this.value + '').replace(/[^0-9]/g, '');
    });
});