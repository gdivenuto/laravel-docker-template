
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
    if (full != null && full.estado_digitalizacion === 'DSC' )
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
                        caratula = (full.caratula != null && full.caratula != '') ? full.caratula : 'no posee';
        ficha +=        '<div class="col-sm-4 col-md-3"><strong>Car&aacute;tula: </strong>{0}</div>'.format(caratula);
                        // Fecha de Ingreso
        ficha +=        '<div class="col-sm-4 col-md-3"><strong>Fecha Ingreso: </strong>{0}</div>'.format(formatearFechaConBarras(full.fecha_entrada_expe));
        ficha +=    '</div>';

                    // Categoría **************************************
        ficha +=    '<div class="row">';
                        // Categoría
        ficha +=        '<div class="col-sm-12 col-md-3"><strong>Categor&iacute;a: </strong>{0}</div>'.format(full.ro_descripcion_categoria);
        ficha +=    '</div>';

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
    }
    return ficha;
};

/**
 * [listenerBotonRefrescar description]
 * @return {[type]} [description]
 */
var listenerBotonRefrescar = function () {
    // Se lista sin utilizar el criterio de búsqueda
    $(location).attr('href','index.php?c=listadoexpedientessindigitalizar&a=view&f_restablecer=1');
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

    // transformo el listado en un DataTable
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
                    d.f_fecha_desde = $('#f_fecha_desde').val();
                    d.f_fecha_hasta = $('#f_fecha_hasta').val();
                }
            },
            // Dejamos solamente la 't'abla y el 'p'aginador
            dom: 't',
            ordering: false,
            responsive: true,
            autoWidth: false,
            // Cantidad de registros por página
            pageLength: 10,
            language: { url: '../librerias/datatables/localisation/es_AR.json' }, 
            columnDefs: [
                { targets: '_all', searchable: false }
            ],
            columns: [ 
                { data: null, render: callbackRenderFichas }
            ],
            drawCallback: function(opciones){
                var tableinfo = dataTableRef.page.info();
                $('#lb_cantidad_resultados').html(tableinfo.recordsTotal);
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

    // *** Componentes de fecha ***
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

    // **************** Comportamiento de botones ************************************
    $('#btn_buscar').click(function () {
        // Si se cumplen las validaciones del formulario
        if (formBusquedaRef.valid()) {
            dataTableRef = setDataTable('index.php?c=listadoexpedientessindigitalizar&a=expedientessindigitalizardatagrid');
            dataTableRef.ajax.reload();
        }
    });
    // Se limpia el criterio de búsqueda
    $('#btn_refrescar').click(listenerBotonRefrescar);
    
    $('#btn_imprimir').click(function () { 
        // Se validan los campos requeridos del form del criterio de búsqueda
        if (formBusquedaRef.valid()) {
            // Se genera el PDF del reporte de Expedientes sin documento cargado
            var url = 'index.php?c=listadoexpedientessindigitalizar&a=generarpdfsindigitalizar&f_fecha_desde={0}&f_fecha_hasta={1}'.format(
                $('#f_fecha_desde').val(),
                $('#f_fecha_hasta').val()
            );
            // Se muestra el pdf en una nueva pestaña
            window.open(url);
        }
    });

    $('#btn_exportar_texto').click(function () { 
        // Se validan los campos requeridos del form del criterio de búsqueda
        if (formBusquedaRef.valid()) {
            // Se genera el documento de texto del reporte de Expedientes sin documento cargado
            $(location).attr('href','index.php?c=listadoexpedientessindigitalizar&a=generardocumentotextosindigitalizar&f_fecha_desde={0}&f_fecha_hasta={1}'.format(
                $('#f_fecha_desde').val(),
                $('#f_fecha_hasta').val())
            );
        }
    });
    
    // Se vuelve a la grilla de expedientes
    $('#btn_volver').click(listenerVolver);
    
    // Asigno enter por defecto
    defaultButtonInputOnEnter(
        ['#v_fecha_desde',
         '#v_fecha_hasta'
        ], '#btn_imprimir');

    // Validación del formulario
    formBusquedaRef = $("#form_sin_digitalizar");
    formBusquedaRef.validate({
    	rules: {
                v_fecha_desde: { 
                    required: true, 
                    regexDate: true 
                },
                v_fecha_hasta: { 
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
                }
			}
    });
    
    // Se oculta el ítem TAREAS del menú principal
    $('#menu_item_tareas').css("display", "none");
});