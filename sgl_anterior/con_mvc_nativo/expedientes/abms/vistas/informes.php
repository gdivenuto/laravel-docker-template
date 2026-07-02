<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaInformes extends VistaBase
{
	private $controlador;
	private $formulario;

	public function __construct()
	{
		$this->controlador = 'informes';
		$this->formulario = 'formInformes';
	}
	
	public function listar($datos = '', $clave = '', $mensaje = '', $tipo_mensaje = '')
	{
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>
		
		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxResultadoConsulta" class="informes_giros_gral msc_texto">
			
			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>
			
			<?php 
			if ( $clave['tipo'] == 'E' ) $tipo_documento = 'Expediente ';
			if ( $clave['tipo'] == 'N' ) $tipo_documento = 'Nota ';
			?>
			<div id="dragger_giros_informes" class="msc_titulos degradado">
				<div style="float:left;text-align:left;padding-left:3px;"><?php echo $tipo_documento.$clave['anio'].' '.$clave['tipo'].' '.$clave['numero'].' '.$clave['cuerpo'].' '.$clave['alcance']; ?></div>
				Informes del Giro
				<div style="float:right;text-align:right;padding-right:3px;"><?php echo $clave['comision_descripcion']; ?></div>
			</div>
			<div style="height:32px;">
				<div class="informes_giros_margen_medio"></div>
				<div class="mscpa_boton degradado" style="float:right">
					<a href="javascript:refrescar('abms/index.php?controlador=giros&accion=listar&anio=<?php echo $clave['anio']; ?>&tipo=<?php echo $clave['tipo']; ?>&numero=<?php echo $clave['numero']; ?>&cuerpo=<?php echo $clave['cuerpo']; ?>&alcance=<?php echo $clave['alcance']; ?>', 'contenidoAjaxPrincipal');">
						<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Cerrar
					</a>
				</div> 
				<?php 
				if ( $clave['giro_cerrado'] == 'no' )
				{
					// SOLO EL PERFIL 1 Y 2 PUEDE DAR DE ALTA UN INFORME
					if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 )
					{
				?>
						<div class="informes_giros_margen_medio"></div>
						<div class="mscpa_boton mscpa_boton_Buscar degradado" style="float:right" id="informe_giros_btNuevo">
							<img src="imagenes/barra/add_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Nuevo
						</div>
				<?php
					}
				}
				?>
			</div>
			
			<div id="ub_listado" class="informes_giros_listado">

				<?php 
				$cantidad = count($datos);
				if ( $cantidad > 0 )
				{
				?>	
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class="e_tabla_texto">
						<thead class="e_tabla_titulos">
							<tr>
							<?php 
							if ( $clave['giro_cerrado'] == 'no' )
							{
								if ( $_SESSION['perfil2'] != 3 )// SOLO EL PERFIL 1 Y 2 PUEDE MODIFICAR Y ELIMINAR INFORMES
								{
							?>
									<th class="orden_link" width="32" colspan="2">&nbsp;</th>
							<?php 
								}
							}
							?>
								<th class="orden_link">Orden Informe</th>
								<th class="orden_link">Fecha Pedido</th>
								<th class="orden_link">Fecha Vuelta</th>
								<th class="orden_link">Detalle</th>
								<th class="orden_link">Observaciones</th>
							</tr>
						</thead>
						<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
						
							<input type="hidden" id="controlador" value="giros" />
							<input type="hidden" id="cantidad" value="<?php echo count($datos); ?>" />
							<?php
							$cantidad = count($datos);
							for ($i=0; $i < $cantidad; $i++)
							{
								$dato = &$datos[$i];
								
								$evento_dobleclick = "";
								// SI EL GIRO NO ESTA CERRADO
								if ( $clave['giro_cerrado'] == 'no' )
								{
									// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN EDITAR
									if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 )
									{ 
										$evento_dobleclick = "onDblClick=\"javascript:refrescar('abms/index.php?controlador=informes&accion=editar&anio=".$dato['anio']."&tipo=".$dato['tipo']."&numero=".$dato['numero']."&cuerpo=".$dato['cuerpo']."&alcance=".$dato['alcance']."&orden_giro=".$dato['orden_giro']."&orden_informe=".$dato['orden_informe']."&comision_descripcion=".$clave['comision_descripcion']."', 'contenidoAjaxResultadoConsulta');\"";
									}
								}	
							?>
								<tr id="e_fila<?php echo $i; ?>" onmouseover="javascript:resaltarFila(<?php echo $i; ?>);" onmouseout="javascript:no_resaltarFila(<?php echo $i; ?>);" onclick="javascript:marcarFila(<?php echo $i; ?>);" <?php echo $evento_dobleclick; ?>> 
								
									<?php 
									if ( $clave['giro_cerrado'] == 'no' )
									{
										//SOLO EL PERFIL 1 o 2 PUEDE MODIFICAR
										if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 )
										{
									?>
											<td width="16">
												<a style="width:16px;height:16px;display:block;" href="javascript:refrescar('abms/index.php?controlador=informes&accion=editar&anio=<?php echo $dato['anio']; ?>&tipo=<?php echo $dato['tipo']; ?>&numero=<?php echo $dato['numero']; ?>&cuerpo=<?php echo $dato['cuerpo']; ?>&alcance=<?php echo $dato['alcance']; ?>&orden_giro=<?php echo $dato['orden_giro']; ?>&orden_informe=<?php echo $dato['orden_informe']; ?>&comision_descripcion=<?php echo $clave['comision_descripcion']; ?>', 'contenidoAjaxResultadoConsulta');" title="Editar">
													<img src="imagenes/b_edit.png" width="14" height="14" align="top" />
												</a>
											</td>
									<?php 
										}
										if ( $_SESSION['perfil2'] != 3 )
										{   //SOLO EL PERFIL 1 Y 2 PUEDEN ELIMINAR ?>	
											<td width="16">
												<a style="width:16px;height:16px;display:block;" href="javascript:if(confirm('Desea eliminar el Informe?')){refrescar('abms/index.php?controlador=informes&accion=eliminar&anio=<?php echo $dato['anio']; ?>&tipo=<?php echo $dato['tipo']; ?>&numero=<?php echo $dato['numero']; ?>&cuerpo=<?php echo $dato['cuerpo']; ?>&alcance=<?php echo $dato['alcance']; ?>&orden_giro=<?php echo $dato['orden_giro']; ?>&orden_informe=<?php echo $dato['orden_informe']; ?>&comision_descripcion=<?php echo $clave['comision_descripcion']; ?>', 'capaVentana');};" title="Eliminar">
													<img src="imagenes/b_drop.png" width="14" height="14" align="top" />
												</a>
											</td>
									<?php 
										}
									}
									?>		
									<td nowrap id="i_orden_informe<?php echo $i; ?>" style="width:90px;height:17px;text-align:right;padding-right:3px;"><?php echo $dato['orden_informe']; ?></td>
									<td nowrap id="i_fecha_pedido_informe<?php echo $i; ?>" style="width:80px;height:17px;text-align:center;"><?php echo ($dato['fecha_pedido_informe']) ? $this->formatearFecha($dato['fecha_pedido_informe']) : '&nbsp;'; ?></td>
									<td nowrap id="i_fecha_vuelta_informe<?php echo $i; ?>" style="width:80px;height:17px;text-align:center;"><?php echo ($dato['fecha_vuelta_informe']) ? $this->formatearFecha($dato['fecha_vuelta_informe']) : '&nbsp;'; ?></td>
									<td nowrap id="i_detalle_informe<?php echo $i; ?>" style="width:200px;height:17px;text-align:left;padding-left:3px;"><?php echo ($dato['detalle_informe']) ? $dato['detalle_informe'] : '&nbsp;'; ?></td>
									<td nowrap id="i_observaciones_informe<?php echo $i; ?>" style="width:291px;height:17px;text-align:left;padding-left:3px;"><?php echo ($dato['observaciones_informe']) ? $dato['observaciones_informe'] : '&nbsp;'; ?></td>
									
								</tr>
							<?php
							}
							?>		
						</tbody>
					</table>
				<?php
				}
				else
				{
					echo "<h1>:: SIN INFORMES ::</h1>";
				}	
				?>	
			</div>
		</div>
		<script>
			$('informe_giros_btNuevo').addEvent('click', function()
			{
			  	refrescar('abms/index.php?controlador=informes&accion=agregar&anio=<?php echo $clave['anio']; ?>&tipo=<?php echo $clave['tipo']; ?>&numero=<?php echo $clave['numero']; ?>&cuerpo=<?php echo $clave['cuerpo']; ?>&alcance=<?php echo $clave['alcance']; ?>&orden_giro=<?php echo $clave['orden_giro']; ?>&comision_descripcion=<?php echo $clave['comision_descripcion']; ?>', 'contenidoAjaxResultadoConsulta');
			});
			
			var menuDrag = new Drag.Move($('contenidoAjaxResultadoConsulta'), {
			   handle: $('dragger_giros_informes')
			});
		</script>
	<?php
	}
		
	public function editar($datos = null, $filtro = null)
	{
		if ( $filtro['tipo'] == 'E' ) $tipo_documento = 'Expediente ';
		if ( $filtro['tipo'] == 'N' ) $tipo_documento = 'Nota ';
	?>
		<script type="text/javascript">
			$('header').setStyle('display','none');
			$('p_menu_ocultado').setStyle('display','block');
		</script>
		
		<div class="msc_titulos degradado">
			<div style="float:left;text-align:left;padding-left:3px;"><?php echo $tipo_documento.$filtro['anio'].' '.$filtro['tipo'].' '.$filtro['numero'].' '.$filtro['cuerpo'].' '.$filtro['alcance']; ?></div>
			Informes del Giro
			<div style="float:right;text-align:right;padding-right:3px;"><?php echo $filtro['comision_descripcion']; ?></div>
		</div>
		
		<!-- BARRA DE BOTONES DE EDICION -->
		<div style="height:32px;">
			<div class="informes_giros_margen_medio"></div>
			<div class="mscpa_boton degradado" style="float:right">
				<a id="btCancelar" title="Cancelar los cambios realizados" href="javascript:refrescar('abms/index.php?controlador=informes&accion=listar&anio=<?php echo $filtro['anio']; ?>&tipo=<?php echo $filtro['tipo']; ?>&numero=<?php echo $filtro['numero']; ?>&cuerpo=<?php echo $filtro['cuerpo']; ?>&alcance=<?php echo $filtro['alcance']; ?>&orden_giro=<?php echo $filtro['orden_giro']; ?>&giro_cerrado=<?php echo $filtro['giro_cerrado']; ?>&comision_descripcion=<?php echo $filtro['comision_descripcion']; ?>', 'capaVentana');">
					<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Cancelar
				</a>
			</div> 
			<div class="informes_giros_margen_medio"></div>
			<div id="btGuardarInforme" class="mscpa_boton mscpa_boton_Buscar degradado" style="float:right">
				<?php
				//SOLO USUARIOS DE PERFIL 1 Y 2 PUEDEN EDITAR EL REGISTRO
				if ( $_SESSION['perfil2'] != 3 )
				{
				?>
					<img src="imagenes/barra/add_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Guardar
				<?php
				}
				else
				{
				?>
					<img src="imagenes/barra/ok_gris_16x16.gif" width="16" height="16" align="top" />
				<?php
				}
				?>
			</div>
		</div>
		
	    <div class="p_borde_superior"></div>
	    
		<form action="abms/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">
			
			<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
			
			<input type="hidden" name="accion" id="accion" value="<?php echo ($datos[0]['tipo']) ? 'modificar' : 'insertar'; ?>" />
			
			<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
			
			<input type="hidden" name="anio" id="anio" value="<?php if (isset($filtro['anio'])){ echo $filtro['anio'];}else{ echo '';} ?>" />
			<input type="hidden" name="tipo" id="tipo" value="<?php if (isset($filtro['tipo'])){ echo $filtro['tipo'];}else{ echo '';} ?>" />
			<input type="hidden" name="numero" id="numero" value="<?php if (isset($filtro['numero'])){ echo $filtro['numero'];}else{ echo 0;} ?>" />
			<input type="hidden" name="cuerpo" id="cuerpo" value="<?php if (isset($filtro['cuerpo'])){ echo $filtro['cuerpo'];}else{ echo 0;} ?>" />
			<input type="hidden" name="alcance" id="alcance" value="<?php if (isset($filtro['alcance'])){ echo $filtro['alcance'];}else{ echo 0;} ?>" />
			<input type="hidden" name="comision_descripcion" id="comision_descripcion" value="<?php if (isset($filtro['comision_descripcion'])){ echo $filtro['comision_descripcion'];}else{ echo '';} ?>" />
			
			<div class="e_edit_gral" style="border:0;">
			
				<div class="e_edit_margen_izq"></div>
				<div class="e_edit_nombres">
					<div class="e_edit_nombre">Orden Giro:</div>
					<div class="e_edit_nombre_margen"></div>
					<div class="e_edit_nombre">Orden Informe:</div>
					<div class="e_edit_nombre_margen"></div>
					<div class="e_edit_nombre">Fecha Pedido:</div>
					<div class="e_edit_nombre_margen"></div>
					<div class="e_edit_nombre">Fecha Vuelta:</div>
					<div class="e_edit_nombre_margen"></div>
					<div class="e_edit_nombre">Detalle:</div>
					<div class="e_edit_nombre_margen"></div>
					<div class="e_edit_nombre">Observaciones:</div>
				</div>
				<div class="e_edit_valores">
					<div class="e_edit_valor">
						<input type="text" name="orden_giro" id="orden_giro" value="<?php echo $filtro['orden_giro']; ?>" size="1" maxlength="2" readonly="readonly" />
					</div>
					<div class="e_edit_nombre_margen"></div>
					<div class="e_edit_valor">
						<?php
						$orden_informe = ($datos[0]['orden_informe']) ? $datos[0]['orden_informe'] : $filtro['ultimoOrden'] + 1;
						?>
						<input type="text" name="orden_informe" id="orden_informe" value="<?php echo $orden_informe; ?>" size="1" maxlength="2" readonly="readonly" />
					</div>
					<div class="e_edit_nombre_margen"></div>
					<div class="e_edit_valor">
						<input type="text" name="fecha_pedido_informe" id="fecha_pedido_informe" value="<?php echo ($datos[0]['fecha_pedido_informe']) ? $this->formatearFecha($datos[0]['fecha_pedido_informe']) : date("d/m/Y"); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" />
						<input type="image" id="img_fecha_pedido_informe" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la Fecha de Pedido." align="top" width="16" height="16">
					</div>
					<div class="e_edit_nombre_margen"></div>
					<div class="e_edit_valor">
						<?php 
						// SI POSEE LA fecha_pedido_informe SE PERMITE SELECCIONAR LA fecha_vuelta_informe 
						if ( isset($datos[0]['fecha_pedido_informe']) )
						{
						?>	
							<input type="text" name="fecha_vuelta_informe" id="fecha_vuelta_informe" value="<?php echo ($datos[0]['fecha_vuelta_informe']) ? $this->formatearFecha($datos[0]['fecha_vuelta_informe']) : ''; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" />	
							<input type="image" id="img_fecha_vuelta_informe" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la Fecha de Vuelta." align="top" width="16" height="16">
						<?php 
						}
						else
						{
						?>	
							<input type="text" name="fecha_vuelta_informe" id="fecha_vuelta_informe" value="" onKeyPress="return solo_enteros_y_barra(event);" size="8" maxlength="10" disabled />
							<span id="cont_fecha_vuelta_informe"><img src="imagenes/calendario/calendario_gris.gif" alt="Deshabilitado por ausencia de la Fecha de Pedido." align="top" width="16" height="16"></span>
						<?php 
						}
						?>
					</div>
					<div class="e_edit_nombre_margen"></div>
					<div class="e_edit_valor">
						<input type="text" name="detalle_informe" id="detalle_informe" value="<?php echo $datos[0]['detalle_informe']; ?>" style="width:500px;" maxlength="35" />
					</div>
					<div class="e_edit_nombre_margen"></div>
					<div class="e_edit_valor">
						<input type="text" name="observaciones_informe" id="observaciones_informe" value="<?php echo $datos[0]['observaciones_informe']; ?>" style="width:500px;" />
					</div>
				</div>
			</div>
		</form>
		<script type="text/javascript">
			
			//CALENDARIO PARA LA FECHA DE PEDIDO
			var calPedido = new Zapatec.Calendar.setup({
				
				inputField	:"fecha_pedido_informe",
				ifFormat	:"%d/%m/%Y",
				button		:"img_fecha_pedido_informe",
				showsTime	:false

			});
			
			//CALENDARIO PARA LA FECHA DE VUELTA
			var calVuelta = new Zapatec.Calendar.setup({
				
				inputField	:"fecha_vuelta_informe",
				ifFormat	:"%d/%m/%Y",
				button		:"img_fecha_vuelta_informe",
				showsTime	:false

			});	
			
			$("img_fecha_pedido_informe").addEvent('click', function()
			{
				$('fecha_vuelta_informe').disabled = false;
				$('cont_fecha_vuelta_informe').setHTML('<input type="image" id="img_fecha_vuelta_informe" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqui para seleccionar la Fecha de Vuelta" align="top" width="16" height="16">');
				
				//CALENDARIO PARA LA FECHA DE VUELTA
				var calVuelta = new Zapatec.Calendar.setup({
					
					inputField	:"fecha_vuelta_informe",
					ifFormat	:"%d/%m/%Y",
					button		:"img_fecha_vuelta_informe",
					showsTime	:false

				});	
			});
			
			<?php
			//SOLO USUARIOS DE PERFIL 1 Y 2 PUEDEN EDITAR EL REGISTRO
			if ( $_SESSION['perfil2'] != 3 )
			{
			?>
				$('btGuardarInforme').addEvent('click', function()
				{
					validarInforme();
				});
			<?php
			}
			?>
			
			setfocus('fecha_pedido_informe');
			
		</script>	
	<?php
	}		
}
