@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
	// ---- Globals -----------------------------------------------------------
	var filtros = {!! ($digesto->id) ? $digesto->filtro : '[]' !!};

	// ---- Functions ---------------------------------------------------------
	var b64EncodeUnicode = function (str) {
	    // first we use encodeURIComponent to get percent-encoded UTF-8,
	    // then we convert the percent encodings into raw bytes which
	    // can be fed into btoa.
	    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
	        function toSolidBytes(match, p1) {
	            return String.fromCharCode('0x' + p1);
	    }));
	}

	var operadorAFiltro = function (operador, valor) {
		switch (operador) {
			case '=': return 'igual';
			case '<>': return 'distinto';
			case 'like':
				if (valor.trim().startsWith('%') && valor.trim().endsWith('%')) {
					return 'contiene';
				} else {
					return (valor.trim().startsWith('%'))
						? 'termina'
						: 'comienza';
				}
		}
	};

	var filtroAOperador = function (operador, valor) {
		switch (operador) {
			case 'igual': return ['=', valor.trim()];
			case 'distinto': return ['<>', valor.trim()];
			case 'comienza': return ['like', `${valor.trim()}%`];
			case 'termina': return ['like', `%${valor.trim()}`];
			case 'contiene': return ['like', `%${valor.trim()}%`];
		}
	};

    var fillData = function () {
        $.each(filtros, function(k, v) {
        	var f_campo = v[0];
        	var f_criterio = operadorAFiltro(v[1], v[2]);
        	var f_valor = v[2].replace(/^%/, '').replace(/%$/, '');
            var f_row = addFormRow();
            f_row.find('.frm_campo').val(f_campo);
            f_row.find('.frm_criterio').val(f_criterio);
            f_row.find('.frm_valor').val(f_valor);
        });
    };

    var convertirFiltroACadena = function () {
    	// Convierto el contenido de los filtros ingresados a un string.
        var filtros = $('#form_data_table tr.form_data_row').map(function() { 
            var f_data = filtroAOperador(
            	$(this).find('.frm_criterio').val(),
            	$(this).find('.frm_valor').val()
            );
            var f_campo = $(this).find('.frm_campo').val();
            var f_criterio = f_data[0];
            var f_valor = f_data[1];
            
            return `["${f_campo}","${f_criterio}","${f_valor}"]`;
        })
        .get()
        .join(',');

        return `[${filtros}]`;
    };

    var addFormRow = function () {
        var new_form_row = $($('#template_form_row').html());
        $('#form_data_table tbody tr:last').before(new_form_row);
        
        return new_form_row;
    };

    // ---- Callbacks ---------------------------------------------------------
    var callbackBtnAddFormRow = function (e) {
        addFormRow();
    };

    var callbackBtnRemoveFormRow = function (e) {
        $(this).closest('.form_data_row').remove();
    };

    var callbackBtnGuardar = function (e) {
    	var filtro = b64EncodeUnicode(convertirFiltroACadena()); // Base64 encoding
    	$('#dataForm').append(`<input type="hidden" name="filtro" value="${filtro}">`);
    	$('#dataForm').submit();
    };

	// ------------------------------------------------------------------------
	// jQuery Document Ready Function -----------------------------------------
	// ------------------------------------------------------------------------
	$(function () {
		// Set moment locale
		moment.locale('{!! \App::getLocale() !!}');

        // Setup 
        fillData();

		// Botones
        $('.btn-guardar').click(callbackBtnGuardar);
        $('#add_form_row').click(callbackBtnAddFormRow);
        $('#form_data_table').on('click', '.btn_del_form_row', callbackBtnRemoveFormRow);
	});
</script>
@endsection

@section('content')
<!-- template -->
<template id="template_form_row">
    <tr class="form_data_row">
        <td>
        	<select class="form-control frm_campo">
				<option value="base">Base</option>
				<option value="acto">Acto</option>
				<option value="nro">Número</option>
				<option value="origen">Origen</option>
				<option value="nro_hcd">Nº Interno</option>
				<option value="exped">Expediente D.E.</option>
				<option value="bloque">Bloque</option>
				<option value="dec_promulga">Decreto Promulgación Nº</option>
				<option value="boletin_nro">Boletín Nº</option>
				<option value="boletin_pag">Boletín Página</option>
				<option value="registro_t">Registrado Tomo</option>
				<option value="registro_f">Registrado Folio</option>
				<option value="abrogacion_a">Abrogación Acto</option>
				<option value="abrogacion_n">Abrogación Nº</option>
				<option value="contenido">Contenido</option>
				<option value="nro_tema">Tema Nº</option>
				<option value="alcance">Alcance Normativo</option>
				<option value="caracter">Carácter</option>
				<option value="recopila">Recopilación</option>
				<option value="sin_nro">Sin Nº</option>
				<option value="ingresa">Ingresa</option>
				<option value="aprobado">Aprobado</option>
				<option value="ausentes">Ausentes</option>
        	</select>
        </td>
        <td>
        	<select class="form-control frm_criterio">
        		<option value="igual">Igual a</option>
        		<option value="distinto">Distinto de</option>
        		<option value="comienza">Comienza con</option>
        		<option value="termina">Termina con</option>
        		<option value="contiene">Contiene</option>
        	</select>
        </td>
        <td><input type="text" class="form-control frm_valor" placeholder="Ingrese valor de comparación"></td>
        <td><button type="button" class="btn btn-secondary btn_del_form_row"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>
    </tr>    
</template>

<!-- contenido -->
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-12">    
            <p class="h4">
                <i class="fa fa-home" aria-hidden="true"></i> &gt;
                <a href="{{ route('backend.dashboard.index') }}">Dashboard</a> &gt; 
                <a href="{{ route('backend.digestos.index') }}">Digestos</a> &gt; 
				@if ($digesto->id)
					Editar <i class="text-capitalize">{{ $digesto->nombre }}</i>
				@else
					Nuevo digesto
				@endif
            </p>
        </div>
    </div>
</div>

<div class="container-fluid">

	@include('backend.layouts.digesto_form_tabs')

	@if ($digesto->id)
		<form id="dataForm" action="{{ route('backend.digestos.update', [ 'digesto' => $digesto->id ]) }}" method="POST">
		@method('put')
	@else
		<form id="dataForm" action="{{ route('backend.digestos.store') }}" method="POST">
	@endif
			@include('backend.layouts.errors')
			
			@csrf

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

			<div class="form-group row">
				<label for="nombre" class="col-sm-2 col-form-label text-right font-weight-bold">Nombre</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $digesto->nombre) }}">
				</div>
			</div>

			<div class="form-group row">
				<label for="publicado" class="col-sm-2 col-form-label text-right font-weight-bold">Publicado</label>
				<div class="col-sm-2">
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="publicado" id="publicado_si" value="S" {{ (old('publicado', $digesto->publicado)) ? 'checked' : '' }}>
						<label class="form-check-label" for="inlineRadio1">S&iacute;</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="publicado" id="publicado_no" value="N" {{ (old('publicado', !$digesto->publicado)) ? 'checked' : '' }}>
						<label class="form-check-label" for="inlineRadio2">No</label>
					</div>
				</div>
			</div>

			<div class="form-group row">
				<label for="descripcion" class="col-sm-2 col-form-label text-right font-weight-bold">Descripci&oacute;n</label>
				<div class="col-sm-10">
					<textarea rows="5" class="form-control" id="descripcion" name="descripcion">{{ old('descripcion', $digesto->descripcion) }}</textarea>
				</div>
			</div>

			<div class="form-group row">
				<label for="filtro" class="col-sm-2 col-form-label text-right font-weight-bold">Filtros por Campos</label>
				<div class="col-sm-10">
		            <table class="table" id="form_data_table">
		                <thead>
		                    <tr>
		                        <th scope="col" style="width: 25%;">Campo</th>
		                        <th scope="col" style="width: 25%;">Criterio</th>
		                        <th scope="col" style="width: 45%;">Valor</th>
		                        <th scope="col" style="width: 5%;">#</th>
		                    </tr>
		                </thead>
		                <tbody>
		                    <tr>
		                        <td colspan="4">
		                            <button type="button" class="btn btn-secondary" id="add_form_row">
		                                <i class="fa fa-plus" aria-hidden="true"></i> Agregar otro filtro
		                            </button>
		                        </td>
		                    </tr>
		                </tbody>
		            </table>
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

    	</form>
    </div>
</div>
@endsection