<?php
abstract class VistaBase {

	private $controlador;
    
	public function __construct() {
        // ...
	}

	public function mostrarContenidoHead() {?>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	    <title><?=TITULO_SISTEMA;?></title>

    	<link href="<?=URL_IMAGENES;?>favicon.ico?v=<?=date("Ymd_His");?>" rel="shortcut icon" type="image/x-icon">

	    <!-- Librería jQuery -->
	    <script src="<?=URL_JS_LIBRERIAS;?>jquery_3.5.1.js"></script>

        <!-- CSS y JS para el Autosugerido -->
        <link href="<?=URL_CSS;?>jquery-ui.css" rel="stylesheet">
        <script src="<?=URL_JS_LIBRERIAS;?>jquery-ui.js"></script>

		<!-- Librería Popper -->
	    <script src="<?=URL_JS_LIBRERIAS;?>popper_1.16.0.js"></script>

	    <!-- JavaScript original de Bootstrap v4.6 -->
    	<script src="<?=URL_JS_LIBRERIAS;?>bootstrap_4.6.0.js"></script>
        
	    <!-- FontAwesome (íconos) -->
	    <script src="<?=URL_JS_LIBRERIAS;?>fontawesome_5.js"></script>

	    <!-- CSS original de Bootstrap v4.6 -->
    	<link href="<?=URL_CSS;?>bootstrap_4.6.0.css" rel="stylesheet">

		<!-- Libreria JS para el DatePicker -->
	    <script src="<?=URL_JS_LIBRERIAS;?>gijgo_1.9.13.min.js" type="text/javascript"></script>
	    <!-- Traducción al Español para el DatePicker -->
	    <script src="<?=URL_JS_LIBRERIAS;?>gijgo_1.9.13.es-es.js" type="text/javascript"></script>
		<!-- Libreria CSS para el DatePicker -->
		<link href="<?=URL_CSS;?>gijgo_1.9.13.min.css" rel="stylesheet" type="text/css" />

	    <!-- CSS propio del Backend -->
	    <link href="<?=URL_CSS;?>propio.css?v=<?=date("Ymd_His");?>" rel="stylesheet">
		<!-- JS propio del Backend -->
		<script src="<?=URL_JS;?>propio.js?v=<?=date("Ymd_His");?>"></script>

		<script>
            // Para prevenir el clic de cierre dentro del menú desplegable
            $(document).on('click', '.dropdown-menu', function (e) {
                e.stopPropagation();
            });

            // Para convertir en acordeón el menú desplegable, en dispositivos de ancho menor a 992
            if ($(window).width() < 992) {
                $('.dropdown-menu a').click(function(e){
                    e.preventDefault();
                    if ($(this).next('.submenu').length){
                        $(this).next('.submenu').toggle();
                    }
                    $('.dropdown').on('hide.bs.dropdown', function () {
                        $(this).find('.submenu').hide();
                    });
                });
            }
        </script>
		<?php }

	/**
	 * Se muestra el Menu Principal del Backend
	 */
	public function mostrarMenuPrincipal() {
		?>
		<!-- Menú de Navegación -->
		<nav id="menu_home" class="navbar navbar-expand-lg navbar-light d-flex justify-content-around">

		    <a class="navbar-brand">
		        <img id="cabecera_logo" src="<?=URL_IMAGENES;?>logo_hcd.png" class="rounded-circle" height="60" alt="<?=TITULO_SISTEMA;?>">
		    </a>

		    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menu_superior" aria-controls="menu_superior" aria-expanded="false" aria-label="Toggle navigation">
		        <span class="navbar-toggler-icon"></span>
		    </button>

		    <div id="menu_superior" class="collapse navbar-collapse">

		        <ul class="navbar-nav mx-auto w-100">

					<?php // Sólo quien posea Acceso a Informática
		            if ($_SESSION['perfil1'] == PERFIL_AREA_INFORMATICA) {?>
			            <li class="nav-item dropdown active mr-0 mr-md-1">
			              	<a id="item_informatica" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			                	INFORM&Aacute;TICA<span class="sr-only">(actual)</span>
			              	</a>
			              	<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li class="contenedor_submenu">
                                	<a class="dropdown-item" href="#">
                                		<i class="fas fa-eye"></i>&nbsp;Auditor&iacute;as&nbsp;
                                	</a>
                                    <ul class="submenu dropdown-menu">
                                        <li>
                                            <a  class="dropdown-item"
                                                href="<?=URL_ABMS;?>?controlador=auditoria_administracion&accion=listar">
                                                Auditor&iacute;a Administraci&oacute;n
                                            </a>
                                        </li>
                                        <li>
                                            <a  class="dropdown-item"
                                                href="<?=URL_ABMS;?>?controlador=auditoria_defensoria&accion=listar">
                                                Auditor&iacute;a Defensor&iacute;a
                                            </a>
                                        </li>
                                        <li>
                                            <a  class="dropdown-item"
                                                href="<?=URL_ABMS;?>?controlador=auditoria_expedientes&accion=listar">
                                                Auditor&iacute;a Expedientes
                                            </a>
                                        </li>
                                        <li>
                                            <a  class="dropdown-item"
                                                href="<?=URL_ABMS;?>?controlador=auditoria_personal&accion=listar">
                                                Auditor&iacute;a Personal
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                	<a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=usuarios&accion=listar">
                                		<i class="fas fa-users"></i>&nbsp;Usuarios
                                	</a>
                                </li>
                                <li>
                                    <a  class="dropdown-item <?=(is_file(RUTA_FLAG_PROCESAMIENTO . NOMBRE_FLAG_EXPORT_INVENTARIO)) ? 'disabled' : '';?>"
                                        href="<?=URL_ABMS;?>?controlador=forzar_export_inventario&accion=generar">
                                        <i class="fas fa-database"></i>&nbsp;Forzar exportaci&oacute;n Inventario
                                    </a>
                                </li>
                                <li class="contenedor_submenu">
                                	<a  class="dropdown-item borde_superior_1" href="#">
                                		<i class="fas fa-desktop"></i>&nbsp;Equipos en Red HCD&nbsp;
                                	</a>
                                    <ul class="submenu dropdown-menu">
                                        <li>
                                        	<a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=equipos_hcd&accion=listar">
                                        		Listado de Equipos
                                        	</a>
                                        </li>
                                        <li>
                                        	<a  class="dropdown-item"
                                        		href="<?=URL_ABMS;?>?controlador=equipos_hcd&accion=generarArchivoConfiguracion">
                                        		Actualizar Configuraci&oacute;n
                                        	</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a class="dropdown-item borde_superior_1"
                                       href="<?=URL_ABMS;?>?controlador=ordenes_comision&accion=listar">
                                        <i class="far fa-file-alt"></i>&nbsp;&Oacute;rdenes del D&iacute;a de Comisiones&nbsp;
                                    </a>
                                </li>
                                <li class="contenedor_submenu">
                                	<a class="dropdown-item" href="#">
                                		<i class="far fa-file-alt"></i>&nbsp;&Oacute;rdenes del D&iacute;a de Sesi&oacute;n&nbsp;
                                	</a>
                                    <ul class="submenu dropdown-menu">
                                        <li>
                                        	<a  class="dropdown-item"
                                        		href="<?=URL_ABMS;?>?controlador=secciones_orden_sesion&accion=listar">
                                        		Secciones
                                        	</a>
                                        </li>
                                        <li>
                                        	<a  class="dropdown-item"
                                        		href="<?=URL_ABMS;?>?controlador=ordenes_sesion&accion=listar">
                                        		Sesiones
                                        	</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a class="dropdown-item borde_superior_1"
                                       href="<?=URL_ABMS;?>?controlador=ficha_web&accion=listar">
                                        <i class="far fa-address-card"></i>&nbsp;Fichas de Autoridades HCD
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="<?=URL_ABMS;?>?controlador=paginas_sitio&accion=editar">
                                        <i class="fas fa-wrench"></i>&nbsp;Configurar mantenimiento sitio web
                                    </a>
                                </li>
                                <li class="contenedor_submenu">
                                    <a class="dropdown-item borde_superior_1" href="#">
                                        <i class="far fa-envelope"></i>&nbsp;Notificaciones&nbsp;
                                    </a>
                                    <ul class="submenu dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=notificaciones_suscriptores&accion=listar">
                                                Suscriptores
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="<?=URL_ABMS;?>?controlador=notificaciones_listas&accion=listar">
                                                Listas de Distribuci&oacute;n
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=notificaciones_grupos&accion=listar">
                                                Grupos de Distribuci&oacute;n
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=notificaciones&accion=listar">
                                                Notificaciones Internas
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a class="dropdown-item borde_superior_1"
                                       href="<?=URL_ABMS;?>?controlador=defensoria&accion=listar">
                                        <i class="fas fa-edit"></i>&nbsp;Inscripciones a Defensor del Pueblo
                                    </a>
                                </li>
                                <li>
                                    <a  class="dropdown-item borde_superior_1" 
                                        href="<?=URL_ABMS;?>?controlador=banca25&accion=listar">
                                        <i class="fas fa-user"></i>&nbsp;Banca 25
                                    </a>
                                </li>
                            </ul>
			            </li>
					<?php }?>

		            <?php // SÓLO EL PERFIL DE Informática ó Actas
		            if ($_SESSION['perfil1'] == PERFIL_AREA_INFORMATICA || $_SESSION['perfil1'] == PERFIL_AREA_ACTAS) {?>
                		<li class="nav-item dropdown mr-0 mr-md-1">
                		    <a  id="item_actas" class="nav-link dropdown-toggle" href="#" role="button"
                        		data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        		ACTAS
                        	</a>
                    		<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        		<li>
                            		<a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=notificaciones&accion=listar">
                            		    <i class="far fa-envelope"></i>&nbsp;Notificaciones Internas
                            		</a>
                        		</li>
                    		</ul>
                		</li>
            		<?php }?>

					<?php // SÓLO EL PERFIL DE Informática ó Administración
		            if ($_SESSION['perfil1'] == PERFIL_AREA_INFORMATICA || $_SESSION['perfil1'] == PERFIL_AREA_ADMINISTRACION) {?>
			            <li class="nav-item dropdown mr-0 mr-md-1">
			                <a  id="item_administracion" class="nav-link dropdown-toggle" href="#" role="button"
			                	data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">ADMINISTRACI&Oacute;N
			                </a>
			                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
			                	<li class="contenedor_submenu">
                                	<a class="dropdown-item" href="#">
                                		<i class="far fa-file-alt"></i>&nbsp;&Oacute;rdenes del D&iacute;a de Sesi&oacute;n&nbsp;
                                	</a>
                                    <ul class="submenu dropdown-menu">
                                        <li>
                                        	<a  class="dropdown-item"
                                        		href="<?=URL_ABMS;?>?controlador=secciones_orden_sesion&accion=listar">
                                        		Secciones
                                        	</a>
                                        </li>
                                        <li>
                                        	<a  class="dropdown-item"
                                        		href="<?=URL_ABMS;?>?controlador=ordenes_sesion&accion=listar">
                                        		Sesiones
                                        	</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="contenedor_submenu">
                                	<a class="dropdown-item borde_superior_1" href="#">
                                        <i class="far fa-envelope"></i>&nbsp;Notificaciones&nbsp;
                                    </a>
                                    <ul class="submenu dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=notificaciones_suscriptores&accion=listar">
                                                Suscriptores
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="<?=URL_ABMS;?>?controlador=notificaciones_listas&accion=listar">
                                                Listas de Distribuci&oacute;n
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=notificaciones_grupos&accion=listar">
                                                Grupos de Distribuci&oacute;n
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=notificaciones&accion=listar">
                                                Notificaciones Internas
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a  class="dropdown-item borde_superior_1" 
                                        href="<?=URL_ABMS;?>?controlador=banca25&accion=listar">
                                        <i class="fas fa-user"></i>&nbsp;Banca 25
                                    </a>
                                </li>
			                </ul>
			            </li>
			        <?php }?>

		            <?php // SÓLO EL PERFIL DE Informática ó Biblioteca
		            if ($_SESSION['perfil1'] == PERFIL_AREA_INFORMATICA || $_SESSION['perfil1'] == PERFIL_AREA_BIBLIOTECA) {?>
			            <li class="nav-item dropdown mr-0 mr-md-1">
			                <a  id="item_biblioteca" class="nav-link dropdown-toggle" href="#" role="button"
			                	data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			                	BIBLIOTECA
			                </a>
			                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
			                	<li>
			                		<a  class="dropdown-item" 
                                        href="<?=URL_ABMS;?>?controlador=concejales_historico&accion=listar">
				                		<i class="fas fa-book"></i>&nbsp;Hist&oacute;rico de Concejales
					            	</a>
					            </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="<?=URL_ABMS;?>?controlador=contenidos_web&accion=editar&id=<?=ID_CONTENIDO_HISTORIA_BIBLIOTECA;?>">
                                        <i class="far fa-file-alt"></i>&nbsp;Historia de la Biblioteca
                                    </a>
                                </li>
					            <li>
			                		<a  class="dropdown-item borde_superior_1" 
                                        href="<?=URL_ABMS;?>?controlador=notificaciones&accion=listar">
				                		<i class="far fa-envelope"></i>&nbsp;Notificaciones Internas
					            	</a>
					            </li>
				            </ul>
			            </li>
			        <?php }?>

			        <?php // SÓLO EL PERFIL DE Informática ó Comisiones
		            if ($_SESSION['perfil1'] == PERFIL_AREA_INFORMATICA || $_SESSION['perfil1'] == PERFIL_AREA_COMISIONES) {?>
			            <li class="nav-item dropdown mr-0 mr-md-1">
			                <a  id="item_comisiones" class="nav-link dropdown-toggle" href="#" role="button"
			                	data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			                	COMISIONES
			                </a>
			                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
			                	<li>
			                		<a  class="dropdown-item" 
                                        href="<?=URL_ABMS;?>?controlador=comisiones_internas&accion=listar">
				                		<i class="fas fa-tags"></i>&nbsp;Comisiones Internas
					            	</a>
					            </li>
                                <?php /**/ ?>
                                <li>
                                    <a class="dropdown-item borde_superior_1"
                                       href="<?=URL_ABMS;?>?controlador=ordenes_comision&accion=listar">
                                        <i class="far fa-file-alt"></i>&nbsp;&Oacute;rdenes del D&iacute;a de Comisiones&nbsp;
                                    </a>
                                </li>
                                <?php /**/ ?>
					            <li class="contenedor_submenu">
                                	<a class="dropdown-item" href="#">
                                		<i class="far fa-file-alt"></i>&nbsp;&Oacute;rdenes del D&iacute;a de Sesi&oacute;n&nbsp;
                                	</a>
                                    <ul class="submenu dropdown-menu">
                                        <li>
                                        	<a  class="dropdown-item"
                                        		href="<?=URL_ABMS;?>?controlador=secciones_orden_sesion&accion=listar">
                                        		Secciones
                                        	</a>
                                        </li>
                                        <li>
                                        	<a  class="dropdown-item"
                                        		href="<?=URL_ABMS;?>?controlador=ordenes_sesion&accion=listar">
                                        		Sesiones
                                        	</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
			                		<a  class="dropdown-item borde_superior_1" 
                                        href="<?=URL_ABMS;?>?controlador=notificaciones&accion=listar">
				                		<i class="far fa-envelope"></i>&nbsp;Notificaciones Internas
					            	</a>
					            </li>
				            </ul>
			            </li>
			        <?php }?>

			        <?php // SÓLO EL PERFIL DE Informática ó Mesa de Entradas
		            if ($_SESSION['perfil1'] == PERFIL_AREA_INFORMATICA || $_SESSION['perfil1'] == PERFIL_AREA_MESA_ENTRADAS) {?>
                        <li class="nav-item dropdown mr-0 mr-md-1">
			                <a id="item_mesa_entradas" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">MESA ENTRADAS</a>
			                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item"
                                       href="<?=URL_ABMS;?>?controlador=participaciones&accion=listar">
                                        <i class="far fa-hand-paper"></i>&nbsp;Participaciones
                                    </a>
                                </li>
                                <li>
    			                	<a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=notificaciones&accion=listar">
    				                	<i class="far fa-envelope"></i>&nbsp;Notificaciones Internas
    				                </a>
                                </li>
				            </ul>
			            </li>
			        <?php }?>

			        <?php // SÓLO EL PERFIL DE Informática ó Modernizacion
		            if ($_SESSION['perfil1'] == PERFIL_AREA_INFORMATICA || $_SESSION['perfil1'] == PERFIL_AREA_MODERNIZACION) {?>
						<li class="nav-item dropdown mr-0 mr-md-1">
			                <a id="item_modernizacion" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">MODERNIZACI&Oacute;N</a>
			                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
			                	<li>
				                	<a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=compras&accion=listar">
					                	<i class="fas fa-dollar-sign"></i>&nbsp;&Oacute;rdenes de Compra
					                </a>
					            </li>
			                	<li class="contenedor_submenu">
                                	<a class="dropdown-item borde_superior_1" href="#">
                                		<i class="fas fa-database"></i>&nbsp;Datos Abiertos&nbsp;
                                	</a>
                                    <ul class="submenu dropdown-menu">
                                        <li><a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=opendata_publicadores&accion=listar">Publicadores</a></li>
                                        <li><a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=opendata_catalogos&accion=listar">Cat&aacute;logos</a></li>
                                        <li><a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=opendata_datasets&accion=listar">DataSets</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a  class="dropdown-item borde_superior_1" 
                                        href="<?=URL_ABMS;?>?controlador=notificaciones&accion=listar">
                                        <i class="far fa-envelope"></i>&nbsp;Notificaciones Internas
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item borde_superior_1"
                                       href="<?=URL_ABMS;?>?controlador=defensoria&accion=listar">
                                        <i class="fas fa-edit"></i>&nbsp;Inscripciones a Defensor del Pueblo
                                    </a>
                                </li>
				            </ul>
			            </li>
			        <?php }?>

			        <?php // SÓLO EL PERFIL DE Informática ó Prensa PUEDE ACCEDER A ESTA SECCION
		            if ($_SESSION['perfil1'] == PERFIL_AREA_INFORMATICA || $_SESSION['perfil1'] == PERFIL_AREA_PRENSA) {?>
						<li class="nav-item dropdown mr-0 mr-md-1">
			                <a  id="item_prensa" class="nav-link dropdown-toggle" href="#" role="button"
			                	data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			                	PRENSA
			                </a>
			                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
			                	<li>
				                	<a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=distinciones&accion=listar">
					                	<i class="far fa-file-alt"></i>&nbsp;Distinciones
					                </a>
					            </li>
					            <li>
				                	<a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=actividades&accion=listar">
					                	<i class="far fa-file-alt"></i>&nbsp;Actividades
					                </a>
					            </li>
			                	<li>
				                	<a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=gacetillas&accion=listar">
					                	<i class="far fa-calendar-alt"></i>&nbsp;Gacetillas
					                </a>
					            </li>
                                <li>
                                    <a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=carousel&accion=listar">
                                        <i class="far fa-images"></i>&nbsp;Carousel del sitio web
                                    </a>
                                </li>
			                	<li>
				                	<a  class="dropdown-item borde_superior_1" 
                                        href="<?=URL_ABMS;?>?controlador=notificaciones&accion=listar">
					                	<i class="far fa-envelope"></i>&nbsp;Notificaciones Internas
					                </a>
					            </li>
				            </ul>
			            </li>
			        <?php }?>

			        <?php // SÓLO EL PERFIL DE Informática ó Presidencia HCD PUEDE ACCEDER A ESTA SECCION
		            if ($_SESSION['perfil1'] == PERFIL_AREA_INFORMATICA || $_SESSION['perfil1'] == PERFIL_AREA_PRESIDENCIA) {?>
						<li class="nav-item dropdown mr-0 mr-md-1">
			                <a  id="item_presidencia" class="nav-link dropdown-toggle" href="#" role="button"
			                	data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			                	PRESIDENCIA HCD
			                </a>
			                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
			                	<li>
				                	<a class="dropdown-item" href="<?=URL_ABMS;?>?controlador=notificaciones&accion=listar">
					                	<i class="far fa-envelope"></i>&nbsp;Notificaciones Internas
					                </a>
					            </li>
                                <li>
                                    <a class="dropdown-item borde_superior_1"
                                       href="<?=URL_ABMS;?>?controlador=defensoria&accion=listar">
                                        <i class="fas fa-edit"></i>&nbsp;Inscripciones a Defensor del Pueblo
                                    </a>
                                </li>
				            </ul>
			            </li>
			        <?php }?>

                    <li class="nav-item dropdown mr-0 mr-md-4 ml-auto">
                        <a  class="nav-link dropdown-toggle" href="#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user"></i>&nbsp;<?=$_SESSION['nombre_usuario'];?>
                        </a>
                        <ul class="dropdown-menu dropdown_menu_sistemas" aria-labelledby="navbarDropdown">
                            <?php
                            $cant_accesos = (isset($_SESSION['accesos'])) ? count($_SESSION['accesos']) : 0;
                            $tiene_permiso_biblioteca = false;

                            for ($a = 0; $a < $cant_accesos; $a++) {
                                if ($_SESSION['accesos'][$a]['id_sistema'] == 4) {
                                    $tiene_permiso_biblioteca = true;
                                    break;
                                }
                            }
                            
                            // Se muestran los enlaces a los sistemas que el usuario posee acceso
                    		for ($a = 0; $a < $cant_accesos; $a++) {
                    			// 2 = Sistema de Expedientes
                    			if ($_SESSION['accesos'][$a]['id_sistema'] == 2) {?>
                                    <li>
                                        <a  class="dropdown-item"
                                            href="<?=URL_RAIZ_SGL;?>html/backend/index.php?c=expedientes&a=view">
                                            <i class="fas fa-file-alt"></i>&nbsp;Expedientes
                                        </a>
                                    </li>
                                <?php
                                }
                                // 3 = Sistema de Personal
                    			if ($_SESSION['accesos'][$a]['id_sistema'] == 3) {?>
                                    <li>
                                        <a  class="dropdown-item"
                                            href="<?=URL_RAIZ_SGL;?>personal/">
                                            <i class="fas fa-users"></i>&nbsp;Personal
                                        </a>
                                    </li>
                                <?php
                                }
                                // 4 = Sistema de Biblioteca
                                if ($_SESSION['accesos'][$a]['id_sistema'] == 4) { ?>
                                    <li>
                                        <a  class="dropdown-item" target="_blank"
                                            href="<?=URL_RAIZ_SGL;?>administracion/abms/index.php?controlador=usuarios&accion=ingresarAlSistemaDeBiblioteca">
                                            <i class="fas fa-book"></i>&nbsp;Biblioteca
                                        </a>
                                    </li>
                                <?php
                                }
                                // 5 = Sistema de Inventario
                                if ($_SESSION['accesos'][$a]['id_sistema'] == 5) { ?>
                                    <li>
                                        <a  class="dropdown-item" target="_blank"
                                            href="<?=URL_RAIZ_SGL;?>administracion/abms/index.php?controlador=usuarios&accion=ingresarAlSistemaDeInventario">
                                            <i class="fas fa-desktop"></i>&nbsp;Inventario
                                        </a>
                                    </li>
                                <?php
                                }
                                // 6 = Sistema de Defensoria
                                if ($_SESSION['accesos'][$a]['id_sistema'] == 6) {?>
                                    <li>
                                        <a  class="dropdown-item"
                                            href="<?=URL_RAIZ_SGL;?>defensoria/abms/">
                                            <i class="fas fa-file-alt"></i>&nbsp;Defensor&iacute;a
                                        </a>
                                    </li>
                                <?php
                                }
		                    }

                            // Si NO tiene acceso al Sistema de Biblioteca (id=4), se lo dirige al Dashboard de consultas
                            if ( ! $tiene_permiso_biblioteca) { ?>
                                <li>
                                    <a href="http://biblioteca.concejomdp.gov.ar/dashboard/dbselector"
                                       class="dropdown-item"
                                       target="_blank">
                                       <i class="fas fa-book"></i>&nbsp;Biblioteca
                                    </a>
                                </li>
                            <?php } ?>

                            <li>
                                <a  class="dropdown-item borde_superior_1"
                                    href="<?=URL_RAIZ_SGL;?>html/backend/index.php?c=login&a=logout">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp;Cerrar sesi&oacute;n
                                </a>
                            </li>
                        </ul>
                    </li>
		        </ul>
		    </div>
		</nav>
		<script>
            // Para evitar el cierre de clic dentro del menú desplegable
            $(document).on('click', '.dropdown-menu', function (e) {
              	e.stopPropagation();
            });

            // Acordeón para pantallas más pequeñas
            if ($(window).width() < 992) {
              	$('.dropdown-menu a').click(function(e){
	                e.preventDefault();

                  	if ($(this).next('.submenu').length){
                    	$(this).next('.submenu').toggle();
                  	}

                  	$('.dropdown').on('hide.bs.dropdown', function () {
                 		$(this).find('.submenu').hide();
	              	})
              	});
            }
        </script>
    	<?php }

	/**
	 * Se muestra el contenedor de la Modal de mensajes del Backend
	 */
	public function mostrarContenedorModal() {?>
		<!-- Modal para Mensajes al usuario -->
		<div id="modal_general" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_general" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
				        <img id="cabecera_logo" src="<?=URL_IMAGENES;?>logo_hcd.png" class="rounded-circle" height="" alt="<?=TITULO_SISTEMA;?>">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
				          <span aria-hidden="true">&times;</span>
				        </button>
			        </div>
					<div class="modal-body">
						<p id="mensaje_en_modal"></p>
					</div>
				</div>
			</div>
		</div>
		<a id="muestra_modal" href="#modal_general" data-toggle="modal" style="display:none"></a>
    <?php }

	/**
	 * Se muestra la Modal para confirmar
	 */
	public function mostrarModalConfirmacion() {?>

         <div id="modal_confirmacion" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_confirmacion" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <img id="cabecera_logo" src="<?=URL_IMAGENES;?>logo_hcd.png" class="rounded-circle" height="" alt="<?=TITULO_SISTEMA;?>">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-exclamation-circle"></i>&nbsp;<span id="mensaje_en_modal_confirmacion"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a id="btnModalConfirmacionSi" class="btn btn-sm btn-success">S&iacute;</a>
                        <a id="btnModalConfirmacionNo" class="btn btn-sm btn-danger">No</a>
                    </div>
                </div>
            </div>
        </div>
        <a id="muestra_modal_confirmacion" href="#modal_confirmacion" data-toggle="modal" style="display:none"></a>
    <?php }

	/**
	 * Se muestra el contenedor del Spinner de carga
	 */
	public function mostrarSpinnerModal() {?>
        <!-- Modal para Mensajes al usuario -->
        <div id="modal_spinner" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_spinner" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <img id="cabecera_logo" src="<?=URL_IMAGENES;?>logo_hcd.png" class="rounded-circle mx-auto" alt="<?=TITULO_SISTEMA;?>">
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-center">
                          <div class="spinner-border text-info" role="status">
                            <span class="sr-only">Procesando...</span>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a id="muestra_spinner" href="#modal_spinner" data-toggle="modal" style="display:none"></a>
    <?php }

	/**
	 * Se muestra el Paginador
	 * @param  integer 	$cant_registros_actual	Número de registros en la página actual
	 * @param  array 	$filtro             	Conjunto de filtros a utilizar
	 * @param  string 	$criterio_buscador 		Criterio de búsqueda para mantener el filtro previo
	 * @param  string 	$controlador       		Nombre del controlador a paginar
	 */
	public function mostrarPaginador($cant_registros_actual, $filtro, $criterio_buscador, $controlador) {
		?>
		<div class="row my-1 p-1 fuente_08 bg-light">
			<div class="col-12 col-md-3 text-center text-md-left">
				Registros en la p&aacute;gina actual: <?=$cant_registros_actual;?>
			</div>
			<div class="col-12 col-md-3 text-center text-md-left">
				Total de resultados: <?=$filtro['cantidad'];?>
			</div>
			<div class="col-12 col-md-6 text-center text-md-left">

				<?php if ($filtro['cantidad'] > 0) {

			if ($filtro['pagina'] != 1) {?>

						<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$controlador;?>&accion=listar<?=$criterio_buscador;?>&pagina=1');" class="mx-2" title="Ver los primeros <?=$filtro['rango'];?> registros">
							<i class="fas fa-angle-double-left"></i>&nbsp;Primeros
						</a>

					<?php }if ($filtro['pagina_ant'] != 0) {?>

						<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$controlador;?>&accion=listar<?=$criterio_buscador;?>&pagina=<?=$filtro['pagina_ant'];?>');" class="mx-2" title="Ver los <?=$filtro['rango'];?> registros anteriores">
							<i class="fas fa-angle-left"></i>&nbsp;Anteriores
						</a>
					<?php }?>
						<span class="text-center">
							<?="&nbsp;" . $filtro['pagina'] . " de " . $filtro['nro_paginas'] . "&nbsp;";?>
						</span>

					<?php if ($filtro['pagina'] != $filtro['nro_paginas']) {?>

						<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$controlador;?>&accion=listar<?=$criterio_buscador;?>&pagina=<?=$filtro['pagina_sgte'];?>');" class="mx-2" title="Ver los <?=$filtro['rango'];?> registros siguientes">
							Siguientes&nbsp;<i class="fas fa-angle-right"></i>
						</a>

					<?php }if ($filtro['pagina'] != $filtro['nro_paginas']) {?>

						<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$controlador;?>&accion=listar<?=$criterio_buscador;?>&pagina=<?=$filtro['nro_paginas'];?>');" class="mx-2" title="Ver los &uacute;ltimos <?=$filtro['rango'];?> registros">
							&Uacute;ltimos&nbsp;<i class="fas fa-angle-double-right"></i>
						</a>
					<?php }?>
				<?php }?>
			</div>
		</div>
    <?php }

	/**
	 * [mostrarPaginadorMostrandoActual description]
	 * @param  [type] $cant_registros_actual [description]
	 * @param  [type] $filtro                [description]
	 * @param  [type] $criterio_buscador     [description]
	 * @param  [type] $controlador           [description]
	 * @return [type]                        [description]
	 */
	public function mostrarPaginadorMostrandoActual($cant_registros_actual, $filtro, $criterio_buscador, $controlador) {
		?>
        <div class="row my-1 p-1 fuente_08 bg-light">
            <div class="col-12 col-md-3 text-center text-md-left">
                Registros en la p&aacute;gina actual: <?=$cant_registros_actual;?>
            </div>
            <div class="col-12 col-md-3 text-center text-md-left">
                Total de resultados: <?=$filtro['cantidad'];?>
            </div>
            <div class="col-12 col-md-6 text-center text-md-left">

        <?php if ($filtro['cantidad'] > 0) {

			if ($filtro['pagina'] != 1) {?>

                <a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$controlador;?>&accion=listar&sentido=primero<?=$criterio_buscador;?>&pagina=1');" class="mx-2" title="Ver los primeros <?=$filtro['rango'];?> registros">
                    <i class="fas fa-angle-double-left"></i>&nbsp;Primeros
                </a>

        <?php }if ($filtro['pagina'] > 1) {?>

                <a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$controlador;?>&accion=listar&sentido=anterior<?=$criterio_buscador;?>&pagina=<?=$filtro['pagina_ant'];?>');" class="mx-2" title="Ver los <?=$filtro['rango'];?> registros anteriores">
                    <i class="fas fa-angle-left"></i>&nbsp;Anteriores
                </a>
        <?php }?>
                <span class="text-center">
                    <?="&nbsp;" . $filtro['pagina'] . " de " . $filtro['nro_paginas'] . "&nbsp;";?>
                </span>

        <?php if ($filtro['pagina'] != $filtro['nro_paginas']) {?>

                <a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$controlador;?>&accion=listar&sentido=siguiente<?=$criterio_buscador;?>&pagina=<?=$filtro['pagina_sgte'];?>');" class="mx-2" title="Ver los <?=$filtro['rango'];?> registros siguientes">
                    Siguientes&nbsp;<i class="fas fa-angle-right"></i>
                </a>

        <?php }if ($filtro['pagina'] != $filtro['nro_paginas']) {?>

                <a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$controlador;?>&accion=listar&sentido=ultimo<?=$criterio_buscador;?>&pagina=<?=$filtro['nro_paginas'];?>');" class="mx-2" title="Ver los &uacute;ltimos <?=$filtro['rango'];?> registros">
                    &Uacute;ltimos&nbsp;<i class="fas fa-angle-double-right"></i>
                </a>
        <?php }}?>
            </div>
        </div>
    <?php }

	/**
	 * Devuelve el formato dia/mes/anio completo
	 * @param  string $fecha yyyy-mm-dd
	 * @return string        dd/mm/yyyy
	 */
	public function formatearFecha($fecha) {
		if ($fecha) {
			if ($fecha != '0000-00-00') {
				$fec_partes = explode("-", $fecha);
				$fecha_a_ver = $fec_partes[2] . '/' . $fec_partes[1] . '/' . $fec_partes[0];

				return $fecha_a_ver;
			} else
				return '';
		} else
			return '';
	}

    public function extraerFecha($fecha_hora) {
        if ($fecha_hora) {
            $partes = explode(" ", $fecha_hora);
            return $this->formatearFecha($partes[0]);
        } else
            return '';
    }

	public function cortaCadena($string, $charlimit) {
		if (substr($string, $charlimit - 1, 1) != '') {
			$string = substr($string, '0', $charlimit);
			$array = explode(' ', $string);
			array_pop($array);
			$new_string = implode(' ', $array);
			return $new_string . ' ...';
		} else {
			return substr($string, '0', $charlimit - 1) . ' ...';
		}

	}

	public function obtenerNombreMes($numero_mes) {
		// NOMBRES DE LOS MESES
		$nombres_meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

		// SE ASIGNA EL NOMBRE DEL MES
		$nombre_mes = $nombres_meses[$numero_mes - 1];

		return $nombre_mes;
	}

	public function rellenarConCerosIzquierda($valor, $longitud) {
		return str_pad($valor, $longitud, '0', STR_PAD_LEFT);
	}

	public function mostrarMilesConXdecimales($valor, $decimales) {
		return number_format($valor, $decimales, ',', '.');
	}

	/**
	 * Devuelve el color de fondo y texto según un estado determinado
	 * @param integer $estado
	 * @return string $color_fondo_y_texto
	 */
	public function mostrarColorEstado($estado) {
		$color_fondo_y_texto = "";

		switch ($estado) {
		case '1':
			$color_fondo_y_texto = "background-color: #FCF8E3;color: #C09853;"; // AMARILLO PASTEL
			break;
		case '2':
			$color_fondo_y_texto = "background-color: #F2DEDE;color: #B94A48;"; // ROJO PASTEL
			break;
		case '3':
			$color_fondo_y_texto = "background-color: #DFF0D8;color: #468847;"; // VERDE PASTEL
			break;
		case '4':
			$color_fondo_y_texto = "background-color: #0C99D5;color: #FFFFFF;"; // AZUL
			break;
		}

		return $color_fondo_y_texto;
	}

	public function reemplazarPorMayusculaAcentuada($cadena) {
		$cadena = str_replace('á', 'Á', $cadena);
		$cadena = str_replace('é', 'É', $cadena);
		$cadena = str_replace('í', 'Í', $cadena);
		$cadena = str_replace('ó', 'Ó', $cadena);
		$cadena = str_replace('ú', 'Ú', $cadena);

		return $cadena;
	}

	public function reemplazarPorHTML($cadena) {

		$cadena = str_replace("á", "&aacute;", $cadena);
		$cadena = str_replace("é", "&eacute;", $cadena);
		$cadena = str_replace("í", "&iacute;", $cadena);
		$cadena = str_replace("ó", "&oacute;", $cadena);
		$cadena = str_replace("ú", "&uacute;", $cadena);
		$cadena = str_replace("ñ", "&ntilde;", $cadena);
		$cadena = str_replace("Á", "&Aacute;", $cadena);
		$cadena = str_replace("É", "&Eacute;", $cadena);
		$cadena = str_replace("Í", "&Iacute;", $cadena);
		$cadena = str_replace("Ó", "&Oacute;", $cadena);
		$cadena = str_replace("Ú", "&Uacute;", $cadena);
		$cadena = str_replace("Ñ", "&Ntilde;", $cadena);
		$cadena = str_replace("ü", "&uuml;", $cadena);
		$cadena = str_replace("Ü", "&Uuml;", $cadena);
		$cadena = str_replace("@", "&#64;", $cadena);
		$cadena = str_replace("°", "&deg;", $cadena);
		$cadena = str_replace("º", "&deg;", $cadena);
		$cadena = str_replace("ª", "&deg;", $cadena);
		$cadena = str_replace('"', "&#34;", $cadena);

		return $cadena;
	}

	public function reemplazarComillaDoble($cadena) {
		return str_replace('"', "'", $cadena);
	}

	public function mostrarVideoYoutube($url, $ancho = 170, $alto = 205) {
		parse_str(parse_url($url, PHP_URL_QUERY));

		$id_video = !empty($v) ? $v : $url;

		return '<iframe width="' . $ancho . '" height="' . $alto . '" src="https://www.youtube.com/embed/' . $id_video . '" frameborder="0" allowfullscreen></iframe>';
	}

	public function mostrarVideoLocal($url) {

		return '<video width="320" height="240" controls><source src="' . $url . '" type="video/mp4"></video>';
	}

	public function mostrarNombreSinPrefijo($nombre, $prefijo) {
		return str_replace($prefijo . '_', '', $nombre);
	}

	/**
	 * Se definen los estilos CSS para el HTML de la Orden del Día de Sesión y de Comisiones
	 */
	public function estilosCssOrden() {?>
        <style type="text/css">
            /* Texto del Decreto, previo al Anexo */
            .od_texto_decreto_previo_anexo {
                font-size: 18px;
                text-align: center;
                margin-bottom: 25px;
            }
            /* TITULO DEL ENCABEZADO*/
            .od_titulo_encabezado {
                font-size: 18px;
                text-align: center;
                font-weight: bold;
                margin-bottom: 25px;
            }

            /* DATOS DE LA ORDEN DEL DIA*/
            .od_datos_orden_dia_sesion {
                height: 140px;
                margin: 30px 0 55px 0;
            }
            .od_datos_orden_dia_sesion table {
                height: 140px;
            }

            .orden_dia_sesion_datos_espacio_izquierdo {
                width: 200px;
            }
            .orden_dia_sesion_datos_titulo {
                width: 110px;
                vertical-align: top;
            }
            .orden_dia_sesion_datos_valor {
                width: 300px;
            }
            .orden_dia_sesion_datos_texto {
                font-size: 15px;
                font-weight: bold;
            }

            /* TITULO "ORDEN DEL DIA"*/
            .od_titulo_orden_del_dia {
                font-size: 16px;
                font-weight: bold;
                text-align: center;
                margin-bottom: 7px;
            }

            /* TITULO "SUMARIO"*/
            .od_titulo_sumario {
                font-size: 14px;
                font-weight: bold;
                text-align: left;
            }
            /* TITULO DE LA SECCION DEL SUMARIO*/
            .od_titulo_sumario_seccion {
                font-size: 18px;
                font-weight: bold;
                text-align: left;
                margin-top: 40px;
            }

            /* TITULO DE LA SUBSECCION DEL SUMARIO*/
            .od_titulo_sumario_subseccion {
                font-size: 15px;
                font-weight: normal;
                text-align: left;
                margin-top: 6px;
            }
            .od_titulo_sumario_mayuscula {
                text-transform: uppercase;
            }

            /* MARGEN ENTRE EL SUMARIO Y LAS SECCIONES DETALLADAS*/
            .od_margen_sumario_secciones {
                clear: both;
                height: 170px;
                font-size: 0;
            }

            /* TITULO DE LA SECCION*/
            .od_titulo_seccion {
                font-size: 15px;
                font-weight: bold;
                text-align: center;
                margin: 25px 0 5px 0;
            }

            /* TITULO DE LA SUBSECCION*/
            .od_titulo_subseccion {
                font-size: 13px;
                font-weight: bold;
                text-align: left;
                margin-top: 20px;
            }

            .od_borde_superior_1 {
                border-top: 1px solid #e9ecef;
            }
        </style>
    <?php }

	/**
	 * Se definen los estilos CSS para el HTML de la Orden del Día de Comisión
	 */
	public function CssOrdenComision() {
		?>
        <style type="text/css">
            .wysiwyg-text-align-left {
                text-align: left;
            }
            .wysiwyg-text-align-center {
                text-align: center;
            }
            .wysiwyg-text-align-right {
                text-align: right;
            }
        </style>
    <?php }

	/**
	 * Se muestra el nombre de la Marca en Comisión
	 * @param  integer $marca_comision  Identificador de la marca en comisión
	 * @return string  $nombre          Nombre de la marca en comisión
	 */
	public function mostrarNombreMarcaComision($marca_comision) {

		switch ($marca_comision) {
		case 1:
			$nombre = "Para tratar";
			break;
		case 2:
			$nombre = "Para su conocimiento";
			break;
		case 3:
			$nombre = "Para archivo";
			break;
		case 5:
			$nombre = "Para convalidar";
			break;
		}

		return $nombre;
	}

	/**
	 * Se obtiene el nombre del archivo a generar para la Orden de Comisión
	 * @param  string $codigo_comision  Código de la Comisión
	 * @return string                   Nombre del archivo
	 */
	public function obtenerNombreArchivoOrdenComision($codigo_comision) {

		switch ($codigo_comision) {
		case '052':
			$nombre = "ordenambiente";
			break;
		case '027':
			$nombre = "ordendeportes";
			break;
		case '054':
			$nombre = "ordenderechoshumanos";
			break;
		case '049':
			$nombre = "ordeneducacion";
			break;
		case '002':
			$nombre = "ordenhacienda";
			break;
		case '051':
			$nombre = "ordenindustria";
			break;
		case '047':
			$nombre = "ordenlegislacion";
			break;
		case '050':
			$nombre = "ordenmovilidadurbana";
			break;
		case '056':
			$nombre = "ordenobras";
			break;
		case '055':
			$nombre = "ordenpoliticasdegenero";
			break;
		case '048':
			$nombre = "ordensaludcomunitaria";
			break;
		case '053':
			$nombre = "ordenseguridadpublica";
			break;
		case '006':
			$nombre = "ordenturismo";
			break;
		}
		return $nombre;
	}

	/**
	 * Devuelve el número del día que le corresponde en la semana
	 * @param  integer $anio [description]
	 * @param  integer $mes  [description]
	 * @param  integer $dia  [description]
	 * @return integer       Número del día que le corresponde en la semana
	 */
	public function obtenerNumeroDia($anio, $mes, $dia) {
		return date("w", mktime(0, 0, 0, $mes, $dia, $anio));
	}

	/**
	 * Devuelve el nombre del día en la semana
	 * @param  string $fecha        Fecha en formato yyyy-mm-dd
	 * @return string $nombre_dia   Nombre del día
	 */
	public function obtenerNombreDia($fecha) {
		// Nombres de días de la semana (0 = domingo, 6 = sabado)
		$nombres_dias = array("Domingo", "Lunes", "Martes", "Mi&eacute;rcoles", "Jueves", "Viernes", "S&aacute;bado");

		// Se separa la fecha por su guión
		$partes = explode('-', $fecha);

		$anio = $partes[0];
		$mes = $partes[1];
		$dia = $partes[2];

		// Se obtiene el número del día en la semana
		$numero_dia_en_semana = $this->obtenerNumeroDia($anio, $mes, $dia);

		// Se obtiene el nombre del día, según su número en la semana
		$nombre_dia = $nombres_dias[$numero_dia_en_semana];

		return $nombre_dia;
	}

	public function mostrarNombreNumeroDiaActual() {
		return $this->obtenerNombreDia(date("Y-m-d")) . ' ' . date("d");
	}

	/**
	 * Devuelve el nombre del mes según su número
	 * @param integer $nro_mes
	 * @return Ambigous <string>
	 */
	public function mostrarNombreMes($nro_mes) {
		$meses = Array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

		return $meses[$nro_mes - 1];
	}

	/**
	 * Muestra la fecha en letras
	 * @param string $fecha
	 */
	public function mostrarFechaLetras($fecha) {
		// Se divide la fecha por cada guión
		$partes_fecha = explode('-', $fecha);

		// Se establece el número del día
		$dia = ($partes_fecha[2] < 10) ? substr($partes_fecha[2], 1, 1) : $partes_fecha[2];

		// Devuelve la fecha en formato [nro del día] de [nombre del mes] de [nro del año]
		return $dia . " de " . $this->mostrarNombreMes($partes_fecha[1]) . " de " . $partes_fecha[0];
	}

	public function mostrarFechaConNombreDiaCompleto($fecha) {
		return $this->obtenerNombreDia($fecha) . ' ' . $this->mostrarFechaLetras($fecha);
	}

}
?>
