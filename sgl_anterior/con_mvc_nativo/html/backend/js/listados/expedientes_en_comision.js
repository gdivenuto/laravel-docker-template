/**
 * Se obtiene la Comisión actual (con fecha de entrada y sin fecha de salida)
 * @param  {array} giros Conjunto de Comisiones de un expediente determinado
 * @return {Giro}      Comisión actual
 */
function obtenerComisionActual(giros) {
    var posicion = giros.length-1;// Empieza en la última Comisión
    var encontrado = false;

    // Mientras no se encuentre la comisión vigente
    while (encontrado === false && posicion >= 0) {
        // Si la comisión posee fecha de entrada y NO posee fecha de salida
        if (giros[posicion].fecha_entrada_giro != null && giros[posicion].fecha_salida_giro === null){
            return giros[posicion]; // Devuelve la comision para mostrar su información
        } else
            posicion--;// Se actualiza la posición para volver a corroborar con la comisión anterior
    }
    if (encontrado === false) // Si NO se encontró una Comisión con fecha de entrada
        return null; // No hay Comisión que mostrar
}

/**
 * Permite visualizar la ficha del expediente en una nueva pestaña
 * @param  {[type]} f_anio    [description]
 * @param  {[type]} f_tipo    [description]
 * @param  {[type]} f_numero  [description]
 * @param  {[type]} f_cuerpo  [description]
 * @param  {[type]} f_alcance [description]
 */
function verFicha(f_anio, f_tipo, f_numero, f_cuerpo, f_alcance) {
    var url = 'index.php?c=expedientes&a=generarpdffichaexpediente&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}'.format(
            f_anio, f_tipo, f_numero, f_cuerpo, f_alcance);
        // Se muestra el pdf en una nueva pestaña
        window.open(url);
}

/**
 * Se renderiza la ficha
 *
 * @param  {[type]} data [description]
 * @param  {[type]} type [description]
 * @param  {[type]} full [description]
 * @param  {[type]} meta [description]
 * @return {[type]}      [description]
 */
var callbackRenderFichas = function (data, type, full, meta) {
    ficha = '';
    dias = '';
    nombre_documento = '';

    // Si hay datos que mostrar
    if (full != null)
    {
        if (full.tipo == 'E') {
            nombre_documento  = 'Expediente';
            titulo_para_boton = 'Ir al Expediente';
        }
        if (full.tipo == 'N') {
            nombre_documento = 'Nota';
            titulo_para_boton = 'Ir a la Nota';
        }
        if (full.tipo == 'R') {
            nombre_documento = 'Recomendaci&oacute;n';
            titulo_para_boton = 'Ir a la Recomendaci&oacute;n';
        }
        ficha +=    '<div class="row">';
        ficha +=        '<div class="col-sm-6 col-md-3">';
        ficha +=            '<strong><span class="glyphicon glyphicon-file"></span>&nbsp;{0} {1} - {2} - {3} - {4} - {5}</strong>'.format(nombre_documento, full.anio, full.tipo, full.numero, full.cuerpo, full.alcance);
        ficha +=        '</div>';
                        // Botón para "Ir al expediente" respectivo y "Ver ficha"
        ficha +=        '<div class="col-sm-6 col-md-3 pull-right text-right">';
        ficha +=            '<button type="button" class="btn btn-primary btn-sm" onClick="javascript:irA(\'expedientes\',{0},\'{1}\',{2},{3},{4});" title="{5}"><span class="glyphicon glyphicon-share-alt"></span>&nbsp;{5}</button>'.format(full.anio, full.tipo, full.numero, full.cuerpo, full.alcance, titulo_para_boton);
        ficha +=            '&nbsp;&nbsp;&nbsp;';
        ficha +=            '<button type="button" class="btn btn-primary btn-sm" onClick="javascript:verFicha({0},\'{1}\',{2},{3},{4});" title="Ver ficha"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Ver ficha</button>'.format(full.anio, full.tipo, full.numero, full.cuerpo, full.alcance);
        ficha +=        '</div>';
        ficha +=    '</div>';

                    // Fila para Iniciador, Carátula y la Fecha de Entrada
        ficha +=    '<div class="row">';
                        // Iniciador
        ficha +=        '<div class="col-sm-4 col-md-3"><strong>Iniciador: </strong>{0}</div>'.format(full.ro_iniciador_descripcion_grp);
                        // Carátula
                        caratula = (full.caratula != null && full.caratula != '') ? full.caratula : 'no posee';
        ficha +=        '<div class="col-sm-4 col-md-3"><strong>Car&aacute;tula: </strong>{0}</div>'.format(caratula);
                        // Fecha de Ingreso
        ficha +=        '<div class="col-sm-4 col-md-3"><strong>Fecha Ingreso: </strong>{0}</div>'.format(formatearFechaConBarras(full.fecha_entrada_expe));
        ficha +=    '</div>';

                    // Categoría, Temas y Autores **************************************
        ficha +=    '<div class="row">';
                        // Categoría
        ficha +=        '<div class="col-sm-4 col-md-3"><strong>Categor&iacute;a: </strong>{0}</div>'.format(full.ro_descripcion_categoria);
                       // Temas ********************************************************
        ficha +=        '<div class="col-sm-4 col-md-3">';
        ficha +=            '<strong>Tema: </strong>';
                        if (full.temas._items.length > 1) {
        ficha +=            '<select class="form-control input-sm">';
                                $.each(full.temas._items, function(index, value) {
                                    ficha += '<option>'+value.ro_descripcion_tema+'</option>';
                                });
        ficha +=            '</select>';
                        } else {
        ficha +=            full.temas._items[0]['ro_descripcion_tema'];
                        }
        ficha +=        '</div>';
                       // Autores ********************************************************
        ficha +=        '<div class="col-sm-4 col-md-3">';
        ficha +=            '<strong>Autor: </strong>';
                        if (full.autores._items.length > 1) {
        ficha +=            '<select class="form-control input-sm">';
                                $.each(full.autores._items, function(index, value) {
                                    ficha += '<option>'+value.ro_descripcion_grp+'</option>';
                                });
        ficha +=            '</select>';
                        } else {
        ficha +=            full.autores._items[0]['ro_descripcion_grp'];
                        }
        ficha +=        '</div>';
        ficha +=    '</div>';

        // Proyectos ********************************************************
        $.each(full.proyectos._items, function(index, value) {
            contenido_extracto = (value.extracto != null) ? value.extracto : 'Sin extracto';

            ficha += '<div class="row">';
            ficha +=    '<div class="col-md-12"><strong>Proyecto N&deg; <span id="prev_proyecto_orden">{0}</span></strong>&nbsp;&nbsp;&nbsp;{1}</div>'.format(value.orden_proyecto, value.ro_descripcion_proyecto);
            ficha += '</div>';
            ficha += '<div class="row">';
            ficha +=     '<div class="col-md-1"><strong>Extracto</strong></div>';
            ficha += '</div>';
            ficha += '<div class="row">';
            ficha +=     '<div class="col-md-12">';
            ficha +=         '<textarea style="width:100%;" class="form-control" readonly>'+contenido_extracto+'</textarea>';
            ficha +=    '</div>';
            ficha += '</div>';
        });
        // Último Estado ********************************************************
        var estado_actual = null;

        if (full.estados._items.length > 0){
            estado_actual = full.estados._items[full.estados._items.length-1];

            fecha_estado = formatearFechaConBarras(estado_actual.fecha_estado);

            ficha += '<div class="row">';
            ficha +=     '<div class="col-sm-8 col-md-6"><strong>Estado: </strong>'+estado_actual.id_codestado+' - '+estado_actual.ro_nombre_estado+'</div>';
            ficha +=     '<div class="col-sm-4 col-md-3"><strong>Fecha de Estado: </strong>'+fecha_estado+'</div>';
            ficha += '</div>';

            $('#prev_estado').html("{0} - {1} ({2})".format(estado_actual.id_codestado, estado_actual.ro_nombre_estado, fecha_estado));
        }
        // Última Comisión ********************************************************
        // Si posee un Estado
        if (estado_actual != null){
            // Si este estado requiere tratamiento en comision
            if(estado_actual.ro_tratamiento_comision == 1){

                comision_actual = obtenerComisionActual(full.giros._items);

                // Se muestra la Comisión vigente (con fecha de entrada y sin fecha de salida)
                if (comision_actual != null) {

                    fecha_comision = formatearFechaConBarras(comision_actual.fecha_entrada_giro);

                    if ( full.ro_cantidad_dias_en_comision >= 0 && full.ro_cantidad_dias_en_comision <= 105 )
                        css_resaltado = 'resaltado-ok'; // COLOR VERDE
                    else {
                        if ( full.ro_cantidad_dias_en_comision >= 106 && full.ro_cantidad_dias_en_comision <= 119 )
                            css_resaltado = 'resaltado-advertencia'; // COLOR AMARILLO
                        else
                            css_resaltado = 'resaltado-alerta'; // COLOR ROJO
                    }

                    ficha += '<div class="row">';
                    ficha +=     '<div class="col-md-6"><strong>Comisi&oacute;n: </strong>{0} - {1}</div>'.format(comision_actual.comision_codigo, comision_actual.ro_descripcion_grp);

                    // Si no se busca por Estado
                    if( $('#f_estado').val() == '' || $('#f_estado').val() == '0' )
                        dias = '&nbsp;&nbsp;<span class="'+css_resaltado+' resaltado_espacio">'+full.ro_cantidad_dias_en_comision+' d&iacute;as</span>';

                    ficha +=     '<div class="col-md-3"><strong>Fecha de Comisi&oacute;n: </strong>'+fecha_comision+dias+'</div>';
                    ficha += '</div>';

                    // En caso que el expediente respectivo posea por lo menos un Informe
                    if (full.informes._items.length > 0) {

                        // Se toma el último Informe
                        ultimo_informe = full.informes._items[full.informes._items.length-1];
                        //console.log(ultimo_informe);
                        //console.log(full.ro_cantidad_dias_en_comision);
                        // Si se venció el plazo de 120 días en la comisión
                        //if ( full.ro_cantidad_dias_en_comision != null && full.ro_cantidad_dias_en_comision > 120 ) {

                        // 04/02/2021 XXXX
                        // Si no se ha devuelto el informe
                        if (ultimo_informe != null && ultimo_informe.fecha_vuelta_informe == '') {
                            // Se formatea la fecha de pedido del informe
                            fecha_pedido_informe  = formatearFechaConBarras(ultimo_informe.fecha_pedido_informe);
                            observaciones_informe = (ultimo_informe.observaciones_informe != null) ? ultimo_informe.observaciones_informe : 'no posee';

                            ficha += '<div class="row">';
                            ficha +=    '<div class="col-md-12"><strong>Informe pendiente</strong> desde el {0}, {1} <strong>Observaciones:</strong> {2}</div>'.format(
                                fecha_pedido_informe,
                                ultimo_informe.detalle_informe,
                                observaciones_informe);
                            ficha += '</div>';
                        }
                    }
                }
            }
        }
    }

    return ficha;
};

/**
 * [listenerBotonRefrescar description]
 * @return {[type]} [description]
 */
var listenerBotonRefrescar = function () {
    // Se lista sin utilizar el criterio de búsqueda
    $(location).attr('href','index.php?c=listadoexpedientesencomision&a=view&f_restablecer=1');
};

/**
 * Se vuelve a la grilola de expedientes
 * @return {[type]} [description]
 */
var listenerVolver = function () {
    // Se redirecciona a la grilla con búsqueda Simple (sólo por Clave)
    $(location).attr('href','index.php?c=expedientes&a=view');
};

/**
 * [setDataTable description]
 * @param {[type]} ajaxUrl [description]
 * @return DataTable
 */
function setDataTable(ajaxUrl) {
    var dom_datatable;

    // Borro y creo el listado con las fichas obtenidas
	idTabla = '#tablaFichas';

	idTablaContainer = '{0}Container'.format(idTabla);

	$(idTablaContainer).empty();
	$(idTablaContainer).append(
		generarGrillaHtml(idTabla, null, true) // sin encabezado
	);
	$(idTabla).addClass("borde_tabla_busqueda_avanzada");

    // Errores customizados para Datatables
    $.fn.dataTable.ext.errMode = 'none';

    // Se define el dom que renderiza el datatable
    // si se buscan los expedientes Vencidos o no
    dom_datatable = ( $('#f_vencidos').prop('checked') ) ? 't' : 'ptp';

    // Se transforma el listado en un DataTable
    // settings: respuesta en bruto del DataTable
    // techNote: ...
	var tabla = $(idTabla)
        .on( 'error.dt', function (e, settings, techNote, message) {
            showModal('Aviso', 'Ha ocurrido un error: {0}'.format(message),
                { btn_cerrar: modalBtnSessionHandler(settings.jqXHR.responseJSON) });
        })
        .DataTable({
    		processing: true,
    		serverSide: true,
    		ajax: {
                url: ajaxUrl,
                data: function ( d ) {
                    // Agrego los parámetros de búsqueda
                    d.f_fecha_desde    = $('#f_fecha_desde').val();
                    d.f_fecha_hasta    = $('#f_fecha_hasta').val();
                    d.f_fecha_listado  = $('#f_fecha_listado').val();
                    d.f_fecha_comision = $('#f_fecha_comision').val();
                    d.f_comision       = $('#f_comision').val();
                    d.f_estado         = $('#f_estado').val();
                    d.f_vencidos       = $('#f_vencidos').val();
                    // En caso de no usarse la modal, este parámetro está vacío
                    d.f_comisiones_elegidas_en_modal = $('#f_comisiones_elegidas_en_modal').val();
                }
            },
            // Dejamos solamente la 't'abla y el 'p'aginador (éste último si NO se buscan sólo los Vencidos)
            dom: dom_datatable,
            ordering: false,
    		responsive: true,
    		autoWidth: false,
    		// Cantidad de registros por página
            pageLength: 10,
    		language: { url: '../librerias/datatables/localisation/es_AR.json' },
    		columnDefs: [
    			{ targets: '_all', searchable: false }
            ],
    		columns: [
    			{ data: null, render: callbackRenderFichas }
            ],
            drawCallback: function(opciones){
                var tableinfo = dataTableRef.page.info();

                // Si hay registros que mostrar
                if (tableinfo.recordsTotal > 0) {
                    // Se habilitan los botones para Imprimir y Exportar a Documento de Texto
                    $('#btn_imprimir').prop('disabled', false);
                    $('#btn_exportar_texto').prop('disabled', false);
                }
                else // Si NO hay para mostrar
                {
                    // Se deshabilitan los botones para Imprimir y Exportar a Documento de Texto
                    $('#btn_imprimir').prop('disabled', true);
                    $('#btn_exportar_texto').prop('disabled', true);
                }

                if ( $('#f_vencidos').prop('checked') )
                    $('#tablaFichasContainer').css('margin-bottom', '20px');
            }
    	});

	return tabla;
};

/**
 * Si se desean tildar o destildar todas las Comisiones
 */
function marcarDesmarcarComisionesEnModal() {
    if ( $('#modal_todas_comisiones').prop('checked') )
        marcarTodosCheckbox('form_expedientes_en_comision');
    else
        desmarcarTodosCheckbox('form_expedientes_en_comision');
}

// Cuando se presiona en el botón "Siguiente" de la modal de Comisiones
function seguirModal() {
    // Se verifica que por lo menos una Comisión esté tildada (en aquellos checkboxes que posean la clase determinada)
    if ( verificarCheckbox('.class_modal_comisiones_elegidas') ) {
        // Se toman las comisiones que fueron seleccionadas
        var comisionesSeleccionadas = $('.class_modal_comisiones_elegidas:checked').map(function(_, el) {
            return $(el).val();
        }).get();

        // Se guardan los códigos de las comisiones elegidas en un campo oculto, separados por coma
        $('#f_comisiones_elegidas_en_modal').val(comisionesSeleccionadas.join(','));

        // Se cierra la modal
        $('#modalComisiones').modal('hide');

        // Se recarga la grilla
        dataTableRef.ajax.reload();
    } else
        showModal("Aviso", "Debe seleccionar por lo menos una Comisi"+'\u00f3'+"n.");
}

/**
 * Se visualiza una modal para buscar una Comisión, utilizando un autosugerido
 */
function mostrarModalComisionAutosugerido() {
    // Se muestra una modal para buscar una Comisión mediante un autosugerido
    $('#modalComisionAutosugerido').modal('show');
    // Se limpia el campo de búsqueda
    $('#modal_comision_sugerida').val('');
    // Se selecciona y se le da el foco al campo de búsqueda por autosugerido
    setfocus('#modal_comision_sugerida');
}

/**
 * Se visualiza una modal para buscar un Estado, utilizando un autosugerido
 */
function mostrarModalEstadoAutosugerido() {
    // Se muestra una modal para buscar un Estado mediante un autosugerido
    $('#modalEstadoAutosugerido').modal('show');
    // Se limpia el campo de búsqueda
    $('#modal_estado_sugerido').val('');
    // Se selecciona y se le da el foco al campo de búsqueda por autosugerido
    setfocus('#modal_estado_sugerido');
}

/**
 * Variables globales
 */
var dataTableRef; // Referencia al DataTable generado
var formBusquedaRef; // Referencia al formulario de búsqueda despues de aplicarle validate().
var comisionesSeleccionadas = '';
/**
 * Entry Point de jQuery
 */
$(document).ready(function () {

	// form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
	fixValidatorBootstrap();

	// Agrego las expresiones regulares extra
	asignarValidatorExpresionesRegulares();

    // Inicialización de los DatePicker
    inicializarDatePicker();

    // Si existe el DataTable y posee registros
    if ( typeof dataTableRef !== 'undefined' && dataTableRef.page.info().recordsTotal > 0 ) {
        // Se habilitan los botones para Imprimir y Exportar a Documento de Texto
        $('#btn_imprimir').prop('disabled', false);
        $('#btn_exportar_texto').prop('disabled', false);
    } else {
        // Se deshabilitan los botones para Imprimir y Exportar a Documento de Texto
        $('#btn_imprimir').prop('disabled', true);
        $('#btn_exportar_texto').prop('disabled', true);
    }

    // Se oculta el encabezado de la tabla, en esta Vista no se utiliza
    $('#tablaFichas thead').css('display', 'none');

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
    // Se define la fecha del Listado, para utilizar en el cálculo de días en comisión de cada expediente
    $('#v_fecha_listado').datepicker({ altField: '#f_fecha_listado'});

    // Se define la fecha de referencia para aquellos expedientes ingresados en la Comisión
    $('#v_fecha_comision').datepicker({ altField: '#f_fecha_comision'});

    // Al elegir una Comisión
    $('#f_comision').change(function () {
        // Si se eligió una Comisión
        if ( $('#f_comision').val() != 0 )
            $('#f_estado').val(0);
     });

    // Al elegir un Estado
    $('#f_estado').change(function () {
        if ( $('#f_estado').val() != 0 )
            $('#f_comision').val(0);
    });

    $('#f_vencidos').click(function () {
        if ( $('#f_vencidos').prop('checked') )
            $('#f_vencidos').val(1);
        else
            $('#f_vencidos').val(0);
    });
	// **************** Comportamiento de botones ************************************
    $('#btn_buscar').click(function () {
        // Si se cumplen las validaciones del formulario
        if (formBusquedaRef.valid()) {
            // Si no se eligió un Estado ni una Comisión
            if ( $('#f_estado').val() == 0 && $('#f_comision').val() == 0 ) {
                // 07/02/2023 XXXX: Retirada la modal de selección de las Comisiones
                // Se muestra una modal con el listado de Comisiones para elegir más de una
                //$('#modalComisiones').modal('show');
                showModal("Aviso", "Debe seleccionar una Comisi"+'\u00f3'+"n o un Estado.");
            } else {
                // Recién al buscar se carga el DataTable
                dataTableRef = setDataTable('index.php?c=listadoexpedientesencomision&a=expedientesencomisiondatagrid');
                dataTableRef.ajax.reload();
            }
        }
    });
    // Se limpia el criterio de búsqueda
    $('#btn_refrescar').click(listenerBotonRefrescar);

    // Se vuelve a la grilla de expedientes
    $('#btn_volver').click(listenerVolver);

    // Una vez elegidas las Comisiones, se envían para utilizarlas como criterio de búsqueda
    $('#btn_siguiente').click(function(){
        seguirModal();
    });

    $('#modal_comision_sugerida').keypress( function(e) {
        if ( $('#modal_comision_sugerida').val() != '' && e.which == 13 )
            $('#btCargarComisionSugerida').focus();
    });

    $('#modal_estado_sugerido').keypress( function(e) {
        if ( $('#modal_estado_sugerido').val() != '' && e.which == 13 )
            $('#btCargarEstadoSugerido').focus();
    });

    $('#btCargarComisionSugerida').click(function() {
        if ( $('#modal_comision_sugerida').val() != '' ) {
            // Se toma la comisión sugerida y elegida
            var comision_sugerida = $('#modal_comision_sugerida').val();
            // Se separa el código y la descripción
            var aux_comision = comision_sugerida.split('-');
            // Se toma el código
            var codigo_comision = aux_comision[0];
            // Se selecciona la Comisión en el combo
            $('#f_comision').val(codigo_comision);
            // Se vacía el combo de Estados
            $('#f_estado').val(0);
            // Se oculta la modal
            $('#modalComisionAutosugerido').modal('hide');
        } else
            setfocus('#modal_comision_sugerida');
    });

    $('#btCargarEstadoSugerido').click(function() {
        if ( $('#modal_estado_sugerido').val() != '' ) {
            // Se toma el estado sugerido y elegido
            var estado_sugerido = $('#modal_estado_sugerido').val();
            // Se separa el código y el nombre
            var aux_estado = estado_sugerido.split('-');
            // Se toma el código
            var id_codestado = aux_estado[0];
            // Se selecciona el Estado en el combo
            $('#f_estado').val(id_codestado);
            // Se vacía el combo de Estados
            $('#f_comision').val(0);
            // Se oculta la modal
            $('#modalEstadoAutosugerido').modal('hide');
        } else
            setfocus('#modal_estado_sugerido');
    });

    // Al tildar o destildar la opción "Todas" en la modal de Comisiones
    $('#modal_todas_comisiones').click(function () {
        marcarDesmarcarComisionesEnModal();
    });

    $('#btn_imprimir').click(function () {
        // Se validan los campos requeridos del form del criterio de búsqueda
        if (formBusquedaRef.valid()) {
            // Se genera el PDF del reporte de Expedientes en Comisión
            var url = 'index.php?c=listadoexpedientesencomision&a=generarpdfexpedientesencomision&f_fecha_desde={0}&f_fecha_hasta={1}&f_fecha_listado={2}&f_fecha_comision={3}&f_comision={4}&f_estado={5}&f_vencidos={6}&f_comisiones_elegidas_en_modal={7}'.format(
                $('#f_fecha_desde').val(),
                $('#f_fecha_hasta').val(),
                $('#f_fecha_listado').val(),
                $('#f_fecha_comision').val(),
                $('#f_comision').val(),
                $('#f_estado').val(),
                $('#f_vencidos').val(),
                $('#f_comisiones_elegidas_en_modal').val()
            );
            // Se muestra el pdf en una nueva pestaña
            window.open(url);
        }
    });

    $('#btn_exportar_texto').click(function () {
        // Se validan los campos requeridos del form del criterio de búsqueda
        if (formBusquedaRef.valid()) {
            // Se genera el documento de texto del reporte de Expedientes en Comisión
            $(location).attr('href','index.php?c=listadoexpedientesencomision&a=generardocumentotextoexpedientesencomision&f_fecha_desde={0}&f_fecha_hasta={1}&f_fecha_listado={2}&f_fecha_comision={3}&f_comision={4}&f_estado={5}&f_vencidos={6}&f_comisiones_elegidas_en_modal={7}'.format(
                $('#f_fecha_desde').val(),
                $('#f_fecha_hasta').val(),
                $('#f_fecha_listado').val(),
                $('#f_fecha_comision').val(),
                $('#f_comision').val(),
                $('#f_estado').val(),
                $('#f_vencidos').val(),
                $('#f_comisiones_elegidas_en_modal').val()
            ));
        }
    });

    // Asigno enter por defecto
    defaultButtonInputOnEnter(
        ['#v_fecha_desde',
         '#v_fecha_hasta',
         '#v_fecha_listado',
         '#v_fecha_comision',
         '#f_comision',
         '#f_estado'
        ], '#btn_buscar');

    // Validación del formulario
    formBusquedaRef = $("#form_expedientes_en_comision");
    formBusquedaRef.validate({
    	rules: {
                v_fecha_desde: {
                    required: true,
                    regexDate: true
                },
                v_fecha_hasta: {
                    required: true,
                    regexDate: true
                },
                v_fecha_listado: {
                    required: true,
                    regexDate: true
                },
                v_fecha_comision: {
                    regexDate: true
                }
    		},
		messages: {
                v_fecha_desde: {
                    required: 'Debe especificar una fecha Desde.'
                },
                v_fecha_hasta: {
                    required: 'Debe especificar una fecha Hasta.'
                },
                v_fecha_listado: {
                    required: 'Debe especificar una fecha Listado.'
                }
			},
        errorLabelContainer: '#msg_error_form'
    });

    // Se oculta el ítem TAREAS del menú principal
    $('#menu_item_tareas').css("display", "none");
});
