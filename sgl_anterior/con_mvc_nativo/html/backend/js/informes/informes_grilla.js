/**
 * [generarHtmlBotonAccion description]
 * @param  {[type]} accion      [description]
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-anio="{3}" data-tipo="{4}" data-numero="{5}" data-cuerpo="{6}" data-alcance="{7}" data-orden_giro="{8}" data-orden_informe="{9}" data-fecha_salida_giro="{10}"></span>&nbsp;'.format(
		accion, descripcion, icono, 
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance, fila.orden_giro, fila.orden_informe, $('#f_fecha_salida_giro').val());
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
    buttonAction += generarHtmlBotonAccion('edit', 'Editar Informe', 'pencil', full);
    buttonAction += generarHtmlBotonAccion('delete', 'Eliminar Informe', 'trash', full);
   
	return buttonAction;
};

/**
 * [listenerBotonAccion description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBotonAccion = function (event) {
	event.preventDefault(event); // Prevengo que el link haga lo propio al hacerle click

	// Obtengo la acción
	accion 			= $(this).data('accion');
	f_anio 			= $(this).data('anio');
	f_tipo 			= $(this).data('tipo');
	f_numero 		= $(this).data('numero');
	f_cuerpo 		= $(this).data('cuerpo');
	f_alcance 		= $(this).data('alcance');
	f_orden_giro 	= $(this).data('orden_giro');
	f_orden_informe = $(this).data('orden_informe');
	
	// Se mantiene la fecha de salida del giro, en caso que posea
	f_fecha_salida_giro = $(this).data('fecha_salida_giro');
	
	// Si se desea editar
	if (accion == 'edit') {
		$(location).attr('href','index.php?c=informes&a=edit&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_orden_giro={5}&f_orden_informe={6}&f_fecha_salida_giro={7}'.format(
			f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden_giro, f_orden_informe, f_fecha_salida_giro ));
	}
	else if (accion == 'delete') {
		item = dataTableRef.row($(this).parents('tr')).data();
		eliminarInforme(item);
	}
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'. Informe: {1}-{2}-{3}-{4}-{5} {6} {7}'.format(accion, f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden_giro, f_orden_informe));
};

/**
 * Se vuelve al listado de Giros
 */
var listenerBotonVolver = function () {
    irA('giros', $('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val());
};

/**
 * Se envía el Informe respectivo para su eliminación
 * @param  {[type]} informe [description]
 * @return {[type]}            [description]
 */
function eliminarInforme (informe) {
	showModal('Atenci&oacute;n', '¿Est&aacute; seguro que desea eliminar el Informe del Expediente: {0}-{1}-{2}-{3}-{4}?'.format(informe.anio, informe.tipo, informe.numero, informe.cuerpo, informe.alcance),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					url: "index.php?c=informes&a=delete",
					dataType: 'json', 
					data: JSON.stringify(informe)
				})
				.done(function( respuesta ) {
					if (respuesta.estado == 'OK') {
						if ((respuesta.data != null) && (typeof respuesta.data.informe != 'undefined'))
							// Refrescar grilla
							dataTableRef.ajax.reload(null, false);
						else
							showModal('Error', 'Se esperaba un Informe y no se recibieron resultados.');
					} else 
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

/**
 *  Al volver a renderizarse la grilla
 *  
 * @param  {[type]} settings [description]
 * @return {[type]}          [description]
 */
var callbackDrawCallback = function(settings) {
	// Si existe el expediente
	if ( settings.json.existeExpediente)
		$('#btn_nuevo_informe').prop('disabled', false); // Se habilita el botón 'Nuevo Informe'
	else // si NO existe
		$('#btn_nuevo_informe').prop('disabled', true); // Se deshabilita el botón 'Nuevo Informe'
};

/**
 * Se definen los títulos de la grilla
 */
function definirTitulosGrilla() {

	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2)
		titulos_grilla = new Array(
			'',
			'Orden Informe',
			'Fecha Pedido',
			'Fecha Vuelta',
			'Detalle',
			'Observaciones'
		);
	else
		titulos_grilla = new Array(
			'Orden Informe',
			'Fecha Pedido',
			'Fecha Vuelta',
			'Detalle',
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
			{data: null, className:'text-center', render: callbackRenderAcciones},
			{data: 'orden_informe', className: 'text-right'},
			{data: 'fecha_pedido_informe', className: 'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.fecha_pedido_informe); }},
			{data: 'fecha_vuelta_informe', className: 'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.fecha_vuelta_informe); }},
			{data: 'detalle_informe'},
			{data: 'observaciones_informe', width: '580px'}
	    ];
	else
		columnas_grilla = [
			{data: 'orden_informe', className: 'text-right'},
			{data: 'fecha_pedido_informe', className: 'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.fecha_pedido_informe); }},
			{data: 'fecha_vuelta_informe', className: 'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.fecha_vuelta_informe); }},
			{data: 'detalle_informe'},
			{data: 'observaciones_informe', width: '580px'}
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
	idTabla = '#grillaInformes';
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
			ordering:  false,
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
				{ targets: '_all', className: 'text-left', searchable: false }
			],
			columns: definirColumnasGrilla(),
			drawCallback: callbackDrawCallback
		});

	// Fuerzo el renderizado de la primera pagina para no dejar con basura
	// el paginador con data de otros registros.
	tabla.page('first').draw('page');

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

	// Se genera la grilla
	dataTableRef = setDataTable('index.php?c=informes&a=datagrid&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val()+'&f_orden_giro='+$('#f_orden_giro').val());
	
	// Comportamiento de botones extra
	$('#btn_nuevo_informe').click(function () { 
		$(location).attr('href','index.php?c=informes&a=add&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val()+'&f_orden_giro='+$('#f_orden_giro').val()+'&f_fecha_salida_giro='+$('#f_fecha_salida_giro').val());
	});
    
	// Para volver a la grilla de Giros
	$('#btn_volver').click(function () { 
		$(location).attr('href','index.php?c=giros&a=view&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());
	});

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);
});