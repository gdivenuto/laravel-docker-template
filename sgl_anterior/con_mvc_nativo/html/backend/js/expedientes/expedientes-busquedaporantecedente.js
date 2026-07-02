
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
        ficha +=        '<div class="col-md-3">';
        ficha +=            '<strong><span class="glyphicon glyphicon-file"></span>&nbsp;{0} {1} - {2} - {3} - {4} - {5}</strong>'.format(nombre_documento, full.anio, full.tipo, full.numero, full.cuerpo, full.alcance);
        ficha +=        '</div>';
                        // Botón para "Ir al expediente" respectivo y "Ver ficha"
        ficha +=        '<div class="col-md-3 pull-right">';
        ficha +=            '<button type="button" class="btn btn-primary btn-sm" onClick="javascript:irA(\'expedientes\',{0},\'{1}\',{2},{3},{4});" title="{5}"><span class="glyphicon glyphicon-share-alt"></span>&nbsp;{5}</button>'.format(full.anio, full.tipo, full.numero, full.cuerpo, full.alcance, titulo_para_boton);
        ficha +=            '&nbsp;&nbsp;&nbsp;';
        ficha +=            '<button type="button" class="btn btn-primary btn-sm" onClick="javascript:verFicha({0},\'{1}\',{2},{3},{4});" title="Ver ficha"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Ver ficha</button>'.format(full.anio, full.tipo, full.numero, full.cuerpo, full.alcance);
        ficha +=        '</div>';
        ficha +=    '</div>';
                    // Fila para la Clave (botón), Iniciador, Carátula y la Fecha de Entrada
        ficha +=    '<div class="row">';
                        // Iniciador
        ficha +=        '<div class="col-md-3"><strong>Iniciador: </strong>{0}</div>'.format(full.ro_iniciador_descripcion_grp);
                        // Carátula
                        caratula = (full.caratula != null && full.caratula != '') ? full.caratula : 'no posee';
        ficha +=        '<div class="col-md-3"><strong>Car&aacute;tula: </strong>{0}</div>'.format(caratula);
                        // Fecha de Ingreso
        ficha +=        '<div class="col-md-3"><strong>Fecha Ingreso: </strong>{0}</div>'.format(formatearFechaConBarras(full.fecha_entrada_expe));
        ficha +=    '</div>';

                    // Categoría, Temas y Autores **************************************
        ficha +=    '<div class="row">';
                        // Categoría
        ficha +=        '<div class="col-md-3"><strong>Categor&iacute;a: </strong>{0}</div>'.format(full.ro_descripcion_categoria);
                       // Temas ********************************************************
        ficha +=        '<div class="col-md-3">';
        ficha +=            '<strong>Tema: </strong>';
                        if (full.temas._items.length > 1) {
        ficha +=            '<select class="form-control">';
                                $.each(full.temas._items, function(index, value) {
                                    ficha += '<option>'+value.ro_descripcion_tema+'</option>';
                                });
        ficha +=            '</select>';
                        } else {
        ficha +=            full.temas._items[0]['ro_descripcion_tema'];
                        }
        ficha +=        '</div>';
                       // Autores ********************************************************
        ficha +=        '<div class="col-md-3">';
        ficha +=            '<strong>Autor: </strong>';
                        if (full.autores._items.length > 1) {
        ficha +=            '<select class="form-control">';
                                $.each(full.autores._items, function(index, value) {
                                    ficha += '<option>'+value.ro_descripcion_grp+'</option>';
                                });
        ficha +=            '</select>';
                        } else {
        ficha +=            full.autores._items[0]['ro_descripcion_grp'];
                        }
        ficha +=        '</div>';
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
        // Último Estado ********************************************************
        var estado_actual = null;

        if (full.estados._items.length > 0){
            estado_actual = full.estados._items[full.estados._items.length-1];

            fecha_estado = formatearFechaConBarras(estado_actual.fecha_estado);

            ficha += '<div class="row">';
            ficha +=     '<div class="col-md-6"><strong>Estado: </strong>'+estado_actual.id_codestado+' - '+estado_actual.ro_nombre_estado+'</div>';
            ficha +=     '<div class="col-md-3"><strong>Fecha de Estado: </strong>'+fecha_estado+'</div>';
            ficha += '</div>';

            $('#prev_estado').html("{0} - {1} ({2})".format(estado_actual.id_codestado, estado_actual.ro_nombre_estado, fecha_estado));
        }
        // Última Comisión ********************************************************
        // Si posee un Estado
        if (estado_actual != null){
            // Si este estado requiere tratamiento en comision, la buscamos
            if(estado_actual.ro_tratamiento_comision == 1){
                // Se muestra la última Comisión
                if (full.giros._items.length > 0){
                    comision_actual = full.giros._items[full.giros._items.length-1];
                    
                    fecha_comision = formatearFechaConBarras(comision_actual.fecha_entrada_giro);

                    ficha += '<div class="row">';
                    ficha +=     '<div class="col-md-6"><strong>Comisi&oacute;n: </strong>{0} - {1}</div>'.format(comision_actual.comision_codigo, comision_actual.ro_descripcion_grp);
                    ficha +=     '<div class="col-md-3"><strong>Fecha de Comisi&oacute;n: </strong>'+fecha_comision+'</div>';
                    ficha += '</div>';
                } 
            }
        }
        // Antecedentes ********************************************************
        if (full.antecedentes._items.length > 0){
            antecedente = full.antecedentes._items[0];

            ficha += '<div class="row">';
            ficha +=     '<div class="col-md-12"><strong>Antecedente: </strong>{0} - {1} - {2} - {3} - {4} - {5}</div>'.format(antecedente.tipo_a, antecedente.numero_a, antecedente.digito_a, antecedente.anio_a, antecedente.cuerpo_a, antecedente.alcance_a);
            ficha += '</div>';
        }
    }
    return ficha;
};

/**
 * [listenerBotonRefrescar description]
 * @return {[type]} [description]
 */
var listenerBotonRefrescar = function () {
    // Se redirecciona a la grilla con búsqueda Por Antecedente
    $(location).attr('href','index.php?c=expedientesbusquedaantecedente&a=view');
};

/**
 * [listenerBusquedaSimple description]
 * @return {[type]} [description]
 */
var listenerBusquedaSimple = function () {
    // Se redirecciona a la grilla con búsqueda Simple (sólo por Clave)
    $(location).attr('href','index.php?c=expedientes&a=view');
};

/**
 * [listenerBusquedaAvanzada description]
 * @return {[type]} [description]
 */
var listenerBusquedaAvanzada = function () {
    // Se redirecciona a la grilla con búsqueda Avanzada
    $(location).attr('href','index.php?c=expedientesbusquedaavanzada&a=view');
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
                    d.f_numero = $('#f_numero').val();
                    d.f_anio = $('#f_anio').val();
                }
            },
            // Dejo solamente la 't'abla y el 'p'aginador
            dom: 'ptp',
            ordering: false,
            responsive: true,
            autoWidth: false,
            // Cantidad de registros por página
            pageLength: 4,
            language: { url: '../librerias/datatables/localisation/es_AR.json' },
            columnDefs: [
                { targets: '_all', className: 'text-left', searchable: false}],
            columns: [ 
                {data: null, render: callbackRenderFichas}
            ],
            drawCallback: function(opciones){
                var tableinfo = dataTableRef.page.info();
                // Si hay registros que mostrar
                if (tableinfo.recordsTotal > 0)
                    // Se habilitan los botones para Imprimir y Exportar a Documento de Texto
                    $('#btn_imprimir').prop('disabled', false);
                else // Si NO hay para mostrar
                    // Se deshabilitan los botones para Imprimir y Exportar a Documento de Texto
                    $('#btn_imprimir').prop('disabled', true);
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

    // Si existe el DataTable y posee registros
    if ( typeof dataTableRef !== 'undefined' && dataTableRef.page.info().recordsTotal > 0 ) {
        // Se habilitan los botones para Imprimir y Exportar a Documento de Texto
        $('#btn_imprimir').prop('disabled', false);
    } else {
        // Se deshabilitan los botones para Imprimir y Exportar a Documento de Texto
        $('#btn_imprimir').prop('disabled', true);
    }
    
    // Se oculta el encabezado de la tabla, en esta Vista no se utiliza
    $('#tablaFichas thead').css('display', 'none');

    $('#f_numero').focus();

    // **************** Comportamiento de botones ************************************
    $('#btn_buscar_por_antecedente').click(function () {
        if (formBusquedaRef.valid()) {
            dataTableRef = setDataTable('index.php?c=expedientesbusquedaantecedente&a=busquedaporantecedentedatagrid');
            dataTableRef.ajax.reload();
        }
    });
    
    $('#btn_refrescar').click(listenerBotonRefrescar);

    $('#btn_imprimir').click(function () { 
        // Se validan los campos requeridos del form del criterio de búsqueda
        if (formBusquedaRef.valid())
            // se inicia la generación del reporte de búsqueda por antecedente
            var url = 'index.php?c=reportes&a=busquedaporantecedente&f_numero={0}&f_anio={1}'.format(
                $('#f_numero').val(), 
                $('#f_anio').val()
            );
            // Se muestra el pdf en una nueva pestaña
            window.open(url);
    });

    $('#btn_busqueda_simple').click(listenerBusquedaSimple);
    $('#btn_busqueda_avanzada').click(listenerBusquedaAvanzada);

    // Asigno enter por defecto
    defaultButtonInputOnEnter(['#f_numero', '#f_anio'], '#btn_buscar_por_antecedente');

    // Año actual
    var year = moment().year();

    // Validación del formulario
    formBusquedaRef = $("#form_busqueda_por_antecedente");
    formBusquedaRef.validate({
        rules: {
            f_anio: { digits: true, range: [1900, year] },
            f_numero: { required: true, digits: true }
        },
        messages: {
            f_anio: {
                digits: "Por favor ingrese un a&ntilde;o v&aacute;lido.",
                range: "El a&ntilde;o debe ser un valor entre 1900 y {0}".format(year)
            },
            f_numero: { 
                digits: "Por favor ingrese un n&uacute;mero v&aacute;lido.",
                required: "Debe ingresar un N&uacute;mero de expediente."
            }
        },
        errorLabelContainer: '#msg_error_form'
    });

    // Se oculta el ítem TAREAS del menú principal
    $('#menu_item_tareas').css("display", "none");
});