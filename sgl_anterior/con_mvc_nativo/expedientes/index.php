<?php
// Script de control de variables de sesion
require_once $_SERVER['DOCUMENT_ROOT'] . '/sgl/librerias/control_sesion.php';

// SI CADUCÓ LA SESION
if (!isset($_SESSION['usuario'])) {
	header("Location: ../index.php?sesion_caducada=true"); // SE VUELVE AL LOGIN
	exit();
} else {
	$_SESSION['agregado_previamente'] = false;
	$_SESSION['cargado_previamente'] = false;
	$_SESSION['ruta_del_sistema'] = "../expedientes";
	// 26/05/2015, XXXX
	$_SESSION['ingreso_expedientes'] = true;
	?>
	<!DOCTYPE html>
	<html lang="es" >
		<head>
			<meta charset="UTF-8">
			<meta http-equiv="Pragma" content="no-cache" >
			<!-- Se agrega el icono favorito de la aplicacion -->
			<link rel="shortcut icon" href="imagenes/icono_sistema.ico" type="image/x-icon"/>
			<title>SGL :: Expedientes</title>

			<!-- CSS general -->
			<link rel="stylesheet" type="text/css" href="css/css_general.css" />

			<!-- CSS para las Ventanas Modales -->
			<link rel="stylesheet" href="css/moodalbox_comprimido.css" />

			<!-- CSS del calendario -->
			<link rel="stylesheet" type="text/css" href="css/calendario_comprimido.css">

			<!-- LIBRERIA Mootools -->
			<script src="js/mootools.js" type="text/javascript" ></script>

			<!-- JAVASCRIPTS PARA EL CALENDARIO -->
			<script src="js/zapatec.js" type="text/javascript" ></script>
			<script src="js/calendario.js" type="text/javascript" ></script>
			<script src="js/lang/calendar-es.js" type="text/javascript" ></script>

			<!-- FUNCIONES VARIAS JAVASCRIPT -->
			<script src="js/funciones_varias.js" type="text/javascript" ></script>

			<!-- JAVASCRIPT PARA EL EFECTO MODAL -->
			<script src="js/moodalbox.v1.2.full.js" type="text/javascript" ></script>
		</head>
		<body id="bodyExpedientes" onKeyUp="pulsar(event)">
			<div id="p_gral" class="p_gral">
				<div id="header" class="degradado">
					<!-- MENU PRINCIPAL SUPERIOR -->
					<div id="menu_ppal">
						<ul style="border-left:0;">
							<?php
// SOLO EL PERFIL 1 ACCEDE AL SUBMENU 'ARCHIVOS'
	if ($_SESSION['perfil4'] == 1) {
		?>
								<li id="item_archivos_menu_gral"><a href="#">Archivos</a>
									<ul>
										<li class=""><a href="javascript:refrescar('abms/index.php?controlador=categorias&accion=listar', 'contenidoAjaxPrincipal');">Categor&iacute;as</a></li>
										<li class=""><a href="javascript:refrescar('abms/index.php?controlador=codestados&accion=listar', 'contenidoAjaxPrincipal');">Estados</a></li>
										<li class=""><a href="javascript:refrescar('abms/index.php?controlador=lugares&accion=listar', 'contenidoAjaxPrincipal');">Lugares</a></li>
										<li class=""><a href="javascript:refrescar('abms/index.php?controlador=codproyectos&accion=listar', 'contenidoAjaxPrincipal');">Proyectos</a></li>
										<li class=""><a href="javascript:refrescar('abms/index.php?controlador=codtemas&accion=listar', 'contenidoAjaxPrincipal');">Temas</a></li>
									</ul>
								</li>
							<?php
}
	?>
							<li id="item_consultas_menu_gral"><a href="#">Consultas</a>
								<ul style="width:200px;">
									<li><a href="javascript:refrescar('consultas/index.php?controlador=consulta_gral&accion=listar_principal', 'capaVentana');" title="Consulta Parametrizada">General</a></li>
									<li><a href="javascript:refrescar('consultas/index.php?controlador=sancionado_promulgado&accion=listar', 'capaVentana');" title="Sancionados o Promulgados">Sancionados o Promulgados</a></li>
									<li><a href="javascript:refrescar('consultas/index.php?controlador=por_antecedente&accion=por_antecedente', 'capaVentana');" title="B&uacute;squeda por Antecedente">por Antecedente</a></li>
								</ul>
							</li>
							<li id="item_listados_menu_gral"><a href="#">Listados</a>
								<ul style="width:200px;">
									<li class=""><a href="javascript:refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_tipo_listado=exped_en_comision', 'capaVentana');" title="Expedientes en Comisi&oacute;n">Expedientes en Comisi&oacute;n</a></li>
									<li class=""><a href="javascript:refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_tipo_listado=orden_del_dia', 'capaVentana');" title="Orden del d&iacute;a de Comisi&oacute;n">Orden del d&iacute;a de Comisi&oacute;n</a></li>
									<li class=""><a href="javascript:refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_tipo_listado=detalle_giros', 'capaVentana');" title="con Detalle de Giros">con Detalle de Giros</a></li>
									<?php
// 03/09/2020 XXXX, todos pueden ver el listado de Informes
	// EL PERFIL 4 NO DEBE ACCEDER AL 'Listado de Informes'
	//if ( $_SESSION['perfil4'] != 4 ) {
	?>
										<li class=""><a href="javascript:refrescar('listados/index.php?controlador=exped_en_comision&accion=listarInformes&l_tipo_listado=informes', 'capaVentana');" title="Listado de Informes">Listado de Informes</a></li>
									<?php
//}
	?>
									<li class="borde_blanco"><a href="javascript:refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_tipo_listado=asuntos_entrados', 'capaVentana');" title="Asuntos Entrados">Asuntos Entrados</a></li>
									<?php
// SOLO PARA PERFIL 1 Ó PERFIL 2
	if ($_SESSION['perfil4'] == 1 || $_SESSION['perfil4'] == 2) {
		?>
										<li class="borde_blanco"><a href="javascript:refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_tipo_listado=expurgo', 'capaVentana');" title="Expedientes p/Expurgo">Expedientes p/Expurgo</a></li>
										<li class="borde_blanco"><a href="javascript:refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_tipo_listado=exped_en_prestamo', 'capaVentana');" title="Expedientes en Pr&eacute;stamo">Expedientes en Pr&eacute;stamo</a></li>
										<li class="borde_blanco"><a href="javascript:refrescar('listados/index.php?controlador=exped_en_comision&accion=listar_expedientes_sin_cargar', 'capaVentana');" title="Expedientes sin documento cargado">Expedientes sin documento cargado</a></li>
										<li class="borde_blanco"><a href="javascript:refrescar('listados/index.php?controlador=exped_en_comision&accion=listar_expedientes_sin_digitalizar', 'capaVentana');" title="Expedientes sin Digitalizar">Expedientes sin Digitalizar</a></li>
									<?php
}
	?>
								</ul>
							</li>
							<?php
// EL PERFIL 4 NO DEBE ACCEDER AL SUBMENU DE 'TAREAS'
	if ($_SESSION['perfil4'] != 4) {
		?>
								<li id="item_tareas_menu_gral"><a href="#">Tareas</a>
									<ul style="width:200px;">
										<!-- Carga de Proyectos -->
										<li class=""><a href="javascript:refrescar('tareas/index.php?controlador=cargar_proyecto&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value, 'capaVentana');" title="Carga Documentos Originales">Cargar Proyectos</a></li>
										<?php
// SOLO PERFIL 1 Y 2 PUEDEN Marcar Comisiones Y Cargar Giros
		if ($_SESSION['perfil4'] == 1 || $_SESSION['perfil4'] == 2) {
			?>
											<!-- Cargar Digitalizaciones -->
											<li class=""><a href="javascript:refrescar('tareas/index.php?controlador=cargar_digitalizacion&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value, 'capaVentana');" title="Cargar Digitalizaciones">Cargar Digitalizaciones</a></li>

											<li class="borde_blanco"><a href="javascript:refrescar('tareas/index.php?controlador=marca_comision&accion=listar', 'capaVentana');" title="Marcar Comisiones">Marcar Comisiones</a></li>
											<li class=""><a href="javascript:refrescar('tareas/index.php?controlador=carga_giros&accion=verificarExistencia&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value, 'contenidoAjaxPrincipal');">Carga de Giros</a></li>
											<li class=""><a href="javascript:refrescar('tareas/index.php?controlador=verificar_digitalizacion&accion=verificar_digitalizacion', 'capaVentana');" title="Verificar Digitalizaci&oacute;n D.E.">Verificar Digitalizaci&oacute;n D.E.</a></li>
										<?php
}
		?>
									</ul>
								</li>
							<?php
}

	// SOLO PARA PERFIL 1 Ó PERFIL 2
	if ($_SESSION['perfil4'] == 1 || $_SESSION['perfil4'] == 2) {
		?>
								<li id="item_prestamos_menu_gral"><a href="#">Pr&eacute;stamos</a>
									<ul style="width:200px;">
										<!-- Se muestran TODOS los Préstamos (del HCD y de Entes Externos) -->
										<li class="">
											<a href="javascript:refrescar('abms/index.php?controlador=prestamos&accion=mostrarGrillaGeneral', 'contenidoAjaxPrincipal');" title="Listado de Pr&eacute;stamos">Listado de Pr&eacute;stamos</a>
										</li>
										<!-- Se muestran las Solicitudes al E.E. -->
										<li class="">
											<a href="javascript:refrescar('abms/index.php?controlador=solicitud_expediente_externo&accion=listar', 'contenidoAjaxPrincipal');" title="Listado de Solicitudes al Ente Externo">Listado de Solicitudes al E.E.</a>
										</li>
									</ul>
								</li>
							<?php
}
	?>
						</ul>
					</div>
				</div>

				<div id="p_menu_ocultado" class="degradado"></div>

				<div id="precarga_principal" style="display:none"></div>
				<div id="contenidoAjaxPrincipal">
					<!-- AQUI SE MUESTRA EL CONTENIDO -->
				</div>
				<div class="p_borde_superior"></div>

				<!--   DATOS DEL PIE DEL SISTEMA   -->
				<div class="p_datos_sistema p_texto_datos_y_pie">
					<div class="p_datos_sistema_pc"><?php echo 'PC: ' . $_SESSION['netpcname']; ?></div>
					<div class="p_datos_sistema_usr"><?php echo 'USR. ' . $_SESSION['usuario']; ?></div>
					<div class="p_datos_sistema_fecha_hora"><?php echo date("d/m/Y"); ?>&nbsp;&nbsp;<span id="reloj"></span></div>
					<div class="p_datos_sistema_acerca"><a href="acerca_de.php" rel="moodalbox 375 145" title="Desarrollado por Inform&aacute;tica H.C.D.">Desarrollado por Inform&aacute;tica H.C.D.</a></div>
				</div>
			</div>

			<input type="hidden" name="anio" id="anio" value="<?php echo (isset($_GET['anio'])) ? $_GET['anio'] : ''; ?>" />
			<input type="hidden" name="tipo" id="tipo" value="<?php echo (isset($_GET['tipo'])) ? $_GET['tipo'] : ''; ?>" />
			<input type="hidden" name="numero" id="numero" value="<?php echo (isset($_GET['numero'])) ? $_GET['numero'] : ''; ?>" />
			<input type="hidden" name="cuerpo" id="cuerpo" value="<?php echo (isset($_GET['cuerpo'])) ? $_GET['cuerpo'] : ''; ?>" />
			<input type="hidden" name="alcance" id="alcance" value="<?php echo (isset($_GET['alcance'])) ? $_GET['alcance'] : ''; ?>" />
			<input type="hidden" name="sentido" id="sentido" value="<?php echo (isset($_GET['sentido'])) ? $_GET['sentido'] : ''; ?>" />

			<input type="hidden" name="mensaje" id="mensaje" value="<?php echo (isset($_GET['mensaje'])) ? $_GET['mensaje'] : $_SESSION['mensaje']; ?>" />
			<input type="hidden" name="tipo_mensaje" id="tipo_mensaje" value="<?php echo (isset($_GET['tipo_mensaje'])) ? $_GET['tipo_mensaje'] : $_SESSION['tipo_mensaje']; ?>" />

			<input type="hidden" name="perfil_para_expedientes" id="perfil_para_expedientes" value="<?php echo $_SESSION['perfil4']; ?>" />
			<input type="hidden" name="por_boton_buscar" id="por_boton_buscar" value="<?php echo (isset($_GET['por_boton_buscar'])) ? $_GET['por_boton_buscar'] : 'no'; ?>" />

			<div id="capaVentana"></div>
			<div id="capaFondo"></div>

			<script type="text/javascript">
				window.addEvent('domready', function() {
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

					// Cada 30 segundos se controla el estado de la sesión, nada más
					tiempo_intervalo = setInterval(controlarSesion, duracion_control);

					// Cada 1 segundo se refrezca la Hora
					setInterval(muestraReloj, 1000);

					// SE VISUALIZA EL LISTADO DE LOS EXPEDIENTES
					refrescar('abms/index.php?controlador=expedientes&accion=listar&anio='+$('anio').value+'&tipo='+$('tipo').value+'&numero='+$('numero').value+'&cuerpo='+$('cuerpo').value+'&alcance='+$('alcance').value+'&sentido='+$('sentido').value+'&mensaje='+$('mensaje').value+'&tipo_mensaje='+$('tipo_mensaje').value+'&por_boton_buscar='+$('por_boton_buscar').value, 'contenidoAjaxPrincipal');
				});
			</script>
		</body>
	</html>
<?php
}
?>
