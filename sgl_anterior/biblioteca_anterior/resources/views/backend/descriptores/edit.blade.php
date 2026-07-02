@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
	// ------------------------------------------------------------------------
	// jQuery Document Ready Function -----------------------------------------
	// ------------------------------------------------------------------------
	$(function () {
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
                <a href="{{ route('backend.descriptores.index') }}">Descriptores</a> &gt; 
				@if ($descriptor->id)
					Editar ID <i>{{ $descriptor->id }}</i>
				@else
					Nuevo descriptor
				@endif                
            </p>
        </div>
    </div>
</div>

<div class="container-fluid">

	@if ($descriptor->id)
		<form id="dataForm" action="{{ route('backend.descriptores.update', [ 'descriptor' => $descriptor->id ]) }}" method="POST">
		@method('put')
	@else
		<form id="dataForm" action="{{ route('backend.descriptores.store') }}" method="POST">
	@endif
			@include('backend.layouts.errors')
			
			@csrf

			<div class="form-group row">
				<label for="tag" class="col-sm-2 col-form-label text-right font-weight-bold">Nombre (Tag)</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="tag" name="tag" value="{{ old('tag', $descriptor->tag) }}">
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-sm-12">
					<a class="btn btn-sm btn-secondary" role="button" href="{{ route('backend.descriptores.index') }}">
						<i class="fa fa-arrow-left" aria-hidden="true"></i> Volver
					</a>
					<button type="button" class="btn btn-sm btn-primary btn-guardar">
						<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
					</button>
				</div>
			</div>
    	</form>
    </div>
</div>
@endsection