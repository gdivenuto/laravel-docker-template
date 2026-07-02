@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
	// Select2 default behavior
    $.fn.select2.defaults.set('theme', 'bootstrap4');

	// ------------------------------------------------------------------------
	// jQuery Document Ready Function -----------------------------------------
	// ------------------------------------------------------------------------
	$(function () {

		// Combo del que se queda
		// ----------------------
		$('#filter_descriptor_que_queda').select2({
            placeholder: "Ingrese nombre del descriptor",
            minimumInputLength: 3,
            closeOnSelect: true,
            ajax: {
                url: '{{ route('backend.descriptores.buscar') }}',
                data: function (params) {
                    return {
                        q: params.term, // el término de búsqueda ingresado
                        base: $('#base').val() // la base seleccionada
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                dataType: 'json',
                delay: 500,
                cache: true
            }
        });
		$('#filter_descriptor_que_queda').on('select2:select', function (e) {
            let id = e.params.data.id;// ID del elemento seleccionado
            $('#id_descriptor_que_queda').val(id);
        });

		// Combo del que se retira
		// --------------------------
		
		$('#filter_descriptor_que_se_va').select2({
            placeholder: "Ingrese nombre del descriptor a retirar",
            minimumInputLength: 3,
            closeOnSelect: true,
            ajax: {
                url: '{{ route('backend.descriptores.buscar') }}',
                data: function (params) {
                    return {
                        q: params.term, // el término de búsqueda ingresado
                        base: $('#base').val() // la base seleccionada
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                dataType: 'json',
                delay: 500,
                cache: true
            }
        });
		$('#filter_descriptor_que_se_va').on('select2:select', function (e) {
            let id = e.params.data.id;// ID del elemento seleccionado
            $('#id_descriptor_que_se_va').val(id);
        });

		$('.btn-reemplazar').click(function (e) {

			// Si no se eligieron
			if ( $('#id_descriptor_que_queda').val() == '' || $('#id_descriptor_que_se_va').val() == '' )
				alert("Debe elegir los descriptores para realizar el reemplazo.");
			// Si son el mismo descriptor
			else if ( $('#id_descriptor_que_queda').val() == $('#id_descriptor_que_se_va').val() )
				alert("Eligió el mismo descriptor, deben ser diferentes.");
			else
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
				Reemplazo de descriptores
            </p>
        </div>
    </div>
</div>

<div class="container-fluid">

	<form id="dataForm" action="{{ route('backend.descriptores.replace') }}" method="POST">

		@include('backend.layouts.errors')
		
		@csrf

		<input type="hidden" id="id_descriptor_que_queda" name="id_descriptor_que_queda">
		<input type="hidden" id="id_descriptor_que_se_va" name="id_descriptor_que_se_va">


		<div class="form-group row">
			<label for="base" class="col-sm-3 col-form-label text-right font-weight-bold">Base</label>
			<div class="col-sm-9">
				<select class="form-control" id="base" name="base">
					<option value="normas">Normas</option>
					<option value="sesiones">Sesiones</option>
					<option value="decretos">Decretos</option>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<label for="filter_descriptor_que_queda" class="col-sm-3 col-form-label text-right font-weight-bold">
				Descriptor que se queda
			</label>
			<div class="col-sm-9">
				<select class="form-control" id="filter_descriptor_que_queda" name="filter_descriptor_que_queda"></select>
			</div>
		</div>
		<div class="form-group row">
			<label for="filter_descriptor_que_se_va" class="col-sm-3 col-form-label text-right font-weight-bold">
				Descriptor que se retira
			</label>
			<div class="col-sm-9">
				<select class="form-control" id="filter_descriptor_que_se_va" name="filter_descriptor_que_se_va"></select>
			</div>
		</div>

		<div class="row mt-3">
			<div class="col-12 text-center">
				<a class="btn btn-sm btn-secondary" role="button" href="{{ route('backend.descriptores.index') }}">
					<i class="fa fa-arrow-left" aria-hidden="true"></i> Volver
				</a>
				<button type="button" class="btn btn-sm btn-primary btn-reemplazar">
					<i class="fa fa-refresh" aria-hidden="true"></i> Reemplazar
				</button>
			</div>
		</div>
	</form>
</div>
@endsection