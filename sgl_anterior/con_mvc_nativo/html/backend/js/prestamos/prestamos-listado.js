/**
 * [generarHtmlBotonAccion description]
 * @param  {[type]} accion      [description]
 * @param  {[type]} descripcion [description]
 * @param  {[type]} icono       [description]
 * @param  {[type]} fila        [description]
 * @return {[type]}             [description]
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-anio="{3}" data-tipo="{4}" data-numero="{5}" data-cuerpo="{6}" data-alcance="{7}" ></span>&nbsp;'.format(
		accion, descripcion, icono, 
		fila.anio, fila.tipo, fila.numero, fila.cuerpo, fila.alcance);
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
    buttonAction += generarHtmlBotonAccion('edit', 'Editar Antecedente', 'pencil', full);
    buttonAction += generarHtmlBotonAccion('eliminar', 'Eliminar Antecedente', 'remove', full);
   
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

	// if (accion == 'edit')
	// 	$(location).attr('href','index.php?c=antecedente&a=edit&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}'.format(
	// 		f_anio, f_tipo, f_numero, f_cuerpo, f_alcance ));
	// else
	showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'. Prestamo: {1}-{2}-{3}-{4}-{5}'.format(accion, f_anio, f_tipo, f_numero, f_cuerpo, f_alcance));
};

/**
 * Se muestra la vista previa del expediente del buscador
 * 
 * @return {[type]} [description]
 */
function mostrarVistaPrevia()
{
	// Peticion asíncrona
	$.ajax({
		method: "GET",
		url: "index.php",
		data: {c: "expedientes", a: "obtenerexpediente", 
			f_anio: $('#f_anio').val(), 
			f_tipo: $('#f_tipo').val(), 
			f_numero: $('#f_numero').val(), 
			f_cuerpo: $('#f_cuerpo').val(), 
			f_alcance: $('#f_alcance').val() }
	}).done(function( data ) {
		// parseo resultados
		var jsonData = $.parseJSON(data);

		// muestro datos
		if (jsonData.estado == "OK") {
			if (jsonData.data != null) {
				// Actualizo la referencia al expediente y la vista previa
				expedienteRef = jsonData.data;
				actualizarVistaPrevia();
			} else 
                showModal('Error', 'Se esperaba un expediente y no se recibieron resultados.');
        } else 
            showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {
        showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
    });
};

/**
 * [actualizarVistaPrevia description]
 * @return {[type]} [description]
 */
function actualizarVistaPrevia() {
	e = expedienteRef;

	// Se muestra la clave del Expediente|Nota|Recomendación
	$('#prev_expediente').html('{0} - {1} - {2} - {3} - {4}'.format(e.anio, e.tipo, e.numero, e.cuerpo, e.alcance));

	// Se muestra la Carátula
	$('#prev_caratula').html(e.caratula);

	// Se muestra la descripción del Iniciador
	$('#prev_iniciador').html("{0} - {1}".format(e.iniciador_codigo, e.ro_iniciador_descripcion_grp));

	// Se muestra la descripción de la categoría
	$('#prev_categoria').html("{0} - {1}".format(e.id_codcategoria, e.ro_descripcion_categoria));
	
	// Autores **********************************************************
	// Cantidad de Autores
	var cantidad_autores = e.autores._items.length;

	// Título según su cantidad
	var titulo_para_autores = (cantidad_autores > 1) ? cantidad_autores+' Autores' : '1 Autor';
	$('#prev_titulo_autores').html(titulo_para_autores);

	// Se muestran los Autores en un combobox
	$('#prev_autores').html('');
	$.each(e.autores._items, function(index, value) {
    	$('#prev_autores').append($("<option />").text("{0} - {1}".format(value.autor_codigo, value.ro_descripcion_grp)));
	});

	// Temas **********************************************************
	// Cantidad de Temas
	var cantidad_temas = e.temas._items.length;

	// Título según su cantidad
	var titulo_para_temas = (cantidad_temas > 1) ? cantidad_temas+' Temas' : '1 Tema';
	$('#prev_titulo_temas').html(titulo_para_temas);
	
	// Se muestran los Temas en un combobox
	$('#prev_temas').html('');
	$.each(e.temas._items, function(index, value) {
		$('#prev_temas').append($("<option />").text("{0} - {1}".format(value.id_codtema, value.ro_descripcion_tema)));
	});
	
	// Último Estado **********************************************************
	// Se muestra el último Estado
	var estado_actual = null;

	if (e.estados._items.length > 0)
	{
		estado_actual = e.estados._items[e.estados._items.length-1];
		fecha_estado = formatearFechaConBarras(estado_actual.fecha_estado);

		$('#prev_estado').html("{0} - {1} ({2})".format(estado_actual.id_codestado, estado_actual.ro_nombre_estado, fecha_estado));
	}
	else
		$('#prev_estado').html('No se puede determinar Estado');

	// Última Comisión **********************************************************
	// Si posee un Estado...
	if (estado_actual != null){
		// Si este estado requiere tratamiento en comision, la buscamos
		if(estado_actual.ro_tratamiento_comision == 1){
			// Se muestra la última Comisión
			if (e.giros._items.length > 0){
				comision_actual = e.giros._items[e.giros._items.length-1];
				$('#prev_comision').html("{0} - {1}".format(comision_actual.comision_codigo, comision_actual.ro_descripcion_grp));
			} 
			else
				$('#prev_comision').html('No se puede determinar Comisi&oacute;n');
		}
		else
			$('#prev_comision').html('No se encuentra en Comisi&oacute;n');
	}

	// Proyectos **********************************************************
	// Se muestran los Proyectos, de a uno por vez, utilizando las flechas de anterior|siguiente
	if (e.proyectos._items.length > 0) 
		actualizarVistaPreviaProyecto(0);
	else {
		//TODO: limpio la vista de proyectos
	}

	// Observaciones **********************************************************
	// Se muestra la Observación
	$('#prev_observaciones_expe').html(e.observaciones_expe);

	// Usuario **********************************************************
	// Se muestra el Usuario que operó en el Expediente|Nota|Recomendación
	$('#prev_usuario').html("{0} ({1})".format(e.ro_codigo_usuario, e.ro_nombre_usuario));
}

/**
 * [actualizarVistaPreviaProyecto description]
 * @param  {[type]} nroProyecto [description]
 * @return {[type]}             [description]
 */
function actualizarVistaPreviaProyecto(nroProyecto) {
	// Verifico cotas de nro de proyecto
	if ((nroProyecto >= 0) && (nroProyecto <= (expedienteRef.proyectos._items.length-1))) {
		// Actualizo la referencia global (para saber donde estoy parado)
		proyectoNroRef = nroProyecto;
		
		$('#prev_proyecto_orden').html("{0} de {1}".format(nroProyecto+1, e.proyectos._items.length));
		
		var textoExtracto = 'Proyecto N&deg; {0} sin extracto.'.format(nroProyecto+1);
		if (e.proyectos._items[nroProyecto].extracto != null) textoExtracto = e.proyectos._items[nroProyecto].extracto;
		$('#prev_proyecto_extracto').html(textoExtracto);
		
		// Actualización de botones
		$('#btn_prev_proyecto_anterior').show();
		$('#btn_prev_proyecto_siguiente').show();
		if (nroProyecto == 0)
			$('#btn_prev_proyecto_anterior').hide();
		if (nroProyecto == (expedienteRef.proyectos._items.length-1))
			$('#btn_prev_proyecto_siguiente').hide();
	}
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
		$('#btn_nuevo_prestamo').prop('disabled', false); // Se habilita el botón 'Nuevo Préstamo'
	else // si NO existe
		$('#btn_nuevo_prestamo').prop('disabled', true); // Se deshabilita el botón 'Nuevo Préstamo'
};

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
	$(idTablaContainer).append(
		generarGrillaHtml(idTabla, 
			new Array( // Títulos de cada columna de la grilla
				'',
				'A&ntilde;o',
				'Tipo',
				'N&uacute;mero',
				'D&iacute;gito',
				'Cuerpo',
				'Alcance',
				'Cpo. Alc.',
				'Anexo Alc.',
				'Cpo. Anexo Alc.',
				'Anexo',
				'Cpo. Anexo',
				'Observaciones',
				'' // Columna para Cargar Documentos del Ejecutivo
			)));
	
	// Errores customizados para Datatables
	$.fn.dataTable.ext.errMode = 'none';

	// transformo la tabla en un DataTable
	var tabla = $(idTabla)
		.on( 'error.dt', function (e, settings, techNote, message) {
			showModal('Aviso', 'Ha ocurrido un error: {0}'.format(message),
				{ btn_cerrar: modalBtnSessionHandler(settings.jqXHR.responseJSON) });
		})
		.DataTable({
			processing: true,
			serverSide: true,
			ordering: false,
			responsive: true,
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
	        // Dejo solamente la 't'abla y el 'p'aginador
            dom: 'tp',
	        autoWidth: false,
			language: { url: '../librerias/datatables/localisation/es_AR.json' }, 
			columnDefs: [ { targets: '_all', className: 'text-left', searchable: false} ],
			columns: [
				{data: null, render: callbackRenderAcciones},
				{data: 'anio_a'},
				{data: 'tipo_a'},
				{data: 'numero_a'},
				{data: 'digito_a'},
				{data: 'cuerpo_a'},
				{data: 'alcance_a'},
				{data: 'cuerpoalcance_a'},
				{data: 'anexoalcance_a'},
				{data: 'cuerpoanexoalcance_a'},
				{data: 'anexo_a'},
				{data: 'cuerpoanexo_a'},
				{data: 'observaciones_antecedentes'},
				{data: null} // Columna para Cargar Documentos del Ejecutivo
			],
			drawCallback: callbackDrawCallback
		});

	return tabla;
};

/**
 * Variables globales
 */
var dataTableRef; // Referencia al DataTable generado
var expedienteRef; // Referencia al expediente en vista previa
var formBusquedaRef; // Referencia al formulario de búsqueda despues de aplicarle validate().
var proyectoNroRef; // Referencia al proyecto actual mostrado en la vista previa

/**
 * Entry Point de jQuery
 */
$(document).ready(function() {
	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	asignarValidatorExpresionesRegulares();

	// Genero la tabla
	dataTableRef = setDataTable('index.php?c=antecedentes&a=listar&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());

	// Se setea el comportamiento de las Solapas
	setearComportamientoSolapas();
	
	// **************** Comportamiento de botones ************************************
	$('#btn_buscar_antecedentes').click(function () { if (formBusquedaRef.valid()) dataTableRef.ajax.reload(); });
	$('#btn_refrescar').click(function () { $(location).attr('href','index.php?c=antecedentes&a=view'); });

	$('#btn_nuevo_antecedente').click(function () {
		$(location).attr('href','index.php?c=antecedentes&a=add&f_anio='+$('#f_anio').val()+'&f_tipo='+$('#f_tipo').val()+'&f_numero='+$('#f_numero').val()+'&f_cuerpo='+$('#f_cuerpo').val()+'&f_alcance='+$('#f_alcance').val());
	});
    
	// Comportamiento de las flechas de la vista previa de proyectos del expediente
	$('#btn_prev_proyecto_anterior').click(function () { actualizarVistaPreviaProyecto(proyectoNroRef-1); });
	$('#btn_prev_proyecto_siguiente').click(function () { actualizarVistaPreviaProyecto(proyectoNroRef+1); });
	
    // Asigno enter por defecto
    defaultButtonInputOnEnter(['#f_anio', '#f_tipo', '#f_numero', '#f_cuerpo', '#f_alcance'], '#btn_buscar_antecedentes');

	// Validacion del formulario
	var year = moment().year(); // año actual

	formBusquedaRef = $("#form_busqueda_antecedente");
	formBusquedaRef.validate({
		rules: {
			f_anio: { required: true, digits: true, range: [1983, year] },
			f_numero: { required: true, digits: true },
			f_cuerpo: { required: true, digits: true },
			f_alcance: { required: true, digits: true }
		},
		messages: {
			f_anio: {
				required: "Por favor ingrese el a&ntilde;o del expediente.",
				digits: "Por favor ingrese un a&ntilde;o de expediente v&aacute;lido.",
				range: "El a&ntilde;o del expediente debe ser un valor entre 1983 y {0}".format(year)
			},
			f_numero: { 
				required: "Por favor ingrese el n&uacute;mero del expediente.",
				digits: "Por favor ingrese un n&uacute;mero de expediente v&aacute;lido." 
			},
			f_cuerpo: { 
				required: "Por favor ingrese el cuerpo del expediente.",
				digits: "Por favor ingrese un cuerpo de expediente v&aacute;lido." 
			},
			f_alcance: { 
				required: "Por favor ingrese el alcance del expediente.",
				digits: "Por favor ingrese un alcance de expediente v&aacute;lido." 
			}
		},
		errorLabelContainer: '#msg_error_form'
	});
});