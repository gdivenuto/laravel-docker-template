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
 *  $this->vista->dataTituloApp		Titulo de la aplicacion
 *  $this->vista->dataTitulo		Titulo de la vista
 *  $this->vista->dataSubtitulo		Subtitulo de la vista
 *  $this->vista->dataTexto			Texto introductorio de la vista
 *  $this->vista->dataUsuario		Instancia del usuario actual.
 *  $this->vista->dataMensajeOk		Mensaje de confirmación que debe mostrarse en la vista.
 *  $this->vista->dataMensajeError	Mensaje de error que debe mostrarse en la vista.
 */
// Si el usaurio se encuentra logueado
if (!is_null($this->vista->dataUsuario)) {
	// Genero los flags para saber si muestro o no los Giros, Revisiones y Firmas pendientes
	$hayGirosPendientes = (NG::girosPendientes()->obtenerGirosPendientesParaUsuarioCantidad($this->vista->dataUsuario) > 0);
	$hayFirmasExpedienteElec = (NG::firmasExpedienteElec()->obtenerFirmasPendientesUsuarioCantidad($this->vista->dataUsuario) > 0);
	$hayRevisionesPendientes = (NG::revExpedienteElecPend()->obtenerRevisionesPendientesUsuarioCantidad($this->vista->dataUsuario) > 0);
} else {
	$hayGirosPendientes = false;
	$hayFirmasExpedienteElec = false;
	$hayRevisionesPendientes = false;
}
?>
<!-- Navbar -->
<!-- navbar-default: comun, navbar-inverse: inverso -->
<!-- navbar-fixed-top: fijo en el tope de la pagina, sin importar scroll (siempre visible) -->
<nav class="navbar navbar-default navbar-fixed-top color-fondo-menu menu-superior-fijo">
	<div class="container-fluid">
		<!-- "Brand" y el boton que despliega el menu cuando se agrupa la barra (responsive) -->
		<div class="navbar-header">
			<!-- data-target indica sobre que div hago collapse -->
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-principal-collapse-1" aria-expanded="false">
				<span class="sr-only">Modo Navegaci&oacute;n</span>
				<!-- Cada icon-bar es una línea del boton de expandir (responsive) -->
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>

			<!-- Brand = marca / Logo -->
			<a class="navbar-brand" href="index.php?c=expedientes&a=view" title="<?php echo $this->vista->dataTituloApp; ?>">
				<img src="../../resources/images/logo_40x40.jpg" class="img-responsive" alt="<?php echo $this->vista->dataTituloApp; ?>">
			</a>
		</div>

		<!-- Agrupa los links de navegacion, formularios, etcetera para ocultar/mostrar con el collapse (responsive) -->
		<div class="collapse navbar-collapse" id="bs-navbar-principal-collapse-1">

			<?php if (!is_null($this->vista->dataUsuario)) { ?>

				<ul class="nav navbar-nav">
					<?php
					// Menu de administrador
					// Sólo usuarios de perfil Administrador (SGL-v2 y SGL-v1)
					if ($this->vista->data['usuario_es_administrador'] && $_SESSION['perfil2'] == 1) {
					?>
					<li class="dropdown">
                        <a href="#" class="dropdown-toggle navbar_titulo_menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;ARCHIVOS <span class="caret"></span></a>
                      	<ul class="dropdown-menu">
                          	<li><a href="index.php?c=categorias&a=view"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Categor&iacute;as</a></li>
                          	<li><a href="index.php?c=codestados&a=view"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Estados</a></li>
                          	<li><a href="index.php?c=lugares&a=view"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Lugares</a></li>
                          	<li><a href="index.php?c=codproyectos&a=view"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Proyectos</a></li>
                          	<li><a href="index.php?c=codtemas&a=view"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Temas</a></li>
                        </ul>
                    </li>
					<?php }?>
					<li class="dropdown">
                        <a href="#" class="dropdown-toggle navbar_titulo_menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-search"></span>&nbsp;CONSULTAS <span class="caret"></span></a>
                      	<ul class="dropdown-menu">
                          	<li><a href="index.php?c=expedientesbusquedaavanzada&a=view"><span class="glyphicon glyphicon-search"></span>&nbsp;B&uacute;squeda avanzada</a></li>
                          	<li><a href="index.php?c=expedientesbusquedaantecedente&a=view"><span class="glyphicon glyphicon-search"></span>&nbsp;B&uacute;squeda por antecedente</a></li>
                          	<?php
							// Sólo para Perfil 1 ó 2
							if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) {
							?>
                          		<li>
                          			<a href="index.php?c=verificardigitalizacion&a=view">
	                          			<span class="glyphicon glyphicon-search"></span>&nbsp;Verificar Digitalizacion D.E.
	                          		</a>
	                          	</li>
                          	<?php }?>
                        </ul>
                    </li>

                    <li class="dropdown">
                  		<a href="#" class="dropdown-toggle navbar_titulo_menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-file"></span>&nbsp;LISTADOS <span class="caret"></span></a>
                      	<ul class="dropdown-menu">
                          	<li><a href="index.php?c=listadoexpedientesencomision&a=view"><span class="glyphicon glyphicon-file"></span>&nbsp;Expedientes en Comisi&oacute;n</a></li>
                          	<li><a href="index.php?c=listadoordendeldia&a=view"><span class="glyphicon glyphicon-file"></span>&nbsp;Orden del D&iacute;a de Comisi&oacute;n</a></li>
                          	<li><a href="index.php?c=listadodetalledegiros&a=view"><span class="glyphicon glyphicon-file"></span>&nbsp;Con detalle de Giros</a></li>
							<?php
							// 03/09/2020 XXXX, todos pueden ver el listado de Informes
							// El Perfil 4 (Consulta Web) NO DEBE acceder al 'Listado de Informes'
							//if ($_SESSION['perfil2'] != 4) {
							?>
                              	<li><a href="index.php?c=listadoinformes&a=view"><span class="glyphicon glyphicon-file"></span>&nbsp;Informes</a></li>
							<?php //} ?>
                              	<li role="separator" class="divider"></li>
                              	<li><a href="index.php?c=listadoasuntosentrados&a=view"><span class="glyphicon glyphicon-file"></span>&nbsp;Asuntos Entrados</a></li>
							<?php
							// Sólo para Perfil 1 ó 2
							if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) {
							?>
                              	<li role="separator" class="divider"></li>
                              	<li><a href="index.php?c=listadoexpedientesparaexpurgo&a=view"><span class="glyphicon glyphicon-file"></span>&nbsp;Expedientes p/Expurgo</a></li>
                              	<li><a href="index.php?c=listadoexpedientesenprestamo&a=view"><span class="glyphicon glyphicon-file"></span>&nbsp;Expedientes en Pr&eacute;stamo</a></li>
                              	<li><a href="index.php?c=listadoexpedientessindocumentocargado&a=view"><span class="glyphicon glyphicon-file"></span>&nbsp;Expedientes sin documento cargado</a></li>
                              	<li><a href="index.php?c=listadoexpedientessindigitalizar&a=view"><span class="glyphicon glyphicon-file"></span>&nbsp;Expedientes sin Digitalizar</a></li>
							<?php }?>
                      	</ul>
                    </li>

					<?php
					// El Perfil 4 no debe acceder al submenú de TAREAS
					if ($_SESSION['perfil2'] != 4) {
					?>
                    <li id="menu_item_tareas" class="dropdown">
                        <a href="#" class="dropdown-toggle navbar_titulo_menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-tasks"></span>&nbsp;TAREAS <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                        	<li class=""><a href="<?= (in_array($_SESSION['perfil2'], [3, 5])) ? 'javascript:seleccionarDocumento()' : 'index.php?c=cargaproyectos&a=view'; ?>"><span class="glyphicon glyphicon-open"></span>&nbsp;Cargar Proyectos</a></li>
							<?php
							// Sólo usuarios de perfil Administrador (SGL-v2) y
							// Sólo usuarios de perfil Administrador y Supervisor (SGL-v1)
							if (($this->vista->data['usuario_es_administrador'] && $_SESSION['perfil2'] == 1) || $_SESSION['perfil2'] == 2) {
							?>
								<li>
									<a href="index.php?c=cargadigitalizaciones&a=view">
										<span class="glyphicon glyphicon-open"></span>&nbsp;Cargar Digitalizaciones
									</a>
								</li>
								<li>
									<a href="index.php?c=cargadigitalizacionesreservadas&a=view">
										<span class="glyphicon glyphicon-open"></span>&nbsp;Cargar Digitalizaciones (Art.11 Decreto 1404)
									</a>
								</li>
								<li>
									<a href="index.php?c=marcacomision&a=view">
										<span class="glyphicon glyphicon-pencil"></span>&nbsp;Marcar Comisiones
									</a>
								</li>
								<li>
									<a href="<?=URL_KRAKEN_BASE;?>administracion/abms/index.php?controlador=ordenes_comision&accion=listar">
										<span class="glyphicon glyphicon-tasks"></span>&nbsp;Orden del D&iacute;a Comisiones
									</a>
								</li>
							<?php }

							// Sólo usuarios de perfil Administrador (SGL-v2) y
							// Sólo usuarios de perfil Administrador y Supervisor (SGL-v1)
							if (($this->vista->data['usuario_es_administrador'] && $_SESSION['perfil2'] == 1) ||
								$_SESSION['perfil2'] == 2) {
							?>
	                            <li>
	                            	<a href="javascript:cargarGiros();">
	                            		<span class="glyphicon glyphicon-open"></span>&nbsp;Carga de Giros
	                            	</a>
	                            </li>
	                        <?php }

	                        // Sólo usuarios de perfil Administrador (SGL-v2) y
							// Sólo usuarios de perfil Administrador y Supervisor (SGL-v1)
							if (($this->vista->data['usuario_es_administrador'] && $_SESSION['perfil2'] == 1) || in_array($_SESSION['perfil2'], [2, 3, 5])) {
							?>
	                            <li role="separator" class="divider"></li>
	                            <li>
	                            	<a href="index.php?c=girospendientes&a=view">
	                            		<span class="glyphicon glyphicon-retweet <?= ($hayGirosPendientes) ? 'actuacion-alert-icon' : '';?>"></span>&nbsp;Giros pendientes
	                            	</a>
	                            </li>
	                        <?php }

							// Sólo usuarios de perfil Administrador, Supervisor, Concejales y Secretario
							if (in_array($_SESSION['perfil2'], [1, 2, 3, 5])) {
							?>
								<li role="separator" class="divider"></li>
	                            <li>
	                            	<a href="index.php?c=firmas&a=view">
		                            	<span class="glyphicon glyphicon-pencil <?= ($hayFirmasExpedienteElec) ? 'actuacion-alert-icon' : '';?>"></span>&nbsp;Firmas pendientes
		                            </a>
		                        </li>
	                            <li>
	                            	<a href="index.php?c=expedienteselecpend&a=view">
	                            		<span class="glyphicon glyphicon-registration-mark <?= ($hayRevisionesPendientes) ? 'actuacion-alert-icon' : '';?>"></span>&nbsp;Revisiones pendientes
	                            	</a>
	                            </li>
							<?php }

							// Sólo usuarios Supervisores (Mesa de Entrada) pueden visualizar todas las Firmas y Revisiones Pendientes
							if (in_array(NG::obtenerUsuarioActual()->id_usuario, SGL_ID_USUARIO_SUPERVISORES_MESA_ENTRADA)) {
							?>
								<li role="separator" class="divider"></li>
	                            <li>
	                            	<a href="index.php?c=firmaspendientessupervisor&a=view">
		                            	<span class="glyphicon glyphicon-pencil"></span>&nbsp;Ver firmas pendientes (para Supervisores)
		                            </a>
		                        </li>
		                        <li>
	                            	<a href="index.php?c=expedienteselecpendsupervisor&a=view">
		                            	<span class="glyphicon glyphicon-registration-mark"></span>&nbsp;Ver revisiones pendientes (para Supervisores)
		                            </a>
		                        </li>
							<?php } ?>
                        </ul>
                    </li>
				<?php }?>

				<?php
				// Sólo para Perfil 1 ó 2
				if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) {
				?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle navbar_titulo_menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-file"></span>&nbsp;PR&Eacute;STAMOS <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="index.php?c=prestamos&a=viewgeneral"><span class="glyphicon glyphicon-file"></span>&nbsp;Listado de Pr&eacute;stamos</a></li>
                            <li><a href="index.php?c=solicitudes&a=view"><span class="glyphicon glyphicon-file"></span>&nbsp;Listado de Solicitudes a Entes Externos</a></li>
                        </ul>
                    </li>
				<?php }?>

				<?php
				// Sólo para Perfil 1 (Admin), 2 (Supervisor) ó 3 (Consulta/Concejales)
				if (in_array($_SESSION['perfil2'], [1, 2, 3])) {
				?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle navbar_titulo_menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-pencil"></span>&nbsp;FIRMADOR ONLINE <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:iniciarActuacion('documento_subir_firmar_pdf', {});"><span class="glyphicon glyphicon-open"></span>&nbsp;Subir y firmar un documento</a></li>
	                        <li><a href="javascript:iniciarActuacion('documento_componer_firmar_pdf', {});"><span class="glyphicon glyphicon-pencil"></span>&nbsp;Componer y firmar un documento</a></li>
                        </ul>
                    </li>
				<?php }?>

					<?php if (SGL_PARA_PRUEBAS) { ?>
					<li class="dropdown">
						<br>
						<span class="resaltado-alerta padding_10">
							<span class="glyphicon glyphicon-info-sign"></span>&nbsp;PARA PRUEBAS
						</span>
					</li>
					<?php }?>
				</ul>

				<?php
				// Se consulta a qué sistemas tiene acceso y el perfil para cada uno
				$accesos = NG::seguridad()->obtenerAccesosUsuario($this->vista->dataUsuario->id_usuario);
				$cant_accesos = (isset($accesos)) ? count($accesos) : 0;
				?>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle tamanio-texto-small" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
							<span class="glyphicon glyphicon-user"></span>&nbsp;<?=$_SESSION['nombre_usuario'];?><span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<?php
							$tiene_permiso_biblioteca = false;

							for ($a = 0; $a < $cant_accesos; $a++) {
								if ($_SESSION['accesos'][$a]['id_sistema'] == 4) {
									$tiene_permiso_biblioteca = true;
									break;
								}
							}

							// Si el usuario tiene más de un acceso
							for ($a = 0; $a < $cant_accesos; $a++) {

								// 1 = Sistema de Administración
								if ($_SESSION['accesos'][$a]['id_sistema'] == 1) {?>
									<li>
										<a href="<?=URL_KRAKEN_BASE;?>administracion/abms/">
											<span class="glyphicon glyphicon-cog"></span>&nbsp;Administraci&oacute;n
										</a>
									</li>
								<?php }
								// 3 = Sistema de Personal
								if ($_SESSION['accesos'][$a]['id_sistema'] == 3) {?>
									<li>
										<a href="<?=URL_KRAKEN_BASE;?>personal/index.php">
											<span class="glyphicon glyphicon-user"></span>&nbsp;Personal
										</a>
									</li>
								<?php
                                } // 4 = Sistema de Biblioteca
                                if ($_SESSION['accesos'][$a]['id_sistema'] == 4) { ?>
                                    <li>
                                        <a  target="_blank"
                                            href="<?=URL_KRAKEN_BASE;?>administracion/abms/index.php?controlador=usuarios&accion=ingresarAlSistemaDeBiblioteca">
                                            <span class="glyphicon glyphicon-tasks"></span>&nbsp;Biblioteca
                                        </a>
                                    </li>
                                <?php
                                } // 5 = Sistema de Inventario
                                if ($_SESSION['accesos'][$a]['id_sistema'] == 5) { ?>
                                    <li>
                                        <a  target="_blank"
                                            href="<?=URL_KRAKEN_BASE;?>administracion/abms/index.php?controlador=usuarios&accion=ingresarAlSistemaDeInventario">
                                            <span class="glyphicon glyphicon-print"></span>&nbsp;Inventario
                                        </a>
                                    </li>
                                <?php
                            	}
                            	// 6 = Sistema de Defensoria
                                if ($_SESSION['accesos'][$a]['id_sistema'] == 6) {?>
                                    <li>
                                        <a href="<?=URL_KRAKEN_BASE;?>defensoria/abms/"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;Defensor&iacute;a</a>
                                    </li>
                                <?php
                                }
							}

							// Si NO tiene acceso al Sistema de Biblioteca (id=4),
							// se lo dirige al Dashboard de consultas
							if ( ! $tiene_permiso_biblioteca) { ?>
								<li>
									<a href="http://biblioteca.concejomdp.gov.ar/dashboard/dbselector" target="_blank">
										<span class="glyphicon glyphicon-book"></span>&nbsp;Biblioteca
									</a>
								</li>
							<?php } ?>
							<li role="separator" class="divider"></li>
							<li>
								<a href="index.php?c=login&a=logout">
									<span class="glyphicon glyphicon-log-out"></span>&nbsp;Cerrar sesi&oacute;n
								</a>
							</li>
						</ul>
					</li>
				</ul>

				<?php
				// Sólo usuarios de perfil Administrador, Supervisor, Concejales y Secretario HCD
				if (in_array($_SESSION['perfil2'], [1, 2, 3, 5])) {
				?>
					<ul class="nav navbar-nav navbar-right">
						<?php
							if ($hayGirosPendientes) {
						?>
							<!-- Giros pendientes -->
							<li class="dropdown">
								<a href="index.php?c=girospendientes&a=view" class="actuacion-alert-icon" title="Hay giros a comisiones pendientes de confirmación.">
									<span class="glyphicon glyphicon-retweet"></span>
								</a>
							</li>
						<?php } // if ($hayGirosPendientes) ?>

						<?php
							if ($hayFirmasExpedienteElec) {
						?>
							<!-- Firmas pendientes -->
							<li class="dropdown">
								<a href="index.php?c=firmas&a=view" class="actuacion-alert-icon" title="Algunos documentos requieren su firma.">
									<span class="glyphicon glyphicon-pencil"></span>
								</a>
							</li>
						<?php } // if ($hayFirmasExpedienteElec) ?>

						<?php
							if ($hayRevisionesPendientes) {
						?>
							<!-- Revisiones pendientes -->
							<li class="dropdown">
								<a href="index.php?c=expedienteselecpend&a=view" class="actuacion-alert-icon" title="Algunos documentos requieren su revisión.">
									<span class="glyphicon glyphicon-registration-mark"></span>
								</a>
							</li>
						<?php } // if ($hayRevisionesPendientes) ?>

						<?php if (SessionController::get()->existe('actuacion')) { ?>
							<!-- Actuación pendiente -->
							<li class="dropdown">
								<a href="index.php?c=actuaciones&a=retomar" class="actuacion-alert-icon" title="Actuación pendiente de resolución.">
									<span class="glyphicon glyphicon-bell"></span>
								</a>
							</li>
						<?php } // if existe actuacion en sesion ?>
					</ul>
				<?php } ?>

			<?php } // if de la existencia del usuario ?>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
<!-- /Navbar -->
