/**
 * [generarHtmlBotonAccion description]
 * @param  {[type]} accion      [description]
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila, color) {
	color = (color != '') 
		? `style="color: ${color}"`
		: '';

	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" {3} data-accion="{0}" title="{1}" data-anio="{4}" data-tipo="{5}" data-numero="{6}" data-cuerpo="{7}" data-alcance="{8}" data-id_pendiente="{9}"></span>&nbsp;&nbsp;'.format(
		accion, descripcion, icono, color,
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance, fila.id_pendiente
		);
}

/**
 * [callbackRenderAcciones description]
 * @param  {[type]} data [description]
 * @param  {[type]} type [description]
 * @param  {[type]} full [description]
 * @param  {[type]} meta [description]
 * @return {[type]}      [description]
 */
var callbackRenderAcciones = function (data, type, full, meta) {
	buttonAction = '';

	buttonAction += generarHtmlBotonAccion('descartar_giros', 'Descartar la confirmación de giros a comisiones', 'trash', full);
   	buttonAction += generarHtmlBotonAccion('ver_giros', 'Ver el lote de giros pendientes a comisiones.', 'search', full);
   	buttonAction += `<a href="${base_url}index.php?c=expedienteselec&a=view&f_anio=${full.anio}&f_tipo=${full.tipo}&f_numero=${full.numero}&f_cuerpo=${full.cuerpo}&f_alcance=${full.alcance}" title="Ir a Expediente Electrónico"><span class="glyphicon glyphicon glyphicon-share-alt"></span></a>&nbsp;&nbsp;`;
	if (full.id_usuario_firmante == id_usuario)
		buttonAction += generarHtmlBotonAccion('confirmar_giros', 'Confirmar los giros a comisiones', 'ok', full, 'green');
    
	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

var callbackRenderExpediente = function (data, type, full, meta) {
	var txt = (full.tipo == 'E') 
		? `<strong>${full.anio}-${full.tipo}-${full.numero}</strong> cpo. <strong>${full.cuerpo}</strong> alc. <strong>${full.alcance}</strong>`
		: `<strong>${full.anio}-${full.tipo}-${full.numero}</strong>`;

	return `<a href="${base_url}index.php?c=expedienteselec&a=view&f_anio=${full.anio}&f_tipo=${full.tipo}&f_numero=${full.numero}&f_cuerpo=${full.cuerpo}&f_alcance=${full.alcance}" title="Ir a Expediente Electrónico">${txt}</a>`
};

var callbackRenderObservaciones = function (data, type, full, meta)
{
	btnVerMas = generarHtmlBotonAccion('verobs', 'Ver Observaciones', 'option-horizontal', full)

	var obs = ((full.observaciones) ?? '').replace('/(?:\r\n|\r|\n)/g', '');

	return ((obs.length > 65)
			? `${obs.substring(0, 65)} ${btnVerMas}`
			: obs);
};

var callbackRenderSolicitante = function (data, type, full, meta) {
	var tag_usuario = `${full.ro_nombre_usuario_solicitante} (${full.ro_codigo_usuario_solicitante})`;
	return (full.id_usuario_solicitante == id_usuario)
		? `<strong>${tag_usuario}</strong>`
		: tag_usuario;
};

var callbackRenderFirmante = function (data, type, full, meta) {
	var tag_usuario = `${full.ro_nombre_usuario_firmante} (${full.ro_codigo_usuario_firmante})`;
	return (full.id_usuario_firmante == id_usuario)
		? `<strong>${tag_usuario}</strong>`
		: tag_usuario;
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
	
	// Data
	f_anio = $(this).data('anio');
	f_tipo = $(this).data('tipo');
	f_numero = $(this).data('numero');
	f_cuerpo = $(this).data('cuerpo');
	f_alcance = $(this).data('alcance');
	f_id_pendiente = $(this).data('id_pendiente');

	// Acciones
	if (accion == 'view') {
		window.open(f_url, '_blank');
	} else if (accion == 'confirmar_giros') {
		iniciarActuacion('expediente_confirmar_giros', {anio: f_anio, tipo: f_tipo, numero: f_numero, cuerpo: f_cuerpo, alcance: f_alcance, id_pendiente: f_id_pendiente});
	} else if (accion == 'ver_giros') {
		verGirosPendientes(f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_id_pendiente);
	} else if (accion == 'descartar_giros') {
		iniciarActuacion('expediente_descartar_giros', {anio: f_anio, tipo: f_tipo, numero: f_numero, cuerpo: f_cuerpo, alcance: f_alcance, id_pendiente: f_id_pendiente});
	} else if (accion == 'verobs') {
		let item = dataTableRef.row($(this).parents('tr')).data();
		showModal(
			`${item.anio}-${item.tipo}-${item.numero} cpo. ${item.cuerpo} alc. ${item.alcance}`, 
			`<p><u>Observaciones:</u></p><p>${item.observaciones.replace(/(?:\r\n|\r|\n)/g, '<br/>')}</p>`
		);
	}
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'.'.format(accion));

};

/**
 * [verGirosPendientes description]
 * @param  {[type]} anio         [description]
 * @param  {[type]} tipo         [description]
 * @param  {[type]} numero       [description]
 * @param  {[type]} cuerpo       [description]
 * @param  {[type]} alcance      [description]
 * @param  {[type]} id_pendiente [description]
 * @return {[type]}              [description]
 */
var verGirosPendientes = function (anio, tipo, numero, cuerpo, alcance, id_pendiente) {
	// Peticion asíncrona
	$.ajax({
		method: "GET",
		url: "index.php",
		dataType: 'json',
		data: {
			c: "girospendientes", 
			a: "vergiros", 
			anio: anio,
			tipo: tipo,
			numero: numero,
			cuerpo: cuerpo,
			alcance: alcance,
			id_pendiente: id_pendiente
		}
	}).done(function( respuesta ) {
		// muestro datos de firma
		if (respuesta.estado == "OK") {
			if (respuesta.data != null && respuesta.data.length > 0) {
				var html_giros = '<p><u>Giros pendientes de confirmación:</u></p><ol>';
				$.each(respuesta.data, function (idx_f, val_g) {

					html_giros += `<li>${val_g.descripcion_grp}`;
					html_giros += (val_g.observaciones != '') 
						? `<ul><li>Observaciones: ${val_g.observaciones}</li></ul>`
						: '';
					html_giros += '</li>';
				});
				html_giros += '</ol>';

				showModal('Giros Pendientes', html_giros, { btn_cerrar: { class: 'btn-default' } });
			} else 
				showModal('Giros Pendientes', 'No se encontró un lote de giros pendientes para el expediente.', {btn_cerrar: {class: 'btn-default'}});
		} else if ((respuesta.estado == "ERROR") || (respuesta.estado == "WARNING")) {
			alert(respuesta.mensaje);
		}
	}).fail(function () {
		alert(respuesta.mensaje);
	});
};


/**
 * [setDataTable description]
 * @param {[type]} ajaxUrl [description]
 * @return DataTable
 */
function setDataTable(ajaxUrl) {
	// Borro y creo la grilla (tabla)
	idTabla = '#grillaGirosPendientes';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(generarGrillaHtml(idTabla, ['Acciones', 'Expediente', 'Cargado por', 'Confirma', 'Observaciones', 'Pendiente desde']));

	// Errores customizados para Datatables
	$.fn.dataTable.ext.errMode = 'none';

	// transformo la tabla en un DataTable
	var tabla = $(idTabla)
		.on( 'error.dt', function (e, settings, techNote, message) {
			showModal('Aviso', 'Ha ocurrido un error: {0}'.format(message),
				{ btn_cerrar: modalBtnSessionHandler(settings.jqXHR.responseJSON) });
		})
		.DataTable({
			stateSave: false, // por defecto es true
			processing: true,
			serverSide: true,
			ordering: false,
			responsive: true,
			autoWidth: false,
	        scrollX: true,
	        pageLength: 20,
			ajax: {
	            url: ajaxUrl,
	            data: function ( d ) {
	            	// Agrego los parámetros de búsqueda.
	            	d.f_detalle = 'asd'; //$('#f_detalle').val();
	            }
	        },
            dom: 'tp', // Definimos la 't'abla y el 'p'aginador
			language: { url: '../librerias/datatables/localisation/es_AR.json' }, 
			columnDefs: [ 
				{ targets: '_all', searchable: false }
			],
			columns: [
				{data: null, width: "1%", render: callbackRenderAcciones},
				{data: null, width: "15%", render: callbackRenderExpediente},
				{data: null, width: "20%", render: callbackRenderSolicitante},
				{data: null, width: "20%", render: callbackRenderFirmante},
				{data: 'observaciones', render: callbackRenderObservaciones},
				{data: 'fecha_hora_entrada', width: "10%"}
			]
		});

	return tabla;
};

/**
 * Variables globales
 */

/**
 * Entry Point de jQuery
 */
$(document).ready(function() {
	controlador_activo = 'girospendientes';

	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Genero la tabla
	dataTableRef = setDataTable('index.php?c=girospendientes&a=datagrid&f_detalle='+$('#f_detalle').val());

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);
});