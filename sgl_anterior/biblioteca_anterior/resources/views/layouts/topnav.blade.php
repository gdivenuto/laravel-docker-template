@php
  $base_url = App\Helpers\IntranetRsc::forceHttps(config('params.hcdBaseUrl'));
  $app_url = App\Helpers\IntranetRsc::forceHttps(config('app.url'));
@endphp
@auth
  @include('backend.layouts.topnav')
@endauth
<nav id="menu_home" class="{{ (Auth::check()) ? 'top-padding' : '' }} navbar navbar-expand-lg navbar-light d-flex justify-content-around">
    <a class="navbar-brand" href="{{ $base_url }}">
        <img id="cabecera_logo" src="{{ $base_url }}/imagenes/logo.png" width="215" height="100" alt="Logo H.C.D.">
    </a>
    <a class="navbar-brand d-inline d-sm-none" href="{{ $base_url }}/webmail/" target="_blank" title="Acceso a Webmail">
        <i class="fa fa-envelope-o"></i>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item dropdown active">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            INSTITUCIONAL<span class="sr-only">(current)</span>
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ $base_url }}/institucional/">Autoridades</a>
            <a class="dropdown-item" href="{{ $base_url }}/institucional/concejales.php">Cuerpo</a>
            <a class="dropdown-item" href="{{ $base_url }}/institucional/listado_bloques.php">Bloques</a>
            <a class="dropdown-item" href="{{ $base_url }}/institucional/personal/">N&oacute;mina de Personal</a>
            <a class="dropdown-item" href="{{ $base_url }}/contacto.php">Correos y Tel&eacute;fonos</a>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            LEGISLATIVA
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ $base_url }}/legislacion/orden_sesion/">Orden del D&iacute;a Sesi&oacute;n</a>
            <a class="dropdown-item" href="{{ $base_url }}/legislacion/orden_comision/">Orden del D&iacute;a Comisiones</a>
            <a class="dropdown-item" href="{{ $base_url }}/institucional/comisiones.php">Comisiones Internas</a>
            <a class="dropdown-item" href="{{ $base_url }}/legislacion/de_la_memoria/">Comisi&oacute;n de la Memoria</a>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            PRENSA
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ $base_url }}/prensa/">Prensa y Ceremonial</a>
            <a class="dropdown-item" href="{{ $base_url }}/prensa/actividades/">Actividades Protocolares</a>
            <a class="dropdown-item" href="{{ $base_url }}/prensa/gacetillas/">Gacetillas</a>
            <a class="dropdown-item" href="{{ $base_url }}/lists/" target="_blank">Suscripci&oacute;n a Gacetillas</a>
            <a class="dropdown-item" href="{{ $base_url }}/links/medios_locales.php">Medios Locales</a>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            BIBLIOTECA
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ $base_url }}/biblioteca/">Nuestra Biblioteca</a>
            <a class="dropdown-item" href="{{ $base_url }}/biblioteca/historia.php">Historia de la Biblioteca</a>
            <a class="dropdown-item" href="{{ $base_url }}/historia/">Historia del H.C.D.</a>
            <a class="dropdown-item" href="{{ $base_url }}/biblioteca/novedadesnormativas/">Novedades Normativas</a>
            <a class="dropdown-item" href="{{ $base_url }}/biblioteca/normativacovid19/">Normativa COVID-19</a>
            <a class="dropdown-item" href="{{ $base_url }}/biblioteca/legislacion/">Legislaci&oacute;n Nacional, Provincial y Municipal</a>
            <a class="dropdown-item" href="{{ $base_url }}/biblioteca/legislacion/REGLAMENTO INTERNO DEL CONCEJO DELIBERANTE.pdf" target="_blank">Reglamento Interno</a>
            <a class="dropdown-item" href="{{ $base_url }}/biblioteca/legislacion/LEY ORGANICA DE LAS MUNICIPALIDADES.pdf" target="_blank">Ley Org&aacute;nica de las Municipalidades</a>
            <a class="dropdown-item" href="{{ $app_url }}">Ordenanzas: Base de Normas Municipales</a>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            ACTAS DE SESIONES
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ $base_url }}/legislacion/actas/">Per&iacute;odos Legislativos</a>
            <a class="dropdown-item" href="{{ $base_url }}/legislacion/bancas/">Banca 25</a>
            <a class="dropdown-item" href="{{ $base_url }}/legislacion/actas_especiales/">Actividad Legislativa y Protocolar</a>
            <a class="dropdown-item" href="{{ $base_url }}/estudiantil.php">Escuelas al Concejo</a>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            EXPEDIENTES
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ $base_url }}/expedientes/index.php">Consulta de Expedientes</a>
            <a class="dropdown-item" href="{{ $base_url }}/expedientes/presentados.php">&Uacute;ltimos Proyectos Presentados</a>
            <a class="dropdown-item" href="{{ $base_url }}/sgl/" target="_blank">Usuarios Registrados</a>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ $base_url }}/contacto.php">CONTACTO</a>
        </li>
      </ul>
    </div>
    <a class="navbar-brand d-none d-sm-inline" href="{{ $base_url }}/webmail/" target="_blank" title="Acceso a Webmail">
        <i class="fa fa-envelope-o"></i>
    </a>
</nav>