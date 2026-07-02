
/**
 * Permite visualizar la ficha del expediente en una nueva pestaña
 * @param  {[type]} f_anio    [description]
 * @param  {[type]} f_tipo    [description]
 * @param  {[type]} f_numero  [description]
 * @param  {[type]} f_cuerpo  [description]
 * @param  {[type]} f_alcance [description]
 */
function verFicha(f_anio, f_tipo, f_numero, f_cuerpo, f_alcance) {
    var url = 'index.php?c=expedientes&a=generarpdffichaexpediente&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}'.format(
            f_anio, f_tipo, f_numero, f_cuerpo, f_alcance);
        // Se muestra el pdf en una nueva pestaña
        window.open(url);
}

/**
 * Se renderiza la ficha
 * 
 * @param  {[type]} data [description]
 * @param  {[type]} type [description]
 * @param  {[type]} full [description]
 * @param  {[type]} meta [description]
 * @return {[type]}      [description]
 */
var callbackRenderFichas = function (data, type, full, meta) {
    ficha = '';
    nombre_documento = '';
    // Si hay datos que mostrar
    if (full != null)
    {
        if (full.tipo == 'E') {
            nombre_documento  = 'Expediente';
            titulo_para_boton = 'Ir al Expediente';
        }
        if (full.tipo == 'N') {
            nombre_documento = 'Nota';
            titulo_para_boton = 'Ir a la Nota';
        }
        if (full.tipo == 'R') {
            nombre_documento = 'Recomendaci&oacute;n';
            titulo_para_boton = 'Ir a la Recomendaci&oacute;n';
        }
        ficha +=    '<div class="row">';
        ficha +=        '<div class="col-sm-6 col-md-3">';
        ficha +=            '<strong><span class="glyphicon glyphicon-file"></span>&nbsp;{0} {1} - {2} - {3} - {4} - {5}</strong>'.format(nombre_documento, full.anio, full.tipo, full.numero, full.cuerpo, full.alcance);
        ficha +=        '</div>';
                        // Botón para 'ir' al expediente respectivo
        ficha +=        '<div class="col-sm-6 col-md-3 pull-right text-right">';
        ficha +=            '<button type="button" class="btn btn-primary btn-sm" onClick="javascript:irA(\'expedientes\',{0},\'{1}\',{2},{3},{4});" title="{5}"><span class="glyphicon glyphicon-share-alt"></span>&nbsp;{5}</button>'.format(full.anio, full.tipo, full.numero, full.cuerpo, full.alcance, titulo_para_boton);
        ficha +=            '&nbsp;&nbsp;&nbsp;';
        ficha +=            '<button type="button" class="btn btn-primary btn-sm" onClick="javascript:verFicha({0},\'{1}\',{2},{3},{4});" title="Ver ficha"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Ver ficha</button>'.format(full.anio, full.tipo, full.numero, full.cuerpo, full.alcance);
        ficha +=        '</div>';
        ficha +=    '</div>';
                    // Fila para Iniciador, Carátula y la Fecha de Entrada
        ficha +=    '<div class="row">';
                        // Iniciador
        ficha +=        '<div class="col-sm-4 col-md-3"><strong>Iniciador: </strong>{0}</div>'.format(full.ro_iniciador_descripcion_grp);
                        // Carátula
                        caratula = (full.ro_caratula != null && full.ro_caratula != '') ? full.ro_caratula : 'no posee';
        ficha +=        '<div class="col-sm-4 col-md-5"><strong>Car&aacute;tula: </strong>{0}</div>'.format(caratula);
                        // Fecha de Ingreso
        ficha +=        '<div class="col-sm-4 col-md-3"><strong>Fecha Ingreso: </strong>{0}</div>'.format(formatearFechaConBarras(full.ro_fecha_entrada_expe));
        ficha +=    '</div>';

        // COLOR VERDE
        if ( full.ro_cantidad_dias_del_informe >= 0 && full.ro_cantidad_dias_del_informe <= 105 )
            css_resaltado = 'resaltado-ok';
        else {
            // COLOR AMARILLO
            if ( full.ro_cantidad_dias_del_informe >= 106 && full.ro_cantidad_dias_del_informe <= 119 )
                css_resaltado = 'resaltado-advertencia';
            else
                // COLOR ROJO
                css_resaltado = 'resaltado-alerta';
        }

        ficha += '<div class="row">';
        ficha +=     '<div class="col-sm-8 col-md-3"><strong>Detalle: </strong>'+full.detalle_informe+'</div>';
        ficha +=     '<div class="col-sm-4 col-md-3"><strong>Pedido el: </strong>'+formatearFechaConBarras(full.fecha_pedido_informe)+'&nbsp;&nbsp;&nbsp;<span class="'+css_resaltado+' resaltado_espacio">'+full.ro_cantidad_dias_del_informe+' d&iacute;as</span></div>';
        ficha += '</div>';

        // Proyectos ********************************************************
        $.each(full.proyectos._items, function(index, value) {
            contenido_extracto = (value.extracto != null) ? value.extracto : 'Sin extracto';

            ficha += '<div class="row">';
            ficha +=    '<div class="col-md-12"><strong>Proyecto N&deg; <span id="prev_proyecto_orden">{0}</span></strong>&nbsp;&nbsp;&nbsp;{1}</div>'.format(value.orden_proyecto, value.ro_descripcion_proyecto);
            ficha += '</div>';
            ficha += '<div class="row">';
            ficha +=     '<div class="col-md-1"><strong>Extracto</strong></div>';
            ficha += '</div>';
            ficha += '<div class="row">';
            ficha +=     '<div class="col-md-12">';
            ficha +=         '<textarea style="width:100%;" class="form-control" readonly>'+contenido_extracto+'</textarea>';
            ficha +=    '</div>';
            ficha += '</div>';
        });
        
        fecha_comision = formatearFechaConBarras(full.ro_fecha_comision);

        ficha += '<div class="row">';
        ficha +=     '<div class="col-sm-8 col-md-6"><strong>Comisi&oacute;n: </strong>{0} - {1}</div>'.format(full.ro_codigo_comision, full.ro_nombre_comision);
        ficha +=     '<div class="col-sm-4 col-md-3"><strong>Fecha de Comisi&oacute;n: </strong>'+fecha_comision+'</div>';
        ficha += '</div>';
    }
    return ficha;
};

/**
 * [listenerBotonRefrescar description]
 * @return {[type]} [description]
 */
var listenerBotonRefrescar = function () {
    // Se redirecciona al mismo listado de informes
    $(location).attr('href','index.php?c=listadoinformes&a=view&f_restablecer=1');
};

/**
 * Se vuelve a la grilola de expedientes
 * @return {[type]} [description]
 */
var listenerVolver = function () {
    // Se redirecciona a la grilla con búsqueda Simple (sólo por Clave)
    $(location).attr('href','index.php?c=expedientes&a=view');
};

/**
 * [setDataTable description]
 * @param {[type]} ajaxUrl [description]
 * @return DataTable
 */
function setDataTable(ajaxUrl) {
    var dom_config;

	// Borro y creo el listado con las fichas obtenidas
	idTabla = '#tablaFichas';

	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(
		generarGrillaHtml(idTabla, null, true) // sin encabezado
	);
	$(idTabla).addClass("borde_tabla_busqueda_avanzada");
	
    // Errores customizados para Datatables
    $.fn.dataTable.ext.errMode = 'none';

    // Se transforma el listado en un DataTable
    // settings: respuesta en bruto del DataTable
    // techNote: ...
	var tabla = $(idTabla)
        .on( 'error.dt', function (e, settings, techNote, message) {
                showModal('Aviso', 'Ha ocurrido un error: {0}'.format(message),
                    { btn_cerrar: modalBtnSessionHandler(settings.jqXHR.responseJSON) });
            })
        .DataTable({
    		processing: true,
    		serverSide: true,
    		ajax: {
                url: ajaxUrl,
                data: function ( d ) {
                    // Agrego los parámetros de búsqueda
                    d.f_fecha_desde   = $('#f_fecha_desde').val();
                    d.f_fecha_hasta   = $('#f_fecha_hasta').val();
                    d.f_fecha_listado = $('#f_fecha_listado').val();
                    d.f_comision      = $('#f_comision').val();
                    d.f_vencidos      = $('#f_vencidos').val();
                }
            },
            // Dejamos solamente la 't'abla y el 'p'aginador (éste último si NO se buscan sólo los Vencidos)
            dom: 'ptp',
            ordering: false,
    		responsive: true,
    		autoWidth: false,
    		// Cantidad de registros por página
            pageLength: 10,
            // Traducción al español
    		language: { url: '../librerias/datatables/localisation/es_AR.json' }, 
    		columnDefs: [
    			{ targets: '_all', searchable: false }
            ],
    		columns: [ 
    			{ data: null, render: callbackRenderFichas }
            ],
            drawCallback: function(opciones){
                var tableinfo = dataTableRef.page.info();
                // Si hay registros que mostrar
                if (tableinfo.recordsTotal > 0) {
                    // Se habilitan los botones para Imprimir y Exportar a Documento de Texto
                    $('#btn_imprimir').prop('disabled', false);
                    $('#btn_exportar_texto').prop('disabled', false);
                }
                else // Si NO hay para mostrar
                {
                    // Se deshabilitan los botones para Imprimir y Exportar a Documento de Texto
                    $('#btn_imprimir').prop('disabled', true);
                    $('#btn_exportar_texto').prop('disabled', true);
                }
            }
    	});

	return tabla;
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
    
    // Si existe el DataTable y posee registros
    if ( typeof dataTableRef !== 'undefined' && dataTableRef.page.info().recordsTotal > 0 ) {
        // Se habilitan los botones para Imprimir y Exportar a Documento de Texto
        $('#btn_imprimir').prop('disabled', false);
        $('#btn_exportar_texto').prop('disabled', false);
    } else {
        // Se deshabilitan los botones para Imprimir y Exportar a Documento de Texto
        $('#btn_imprimir').prop('disabled', true);
        $('#btn_exportar_texto').prop('disabled', true);
    }
    
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
    $('#v_fecha_listado').datepicker({ altField: '#f_fecha_listado'});

    $('#f_vencidos').click(function () {
        if ( $('#f_vencidos').prop('checked') )
            $('#f_vencidos').val(1);
        else
            $('#f_vencidos').val(0);
    });
    // **************** Comportamiento de botones ************************************
    $('#btn_buscar').click(function () {
        // Si se cumplen las validaciones del formulario
        if (formBusquedaRef.valid()) {
            // Si no se eligió una Comisión
            if ( $('#f_comision').val() == 0 )
                showModal('Aviso', "Debe elegir una Comisi"+o_acentuada+"n!");
            else {
                dataTableRef = setDataTable('index.php?c=listadoinformes&a=informesdatagrid');
                dataTableRef.ajax.reload();
            }
        }
    });
    // Se limpia el criterio de búsqueda
    $('#btn_refrescar').click(listenerBotonRefrescar);
	
    $('#btn_imprimir').click(function () { 
        // Se validan los campos requeridos del form del criterio de búsqueda
        if (formBusquedaRef.valid()) {
            // Se genera el PDF del reporte de Informes
            var url = 'index.php?c=listadoinformes&a=generarpdfinformes&f_fecha_desde={0}&f_fecha_hasta={1}&f_fecha_listado={2}&f_comision={3}&f_vencidos={4}'.format(
                $('#f_fecha_desde').val(),
                $('#f_fecha_hasta').val(),
                $('#f_fecha_listado').val(),
                $('#f_comision').val(),
                $('#f_vencidos').val()
            );
            // Se muestra el pdf en una nueva pestaña
            window.open(url);
        }
    });

    $('#btn_exportar_texto').click(function () { 
        // Se validan los campos requeridos del form del criterio de búsqueda
        if (formBusquedaRef.valid()) {
            // Se genera el PDF del reporte de Informes
            $(location).attr('href','index.php?c=listadoinformes&a=generardocumentotextoinformes&f_fecha_desde={0}&f_fecha_hasta={1}&f_fecha_listado={2}&f_comision={3}&f_vencidos={4}'.format(
                $('#f_fecha_desde').val(),
                $('#f_fecha_hasta').val(),
                $('#f_fecha_listado').val(),
                $('#f_comision').val(),
                $('#f_vencidos').val()));
        }
    });

    // Se vuelve a la grilla de expedientes
    $('#btn_volver').click(listenerVolver);
    
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

    // Asigno enter por defecto
    defaultButtonInputOnEnter(
        ['#v_fecha_desde',
         '#v_fecha_hasta',
         '#v_fecha_listado', 
         '#f_comision' 
        ], '#btn_buscar');

    // Validación del formulario
    formBusquedaRef = $("#form_listado_informes");
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
                    required: true, 
                    regexDate: true
                }
    		},
		messages: {
                v_fecha_desde: { 
                    required: 'Debe especificar una fecha Desde.'
                },
                v_fecha_hasta: { 
                    required: 'Debe especificar una fecha Hasta.'
                },
                v_fecha_listado: { 
                    required: 'Debe especificar una fecha Informe.'
                }
			}
    });
    
    // Se oculta el ítem TAREAS del menú principal
    $('#menu_item_tareas').css("display", "none");
});