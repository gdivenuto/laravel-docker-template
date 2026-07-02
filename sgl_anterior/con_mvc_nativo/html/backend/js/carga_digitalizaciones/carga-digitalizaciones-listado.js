
/**
 * [listenerBotonAccion description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBotonAccion = function (event) {
	event.preventDefault(event); // Prevengo que el link haga lo propio al hacerle click

	// Obtengo la acción
	accion = $(this).data('accion');
	// Clave del expediente
	expe_anio = $(this).data('expe-anio');
	expe_tipo = $(this).data('expe-tipo');
	expe_numero = $(this).data('expe-numero');
	// Nombre de la digitalización temporal 
	f_archivo = $(this).data('archivo');

	f_tipo = $(this).data('tipo');
	f_url = $(this).data('url');
	
	// Se elimina la digitalización elegida
	if (accion == 'eliminarDigitalizacionTemporal')
		eliminarDigitalizacionTemporal(expe_anio, expe_tipo, expe_numero, f_archivo);
	// Se carga la digitalización elegida
	else if (accion == 'cargarDigitalizacionTemporal')
		cargarDigitalizacionTemporal(f_archivo);
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'.'.format(accion));
};

/**
 * Se elimina una digitalización temporal
 * @param  {[type]} expe_anio    [description]
 * @param  {[type]} expe_tipo    [description]
 * @param  {[type]} expe_numero  [description]
 * @param  {[type]} f_archivo 	 [description]
 * @return {[type]}              [description]
 */
function eliminarDigitalizacionTemporal (expe_anio, expe_tipo, expe_numero, f_archivo) {
	//console.log(expe_anio, expe_tipo, expe_numero, f_archivo);
	showModal('Confirmaci&oacute;n', '¿Est&aacute; seguro que desea eliminar la digitalizaci&oacute;n temporal para el Expediente: {0}-{1}-{2}?'.format(expe_anio, expe_tipo, expe_numero),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					url: "index.php?c=cargadigitalizaciones&a=eliminardigitalizaciontemporal",
					data: { archivo: f_archivo }
				})
				.done(function( respuesta ) {
					// Se recarga la grilla de las digitalizaciones temporales
					dataTableDocumentos.ajax.reload(null, false);
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
 * Se carga la Digitalización temporal
 * @param  {[type]} f_archivo Nombre de la digitalización
 * @return {[type]}
 */
function cargarDigitalizacionTemporal(f_archivo) {

	archivosSeleccionados.push(f_archivo);

	// Peticion asíncrona
    $.ajax({
        method: "POST",
        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
        url: "index.php?c=cargadigitalizaciones&a=cargardigitalizaciones",
        dataType: 'json', 
        data: JSON.stringify({ archivos: archivosSeleccionados })
    })
    .done(function( respuesta ) {
    	//console.log(respuesta);
    	if (respuesta.estado == 'OK') {
			if (respuesta.data != null) {
				// respuesta.data incluye:
				// 	 el nombre del archivo
				// 	 el estado: OK (carga satisfactoria), WARNING (cuando ya existe el documento) o ERROR (al intentar cargarlo)
				// 	 el mensaje, que describe el resultado del movimiento que se realizó o intentó realizar
				
				// Se retira del array
				pos = $.inArray(f_archivo, archivosSeleccionados);
				if (pos !== -1)
					archivosSeleccionados.splice(pos, 1);

				if (respuesta.data[0].estado == 'OK') {
        			// Se recarga la grilla de las digitalizaciones temporales
					dataTableDocumentos.ajax.reload(null, false);
				}
				else if (respuesta.data[0].estado == 'WARNING') {
        			// Digitalización existente
        			digi_existente = respuesta.data[0].archivo;
        			// Si el nombre contiene la letra 'a' o 'A'
        			if (digi_existente.indexOf("a") > -1 || digi_existente.indexOf("A") > -1)
            			// Se agrega directamente a la existente, sin preguntarle al usuario
            			agregarDigitalizacion(digi_existente);
            		else
        				// Se le consulta al usuario si desea sobreescribir o agregar (unir) a la existente
        				consultarAlUsuario(digi_existente);
        		} 
		    } else // Si no se recibieron resultados de la petición Ajax
                showModal('Error', 'Se esperaba un informe de estado y no se recibieron resultados.');
        } else // Si falló la operación en el controlador
            showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {
        showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
    });
};

var callbackRenderEnlaceDocumento = function (full) {
    
	return '<a href="{0}" target="_blank" title="Ver documento">{1}</a>'.format(full.url+'?v='+Math.random(), full.archivo);
};

var callbackRenderClaveExpediente = function (full) {
	if (full.expediente)
		return '{0}-{1}-{2}-{3}-{4}'.format(full.expediente.anio, full.expediente.tipo, full.expediente.numero, full.expediente.cuerpo, full.expediente.alcance);
	else
		return '<span class="text-danger">Expediente inexistente</span>';
}

var callbackRenderCaratulaExpediente = function (full) {
	return (full.expediente) ? full.expediente.caratula : '';
}

/**
 * Se renderiza el botón para eliminar la Digitalización temporal
 * @param  {[type]} full [description]
 * @return {[type]}      [description]
 */
var callbackRenderEliminacion = function (full) {
	//console.log(full);
	buttonAction = '<span class="btn-accion-contenido glyphicon glyphicon-trash" data-accion="eliminarDigitalizacionTemporal" title="Eliminar digitalizacion temporal" data-archivo="{0}" data-url="{1}" data-expe-anio="{2}" data-expe-tipo="{3}" data-expe-numero="{4}" ></span>&nbsp;&nbsp;'.format(
		full.archivo, full.url, 
		full.expediente.anio, full.expediente.tipo, full.expediente.numero);

	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

/**
 * Se renderiza el botón para cargar la Digitalización temporal
 * @param  {[type]} full [description]
 * @return {[type]}      [description]
 */
var callbackRenderCarga = function (full) {
	// Primero verifico que sea un expediente valido
	if (full.expediente === null)
		return '';

	// Se genera el botón para cargar la digitalización
	var control = '<span class="btn-accion-contenido glyphicon glyphicon-open" data-accion="cargarDigitalizacionTemporal" title="Cargar digitalizaci&oacute;n" data-tipo="{0}" data-archivo="{1}" data-url="{2}" ></span>';
	
	return control.format(full.tipo, full.archivo, full.url);
}

/**
 * Se le consulta al usuario si desea sobreescribir o agregar (unir) la digitalización a la existente
 * @param  {[type]} digi_existente 		Nombre del archivo de la digitalización
 */
function consultarAlUsuario(digi_existente) {
	// Se le pregunta al usuario si desea sobreescribir o agregar a uno existente
	showModal(
		'Atenci&oacute;n', 
		'La digitalizaci&oacute;n <strong>'+digi_existente+'</strong> ya existe ¿desea Sobreescribirla o Agregarla (unirla) a la existente?',
		{
			// Se sobreescribe el archivo de la digitalización
			btn_sobreescribir: {
				action: {
					fn: function (ev) {
						sobreescribirDigitalizacion(digi_existente);
					}
				}
			},
			// Se agrega la opción de unir la digitalización a la existente
			// Ejemplo: PDF existente + PDF a agregar = PDF único para el expediente respectivo
			btn_agregar: {
				action: {
					fn: function (ev) {
						agregarDigitalizacion(digi_existente);
					}
				}
			},
			btn_cancelar: { 
				class: 'btn-primary',
				action: function (e) {
					// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
					$(this).modal('hide');
				}
			}
		}
	);
}

/**
 * Se sobreescribe el archivo de la digitalización
 * @param  {[type]} digi Nombre del archivo de la digitalización
 */
function sobreescribirDigitalizacion(digi) {
	// Peticion asíncrona
    $.ajax({
        method: "POST",
        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
        url: "index.php?c=cargadigitalizaciones&a=sobreescribirdigitalizacion",
        dataType: 'json', 
        data: JSON.stringify({ digitalizacion: digi }) // cadena json con: el nombre completo de la digitalización (extensión pdf incluída)
    })
    .done(function( respuesta ) {
    	if (respuesta.estado == 'OK') {
            if (respuesta.data != null) {
            	// Se recarga la grilla de los documentos
            	dataTableDocumentos.ajax.reload(null, false);
			} else // Si no se recibieron resultados de la petición Ajax
                showModal('Error', 'Se esperaba un informe de estado y no se recibieron resultados.');
        } else // Si falló la operación en el controlador
            showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {
        showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
    });
}

/**
 * Se agrega la digitalización a la existente (se une)
 * @param  {[type]} digi Nombre del archivo de la digitalización
 */
function agregarDigitalizacion(digi) {
	// Peticion asíncrona
    $.ajax({
        method: "POST",
        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
        url: "index.php?c=cargadigitalizaciones&a=agregardigitalizacion",
        dataType: 'json', 
        data: JSON.stringify({ digitalizacion: digi }) // cadena json con: el nombre completo de la digitalización (extensión pdf incluída)
    })
    .done(function(respuesta, evt) {
    	if (respuesta.estado == 'OK') {
            if (respuesta.data != null) {
            	// Se recarga la grilla de los documentos
            	dataTableDocumentos.ajax.reload(null, false);
            } else // Si no se recibieron resultados de la petición Ajax
                showModal('Error', 'Se esperaba un informe de estado y no se recibieron resultados.');
        } else // Si falló la operación en el controlador
            showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {
        showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
    });
}

/**
 *  Al volver a renderizarse la grilla
 *  
 * @param  {[type]} settings [description]
 * @return {[type]}          [description]
 */
var callbackDrawCallback = function(settings) {
	if (dataTableDocumentos.rows().count() > 0) {
		var primer_expediente = settings.json.data[0].expediente;

		// Si posee registros
		if (settings.json.recordsFiltered > 0) {
			// Se dispara el evento click de la primer fila
			$(idTabla+' tbody tr:first').trigger('click');
			ocultarErrorGrilla();
		} else {
			// Si no hay resultados, en vez de simular un click, invoco la vista previa @ expedientes-busquedasimple-common.js
			vistaPreviaExpediente(primer_expediente.anio, primer_expediente.tipo, primer_expediente.numero, primer_expediente.cuerpo, primer_expediente.alcance);
			mostrarErrorGrilla('<span class="glyphicon glyphicon-exclamation-sign anim-vibrar"></span> El expediente <strong>{0} - {1} - {2} - {3} - {4}</strong> no existe.'.format(primer_expediente.anio, primer_expediente.tipo, primer_expediente.numero, primer_expediente.cuerpo, primer_expediente.alcance));
		}
	}
};

/**
 * [listenerTableRowClick description]
 * @return {[type]} [description]
 */
var listenerTableRowClick = function (e) {
	// Se deshabilita el evento cuando el clic se hace dentro de un boton de accion en la fila (TR)
 	var fila = ($(e.target).hasClass('btn-accion-contenido')) ? null : dataTableDocumentos.row(this).data();
	
	// Si tengo una fila válida...
	if (fila != null) {
		// Marco la fila seleccionada
		if ( $(this).hasClass('fila-destacada') ) 
	        $(this).removeClass('fila-destacada');
	    else {
	        dataTableDocumentos.$('tr.fila-destacada').removeClass('fila-destacada');
	        $(this).addClass('fila-destacada');
	    }
	    // Invoco la vista previa @ expedientes-busquedasimple-common.js
	    if (fila.expediente)
	    	vistaPreviaExpediente(fila.expediente.anio, fila.expediente.tipo, fila.expediente.numero, fila.expediente.cuerpo, fila.expediente.alcance);
		else
			limpiarVistaPreviaExpediente();
	}
};

/**
 * Se configura la grilla del DataTable
 * 
 * @param string ajaxUrl Url 
 * @return DataTable
 */
function setDataTable(ajaxUrl) {
	// Se borra y crea la grilla (tabla)
	idTabla = '#grillaContenidoTemporal';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(
		generarGrillaHtml(idTabla, 
			new Array(
				'Eliminar',
				'Cargar',
				'Documento',
				'Expediente',
				'Car&aacute;tula')));

	// Errores customizados para Datatables
	$.fn.dataTable.ext.errMode = 'none';

	// Se transforma la tabla en un DataTable
	var tabla = $(idTabla)
		.on( 'error.dt', function (e, settings, techNote, message) {
			showModal('Aviso', 'Ha ocurrido un error: {0}'.format(message),
				{ btn_cerrar: modalBtnSessionHandler(settings.jqXHR.responseJSON) });
		})
		.DataTable({
			stateSave: false,
			processing: true, // para que muestre "Cargando...", en modo local no se llega a visualizar
			serverSide: true,
			ordering:  false,
			searching: true,
            responsive: true,
            scrollX: true,
            ajax: ajaxUrl,
			// Dejamos la (t)abla y el (p)aginador
            dom: 'tp',
            language: { url: 'js/datatables/localisation/es_AR.json'}, 
            // Definición de las columnas
            columnDefs: [ { className: 'text-left', targets: '_all', searchable: false } ],
            // El dato a mostrar de cada columna
    		columns: [ 
    			{ data: null, className: 'text-center', width: '10px', render: callbackRenderEliminacion },
				{ data: null, className: 'text-center', width: '10px', render: callbackRenderCarga },
				{ data: null, width: '90px', render: callbackRenderEnlaceDocumento },
				{ data: null, className: 'text-center', width: '90px', render: callbackRenderClaveExpediente },
				{ data: null, render: callbackRenderCaratulaExpediente }
			],
			// Cantidad de documentos por página
            pageLength: 16,
			drawCallback: callbackDrawCallback
		});

	// Asigno eventos a la tabla
	$(idTabla+' tbody').on('click', 'tr', listenerTableRowClick);

	return tabla;
};

/**
 * Variables globales
 */
var dataTableDocumentos; // Referencia al DataTable generado
var archivosSeleccionados; // Conjunto de archivos seleccionados
var siguientes;

/**
 * Entry Point de jQuery
 */
$(document).ready(function() {
	// Se vuelve a la solapa de Expedientes
	$('#btn_busqueda_simple').click(function () { $(location).attr('href','index.php?c=expedientes&a=view'); });

	// Se inicializa el arreglo de archivos seleccionados
	archivosSeleccionados = new Array();

	// Se genera la grilla con el contenido del directorio temporal
	dataTableDocumentos = setDataTable('index.php?c=cargadigitalizaciones&a=datagriddocall');

	// Comportamiento de las flechas de la vista previa de proyectos del expediente
	$('#btn_prev_proyecto_anterior').click(function (e) { e.preventDefault(); actualizarVistaPreviaProyecto(proyectoNroRef-1); });
	$('#btn_prev_proyecto_siguiente').click(function (e) { e.preventDefault(); actualizarVistaPreviaProyecto(proyectoNroRef+1); });

	// Asignamos para aquellos elementos que posean la clase CSS 'btn-accion-contenido'
	// el evento click de esta forma, para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);
});