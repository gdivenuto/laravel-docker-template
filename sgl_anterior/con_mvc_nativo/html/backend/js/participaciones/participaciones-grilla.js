/**
 * Se renderiza un botón para una acción específica, con su descripción e ícono respectivos
 * @param  {[type]} accion      [description]
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-anio="{3}" data-tipo="{4}" data-numero="{5}" data-cuerpo="{6}" data-alcance="{7}" data-numero_participacion="{8}" ></span>&nbsp;'.format(
		accion, descripcion, icono, 
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance, fila.numero_participacion);
}

/**
 * Se renderiza el botón para eliminar el documento original de un expediente determinado
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonAccionDocumento(fila) {
	return '<span class="btn-accion-contenido-documento glyphicon glyphicon-trash" data-accion="eliminarDocOriginal" title="Eliminar documento original" data-archivo="{0}" data-url="{1}" data-anio="{2}" data-tipo="{3}" data-numero="{4}" data-cuerpo="{5}" data-alcance="{6}" ></span>&nbsp;&nbsp;'.format(
		fila.archivo, fila.url, 
		fila.expediente.anio, fila.expediente.tipo, fila.expediente.numero, fila.expediente.cuerpo, fila.expediente.alcance);
}

/**
 * Se generan los botones para editar, eliminar y el que informa si se encuentra el proyecto Promulgado o Vetado 
 * @param  {[type]} data [description]
 * @param  {[type]} type [description]
 * @param  {[type]} full [description]
 * @param  {[type]} meta [description]
 * @return {[type]}      [description]
 */
var callbackRenderAcciones = function (data, type, full, meta) {
	buttonAction = '';

	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {
    	buttonAction += generarHtmlBotonAccion('delete', 'Eliminar Participaci&oacute;n', 'trash', full);
    }
    
	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

var callbackRenderInstitucion = function (data, type, full, meta) {

	if (full.institucion_nombre != null && full.institucion_domicilio != null) {
		return '<span style="white-space: nowrap">{0} - {1}</span>'.format(full.institucion_nombre, full.institucion_domicilio);	
	} else {
		return '&nbsp';
	}
}

var callbackRenderFicha = function (data, type, full, meta) {
	//console.log(full);
	var url_ficha = '';
    url_ficha += url_base+'administracion/abms/index.php?controlador=participaciones&accion=generarFicha';
    url_ficha += '&anio='+full.anio;
    url_ficha += '&tipo='+full.tipo;
    url_ficha += '&numero='+full.numero;
    url_ficha += '&cuerpo='+full.cuerpo;
    url_ficha += '&alcance='+full.alcance;
    url_ficha += '&numero_participacion='+full.numero_participacion;
     
	return '<a href="'+url_ficha+'" target="_blank">PDF</a>';
}

/**
 * Se visualiza la modal para mostrar la foto
 */
function mostrarModalFichaParticipacion() {
    // Se muestra la modal
    $('#modalFichaParticipacion').modal('show');
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
	
	f_anio = $(this).data('anio');
	f_tipo = $(this).data('tipo');
	f_numero = $(this).data('numero');
	f_cuerpo = $(this).data('cuerpo');
	f_alcance = $(this).data('alcance');
	f_numero_participacion = $(this).data('numero_participacion');
	
	if (accion == 'delete') {
		item = dataTableRef.row($(this).parents('tr')).data();
		eliminarParticipacion(item);
	}
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'. Participaci&oacute;n del Expediente: {1}-{2}-{3}-{4}-{5} Numero: {6}'.format(accion, f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_numero_participacion));
};

/**
 * Se envía la participacion respectiva para su eliminación
 * @param  {[type]} participacion [description]
 * @return {[type]}            [description]
 */
function eliminarParticipacion(participacion) {
	showModal('Atenci&oacute;n', '¿Est&aacute; seguro que desea eliminar la Participaci&oacute;n del Expediente: {0}-{1}-{2}-{3}-{4}?'.format(participacion.anio, participacion.tipo, participacion.numero, participacion.cuerpo, participacion.alcance),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					url: "index.php?c=participaciones&a=delete",
					dataType: 'json', 
					data: JSON.stringify(participacion)
				})
				.done(function( respuesta ) {
					if (respuesta.estado == 'OK') {
						if ((respuesta.data != null) && (typeof respuesta.data.participacion != 'undefined'))
							// Refrescar grilla
							dataTableRef.ajax.reload(null, false);
						else
							showModal('Error', 'Se esperaba una Participaci&oacute;n y no se recibieron resultados.');
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

	// Si el expediente NO se ha habilitado en las Participaciones aún
	if ( settings.json.estaHabilitado == 0) {
		$('#btn_habilitar_participacion').css('display', 'inline-block'); // Se muestra el botón para Habilitarlo
		$('#btn_examinar_propuesta').css('display', 'none'); // Se oculta el botón para Cargar la propuesta
		$('#enlace_propuesta').css('display', 'none'); // Se oculta el enlace de la propuesta (no existe)
		$('#btn_informe_participaciones').css('display', 'none'); // Se oculta el botón para generar el informe
		$('#btn_moderar').css('display', 'none'); // Se oculta el botón para moderar

	} // Si el expediente se ha habilitado y NO se ha cargado su Propuesta aún
	else if ( settings.json.estaHabilitado == 1 && settings.json.urlPropuesta == null) {
		$('#btn_habilitar_participacion').css('display', 'none'); // Se oculta el botón para Habilitarlo
		$('#btn_examinar_propuesta').css('display', 'inline-block'); // Se muestra el botón para Cargar la propuesta
		$('#enlace_propuesta').css('display', 'none'); // Se oculta el enlace de la propuesta (no existe)
		$('#btn_informe_participaciones').css('display', 'none'); // Se oculta el botón para generar el informe
		$('#btn_moderar').css('display', 'inline'); // Se muestra el botón para moderar

	} // Si el expediente se ha habilitado y ya posee su Propuesta
	else if ( settings.json.estaHabilitado == 1 && settings.json.urlPropuesta != null) {
		$('#btn_habilitar_participacion').css('display', 'none'); // Se oculta el botón para Habilitarlo
		$('#btn_examinar_propuesta').css('display', 'none'); // Se oculta el botón para Cargar la propuesta
		$('#btn_moderar').css('display', 'inline'); // Se muestra el botón para generar el informe
				
		// Se carga la URL de la propuesta (pdf)
		$('#enlace_propuesta').prop('href', settings.json.urlPropuesta);
		$('#enlace_propuesta').css('display', 'inline'); // Se muestra el enlace de la propuesta

		var url_informe = '';
	    url_informe += url_base+'administracion/abms/index.php?controlador=participaciones&accion=generarInforme';
	    url_informe += '&anio='+$('#f_anio').val();
	    url_informe += '&tipo='+$('#f_tipo').val();
	    url_informe += '&numero='+$('#f_numero').val();
	    url_informe += '&cuerpo='+$('#f_cuerpo').val();
	    url_informe += '&alcance='+$('#f_alcance').val();
	    url_informe += '&estado=1';
	    
	    $('#btn_informe_participaciones').prop('href', url_informe);
	    $('#btn_informe_participaciones').css('display', 'inline'); // Se muestra el botón para generar el informe
	}
};

/**
 * Se configura la grilla del DataTable
 * 
 * @param {[type]} ajaxUrl [description]
 * @return DataTable
 */
function setDataTable(ajaxUrl) {
	// Borro y creo la grilla (tabla)
	idTabla = '#grillaParticipaciones';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(
		generarGrillaHtml(idTabla, 
			new Array( // Títulos de cada columna de la grilla
				'Acciones',
				'Nro',
				'Fecha',
				'Participante',
				'Tel&eacute;fono',
				'Mail',
				'Instituci&oacute;n',
				'Ficha'
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
			stateSave: false, // por defecto es true
			processing: true, // para que muestre "Cargando...", en modo local no se llega a visualizar
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
				{ targets: '_all', searchable: false, className: 'text-left' }
			],
			columns: [ 
				{data: null, className:'text-center', render: callbackRenderAcciones},
				{data: 'numero_participacion'},
				{data: 'fecha', className:'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.fecha); }},
				{data: 'apellidoynombre'},
				{data: 'telefono'},
				{data: 'mail'},
				{data: null, render: callbackRenderInstitucion},
				{data: null, className:'text-center', render: callbackRenderFicha}
			],
			drawCallback: callbackDrawCallback
		});

	return tabla;
}

/**
 * Variables globales
 */
// var dataTableRef; @ expedientes-busquedasimple-common.js
// var expedienteRef; @ expedientes-busquedasimple-common.js
// var formBusquedaRef; @ expedientes-busquedasimple-common.js
// var proyectoNroRef; @ expedientes-busquedasimple-common.js

/**
 * Código inicial del documento
 */
$(document).ready(function() {

	controlador_activo = 'participaciones';

	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	asignarValidatorExpresionesRegulares();

	// Genero la tabla
	dataTableRef = setDataTable('index.php?c=participaciones&a=datagrid&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());
	
	// Inicializo el formulario
	setearComportamientoSolapas(); // @ expedientes-busquedasimple-common.js
	setearComportamientoBotonesNavegacion(); // @ expedientes-busquedasimple-common.js
	setearComportamientoValidate(); // @ expedientes-busquedasimple-common.js
	
	// Al presionar el botón para habilitar al expediente en la participación
	$('#btn_habilitar_participacion').click(function () {
		$(location).attr('href','index.php?c=participaciones&a=editarhabilitacion&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());
	});

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

	$('#btn_nuevo_expediente').css('display', 'none');

	$('#solapa_participaciones').addClass('active');

	// No son necesarios en la solapa de Participaciones
	$('#btn_primer_pagina_movil').css('display', 'none');
	$('#btn_primer_pagina').css('display', 'none');
	$('#btn_pagina_anterior').css('display', 'none');
	$('#btn_pagina_siguiente').css('display', 'none');
	$('#btn_ultima_pagina').css('display', 'none');
	
	// Se utilizan para ver la info del Expediente/Nota/Recomendación Anterior y Siguiente
	$('#btn_expediente_anterior').click(listenerBtnVerExpedienteAnterior);
	$('#btn_expediente_siguiente').click(listenerBtnVerExpedienteSiguiente);


    // Botón para la carga de la Propuesta
    $('#btn_examinar_propuesta').click(function () { $('#f_propuesta').click(); });
    // Al seleccionar un documento mediante el botón "Cargar Propuesta"
    $('#f_propuesta').change(function(){
        // Se asigna la clave del expediente al cual se le carga la Propuesta
        $('#propuesta_anio').val($('#f_anio').val());
        $('#propuesta_tipo').val($('#f_tipo').val());
        $('#propuesta_numero').val($('#f_numero').val());
        $('#propuesta_cuerpo').val($('#f_cuerpo').val());
        $('#propuesta_alcance').val($('#f_alcance').val());
        // Se envía el formulario
        $('#form_upload_propuesta').submit();
    });
	
	// Botón para Moderar las participaciones
	$('#btn_moderar').click(function () {
		// Se asigna la clave del expediente al cual se desea moderar sus participaciones
        $('#moderacion_anio').val($('#f_anio').val());
        $('#moderacion_tipo').val($('#f_tipo').val());
        $('#moderacion_numero').val($('#f_numero').val());
        $('#moderacion_cuerpo').val($('#f_cuerpo').val());
        $('#moderacion_alcance').val($('#f_alcance').val());
        // Se envía el formulario
        $('#form_moderacion').submit();
	});

	// Botón para Imprimir las participaciones
	$('#btn_informe_participaciones').click(function () {
		// Se asigna la clave del expediente al cual se desea moderar sus participaciones
        $('#informe_anio').val($('#f_anio').val());
        $('#informe_tipo').val($('#f_tipo').val());
        $('#informe_numero').val($('#f_numero').val());
        $('#informe_cuerpo').val($('#f_cuerpo').val());
        $('#informe_alcance').val($('#f_alcance').val());
        // Se envía el formulario
        $('#form_informe').submit();
	});
});

