<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaPorAntecedente extends VistaBase
{
    private $controlador;
    private $formulario;
    
    public function __construct()
    {
		$this->controlador = 'por_antecedente';
		$this->formulario = 'formPorAntecedente';
    }
    
    public function por_antecedente($listado = '', $mensaje = '', $filtro = '')
	{
		if ($mensaje != ''){echo '<div class="abm_mensaje_resultado">'.$mensaje.'</div>';}
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>
		
		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxResultadoConsulta" class="msc_gral msc_texto">
			
			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>
			
			<form action="consultas/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">
					
				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="por_antecedente" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="bpa_enviado" id="bpa_enviado" value="enviado" />
				<input type="hidden" name="c_cantidad_TOTAL" id="c_cantidad_TOTAL" value="<?php echo $filtro['c_cantidad']; ?>" />
				
				<div id="dragger_por_antecedente" class="msc_titulos degradado">Por Antecedente</div>  
				<div style="height:10px;font-size:0;"></div>
				<div style="height:32px;">
					<div class="mscpa_margen_medio_buscador"></div>
					<div class="mscpa_dato_buscador">
						<strong>N&uacute;mero: </strong><input type="text" name="bpa_numero" id="bpa_numero" value="<?php echo $filtro['bpa_numero']; ?>" onKeyPress="return soloEnteros(event);" size="4" maxlength="10" tabindex="1" />
					</div>
					<div class="mscpa_margen_medio_buscador"></div>
					<div class="mscpa_dato_buscador">
						<strong>A&ntilde;o: </strong><input type="text" name="bpa_anio" id="bpa_anio" value="<?php echo $filtro['bpa_anio']; ?>" onKeyPress="return soloEnteros(event);" size="4" maxlength="4" tabindex="2" />
					</div>
					<div class="mscpa_margen_medio_buscador"></div>
					<div id="msc_btBuscar_antecedente" class="p_boton_edicion" tabindex="3">
						<a href="javascript:buscar();" >
							<img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Buscar
						</a>
					</div>
					<div class="mscpa_margen_medio_buscador"></div>
					<div class="p_boton_edicion" tabindex="4">
						<a href="javascript:cerrarModal_por_antecedente('<?php echo $_SESSION['clave_expediente_referenciado']['anio']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['tipo']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['numero']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['cuerpo']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['alcance']; ?>');">
							<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Cerrar
						</a>
					</div> 
				</div>
				<div class="mscpa_borde1">
					<div class="mscpa_borde2">
						<?php
						if (!$filtro['bpa_enviado'])
						{
						?>		
							<!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->
							<div class="msc_fondo_item">Item de B&uacute;squeda por Antecedente</div>
							<div class="msc_fondo_item">Item de B&uacute;squeda por Antecedente</div>
							<div class="msc_fondo_item">Item de B&uacute;squeda por Antecedente</div>
							<div class="msc_fondo_item">Item de B&uacute;squeda por Antecedente</div>
						<?php 
						}
						else
						{
							if ( $listado == '' )
							{ 
								echo '<h1>Sin resultados</h1>'; 
							}
							else
							{
								$modelo = new consultaGralModel();	//Se crea una instancia del modelo		
								
								// SE LISTAN LAS FICHAS DE LOS EXPEDIENTES
								$cantidad = count($listado);
								for ($i=0; $i < $cantidad; $i++)
								{
									$ficha = &$listado[$i];
								?>
									<div class="mscpa_gral">
										<div style="height:19px;clear:both;">
										<div class="mscpa_expediente">
										
											<?php echo $ficha['anio']; ?>
											&nbsp;
											<?php echo $ficha['tipo']; ?>
											&nbsp;
											<?php echo $ficha['numero']; ?>
											&nbsp;
											<?php echo $ficha['cuerpo']; ?>
											&nbsp;
											<?php echo $ficha['alcance']; ?>
												  
										</div>	
										<div class="mscpa_titulo mscpa_tit_caratula">Car&aacute;tula:</div>
										<div class="mscpa_caratula">
											<?php echo $ficha['caratula']; ?>
										</div>	
										<!-- PRIMERO EL VALOR Y LUEGO EL TITULO POR LA POSICION RIGHT -->	
										<div class="mscpa_fecha_entrada">
											<?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?>
										</div>
										<div class="mscpa_titulo mscpa_tit_fecha">Fec. Expe.:</div>
										</div>
										<div style="height:19px;clear:both;">
											<div class="mscpa_titulo">Tema:&nbsp;</div>
											<div class="mscpa_tema_e_iniciador_categoria_y_autor">
												<select name="temas" class="msc_combo">
													<?php
													//SE OBTIENEN LOS Temas DEL Expediente RESULTANTE
													$temas = $modelo->obtenerTemasFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
													
													$cant_temas = count($temas);
													for ($t=0; $t < $cant_temas; $t++)
													{
														$tema = &$temas[$t];
													?>
														<option value="<?php echo $t; ?>"><?php echo $tema['descripcion_tema']; ?></option>
													<?php
													}	
													?>
												</select>
											</div>
											<div class="mscpa_titulo">Iniciador:&nbsp;</div>
											<div class="mscpa_tema_e_iniciador_categoria_y_autor">
												<?php echo $ficha['iniciador']; ?>
											</div>
										</div>
										<div style="height:19px;clear:both;">
											<div class="mscpa_titulo">Categor&iacute;a:&nbsp;</div>
											<div class="mscpa_tema_e_iniciador_categoria_y_autor">
												<?php echo $ficha['categoria']; ?>
											</div>
											<div class="mscpa_titulo">Autor:&nbsp;</div>
											<div class="mscpa_tema_e_iniciador_categoria_y_autor">
												<select name="autores" class="msc_combo">
													<?php
													//SE OBTIENEN LOS Autores DEL Expediente RESULTANTE
													$autores = $modelo->obtenerAutoresFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
								
													$cant_autores = count($autores);
													for ($a=0; $a < $cant_autores; $a++)
													{
														$autor = &$autores[$a];
													?>
														<option value="<?php echo $a; ?>"><?php echo $autor['descripcion_grp']; ?></option>
													<?php
													}
													?>
												</select>
											</div>
										</div>
										<?php
										//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE (LUEGO MOSTRAR UNA FICHA POR CADA UNO!!!)
										$proyectos = $modelo->obtenerProyectosFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
							
										//SE MUESTRAN LOS Proyectos DEL Expediente RESULTANTE (LUEGO MOSTRAR UNA FICHA POR CADA PROYECTO!!!)
										$cant_proyectos = count($proyectos);
										for ($p=0; $p < $cant_proyectos; $p++)
										{
											$proyecto = &$proyectos[$p];
										?>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo">Proyecto:&nbsp;</div>
												<div class="lcg_nrotipo_proyecto"><?php echo $p+1; //CONTADOR DE PROYECTOS ?></div>
												<div class="lcg_descripcion_proyecto"><?php echo $proyecto['descripcion_proyecto']; ?></div>
											</div>	
											<div style="height:40px;clear:both;">
												<div class="mscpa_titulo" style="height:37px;">Extracto:&nbsp;</div>
												<div class="mscpa_extracto"><?php echo $proyecto['extracto']; ?></div>
											</div>
										<?php
										}
										//SE OBTIENE EL Estado DEL Expediente RESULTANTE
										$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
										?>
										<div style="height:19px;clear:both;">
											<div class="mscpa_titulo">Estado:&nbsp;</div>
											<div class="mscpa_estado_comision"><?php echo $estado[0]['nombre_estado']; ?></div>
											<div class="mscpa_fechaestado_fechagiro"><?php echo $this->formatearFecha($estado[0]['fecha_estado']); ?></div>
										</div>
										<?php
										// SE OBTIENEN LOS id DE LOS ESTADOS 3, 16 y 79 (PUEDEN SER DIFERENTES AL codigo_estado)
										$id_estadoA = $modelo->obtenerIdSegunCodigo('hcd.expe_codestados', 'id_codestado', 'codigo_estado', 3);
										$id_estadoB = $modelo->obtenerIdSegunCodigo('hcd.expe_codestados', 'id_codestado', 'codigo_estado', 16);
										$id_estadoC = $modelo->obtenerIdSegunCodigo('hcd.expe_codestados', 'id_codestado', 'codigo_estado', 79);
										
										// SI EL Estado ES 3, 16 ó 79 SE OBTIENE LA Comision
										if ( ($estado[0]['id_codestado'] == $id_estadoA ) OR ($estado[0]['id_codestado'] == $id_estadoB ) OR ($estado[0]['id_codestado'] == $id_estadoC ) )
										{
											//SE OBTIENE LA Comision DEL Expediente RESULTANTE
											$comision = $modelo->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
										?>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo">Comisi&oacute;n:&nbsp;</div>
												<div class="mscpa_estado_comision"><?php echo $comision[0]['comision']; ?></div>
												<div class="mscpa_fechaestado_fechagiro"><?php echo $this->formatearFecha($comision[0]['fecha_giro']); ?></div>
											</div>
										<?php
										}
										?>  

										<?php
										//SE OBTIENE EL Antecedente DEL Expediente RESULTANTE
										$antecedente = $modelo->obtenerAntecedentesFicha($ficha['numero'], $ficha['anio']);
										?>
										<div style="height:19px;clear:both;">
											<div class="mscpa_titulo">Anteced.:&nbsp;</div>
											<div class="mscpa_antecedente">
												<?php echo $antecedente[0]['tipo_a']; ?>
												&nbsp;&nbsp;
												<?php echo $antecedente[0]['numero_a']; ?>
												&nbsp;&nbsp;&nbsp;&nbsp;
												<?php echo $antecedente[0]['digito_a']; ?>
												&nbsp;&nbsp;
												<?php echo $antecedente[0]['anio_a']; ?>
												&nbsp;&nbsp;
												<?php echo $antecedente[0]['cuerpo_a']; ?>
												&nbsp;&nbsp;
												<?php echo $antecedente[0]['alcance_a']; ?>
											</div>
											<div class="cyl_boton_ver_expediente_y_ficha">
												<a href="javascript:verExpediente('<?php echo $ficha['anio']; ?>', '<?php echo $ficha['tipo']; ?>', '<?php echo $ficha['numero']; ?>', '<?php echo $ficha['cuerpo']; ?>', '<?php echo $ficha['alcance']; ?>', 'por_antecedente');">Ver exped.</a>
											</div>
											<div class="cyl_boton_ver_expediente_y_ficha">
												<a href="javascript:modalGabyFicha('consultas/index.php?controlador=ficha&accion=ver_ficha_modal&anio=<?php echo $ficha['anio']; ?>&tipo=<?php echo $ficha['tipo']; ?>&numero=<?php echo $ficha['numero']; ?>&cuerpo=<?php echo $ficha['cuerpo']; ?>&alcance=<?php echo $ficha['alcance']; ?>');">Ficha</a>
											</div>
										</div>
									</div>
								<?php
								}//FIN DEL for
							}
						}
						?>	
					</div>	   
				</div>
			</form>
		</div>
		<script>
			
			function buscar()
			{
				var mensaje = '';
				var error = false;
			
				if ($('bpa_numero').value == ''){
					error = true;
				}
				
				if (error)
				{
					mensaje = "Debe ingresar el N"+'\u00fa'+"mero.";
					alert(mensaje);
				}
				else
				{	
					enviarForm('formPorAntecedente', 'consultas', 'contenidoAjaxResultadoConsulta');
				}	
			}
			
			$('bpa_numero').addEvent('keydown', function(event){
				if(event.key == 'Enter')
				{
					buscar();
				}
			});
			
			$('bpa_anio').addEvent('keydown', function(event){
				if(event.key == 'Enter')
				{
					buscar();
				}
			});
			
		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoConsulta'), {
			   handle: $('dragger_por_antecedente')
			});
		</script>		
    <?php
    }
	
}
?>
