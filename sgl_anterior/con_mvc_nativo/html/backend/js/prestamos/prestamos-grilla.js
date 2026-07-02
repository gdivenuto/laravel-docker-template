/**
 * [generarHtmlBotonAccion description]
 * @param  {[type]} accion      [description]
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-anio="{3}" data-tipo="{4}" data-numero="{5}" data-cuerpo="{6}" data-alcance="{7}" data-digito="{8}" data-cuerpoalcance="{9}" data-anexoalcance="{10}" data-cuerpoanexoalcance="{11}" data-anexo="{12}" data-cuerpoanexo="{13}" data-fecha_solicitud="{14}"></span>&nbsp;&nbsp;'.format(
		accion, descripcion, icono,
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance,
		fila.digito, fila.cuerpoalcance, fila.anexoalcance,
		fila.cuerpoanexoalcance, fila.anexo, fila.cuerpoanexo,
		fila.fecha_solicitud);
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

	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {
    	buttonAction += generarHtmlBotonAccion('edit', 'Editar Pr&eacute;stamo', 'pencil', full);
	    // Se permite Eliminar si el estado es: Solicitado, Devuelto ó Anulado
	    if (full.estado == 'S' || full.estado == 'D' || full.estado == 'A')
	    	buttonAction += generarHtmlBotonAccion('delete', 'Eliminar Pr&eacute;stamo', 'trash', full);
    }

	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

var callbackRenderAccionFechaSolicitud = function (data, type, full, meta) {
	// Por defecto
	var valor = '---';
	// Si ya posee la fecha de Solicitud
	if (full.fecha_solicitud != null)
		// Se formatea la fecha para mostrarla, retirando la parte de la hora
		valor = formatearFechaConBarras(full.fecha_solicitud.substr(0,10));

	return valor;
};

var callbackRenderAccionFechaPrestamo = function (data, type, full, meta) {
	// Por defecto
	var valor = '---';
	// Si ya posee la fecha de Prestado
	if (full.fecha_prestado != null)
		// Se formatea la fecha para mostrarla, retirando la parte de la hora
		valor = formatearFechaConBarras(full.fecha_prestado.substr(0,10));
	// Si su estado siguiente es Prestado
	else if ($.inArray('P', full.ro_estados_siguientes) >= 0) {
		// Se arma el botón para PRESTAR el préstamo
		valor = '<a href="index.php?c=prestamos&a=editprestado&grilla=solapa&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud={11}" class="btn btn-primary btn-sm" role="button"><span class="glyphicon glyphicon-edit"></span>&nbsp;Prestar</a>'.format(
			full.anio, full.tipo, full.numero, full.cuerpo, full.alcance,
			full.digito, full.cuerpoalcance, full.anexoalcance,
			full.cuerpoanexoalcance, full.anexo, full.cuerpoanexo,
			full.fecha_solicitud
		);
	}

	return valor;
};

var callbackRenderAccionFechaDevolucion = function (data, type, full, meta) {
	// Por defecto
	var valor = '---';
	// Si ya posee la fecha de Devuelto
	if (full.fecha_devuelto != null)
		// Se formatea la fecha para mostrarla, retirando la parte de la hora
		valor = formatearFechaConBarras(full.fecha_devuelto.substr(0,10));
	// Si su estado siguiente es Devuelto
	else if ($.inArray('D', full.ro_estados_siguientes) >= 0)
		// Se arma el botón para DEVOLVER el préstamo
		valor = '<a href="index.php?c=prestamos&a=editdevuelto&grilla=solapa&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud={11}" class="btn btn-success btn-sm" role="button"><span class="glyphicon glyphicon-edit"></span>&nbsp;Devolver</a>'.format(
			full.anio, full.tipo, full.numero, full.cuerpo, full.alcance,
			full.digito, full.cuerpoalcance, full.anexoalcance,
			full.cuerpoanexoalcance, full.anexo, full.cuerpoanexo,
			full.fecha_solicitud
		);

	return valor;
};

var callbackRenderAccionFechaAnulado = function (data, type, full, meta) {
	// Por defecto
	var valor = '---';
	// Si ya posee la fecha de Anulado
	if (full.fecha_anulado != null)
		// Se formatea la fecha para mostrarla, retirando la parte de la hora
		valor = formatearFechaConBarras(full.fecha_anulado.substr(0,10));
	// Si su estado siguiente es Anulado
	else if ($.inArray('A', full.ro_estados_siguientes) >= 0)
		// Se arma el botón para ANULAR el préstamo
		valor = '<a href="index.php?c=prestamos&a=editanulado&grilla=solapa&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud={11}" class="btn btn-danger btn-sm" role="button"><span class="glyphicon glyphicon-edit"></span>&nbsp;Anular</a>'.format(
			full.anio, full.tipo, full.numero, full.cuerpo, full.alcance,
			full.digito, full.cuerpoalcance, full.anexoalcance,
			full.cuerpoanexoalcance, full.anexo, full.cuerpoanexo,
			full.fecha_solicitud
		);

	return valor;
};

var callbackRenderEstado = function (data, type, full, meta) {
	var cadena;

	switch (full.estado) {
		case 'S' : // Solicitado al HCD
			cadena = "<span style='padding:5px;display:block;"+mostrarColorSegunEstado(full.estado)+"'>Solicitado al HCD</span>";
			break;

		case 'P' : // Prestado desde el HCD
			cadena = "<span style='padding:5px;display:block;"+mostrarColorSegunEstado(full.estado)+"'>Prestado desde el HCD</span>";
			break;

		case 'D' : // Devuelto al HCD
			cadena = "<span style='padding:5px;display:block;"+mostrarColorSegunEstado(full.estado)+"'>Devuelto al HCD</span>";
			break;

		case 'A' : // Prestamo anulado
			cadena = "<span style='padding:5px;display:block;"+mostrarColorSegunEstado(full.estado)+"'>Pr&eacute;stamo anulado</span>";
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
	f_fecha_solicitud 	 = $(this).data('fecha_solicitud');

	// Si se desea editar la información del préstamo
	if (accion == 'edit')
		$(location).attr('href','index.php?c=prestamos&a=editinfo&f_grilla=solapa&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud={11}'.format(
			f_anio, f_tipo, f_numero, f_cuerpo, f_alcance,
			f_digito, f_cuerpoalcance, f_anexoalcance, f_cuerpoanexoalcance, f_anexo, f_cuerpoanexo,
			f_fecha_solicitud)
		);
	// Si se desea eliminar un préstamo
	else if (accion == 'delete') {
		item = dataTableRef.row($(this).parents('tr')).data();
		eliminarPrestamo(item);
	} else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'. Prestamo: {1}-{2}-{3}-{4}-{5}-{6}-{7}-{8}-{9}-{10}-{11}-{12}'.format(
			accion,
			f_anio, f_tipo, f_numero, f_cuerpo, f_alcance,
			f_digito, f_cuerpoalcance, f_anexoalcance, f_cuerpoanexoalcance, f_anexo, f_cuerpoanexo,
			f_fecha_solicitud)
		);
};

/**
 * Se envía el Préstamo respectivo para su eliminación
 * @param  {[type]} prestamo [description]
 * @return {[type]}            [description]
 */
function eliminarPrestamo(prestamo) {
	showModal('Atenci&oacute;n', "¿Est&aacute; seguro que desea eliminar el Pr&eacute;stamo del expediente: {0}-{1}-{2}-{3}-{4}?".format(prestamo.anio, prestamo.tipo, prestamo.numero, prestamo.cuerpo, prestamo.alcance),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					url: "index.php?c=prestamos&a=delete&f_grilla=solapa",
					dataType: 'json',
					data: JSON.stringify(prestamo)
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
		case 'S':
			color_fondo_y_texto = "background-color: #FCF8E3;color: #C09853;";// AMARILLO PASTEL
			break;
		case 'P':
			color_fondo_y_texto = "background-color: #F2DEDE;color: #B94A48;";// ROJO PASTEL
			break;
		case 'D':
			color_fondo_y_texto = "background-color: #DFF0D8;color: #468847;";// VERDE PASTEL
			break;
		case 'A':
			color_fondo_y_texto = "background-color: #D9D9D9;color: #3A3A3A;";// GRIS PASTEL
			break;
	}

	return color_fondo_y_texto;
}
/**
 *  Al volver a renderizarse la grilla
 *
 * @param  {[type]} settings [description]
 * @return {[type]}          [description]
 */
var callbackDrawCallback = function(settings) {
	// Se muestra la vista previa del expediente del buscador al terminar de dibujar el datatable
	// @ expedientes-busquedasimple-common.js
	actualizarVistaSiExisteExpediente(settings.json.existeExpediente);

	// Si existe el expediente
	if ( settings.json.existeExpediente)
		$('#btn_nuevo_prestamo').prop('disabled', false); // Se habilita el botón 'Nuevo Prestamo'
	else // si NO existe
		$('#btn_nuevo_prestamo').prop('disabled', true); // Se deshabilita el botón 'Nuevo Prestamo'
};

/**
 * Se definen los títulos de la grilla
 */
function definirTitulosGrilla() {

	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2)
		titulos_grilla = new Array(
									'Acciones',
									'Solicitante',
									'Fecha Solicitud',
									'Fecha Pr&eacute;stamo',
									'Fecha Devoluci&oacute;n',
									'Fecha Anulado',
									'Estado',
									'Nro.',
									'Folio',
									'Observaciones'
								  );
	else
		titulos_grilla = new Array(
									'Solicitante',
									'Fecha Solicitud',
									'Fecha Pr&eacute;stamo',
									'Fecha Devoluci&oacute;n',
									'Fecha Anulado',
									'Estado',
									'Nro.',
									'Folio',
									'Observaciones'
								  );

	return titulos_grilla;
}

/**
 * Se definen las columnas de la grilla
 */
function definirColumnasGrilla() {

	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2)

		columnas_grilla = [
							{data: null, className: 'text-center', render: callbackRenderAcciones},
							{data: 'ro_solicitante_nombre'}, // Solicitante
							{data: null, className: 'text-center', render: callbackRenderAccionFechaSolicitud},	 // Fecha Solicitud
							{data: null, className: 'text-center', render: callbackRenderAccionFechaPrestamo},	 // Fecha Préstamo
							{data: null, className: 'text-center', render: callbackRenderAccionFechaDevolucion}, // Fecha Devolución
							{data: null, className: 'text-center', render: callbackRenderAccionFechaAnulado},	 // Fecha Anulado
							{data: null, render: callbackRenderEstado}, // Estado
							{data: 'libro_numero'}, // Libro Número
							{data: 'libro_folio'}, // Libro Folio
							{data: 'observaciones_prestamo'}
					      ];
	else
		columnas_grilla = [
							{data: 'ro_solicitante_nombre'}, // Solicitante
							{data: null, className: 'text-center', render: callbackRenderAccionFechaSolicitud},	 // Fecha Solicitud
							{data: null, className: 'text-center', render: callbackRenderAccionFechaPrestamo},	 // Fecha Préstamo
							{data: null, className: 'text-center', render: callbackRenderAccionFechaDevolucion}, // Fecha Devolución
							{data: null, className: 'text-center', render: callbackRenderAccionFechaAnulado},	 // Fecha Anulado
							{data: null, render: callbackRenderEstado}, // Estado
							{data: 'libro_numero'}, // Libro Número
							{data: 'libro_folio'}, // Libro Folio
							{data: 'observaciones_prestamo'}
						  ];

	return columnas_grilla;
}

/**
 * [setDataTable description]
 * @param {[type]} ajaxUrl [description]
 * @return DataTable
 */
function setDataTable(ajaxUrl) {
	// Borro y creo la grilla (tabla)
	idTabla = '#grillaPrestamos';
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
			language: { url: '../librerias/datatables/localisation/es_AR.json' },
			columnDefs: [
				{ className: 'text-left', targets: '_all', searchable: false }
			],
			columns: definirColumnasGrilla(),
			drawCallback: callbackDrawCallback
		});

	return tabla;
};

/**
 * Entry Point de jQuery
 */
$(document).ready(function() {
	controlador_activo = 'prestamos';

	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	asignarValidatorExpresionesRegulares();

	// Genero la tabla
	dataTableRef = setDataTable('index.php?c=prestamos&a=datagrid&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());

	// Inicializo el formulario
	setearComportamientoSolapas(); // @ expedientes-busquedasimple-common.js
	setearComportamientoBotonesNavegacion(); // @ expedientes-busquedasimple-common.js
	setearComportamientoValidate(); // @ expedientes-busquedasimple-common.js

	// Comportamiento de botones extra
	$('#btn_nuevo_prestamo').click(function () {
		$(location).attr('href','index.php?c=prestamos&a=add&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());
	});

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

	$('#btn_nuevo_expediente').css('display', 'none');

	$('#solapa_prestamos').addClass('active');

	// 09/09/2020 XXXX
	// ---------------------
	// No son necesarios en la solapa de Préstamos
	$('#btn_primer_pagina_movil').css('display', 'none');
	$('#btn_primer_pagina').css('display', 'none');
	$('#btn_pagina_anterior').css('display', 'none');
	$('#btn_pagina_siguiente').css('display', 'none');
	$('#btn_ultima_pagina').css('display', 'none');

	// Para la versión Móvil, se utilizan para ver el Expediente/Nota/Recomendación Anterior o Siguiente
	$('#btn_expediente_anterior').click(listenerBtnVerExpedienteAnterior);
	$('#btn_expediente_siguiente').click(listenerBtnVerExpedienteSiguiente);

	// 10/09/2020 XXXX
	// Se verifica la existencia del siguiente Expediente/Nota/Recomendación
	//verificarBotonSiguienteExpediente();
});
