@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // ---- Globals -----------------------------------------------------------
    var descriptores_digesto = @json($digesto->descriptores->map(function ($d) { return ['id'=>$d->id, 'tag'=>$d->tag, 'condicion'=>$d->pivot->condicion]; }));
    var handler_buscar_descriptor = null;
    var timeout_buscar_descriptor = 400;    // timeout para disparar busqueda de descriptores
    var charcount_buscar_descriptor = {{ config('params.charCountBackendDescriptorJson') }};   // cantidad minima de caracteres para disparar busqueda de descriptores
    var maxresults_buscar_descriptor = {{ config('params.maxBackendDescriptorJson') }};  // cantidad maxima de resultados en busqueda de descriptores
    var current_maxresults = maxresults_buscar_descriptor;

    // ---- Functions ---------------------------------------------------------
    var fillData = function () {
        // Ordeno descriptores por tag
        descriptores_digesto.sort((a, b) => (a.tag > b.tag) ? 1 : -1 );

        // Muestro resultados
        $('#descriptores_seleccionados').empty();
        $.each(descriptores_digesto, function(k, v) {
            $('#descriptores_seleccionados').append(
                `<li class="list-group-item" data-descriptor_id="${v.id}" data-descriptor_condicion="${v.condicion}">` + 
                    '<button type="button" class="btn btn-sm btn-secondary btn-delete-descriptor"><i class="fa fa-trash-o" aria-hidden="true"></i></button> ' +
                    '<select class="frm_descriptor_condicion">' +
                        `<option value="and" ${(v.condicion == 'and') ? 'selected' : ''}>Requerido</option>` +
                        `<option value="or" ${(v.condicion == 'or') ? 'selected' : ''}>Opcional</option>` +
                    '</select>&nbsp;' +
                    v.tag + 
                '</li>'
            );
        });
    };

    var addDescriptorSeleccionado = function (d_id, d_tag) {
        if ($('#descriptores_seleccionados').find(`li[data-descriptor_id=${d_id}]`).length == 0) {
            $('#descriptores_seleccionados').append(
                `<li class="list-group-item" data-descriptor_id="${d_id}" data-descriptor_condicion="or">` + 
                    '<button type="button" class="btn btn-sm btn-secondary btn-delete-descriptor"><i class="fa fa-trash-o" aria-hidden="true"></i></button> ' +
                    '<i class="fa fa-plus" aria-hidden="true"></i> ' +
                    '<select class="frm_descriptor_condicion">' +
                        '<option value="and">Requerido</option>' +
                        '<option value="or" selected>Opcional</option>' +
                    '</select>&nbsp;' +
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
                url: `{{ route('backend.descriptornormas.getdescriptoresjson') }}?t=${str}&c=${current_maxresults}`,
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
                                `<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <i>El descriptor <strong>${descriptor_tag}</strong> no existe.` +
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
        $('#modalconfirm_body_txt').html(`Está a punto de agregar el descriptor <strong>${d_tag}</strong>.<br/><br/>¿Está seguro?`);
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
                    'base': 'normas', // por defecto los descriptores de digesto se almacenan como pertenecientes a 'normas'
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

    var callbackUpdateCondicionDescriptor = function (e) {
        var item = $(this).parent();
        item.data('descriptor_condicion', $(this).val());
    };

    var callbackBtnGuardar = function (e) {
        // Convierto el contenido de los descriptores seleccionados a un form array.
        $('#descriptores_seleccionados li').each(function(i) { 
            var d_id = $(this).data('descriptor_id');
            var d_condicion = $(this).data('descriptor_condicion');
            $('#dataForm').append(`<input type="hidden" name="descriptores[${i}][descriptor_id]" value="${d_id}">`);
            $('#dataForm').append(`<input type="hidden" name="descriptores[${i}][descriptor_condicion]" value="${d_condicion}">`);
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
        $('#descriptores_seleccionados').on('change', '.frm_descriptor_condicion', callbackUpdateCondicionDescriptor);
	});
</script>
@endsection

@section('content')
<!-- modalconfirm -->
@include('layouts.modalconfirm')

<!-- form oculto -->
<form id="dataForm" class="invisible" action="{{ route('backend.descriptordigestos.update', [ 'digesto' => $digesto ]) }}" method="POST">
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
                <a href="{{ route('backend.digestos.index') }}">Digestos</a> &gt; 
                <a href="{{ route('backend.digestos.edit', [ 'digesto' => $digesto ]) }}">Editar <i class="text-capitalize">{{ $digesto->nombre }}</i></a> &gt; 
                Descriptores
            </p>
        </div>
    </div>
</div>

<div class="container-fluid">

    @include('backend.layouts.digesto_form_tabs')
    
    @include('backend.layouts.errors')

    <div class="row mb-3">
        <div class="col-sm-12">
            <a class="btn btn-secondary" role="button" href="{{ route('backend.digestos.index') }}">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Volver
            </a>
            <button type="button" class="btn btn-primary btn-guardar">
                <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
            </button>
            @if ($digesto->id)
            <div class="btn-group">
                <a class="btn btn-secondary" role="button" href="{{ route('normas.searchkeyword', ['normas_db' => $digesto->nombre ]) }}">
                    <i class="fa fa-eye" aria-hidden="true"></i> Previsualizar
                </a>
                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Más...</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('normas.searchkeyword', ['normas_db' => $digesto->nombre ]) }}">Previsualizar aquí</a>
                    <a class="dropdown-item" href="{{ route('normas.searchkeyword', ['normas_db' => $digesto->nombre ]) }}" target="_blank">Previsualizar en otra ventana</a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <h5>Descriptores disponibles</strong>):</h5>
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
            <a class="btn btn-secondary" role="button" href="{{ route('backend.digestos.index') }}">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Volver
            </a>
            <button type="button" class="btn btn-primary btn-guardar">
                <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
            </button>
            @if ($digesto->id)
            <div class="btn-group">
                <a class="btn btn-secondary" role="button" href="{{ route('normas.searchkeyword', ['normas_db' => $digesto->nombre ]) }}">
                    <i class="fa fa-eye" aria-hidden="true"></i> Previsualizar
                </a>
                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Más...</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('normas.searchkeyword', ['normas_db' => $digesto->nombre ]) }}">Previsualizar aquí</a>
                    <a class="dropdown-item" href="{{ route('normas.searchkeyword', ['normas_db' => $digesto->nombre ]) }}" target="_blank">Previsualizar en otra ventana</a>
                </div>
            </div>
            @endif
        </div>
    </div>   
</div>    

@endsection