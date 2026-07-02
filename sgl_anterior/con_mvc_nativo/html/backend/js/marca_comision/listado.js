/**
 * Se habilita la clave del expediente seleccionado para modificar su Marca en la Comisión
 * @param  {[type]} fila [description]
 * @return {[type]}      [description]
 */
function habilitarClaveExpedienteComision(fila) {
	
	$('#clave_expediente'+fila).prop('disabled', false);

	$('#anio_marca_comision'+fila).css({'color': '#000', 'background-color': '#ADD8E6'});
	$('#tipo_marca_comision'+fila).css({'color': '#000', 'background-color': '#ADD8E6'});
	$('#numero_marca_comision'+fila).css({'color': '#000', 'background-color': '#ADD8E6'});
	$('#cuerpo_marca_comision'+fila).css({'color': '#000', 'background-color': '#ADD8E6'}); 
	$('#alcance_marca_comision'+fila).css({'color': '#000', 'background-color': '#ADD8E6'});
	
	$('#i_nombre_marca'+fila).css({'color': '#000', 'background-color': '#ADD8E6'});
}

/**
 * Se vuelve a la grilola de expedientes
 * @return {[type]} [description]
 */
var listenerVolver = function () {
    // Se redirecciona a la grilla con búsqueda Simple (sólo por Clave)
    $(location).attr('href','index.php?c=expedientes&a=view');
};

/**
 * Se visualiza una modal para buscar una Comisión, utilizando un autosugerido
 */
function mostrarModalComisionAutosugerido() {
    // Se muestra una modal para buscar una Comisión mediante un autosugerido
    $('#modalComisionAutosugerido').modal('show');
    // Se limpia el campo de búsqueda
    $('#modal_comision_sugerida').val('');
    // Se selecciona y se le da el foco al campo de búsqueda por autosugerido
    setfocus('#modal_comision_sugerida');
}

/**
 * Variables globales
 */
var dataTableRef; // Referencia al DataTable generado
var formBusquedaRef; // Referencia al formulario de búsqueda despues de aplicarle validate().

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
 
    // Se oculta el encabezado de la tabla, en esta Vista no se utiliza
    $('#tablaFichas thead').css('display', 'none');

    // Componentes de fecha
    // Se define la fecha Desde
    $('#v_fecha_desde').datepicker({ altField: '#f_fecha_desde',
                                     onSelect: function(fecha_elegida) {
                                        // Se establece como valor mínimo de la fecha hasta 
                                        $("#v_fecha_hasta").datepicker( "option", "minDate", fecha_elegida);
                                     }
                                   });
    // Se define la fecha Hasta, donde su fecha mínima es la fecha Desde
    $('#v_fecha_hasta').datepicker({ altField: '#f_fecha_hasta', 
                                     minDate: $('#v_fecha_desde').val()
                                   });
    // Se define la fecha del Listado, para utilizar en el cálculo de días en comisión de cada expediente
    $('#v_fecha_listado').datepicker();

    $('#modal_comision_sugerida').keypress( function(e) {
        if ( $('#modal_comision_sugerida').val() != '' && e.which == 13 )
            $('#btCargarComisionSugerida').focus();
    });

    $('#btCargarComisionSugerida').click(function() {
        if ( $('#modal_comision_sugerida').val() != '' ) {
            // Se toma la comisión sugerida y elegida
            var comision_sugerida = $('#modal_comision_sugerida').val();
            // Se separa el código y la descripción
            var aux_comision = comision_sugerida.split('-');
            // Se toma el código
            var codigo_comision = aux_comision[0];

            // Se selecciona la Comisión en el combo
            $('#f_comision').val(codigo_comision);
            // Se oculta la modal
            $('#modalComisionAutosugerido').modal('hide');
        } else
            setfocus('#modal_comision_sugerida');
    });

	// **************** Comportamiento de botones ************************************
    $('#btn_buscar').click(function () {
        // Si se cumplen las validaciones del formulario
        if ( formBusquedaRef.valid() ) {
        	// Si se eligió una Comisión
        	if ( $('#f_comision').val() != '0' ) {
            	
            	$(location).attr('href','index.php?c=marcacomision&a=mostrar&f_fecha_desde={0}&f_fecha_hasta={1}&f_fecha_listado={2}&f_comision={3}'.format(
	                $('#f_fecha_desde').val(),
	                $('#f_fecha_hasta').val(),
	                $('#f_fecha_listado').val(),
	                $('#f_comision').val()
	            ));
        	}
            else
            	showModal("Aviso", "Debe seleccionar una Comisi"+'\u00f3'+"n.");
        }
    });

    // Se vuelve a la grilla de expedientes
    $('#btn_volver').click(listenerVolver);
	
	// Al guardar las Marcas definidas
    $('#btn_guardar').click(function () {
        // Se validan los campos requeridos del form del criterio de búsqueda
        if (formBusquedaRef.valid()) {
        	$.ajax({
                type: "POST",
                url: "index.php?c=marcacomision&a=save", // El script donde se realizará la petición
                data: $('#form_marca_comision').serialize() // Se adjuntan los campos del formulario enviado
            })
            .done(function( respuesta ) {
                // Una vez guardadas las Marcas con éxito
                if (respuesta.estado == 'OK') {
                    // Se vuelve a mostrar la grilla con los cambios realizados
    				$(location).attr('href','index.php?c=marcacomision&a=mostrar&f_fecha_desde={0}&f_fecha_hasta={1}&f_fecha_listado={2}&f_comision={3}'.format(
                        respuesta.f_fecha_desde,
                        respuesta.f_fecha_hasta,
                        respuesta.f_fecha_listado,
                        respuesta.f_comision
                    ));
                } else 
                    showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
            });
        }
    });

    // Al limpiar las Marcas en la Comisión y fechas elegidas
    $('#btn_limpiar_marcas').click(function () { 
        // Se validan los campos requeridos del form del criterio de búsqueda
        if (formBusquedaRef.valid()) {
            // Se limpian las Marcas de los Expedientes en la Comisión respectiva
            $(location).attr('href','index.php?c=marcacomision&a=limpiarmarcas&f_fecha_desde={0}&f_fecha_hasta={1}&f_fecha_listado={2}&f_comision={3}'.format(
                $('#f_fecha_desde').val(),
                $('#f_fecha_hasta').val(),
                $('#f_fecha_listado').val(),
                $('#f_comision').val()
            ));
        }
    });

    // Asigno enter por defecto
    defaultButtonInputOnEnter(
        ['#v_fecha_desde',
         '#v_fecha_hasta',
         '#v_fecha_listado', 
         '#f_comision'
        ], '#btn_buscar');

    // Validación del formulario
    formBusquedaRef = $("#form_marca_comision");
    formBusquedaRef.validate({
    	rules: {
                v_fecha_desde: { 
                    required: true, 
                    regexDate: true 
                },
                v_fecha_hasta: { 
                    required: true, 
                    regexDate: true
                },
                v_fecha_listado: {
                    regexDate: true
                }
    		},
		messages: {
                v_fecha_desde: { 
                    required: 'Debe especificar una fecha Desde.'
                },
                v_fecha_hasta: { 
                    required: 'Debe especificar una fecha Hasta.'
                }
			},
        errorLabelContainer: '#msg_error_form'
    });
});