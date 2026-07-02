<!-- Pie del sitio web -->
@php
  $base_url = App\Helpers\IntranetRsc::forceHttps(config('params.hcdBaseUrl'));
  $app_url = App\Helpers\IntranetRsc::forceHttps(config('app.url'));
@endphp
<footer class="clearfix mt-3">
    <!-- Enlaces del Pie -->
    <div class="row no-gutters ">
        <div class="col-sm-2 ml-0 ml-sm-2 ml-md-3 ml-lg-5">
          <ul>
            <li>Institucional
                <ul>
                    <li><a href="{{ $base_url }}/institucional/">Autoridades</a></li>
                    <li><a href="{{ $base_url }}/institucional/concejales.php">Cuerpo</a></li>
                    <li><a href="{{ $base_url }}/institucional/comisiones.php">Comisiones Internas</a></li>
                    <li><a href="{{ $base_url }}/institucional/personal/">N&oacute;mina de Personal</a></li>
                    <li><a href="{{ $base_url }}/contacto.php">Correos y Tel&eacute;fonos</a></li>
                    <li><a href="{{ $base_url }}/informegestion2020.pdf?v=20210624_0906" target="_blank">Informe de Gesti&oacute;n 2020</a></li>
                </ul>
            </li>
          </ul>
        </div>
        <div class="col-sm-2">
          <ul>
            <li>Labor Legislativa
                <ul>
                    <li><a href="{{ $base_url }}/legislacion/orden_sesion/">Orden del D&iacute;a Sesi&oacute;n</a></li>
                    <li><a href="{{ $base_url }}/legislacion/orden_comision/">Orden del D&iacute;a Comisiones</a></li>
                    <li><a href="{{ $base_url }}/legislacion/de_la_memoria/">Comisi&oacute;n de la Memoria</a></li>
                </ul>
            </li>
          </ul>
        </div>
        <div class="col-sm-2">
          <ul>
            <li>Prensa
                <ul>
                    <li><a href="{{ $base_url }}/prensa/gacetillas/">Gacetillas</a></li>
                                        <li><a href="http://www.concejomdp.gob.ar/lists/" target="_blank">Suscripci&oacute;n</a></li>
                    <li><a href="{{ $base_url }}/prensa/actividades/">Actividades Protocolares</a></li>
                    <li><a href="{{ $base_url }}/prensa/fotos_hcd/">Fotos de Actos Protocolares</a></li>
                    <li><a href="{{ $base_url }}/galeria/mdp/">Fotos de General Pueyrredon</a></li>
                    <li><a href="{{ $base_url }}/links/medios_locales.php">Medios Locales</a></li>
                </ul>
            </li>
          </ul>
        </div>
        <div class="col-sm-2">
          <ul>
            <li>Biblioteca
                <ul>
                    <li><a href="{{ $base_url }}/biblioteca/misiones.php">Misiones y Funciones</a></li>
                    <li><a href="{{ $app_url }}">Base de Normas Municipales</a></li>
                    <li><a href="{{ $base_url }}/biblioteca/novedadesnormativas/">Novedades Normativas</a></li>
                    <li><a href="{{ $base_url }}/biblioteca/normativacovid19/">Normativa COVID-19</a></li>
                    <li><a href="{{ $base_url }}/biblioteca/legislacion/">Legislaci&oacute;n Nacional Provincial y Municipal</a></li>
                </ul>
            </li>
          </ul>
        </div>
        <div class="col-sm-3">
            <ul>
                <li>Actas de Sesiones
                    <ul>
                        <li><a href="{{ $base_url }}/legislacion/actas/">Per&iacute;odos Legislativos</a></li>
                        <li><a href="{{ $base_url }}/legislacion/bancas/">Banca 25</a></li>
                        <li><a href="{{ $base_url }}/legislacion/actas_especiales/">Actividad Legislativa y Protocolar</a></li>
                        <li><a href="{{ $base_url }}/estudiantil.php">Escuelas al Concejo</a><br></li>
                    <li class="pt-3">
                        <a href="https://www.facebook.com/concejomdp/" target="_blank"><img src="{{ $base_url }}/imagenes/redes/facebook.png" class="" width="25" height="25"></a>&nbsp;&nbsp;&nbsp;
                        <a href="https://twitter.com/@concejomdp" target="_blank"><img src="{{ $base_url }}/imagenes/redes/twitter.png" class="" width="25" height="25"></a>&nbsp;&nbsp;&nbsp;
                        <a href="https://www.instagram.com/concejomdp/" target="_blank"><img src="{{ $base_url }}/imagenes/redes/instagram.png" class="" width="25" height="25"></a>&nbsp;&nbsp;&nbsp;
                        <a href="https://www.youtube.com/user/concejomdp" target="_blank"><img src="{{ $base_url }}/imagenes/redes/youtube.png" class="" width="25" height="25"></a>
                    </li>
                        
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <!-- Copyright -->
    <div class="row">
        <div class="col clearfix mx-auto text-center p-3">
            <a href="{{ $base_url }}/copyright.php">Honorable Concejo Deliberante de General Pueyrredon | Hip&oacute;lito Yrigoyen 1627 2&deg; piso | B7600DOM | Mar del Plata | Buenos Aires | Argentina | +54 223 499 6525</a>
        </div>
    </div>
</footer>