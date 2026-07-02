@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
	// ---- Functions ---------------------------------------------------------
	var setDatetimepickerDefault = function (control, date_val) {
		// Formato general de Fecha y Hora
		var dateFormat = 'YYYY-MM-DD';
		var MinDate = "1810-01-01";
		var dateMin = moment(MinDate, dateFormat);

		$(control).datetimepicker({
			sideBySide: true,
			format: dateFormat,
			date: date_val,
			minDate: dateMin
		})
	};

	// ------------------------------------------------------------------------
	// jQuery Document Ready Function -----------------------------------------
	// ------------------------------------------------------------------------
	$(function () {
		// Set moment locale
		moment.locale('{!! \App::getLocale() !!}');

		// Configuración de controles de fecha
		setDatetimepickerDefault('#fec_sancion', '{{ old('fec_sancion', $norma->fec_sancion) }}');
		setDatetimepickerDefault('#fec_promulga', '{{ old('fec_promulga', $norma->fec_promulga) }}');
		setDatetimepickerDefault('#fec_publica', '{{ old('fec_publica', $norma->fec_publica) }}');
		setDatetimepickerDefault('#fec_incluido', '{{ old('fec_incluido', $norma->fec_incluido) }}');
		setDatetimepickerDefault('#fec_excluido', '{{ old('fec_excluido', $norma->fec_excluido) }}');

		// Botones
		$('.btn-guardar').click(function (e) {
			$('#dataForm').submit();
		});

		// Al elegir una Base
		$('#base').change(function (e) {
			let base_elegida = $(this).val();

	        $.ajax({
	            method: 'GET',
	            url: `{{ route('backend.normas.dtgetactosjson') }}?base=${base_elegida}`,
	            contentType: 'application/json'
	        })
	        .done(function(response, textStatus, jqXHR) {
	        	// Se retiran las opciones del combo de Acto
	        	$('#acto').empty();
	        	// Si hay respuesta
                if (response.status == 'OK') {
                    if (response.data.actos) {
                        // Por cada Acto, se agrega una opción al combo
                        $.each(response.data.actos, function(k, v) {
                        	$('#acto').append(`<option value="${k}">${v} (${k})</option>`);
                        });
                    }
                }
	        })
	        .fail(function( jqXHR, textStatus, errorThrown ) {
                alert(`Ha ocurrido un error al buscar: ${errorThrown}`);
            });
		});
	});
</script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-12">    
            <p class="h4">
                <i class="fa fa-home" aria-hidden="true"></i> &gt;
                <a href="{{ route('backend.dashboard.index') }}">Dashboard</a> &gt; 
                <a href="{{ route('backend.normas.index') }}">Normas</a> &gt; 
				@if ($norma->id)
					Editar ID <i>{{ $norma->id }}</i>
				@else
					Nueva norma
				@endif                
            </p>
        </div>
    </div>
</div>

<div class="container-fluid">

	@include('backend.layouts.norma_form_tabs')

	@if ($norma->id)
		<form id="dataForm" action="{{ route('backend.normas.update', [ 'norma' => $norma->id ]) }}" method="POST">
		@method('put')
	@else
		<form id="dataForm" action="{{ route('backend.normas.store') }}" method="POST">
	@endif
			@include('backend.layouts.errors')
			
			@csrf

			<div class="row mb-3">
				<div class="col-sm-12">
					<a class="btn btn-secondary" role="button" href="{{ route('backend.normas.index') }}">
						<i class="fa fa-arrow-left" aria-hidden="true"></i> Volver
					</a>
					<button type="button" class="btn btn-primary btn-guardar">
						<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
					</button>
					@if ($norma->id)
					<a class="btn btn-secondary" role="button" href="{{ route('backend.normas.clonar', ['norma' => $norma->id])}}">
						<i class="fa fa-clone" aria-hidden="true"></i> Duplicar
					</a>
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
					@endif
				</div>
			</div>

			<div class="form-group row">
				<label for="base" class="col-sm-2 col-form-label text-right font-weight-bold">Base</label>
				<div class="col-sm-10">
					<?php $o_base = old('base', $norma->base); ?>
					<select class="form-control" id="base" name="base">
						@foreach ($norma_bases as $nb_k => $nb_v)
							<option value="{{ $nb_k }}" {{ ($o_base == $nb_k) ? 'selected':'' }}>{{ $nb_v }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group row">
				<label for="acto" class="col-sm-2 col-form-label text-right font-weight-bold">Acto</label>
				<div class="col-sm-4">
					<?php $o_acto = old('acto', $norma->acto); ?>
					<select class="form-control" id="acto" name="acto">
						@foreach ($norma_tipo_acto as $nta_k => $nta_v)
							<option value="{{ $nta_k }}" {{ ($o_acto == $nta_k) ? 'selected' : '' }}>{{ $nta_v }} ({{ $nta_k }})</option>
						@endforeach
					</select>
				</div>

				<label for="nro" class="col-sm-2 col-form-label text-right font-weight-bold">N&uacute;mero</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="nro" name="nro" value="{{ old('nro', $norma->nro) }}">
				</div>
			</div>

			<div class="form-group row">
				<label for="origen" class="col-sm-2 col-form-label text-right font-weight-bold">Origen</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="origen" name="origen" value="{{ old('origen', $norma->origen) }}">
				</div>

				<label for="nro_hcd" class="col-sm-2 col-form-label text-right font-weight-bold">N&deg; Interno</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="nro_hcd" name="nro_hcd" value="{{ old('nro_hcd', $norma->nro_hcd) }}">
				</div>
			</div>

			<div class="form-group row">
				<label for="exped" class="col-sm-2 col-form-label text-right font-weight-bold">Expediente D.E.</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="exped" name="exped" value="{{ old('exped', $norma->exped) }}">
				</div>
				
				<label for="hcd_exped" class="col-sm-2 col-form-label text-right font-weight-bold">Expediente HCD</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="hcd_exped" name="hcd_exped" value="{{ old('hcd_exped', $norma->hcd_exped) }}">
				</div>

				<label for="bloque" class="col-sm-2 col-form-label text-right font-weight-bold">Bloque</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="bloque" name="bloque" value="{{ old('bloque', $norma->bloque) }}">
				</div>
			</div>

			<div class="form-group row">
				<label for="fec_sancion" class="col-sm-2 col-form-label text-right font-weight-bold">Sanci&oacute;n</label>
				<div class="col-md-2">
					<div class="input-group date" id="fec_sancion" data-target-input="nearest">
						<div class="input-group-append" data-target="#fec_sancion" data-toggle="datetimepicker">
							<div class="input-group-text"><i class="fa fa-calendar"></i></div>
						</div>
						<input type="text" class="form-control datetimepicker-input" name="fec_sancion" data-target="#fec_sancion" value="{{ old('fec_sancion', $norma->fec_sancion) }}"/>
					</div>
				</div>

				<label for="fec_promulga" class="col-sm-2 col-form-label text-right font-weight-bold">Promulgaci&oacute;n</label>
				<div class="col-md-2">
					<div class="input-group date" id="fec_promulga" data-target-input="nearest">
						<div class="input-group-append" data-target="#fec_promulga" data-toggle="datetimepicker">
							<div class="input-group-text"><i class="fa fa-calendar"></i></div>
						</div>
						<input type="text" class="form-control datetimepicker-input" name="fec_promulga" data-target="#fec_promulga" value="{{ old('fec_promulga', $norma->fec_promulga) }}"/>
					</div>
				</div>

				<label for="fec_publica" class="col-sm-2 col-form-label text-right font-weight-bold">Publicaci&oacute;n</label>
				<div class="col-md-2">
					<div class="input-group date" id="fec_publica" data-target-input="nearest">
						<div class="input-group-append" data-target="#fec_publica" data-toggle="datetimepicker">
							<div class="input-group-text"><i class="fa fa-calendar"></i></div>
						</div>
						<input type="text" class="form-control datetimepicker-input" name="fec_publica" data-target="#fec_publica" value="{{ old('fec_publica', $norma->fec_publica) }}"/>
					</div>
				</div>
			</div>

			<div class="form-group row">
				<label for="dec_promulga" class="col-sm-2 col-form-label text-right font-weight-bold">Decreto Promulgaci&oacute;n N&deg;</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="dec_promulga" name="dec_promulga" value="{{ old('dec_promulga', $norma->dec_promulga) }}">
				</div>

				<label for="boletin_nro" class="col-sm-2 col-form-label text-right font-weight-bold">Bolet&iacute;n N°</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="boletin_nro" name="boletin_nro" value="{{ old('boletin_nro', $norma->boletin_nro) }}">
				</div>

				<label for="boletin_pag" class="col-sm-2 col-form-label text-right font-weight-bold">Bolet&iacute;n P&aacute;gina</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="boletin_pag" name="boletin_pag" value="{{ old('boletin_pag', $norma->boletin_pag) }}">
				</div>
			</div>

			<div class="form-group row">
				<label for="abrogacion_a" class="col-sm-2 col-form-label text-right font-weight-bold">Abrogaci&oacute;n Acto</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="abrogacion_a" name="abrogacion_a" value="{{ old('abrogacion_a', $norma->abrogacion_a) }}">
				</div>

				<label for="abrogacion_n" class="col-sm-2 col-form-label text-right font-weight-bold">Abrogaci&oacute;n Nº</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="abrogacion_n" name="abrogacion_n" value="{{ old('abrogacion_n', $norma->abrogacion_n) }}">
				</div>

				<label for="nro_tema" class="col-sm-2 col-form-label text-right font-weight-bold">Tema N&deg;</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="nro_tema" name="nro_tema" value="{{ old('nro_tema', $norma->nro_tema) }}">
				</div>				
			</div>

			<div class="form-group row">
				<label for="registro_t" class="col-sm-2 col-form-label text-right font-weight-bold">Registrado Tomo</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="registro_t" name="registro_t" value="{{ old('registro_t', $norma->registro_t) }}">
				</div>

				<label for="registro_f" class="col-sm-2 col-form-label text-right font-weight-bold">Registrado Folio</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="registro_f" name="registro_f" value="{{ old('registro_f', $norma->registro_f) }}">
				</div>

				<label for="recopila" class="col-sm-2 col-form-label text-right font-weight-bold">Recopilaci&oacute;n</label>
				<div class="col-sm-2">
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="recopila" id="recopila_si" value="S" {{ (old('recopila', $norma->recopila) == 'S') ? 'checked' : '' }}>
						<label class="form-check-label" for="inlineRadio1">S&iacute;</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="recopila" id="recopila_no" value="N" {{ (old('recopila', $norma->recopila) == 'N') ? 'checked' : '' }}>
						<label class="form-check-label" for="inlineRadio2">No</label>
					</div>
				</div>
			</div>

			<div id="contenido_control" class="form-group row">
				<label for="contenido" class="col-sm-2 col-form-label text-right font-weight-bold">Contenido</label>
				<div class="col-sm-10">
					<textarea class="form-control" id="contenido" name="contenido" rows="5">{{ old('contenido', $norma->contenido) }}</textarea>
				</div>
			</div>

			<div class="form-group row">
				<label for="alcance" class="col-sm-2 col-form-label text-right font-weight-bold">Alcance Normativo</label>
				<div class="col-sm-4">
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="alcance" id="alcance_general" value="G" {{ (old('alcance', $norma->alcance) == 'G') ? 'checked' : '' }}>
						<label class="form-check-label" for="inlineRadio1">General</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="alcance" id="alcance_particular" value="P" {{ (old('alcance', $norma->alcance) == 'P') ? 'checked' : '' }}>
						<label class="form-check-label" for="inlineRadio2">Particular</label>
					</div>
				</div>

				<label for="caracter" class="col-sm-2 col-form-label text-right font-weight-bold">Car&aacute;cter</label>
				<div class="col-sm-4">
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="caracter" id="caracter_permanente" value="P" {{ (old('caracter', $norma->caracter) == 'P') ? 'checked' : '' }}>
						<label class="form-check-label" for="inlineRadio1">Permanente</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="caracter" id="caracter_transitorio" value="T" {{ (old('caracter', $norma->caracter) == 'T') ? 'checked' : '' }}>
						<label class="form-check-label" for="inlineRadio2">Transitorio</label>
					</div>
				</div>
			</div>

			<div class="form-group row">
				<label for="fec_incluido" class="col-sm-2 col-form-label text-right font-weight-bold">Inclu&iacute;do</label>
				<div class="col-md-4">
					<div class="input-group date" id="fec_incluido" data-target-input="nearest">
						<div class="input-group-append" data-target="#fec_incluido" data-toggle="datetimepicker">
							<div class="input-group-text"><i class="fa fa-calendar"></i></div>
						</div>
						<input type="text" class="form-control datetimepicker-input" name="fec_incluido" data-target="#fec_incluido" value="{{ old('fec_incluido', $norma->fec_incluido) }}"/>
					</div>
				</div>

				<label for="fec_excluido" class="col-sm-2 col-form-label text-right font-weight-bold">Exclu&iacute;do</label>
				<div class="col-md-4">
					<div class="input-group date" id="fec_excluido" data-target-input="nearest">
						<div class="input-group-append" data-target="#fec_excluido" data-toggle="datetimepicker">
							<div class="input-group-text"><i class="fa fa-calendar"></i></div>
						</div>
						<input type="text" class="form-control datetimepicker-input" name="fec_excluido" data-target="#fec_excluido" value="{{ old('fec_excluido', $norma->fec_excluido) }}"/>
					</div>
				</div>
			</div>

			<div class="form-group row">
				<label for="sin_nro" class="col-sm-2 col-form-label text-right font-weight-bold">Sin N&deg;</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="sin_nro" name="sin_nro" value="{{ old('sin_nro', $norma->sin_nro) }}">
				</div>

				<label for="ingresa" class="col-sm-2 col-form-label text-right font-weight-bold">Ingresa</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="ingresa" name="ingresa" value="{{ old('ingresa', $norma->ingresa) }}">
				</div>
			</div>

			<div class="form-group row">
				<label for="aprobado" class="col-sm-2 col-form-label text-right font-weight-bold">Aprobado</label>
				<div class="col-sm-4">
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="aprobado" id="aprobado_unanimidad" value="U" {{ (old('aprobado', $norma->aprobado) == 'U') ? 'checked' : '' }}>
						<label class="form-check-label" for="inlineRadio1">Por unanimidad</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="aprobado" id="aprobado_mayoria" value="M" {{ (old('aprobado', $norma->aprobado) == 'M') ? 'checked' : '' }}>
						<label class="form-check-label" for="inlineRadio2">Por mayor&iacute;a</label>
					</div>
				</div>

				<label for="ausentes" class="col-sm-2 col-form-label text-right font-weight-bold">Ausentes</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="ausentes" name="ausentes" value="{{ old('ausentes', $norma->ausentes) }}">
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
					@if ($norma->id)
					<a class="btn btn-secondary" role="button" href="{{ route('backend.normas.clonar', ['norma' => $norma->id])}}">
						<i class="fa fa-clone" aria-hidden="true"></i> Duplicar
					</a>
					<div class="btn-group">
						<a class="btn btn-secondary" role="button" href="{{ route('normas.show', ['normas_db' => $norma->base, 'norma' => $norma->id]) }}">
							<i class="fa fa-eye" aria-hidden="true"></i> Previsualizar
						</a>
						<button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<span class="sr-only">M&aacute;s...</span>
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="{{ route('backend.normas.show', ['norma' => $norma]) }}">Previsualizar aqu&iacute;</a>
							<a class="dropdown-item" href="{{ route('backend.normas.show', ['norma' => $norma]) }}" target="_blank">Previsualizar en otra ventana</a>
						</div>
					</div>
					@endif
				</div>
			</div>

    	</form>
    </div>
</div>
@endsection