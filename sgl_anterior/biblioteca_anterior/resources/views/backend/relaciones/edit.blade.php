@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // ---- Globals -----------------------------------------------------------
    var relaciones = @json($relaciones);
    var tipo_desc_origen = {
        'A': 'Abroga',
        'D': 'Deroga',
        'R': 'Reglamenta',
        'M': 'Modifica'
    };
    var tipo_desc_destino = {
        'A': 'Abrogada por',
        'D': 'Derogada por',
        'R': 'Reglamentada por',
        'M': 'Modificada por'
    };

    // ---- Functions ---------------------------------------------------------
    var fillData = function () {
        // Muestro resultados
        $.each(relaciones, function(k, v) {
            var f_row = addFormRow();
            f_row.find('.frm_id').val(v.id);
            f_row.find('.frm_sentido').val(v.sentido);
            f_row.find('.frm_tipo').val(v.tipo);
            f_row.find('.frm_a').val(v.a);
            f_row.find('.frm_n').val(v.n);
            f_row.find('.frm_p').val(v.p);

            f_row.find('.frm_sentido').trigger('change');
        });
    };

    var addFormRow = function () {
        var new_form_row = $($('#template_form_row').html());
        $('#form_data_table tbody tr:last').before(new_form_row);
        
        return new_form_row;
    };

    // ---- Callbacks ---------------------------------------------------------

    var callbackChangeFrmSentido = function (e) {
        var row = $(this).parents('tr.form_data_row');
        var tipo_desc = ($(this).val() == 'O') 
            ? tipo_desc_origen
            : tipo_desc_destino;
        row.find('select.frm_tipo > option').each(function (i) {
           $(this).text(tipo_desc[$(this).val()]);
        });
    };

    var callbackBtnAddFormRow = function (e) {
        addFormRow();
    };

    var callbackBtnRemoveFormRow = function (e) {
        $(this).closest('.form_data_row').remove();
    };

    var callbackBtnGuardar = function (e) {
        // Convierto el contenido de los datos ingresados a un form array.
        $('#form_data_table tr.form_data_row').each(function(i) { 
            var d_id = $(this).find('.frm_id').val();
            var d_sentido = $(this).find('.frm_sentido').val();
            var d_tipo = $(this).find('.frm_tipo').val();
            var d_a = $(this).find('.frm_a').val();
            var d_n = $(this).find('.frm_n').val();
            var d_p = $(this).find('.frm_p').val();
            $('#dataForm').append(`<input type="text" name="relaciones[${i}][id]" value="${d_id}">`);
            $('#dataForm').append(`<input type="text" name="relaciones[${i}][sentido]" value="${d_sentido}">`);
            $('#dataForm').append(`<input type="text" name="relaciones[${i}][tipo]" value="${d_tipo}">`);
            $('#dataForm').append(`<input type="text" name="relaciones[${i}][a]" value="${d_a}">`);
            $('#dataForm').append(`<input type="text" name="relaciones[${i}][n]" value="${d_n}">`);
            $('#dataForm').append(`<input type="text" name="relaciones[${i}][p]" value="${d_p}">`);
        });

        // Submit form
        $('#dataForm').submit();
    };

    // ------------------------------------------------------------------------
    // jQuery Document Ready Function -----------------------------------------
    // ------------------------------------------------------------------------
	$(document).ready(function() {
        // Botones
        $('.btn-guardar').click(callbackBtnGuardar);
        $('#add_form_row').click(callbackBtnAddFormRow);
        $('#form_data_table').on('click', '.btn_del_form_row', callbackBtnRemoveFormRow);
        $('#form_data_table').on('change', '.frm_sentido', callbackChangeFrmSentido);
        
        // Setup 
        // Hago el fill despues de asignar eventos para que se disparen durate el setup
        fillData();
	});
</script>
@endsection

@section('content')

<!-- template -->
<template id="template_form_row">
    <tr class="form_data_row">
        <input type="hidden" class="frm_id" value="">
        <td>
            <select class="form-control frm_sentido">
                <option value="O">Origen</option>
                <option value="D">Destino</option>
            </select>
        </td>
        <td>
            <select class="form-control frm_tipo">
                <option value="A">Abroga</option>
                <option value="D">Deroga</option>
                <option value="R">Reglamenta</option>
                <option value="M">Modifica</option>
            </select>        
        </td>
        <td>
            <select class="form-control frm_a">
                @foreach ($norma_tipo_acto as $nta_k => $nta_v)
                    <option value="{{ $nta_k }}">{{ $nta_v }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="text" class="form-control frm_n" placeholder="Ingrese número"></td>
        <td><input type="text" class="form-control frm_p" placeholder="Ingrese artículo/anexo"></td>
        <td><button type="button" class="btn btn-secondary btn_del_form_row"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>
    </tr>    
</template>

<!-- form oculto -->
<form id="dataForm" class="invisible" action="{{ route('backend.relaciones.update', [ 'norma' => $norma ]) }}" method="POST">
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
                Relaciones
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
                        <th scope="col" style="width: 12%;">Sentido</th>
                        <th scope="col" style="width: 17%;">Tipo</th>
                        <th scope="col" style="width: 26%;">Acto</th>
                        <th scope="col" style="width: 18%;">N&uacute;mero</th>
                        <th scope="col" style="width: 22%;">Art&iacute;culo/Anexo</th>
                        <th scope="col" style="width: 5%;">#</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6">
                            <button type="button" class="btn btn-secondary" id="add_form_row">
                                <i class="fa fa-plus" aria-hidden="true"></i> Agregar otra relaci&oacute;n
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
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