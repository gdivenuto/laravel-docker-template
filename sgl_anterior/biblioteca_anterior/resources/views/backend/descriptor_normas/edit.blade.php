@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // ---- Globals -----------------------------------------------------------
    var descriptores_norma = @json($norma->descriptores->map(function ($d) { return ['id'=>$d->id, 'tag'=>$d->tag]; }));
    var handler_buscar_descriptor = null;
    var timeout_buscar_descriptor = 400;    // timeout para disparar busqueda de descriptores
    var charcount_buscar_descriptor = {{ config('params.charCountBackendDescriptorJson') }};   // cantidad minima de caracteres para disparar busqueda de descriptores
    var maxresults_buscar_descriptor = {{ config('params.maxBackendDescriptorJson') }};  // cantidad maxima de resultados en busqueda de descriptores
    var current_maxresults = maxresults_buscar_descriptor;

    // ---- Functions ---------------------------------------------------------
    var fillData = function () {
        // Ordeno descriptores por tag
        descriptores_norma.sort((a, b) => (a.tag > b.tag) ? 1 : -1 );

        // Muestro resultados
        $('#descriptores_seleccionados').empty();
        $.each(descriptores_norma, function(k, v) {
            $('#descriptores_seleccionados').append(
                `<li class="list-group-item" data-descriptor_id="${v.id}">` + 
                    '<button type="button" class="btn btn-sm btn-secondary btn-delete-descriptor"><i class="fa fa-trash-o" aria-hidden="true"></i></button> ' +
                    v.tag + 
                '</li>'
            );
        });
    };

    var addDescriptorSeleccionado = function (d_id, d_tag) {
        if ($('#descriptores_seleccionados').find(`li[data-descriptor_id=${d_id}]`).length == 0) {
            $('#descriptores_seleccionados').append(
                `<li class="list-group-item" data-descriptor_id="${d_id}">` + 
                    '<button type="button" class="btn btn-sm btn-secondary btn-delete-descriptor"><i class="fa fa-trash-o" aria-hidden="true"></i></button> ' +
                    '<i class="fa fa-plus" aria-hidden="true"></i> ' +
                    d_tag + 
                '</li>'
            );
        }
    };

    // ---- Callbacks ---------------------------------------------------------
    var callbackBuscarDescriptor = function () {
        var str = $('#descriptores_disponibles_search').val().trim().toLowerCase();

        if ((str != '') && (str.length >= charcount_buscar_descriptor)) {
            $.ajax({
                method: 'GET',
                url: `{{ route('backend.descriptornormas.getdescriptoresjson') }}?b={{ $norma->base }}&t=${str}&c=${current_maxresults}`,
                contentType: 'application/json'
            })
            .done(function(response, textStatus, jqXHR) {
                $('#descriptores_disponibles').empty();

                if (response.status == 'OK') {
                    if (response.data.descriptores.length > 0) {
                        $.each(response.data.descriptores, function(k, v) {
                            $('#descriptores_disponibles').append(
                                `<li class="list-group-item" data-descriptor_id="${v.id}">` + 
                                    '<button type="button" class="btn btn-sm btn-secondary btn-add-descriptor"><i class="fa fa-plus" aria-hidden="true"></i></button> ' +
                                    v.tag +
                                '</li>'
                            );
                        });
                        if (response.data.recortado) {
                            $('#descriptores_disponibles').prepend(`<li class="list-group-item"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <i>Se encontraron más de ${response.data.filtro_cantidad} descriptores. Por favor, refine la búsqueda.</i> <a class="btn-ver-mas" href="#">[Ver ${maxresults_buscar_descriptor} más]</a></li>`);
                            $('#descriptores_disponibles').append(`<li class="list-group-item"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <i>El resto de los descriptores han sido omitidos. Por favor, refine la búsqueda.</i> <a class="btn-ver-mas" href="#">[Ver ${maxresults_buscar_descriptor} más]</a></li>`);
                        }
                    } else {
                        var descriptor_tag = $('#descriptores_disponibles_search').val().trim().toUpperCase();
                        $('#descriptores_disponibles').prepend(
                            '<li class="list-group-item">' + 
                                `<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <i>El descriptor <strong>${descriptor_tag}</strong> no existe en la base <strong class="text-capitalize">{{ $norma->base }}</strong>.</i>` +
                                '<br/>' + 
                                `<button type="button" class="btn btn-sm btn-secondary btn-nuevo-descriptor mt-1" data-descriptor_tag="${descriptor_tag}"><i class="fa fa-plus" aria-hidden="true"></i> Crear <strong>"${descriptor_tag}"</strong></button>` +
                            '</li>'
                        );
                    }
                } else {
                    alert(`Ha ocurrido un error al buscar (${response.status}): ${response.message}`);
                }
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                alert(`Ha ocurrido un error al buscar: ${errorThrown}`);
            });
        } else {
            $('#descriptores_disponibles').empty();
        }
    };

    var callbackLinkVerMasDescriptores = function (e) {
        e.preventDefault();
        current_maxresults += maxresults_buscar_descriptor;
        callbackBuscarDescriptor();
    }

    var callbackAddDescriptorDisponible = function (e) {
        addDescriptorSeleccionado(
            $(this).parent().data('descriptor_id'),
            $(this).parent().text()
        );
    };

    var callbackRemoveDescriptorSeleccionado = function (e) {
        $(this).parent().remove();
    };

    var callbackBtnConfirmNuevoDescriptor = function (e) {
        var d_tag = $(this).data('descriptor_tag');
        $('#modalconfirm_body_txt').html(`Está a punto de agregar el descriptor <strong>${d_tag}</strong> a la base <strong class="text-capitalize">{{ $norma->base }}</strong>.<br/><br/>¿Está seguro?`);
        $('#modalconfirm').modal();
    };

    var callbackBtnNuevoDescriptor = function (e) {
        var str = $('#descriptores_disponibles_search').val().trim().toLowerCase();

        if ((str != '') && (str.length >= charcount_buscar_descriptor)) {
            $.ajax({
                method: 'POST',
                url: '{{ route('backend.descriptornormas.adddescriptorjson') }}',
                data: JSON.stringify({
                    '_token': '{{ csrf_token() }}',
                    'base': '{{ $norma->base }}',
                    'tag': str
                }),
                contentType: 'application/json'
            })
            .done(function(response, textStatus, jqXHR) {
                if (response.status == 'OK') {
                    addDescriptorSeleccionado(response.data.id, response.data.tag);
                    callbackBuscarDescriptor();
                    $('#modalconfirm').modal('hide');
                } else {
                    alert(`Ha ocurrido un error al crear descriptor (${response.status}): ${response.message}`);
                }
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                alert(`Ha ocurrido un error al crear descriptor: ${errorThrown}`);
            });
        }
    };

    var callbackBtnGuardar = function (e) {
        // Convierto el contenido de los descriptores seleccionados a un form array.
        $('#descriptores_seleccionados li').each(function(i) { 
            var d_id = $(this).data('descriptor_id'); 
            $('#dataForm').append(`<input type="text" name="descriptores[${i}][descriptor_id]" value="${d_id}">`);
        });

        // Submit form
        $('#dataForm').submit();
    };

    // ------------------------------------------------------------------------
    // jQuery Document Ready Function -----------------------------------------
    // ------------------------------------------------------------------------
	$(document).ready(function() {
        // Setup
        fillData();
        $('#descriptores_disponibles_search').prop('placeholder', `Ingrese al menos ${charcount_buscar_descriptor} letras para buscar un descriptor`)

        // Inicializa el hander con una ejecucion "forzada" del metodo
        handler_buscar_descriptor = setTimeout(callbackBuscarDescriptor, timeout_buscar_descriptor);
        
        $('#descriptores_disponibles_search').keydown(function (e) {
            if (e.keyCode == 13)
                e.preventDefault();
            else {
                // Cada vez que se dispara la busqueda por tipeo, reseteo la cantidad de resultados
                current_maxresults = maxresults_buscar_descriptor;

                // Limpio el timeout y lanzo el proximo
                clearTimeout(handler_buscar_descriptor);
                handler_buscar_descriptor = setTimeout(callbackBuscarDescriptor, timeout_buscar_descriptor);
            }
        });

        // Botones
        $('.btn-guardar').click(callbackBtnGuardar);
        $('#modalconfirm_btn_yes').click(callbackBtnNuevoDescriptor);
        $('#descriptores_disponibles').on('click', '.btn-add-descriptor', callbackAddDescriptorDisponible);
        $('#descriptores_disponibles').on('click', '.btn-nuevo-descriptor', callbackBtnConfirmNuevoDescriptor);
        $('#descriptores_disponibles').on('click', '.btn-ver-mas', callbackLinkVerMasDescriptores);
        $('#descriptores_seleccionados').on('click', '.btn-delete-descriptor', callbackRemoveDescriptorSeleccionado);

	});
</script>
@endsection

@section('content')

<!-- modalconfirm -->
@include('layouts.modalconfirm')

<!-- form oculto -->
<form id="dataForm" class="invisible" action="{{ route('backend.descriptornormas.update', [ 'norma' => $norma ]) }}" method="POST">
    @method('put')
    @csrf
</form>

<!-- contenido -->
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-12">    
            <p class="h4">
                <i class="fa fa-home" aria-hidden="true"></i> &gt;
                <a href="{{ route('backend.dashboard.index') }}">Dashboard</a> &gt; 
                <a href="{{ route('backend.normas.index') }}">Normas</a> &gt;
                <a href="{{ route('backend.normas.edit', ['norma' => $norma]) }}">Editar ID <i>{{ $norma->id }}</i></a> &gt; 
                Descriptores
            </p>
        </div>
    </div>
</div>

<div class="container-fluid">

    @include('backend.layouts.norma_form_tabs')
    
    @include('backend.layouts.errors')

    <div class="row mb-3">
        <div class="col-sm-12">
            <a class="btn btn-secondary" role="button" href="{{ route('backend.normas.index') }}">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Volver
            </a>
            <button type="button" class="btn btn-primary btn-guardar">
                <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
            </button>
            <div class="btn-group">
                <a class="btn btn-secondary" role="button" href="{{ route('normas.show', ['normas_db' => $norma->base, 'norma' => $norma->id]) }}">
                    <i class="fa fa-eye" aria-hidden="true"></i> Previsualizar
                </a>
                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Más...</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('backend.normas.show', ['norma' => $norma]) }}">Previsualizar aquí</a>
                    <a class="dropdown-item" href="{{ route('backend.normas.show', ['norma' => $norma]) }}" target="_blank">Previsualizar en otra ventana</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <h5>Descriptores disponibles (base <strong class="text-capitalize">{{ $norma->base }}</strong>):</h5>
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="descriptores_disponibles_search" value="" placeholder="">
                <div class="input-group-append">
                    <span class="input-group-text" id="descriptores_disponibles_search_btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </span>
                </div>
            </div>
            <ul id="descriptores_disponibles" class="list-group list-group-flush overflow-auto" style="height: 300px;">
            </ul>
        </div>
        <div class="col-sm-6">
            <h5>Descriptores seleccionados:</h5>
            <ul id="descriptores_seleccionados" class="list-group list-group-flush overflow-auto" style="height: 350px;">
            </ul>            
        </div>
    </div>

    <div class="row my-3">
        <div class="col-sm-12">
            <a class="btn btn-secondary" role="button" href="{{ route('backend.normas.index') }}">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Volver
            </a>
            <button type="button" class="btn btn-primary btn-guardar">
                <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
            </button>
            <div class="btn-group">
                <a class="btn btn-secondary" role="button" href="{{ route('normas.show', ['normas_db' => $norma->base, 'norma' => $norma->id]) }}">
                    <i class="fa fa-eye" aria-hidden="true"></i> Previsualizar
                </a>
                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Más...</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('backend.normas.show', ['norma' => $norma]) }}">Previsualizar aquí</a>
                    <a class="dropdown-item" href="{{ route('backend.normas.show', ['norma' => $norma]) }}" target="_blank">Previsualizar en otra ventana</a>
                </div>
            </div>
        </div>
    </div>    
</div>    


@endsection