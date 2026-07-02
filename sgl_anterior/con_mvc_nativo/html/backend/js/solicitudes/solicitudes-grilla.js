/**
 * Se generan los botones respectivos
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-anio="{3}" data-tipo="{4}" data-numero="{5}" data-cuerpo="{6}" data-alcance="{7}" data-digito="{8}" data-cuerpoalcance="{9}" data-anexoalcance="{10}" data-cuerpoanexoalcance="{11}" data-anexo="{12}" data-cuerpoanexo="{13}" data-fecha_solicitud_hcd="{14}"></span>&nbsp;&nbsp;'.format(
		accion, descripcion, icono, 
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance, 
		fila.digito, fila.cuerpoalcance, fila.anexoalcance, 
		fila.cuerpoanexoalcance, fila.anexo, fila.cuerpoanexo,
		fila.fecha_solicitud_hcd);
}

/**
 * Se renderizan los enlaces para Editar y Eliminar
 */
var callbackRenderAcciones = function (data, type, full, meta) {
	buttonAction = '';
    buttonAction += generarHtmlBotonAccion('edit', 'Editar Solicitud', 'pencil', full);

    // Se permite Eliminar si el estado es: Devuelto al E.E. ó Anulado
    if (full.estado == 'DEE' || full.estado == 'AEE')	//full.estado == 'SHCD' || full.estado == 'SEE' || 
    	buttonAction += generarHtmlBotonAccion('delete', 'Eliminar Solicitud', 'trash', full);
    
	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

/**
 * Se renderiza el identificador del expediente a prestar
 */
var callbackRenderMostrarClaveExpediente = function (data, type, full, meta) {
	// Clave del expediente solicitado para préstamo
	var identificador = full.anio+'-'+full.tipo+'-'+full.numero+'-'+full.cuerpo+'-'+full.alcance+'-'+full.digito+'-'+full.cuerpoalcance+'-'+full.anexoalcance+'-'+full.cuerpoanexoalcance+'-'+full.anexo+'-'+full.cuerpoanexo;
	// Enlace para ver los Préstamos asociados a dicha Solicitud
	var btnVerPrestamos = '&nbsp;<a href="index.php?c=prestamos&a=viewgeneral&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}" title="Ver Pr&eacute;stamos"><span class="glyphicon glyphicon-list-alt"></span></a>'.format(full.anio, full.tipo, full.numero, full.cuerpo, full.alcance, full.digito);
	
	return identificador+btnVerPrestamos;
};

/**
 * Se renderiza el botón para SOLICITAR el pedido al HCD
 */
var callbackRenderAccionFechaSolicitudHCD = function (data, type, full, meta) {
	// Por defecto
	var valor = '---';
	// Si ya posee la fecha de Solicitud al HCD
	if (full.fecha_solicitud_hcd != null)
		// Se formatea la fecha para mostrarla, retirando la parte de la hora
		valor = formatearFechaConBarras(full.fecha_solicitud_hcd.substr(0,10));
	
	return valor;
};

/**
 * Se renderiza el botón para SOLICITAR el pedido al Ente Externo
 */
var callbackRenderAccionFechaSolicitudEE = function (data, type, full, meta) {
	// Por defecto
	var valor = '---';
	// Si ya posee la fecha de Solicitud al E.E.
	if (full.fecha_solicitud_ee != null)
		// Se formatea la fecha para mostrarla, retirando la parte de la hora
		valor = formatearFechaConBarras(full.fecha_solicitud_ee.substr(0,10));
	// Si su estado siguiente es Solicitado al E.E.
	else if ($.inArray('SEE', full.ro_estados_siguientes) >= 0) {
		// Se arma el botón para SOLICITAR al E.E.
		valor = '<a href="index.php?c=solicitudes&a=editsolicitadoee&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud_hcd={11}" class="btn btn-warning btn-sm" role="button"><span class="glyphicon glyphicon-edit"></span>&nbsp;Solicitar al E.E.</a>'.format(
			full.anio, full.tipo, full.numero, full.cuerpo, full.alcance, 
			full.digito, full.cuerpoalcance, full.anexoalcance, 
			full.cuerpoanexoalcance, full.anexo, full.cuerpoanexo,
			full.fecha_solicitud_hcd
		);
	}

	return valor;
};

/**
 * Se renderiza el botón para INGRESAR la solicitud al HCD
 */
var callbackRenderAccionFechaIngresadoEE = function (data, type, full, meta) {
	// Por defecto
	var valor = '---';
	// Si ya posee la fecha de Ingreso
	if (full.fecha_ingresado_ee != null)
		// Se formatea la fecha para mostrarla, retirando la parte de la hora
		valor = formatearFechaConBarras(full.fecha_ingresado_ee.substr(0,10));
	// Si su estado siguiente es Ingresado del E.E.
	else if ($.inArray('IEE', full.ro_estados_siguientes) >= 0) {
		// Se arma el botón para INGRESAR la solicitud al HCD
		valor = '<a href="index.php?c=solicitudes&a=editingresado&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud_hcd={11}" class="btn btn-primary btn-sm" role="button"><span class="glyphicon glyphicon-edit"></span>&nbsp;Ingresar al HCD</a>'.format(
			full.anio, full.tipo, full.numero, full.cuerpo, full.alcance, 
			full.digito, full.cuerpoalcance, full.anexoalcance, 
			full.cuerpoanexoalcance, full.anexo, full.cuerpoanexo,
			full.fecha_solicitud_hcd
		);
	}

	return valor;
};
/**
 * Se renderiza el botón para DEVOLVER la solicitud al Ente Externo
 */
var callbackRenderAccionFechaDevolucionEE = function (data, type, full, meta) {
	// Por defecto
	var valor = '---';
	var fecha_aux;

	// Si ya posee la fecha de Devuelto
	if (full.fecha_devuelto_ee != null)
		// Se formatea la fecha para mostrarla, retirando la parte de la hora
		valor = formatearFechaConBarras(full.fecha_devuelto_ee.substr(0,10));
	// Si su estado siguiente es Devuelto
	else if ($.inArray('DEE', full.ro_estados_siguientes) >= 0) {
		// Si existe por lo menos un Préstamo pendiente (Solicitado y NO Prestado aún)
        if (full.ro_existe_prestamo_pendiente) {
        	// Se arma el botón para DEVOLVER la Solicitud al Ente Externo
        	// Previamente se informa al usuario 
        	// la existencia de un Préstamo PENDIENTE (Solicitado y NO Prestado aún)
        	valor  = '<a href="javascript:informarPrestamoPendiente(';
			valor += full.anio+',';
			valor += '\''+full.tipo+'\',';
			valor += full.numero+',';
			valor += full.cuerpo+',';
			valor += full.alcance+','; 
			valor += '\''+full.digito+'\',';
			valor += full.cuerpoalcance+',';
			valor += full.anexoalcance+','; 
			valor += full.cuerpoanexoalcance+',';
			valor += full.anexo+',';
			valor += full.cuerpoanexo+',';
			valor += '\''+full.fecha_solicitud_hcd+'\'';
			valor += ');" class="btn btn-success btn-sm" role="button"><span class="glyphicon glyphicon-edit"></span>&nbsp;Devolver al E.E.</a>';
        } else {
        	// Se arma el botón para DEVOLVER la Solicitud al Ente Externo
			valor = '<a href="index.php?c=solicitudes&a=editdevueltoee&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud_hcd={11}" class="btn btn-success btn-sm" role="button"><span class="glyphicon glyphicon-edit"></span>&nbsp;Devolver al E.E.</a>'.format(
				full.anio, full.tipo, full.numero, full.cuerpo, full.alcance, 
				full.digito, full.cuerpoalcance, full.anexoalcance, 
				full.cuerpoanexoalcance, full.anexo, full.cuerpoanexo, 
				full.fecha_solicitud_hcd
			);
		}
	}

	return valor;
};

/**
 * Se informa al usuario la existencia de un Préstamo PENDIENTE (Solicitado y NO Prestado aún)
 * @param  {[type]} panio                [description]
 * @param  {[type]} ptipo                [description]
 * @param  {[type]} pnumero              [description]
 * @param  {[type]} pcuerpo              [description]
 * @param  {[type]} palcance             [description]
 * @param  {[type]} pdigito              [description]
 * @param  {[type]} pcuerpoalcance       [description]
 * @param  {[type]} panexoalcance        [description]
 * @param  {[type]} pcuerpoanexoalcance  [description]
 * @param  {[type]} panexo               [description]
 * @param  {[type]} pcuerpoanexo         [description]
 * @param  {[type]} pfecha_solicitud_hcd [description]
 */
function informarPrestamoPendiente( panio, ptipo, pnumero, pcuerpo, palcance, 
									pdigito, pcuerpoalcance, panexoalcance, 
									pcuerpoanexoalcance, panexo, pcuerpoanexo,
									pfecha_solicitud_hcd) {
	
	showModal('Atenci&oacute;n', "Existen Pr&eacute;stamos pendientes \n¿Desea realizar la devoluci&oacute;n? Se generar&aacute; una nueva Solicitud para los pedidos del expediente {0}-{1}-{2}".format(panio, ptipo, pnumero),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Se redirecciona al controlador para editar la Devolución al Ente Externo
				// con el parámetro f_generar_nueva_solicitud en 1
				// para que una vez guardada la solicitud devuelta, genera una nueva solicitud para el préstamo pendiente
				location.href = 'index.php?c=solicitudes&a=editdevueltoee&f_generar_nueva_solicitud=1&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud_hcd={11}'.format(
					panio, ptipo, pnumero, pcuerpo, palcance, 
					pdigito, pcuerpoalcance, panexoalcance, 
					pcuerpoanexoalcance, panexo, pcuerpoanexo,
					pfecha_solicitud_hcd
				);
			}
		},
		btn_no: { class: 'btn-primary' }
	});
}

var callbackRenderAccionFechaAnuladoEE = function (data, type, full, meta) {
	// Por defecto
	var valor = '---';
	// Si ya posee la fecha de Anulado
	if (full.fecha_anulado_ee != null)
		// Se formatea la fecha para mostrarla, retirando la parte de la hora
		valor = formatearFechaConBarras(full.fecha_anulado_ee.substr(0,10));
	// Si su estado siguiente es Anulado
	else if ($.inArray('AEE', full.ro_estados_siguientes) >= 0)
		// Se arma el botón para ANULAR la Solicitud
		valor = '<a href="index.php?c=solicitudes&a=editanulado&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud_hcd={11}" class="btn btn-danger btn-sm" role="button"><span class="glyphicon glyphicon-edit"></span>&nbsp;Anular</a>'.format(
			full.anio, full.tipo, full.numero, full.cuerpo, full.alcance, 
			full.digito, full.cuerpoalcance, full.anexoalcance, 
			full.cuerpoanexoalcance, full.anexo, full.cuerpoanexo,
			full.fecha_solicitud_hcd
		);

	return valor;
};

var callbackRenderEstado = function (data, type, full, meta) {
	var cadena;
	
	switch (full.estado) {
		case 'SHCD' : // Solicitado al HCD
			cadena = "<span style='padding:5px;display:block;"+mostrarColorSegunEstado(full.estado)+"'>Solicitado al HCD</span>";
			break;
				
		case 'SEE' : // Solicitado al Ente Externo
			cadena = "<span style='padding:5px;display:block;"+mostrarColorSegunEstado(full.estado)+"'>Solicitado al Ente Externo</span>";
			break;
				
		case 'IEE' : // Ingresado del Ente Externo
			cadena = "<span style='padding:5px;display:block;"+mostrarColorSegunEstado(full.estado)+"'>Ingresado del Ente Externo</span>";
			break;
				
		case 'DEE' : // Devuelto al Ente Externo
			cadena = "<span style='padding:5px;display:block;"+mostrarColorSegunEstado(full.estado)+"'>Devuelto al Ente Externo</span>";
			break;

		case 'AEE' : // Anulado
			cadena = "<span style='padding:5px;display:block;"+mostrarColorSegunEstado(full.estado)+"'>Anulado</span>";
			break;
				
		default:
			cadena = "";
			break;
	}
	return cadena;
};

/**
 * [listenerBotonAccion description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBotonAccion = function (event) {
	event.preventDefault(event); // Prevengo que el link haga lo propio al hacerle click

	// Obtengo la acción
	accion = $(this).data('accion');
	
	f_anio    = $(this).data('anio');
	f_tipo    = $(this).data('tipo');
	f_numero  = $(this).data('numero');
	f_cuerpo  = $(this).data('cuerpo');
	f_alcance = $(this).data('alcance');
	
	f_digito 			 = $(this).data('digito');
	f_cuerpoalcance 	 = $(this).data('cuerpoalcance');
	f_anexoalcance 	     = $(this).data('anexoalcance');
	f_cuerpoanexoalcance = $(this).data('cuerpoanexoalcance');
	f_anexo 			 = $(this).data('anexo');
	f_cuerpoanexo 	     = $(this).data('cuerpoanexo');

	f_fecha_solicitud_hcd = $(this).data('fecha_solicitud_hcd');

	// Si se desea editar la información de la Solicitud
	if (accion == 'edit')
		$(location).attr('href','index.php?c=solicitudes&a=editinfo&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud_hcd={11}'.format(
			f_anio, f_tipo, f_numero, f_cuerpo, f_alcance,
			f_digito, f_cuerpoalcance, f_anexoalcance, f_cuerpoanexoalcance, f_anexo, f_cuerpoanexo, 
			f_fecha_solicitud_hcd)
		);
	// Si se desea eliminar una Solicitud
	else if (accion == 'delete') {
		item = dataTableRef.row($(this).parents('tr')).data();
		eliminarSolicitud(item);
	} else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'. Solicitud: {1}-{2}-{3}-{4}-{5}-{6}-{7}-{8}-{9}-{10}-{11}-{12}'.format(
			accion, 
			f_anio, f_tipo, f_numero, f_cuerpo, f_alcance,
			f_digito, f_cuerpoalcance, f_anexoalcance, f_cuerpoanexoalcance, f_anexo, f_cuerpoanexo, 
			f_fecha_solicitud_hcd)
		);
};

/**
 * Se envía la Solicitud respectiva para su eliminación
 * @param  {[type]} solicitud_ee [description]
 * @return {[type]}            [description]
 */
function eliminarSolicitud(solicitud_ee) {
	showModal('Atenci&oacute;n', "¿Est&aacute; seguro que desea eliminar la Solicitud {0}-{1}-{2} definitivamente?".format(solicitud_ee.anio, solicitud_ee.tipo, solicitud_ee.numero),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					url: "index.php?c=solicitudes&a=delete",
					dataType: 'json', 
					data: JSON.stringify(solicitud_ee)
				})
				.done(function( respuesta ) {
					if (respuesta.estado == 'OK')
						// Refrescar grilla
						dataTableRef.ajax.reload(null, false);
					else 
						showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {
					showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
				});
			}
		},
		btn_no: { class: 'btn-primary' }
	});
}

function mostrarColorSegunEstado(estado) {
	var color_fondo_y_texto = "";
	
	switch (estado) {
		case 'SHCD':
			color_fondo_y_texto = "background-color: #FCF8E3;color: #C09853;";// AMARILLO PASTEL
			break;
		case 'SEE':
			color_fondo_y_texto = "background-color: #FCF8E3;color: #C09853;";// AMARILLO PASTEL
			break;
		case 'IEE':
			color_fondo_y_texto = "background-color: #F2DEDE;color: #B94A48;";// ROJO PASTEL
			break;
		case 'DEE':
			color_fondo_y_texto = "background-color: #DFF0D8;color: #468847;";// VERDE PASTEL
			break;
		case 'AEE':
			color_fondo_y_texto = "background-color: #D9D9D9;color: #3A3A3A;";// GRIS PASTEL
			break;
	}
	
	return color_fondo_y_texto;
}

/**
 * [setDataTable description]
 * @param {[type]} ajaxUrl [description]
 * @return DataTable
 */
function setDataTable(ajaxUrl) {
	// Borro y creo la grilla (tabla)
	idTabla = '#grillaSolicitudes';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(
		generarGrillaHtml(idTabla, 
			new Array( // Títulos de cada columna de la grilla
				'&nbsp;',
				'Expediente',
				'Fecha Solicitud HCD',
				'Fecha Solicitud E.E.',
				'Fecha Ingreso HCD',
				'Fecha Devoluci&oacute;n E.E.',
				'Fecha Anulado',
				'Estado',
				'Observaciones'
			)
		)
	);
	
	// Errores customizados para Datatables
	$.fn.dataTable.ext.errMode = 'none';

	// transformo la tabla en un DataTable
	var tabla = $(idTabla)
		.on( 'error.dt', function (e, settings, techNote, message) {
			showModal('Aviso', 'Ha ocurrido un error: {0}'.format(message),
				{ btn_cerrar: modalBtnSessionHandler(settings.jqXHR.responseJSON) });
		})
		.DataTable({
			stateSave: true,
			processing: true,
			serverSide: true,
			ordering: false,
			responsive: true,
			scrollX: true,
			ajax: {
	            url: ajaxUrl,
	            data: function ( d ) {
	            	// Agrego los parámetros de búsqueda
	            	d.f_anio    = $('#f_anio').val();
	            	d.f_tipo    = $('#f_tipo').val();
	            	d.f_numero  = $('#f_numero').val();
	            	d.f_cuerpo  = $('#f_cuerpo').val();
	            	d.f_alcance = $('#f_alcance').val();

	            	d.f_digito 			   = $('#f_digito').val();
	            	d.f_cuerpoalcance 	   = $('#f_cuerpoalcance').val();
	            	d.f_anexoalcance 	   = $('#f_anexoalcance').val();
	            	d.f_cuerpoanexoalcance = $('#f_cuerpoanexoalcance').val();
	            	d.f_anexo 			   = $('#f_anexo').val();
	            	d.f_cuerpoanexo 	   = $('#f_cuerpoanexo').val();

	            	d.f_fecha_desde = $('#f_fecha_desde').val();
                    d.f_fecha_hasta = $('#f_fecha_hasta').val();
                    // Estados elegidos
                    d.f_estado_solicitado_hcd = ($("#f_estado_solicitado_hcd").prop('checked')) ? $('#f_estado_solicitado_hcd').val() : '';
	            	d.f_estado_solicitado_ee  = ($("#f_estado_solicitado_ee").prop('checked')) ? $('#f_estado_solicitado_ee').val() : '';
	            	d.f_estado_ingresado_ee   = ($("#f_estado_ingresado_ee").prop('checked')) ? $('#f_estado_ingresado_ee').val() : '';
	            	d.f_estado_devuelto_ee    = ($("#f_estado_devuelto_ee").prop('checked')) ? $('#f_estado_devuelto_ee').val() : '';
	            	d.f_estado_anulado 	  	  = ($("#f_estado_anulado").prop('checked')) ? $('#f_estado_anulado').val() : '';
	            }
	        },
	        // Dejo solamente la 't'abla y el 'p'aginador
            dom: 't<"btn-sm"p>',
	        autoWidth: false,
			language: { url: '../librerias/datatables/localisation/es_AR.json' }, 
			columnDefs: [ 
				{ className: 'text-left', targets: '_all', searchable: false }
			],
			columns: [
				{data: null, width: '5px', render: callbackRenderAcciones},
				{data: null, width: '130px', render: callbackRenderMostrarClaveExpediente}, // Expediente del Préstamo
				{data: null, width: '30px', className: 'text-center', render: callbackRenderAccionFechaSolicitudHCD}, // Fecha Solicitud al HCD
				{data: null, width: '30px', className: 'text-center', render: callbackRenderAccionFechaSolicitudEE},  // Fecha Solicitud al E.E.
				{data: null, width: '30px', className: 'text-center', render: callbackRenderAccionFechaIngresadoEE},  // Fecha Ingresado del HCD
				{data: null, width: '30px', className: 'text-center', render: callbackRenderAccionFechaDevolucionEE}, // Fecha Devolución al E.E.
				{data: null, width: '30px', className: 'text-center', render: callbackRenderAccionFechaAnuladoEE},	  // Fecha Anulado
				{data: null, width: '100px', render: callbackRenderEstado}, // Estado
				{data: 'observaciones', width: '400px'}
			],
			// Cantidad de registros por página
            pageLength: 7
		});

	return tabla;
};

/**
 * Variables globales
 */
// var dataTableRef; @ expedientes-busquedasimple-common.js

/**
 * Entry Point de jQuery
 */
$(document).ready(function() {
	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	asignarValidatorExpresionesRegulares();

    // Inicialización de los DatePicker
    inicializarDatePicker();
    
	// Genero la grilla general
	dataTableRef = setDataTable('index.php?c=solicitudes&a=datagrid');

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
    
    $('#f_estado_solicitado_hcd').click(function () {
        $('#f_estado_solicitado_hcd').val(($('#f_estado_solicitado_hcd').prop('checked')) ? 'SHCD' : '');
    });

    $('#f_estado_solicitado_ee').click(function () {
        $('#f_estado_solicitado_ee').val(($('#f_estado_solicitado_ee').prop('checked')) ? 'SEE' : '');
    });

    $('#f_estado_ingresado_ee').click(function () {
        $('#f_estado_ingresado_ee').val(($('#f_estado_ingresado_ee').prop('checked')) ? 'IEE' : '');
    });

    $('#f_estado_devuelto_ee').click(function () {
        $('#f_estado_devuelto_ee').val(($('#f_estado_devuelto_ee').prop('checked')) ? 'DEE' : '');
    });

    $('#f_estado_anulado').click(function () {
        $('#f_estado_anulado').val(($('#f_estado_anulado').prop('checked')) ? 'AEE' : '');
    });

	// Se genera el PDF del listado de Solicitudes
    $('#btn_generar_reporte').click(function () { 
        // Se arma la url con el criterio de búsqueda utilizado
        var url = 'index.php?c=solicitudes&a=generarpdfsolicitudes&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud_hcd_desde={11}&f_fecha_solicitud_hcd_hasta={12}&f_estado_solicitado_hcd={13}&f_estado_solicitado_ee={14}&f_estado_ingresado_ee={15}&f_estado_devuelto_ee={16}&f_estado_anulado={17}'.format(
            $('#f_anio').val(),
            $('#f_tipo').val(),
            $('#f_numero').val(),
            $('#f_cuerpo').val(),
            $('#f_alcance').val(),
            $('#f_digito').val(),
            $('#f_cuerpoalcance').val(),
            $('#f_anexoalcance').val(),
            $('#f_cuerpoanexoalcance').val(),
            $('#f_anexo').val(),
            $('#f_cuerpoanexo').val(),
            $('#f_fecha_desde').val(),
            $('#f_fecha_hasta').val(),
            $('#f_estado_solicitado_hcd').val(),
            $('#f_estado_solicitado_ee').val(),
            $('#f_estado_ingresado_ee').val(),
            $('#f_estado_devuelto_ee').val(),
            $('#f_estado_anulado').val()
        );
        
        // Se muestra el pdf en una nueva pestaña
        window.open(url);
    });

	// Para volver a la grilla de expedientes
	$('#btn_volver').click(function () {
		$(location).attr('href','index.php?c=expedientes&a=view');
	});
	// Para buscar según el criterio de búsqueda elegido
	$('#btn_buscar').click(function () {
		dataTableRef.ajax.reload();
	});
	// Se limpia el criterio de búsqueda, se muestran todas las solicitudes
	$('#btn_restablecer').click(function () {
		$(location).attr('href','index.php?c=solicitudes&a=view');
	});

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

	// Se oculta el ítem TAREAS del menú principal
    $('#menu_item_tareas').css("display", "none");
});