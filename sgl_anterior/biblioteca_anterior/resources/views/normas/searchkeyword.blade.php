@extends('layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // Globals
    var criteria_list = [];
    var prev_criteria_list = [];
    var link_target = '';

    // Select2 default behavior
    $.fn.select2.defaults.set('theme', 'bootstrap4');

    // Delegates --------------------------------------------------------------
    @include('layouts.paginatorlogic')

    var restoreSearchParams = function (search_params) {
        // Restore previous search parameters
        if (search_params) {
            // If I have a criteria, do the search
            if (search_params.criteria.length > 0) {
                criteria_list = search_params.criteria;
                addSearchCriteria(''); // force render
                $('#search_on_search').prop('checked', search_params.searchonsearch); 
                doSearch();
            }
        }
    };

    var parseCriteriaArgs = function (criteria) {
        var crit_list = criteria.trim().split(/ (?=(?:(?:[^"]*"){2})*[^"]*$)/);
        return $.map(crit_list, function (v) {
            var r = v.replace(/^(\s|")+/, '').replace(/(\s|")+$/, '');
            if (r != '') return r;
        });
    }

    var addSearchCriteria = function (criteria) {
        $('#criteria_list_container').empty();

        prev_criteria_list = criteria_list.map((x) => x); // backup criteria (clone array)
        
        if (criteria.trim() != '') criteria_list = criteria_list.concat(parseCriteriaArgs(criteria));
        if (criteria_list.length > 0 ) $('#criteria_list_container').append('<strong>Criterios buscados:</strong> <button type="button" class="btn btn-secondary btn-sm btn-clear-search" alt="Limpiar búsqueda"><i class="fa fa-trash-o" aria-hidden="true"></i></button> ');

        $.each(criteria_list, function (i, v) {
            var next_term = (i != (criteria_list.length - 1)) 
                ? ' <i class="fa fa-arrow-right" aria-hidden="true"></i> '
                : '';
            $('#criteria_list_container').append(`<button type="button" class="btn btn-secondary btn-sm btn-remove-criteria" data-index="${i}">${v} <i class="fa fa-times" aria-hidden="true"></i></button>${next_term}`);
        });
    };

    var removeSearchCriteria = function () {
        var criteria_index = $(this).data('index');
        if (criteria_index >= 0 && criteria_index < criteria_list.length) {
            criteria_list.splice(criteria_index, 1);
            addSearchCriteria(''); // force render
            if (criteria_list.length == 0)
                clearSearch();
            else
                doSearch();
        }
    };

    var clearSearch = function () {
        $.ajax({
            method: 'GET',
            url: '{{ route('normas.clearsessionsearchjson', ['search_type' => 'keywordsearch']) }}',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            contentType: 'application/json'
        })
        .done(function(response, textStatus, jqXHR) { 
            criteria_list = [];
            $('#criteria_args').val('');
            addSearchCriteria(''); // force render
            $('#cant_resultados').empty();
            $('#resultados').empty();
            setupPaginator();
            enableDownloadButtons(false);
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            alert(`Ha ocurrido un error: ${errorThrown}`);
        });
    }

    var enableDownloadButtons = function (show) {
        if (show) {
            $('#btn_download_pdf').removeAttr('disabled');
            $('#btn_download_pdf').removeClass('d-none');
        } else {
            $('#btn_download_pdf').attr('disabled', true);
            $('#btn_download_pdf').addClass('d-none');
        }
    };

    var doSearch = function (page = 1) {
        var criteria_args = $('#criteria_args').val();
        var criteria_searchonsearch = $('#search_on_search').is(':checked');

        if (criteria_searchonsearch) {
            $('#criteria_args').val('');
        } else {
            criteria_list = [];
        }
        addSearchCriteria(criteria_args);

        if (criteria_list.length > 0) {
            disablePaginator();
            $('#progress_bar').show();

            $.ajax({
                method: 'POST',
                url: `{{ route('normas.getnormaskeywordsearchjson', [ 'normas_db' => $normas_db ]) }}?page=${page}`,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: JSON.stringify({params: { criteria: criteria_list, searchonsearch: criteria_searchonsearch}}),
                contentType: 'application/json'
            })
            .done(function(response, textStatus, jqXHR) {
                if (response.status == 'OK') {
                    if (response.data.total > 0) {
                        $('#cant_resultados').html(`Se encontraron ${response.data.total} ocurrencia(s).`);
                    } else {
                        $('#cant_resultados').html(`No se encontraron resultados.`);
                    }
                    $('#resultados').empty();
                    renderPaginator(response.data);
                    enableDownloadButtons(response.data.total > 0);

                    $.each(response.data.data, function (i, v) {
                        var desc_badges = $.map(v.descriptores, function (v, i) {
                            return `<a href="{{ route('normas.searchsimple', [ 'normas_db' => $normas_db, 'descriptor_id' => '_descid_']) }}" class="badge badge-secondary" ${link_target}>${v.tag}</a>`.replace('_descid_', v.id);
                        });
                        
                        fsancion = (v.fec_sancion) ? moment(v.fec_sancion).format("DD/MM/YYYY") : '(sin fecha)';
                        fpromulga = (v.fec_promulga) ? moment(v.fec_promulga).format("DD/MM/YYYY") : '(sin fecha)';
                        extra_btn = '';

                        @auth
                            extra_btn = `<a href="{{ route('backend.normas.edit', ['norma' => '_norma_'])}}" class="btn btn-sm btn-secondary" role="button" ${link_target}><i class="fa fa-pencil" aria-hidden="true"></i></a> `.replace('_norma_', v.id);
                        @endauth
                        
                        $('#resultados').append($(`<li class="list-group-item"><a href="{{ route('normas.show', [ 'normas_db' => $normas_db, 'norma' => '_norma_']) }}" class="btn btn-sm btn-primary" role="button" ${link_target}><i class="fa fa-plus" aria-hidden="true"></i></a> ${extra_btn}<span class="lead"><span class="badge badge-outline-primary text-capitalize">${v.base_tag}</span></span> <strong>${v.acto_desc}</strong> ${v.nro} <strong>Fecha Sanci&oacute;n:</strong> ${fsancion} <strong>Fecha Promulgaci&oacute;n:</strong> ${fpromulga} <br/> <strong>Contenido:</strong> ${v.contenido} <br/> ${desc_badges.join(' ')} </li>`.replace('_norma_', v.id)));
                    });
                } else {
                    if (! $('#search_on_search').is(':checked')) {
                        clearSearch();
                    } else {
                        criteria_list = prev_criteria_list.map((x) => x); // restore criteria
                        addSearchCriteria(''); // force render
                    }
                    alert(sprintf('ERROR: %s', response.message));
                }
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                alert(`Ha ocurrido un error al buscar: ${errorThrown}`);
            })
            .always(function () {
                $('#progress_bar').hide();
            });
        } else {
            alert('Debe ingresar al menos un criterio de búsqueda.');
        }
    };

    @include('normas.devicedetect')

    // jQuery Document Ready --------------------------------------------------
    $(function () {
        // Device detection
        link_target = (detectDevice().device == 'computer') ? 'target="_blank"' : '';

        // Set moment locale
        moment.locale('{!! \App::getLocale() !!}');

        // Hide progress by default
        $('#progress_bar').hide();

        // Setup Paginator
        setupPaginator();

        // Button behaviour
        $('#btn_submit').click(function(e) {
            e.preventDefault();
            doSearch();
        });
        $('#btn_download_pdf').click(function(e) {
            window.location.href = '{{ route('normas.getnormaskeywordpdf', [ 'normas_db' => $normas_db ]) }}';
        });
        $('.toggle-show-descriptores').click(function(e) {
            e.preventDefault();
            $('.toggle-show-descriptores').toggleClass('d-none');
            $('#descriptores_short').toggleClass('d-none');
            $('#descriptores_long').toggleClass('d-none');
        });

        // Add/Remove behaviour
        $('#criteria_list_container').on('click', 'button.btn-remove-criteria', removeSearchCriteria);
        $('#criteria_list_container').on('click', 'button.btn-clear-search', clearSearch);

        // Restore previous search or redirected search
        @if (is_null($redirect_criteria))
            restoreSearchParams(@json(session('params_keywordsearch')));
        @else
            $('#criteria_args').val('{!! $redirect_criteria !!}');
            $('#btn_submit').trigger('click');
        @endif
    });
</script>
@endsection

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="btn-group mb-2">
                <button class="btn btn-outline-secondary btn-lg dropdown-toggle" type="button" id="dropbtn_cambio_db" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Base de Datos: <strong class="text-capitalize">{{ $normas_db }}</strong></button>
                <div class="dropdown-menu" aria-labelledby="dropbtn_cambio_db">
                    @foreach ($bases as $b)
                        @if ($normas_db != $b)
                            <a class="dropdown-item" href="{{ route('normas.searchkeyword', ['normas_db' => $b]) }}">Ir a <strong class="text-capitalize">{{ $b }}</strong> </a>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="btn-group mb-2">
                <button class="btn btn-outline-secondary btn-lg dropdown-toggle" type="button" id="dropbtn_buscar_por" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">B&uacute;squeda: <strong>Por Palabra Clave</strong></button>
                <div class="dropdown-menu" aria-labelledby="dropbtn_buscar_por">
                    <a class="dropdown-item" href="{{ route('normas.searchsimple', [ 'normas_db' => $normas_db]) }}">B&uacute;squeda <strong>Simple por Descriptor</strong></a>
                    <a class="dropdown-item" href="{{ route('normas.search', ['normas_db' => $normas_db]) }}">B&uacute;squeda <strong>Avanzada</strong></a>
                </div>
            </div>
        </div>
    </div>
    
    @isset($digesto)
        @if ($digesto->descriptores->count() > 0)
        <div class="row">
            <div class="col mb-3">
                Descriptores aplicados a base de datos ({{ $digesto->descriptores->count() }} descriptor{{ ($digesto->descriptores->count() > 1) ? 'es' : '' }}): <a href="#" class="toggle-show-descriptores badge badge-primary d-none">Ver menos...</a> <br>
                <span id="descriptores_short">
                    @foreach ($digesto->descriptores->take(10) as $d)
                        <a href="{{ route('normas.searchsimple', [ 'normas_db' => $digesto->nombre, 'descriptor_id' => $d->id]) }}" class="badge badge-{{ ($d->pivot->condicion == 'and') ? 'dark' : 'secondary' }}">{{ $d->tag }}</a> 
                    @endforeach
                    @if ($digesto->descriptores->count() > 10)
                        <a href="#" class="toggle-show-descriptores badge badge-primary">Ver todos...</a> 
                    @endif
                </span>
                <span id="descriptores_long" class="d-none">
                    @foreach ($digesto->descriptores as $d)
                        <a href="{{ route('normas.searchsimple', [ 'normas_db' => $digesto->nombre, 'descriptor_id' => $d->id]) }}" class="badge badge-{{ ($d->pivot->condicion == 'and') ? 'dark' : 'secondary' }}">{{ $d->tag }}</a> 
                    @endforeach
                </span>
            </div>
        </div>
        @endif
    @endisset

    <form class="mb-3" id="search_form">
        <div class="form-group">
            <label for="criteria_args">Criterio de B&uacute;squeda</label>
            <input class="form-control" id="criteria_args" name="criteria_args" placeholder="Ingrese palabras clave."/>
            <small id="criteria_argsHelpBlock" class="form-text text-muted">
                Ingrese los t&eacute;rminos de b&uacute;squeda separados por espacios. El orden de los t&eacute;rminos indica como ser&aacute; aplicada la predecencia de la b&uacute;squeda.<br/>
                Para buscar frases completas, encierrelas entre comillas dobles. Por ejemplo: "osse tarifas 2021".<br/>
                Para buscar por fechas, respete el formato AAAA-MM-DD. Por ejemplo: 2021-02-25, o bien 2021-02.
            </small>
        </div>
        <div class="form-group form-check">
            <input class="form-check-input active" type="checkbox" value="" id="search_on_search" checked="checked">
            <label class="form-check-label" for="search_on_search">Buscar en resultados</label>
            <small id="search_on_searchHelpBlock" class="form-text text-muted">
                Si marca esta casilla, la búsqueda se realizar&aacute; sobre los resultados previos.
            </small>
        </div>
        <a href="{{ route('dashboard.showdbselector') }}" class="btn btn-secondary" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> Volver</a>
        <button type="submit" class="btn btn-primary" id="btn_submit">Consultar</button>
    </form>
    
    <div class="row">
        <div class="col-md-12" id="criteria_list_container"></div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <ul id="criteria_desc"></ul>
            
            <h6>
                <button type="button" class="btn btn-sm btn-secondary d-none" id="btn_download_pdf" disabled><i class="fa fa-download" aria-hidden="true"></i> Descargar Resultados</button>    
                <span id="cant_resultados"></span>
            </h6>
            
            @include('layouts.paginator')
            
            <div class="progress mt-3" id="progress_bar">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <ol class="list-group mb-3" id="resultados"></ol>

            @include('layouts.paginator')

        </div>
    </div>
</div>
@endsection