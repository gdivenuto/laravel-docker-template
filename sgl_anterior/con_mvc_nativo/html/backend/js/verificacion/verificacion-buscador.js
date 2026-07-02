
/**
 * [listenerBotonAccion description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBotonAccion = function (event) {
    //event.preventDefault(event); // Prevengo que el link haga lo propio al hacerle click

    // Obtengo la acción
    accion = $(this).data('accion');

    f_archivo = $(this).data('archivo');
    f_url = $(this).data('url');

    if (accion == 'descargar') {
        $(location).prop('target', '_blank');
        $(location).prop('href', f_url);
    } else
        showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'.'.format(accion));
};

function generarHtmlBotonAccionDocumento(accion, descripcion, icono, fila) {
    return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-archivo="{3}" data-url="{4}"></span>&nbsp;'.format(
        accion, descripcion, icono, fila.archivo, fila.url);
}

/**
 * 16/07/2020 XXXX
 * Se visualizan las fechas de cada documento
 * @param  {[type]} full [description]
 * @return {[type]}      [description]
 */
var callbackRenderFechaDocumento = function (full) {
    return full.fecha;
}

var callbackRenderAccionesDocumento = function (full) {
    //buttonAction = generarHtmlBotonAccionDocumento('descargar', 'Descargar', 'download-alt', full);
    // fix para evitar que haga "wrap" en los controles
    //return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);

    return '<a href="'+full.url+'" target="_blank" title="Ver documento">'+full.archivo+'</a>';
};

/**
 * [listenerBotonRefrescar description]
 * @return {[type]} [description]
 */
var listenerBotonRefrescar = function () {
    // Se recarga la vista
    $(location).attr('href','index.php?c=verificardigitalizacion&a=view');
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
 * [setDataTable description]
 * @param {[type]} ajaxUrl [description]
 * @return DataTable
 */
function setDataTable(ajaxUrl) {
    // Borro y creo el listado con las fichas obtenidas
    idTabla = '#grillaDocumentos';
    idTablaContainer = '{0}Container'.format(idTabla);

    $(idTablaContainer).empty();
    $(idTablaContainer).append(generarGrillaHtml(idTabla, new Array('Fecha','Documento')));

    $(idTabla).addClass("borde_tabla_busqueda_avanzada");

    // Errores customizados para Datatables
    $.fn.dataTable.ext.errMode = 'none';

    // Se transforma el listado de documentos en un DataTable
    // settings: respuesta en bruto del DataTable
    // techNote: ...
    var tabla = $(idTabla)
        .on( 'error.dt', function (e, settings, techNote, message) {
            showModal('Aviso', 'Ha ocurrido un error: {0}'.format(message),
                { btn_cerrar: modalBtnSessionHandler(settings.jqXHR.responseJSON) });
        })
        .DataTable({
            processing: true, // para que muestre "Cargando...", en modo local no se llega a visualizar
            serverSide: true,
            ordering: false,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: ajaxUrl,
                data: function ( d ) {
                    // Agrego los parámetros de búsqueda
                    d.f_anio = $('#f_anio').val();
                    d.f_numero = $('#f_numero').val();
                    d.f_digito = $('#f_digito').val();
                }
            },
            // Dejamos solamente la 't'abla
            dom: 't',
            language: { url: '../librerias/datatables/localisation/es_AR.json' },
            columnDefs: [ { className: 'text-left', targets: '_all', searchable: false } ],
            columns: [
                { data: null, render: callbackRenderFechaDocumento, className: 'text-center', width:'60px' },
                { data: null, render: callbackRenderAccionesDocumento }
            ]
        });

    return tabla;
};

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

    dataTableRef = setDataTable('index.php?c=verificardigitalizacion&a=mostrarcontenidodirectorio');

    // Asignamos para aquellos elementos que posean la clase CSS 'btn-accion-contenido'
    // el evento click de esta forma, para que exista siempre (para las instancias actuales y las futuras)
    $(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

    $('#f_anio').focus();

    // **************** Comportamiento de botones ************************************
    $('#btn_buscar').click(function () {
        if ( formBusquedaRef.valid() )
            dataTableRef.ajax.reload();
    });

    $('#btn_refrescar').click(listenerBotonRefrescar);

    $('#btn_busqueda_simple').click(listenerBusquedaSimple);

    // Asigno enter por defecto
    defaultButtonInputOnEnter(['#f_anio', '#f_numero', '#f_digito'], '#btn_buscar');

    // Año actual
    var year = moment().year();

    // Validación del formulario
    formBusquedaRef = $("#form_busqueda");
    formBusquedaRef.validate({
        rules: {
            f_anio: { digits: true, range: [1900, year] },
            f_numero: { required: true, digits: true },
            f_digito: { required: true }
        },
        messages: {
            f_anio: {
                digits: "Por favor ingrese un A&ntilde;o v&aacute;lido.",
                range: "El a&ntilde;o debe ser un valor entre 1900 y {0}".format(year)
            },
            f_numero: {
                digits: "Por favor ingrese un N&uacute;mero v&aacute;lido.",
                required: "Debe ingresar un N&uacute;mero de expediente."
            },
            f_digito: {
                required: "Debe ingresar el D&iacute;gito del expediente."
            }
        },
        errorLabelContainer: '#msg_error_form'
    });

    // Se oculta el ítem TAREAS del menú principal
    $('#menu_item_tareas').css("display", "none");
});
