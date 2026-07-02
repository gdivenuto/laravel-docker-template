@extends('layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // Select2 default behavior
    $.fn.select2.defaults.set('theme', 'bootstrap4');

    // Globals 
    var link_target = '';

    // Delegates --------------------------------------------------------------
    var buildCriteriaText = function (criteria) {
        $('#criteria_desc').empty();
    };

    var doSearch = function () {
        var criteria_args = $('#criteria_args').val();
        var criteria_usetextop = $('#use_text_operators').is(':checked');

    	if (criteria_args.trim() != '') {
	   		$.ajax({
	            method: 'POST',
	            url: '{{ route('normas.getnormascontentsearchjson') }}',
	            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')	},
	            data: JSON.stringify({params: {criteria: criteria_args.trim(), usetextop: criteria_usetextop}}),
	            contentType: 'application/json'
	        })
	        .done(function(response, textStatus, jqXHR) {
	            if (response.status == 'OK') {
	                $('#cant_resultados').html(`Se encontraron ${response.data.data.length} ocurrencia(s).`);
                    $('#resultados').empty();
                    buildCriteriaText(response.data.criteria);
                    $.each(response.data.data, function (i, v) {
                        var desc_badges = $.map(v.descriptores, function (v, i) {
                            return `<a href="{{ route('normas.searchsimple', [ 'descriptor_id' => '_descid_']) }}" class="badge badge-secondary" ${link_target}>${v.tag}</a>`.replace('_descid_', v.id);
                        });
                        
                        $('#resultados').append($(`<li class="list-group-item"><strong>${v.acto_desc}</strong> ${v.nro} <strong>Fecha Sanc.:</strong> ${v.fec_sancion ?? '-'} <strong>Fecha Prom.:</strong> ${v.fec_promulga ?? '-'} <a href="{{ route('normas.show', ['norma' => '_norma_']) }}" ${link_target}>[Ampliar]</a> <br/> <strong>Contenido:</strong> ${v.contenido} <br/> ${desc_badges.join(' ')} </li>`.replace('_norma_', v.id)));
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
        $('#criteria_desc').append(`<li><strong>Criterio de Búsqueda:</strong> ${criteria}</li>`);
    };

    var restoreSearchParams = function (search_params) {
		// Restore previous search parameters
        if (search_params) {
            // If I have a criteria, do the search
            if (search_params.criteria.trim() != '') {
                $('#criteria_args').val(search_params.criteria);
                $('#criteria_usetextop').prop('checked', search_params.usetextop); 
                doSearch();
            }
        }    	
    };

    @include('normas.devicedetect')

    // jQuery Document Ready --------------------------------------------------
    $(function () {
        // Device detection
        link_target = (detectDevice().device == 'computer') ? 'target="_blank"' : '';

        // Set moment locale
        moment.locale('{!! \App::getLocale() !!}');

        // Button behaviour
        $('#btn_submit').click(function(e) {
            e.preventDefault();
            doSearch();
        });

        // Restore previous search
        restoreSearchParams(@json(session('params_contentsearch')));
    });
</script>
@endsection

@section('content')

<div class="container-fluid">
    <h3>Normas - B&uacute;squeda Por Contenido</h3>
    <form class="mb-3" id="search_form">
    	<div class="form-group">
            <label for="criteria_args">Criterio de B&uacute;squeda</label>
    		<input class="form-control" id="criteria_args" name="criteria_args" placeholder="Ingrese los t&eacute;rminos de b&uacute;squeda separados por espacios."/>
        </div>
        <div class="form-group form-check">
            <input class="form-check-input" type="checkbox" value="" id="use_text_operators">
            <label class="form-check-label" for="use_text_operators">Utilizar operadores</label>
            <small id="use_text_operatorsHelpBlock" class="form-text text-muted">
                Si marca esta casilla, podrá utilizar operadores en los términos de búsqueda. Para mas referencia:<br/>
                <a href="https://dev.mysql.com/doc/refman/5.6/en/fulltext-boolean.html">https://dev.mysql.com/doc/refman/5.6/en/fulltext-boolean.html</a>
            </small>
        </div>
    	<button type="submit" class="btn btn-primary" id="btn_submit">Consultar</button>
    </form>

    <div class="row">
        <div class="col-md-12">
        	<ul id="criteria_desc"></ul>
            <h6 id="cant_resultados"></h6>
            <ol class="list-group" id="resultados"></ol>
        </div>
    </div>
</div>
@endsection