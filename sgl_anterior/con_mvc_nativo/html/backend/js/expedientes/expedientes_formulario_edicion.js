/**
 * [listenerBotonCancelar description]
 * @return {[type]} [description]
 */
var listenerBotonCancelar = function () {
    // Verifico si estaba editando o agregando
    if ($('#f_anio').is('[readonly]'))
        irA('expedientes', $('#f_anio').val(), $('#f_tipo').val(), $('#f_numero').val(), $('#f_cuerpo').val(), $('#f_alcance').val());
    else
        $(location).attr('href', 'index.php?c=expedientes&a=view');
};

/**
 * Actualiza un combo Hijo según el valor del combo Padre
 * en realidad deja visibles sólo aquellos cuyo valor comiencen con el valor del combo Padre
 *
 * @param  {[type]} comboPadre [description]
 * @param  {[type]} comboHijo  [description]
 * @return {[type]}            [description]
 */
function updateComboPrefix(comboPadre, comboHijo) {
    // Se ocultan todas las opciones del combo Hijo
    $(comboHijo+' option').each(function(index, value){
        $(value).hide();
    });
    // Se visualizan sólo las opciones del combo Hijo que comiencen con el valor del combo Padre
    $(comboHijo+' option[ value ^= '+$(comboPadre).val()+' ]').each(function(index, value){
        $(value).show();
    });
    // Se visualiza sin opción el combo Hijo
    $(comboHijo+' option[ value = 0 ]').show();
    // Se asigna dicha opción (la vacía)
    $(comboHijo).val(0);
    // Se dispara el evento 'change' del combo Hijo
    $(comboHijo).trigger('change');
}

/**
 * [listenerBotonGuardar description]
 * @return {[type]} [description]
 */
var listenerBotonGuardar = function () {

    // Primero se verifica el validador
    if (formEdicionExpediente.valid()) {

        // Si se le asignó al expediente por lo menos un Tema y un Autor
        if ( ( expediente.temas._items.length > 0 ) && ( expediente.autores._items.length > 0 ) ) {
            // Paso los valores del formulario al expediente
            asignarValoresExpediente();

            // Peticion asíncrona
            $.ajax({
                method: "POST",
                contentType: "application/json", // indicamos que enviamos un JSON como parametro (cuerpo de la peticion)
                url: "index.php?c=expedientes&a=save",
                dataType: 'json',
                data: JSON.stringify(expediente)
            })
            .done(function( respuesta ) {
                if (respuesta.estado == 'OK') {
                    if (respuesta.data != null)
                        irA('expedientes', respuesta.data.anio, respuesta.data.tipo, respuesta.data.numero, respuesta.data.cuerpo, respuesta.data.alcance);
                    else
                        showModal('Error', 'Se esperaba un expediente y no se recibieron resultados.');
                } else
                    showModal('Error', respuesta.mensaje, { btn_cerrar: modalBtnSessionHandler(respuesta) });
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
            });
        } else {
            showModal('Error', 'Debe asignar al menos un Tema y un Autor al expediente.');
        }
    }
};

var listenerBotonDesagregar = function (e) {

    // Se limpian los campos del "Agregado a"
    $('#f_agregado_anio').val('');
    $('#f_agregado_tipo').val('');
    $('#f_agregado_numero').val('');
    $('#f_agregado_cuerpo').val('');
    $('#f_agregado_alcance').val('');

    // Se actualiza el comportamiento de los campos del "Agregado a"
    callbackActualizarAgregadoA();
};

/**
 * Se cargan los atributos (propiedades del objeto 'expediente') con cada campo del formulario de edición
 */
function asignarValoresExpediente()
{
    expediente.anio = $('#f_anio').val();
    expediente.tipo = $('#f_tipo').val();
    // Si no se ha ingresado un valor para 'Número'
    if ( $('#f_numero').val().trim() === null || $('#f_numero').val().trim() === '')
        expediente.numero = -1;
    else
        expediente.numero = $('#f_numero').val().trim();
    expediente.cuerpo = $('#f_cuerpo').val();
    expediente.alcance = $('#f_alcance').val();
    expediente.fecha_entrada_expe = $('#f_fecha_entrada_expe').val();

    iniciador = $('#f_iniciador').val().split('|'); // separar el id del iniciador
    expediente.iniciador_tipo = iniciador[0];
    expediente.iniciador_codigo = iniciador[1];

    expediente.id_codcategoria = $('#f_categoria').val();
    expediente.caratula = $('#f_caratula').val();
    expediente.agregado_anio = ($('#f_agregado_anio').val()) ? $('#f_agregado_anio').val(): null;
    expediente.agregado_tipo = ($('#f_agregado_tipo').val()) ? $('#f_agregado_tipo').val(): null;
    expediente.agregado_numero = ($('#f_agregado_numero').val()) ? $('#f_agregado_numero').val(): null;
    expediente.agregado_cuerpo = ($('#f_agregado_cuerpo').val()) ? $('#f_agregado_cuerpo').val(): null;
    expediente.agregado_alcance = ($('#f_agregado_alcance').val()) ? $('#f_agregado_alcance').val(): null;
    expediente.observaciones_expe = $('#f_observaciones_expe').val();
    // Agregado el 03/06/2020 XXXX
    expediente.digi_completa = $('#f_digi_completa').val();
}

/**
 * Se asignan sus valores en cada campo respectivo del formulario de edición
 * @param  Object expediente Objeto Expediente deserializado
 */
function asignarExpedienteAFormulario(expediente)
{
    $('#f_anio').val(decodeEntities(expediente.anio));
    $('#f_tipo').val(decodeEntities(expediente.tipo));
    if(expediente.numero != 0)
        $('#f_numero').val(decodeEntities(expediente.numero));
    else
        $('#f_numero').val('');
    $('#f_cuerpo').val(decodeEntities(expediente.cuerpo));
    $('#f_alcance').val(decodeEntities(expediente.alcance));

    $('#v_fecha_entrada_expe').val(decodeEntities(formatearFechaConBarras(expediente.fecha_entrada_expe)));
    $('#f_fecha_entrada_expe').val(decodeEntities(expediente.fecha_entrada_expe));// es el campo hidden con la fecha en formato yyyy-mm-dd

    $('#v_iniciador_tipo').val((expediente.iniciador_tipo != '') ? decodeEntities(expediente.iniciador_tipo) : 'G');
    $('#f_iniciador').val(decodeEntities(expediente.iniciador_tipo+'|'+expediente.iniciador_codigo));

    $('#f_categoria').val(decodeEntities(expediente.id_codcategoria));

    $('#f_caratula').val(decodeEntities(expediente.caratula));
    $('#f_agregado_anio').val(decodeEntities(expediente.agregado_anio));
    $('#f_agregado_tipo').val(decodeEntities(expediente.agregado_tipo));
    $('#f_agregado_numero').val(decodeEntities(expediente.agregado_numero));
    $('#f_agregado_cuerpo').val(decodeEntities(expediente.agregado_cuerpo));
    $('#f_agregado_alcance').val(decodeEntities(expediente.agregado_alcance));
    $('#f_observaciones_expe').val(decodeEntities(expediente.observaciones_expe));
    // Agregado el 03/06/2020 XXXX
    $('#f_digi_completa').val(decodeEntities(expediente.digi_completa));
}

var callbackActualizarAgregadoA = function () {
    // Si todos los campos de AgregadoA tienen valor, verifico el numero de expediente
    if ( $('#f_agregado_anio').val().trim() != '' &&
         $('#f_agregado_tipo').val().trim() != '' &&
         $('#f_agregado_numero').val().trim() != '' &&
         $('#f_agregado_cuerpo').val().trim() != '' &&
         $('#f_agregado_alcance').val().trim() != '') {

        // Peticion asíncrona
        $.ajax({
            method: "GET",
            url: "index.php",
            dataType: 'json',
            data: {c: "expedientes", a: "obtenerexpediente",
                f_anio: $('#f_agregado_anio').val().trim(),
                f_tipo: $('#f_agregado_tipo').val().trim(),
                f_numero: $('#f_agregado_numero').val().trim(),
                f_cuerpo: $('#f_agregado_cuerpo').val().trim(),
                f_alcance: $('#f_agregado_alcance').val().trim() }
        }).done(function( respuesta ) {
            // Indico que el expediente es correcto/incorrecto
            //$('#v_icono_agregado_valido').show().removeClass().addClass('glyphicon glyphicon-ok-circle');
            $('#v_msg_agregado_valido').hide();
            if (respuesta.estado == "OK") {
                if (respuesta.data != null) {
                    // Se muestra un ícono para informar que el expediente existe y es válido como 'Agregado a'
                    //$('#v_icono_agregado_valido').show().removeClass().addClass('glyphicon glyphicon-ok-circle forzar-texto-color-verde');
                    $('#v_msg_agregado_valido').html('<strong>Entrada:</strong> {0}, <strong>Car&aacute;tula:</strong> {1} <button id="btn_desagregar" class="btn btn-sm btn-primary" type="button"><span class="glyphicon glyphicon-link"></span>&nbsp;Desagregar</button>'.format(formatearFechaConBarras(respuesta.data.fecha_entrada_expe), respuesta.data.caratula));
                } else  {
                    // Se muestra un ícono para informar que el expediente NO existe
                    //$('#v_icono_agregado_valido').show().removeClass().addClass('glyphicon glyphicon-remove-circle forzar-texto-color-rojo');
                    $('#v_msg_agregado_valido').html('ERROR: Se esperaba un expediente y se obtuvo null.');
                }
            } else {
                // Se muestra un ícono para informar que el expediente NO existe
                //$('#v_icono_agregado_valido').show().removeClass().addClass('glyphicon glyphicon-remove-circle forzar-texto-color-rojo');
                $('#v_msg_agregado_valido').html("No existe el expediente 'Agregado a'.");
            }
            $('#v_msg_agregado_valido').show();
        }).fail(function () {
            $('#v_msg_agregado_valido').hide();
        });
    } else {
        //$('#v_icono_agregado_valido').show().removeClass().addClass('glyphicon glyphicon-ok-circle');
        $('#v_msg_agregado_valido').hide();
    }
}

/**
 * Variables globales
 */
var dataTableRefTemas; // Referencia al DataTable generado
var formEdicionExpediente; // Referencia al formulario de edición de expediente después de aplicarle validate().

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

    $('#v_fecha_entrada_expe').focus();

    // Al seleccionar un tipo de Iniciador (G|V)
    $( "#v_iniciador_tipo" ).change(function() {
        // Se actualiza el combo de nombres según el tipo elegido
        updateComboPrefix('#v_iniciador_tipo', '#f_iniciador');
    });

    // Al seleccionar un tipo de Autor (G|V)
    $( "#v_tipo_autor" ).change(function() {
        // Se actualiza el combo de nombres según el tipo elegido
        updateComboPrefix('#v_tipo_autor', '#v_autores');
    });

    // Componente de fecha
    $('#v_fecha_entrada_expe').datepicker({ altField: '#f_fecha_entrada_expe' });

    // Agregado el 03/06/2020 XXXX ************************************************
    // Al renderizarse el checkbox de la digitalización completa
    $('#f_digi_completa').ready(function () {
        if ( $('#f_digi_completa').val() == 1 ) // si está seteado en 1
            $('#f_digi_completa').prop("checked", true); // se tilda
        else
            $('#f_digi_completa').prop("checked", false); // se destilda
    });
    // Al modificarse el checkbox de la digitalización completa
    $('#f_digi_completa').change(function () {
        if ( $('#f_digi_completa').val() == 1 ) // si está seteado en 1
            $('#f_digi_completa').prop("checked", true); // se tilda
        else
            $('#f_digi_completa').prop("checked", false); // se destilda
    });
    // Al usar el checkbox de la digitalización completa
    $('#f_digi_completa').click(function () {
        if ( $('#f_digi_completa').prop('checked') ) // si se tilda
            $('#f_digi_completa').val(1); // se setea en 1
        else
            $('#f_digi_completa').val(0); // se setea en 0
    });
    // Al editar el número del Agregado
    $('#f_agregado_numero').keyup(function (){
        // Si se ingresó el número del Agregado
        if ($('#f_agregado_numero').val() != '') {
            // Si el Cuerpo y Alcance del Agregado se encuentran vacíos
            if ($('#f_agregado_cuerpo').val() == '' && $('#f_agregado_alcance').val() == '') {
                // Se les asigna cero a cada uno
                $('#f_agregado_cuerpo').val(0);
                $('#f_agregado_alcance').val(0);
            }
        }
    });

    // 06/03/2018, FUNCIONALIDAD COMENTADA POR PEDIDO DE PABLO
    // $('#v_temas').on('click', function () {
    //     if ( $('#v_temas').val() != '0' )
    //         $('#btn_agregar_tema').trigger('click');// Se agrega el Tema si no está cargado previamente
    // });
    // $('#v_temas').on('keypress', function(evento){
    //     if (evento.which == 13)// Sólo si se presiona la tecla Enter
    //         if ( $('#v_temas').val() != '0' )
    //             $('#btn_agregar_tema').trigger('click');// Se agrega el Tema si no está cargado previamente
    // });
    // $('#v_autores').on('click', function () {
    //     if ( $('#v_autores').val() != '0' )
    //         $('#btn_agregar_autor').trigger('click');// Se agrega el Autor si no está cargado previamente
    // });
    // $('#v_autores').on('keypress', function(evento){
    //     if (evento.which == 13)// Sólo si se presiona la tecla Enter
    //         if ( $('#v_autores').val() != '0' )
    //             $('#btn_agregar_autor').trigger('click');// Se agrega el Autor si no está cargado previamente
    // });

    // -- Grilla de Temas -----------------------------------------------------
    dataTableRefTemas = $('#grillaTemas')
        .DataTable({
            data: expediente.temas._items,
            dom: 't', // Los elementos de control de la grilla y en qué orden (https://datatables.net/reference/option/dom).
            ordering: false,
            paging: false,
            responsive: true,
            autoWidth: false,
            language: { url: '../librerias/datatables/localisation/es_AR.json' },
            columnDefs: [ { targets: '_all',  className: 'text-left', searchable: false} ],
            columns: [
                { data: null, render: function (data, type, full, meta) { return '<span class="glyphicon glyphicon-trash" title="Remover" style="cursor:pointer"></span>'; } },
                { data: 'id_codtema' },
                { data: 'ro_descripcion_tema' }
            ],
            drawCallback: function(settings) {
                // datos = dataTableRefTemas.rows().data();
                // $.each(datos, function (index, value) { console.log(value); });
            }
        })
        .on('click', 'span.glyphicon-trash', function () {
            // Obtengo el tema a eliminar a partir de la referencia de la fila.
            item = dataTableRefTemas.row($(this).parents('tr')).data();
            if(item !== null) {
                eliminarElementoPorReferencia(item, expediente.temas); // elimino por referencia
                dataTableRefTemas.clear().rows.add(expediente.temas._items).draw(); // refresco la grilla
            }
        });

    // Botón "agregar tema"
    $('#btn_agregar_tema').on('click', function () {
        id_codtema = $('#v_temas').val();
        ro_descripcion_tema = $('#v_temas option:selected').text();

        if (id_codtema != 0) {
            // Obtengo los datos del DataTable de Temas y busco duplicados
            datos = dataTableRefTemas.rows().data();
            resultadoBusqueda = $.grep(datos, function(element){ return element.id_codtema == id_codtema; });
            // Si NO se encuentra listado el Tema
            if ( resultadoBusqueda == 0 ) {
                // Creamos un Tema y le asignamos los valores del Tema elegido
                var item = new Object();
                item.anio = expediente.anio;
                item.tipo = expediente.tipo;
                item.numero = expediente.numero;
                item.cuerpo = expediente.cuerpo;
                item.alcance = expediente.alcance;
                item.id_codtema = id_codtema;
                item.id_usuario = -1; // aquellos que tengan id_usuario -1 se pisan con el usuario actual
                item.ro_descripcion_tema = ro_descripcion_tema;

                // Agregamos el Item
                agregarElemento(item, 'Tema', expediente.temas);
                dataTableRefTemas.clear().rows.add(expediente.temas._items).draw(); // refresco la grilla
            }
            // else alert('Elemento duplicado!');
        }
        // else alert('Debe seleccionar un tema.');
    });

    // -- Grilla de Autores -----------------------------------------------------
    dataTableRefAutores = $('#grillaAutores')
        .DataTable({
            data: expediente.autores._items,
            dom: 't', // Los elementos de control de la grilla y en qué orden (https://datatables.net/reference/option/dom).
            ordering: false,
            paging: false,
            responsive: true,
            autoWidth: false,
            language: { url: '../librerias/datatables/localisation/es_AR.json' },
            columnDefs: [ { targets: '_all', searchable: false} ],
            columns: [
                { data: null, render: function (data, type, full, meta) { return '<span class="glyphicon glyphicon-trash" title="Remover" style="cursor:pointer"></span>'; } },
                { data: 'autor_tipo' },
                { data: 'autor_codigo' },
                { data: 'ro_descripcion_grp' }
            ],
            drawCallback: function(settings) {
                // datos = dataTableRefAutores.rows().data();
                // $.each(datos, function (index, value) { console.log(value); });
            }
        })
        .on('click', 'span.glyphicon-trash', function () {
            // Obtengo el autor a eliminar a partir de la referencia de la fila.
            item = dataTableRefAutores.row($(this).parents('tr')).data();
            if(item !== null) {
                eliminarElementoPorReferencia(item, expediente.autores); // elimino por referencia
                dataTableRefAutores.clear().rows.add(expediente.autores._items).draw(); // refresco la grilla
            }
        });

    // Botón "agregar Autor"
    $('#btn_agregar_autor').on('click', function () {

        // Se separa el valor del combo para tomar:
        // Tipo, Código, Bloque Tipo y Bloque Código
        // 05/12/2025 XXXX
        // Se agrega el tipo y código DEL BLOQUE del autor (antes no se tomaban en cuenta)
        var partes = $('#v_autores').val().split('|');
        //var clave = [partes[0], partes[1], partes[2], partes[3]];
        //var clave = separarTipoCodigo($('#v_autores').val());
        autor_tipo = partes[0];  // Se toma el Tipo
        autor_codigo = partes[1]; // Se toma el Código
        autor_bloque_tipo = partes[2];  // Se toma el Bloque Tipo
        autor_bloque_codigo = partes[3];  // Se toma el Bloque Código

        // Se toma la descripción (el texto) del combo de Autores
        // 26/02/2021 XXXX
        // --------------------
        // Se divide la descripción por cada '-'
        var descripcion = $('#v_autores option:selected').text().split('-');
        // Nos quedamos sólo con el nombre (retiramos el tipo y el código)
        ro_descripcion_grp = descripcion[2];

        if (autor_tipo != 0){
            // Buscamos duplicados de Tipo y Código respectivamente
            resultadoBusqueda = $.grep(expediente.autores._items, function(element){ return (element.autor_tipo == autor_tipo) && (element.autor_codigo == autor_codigo); });

            if ( resultadoBusqueda == 0) {
                // Creamos un Autor y le asignamos los valores del Autor elegido
                var unAutor = new Object();
                unAutor.anio = expediente.anio;
                unAutor.tipo = expediente.tipo;
                unAutor.numero = expediente.numero;
                unAutor.cuerpo = expediente.cuerpo;
                unAutor.alcance = expediente.alcance;
                unAutor.autor_tipo = autor_tipo;
                unAutor.autor_codigo = autor_codigo;
                // 05/12/2025 XXXX
                // Se asigna el tipo y código del Bloque del autor (se les asignaba null antes)
                unAutor.autor_bloque_tipo = autor_bloque_tipo;
                unAutor.autor_bloque_codigo = autor_bloque_codigo;

                unAutor.id_usuario = -1; // aquellos que tengan id_usuario -1 se pisan con el usuario actual
                unAutor.ro_descripcion_grp = ro_descripcion_grp;

                // Agregamos el Autor
                agregarElemento(unAutor, 'Autor', expediente.autores);
                dataTableRefAutores.clear().rows.add(expediente.autores._items).draw(); // refresco la grilla
            }
            // else alert('Elemento duplicado!');
        }
        // else alert('Debe seleccionar un autor.');
    });

    // -- Botones Guardar/Cancelar --------------------------------------------
    $('#btn_guardar').click(listenerBotonGuardar);
    $('#btn_cancelar').click(listenerBotonCancelar);

    // 12/01/2023 XXXX
    // -- Botón para desagregar el expediente
    $('#v_msg_agregado_valido').on('click', '#btn_desagregar', listenerBotonDesagregar);
    // Fix para detener el evento keyup del campo "f_agregado_numero"
    $('#v_msg_agregado_valido').on('mousedown', '#btn_desagregar', function (e) {
        e.preventDefault();
    });

    // -- Controles de AgregadoA valido ---------------------------------------
    $('.grupo-agregado-a').change(callbackActualizarAgregadoA);
    $('.grupo-agregado-a').keyup(callbackActualizarAgregadoA);

    // Se dispara el evento 'change' del combo de Iniciador Tipo para que se refresque el combo de Iniciador Código
    $("#v_iniciador_tipo").trigger('change');

    // Se dispara el evento 'change' del combo de Autor Tipo para que se refresque el combo de Autor Código
    $("#v_tipo_autor").trigger('change');

    // Se utiliza el objeto expediente para asignar los valores en cada campo del formulario de edición
    asignarExpedienteAFormulario(expediente);

    // Actualizo indicador de expediente agregado valido
    callbackActualizarAgregadoA();

    // Validacion del formulario
    var year = moment().year(); // año actual

    // -- Validación del formulario "form_edicion_expediente" ------------------
    // Agregamos un metodo extra al validate solamente para este formulario
    $.validator.addMethod("grupoOpcional", function(value, element) {
        // // Si alguno de los campos de "Agregado a..." ha sido editado
        //
        // REVISARLO 03/06/2020 XXXX
        //
        // if ( $('#f_agregado_anio').val().trim() != '' ||
        //      $('#f_agregado_tipo').val().trim() != '' ||
        //      $('#f_agregado_numero').val().trim() != '' ||
        //      $('#f_agregado_cuerpo').val().trim() != '' ||
        //      $('#f_agregado_alcance').val().trim() != '' ||
        //      $('#f_agregado_cuerpo').val().trim() != '0' ||
        //      $('#f_agregado_alcance').val().trim() != '0'
        //    )
        //     return !(   $('#f_agregado_anio').val().trim() == '' ||
        //                 $('#f_agregado_tipo').val().trim() == '' ||
        //                 $('#f_agregado_numero').val().trim() == '' ||
        //                 $('#f_agregado_cuerpo').val().trim() == '' ||
        //                 $('#f_agregado_alcance').val().trim() == '' ||
        //                 $('#f_agregado_cuerpo').val().trim() == '0' ||
        //                 $('#f_agregado_alcance').val().trim() == '0' );
        // else
            return true;
    }, "Si desea ingresar 'Agregado a', debe completar todos los campos.");

    formEdicionExpediente = $("#form_edicion_expediente");
    formEdicionExpediente.validate({
        groups: {
            f_agregado_a: "f_agregado_anio f_agregado_tipo f_agregado_numero f_agregado_cuerpo f_agregado_alcance"
        },
        rules: {
                f_anio: { required: true, digits: true, range: [1983, year] },
                f_numero: { required: false, digits: true },
                f_cuerpo: { required: true, digits: true },
                f_alcance: { required: true, digits: true },
                v_fecha_entrada_expe: { required: true, regexDate: true },
                f_iniciador: { required: true, seleccionNoCero: true },
                f_categoria: { required: true, seleccionNoCero: true },
                f_agregado_anio: { digits: true, range: [1983, year], grupoOpcional: true },
                f_agregado_tipo: { grupoOpcional: true },
                f_agregado_numero: { digits: true, grupoOpcional: true,},
                f_agregado_cuerpo: { digits: true, grupoOpcional: true },
                f_agregado_alcance: { digits: true, grupoOpcional: true}
               },
        messages: {
                f_anio: {
                    required: "Por favor ingrese el a&ntilde;o del expediente.",
                    digits: "Por favor ingrese un a&ntilde;o de expediente v&aacute;lido.",
                    range: "El a&ntilde;o del expediente debe ser un valor entre 1983 y {0}".format(year)
                },
                f_numero: {
                    digits: "Por favor ingrese un n&uacute;mero de expediente v&aacute;lido."
                },
                f_cuerpo: {
                    required: "Por favor ingrese el cuerpo del expediente.",
                    digits: "Por favor ingrese un cuerpo de expediente v&aacute;lido."
                },
                f_alcance: {
                    required: "Por favor ingrese el alcance del expediente.",
                    digits: "Por favor ingrese un alcance de expediente v&aacute;lido."
                },
                v_fecha_entrada_expe: {
                    required: "Debe especificar la fecha de entrada del expediente."
                },
                f_iniciador: {
                    required: "Debe especificar el iniciador del expediente.",
                    seleccionNoCero: "Debe especificar el iniciador del expediente."
                },
                f_categoria: {
                    required: "Debe especificar la categor&iacute;a del expediente.",
                    seleccionValida: "Debe seleccionar una categor&iacute;a."
                },
                f_agregado_anio: {
                    digits: "Por favor ingrese un a&ntilde;o de expediente 'agregado a' v&aacute;lido.",
                    range: "El a&ntilde;o del expediente 'agregado a' debe ser un valor entre 1983 y {0}".format(year)
                },
                f_agregado_numero: {
                    digits: "Por favor ingrese un n&uacute;mero de expediente 'agregado a' v&aacute;lido."
                },
                f_agregado_cuerpo: {
                    digits: "Por favor ingrese un cuerpo v&aacute;lido para el expediente agregado."
                },
                f_agregado_alcance: {
                    digits: "Por favor ingrese un alcance v&aacute;lido para el expediente agregado."
                }
            },
        errorLabelContainer: '#msg_error_form'
    });

    // Sólo se permite el ingreso de valores numéricos en aquellos campos que posean la clase CSS 'solo-numero'
    $('.solo-numero').keyup(function (){
        this.value = (this.value + '').replace(/[^0-9]/g, '');
    });
});
