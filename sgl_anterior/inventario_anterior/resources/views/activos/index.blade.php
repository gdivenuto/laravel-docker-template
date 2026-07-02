@extends('layouts.app')

@section('script')
<script>
    // Select2 default behavior
    $.fn.select2.defaults.set('theme', 'bootstrap');
    $.fn.select2.defaults.set('language', 'es');

    // Definitions
    var indexTable;
    var lastCustomSearch = '';

    // Delegates

    var renderHabilitado = function (data, type, full, meta) {

        if (full.activos.habilitado == '1')
            return '<span style="background-color:#38c172;padding:5px" title="Recurso Habilitado">A</span>';
        else
            return '<span style="background-color:#e3342f;color:#fff;padding:5px" title="Recurso dado de Baja">B</span>';
    }

    var renderIdConBotones = function (data, type, full, meta) {

        bloque_acciones = renderSmallButtonBar({
            iconView: sprintf('<a role="button" class="btn btn-light btn-sm py-0" href="{{ route('activos.show', [ 'id' => '%s']) }}" title="Ver"><i class="fa fa-search" aria-hidden="true"></i>', data),
            iconEdit: sprintf('<a role="button" class="btn btn-light btn-sm py-0" href="{{ route('activos.edit', [ 'id' => '%s']) }}" title="Editar"><i class="fa fa-pencil-square-o"></i></a>', data),
            iconClone: sprintf('<a role="button" class="btn btn-light btn-sm py-0" href="{{ route('activos.clone', [ 'id' => '%s']) }}" title="Clonar"><i class="fa fa-clone"></i></a>', data),
            iconDelete: '<button type="button" class="btn btn-light btn-sm py-0 btn-delete" title="Eliminar"><i class="fa fa-trash-o"></i></button>',
        });

        // Se oculta el contenedor de los botones de acción con la clase d-md-none de bootstrap (se muestran al posicionarse en las columnas "td" de la fila respectiva)
        bloque_acciones = sprintf('<div class="bloque-botones-acciones d-md-none p-0 m-0">%s</div>', bloque_acciones);

        return sprintf('<div class="bloque-id">%s</div>%s', data, bloque_acciones);
    };

    var renderOrdenCompra = function (data, type, full, meta) {
        // Si es una orden de compra, aplico la RegEx para armar el link al sitio del ejecutivo
        if (full.activos.tipo_origen == 'OC') {
            var apiURL = '{{ config('remoteapi.apiOrdenComprasURL') }}';
            var dataOC = (data === undefined) ? [] : data.split('/');
            if (dataOC.length == 2) {
                if (!isNaN(parseInt(dataOC[0])) && !isNaN(parseInt(dataOC[1]))) {
                    if (dataOC[1].length <= 2)
                        dataOC[1] = (parseInt(dataOC[1]) >= 50) ? '19'+dataOC[1] : '20'+dataOC[1];
                    return sprintf('%s <a href="%s" target="_blank">%s</a>',
                        full.activos.tipo_origen,
                        sprintf(apiURL, dataOC[1], dataOC[0]),
                        data
                    );
                } else {
                    return sprintf('%s %s', full.activos.tipo_origen, data);
                }
            } else {
                return data;
            }
        } else {
            return (data === undefined) ? data : sprintf('%s %s', full.activos.tipo_origen, data);
        }
    };

    var hintBotonFiltro = function(isHinted = true) {
        if (isHinted)
            $('#btn_aplicar_filtro').removeClass('btn-secondary').addClass('btn-success');
        else
            $('#btn_aplicar_filtro').removeClass('btn-success').addClass('btn-secondary');
    }

    var clearFilters = function () {
        $('#datatables_custom_search').val('');

        // Text fields clear
        $.each(['orden_compra', 'fecha_alta', 'ubicacion'], function (i, v) {
            $('#filter_'+v).val('');
        });
        $.each(['marca', 'modelo', 'legajo', 'cod_area'], function (i, v) {
            $('#filter_'+v).val(null).trigger('change');
        });
        $.each(['fecha_alta_desde', 'fecha_alta_hasta'], function (i, v) {
            $('input[name=filter_'+v+']').val('');
        });

        // Normal select clear
        $('#filter_grupo_id option[value="{{ config('defaults.activoGrupoID') }}"]').prop('selected', true);
        $('#filter_habilitado option[value="{{ config('defaults.habilitado') }}"]').prop('selected', true);
        $('#filter_tipo_id option[value="{{ config('defaults.activoTipoID') }}"]').prop('selected', true);

        // Check fields clear (despues de setear los valores de los campos, para que
        // no se dispare el evento onChange y me vuelva a marcar los filtros)
        $.each(['grupo_id', 'habilitado', 'tipo_id', 'legajo', 'marca', 'modelo', 'fecha_alta_desde', 'fecha_alta_hasta', 'orden_compra', 'legajo', 'cod_area', 'ubicacion'], function (i, v) {
            $('#filter_has_'+v).prop('checked', false);
        });

        // Reset datatables last search
        indexTable.search('').draw();
        hintBotonFiltro(false);
    };

    var generateAjaxParams = function (container) {
        if ($('#filter_has_grupo_id').prop('checked'))     container.filter_grupo_id = $('#filter_grupo_id').val();
        if ($('#filter_has_tipo_id').prop('checked'))      container.filter_tipo_id = $('#filter_tipo_id').val();
        if ($('#filter_has_marca').prop('checked'))        container.filter_marca = $('#filter_marca').val();
        if ($('#filter_has_modelo').prop('checked'))       container.filter_modelo = $('#filter_modelo').val();
        if ($('#filter_has_orden_compra').prop('checked')) container.filter_orden_compra = $('#filter_orden_compra').val();
        if ($('#filter_has_legajo').prop('checked'))       container.filter_legajo = $('#filter_legajo').val();
        if ($('#filter_has_cod_area').prop('checked'))     container.filter_cod_area = $('#filter_cod_area').val();
        if ($('#filter_has_ubicacion').prop('checked'))    container.filter_ubicacion = $('#filter_ubicacion').val();
        if ($('#filter_has_habilitado').prop('checked'))   container.filter_habilitado = $('#filter_habilitado').val();

        if ($('#filter_has_fecha_alta_desde').prop('checked')) container.filter_fecha_alta_desde = $('input[name=filter_fecha_alta_desde]').val();
        if ($('#filter_has_fecha_alta_hasta').prop('checked')) container.filter_fecha_alta_hasta = $('input[name=filter_fecha_alta_hasta]').val();
    };

    var getFilterPreset = function () {
        @if (isset($filtros['filter_grupo_id']))
            $('#filter_grupo_id option[value="{{ $filtros['filter_grupo_id'] }}"]').prop('selected', true);
            $('#filter_has_grupo_id').prop('checked', true);
            refreshTiposPorGrupo();
        @else
            $('#filter_grupo_id option[value="{{ config('defaults.activoGrupoID') }}"]').prop('selected', true);
            $('#filter_has_grupo_id').prop('checked', false);
        @endif

        @if (isset($filtros['filter_habilitado']))
            $('#filter_habilitado option[value="{{ $filtros['filter_habilitado'] }}"]').prop('selected', true);
            $('#filter_has_habilitado').prop('checked', true);
        @else
            $('#filter_habilitado option[value="{{ config('defaults.habilitado') }}"]').prop('selected', true);
            $('#filter_has_habilitado').prop('checked', false);
        @endif

        @if (isset($filtros['filter_tipo_id']))
            $('#filter_tipo_id option[value="{{ $filtros['filter_tipo_id'] }}"]').prop('selected', true);
            $('#filter_has_tipo_id').prop('checked', true);
        @else
            $('#filter_tipo_id option[value="{{ config('defaults.activoTipoID') }}"]').prop('selected', true);
            $('#filter_has_tipo_id').prop('checked', false);
        @endif

        @isset($filtros['filter_marca'])
            $('#filter_marca')
                .append(new Option('{{ $filtros['filter_marca'] }}', '{{ $filtros['filter_marca'] }}', true, true))
                .trigger('change');
            $('#filter_has_marca').prop('checked', true);
        @endisset

        @isset($filtros['filter_modelo'])
            $('#filter_modelo')
                .append(new Option('{{ $filtros['filter_modelo'] }}', '{{ $filtros['filter_modelo'] }}', true, true))
                .trigger('change');
            $('#filter_has_modelo').prop('checked', true);
        @endisset

        @isset($filtros['filter_orden_compra'])
            $('#filter_orden_compra')
                .append(new Option('{{ $filtros['filter_orden_compra'] }}', '{{ $filtros['filter_orden_compra'] }}', true, true))
                .trigger('change');
            $('#filter_has_orden_compra').prop('checked', true);
        @endisset

        @isset($filtros['filter_legajo'])
            $('#filter_legajo')
                .append(new Option('{{ $filtros['filter_legajo'] }}', '{{ $filtros['filter_legajo'] }}', true, true))
                .trigger('change');
            $('#filter_has_legajo').prop('checked', true);
        @endisset

        @isset($filtros['filter_cod_area'])
            $('#filter_cod_area')
                .append(new Option('{{ $filtros['filter_cod_area'] }}', '{{ $filtros['filter_cod_area'] }}', true, true))
                .trigger('change');
            $('#filter_has_cod_area').prop('checked', true);
        @endisset

        @isset($filtros['filter_ubicacion'])
            $('#filter_ubicacion').val('{{ $filtros['filter_ubicacion'] }}');
            $('#filter_has_ubicacion').prop('checked', true);
        @endisset

        @isset($filtros['filter_fecha_alta_desde'])
            $('#filter_fecha_alta_desde').datetimepicker('defaultDate', '{{ $filtros['filter_fecha_alta_desde'] }}');
            $('#filter_has_fecha_alta_desde').prop('checked', true);
        @endisset

        @isset($filtros['filter_fecha_alta_hasta'])
            $('#filter_fecha_alta_hasta').datetimepicker('defaultDate', '{{ $filtros['filter_fecha_alta_hasta'] }}');
            $('#filter_has_fecha_alta_hasta').prop('checked', true);
        @endisset
    };

    var refreshTiposPorGrupo = function () {
        var filtro = ($('#filter_has_grupo_id').prop('checked'))
            ? $('#filter_grupo_id').val()
            : 'all';

        $.ajax({
            method: 'GET',
            url: sprintf('{{ route('grupos.jsongettiposbyid', [ 'grupo_id' => '%s' ]) }}', filtro),
            contentType: 'application/json'
        })
        .done(function( data, textStatus, jqXHR ) {
            if (data.status == 'OK') {
                var currTipoId = $('#filter_tipo_id').val(); // guardo el tipo para volver a seleccionarlo

                $('#filter_tipo_id').empty();

                $.each(data.data, function (i, item) {
                    $('#filter_tipo_id').append($('<option>', {
                        value: item.id,
                        text : item.nombre
                    }));
                });

                // Vuelvo a seleccionar el mismo tipo, si es que esta presente
                $('#filter_tipo_id option[value="'+currTipoId+'"]').prop('selected', true);
            }
            else
                alert(sprintf('ERROR: %s', data.message));
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            alert(sprintf('Ha ocurrido un error al verificar el Nª de Inventario del Activo: %s', errorThrown));
        });
    }

    // jQuery Document Ready
    $(function () {
        // Set moment locale
        moment.locale('{!! \App::getLocale() !!}');

        // ---- Input setup ---------------------------------------------------

        // Formato general de Fecha y Hora
        var dateFormat = 'YYYY-MM-DD';
        var MinDate = "1980-01-01";
        var dateMin = moment(MinDate, dateFormat);
        $('#filter_fecha_alta_desde').datetimepicker({
            sideBySide: true,
            format: dateFormat,
            minDate: dateMin
        })
        $('#filter_fecha_alta_desde').on('change.datetimepicker', function (e) {
            $('#filter_has_fecha_alta_desde').prop('checked', true);
            hintBotonFiltro();
        });

        $('#filter_fecha_alta_hasta').datetimepicker({
            sideBySide: true,
            format: dateFormat,
            minDate: dateMin
        })
        $('#filter_fecha_alta_hasta').on('change.datetimepicker', function (e) {
            $('#filter_has_fecha_alta_hasta').prop('checked', true);
            hintBotonFiltro();
        });

        $('#filter_grupo_id').change(function (e) {
            $('#filter_has_grupo_id').prop('checked', true);
            $('#filter_has_grupo_id').trigger('change');
            hintBotonFiltro();
        });

        $('#filter_tipo_id').change(function (e) {
            $('#filter_has_tipo_id').prop('checked', true);
            hintBotonFiltro();
        });

        $('#filter_habilitado').change(function (e) {
            $('#filter_has_habilitado').prop('checked', true);
            hintBotonFiltro();
        });

        $('#filter_marca').select2({
            placeholder: "Marque la casilla e ingrese Marca",
            tags: true, // permito ingreso manual
            // minimumInputLength: 3,
            closeOnSelect: true,
            ajax: {
                url: '{{ route('activos.getautocompletemarcajson') }}',
                dataType: 'json',
                delay: 333,
                cache: true,
                processResults: function (data) {
                    return { results: $.map(data, function (item) { return { id: item.marca, text: item.marca } }) };
                }
            }
        })
        .on('change', function (e) {
            $('#filter_has_marca').prop('checked', true);
            hintBotonFiltro();
        });

        $('#filter_modelo').select2({
            placeholder: "Marque la casilla e ingrese Modelo",
            tags: true, // permito ingreso manual
            // minimumInputLength: 3,
            closeOnSelect: true,
            ajax: {
                url: '{{ route('activos.getautocompletemodelojson') }}',
                dataType: 'json',
                delay: 333,
                cache: true,
                processResults: function (data) {
                    return { results: $.map(data, function (item) { return { id: item.modelo, text: item.modelo } }) };
                }
            }
        })
        .on('change', function (e) {
            $('#filter_has_modelo').prop('checked', true);
            hintBotonFiltro();
        });

        $('#filter_orden_compra').select2({
            placeholder: "Marque la casilla e ingrese Orden de Compra",
            tags: true, // permito ingreso manual
            // minimumInputLength: 3,
            closeOnSelect: true,
            ajax: {
                url: '{{ route('activos.getautocompleteordencomprajson') }}',
                dataType: 'json',
                delay: 333,
                cache: true,
                processResults: function (data) {
                    return { results: $.map(data, function (item) { return { id: item.orden_compra, text: item.orden_compra } }) };
                }
            }
        })
        .on('change', function (e) {
            $('#filter_has_orden_compra').prop('checked', true);
            hintBotonFiltro();
        });

        $('#filter_legajo').select2({
            placeholder: 'Marque la casilla e ingrese Responsable',
            // minimumInputLength: 3,
            closeOnSelect: true,
            ajax: {
                url: '{{ route('responsables.getautocompleteresponsablefiltrojson') }}',
                dataType: 'json',
                delay: 333,
                cache: true,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.legajo,
                                text: sprintf('%d | %s, %s', item.legajo, item.apellido, item.nombre)
                            }
                        })
                    };
                }
            }
        })
        .on('change', function (e) {
            $('#filter_has_legajo').prop('checked', true);
            hintBotonFiltro();
        });

        $('#filter_cod_area').select2({
            placeholder: "Marque la casilla e ingrese Área",
            // minimumInputLength: 3,
            closeOnSelect: true,
            ajax: {
                url: '{{ route('areas.getautocompleteareajson') }}',
                dataType: 'json',
                delay: 333,
                cache: true,
                processResults: function (data) {
                    return { results: $.map(data, function (item) { return { id: item.cod_area, text: item.nombre } }) };
                }
            }
        })
        .on('change', function (e) {
            $('#filter_has_cod_area').prop('checked', true);
            hintBotonFiltro();
        });

        $('#filter_ubicacion').keyup(function (e) {
            $('#filter_has_ubicacion').prop('checked', true);
            hintBotonFiltro();
        });

        $('#filter_has_grupo_id').change(function(e) { refreshTiposPorGrupo(); hintBotonFiltro(); });
        $('#filter_has_tipo_id').change(function(e) { hintBotonFiltro(); });
        $('#filter_has_marca').change(function(e) { hintBotonFiltro(); });
        $('#filter_has_modelo').change(function(e) { hintBotonFiltro(); });
        $('#filter_has_orden_compra').change(function(e) { hintBotonFiltro(); });
        $('#filter_has_legajo').change(function(e) { hintBotonFiltro(); });
        $('#filter_has_cod_area').change(function(e) { hintBotonFiltro(); });
        $('#filter_has_ubicacion').change(function(e) { hintBotonFiltro(); });
        $('#filter_has_habilitado').change(function(e) { hintBotonFiltro(); });
        // Para las fechas, aplico el "stopPropagation". Esto es porque el input se encuentra
        // dentro de un div que ya aplica el evento onChange... si no lo hago, el checkbox siempre
        // queda marcado.
        $('#filter_has_fecha_alta_desde').change(function(e) { e.stopPropagation(); hintBotonFiltro(); });
        $('#filter_has_fecha_alta_hasta').change(function(e) { e.stopPropagation(); hintBotonFiltro(); });


        // Preset de filtros segun variables de sesion
        getFilterPreset();

        // DataTable setup
        indexTable = $('#indexTable').DataTable({
            stateSave: true,
            stateLoadParams: function (settings, data) {
                // Al recargar el estado anterior de la grilla, guardo el criterio de filtro para
                // agregarlo al campo de busqueda "custom" (#datatables_custom_search)
                lastCustomSearch = data.search.search;
            },
            processing: true,
            serverSide: true,
            language: { url: '{{ asset(sprintf('json/datatables.%s.json', \App::getLocale())) }}' },
            dom:
                //"<'row'<'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f><'col-sm-12 col-md-4'p>>" +
                "<'row'<'col-sm-12 col-md-2'B><'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'<'#search_container.row'>><'col-sm-12 col-md-3'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-2'l><'col-sm-12 col-md-2'i><'col-sm-12 col-md-8'p>>",
            fnInitComplete: function() {
                // Al finalizar de inicializar la tabla, agrego un campo de búsqueda custom al datatables
                $('#search_container').html('<div class="col-md-12"><input type="text" class="form-control {{ (count($filtros) > 0) ? 'd-none' : '' }}" id="datatables_custom_search" value="'+lastCustomSearch+'" placeholder="Ingrese términos de búsqueda"></div>');
                $('#datatables_custom_search').keyup(function () {
                    indexTable.search($(this).val()).draw();
                });
            },
            ajax: {
                url: '{{ route('activos.getdatatablesjson') }}',
                data: function (d) { generateAjaxParams(d); }
            },
            pageLength: {{ config('defaults.tableResultPerPage') }},
            order: [[0, 'desc']],
            columnDefs: [
               { targets: [2, 3, 4, 6, 7], orderable: true, searchable: true, className: 'text-nowrap' },
               { targets: [0], orderable: true, searchable: true, className: 'container-bloque-botones' },
               { targets: [5, 1, 9, 10], orderable: true, searchable: true },
               { targets: [8, 11, 12], orderable: false, searchable: false }, // activos.ubicacion
            ],
            columns: [
                { data: 'activos.id', name: 'activos.id', defaultContent: '-', render: renderIdConBotones },
                { data: 'activos.nro_inventario', name: 'activos.nro_inventario', defaultContent: '-' },
                { data: 'activo_tipos.nombre', name: 'activo_tipos.nombre', defaultContent: '-' },
                { data: 'activos.marca', name: 'activos.marca', defaultContent: '-' },
                { data: 'activos.modelo', name: 'activos.modelo', defaultContent: '-' },
                { data: 'activos.nombre_equipo', name: 'activos.nombre_equipo', defaultContent: '-' },
                { data: 'activos.nro_serie', name: 'activos.nro_serie', defaultContent: "-"  },
                { data: 'activos.orden_compra', name: 'activos.orden_compra', defaultContent: '-', render: renderOrdenCompra },
                { data: 'activos.fecha_alta', name: 'activos.fecha_alta', defaultContent: '-' },
                { data: 'responsables.apellido', name: 'responsables.apellido', defaultContent: '-' },
                { data: 'areas.nombre', name: 'areas.nombre', defaultContent: "-" },
                { data: 'activos.ubicacion', name: 'activos.ubicacion', defaultContent: "-"  },
                { data: 'activos.habilitado', name: 'activos.habilitado', render: renderHabilitado },
            ],
            buttons: [
                {
                    text: '<i class="fa fa-plus" aria-hidden="true"></i> Nuevo',
                    action: function ( e, dt, node, config ) {
                        $(location).attr('href', '{{ route('activos.create') }}');
                    }
                },{
                    text: '<i class="fa fa-filter" aria-hidden="true"></i> Filtros',
                    className: '{{ (count($filtros) > 0) ? 'active' : '' }}',
                    action: function ( e, dt, node, config ) {
                        node.toggleClass('active'); // estilo de boton

                        $('#datatables_custom_search').toggle(); // muestro/oculto filtro estándar
                        $('#adv_filter').toggleClass('d-none');  // Muestro/oculto los filtros

                        if (node.hasClass('active')) {
                            // Reset datatables last search (campo de texto de busqueda general)
                            $('#datatables_custom_search').val('');
                            indexTable.search('').draw();
                        } else {
                            // Limpio los filtros al cerrar el panel de filtros extendidos
                            clearFilters();
                        }
                    }
                }
            ]
        });

        $('#indexTable tbody').on('click', 'button.btn-delete', function () {
            var data = indexTable.row( $(this).parents('tr') ).data();
            // Modal config
            $('#modalconfirm_body_txt').html(
                sprintf('Est&aacute; a punto de <strong>dar de baja</strong> el activo <strong>Nº %d</strong>: <ul><li>Nº Pat: %s</li><li>Marca: %s</li><li>Modelo: %s</li></ul><strong>¿Est&aacute; seguro?</strong>',
                    data.activos.id,
                    (data.activos.nro_inventario === undefined) ? '(sin n&uacute;mero)' : data.activos.nro_inventario,
                    (data.activos.marca === undefined) ? '(sin marca)' : data.activos.marca,
                    (data.activos.modelo === undefined) ? '(sin modelo)' : data.activos.modelo
                )
            );
            $('#modalconfirm_btn_yes').attr('href', sprintf('{{ route('activos.delete', [ 'id' => '%s' ]) }}', data.activos.id));
            $('#modalconfirm').modal('show');
        });

        $('#indexTable tbody').on('dblclick', 'td', function () {
            var data = indexTable.row( $(this).parents('tr') ).data();
            window.location.href = sprintf('{{ route('activos.edit', ['id' => '%s']) }}', data.activos.id);
        });

        $('#indexTable tbody').on('mouseover', 'td', function () {
            // Se oculta el contenedor con el nombre del Tipo
            $(this).closest('tr').find('td:first-child .bloque-id').addClass('d-md-none');
            // Se muestra el contenedor con los botones de acción
            $(this).closest('tr').find('td:first-child .bloque-botones-acciones').removeClass('d-md-none');
        });

        $('#indexTable tbody').on('mouseout', 'td', function () {
            // Se muestra el contenedor con el nombre del Tipo
            $(this).closest('tr').find('td:first-child .bloque-id').removeClass('d-md-none');
            // Se oculta el contenedor de los botones de acción
            $(this).closest('tr').find('td:first-child .bloque-botones-acciones').addClass('d-md-none');
        });

        $('#btn_aplicar_filtro').click(function() {
            indexTable.search('').draw();
            hintBotonFiltro(false);
        });

        $('#btn_limpiar_filtro').click(function () {
            clearFilters();
        });

        $('#btn_exportar').click(function () {
            $('#modalexport_title_txt').html('<i class="fa fa-download" aria-hidden="true"></i> Exportación');
            $('#modalexport .modal-body').html(`
                <p id="modalexport_body_txt">Seleccione criterio de orden y formato al que desea exportar.</p>
                <div class="row">
                    <div class="col-md-2 text-right">Columna</div>
                    <div class="col-md-4">
                        <select id="sort_col" class="form-control">
                            <option value="activos_id" selected>ID Interno</option>
                            <option value="activos_nro_inventario">Nº Registro Patrimonial</option>
                            <option value="activos_marca">Marca</option>
                            <option value="activos_modelo">Modelo</option>
                        </select>
                    </div>
                    <div class="col-md-2 text-right">Sentido</div>
                    <div class="col-md-4">
                        <select id="sort_type" class="form-control">
                            <option value="sort_asc" selected>Ascendente</option>
                            <option value="sort_desc">Descendente</option>
                        </select>
                    </div>
                </div>
            `);
            $('#btn_exportar_pdf').unbind('click');
            $('#btn_exportar_pdf').click(function () {
                var params = {
                    sort_col: $('#sort_col').val(),
                    sort_type: $('#sort_type').val()
                };

                generateAjaxParams(params);

                var uriParams = $.map(params, function(val, key) {
                    return sprintf('%s=%s', key, val);
                }).join('&');

                $(location).attr('href', sprintf('{{ route('activos.exportpdf') }}?%s', uriParams));
            });


            $('#modalexport').modal('show');
        });

        $('#btn_tomainventario').click(function () {
            $('#modalexport_title_txt').html('<i class="fa fa-download" aria-hidden="true"></i> Toma de Inventario');
            $('#modalexport .modal-body').html(`
                <p id="modalexport_body_txt">Indique Secretaría y Dependencia.</p>
                <div class="row">
                    <div class="col-md-2 text-right">Secretaría</div>
                    <div class="col-md-10">
                        <input id="secretaria" class="form-control" value="{{ config('defaults.secretaria') }}"/>
                    </div>
                    <div class="col-md-2 text-right">Dependencia</div>
                    <div class="col-md-10">
                        <input id="dependencia" class="form-control" value="{{ config('defaults.dependencia') }}" />
                    </div>
                </div>
                <p></p>
                <p id="modalexport_body_txt">Seleccione criterio de orden y formato al que desea exportar.</p>
                <div class="row">
                    <div class="col-md-2 text-right">Columna</div>
                    <div class="col-md-4">
                        <select id="sort_col" class="form-control">
                            <option value="activos_nro_inventario">Nº Registro Patrimonial</option>
                            <option value="activos_marca">Marca</option>
                            <option value="activos_modelo">Modelo</option>
                        </select>
                    </div>
                    <div class="col-md-2 text-right">Sentido</div>
                    <div class="col-md-4">
                        <select id="sort_type" class="form-control">
                            <option value="sort_asc" selected>Ascendente</option>
                            <option value="sort_desc">Descendente</option>
                        </select>
                    </div>
                </div>
            `);
            $('#btn_exportar_pdf').unbind('click');
            $('#btn_exportar_pdf').click(function () {
                var params = {
                    secretaria: $('#secretaria').val(),
                    dependencia: $('#dependencia').val(),
                    sort_col: $('#sort_col').val(),
                    sort_type: $('#sort_type').val()
                };

                generateAjaxParams(params);

                var uriParams = $.map(params, function(val, key) {
                    return sprintf('%s=%s', key, val);
                }).join('&');

                $(location).attr('href', sprintf('{{ route('activos.tomainventario') }}?%s', uriParams));
            });

            $('#modalexport').modal('show');
        });

    });
</script>
@endsection

@section('content')

    @include('layouts.modalconfirm')

<div id="modalexport" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalexport_title_txt" class="modal-title">
                    <i class="fa fa-download" aria-hidden="true"></i> Exportaci&oacute;n
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="modalexport_body_txt">Seleccione criterio de orden y formato al que desea exportar.</p>
                <div class="row">
                    <div class="col-md-2 text-right">Columna</div>
                    <div class="col-md-4">
                        <select id="sort_col" class="form-control">
                            <option value="activos_id" selected>ID Interno</option>
                            <option value="activos_nro_inventario">Nº Registro Patrimonial</option>
                            <option value="activos_marca">Marca</option>
                            <option value="activos_modelo">Modelo</option>
                        </select>
                    </div>
                    <div class="col-md-2 text-right">Sentido</div>
                    <div class="col-md-4">
                        <select id="sort_type" class="form-control">
                            <option value="sort_asc" selected>Ascendente</option>
                            <option value="sort_desc">Descendente</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn_exportar_pdf" type="button" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-pdf" aria-hidden="true"></i> PDF</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <h2>Activos</h2>
    <div id="adv_filter" class="row mb-3 {{ (count($filtros) == 0) ? 'd-none' : '' }}">

        <div class="col-md-12">
            <h5>Filtros Avanzados</h5>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1 text-right">Grupo</div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm mb-1">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input id="filter_has_grupo_id" type="checkbox" aria-label="">
                            </div>
                        </div>
                        <select id="filter_grupo_id" class="form-control">
                            @forEach ($grupos as $g)
                                <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                            @endForEach
                        </select>
                    </div>
                </div>

                <div class="col-md-1 text-right"><span class="text-info">(*)</span> Habilitado</div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm mb-1">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input id="filter_has_habilitado" type="checkbox" aria-label="">
                            </div>
                        </div>
                        <select class="form-control select2-single" id="filter_habilitado">
                            <option value="1">1 - Alta</option>
                            <option value="0">0 - Baja</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-1 text-right">Tipo</div>
        <div class="col-md-3">
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input id="filter_has_tipo_id" type="checkbox" aria-label="">
                    </div>
                </div>
                <select id="filter_tipo_id" class="form-control">
                    @forEach ($activo_tipos as $at)
                        <option value="{{ $at->id }}">{{ $at->nombre }}</option>
                    @endForEach
                </select>
            </div>
        </div>

        <div class="col-md-1 text-right"><span class="text-info">(*)</span> Marca</div>
        <div class="col-md-3">
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input id="filter_has_marca" type="checkbox" aria-label="">
                    </div>
                </div>
                <select class="form-control select2-single" id="filter_marca"></select>
            </div>
        </div>

        <div class="col-md-1 text-right"><span class="text-info">(*)</span> Modelo</div>
        <div class="col-md-3">
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input id="filter_has_modelo" type="checkbox" aria-label="">
                    </div>
                </div>
                <select class="form-control select2-single" id="filter_modelo"></select>
            </div>
        </div>

        <div class="col-md-1 text-right">Orden Compra</div>
        <div class="col-md-3">
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input id="filter_has_orden_compra" type="checkbox" aria-label="">
                    </div>
                </div>
                <select class="form-control select2-single" id="filter_orden_compra"></select>
            </div>
        </div>

        <div class="col-md-1 text-right">Desde Fecha Alta</div>
        <div class="col-md-3">
            <div class="input-group input-group-sm mb-1" id="filter_fecha_alta_desde" data-target-input="nearest">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input id="filter_has_fecha_alta_desde" type="checkbox" aria-label="">
                    </div>
                </div>
                <div class="input-group-prepend" data-target="#filter_fecha_alta_desde" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
                <input name="filter_fecha_alta_desde" type="text" class="form-control datetimepicker-input" data-target="#filter_fecha_alta_desde" placeholder="Marque la casilla e ingrese Fecha de Alta Inicial" aria-label="">
            </div>
        </div>

        <div class="col-md-1 text-right">Hasta Fecha Alta</div>
        <div class="col-md-3">
            <div class="input-group input-group-sm mb-1" id="filter_fecha_alta_hasta" data-target-input="nearest">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input id="filter_has_fecha_alta_hasta" type="checkbox" aria-label="">
                    </div>
                </div>
                <div class="input-group-prepend" data-target="#filter_fecha_alta_hasta" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
                <input name="filter_fecha_alta_hasta" type="text" class="form-control datetimepicker-input" data-target="#filter_fecha_alta_hasta" placeholder="Marque la casilla e ingrese Fecha de Alta Final" aria-label="">
            </div>
        </div>

        <div class="col-md-1 text-right">Responsable</div>
        <div class="col-md-3">
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input id="filter_has_legajo" type="checkbox" aria-label="">
                    </div>
                </div>
                <select class="form-control select2-single" id="filter_legajo"></select>
            </div>
        </div>

        <div class="col-md-1 text-right">&Aacute;rea</div>
        <div class="col-md-3">
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input id="filter_has_cod_area" type="checkbox" aria-label="">
                    </div>
                </div>
                <select class="form-control select2-single" id="filter_cod_area"></select>
            </div>
        </div>

        <div class="col-md-1 text-right"><span class="text-info">(*)</span> Ubicaci&oacute;n</div>
        <div class="col-md-3">
            <div class="input-group input-group-sm mb-1">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input id="filter_has_ubicacion" type="checkbox" aria-label="">
                    </div>
                </div>
                <input id="filter_ubicacion" type="text" class="form-control" placeholder="Marque la casilla e ingrese Ubicación" aria-label="">
            </div>
        </div>

        <div class="col-md-12">
            <p>Los campos marcados con <span class="text-info">(*)</span> permiten b&uacute;squedas parciales, por ejemplo: <i>hewl</i> para obtener <i>Hewlett Packard</i>.</p>
        </div>

        <div class="col-md-4">
            <button id="btn_aplicar_filtro" class="btn btn-secondary btn-sm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Aplicar</button>
            <button id="btn_limpiar_filtro" class="btn btn-secondary btn-sm"><i class="fa fa-ban" aria-hidden="true"></i> Limpiar</button>
            <button id="btn_exportar" class="btn btn-secondary btn-sm"><i class="fa fa-download" aria-hidden="true"></i> Exportar</button>
            <button id="btn_tomainventario" class="btn btn-secondary btn-sm"><i class="fa fa-download" aria-hidden="true"></i> Toma de Inventario</button>
        </div>

    </div> <!-- adv_filter -->

    <div class="table-responsive">
        <table class="table table-hover table-sm" id="indexTable">
            <thead>
                <tr>
                    <th class="text-nowrap">ID</th>
                    <th class="text-nowrap">Reg Pat</th>
                    <th class="text-nowrap">Tipo</th>
                    <th class="text-nowrap">Marca</th>
                    <th class="text-nowrap">Modelo</th>
                    <th class="text-nowrap">Detalle</th>
                    <th class="text-nowrap">N&deg; Serie</th>
                    <th class="text-nowrap">Origen</th>
                    <th class="text-nowrap">Fecha Alta</th>
                    <th class="text-nowrap">Responsable</th>
                    <th class="text-nowrap">&Aacute;rea</th>
                    <th class="text-nowrap">Ubicaci&oacute;n</th>
                    <th class="text-nowrap">&nbsp;</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection
