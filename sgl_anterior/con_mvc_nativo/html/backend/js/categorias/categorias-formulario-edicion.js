
// 07/01/2022 XXXX: se retira el campo codigo_categoria

/**
 * Se vuelve al listado de Categorías
 */
var listenerBotonCancelar = function () {
    $(location).attr('href', 'index.php?c=categorias&a=view');
};

/**
 * Se envían los datos para guardar la Categoría
 */
var listenerBotonGuardar = function () {
    // Primero verifico el validador
    if (formEdicionCategoria.valid()) {

        // Paso los valores del formulario a la sancion
        asignarValoresCategoria();

        // Peticion asíncrona
        $.ajax({
            method: "POST",
            contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
            url: "index.php?c=categorias&a=save",
            data: JSON.stringify(codcategoria) // envio los parametros como JSON
        })
        .done(function( respuesta ) {
            if (respuesta.estado == 'OK') {
                if (respuesta.data != null)
                    $(location).attr('href', 'index.php?c=categorias&a=view');
                else
                    showModal('Error', 'Se esperaba una categor&iacute;a y no se recibieron resultados.');
            } else
                showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
        });
    }
};

/**
 * Se cargan los atributos (propiedades del objeto 'codcategoria') con cada campo del formulario de edición
 */
function asignarValoresCategoria()
{
    codcategoria.id_codcategoria = ($('#f_id_codcategoria').val() != '') ? $('#f_id_codcategoria').val() : 0;
    codcategoria.descripcion_categoria = $('#f_descripcion_categoria').val();
    codcategoria.vigencia_desde_categoria = ($('#f_vigencia_desde_categoria').val() != '') ? $('#f_vigencia_desde_categoria').val(): null;
    codcategoria.vigencia_hasta_categoria = ($('#f_vigencia_hasta_categoria').val() != '') ? $('#f_vigencia_hasta_categoria').val(): null;
    codcategoria.habilitado_categoria = $('#f_habilitado_categoria').val();
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object codcategoria Objeto Codcategoria deserializado
 */
function asignarCategoriaAFormulario(codcategoria)
{
    $('#f_id_codcategoria').val((codcategoria.id_codcategoria == 0) ? '' : decodeEntities(codcategoria.id_codcategoria));
    $('#f_descripcion_categoria').val(decodeEntities(codcategoria.descripcion_categoria));
    $('#f_vigencia_desde_categoria').val(decodeEntities(codcategoria.vigencia_desde_categoria));
    $('#f_vigencia_hasta_categoria').val(decodeEntities(codcategoria.vigencia_hasta_categoria));
    $('#f_habilitado_categoria').val(codcategoria.habilitado_categoria);
}

/**
 * Variables globales
 */
var formEdicionCategoria; // Referencia al formulario de edición de la Categoría después de aplicarle validate().

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
    $('#v_vigencia_desde_categoria').datepicker({ altField: '#f_vigencia_desde_categoria',
                                                  onSelect: function(fecha_elegida) {
                                                    // Se establece como valor mínimo de la fecha hasta
                                                    $("#v_vigencia_hasta_categoria").datepicker( "option", "minDate", fecha_elegida);
                                                  }
                                                });
    // Se define la fecha Hasta, donde su fecha mínima es la fecha Desde
    $('#v_vigencia_hasta_categoria').datepicker({ altField: '#f_vigencia_hasta_categoria',
                                                  minDate: $('#v_vigencia_desde_categoria').val()
                                                });

    // Al renderizarse el checkbox
    $('#f_habilitado_categoria').ready(function () {
        if ( $('#f_habilitado_categoria').val() == 1 ) // si está seteado en 1
            $('#f_habilitado_categoria').prop("checked", true); // se tilda
        else
            $('#f_habilitado_categoria').prop("checked", false); // se destilda
    });
    // Al modificarse el checkbox
    $('#f_habilitado_categoria').change(function () {
        if ( $('#f_habilitado_categoria').val() == 1 ) // si está seteado en 1
            $('#f_habilitado_categoria').prop("checked", true); // se tilda
        else
            $('#f_habilitado_categoria').prop("checked", false); // se destilda
    });
    // Al usar el checkbox
    $('#f_habilitado_categoria').click(function () {
        if ( $('#f_habilitado_categoria').prop('checked') ) // si se tilda
            $('#f_habilitado_categoria').val(1); // se setea en 1
        else
            $('#f_habilitado_categoria').val(0); // se setea en 0
    });

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // Se utiliza el objeto codcategoria para asignar los valores en cada campo del formulario de edición
    asignarCategoriaAFormulario(codcategoria);

    // -- Validación del formulario "form_edicion_categoria" ------------------
    formEdicionCategoria = $("#form_edicion_categoria");
    formEdicionCategoria.validate({
        rules: {
                f_descripcion_categoria: { required: true },
                v_vigencia_desde_categoria: { regexDate: true },
                v_vigencia_hasta_categoria: { regexDate: true }
               },
        messages: {
                f_descripcion_categoria: {
                    required: "Por favor ingrese una descripci&oacute;n."
                }
            }
    });

    // Sólo se permite el ingreso de valores numéricos en aquellos campos que posean la clase CSS 'solo-numero'
    $('.solo-numero').keyup(function (){
        this.value = (this.value + '').replace(/[^0-9]/g, '');
    });
});
