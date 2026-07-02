<!DOCTYPE html>
@php
  $base_url = App\Helpers\IntranetRsc::forceHttps(config('params.hcdBaseUrl'));
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Honorable Concejo Deliberante</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>
    @include('layouts.topnav')
    
    <main class="py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 alert alert-info">
                    <h1>{{ config('app.name', 'Biblioteca-HCD') }}</h1>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="jumbotron">
                        <h2 class="display-4"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Documento no encontrado</h2>
                        <hr>
                        <p class="lead">El documento solicitado no se encuentra disponible.</p>
                        <p>Solicite ayuda en línea a traves del siguiente <a href="{{ $base_url }}/participacion/contacto_biblioteca.php">formulario</a> de lunes a viernes de 9 a 14Hs.</p>
                        <p>Disculpe las molestias. Muchas gracias.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('layouts.footer')

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

</body>
</html>
