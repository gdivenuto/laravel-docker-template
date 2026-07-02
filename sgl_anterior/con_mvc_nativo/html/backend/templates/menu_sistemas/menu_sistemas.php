<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 *
 *  $this->vista->data 				Array asociativo que contiene todos los parámetros de la vista para ser
 *  								utilizados en la plantilla.
 *
 * Además:
 *
 *  $this->vista->dataTitulo		Titulo de la vista
 *  $this->vista->dataSubtitulo		Subtitulo de la vista
 *  $this->vista->dataTexto			Texto introductorio de la vista
 *  $this->vista->dataUsuario		Instancia del usuario actual.
 *  $this->vista->dataMensajeOk		Mensaje de confirmación que debe mostrarse en la vista.
 *  $this->vista->dataMensajeError	Mensaje de error que debe mostrarse en la vista.
 */
?>
<div class="row">
	<div id="menu_contenedor" class="col-xs-10 col-xs-offset-1 col-md-4 col-md-offset-4">
		<div class="list-group">
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
				if ($_SESSION['accesos'][$a]['id_sistema'] == 1) {
					// Para implementar el nuevo sistema de Administración
					// y dejar el actual temporalmente hasta migrarlo entero
					// -----------------------------------------------------
					?>
					<a  href="<?=URL_KRAKEN_BASE;?>administracion/abms/"
						class="list-group-item opcion">
						<h1 class="list-group-item-heading">
							<span class="glyphicon glyphicon-tasks"></span>&nbsp;Administraci&oacute;n
						</h1>
					</a>
				<?php }

				// 2 = Sistema de Expedientes
				if ($_SESSION['accesos'][$a]['id_sistema'] == 2) {
					// Sólo para Notificar al Perfil 1 o 2 en Expedientes
					// y al perfil de informática (14) o Mesa de Entradas (24) en Administración
					if (($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) && 
						($_SESSION['perfil1'] == 14 ||$_SESSION['perfil1'] == 24)) {
						$_SESSION['verificacion_ppc_hecha'] = 0;
					} ?>
					<a href="index.php?c=expedientes&a=view" class="list-group-item opcion">
						<h1 class="list-group-item-heading">
							<span class="glyphicon glyphicon-list-alt"></span>&nbsp;Expedientes
						</h1>
					</a>
				<?php }

				// 3 = Sistema de Personal
				if ($_SESSION['accesos'][$a]['id_sistema'] == 3) { ?>
					<a href="<?=URL_KRAKEN_BASE;?>personal/" class="list-group-item opcion">
						<h1 class="list-group-item-heading">
							<span class="glyphicon glyphicon-user"></span>&nbsp;Personal
						</h1>
					</a>
				<?php }

				// 4 = Sistema de Biblioteca
				if ($_SESSION['accesos'][$a]['id_sistema'] == 4) { ?>
					<a  href="<?=URL_KRAKEN_BASE;?>administracion/abms/index.php?controlador=usuarios&accion=ingresarAlSistemaDeBiblioteca" target="_blank" class="list-group-item opcion">
						<h1 class="list-group-item-heading">
							<span class="glyphicon glyphicon-book"></span>&nbsp;Biblioteca
						</h1>
					</a>
				<?php }

				// 5 = Sistema de Inventario
				if ($_SESSION['accesos'][$a]['id_sistema'] == 5) { ?>
					<a  href="<?=URL_KRAKEN_BASE;?>administracion/abms/index.php?controlador=usuarios&accion=ingresarAlSistemaDeInventario" target="_blank"
						class="list-group-item opcion">
						<h1 class="list-group-item-heading">
							<span class="glyphicon glyphicon-folder-open"></span>&nbsp;Inventario
						</h1>
					</a>
				<?php }

				// 6 = Sistema de Defensoria
				if ($_SESSION['accesos'][$a]['id_sistema'] == 6) { ?>
					<a href="<?=URL_KRAKEN_BASE;?>defensoria/abms/" class="list-group-item opcion">
						<h1 class="list-group-item-heading">
							<span class="glyphicon glyphicon-list-alt"></span>&nbsp;Defensoria del Pueblo
						</h1>
					</a>
				<?php }

			}

			// Si NO tiene acceso al Sistema de Biblioteca (id=4), se lo dirige al Dashboard de consultas
			if ( ! $tiene_permiso_biblioteca) { ?>
				<a  href="http://biblioteca.concejomdp.gov.ar/dashboard/dbselector" 
					target="_blank" class="list-group-item opcion">
					<h1 class="list-group-item-heading">
						<span class="glyphicon glyphicon-book"></span>&nbsp;Biblioteca
					</h1>
				</a>
			<?php } ?>
			
			<a href="index.php?c=login&a=logout" class="list-group-item opcion">
				<h1 class="list-group-item-heading">
					<span class="glyphicon glyphicon-off"></span>&nbsp;Salir
				</h1>
			</a>
		</div>
	</div>
</div>
<style type="text/css">
	body {
		background-image: url(../../resources/images/fondo_hcd_gris.jpg);
	 	background-repeat: no-repeat;
		background-size: cover;
	}
	nav, footer {
		display: none;
	}
	.page-header {
		border-bottom: 0;
	}
	#menu_contenedor {
		margin-top: 2%;
	 	padding: 20px 30px;
		border-radius: 10;
	}
	#menu_contenedor a {
		margin: 10px;
		padding: 20px;
		background-color: #2da4c6;
		opacity: .8;
		border-top-right-radius: 20px;
		border-bottom-left-radius: 20px;
	}
	#menu_contenedor a h1 {
		font-size: 22px;
		font-style: italic;
		color: #fff;
	}
	@media (max-width: 740px) {
		#menu_contenedor a h1 {
			font-size: 18px;
		}
	}
</style>
<script>
	jQuery(document).ready(function() {
		// Se definen las opacidades y la duración de la transición
		var opacidad_inicial = 0.7, opacidad_hasta = 0.9, duracion = 250;

		// Se setea la opacidad
		jQuery('.opcion').css('opacity',opacidad_inicial).hover(function() {
				jQuery(this).fadeTo(duracion, opacidad_hasta);
			}, function() {
				jQuery(this).fadeTo(duracion, opacidad_inicial);
			}
		);
	});
</script>