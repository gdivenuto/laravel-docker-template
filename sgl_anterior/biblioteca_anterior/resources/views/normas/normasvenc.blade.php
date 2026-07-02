@extends('layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // Select2 default behavior
    $.fn.select2.defaults.set('theme', 'bootstrap4');

    // Globals ----------------------------------------------------------------
    var link_target = '';
    
    // Delegates --------------------------------------------------------------
    @include('layouts.paginatorlogic')

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
        disablePaginator();

        $.ajax({
            method: 'GET',
            url: `{{ route('normas.getnormasavencerjson') }}?page=${page}`,
            contentType: 'application/json'
        })
        .done(function(response, textStatus, jqXHR) {
            if (response.status == 'OK') {
                $('#cant_resultados').html(`Se encontraron ${response.data.total} ocurrencia(s).`);
                $('#resultados').empty();
                renderPaginator(response.data);
                enableDownloadButtons(response.data.total > 0);

                $.each(response.data.data, function (i, v) {
                    var desc_badges = $.map(v.descriptores, function (v, i) {
                        return `<a href="{{ route('normas.searchsimple', [ 'normas_db' => 'normas', 'descriptor_id' => '_descid_']) }}" class="badge badge-secondary" ${link_target}>${v.tag}</a>`.replace('_descid_', v.id);
                    });
                    
                    fsancion = (v.fec_sancion) ? moment(v.fec_sancion).format("DD/MM/YYYY") : '(sin fecha)';
                    fpromulga = (v.fec_promulga) ? moment(v.fec_promulga).format("DD/MM/YYYY") : '(sin fecha)';
                    fexcluido = (v.fec_excluido) ? moment(v.fec_excluido).format("DD/MM/YYYY") : '(sin fecha)';
                    $('#resultados').append($(`<li class="list-group-item"><a href="{{ route('normas.show', [ 'normas_db' => 'normas', 'norma' => '_norma_']) }}" class="btn btn-sm btn-primary" role="button" ${link_target}><i class="fa fa-plus" aria-hidden="true"></i></a> <span class="lead"><span class="badge badge-outline-primary text-capitalize">${v.base_tag}</span></span> <strong>${v.acto_desc}</strong> ${v.nro} <strong>Fecha Vencimiento:</strong> ${fexcluido} <br/> <strong>Fecha Sanci&oacute;n:</strong> ${fsancion} <strong>Fecha Promulgaci&oacute;n:</strong> ${fpromulga} <br/> <strong>Contenido:</strong> ${v.contenido} <br/> ${desc_badges.join(' ')} </li>`.replace('_norma_', v.id)));
                });
            } else 
                alert(sprintf('ERROR: %s', response.message));
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            alert(`Ha ocurrido un error al buscar: ${errorThrown}`);
        });
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

        // Auto start search
        doSearch();

        // Button behaviour
        $('#btn_download_pdf').click(function(e) {
            window.location.href = '{{ route('normas.getnormasavencerpdf') }}';
        });

    });
</script>
@endsection

@section('content')
<div class="container-fluid">

    <h2>Vencimientos</h2>
    <div class="row mt-2 mb-2">
        <div class="col">
            <p>A continuaci&oacute;n se detallan las normas que tienen fecha de vencimiento dentro de los pr&oacute;ximos <strong>{{ config('params.normaVencDayCount') }} días</strong>, desde el <strong>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</strong> hasta el <strong>{{ \Carbon\Carbon::now()->startOfDay()->addDay(config('params.normaVencDayCount'))->format('d/m/Y') }}</strong> inclusive.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h6>
                <button type="button" class="btn btn-sm btn-secondary d-none" id="btn_download_pdf" disabled><i class="fa fa-download" aria-hidden="true"></i> Descargar Resultados</button>    
                <span id="cant_resultados"></span>
            </h6>

            @include('layouts.paginator')

            <ul class="list-group" id="resultados"></ul>

            @include('layouts.paginator')
        </div>
    </div>
</div>
@endsection