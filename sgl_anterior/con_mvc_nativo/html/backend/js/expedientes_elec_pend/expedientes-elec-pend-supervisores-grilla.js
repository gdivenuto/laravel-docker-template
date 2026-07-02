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

	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" {3} data-accion="{0}" title="{1}" data-anio="{4}" data-tipo="{5}" data-numero="{6}" data-cuerpo="{7}" data-alcance="{8}" data-orden="{9}" data-id_revision="{10}"></span>&nbsp;&nbsp;'.format(
		accion, descripcion, icono, color,
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance, fila.orden, fila.id_revision
		);
}

/**
 * Se genera solamente el Icono Html (no se usa la clase 'btn-accion-contenido' ni el data-accion)
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @param  {[type]} color       [description]
 * @return {[type]}             [description]
 */
function generarHtmlSoloIcono(descripcion, icono, fila, color)
{
	color = (color != '') 
		? `style="color: ${color}"`
		: '';

	return '<span class="glyphicon glyphicon-{1}" {2} title="{0}" data-anio="{3}" data-tipo="{4}" data-numero="{5}" data-cuerpo="{6}" data-alcance="{7}" data-orden="{8}"></span>&nbsp;&nbsp;'.format(
		descripcion, icono, color,
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance, fila.orden
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

	buttonAction += generarHtmlBotonAccion('verfirmantes', 'Firmantes seleccionados para el documento pendiente.', 'pencil', full);
	buttonAction += generarHtmlBotonAccion('verrevision', 'Estado de la revisión del documento.', 'registration-mark', full);
	buttonAction += `<a href="index.php?c=expedienteselecpend&a=verdocumento&f_anio=${full.anio}&f_tipo=${full.tipo}&f_numero=${full.numero}&f_cuerpo=${full.cuerpo}&f_alcance=${full.alcance}&f_orden=${full.orden}" download title="Descargar documento."><span class="glyphicon glyphicon-save"></span></a>&nbsp;&nbsp;`;
	
    // Todos los usuarios pueden Ver los archivos embebidos
    if (full.ro_embebido)
    	buttonAction += generarHtmlBotonAccion('listarembebidos', 'El documento posee archivos embebidos.', 'paperclip', full);
   	else
   		buttonAction += generarHtmlSoloIcono('No posee documentos embebidos.', 'paperclip', full, 'lightgray');

	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

var callbackRenderExpediente = function (data, type, full, meta) {
	var txt = (full.tipo == 'E') 
		? `<strong>${full.anio}-${full.tipo}-${full.numero}</strong> cpo. <strong>${full.cuerpo}</strong> alc. <strong>${full.alcance}</strong>`
		: `<strong>${full.anio}-${full.tipo}-${full.numero}</strong>`;

	return `<a href="${base_url}index.php?c=expedienteselec&a=view&f_anio=${full.anio}&f_tipo=${full.tipo}&f_numero=${full.numero}&f_cuerpo=${full.cuerpo}&f_alcance=${full.alcance}" title="Ir a Expediente Electrónico">${txt}</a>`
};

var callbackRenderDetalle = function (data, type, full, meta)
{
	return `<a href="index.php?c=expedienteselecpend&a=verdocumento&f_anio=${full.anio}&f_tipo=${full.tipo}&f_numero=${full.numero}&f_cuerpo=${full.cuerpo}&f_alcance=${full.alcance}&f_orden=${full.orden}" target="_blank" title="Ver documento">${full.ro_detalle}</a>`;
}

var callbackRenderObservaciones = function (data, type, full, meta)
{
	btnVerMas = generarHtmlBotonAccion('verobs', 'Ver Observaciones', 'option-horizontal', full)

	var obs = ((full.ro_observaciones_ee) ?? '').replace('/(?:\r\n|\r|\n)/g', '');

	return ((obs.length > 45)
		? `${obs.substring(0, 45)} ${btnVerMas}`
		: obs);
};

var callbackRenderSolicitante = function (data, type, full, meta) {
	var tag_usuario = `${full.ro_nombre_usuario_solicitante} (${full.ro_codigo_usuario_solicitante})`;
	// return (full.ro_id_usuario_solicitante == id_usuario)
	// 	? `<strong>${tag_usuario}</strong>`
	// 	: tag_usuario;
	return tag_usuario;
};

var callbackRenderRevisor = function (data, type, full, meta) {
	var tag_usuario = `${full.ro_nombre_usuario} (${full.ro_codigo_usuario})`;
	// return (full.id_usuario == id_usuario)
	// 	? `<strong>${tag_usuario}</strong>`
	// 	: tag_usuario;
	return tag_usuario;
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
	f_orden = $(this).data('orden');
	f_id_revision = $(this).data('id_revision');

	// Acciones
	if (accion == 'view') {
		window.open(f_url, '_blank');
	} else if (accion == 'verfirmantes') {
		verFirmantes(f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden);
	} else if (accion == 'verrevision') {
		verRevision(f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden);
	} else if (accion == 'listarembebidos') {
		$(location).attr('href',`index.php?c=expedienteselecpend&a=listarembebidos&f_anio=${f_anio}&f_tipo=${f_tipo}&f_numero=${f_numero}&f_cuerpo=${f_cuerpo}&f_alcance=${f_alcance}&f_orden=${f_orden}`);
	} else if (accion == 'verobs') {
		let item = dataTableRef.row($(this).parents('tr')).data();
		showModal(
			`${item.anio}-${item.tipo}-${item.numero} cpo. ${item.cuerpo} alc. ${item.alcance}`, 
			`<p><u>Observaciones:</u></p><p>${item.ro_observaciones_ee.replace(/(?:\r\n|\r|\n)/g, '<br/>')}</p>`
		);
	}
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'.'.format(accion));
};

/**
 * [verFirmantes description]
 * @param  {[type]} anio        [description]
 * @param  {[type]} tipo        [description]
 * @param  {[type]} numero      [description]
 * @param  {[type]} cuerpo      [description]
 * @param  {[type]} alcance     [description]
 * @param  {[type]} orden       [description]
 * @param  {[type]} id_revision [description]
 * @return {[type]}             [description]
 */
var verFirmantes = function (anio, tipo, numero, cuerpo, alcance, orden) {
	// Peticion asíncrona
	$.ajax({
		method: "GET",
		url: "index.php",
		dataType: 'json',
		data: {
			c: "expedienteselecpend", 
			a: "verfirmantes", 
			anio: anio,
			tipo: tipo,
			numero: numero,
			cuerpo: cuerpo,
			alcance: alcance,
			orden: orden
		}
	}).done(function( respuesta ) {
		// muestro datos de firma
		if (respuesta.estado == "OK") {
			if (respuesta.data != null && respuesta.data.length > 0) {
				var html_firmas = '<p><u>Registro de firmas:</u></p>';
				html_firmas += '<p>Estas solicitudes de firma se harán efectivas cuando el documento haya sido <strong>revisado y confirmado</strong>.</p><ol>';
				$.each(respuesta.data, function (idx_f, val_f) {
					estado = (idx_f == 0) ? 'firmado' : 'pendiente';
					color_estado = '';
					firma_estado = '';
					switch (estado) {
						case 'firmado': color_estado = 'green'; firma_estado = 'Firmado'; break;
						case 'pendiente': color_estado = 'red'; break;
						case 'cancelado': color_estado = 'orange'; firma_estado = 'Cancelado'; break;
					};
					html_firmas += `<li>Estado: <strong><span style="color: ${color_estado}">${estado.toUpperCase()}</span></strong>`;
					html_firmas += '<ul>';
					html_firmas += `<li>Signatario: <strong>${val_f.ro_nombre_usuario}</strong> ; Solicitante: <strong>${val_f.ro_nombre_usuario_solicitante}</strong></li>`;
					html_firmas += '</ul></li>';
				});
				html_firmas += '</ol>';

				showModal('Firmas del documento a revisar', html_firmas, { btn_cerrar: { class: 'btn-default' } });
			} else 
				showModal('Firmas del documento a revisar', 'No se encontraron firmas pendientes para el documento a revisar.', {btn_cerrar: {class: 'btn-default'}});
		} else if ((respuesta.estado == "ERROR") || (respuesta.estado == "WARNING")) {
			alert(respuesta.mensaje);
		}
	}).fail(function () {
		alert(respuesta.mensaje);
	});
};

/**
 * [verRevision description]
 * @param  {[type]} anio    [description]
 * @param  {[type]} tipo    [description]
 * @param  {[type]} numero  [description]
 * @param  {[type]} cuerpo  [description]
 * @param  {[type]} alcance [description]
 * @param  {[type]} orden   [description]
 * @return {[type]}         [description]
 */
var verRevision = function (anio, tipo, numero, cuerpo, alcance, orden) {
	// Peticion asíncrona
	$.ajax({
		method: "GET",
		url: "index.php",
		dataType: 'json',
		data: {
			c: "expedienteselecpend", 
			a: "verrevision", 
			anio: anio,
			tipo: tipo,
			numero: numero,
			cuerpo: cuerpo,
			alcance: alcance,
			orden: orden
		}
	}).done(function( respuesta ) {
		// muestro datos de firma
		if (respuesta.estado == "OK") {
			if (respuesta.data != null && respuesta.data.length > 0) {
				var html_revisiones = '<ol>';
				$.each(respuesta.data, function (idx_r, val_r) {
					color_estado = '';
					firma_estado = val_r.estado;
					switch (val_r.estado) {
						case 'confirmado': color_estado = 'green'; firma_estado = 'Confirmada'; break;
						case 'pendiente': color_estado = 'red'; break;
						case 'rechazado': color_estado = 'orange'; firma_estado = 'Rechazada'; break;
					};

					fecha_salida = (val_r.fecha_hora_salida !== null)
						? `; ${firma_estado} el <strong>${val_r.fecha_hora_salida}</strong>`
						: '';

					html_revisiones += `<li>Revisión: <strong><span style="color: ${color_estado}">${firma_estado.toUpperCase()}</span></strong>`;
					html_revisiones += '<ul>';
					html_revisiones += `<li>Revisor: <strong>${val_r.ro_nombre_usuario}</strong> ; Solicitante: <strong>${val_r.ro_nombre_usuario_solicitante}</strong></li>`;
					html_revisiones += `<li>Solicitado el <strong>${val_r.fecha_hora_entrada}</strong> ${fecha_salida}</li>`;
					html_revisiones += '</ul></li>';
				});
				html_revisiones += '</ol>';

				showModal('Registro de Revisiones', html_revisiones, { btn_cerrar: { class: 'btn-default' } });
			} else 
				showModal('Registro de Revisiones', 'No se encontraron firmas pendientes para el documento a revisar.', {btn_cerrar: {class: 'btn-default'}});
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
	idTabla = '#grillaRevisionesPendientesParaSupervisor';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(
		generarGrillaHtml(
			idTabla, 
			['Acciones', 'Expediente', 'Detalle', 'Cargado por', 'Confirma', 'Observaciones', 'Pendiente desde']
		));

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
	        pageLength: 15,
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
				{data: null, width: "14%", render: callbackRenderExpediente},
				{data: null, width: "15%", render: callbackRenderDetalle},
				{data: null, width: "20%", render: callbackRenderSolicitante},
				{data: null, width: "20%", render: callbackRenderRevisor},
				{data: null, width: "20%", render: callbackRenderObservaciones},
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
	controlador_activo = 'expedienteselecpendsupervisor';

	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Genero la tabla
	dataTableRef = setDataTable('index.php?c=expedienteselecpendsupervisor&a=datagrid&f_detalle='+$('#f_detalle').val());

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);
});