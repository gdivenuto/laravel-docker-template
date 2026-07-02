<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaMarcaComision extends VistaBase
{
	private $controlador;
	private $formulario;
	private $nombre_marca;
	private $cantidad;

	public function __construct()
	{
		$this->controlador = 'marca_comision';
		$this->formulario = 'formMarcaComision';
	}
	
	public function listar($listado = null, $listadoComisiones = '', $filtro = '', $mensaje = '')
	{
		$modelo = new marcaComisionModel();
												
		$cantidad_listado = count($listado);
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>
		
		<div id="precarga_modal" style="display:none"></div>
		
		<div id="contenidoAjaxMarcaComision" class="tareas_mc_gral mstcg_texto">
			
			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>
			
			<form action="tareas/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">
				
				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="listar" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="enviado" id="enviado" value="enviado" />
				<input type="hidden" name="cantidad" id="cantidad" value="<?php echo $cantidad_listado; ?>" />
				<input type="hidden" name="se_limpia" id="se_limpia" value="no" />
				
				<!-- Clave del expediente del buscador para volver luego de guardar las Marcas -->
				<input type="hidden" name="mc_f_anio" id="mc_f_anio" value="" />
				<input type="hidden" name="mc_f_tipo" id="mc_f_tipo" value="" />
				<input type="hidden" name="mc_f_numero" id="mc_f_numero" value="" />
				<input type="hidden" name="mc_f_cuerpo" id="mc_f_cuerpo" value="" />
				<input type="hidden" name="mc_f_alcance" id="mc_f_alcance" value="" />
					
				<div id="dragger_marca_comision" class="msc_titulos degradado">Marca Comisi&oacute;n</div>
				<div style="height:81px;margin:5px 0 7px 10px;">
					<div class="tareas_mc_criterio tareas_mc_texto">
						<div style="height:21px;">
							&nbsp;Ingresados desde el :
							<input type="text" id="mstmc_fecha_desde" name="mstmc_fecha_desde" value="<?php if ($filtro['mstmc_fecha_desde']!=''){echo $this->formatearFecha($filtro['mstmc_fecha_desde']); }else{echo '01/01/'.(date("Y")-10); } ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" <?php if ( Validador::validarParametro('enviado') == 'enviado' ){ echo 'disabled'; } ?> />
							<?php 
							if ( Validador::validarParametro('enviado') != 'enviado' )
							{ 
							?>
								<input type="image" id="mstmc_btffecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">	
							<?php 
							} 
							?>
							&nbsp;Hasta el :
							<input type="text" id="mstmc_fecha_hasta" name="mstmc_fecha_hasta" value="<?php if ($filtro['mstmc_fecha_hasta']!=''){echo $this->formatearFecha($filtro['mstmc_fecha_hasta']); }else{echo date("d/m/Y"); } ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" <?php if ( Validador::validarParametro('enviado') == 'enviado' ){ echo 'disabled'; } ?>/>
							<?php 
							if ( Validador::validarParametro('enviado') != 'enviado' )
							{ 
							?>
								<input type="image" id="mstmc_btffecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
							<?php 
							} 
							?>
						</div>
						<div style="height:7px;font-size:0;"></div>
						<div style="height:21px;">
							&nbsp;Fecha de listado :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="text" id="mstmc_fecha_de_listado" name="mstmc_fecha_de_listado" value="<?php echo ($filtro['mstmc_fecha_de_listado']) ? $filtro['mstmc_fecha_de_listado'] : date("d/m/Y"); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" <?php if ( Validador::validarParametro('enviado') == 'enviado' ){ echo 'disabled'; } ?> />
							<?php 
							if ( Validador::validarParametro('enviado') != 'enviado' )
							{ 
							?>
								<input type="image" id="mstmc_btffecha_de_listado" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha de listado." width="16" height="16">	
							<?php 
							} 
							?>
						</div>
						<div style="height:7px;font-size:0;"></div>
						<div style="height:21px;">
							&nbsp;En Comisi&oacute;n de:
							<select id="c_comision" name="mstmc_comision" class="msl_combo" <?php if ( Validador::validarParametro('enviado') == 'enviado' ){ echo 'disabled'; } ?> style="width:237px;" >
								<option value="0">0, TODAS</option>
								<?php
								$cant_comisiones = count($listadoComisiones);
								for ($c=0; $c < $cant_comisiones; $c++)
								{
									$comision = &$listadoComisiones[$c];
								?>	
									<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>" <?php if ($comision['tipo_grp']==$filtro['mstmc_comision_tipo'] && $comision['codigo_grp']==$filtro['mstmc_comision_codigo']){echo 'selected';} ?>><?php echo $comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
								<?php
								}
								?>
							</select>
							<?php 
							if ( Validador::validarParametro('enviado') != 'enviado' )
							{
							?>	
								&nbsp;
								<a href="javascript:modalGaby('consultas/index.php?controlador=consulta_gral&accion=pedirNombreComisionModal&c_solo_habilitado=1');" title="Buscar por Nombre de Comisi&oacute;n"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
							<?php
							}
							?>
						</div>
					</div>
					<div class="tareas_mc_botones tareas_mc_texto">
					    
					    <div style="height:21px;">
							<?php 
							if ( Validador::validarParametro('enviado') != 'enviado')
							{
							?>
								<div class="msc_boton degradado">
									<a id="mstmc_btBuscar" href="#">
										<img src="imagenes/zoom_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Buscar
									</a>
								</div>
								<div class="msc_boton degradado">
									<a id="mstmc_btCerrar" href="#">
										<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
									</a>
								</div>
							<?php 
							}
							else
							{
							?>	
								<div class="msc_boton degradado">
									<a id="mstmc_btLimpiar" href="#">
										<img src="imagenes/barra/limpiar.jpeg" width="16" height="16" />&nbsp;&nbsp;&nbsp;Limpiar Marcas
									</a>
								</div>
								<div class="msc_boton degradado">
									<a id="mstmc_btGuardar" href="#">
										<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Guardar
									</a>
								</div>
								<div class="msc_boton degradado">
									<a id="mstmc_btCancelar" href="#">
										<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cancelar
									</a>
								</div>
							<?php 
							} 
							?>
					    </div>
					</div>
				</div>	
				<div class="msc_borde1">
					<table class="tareas_mc_marca_comision_tabla_contenedora">
						<thead>
							<tr>
								<th class="tareas_mc_marca_comision_anio" style="width:35px;text-align:center;" >A&ntilde;o</th>
								<th class="tareas_mc_marca_comision_tipo" style="width:32px;text-align:center;" >Tipo</th>
								<th class="tareas_mc_marca_comision_numero" style="width:50px;text-align:center;" >N&uacute;mero</th>
								<th class="tareas_mc_marca_comision_cuerpo" style="width:30px;text-align:left;" >Cpo.</th>
								<th class="tareas_mc_marca_comision_alcance" style="width:30px;text-align:left;" >Alc.</th>
								<th class="tareas_mc_marca_comision_dias" style="width:30px;text-align:left;">D&iacute;as</th>
								<th class="tareas_mc_marca_comision_tipo_marca" style="width:90px;text-align:center;">Tipo marca</th>
								<th class="tareas_mc_marca_comision_inicial mstmc_marca_comision_inicial_sin_marca" style="width:18px;text-align:left;">S</th>
								<th class="tareas_mc_marca_comision_inicial mstmc_marca_comision_inicial_para_tratar" style="width:18px;text-align:left;">T</th>
								<th class="tareas_mc_marca_comision_inicial mstmc_marca_comision_inicial_para_conocimiento" style="width:18px;text-align:left;">C</th>
								<th class="tareas_mc_marca_comision_inicial mstmc_marca_comision_inicial_para_archivo" style="width:18px;text-align:left;">A</th>
								<th class="tareas_mc_marca_comision_inicial mstmc_marca_comision_inicial_ultima_columna" style="width:30px;text-align:left;">P</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="12" style="border:none">
									<div class="tareas_mc_marca_comision_parte_movil">
										<table class="tareas_mc_marca_comision_tabla_movil">
											<tbody>
												<?php
												for ($i = 0; $i < $cantidad_listado; $i++)
												{
													$dato = &$listado[$i];
													
													switch ($dato['marca_comision'])
													{
														case '0':
															$this->nombre_marca = "Sin marca";
															break;
														case '1':
															$this->nombre_marca = "Para tratar";
															break;
														case '2':
															$this->nombre_marca = "Para su conoc.";
															break;
														case '3':
															$this->nombre_marca = "Para archivo";
															break;	
														case '4':
															$this->nombre_marca = "Para pr&oacute;rroga";
															break;		
														default:
															$this->nombre_marca = "Sin marca";
															$dato['marca_comision'] = 0;
															break;
													}
													?>
													<!-- INDICA SI SE HA MODIFICADO LA MARCA DEL EXPEDIENTE -->
													<input type="hidden" name="marca_modificada<?php echo $i; ?>" id="marca_modificada<?php echo $i; ?>" value="false" />
													
													<tr id="e_fila<?php echo $i; ?>">
														
														<input type="hidden" id="clave_expediente<?php echo $i; ?>" name="clave_expediente<?php echo $i; ?>" value="<?php echo $dato['anio'].'-'.$dato['tipo'].'-'.$dato['numero'].'-'.$dato['cuerpo'].'-'.$dato['alcance']; ?>" disabled >
														
														<td id="anio_marca_comision<?php echo $i; ?>" class="tareas_mc_marca_comision_anio" style="color:#454B4F" ><?php echo $dato['anio']; ?></td>
														<td id="tipo_marca_comision<?php echo $i; ?>" class="tareas_mc_marca_comision_tipo" style="color:#454B4F" ><?php echo $dato['tipo']; ?></td>
														<td id="numero_marca_comision<?php echo $i; ?>" class="tareas_mc_marca_comision_numero" style="color:#454B4F" ><?php echo $dato['numero']; ?></td>
														<td id="cuerpo_marca_comision<?php echo $i; ?>" class="tareas_mc_marca_comision_cuerpo" style="color:#454B4F" ><?php echo $dato['cuerpo']; ?></td>
														<td id="alcance_marca_comision<?php echo $i; ?>" class="tareas_mc_marca_comision_alcance" style="color:#454B4F" ><?php echo $dato['alcance']; ?></td>
														
														<?php 
														// COLOR VERDE PASTEL
														if ( $dato['dias'] >= 0 && $dato['dias'] <= 105 )
														{
															$css_color = 'style="font-weight:700;text-align:right;padding-right:2px;border:1px solid silver;background-color: #DFF0D8;color: #468847;"';
														}
														else 
														{
															// COLOR AMARILLO PASTEL
															if ( $dato['dias'] >= 106 && $dato['dias'] <= 119 )
															{
																$css_color = 'style="font-weight:700;text-align:right;padding-right:2px;border:1px solid silver;background-color: #FCF8E3;color: #C09853;"';
															}
															else
															{
																// COLOR ROJO PASTEL
																$css_color =  'style="font-weight:700;text-align:right;padding-right:2px;border:1px solid silver;background-color: #F2DEDE;color: #B94A48;"';
															}
														}
														?>	
														<td class="tareas_mc_marca_comision_dias" <?php echo $css_color; ?> >
															<span style="width:30px;font-size:11px;text-align:right;padding-right:5px;"><?php echo $dato['dias']; ?></span>
														</td>
														
														<td class="tareas_mc_marca_comision_tipo_marca"><input type="text" name="nombre_marca" id="i_nombre_marca<?php echo $i; ?>" value="<?php echo $this->nombre_marca; ?>" readonly="readonly" style="background-color:#fff;border:1px solid silver;" disabled ></td>
														
														<td class="tareas_mc_marca_comision_inicial"><input type="radio" name="i_tipo_marca<?php echo $i; ?>" id="i_sin_marca<?php echo $i; ?>" value="0" onclick="javascript:habilitarClaveExpedienteComision(<?php echo $i; ?>);$('i_nombre_marca<?php echo $i; ?>').value='Sin marca';" onchange="javascript:$('marca_modificada<?php echo $i; ?>').value=true;" checked ></td>
														<td class="tareas_mc_marca_comision_inicial"><input type="radio" name="i_tipo_marca<?php echo $i; ?>" id="i_para_tratar<?php echo $i; ?>" value="1" onclick="javascript:habilitarClaveExpedienteComision(<?php echo $i; ?>);$('i_nombre_marca<?php echo $i; ?>').value='Para tratar';" onchange="javascript:$('marca_modificada<?php echo $i; ?>').value=true;" <?php if ($dato['marca_comision']==1){ echo 'checked'; } ?> ></td>
														<td class="tareas_mc_marca_comision_inicial"><input type="radio" name="i_tipo_marca<?php echo $i; ?>" id="i_para_su_conoc<?php echo $i; ?>" value="2" onclick="javascript:habilitarClaveExpedienteComision(<?php echo $i; ?>);$('i_nombre_marca<?php echo $i; ?>').value='Para su conoc.';" onchange="javascript:$('marca_modificada<?php echo $i; ?>').value=true;" <?php if ($dato['marca_comision']==2){ echo 'checked'; } ?> ></td>
														<td class="tareas_mc_marca_comision_inicial"><input type="radio" name="i_tipo_marca<?php echo $i; ?>" id="i_para_archivo<?php echo $i; ?>" value="3" onclick="javascript:habilitarClaveExpedienteComision(<?php echo $i; ?>);$('i_nombre_marca<?php echo $i; ?>').value='Para archivo';" onchange="javascript:$('marca_modificada<?php echo $i; ?>').value=true;" <?php if ($dato['marca_comision']==3){ echo 'checked'; } ?> ></td>
														<td class="tareas_mc_marca_comision_inicial"><input type="radio" name="i_tipo_marca<?php echo $i; ?>" id="i_para_prorroga<?php echo $i; ?>" value="4" onclick="javascript:habilitarClaveExpedienteComision(<?php echo $i; ?>);$('i_nombre_marca<?php echo $i; ?>').value='Para pr&oacute;rroga';" onchange="javascript:$('marca_modificada<?php echo $i; ?>').value=true;" <?php if ($dato['marca_comision']==4){ echo 'checked'; } ?> ></td>
														
													</tr>
												<?php
												} // FIN DEL for
												?>
											</tbody>
										</table>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>	
				<div class="tareas_mc_contador degradado">
					Expedientes en Comisi&oacute;n:&nbsp;&nbsp;&nbsp;<?php echo ( $cantidad_listado > 0 ) ? $cantidad_listado : '0'; ?>
				</div>
			</form>
		</div>	
		<script>
			function habilitarClaveExpedienteComision(fila)
			{
				$('clave_expediente'+fila).disabled = false;
				
				$('anio_marca_comision'+fila).setStyle('color', '#000');
				$('tipo_marca_comision'+fila).setStyle('color', '#000');
				$('numero_marca_comision'+fila).setStyle('color', '#000');
				$('cuerpo_marca_comision'+fila).setStyle('color', '#000'); 
				$('alcance_marca_comision'+fila).setStyle('color', '#000');
				
				$('i_nombre_marca'+fila).setStyle('color', '#000');
			}
			
		    var menuDrag = new Drag.Move($('contenidoAjaxMarcaComision'), {
			   handle: $('dragger_marca_comision')
			});

			window.addEvent('domready', function()
			{
				// Clave del expediente del buscador para volver luego de guardar las Marcas
				$('mc_f_anio').value    = $('f_anio').value;
				$('mc_f_tipo').value    = $('f_tipo').value;
				$('mc_f_numero').value  = $('f_numero').value;
				$('mc_f_cuerpo').value  = $('f_cuerpo').value;
				$('mc_f_alcance').value = $('f_alcance').value;

				<?php 
				if ( Validador::validarParametro('enviado') != 'enviado' )
				{
				?>
					//CALENDARIO PARA LA FECHA DESDE
					var calDesde = new Zapatec.Calendar.setup({
						inputField	:"mstmc_fecha_desde",
						ifFormat	:"%d/%m/%Y",
						button		:"mstmc_btffecha_desde",
						showsTime	:false
					});
					
					//CALENDARIO PARA LA FECHA HASTA
					var calHasta = new Zapatec.Calendar.setup({
						inputField	:"mstmc_fecha_hasta",
						ifFormat	:"%d/%m/%Y",
						button		:"mstmc_btffecha_hasta",
						showsTime	:false
					});
					
					//CALENDARIO PARA LA FECHA DE LISTADO
					var calHasta = new Zapatec.Calendar.setup({
						inputField	:"mstmc_fecha_de_listado",
						ifFormat	:"%d/%m/%Y",
						button		:"mstmc_btffecha_de_listado",
						showsTime	:false
					});
					
					$('mstmc_btBuscar').addEvent('click', function()
					{
						var mensaje = "";
						var error = false;
						
						if( $('mstmc_fecha_desde').value == '' )
						{
							mensaje += "Debe seleccionar la Fecha Desde de Ingreso.<br>";
							error = true;
						}
						if( $('mstmc_fecha_hasta').value == '' )
						{
							mensaje += "Debe seleccionar la Fecha Hasta de Ingreso.<br>";
							error = true;
						}
						if( $('c_comision').value == '0' )
						{
							mensaje += "Debe seleccionar una Comisi"+'\u00f3'+"n.<br>";
							error = true;
						}
						if ( esLaFechaMayor($('mstmc_fecha_desde').value, $('mstmc_fecha_hasta').value) )
						{
							mensaje += "La fecha Desde debe ser menor a la fecha Hasta.";
							error = true;
						}

						if(error)
							alert(mensaje);
						else
							enviarForm('formMarcaComision', 'tareas', 'contenidoAjaxMarcaComision');
					});

					$('mstmc_btCerrar').addEvent('click', function()
					{
						var url = 'index.php?anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&sentido=anterior';
						
						location.href = url;
					});
				<?php
				}
				else
				{
				?>
					$('mstmc_btGuardar').addEvent('click', function()
					{
						// SE SETEA LA ACCION A REALIZAR AL ENVIAR EL FORMULARIO
						$('accion').value = 'guardar';

						for (var i = 0 ; i < $('cantidad').value ; i++)
						{
							// SI NO SE MODIFICÓ, SE DESHABILITA PARA NO POSTEARLO
							if ( $('marca_modificada'+i).value == 'false' )
							{
								$('marca_modificada'+i).disabled = true;
								$('i_sin_marca'+i).disabled      = true;
								$('i_para_tratar'+i).disabled    = true;
								$('i_para_su_conoc'+i).disabled  = true;
								$('i_para_archivo'+i).disabled   = true;
								$('i_para_prorroga'+i).disabled  = true;
							}
						}
						
						enviarForm('formMarcaComision', 'tareas', 'contenidoAjaxMarcaComision');
					});

					$('mstmc_btCancelar').addEvent('click', function()
					{
						if (confirm('¿Desea cancelar los cambios?')){ 
							refrescar('tareas/index.php?controlador=marca_comision&accion=listar', 'capaVentana');
						};
					});
					
					if ( $('cantidad').value > 0 )
					{
						$('mstmc_btLimpiar').addEvent('click', function()
						{
							<?php
							for ($i = 0; $i < $cantidad_listado; $i++)
							{
								$dato = &$listado[$i];
								
								if ( $dato['marca_comision'] != 0 )
								{
							?>
									// SE ASIGNA Sin Marca
									$('i_sin_marca<?php echo $i; ?>').checked = true;
									
									// SE ESTABLECE COMO MODIFICADA
									$('marca_modificada<?php echo $i; ?>').value = 'true';
									
									// SE HABILITA EL CAMPO DE LA CLAVE DEL EXPEDIENTE/NOTA
									habilitarClaveExpedienteComision(<?php echo $i; ?>);
									
									// SE ESTABLECE Sin marca
									$('i_nombre_marca<?php echo $i; ?>').value = "Sin marca";
							<?php
								}
							}
							?>
						});
					}
				<?php
				}
				?>
			});
		</script>
	<?php
	}
	
	public function retornar($mensaje = '', $tipo_mensaje = '')
	{
		$_SESSION['mensaje'] = $mensaje;
		$_SESSION['tipo_mensaje'] = $tipo_mensaje;
	?>
	    <script type="text/javascript">
			location.href = "index.php";// PARA VOLVER AL INICIO
	    </script>
	<?php
	}
}
?>
