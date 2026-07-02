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
    buttonAction += generarHtmlBotonAccion('edit', 'Editar Pr&eacute;stamo', 'pencil', full);

    // Se permite Eliminar si el estado es: Solicitado, Devuelto ó Anulado
    if (full.estado == 'S' || full.estado == 'D' || full.estado == 'A')	
    	buttonAction += generarHtmlBotonAccion('delete', 'Eliminar Pr&eacute;stamo', 'trash', full);
    
	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

var callbackRenderMostrarClaveExpediente = function (data, type, full, meta) {
	// Devuelve la clave del expediente prestado/a prestar
	var identificador = full.anio+'-'+full.tipo+'-'+full.numero+'-'+full.cuerpo+'-'+full.alcance+'-'+full.digito+'-'+full.cuerpoalcance+'-'+full.anexoalcance+'-'+full.cuerpoanexoalcance+'-'+full.anexo+'-'+full.cuerpoanexo;
	var btnVerSolicitud = '';

	// Si el préstamo es de un expediente externo (D = D.E., O = Otro Ente)
    if (full.tipo == 'D' || full.tipo == 'O')
    	// Se arma el enlace para ver la Solicitud asociada a dicho Préstamo
    	btnVerSolicitud = '&nbsp;<a href="index.php?c=solicitudes&a=view&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}" title="Ver Solicitud"><span class="glyphicon glyphicon-list-alt"></span></a>'.format(full.anio, full.tipo, full.numero, full.cuerpo, full.alcance, full.digito);
    
    return identificador+btnVerSolicitud;
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
		valor = '<a href="index.php?c=prestamos&a=editprestado&f_grilla=general&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud={11}" class="btn btn-primary btn-sm" role="button"><span class="glyphicon glyphicon-edit"></span>&nbsp;Prestar</a>'.format(
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
		valor = '<a href="index.php?c=prestamos&a=editdevuelto&f_grilla=general&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud={11}" class="btn btn-success btn-sm" role="button"><span class="glyphicon glyphicon-edit"></span>&nbsp;Devolver</a>'.format(
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
		valor = '<a href="index.php?c=prestamos&a=editanulado&f_grilla=general&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud={11}" class="btn btn-danger btn-sm" role="button"><span class="glyphicon glyphicon-edit"></span>&nbsp;Anular</a>'.format(
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
		$(location).attr('href','index.php?c=prestamos&a=editinfo&f_grilla=general&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_fecha_solicitud={11}'.format(
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
					url: "index.php?c=prestamos&a=delete&f_grilla=general",
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
 * [setDataTable description]
 * @param {[type]} ajaxUrl [description]
 * @return DataTable
 */
function setDataTable(ajaxUrl) {
	// Borro y creo la grilla (tabla)
	idTabla = '#grillaPrestamosGeneral';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(
		generarGrillaHtml(idTabla, 
			new Array( // Títulos de cada columna de la grilla
				'&nbsp;',
				'Expediente',
				'Solicitante',
				'Fecha Solicitud',
				'Fecha Pr&eacute;stamo',
				'Fecha Devoluci&oacute;n',
				'Fecha Anulado',
				'Estado',
				'Nro.',
				'Folio',
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
	            	d.f_anio = $('#f_anio').val();
	            	d.f_tipo = $('#f_tipo').val();
	            	d.f_numero = $('#f_numero').val();
	            	d.f_cuerpo = $('#f_cuerpo').val();
	            	d.f_alcance = $('#f_alcance').val();

	            	d.f_digito 			   = $('#f_digito').val();
	            	d.f_cuerpoalcance 	   = $('#f_cuerpoalcance').val();
	            	d.f_anexoalcance 	   = $('#f_anexoalcance').val();
	            	d.f_cuerpoanexoalcance = $('#f_cuerpoanexoalcance').val();
	            	d.f_anexo 			   = $('#f_anexo').val();
	            	d.f_cuerpoanexo 	   = $('#f_cuerpoanexo').val();

	            	d.f_solicitante = $('#f_solicitante').val();// Tipo y Código, juntos mediante un |
                    d.f_fecha_desde = $('#f_fecha_desde').val();
                    d.f_fecha_hasta = $('#f_fecha_hasta').val();
                    // Estados elegidos
                    d.f_estado_solicitado = ($("#f_estado_solicitado").prop('checked')) ? $('#f_estado_solicitado').val() : '';
	            	d.f_estado_prestado   = ($("#f_estado_prestado").prop('checked')) ? $('#f_estado_prestado').val() : '';
	            	d.f_estado_devuelto   = ($("#f_estado_devuelto").prop('checked')) ? $('#f_estado_devuelto').val() : '';
	            	d.f_estado_anulado 	  = ($("#f_estado_anulado").prop('checked')) ? $('#f_estado_anulado').val() : '';
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
				{data: 'ro_solicitante_nombre', width: '200px'}, // Solicitante
				{data: null, width: '30px', className: 'text-center', render: callbackRenderAccionFechaSolicitud},	 // Fecha Solicitud
				{data: null, width: '30px', className: 'text-center', render: callbackRenderAccionFechaPrestamo},	 // Fecha Préstamo
				{data: null, width: '30px', className: 'text-center', render: callbackRenderAccionFechaDevolucion},  // Fecha Devolución
				{data: null, width: '30px', className: 'text-center', render: callbackRenderAccionFechaAnulado},	 // Fecha Anulado
				{data: null, width: '100px', render: callbackRenderEstado}, // Estado
				{data: 'libro_numero', width: '1px'}, // Libro Número
				{data: 'libro_folio', width: '1px'}, // Libro Folio
				{data: 'observaciones_prestamo', width: '200px'}
			],
			// Cantidad de registros por página
            pageLength: 8
		});

	return tabla;
};

/**
 * Se visualiza una modal para buscar un Solicitante, utilizando un autosugerido
 */
function mostrarModalSolicitanteAutosugerido() {
    // Se muestra una modal para buscar un Solicitante mediante un autosugerido
    $('#modalSolicitanteAutosugerido').modal('show');
    // Se limpia el campo de búsqueda
    $('#modal_solicitante_sugerido').val('');
    // Se selecciona y se le da el foco al campo de búsqueda por autosugerido
    setfocus('#modal_solicitante_sugerido');
}

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
	dataTableRef = setDataTable('index.php?c=prestamos&a=datagridgeneral');

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

    $('#f_estado_solicitado').click(function () {
        $('#f_estado_solicitado').val(($('#f_estado_solicitado').prop('checked')) ? 'S' : '');
    });

    $('#f_estado_prestado').click(function () {
        $('#f_estado_prestado').val(($('#f_estado_prestado').prop('checked')) ? 'P' : '');
    });

    $('#f_estado_devuelto').click(function () {
        $('#f_estado_devuelto').val(($('#f_estado_devuelto').prop('checked')) ? 'D' : '');
    });

    $('#f_estado_anulado').click(function () {
        $('#f_estado_anulado').val(($('#f_estado_anulado').prop('checked')) ? 'A' : '');
    });

	// Para ingresar un nuevo registro
	$('#btn_nuevo_prestamo').click(function () {
		$(location).attr('href','index.php?c=prestamos&a=addgeneral');
	});

	// Se genera el PDF del listado de Préstamos
    $('#btn_generar_reporte').click(function () { 
        // Se arma la url con el criterio de búsqueda utilizado
        var url = 'index.php?c=prestamos&a=generarpdfprestamos&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_digito={5}&f_cuerpoalcance={6}&f_anexoalcance={7}&f_cuerpoanexoalcance={8}&f_anexo={9}&f_cuerpoanexo={10}&f_solicitante={11}&f_fecha_solicitud_desde={12}&f_fecha_solicitud_hasta={13}&f_estado_solicitado={14}&f_estado_prestado={15}&f_estado_devuelto={16}&f_estado_anulado={17}'.format(
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
            $('#f_solicitante').val(),
            $('#f_fecha_desde').val(),
            $('#f_fecha_hasta').val(),
            $('#f_estado_solicitado').val(),
            $('#f_estado_prestado').val(),
            $('#f_estado_devuelto').val(),
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
	// Se limpia el criterio de búsqueda, se muestran todos
	$('#btn_restablecer').click(function () {
		$(location).attr('href','index.php?c=prestamos&a=viewgeneral');
	});

    $('#modal_solicitante_sugerido').keypress( function(e) {
        if ( $('#modal_solicitante_sugerido').val() != '' && e.which == 13 )
            $('#btCargarSolicitanteSugerido').focus();
    });
    
    $('#btCargarSolicitanteSugerido').click(function() {
        if ( $('#modal_solicitante_sugerido').val() != '' ) {
            // Se toma el solicitante sugerido y elegido
            var solicitante_sugerido = $('#modal_solicitante_sugerido').val();
            // Se separa el tipo, el código y la descripción
            var aux_solicitante = solicitante_sugerido.split('-');
            // Se toma el código
            var codigo_solicitante = aux_solicitante[0]+'|'+aux_solicitante[1];
            // Se selecciona el Iniciador en el combo
            $('#f_solicitante').val(codigo_solicitante);
            // Se oculta la modal
            $('#modalSolicitanteAutosugerido').modal('hide');
        } else
            setfocus('#modal_solicitante_sugerido');
    });

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

	// Se oculta el ítem TAREAS del menú principal
    $('#menu_item_tareas').css("display", "none");
});