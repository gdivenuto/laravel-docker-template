/**
 * Se genera el Html con el Botón con su Acción respectiva
 * @param  {[type]} accion      [description]
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @param  {[type]} color       [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila, color)
{
	color = (color != '') 
		? `style="color: ${color}"`
		: '';

	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" {3} data-accion="{0}" title="{1}" data-anio="{4}" data-tipo="{5}" data-numero="{6}" data-cuerpo="{7}" data-alcance="{8}" data-orden="{9}"></span>&nbsp;&nbsp;'.format(
		accion, descripcion, icono, color,
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance, fila.orden
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
var callbackRenderAcciones = function (data, type, full, meta)
{
	buttonAction = '';

	// Para usuarios Administrador o Supervisor
	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2)
	{
		// Si fue Alcanzado por el Dec 1404
		if (full.dec1404)
	    	buttonAction += generarHtmlBotonAccion('alcanzadod1404', 'Alcanzado por Art. 11 Dec. 1404', 'eye-close', full, 'red');
	    else
	    	buttonAction += generarHtmlBotonAccion('alcanzadod1404', 'No Alcanzado por Art. 11 Dec. 1404', 'eye-open', full, 'green');
    }
    else {
    	// Sólo para usuarios Concejales o Periodistas
    	if (perfil_usuario_actual != 1 && perfil_usuario_actual != 2)
		{
			// Si fue Alcanzado por el Dec 1404
			if (full.dec1404)
		    	buttonAction += generarHtmlSoloIcono('Alcanzado por Art. 11 Dec. 1404', 'eye-close', full, 'red');
		    else
		    	buttonAction += generarHtmlSoloIcono('No Alcanzado por Art. 11 Dec. 1404', 'eye-open', full, 'green');
	    }
    }

	// Para usuarios Administrador, Supervisor o Concejales
	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2 || perfil_usuario_actual == 3)
	{
		if (full.ro_total_firmas == (full.ro_cant_firmados + full.ro_cant_cancelados)) {
    		if (full.ro_total_firmas == full.ro_cant_firmados)
	    		buttonAction += generarHtmlBotonAccion('verfirmas', 'Todos los signatarios han firmado el documento.', 'pencil', full, 'green');
	    	else 
	    		buttonAction += generarHtmlBotonAccion('verfirmas', 'Algunos signatarios han cancelado la firma del documento.', 'pencil', full, 'orange');
    	} else
    		buttonAction += generarHtmlBotonAccion('verfirmas', 'Hay firmas pendientes para el documento.', 'pencil', full, 'red');
    }

    // Todos los usuarios pueden ver los Certificados de las firmas digitales
    buttonAction += generarHtmlBotonAccion('vercertificado', 'Ver firmas digitales', 'lock', full);
    
    // Todos los usuarios pueden Descargar el documento del expediente electrónico
    buttonAction += `<a href="index.php?c=expedienteselec&a=verdocumento&f_anio=${full.anio}&f_tipo=${full.tipo}&f_numero=${full.numero}&f_cuerpo=${full.cuerpo}&f_alcance=${full.alcance}&f_orden=${full.orden}" download title="Descargar documento"><span class="glyphicon glyphicon-save"></span></a>&nbsp;&nbsp;`;
	
    // Todos los usuarios pueden Ver los archivos embebidos
    if (full.embebido)
    	buttonAction += generarHtmlBotonAccion('listarembebidos', 'El documento posee archivos embebidos.', 'paperclip', full);
   	else
   		buttonAction += generarHtmlSoloIcono('No posee documentos embebidos.', 'paperclip', full, 'lightgray');

	return buttonAction;
};

var callbackRenderDetalle = function (data, type, full, meta)
{
	return `<a href="index.php?c=expedienteselec&a=verdocumento&f_anio=${full.anio}&f_tipo=${full.tipo}&f_numero=${full.numero}&f_cuerpo=${full.cuerpo}&f_alcance=${full.alcance}&f_orden=${full.orden}" target="_blank" title="Ver documento">${full.detalle}</a>`;
}

var callbackRenderFecha = function (data, type, full, meta)
{
	return formatearFechaConBarras(full.fecha_hora);
}

let callbackRenderObservaciones = function (data, type, full, meta)
{
	btnEditar = (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) 
		? generarHtmlBotonAccion('editarobs', 'Editar Observaciones', 'edit', full)
		: '';

	btnVerMas = generarHtmlBotonAccion('verobs', 'Ver Observaciones', 'option-horizontal', full)

	let obs = ((full.observaciones) ?? '').replace('/(?:\r\n|\r|\n)/g', '');

	return btnEditar + 
		((obs.length > 45)
			? `${obs.substring(0, 45)} ${btnVerMas}`
			: obs);
}

var callbackRenderUsuario = function (data, type, full, meta)
{
	return '<span title="'+full.ro_nombre_usuario+'">'+full.ro_codigo_usuario+'</span>';
}

/**
 *  Al volver a renderizarse la grilla
 *  
 * @param  {[type]} settings [description]
 * @return {[type]}          [description]
 */
var callbackDrawCallback = function(settings)
{
	// Se muestra la vista previa del expediente del buscador al terminar de dibujar el datatable
	// @ expedientes-busquedasimple-common.js
	actualizarVistaSiExisteExpediente(settings.json.existeExpediente);

	// Si existe el expediente
	if ( settings.json.existeExpediente)
		$('#btn_nuevo_estado').prop('disabled', false); // Se habilita el botón 'Nuevo Estado'
	else // si NO existe
		$('#btn_nuevo_estado').prop('disabled', true); // Se deshabilita el botón 'Nuevo Estado'
};

/**
 * Al renderizarse la vista, si hay un archivo 'pendiente' de descarga,
 * lo abro en otra ventana.
 * @return {[type]} [description]
 */
var descargarArchivoPendiente = function()
{
	var archivo = $('#f_archivo_descarga').val().trim();
	if (archivo != '') {
		$(`<a id="archivo_descarga_holder" href="${archivo}" style="display: none" download></a>`).appendTo('body');
		$('#archivo_descarga_holder')[0].click();
	}
}

/**
 * Se definen los títulos de la grilla
 */
function definirTitulosGrilla()
{
	return new Array('Acciones', 'Orden','Detalle', 'Fecha/Hora', 'Observaciones', 'Usuario');
}

/**
 * Se definen las columnas de la grilla
 */
function definirColumnasGrilla()
{
	return [
			{data: null, width:'60px', className:'text-center', render: callbackRenderAcciones},
			{data: 'orden', width: '1px', className:'text-right'},
			{data: 'detalle', width:'350px', render: callbackRenderDetalle},
			{data: 'fecha_hora', width:'50px', className:'text-center text-nowrap', render: callbackRenderFecha},
			{data: 'observaciones', render: callbackRenderObservaciones},
			{data: 'ro_codigo_usuario', render: callbackRenderUsuario},
	       ];
}

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

	// Acciones
	if (accion == 'alcanzadod1404') {
		iniciarActuacion('expe_elec_alcanzar_dec1404', {anio: f_anio, tipo: f_tipo, numero: f_numero, cuerpo: f_cuerpo, alcance: f_alcance, orden: f_orden});
	} else if (accion == 'verfirmas') {
		verFirmas(f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden);
	} else if (accion == 'vercertificado') {
		verCertificadosDigitales(f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden);
	} else if (accion == 'listarembebidos') {
		$(location).attr('href',`index.php?c=expedienteselec&a=listarembebidos&f_anio=${f_anio}&f_tipo=${f_tipo}&f_numero=${f_numero}&f_cuerpo=${f_cuerpo}&f_alcance=${f_alcance}&f_orden=${f_orden}`);
	} else if (accion == 'editarobs') {
		iniciarActuacion('expe_elec_editar_obs', {anio: f_anio, tipo: f_tipo, numero: f_numero, cuerpo: f_cuerpo, alcance: f_alcance, orden: f_orden});
	} else if (accion == 'verobs') {
		let item = dataTableRef.row($(this).parents('tr')).data();
		showModal(
			`${item.anio}-${item.tipo}-${item.numero} cpo. ${item.cuerpo} alc. ${item.alcance}, orden: ${item.orden}`, 
			`<p><u>Observaciones:</u> ${item.detalle}</p><p>${item.observaciones.replace(/(?:\r\n|\r|\n)/g, '<br/>')}</p>`
		);
	} else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'.'.format(accion));
};

/**
 * [verCertificadosDigitales description]
 * @param  {[type]} documento [description]
 * @param  {[type]} firmado   [description]
 * @return {[type]}           [description]
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
					pendiente_hint = (val_f.estado == 'pendiente') 
						? ` (pendiente por ${val_f.ro_dias_pendiente} días)`
						: '';
					html_firmas += `<li>Estado: <strong><span style="color: ${color_estado}">${val_f.estado.toUpperCase()}</span></strong>${pendiente_hint}`;
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
	idTabla = '#grillaExpedientesElec';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(generarGrillaHtml(idTabla, definirTitulosGrilla()));
	
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
			autoWidth: false,
	        scrollX: true,
			ajax: {
	            url: ajaxUrl,
	            data: function ( d ) {
	            	// Agrego los parámetros de búsqueda.
	            	d.f_anio = $('#f_anio').val();
	            	d.f_tipo = $('#f_tipo').val();
	            	d.f_numero = $('#f_numero').val();
	            	d.f_cuerpo = $('#f_cuerpo').val();
	            	d.f_alcance = $('#f_alcance').val();
	            }
	        },
            dom: 'tp', // Definimos la 't'abla y el p'aginador
            pageLength: 15, // Cantidad de registros por página
			language: { url: '../librerias/datatables/localisation/es_AR.json' }, 
			columnDefs: [ 
				{ targets: '_all', searchable: false, className: 'text-left' }
			],
			columns: definirColumnasGrilla(),
			drawCallback: callbackDrawCallback
		});

	// Fuerzo el renderizado de la primera pagina para no dejar con basura
	// el paginador con data de otros expedientes electronicos.
	tabla.page('first').draw('page');

	return tabla;
};

/**
 * Variables globales
 */
// var dataTableRef; @ expedientes-busquedasimple-common.js
// var expedienteRef; @ expedientes-busquedasimple-common.js
// var formBusquedaRef; @ expedientes-busquedasimple-common.js
// var proyectoNroRef; @ expedientes-busquedasimple-common.js

/**
 * Entry Point de jQuery
 */
$(document).ready(function() {
	controlador_activo = 'expedienteselec';

	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	asignarValidatorExpresionesRegulares();

	// Genero la tabla
	dataTableRef = setDataTable('index.php?c=expedienteselec&a=datagrid&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());
	
	// Inicializo el formulario
	setearComportamientoSolapas(); // @ expedientes-busquedasimple-common.js
	setearComportamientoBotonesNavegacion(); // @ expedientes-busquedasimple-common.js
	setearComportamientoValidate(); // @ expedientes-busquedasimple-common.js
	
	// Comportamiento para Cargar/subir un documento
	$('#btn_cargar_documento').click(function (e) {
		e.preventDefault();
		iniciarActuacion('expediente_subir_pdf', {
			'anio' : $('#f_anio').val(),
			'tipo' : $('#f_tipo').val(),
			'numero' : $('#f_numero').val(),
			'cuerpo' : $('#f_cuerpo').val(),
			'alcance' : $('#f_alcance').val()
		});
	});

	// Comportamiento para Cargar/subir un archivo ZIP
	$('#btn_cargar_archivo_zip').click(function (e) {
		e.preventDefault();
		iniciarActuacion('expediente_subir_zip', {
			'anio' : $('#f_anio').val(),
			'tipo' : $('#f_tipo').val(),
			'numero' : $('#f_numero').val(),
			'cuerpo' : $('#f_cuerpo').val(),
			'alcance' : $('#f_alcance').val()
		});
	});

	// Comportamiento para Componer un documento
	$('#btn_componer_documento').click(function (e) {
		e.preventDefault();
		iniciarActuacion('expediente_componer_pdf', {
			'anio' : $('#f_anio').val(),
			'tipo' : $('#f_tipo').val(),
			'numero' : $('#f_numero').val(),
			'cuerpo' : $('#f_cuerpo').val(),
			'alcance' : $('#f_alcance').val()
		});		
	});

	// Comportamiento para Descargar expediente electrónico
	$('#btn_descargar_expe_elec').click(function (e) {
		e.preventDefault();
		iniciarActuacion('expe_elec_descargar', {
			'anio' : $('#f_anio').val(),
			'tipo' : $('#f_tipo').val(),
			'numero' : $('#f_numero').val(),
			'cuerpo' : $('#f_cuerpo').val(),
			'alcance' : $('#f_alcance').val()
		});		
	});	
	
	// Comportamiento para Enviar expediente electrónico
	$('#btn_enviar_expe_elec').click(function (e) {
		e.preventDefault();
		iniciarActuacion('expe_elec_enviar_mail', {
			'anio' : $('#f_anio').val(),
			'tipo' : $('#f_tipo').val(),
			'numero' : $('#f_numero').val(),
			'cuerpo' : $('#f_cuerpo').val(),
			'alcance' : $('#f_alcance').val()
		});		
	});	

	// Comportamiento para cargar los giros al expediente
	$('#btn_cargar_giros_expe_elec').click(function (e) {
		e.preventDefault();
		iniciarActuacion('expediente_cargar_giros', {
			'anio' : $('#f_anio').val(),
			'tipo' : $('#f_tipo').val(),
			'numero' : $('#f_numero').val(),
			'cuerpo' : $('#f_cuerpo').val(),
			'alcance' : $('#f_alcance').val()
		});		
	});	

	// Comportamiento para convalidar los giros al expediente
	$('#btn_convalidar_giros_expe_elec').click(function (e) {
		e.preventDefault();
		alert('No implementado.');
		// iniciarActuacion('expediente_convalidar_giros', {
		// 	'anio' : $('#f_anio').val(),
		// 	'tipo' : $('#f_tipo').val(),
		// 	'numero' : $('#f_numero').val(),
		// 	'cuerpo' : $('#f_cuerpo').val(),
		// 	'alcance' : $('#f_alcance').val()
		// });
	});	

	// Comportamiento para ver la carátula actual
	$('#btn_ver_caratula').click(function (e) {
		e.preventDefault();
		// Peticion asíncrona
		$.ajax({
			method: "GET",
			url: "index.php",
			dataType: 'json',
			data: {
				c: "expedienteselec", 
				a: "obtenercaratula", 
				anio: $('#f_anio').val(),
				tipo: $('#f_tipo').val(),
				numero: $('#f_numero').val(),
				cuerpo: $('#f_cuerpo').val(),
				alcance: $('#f_alcance').val()
			}
		}).done(function( respuesta ) {
			if (respuesta.estado == "OK") {
				if (respuesta.data != null) {
					// Se muestra el documento de la carátula del expediente electrónico
					window.open(respuesta.data, '_blank');
				}
			} else if ((respuesta.estado == "ERROR") || (respuesta.estado == "WARNING")) {
				showModal('Aviso', respuesta.mensaje);
				
			}
		}).fail(function () {
			showModal('Error', respuesta.mensaje);
		});
	});
	
	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

	$('#btn_nuevo_expediente').css('display', 'none');
	
	$('#solapa_expedientes_elec').addClass('active');
	
	// No son necesarios en la solapa de Expedientes Electrónicos
	$('#btn_primer_pagina_movil').css('display', 'none');
	$('#btn_primer_pagina').css('display', 'none');
	$('#btn_pagina_anterior').css('display', 'none');
	$('#btn_pagina_siguiente').css('display', 'none');
	$('#btn_ultima_pagina').css('display', 'none');
	
	// Para la versión Móvil, se utilizan para ver el Expediente/Nota/Recomendación Anterior o Siguiente
	$('#btn_expediente_anterior').click(listenerBtnVerExpedienteAnterior);
	$('#btn_expediente_siguiente').click(listenerBtnVerExpedienteSiguiente);

	// Verifico si tengo algun archivo pendiente de descarga
	descargarArchivoPendiente();
});