@extends('layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // Select2 default behavior
    $.fn.select2.defaults.set('theme', 'bootstrap4');

    // Globals ----------------------------------------------------------------
    var default_descriptors = @json($default_descriptors);
    var default_logic = '{{ $descriptor_logic }}';
    var link_target = '';
    
    // Delegates --------------------------------------------------------------
    @include('layouts.paginatorlogic')


    var copyToClipboard = function (textToCopy) {
        // navigator clipboard api needs a secure context (https)
        if (navigator.clipboard && window.isSecureContext) {
            // navigator clipboard api method'
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

    var enableDownloadButtons = function (show) {
        if (show) {
            $('#btn_download_pdf').removeAttr('disabled');
            $('#btn_download_pdf').removeClass('d-none');
            $('#btn_share').removeAttr('disabled');
            $('#btn_share').removeClass('d-none');
        } else {
            $('#btn_download_pdf').attr('disabled', true);
            $('#btn_download_pdf').addClass('d-none');
            $('#btn_share').attr('disabled', true);
            $('#btn_share').addClass('d-none');
        }
    };

    var doSearch = function (page = 1) {
        var filter_logic = $('input[name=filter_logic]:checked', '#search_form').val();
        var filter_descriptores = ($('#filter_has_descriptores').prop('checked')) 
            ? $('#filter_descriptores').val().join()
            : null;

        if (['or', 'and'].includes(filter_logic) && filter_descriptores) {
            disablePaginator();

            $.ajax({
                method: 'GET',
                url: `{{ route('normas.getnormasbydescriptorjson', [ 'normas_db' => $normas_db, 'descriptor_logic' => '_logic_', 'descriptor_id_list' => '_list_' ]) }}?page=${page}`
                    .replace('_logic_', filter_logic)
                    .replace('_list_', filter_descriptores),
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
                            // por el bind de la funcion delegada, this = $('#filter_descriptores').val()
                            return `<a href="{{ route('normas.searchsimple', [ 'normas_db' => $normas_db, 'descriptor_id' => '_descid_']) }}" class="badge badge-${(this.includes(v.id.toString())) ? 'primary' : 'secondary'}" ${link_target}>${v.tag}</a>`.replace('_descid_', v.id);
                        }.bind($('#filter_descriptores').val()));
                        
                        fsancion = (v.fec_sancion) ? moment(v.fec_sancion).format("DD/MM/YYYY") : '(sin fecha)';
                        fpromulga = (v.fec_promulga) ? moment(v.fec_promulga).format("DD/MM/YYYY") : '(sin fecha)';
                        extra_btn = '';
                        
                        @auth
                            extra_btn = `<a href="{{ route('backend.normas.edit', ['norma' => '_norma_'])}}" class="btn btn-sm btn-secondary" role="button" ${link_target}><i class="fa fa-pencil" aria-hidden="true"></i></a> `.replace('_norma_', v.id);
                        @endauth
                        
                        $('#resultados').append($(`<li class="list-group-item"><a href="{{ route('normas.show', [ 'normas_db' => $normas_db, 'norma' => '_norma_']) }}" class="btn btn-sm btn-primary" role="button" ${link_target}><i class="fa fa-plus" aria-hidden="true"></i></a>  ${extra_btn}<span class="lead"><span class="badge badge-outline-primary text-capitalize">${v.base_tag}</span></span> <strong>${v.acto_desc}</strong> ${v.nro} <strong>Fecha Sanci&oacute;n:</strong> ${fsancion} <strong>Fecha Promulgaci&oacute;n:</strong> ${fpromulga} <br/> <strong>Contenido:</strong> ${v.contenido} <br/> ${desc_badges.join(' ')} </li>`.replace('_norma_', v.id)));
                    });
                }
                else 
                    alert(sprintf('ERROR: %s', response.message));
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                alert(`Ha ocurrido un error al buscar: ${errorThrown}`);
            });
        } else {
            alert('Debe especificar al menos un descriptor.');
        }
    }

    @include('normas.devicedetect')

    // jQuery Document Ready --------------------------------------------------
    $(function () {
        // Device detection
        link_target = (detectDevice().device == 'computer') ? 'target="_blank"' : '';

        // Set moment locale
        moment.locale('{!! \App::getLocale() !!}');

        // Setup Paginator
        setupPaginator();

        // ---- Input setup ---------------------------------------------------
        $('#filter_descriptores').select2({
            placeholder: "Ingrese Descriptores",
            //tags: true, // permito ingreso manual
            minimumInputLength: 3,
            closeOnSelect: true,
            ajax: {
                url: '{{ route('descriptores.getautocompletedescriptorjson') }}',
                data: function (p) { return {term: p.term, _type: p._type, q: p.term, normas_db: '{{ $normas_db }}'}; },
                dataType: 'json',
                delay: 500,
                cache: true
            }
        })
        .on('change', function (e) {
            $('#filter_has_descriptores').prop('checked', true);
        });

        // ---- Button setup --------------------------------------------------
        $('#btn_submit').click(function(e) {
            e.preventDefault();
            doSearch();
        });
        $('#btn_download_pdf').click(function(e) {
            var filter_logic = $('input[name=filter_logic]:checked', '#search_form').val();
            var filter_descriptores = ($('#filter_has_descriptores').prop('checked')) 
                ? $('#filter_descriptores').val().join()
                : null;            
            window.location.href = `{{ route('normas.getnormasbydescriptorpdf', [ 'normas_db' => $normas_db, 'descriptor_logic' => '_logic_', 'descriptor_id_list' => '_list_' ]) }}`
                    .replace('_logic_', filter_logic)
                    .replace('_list_', filter_descriptores);
        });
        $('.toggle-show-descriptores').click(function(e) {
            e.preventDefault();
            $('.toggle-show-descriptores').toggleClass('d-none');
            $('#descriptores_short').toggleClass('d-none');
            $('#descriptores_long').toggleClass('d-none');
        });
        $('#btn_share').click(function(e) {
            var filter_logic = $('input[name=filter_logic]:checked', '#search_form').val();
            var filter_descriptores = ($('#filter_has_descriptores').prop('checked')) 
                ? $('#filter_descriptores').select2('data')
                : [];
            var result = '';
            if (filter_descriptores.length > 0) {
                // preparo la lista de descriptores
                var lista_descriptores = $.map(filter_descriptores, function(d, i) { return d.text; });

                // se hace en encodeURI/decodeURI para evitar el 'doble encode'
                var descriptor_link = encodeURI(decodeURI(
                    `{{ route('normas.searchsimpletag', [ 'normas_db' => $normas_db, 'descriptor_logic' => '_descriptor_logic_', 'tag_str' => '_tag_']) }}`
                    .replace('_descriptor_logic_', filter_logic)
                    .replace('_tag_', lista_descriptores.join('|'))
                ));
                var qr_link_code = encodeURI(decodeURI(
                    `{{ route('qr.normasearchsimpletag', [ 'normas_db' => $normas_db, 'descriptor_logic' => '_descriptor_logic_', 'tag_str' => '_tag_']) }}`
                    .replace('_descriptor_logic_', filter_logic)
                    .replace('_tag_', lista_descriptores.join('|'))
                ));
                copyToClipboard(descriptor_link)
                    .then(() => {
                        $('#modalalert_body_txt').html(`<p class="text-center"><strong>¡Enlace de búsqueda copiado al portapapeles!</strong></p><p class="text-center"><img class="border border-dark rounded" src="${qr_link_code}"></img></p><p class="text-center"><small>Si lo desea, además puede utilizar este código QR para compartir la búsqueda.</small></p>`);
                    })
                    .catch(() => $('#modalalert_body_txt').html('<p>Ha ocurrido un error al copiar el enlace de descriptor al portapapeles.</p>'));
            } else {
                $('#modalalert_body_txt').html('Debe especificar al menos un descriptor para obtener el enlace.');
            }
            $('#modalalert').modal();
        });

        // ---- Has default descriptor by URL ---------------------------------
        if (default_descriptors) {
            // Pickup descriptors
            $.each(default_descriptors, function (i, v) {
                $('#filter_descriptores').append(new Option(v.tag, v.id, true, true)); 
            })
            $('#filter_descriptores').trigger('change');

            // Pickup logic
            $('#filter_logic_or').attr('checked', default_logic == 'or');
            $('#filter_logic_and').attr('checked', default_logic == 'and');

            // Trigger search
            $('#btn_submit').trigger('click');
        }

    });
</script>
@endsection

@section('content')

@include('layouts.modalalert')

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
                <button class="btn btn-outline-secondary btn-lg dropdown-toggle" type="button" id="dropbtn_buscar_por" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">B&uacute;squeda: <strong>Simple por Descriptor</strong></button>
                <div class="dropdown-menu" aria-labelledby="dropbtn_buscar_por">
                    <a class="dropdown-item" href="{{ route('normas.searchkeyword', ['normas_db' => $normas_db]) }}">B&uacute;squeda <strong>Por Palabra Clave</strong></a>
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
        <div class="form-row">
            <div class="form-group col-md-12">
                <legend class="col-form-label">Tipo de b&uacute;squeda</legend>
                <div class="input-group input-group-sm">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter_logic" id="filter_logic_or" value="or" checked>
                        <label class="form-check-label" for="filter_logic_or">Que posea <strong>alguno</strong> de los descriptores</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter_logic" id="filter_logic_and" value="and">
                        <label class="form-check-label" for="filter_logic_and">Que posea <strong>todos</strong> los descriptores</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="filter_descriptores">Descriptores</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input id="filter_has_descriptores" type="checkbox" aria-label="">
                        </div>
                    </div>
                    <select class="form-control" id="filter_descriptores" multiple="multiple"></select>
                </div>
            </div>
        </div>
        <a href="{{ route('dashboard.showdbselector') }}" class="btn btn-secondary" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> Volver</a>
        <button type="submit" class="btn btn-primary" id="btn_submit">Consultar</button>
    </form>

    <div class="row">
        <div class="col-md-12">
            <h6>
                <button type="button" class="btn btn-sm btn-secondary d-none" id="btn_download_pdf" disabled><i class="fa fa-download" aria-hidden="true"></i> Descargar Resultados</button>    
                <button type="button" class="btn btn-sm btn-secondary d-none" id="btn_share" disabled><i class="fa fa-share-alt" aria-hidden="true"></i></button>                
                <span id="cant_resultados"></span>
            </h6>

            @include('layouts.paginator')

            <ul class="list-group" id="resultados"></ul>

            @include('layouts.paginator')
        </div>
    </div>
</div>
@endsection