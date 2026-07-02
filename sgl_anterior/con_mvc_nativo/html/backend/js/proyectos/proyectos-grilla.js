/**
 * Se renderiza un botón para una acción específica, con su descripción e ícono respectivos
 * @param  {[type]} accion      [description]
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-anio="{3}" data-tipo="{4}" data-numero="{5}" data-cuerpo="{6}" data-alcance="{7}" data-orden_proyecto="{8}" ></span>&nbsp;'.format(
		accion, descripcion, icono,
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance, fila.orden_proyecto);
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
 * Se renderiza el botón para eliminar la Digitalización de un expediente determinado
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonEliminarDigitalizacion(fila) {
	return '<span class="btn-accion-contenido-documento glyphicon glyphicon-trash" data-accion="eliminarDigitalizacion" title="Eliminar Digitalizaci&oacute;n" data-archivo="{0}" data-url="{1}" data-anio="{2}" data-tipo="{3}" data-numero="{4}" data-cuerpo="{5}" data-alcance="{6}" ></span>&nbsp;&nbsp;'.format(
		fila.archivo, fila.url,
		fila.expediente.anio, fila.expediente.tipo, fila.expediente.numero, fila.expediente.cuerpo, fila.expediente.alcance);
}

/**
 * Se renderiza el botón para eliminar el documento público de un expediente determinado
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonEliminarDocumentoPublico(fila) {
	return '<span class="btn-accion-contenido-documento glyphicon glyphicon-trash" data-accion="eliminarDocumentoPublico" title="Eliminar documento" data-archivo="{0}" data-url="{1}" data-anio="{2}" data-tipo="{3}" data-numero="{4}" data-cuerpo="{5}" data-alcance="{6}" ></span>&nbsp;&nbsp;'.format(
		fila.archivo, fila.url,
		fila.expediente.anio, fila.expediente.tipo, fila.expediente.numero, fila.expediente.cuerpo, fila.expediente.alcance);
}

/**
 * 03/08/2020
 * Se renderiza el botón para eliminar el Reservado de un expediente determinado
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonEliminarReservado(fila) {
	return '<span class="btn-accion-contenido-reservado glyphicon glyphicon-trash" data-accion="eliminarReservado" title="Eliminar documento" data-archivo="{0}" data-url="{1}" data-anio="{2}" data-tipo="{3}" data-numero="{4}" data-cuerpo="{5}" data-alcance="{6}" ></span>&nbsp;&nbsp;'.format(
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
    	buttonAction += generarHtmlBotonAccion('edit', 'Editar Proyecto', 'pencil', full);
    	buttonAction += generarHtmlBotonAccion('delete', 'Eliminar Proyecto', 'trash', full);
    }

    // Icono para determinar si esta promulgado o vetado
    if ((full.ro_fecha_promulga != null) && (full.ro_fecha_promulga != ''))
    	buttonAction += '<span class="btn-accion-contenido glyphicon glyphicon-thumbs-up forzar-texto-color-verde" title="Promulgado"></span>';
    else if ((full.ro_fecha_veto != null) && (full.ro_fecha_veto != ''))
    	buttonAction += '<span class="btn-accion-contenido glyphicon glyphicon-thumbs-down forzar-texto-color-rojo" title="Vetado"></span>';
    else
    	buttonAction += '<span class="glyphicon glyphicon-question-sign" title="Ni promulgado, ni vetado"></span>';

	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

/**
 * Se renderiza el botón para eliminar sólo:
 * el original
 * el original renombrado
 * la digitalización
 * otros documentos públicos
 * @param  {[type]} full [description]
 * @return {[type]}      [description]
 */
var callbackRenderAccionEliminarDocumentoPublico = function (full) {
	//console.log(full);
	buttonAction = '';

	nombre_codificado = obtenerNombreCodificado(full.expediente.anio, full.expediente.tipo, full.expediente.numero);

	// Si el archivo es el documento original
	if (full.tipo == 'proyecto' && $.inArray(full.archivo, ['original.doc', 'original.docx', 'original.odt']) >= 0) {
		buttonAction += generarHtmlBotonAccionDocumento(full);
	} else if (full.archivo.indexOf("original") != -1) {
		buttonAction += generarHtmlBotonAccionDocumento(full);
	} else if (full.archivo == (nombre_codificado+'.pdf')) {
		buttonAction += generarHtmlBotonEliminarDigitalizacion(full);
	}
	// Sólo el perfil 1 puede eliminar el resto de documentos públicos
	else if (full.tipo == 'proyecto' && perfil_usuario_actual == 1) {
		buttonAction += generarHtmlBotonEliminarDocumentoPublico(full);
	}
	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

/**
 * 03/08/2020 XXXX
 * Se renderiza el botón para mover el documento Reservado
 * @param  {[type]} full [description]
 * @return {[type]}      [description]
 */
var callbackRenderAccionMoverDocumentoPublico = function (full) {

	buttonAction = '<span class="btn-accion-contenido-documento glyphicon glyphicon-chevron-right" data-accion="moverDocumentoPublico" title="Mover documento" data-archivo="{0}" data-url="{1}" data-anio="{2}" data-tipo="{3}" data-numero="{4}" data-cuerpo="{5}" data-alcance="{6}" ></span>&nbsp;&nbsp;'.format(
		full.archivo, full.url,
		full.expediente.anio, full.expediente.tipo, full.expediente.numero, full.expediente.cuerpo, full.expediente.alcance);

	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

/**
 * 03/08/2020 XXXX
 * Se renderiza el botón para eliminar el documento Reservado
 * @param  {[type]} full [description]
 * @return {[type]}      [description]
 */
var callbackRenderAccionEliminarReservado = function (full) {

	buttonAction = '<span class="btn-accion-contenido-reservado glyphicon glyphicon-trash" data-accion="eliminarReservado" title="Eliminar documento" data-archivo="{0}" data-url="{1}" data-anio="{2}" data-tipo="{3}" data-numero="{4}" data-cuerpo="{5}" data-alcance="{6}" ></span>&nbsp;&nbsp;'.format(
		full.archivo, full.url,
		full.expediente.anio, full.expediente.tipo, full.expediente.numero, full.expediente.cuerpo, full.expediente.alcance);

	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

/**
 * 03/08/2020 XXXX
 * Se renderiza el botón para mover el documento Reservado
 * @param  {[type]} full [description]
 * @return {[type]}      [description]
 */
var callbackRenderAccionMoverReservado = function (full) {

	buttonAction = '<span class="btn-accion-contenido-reservado glyphicon glyphicon-chevron-left" data-accion="moverReservado" title="Mover documento" data-archivo="{0}" data-url="{1}" data-anio="{2}" data-tipo="{3}" data-numero="{4}" data-cuerpo="{5}" data-alcance="{6}" ></span>&nbsp;&nbsp;'.format(
		full.archivo, full.url,
		full.expediente.anio, full.expediente.tipo, full.expediente.numero, full.expediente.cuerpo, full.expediente.alcance);

	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

/**
 * Se visualizan las fechas de cada documento
 * @param  {[type]} full [description]
 * @return {[type]}      [description]
 */
var callbackRenderFechaDocumento = function (full) {
	return full.fecha;
}

/**
 * Se renderizan los nombres de cada documento, si es original, uno temporal (AAENNNNN) u otro del D.E.
 * @param  {[type]} full [description]
 * @return {[type]}      [description]
 */
var callbackRenderNombreDocumento = function (full) {
	// Si el archivo es el documento original, se muestra resaltado
	if (full.tipo == 'proyecto' && $.inArray(full.archivo, ['original.doc', 'original.docx', 'original.odt']) >= 0) {
		nombre = '<span class="text-success">{0}</span>'.format(full.archivo);

		enlace = '<a href="'+full.url.replace('#', '%23')+'?v='+Math.random()+'" target="_blank" title="Ver documento">'+nombre+'</a>';
	}
	else if (full.tipo == 'temporal') { // Si es temporal
		// Sólo el perfil 1 o 2 pueden ver y descargar los documentos temporales
		if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {
			nombre = '<span class="text-warning">{0}</span>'.format(full.archivo);

			enlace = '<a href="'+full.url.replace('#', '%23')+'?v='+Math.random()+'" target="_blank" title="Ver documento">'+nombre+'</a>';
		} else
			enlace = '<span class="text-warning">{0}</span>'.format(full.archivo);
	} else
		enlace = '<a href="'+full.url.replace('#', '%23')+'?v='+Math.random()+'" target="_blank" title="Ver documento">'+full.archivo+'</a>';

	return enlace;
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
	f_orden_proyecto = $(this).data('orden_proyecto');

	if (accion == 'edit')
		$(location).attr('href','index.php?c=proyectos&a=edit&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_orden_proyecto={5}'.format(
			f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden_proyecto ));
	else if (accion == 'delete') {
		item = dataTableRef.row($(this).parents('tr')).data();
		eliminarProyecto(item);
	}
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'. Proyecto del Expediente: {1}-{2}-{3}-{4}-{5} Orden: {6}'.format(accion, f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_orden_proyecto));
};

/**
 * [listenerBotonAccionDocumento description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBotonAccionDocumento = function (event) {
	event.preventDefault(event); // Prevengo que el link haga lo propio al hacerle click

	// Obtengo la acción
	accion = $(this).data('accion');
	// Clave del expediente
	f_anio = $(this).data('anio');
	f_tipo = $(this).data('tipo');
	f_numero = $(this).data('numero');
	// Nombre del archivo Original
	f_archivo = $(this).data('archivo');

	if (accion == 'eliminarDocOriginal')
		eliminarDocumentoOriginal(f_anio, f_tipo, f_numero, f_archivo);
	else if (accion == 'eliminarDigitalizacion')
		eliminarDigitalizacion(f_anio, f_tipo, f_numero, f_archivo);
	else if (accion == 'eliminarDocumentoPublico')
		eliminarDocumentoPublico(f_anio, f_tipo, f_numero, f_archivo);
	else if (accion == 'moverDocumentoPublico')
		moverDocumentoPublico(f_anio, f_tipo, f_numero, f_archivo);
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'.'.format(accion));
};

/**
 * [listenerBotonAccionReservado description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBotonAccionReservado = function (event) {
	event.preventDefault(event); // Prevengo que el link haga lo propio al hacerle click

	// Obtengo la acción
	accion = $(this).data('accion');
	// Clave del expediente
	f_anio = $(this).data('anio');
	f_tipo = $(this).data('tipo');
	f_numero = $(this).data('numero');
	// Nombre del archivo Reservado
	f_archivo = $(this).data('archivo');

	if (accion == 'eliminarReservado')
		eliminarReservado(f_anio, f_tipo, f_numero, f_archivo);
	else if (accion == 'moverReservado')
		moverReservado(f_anio, f_tipo, f_numero, f_archivo);
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'.'.format(accion));
};

/**
 * Se envía el proyecto respectivo para su eliminación
 * @param  {[type]} proyecto [description]
 * @return {[type]}            [description]
 */
function eliminarProyecto(proyecto) {
	showModal('Atenci&oacute;n', '¿Est&aacute; seguro que desea eliminar el Proyecto del Expediente: {0}-{1}-{2}-{3}-{4}?'.format(proyecto.anio, proyecto.tipo, proyecto.numero, proyecto.cuerpo, proyecto.alcance),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					url: "index.php?c=proyectos&a=delete",
					dataType: 'json',
					data: JSON.stringify(proyecto)
				})
				.done(function( respuesta ) {
					if (respuesta.estado == 'OK') {
						if ((respuesta.data != null) && (typeof respuesta.data.proyecto != 'undefined'))
							// Refrescar grilla
							dataTableRef.ajax.reload(null, false);
						else
							showModal('Error', 'Se esperaba un Proyecto y no se recibieron resultados.');
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
 * Se elimina el documento original
 * @param  {[type]} f_anio    [description]
 * @param  {[type]} f_tipo    [description]
 * @param  {[type]} f_numero  [description]
 * @param  {[type]} f_archivo [description]
 * @return {[type]}           [description]
 */
function eliminarDocumentoOriginal(f_anio, f_tipo, f_numero, f_archivo) {

	showModal('Confirmaci&oacute;n', '¿Est&aacute; seguro que desea eliminar el documento original del Expediente: {0}-{1}-{2}?'.format(f_anio, f_tipo, f_numero),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					url: "index.php?c=cargaproyectos&a=eliminardocumentooriginal",
					data: {
						anio: f_anio,
						tipo: f_tipo,
						numero: f_numero,
						archivo: f_archivo
					}
				})
				.done(function( respuesta ) {
					// Se recarga la grilla de proyectos
					dataTableRef.ajax.reload(null, false);
					// Se recarga la grilla de los documentos
					dataTableDocumentos.ajax.reload(null, false);
					// Se recarga la grilla de los documentos Reservados
					dataTableReservados.ajax.reload(null, false);
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
 * Se elimina la digitalización
 * @param  {[type]} f_anio    [description]
 * @param  {[type]} f_tipo    [description]
 * @param  {[type]} f_numero  [description]
 * @param  {[type]} f_archivo [description]
 * @return {[type]}           [description]
 */
function eliminarDigitalizacion(f_anio, f_tipo, f_numero, f_archivo) {

	showModal('Confirmaci&oacute;n', '¿Est&aacute; seguro que desea eliminar la digitalizaci&oacute;n del Expediente: {0}-{1}-{2}?'.format(f_anio, f_tipo, f_numero),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					url: "index.php?c=cargaproyectos&a=eliminardigitalizacion",
					data: {
						anio: f_anio,
						tipo: f_tipo,
						numero: f_numero,
						archivo: f_archivo
					}
				})
				.done(function( respuesta ) {
					// Se recarga la grilla de proyectos
					dataTableRef.ajax.reload(null, false);
					// Se recarga la grilla de los documentos
					dataTableDocumentos.ajax.reload(null, false);
					// Se recarga la grilla de los documentos Reservados
					dataTableReservados.ajax.reload(null, false);
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
 * Se elimina un documento público
 * @param  {[type]} f_anio    [description]
 * @param  {[type]} f_tipo    [description]
 * @param  {[type]} f_numero  [description]
 * @param  {[type]} f_archivo [description]
 * @return {[type]}           [description]
 */
function eliminarDocumentoPublico(f_anio, f_tipo, f_numero, f_archivo) {
	//console.log(f_anio, f_tipo, f_numero, f_archivo);
	showModal('Confirmaci&oacute;n', '¿Est&aacute; seguro que desea eliminar el documento del Expediente: {0}-{1}-{2}?'.format(f_anio, f_tipo, f_numero),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					url: "index.php?c=cargaproyectos&a=eliminardocumentopublico",
					data: {
						anio: f_anio,
						tipo: f_tipo,
						numero: f_numero,
						archivo: f_archivo
					}
				})
				.done(function( respuesta ) {
					// Se recarga la grilla de proyectos
					dataTableRef.ajax.reload(null, false);
					// Se recarga la grilla de los documentos públicos
					dataTableDocumentos.ajax.reload(null, false);
					// Se recarga la grilla de los documentos Reservados
					dataTableReservados.ajax.reload(null, false);
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
 * Se elimina un documento Reservado
 * @param  {[type]} f_anio    [description]
 * @param  {[type]} f_tipo    [description]
 * @param  {[type]} f_numero  [description]
 * @param  {[type]} f_archivo [description]
 * @return {[type]}           [description]
 */
function eliminarReservado(f_anio, f_tipo, f_numero, f_archivo) {
	//console.log(f_anio, f_tipo, f_numero, f_archivo);
	showModal('Confirmaci&oacute;n', '¿Est&aacute; seguro que desea eliminar el documento del Expediente: {0}-{1}-{2}?'.format(f_anio, f_tipo, f_numero),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					url: "index.php?c=cargaproyectos&a=eliminarreservado",
					data: {
						anio: f_anio,
						tipo: f_tipo,
						numero: f_numero,
						archivo: f_archivo
					}
				})
				.done(function( respuesta ) {
					// Se recarga la grilla de proyectos
					dataTableRef.ajax.reload(null, false);
					// Se recarga la grilla de los documentos
					dataTableDocumentos.ajax.reload(null, false);
					// Se recarga la grilla de los documentos Reservados
					dataTableReservados.ajax.reload(null, false);
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
 * Se mueve un documento, de público a reservado
 * @param  {[type]} f_anio    [description]
 * @param  {[type]} f_tipo    [description]
 * @param  {[type]} f_numero  [description]
 * @param  {[type]} f_archivo [description]
 * @return {[type]}           [description]
 */
function moverDocumentoPublico(f_anio, f_tipo, f_numero, f_archivo) {
	// Se envía la peticion al controlador
	$.ajax({
		method: "POST",
		url: "index.php?c=cargaproyectos&a=moverdocumentopublicoareservado",
		data: {
			anio: f_anio,
			tipo: f_tipo,
			numero: f_numero,
			archivo: f_archivo
		}
	})
	.done(function( respuesta ) {
		if (respuesta.estado == 'OK') {
			// Se recarga la grilla de proyectos
			dataTableRef.ajax.reload(null, false);
			// Se recarga la grilla de los documentos públicos
			dataTableDocumentos.ajax.reload(null, false);
			// Se recarga la grilla de los documentos Reservados
			dataTableReservados.ajax.reload(null, false);
		}
		// Si el documento ya existe
		else if (respuesta.estado == 'WARNING') {
			var extensionReservado = respuesta.data.substr(respuesta.data.length - 4);
	    	// Si es un PDF
			if (extensionReservado == '.pdf') {
				// Se le consulta al usuario si desea sobreescribirlo o agregarlo
				consultarPdfReservadoEnMovimiento(respuesta.data)
			} else {
		    	// Se le consulta al usuario si desea sobreescribirlo
	    		consultarSobreescrituraReservadoEnMovimiento(respuesta.data);
	    	}
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {
		showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
	});
}

/**
 * Se mueve un documento, de reservado a público
 * @param  {[type]} f_anio    [description]
 * @param  {[type]} f_tipo    [description]
 * @param  {[type]} f_numero  [description]
 * @param  {[type]} f_archivo [description]
 * @return {[type]}           [description]
 */
function moverReservado(f_anio, f_tipo, f_numero, f_archivo) {
	// Se envía la peticion al controlador
	$.ajax({
		method: "POST",
		url: "index.php?c=cargaproyectos&a=moverdocumentoreservadoapublico",
		data: {
			anio: f_anio,
			tipo: f_tipo,
			numero: f_numero,
			archivo: f_archivo
		}
	})
	.done(function( respuesta ) {
		if (respuesta.estado == 'OK') {
			// Se recarga la grilla de proyectos
			dataTableRef.ajax.reload(null, false);
			// Se recarga la grilla de los documentos públicos
			dataTableDocumentos.ajax.reload(null, false);
			// Se recarga la grilla de los documentos Reservados
			dataTableReservados.ajax.reload(null, false);
		}
		// Si el documento ya existe
		else if (respuesta.estado == 'WARNING') {
			var extensionPublico = respuesta.data.substr(respuesta.data.length - 4);
	    	// Si es un PDF
			if (extensionPublico == '.pdf') {
				// Se le consulta al usuario si desea sobreescribirlo o agregarlo
				consultarPdfPublicoEnMovimiento(respuesta.data)
			} else {
		    	// Se le consulta al usuario si desea sobreescribirlo
	    		consultarSobreescrituraPublicoEnMovimiento(respuesta.data);
	    	}
		}
	})
	.fail(function( jqXHR, textStatus, errorThrown ) {
		showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
	});
}

/**
 * [cargarTemporal description]
 * @param  {[type]} f_archivos [description]
 * @param  {[type]} f_forzar [description]
 * @return {[type]}           [description]
 */
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

								archivos_existentes = []; // Inicializamos el grupo de documentos existentes (cuyo estado = WARNING)
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

		                    	dataTableDocumentos.ajax.reload(null, false); // Se recarga la grilla de los documentos

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
		$('#btn_nuevo_proyecto').prop('disabled', false); // Se habilita el botón 'Nuevo proyecto'
	else // si NO existe
		$('#btn_nuevo_proyecto').prop('disabled', true); // Se deshabilita el botón 'Nuevo proyecto'
};

/**
 * Se configura la grilla del DataTable
 *
 * @param {[type]} ajaxUrl [description]
 * @return DataTable
 */
function setDataTable(ajaxUrl) {
	// Borro y creo la grilla (tabla)
	idTabla = '#grillaProyectos';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(
		generarGrillaHtml(idTabla,
			new Array( // Títulos de cada columna de la grilla
				'Acciones',
				'Orden',
				'Descripci&oacute;n',
				'Nro. Promulga',
				'Fecha Promulga',
				'Dec. Promulga',
				'Fecha Veto',
				'Observaciones')));

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
				{data: 'orden_proyecto'},
				{data: 'ro_descripcion_proyecto'},
				{data: 'ro_numero_promulga'},
				{data: 'ro_fecha_promulga', render: function (data, type, full, meta) { return formatearFechaConBarras(full.ro_fecha_promulga); }},
				{data: 'ro_decreto_promulga'},
				{data: 'ro_fecha_veto', render: function (data, type, full, meta) { return formatearFechaConBarras(full.ro_fecha_veto); }},
				{data: 'observaciones_proyecto'}
			],
			drawCallback: callbackDrawCallback
		});

	return tabla;
}

/**
 * Se configura la grilla del DataTable de los documentos del expediente respectivo
 * @return DataTable
 */
function setDataTableDocumentos(ajaxUrl) {
	// Borro y creo la grilla (tabla)
	idTabla = '#grillaDocumentos';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();

	// 17/07/2020 XXXX
	// Sólo los perfiles 1 y 2 pueden Eliminar documentos
	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {

		$(idTablaContainer).append(generarGrillaHtml(idTabla, new Array('&nbsp;','Fecha','Documento','&nbsp;')));
		columnas_documentos = [
			{ data: null, render: callbackRenderAccionEliminarDocumentoPublico, className: 'text-center', width:'10px' },
			{ data: null, render: callbackRenderFechaDocumento, className: 'text-center', width:'60px' },
			{ data: null, render: callbackRenderNombreDocumento },
			{ data: null, render: callbackRenderAccionMoverDocumentoPublico, className: 'text-center', width:'10px' },
		];
	} else {
		$(idTablaContainer).append(generarGrillaHtml(idTabla, new Array('Fecha','Documento')));
		columnas_documentos = [
			{ data: null, render: callbackRenderFechaDocumento, className: 'text-center', width:'60px' },
			{ data: null, render: callbackRenderNombreDocumento }
		];
	}

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
			ajax: {
	            url: ajaxUrl,
	            data: function ( d ) {
	            	// Agrego los parámetros de búsqueda.
	            	d.f_anio = $('#f_anio').val();
	            	d.f_tipo = $('#f_tipo').val();
	            	d.f_numero = $('#f_numero').val();
	            }
	        },
	        dom: 'tp', // Definimos la 't'abla y el p'aginador
	        autoWidth: false,
			language: { url: '../librerias/datatables/localisation/es_AR.json' },
			columnDefs: [
				{ targets: '_all', className: 'text-left', searchable: false}
			],
			columns: columnas_documentos,
            pageLength: 8 // Cantidad de registros por página
		});

	return tabla;
}

/**
 * 03/08/2020
 * Se configura la grilla del DataTable de los documentos Reservados del expediente respectivo
 * @return DataTable
 */
function setDataTableReservados(ajaxUrl) {
	// Borro y creo la grilla (tabla)
	idTabla = '#grillaReservados';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();

	// Sólo los perfiles 1 y 2 pueden Eliminar y Mover, el documento reservado
	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {

		$(idTablaContainer).append(generarGrillaHtml(idTabla, new Array('&nbsp;','&nbsp;','Fecha','Documento')));
		columnas_documentos = [
			{ data: null, render: callbackRenderAccionMoverReservado, className: 'text-center', width:'10px' },
			{ data: null, render: callbackRenderAccionEliminarReservado, className: 'text-center', width:'10px' },
			{ data: null, render: callbackRenderFechaDocumento, className: 'text-center', width:'60px' },
			{ data: null, render: callbackRenderNombreDocumento }
		];
	} else {
		$(idTablaContainer).append(generarGrillaHtml(idTabla, new Array('Fecha','Documento')));
		columnas_documentos = [
			{ data: null, render: callbackRenderFechaDocumento, className: 'text-center', width:'60px' },
			{ data: null, render: callbackRenderNombreDocumento }
		];
	}

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
			ajax: {
	            url: ajaxUrl,
	            data: function ( d ) {
	            	// Agrego los parámetros de búsqueda.
	            	d.f_anio = $('#f_anio').val();
	            	d.f_tipo = $('#f_tipo').val();
	            	d.f_numero = $('#f_numero').val();
	            }
	        },
	        dom: 'tp', // Definimos la 't'abla y el p'aginador
	        autoWidth: false,
			language: { url: '../librerias/datatables/localisation/es_AR.json' },
			columnDefs: [
				{ targets: '_all', className: 'text-left', searchable: false}
			],
			columns: columnas_documentos,
            pageLength: 8 // Cantidad de registros por página
		});

	return tabla;
}

/**
 * Se le consulta al usuario si desea sobreescribir el documento Público existente
 * @param  {[type]} documento_existente 	Nombre del archivo
 */
function consultarSobreescrituraPublico(documento_existente) {
	// Se le pregunta al usuario si desea sobreescribir el documento existente
	showModal(
		'Atenci&oacute;n',
		'El documento <strong>'+documento_existente+'</strong> ya existe ¿desea Sobreescribirlo?',
		{
			btn_sobreescribir: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=sobreescribirdocpublico",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	documento_existente: documento_existente
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se limpia
					            	doc_publico_existente = 0;
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
					$.ajax({
				        method: "POST",
				        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
				        url: "index.php?c=cargaproyectos&a=eliminardocumentoauxiliar",
				        dataType: 'json',
				        data: JSON.stringify({
				        	anio: $('#f_anio').val(),
							tipo: $('#f_tipo').val(),
							numero: $('#f_numero').val(),
				        	documento_auxiliar: documento_existente
				        })
				    })
				    .done(function( respuesta ) {
						// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
						$(this).modal('hide');
				    	// Se recarga la grilla de proyectos
						dataTableRef.ajax.reload(null, false);
						// Se recarga la grilla de los documentos
						dataTableDocumentos.ajax.reload(null, false);
						// Se recarga la grilla de los documentos Reservados
						dataTableReservados.ajax.reload(null, false);
					})
				    .fail(function( jqXHR, textStatus, errorThrown ) {
				        showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
				    });
				}
			}
		}
	);
}

/**
 * Se le consulta al usuario si desea sobreescribir el documento Reservado existente
 * @param  {[type]} documento_existente 	Nombre del archivo
 */
function consultarSobreescrituraReservado(documento_existente) {
	// Se le pregunta al usuario si desea sobreescribir el documento existente
	showModal(
		'Atenci&oacute;n',
		'El documento <strong>'+documento_existente+'</strong> ya existe ¿desea Sobreescribirlo?',
		{
			btn_sobreescribir: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=sobreescribirdocreservado",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	documento_existente: documento_existente
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se limpia
					            	doc_reservado_existente = 0;
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
					$.ajax({
				        method: "POST",
				        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
				        url: "index.php?c=cargaproyectos&a=eliminardocumentoauxiliar",
				        dataType: 'json',
				        data: JSON.stringify({
				        	anio: $('#f_anio').val(),
							tipo: $('#f_tipo').val(),
							numero: $('#f_numero').val(),
				        	documento_auxiliar: documento_existente
				        })
				    })
				    .done(function( respuesta ) {
						// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
						$(this).modal('hide');
				    	// Se recarga la grilla de proyectos
						dataTableRef.ajax.reload(null, false);
						// Se recarga la grilla de los documentos
						dataTableDocumentos.ajax.reload(null, false);
						// Se recarga la grilla de los documentos Reservados
						dataTableReservados.ajax.reload(null, false);
					})
				    .fail(function( jqXHR, textStatus, errorThrown ) {
				        showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
				    });
				}
			}
		}
	);
}

/**
 * Se le consulta al usuario si desea sobreescribir o agregar el documento público existente
 * @param  {[type]} documento_existente 	Nombre del archivo
 */
function consultarPdfPublico(documento_existente) {
	// Se le pregunta al usuario si desea sobreescribir o agregar el documento público existente
	showModal(
		'Atenci&oacute;n',
		'El documento <strong>'+documento_existente+'</strong> ya existe ¿desea Sobreescribirlo o Agregarlo?',
		{
			btn_sobreescribir: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=sobreescribirdocpublico",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	documento_existente: documento_existente
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se limpia
					            	doc_publico_existente = 0;
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
			btn_agregar: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=agregardocumentopublicopdf",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	documento_existente: documento_existente
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se limpia
					            	doc_publico_existente = 0;
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
					$.ajax({
				        method: "POST",
				        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
				        url: "index.php?c=cargaproyectos&a=eliminardocumentoauxiliar",
				        dataType: 'json',
				        data: JSON.stringify({
				        	anio: $('#f_anio').val(),
							tipo: $('#f_tipo').val(),
							numero: $('#f_numero').val(),
				        	documento_auxiliar: documento_existente
				        })
				    })
				    .done(function( respuesta ) {
						// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
						$(this).modal('hide');
				    	// Se recarga la grilla de proyectos
						dataTableRef.ajax.reload(null, false);
						// Se recarga la grilla de los documentos
						dataTableDocumentos.ajax.reload(null, false);
						// Se recarga la grilla de los documentos Reservados
						dataTableReservados.ajax.reload(null, false);
					})
				    .fail(function( jqXHR, textStatus, errorThrown ) {
				        showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
				    });
				}
			}
		}
	);
}

/**
 * Se le consulta al usuario si desea sobreescribir o agregar el documento reservado existente
 * @param  {[type]} documento_existente 	Nombre del archivo
 */
function consultarPdfReservado(documento_existente) {
	// Se le pregunta al usuario si desea sobreescribir o agregar el documento reservado existente
	showModal(
		'Atenci&oacute;n',
		'El documento <strong>'+documento_existente+'</strong> ya existe ¿desea Sobreescribirlo o Agregarlo?',
		{
			btn_sobreescribir: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=sobreescribirdocreservado",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	documento_existente: documento_existente
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se limpia
					            	doc_reservado_existente = 0;
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
			btn_agregar: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=agregardocumentoreservadopdf",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	documento_existente: documento_existente
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se limpia
					            	doc_reservado_existente = 0;
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
					$.ajax({
				        method: "POST",
				        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
				        url: "index.php?c=cargaproyectos&a=eliminardocumentoauxiliar",
				        dataType: 'json',
				        data: JSON.stringify({
				        	anio: $('#f_anio').val(),
							tipo: $('#f_tipo').val(),
							numero: $('#f_numero').val(),
				        	documento_auxiliar: documento_existente
				        })
				    })
				    .done(function( respuesta ) {
						// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
						$(this).modal('hide');
				    	// Se recarga la grilla de proyectos
						dataTableRef.ajax.reload(null, false);
						// Se recarga la grilla de los documentos
						dataTableDocumentos.ajax.reload(null, false);
						// Se recarga la grilla de los documentos Reservados
						dataTableReservados.ajax.reload(null, false);
					})
				    .fail(function( jqXHR, textStatus, errorThrown ) {
				        showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
				    });
				}
			}
		}
	);
}

/**
 * Se le consulta al usuario si desea sobreescribir o agregar el documento público existente, al querer moverlo
 * @param  {[type]} documento_existente 	Nombre del archivo
 */
function consultarPdfPublicoEnMovimiento(documento_existente) {
	// Se le pregunta al usuario si desea sobreescribir o agregar el documento público existente
	showModal(
		'Atenci&oacute;n',
		'El documento a mover <strong>'+documento_existente+'</strong> ya existe ¿desea Sobreescribirlo o Agregarlo?',
		{
			btn_sobreescribir: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=sobreescribirdocpublicoenmovimiento",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	documento_existente: documento_existente
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
			btn_agregar: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=agregardocumentopublicopdfenmovimiento",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	documento_existente: documento_existente
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
			btn_cancelar: { class: 'btn-primary' }
		}
	);
}

/**
 * Se le consulta al usuario si desea sobreescribir el documento Público existente, al querer moverlo
 * @param  {[type]} documento_existente 	Nombre del archivo
 */
function consultarSobreescrituraPublicoEnMovimiento(documento_existente) {
	// Se le pregunta al usuario si desea sobreescribir el documento existente
	showModal(
		'Atenci&oacute;n',
		'El documento a mover <strong>'+documento_existente+'</strong> ya existe ¿desea Sobreescribirlo?',
		{
			btn_sobreescribir: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=sobreescribirdocpublicoenmovimiento",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	documento_existente: documento_existente
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
			btn_cancelar: { class: 'btn-primary' }
		}
	);
}

/**
 * Se le consulta al usuario si desea sobreescribir o agregar el documento público existente, al querer moverlo
 * @param  {[type]} documento_existente 	Nombre del archivo
 */
function consultarPdfReservadoEnMovimiento(documento_existente) {
	// Se le pregunta al usuario si desea sobreescribir o agregar el documento público existente
	showModal(
		'Atenci&oacute;n',
		'El documento a mover <strong>'+documento_existente+'</strong> ya existe ¿desea Sobreescribirlo o Agregarlo?',
		{
			btn_sobreescribir: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=sobreescribirdocreservadoenmovimiento",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	documento_existente: documento_existente
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
			btn_agregar: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=agregardocumentoreservadopdfenmovimiento",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	documento_existente: documento_existente
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
			btn_cancelar: { class: 'btn-primary' }
		}
	);
}

/**
 * Se le consulta al usuario si desea sobreescribir el documento Público existente, al querer moverlo
 * @param  {[type]} documento_existente 	Nombre del archivo
 */
function consultarSobreescrituraReservadoEnMovimiento(documento_existente) {
	// Se le pregunta al usuario si desea sobreescribir el documento existente
	showModal(
		'Atenci&oacute;n',
		'El documento a mover <strong>'+documento_existente+'</strong> ya existe ¿desea Sobreescribirlo?',
		{
			btn_sobreescribir: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=sobreescribirdocreservadoenmovimiento",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	documento_existente: documento_existente
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
			btn_cancelar: { class: 'btn-primary' }
		}
	);
}

/**
 * Se le consulta al usuario si desea sobreescribir o agregar la digitalización existente
 * @param  {string}  digitalizacion_existente 	Nombre del archivo
 * @param  {integer} es_reservada				Si es reservada o no
 */
function consultarDigitalizacion(digitalizacion_existente, es_reservada) {
	// Se le pregunta al usuario si desea sobreescribir o agregar la digitalización existente
	showModal(
		'Atenci&oacute;n',
		'La digitalizacion a cargar <strong>'+digitalizacion_existente+'</strong> ya existe ¿desea Sobreescribirla o Agregarla?',
		{
			btn_sobreescribir: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=sobreescribirdigitalizacion",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	digitalizacion_existente: digitalizacion_existente,
					        	es_reservada: es_reservada
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se limpia
					            	doc_publico_existente = 0;
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
			btn_agregar: {
				action: {
					fn: function (ev) {
					    $.ajax({
					        method: "POST",
					        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					        url: "index.php?c=cargaproyectos&a=agregardigitalizacion",
					        dataType: 'json',
					        data: JSON.stringify({
					        	anio: $('#f_anio').val(),
								tipo: $('#f_tipo').val(),
								numero: $('#f_numero').val(),
					        	digitalizacion_existente: digitalizacion_existente,
					        	es_reservada: es_reservada
					        })
					    })
					    .done(function( respuesta ) {
					    	if (respuesta.estado == 'OK') {
					            if (respuesta.data != null) {
					            	// Se limpia
					            	doc_publico_existente = 0;
					            	// Se recarga la grilla de proyectos
									dataTableRef.ajax.reload(null, false);
									// Se recarga la grilla de los documentos
									dataTableDocumentos.ajax.reload(null, false);
									// Se recarga la grilla de los documentos Reservados
									dataTableReservados.ajax.reload(null, false);

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
					$.ajax({
				        method: "POST",
				        contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
				        url: "index.php?c=cargaproyectos&a=eliminardocumentoauxiliar",
				        dataType: 'json',
				        data: JSON.stringify({
				        	anio: $('#f_anio').val(),
							tipo: $('#f_tipo').val(),
							numero: $('#f_numero').val(),
				        	documento_auxiliar: digitalizacion_existente,
				        	es_reservada: es_reservada
				        })
				    })
				    .done(function( respuesta ) {
						// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
						$(this).modal('hide');
				    	// Se recarga la grilla de proyectos
						dataTableRef.ajax.reload(null, false);
						// Se recarga la grilla de los documentos
						dataTableDocumentos.ajax.reload(null, false);
						// Se recarga la grilla de los documentos Reservados
						dataTableReservados.ajax.reload(null, false);
					})
				    .fail(function( jqXHR, textStatus, errorThrown ) {
				        showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
				    });
				}
			}
		}
	);
}

/**
 * Variables globales
 */
// var dataTableRef; @ expedientes-busquedasimple-common.js
// var expedienteRef; @ expedientes-busquedasimple-common.js
// var formBusquedaRef; @ expedientes-busquedasimple-common.js
// var proyectoNroRef; @ expedientes-busquedasimple-common.js
var dataTableDocumentos;
var dataTableReservados;

/**
 * Código inicial del documento
 */
$(document).ready(function() {
	controlador_activo = 'proyectos';

	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	asignarValidatorExpresionesRegulares();

	// Genero la tabla
	dataTableRef = setDataTable('index.php?c=proyectos&a=datagrid&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());

	// Se genera la tabla de los documentos del expediente respectivo
	dataTableDocumentos = setDataTableDocumentos('index.php?c=proyectos&a=datagriddocexp&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val());

	// Se genera la tabla de los documentos Reservados del expediente respectivo
	dataTableReservados = setDataTableReservados('index.php?c=proyectos&a=datagridreservadosexp&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val());

	// Inicializo el formulario
	setearComportamientoSolapas(); // @ expedientes-busquedasimple-common.js
	setearComportamientoBotonesNavegacion(); // @ expedientes-busquedasimple-common.js
	setearComportamientoValidate(); // @ expedientes-busquedasimple-common.js

	// sumo un callback más al comportamiento por defecto del
	// boton de buscar expediente, para refrescar la segunda grilla
	// de documentos asociados
	$('#btn_buscar_expediente').click(function () { if (formBusquedaRef.valid()) dataTableDocumentos.ajax.reload(); });

	// Comportamiento de botones extra
	$('#btn_nuevo_proyecto').click(function () {
		$(location).attr('href','index.php?c=proyectos&a=add&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());
	});

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

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido-documento', listenerBotonAccionDocumento);

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido-reservado', listenerBotonAccionReservado);

	$('#btn_nuevo_expediente').css('display', 'none');

	$('#solapa_proyectos').addClass('active');

	// -- Botón para la carga de la Digitalización
    $('#btn_examinar_digitalizacion').click(function () { $('#f_digitalizacion').click(); });
    // Al seleccionar un documento mediante el botón "Cargar Digitalización"
    $('#f_digitalizacion').change(function(){
		// Se asigna la clave del expediente al cual se le carga la Digitalización
    	$('#digi_anio').val($('#f_anio').val());
    	$('#digi_tipo').val($('#f_tipo').val());
    	$('#digi_numero').val($('#f_numero').val());
    	$('#digi_cuerpo').val($('#f_cuerpo').val());
    	$('#digi_alcance').val($('#f_alcance').val());
        // Se envía el formulario
        $('#form_upload_digi_desde_solapa').submit();
    });

	// -- Botón para la carga de la Digitalización
    $('#btn_examinar_digitalizacion_reservada').click(function () { $('#f_digitalizacion_reservada').click(); });
    // Al seleccionar un documento mediante el botón "Cargar Digitalización"
    $('#f_digitalizacion_reservada').change(function(){
		// Se asigna la clave del expediente al cual se le carga la Digitalización
    	$('#digi_reservada_anio').val($('#f_anio').val());
    	$('#digi_reservada_tipo').val($('#f_tipo').val());
    	$('#digi_reservada_numero').val($('#f_numero').val());
    	$('#digi_reservada_cuerpo').val($('#f_cuerpo').val());
    	$('#digi_reservada_alcance').val($('#f_alcance').val());
         // Se envía el formulario
        $('#form_upload_digi_reservada_desde_solapa').submit();
    });

    // 2023-03-14: Se unificaron en un solo botón para la carga de Documentos
    // Se utiliza la ActuacionExpedienteSubirArchivoTrabajo
    // ----------------------------------------------------------------------
    $('#btn_examinar_documento_unificado').click(function () {
    	iniciarActuacion(
    		'expediente_subir_archivo_trabajo',
    		{
    			anio: $('#f_anio').val(),
    		 	tipo: $('#f_tipo').val(),
    		 	numero: $('#f_numero').val(),
    			cuerpo: $('#f_cuerpo').val(),
    			alcance: $('#f_alcance').val()
    		}
    	);
    });

    // Si ya existe un documento que se desea cargar
    if (digitalizacion_existente != 0) {
    	var extensionDigitalizacion = digitalizacion_existente.substr(digitalizacion_existente.length - 4);
    	// Si es un PDF
		if (extensionDigitalizacion == '.pdf') {
			// Se le consulta al usuario si desea sobreescribirlo o agregarlo
			consultarDigitalizacion(digitalizacion_existente, 0);
		} else {
	    	showModal('Atenci&oacute;n', 'No es un documento PDF el que intenta cargar.');
    	}
    }

    // Si ya existe un documento que se desea cargar
    if (doc_publico_existente != 0) {
    	var extensionPublico = doc_publico_existente.substr(doc_publico_existente.length - 4);
    	// Si es un PDF
		if (extensionPublico == '.pdf') {
			// Se le consulta al usuario si desea sobreescribirlo o agregarlo
			consultarPdfPublico(doc_publico_existente);
		} else {
	    	// Se le consulta al usuario si desea sobreescribirlo
    		consultarSobreescrituraPublico(doc_publico_existente);
    	}
    }

    // Si ya existe un documento que se desea cargar
    if (digitalizacion_reservada_existente != 0) {
    	var extensionDigitalizacionReservada = digitalizacion_reservada_existente.substr(digitalizacion_reservada_existente.length - 4);
    	// Si es un PDF
		if (extensionDigitalizacionReservada == '.pdf') {
			// Se le consulta al usuario si desea sobreescribirlo o agregarlo
			consultarDigitalizacion(digitalizacion_reservada_existente, 1);
		} else {
	    	showModal('Atenci&oacute;n', 'No es un documento PDF el que intenta cargar.');
    	}
    }

    // Si ya existe un documento Reservado que se desea cargar
    if (doc_reservado_existente != 0) {
    	var extensionReservado = doc_reservado_existente.substr(doc_reservado_existente.length - 4);
    	// Si es un PDF
		if (extensionReservado == '.pdf') {
			// Se le consulta al usuario si desea sobreescribirlo o agregarlo
			consultarPdfReservado(doc_reservado_existente);
		} else {
	    	// Se le consulta al usuario si desea sobreescribirlo
	    	consultarSobreescrituraReservado(doc_reservado_existente);
	    }
    }

	// No son necesarios en la solapa de Proyectos
	$('#btn_primer_pagina_movil').css('display', 'none');
	$('#btn_primer_pagina').css('display', 'none');
	$('#btn_pagina_anterior').css('display', 'none');
	$('#btn_pagina_siguiente').css('display', 'none');
	$('#btn_ultima_pagina').css('display', 'none');

	// Se utilizan para ver la info del Expediente/Nota/Recomendación Anterior y Siguiente
	$('#btn_expediente_anterior').click(listenerBtnVerExpedienteAnterior);
	$('#btn_expediente_siguiente').click(listenerBtnVerExpedienteSiguiente);
});
