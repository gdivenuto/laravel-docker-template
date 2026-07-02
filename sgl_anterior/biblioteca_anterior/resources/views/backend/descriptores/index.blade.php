@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // ---- Callbacks ---------------------------------------------------------
    var callbackRenderAcciones = function (data, type, row, meta) {
        var buttons = '';
        buttons += '<div class="btn-group" role="group" aria-label="Controles">';

        buttons += `<a role="button" class="btn btn-outline-secondary btn-sm" href="{{ route('backend.descriptores.edit', ['descriptor' => '__DESCRIPTOR_ID__']) }}"><i class="fa fa-pencil" aria-hidden="true"></i></a>`
            .replace('__DESCRIPTOR_ID__', data);

        buttons += `<a role="button" class="btn btn-outline-secondary btn-sm" href="{{ route('backend.descriptores.destroy', ['descriptor' => '__DESCRIPTOR_ID__']) }}"><i class="fa fa-trash" aria-hidden="true"></i></a>`
            .replace('__DESCRIPTOR_ID__', data);

        buttons += '</div>';
        return buttons;
    };

    var callbackRenderId = function (data, type, row, meta) {
        return `${row.id}`;
    };

    var callbackRenderCreatedAt = function (data, type, row, meta) {
        return (data) ? moment(data).format('DD/MM/YYYY, HH:mm')+' hs' : '';
    };

    var callbackRenderUpdatedAt = function (data, type, row, meta) {
        return (data) ? moment(data).format('DD/MM/YYYY, HH:mm')+' hs' : '';
    };

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
                url: '{{ route('backend.descriptores.dtgetdescriptoresjson') }}'
            },
            columnDefs: [
              { targets: [0], orderable: false, searchable: false },
              { targets: [1,2], orderable: true, searchable: true },
              { targets: [3,4], orderable: true, searchable: false }
            ],
            columns: [
                { data: 'id', name: 'id', width:'5%', render: callbackRenderAcciones },
                { data: 'id', name: 'id', width:'7%',  render: callbackRenderId },
                { data: 'tag', name: 'tag', width:'40%' },
                { data: 'created_at', name: 'created_at', width:'14%', render: callbackRenderCreatedAt },
                { data: 'updated_at', name: 'updated_at', width:'14%', render: callbackRenderUpdatedAt },
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
                Descriptores
            </p>
        </div>
    </div>
    
    @include('backend.layouts.errors')

    <div class="table-responsive-sm mt-2">
        <table id="datagrid" class="table table-striped table-sm table-hover">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>Id</th>
                    <th>Tag</th>
                    <th>Fecha Desde</th>
                    <th>Fecha Hasta</th>
                </tr>
            </thead>
            <tbody class="small">
            </tbody>
        </table>
    </div>
</div>
@endsection