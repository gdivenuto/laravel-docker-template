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
	return '<span class="btn-accion-contenido glyphicon glyphicon-{2}" data-accion="{0}" title="{1}" data-id_codproyecto="{3}" ></span>&nbsp;&nbsp;&nbsp;'.format(
		accion, descripcion, icono, fila.id_codproyecto);
}

/**
 * Se renderizan los botones editar y eliminar
 * 
 * @param  {[type]} full [description]
 * @return {[type]}      [description]
 */
var callbackRenderAcciones = function (full) {
	buttonAction = '';
    buttonAction += generarHtmlBotonAccion('edit', 'Editar Codificadora de Proyectos', 'pencil', full);
    buttonAction += generarHtmlBotonAccion('delete', 'Eliminar Codificadora de Proyectos', 'trash', full);
   
	return buttonAction;
};

/**
 * Se renderiza el link para habilitar|deshabilitar el registro
 * 
 * @param  {[type]} full Instancia completa de la Codificadora respectiva
 */
var callbackRenderHabilitado = function (full) {

	// Si se encuentra habilitada la Codificadora
	if (full.habilitado_codproy !== null && full.habilitado_codproy == '1')
		return '<span class="btn-accion-contenido glyphicon glyphicon-ok" data-accion="setHabilitado" title="Deshabilitar el registro" data-id_codproyecto="{0}" ></span>'.format(full.id_codproyecto);
	// Si se encuentra deshabilitada la Codificadora
	else if (full.habilitado_codproy !== null && full.habilitado_codproy == '0')
		return '<span class="btn-accion-contenido glyphicon glyphicon-remove" data-accion="setHabilitado" title="Habilitar el registro" data-id_codproyecto="{0}" ></span>'.format(full.id_codproyecto);
	else 
		return '';
}

/**
 * [listenerBotonAccion description]
 * @param  {[type]} event [description]
 * @return {[type]}       [description]
 */
var listenerBotonAccion = function (event) {
	event.preventDefault(event); // Prevengo que el link haga lo propio al hacerle click

	// Se recibe la acción
	accion = $(this).data('accion');
	// Se recibe el Id de la Codificadora del Proyecto
	f_id_codproyecto = $(this).data('id_codproyecto');
	
	if (accion == 'edit')
		$(location).attr('href','index.php?c=codproyectos&a=edit&f_id_codproyecto='+f_id_codproyecto);
	else if (accion == 'delete') {
		//eliminar(f_id_codproyecto);
		item = dataTableRef.row($(this).parents('tr')).data();
		eliminarCodproyecto(item);
	}
	else if (accion == 'setHabilitado')
		setearHabilitado(f_id_codproyecto);
	else
		showModal('Aviso', 'Acci&oacute;n no definida: \'{0}\'. Codificadora del Proyecto: {1}'.format(accion, f_id_codproyecto));
};

function setearHabilitado (f_id_codproyecto) {
	showModal('Atenci&oacute;n', '¿Est&aacute; seguro que modificar el proyecto: {0}?'.format(f_id_codproyecto),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
			        method: "GET",
			        url: "index.php?c=codproyectos&a=setHabilitado",
			        data : { f_id_codproyecto : f_id_codproyecto }
			    })
			    .done(function() {
			    	dataTableRef.ajax.reload(null, false);// false para mantener la paginación actual
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
 * 16/01/2018
 * Se envía la codificadora respectiva para su eliminación
 * @param  {[type]} codproyecto [description]
 * @return {[type]}            [description]
 */
function eliminarCodproyecto(codproyecto){
	showModal('Atenci&oacute;n', '¿Est&aacute; seguro que desea eliminar la Codificadora de Proyecto: {0}?'.format(codproyecto.descripcion_proyecto),
	{
		btn_si: {
			action: function (e) {
				// Cierro el modal para no ejecutarlo por error nuevamente sin haberlo cerrado
				$(this).modal('hide');

				// Envio la peticion al controlador
				$.ajax({
					method: "POST",
					contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
					url: "index.php?c=codproyectos&a=delete",
					dataType: 'json', 
					data: JSON.stringify(codproyecto)
				})
				.done(function( respuesta ) {
					if (respuesta.estado == 'OK') {
						if ((respuesta.data != null) && (typeof respuesta.data.codproyecto != 'undefined'))
							// Se recarga la grilla
			    			dataTableRef.ajax.reload(null, false);// false = para mantener la paginación actual
						else
							showModal('Error', 'Se esperaba una codificadora de proyecto y no se recibieron resultados.');
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
	idTabla = '#grillaCodproyectos';
	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(
		generarGrillaHtml(idTabla, 
			new Array(
				'',
				'C&oacute;digo',
				'Descripci&oacute;n',
				'Vigente Desde', 
				'Vigente Hasta', 
				'Habilitado')));

	//$(idTabla).addClass("borde_tabla_busqueda_avanzada");
	
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
            	{ targets: '_all', searchable: false, className: 'text-left'},
            	{ targets: 3, searchable: true } // Descripción
            ],
            // El dato a mostrar de cada columna
    		columns: [ 
				{data: null, width: '15px', className: 'text-center', render: callbackRenderAcciones}, // botones editar|eliminar
				{data: 'id_codproyecto', width: '0px', className: 'text-right'},
				{data: 'descripcion_proyecto', width: '600px'},
				{data: 'vigencia_desde_codproy', width: '30px', className: 'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.vigencia_desde_codproy); }},
				{data: 'vigencia_hasta_codproy', width: '30px', className: 'text-center', render: function (data, type, full, meta) { return formatearFechaConBarras(full.vigencia_hasta_codproy); }},
				{data: null, width: '5px', className: 'text-center', render: callbackRenderHabilitado}
			],
			drawCallback: function (settings) {
				// Se muestra él nombre de la codificadora
				$(".titulo-codificadora").html('CODIFICADORA DE PROYECTOS');
				// Se insertan los botones en la tabla con JS
				$('#contenedor-botones-dt').detach().appendTo('#btn-dt-container');
			}
		});

	// Asigno eventos a la tabla
	$(idTabla+' tbody').on('click', 'tr', listenerTableRowClick);
	
	return tabla;
};

/**
 * Variables globales
 */
var dataTableRef; // Referencia al DataTable generado

/**
 * Entry Point de jQuery
 */
$(document).ready(function() {
	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	asignarValidatorExpresionesRegulares();

	// Comportamiento del botón "Nueva"
	$('#btn_nuevo_codproyecto').click(function () { $(location).attr('href','index.php?c=codproyectos&a=add'); });
	
	// Comportamiento del botón "Volver"
	$('#btn_volver').click(function () { $(location).attr('href','index.php?c=expedientes&a=view'); });
	
	// Se genera la tabla
	dataTableRef = setDataTable('index.php?c=codproyectos&a=listado');

	// Asigno el evento click de esta forma para que exista siempre (para las instancias actuales y las futuras)
	$(document).on('click', '.btn-accion-contenido', listenerBotonAccion);

	// Se oculta el ítem TAREAS del menú principal
    $('#menu_item_tareas').css("display", "none");
});