@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // ---- Globals -----------------------------------------------------------
    var hcd_expedientes_norma = @json($hcd_expedientes_norma);

    // ---- Functions ---------------------------------------------------------
    var fillData = function () {
        // Muestro resultados
        $.each(hcd_expedientes_norma, function(k, v) {
            var f_row = addFormRow();
            f_row.find('.frm_id').val(v.id);
            f_row.find('.frm_hcd_exped').val(v.hcd_exped);
        });
    };

    var addFormRow = function () {
        var new_form_row = $($('#template_form_row').html());
        $('#form_data_table tbody tr:last').before(new_form_row);
        
        return new_form_row;
    };

    // ---- Callbacks ---------------------------------------------------------

    var callbackBtnAddFormRow = function (e) {
        addFormRow();
    }

    var callbackBtnRemoveFormRow = function (e) {
        $(this).closest('.form_data_row').remove();
    }

    var callbackBtnGuardar = function (e) {
        // Convierto el contenido de los datos ingresados a un form array.
        $('#form_data_table tr.form_data_row').each(function(i) { 
            var d_id = $(this).find('.frm_id').val();
            var d_hcd_exped = $(this).find('.frm_hcd_exped').val();
            $('#dataForm').append(`<input type="text" name="hcd_expedientes[${i}][id]" value="${d_id}">`);
            $('#dataForm').append(`<input type="text" name="hcd_expedientes[${i}][hcd_exped]" value="${d_hcd_exped}">`);
        });

        // Submit form
        $('#dataForm').submit();
    }

    // ------------------------------------------------------------------------
    // jQuery Document Ready Function -----------------------------------------
    // ------------------------------------------------------------------------
	$(document).ready(function() {
        // Setup
        fillData();

        // Botones
        $('.btn-guardar').click(callbackBtnGuardar);
        $('#add_form_row').click(callbackBtnAddFormRow);
        $('#form_data_table').on('click', '.btn_delete_form_row', callbackBtnRemoveFormRow);
	});
</script>
@endsection

@section('content')

<!-- template -->
<template id="template_form_row">
    <input type="hidden" class="frm_id" value="">
    <tr class="form_data_row">
        <td>
            <input type="text" class="form-control frm_hcd_exped" placeholder="Ingrese número de Expediente HCD">
        </td>
        <td>
            <button type="button" class="btn btn-secondary btn_docs_form_row">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </button>
        </td>
        <td>
            <button type="button" class="btn btn-secondary btn_delete_form_row">
                <i class="fa fa-trash-o" aria-hidden="true"></i>
            </button>
        </td>
    </tr>    
</template>

<!-- form oculto -->
<form id="dataForm" class="invisible" action="{{ route('backend.hcdexpedientes.update', [ 'norma' => $norma ]) }}" method="POST">
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
                Expedientes HCD
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
        <div class="col-sm-12">
            <table class="table" id="form_data_table">
                <thead>
                    <tr>
                        <th scope="col" style="width: 90%;">Expediente HCD</th>
                        <th scope="col" style="width: 10%;">#</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2">
                            <button type="button" class="btn btn-secondary" id="add_form_row">
                                <i class="fa fa-plus" aria-hidden="true"></i> Agregar otro expediente
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if ( isset($documentos[1]) && count($documentos[1]) > 0)
                <span class="font-weight-bold">Documentos del Expediente del HCD:</span>
                <ul>
                    @foreach ($documentos[1] as $file)
                        <li>
                            <a href="{{ $documentos[0].$file }}" target="_blank">{{ $file }}</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="alert alert-primary" role="alert">El Expediente del HCD no posee documentos.</div>
            @endif
        </div>
    </div>

    <!-- <div class="row my-3">
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
    </div>     -->
</div>    


@endsection