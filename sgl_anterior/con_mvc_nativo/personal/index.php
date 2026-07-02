<?php
// Script de control de variables de sesion
require_once $_SERVER['DOCUMENT_ROOT'] . '/sgl/librerias/control_sesion.php';

// SI CADUCÓ LA SESION
if (!isset($_SESSION['usuario'])) {
	header("Location: ../index.php?sesion_caducada=true"); // SE VUELVE AL LOGIN
	exit();
} else {
	// 26/05/2015, XXXX
	$_SESSION['ingreso_personal'] = true;

	$_SESSION['id_sistema_actual'] = 3;
	?>
	<!DOCTYPE html>
	<html lang="es" >
		<head>
			<meta charset="UTF-8">
			<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9">

			<title>SGL :: Personal</title>
			<link rel="shortcut icon" href="imagenes/icono_sistema.ico" type="image/x-icon"/>

			<script type="text/javascript" src="js/mootools.js" ></script>
			<script type="text/javascript" src="js/funciones_varias.js" ></script>

			<!-- CSS GENERAL -->
			<link rel="stylesheet" href="css/general.css" />

			<!-- JS para las Ventanas Modales -->
			<script type="text/javascript" src="js/moodalbox.v1.2.full.js"></script>
			<!-- Estilos CSS para las Ventanas Modales -->
			<link rel="stylesheet" href="css/moodalbox_comprimido.css" />

			<!-- JS para los calendarios -->
			<script type="text/javascript" src="js/zapatec.js" ></script>
			<script type="text/javascript" src="js/calendario.js" ></script>
			<script type="text/javascript" src="js/lang/calendar-es.js" ></script>
			<!-- El CSS principal del calendario -->
			<link rel="stylesheet" href="css/calendario.css" >
		</head>
		<body id="body" onkeypress="pulsar(event)" >

			<script>
				// SE MODIFICAN ALGUNOS VALORES CSS SEGUN EL NAVEGADOR UTILIZADO
				var sistema_operativo = navigator.appVersion.toLowerCase();
				var navegador = navigator.userAgent;

				// SI SE UTILIZA WINDOWS
				if ( sistema_operativo.indexOf('windows') != -1 )
				{
					if ( navegador.indexOf('Firefox') !=-1 )// SI SE UTILIZA FIREFOX
					{
						document.write('<link rel="stylesheet" href="css/adaptado_para_windows.css" />');
					}
					else if ( navegador.indexOf('Chrome') !=-1 )// SI SE UTILIZA CHROME
					{
						document.write('<link rel="stylesheet" href="css/adaptado_para_chrome.css" />');
					}
					else if ( navegador.indexOf('MSIE') !=-1 ) // SI SE UTILIZA Internet Explorer
					{
						document.write('<link rel="stylesheet" type="text/css" href="css/adaptado_para_ie.css" />');
					}
				}
			</script>

			<div id="p_gral" class="p_gral">

				<div id="header" class="degradado">

					<div id="menu_ppal" style="width:100%;">
						<ul>
							<?php
if ($_SESSION['perfil3'] != 3) {
		?>
								<li class=""><a href="#">Informes</a>
									<ul style="width: 200px;">
										<li class=""><a href="javascript:refrescar('informes/index.php?controlador=informes&accion=listar', 'capaVentana');" title="Consulta General">Consulta de Empleados</a></li>
										<li class=""><a href="javascript:refrescar('informes/index.php?controlador=informes&accion=listarPersonalBloques', 'capaVentana');" title="Personal Planta Pol&iacute;tica">Personal Planta Pol&iacute;tica</a></li>
										<li class=""><a href="javascript:refrescar('informes/index.php?controlador=informes&accion=listarPersonalPlantaPermanente', 'capaVentana');" title="Personal Planta Permanente">Personal Planta Permanente</a></li>
										<li class="borde_separador_menu"><a href="javascript:refrescar('informes/index.php?controlador=informes&accion=listarParaSitioMGP', 'capaVentana');" title="Para sitio MGP">Para sitio MGP</a></li>
										<li class="borde_separador_menu"><a href="javascript:refrescar('informes/index.php?controlador=informes&accion=listarParaLiquidaciones', 'capaVentana');" title="Listado para Liquidaciones">Listado para Liquidaciones</a></li>
										<li class=""><a href="javascript:refrescar('informes/index.php?controlador=informes&accion=listarParaCertificado', 'capaVentana');" title="Certificado Mensual">Certificado Mensual</a></li>
										<li class=""><a href="javascript:refrescar('informes/index.php?controlador=informes&accion=listarPorConcejal', 'capaVentana');" title="Listado por Concejal">Listado por Concejal</a></li>
										<li class=""><a href="javascript:refrescar('informes/index.php?controlador=credenciales&accion=elegirConcejalesParaCredenciales', 'capaVentana');" title="Generar Credenciales para Concejales">Generar Credenciales para Concejales</a></li>
										<li class=""><a href="javascript:refrescar('informes/index.php?controlador=credenciales&accion=elegirDefensoresPuebloParaCredenciales', 'capaVentana');" title="Generar Credenciales para Defensores del Pueblo">Generar Credenciales para Defensores del Pueblo</a></li>
									</ul>
								</li>
								<li class=""><a href="#">Tareas</a>
									<ul style="width: 320px;">
										<li class=""><a href="javascript:refrescar('informes/index.php?controlador=informes&accion=reasignarDependientes', 'capaVentana');" title="Reasignar personal dependiente a otro Concejal">Reasignar personal dependiente entre Concejales</a></li>
									</ul>
								</li>
							<?php
}
	?>
							<li class=""><a href="#">Archivos</a>
								<ul style="width: 120px;">
									<li class=""><a href="javascript:refrescar('abms/index.php?controlador=codareas&accion=listar', 'contenidoAjaxPrincipal');">&Aacute;reas</a></li>
									<li class=""><a href="javascript:refrescar('abms/index.php?controlador=codcargos&accion=listar', 'contenidoAjaxPrincipal');">Cargos</a></li>
								</ul>
							</li>
						</ul>
						<ul style="float:right;margin-right:70px">
							<li><a href="#" style="text-transform: capitalize;"><?=$_SESSION['nombre_usuario'];?></a>
								<ul style="width: 160px;">
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
												<a href="../administracion/abms/">Administraci&oacute;n</a>
											</li>
										<?php }
										// 2 = Sistema de Expedientes
										if ($_SESSION['accesos'][$a]['id_sistema'] == 2) {?>
											<li>
												<a href="../html/backend/index.php?c=expedientes&a=view">Expedientes</a>
											</li>
										<?php
										} // 4 = Sistema de Biblioteca
		                                if ($_SESSION['accesos'][$a]['id_sistema'] == 4) { ?>
		                                    <li>
		                                        <a  target="_blank"
		                                            href="../administracion/abms/index.php?controlador=usuarios&accion=ingresarAlSistemaDeBiblioteca">Biblioteca</a>
		                                    </li>
		                                <?php
		                                } // 5 = Sistema de Inventario
		                                if ($_SESSION['accesos'][$a]['id_sistema'] == 5) { ?>
		                                    <li>
		                                        <a  target="_blank"
		                                            href="../administracion/abms/index.php?controlador=usuarios&accion=ingresarAlSistemaDeInventario">Inventario</a>
		                                    </li>
		                                <?php
		                            	}
		                            	// 6 = Sistema de Defensoria
		                                if ($_SESSION['accesos'][$a]['id_sistema'] == 6) {?>
		                                    <li>
		                                        <a href="../defensoria/abms/">Defensor&iacute;a</a>
		                                    </li>
		                                <?php
		                                }
									}

									// Si NO tiene acceso al Sistema de Biblioteca (id=4), se lo dirige al Dashboard de consultas
									if ( ! $tiene_permiso_biblioteca) { ?>
		                                <li>
		                                    <a href="http://biblioteca.concejomdp.gov.ar/dashboard/dbselector" target="_blank">
		                                       Biblioteca
		                                    </a>
		                                </li>
		                            <?php } ?>

									<li class="borde_separador_menu">
										<a href="../html/backend/index.php?c=login&a=logout">Cerrar sesi&oacute;n</a>
									</li>
								</ul>
							</li>
						</ul>
					</div>

				</div>
				<div id="p_menu_ocultado"></div>

				<div id="precarga_principal"></div>
				<div id="contenidoAjaxPrincipal">
					<!-- AQUI SE MUESTRA EL CONTENIDO -->
				</div>

				<!--   DATOS DEL PIE DEL SISTEMA   -->
				<div class="p_edicion_datos_borde_superior"></div>
				<div class="p_datos_sistema p_texto_datos_y_pie">
					<div class="p_datos_sistema_pc"><?php echo 'PC: ' . $_SESSION['netpcname']; ?></div>
					<div class="p_datos_sistema_usr"><?php echo 'USR. ' . $_SESSION['usuario']; ?></div>
					<div class="p_datos_sistema_fecha_hora"><?php echo date("d/m/Y"); ?>&nbsp;&nbsp;<span id="reloj"></span> hs.</div>
					<div class="p_datos_sistema_acerca">
						<a href="acerca_de.php" rel="moodalbox 375 145" title="Desarrollado por Inform&aacute;tica H.C.D.">Desarrollado por Inform&aacute;tica H.C.D.</a>
					</div>
				</div>

			</div>

			<input  type="hidden" name="mensaje" id="mensaje"
					value="<?= (isset($_SESSION['personal']['mensaje'])) ? $_SESSION['personal']['mensaje'] : ''; ?>" />

			<input  type="hidden" name="tipo_mensaje" id="tipo_mensaje"
					value="<?= (isset($_SESSION['personal']['tipo_mensaje'])) ? $_SESSION['personal']['tipo_mensaje'] : ''; ?>" />

			<input  type="hidden" name="se_sigue_editando" id="se_sigue_editando"
					value="<?= (isset($_SESSION['personal']['se_sigue_editando'])) ? $_SESSION['personal']['se_sigue_editando'] : 'no'; ?>" />

			<input  type="hidden" name="seguir_en_solapa_ddjj" id="seguir_en_solapa_ddjj"
					value="<?= (isset($_SESSION['personal']['seguir_en_solapa_ddjj'])) ? $_SESSION['personal']['seguir_en_solapa_ddjj'] : 'no'; ?>" />

			<input  type="hidden" name="seguir_en_solapa_legajos_digitalizados" id="seguir_en_solapa_legajos_digitalizados"
					value="<?= (isset($_SESSION['personal']['seguir_en_solapa_legajos_digitalizados'])) ? $_SESSION['personal']['seguir_en_solapa_legajos_digitalizados'] : 'no'; ?>" />

			<div id="capaVentana"></div>

			<div id="capaFondo"></div>

			<?php
			// SE ELIMINAN VALORES ESPECÍFICOS DE LA SESION
			unset($_SESSION['personal']['mensaje']);
			unset($_SESSION['personal']['tipo_mensaje']);
			?>
			<script>
				window.addEvent('domready', function() {
					var accion;
					var tiempo_intervalo;
					var duracion_control = 30000; // 30 segundos

					// Se verifica el estado de la sesión
					function controlarSesion() {
					    var miJSON = new Json.Remote('/sgl/librerias/estado_sesion.php', {
					        onComplete: function(objeto) {
					        	// Si caducó la sesion
					            if (objeto.sesion_caducada === 'SI')
					                // Se redirecciona al Login
					                location.href = "index.php?sesion_caducada=true";
					        }
					    });
					    miJSON.send();
					}

					// Cada 30 segundos se controla el estado de la sesión
					tiempo_intervalo = setInterval(controlarSesion, duracion_control);

					// Cada 1 segundo refrezca la Hora
					setInterval("muestraReloj()", 1000);

					// Si se vuelve de haber cargado una DDJJ
					if ( $('seguir_en_solapa_ddjj').value == 'si' ) {
						// Para seguir en la solapa de DDJJ
						accion = 'seguirEditandoDDJJ';

					} // Si se vuelve de haber cargado un Legajo Digitalizado
					else if ( $('seguir_en_solapa_legajos_digitalizados').value == 'si' ) {
						// Para seguir en la solapa de DDJJ
						accion = 'seguirEditandoLegajosDigitalizados';
					}
					else {
						// Se establece la accion a ejecutar si se desea seguir editando un legajo o visualizar el listado principal
						accion = ($('se_sigue_editando').value == 'si') ? 'seguirEditando' : 'listar';
					}
					// Se muestra el listado principal o el formulario de edición de un legajo luego de haber cargado su foto
					refrescar('abms/index.php?controlador=personal&accion='+accion+'&mensaje='+$('mensaje').value+'&tipo_mensaje='+$('tipo_mensaje').value, 'contenidoAjaxPrincipal');
				});
			</script>
		</body>
	</html>
<?php
}
?>
