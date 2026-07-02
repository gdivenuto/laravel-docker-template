/**
 * Variables globales
 */
var dataTableRef; // Referencia al DataTable generado
var expedienteRef; // Referencia al expediente en vista previa
var formBusquedaRef; // Referencia al formulario de búsqueda despues de aplicarle validate().
var proyectoNroRef; // Referencia al proyecto actual mostrado en la vista previa

var controlador_activo = 'expedientes'; // Por defecto

var url_directorio_proyectos = '../../expedientes/proyectos/';

var perfil_usuario_actual;

/**
 * Se setea el comportamiento de las Solapas
 */
function setearComportamientoSolapas()
{
	$('.solapa').click(function(event) {
		event.preventDefault(event); // Prevengo que el link haga lo propio al hacerle click

		controlador = $(this).data('controlador'); // obtengo el controlador desde la solapa (data-controlador)

		// parametros
		anio = $('#f_anio').val().trim();
		tipo = $('#f_tipo').val().trim();
		numero = $('#f_numero').val().trim();
		cuerpo = $('#f_cuerpo').val().trim();
		alcance = $('#f_alcance').val().trim();

		// Se activa la solapa respectiva
		$('#solapa_'+controlador).addClass('active');

		// cambio de solapa
		irA(controlador, anio, tipo, numero, cuerpo, alcance);
	});

	// por defecto los errores de grilla se ocultan
	ocultarErrorGrilla();
}

/**
 * [mostarErrorGrilla description]
 * @param  {[type]} msg [description]
 * @return {[type]}     [description]
 */
function mostrarErrorGrilla(msg) {
	$('#row_error_grilla').show();
	$('#msg_error_grilla').html(msg);
}

/**
 * [ocultarErrorGrilla description]
 * @param  {[type]} msg [description]
 * @return {[type]}     [description]
 */
function ocultarErrorGrilla() {
	$('#row_error_grilla').hide();
	$('#msg_error_grilla').html('');
}

function actualizarVistaSiExisteExpediente(existeExpediente) {
	// Si hay resultados, invoco la vista previa. Sino muestro una indicacion.
	if (existeExpediente) {
		vistaPreviaExpediente($('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(),	$('#f_cuerpo').val(), $('#f_alcance').val() );
		ocultarErrorGrilla();
	} else {
		limpiarVistaPreviaExpediente();
		mostrarErrorGrilla('<span class="glyphicon glyphicon-exclamation-sign anim-vibrar"></span> El expediente <strong>{0} - {1} - {2} - {3} - {4}</strong> no existe.'.format($('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val()));
	}
}

/**
 * Se setea el comportamiento de los botones de navegación.
 */
function setearComportamientoBotonesNavegacion() {

	// Comportamiento de los botones de navegación
	$('#btn_buscar_expediente').click(function () {

		if (formBusquedaRef.valid()) {
			// Se recarga
			//dataTableRef.ajax.reload();

			// Tomamos los campos del buscador (se forza)
			anio = $('#f_anio').val().trim();
			tipo = $('#f_tipo').val().trim();
			numero = $('#f_numero').val().trim();
			cuerpo = $('#f_cuerpo').val().trim();
			alcance = $('#f_alcance').val().trim();

			// Se "forza" la recarga de la solapa activa
			irA(controlador_activo, anio, tipo, numero, cuerpo, alcance);

			// Se verifica el número que quedó para deshabilitar o no el botón Anterior
			//verificarBotonAnteriorExpediente();
			// 10/09/2020 XXXX
			// Se verifica la existencia del siguiente Expediente/Nota/Recomendación
			//verificarBotonSiguienteExpediente();
		}
	});
	$('#btn_refrescar').click(function () { $(location).attr('href','index.php?c=expedientes&a=view'); });
    $('#btn_busqueda_avanzada').click(function () { $(location).attr('href','index.php?c=expedientesbusquedaavanzada&a=view'); });
    $('#btn_busqueda_por_antecedente').click(function () { $(location).attr('href','index.php?c=expedientesbusquedaantecedente&a=view'); });
    $('#btn_verificar_digitalizacion').click(function () { $(location).attr('href','index.php?c=verificardigitalizacion&a=view'); });

	// Comportamiento de las flechas de la vista previa de proyectos del expediente
	$('#btn_prev_proyecto_anterior').click(function (e) { e.preventDefault(); actualizarVistaPreviaProyecto(proyectoNroRef-1); });
	$('#btn_prev_proyecto_siguiente').click(function (e) { e.preventDefault(); actualizarVistaPreviaProyecto(proyectoNroRef+1); });

	// Asigno enter por defecto
    defaultButtonInputOnEnter(['#f_numero', '#f_anio', '#f_tipo', '#f_cuerpo', '#f_alcance'], '#btn_buscar_expediente');
}

/**
 * Se setea el comportamiento del Validate para el formulario.
  */
function setearComportamientoValidate() {
	// Validacion del formulario
	var year = moment().year(); // año actual

	formBusquedaRef = $("#form_busqueda_expediente");
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
}

/**
 * [limpiarVistaPreviaExpediente description]
 * @return {[type]} [description]
 */
function limpiarVistaPreviaExpediente() {
	// Se muestra la clave del Expediente|Nota|Recomendación
	$('#prev_expediente').html('');

	// Se muestra la Carátula
	$('#prev_caratula').html('');

	// Se muestra la descripción del Iniciador
	$('#prev_iniciador').html('');

	// Se muestra la descripción de la categoría
	$('#prev_categoria').html('');

	// Autores **********************************************************
	$('#prev_titulo_autores').html('Sin autores');
	// Se muestran los Autores en un combobox
	$('#prev_autores').html('');

	// Temas **********************************************************
	$('#prev_titulo_temas').html('Sin temas');
	// Se muestran los Temas en un combobox
	$('#prev_temas').html('');

	// Último Estado **********************************************************
	$('#prev_estado').html('');

	// Comisión **********************************************************
	$('#prev_comision').html('');

	// Proyectos **********************************************************
	$('#btn_prev_proyecto_anterior').hide();
	$('#btn_prev_proyecto_siguiente').hide();
	$('#prev_proyecto_orden').html('---');
	$('#prev_proyecto_extracto').html('');

	// Observaciones **********************************************************
	// Se muestra la Observación
	$('#prev_observaciones_expe').html('');

	// Usuario **********************************************************
	// Se muestra el Usuario que operó en el Expediente|Nota|Recomendación
	$('#prev_usuario').html('');
}

/**
 * Se muestra la vista previa del expediente del buscador
 *
 * @return {[type]} [description]
 */
function vistaPreviaExpediente(anio, tipo, numero, cuerpo, alcance)
{
	// Peticion asíncrona
	$.ajax({
		method: "GET",
		url: "index.php",
		dataType: 'json',
		data: {c: "expedientes", a: "obtenerexpediente",
			f_anio: anio,
			f_tipo: tipo,
			f_numero: numero,
			f_cuerpo: cuerpo,
			f_alcance: alcance }
	}).done(function( respuesta ) {
		// muestro datos
		if (respuesta.estado == "OK") {
			if (respuesta.data != null) {
				// Actualizo la referencia al expediente y la vista previa
				expedienteRef = respuesta.data;
				actualizarVistaPrevia();
			} else {
				limpiarVistaPreviaExpediente();
				showModal('Error', 'Se esperaba un expediente y se recibi&oacute; null.');
			}
		} else if ((respuesta.estado == "ERROR") || (respuesta.estado == "WARNING")) {
			limpiarVistaPreviaExpediente();
			showModal((respuesta.estado == "ERROR") ? 'Error' : 'Advertencia',
				respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
		}
	}).fail(function () {
		limpiarVistaPreviaExpediente();
		showModal('Error', 'Ha ocurrido un error inesperado al consultar expediente.');
	});
};

/**
 * [actualizarVistaPrevia description]
 * @return {[type]} [description]
 */
function actualizarVistaPrevia() {
	// Info del Expediente
	e = expedienteRef;

	// Nombre codificado en base a la clave del expediente
	nombre_codificado_digi = obtenerNombreCodificado(e.anio, e.tipo, e.numero);

	// Se muestra la clave del Expediente|Nota|Recomendación
	$('#prev_expediente').html('<strong>{0} - {1} - {2} - {3} - {4}</strong>'.format(e.anio, e.tipo, e.numero, e.cuerpo, e.alcance));

	// Se muestra el estado del Proyecto **********************************************************
	if (e.estado_proyecto == 'C') {
		// Si posee el "original" o el "deforma"
		if (e.url_proyecto.indexOf("sin_original_y_deforma") == -1)
			url = e.url_proyecto+'?v='+Math.random();
		else
			url = "index.php?c=proyectos&a=view&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}".format(e.anio, e.tipo, e.numero, e.cuerpo, e.alcance);

		$('#prev_estado_proyecto').html('<a class="text-success" href="{0}">CARGADO</a>'.format(url));
	}

	else if (e.estado_proyecto == 'PC'){
		// Sólo los usuarios de Perfil 1 y 2 deben ver el temporal
    	if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {
			$('#prev_estado_proyecto').html('<a class="texto-advertencia" href="{0}">PARA CARGAR</a>'.format(e.url_proyecto_temporal+'?v='+Math.random()));
		} else {
			$('#prev_estado_proyecto').html('<span class="texto-advertencia" href="#">PARA CARGAR</span>');
		}
	}

	else if (e.estado_proyecto == 'C_PC') {
		// Enlace del proyecto "Cargado"
		enlace_proyecto_cargado = '<a class="text-success" href="{0}">CARGADO</a>'.format(e.url_proyecto+'?v='+Math.random());

		// Sólo los usuarios de Perfil 1 y 2 deben ver el Proyecto temporal "Para cargar"
		if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {
			enlace_proyecto_temporal = '<a class="texto-advertencia" href="{0}">PARA CARGAR</a>'.format(e.url_proyecto_temporal+'?v='+Math.random());
		} else {
			enlace_proyecto_temporal = '<span class="texto-advertencia">PARA CARGAR</span>';
		}

		$('#prev_estado_proyecto').html(enlace_proyecto_cargado+' | '+enlace_proyecto_temporal);
	}
	else
		$('#prev_estado_proyecto').html('<span class="text-danger">SIN CARGAR</span>');

	// Se muestra el estado de la Digitalización  **********************************************************
	if (e.estado_digitalizacion == 'DC') {
		// URL de la digitalización "Cargada" ó "Completa"
		url = '{0}{1}/{2}/{3}.pdf'.format(url_directorio_proyectos, e.anio, nombre_codificado_digi, nombre_codificado_digi);
		// Si el expediente tiene seteada la digitalización como Completa o no
		titulo = (e.digi_completa == '1') ? 'COMPLETA' : 'CARGADA';

		$('#prev_estado_digitalizacion').html('<a class="text-success" href="{0}" target="_blank">{1}</a>'.format(url+'?v='+Math.random(), titulo));
	}
	else if (e.estado_digitalizacion == 'DPC') {
		// Enlace para la digitalización "Para cargar", según el perfil del usuario
		if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {
			$('#prev_estado_digitalizacion').html('<a class="texto-advertencia" href="{0}" target="_blank">PARA CARGAR</a>'.format(e.url_digitalizacion+'?v='+Math.random()));
		} else {
			$('#prev_estado_digitalizacion').html('<span class="texto-advertencia">PARA CARGAR</span>');
		}
	}
	else if (e.estado_digitalizacion == 'DC_PC') {
		// URL de la digitalización "Cargada" ó "Completa"
		url_cargada = '{0}{1}/{2}/{3}.pdf'.format(url_directorio_proyectos, e.anio, nombre_codificado_digi, nombre_codificado_digi);
		// Si el expediente tiene seteada la digitalización como Completa o no
		titulo_cargada = (e.digi_completa == '1') ? 'COMPLETA' : 'CARGADA';
		enlace_cargada = '<a class="text-success" href="{0}" target="_blank">{1}</a>'.format(url_cargada+'?v='+Math.random(), titulo_cargada);

		// Enlace para la digitalización "Para cargar", según el perfil del usuario
		if (perfil_usuario_actual == 1 || perfil_usuario_actual == 2) {
			enlace_temporal = '<a class="texto-advertencia" href="{0}" target="_blank">PARA CARGAR</a>'.format(e.url_digitalizacion+'?v='+Math.random());
		} else {
			enlace_temporal = '<span class="texto-advertencia">PARA CARGAR</span>';
		}

		$('#prev_estado_digitalizacion').html(enlace_cargada+' | '+enlace_temporal);
	}
	else
		$('#prev_estado_digitalizacion').html('<span class="text-danger">SIN CARGAR</span>');

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
	$('#prev_titulo_autores').html((cantidad_autores > 0) ? '<strong>'+titulo_para_autores+'</strong>' : 'Sin autores');

	// *** Se muestran los Autores ***
	$('#contenedor_prev_autores').html('');
	// en caso de poseer más de uno
	if ( cantidad_autores > 1 ) {
		// se visualizan en un combobox
		$('#contenedor_prev_autores').html('<select id="prev_autores" class="form-control input-sm"></select>');
		$.each(e.autores._items, function(index, value) {
	    	$('#prev_autores').append($("<option />").text("{0} - {1}".format(value.autor_codigo, value.ro_descripcion_grp)));
		});
	} else
		// Si posee solo uno, se visualiza su descripción como texto simple
		$('#contenedor_prev_autores').html("{0} - {1}".format(e.autores._items[0]['autor_codigo'], e.autores._items[0]['ro_descripcion_grp']));

	// Temas **********************************************************
	// Cantidad de Temas
	var cantidad_temas = e.temas._items.length;

	// Título según su cantidad
	var titulo_para_temas = (cantidad_temas > 1) ? cantidad_temas+' Temas' : '1 Tema';
	$('#prev_titulo_temas').html((cantidad_temas > 0) ? '<strong>'+titulo_para_temas+'</strong>' : 'Sin temas');

	// *** Se muestran los Temas ***
	$('#contenedor_prev_temas').html('');
	// en caso de poseer más de uno
	if ( cantidad_temas > 1 ) {
		// se visualizan en un combobox
		$('#contenedor_prev_temas').html('<select id="prev_temas" class="form-control input-sm"></select>');
		$.each(e.temas._items, function(index, value) {
			$('#prev_temas').append($("<option />").text("{0} - {1}".format(value.id_codtema, value.ro_descripcion_tema)));
		});
	} else
		// Si posee solo uno, se visualiza su descripción como texto simple
		$('#contenedor_prev_temas').html(e.temas._items[0]['ro_descripcion_tema']);;

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

	// Comisión Actual **********************************************************
	// Si posee un Estado...
	if (estado_actual != null) {
		// Si este estado requiere tratamiento en Comisión, la buscamos
		if(estado_actual.ro_tratamiento_comision == 1) {
			// Se inicializa
			comision_actual = null;
			// Si hay giros en Comisión
			if (e.giros._items.length > 0) {
				// Se recorren los Giros en Comisión que posea el expediente
				$.each(e.giros._items, function(index, value) {
					// Si posee Fecha de Entrada y NO fecha de Salida
					if (value.fecha_entrada_giro != null && value.fecha_salida_giro == null)
						comision_actual = value;
				});
				// Si está en Comisión
				if (comision_actual != null)
					$('#prev_comision').html("{0} - {1}".format(comision_actual.comision_codigo, comision_actual.ro_descripcion_grp));
				else
					$('#prev_comision').html('&nbsp;');
			}
			else
				$('#prev_comision').html('No se puede determinar Comisi&oacute;n');
		}
		else
			$('#prev_comision').html('&nbsp;');
	}

	// Proyectos **********************************************************
	// Se muestran los Proyectos, de a uno por vez, utilizando las flechas de anterior|siguiente
	if (e.proyectos._items.length > 0)
		actualizarVistaPreviaProyecto(0);
	else {
		proyectoNroRef = 0;
		$('#btn_prev_proyecto_anterior').hide();
		$('#btn_prev_proyecto_siguiente').hide();
		$('#prev_proyecto_orden').html('---');
		$('#prev_proyecto_extracto').html('Expediente sin proyectos asignados.');
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

		if (expedienteRef.proyectos._items.length > 1) {
			$('#prev_contenedor_botones_proyectos').css('display', 'block');
			$('#prev_contenedor_textarea_proyectos').removeClass('col-md-12').addClass('col-md-11');
		} else {
			$('#prev_contenedor_botones_proyectos').css('display', 'none');// Se ocultan las flechas
			$('#prev_contenedor_textarea_proyectos').removeClass('col-md-11').addClass('col-md-12');// Se muestra al 100% el textarea
		}
	}
}

/**
 * Se verifica si hay Expedientes en Estado 90 (en PPC) y su fecha es mayor a 30 días
 */
function verificarExpedientesEnPPCVencidos() {
	// Peticion asíncrona
	$.ajax({
		method: "GET",
		url: "index.php",
		dataType: 'json',
		data: {c: "expedientes", a: "verificarexpedientesenppcvencidos"}
	}).done(function( respuesta ) {
		// muestro datos
		if (respuesta.estado == "OK") {
			if (respuesta.data != null) {
				//console.log(respuesta.data);
				var expe_vencidos = '';
				// Se recorren los expedientes vencidos
				$.each(respuesta.data, function(index, value) {

					expe_vencidos += obtenerNombreSegunTipo(value.tipo);
					expe_vencidos += '<b> '+value.anio+'-'+value.tipo+'-'+value.numero+'-'+value.cuerpo+'-'+value.alcance+'</b>';
					//expe_vencidos += '</b>&nbsp;&nbsp;&nbsp;&nbsp;Fecha: <b>'+formatearFechaConBarras(value.fecha_estado)+'</b> - ';
					expe_vencidos += ', en PPC, ha vencido el <b>'+sumarDias(value.fecha_estado, 30)+'</b><br><br>';
				});
				// Si hay expedientes en estado PPC
				if (expe_vencidos != ''){
					showModal('Aviso', expe_vencidos);
				}
			}
		}
	}).fail(function () {
		showModal('Error', 'Ha ocurrido un error inesperado al verificar expedientes vencidos.');
	});
}

function obtenerNombreSegunTipo(tipo) {

	switch (tipo) {
		case 'E':
			mensaje = 'El Expediente';
			break;
		case 'N':
			mensaje = 'La Nota';
			break;
		case 'R':
			mensaje = 'La Recomendación';
			break;
	}
	return mensaje;
}
