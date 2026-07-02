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
		setDatetimepickerDefault('#fec_desde', '{{ old('fec_desde', $intendencia->fec_desde) }}');
		setDatetimepickerDefault('#fec_hasta', '{{ old('fec_hasta', $intendencia->fec_hasta) }}');

		// Botones
		$('.btn-guardar').click(function (e) {
			$('#dataForm').submit();
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
                <a href="{{ route('backend.intendencias.index') }}">Intendencias</a> &gt; 
				@if ($intendencia->id)
					Editar ID <i>{{ $intendencia->id }}</i>
				@else
					Nueva intendencia
				@endif                
            </p>
        </div>
    </div>
</div>

<div class="container-fluid">

	@if ($intendencia->id)
		<form id="dataForm" action="{{ route('backend.intendencias.update', [ 'intendencia' => $intendencia->id ]) }}" method="POST">
		@method('put')
	@else
		<form id="dataForm" action="{{ route('backend.intendencias.store') }}" method="POST">
	@endif
			@include('backend.layouts.errors')
			
			@csrf

			<div class="row mb-3">
				<div class="col-sm-12">
					<a class="btn btn-secondary" role="button" href="{{ route('backend.intendencias.index') }}">
						<i class="fa fa-arrow-left" aria-hidden="true"></i> Volver
					</a>
					<button type="button" class="btn btn-primary btn-guardar">
						<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
					</button>
				</div>
			</div>

			<div class="form-group row">
				<label for="intendente" class="col-sm-2 col-form-label text-right font-weight-bold">Intendente</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="intendente" name="intendente" value="{{ old('intendente', $intendencia->intendente) }}">
				</div>

				<label for="nro" class="col-sm-2 col-form-label text-right font-weight-bold">Nº de Mandato</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="nro" name="nro" value="{{ old('nro', $intendencia->nro) }}">
				</div>
			</div>

			<div class="form-group row">
				<label for="fec_desde" class="col-sm-2 col-form-label text-right font-weight-bold">Inicio de Mandato</label>
				<div class="col-md-4">
					<div class="input-group date" id="fec_desde" data-target-input="nearest">
						<div class="input-group-append" data-target="#fec_desde" data-toggle="datetimepicker">
							<div class="input-group-text"><i class="fa fa-calendar"></i></div>
						</div>
						<input type="text" class="form-control datetimepicker-input" name="fec_desde" data-target="#fec_desde" value="{{ old('fec_desde', $intendencia->fec_desde) }}"/>
					</div>
				</div>

				<label for="fec_hasta" class="col-sm-2 col-form-label text-right font-weight-bold">Fin de Mandato</label>
				<div class="col-md-4">
					<div class="input-group date" id="fec_hasta" data-target-input="nearest">
						<div class="input-group-append" data-target="#fec_hasta" data-toggle="datetimepicker">
							<div class="input-group-text"><i class="fa fa-calendar"></i></div>
						</div>
						<input type="text" class="form-control datetimepicker-input" name="fec_hasta" data-target="#fec_hasta" value="{{ old('fec_hasta', $intendencia->fec_hasta) }}"/>
					</div>
				</div>
			</div>

			<div class="row my-3">
				<div class="col-sm-12">
					<a class="btn btn-secondary" role="button" href="{{ route('backend.intendencias.index') }}">
						<i class="fa fa-arrow-left" aria-hidden="true"></i> Volver
					</a>
					<button type="button" class="btn btn-primary btn-guardar">
						<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
					</button>
				</div>
			</div>

    	</form>
    </div>
</div>
@endsection