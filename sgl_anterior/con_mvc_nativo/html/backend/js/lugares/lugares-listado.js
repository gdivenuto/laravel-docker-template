/**
 * Se genera el botón determinado por los parámetros respectivos
 * 
 * @param  string accion      Acción a ejecutar en el controlador
 * @param  string descripcion Texto a mostrar como title del botón (link)
 * @param  string icono       Icono a mostrar en el botón
 * @param  objeto fila        Instancia con los datos de la fila
 * 
 * @return string             Botón (link) renderizado
 */
function generarHtmlBotonAccion(accion, descripcion, icono, fila) {
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-tipo_grp="{3}" data-codigo_grp="{4}" ></span>&nbsp;'.format(
		accion, descripcion, icono, fila.tipo_grp, fila.codigo_grp);
}

/**
 * Se renderizan los botones editar y eliminar
 * 
 * @param  {[type]} data [description]
 * @param  {[type]} type [description]
 * @param  {[type]} full [description]
 * @param  {[type]} meta [description]
 * @return {[type]}      [description]
 */
var callbackRenderAcciones = function (data, type, full, meta) {
	buttonAction = '';
    buttonAction += generarHtmlBotonAccion('edit', 'Editar Lugar (Codificadora de Iniciadores, Comisiones y Autores)', 'pencil', full);
    buttonAction += generarHtmlBotonAccion('delete', 'Eliminar Lugar (Codificadora de Iniciadores, Comisiones y Autores)', 'trash', full);
   
	return buttonAction;
};

/**
 * Se renderiza la celda de Observaciones
 * 
 * @param  {[type]} full Instancia completa de la Codificadora respectiva
 */
var callbackRenderObservaciones = function (full) {
	// Si posee observaciones
	if (full.observaciones_grp !== null) {
		// Si posee más de 35 caracteres
		if (full.observaciones_grp.length > 35)
			// Se muestran los primeros 35 caracteres
			return '<span title="{0}">{1}...<span class="glyphicon glyphicon-comment"></span></span>'.format(full.observaciones_grp, full.observaciones_grp.slice(0, 35));
		else
			return full.observaciones_grp;// Se muestra completa
	} else 
		return '';
};

/**
 * Se renderiza el link para habilitar|deshabilitar el registro
 * 
 * @param  {[type]} full Instancia completa de la Codificadora respectiva
 */
var callbackRenderHabilitado = function (full) {

	// Si se encuentra habilitada la Codificadora
	if (full.habilitado_grp !== null && full.habilitado_grp == '1')
		return '<span class="btn-accion-contenido glyphicon glyphicon-ok" data-accion="setHabilitado" title="Deshabilitar el registro" data-tipo_grp="{0}" data-codigo_grp="{1}" ></span>'.format(full.tipo_grp, full.codigo_grp);
	// Si se encuentra deshabilitada la Codificadora
	else if (full.habilitado_grp !== null && full.habilitado_grp == '0')
		return '<span class="btn-accion-contenido glyphicon glyphicon-remove" data-accion="setHabilitado" title="Habilitar el registro" data-tipo_grp="{0}" data-codigo_grp="{1}" ></span>'.format(full.tipo_grp, full.codigo_grp);
	else 
		return '';
};

/**
 * [listenerBotonAccion description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBotonAccion = function (event) {
	event.preventDefault(event); // Prevengo que el link haga lo propio al hacerle click

	// Se recibe la acción
	accion = $(this).data('accion');
	// Se recibe el Tipo y Código de la codificadora de Lugares
	f_tipo_grp = $(this).data('tipo_grp');
	f_codigo_grp = $(this).data('codigo_grp');
	
	if (accion == 'edit')
		$(location).attr('href','index.php?c=lugares&a=edit&f_tipo_grp='+f_tipo_grp+'&f_codigo_grp='+f_codigo_grp);
	else if (accion == 'delete') {
		item = dataTableRef.row($(this).parents('tr')).data();
		eliminarLugar(item);
	}
	else if (accion == 'setHabilitado')
		setearHabilitado(f_tipo_grp, f_codigo_grp);
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'. Codificadora de Lugares: {1}-{2}'.format(accion, f_tipo_grp, f_codigo_grp));
};

function setearHabilitado (f_tipo_grp, f_codigo_grp) {
	showModal('Atenci&oacute;n', '¿Desea modificar la Codificadora de Lugares (Iniciadores, Comisiones y/o Autores) con tipo: {0}  y c&oacute;digo: {1}?'.format(f_tipo_grp, f_codigo_grp),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				$.ajax({
			        method: "GET",
			        url: "index.php?c=lugares&a=setHabilitado",
			        data : { f_tipo_grp : f_tipo_grp, f_codigo_grp : f_codigo_grp }
			    }).done(function() {
			    	dataTableRef.ajax.reload(null, false);// false para mantener la paginación actual
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
 * 16/01/2018
 * Se envía la codificadora respectiva para su eliminación
 * @param  {[type]} lugar [description]
 * @return {[type]}            [description]
 */
function eliminarLugar(lugar){
	showModal('Atenci&oacute;n', '¿Est&aacute; seguro que desea eliminar el Lugar: {0}?'.format(lugar.descripcion_grp),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					url: "index.php?c=lugares&a=delete",
					dataType: 'json', 
					data: JSON.stringify(lugar)
				})
				.done(function( respuesta ) {
					if (respuesta.estado == 'OK') {
						if ((respuesta.data != null) && (typeof respuesta.data.lugar != 'undefined'))
							// Se recarga la grilla
			    			dataTableRef.ajax.reload(null, false);// false = para mantener la paginación actual
						else
							showModal('Error', 'Se esperaba un Lugar y no se recibieron resultados.');
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
	idTabla = '#grillaLugares';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(
		generarGrillaHtml(idTabla, 
			new Array(
				'',
				'Tipo',
				'C&oacute;digo',
				'Descripci&oacute;n',
				'Tipo Bloque',
				'C&oacute;digo Bloque',
				'Vigente Desde', 
				'Vigente Hasta',
				'Observaciones',
				'Habilitado')));

	// Errores customizados para Datatables
	$.fn.dataTable.ext.errMode = 'none';

	// Se transforma la tabla en un DataTable
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
            ajax: ajaxUrl,
			// Agregamos el título y los botones al Datatable, junto al buscador, la tabla y el paginador
            dom: "<'row'<'col-sm-4 col-md-3 titulo-codificadora'><'col-sm-5 col-md-6 buscador-codificadora'f><'#btn-dt-container.col-sm-3 col-md-3'>>tp",
            language: { url: 'js/datatables/localisation/es_AR.json'}, 
            // Definición de las columnas
            columnDefs: [ 
            	{ targets: '_all',  className: 'text-left', searchable: false},
            	{ targets: 3, searchable: true } // Descripción_grp
            ],
            // El dato a mostrar de cada columna
    		columns: [ 
				{data: null, width: '15px', render: callbackRenderAcciones}, // botones editar|eliminar
				{data: 'tipo_grp', width: '15px'},
				{data: 'codigo_grp', width: '40px'},
				{data: 'descripcion_grp', width: '350px'},
				{data: 'bloque_tipo', width: '15px'},
				{data: 'bloque_codigo', width: '40px'},
				{data: 'vigente_Desde_grp', width: '30px', className:'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.vigente_Desde_grp); }},
				{data: 'vigente_Hasta_grp', width: '30px', className:'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.vigente_Hasta_grp); }},
				{data: null, width: '250px', render: callbackRenderObservaciones},
				{data: null, width: '5px', className: 'text-center', render: callbackRenderHabilitado}
			],
			drawCallback: function (settings) {
				// Se muestra él nombre de la codificadora
				$(".titulo-codificadora").html('CODIFICADORA DE LUGARES');
				// Se insertan los botones en la tabla con JS
				$('#contenedor-botones-dt').detach().appendTo('#btn-dt-container');
			}
		});

	// Asigno eventos a la tabla
	$(idTabla+' tbody').on('click', 'tr', listenerTableRowClick);
	
	return tabla;
}

/**
 * Variables globales
 */
var dataTableRef; // Referencia al DataTable generado

/**
 * Entry Point de jQuery
 */
$(document).ready(function() {
	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	//fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	//asignarValidatorExpresionesRegulares();

	// Comportamiento del botón "Nuevo"
	$('#btn_nuevo_lugar').click(function () { $(location).attr('href','index.php?c=lugares&a=add'); });
	
	// Comportamiento del botón "Volver"
	$('#btn_volver').click(function () { $(location).attr('href','index.php?c=expedientes&a=view'); });
	
	// Se genera la tabla
	dataTableRef = setDataTable('index.php?c=lugares&a=listado');

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

	// Se oculta el ítem TAREAS del menú principal
    $('#menu_item_tareas').css("display", "none");
});