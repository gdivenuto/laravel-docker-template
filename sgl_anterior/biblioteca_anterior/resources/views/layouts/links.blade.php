@php
  $base_url = App\Helpers\IntranetRsc::forceHttps(config('params.hcdBaseUrl'));
  $migrate_data = (Storage::disk('local')->exists('migrate_data.txt'))
    ? Storage::disk('local')->get('migrate_data.txt')
    : '?';
@endphp

<hr/>

<ul class="d-none d-sm-flex nav nav-pills nav-justified mb-0 mt-0">
    <a class="nav-item nav-link" href="{{ $base_url }}/biblioteca/novedadesnormativas">Novedades Normativas</a>
    <a class="nav-item nav-link" href="{{ $base_url }}/biblioteca/legislacion/">Normas Generales Recomendadas</a>
    <a class="nav-item nav-link" href="http://catalogodelibros.mardelplata.gov.ar/" target="_blank">Base Bibliogr&aacute;fica del Sistema Municipal de Bibliotecas P&uacute;blicas</a>
    <a class="nav-item nav-link" href="{{ Storage::url('digesto.txt') }}" target="_blank" download>Descargar Digesto Referencial</a>
    <a class="nav-item nav-link" href="{{ route('normas.normasvenc') }}" target="_blank">Vencimientos</a>
    <a class="nav-item nav-link" href="http://www.mardelplata.gob.ar/Contenido/boletin-municipal" target="_blank">&nbsp;Bolet&iacute;n Municipal</a>
</ul>

<div class="d-block d-sm-none container-fluid">
    <div class="row">
        <div class="col-12 mb-1">
            <a class="btn btn-sm btn-primary btn-lg btn-block" href="{{ $base_url }}/biblioteca/novedadesnormativas">Novedades Normativas</a>
        </div>
        <div class="col-12 mb-1">
            <a class="btn btn-sm btn-primary btn-lg btn-block" href="{{ $base_url }}/biblioteca/legislacion/">Normas Generales Recomendadas</a>
        </div>
        <div class="col-12 mb-1">
            <a class="btn btn-sm btn-primary btn-lg btn-block" href="http://catalogodelibros.mardelplata.gov.ar/" target="_blank">Base Bibliogr&aacute;fica del Sistema Municipal de Bibliotecas P&uacute;blicas</a>
        </div>
        <div class="col-12 mb-1">
            <a class="btn btn-sm btn-primary btn-lg btn-block" href="{{ $base_url }}/biblioteca/legislacion/digesto.txt" target="_blank" download>Descargar Digesto Referencial</a>
        </div>
        <div class="col-12 mb-1">
            <a class="btn btn-sm btn-primary btn-lg btn-block" href="{{ route('normas.normasvenc') }}" target="_blank">Vencimientos</a>
        </div>
        <div class="col-12 mb-1">
            <a class="btn btn-sm btn-primary btn-lg btn-block" href="http://www.mardelplata.gob.ar/Contenido/boletin-municipal" target="_blank">&nbsp;Bolet&iacute;n Municipal</a>
        </div>
    </div>
</div>

<hr/>

<div class="container-fluid">
    <div class="row">
        <div class="col-6">
            Ayuda en l&iacute;nea a traves del siguiente <a href="{{ $base_url }}/participacion/contacto_biblioteca.php">formulario</a> de lunes a viernes de 9 a 14Hs.
        </div>
        <div class="col-6 text-right">
            &Uacute;ltima actualizaci&oacute;n de la Base de Datos: {{ $migrate_data." GMT-3" }}
        </div>
    </div>
</div>