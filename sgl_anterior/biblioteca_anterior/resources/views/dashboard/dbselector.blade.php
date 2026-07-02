@extends('layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
	// Globals ----------------------------------------------------------------
	var basePorActo = @json(FakeAttrib::tipoActoBase());

	// Delegates --------------------------------------------------------------
	var fillNormaTipoSelect = function () {
		$.each(basePorActo, function (idx, item) {
		    $('#norma_tipo').append($('<option>', { 
		        value: idx,
		        text : item.desc
		    }));
		});
	};

	var onChangeNormaTipoSelect = function () {
		var new_value = $(this).val();
		if (new_value) {
			if (basePorActo[new_value].year)
				$('#norma_ano').show();
			else {
				$('#norma_ano').val('');
				$('#norma_ano').hide();
			}
			$('#btn_buscar_norma').prop('disabled', false);
		} else {
			$('#btn_buscar_norma').prop('disabled', true);
			$('#norma_ano').hide();
		}
	};

	var doSearchNorma = function () {
		var error = '';
		var norma_base = basePorActo[$('#norma_tipo').val()];
		var norma_nro = $('#norma_nro').val().trim();
		var norma_ano = $('#norma_ano').val().trim();

		$('#buscar_error').addClass('d-none');

		if (! /^[0-9]{1,10}$/.test(norma_nro) ) 
			error += 'Ingrese de uno a diez dígitos para el número, sin puntos ni guiones.<br/>';
		
		if (norma_base.year) {
			if (! /^([0-9]{2}|[0-9]{4})$/.test(norma_ano))
				error += 'Ingrese de dos o cuatro dígitos para el año, sin puntos ni guiones.<br/>';
		}

		if (error == '') {
			var norma_fullnro = (norma_base.year) ? norma_nro+'-'+norma_ano.slice(-2) : norma_nro;
	        $.ajax({
	            method: 'GET',
	            url: `{{ route('normas.getbyactonumerojson', [ 'normas_db' => '_norma_db_', 'acto' => '_acto_', 'nro' => '_nro_' ]) }}`
	                .replace('_norma_db_', norma_base.base)
	                .replace('_acto_', $('#norma_tipo').val())
	                .replace('_nro_', norma_fullnro),
	            contentType: 'application/json'
	        })
	        .done(function(response, textStatus, jqXHR) {
	            if (response.status == 'OK') {
	                var href_location = `{{ route('normas.show', [ 'normas_db' => '_norma_db_', 'norma' => '_id_']) }}`
		                .replace('_norma_db_', response.data.base)
		                .replace('_id_', response.data.id);
	            	if (detectDevice().device == 'computer')
	            		window.open(href_location);
	            	else
	            		window.location.href = href_location;
	            } else {
	            	if (response.status == 'WARNING')
	            		$('#buscar_error').removeClass('alert-danger d-none').addClass('alert-secondary');
	            	else
						$('#buscar_error').removeClass('alert-secondary d-none').addClass('alert-danger');
	            	$('#buscar_error').html(response.message);
	            }
	        })
	        .fail(function( jqXHR, textStatus, errorThrown ) {
	            alert(`Ha ocurrido un error al buscar: ${errorThrown}`);
	        });
		} else {
			$('#buscar_error').removeClass('alert-secondary d-none').addClass('alert-danger');
	        $('#buscar_error').html(error);
		}
	};

	@include('normas.devicedetect')

    // jQuery Document Ready --------------------------------------------------
    $(function () {
        // Set moment locale
        moment.locale('{!! \App::getLocale() !!}');

        // Setup options
        fillNormaTipoSelect();

        // Setup controls
        $('#norma_tipo').change(onChangeNormaTipoSelect);
        $('#norma_tipo').trigger('change');
		$('#norma_nro').keypress(function (e) { 
        	if (e.which == 13) {
        		$('#btn_buscar_norma').trigger('click');
        		return false;
        	}
        });
        $('#btn_buscar_norma').click(doSearchNorma);

		$('#btn_buscar_tema').click(function (e) { 
			if ($('#criteria_args').val().trim() != '')
				$('#search_form').submit(); 
		});
        $('#criteria_args').keypress(function (e) { 
        	if (e.which == 13) {
        		$('#btn_buscar_tema').trigger('click');
        		return false;
        	}
        });

    });	
</script>
@endsection

@section('content')
<div class="container-fluid">
	<h2>Buscar una Norma</h2>
	<div class="row mt-2 mb-3">
		<div class="col-sm-6">
	        <div class="form-group">
				<div class="input-group">
					<select class="form-control" id="norma_tipo">
						<option value="-" selected disabled>Tipo</option>
 					</select>
					<input type="text" class="form-control" id="norma_nro" placeholder="Número">
					<input type="text" class="form-control" id="norma_ano" placeholder="Año">
					<button class="btn btn-primary" id="btn_buscar_norma">Buscar <i class="fa fa-search" aria-hidden="true"></i></button>
				</div>
				<div id="buscar_error" class="alert d-none mt-1" role="alert"></div>
	            <small id="busquedaNormaHelpBlock" class="form-text text-muted">
	                Si ya conoce el tipo y n&uacute;mero de acto que desea consultar, puede realizar una b&uacute;squeda directa. Sino acceda una base de datos espec&iacute;fica para una búsqueda con m&aacute;s criterios de selecci&oacute;n.
	            </small>
	        </div>
		</div>
	</div>

	<h2>Buscar por Tema</h2>
	<form class="row mb-3" id="search_form" method="POST" action="{{ route('normas.searchkeywordredirect') }}">
        @csrf
        <div class="form-group col-md-6">
            <div class="input-group">
	            <input class="form-control" id="criteria_args" name="criteria_args" placeholder="Ingrese palabras clave."/>
		        <button type="button" class="btn btn-primary" id="btn_buscar_tema">Buscar <i class="fa fa-search" aria-hidden="true"></i></button>
            </div>
			@error('criteria_args')
				<div id="criteria_argsErrorBlock" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <p class="mb-0">Ha ingresado términos de búsqueda inválidos; por favor, lea la ayuda a continuación e intente nuevamente.</p>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
				</div>
			@enderror            
            <small id="criteria_argsHelpBlock" class="form-text text-muted">
                Ingrese los t&eacute;rminos de b&uacute;squeda separados por espacios. El orden de los t&eacute;rminos indica como ser&aacute; aplicada la predecencia de la b&uacute;squeda.<br/>
                Para buscar frases completas, encierrelas entre comillas dobles. Por ejemplo: "osse tarifas 2021".<br/>
                Para buscar por fechas, respete el formato AAAA-MM-DD. Por ejemplo: 2021-02-25, o bien 2021-02.
            </small>
        </div>
    </form>

	<h2>Buscar en otras Bases de Datos</h2>
	<div class="row justify-content-md-center mt-2">
		<div class="col-sm mb-2">
			<div class="card h-100">
				<h2 class="card-header">Normas</h2>
				<div class="card-body">
					<h5 class="card-title">Todas las Normas sancionadas vigentes o no, incluye normativa particular y transitoria a partir de 1994.</h5>
				</div>
				<div class="card-footer">
					<a href="{{ route('normas.searchkeyword', ['normas_db' => 'normas']) }}" class="btn btn-primary" role="button">Ir a <strong>Normas</strong> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
				</div>
			</div>
		</div>
		<div class="col-sm mb-2">
			<div class="card h-100">
				<h2 class="card-header">Digesto</h2>
				<div class="card-body">
					<h5 class="card-title">Todas las Normas sancionadas vigentes de carácter general y permanente.</h5>
				</div>
				<div class="card-footer">
					<a href="{{ route('normas.searchkeyword', ['normas_db' => 'digesto']) }}" class="btn btn-primary" role="button">Ir a <strong>Digesto</strong> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
				</div>
			</div>
		</div>
		<div class="col-sm mb-2">
			<div class="card h-100">
				<h2 class="card-header">Sesiones</h2>
				<div class="card-body">
					<h5 class="card-title">Normas y temas del Honorable Cuerpo, por ejemplo, cuestión previa, homenajes y pedidos de informes.</h5>
				</div>
				<div class="card-footer">
					<a href="{{ route('normas.searchkeyword', ['normas_db' => 'sesiones']) }}" class="btn btn-primary" role="button">Ir a <strong>Sesiones</strong> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
				</div>
			</div>
		</div>
		<div class="col-sm mb-2">
			<div class="card h-100">
				<h2 class="card-header">Decretos</h2>
				<div class="card-body">
					<h5 class="card-title">Decretos del Departamento Ejecutivo.</h5>
				</div>
				<div class="card-footer">
					<a href="{{ route('normas.searchkeyword', ['normas_db' => 'decretos']) }}" class="btn btn-primary" role="button">Ir a <strong>Decretos</strong> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
				</div>
			</div>
		</div>
		<div class="col-sm mb-2">
			<div class="card h-100">
				<h2 class="card-header">Todas</h2>
				<div class="card-body">
					<h5 class="card-title">Buscar en toda las bases de datos de la Biblioteca-HCD.</h5>
				</div>
				<div class="card-footer">
					<a href="{{ route('normas.searchkeyword', ['normas_db' => 'todas']) }}" class="btn btn-primary" role="button">Ir a <strong>Todas</strong> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
				</div>
			</div>
		</div>		
	</div>

	<h2 class="mt-3">Digestos Espec&iacute;ficos</h2>
	<div class="row justify-content-md-center mt-2">
	@forelse ($digestos as $digesto)
		@if ($digesto->nombre != 'digesto')
			<div class="col-sm mb-2">
				<div class="card h-100">
					<h2 class="card-header text-capitalize">{{$digesto->nombre}}</h2>
					<div class="card-body">
						<h5 class="card-title descripcion_con_html">
							{!! html_entity_decode($digesto->descripcion) !!}
							@if (config('params.backend_enabled'))
								@if (!$digesto->publicado)
									<br/><span class="badge badge-danger">NO PUBLICADO</span>
								@endif
							@endif
						</h5>
					</div>
					<div class="card-footer">
						<a href="{{ route('normas.searchkeyword', ['normas_db' => $digesto->nombre]) }}" class="btn btn-primary" role="button">Ir a <strong class="text-capitalize">{{$digesto->nombre}}</strong> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
					</div>
				</div>
			</div>
		@endif
	@empty
		<div class="col-md-12">
			<p>En este momento no hay digestos especiales activos.</p>
		</div>
	@endforelse
	</div>
</div>

@endsection