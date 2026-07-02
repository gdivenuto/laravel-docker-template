<?php
abstract class VistaBase {

	protected $controlador;
	protected $modelo;

	public function __construct() {	}

	public function mostrarContenidoHead() {?>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	    <title><?=TITULO_SISTEMA;?></title>

    	<link type="image/x-icon" href="<?=URL_IMAGENES;?>favicon.ico" rel="shortcut icon" >

	    <script src="<?=URL_JS_LIBRERIAS;?>jquery_3.5.1.js"></script>
	    <script src="<?=URL_JS_LIBRERIAS;?>popper_1.16.0.js"></script>
	    <script src="<?=URL_JS_LIBRERIAS;?>bootstrap_4.6.0.js"></script>
	    <script src="<?=URL_JS_LIBRERIAS;?>fontawesome_5.js"></script>

	    <link href="<?=URL_CSS;?>bootstrap_4.6.0.css" rel="stylesheet">

		<link href="<?=URL_CSS;?>jquery-ui.css" rel="stylesheet">
		<script src="<?=URL_JS_LIBRERIAS;?>jquery-ui.js"></script>

	    <script src="<?=URL_JS_LIBRERIAS;?>gijgo_1.9.13.min.js" type="text/javascript"></script>
	    <script src="<?=URL_JS_LIBRERIAS;?>gijgo_1.9.13.es-es.js" type="text/javascript"></script>
		<link href="<?=URL_CSS;?>gijgo_1.9.13.min.css" rel="stylesheet" type="text/css" />

	    <link href="<?=URL_CSS;?>propio.css?v=<?=date("Ymd_His");?>" rel="stylesheet">
		<script src="<?=URL_JS;?>propio.js?v=<?=date("Ymd_His");?>"></script>
		<?php }

	/**
	 * Se muestra el Menu Principal del Backend
	 */
	public function mostrarMenuPrincipal() {?>
		<!-- Menú de Navegación -->
		<nav id="menu_home" class="navbar navbar-expand-lg navbar-light d-flex justify-content-around">
		    <a class="navbar-brand" href="<?=URL_ABMS;?>?controlador=<?=CONTROLADOR_POR_DEFECTO;?>&accion=listar">
		        <img id="cabecera_logo" src="<?=URL_IMAGENES;?>logo.png" height="62" alt="<?=TITULO_SISTEMA;?>">
		    </a>

		    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menu_superior" aria-controls="menu_superior" aria-expanded="false" aria-label="Toggle navigation">
		        <span class="navbar-toggler-icon"></span>
		    </button>

		    <div id="menu_superior" class="collapse navbar-collapse">
		        <ul class="navbar-nav mx-auto">
					<!-- <li class="nav-item dropdown mr-0 mr-md-5">
		                <a id="item_modelo_escrito" href="<?=URL_ABMS;?>?controlador=modelo_escrito&accion=listar" class="nav-link">
		                	<i class="fas fa-edit"></i>&nbsp;MODELOS de ESCRITO
		                </a>
			        </li> -->
		            <li class="nav-item dropdown mr-0 mr-md-3">
		                <a id="item_tipo_proceso" href="<?=URL_ABMS;?>?controlador=tipo_proceso&accion=listar" class="nav-link">
		                	<i class="fas fa-tags"></i>&nbsp;TIPOS de PROCESO
		                </a>
			        </li>
			        <li class="nav-item dropdown mr-0 mr-md-3">
			        	<a  id="item_presentante" href="<?=URL_ABMS;?>?controlador=presentador&accion=listar" class="nav-link">
		                	<i class="fas fa-users"></i>&nbsp;PRESENTANTES
		                </a>
		            </li>
					<li class="nav-item dropdown mr-0 mr-md-3">
			        	<a  id="item_expediente" href="<?=URL_ABMS;?>?controlador=expediente&accion=listar" class="nav-link">
		                	<i class="fas fa-file-alt"></i>&nbsp;EXPEDIENTES
		                </a>
		            </li>
		            <li class="nav-item dropdown mr-0 mr-md-3">
			        	<a  id="item_nota" href="<?=URL_ABMS;?>?controlador=nota&accion=listar" class="nav-link">
		                	<i class="fas fa-file-alt"></i>&nbsp;NOTAS
		                </a>
		            </li>
		            <li class="nav-item dropdown mr-0 mr-md-3">
			        	<a  id="item_remitente" href="<?=URL_ABMS;?>?controlador=remitente&accion=listar" class="nav-link">
		                	<i class="fas fa-users"></i>&nbsp;REMITENTES
		                </a>
		            </li>
		            <li class="nav-item dropdown mr-0 mr-md-3">
			        	<a  id="item_resolucion" href="<?=URL_ABMS;?>?controlador=resolucion&accion=listar" class="nav-link">
		                	<i class="fas fa-file-alt"></i>&nbsp;RESOLUCIONES
		                </a>
		            </li>
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

                    			// 1 = Sistema de Administración
								if ($_SESSION['accesos'][$a]['id_sistema'] == 1) {?>
									<li>
										<a  class="dropdown-item"
											href="<?=URL_RAIZ_SGL;?>administracion/abms/">
											<i class="fas fa-wrench"></i>&nbsp;Administraci&oacute;n
										</a>
									</li>
								<?php
								}
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
                                <?php }
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
				        <img src="<?=URL_IMAGENES;?>logo.png" height="72" alt="<?=TITULO_SISTEMA;?>" class="img-fluid mx-auto" />
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
	 * Se muestra el contenedor del Spinner de carga
	 */
	public function mostrarSpinnerModal() {?>
		<!-- Modal para Mensajes al usuario -->
		<div id="modal_spinner" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_spinner" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
				        <img src="<?=URL_IMAGENES;?>logo.png" height="72" alt="<?=TITULO_SISTEMA;?>" class="img-fluid mx-auto" />
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
	 * Se muestra un contenido en una Modal más grande (Large)
	 */
	public function mostrarContenidoEnModalGrande() {?>
		<div id="modal_contenido" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_contenido" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
				        <img src="<?=URL_IMAGENES;?>logo.png" height="72" alt="<?=TITULO_SISTEMA;?>" class="img-fluid mx-auto" />
				        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
				          <span aria-hidden="true">&times;</span>
				        </button>
			        </div>
					<div id="contenido_en_modal" class="modal-body">
					</div>
				</div>
			</div>
		</div>
		<a id="muestra_modal_contenido" href="#modal_contenido" data-toggle="modal" style="display:none"></a>
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
				<?php }}?>
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
			} else {
				return '';
			}
		} else {
			return '';
		}
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
		$nombres_meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		return $nombres_meses[$numero_mes - 1];
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

	public function mostrarNombreSinPrefijo($nombre, $prefijo) {
		return str_replace($prefijo . '_', '', $nombre);
	}

}
?>
