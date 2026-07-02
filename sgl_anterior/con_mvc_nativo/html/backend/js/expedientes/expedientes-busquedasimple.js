/**
 * [generarHtmlBotonAccion description]
 * @param  {[type]} accion      [description]
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-anio="{3}" data-tipo="{4}" data-numero="{5}" data-cuerpo="{6}" data-alcance="{7}" ></span>&nbsp;&nbsp;'.format(
		accion, descripcion, icono,
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance);
}

/**
 * Se genera el link del estado del Proyecto de un expediente determinado
 * @param  {[type]} expediente 		Objeto con la información del Expediente
 * @return {[type]}            		Enlace generado para visualizar el estado del proyecto
 */
function generarLinkEstadoProyecto(expediente) {

	estiloEstadoProyecto = '';
    switch (expediente.estado_proyecto) {
    	// Cargado
    	case 'C':
    		estiloEstadoProyecto = 'resaltado-ok';
    		tituloEstado = 'Proyecto Cargado';
			// Si posee documentos pero NO un original ni un deforma
    		// se muestra la solapa de Proyectos para mostrar dichos documentos
    		if (expediente.url_proyecto == "sin_original_y_deforma") {
    			url = "index.php?c=proyectos&a=view&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}".format(expediente.anio, expediente.tipo, expediente.numero, expediente.cuerpo, expediente.alcance);
    			target = '';
    		// sino, se visualiza el original o el deforma respectivo
    		} else {
				url = expediente.url_proyecto+'?v='+Math.random();// Ruta del archivo del proyecto (proyectos/AAAA/AAENNNNN/original.doc ó deforma.doc)
    			target = 'target="_blank"';
    		}

    		enlace = '&nbsp;<a href="{0}" class="celda_documento {1}" title="{2}" {3} data-anio="{4}" data-tipo="{5}" data-numero="{6}" data-cuerpo="{7}" data-alcance="{8}" >P</a>'.format(
				url, estiloEstadoProyecto, tituloEstado, target,
				expediente.anio, expediente.tipo, expediente.numero, expediente.cuerpo, expediente.alcance);
    		break;

    	// "Para Cargar" ó "Cargado y Para Cargar"
    	case 'PC':
    	case 'C_PC':
    		// Sólo los usuarios de Perfil 1 y 2 deben ver el temporal
    		if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {

    			url = expediente.url_proyecto_temporal+'?v='+Math.random();// Ruta del archivo temporal (Ej: proyectos/temporal/AAENNNNN.doc)

    			enlace = '&nbsp;<a href="{0}" class="celda_documento resaltado-advertencia" title="Proyecto Para Cargar" target="_blank" data-anio="{1}" data-tipo="{2}" data-numero="{3}" data-cuerpo="{4}" data-alcance="{5}" >P</a>'.format(
				url, expediente.anio, expediente.tipo, expediente.numero, expediente.cuerpo, expediente.alcance);

    		} else { // Los usuarios de Bloque Político y de Consulta Web NO deben ver el temporal
    			enlace = '&nbsp;<span class="celda_documento resaltado-advertencia" title="Proyecto Para Cargar">P</span>';
    		}
    		break;

    	// Sin Cargar
    	case 'SC':
    		enlace = '&nbsp;<span class="celda_documento resaltado-alerta" title="Proyecto Sin Cargar">P</span>';
    		break;
    }

    return enlace;
}

/**
 * Se genera el link del estado de Digitalización de un expediente determinado
 * @param  {[type]} expediente 		Objeto con la información del Expediente
 * @return {[type]}            		Enlace generado para visualizar el estado de la digitalización
 */
function generarLinkEstadoDigitalizacion(expediente) {

	// Nombre codificado en base a la clave del expediente
	nombre_codificado_digi = obtenerNombreCodificado(expediente.anio, expediente.tipo, expediente.numero);

    switch (expediente.estado_digitalizacion) {
    	// Digitalización "Cargada" ó "Completa"
    	case 'DC':
    		// URL del archivo de la digitalización
			url = url_directorio_proyectos+'{0}/{1}/{2}.pdf?v={3}'.format(expediente.anio, nombre_codificado_digi, nombre_codificado_digi, Math.random());
    		// Si el expediente tiene seteada la digitalización como Completa o no
    		tituloCelda = (expediente.digi_completa == '1') ? 'DC' : 'D';
    		tituloEstadoDigitalizacion = (expediente.digi_completa == '1') ? 'Digitalizaci&oacute;n Completa' : 'Digitalizaci&oacute;n Cargada';

    		enlace = '&nbsp;<a href="{5}" target="_blank" class="celda_documento resaltado-ok" title="{7}" data-anio="{0}" data-tipo="{1}" data-numero="{2}" data-cuerpo="{3}" data-alcance="{4}" >{6}</a>'.format(
				expediente.anio, expediente.tipo, expediente.numero, expediente.cuerpo, expediente.alcance,
				url, tituloCelda, tituloEstadoDigitalizacion);
    		break;

    	// Digitalización "Para Cargar" ó "Cargada y Para Cargar"
    	case 'DPC':
    	case 'DC_PC':
    		// Sólo los usuarios de Perfil 1 y 2 deben ver el temporal
    		if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {

	    		// URL del archivo de la digitalización temporal
				url = expediente.url_digitalizacion+'?v='+Math.random();

				enlace = '&nbsp;<a href="{0}" target="_blank" class="celda_documento resaltado-advertencia" title="Digitalizaci&oacute;n Para Cargar" data-anio="{1}" data-tipo="{2}" data-numero="{3}" data-cuerpo="{4}" data-alcance="{5}" >D</a>'.format(
				url, expediente.anio, expediente.tipo, expediente.numero, expediente.cuerpo, expediente.alcance);

			} else { // Los usuarios de Bloque Político y de Consulta Web NO deben ver el temporal
    			enlace = '&nbsp;<span class="celda_documento resaltado-advertencia" title="Digitalizaci&oacute;n Para Cargar">D</span>';
    		}
    		break;

    	// Digitalización "Sin cargar"
    	case 'DSC':
      		enlace = '&nbsp;<span class="celda_documento resaltado-alerta" title="Digitalizaci&oacute;n Sin Cargar">D</span>';
    		break;

    	// 09/02/2023 XXXX
    	// --------------------
    	// Expediente Electrónico SIN documentos
    	case 'EESD':
    		enlace = '&nbsp;<span class="celda_documento resaltado-alerta" title="Expediente Electr&oacute;nico sin actuaciones">E</span>';
    		break;

    	// Expediente Electrónico CON documentos
    	case 'EECD':
    		enlace = '&nbsp;<a href="index.php?c=expedienteselec&a=view&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}" class="celda_documento resaltado-ok" title="Expediente Electr&oacute;nico con actuaciones">E</a>'.format(
    			expediente.anio, expediente.tipo, expediente.numero, expediente.cuerpo, expediente.alcance);
    		break;
    }

	return enlace;
}

/**
 * Modificado el 27/02/2019 por XXXX
 * para asemejar el estado del Proyecto de la versión 1
 *
 * [callbackRenderAcciones description]
 * @param  {[type]} data [description]
 * @param  {[type]} type [description]
 * @param  {[type]} full [description]
 * @param  {[type]} meta [description]
 * @return {[type]}      [description]
 */
var callbackRenderAcciones = function (data, type, full, meta) {
	buttonAction = '';

	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2)
    	buttonAction += generarHtmlBotonAccion('edit', 'Editar Expediente', 'pencil', full);

    if (perfil_usuario_actual == 1) {
    	// 2026-03-19 XXXX, sólo se permite eliminar si NO posee documentos
    	if (full.estado_digitalizacion === 'EESD') {
    		buttonAction += generarHtmlBotonAccion('delete', 'Eliminar Expediente', 'trash', full);
    	} else {
    		buttonAction += '<span title="No se permite eliminar, posee documentaci&oacute;n electr&oacute;nica asociada">---&nbsp;&nbsp;</span>';
    	}
    }

    // Sólo los Perfiles 1 y 2 pueden imprimir y generar la etiqueta
    if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {
    	buttonAction += generarHtmlBotonAccion('imprimir', 'Imprimir Expediente', 'print', full);
    	buttonAction += generarHtmlBotonAccion('etiqueta', 'Generar Etiqueta de Expediente', 'list-alt', full);
    }

	// Se genera el link del estado del Proyecto del expediente respectivo
	buttonAction += generarLinkEstadoProyecto(full);

	// Se genera el link del estado de Digitalización del expediente respectivo
	buttonAction += generarLinkEstadoDigitalizacion(full);

	return buttonAction;
};

/**
 * [callbackRenderAgregadoA description]
 * @param  {[type]} data [description]
 * @param  {[type]} type [description]
 * @param  {[type]} full [description]
 * @param  {[type]} meta [description]
 * @return {[type]}      [description]
 */
var callbackRenderAgregadoA = function (data, type, full, meta) {
	// Si existe el año del agregado_a
	if (full.agregado_anio !== null && full.agregado_anio != 0)
	{
		valor_agregado_cuerpo = (full.agregado_cuerpo != null) ? full.agregado_cuerpo : 0;
		valor_agregado_alcance = (full.agregado_alcance != null) ? full.agregado_alcance : 0;

		return '<a href="index.php?c=expedientes&a=view&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}">{0} - {1} - {2} - {3} - {4}</a>'.format(
			full.agregado_anio, full.agregado_tipo, full.agregado_numero, valor_agregado_cuerpo, valor_agregado_alcance);
	}
	else
		return '&nbsp;';
}

/**
 * [listenerBotonAccion description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBotonAccion = function (event) {
	event.preventDefault(event); // Prevengo que el link haga lo propio al hacerle click

	// Obtengo la acción
	accion    = $(this).data('accion');
	// Obtenemos la clave
	f_anio    = $(this).data('anio');
	f_tipo    = $(this).data('tipo');
	f_numero  = $(this).data('numero');
	f_cuerpo  = $(this).data('cuerpo');
	f_alcance = $(this).data('alcance');

	// Si se edita un Expediente
	if (accion == 'edit')
		$(location).attr('href','index.php?c=expedientes&a=edit&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}'.format(
			f_anio, f_tipo, f_numero, f_cuerpo, f_alcance ));
	// Si se elimina un Expediente
	else if (accion == 'delete') {
		item = dataTableRef.row($(this).parents('tr')).data();
		eliminarExpediente(item);
	}
	else if (accion == 'imprimir') {
		var url = 'index.php?c=expedientes&a=generarpdffichaexpediente&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}'.format(
			f_anio, f_tipo, f_numero, f_cuerpo, f_alcance);

		// Se muestra el pdf en una nueva pestaña
        window.open(url);
	}
	else if (accion == 'etiqueta') {
		iniciarActuacion('expe_elec_caratular', {anio: f_anio, tipo: f_tipo, numero: f_numero, cuerpo: f_cuerpo, alcance: f_alcance});
	}
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'. Expediente: {1}-{2}-{3}-{4}-{5}'.format(accion, f_anio, f_tipo, f_numero, f_cuerpo, f_alcance));
};

/**
 * Se envía el expediente respectivo para su eliminación
 * @param  {[type]} expediente [description]
 * @return {[type]}            [description]
 */
function eliminarExpediente (expediente) {
	showModal('Atenci&oacute;n', '¿Est&aacute; seguro que desea eliminar el Expediente: {0}-{1}-{2}-{3}-{4}?'.format(expediente.anio, expediente.tipo, expediente.numero, expediente.cuerpo, expediente.alcance),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					url: "index.php?c=expedientes&a=delete",
					dataType: 'json',
					data: JSON.stringify(expediente)
				})
				.done(function( respuesta ) {
					if (respuesta.estado == 'OK') {
						if ((respuesta.data != null) && (typeof respuesta.data.expediente != 'undefined') && (typeof respuesta.data.expediente_siguiente != 'undefined')) {

							// Muestro el ultimo expediente (despues de eliminar con exito)
							$('#f_anio').val(respuesta.data.expediente_siguiente.anio);
							$('#f_tipo').val(respuesta.data.expediente_siguiente.tipo);
							$('#f_numero').val(respuesta.data.expediente_siguiente.numero);
							$('#f_cuerpo').val(respuesta.data.expediente_siguiente.cuerpo);
							$('#f_alcance').val(respuesta.data.expediente_siguiente.alcance);

							// Refrescar grilla
							dataTableRef.ajax.reload(null, false);
						}
						else
							showModal('Error', 'Se esperaba un expediente y no se recibieron resultados.');
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
 * [listenerBtnPrimerPagina description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBtnPrimerPagina = function (event) {
	if (expedientePrimerPagina != null) {
		$('#f_anio').val(expedientePrimerPagina.anio);
		$('#f_tipo').val(expedientePrimerPagina.tipo);
		$('#f_numero').val(expedientePrimerPagina.numero);
		$('#f_cuerpo').val(expedientePrimerPagina.cuerpo);
		$('#f_alcance').val(expedientePrimerPagina.alcance);
		dataTableRef.ajax.reload();
	} else
		showModal('Error', 'No se puede determinar cual es el primer expediente.');
};

/**
 * [listenerBtnPaginaAnterior description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBtnPaginaAnterior = function (event) {
	if (expedientePaginaAnterior != null) {
		$('#f_anio').val(expedientePaginaAnterior.anio);
		$('#f_tipo').val(expedientePaginaAnterior.tipo);
		$('#f_numero').val(expedientePaginaAnterior.numero);
		$('#f_cuerpo').val(expedientePaginaAnterior.cuerpo);
		$('#f_alcance').val(expedientePaginaAnterior.alcance);
		dataTableRef.ajax.reload();
	} else
		showModal('Error', 'No se puede determinar cual es el primer expediente de la p&aacute;gina anterior.');
};

/**
 * [listenerBtnPaginaSiguiente description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBtnPaginaSiguiente = function (event) {
	if (expedientePaginaSiguiente != null) {
		$('#f_anio').val(expedientePaginaSiguiente.anio);
		$('#f_tipo').val(expedientePaginaSiguiente.tipo);
		$('#f_numero').val(expedientePaginaSiguiente.numero);
		$('#f_cuerpo').val(expedientePaginaSiguiente.cuerpo);
		$('#f_alcance').val(expedientePaginaSiguiente.alcance);
		dataTableRef.ajax.reload();
	}
	else if (expedienteUltimaPagina != null) {
		$('#f_anio').val(expedienteUltimaPagina.anio);
		$('#f_tipo').val(expedienteUltimaPagina.tipo);
		$('#f_numero').val(expedienteUltimaPagina.numero);
		$('#f_cuerpo').val(expedienteUltimaPagina.cuerpo);
		$('#f_alcance').val(expedienteUltimaPagina.alcance);
		dataTableRef.ajax.reload();
	}
};

/**
 * [listenerBtnUltimaPagina description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBtnUltimaPagina = function (event) {
	if (expedienteUltimaPagina != null) {
		$('#f_anio').val(expedienteUltimaPagina.anio);
		$('#f_tipo').val(expedienteUltimaPagina.tipo);
		$('#f_numero').val(expedienteUltimaPagina.numero);
		$('#f_cuerpo').val(expedienteUltimaPagina.cuerpo);
		$('#f_alcance').val(expedienteUltimaPagina.alcance);
		dataTableRef.ajax.reload();
	} else
		showModal('Error', 'No se puede determinar cual es el último expediente.');
};

/**
 * [listenerTableRowClick description]
 * @return {[type]} [description]
 */
var listenerTableRowClick = function (e) {
	// Se deshabilita el evento cuando el clic se hace dentro de un boton de accion en la fila (TR)
 	var fila = ($(e.target).hasClass('btn-accion-contenido')) ? null : dataTableRef.row(this).data();

	// Si tengo una fila válida...
	if (fila != null) {
		// Marco la fila seleccionada
		if ( $(this).hasClass('fila-destacada') )
	        $(this).removeClass('fila-destacada');
	    else {
	        dataTableRef.$('tr.fila-destacada').removeClass('fila-destacada');
	        $(this).addClass('fila-destacada');
	    }

	    // Invoco la vista previa @ expedientes-busquedasimple-common.js
		vistaPreviaExpediente(fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance);

		// Actualizo la clave del navegador de expedientes
		$('#f_anio').val(fila.anio);
		$('#f_tipo').val(fila.tipo);
		$('#f_numero').val(fila.numero);
		$('#f_cuerpo').val(fila.cuerpo);
		$('#f_alcance').val(fila.alcance);
	}
};

/**
 * [callbackDrawCallback description]
 * @param  {[type]} settings [description]
 * @return {[type]}          [description]
 */
var callbackDrawCallback = function(settings) {
	expedientePrimerPagina 	  = settings.json.expedientePrimerPagina; // Referencia al expediente de la primer página
	expedientePaginaAnterior  = settings.json.expedientePaginaAnterior; // Referencia al expediente de la página anterior
	expedientePaginaActual    = settings.json.expedientePaginaActual; // Referencia al expediente de la página actual
	expedientePaginaSiguiente = settings.json.expedientePaginaSiguiente; // Referencia al expediente de la página siguiente
	expedienteUltimaPagina    = settings.json.expedienteUltimaPagina; // Referencia al expediente de la última página

	// Actualizo los controles del filtro
	if (expedientePaginaActual != null) {
		$('#f_anio').val(expedientePaginaActual.anio);
		$('#f_tipo').val(expedientePaginaActual.tipo);
		$('#f_numero').val(expedientePaginaActual.numero);
		$('#f_cuerpo').val(expedientePaginaActual.cuerpo);
		$('#f_alcance').val(expedientePaginaActual.alcance);
	}

	// Actualizo botones
	$('.btn_primer_pagina').prop('disabled', expedientePaginaAnterior == null);
	$('.btn_pagina_anterior').prop('disabled', expedientePaginaAnterior == null);

    // Si se trata del último expediente
    if (expedienteUltimaPagina != null &&
    	expedientePaginaActual.anio == expedienteUltimaPagina.anio &&
    	expedientePaginaActual.tipo == expedienteUltimaPagina.tipo &&
    	expedientePaginaActual.numero == expedienteUltimaPagina.numero) {
    	// Se deshabilitan los botones Siguiente y Último
		$('.btn_pagina_siguiente').prop('disabled', true);
		$('.btn_ultima_pagina').prop('disabled', true);
	} else {
		// Se habilitan los botones Siguiente y Último
		$('.btn_pagina_siguiente').prop('disabled', false);
		$('.btn_ultima_pagina').prop('disabled', false);
	}

	// simulamos un click en la ULTIMA fila (como en la versión 1 del SGL)
	// Se reemplaza first por last ( 07/08/2017, XXXX)
	// *********************************************************************
	// Al redibujar la grilla (pasar de página, busqueda "inline", etc)
	// simulamos un click en la última fila del cuerpo de la grilla
	// para actualizar la vista previa. Verifico tener filas de resultados...
	// Es la única solapa donde no se utiliza actualizarVistaSiExisteExpediente() porque
	// tiene un comportamiento diferente (tiene que simular un click en la grilla).
	if (settings.json.recordsFiltered > 0) {
		$(idTabla+' tbody tr:last').trigger('click');//se reemplazó first por last
		ocultarErrorGrilla();
	} else {
		// Si no hay resultados, en vez de simular un click, invoco la vista previa @ expedientes-busquedasimple-common.js
		vistaPreviaExpediente($('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(),	$('#f_cuerpo').val(), $('#f_alcance').val() );
		mostrarErrorGrilla('<span class="glyphicon glyphicon-exclamation-sign anim-vibrar"></span> El expediente <strong>{0} - {1} - {2} - {3} - {4}</strong> no existe.'.format($('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val()));
	}

	// 17/08/2021 XXXX
	// Sólo para perfil 1 o 2, y que recién haya ingresado al sistema
	if ((perfil_usuario_actual == 1 || perfil_usuario_actual == 2) && verificacion_ppc_hecha === '0') {
		// Se marca como verificada
		verificacion_ppc_hecha = 1;
		// Se muestra la modal con la notificación al usuario respectivo
		verificarExpedientesEnPPCVencidos();
	}
};

/**
 * [setDataTable description]
 * @param {[type]} ajaxUrl [description]
 * @return DataTable
 */
function setDataTable(ajaxUrl) {
	// Borro y creo la grilla (tabla)
	idTabla = '#grillaExpedientes';
	idTablaContainer = '{0}Container'.format(idTabla);

	// *** 08/08/2017 XXXX ********
	// se reemplazó generarGrillaHtml para definir las dimensiones de cada thead
	// (por el scroll vertical, es necesario alinear cada columna del encabezado de la tabla)
	html_grilla  = '<table class="table" id="grillaExpedientes">';
	html_grilla += 		'<thead class="color-fondo-menu">';
	html_grilla += 			'<th>&nbsp;</th>';
	html_grilla += 			'<th>Expediente</th>';
	html_grilla += 			'<th>Iniciador</th>';
	html_grilla += 			'<th>C&oacute;digo</th>';
	html_grilla += 			'<th>Fecha entrada</th>';
	html_grilla += 			'<th>Car&aacute;tula</th>';
	html_grilla += 			'<th>Agregado a</th>';
	html_grilla += 		'</thead>';
	// El Id del tbody lo utilizamos luego para desplazar el scroll a la última fila de cada página
	html_grilla += '<tbody id="cuerpo_grilla_expedientes"></tbody></table>';

	// Se vacía el contenedor de la tabla
	$(idTablaContainer).empty();
	// Se añade la grilla, con su encabezado y sin contenido
	$(idTablaContainer).append(html_grilla);

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
	        // se quitó 'l' (el combo del límite de registros por página)
	        dom: 't', // Los elementos de control de la grilla y en qué orden (https://datatables.net/reference/option/dom).
	        ordering: false,
			responsive: true,
			autoWidth: false, // Se cambió a false por un bug del Firefox, que no renderizaba correctamente el Datatable
			scrollY: '100%',// scroll vertical
			scrollCollapse: true,
        	language: { url: '../librerias/datatables/localisation/es_AR.json' },
			columnDefs: [
				{ targets: '_all', className: 'text-left', searchable: false }
			],
			columns: [
				{ data: null, className: 'text-center', render: callbackRenderAcciones },
				{ data: null, render: function (data, type, full, meta) { return '{0} - {1} - {2} - {3} - {4}'.format(full.anio, full.tipo, full.numero, full.cuerpo, full.alcance); } },
				{ data: 'iniciador_tipo' },
				{ data: 'iniciador_codigo' },
				{ data: 'fecha_entrada_expe', className: 'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.fecha_entrada_expe); } },
				{ data: 'caratula' },
				{ data: null, render: callbackRenderAgregadoA }
			],
			drawCallback: callbackDrawCallback
		});

	// Asigno eventos a la tabla
	$(idTabla+' tbody').on('click', 'tr', listenerTableRowClick);

	return tabla;
};

/**
 * Variables globales
 */
// var dataTableRef; @ expedientes-busquedasimple-common.js
// var expedienteRef; @ expedientes-busquedasimple-common.js
// var formBusquedaRef; @ expedientes-busquedasimple-common.js
// var proyectoNroRef; @ expedientes-busquedasimple-common.js
var expedientePrimerPagina; 	// Referencia al expediente de la primer página
var expedientePaginaAnterior; 	// Referencia al expediente de la página anterior
var expedientePaginaActual; 	// Referencia al expediente de la página actual
var expedientePaginaSiguiente; 	// Referencia al expediente de la página siguiente
var expedienteUltimaPagina; 	// Referencia al expediente de la última página

var url_directorio_proyectos = '../../expedientes/proyectos/';

/**
 * Entry Point de jQuery
 */
$(document).ready(function() {
	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	asignarValidatorExpresionesRegulares();

	// Genero la tabla
	dataTableRef = setDataTable('index.php?c=expedientes&a=busquedasimpledatagrid');

	// Inicializo el formulario
	setearComportamientoSolapas(); // @ expedientes-busquedasimple-common.js
	setearComportamientoBotonesNavegacion(); // @ expedientes-busquedasimple-common.js
	setearComportamientoValidate(); // @ expedientes-busquedasimple-common.js

	// Comportamiento de botones extra
	$('#btn_nuevo_expediente').click(function () { $(location).attr('href','index.php?c=expedientes&a=add'); });

	// Listeners de botones de paginación (por clase, porque se repiten)
	$('.btn_primer_pagina').click(listenerBtnPrimerPagina);
	$('.btn_pagina_anterior').click(listenerBtnPaginaAnterior);
	$('.btn_pagina_siguiente').click(listenerBtnPaginaSiguiente);
	$('.btn_ultima_pagina').click(listenerBtnUltimaPagina);

	// 09/09/2020 XXXX
	// No se utilizan en la solapa de Expedientes, solamente se utilizan en el resto de las solapas
	$('#btn_expediente_anterior').css('display', 'none');
	$('#btn_expediente_siguiente').css('display', 'none');

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

	$('#btn_nuevo_expediente').css('display', 'inline');

	// Se resalta la solapa de Expedientes
	$('#solapa_expedientes').addClass('active');

    // Se muestra el ítem TAREAS del menú principal
    $('#menu_item_tareas').css("display", "inline-block");

});
