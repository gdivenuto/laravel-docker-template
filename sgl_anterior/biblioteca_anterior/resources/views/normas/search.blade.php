@extends('layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // Select2 default behavior
    $.fn.select2.defaults.set('theme', 'bootstrap4');

    // Globals ----------------------------------------------------------------
    var data_intendencias = @json($intendencias);
    var data_basePorActo = @json(FakeAttrib::tipoActoBaseDinamico($normas_db));
    var link_target = '';

    // Delegates --------------------------------------------------------------
    @include('layouts.paginatorlogic')

    var addCriteria = function () {
    	var new_criteria = $($('#template_row_criteria').html());
    	$('#table_criteria tr:last').before(new_criteria);
    	new_criteria.find('.criteria-type').trigger('change');
    	
    	// si no tengo tipos de acto disponibles, elimino la opción
    	if (data_basePorActo.length == 0) {
    		new_criteria.find('option[value="tipo_acto"]').remove();	
    	}
    };

    var delCriteria = function () {
    	$(this).parents('tr').remove();
    };

    var focusinCriteria = function () {
    	// para salvar el valor anterior
    	$(this).data('prev-value', $(this).val());
    };

    var changeCriteria = function () {
    	var new_value = $(this).val();
    	var input_criteria_container = $(this).parent().siblings('.criteria-input-container');
    	switch (new_value) {
    		case 'has_or':
			case 'has_and':
			case 'has_not':
				// evito recrear el input si vengo de un criterio "compatible"
				if (! ['has_or', 'has_and', 'has_not'].includes($(this).data('prev-value')) ) {
			    	input_criteria_container.empty();
					var input_criteria = $('<select class="form-control criteria-input" multiple="multiple"></select>');
					input_criteria_container.append(input_criteria);
					input_criteria.select2({
			            placeholder: "Ingrese Descriptores",
			            //tags: true, // permito ingreso manual
			            minimumInputLength: 3,
			            closeOnSelect: true,
			            ajax: {
			                url: '{{ route('descriptores.getautocompletedescriptorjson') }}',
			                data: function (p) { return {term: p.term, _type: p._type, q: p.term, normas_db: '{{ $normas_db }}'}; },
			                dataType: 'json',
			                delay: 500,
			                cache: true
			            }
			        });
				}
				break;
			case 'begins':
			case 'ends':
			case 'contains':
			case 'full_content':
				// evito recrear el input si vengo de un criterio "compatible"
				if (! ['begins', 'ends', 'contains', 'full_content'].includes($(this).data('prev-value')) ) {
			    	input_criteria_container.empty();
					var input_criteria = $('<input type="text" class="form-control criteria-input" value="" placeholder="Ingrese criterio de selección"/>');
					input_criteria_container.append(input_criteria);
				}
				break;
			case 'date_sancion':
			case 'date_promulga':
			case 'date_publica ':
				// evito recrear el input si vengo de un criterio "compatible"
				if (! ['date_sancion', 'date_promulga', 'date_publica'].includes($(this).data('prev-value')) ) {
					input_criteria_container.empty();
					var input_criteria = $(
						'<div class="input-group">' +
						'	<input type="text" class="form-control criteria-input" value="" placeholder="Desde la fecha (AAAA-MM-DD)">' +
						'	<input type="text" class="form-control criteria-input" value="" placeholder="Hasta la fecha (AAAA-MM-DD)">' +
						'</div>');
					input_criteria_container.append(input_criteria);
				}
				break;
			case 'date_intendencia':
				// evito recrear el input si vengo del mismo criterio
				if ( $(this).data('prev-value') != 'date_intendencia' ) {
					input_criteria_container.empty();
					var input_criteria = $('<select class="form-control criteria-input"></select>');
					$.each(data_intendencias, function (k, i) {
					    input_criteria.append($('<option>', { value: i.value, text : i.text }));
					});
					input_criteria_container.append(input_criteria);
				}
				break;
			case 'tipo_acto':
				// evito recrear el input si vengo del mismo criterio
				if ( $(this).data('prev-value') != 'tipo_acto' ) {

					input_criteria_container.empty();
					var input_criteria = $('<select class="form-control criteria-input"></select>');
					$.each(data_basePorActo, function (k, i) {
					    input_criteria.append($('<option>', { value: k, text : i }));
					});
					input_criteria_container.append(input_criteria);
				}
				break;
    	};
    	// actualizo el "valor anterior"
    	$(this).data('prev-value', new_value);
    };

    var enableDownloadButtons = function (show) {
        if (show) {
            $('#btn_download_pdf').removeAttr('disabled');
            $('#btn_download_pdf').removeClass('d-none');
        } else {
            $('#btn_download_pdf').attr('disabled', true);
            $('#btn_download_pdf').addClass('d-none');
        }
    };

    var doSearch = function (page = 1) {
    	// filter empty input parameters
    	$('#table_criteria tbody tr.criteria-row').each(function() {
    		if ($(this).find('td .criteria-input').length > 1) {
    			iv_list = $(this).find('td .criteria-input').map(function() { return this.value; }).get().filter(x => x != '');
    			if (iv_list.length == 0) $(this).remove();
    		} else {
    			if ($(this).find('td .criteria-input').val() == '') $(this).remove();
    		}
    	});

    	// generate input parameters
    	var input_parameters = $('#table_criteria tbody tr.criteria-row').map(function() {
    		input_val = ($(this).find('td .criteria-input').length > 1)
    			? $(this).find('td .criteria-input').map(function() { return this.value; }).get()
    			: $(this).find('td .criteria-input').val();
    		return {
    			type: $(this).find('td select.criteria-type').val(),
    			value: input_val
    		};
    	}).get();

    	if (input_parameters.length > 0) {
    		disablePaginator();
    		
	   		$.ajax({
	            method: 'POST',
	            url: `{{ route('normas.getnormasadvsearchjson', ['normas_db' => $normas_db]) }}?page=${page}`,
	            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')	},
	            data: JSON.stringify({params: input_parameters}),
	            contentType: 'application/json'
	        })
	        .done(function(response, textStatus, jqXHR) {
	            if (response.status == 'OK') {
	            	// 'response.data.data.total' me da los resultados sin agrupar, es un bug de eloquent/paginate
                    if (response.data.data.total > 0) {
                        //$('#cant_resultados').html(`Se encontraron ${response.data.data.total} ocurrencia(s).`);
                        $('#cant_resultados').html('');
                    } else {
                        $('#cant_resultados').html(`No se encontraron resultados.`);
                    }
                    $('#resultados').empty();
                    buildCriteriaText(response.data.criteria);
                    renderPaginator(response.data.data);
                    enableDownloadButtons(response.data.data.total > 0);

                    $.each(response.data.data.data, function (i, v) {
                        var desc_badges = $.map(v.descriptores, function (v, i) {
                            return `<a href="{{ route('normas.searchsimple', [ 'normas_db' => $normas_db, 'descriptor_id' => '_descid_']) }}" class="badge badge-secondary" ${link_target}>${v.tag}</a>`.replace('_descid_', v.id);
                        });
                        
						fsancion = (v.fec_sancion) ? moment(v.fec_sancion).format("DD/MM/YYYY") : '(sin fecha)';
                        fpromulga = (v.fec_promulga) ? moment(v.fec_promulga).format("DD/MM/YYYY") : '(sin fecha)';
                        extra_btn = '';
                        
                        @auth
                            extra_btn = `<a href="{{ route('backend.normas.edit', ['norma' => '_norma_'])}}" class="btn btn-sm btn-secondary" role="button" ${link_target}><i class="fa fa-pencil" aria-hidden="true"></i></a> `.replace('_norma_', v.id);
                        @endauth
                        
                        $('#resultados').append($(`<li class="list-group-item"><a href="{{ route('normas.show', [ 'normas_db' => $normas_db, 'norma' => '_norma_']) }}" class="btn btn-sm btn-primary" role="button" ${link_target}><i class="fa fa-plus" aria-hidden="true"></i></a> ${extra_btn}<span class="lead"><span class="badge badge-outline-primary text-capitalize">${v.base_tag}</span></span> <strong>${v.acto_desc}</strong> ${v.nro} <strong>Fecha Sanci&oacute;n:</strong> ${fsancion} <strong>Fecha Promulgaci&oacute;n:</strong> ${fpromulga} <br/> <strong>Contenido:</strong> ${v.contenido} <br/> ${desc_badges.join(' ')} </li>`.replace('_norma_', v.id)));
                    });
	            } else 
	                alert(sprintf('ERROR: %s', response.message));
	        })
	        .fail(function( jqXHR, textStatus, errorThrown ) {
	            alert(`Ha ocurrido un error al buscar: ${errorThrown}`);
	        });
    	} else {
    		alert('Debe ingresar al menos un criterio de búsqueda.');
    	}
    };

    var buildCriteriaText = function (criteria) {
    	$('#criteria_desc').empty();
    	var desc_list = $.map(criteria, function (v, i) {
    		switch (v.type) {
    			case 'has_or': 		  return '<strong>Alguno de los descriptores:</strong> ' + v.value.join(' <strong>ó</strong> ');
				case 'has_and': 	  return '<strong>Todos los descriptores:</strong> ' + v.value.join(' <strong>y</strong> ');
				case 'has_not': 	  return '<strong>Ninguno de los descriptores:</strong> ' + v.value.join(' <strong>ó</strong> ');
    			case 'begins': 		  return '<strong>Alguno de los descriptores que comienzan con:</strong> ' + v.value.join(' <strong>ó</strong> ');
				case 'ends': 		  return '<strong>Alguno de los descriptores que terminan con:</strong> ' + v.value.join(' <strong>ó</strong> ');
				case 'contains': 	  return '<strong>Alguno de los descriptores que contienen:</strong> ' + v.value.join(' <strong>ó</strong> ');
				case 'tipo_acto': 	  return '<strong>Tipo de acto:</strong> ' + v.value.map(function(v) { return data_basePorActo[v]; }).join(' <strong>ó</strong> ');
				case 'date_sancion':  return '<strong>Fecha de sanción:</strong> desde ' + v.value.map(function(v) {return v ?? '??';}).join(' hasta ');
				case 'date_promulga': return '<strong>Fecha de promulgación:</strong> desde ' + v.value.map(function(v) {return v ?? '??';}).join(' hasta ');
				case 'date_publica':  return '<strong>Fecha de publicación:</strong> desde ' + v.value.map(function(v) {return v ?? '??';}).join(' hasta ');
				case 'date_intendencia':  return '<strong>Sancionado durante la Intendencia de:</strong> ' + data_intendencias.find(x => x.value == v.value[0]).text;
				case 'full_content':  return '<strong>Contenido:</strong> ' + v.value.join(' <strong>y</strong> ');
    		}
    	});
    	$.each(desc_list, function (i, v) {
    		$('#criteria_desc').append(`<li>${v}</li>`);
    	});
    };

    var restoreSearchParams = function (search_params) {
		// Restore previous search parameters
        if (search_params) {
      	
        	// Agrego los criterios salvo los descriptores 
        	// (los descriptores se completan mediante una consulta a la API
        	// para obtener las descripciones)
        	$.each(search_params, function (i, v) {
        		if (! ['has_or', 'has_and', 'has_not'].includes(v.type)) {
					$('#add_criteria').trigger('click');
					criteria_selector = $('#table_criteria select.criteria-type:last');
					criteria_selector.val(v.type);
					criteria_selector.trigger('change');
					if (['date_sancion', 'date_promulga', 'date_publica'].includes(v.type)) {
						$('#table_criteria input.criteria-input:last').prev('input').val(v.value[0]);
						$('#table_criteria input.criteria-input:last').val(v.value[1]);
					} else if (v.type == 'date_intendencia') {
						$('#table_criteria select.criteria-input:last').val(v.value).change();
					} else if (v.type == 'tipo_acto') {
						// existe este tipo de acto para esta base?
						if (v.value in data_basePorActo) {
							$('#table_criteria select.criteria-input:last').val(v.value).change();
						} else {
							criteria_selector.parents('tr').remove();
						}
					} else {
						$('#table_criteria input.criteria-input:last').val(v.value);
					}
				}
			});	

        	// Obtengo el tag de los descriptores a partir del id, para regenerar los campos de busqueda
        	descriptor_id_list = [];
        	$.each(search_params, function (i, v) {
        		if (['has_or', 'has_and', 'has_not'].includes(v.type))
        			descriptor_id_list = descriptor_id_list.concat(v.value);
        	});

        	// Filtro para no tener repetidos
        	descriptor_id_list = descriptor_id_list.filter(function (x, i, a) { return a.indexOf(x) === i; });

        	// Utilizo la api para regenerar los descriptores si tengo una lista de id de descriptores
        	if (descriptor_id_list.length > 0) {
	        	// Obtengo descripciones
	        	$.ajax({
		            method: 'POST',
		            url: '{{ route('descriptores.getdescriptorbyidjson') }}',
		            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')	},
		            data: JSON.stringify({id_list: descriptor_id_list}),
		            contentType: 'application/json'
		        })
		        .then(function(response, textStatus, jqXHR ) {
		        	if (response.status == 'OK') {
						$.each(search_params, function (i, v) {
							if (['has_or', 'has_and', 'has_not'].includes(v.type)) {
								$('#add_criteria').trigger('click');
								criteria_selector = $('#table_criteria select.criteria-type:last');
								criteria_selector.val(v.type);
								criteria_selector.trigger('change');
								$.each(v.value, function (idx, desc_id) {
									$('#table_criteria select.criteria-input:last')
										.append(new Option(response.data[desc_id], desc_id, true, true))
										.trigger('change');
								});
							}
						});	        		
		        	} else 
		        		alert(sprintf('ERROR: %s', response.message));
		        });
        	} // if (descriptor_id_list.length > 0) {
        }    	
    };

	@include('normas.devicedetect')

    // jQuery Document Ready --------------------------------------------------
    $(function () {
        // Device detection
        link_target = (detectDevice().device == 'computer') ? 'target="_blank"' : '';

        // Set moment locale
        moment.locale('{!! \App::getLocale() !!}');

        // Setup Paginator
        setupPaginator();

        // Button behaviour
        $('#add_criteria').click(addCriteria);
        $('#table_criteria tbody').on('click', '.btn-del-criteria', delCriteria);
        $('#table_criteria tbody').on('focusin', '.criteria-type', focusinCriteria);
        $('#table_criteria tbody').on('change', '.criteria-type', changeCriteria);
        $('#btn_submit').click(function(e) {
            e.preventDefault();
            doSearch();
        });
        $('#btn_download_pdf').click(function(e) {
            window.location.href = '{{ route('normas.getnormasadvpdf', [ 'normas_db' => $normas_db ]) }}';
        });        
        $('.toggle-show-descriptores').click(function(e) {
            e.preventDefault();
            $('.toggle-show-descriptores').toggleClass('d-none');
            $('#descriptores_short').toggleClass('d-none');
            $('#descriptores_long').toggleClass('d-none');
        });

        // Restore previous search
        restoreSearchParams(@json(session('params_advsearch')));
    });
</script>
@endsection

@section('content')
<template id="template_row_criteria">
	<tr class="criteria-row">
		<td>
			<select class="form-control criteria-type" data-prev-value="---">
				<option value="has_or" selected>Alguno de los descriptores</option>
				<option value="has_and">Todos los descriptores</option>
				<option value="has_not">Ninguno de los descriptores</option>
				<option value="begins">Alguno de los descriptores que comienzan con</option>
				<option value="ends">Alguno de los descriptores que terminan con</option>
				<option value="contains">Alguno de los descriptores que contienen</option>
				<option value="tipo_acto">Tipo de acto</option>
				<option value="date_sancion">Fecha de sanción entre</option>
				<option value="date_promulga">Fecha de promulgación entre</option>
				<option value="date_publica">Fecha de publicación entre</option>
				<option value="date_intendencia">Sancionado durante la Intendencia de</option>
				<option value="full_content">Contenido</option>
			</select>
		</td>
		<td class="criteria-input-container">&nbsp;</td>
		<td><button type="button" class="btn btn-secondary btn-del-criteria"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>
	</tr>
</template>

<div class="container-fluid">

    <div class="row">
        <div class="col">
            <div class="btn-group mb-2">
                <button class="btn btn-outline-secondary btn-lg dropdown-toggle" type="button" id="dropbtn_cambio_db" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Base de Datos: <strong class="text-capitalize">{{ $normas_db }}</strong></button>
                <div class="dropdown-menu" aria-labelledby="dropbtn_cambio_db">
                    @foreach ($bases as $b)
                        @if ($normas_db != $b)
                            <a class="dropdown-item" href="{{ route('normas.searchkeyword', ['normas_db' => $b]) }}">Ir a <strong class="text-capitalize">{{ $b }}</strong> </a>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="btn-group mb-2">
                <button class="btn btn-outline-secondary btn-lg dropdown-toggle" type="button" id="dropbtn_buscar_por" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">B&uacute;squeda: <strong>Avanzada</strong></button>
                <div class="dropdown-menu" aria-labelledby="dropbtn_buscar_por">
                    <a class="dropdown-item" href="{{ route('normas.searchkeyword', [ 'normas_db' => $normas_db]) }}">B&uacute;squeda <strong>Por Palabra Clave</strong></a>
                    <a class="dropdown-item" href="{{ route('normas.searchsimple', [ 'normas_db' => $normas_db]) }}">B&uacute;squeda <strong>Simple por Descriptor</strong></a>
                </div>
            </div>
        </div>
	</div>

    @isset($digesto)
        @if ($digesto->descriptores->count() > 0)
        <div class="row">
            <div class="col mb-3">
                Descriptores aplicados a base de datos ({{ $digesto->descriptores->count() }} descriptor{{ ($digesto->descriptores->count() > 1) ? 'es' : '' }}): <a href="#" class="toggle-show-descriptores badge badge-primary d-none">Ver menos...</a> <br>
                <span id="descriptores_short">
                    @foreach ($digesto->descriptores->take(10) as $d)
                        <a href="{{ route('normas.searchsimple', [ 'normas_db' => $digesto->nombre, 'descriptor_id' => $d->id]) }}" class="badge badge-{{ ($d->pivot->condicion == 'and') ? 'dark' : 'secondary' }}">{{ $d->tag }}</a> 
                    @endforeach
                    @if ($digesto->descriptores->count() > 10)
                        <a href="#" class="toggle-show-descriptores badge badge-primary">Ver todos...</a> 
                    @endif
                </span>
                <span id="descriptores_long" class="d-none">
                    @foreach ($digesto->descriptores as $d)
                        <a href="{{ route('normas.searchsimple', [ 'normas_db' => $digesto->nombre, 'descriptor_id' => $d->id]) }}" class="badge badge-{{ ($d->pivot->condicion == 'and') ? 'dark' : 'secondary' }}">{{ $d->tag }}</a> 
                    @endforeach
                </span>
            </div>
        </div>
        @endif
    @endisset
    
    <form class="mb-3" id="search_form">
    	<div class="row">
    		<div class="col-md-12">
	    		<table class="table table-sm table-responsive-md" id="table_criteria">
	    			<thead>
	    				<tr>
							<th scope="col" style="width: 30%">Criterio</th>
							<th scope="col" style="width: 65%">Argumento</th>
							<th scope="col" style="width: 5%">#</th>
	    				</tr>
	    			</thead>
	    			<tbody>
	    				<tr>
	    					<td colspan="3">
	    						<button type="button" class="btn btn-secondary" id="add_criteria">
	    							<i class="fa fa-plus" aria-hidden="true"></i> Agregar criterio
	    						</button>
	    					</td>
	    				</tr>
	    			</tbody>
	    		</table>
	    	</div>
    	</div>
    	<a href="{{ route('dashboard.showdbselector') }}" class="btn btn-secondary" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> Volver</a>
    	<button type="submit" class="btn btn-primary" id="btn_submit">Consultar</button>
    </form>

    <div class="row">
        <div class="col-md-12">
        	<ul id="criteria_desc"></ul>
            <h6>
                <button type="button" class="btn btn-sm btn-secondary d-none" id="btn_download_pdf" disabled><i class="fa fa-download" aria-hidden="true"></i> Descargar Resultados</button>    
                <span id="cant_resultados"></span>
            </h6>

            @include('layouts.paginator')

            <ol class="list-group" id="resultados"></ol>

            @include('layouts.paginator')
        </div>
    </div>
</div>
@endsection