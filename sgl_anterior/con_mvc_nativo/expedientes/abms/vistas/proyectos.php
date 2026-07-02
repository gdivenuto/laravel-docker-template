<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaProyectos extends VistaBase
{
	private $controlador;
	private $formulario;

	public function __construct()
	{
	    $this->controlador = 'proyectos';
	    $this->formulario  = 'formProyectos';
	}

	public function listar($datos = '', $mensaje = '', $tipo_mensaje = '', $filtro= '')
	{
		// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);
	?>
		<script>
		    $('header').setStyle('display','block');
		    $('p_menu_ocultado').setStyle('display','none');
		    $("capaFondo").setStyle('visibility','hidden');
			$("capaVentana").setStyle('visibility','hidden');
		</script>

	    <div class="p_borde_superior"></div>

		<!-- BARRA DE NAVEGACION SUPERIOR -->
		<div id="p_barra_navegacion" class="p_barra_navegacion">

			<div class="p_bnav_contenedor_3bt p_bnav_sin_borde_izquierdo">
				<a id="btBG" href="javascript:refrescar('consultas/index.php?controlador=consulta_gral&accion=listar_principal', 'capaVentana');" title="Consulta Parametrizada">
					<img id="imgBG" src="imagenes/barra/buscar_16x16.gif" width="16" height="16" />
				</a>
				<a id="btBA" title="Buscar por Antecedente" href="javascript:refrescar('consultas/index.php?controlador=por_antecedente&accion=por_antecedente', 'capaVentana');" title="B&uacute;squeda por Antecedente">
					<img id="imgBA" src="imagenes/barra/buscar_ant_16x16.gif" width="16" height="16" />
				</a>
				<?php
				// SOLO PERFIL 1 Y 2 PUEDEN VERIFICAR LA DIGITALIZACION DE DOCUMENTOS DEL D.E.
				if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 )
				{
				?>
					<a href="javascript:refrescar('tareas/index.php?controlador=verificar_digitalizacion&accion=verificar_digitalizacion', 'capaVentana');" title="Verificar Digitalizaci&oacute;n D.E.">
						<img id="imgBA" src="imagenes/barra/verificar_digitalizacion_16x16.gif" width="16" height="16" />
					</a>
				<?php
				}
				?>
			</div>

			<div class="p_bnav_contenedor_3bt">
				<?php
				//SOLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
				if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2)
				{
				?>
					<a id="btAgregar" style="width:30px;" title="Agregar Registro" href="javascript:refrescar('abms/index.php?controlador=proyectos&accion=agregar&anio=<?php echo $filtro['anio']; ?>&tipo=<?php echo $filtro['tipo']; ?>&numero=<?php echo $filtro['numero']; ?>&cuerpo=<?php echo $filtro['cuerpo']; ?>&alcance=<?php echo $filtro['alcance']; ?>&ingreso_individual=true', 'contenidoAjaxPrincipal');">
						<img id="imgAgregar" src="imagenes/barra/add_16x16.gif" width="16" height="16" />
					</a>
				<?php
				}
				else
				{
				?>
					<a id="btAgregar" style="width:30px;" title="Funci&oacute;n deshabilitada" href="#">
						<img id="imgAgregar" src="imagenes/barra/add_gris_16x16.gif" width="16" height="16" />
					</a>
				<?php
				}
				?>
			</div>
			<div class="p_bnav_contenedor_2bt">
				<a id="btGuardar" title="Aceptar los cambios realizados" href="#">
					<img id="imgGuardar" src="imagenes/barra/ok_gris_16x16.gif" width="16" height="16" />
				</a>
				<a id="btCancelar" title="Cancelar los cambios realizados" href="#">
					<img id="imgCancelar" src="imagenes/barra/error_gris_16x16.gif" width="16" height="16" />
				</a>
			</div>
			<div class="p_bnav_contenedor_2bt p_bnav_sin_borde_derecho">
				<a id="btPrint" title="Imprimir el registro seleccionado" href="#">
					<img id="imgPrint" src="imagenes/barra/print_16x16.gif" width="16" height="16" />
				</a>
				<?php
				// SOLO EL PERFIL 1 PUEDE ELIMINAR
				if ( $_SESSION['perfil2'] != 4 )
				{
				?>
					<a id="btPrintEtiq" title="Imprimir la Etiqueta del Expediente" href="#">
						<img id="imgPrintEtiq" src="imagenes/barra/print_etiq_16x16.gif" width="16" height="16" />
					</a>
				<?php
				}
				?>
			</div>

			<div class="p_bnav_contenedor_btSalir">
				<a id="btSalir" title="Volver al listado de Expedientes." href="index.php">
					<img src="imagenes/barra/volver.jpeg" width="17" height="17" />
				</a>
			</div>

		</div>

	    <div class="p_borde_superior"></div>

	    <!-- BUSCADOR POR EXPEDIENTE -->
		<div class="p_buscador">
			<input type="hidden" id="nro_paginas" value="<?php echo $filtro['nro_paginas']; ?>">
			<!--  AQUI SE GUARDA EL NOMBRE DEL CAMPO POR EL CUAL SE ORDENA Y SE BUSCA  -->
			<input type="hidden" name="campo_orden" id="campo_orden" value="<?php echo $filtro['campo_orden']; ?>" />

			<div style="height:20px;">
				<form action="abms/index.php" method="post" name="formBuscador" id="formBuscador" class="p_buscador_form">

					<input type="hidden" id="controlador" value="proyectos" />
					<input type="hidden" id="cantidad" value="<?php echo count($datos); ?>" />
					<input type="hidden" id="pagina" value="<?php echo $filtro['pagina']; ?>" />
					<input type="hidden" id="nroFila_elegida" value="" />
					<input type="hidden" id="tipo_oculto" value="<?php echo (isset($filtro['tipo'])) ? $filtro['tipo'] : 'E'; ?>" />

					<div class="p_buscador_margen_datos"></div>
					<div class="p_buscador_dato">
						<input type="text" name="f_anio" id="f_anio" value="<?php echo $filtro['anio']; ?>" onKeyPress="return soloEnteros(event);" onKeyUp="javascript:respetar_anio(this);" size="2" maxlength="4" />
					</div>
					<div class="p_buscador_margen_datos"></div>
					<div class="p_buscador_dato">
						<select name="f_tipo" id="f_tipo" class="p_buscador_dato_tipo">
							<option value="E">E</option>
							<option value="N">N</option>
							<option value="R">R</option>
						</select>
					</div>
					<div class="p_buscador_margen_datos"></div>
					<div class="p_buscador_dato">
						<input type="text" name="f_numero" id="f_numero" value="<?php echo $filtro['numero']; ?>" onKeyPress="return soloEnteros(event);" size="3" maxlength="5" />
					</div>
					<div class="p_buscador_margen_datos"></div>
					<div class="p_buscador_dato">
						<input type="text" name="f_cuerpo" id="f_cuerpo" value="<?php echo $filtro['cuerpo']; ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
					</div>
					<div class="p_buscador_margen_datos"></div>
					<div class="p_buscador_dato">
						<input type="text" name="f_alcance" id="f_alcance" value="<?php echo $filtro['alcance']; ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
					</div>

					<div class="p_buscador_margen_datos"></div>

					<div class="p_boton_edicion">
						<a title="Buscar" href="javascript:buscarProyectos();">
							<img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" />&nbsp;Buscar
						</a>
					</div>

					<div class="p_buscador_margen_datos"></div>

					<div class="p_boton_edicion">
						<a title="Restablecer" href="javascript:location.href='index.php';">
							<img src="imagenes/limpiar.png" width="16" height="16" align="top" />&nbsp;Restablecer
						</a>
					</div>

					<div style="width:15px;height:20px;float:left;"></div>

					<div class="p_buscador_info p_buscador_texto">
						<span id="cant_expedientes" style="padding:2px 0 0 0;">Cantidad Expedientes : <?php echo $_SESSION['totalExpedientes']; ?></span>
						<br>
						<span id="documento" style="padding:2px 0 0 0;"><strong>Proyecto:<span id="estado_doc" style="font-size:14px;"></span></strong></span>
						<!-- 2020/05/07 XXXX, se agrega el estado de la Digitalización del expediente seleccionado -->
						&nbsp;&nbsp;&nbsp;
						<span id="digitalizacion" style="padding:2px 0 0 0;">
							<strong>Digitalizaci&oacute;n:<span id="estado_digitalizacion" style="font-size:14px;"></span></strong>
						</span>
					</div>
					<div id="modifico_usr" class="p_usuario_afectado p_buscador_texto"></div>

				</form>
			</div>
		</div>

	    <div class="p_borde_superior"></div>

	    <!-- SOLAPAS -->
		<div class="p_solapas_titulos p_buscador_texto">

			<div id="p_solapa_link1" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=expedientes&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&sentido=anterior', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link1').setStyle('color','#315299');" onmouseOut="$('p_solapa_link1').setStyle('color','#000');">Expedientes</div>
			<div id="p_solapa_link2" class="p_solapa_link" style="background-color:silver" onclick="refrescar('abms/index.php?controlador=proyectos&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link2').setStyle('color','#315299');" onmouseOut="$('p_solapa_link2').setStyle('color','#000');">Proyectos</div>
			<div id="p_solapa_link3" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=giros&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link3').setStyle('color','#315299');" onmouseOut="$('p_solapa_link3').setStyle('color','#000');">Giros</div>
			<div id="p_solapa_link4" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=sanciones&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link4').setStyle('color','#315299');" onmouseOut="$('p_solapa_link4').setStyle('color','#000');">Sanciones</div>
			<div id="p_solapa_link5" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=estados&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link5').setStyle('color','#315299');" onmouseOut="$('p_solapa_link5').setStyle('color','#000');">Estados</div>
			<div id="p_solapa_link6" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=antecedentes&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link6').setStyle('color','#315299');" onmouseOut="$('p_solapa_link6').setStyle('color','#000');">Antecedentes</div>
			<?php
			// SOLO PERFIL 1 Y 2 PUEDEN VERIFICAR LOS PRESTAMOS DEL EXPEDIENTE
			//if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 ) {
			?>
				<div id="p_solapa_link7" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=prestamos&accion=listarEnSolapa&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link7').setStyle('color','#315299');" onmouseOut="$('p_solapa_link7').setStyle('color','#000');">Pr&eacute;stamos</div>
			<?php
			//}
			?>
			<div id="p_solapa_link8" class="p_solapa_link" style="color:silver;" onclick="#">Ruta</div>	<!--  onmouseOver="$('p_solapa_link8').setStyle('color','#315299');" onmouseOut="$('p_solapa_link8').setStyle('color','#000');" -->

		</div>

	    <div class="p_borde_superior_sin_linea"></div>

		<div id="ub_listado" class="contenedor_listado_proyectos">
			<table class="e_tabla_texto">
				<?php
				$cantidad = count($datos);
				if ( $cantidad > 0)
				{
				?>
					<thead class="e_tabla_titulos">
						<tr>
							<?php
							// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN EDITAR
							if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2)
							{
							?>
								<th class="orden_link" width="32" colspan="2" >&nbsp;</th>
							<?php
							}
							?>
							<th class="orden_link">Orden</th>
							<th class="orden_link">C&oacute;digo</th>
							<th class="orden_link">Tipo</th>
							<th class="orden_link">Nro.Prom.</th>
							<th class="orden_link">Fecha Promulg.</th>
							<th class="orden_link">Nro Decreto</th>
							<th class="orden_link">Observaciones</th>
						</tr>
					</thead>
					<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
						<?php
						$n = count($datos);
						for ($i=0; $i < $n; $i++)
						{
							$dato = &$datos[$i];

							$evento_dobleclick = "";
							//SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN EDITAR
							if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2)
							{
								$evento_dobleclick = "onDblClick=\"javascript:refrescar('abms/index.php?controlador=proyectos&accion=editar&anio=".$dato['anio']."&tipo=".$dato['tipo']."&numero=".$dato['numero']."&cuerpo=".$dato['cuerpo']."&alcance=".$dato['alcance']."&orden_proyecto=".$dato['orden_proyecto']."', 'contenidoAjaxPrincipal');\"";
							}
						?>
							<tr id="e_fila<?php echo $i; ?>" onmouseover="javascript:resaltarFila(<?php echo $i; ?>);" onmouseout="javascript:no_resaltarFila(<?php echo $i; ?>);" onclick="javascript:marcarFila(<?php echo $i; ?>);$('modifico_usr').setHTML('Modificado por : <?php echo $dato['codigo_usuario']; ?>');" <?php echo $evento_dobleclick; ?> >
								<?php
								// SOLO EL PERFIL 1 o 2 PUEDE MODIFICAR
								if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 )
								{
								?>
									<td width="16">
										<a style="width:16px;height:16px;display:block;" href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editar&anio=<?php echo $dato['anio']; ?>&tipo=<?php echo $dato['tipo']; ?>&numero=<?php echo $dato['numero']; ?>&cuerpo=<?php echo $dato['cuerpo']; ?>&alcance=<?php echo $dato['alcance']; ?>&orden_proyecto=<?php echo $dato['orden_proyecto']; ?>&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" title="Editar">
											<img src="imagenes/b_edit.png" width="14" height="14" align="top" />
										</a>
									</td>
									<td width="16">
										<a style="width:16px;height:16px;display:block;" href="javascript:if(confirm('Desea eliminar el Proyecto?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminar&anio=<?php echo $dato['anio']; ?>&tipo=<?php echo $dato['tipo']; ?>&numero=<?php echo $dato['numero']; ?>&cuerpo=<?php echo $dato['cuerpo']; ?>&alcance=<?php echo $dato['alcance']; ?>&orden_proyecto=<?php echo $dato['orden_proyecto']; ?>', 'contenidoAjaxPrincipal');};" title="Eliminar">
											<img src="imagenes/b_drop.png" width="14" height="14" align="top" />
										</a>
									</td>
								<?php
								}
								?>
								<td id="i_orden_proyecto<?php echo $i; ?>" style="width:43px;height:17px;text-align:right;padding-right:3px;"><?php echo $dato['orden_proyecto']; ?></td>
								<td id="i_id_codproyecto<?php echo $i; ?>" style="width:50px;height:17px;text-align:right;padding-right:3px;"><?php echo $dato['id_codproyecto']; ?></td>
								<td id="i_descripcion_proyecto<?php echo $i; ?>" style="width:110px;height:17px;text-align:left;padding-left:3px;"><?php echo $dato['descripcion_proyecto']; ?></td>
								<td id="i_numero_promulga<?php echo $i; ?>" style="width:72px;height:17px;"><?php echo ($dato['numero_promulga']) ? $dato['numero_promulga'] : '&nbsp;'; ?></td>
								<td id="i_fecha_promulga<?php echo $i; ?>" style="width:120px;height:17px;text-align:right;padding-right:3px;"><?php echo ($dato['fecha_promulga']) ? $dato['fecha_promulga'] : '&nbsp;'; ?></td>
								<td id="i_decreto_promulga<?php echo $i; ?>" style="width:90px;height:17px;text-align:left;padding-left:3px;"><?php echo ($dato['decreto_promulga']) ? $dato['decreto_promulga'] : '&nbsp;'; ?></td>
								<td id="i_observaciones_proyecto<?php echo $i; ?>" style="width:470px;height:17px;text-align:left;padding-left:3px;"><?php echo ($dato['observaciones_proyecto'] != '') ? $dato['observaciones_proyecto'] : '&nbsp;' ; ?></td>
							</tr>
						<?php
						}

						$posicion_en_el_listado = $i-1; // POR DEFECTO
						?>
					</tbody>
				<?php
				}
				?>
			</table>
		</div>
		<div class="proy_word">
			<?php
		    $url = "tareas/index.php?controlador=cargar_proyecto&accion=mostrar_contenido_dir&pftp_anio=".$filtro['anio']."&pftp_tipo=".$filtro['tipo']."&pftp_numero=".$filtro['numero']."";

		    $directorio_expe = $this->armar_nombre_directorio($filtro);

		    $ruta_documento = "proyectos/".$filtro['anio']."/".$directorio_expe."/";

		    $imagen_habilitado = "imagenes/iconos_office/swritter.gif";
		    $imagen_deshabilitado = "imagenes/iconos_office/swritter_gris.gif";

		    $vinculo_original = "<img src='".$imagen_deshabilitado."' width='16' height='16' align='top'>&nbsp;&nbsp;Original";
		    if ( $dir_abierto = @opendir("../".$ruta_documento) )
		    {
				while ( false !== ( $file = readdir($dir_abierto) ) )
				{
					if ( strtolower($file) == 'original.doc' )
					{
						$estilo_boton_original = "proy_boton_habilitado";
						$vinculo_original = "<a href='".$ruta_documento.$file."' target='_blank' ><img src='".$imagen_habilitado."' width='16' height='16' align='top'>&nbsp;&nbsp;Original</a>";
						break;
					}
					else
					{
						$estilo_boton_original = "proy_boton_deshabilitado";
					}
				}
				closedir($dir_abierto);
		    }

		    $vinculo_deforma = "<img src='".$imagen_deshabilitado."' width='16' height='16' align='top'>&nbsp;&nbsp;De Forma";
		    if ( $dir_abierto = @opendir("../".$ruta_documento) )
		    {
				while ( false !== ( $file = readdir($dir_abierto) ) )
				{
					if ( strtolower($file) == 'deforma.doc' )
					{
						$estilo_boton_deforma = "proy_boton_habilitado";
						$vinculo_deforma = "<a href='".$ruta_documento.$file."' target='_blank' ><img src='".$imagen_habilitado."' width='16' height='16' align='top'>&nbsp;&nbsp;De Forma</a>";
						break;
					}
					else
					{
						$estilo_boton_deforma = "proy_boton_deshabilitado";
					}
				}
				closedir($dir_abierto);
		    }
			?>
			<div class="proy_boton <?php echo $estilo_boton_original; ?>">
				<?php echo $vinculo_original; ?>
			</div>
			<div class="proy_margen_bt"></div>
			<div class="proy_boton <?php echo $estilo_boton_deforma; ?>">
				<?php echo $vinculo_deforma; ?>
			</div>
			<div class="proy_margen_bt"></div>
			<div class="proy_boton proy_bt_otros">
				<a href="<?php echo $url; ?>" rel="moodalbox 430 170">
				    <img src="imagenes/iconos_office/dir.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Ver todos los Documentos
				</a>
			</div>
		</div>

	    <div class="p_borde_superior"></div>

		<div id="capa_datos_inferior">
			<!-- AQUI SE VISUALIZAN LOS DATOS DEL EXPEDIENTE SELECCIONADO -->
		</div>

		<script type="text/javascript">
			// PARA LAS VENTANAS MODALES
		    Window.onDomReady(MOOdalBox.init.bind(MOOdalBox));

		    $('f_tipo').value = $('tipo_oculto').value;

		    //SE CARGAN (VISUALIZAN) LOS DATOS EN EL BUSCADOR SUPERIOR
		    cargarBuscador($('f_anio').value, $('f_tipo').value, $('f_numero').value, $('f_cuerpo').value, $('f_alcance').value);

		    //SE VISUALIZAN LOS DATOS(Iniciador, Categoria, Autores, Temas) DEL EXPEDIENTE SELECCIONADO
		    pedirDatos($('f_anio').value, $('f_tipo').value, $('f_numero').value, $('f_cuerpo').value, $('f_alcance').value);

		    // SE SETEA EL HREF DE 'btPrint' PARA GENERAR LA FICHA EN PDF
		    $('btPrint').addEvent('click', function(){
				$('btPrint').setProperty('href','consultas/index.php?controlador=ficha&accion=generar_ficha&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value);
				$('btPrint').setProperty('target','_blank');
		    });

		    // SE SETEA EL HREF DE 'btPrintEtiq' PARA GENERAR LA ETIQUETA EN PDF
		    $('btPrintEtiq').addEvent('click', function(){
				$('btPrintEtiq').setProperty('href','consultas/index.php?controlador=ficha&accion=generarEtiqueta_ficha&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value);
				$('btPrintEtiq').setProperty('target','_blank');
		    });

			function buscarProyectos() {
				refrescar('abms/index.php?controlador='+$('controlador').value+'&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value, 'contenidoAjaxPrincipal');
			}

			$('f_anio').addEvents({
				click: function() {
					se_busca = true;
				},
				keydown: function(event) {
					if(event.key == 'Enter')
						buscarProyectos();
				}
			});

			$('f_numero').addEvents({
				click: function() {
					se_busca = true;
				},
				keydown: function(event) {
					if(event.key == 'Enter')
						buscarProyectos();
				}
			});

			$('f_cuerpo').addEvents({
				click: function() {
					se_busca = true;
				},
				keydown: function(event) {
					if(event.key == 'Enter')
						buscarProyectos();
				}
			});

			$('f_alcance').addEvents({
				click: function() {
					se_busca = true;
				},
				keydown: function(event) {
					if(event.key == 'Enter')
						buscarProyectos();
				}
			});

			// Si hay registros
		    if ( $('cantidad').value > 0 ) {
				// SE SETEA LA FILA ACTUAL
				$('nroFila_elegida').value = '<?php echo $posicion_en_el_listado; ?>';

				// SE MARCA EL ULTIMO REGISTRO DEL LISTADO
				$('e_fila'+$('nroFila_elegida').value).setStyles({'background-color':'#76A0CD'});

				// SE BAJA EL SCROLL AL ACTUALIZAR EL LISTADO
				window.setTimeout("bajarScrollListados('ub_listado')",3);

				// SE ESTABLECE EL FOCO EN LA FILA MARCADA
				$('i_orden_proyecto'+$('nroFila_elegida').value).tabindex = 1;
		    }
		</script>
	<?php
	}

	// SE ARMA EL NOMBRE DEL DIRECTORIO PARA EL EXPEDIENTE RESPECTIVO
	public function armar_nombre_directorio($expe)
	{
	    $anio_corto = substr($expe['anio'], -2);
	    $tipo = $expe['tipo'];
	    $aux_numero = 100000+$expe['numero'];
	    $numero = substr($aux_numero, -5);

	    return  $anio_corto.$tipo.$numero;
	}

	//	$datos CONTIENE LA INFORMACION DEL PROYECTO A EDITAR (SI SE AGREGA UN PROYECTO ESTA VACIO)
	//	$proyRelacionados ES EL LISTADO DE PROYECTOS DEL MISMO EXPEDIENTE
	//	$codigos_proy ES EL LISTADO DE CODIGOS DE PROYECTOS A MOSTRAR EN EL COMBO
	public function editar($datos = null, $proyRelacionados = null, $codigos_proy = null, $filtro = null, $mensaje = '')
	{
		if ( !empty($mensaje) ){ echo '<div class="abm_mensaje_resultado">'.$mensaje.'</div>'; }
	?>
		<script>
			$('header').setStyle('display','none');
			$('p_menu_ocultado').setStyle('display','block');
		</script>

	    <div class="p_borde_superior"></div>

		<!-- BARRA DE NAVEGACION SUPERIOR -->
		<div id="p_barra_navegacion" class="p_barra_navegacion">
			<div class="p_bnav_contenedor_2bt p_bnav_sin_borde_izquierdo">
				<a id="btBG" title="Consulta Gen&eacute;rica" href="#">
					<img id="imgBG" src="imagenes/barra/buscar_gris_16x16.gif" width="16" height="16" />
				</a>
				<a id="btBA" title="Buscar por Antecedente" href="#">
					<img id="imgBA" src="imagenes/barra/buscar_ant_gris_16x16.gif" width="16" height="16" />
				</a>
			</div>
			<!-- PAGINADOR -->
			<div class="p_bnav_contenedor_4bt">
				<a id="btPrimero" href="#">
					<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="top" />
				</a>
				<a id="btAnterior" href="#">
					<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="top" />
				</a>
				<a id="btSiguiente" href="#">
					<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="top" />
				</a>
				<a id="btUltimo" href="#">
					<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="top" />
				</a>
			</div><!-- FIN DEL PAGINADOR -->
			<div class="p_bnav_contenedor_3bt">
				<a id="btAgregar" style="width:30px;" title="Agregar Registro" href="#">
					<img id="imgAgregar" src="imagenes/barra/add_gris_16x16.gif" width="16" height="16" />
				</a>
			</div>
			<div class="p_bnav_contenedor_2bt">
				<?php
				//SOLO USUARIOS DE PERFIL 1 Y 2 PUEDEN EDITAR EL REGISTRO
				if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 )
				{
				?>
					<a id="btGuardar" title="Aceptar los cambios realizados" href="javascript:validarProyecto(true);">
						<img id="imgGuardar" src="imagenes/barra/ok_16x16.gif" width="16" height="16" />
					</a>
				<?php
				}
				else
				{
				?>
					<a id="btGuardar" style="width:30px;" title="Funci&oacute;n deshabilitada" href="#">
						<img id="imgAgregar" src="imagenes/barra/ok_gris_16x16.gif" width="16" height="16" />
					</a>
				<?php
				}
				?>
				<a id="btCancelar" title="Cancelar los cambios realizados" href="javascript:refrescar('abms/index.php?controlador=proyectos&accion=listar&anio=<?php echo $filtro['anio']; ?>&tipo=<?php echo $filtro['tipo']; ?>&numero=<?php echo $filtro['numero']; ?>&cuerpo=<?php echo $filtro['cuerpo']; ?>&alcance=<?php echo $filtro['alcance']; ?>', 'contenidoAjaxPrincipal');">
					<img id="imgCancelar" src="imagenes/barra/error_16x16.gif" width="16" height="16" />
				</a>
			</div>
			<div class="p_bnav_contenedor_2bt">
				<a id="btPrint" title="Imprimir el registro seleccionado" href="#">
					<img id="imgPrint" src="imagenes/barra/print_gris_16x16.gif" width="16" height="16" />
				</a>
				<a id="btPrintEtiq" title="Imprimir la Etiqueta del Expediente" href="#">
					<img id="imgPrintEtiq" src="imagenes/barra/print_etiq_gris_16x16.gif" width="16" height="16" />
				</a>
			</div>
		</div>

	    <div class="p_borde_superior"></div>

		<div class="e_formulario_edicion">
			<form action="abms/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />

				<input type="hidden" name="accion" id="accion" value="<?php echo ($datos[0]['tipo']) ? 'modificar' : 'insertar'; ?>" />

				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="por_boton_Agregar" id="por_boton_Agregar" value="">

				<!-- CLAVE DEL EXPEDIENTE -->
				<input type="hidden" name="anio" id="anio" value="<?php echo ($filtro['anio']) ? $filtro['anio'] : ''; ?>" />
				<input type="hidden" name="tipo" id="tipo" value="<?php echo ($filtro['tipo']) ? $filtro['tipo'] : ''; ?>" />
				<input type="hidden" name="numero" id="numero" value="<?php echo ($filtro['numero']) ? $filtro['numero'] : ''; ?>" />
				<input type="hidden" name="cuerpo" id="cuerpo" value="<?php echo ($filtro['cuerpo']) ? $filtro['cuerpo'] : '0'; ?>" />
				<input type="hidden" name="alcance" id="alcance" value="<?php echo ($filtro['alcance']) ? $filtro['alcance'] : '0'; ?>" />

				<div class="e_datos_sup">
					<div class="e_datos_label e_datos_texto">A&ntilde;o:</div>
					<div class="e_datos_valor">
						<input name="d_anio" value="<?php echo ($filtro['anio']) ? $filtro['anio'] : ''; ?>" class="e_datos_texto" size="3" maxlength="4" style="border:0;" disabled />
					</div>
					<div class="e_datos_label e_datos_texto">Tipo:</div>
					<div class="e_datos_valor">
						<input name="d_tipo" value="<?php echo ($filtro['tipo']) ? $filtro['tipo'] : ''; ?>" class="e_datos_texto" size="1" maxlength="1" style="border:0;" disabled />
					</div>
					<div class="e_datos_label e_datos_texto">N&uacute;mero:</div>
					<div class="e_datos_valor e_datos_texto">
						<input name="d_numero" value="<?php echo ($filtro['numero']) ? $filtro['numero'] : ''; ?>" class="e_datos_texto" size="10" maxlength="10" style="border:0;" disabled />
					</div>
					<div class="e_datos_label e_datos_texto">Cuerpo:</div>
					<div class="e_datos_valor e_datos_texto">
						<input name="d_cuerpo" value="<?php echo ($filtro['cuerpo']) ? $filtro['cuerpo'] : '0'; ?>" class="e_datos_texto" size="1" maxlength="4" style="border:0;" disabled />
					</div>
					<div class="e_datos_label e_datos_texto">Alcance:</div>
					<div class="e_datos_valor e_datos_texto">
						<input name="d_alcance" value="<?php echo ($filtro['alcance']) ? $filtro['alcance'] : '0'; ?>" class="e_datos_texto" size="1" maxlength="4" style="border:0;" disabled />
					</div>
				</div>

				<div class="p_borde_superior"></div>

				<div class="e_solapas_titulos p_buscador_texto">
					<div class="e_solapa_al_editar">Expedientes</div>
					<div class="e_solapa_al_editar" style="color:#000;background-color:silver">Proyectos</div>
					<div class="e_solapa_al_editar">Giros</div>
					<div class="e_solapa_al_editar">Sanciones</div>
					<div class="e_solapa_al_editar">Estados</div>
					<div class="e_solapa_al_editar">Antecedentes</div>
					<div class="e_solapa_al_editar">Pr&eacute;stamos</div>
					<div class="e_solapa_al_editar">Ruta</div>
				</div>
				<div class="proy_edicion">
					Tipo:&nbsp;&nbsp;
					<select name="id_codproyecto" id="id_codproyecto" style="width:170px;border:0;font-size:11px;">
						<?php
						$n = count($codigos_proy);
						for ($i=0; $i < $n; $i++)
						{
							$codigoProy = &$codigos_proy[$i];
						?>
							<option value="<?php echo $codigoProy['id_codproyecto']; ?>" ><?php echo $codigoProy['id_codproyecto'].' - '.$codigoProy['descripcion_proyecto']; ?></option>
						<?php
						}
						?>
					</select>
					&nbsp;&nbsp;&nbsp;&nbsp;
					Orden:&nbsp;&nbsp;
					<?php
					$orden = ($datos[0]['orden_proyecto']) ? $datos[0]['orden_proyecto'] : $filtro['ultimoOrden'] + 1;
					?>
					<input type="text" name="orden_proyecto" id="orden_proyecto" value="<?php echo $orden; ?>" size="1" maxlength="2" readonly="readonly" />
					<br><br>Extracto:<br>
					<textarea name="extracto" id="extracto" style="width:410px;height:95px;"><?php echo ($datos[0]['extracto']) ? $datos[0]['extracto'] : ''; ?></textarea>
					<br><br>Observaciones:<br>
					<textarea name="observaciones_proyecto" id="observaciones_proyecto" style="width:410px;height:54px;"><?php echo ($datos[0]['observaciones_proyecto']) ? $datos[0]['observaciones_proyecto'] : ''; ?></textarea>
				</div>
			</form>
		</div>
		<script>
			$('id_codproyecto').value = '<?php echo ($datos[0]['id_codproyecto']) ? $datos[0]['id_codproyecto'] : 0; ?>';

			setfocus('extracto');
		</script>
	<?php
	}

	public function esElEditable($elEditado, $elRelacionado)
	{
		if ($elEditado['anio'] == $elRelacionado['anio'] && $elEditado['tipo'] == $elRelacionado['tipo'] && $elEditado['cuerpo'] == $elRelacionado['cuerpo'] && $elEditado['alcance'] == $elRelacionado['alcance'] && $elEditado['orden_proyecto'] == $elRelacionado['orden_proyecto'])
		{
			return true;
		}
		return false;
	}

	/**
	 * Se muestra el contenido de un directorio determinado en una ventana modal
	 *
	 * @param string $directorio_proyectos
	 * @param string $archivo_proyectos
	 * @param array $expe
	 */
	public function mostrar_contenido_dir($directorio_proyectos, $archivo_proyectos, $expe)
	{
	?>
	    <div>
			<div class="proy_titulo_superior_directorio degradado">
				<?php
				$tipo = ( $expe['pftp_tipo'] == 'E') ? "del Expediente " : "de la Nota ";
				echo "Documentos ".$tipo.$expe['pftp_anio']." ".$expe['pftp_tipo']." ".$expe['pftp_numero'];
				?>
			</div>
			<div class="proy_contenido_directorio">
				<?php
				if ( $dir_abierto = @opendir($directorio_proyectos) )
				{
					// Obtengo los archivos y directorios por separado para generar una salida ordenada.
					$archivos = array();
					$directorios = array();
					while ( false !== ( $f = readdir($dir_abierto) ) ) {
						if (preg_match('/^~/', $f) !== 1 && $f != '..' && $f != '.' ) {
                            if (is_dir($directorio_proyectos.'/'.$f))
								$directorios[] = $f;
							else
								$archivos[] = $f;
						}
					}
					closedir($dir_abierto);

					// Ordeno de forma "natural", case-insensitive
					natcasesort($archivos);
					natcasesort($directorios);

					// Primero van los directorios, luego los archivos
					$full_dir = array_merge($directorios, $archivos);

					// Genero la salida
					foreach ($full_dir as $file)
					{
						$extension = substr($file, -3);
						echo '<a href="'.$archivo_proyectos.$file.'" target="_blank" >';
						echo "&nbsp;".$this->obtenerImagenExtension($extension);
						echo "&nbsp;".$file;
						echo "</a><br>";
					}
				}
				?>
			</div>
	    </div>
	<?php
	}

	public function obtenerImagenExtension($extension)
	{
		switch ($extension)
		{
			case 'doc':
			case 'DOC':
			case 'docx':
				$img_extension = '<img src="imagenes/iconos_office/doc.jpg" width="17" height="17" >';
				break;
			case 'pdf':
			case 'PDF':
				$img_extension = '<img src="imagenes/iconos_office/pdf.jpg" width="17" height="17" >';
				break;
			case 'xls':
			case 'XLS':
			case 'ods':
			case 'xlsx':
				$img_extension = '<img src="imagenes/iconos_office/xls.jpg" width="17" height="17" >';
				break;
			case 'ppt':
			case 'PPT':
			case 'pptx':
				$img_extension = '<img src="imagenes/iconos_office/ppt.jpg" width="17" height="17" >';
				break;
			case 'rar':
			case 'RAR':
				$img_extension = '<img src="imagenes/iconos_office/rar.jpg" width="17" height="17" >';
				break;
			default:
				$img_extension = '<img src="imagenes/iconos_office/archivo.jpeg" width="17" height="17" >';
				break;
		}
		return $img_extension;
	}

	public function listarModal($datos)
	{
	?>
		<div class="ub_listado">
			<table width="100%" class="e_tabla_texto">
		   		<thead class="e_tabla_titulos">
		  			<tr>
						<th>Orden</th>
		  				<th>Descripci&oacute;n</th>
		  			</tr>
		  		</thead>
				<tbody class="e_cuerpo_scrolleable">
					<?php
					$n = count($datos);
					for ($m=0; $m < $n; $m++)
					{
						$dato = &$datos[$m];
					?>
						<tr id="tm_fila<?php echo $m; ?>" onclick="javascript:volverModal_proyectos('orden_proyecto', 'descripcion_proyecto', '<?php echo $dato['orden_proyecto']; ?>', '<?php echo $dato['descripcion_proyecto']; ?>');" onmouseover="javascript:$('tm_fila<?php echo $m; ?>').setStyle('background-color','#DDDDDD');" onmouseout="javascript:$('tm_fila<?php echo $m; ?>').setStyle('background-color','#fff');">
							<td style="padding-left:5px;"><?php echo $dato['orden_proyecto']; ?></td>
							<td style="padding-left:5px;"><?php echo $dato['descripcion_proyecto']; ?></td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
	<?php
	}

}
?>
