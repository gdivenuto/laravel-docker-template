
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

	if (accion == 'descargar')
		$(location).attr('href', f_url);
	else if (accion == 'eliminarDocumentoTemporal')
		eliminarDocumentoTemporal(expe_anio, expe_tipo, expe_numero, f_archivo);
	// Se carga el documento elegido
	else if (accion == 'cargarDocumentoTemporal')
		cargarDocumentoTemporal(f_archivo);
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'.'.format(accion));
};

/**
 * 21/07/2020 XXXX
 * Se elimina un documento temporal
 * @param  {[type]} expe_anio    [description]
 * @param  {[type]} expe_tipo    [description]
 * @param  {[type]} expe_numero  [description]
 * @param  {[type]} f_archivo 	 [description]
 * @return {[type]}              [description]
 */
function eliminarDocumentoTemporal (expe_anio, expe_tipo, expe_numero, f_archivo) {
	//console.log(expe_anio, expe_tipo, expe_numero, f_archivo);
	showModal('Confirmaci&oacute;n', '¿Est&aacute; seguro que desea eliminar el documento temporal para el Expediente: {0}-{1}-{2}?'.format(expe_anio, expe_tipo, expe_numero),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					url: "index.php?c=cargaproyectos&a=eliminardocumentotemporal",
					data: {
						archivo: f_archivo
					}
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
 * Se realiza la carga del documento temporal
 * @param  {[type]} f_archivo [description]
 * @return {[type]}           [description]
 */
function cargarDocumentoTemporal(f_archivo) {
	// Se agrega el archivo al listado de seleccionados
	archivosSeleccionados.push(f_archivo);

	// Peticion asíncrona
    $.ajax({
        method: "POST",
        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
        url: "index.php?c=cargaproyectos&a=movetemporal",
        dataType: 'json',
        data: JSON.stringify({
        	forzar: false, // Se inhabilita la sobreescritura ANTES de preguntar
        	archivos: archivosSeleccionados
        })
    })
    .done(function( respuesta ) {
    	if (respuesta.estado == 'OK') {
            if (respuesta.data != null) {
            	// respuesta.data incluye:
				// 	 el nombre del archivo, o un grupo de nombres
				// 	 el estado: OK (carga satisfactoria), WARNING (cuando ya existe el documento) o ERROR (al intentar cargar uno)
				// 	 el mensaje, que describe el resultado del movimiento que se realizó o intentó realizar

				// Se retira del array
				pos = $.inArray(f_archivo, archivosSeleccionados);
				if (pos !== -1)
					archivosSeleccionados.splice(pos, 1);
				// Si la respuesta es exitosa
				if (respuesta.data[0].estado == 'OK') {
        			// Se recarga la grilla de los documentos temporales
					dataTableDocumentos.ajax.reload(null, false);
				}
				// Si la respuesta es un Warning
				else if (respuesta.data[0].estado == 'WARNING') {
        			// Se trata de un Documento existente
        			documento_existente = respuesta.data[0].archivo;
        			// Se le pregunta al usuario si desea sobreescribirlo
        			consultarAlUsuario(documento_existente);
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

/**
 * Se le consulta al usuario si desea sobreescribir el documento existente
 * @param  {[type]} documento_existente 	Nombre del archivo
 */
function consultarAlUsuario(documento_existente) {
	// Se le pregunta al usuario si desea sobreescribir el documento existente
	showModal(
		'Atenci&oacute;n',
		'El documento <strong>'+documento_existente+'</strong> ya existe ¿desea Sobreescribirlo?',
		{
			btn_sobreescribir: {
				action: {
					fn: function (ev) {
						// Se agrega el archivo al listado de seleccionados
						archivosSeleccionados.push(documento_existente);

					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=movetemporal",
					        dataType: 'json',
					        data: JSON.stringify({
					        	forzar: true, // Se habilita la sobreescritura
					        	archivos: archivosSeleccionados
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se retira del array
									pos = $.inArray(documento_existente, archivosSeleccionados);
									if (pos !== -1)
										archivosSeleccionados.splice(pos, 1);
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

function generarHtmlBotonAccionDocumento(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-tipo="{3}" data-archivo="{4}" data-url="{5}"></span>&nbsp;'.format(
		accion, descripcion, icono,
		fila.tipo, fila.archivo, fila.url);
}

var callbackRenderAccionesDocumento = function (full) {
    buttonAction = generarHtmlBotonAccionDocumento('descargar', 'Descargar', 'download-alt', full);
    // fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
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
 * 21/07/2020 XXXX
 * Se renderiza el botón para eliminar el documento temporal
 * @param  {[type]} full [description]
 * @return {[type]}      [description]
 */
var callbackRenderEliminacion = function (full) {
	//console.log(full);
	buttonAction = '<span class="btn-accion-contenido glyphicon glyphicon-trash" data-accion="eliminarDocumentoTemporal" title="Eliminar documento temporal" data-archivo="{0}" data-url="{1}" data-expe-anio="{2}" data-expe-tipo="{3}" data-expe-numero="{4}" ></span>&nbsp;&nbsp;'.format(
		full.archivo, full.url,
		full.expediente.anio, full.expediente.tipo, full.expediente.numero);

	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

/**
 * Se renderiza el botón para cargar el Documento temporal
 * @param  {[type]} full [description]
 * @return {[type]}      [description]
 */
var callbackRenderCarga = function (full) {
	// Primero verifico que sea un expediente valido
	if (full.expediente === null)
		return '';
	// Se genera el botón para cargar la digitalización
	var control = '<span class="btn-accion-contenido glyphicon glyphicon-open" data-accion="cargarDocumentoTemporal" title="Cargar documento" data-tipo="{0}" data-archivo="{1}" data-url="{2}" ></span>';

	return control.format(full.tipo, full.archivo, full.url);
}

/**
 * [cargarTemporal description]
 * @param  {[type]} f_archivos [description]
 * @param  {[type]} f_forzar [description]
 * @return {[type]}           [description]
 *
function cargarTemporal(f_archivos, f_forzar, f_texto) {
	if (typeof f_texto == 'undefined' || f_texto === null || f_texto == '') {
		var archivosDetalle = '';
		$.each(f_archivos, function(key, value) {
			archivosDetalle += '<li>{0}</li>'.format(value);
		});
		f_texto = '¿Est&aacute; seguro que desea asignar estos documentos como originales? <br><ul>{0}</ul>'.format(archivosDetalle);
	}

	var ajaxParam = { forzar: f_forzar, archivos: f_archivos };

	showModal('Confirmaci&oacute;n', f_texto,
	{
		btn_no: { class: 'btn-primary' },
		btn_si: {
			action: {
				eventData: { ajaxData: ajaxParam },
				fn: function (ev) {
					// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
					$(this).modal('hide');

					// Peticion asíncrona
		            $.ajax({
		                method: "POST",
		                contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
		                url: "index.php?c=cargaproyectos&a=movetemporal",
		                dataType: 'json',
		                data: JSON.stringify(ev.data.ajaxData) // cadena json con: los 'archivos' y el flag 'forzar' para sobreescribir o no el/los archivo/s
		            })
		            .done(function( respuesta ) {
		            	if (respuesta.estado == 'OK') {
		                    if (respuesta.data != null) {
		                    	// respuesta.data incluye:
								// 	 el nombre del archivo, o un grupo de nombres
								// 	 el estado: OK (carga satisfactoria), WARNING (cuando ya existe el documento) o ERROR (al intentar cargar uno)
								// 	 el mensaje, que describe el resultado del movimiento que se realizó o intentó realizar

								archivos_existentes = new Array(); // Inicializamos el grupo de documentos existentes (cuyo estado = WARNING)
								listado_cargados = listado_erroneos = listado_existentes = texto = ''; 	// Inicializamos los listados

		                    	for (i=0; i<respuesta.data.length; i++) // Recorremos la data resultante, puede contener un sólo documento o un conjunto de ellos
		                    		if (respuesta.data[i].estado == 'OK')
		                    			listado_cargados += '<li class="text-success">{0}: {1}</li>'.format(respuesta.data[i].archivo, respuesta.data[i].mensaje);
		                    		else if (respuesta.data[i].estado == 'ERROR')
		                    			listado_erroneos += '<li class="text-danger">{0}: {1}</li>'.format(respuesta.data[i].archivo, respuesta.data[i].mensaje);
		                    		else if (respuesta.data[i].estado == 'WARNING') {
		                    			archivos_existentes.push(respuesta.data[i].archivo);
		                    			listado_existentes += '<li class="text-warning">{0}: {1}</li>'.format(respuesta.data[i].archivo, respuesta.data[i].mensaje);
		                    		}

		                    	if (listado_cargados != '')
		                    		texto += 'Documentos cargados satisfactoriamente: <br><ul>{0}</ul><br>'.format(listado_cargados);
		                    	if (listado_erroneos != '')
		                    		texto += 'Documentos con errores: <br><ul>{0}</ul><br>'.format(listado_erroneos);
		                    	if (listado_existentes != '')
		                    		texto += 'Documentos existentes ¿desea sobreescribirlos? (se conservar&aacute; una copia del original): <br><ul>{0}</ul>'.format(listado_existentes);

		                    	// Elimino todos los archivosSeleccionados menos los que quedaron pendientes
		                    	archivosSeleccionados = archivos_existentes;

		                    	// Se recarga la grilla de los documentos
		                    	dataTableDocumentos.ajax.reload(null, false);

		                    	// Confirmación en caso de archivos originales preexistentes
		                    	if (archivos_existentes.length == 0)
		                    		showModal('Atenci&oacute;n', texto);
		                    	else
									// Debido a que estoy forzando la carga, no voy a tener WARNINGS de los expedientes que ya tengan el "original.doc"
									cargarTemporal(archivos_existentes, true, texto);

		                    } else // Si no se recibieron resultados de la petición Ajax
		                        showModal('Error', 'Se esperaba un informe de estado y no se recibieron resultados.');
		                } else // Si falló la operación en el controlador
		                    showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
		            })
		            .fail(function( jqXHR, textStatus, errorThrown ) {
		            	showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
		            });
				} // fn: function(ev)
			} // action
		} // btn_si
	}); // showModal
};
/**/

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

/**
 * Entry Point de jQuery
 */
$(document).ready(function() {
	// Se vuelve
	$('#btn_busqueda_simple').click(function () { $(location).attr('href','index.php?c=expedientes&a=view'); });

	// Se inicializa el arreglo de archivos seleccionados
	archivosSeleccionados = new Array();

	// Se genera la grilla con el contenido del directorio temporal
	dataTableDocumentos = setDataTable('index.php?c=cargaproyectos&a=datagriddocall');

	// Comportamiento de las flechas de la vista previa de proyectos del expediente
	$('#btn_prev_proyecto_anterior').click(function (e) { e.preventDefault(); actualizarVistaPreviaProyecto(proyectoNroRef-1); });
	$('#btn_prev_proyecto_siguiente').click(function (e) { e.preventDefault(); actualizarVistaPreviaProyecto(proyectoNroRef+1); });

	// Asignamos para aquellos elementos que posean la clase CSS 'btn-accion-contenido'
	// el evento click de esta forma, para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);
});
