@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // ---- Callbacks ---------------------------------------------------------
    var callbackRenderFecha = function (data, type, row, meta) {
        return (data) 
            ? moment(data).format('DD/MM/YYYY')
            : 'Actualidad';
    }

    var callbackRenderUsuario = function (data, type, row, meta) {
        return row.email.replace('@concejomdp.gov.ar', '');
    }

    var callbackRenderTabla = function (data, type, row, meta) {
        return row.auditable_type.replace('App\\', '');
    }

    var callbackRenderOldValues = function (data, type, row, meta) {
        
        return (row.old_values == '[]') 
            ? '(sin cambios)' 
            : row.old_values
                .replaceAll("&quot;", "'")
                .replaceAll(",", "<br>")
                .replaceAll("{", "")
                .replaceAll("}", "")
                .replaceAll("'", "");
    };

    var callbackRenderNewValues = function (data, type, row, meta) {
        return (row.new_values == '[]') 
            ? '(sin cambios)' 
            : row.new_values
                .replaceAll("&quot;", "'")
                .replaceAll(",", "<br>")
                .replaceAll("{", "")
                .replaceAll("}", "")
                .replaceAll("'", "");
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
                url: '{{ route('backend.audit.dtgetauditjson') }}'
            },
            columns: [
                { data: 'created_at', name: 'created_at', width:'5%', render: callbackRenderFecha },
                { data: 'email', name: 'email', width:'15%', render: callbackRenderUsuario },
                { data: 'event', name: 'event', width:'10%' },
                { data: 'auditable_type', name: 'auditable_type', width:'10%', render: callbackRenderTabla },
                { data: 'auditable_id', name: 'auditable_id', width:'15%' },
                { data: 'old_values', name: 'old_values', width:'20%', render: callbackRenderOldValues },
                { data: 'new_values', name: 'new_values', width:'20%', render: callbackRenderNewValues },
            ],
            pageLength: {{ config('app.backend_tableResultPerPage') }},
            order: [[0, 'desc']]
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
                Auditor&iacute;a
            </p>
        </div>
    </div>
    
    @include('backend.layouts.errors')

    <div class="table-responsive-sm mt-2">
        <table id="datagrid" class="table table-striped table-sm table-hover">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acci&oacute;n</th>
                    <th>Tabla</th>
                    <th>Id afectado</th>
                    <th>Info Previa</th>
                    <th>Info Nueva</th>
                </tr>
            </thead>
            <tbody class="small">
            </tbody>
        </table>
    </div>
</div>
@endsection