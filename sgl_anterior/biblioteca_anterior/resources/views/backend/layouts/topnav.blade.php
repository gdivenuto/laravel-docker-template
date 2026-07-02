<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark navbar-backend">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('backend.dashboard.index') }}">
            <img class="img-responsive rounded-circle" src="{{ asset('img/logo_40x40.jpg') }}" alt="" />&nbsp;&nbsp;{{ config('app.name', 'Biblioteca-HCD') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a id="navbarDropdownNormas" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fa fa-file-text-o" aria-hidden="true"></i> Normas <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownNormas">
                            <a class="dropdown-item" href="{{ route('backend.normas.index') }}"><i class="fa fa-arrow-right" aria-hidden="true"></i> Gestionar</a>
                            <a class="dropdown-item" href="{{ route('backend.normas.create') }}"><i class="fa fa-plus" aria-hidden="true"></i> Nueva</a>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a id="navbarDropdownDigestos" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fa fa-filter" aria-hidden="true"></i> Digestos <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownDigestos">
                            <a class="dropdown-item" href="{{ route('backend.digestos.index') }}"><i class="fa fa-arrow-right" aria-hidden="true"></i> Gestionar</a>
                            <a class="dropdown-item" href="{{ route('backend.digestos.create') }}"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo</a>
                        </div>
                    </li>                    

                    <li class="nav-item dropdown">
                        <a id="navbarDropdownBuscar" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fa fa-search" aria-hidden="true"></i> Buscar <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownBuscar">
                            <a class="dropdown-item" href="{{ route('normas.searchsimple', ['normas_db'=>'todas']) }}"><i class="fa fa-tag" aria-hidden="true"></i> por Descriptor</a>
                            <a class="dropdown-item" href="{{ route('normas.searchkeyword', ['normas_db'=>'todas']) }}"><i class="fa fa-tags" aria-hidden="true"></i> por Palabra Clave</a>
                            <a class="dropdown-item" href="{{ route('normas.search', ['normas_db'=>'todas']) }}"><i class="fa fa-search" aria-hidden="true"></i> Avanzado</a>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a id="navbarDropdownAdministrar" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fa fa-cogs" aria-hidden="true"></i> Administrar <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownAdministrar">
                            <a class="dropdown-item" href="{{ route('backend.intendencias.index') }}"><i class="fa fa-arrow-right" aria-hidden="true"></i> Gestionar Intendencias</a>
                            <a class="dropdown-item" href="{{ route('backend.intendencias.create') }}"><i class="fa fa-plus" aria-hidden="true"></i> Nueva Intendencia</a>
                            <hr>
                            <a class="dropdown-item" href="{{ route('backend.descriptores.index') }}"><i class="fa fa-arrow-right" aria-hidden="true"></i> Gestionar Descriptores</a>
                            <a class="dropdown-item" href="{{ route('backend.descriptores.create') }}"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Descriptor</a>
                            <a class="dropdown-item" href="{{ route('backend.descriptores.choise') }}"><i class="fa fa-refresh" aria-hidden="true"></i> Reemplazar Descriptores</a>
                            <hr>
                            <a class="dropdown-item" href="{{ route('backend.audit.index') }}">
                                <i class="fa fa-eye" aria-hidden="true"></i> Auditor&iacute;a
                            </a>
                            <hr>
                            <a class="dropdown-item" href="{{ route('backend.dashboard.publishconfirm') }}"><i class="fa fa-cloud-upload" aria-hidden="true"></i> Publicar Contenidos</a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard.showdbselector') }}">
                            <i class="fa fa-eye" aria-hidden="true"></i> Previsualizar <span class="caret"></span>
                        </a>
                    </li>

                @endauth
            </ul>
            
            @if (Storage::disk('local')->exists('marca_publicacion.txt'))
                <h2 class="navbar-text my-0">
                    <i class="fa fa-cloud-upload text-danger" aria-hidden="true" title="Publicación de Contenidos en curso"></i>
                </h2>
            @endif

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}"><i class="fa fa-sign-in" aria-hidden="true"></i> {{ __('Login') }}</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdownUser" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fa fa-user" aria-hidden="true"></i> {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownUser">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out" aria-hidden="true"></i> {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
            </div>
    </div>
</nav>