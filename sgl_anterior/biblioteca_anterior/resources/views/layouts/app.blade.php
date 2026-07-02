<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            @if (config('params.inTestMode'))
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h4 class="alert-heading"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Esto es una demostraci&oacute;n!</h4>
                            <p class="mb-0"><strong>Esta aplicaci&oacute;n es una demostraci&oacute;n</strong> de las diferentes b&uacute;squedas que podr&aacute;n realizarse en la nueva intefaz web de la Biblioteca del Honorable Concejo Deliberante. La informaci&oacute;n aqu&iacute; presentada no garantiza resultados v&aacute;lidos y/o actualizados, y es posible que algunas funcionalidades est&eacute;n incompletas. No es un producto terminado y est&aacute; sujeto a cambios constantes.</p>
                            <p class="mb-0"><strong>Por favor, no utilice esta aplicaci&oacute;n como referencia fidedigna en un &aacute;mbito de trabajo real.</strong> ¡Muchas gracias!</p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @yield('content')
    </main>

    @includeWhen(Request::path() !=  'dashboard' ,'layouts.links')

    @include('layouts.footer')

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('script')

</body>
</html>
