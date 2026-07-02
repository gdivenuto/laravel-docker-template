@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // ---- Callbacks ---------------------------------------------------------
    var callbackRenderAcciones = function (data, type, row, meta) {
        var buttons = '';
        buttons += '<div class="btn-group" role="group" aria-label="Controles">';
        buttons += `<a role="button" class="btn btn-outline-secondary btn-sm" href="{{ route('backend.normas.show', ['norma' => '__NORMA_ID__']) }}"><i class="fa fa-eye" aria-hidden="true"></i></a>`
            .replace('__NORMA_ID__', data);
        buttons += `<a role="button" class="btn btn-outline-secondary btn-sm" href="{{ route('backend.normas.edit', ['norma' => '__NORMA_ID__']) }}"><i class="fa fa-pencil" aria-hidden="true"></i></a>`
            .replace('__NORMA_ID__', data);
        buttons += '</div>';
        return buttons;
    };

    var callbackRenderId = function (data, type, row, meta) {
        return `${row.id}`;
    };

    var callbackRenderNroHcd = function (data, type, row, meta) {
        return (row.nro_hcd == '') ? '(no posee)' : row.nro_hcd;
    };

    var callbackRenderActo = function (data, type, row, meta) {
        return `${row.acto_desc} ${row.nro}`;
    };

    var callbackRenderExped = function (data, type, row, meta) {
        return (row.exped == '') ? '(no posee)' : row.exped;
    };

    var callbackRenderUsuario = function (data, type, row, meta) {
        return (row.usuario_codigo == '') ? '' : row.usuario_codigo;
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
                // Se deshabilita el buscador
                "<'row'<'col-sm-12 col-md-3'f><'col-sm-12 col-md-3'l><'col-sm-12 col-md-6'p>>" +
                //"<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'f><'col-sm-12 col-md-6'p>>" +
                //"<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-3'><'col-sm-12 col-md-6'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6'p>>",
            ajax: {
                url: '{{ route('backend.normas.dtgetnormasjson') }}'
            },
            //columnDefs: [
            //   { targets: [0, 2], orderable: true, searchable: false },
            //   { targets: [1, 3], orderable: false, searchable: false }
            //],
            columnDefs: [
              { targets: [0,3], orderable: false, searchable: false },
              { targets: [2,4,6,7,8], orderable: true, searchable: false },
              { targets: [1,5], orderable: true, searchable: true }
            ],
            columns: [
                { data: 'id',         name: 'id',                      render: callbackRenderAcciones },
                { data: 'id',         name: 'id',         width:'7%',  render: callbackRenderId },
                { data: 'nro_hcd',    name: 'nro_hcd',    width:'10%', render: callbackRenderNroHcd },
                { data: 'acto_desc',  name: 'acto_desc',  width:'20%', render: callbackRenderActo },
                { data: 'exped',      name: 'exped',      width:'15%', render: callbackRenderExped },
                { data: 'base',       name: 'base',       width:'7%'},
                { data: 'usuario_codigo', name: 'usuario_codigo', width:'7%', render: callbackRenderUsuario },
                { data: 'created_at', name: 'created_at', width:'14%', render: callbackRenderCreatedAt },
                { data: 'updated_at', name: 'updated_at', width:'14%', render: callbackRenderUpdatedAt },
            ],
            pageLength: {{ config('app.backend_tableResultPerPage') }},
            //order: [[1, 'desc']]
            order: [1, 'desc']
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
                Normas
            </p>
        </div>
    </div>
    
    <div class="table-responsive-sm mt-2">
        <table id="datagrid" class="table table-striped table-sm table-hover">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th class="">Id</th>
                    <th class="">Nro. Interno</th>
                    <th class="">Acto</th>
                    <th class="">Expediente DE</th>
                    <th class="">Base</th>
                    <th class="">Usuario</th>
                    <th class="">Creada el</th>
                    <th class="">Modificada el</th>
                </tr>
            </thead>
            <tbody class="small">
            </tbody>
        </table>
    </div>

</div>
@endsection