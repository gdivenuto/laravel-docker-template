/**
 * [generarHtmlBotonAccion description]
 * @param  {[type]} accion      [description]
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-anio="{3}" data-tipo="{4}" data-numero="{5}" data-cuerpo="{6}" data-alcance="{7}" data-orden_giro="{8}" data-fecha_entrada_giro="{9}" data-fecha_salida_giro="{10}" ></span>&nbsp;&nbsp;'.format(
		accion, descripcion, icono,
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance, fila.orden_giro, fila.fecha_entrada_giro, fila.fecha_salida_giro);
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
		buttonAction += generarHtmlBotonAccion('edit', 'Editar Giro', 'pencil', full);
		buttonAction += generarHtmlBotonAccion('delete', 'Eliminar Giro', 'trash', full);
	}
	return buttonAction;
};

/**
 * 12/02/2021 XXXX
 * Se genera el Html de la flecha para reordenar los Giros
 * @param  {[type]} accion      [description]
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlFlechaAccion(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-anio="{3}" data-tipo="{4}" data-numero="{5}" data-cuerpo="{6}" data-alcance="{7}" data-orden_giro="{8}" data-fecha_entrada_giro="{9}" data-fecha_salida_giro="{10} data-ro_puede_reordenarse_con_giro_anterior={11} data-ro_puede_reordenarse_con_giro_siguiente={12}" ></span>&nbsp;&nbsp;'.format(
		accion, descripcion, icono,
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance, fila.orden_giro,
		fila.fecha_entrada_giro,
		fila.fecha_salida_giro,
		fila.ro_puede_reordenarse_con_giro_anterior,
		fila.ro_puede_reordenarse_con_giro_siguiente);
}

/**
 *  11/02/2021 XXXX
 * Se renderizan las Flechas para reordenar los Giros
 * @param  {[type]} data [description]
 * @param  {[type]} type [description]
 * @param  {[type]} full [description]
 * @param  {[type]} meta [description]
 * @return {[type]}      [description]
 */
var callbackRenderFlechas = function (data, type, full, meta) {
	buttonAction = '';

	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {

		//if (full.fecha_entrada_giro == null && full.orden_giro > 1)
		if (full.ro_puede_reordenarse_con_giro_anterior == 1)
			buttonAction += generarHtmlFlechaAccion('subir', 'Subir Giro', 'arrow-up', full);

		//if (full.fecha_entrada_giro == null)
		if (full.ro_puede_reordenarse_con_giro_siguiente == 1)
			buttonAction += generarHtmlFlechaAccion('bajar', 'Bajar Giro', 'arrow-down', full);
	}

	return buttonAction;
};

/**
 * Para visualizar los Informes del giro respectivo
 * @param  {[type]} data [description]
 * @param  {[type]} type [description]
 * @param  {[type]} full [description]
 * @param  {[type]} meta [description]
 * @return {[type]}      [description]
 */
var callbackRenderListarInformes = function (data, type, full, meta) {
	buttonAction = '';

	// Si posee fecha de entrada
	if ( full.fecha_entrada_giro != null )
		// Se muestra el ícono para visualizar la grilla de Informes
    	buttonAction += generarHtmlBotonAccion('listar_informes', 'Ver Informes', 'file', full);

	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

/**
 * [callbackRenderCantidadDiasComision description]
 * @param  {[type]} data [description]
 * @param  {[type]} type [description]
 * @param  {[type]} full [description]
 * @param  {[type]} meta [description]
 * @return {[type]}      [description]
 */
var callbackRenderCantidadDiasComision = function (data, type, full, meta) {
	if ( full.cantidad_dias_en_comision == -1 )
		return '';
	else {
		if ( full.cantidad_dias_en_comision >= 0 && full.cantidad_dias_en_comision <= 105 )
	        css_resaltado = 'resaltado-ok'; // COLOR VERDE
	    else if ( full.cantidad_dias_en_comision >= 106 && full.cantidad_dias_en_comision <= 119 )
	        css_resaltado = 'resaltado-advertencia'; // COLOR AMARILLO
	    else
	        css_resaltado = 'resaltado-alerta'; // COLOR ROJO

		return '<span class="'+css_resaltado+' resaltado_espacio resaltado_para_giros">'+full.cantidad_dias_en_comision+'</span>';
	}
};

/**
 * [listenerBotonAccion description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBotonAccion = function (event) {
	event.preventDefault(event); // Prevengo que el link haga lo propio al hacerle click

	// Obtengo la acción
	accion 				 = $(this).data('accion');
	f_anio 				 = $(this).data('anio');
	f_tipo 				 = $(this).data('tipo');
	f_numero 			 = $(this).data('numero');
	f_cuerpo 			 = $(this).data('cuerpo');
	f_alcance 			 = $(this).data('alcance');
	f_orden_giro 		 = $(this).data('orden_giro');
	f_fecha_entrada_giro = $(this).data('fecha_entrada_giro');
	f_fecha_salida_giro  = $(this).data('fecha_salida_giro');

	// Si se desea editar
	if (accion == 'edit')
		$(location).attr('href','index.php?c=giros&a=edit&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_orden_giro={5}'.format(
			f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden_giro ));
	// Si se desea eliminar
	else if (accion == 'delete') {
		item = dataTableRef.row($(this).parents('tr')).data();
		eliminarGiro(item);
	}
	// Si se desea subir por el Orden de Giro
	else if (accion == 'subir') {
		// Envio la peticion al controlador
		$.ajax({
			method: "POST",
			url: "index.php?c=giros&a=subirordengiro",
			data: {
				f_anio: f_anio,
				f_tipo: f_tipo,
				f_numero: f_numero,
				f_cuerpo: f_cuerpo,
				f_alcance: f_alcance,
				f_orden_giro: f_orden_giro
			}
		})
		.done(function( respuesta ) {
			// Se recarga la grilla de Giros
			dataTableRef.ajax.reload(null, false);
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {
			showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
		});
	}
	// Si se desea bajar por el Orden de Giro
	else if (accion == 'bajar') {
		// Envio la peticion al controlador
		$.ajax({
			method: "POST",
			url: "index.php?c=giros&a=bajarordengiro",
			data: {
				f_anio: f_anio,
				f_tipo: f_tipo,
				f_numero: f_numero,
				f_cuerpo: f_cuerpo,
				f_alcance: f_alcance,
				f_orden_giro: f_orden_giro
			}
		})
		.done(function( respuesta ) {
			// Se recarga la grilla de Giros
			dataTableRef.ajax.reload(null, false);
		})
		.fail(function( jqXHR, textStatus, errorThrown ) {
			showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
		});
	}
	// Si se desean ver los Informes
	else if (accion == 'listar_informes') {
		// Si posee fecha de entrada
		if ( f_fecha_entrada_giro != null )
			// Se visualiza la grilla de Informes para dicho Giro
			$(location).attr('href','index.php?c=informes&a=view&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_orden_giro={5}&f_fecha_salida_giro={6}'.format(
				f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden_giro, f_fecha_salida_giro));
	}
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'. Giro: {1}-{2}-{3}-{4}-{5} {6}'.format(accion, f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden_giro));
};

/**
 * Se envía el giro respectivo para su eliminación
 * @param  {[type]} giro [description]
 * @return {[type]}            [description]
 */
function eliminarGiro (giro) {
	showModal('Atenci&oacute;n', '¿Est&aacute; seguro que desea eliminar el Giro del Expediente: {0}-{1}-{2}-{3}-{4}?'.format(giro.anio, giro.tipo, giro.numero, giro.cuerpo, giro.alcance),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					url: "index.php?c=giros&a=delete",
					dataType: 'json',
					data: JSON.stringify(giro)
				})
				.done(function( respuesta ) {
					if (respuesta.estado == 'OK') {
						if ((respuesta.data != null) && (typeof respuesta.data.giro != 'undefined'))
							// Refrescar grilla
							dataTableRef.ajax.reload(null, false);
						else
							showModal('Error', 'Se esperaba un Giro y no se recibieron resultados.');
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
	// Se muestra la vista previa del expediente del buscador al terminar de dibujar el datatable
	// @ expedientes-busquedasimple-common.js
	actualizarVistaSiExisteExpediente(settings.json.existeExpediente);

	// Si existe el expediente
	if ( settings.json.existeExpediente)
		$('#btn_nuevo_giro').prop('disabled', false); // Se habilita el botón 'Nuevo Giro'
	else // si NO existe
		$('#btn_nuevo_giro').prop('disabled', true); // Se deshabilita el botón 'Nuevo Giro'
};

/**
 * Se definen los títulos de la grilla
 */
function definirTitulosGrilla() {

	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2)
		titulos_grilla = new Array(
									'Acciones',
									'Orden',
									'Tipo',
									'C&oacute;digo',
									'Comisi&oacute;n',
									'Fecha Entrada',
									'Fecha Salida',
									'Dictamen',
									'Observaciones',
									'Informes', // para ver los Informes del giro respectivo
									//'Cant. D&iacute;as', // Cantidad de días en Comisión (con color)
									'Ordenar Giros'
								  );
	else
		titulos_grilla = new Array(
									'Orden',
									'Tipo',
									'C&oacute;digo',
									'Comisi&oacute;n',
									'Fecha Entrada',
									'Fecha Salida',
									'Dictamen',
									'Observaciones',
									'Informes'//, // para ver los Informes del giro respectivo
									//'Cant. D&iacute;as' // Cantidad de días en Comisión (con color)
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
							{data: 'orden_giro'},
							{data: 'comision_tipo'},
							{data: 'comision_codigo'},
							{data: 'ro_descripcion_grp'},
							{data: 'fecha_entrada_giro', className: 'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.fecha_entrada_giro); }},
							{data: 'fecha_salida_giro', className: 'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.fecha_salida_giro); }},
							{data: 'dictamen_giro'},
							{data: 'observaciones_giro'},
							{data: null, className: 'text-center', render: callbackRenderListarInformes}, // columna para visualizar sus Informes, en caso que posea
							//{data: null, className: 'text-right', render: callbackRenderCantidadDiasComision}, // Cantidad de días en la comisión
					        {data: null, className:'text-center', render: callbackRenderFlechas}
					      ];
	else
		columnas_grilla = [
							{data: 'orden_giro'},
							{data: 'comision_tipo'},
							{data: 'comision_codigo'},
							{data: 'ro_descripcion_grp'},
							{data: 'fecha_entrada_giro', className: 'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.fecha_entrada_giro); }},
							{data: 'fecha_salida_giro', className: 'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.fecha_salida_giro); }},
							{data: 'dictamen_giro'},
							{data: 'observaciones_giro'},
							{data: null, className: 'text-center', render: callbackRenderListarInformes}, // columna para visualizar sus Informes, en caso que posea
							//{data: null, className: 'text-right', render: callbackRenderCantidadDiasComision} // Cantidad de días en la comisión
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
	idTabla = '#grillaGiros';
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
	        // Cantidad de registros por página
            pageLength: 50,// NO HACE CASO, se debe redefinir en el controlador !!!
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
            dom: 't', // Definimos la 't'abla
			language: { url: '../librerias/datatables/localisation/es_AR.json' },
			columnDefs: [
				{ targets: '_all', searchable: false, className:'text-left' }
			],
			columns: definirColumnasGrilla(),
			drawCallback: callbackDrawCallback
		});

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
	controlador_activo = 'giros';

	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	asignarValidatorExpresionesRegulares();

	// Genero la tabla
	dataTableRef = setDataTable('index.php?c=giros&a=datagrid&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());

	// Inicializo el formulario
	setearComportamientoSolapas(); // @ expedientes-busquedasimple-common.js
	setearComportamientoBotonesNavegacion(); // @ expedientes-busquedasimple-common.js
	setearComportamientoValidate(); // @ expedientes-busquedasimple-common.js

	// Comportamiento de botones extra
	$('#btn_nuevo_giro').click(function () {
		$(location).attr('href','index.php?c=giros&a=add&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());
	});

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

	$('#btn_nuevo_expediente').css('display', 'none');

	$('#solapa_giros').addClass('active');

	// 09/09/2020 XXXX
	// ---------------------
	// No son necesarios en la solapa de Giros
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
