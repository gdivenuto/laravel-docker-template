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

	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" {3} data-accion="{0}" title="{1}" data-anio="{4}" data-tipo="{5}" data-numero="{6}" data-cuerpo="{7}" data-alcance="{8}" data-orden="{9}" data-id_firma="{10}"></span>&nbsp;&nbsp;'.format(
		accion, descripcion, icono, color,
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance, fila.orden, fila.id_firma
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

	buttonAction += generarHtmlBotonAccion('rechazar', 'Rechazar la solicitud de firma del documento', 'remove', full, 'red');
	buttonAction += generarHtmlBotonAccion('verfirmas', 'Hay firmas pendientes para el documento.', 'pencil', full);
	buttonAction += generarHtmlBotonAccion('vercertificado', 'Ver firmas digitales', 'lock', full);
	buttonAction += `<a href="index.php?c=expedienteselec&a=verdocumento&f_anio=${full.anio}&f_tipo=${full.tipo}&f_numero=${full.numero}&f_cuerpo=${full.cuerpo}&f_alcance=${full.alcance}&f_orden=${full.orden}" download title="Descargar documento"><span class="glyphicon glyphicon-save"></span></a>&nbsp;&nbsp;`;
   	buttonAction += `<a href="${base_url}index.php?c=expedienteselec&a=view&f_anio=${full.anio}&f_tipo=${full.tipo}&f_numero=${full.numero}&f_cuerpo=${full.cuerpo}&f_alcance=${full.alcance}" title="Ir a Expediente Electrónico"><span class="glyphicon glyphicon glyphicon-share-alt"></span></a>&nbsp;&nbsp;`;
    if (full.ro_embebido)
    	buttonAction += generarHtmlBotonAccion('listarembebidos', 'El documento posee archivos embebidos.', 'paperclip', full);
   	else
   		buttonAction += generarHtmlSoloIcono('No posee documentos embebidos.', 'paperclip', full, 'lightgray');
	buttonAction += generarHtmlBotonAccion('firmar', 'Firmar el documento', 'edit', full, 'green');

    
	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

var callbackRenderExpeElec = function (data, type, full, meta) {
	var txt = (full.tipo == 'E') 
		? `<strong>${full.anio}-${full.tipo}-${full.numero}</strong> cpo. <strong>${full.cuerpo}</strong> alc. <strong>${full.alcance}</strong>`
		: `<strong>${full.anio}-${full.tipo}-${full.numero}</strong>`;

	return `<a href="${base_url}index.php?c=expedienteselec&a=view&f_anio=${full.anio}&f_tipo=${full.tipo}&f_numero=${full.numero}&f_cuerpo=${full.cuerpo}&f_alcance=${full.alcance}" title="Ir a Expediente Electrónico">${txt}</a>, orden <strong>${full.orden}</strong>`
};

var callbackRenderDetalle = function (data, type, full, meta)
{
	return `<a href="index.php?c=expedienteselec&a=verdocumento&f_anio=${full.anio}&f_tipo=${full.tipo}&f_numero=${full.numero}&f_cuerpo=${full.cuerpo}&f_alcance=${full.alcance}&f_orden=${full.orden}" target="_blank" title="Ver documento">${full.ro_detalle}</a>`;
}

var callbackRenderObservaciones = function (data, type, full, meta)
{
	btnVerMas = generarHtmlBotonAccion('verobs', 'Ver Observaciones', 'option-horizontal', full)

	var obs = ((full.ro_observaciones_ee) ?? '').replace('/(?:\r\n|\r|\n)/g', '');

	return ((obs.length > 65)
			? `${obs.substring(0, 65)} ${btnVerMas}`
			: obs);
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
	f_id_firma = $(this).data('id_firma');

	// Acciones
	if (accion == 'view') {
		window.open(f_url, '_blank');
	} else if (accion == 'verfirmas') {
		verFirmas(f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden);
	} else if (accion == 'vercertificado') {
		verCertificadosDigitales(f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden);
	} else if (accion == 'listarembebidos') {
		$(location).attr('href',`index.php?c=expedienteselec&a=listarembebidos&f_anio=${f_anio}&f_tipo=${f_tipo}&f_numero=${f_numero}&f_cuerpo=${f_cuerpo}&f_alcance=${f_alcance}&f_orden=${f_orden}`);
	} else if (accion == 'firmar') {
		iniciarActuacion('expe_elec_firmar', {anio: f_anio, tipo: f_tipo, numero: f_numero, cuerpo: f_cuerpo, alcance: f_alcance, orden: f_orden, id_firma: f_id_firma});
	} else if (accion == 'rechazar') {
		iniciarActuacion('expe_elec_rechazar_firma', {anio: f_anio, tipo: f_tipo, numero: f_numero, cuerpo: f_cuerpo, alcance: f_alcance, orden: f_orden, id_firma: f_id_firma});
	} else if (accion == 'verobs') {
		let item = dataTableRef.row($(this).parents('tr')).data();
		showModal(
			`${item.anio}-${item.tipo}-${item.numero} cpo. ${item.cuerpo} alc. ${item.alcance}, orden: ${item.orden}`, 
			`<p><u>Observaciones:</u> ${item.ro_detalle}</p><p>${item.ro_observaciones_ee.replace(/(?:\r\n|\r|\n)/g, '<br/>')}</p>`
		);
	}
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'.'.format(accion));
};

/**
 * [verFirmas description]
 * @param  {[type]} anio    [description]
 * @param  {[type]} tipo    [description]
 * @param  {[type]} numero  [description]
 * @param  {[type]} cuerpo  [description]
 * @param  {[type]} alcance [description]
 * @param  {[type]} orden   [description]
 * @return {[type]}         [description]
 */
var verFirmas = function (anio, tipo, numero, cuerpo, alcance, orden) {
	// Peticion asíncrona
	$.ajax({
		method: "GET",
		url: "index.php",
		dataType: 'json',
		data: {
			c: "expedienteselec", 
			a: "verfirmas", 
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
				var html_firmas = '<p><u>Registro de firmas:</u></p><ol>';
				$.each(respuesta.data, function (idx_f, val_f) {
					color_estado = '';
					firma_estado = '';
					switch (val_f.estado) {
						case 'firmado': color_estado = 'green'; firma_estado = 'Firmado'; break;
						case 'pendiente': color_estado = 'red'; break;
						case 'cancelado': color_estado = 'orange'; firma_estado = 'Cancelado'; break;
					};
					fecha_salida = (val_f.fecha_hora_salida !== null)
						? `; ${firma_estado} el <strong>${val_f.fecha_hora_salida}</strong>`
						: '';
					html_firmas += `<li>Estado: <strong><span style="color: ${color_estado}">${val_f.estado.toUpperCase()}</span></strong>`;
					html_firmas += '<ul>';
					html_firmas += `<li>Signatario: <strong>${val_f.ro_nombre_usuario}</strong> ; Solicitante: <strong>${val_f.ro_nombre_usuario_solicitante}</strong></li>`;
					html_firmas += `<li>Solicitado el <strong>${val_f.fecha_hora_entrada}</strong> ${fecha_salida}</li>`;
					html_firmas += '</ul></li>';
				});
				html_firmas += '</ol>';

				showModal('Registro de Firmas', html_firmas, { btn_cerrar: { class: 'btn-default' } });
			} else 
				showModal('Registro de Firmas', 'No se encontró un registro de firmas para el documento.', {btn_cerrar: {class: 'btn-default'}});
		} else if ((respuesta.estado == "ERROR") || (respuesta.estado == "WARNING")) {
			alert(respuesta.mensaje);
		}
	}).fail(function () {
		alert(respuesta.mensaje);
	});
};

/**
 * [verCertificadosDigitales description]
 * @param  {[type]} documento [description]
 * @param  {[type]} firmado   [description]
 * @return {[type]}           [description]
 */
var verCertificadosDigitales = function (anio, tipo, numero, cuerpo, alcance, orden) {
	// Peticion asíncrona
	$.ajax({
		method: "GET",
		url: "index.php",
		dataType: 'json',
		data: {
			c: "expedienteselec", 
			a: "vercertificados", 
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
				var html_firmas = '';
				$.each(respuesta.data, function (idx_f, val_f) {
					html_firmas += `<p><u>Firma Digital Nº ${idx_f+1}</u></p><ul>`;
					$.each(val_f, function (idx_c, val_c) {
						html_firmas += `<li><u>Certificado Nº ${idx_c+1}, Firmante:</u> <strong>${val_c['subject']['CN']}</strong><ul><li>`;
						
						html_firmas += $.map(val_c['subject'], function (val_s, idx_s) {
							return `<strong>${idx_s}:</strong> ${val_s}`;
						}).join('; ');
						html_firmas += '</li></ul></li>';

						html_firmas += `<li><u>Certificado Nº ${idx_c+1}, Certificante:</u> <strong>${val_c['issuer']['CN']}</strong><ul><li>`;
						html_firmas += $.map(val_c['issuer'], function (val_i, idx_i) {
							return `<strong>${idx_i}: </strong> ${val_i}`;
						}).join('; ');
						html_firmas += '</li></ul></li>';
					});
					html_firmas += '</ul><hr/>';
				});

				showModal('Firmas Digitales', html_firmas, { btn_cerrar: { class: 'btn-default' } });
			} else 
				showModal('Firmas Digitales', 'No se encontraron firmas digitales en el documento.', {btn_cerrar: {class: 'btn-default'}});
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
	idTabla = '#grillaFirmas';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(generarGrillaHtml(idTabla, ['Acciones', 'Documento', 'Detalle', 'Observaciones', 'Pendiente desde']));

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
				{data: null, width: "1%", className: 'text-center', render: callbackRenderAcciones},
				{data: null, width: "15%", render: callbackRenderExpeElec},
				{data: 'ro_detalle', width: "40%", render: callbackRenderDetalle},
				{data: 'ro_observaciones_ee', render: callbackRenderObservaciones},
				{data: 'fecha_hora_entrada', width: "10%"},
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
	controlador_activo = 'firmas';

	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Genero la tabla
	dataTableRef = setDataTable('index.php?c=firmas&a=datagrid&f_detalle='+$('#f_detalle').val());

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

});