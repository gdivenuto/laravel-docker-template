@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // ---- Callbacks ---------------------------------------------------------
    var callbackRenderAcciones = function (data, type, row, meta) {
        var buttons = '';
        buttons += '<div class="btn-group" role="group" aria-label="Controles">';
        buttons += `<a role="button" class="btn btn-outline-secondary btn-sm" href="{{ route('normas.searchkeyword', ['normas_db' => '__DIGESTO_NOMBRE__']) }}"><i class="fa fa-eye" aria-hidden="true"></i></a>`
            .replace('__DIGESTO_NOMBRE__', row.nombre);
        buttons += `<a role="button" class="btn btn-outline-secondary btn-sm" href="{{ route('backend.digestos.edit', ['digesto' => '__DIGESTO_ID__']) }}"><i class="fa fa-pencil" aria-hidden="true"></i></a>`
            .replace('__DIGESTO_ID__', data);
        buttons += '</div>';
        return buttons;
    };

    var callbackRenderNombre = function (data, type, row, meta) {
        return `<span class="text-capitalize">${data}</span>`;
    }

    var callbackRenderPublicado = function (data, type, row, meta) {
        return `<h5>${(data) ? '<span class="badge badge-success">S&iacute;</span>' : '<span class="badge badge-danger">No</span>'}</h5>`;
    }

    // ------------------------------------------------------------------------
    // jQuery Document Ready Function -----------------------------------------
    // ------------------------------------------------------------------------
    $(function () {
        // Set moment locale
        moment.locale('{!! \App::getLocale() !!}');

        // Datatable config
	    $('#datagrid').DataTable({
            processing: true,
            serverSide: true,
            language: { url: '{{ asset(sprintf("json/datatables.%s.json", \App::getLocale())) }}' },
			dom: 
                "<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-6'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6'p>>",
            ajax: {
                url: '{{ route('backend.digestos.dtgetdigestosjson') }}'
            },
            //columnDefs: [
            //   { targets: [0, 2], orderable: true, searchable: false },
            //   { targets: [1, 3], orderable: false, searchable: false }
            //],
            columns: [
                { data: 'id',          name: 'id',          width:'8%', render: callbackRenderAcciones },
                { data: 'publicado',   name: 'publicado',   width:'8%', render: callbackRenderPublicado },
                { data: 'nombre',      name: 'nombre',      width:'24%', render: callbackRenderNombre },
                { data: 'descripcion', name: 'descripcion', width:'60%'},
            ],
            pageLength: {{ config('app.backend_tableResultPerPage') }},
            order: [[2, 'asc']]
	    });
	});
</script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">    
            <p class="h4">
                <i class="fa fa-home" aria-hidden="true"></i> &gt;
                <a href="{{ route('backend.dashboard.index') }}">Dashboard</a> &gt;
                Digestos
            </p>
        </div>
    </div>
    
    <div class="table-responsive-sm mt-2">
        <table id="datagrid" class="table table-striped table-sm table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Publicado</th>
                    <th>Nombre</th>
                    <th>Descripci&oacute;n</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

</div>
@endsection