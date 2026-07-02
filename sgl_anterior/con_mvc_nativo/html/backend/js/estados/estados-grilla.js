/**
 * [generarHtmlBotonAccion description]
 * @param  {[type]} accion      [description]
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-anio="{3}" data-tipo="{4}" data-numero="{5}" data-cuerpo="{6}" data-alcance="{7}" data-fecha_estado="{8}" data-orden_estado="{9}"></span>&nbsp;&nbsp;'.format(
		accion, descripcion, icono,
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance, fila.fecha_estado, fila.orden_estado);
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
    	buttonAction += generarHtmlBotonAccion('edit', 'Editar Estado', 'pencil', full);
    	buttonAction += generarHtmlBotonAccion('delete', 'Eliminar Estado', 'trash', full);
    }

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
	accion = $(this).data('accion');
	f_anio = $(this).data('anio');
	f_tipo = $(this).data('tipo');
	f_numero = $(this).data('numero');
	f_cuerpo = $(this).data('cuerpo');
	f_alcance = $(this).data('alcance');
	f_fecha_estado = $(this).data('fecha_estado');
	f_orden_estado = $(this).data('orden_estado');

	if (accion == 'edit')
		$(location).attr('href','index.php?c=estados&a=edit&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_fecha_estado={5}&f_orden_estado={6}'.format(
			f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_fecha_estado, f_orden_estado ));
	else if (accion == 'delete') {
		item = dataTableRef.row($(this).parents('tr')).data();
		eliminarEstado(item);
	}
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'. Estado del Expediente: {1}-{2}-{3}-{4} Orden {5}'.format(accion, f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden_estado));
};

/**
 * Se envía el Estado respectivo para su eliminación
 * @param  {[type]} estado [description]
 * @return {[type]}            [description]
 */
function eliminarEstado (estado) {
	showModal('Atenci&oacute;n', '¿Est&aacute; seguro que desea eliminar el Estado del Expediente: {0}-{1}-{2}-{3}-{4}?'.format(estado.anio, estado.tipo, estado.numero, estado.cuerpo, estado.alcance),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					url: "index.php?c=estados&a=delete",
					dataType: 'json',
					data: JSON.stringify(estado)
				})
				.done(function( respuesta ) {
					if (respuesta.estado == 'OK') {
						if ((respuesta.data != null) && (typeof respuesta.data.estado != 'undefined'))
							// Refrescar grilla
							dataTableRef.ajax.reload(null, false);
						else
							showModal('Error', 'Se esperaba un Estado y no se recibieron resultados.');
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
 * Se envían los datos de la clave del Estado respectivo para su eliminación
 * @param  {[type]} f_anio           [description]
 * @param  {[type]} f_tipo           [description]
 * @param  {[type]} f_numero         [description]
 * @param  {[type]} f_cuerpo         [description]
 * @param  {[type]} f_alcance        [description]
 * @param  {[type]} f_orden_estado [description]
 * @param  {[type]} f_fecha_estado [description]
 */
function eliminar (f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden_estado, f_fecha_estado) {
	showModal('Atenci&oacute;n', '¿Desea eliminar el Estado del Expediente: {0}-{1}-{2}-{3}-{4}, de Orden {5} y fecha el {6}?'.format(f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden_estado, formatearFechaConBarras(f_fecha_estado)),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
			        method: "GET",
			        url: "index.php?c=estados&a=delete",
			        data : { f_anio : f_anio,
			        		 f_tipo : f_tipo,
			        		 f_numero : f_numero,
			        		 f_cuerpo : f_cuerpo,
			        		 f_alcance : f_alcance,
			        		 f_orden_estado : f_orden_estado,
			        		 f_fecha_estado : f_fecha_estado
			        	   }
			    })
			    .done(function() {
			    	dataTableRef.ajax.reload(null, false);
			    })
			    .fail(function( jqXHR, textStatus, errorThrown ) {
			        showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
			    });
			}
		},
		btn_no: { class: 'btn-primary' }
	});
};

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
		$('#btn_nuevo_estado').prop('disabled', false); // Se habilita el botón 'Nuevo Estado'
	else // si NO existe
		$('#btn_nuevo_estado').prop('disabled', true); // Se deshabilita el botón 'Nuevo Estado'
};

/**
 * Se definen los títulos de la grilla
 */
function definirTitulosGrilla() {

	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2)
		titulos_grilla = new Array(
									'Acciones',
									'Fecha',
									'Orden',
									'C&oacute;digo',
									'Estado',
									'Observaciones'
								  );
	else
		titulos_grilla = new Array(
									'Fecha',
									'Orden',
									'C&oacute;digo',
									'Estado',
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
							{data: null, width:'20px', className:'text-center', render: callbackRenderAcciones},
							{data: 'fecha_estado', width:'50px', className:'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.fecha_estado); }},
							{data: 'orden_estado', width:'50px'},
							{data: 'id_codestado', width:'50px'},
							{data: 'ro_nombre_estado'},
							{data: 'observaciones_estado', width: '300px'}
					      ];
	else
		columnas_grilla = [
							{data: 'fecha_estado', width:'50px', className:'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.fecha_estado); }},
							{data: 'orden_estado', width:'50px'},
							{data: 'id_codestado', width:'50px'},
							{data: 'ro_nombre_estado'},
							{data: 'observaciones_estado', width: '300px'}
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
	idTabla = '#grillaEstados';
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
	controlador_activo = 'estados';

	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	asignarValidatorExpresionesRegulares();

	// Genero la tabla
	dataTableRef = setDataTable('index.php?c=estados&a=datagrid&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());

	// Inicializo el formulario
	setearComportamientoSolapas(); // @ expedientes-busquedasimple-common.js
	setearComportamientoBotonesNavegacion(); // @ expedientes-busquedasimple-common.js
	setearComportamientoValidate(); // @ expedientes-busquedasimple-common.js

	// Comportamiento de botones extra
	$('#btn_nuevo_estado').click(function () {
		$(location).attr('href','index.php?c=estados&a=add&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());
	});

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

	$('#btn_nuevo_expediente').css('display', 'none');

	$('#solapa_estados').addClass('active');

	// 09/09/2020 XXXX
	// ---------------------
	// No son necesarios en la solapa de Estados
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
