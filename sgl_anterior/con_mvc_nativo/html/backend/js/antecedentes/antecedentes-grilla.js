/**
 * [generarHtmlBotonAccion description]
 * @param  {[type]} accion      [description]
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-anio="{3}" data-tipo="{4}" data-numero="{5}" data-cuerpo="{6}" data-alcance="{7}" data-anio_a="{8}" data-tipo_a="{9}" data-numero_a="{10}" data-digito_a="{11}" data-cuerpo_a="{12}" data-alcance_a="{13}" data-cuerpoalcance_a="{14}" data-anexoalcance_a="{15}" data-cuerpoanexoalcance_a="{16}" data-anexo_a="{17}" data-cuerpoanexo_a="{18}"></span>&nbsp;&nbsp;'.format(
		accion, descripcion, icono,
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance,
		fila.anio_a, fila.tipo_a, fila.numero_a, fila.digito_a, fila.cuerpo_a, fila.alcance_a,
		fila.cuerpoalcance_a, fila.anexoalcance_a, fila.cuerpoanexoalcance_a, fila.anexo_a, fila.cuerpoanexo_a);
}

// Retorna una Promise
function copyToClipboard(textToCopy) {
    // navigator clipboard api needs a secure context (https)
    if (navigator.clipboard && window.isSecureContext) {
        // navigator clipboard api method
        return navigator.clipboard.writeText(textToCopy);
    } else {
        // text area method
        let textArea = document.createElement("textarea");
        textArea.value = textToCopy;
        // make the textarea out of viewport
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        return new Promise((res, rej) => {
            // here the magic happens
            document.execCommand('copy') ? res() : rej();
            textArea.remove();
        });
    }
}

function copiarTexto(texto) {
  	// Se copia el texto al portapapeles
  	//navigator.clipboard.writeText(texto);
  	copyToClipboard(texto)
    	.then(() => console.log('Texto copiado !'))
    	.catch(() => console.log('error'));

  	// Se muestra el resultado del copiado
  	$('#resultado_copiado').removeClass('display_none');

  	// A los dos segundos se retira
  	setTimeout(function() {
		$('#resultado_copiado').addClass('display_none');
	}, 2000);
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
    	buttonAction += generarHtmlBotonAccion('edit', 'Editar Antecedente', 'pencil', full);
    	buttonAction += generarHtmlBotonAccion('delete', 'Eliminar Antecedente', 'trash', full);
    }

	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

var callbackRenderAccionVerObservaciones = function (full) {
	// Si posee observaciones
	if (full.observaciones_antecedentes !== null) {

		// Icono para visualizar las Observaciones al acercarse con el mouse
		let icono_ver = '<span class="glyphicon glyphicon-comment" title="{0}"></span>'.format(full.observaciones_antecedentes);

		// Icono para Copiar al portapapeles el texto de la las Observaciones
		let icono_copiar = '<span class="glyphicon glyphicon-floppy-saved" role="button" onclick="copiarTexto(\'{0}\')" title="Copiar las Observaciones al portapapeles"></span>'.format(full.observaciones_antecedentes);

		let resultado_copiado = '&nbsp;<span id="resultado_copiado" class="padding_5 bg-success display_none">Ok!</span>';

		return icono_ver+'&nbsp;&nbsp;&nbsp;'+icono_copiar+resultado_copiado;
	}
	else
		return '';
};

/**
 * Acción para Ir al Antecedente de un expediente determinado
 * @param  {[type]} data [description]
 * @param  {[type]} type [description]
 * @param  {[type]} full [description]
 * @param  {[type]} meta [description]
 * @return {[type]}      [description]
 */
var callbackRenderAccionIrAlAntecedente = function (data, type, full, meta) {
	buttonAction = '';
	if ((full.tipo_a == 'E') || (full.tipo_a == 'N'))
    	buttonAction += generarHtmlBotonAccion('ir', 'Ir al Antecedente', 'share-alt', full);

	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
};

/**
 * Acción para tomar los documentos de un directorio de expedientes del Depto. Ejecutivo
 * y cargarlos en el sistema, para el expediente respectivo
 * @param  {[type]} data [description]
 * @param  {[type]} type [description]
 * @param  {[type]} full [description]
 * @param  {[type]} meta [description]
 * @return {[type]}      [description]
 */
var callbackRenderAccionCargarDocumentosExpedienteDE = function (data, type, full, meta) {
	buttonAction = '';
	// Sólo para expedientes del D.E.
	if ( full.tipo_a == 'D' && full.existe_directorio_expe_depto_ejecutivo )
    	buttonAction += generarHtmlBotonAccion('mostrar_documentos_de', 'Cargar documentos', 'open', full);

	// fix para evitar que haga "wrap" en los controles
	return '<span style="white-space: nowrap">{0}</span>'.format(buttonAction);
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

	f_anio_a    = $(this).data('anio_a');
	f_tipo_a    = $(this).data('tipo_a');
	f_numero_a  = $(this).data('numero_a');
	f_digito_a  = $(this).data('digito_a');
	f_cuerpo_a  = $(this).data('cuerpo_a');
	f_alcance_a = $(this).data('alcance_a');

	f_cuerpoalcance_a 	   = $(this).data('cuerpoalcance_a');
	f_anexoalcance_a 	   = $(this).data('anexoalcance_a');
	f_cuerpoanexoalcance_a = $(this).data('cuerpoanexoalcance_a');
	f_anexo_a 			   = $(this).data('anexo_a');
	f_cuerpoanexo_a 	   = $(this).data('cuerpoanexo_a');

	if (accion == 'edit')
		$(location).attr('href','index.php?c=antecedentes&a=edit&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_anio_a={5}&f_tipo_a={6}&f_numero_a={7}&f_digito_a={8}&f_cuerpo_a={9}&f_alcance_a={10}&f_cuerpoalcance_a={11}&f_anexoalcance_a={12}&f_cuerpoanexoalcance_a={13}&f_anexo_a={14}&f_cuerpoanexo_a={15}'.format(
			f_anio, f_tipo, f_numero, f_cuerpo, f_alcance,
			f_anio_a, f_tipo_a, f_numero_a, f_digito_a, f_cuerpo_a, f_alcance_a,
			f_cuerpoalcance_a, f_anexoalcance_a, f_cuerpoanexoalcance_a, f_anexo_a, f_cuerpoanexo_a));
	else if (accion == 'delete') {
		item = dataTableRef.row($(this).parents('tr')).data();
		eliminarAntecedente(item);
	}
	else if (accion == 'ir') {
		// Sólo si el Antecedente es un Expediente o una Nota
		if ((f_tipo_a == 'E') || (f_tipo_a == 'N'))
			$(location).attr('href','index.php?c=expedientes&a=view&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}'.format(
				f_anio_a, f_tipo_a, f_numero_a, f_cuerpo_a, f_alcance_a));
	}
	else if (accion == 'mostrar_documentos_de') {
		// Sólo si el Antecedente es un expediente del Depto. Ejecutivo (se verifica nuevamente por las dudas)
		if (f_tipo_a == 'D')
		 	$(location).attr('href','index.php?c=antecedentes&a=mostrarDocumentosDE&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}&f_anio_a={5}&f_tipo_a={6}&f_numero_a={7}&f_digito_a={8}'.format(
		 		f_anio, f_tipo, f_numero, f_cuerpo, f_alcance, f_anio_a, f_tipo_a, f_numero_a, f_digito_a));
	}
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'. Antecedente: {1}-{2}-{3}-{4}-{5}-{6}-{7}-{8}-{9}-{10}-{11}-{12}-{13}-{14}-{15}-{16}'.format(
			accion,
			f_anio, f_tipo, f_numero, f_cuerpo, f_alcance,
			f_anio_a, f_tipo_a, f_numero_a, f_digito_a, f_cuerpo_a, f_alcance_a,
			f_cuerpoalcance_a, f_anexoalcance_a, f_cuerpoanexoalcance_a, f_anexo_a, f_cuerpoanexo_a
		));
};

/**
 * Se envía el Antecedente respectivo para su eliminación
 * @param  {[type]} antecedente [description]
 * @return {[type]}            [description]
 */
function eliminarAntecedente (antecedente) {
	showModal('Atenci&oacute;n', '¿Est&aacute; seguro que desea eliminar el Antecedente del Expediente: {0}-{1}-{2}-{3}-{4}?'.format(antecedente.anio, antecedente.tipo, antecedente.numero, antecedente.cuerpo, antecedente.alcance),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					url: "index.php?c=antecedentes&a=delete",
					dataType: 'json',
					data: JSON.stringify(antecedente)
				})
				.done(function( respuesta ) {
					if (respuesta.estado == 'OK') {
						if ((respuesta.data != null) && (typeof respuesta.data.antecedente != 'undefined'))
							// Refrescar grilla
							dataTableRef.ajax.reload(null, false);
						else
							showModal('Error', 'Se esperaba un Antecedente y no se recibieron resultados.');
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
		$('#btn_nuevo_antecedente').prop('disabled', false); // Se habilita el botón 'Nuevo Antecedente'
	else // si NO existe
		$('#btn_nuevo_antecedente').prop('disabled', true); // Se deshabilita el botón 'Nuevo Antecedente'
};

/**
 * Se definen los títulos de la grilla
 */
function definirTitulosGrilla() {

	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2)
		titulos_grilla = new Array(
									'&nbsp;',
									'A&ntilde;o',
									'Tipo',
									'N&uacute;mero',
									'D&iacute;gito',
									'Cpo.',
									'Alc.',
									'Cpo. Alc.',
									'Anexo Alc.',
									'Cpo. Anexo Alc.',
									'Anexo',
									'Cpo. Anexo',
									'Observ.',
									'Ver Antec.',
									'Cargar Doc.' // Columna para Cargar Documentos del Ejecutivo
								  );
	else
		titulos_grilla = new Array(
									'A&ntilde;o',
									'Tipo',
									'N&uacute;mero',
									'D&iacute;gito',
									'Cpo.',
									'Alc.',
									'Cpo. Alc.',
									'Anexo Alc.',
									'Cpo. Anexo Alc.',
									'Anexo',
									'Cpo. Anexo',
									'Observ.',
									'Ver Antec.'
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
							{data: 'anio_a', className: 'text-center'},
							{data: 'tipo_a'},
							{data: 'numero_a', className: 'text-right'},
							{data: 'digito_a'},
							{data: 'cuerpo_a'},
							{data: 'alcance_a'},
							{data: 'cuerpoalcance_a'},
							{data: 'anexoalcance_a'},
							{data: 'cuerpoanexoalcance_a'},
							{data: 'anexo_a'},
							{data: 'cuerpoanexo_a'},
							{data: null, className: 'text-center', render: callbackRenderAccionVerObservaciones},
							{data: null, className: 'text-center', render: callbackRenderAccionIrAlAntecedente}, // Columna para ir al Expediente
							{data: null, className: 'text-center', render: callbackRenderAccionCargarDocumentosExpedienteDE} // Columna para Cargar Documentos del Ejecutivo
					      ];
	else
		columnas_grilla = [
							{data: 'anio_a', className: 'text-center'},
							{data: 'tipo_a'},
							{data: 'numero_a', className: 'text-right'},
							{data: 'digito_a'},
							{data: 'cuerpo_a'},
							{data: 'alcance_a'},
							{data: 'cuerpoalcance_a'},
							{data: 'anexoalcance_a'},
							{data: 'cuerpoanexoalcance_a'},
							{data: 'anexo_a'},
							{data: 'cuerpoanexo_a'},
							{data: null, className: 'text-center', render: callbackRenderAccionVerObservaciones},
							{data: null, className: 'text-center', render: callbackRenderAccionIrAlAntecedente} // Columna para ir al Expediente
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
	idTabla = '#grillaAntecedentes';
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
			stateSave: false, // por defecto es true
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
				{ className: 'text-center', targets: '_all', searchable: false }
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
	controlador_activo = 'antecedentes';

	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	asignarValidatorExpresionesRegulares();

	// Genero la tabla
	dataTableRef = setDataTable('index.php?c=antecedentes&a=datagrid&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());

	// Inicializo el formulario
	setearComportamientoSolapas(); // @ expedientes-busquedasimple-common.js
	setearComportamientoBotonesNavegacion(); // @ expedientes-busquedasimple-common.js
	setearComportamientoValidate(); // @ expedientes-busquedasimple-common.js

	// Comportamiento de botones extra
	$('#btn_nuevo_antecedente').click(function () {
		$(location).attr('href','index.php?c=antecedentes&a=add&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());
	});

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

	$('#btn_nuevo_expediente').css('display', 'none');

	$('#solapa_antecedentes').addClass('active');

	// 09/09/2020 XXXX
	// ---------------------
	// No son necesarios en la solapa de Antecedentes
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
