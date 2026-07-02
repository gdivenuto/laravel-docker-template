<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaConsultaGral extends VistaBase
{
    private $controlador;
    private $formulario;
    private $nroPagina;

    public function __construct()
    {
		$this->controlador = 'consulta_gral';
		$this->formulario = 'formConsultaGral';
    }
    
    public function listar_principal($listado = '', $listadoIniciadores = '', $listadoAutores = '', $listadoComisiones = '', $listadoCategorias = '', $listadoTemas = '', $listadoEstados = '', $mensaje = '', $filtro = '')
    {
		//fputs(fopen('SESSION_filtro_cta_gral.txt', 'w'), print_r($_SESSION['filtro_cta_gral'], true));
		//fputs(fopen('filtro_listar_principal_cta_gral.txt', 'w'), print_r($filtro, true));
		
		if ($mensaje != ''){ echo '<div class="abm_mensaje_resultado">'.$mensaje.'</div>'; }
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
				<input type="hidden" name="accion" id="accion" value="listar_principal" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="c_enviado" id="c_enviado" value="enviado" />
				<input type="hidden" name="c_boton_presionado" id="c_boton_presionado" value="<?php echo $filtro['c_boton_presionado']; ?>" />
				<input type="hidden" name="c_cantidad_TOTAL" id="c_cantidad_TOTAL" value="<?php echo $filtro['c_cantidad']; ?>" />
				
				<div id="dragger_consulta_gral" class="msc_titulos degradado">Consulta General</div>
				<!--<div style="height:3px;font-size:0;"></div>-->
				<div style="height:130px;">
					<div class="msc_nombres_filtro">
						<div class="msc_dato_filtro">&nbsp;Iniciado por:</div>
						<div style="height:3px;font-size:0;"></div>
						<div class="msc_dato_filtro">&nbsp;El Autor es:</div>	
						<div style="height:3px;font-size:0;"></div>
						<div class="msc_dato_filtro">&nbsp;En Comisi&oacute;n:</div>
						<div style="height:7px;font-size:0;"></div>
						<div class="msc_dato_filtro">&nbsp;Ingreso del:</div>
						<div style="height:7px;font-size:0;"></div>
						<div class="msc_dato_filtro">&nbsp;Habilitados:</div>
					</div>
					<div class="msc_valores_filtro">
						<div id="msc_dato_filtro_iniciador" class="msc_dato_filtro">
							<select id="c_iniciado" name="c_iniciado" class="msc_combo msc_combo_ancho" style="width:128px;">
								<option value="0">0, TODOS</option>
								<?php
								$cant_iniciadores = count($listadoIniciadores);
								for ($i=0; $i < $cant_iniciadores; $i++)
								{
									$iniciador = &$listadoIniciadores[$i];
								?>	
									<option value="<?php echo $iniciador['tipo_grp'].'-'.$iniciador['codigo_grp']; ?>"><?php echo $iniciador['tipo_grp'].', '.$iniciador['codigo_grp'].', '.$iniciador['descripcion_grp']; ?></option>
								<?php
								}
								?>
							</select>
							&nbsp;
							<a href="javascript:modalGaby('consultas/index.php?controlador=consulta_gral&accion=pedirNombreIniciadorModal&c_solo_habilitado='+$('c_solo_habilitado').value+'');" title="Buscar por Nombre de Iniciador"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
						</div>
						<div style="height:3px;font-size:0;"></div>
						<div id="msc_dato_filtro_autor" class="msc_dato_filtro">
							<select id="c_autor" name="c_autor" class="msc_combo msc_combo_ancho" style="width:128px;">
								<option value="0">0, TODOS</option>
								<?php
								$cant_autores = count($listadoAutores);
								for ($a=0; $a < $cant_autores; $a++)
								{
									$autor = &$listadoAutores[$a];
								?>	
									<option value="<?php echo $autor['autor_tipo'].'-'.$autor['autor_codigo']; ?>"><?php echo $autor['autor_tipo'].', '.$autor['autor_codigo'].', '.$autor['autor_descripcion']; ?></option>
								<?php
								}
								?>
							</select>
							&nbsp;
							<a href="javascript:modalGaby('consultas/index.php?controlador=consulta_gral&accion=pedirNombreAutorModal&c_solo_habilitado='+$('c_solo_habilitado').value+'');" title="Buscar por Nombre de Autor"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
						</div>
						<div style="height:3px;font-size:0;"></div>
						<div id="msc_dato_filtro_comision" class="msc_dato_filtro">
							<select id="c_comision" name="c_comision" class="msc_combo msc_combo_ancho" style="width:128px;">
								<option value="0">0, TODAS</option>
								<?php
								$cant_comisiones = count($listadoComisiones);
								for ($c=0; $c < $cant_comisiones; $c++)
								{
									$comision = &$listadoComisiones[$c];
								?>	
									<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>"><?php echo $comision['tipo_grp'].', '.$comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
								<?php
								}
								?>
							</select>
							&nbsp;
							<a id="imagen_zoom_comisiones" href="javascript:modalGaby('consultas/index.php?controlador=consulta_gral&accion=pedirNombreComisionModal&c_solo_habilitado='+$('c_solo_habilitado').value+'');" title="Buscar por Nombre de Comisi&oacute;n"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
						</div>
						<div style="height:3px;font-size:0;"></div>
						<div class="msc_dato_filtro">
							<input type="text" id="c_fecha_desde" name="c_fecha_desde" value="<?php echo ($_SESSION['filtro_cta_gral']['c_fecha_desde']) ? $this->formatearFecha($_SESSION['filtro_cta_gral']['c_fecha_desde']) : '01/01/'.( date("Y")-10 ); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msc_btffecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">	
							&nbsp;al&nbsp;
							<input type="text" id="c_fecha_hasta" name="c_fecha_hasta" value="<?php echo ($_SESSION['filtro_cta_gral']['c_fecha_hasta']) ? $this->formatearFecha($_SESSION['filtro_cta_gral']['c_fecha_hasta']) : date("d/m/Y"); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msc_btffecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
						</div>
						<div style="height:7px;font-size:0;"></div>
						<div class="msc_dato_filtro">
							<input type="hidden" name="c_solo_habilitado" id="c_solo_habilitado" value="<?php echo($_SESSION['filtro_cta_gral']['c_solo_habilitado']) ? $_SESSION['filtro_cta_gral']['c_solo_habilitado'] : 1; ?>" />
							<input type="checkbox" name="habilitado" id="habilitado" style="vertical-align: middle;" onchange="javascript:chequear('c_solo_habilitado');refrescarCombos($('c_solo_habilitado').value);" >
						</div>
					</div>
					<div class="msc_margen_filtro"></div>
					
					<div class="msc_nombres_filtro">
						<div class="msc_dato_filtro">Categor&iacute;a:</div>
						<div style="height:3px;font-size:0;"></div>
						<div class="msc_dato_filtro">Con Tema:</div>
						<div style="height:3px;font-size:0;"></div>						
						<div class="msc_dato_filtro">Con Estado:</div>
						<div style="height:7px;font-size:0;"></div>
						<div class="msc_dato_filtro">Con Palabras:</div>
					</div>
					
					<div class="msc_valores_filtro2">	
						<div id="msc_dato_filtro_categoria" class="msc_dato_filtro">
							<select id="c_categoria" name="c_categoria" class="msc_combo" style="width:128px;">
								<option value="0">0, TODAS</option>
								<?php
								$cant_categorias = count($listadoCategorias);
								for ($cat=0; $cat < $cant_categorias; $cat++)
								{
									$categoria = &$listadoCategorias[$cat];
								?>
									<option value="<?php echo $categoria['id_codcategoria']; ?>"><?php echo $categoria['codigo_categoria'].', '.$categoria['descripcion_categoria']; ?></option>
								<?php
								}
								?>
							</select>	
							&nbsp;
							<a href="javascript:modalGaby('abms/index.php?controlador=categorias&accion=pedirNombreModal&c_solo_habilitado='+$('c_solo_habilitado').value+'');" title="Buscar por Nombre de Categor&iacute;a"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
						</div>
						<div style="height:3px;font-size:0;"></div>
						<div id="msc_dato_filtro_tema" class="msc_dato_filtro">
							<select id="c_tema" name="c_tema" class="msc_combo" style="width:128px;">
								<option value="0">0, TODOS</option>
								<?php
								$cant_temas = count($listadoTemas);
								for ($t=0; $t < $cant_temas; $t++)
								{
									$tema = &$listadoTemas[$t];
								?>	
									<option value="<?php echo $tema['id_codtema']; ?>"><?php echo $tema['codigo_tema'].', '.$tema['descripcion_tema']; ?></option>
								<?php
								}
								?>
							</select>
							&nbsp;
							<a href="javascript:modalGaby('abms/index.php?controlador=codtemas&accion=pedirNombreModal&c_solo_habilitado='+$('c_solo_habilitado').value+'');" title="Buscar por Nombre de Tema"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
						</div>
						<div style="height:3px;font-size:0;"></div>
						<div id="msc_dato_filtro_estado" class="msc_dato_filtro">
							<select id="c_estado" name="c_estado" class="msc_combo" style=" width:128px;">
								<option value="0">0, TODOS</option>
								<?php
								$cant_estados = count($listadoEstados);
								for ($e=0; $e < $cant_estados; $e++)
								{
									$estado = &$listadoEstados[$e];
								?>	
									<option value="<?php echo $estado['id_codestado']; ?>"><?php echo $estado['codigo_estado'].', '.$estado['nombre_estado']; ?></option>
								<?php
								}
								?>
							</select>
							&nbsp;
							<a id="imagen_zoom_estados" href="javascript:modalGaby('abms/index.php?controlador=codestados&accion=pedirNombreModal&c_solo_habilitado='+$('c_solo_habilitado').value+'');" title="Buscar por Nombre de Estado"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
						</div>
						<div style="height:3px;font-size:0;"></div>
						<div class="msc_dato_filtro">
							<input type="text" id="c_palabra" name="c_palabra" value="<?php echo $_SESSION['filtro_cta_gral']['c_palabra']; ?>" class="msc_fpalabra" />
						</div>
					</div>
					
					<div class="msc_botones">
						<?php
						if ( $filtro['c_enviado'] && $listado != '' )
						{
						?>
						  <div id="contenedor_btImpresion" class="msc_boton degradado">
							  <a id="btImpresion" href="#" title="Generar un formato para Impresi&oacute;n." ><img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;&nbsp;Imprimir</a>
						  </div>
						  <div id="contenedor_btProcesarTexto" class="msc_boton degradado">
							  <a id="btProcesarTexto" href="#" title="Abrir o Descargar como Documento de Texto." ><img src="imagenes/iconos_office/doc.jpg" width="16" height="16" align="left" />&nbsp;&nbsp;Procesar Texto</a>
						  </div>
						  <div id="contenedor_btGenerarCSV" class="msc_boton degradado">
							  <a id="btGenerarCSV" href="#" title="Abrir o Descargar como Planilla de C&aacute;lculo." ><img src="imagenes/iconos_office/xls.jpg" width="16" height="16" align="left" />&nbsp;&nbsp;Planilla de Calc.</a>
						  </div>
						<?php
						}
						?>
						<div class="msc_boton degradado">
							<a href="javascript:buscar();">
								<img src="imagenes/zoom_16x16.gif" width="16" height="16" />&nbsp;&nbsp;Buscar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:limpiar();">
								<img src="imagenes/barra/limpiar.jpeg" width="16" height="16" />&nbsp;&nbsp;Limpiar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:cerrarModal('<?php echo $_SESSION['clave_expediente_referenciado']['anio']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['tipo']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['numero']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['cuerpo']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['alcance']; ?>');">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
							</a>
						</div>
					</div>
				</div>
				<?php 
				if ( $filtro['c_enviado'] != '' )
				{
				?>	
					<!-- PAGINADOR -->
					<div class="msc_paginador">
						<div class="msc_margen_paginador"></div>
						<div class="msc_flechas_paginador">
						<?php
						$filtro_para_paginador = "&c_iniciado='+$('c_iniciado').value+'&c_autor='+$('c_autor').value+'&c_comision='+$('c_comision').value+'&c_fecha_desde='+$('c_fecha_desde').value+'&c_fecha_hasta='+$('c_fecha_hasta').value+'&c_categoria='+$('c_categoria').value+'&c_tema='+$('c_tema').value+'&c_estado='+$('c_estado').value+'&c_palabra='+$('c_palabra').value+'&c_solo_habilitado='+$('c_solo_habilitado').value+'&c_enviado=enviado";
						
						if ($filtro['c_pagina'] != 1)
						{
						?>
							<a id="btPrimero" title="Primer Registro" href="javascript:refrescar('consultas/index.php?controlador=consulta_gral&accion=listar_principal<?php echo $filtro_para_paginador; ?>&c_pagina=1', 'contenidoAjaxResultadoConsulta');">
								<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="14" height="14" />
							</a>
						<?php
						}
						else
						{
						?>
							<a id="btPrimero" href="#">
								<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" />
							</a>
						<?php
						}
						if ($filtro['c_pagina_ant'] != 0){
						?>
							<a id="btAnterior" title="Registro Anterior" href="javascript:refrescar('consultas/index.php?controlador=consulta_gral&accion=listar_principal<?php echo $filtro_para_paginador; ?>&c_pagina=<?php echo $filtro['c_pagina_ant']; ?>', 'contenidoAjaxResultadoConsulta');">
								<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="14" height="14" />
							</a>
						<?php
						}
						else
						{
						?>
							<a id="btAnterior" href="#">
								<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" />
							</a>
						<?php
						}
						?>
						</div>  
						<div class="msc_detalle_paginador">
							<?php echo $filtro['c_pagina'].' de '.$filtro['c_nro_paginas']; ?>
						</div>  
						<div class="msc_flechas_paginador">
						<?php
						if ($filtro['c_pagina'] != $filtro['c_nro_paginas'])
						{
						?>
							<a id="btSiguiente" title="Registro Siguiente" href="javascript:refrescar('consultas/index.php?controlador=consulta_gral&accion=listar_principal<?php echo $filtro_para_paginador; ?>&c_pagina=<?php echo $filtro['c_pagina_sgte']; ?>', 'contenidoAjaxResultadoConsulta');">
								<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="14" height="14" />
							</a>
						<?php
						}
						else
						{
						?>
							<a id="btSiguiente" href="#">
								<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" />
							</a>
						<?php
						}
						if ($filtro['c_pagina'] != $filtro['c_nro_paginas'])
						{
						?>
							<a id="btUltimo" title="Ultimo Registro" href="javascript:refrescar('consultas/index.php?controlador=consulta_gral&accion=listar_principal<?php echo $filtro_para_paginador; ?>&c_pagina=<?php echo $filtro['c_nro_paginas']; ?>', 'contenidoAjaxResultadoConsulta');">
								<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="14" height="14" />
							</a>
						<?php
						}
						else
						{
						?>
							<a id="btUltimo" href="#">
								<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" />
							</a>
						<?php
						}
						?>
						</div>
					</div>
					<!-- FIN DEL PAGINADOR -->
				<?php 
				}
				?>
				<div class="msc_borde1">
					<div id="msc_borde2" class="msc_borde2">
						
						<?php
						// SI NO SE HA BUSCADO AÚN
						if ( !$filtro['c_enviado'] )
						{
						?>	
							<!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->	
							<div class="msc_fondo_item">Item de la Consulta General</div>
							<div class="msc_fondo_item">Item de la Consulta General</div>
							<div class="msc_fondo_item">Item de la Consulta General</div>
							<div class="msc_fondo_item">Item de la Consulta General</div>
							<div class="msc_fondo_item">Item de la Consulta General</div>
						<?php 
						}
						else
						{
							if ( $listado == '' )
							{ 
								echo '<br><h1>Sin resultados</h1>';
							}
							else
							{
								//Se crea una instancia del modelo
								$modelo = new consultaGralModel();
								
								//fputs(fopen('listado_consulta_gral.txt', 'w'),print_r($listado, true));
								//SE LISTAN LAS FICHAS DE LOS EXPEDIENTES
								$cantidad = count($listado);
								for ($exp=0; $exp < $cantidad; $exp++)
								{
									$ficha = &$listado[$exp];
							?>
									<div class="mscpa_gral">
										<div style="height:27px;clear:">
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
												<?php
												$caratula = $modelo->obtenerCaratula($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
												echo $caratula;
												?>
											</div>
											<!-- PRIMERO EL VALOR Y LUEGO EL TITULO POR LA POSICION RIGHT -->
											<div class="mscpa_fecha_entrada">
												<?php 
												$fecha_entrada_expe = $modelo->obtenerFechaEntradaExpe($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);	
												echo $this->formatearFecha($fecha_entrada_expe); 
												?>
											</div>	
											<div class="mscpa_titulo mscpa_tit_fecha">Fecha Expe.:</div>									
											
										</div>
										<div style="height:19px;clear:both;">
											<div class="mscpa_titulo">Tema:&nbsp;</div>
											<div class="mscpa_tema_e_iniciador_categoria_y_autor">
												<select name="temas" class="msc_combo">
												<?php
												//SE OBTIENEN LOS Temas DEL Expediente RESULTANTE
												$temas = $modelo->obtenerTemasFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
												$cant_t = count($temas);
												for ($t=0; $t < $cant_t; $t++){
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
											<?php 
											//SE OBTIENE EL Iniciador DEL Expediente RESULTANTE
											$iniciador = $modelo->obtenerIniciador($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
											echo $iniciador;
											?>
											</div>
										</div>
										<div style="height:19px;clear:both;">
											<div class="mscpa_titulo">Categor&iacute;a:&nbsp;</div>
											<div class="mscpa_tema_e_iniciador_categoria_y_autor">
											<?php 
											//SE OBTIENE LA Categoria DEL Expediente RESULTANTE
											$categoria = $modelo->obtenerCategoria($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
											echo $categoria; 
											?>
											</div>
											<div class="mscpa_titulo">Autor:&nbsp;</div>
											<div class="mscpa_tema_e_iniciador_categoria_y_autor">
												<select name="autores" class="msc_combo">
												<?php
												//SE OBTIENEN LOS Autores DEL Expediente RESULTANTE
												$autores = $modelo->obtenerAutoresFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
												$cant_a = count($autores);
												for ($a=0; $a < $cant_a; $a++){
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
										$cant_p = count($proyectos);
										for ($p=0; $p < $cant_p; $p++)
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
										$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $filtro['c_estado']);
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
											$comision = $modelo->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $ficha['comision_tipo'], $ficha['comision_codigo']);
											//fputs(fopen('comision_en_la_vista'.$p.'.txt','w'),print_r($comision, true));
										?>
											<div style="height:19px;clear:both;">
											<div class="mscpa_titulo">Comisi&oacute;n:&nbsp;</div>
											<div class="mscpa_estado_comision"><?php echo $comision[0]['comision']; ?></div>
											<div class="mscpa_fechaestado_fechagiro"><?php echo $this->formatearFecha($comision[0]['fecha_giro']); ?></div>
											</div>
										<?php 
										}
										?>
										<div style="height:19px;clear:both;">
											<div class="cyl_boton_ver_expediente_y_ficha">
												<a href="javascript:verExpediente('<?php echo $ficha['anio']; ?>', '<?php echo $ficha['tipo']; ?>', '<?php echo $ficha['numero']; ?>', '<?php echo $ficha['cuerpo']; ?>', '<?php echo $ficha['alcance']; ?>', 'consulta_gral');">Ver exped.</a>
											</div>
											<div class="cyl_boton_ver_expediente_y_ficha">
												<a href="javascript:modalGabyFicha('consultas/index.php?controlador=ficha&accion=ver_ficha_modal&anio=<?php echo $ficha['anio']; ?>&tipo=<?php echo $ficha['tipo']; ?>&numero=<?php echo $ficha['numero']; ?>&cuerpo=<?php echo $ficha['cuerpo']; ?>&alcance=<?php echo $ficha['alcance']; ?>');">Ficha</a>
											</div>
										</div>
									</div>
							<?php
								}//FIN DEL for
							?>    
								<script type="text/javascript">
								  var scroller = new Fx.Scroll($('msc_borde2'));
								  scroller.toTop();
								</script>
						<?php        
							}
						}
						?>	
					</div><!-- FIN DE msc_borde2 -->
				</div><!-- FIN DE msc_borde1 -->
				
				<!-- 05/06/2015: AGREGADO EL TOTAL DE EXPEDIENTES/NOTAS DEL RESULTADO DE LA CONSULTA GENERAL -->
				<div class="msc_totalizador_pie_ventana_modal degradado" >
					Total de Expedientes / Notas: <?php echo $filtro['c_cantidad']; ?>
				</div>
			</form>
		</div>	
		<script>
			// CALENDARIO PARA LA FECHA DESDE
			var calDesde = new Zapatec.Calendar.setup({
				
				inputField:"c_fecha_desde",
				ifFormat:"%d/%m/%Y",
				button:"msc_btffecha_desde",
				showsTime:false
			});
			
			// CALENDARIO PARA LA FECHA HASTA
			var calHasta = new Zapatec.Calendar.setup({
				
				inputField	:"c_fecha_hasta",
				ifFormat	:"%d/%m/%Y",
				button		:"msc_btffecha_hasta",
				showsTime	:false
			});
			
		    function refrescarCombos(habilitado)
		    {
				refrescarCombo('consultas/index.php?controlador=consulta_gral&accion=refrescarComboIniciadores&habilitado='+habilitado+'&iniciador=<?php echo $_SESSION['filtro_cta_gral']['c_iniciado']; ?>','msc_dato_filtro_iniciador');
				refrescarCombo('consultas/index.php?controlador=consulta_gral&accion=refrescarComboCategorias&habilitado='+habilitado+'&categoria=<?php echo $_SESSION['filtro_cta_gral']['c_categoria']; ?>','msc_dato_filtro_categoria');
				refrescarCombo('consultas/index.php?controlador=consulta_gral&accion=refrescarComboAutores&habilitado='+habilitado+'&autor=<?php echo $_SESSION['filtro_cta_gral']['c_autor']; ?>','msc_dato_filtro_autor');
				refrescarCombo('consultas/index.php?controlador=consulta_gral&accion=refrescarComboTemas&habilitado='+habilitado+'&tema=<?php echo $_SESSION['filtro_cta_gral']['c_tema']; ?>','msc_dato_filtro_tema');
				refrescarCombo('consultas/index.php?controlador=consulta_gral&accion=refrescarComboEstados&habilitado='+habilitado+'&estado=<?php echo $_SESSION['filtro_cta_gral']['c_estado']; ?>','msc_dato_filtro_estado');
				refrescarCombo('consultas/index.php?controlador=consulta_gral&accion=refrescarComboComisiones&habilitado='+habilitado+'&comision=<?php echo $_SESSION['filtro_cta_gral']['c_comision']; ?>','msc_dato_filtro_comision');
		    }
		    
			function buscar()
			{
				var mensaje = '';
				var error = false;
			
				if ( !fecha_valida($('c_fecha_desde').value) )
				{
					error = true;
					mensaje = "Debe ingresar un valor v"+'\u00e1'+"lido para la Fecha Desde y/o la Fecha Hasta.<br>Gracias.";
				}
				if ( !fecha_valida($('c_fecha_hasta').value) )
				{
					error = true;
					mensaje = "Debe ingresar un valor v"+'\u00e1'+"lido para la Fecha Desde y/o la Fecha Hasta.<br>Gracias.";
				}

				if ( esLaFechaMayor($('c_fecha_desde').value, $('c_fecha_hasta').value) )
				{
					error = true;
					mensaje = "La fecha Desde debe ser menor a la fecha Hasta en el criterio de busqueda.";
				}

				if (error){
					alert(mensaje);
				}else{	
					enviarForm('formConsultaGral', 'consultas', 'contenidoAjaxResultadoConsulta');
				}	
			}
			
			function limpiar()
			{
				$('c_iniciado').value = 0;
				$('c_autor').value = 0;
				$('c_comision').value = 0;
				$('c_categoria').value = 0;
				$('c_tema').value = 0;
				$('c_estado').value = 0;
				$('c_palabra').value = '';
				$('c_fecha_desde').value = '<?php echo '01/01/'.( date("Y")-10 ); ?>';
				$('c_fecha_hasta').value = '<?php echo date("d/m/Y"); ?>';
			}
			
			$('c_comision').addEvent('change', function()
			{
				if ( $('c_comision').value != 0 ){
					$('c_estado').disabled = true;
					$('imagen_zoom_estados').setStyle('display', 'none');
				}else{
					$('c_estado').disabled = false;
					$('imagen_zoom_estados').setStyle('display', 'inline');
				}
			});
			
			$('c_estado').addEvent('change', function()
			{
				if ( $('c_estado').value != 0 ){
					$('c_comision').disabled = true;
					$('imagen_zoom_comisiones').setStyle('display', 'none');
				}else{
					$('c_comision').disabled = false;
					$('imagen_zoom_comisiones').setStyle('display', 'inline');
				}
			});
			
			$('c_iniciado').value = <?php echo ($_SESSION['filtro_cta_gral']['c_iniciado']) ? "'".$_SESSION['filtro_cta_gral']['c_iniciado']."'" : 0 ; ?>;
			$('c_autor').value = <?php echo ($_SESSION['filtro_cta_gral']['c_autor']) ? "'".$_SESSION['filtro_cta_gral']['c_autor']."'" : 0 ; ?>;
			$('c_comision').value = <?php echo ($_SESSION['filtro_cta_gral']['c_comision']) ? "'".$_SESSION['filtro_cta_gral']['c_comision']."'" : 0 ; ?>;
			$('c_categoria').value = <?php echo ($_SESSION['filtro_cta_gral']['c_categoria']) ? $_SESSION['filtro_cta_gral']['c_categoria'] : 0 ; ?>;
			$('c_tema').value = <?php echo ($_SESSION['filtro_cta_gral']['c_tema']) ? $_SESSION['filtro_cta_gral']['c_tema'] : 0 ; ?>;
			$('c_estado').value = <?php echo ($_SESSION['filtro_cta_gral']['c_estado']) ? $_SESSION['filtro_cta_gral']['c_estado'] : 0 ; ?>;
			
			// SI SE FILTRA POR Estado, SE DESHABILITA LA Comision
			if ( $('c_estado').value != 0 )
			{
				$('c_comision').disabled = true;
				$('imagen_zoom_comisiones').setStyle('display', 'none');
			}
			else
			{
				$('c_comision').disabled = false;
				$('imagen_zoom_comisiones').setStyle('display', 'inline');
			}
			
			// SI SE FILTRA POR Comision, SE DESHABILITA EL Estado 
			if ( $('c_comision').value != 0 )
			{
				$('c_estado').disabled = true;
				$('imagen_zoom_estados').setStyle('display', 'none');
			}
			else
			{
				$('c_estado').disabled = false;
				$('imagen_zoom_estados').setStyle('display', 'inline');
			}
			
			$('c_fecha_desde').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msc_btffecha_desde').disabled = false;
					$('msc_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msc_btffecha_desde').disabled = true;
						$('msc_btffecha_hasta').disabled = true;
						
						buscar();
					}
				}
			});
			
			$('c_fecha_hasta').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msc_btffecha_desde').disabled = false;
					$('msc_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msc_btffecha_desde').disabled = true;
						$('msc_btffecha_hasta').disabled = true;
						
						buscar();
					}
				}
			});
			
			$('c_palabra').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msc_btffecha_desde').disabled = false;
					$('msc_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msc_btffecha_desde').disabled = true;
						$('msc_btffecha_hasta').disabled = true;
						
						if( $('c_palabra').value != '' )
						{	
							buscar();
						}
					}
				}
			});
			
			<?php
		    if ( $_SESSION['filtro_cta_gral']['c_solo_habilitado'] == '' )
		    {
			?>
				$('habilitado').checked = true;
				$('c_solo_habilitado').value = 1;  
		    <?php 
		    }
		    elseif ( $_SESSION['filtro_cta_gral']['c_solo_habilitado'] == 0 )
		    {
			?>
				$('habilitado').checked = false;
				$('c_solo_habilitado').value = 0;
		    <?php 
		    }
		    else
		    {
			?>
				$('habilitado').checked = true;
				$('c_solo_habilitado').value = 1;
		    <?php 
		    }
		    ?>
		    
			<?php 
			// SI SE HAN OBTENIDO RESULTADOS, SE CONFIGURAN LOS BOTONES DE FORMATO DE IMPRESION, FORMATO PARA PROCESADOR DE TEXTO Y PLANILLA DE CALCULO
			if ( $listado != '' )
			{
			?>
				$('btImpresion').addEvent('click', function()
				{
				  	if ($('c_cantidad_TOTAL').value >= 5000)
				  	{
						alert("El resultado de su consulta es muy largo para imprimir.");
				  	}
				  	else
				  	{
						$('btImpresion').setProperty('href', 'consultas/index.php?controlador=consulta_gral&accion=armar_listado_completo&formato=impresion&c_iniciado='+$('c_iniciado').value+'&c_autor='+$('c_autor').value+'&c_comision='+$('c_comision').value+'&c_fecha_desde='+$('c_fecha_desde').value+'&c_fecha_hasta='+$('c_fecha_hasta').value+'&c_categoria='+$('c_categoria').value+'&c_tema='+$('c_tema').value+'&c_estado='+$('c_estado').value+'&c_palabra='+$('c_palabra').value+'');
					  	$('btImpresion').setProperty('target', '_blank');
					  	$('contenedor_btImpresion').setStyle('display', 'none');
					  	$('contenedor_btProcesarTexto').setStyle('display', 'none');
					  	$('contenedor_btGenerarCSV').setStyle('display', 'none');
				  	}
				});
			
				$('btProcesarTexto').addEvent('click', function()
				{
				  	if ($('c_cantidad_TOTAL').value >= 5000)
				  	{
						alert("El resultado de su consulta es muy largo para procesar su texto.");
				  	}
				  	else
				  	{
						$('btProcesarTexto').setProperty('href', 'consultas/index.php?controlador=consulta_gral&accion=armar_listado_completo&formato=texto&c_iniciado='+$('c_iniciado').value+'&c_autor='+$('c_autor').value+'&c_comision='+$('c_comision').value+'&c_fecha_desde='+$('c_fecha_desde').value+'&c_fecha_hasta='+$('c_fecha_hasta').value+'&c_categoria='+$('c_categoria').value+'&c_tema='+$('c_tema').value+'&c_estado='+$('c_estado').value+'&c_palabra='+$('c_palabra').value+'');
						$('btProcesarTexto').setProperty('target', '_blank');
						$('contenedor_btProcesarTexto').setStyle('display', 'none');
						$('contenedor_btImpresion').setStyle('display', 'none');
						$('contenedor_btGenerarCSV').setStyle('display', 'none');
				  	}
				});

				$('btGenerarCSV').addEvent('click', function()
				{
				  	if ($('c_cantidad_TOTAL').value >= 5000)
				  	{
						alert("El resultado de su consulta es muy largo para generar la planilla.");
				  	}
				  	else
				  	{
						$('btGenerarCSV').setProperty('href', 'consultas/index.php?controlador=consulta_gral&accion=armar_listado_completo&formato=csv&c_iniciado='+$('c_iniciado').value+'&c_autor='+$('c_autor').value+'&c_comision='+$('c_comision').value+'&c_fecha_desde='+$('c_fecha_desde').value+'&c_fecha_hasta='+$('c_fecha_hasta').value+'&c_categoria='+$('c_categoria').value+'&c_tema='+$('c_tema').value+'&c_estado='+$('c_estado').value+'&c_palabra='+$('c_palabra').value+'');
						$('btGenerarCSV').setProperty('target', '_blank');
						$('contenedor_btProcesarTexto').setStyle('display', 'none');
						$('contenedor_btImpresion').setStyle('display', 'none');
						$('contenedor_btGenerarCSV').setStyle('display', 'none');
				  	}
				});
			<?php	
			}
			
			// 27/11/2012
			// SI SE VUELVE DE HABER VISTO EL EXPEDIENTE 
			if ( $_SESSION['cerrar_modal_cta_gral'] == 'no' )
			{
				$_SESSION['cerrar_modal_cta_gral'] = null;
			?>
				refrescar('consultas/index.php?controlador=consulta_gral&accion=listar_principal&c_iniciado='+$('c_iniciado').value+'&c_autor='+$('c_autor').value+'&c_comision='+$('c_comision').value+'&c_fecha_desde='+$('c_fecha_desde').value+'&c_fecha_hasta='+$('c_fecha_hasta').value+'&c_categoria='+$('c_categoria').value+'&c_tema='+$('c_tema').value+'&c_estado='+$('c_estado').value+'&c_palabra='+$('c_palabra').value+'&c_solo_habilitado='+$('c_solo_habilitado').value+'&c_enviado=enviado&c_pagina=<?php echo $_SESSION['filtro_cta_gral']['c_pagina']; ?>', 'contenidoAjaxResultadoConsulta');
			<?php
			}
			?>
			
		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoConsulta'), {
			   handle: $('dragger_consulta_gral')
			  }
			);
		</script>
    <?php
    }

    /**
     * SE GENERA EL LISTADO A IMPRIMIR
     * 
     * @param array $listado_para_impresion
     * @param array $filtro_para_impresion
     */
    public function generar_formato_para_impresion($listado_para_impresion = '', $filtro_para_impresion = '')
    {
		header("Content-Type: text/html; charset=UTF-8");
		$modelo = new consultaGralModel();
		?>
		<style type="text/css">
			.imp_texto {
				font-family: Arial;
				font-size: 12px;
			}
			.imp_titulo_general {
				padding:10px 0 0 150px;
				font-family: Arial;
				font-size: 17px;
				font-weight:700;
				text-decoration:underline;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			.imp_subtitulo_general{
				padding:10px 0 20px 10px;
				font-size:14px;
				font-weight:700;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;  
			}
			/************************************************************/
			.imp_titulos_de_clave{
				height:25px;
				border:2px solid #000;
				border-bottom:0;
				clear:both;
			}
			.imp_titulos_de_clave_izq{
				width:395px;
				height:20px;
				padding:5px 0 0 5px;
				float:left;
				font-size:14px;
				font-weight:700;
			}
			.imp_titulos_de_clave_der{
				width:125px;
				height:20px;
				padding:5px 5px 0 0;
				float:right;
				text-align:right;
				font-size:14px;
			}
			.imp_ficha_titulos_de_clave{
				height:25px;
				border-top:2px solid #000;
			}
			.imp_ficha_titulos_de_clave_izq{
				width:537px;
				height:20px;
				padding:5px 0 0 5px;
				float:left;
				font-size:14px;
				font-weight:700;
			}
			/************************************************************
				PARA LA FICHA DEL EXPEDIENTE
			************************************************************/
			.imp_bordes{
				border-left:2px solid #000;
				border-right:2px solid #000;
				border-bottom:2px solid #000;
			}
			.imp_ficha{
				height:20px;
				clear:both;
			}
			.imp_ficha_nombre{
				width:145px;
				height:20px;
				padding-left:5px;
				float:left;
			}
			.imp_ficha_valor{
				width:250px;
				height:20px;
				float:left;
			}
			.imp_ficha_valor_mas_largo{
				width:300px;
			}
			.imp_ficha_valor_caratula {
				padding: 0 5px 0 0;
				height: 20px;
				float: left;
			}
			.imp_ficha_valor_temas_autores {
				width: 500px;
			}
			.imp_ficha_valor_orden_proyecto{
				width:20px;
				height:20px;
				float:right;
			}
			.imp_ficha_extracto{
				padding:5px 0 5px 0;
				font-size:12px;
				clear:both;
			}
			.imp_ficha_nombre_extracto{
				width:145px;
				padding:5px 0 5px 5px;
				float:left;
			}
			.imp_ficha_valor_extracto{
				width:550px;
				padding:5px 0 5px 0;
				float:left;
			}
			.imp_ficha_fecha_estado {
				height: 20px;
				padding-right: 10px;
				text-align: right;
				float: right;
			}
			.btImprimir{
				text-align:center;
				padding:7px;
			}
		</style>
		
		<style media="print" type="text/css">
			#btImprimir {
			   display:none;
			}
		</style>
		
		<div id="btImprimir" class="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();">
		</div>	
		
		<?php $this->encabezado_reporte(); ?>
		
		<div class="imp_titulo_general">Consulta General</div>

		<?php $this->criterio_busqueda_reporte($filtro_para_impresion); ?>
		
		<div class="imp_texto imp_titulos_de_clave">
			<div class="imp_titulos_de_clave_izq">A&ntilde;o&nbsp;&nbsp;&nbsp;T.&nbsp;&nbsp;Nro.&nbsp;&nbsp;Cpo.&nbsp;Alc.</div>
			<div class="imp_titulos_de_clave_der">Fecha Ingreso</div>
		</div>
		<div class="imp_bordes">    
			<?php
			$cantidad = count($listado_para_impresion);
			for ($f=0; $f < $cantidad; $f++)
			{
				$ficha = &$listado_para_impresion[$f];
			?>
				<div class="imp_texto imp_ficha_titulos_de_clave">
					<div class="imp_ficha_titulos_de_clave_izq"><?php echo $ficha['anio']; ?>&nbsp;<?php echo $ficha['tipo']; ?>&nbsp;<?php echo $ficha['numero']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['cuerpo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['alcance']; ?></div>
					<div class="imp_titulos_de_clave_der"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
				</div>
				<div class="imp_texto imp_ficha">	
					<div class="imp_ficha_nombre">Car&aacute;tula</div>
					<div class="imp_ficha_valor_caratula"><?php echo $ficha['caratula']; ?></div>
				</div>
				<div class="imp_texto imp_ficha">	
					<div class="imp_ficha_nombre">Iniciador / Categor&iacute;a</div>
					<div class="imp_ficha_valor_extracto ">
						<?php echo $ficha['iniciador']; ?>&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;<?php echo $ficha['categoria']; ?>
					</div>
				</div>
				<div class="imp_texto imp_ficha">	
					<div class="imp_ficha_nombre">Temas</div>
					<div class="imp_ficha_valor imp_ficha_valor_temas_autores">
						<?php 
						$cantidad_temas = count($ficha['temas']);
						for ($t=0; $t < $cantidad_temas; $t++)
						{
							$tema = &$ficha['temas'][$t];
							echo $tema['descripcion_tema']; 
							if ( $t != ($cantidad_temas - 1) ) echo '<b>;</b> ';
						}
						?>
					</div>
				</div>
				<div class="imp_texto imp_ficha">	
					<div class="imp_ficha_nombre">Autores</div>
					<div class="imp_ficha_valor imp_ficha_valor_temas_autores">
						<?php 
						$cantidad_autores = count($ficha['autores']);
						for ($a=0; $a < $cantidad_autores; $a++)
						{
							$autor = &$ficha['autores'][$a];
							echo $autor['descripcion_grp']; 
							if ( $a != ($cantidad_autores - 1) ) echo '<b>;</b> ';
						}
						?>
					</div>
				</div>	
				<?php
				$cantidad_proy = count($ficha['proyectos']);
				for ($p=0; $p < $cantidad_proy; $p++)
				{
					$proyecto = &$ficha['proyectos'][$p];
				?>
					<div class="imp_texto imp_ficha">	
						<div class="imp_ficha_nombre">Proyecto de</div>
						<div class="imp_ficha_valor"><?php echo $proyecto['descripcion_proyecto']; ?></div>
					</div>			
					<div class="imp_texto imp_ficha">	
						<div class="imp_ficha_nombre">Extracto</div>
						<div class="imp_ficha_valor_extracto"><?php echo $proyecto['extracto']; ?></div>
					</div>	
				<?php  
				}
				?>
				<div class="imp_texto imp_ficha">	
					<?php 
						$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $ficha['id_codestado']);
					?>
					<div class="imp_ficha_nombre">Estado / Desde</div>
					<div class="imp_ficha_valor imp_ficha_valor_mas_largo"><?php echo $estado[0]['nombre_estado']; ?></div>
					<div class="imp_ficha_fecha_estado"><?php echo $this->formatearFecha($estado[0]['fecha_estado']); ?></div>
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
					$comision = $modelo->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $ficha['comision_tipo'], $ficha['comision_codigo']);
					?>
					<div class="imp_texto imp_ficha">	
						<div class="imp_ficha_nombre">Comisi&oacute;n / Desde</div>
						<div class="imp_ficha_valor_caratula"><?php echo $comision[0]['comision']; ?></div>
						<div class="imp_ficha_fecha_estado"><?php echo $this->formatearFecha($comision[0]['fecha_giro']); ?></div>
					</div>
				<?php  
				}
			  
			} // FIN DEL for		
			?>
		</div>
	<?php
		echo $this->pie_reporte();
	?>
		<div id="btImprimir" class="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();">
		</div>
	<?php
    }
/******************************************************************************************************************
	ENCABEZADO DEL REPORTE EN FORMATO PARA IMPRIMIR
******************************************************************************************************************/	
    private function encabezado_reporte()
    {
    ?>  
		<style type="text/css">
			.imp_texto {
				font-family: Arial;
				font-size: 12px;
			}
			.imp_encabezado{
				height:122px;
				border:2px solid #000;
			}
			.imp_encabezado_logo{
				width:167px;
				height:122px;
				float:left;
			}
			.imp_encabezado_titulos{
				width:470px;
				height:122px;
				float:left;
			}
			.imp_encabezado_titulo1{
				width:470px;
				padding-top:20px;
				text-align:left;
				font-size:14px;
				font-weight:700;
			}
			.imp_encabezado_titulo2{
				width:470px;
				padding-top:10px;
				font-size:14px;
				text-align:right;
			}
			.imp_encabezado_titulo3{
				width:470px;
				padding-top:10px;
				text-align:left;
				font-size:17px;
				font-weight:700;
			}
		</style>
		<div class="imp_encabezado">
			<div class="imp_encabezado_logo"><img src="../imagenes/escudo_cuatro_colores.gif" width="102" height="119" ></div>
			<div class="imp_texto imp_encabezado_titulos">
				<div class="imp_encabezado_titulo1">Municipalidad de General Pueyrredon</div>
				<div class="imp_encabezado_titulo2">Sistema de Expedientes</div>
				<div class="imp_encabezado_titulo3">Honorable Concejo Deliberante</div>
			</div>
		</div>
    <?php
    }
/********************************************************************************************************************
	CRITERIO DE BUSQUEDA EN FORMATO PARA IMPRIMIR
******************************************************************************************************************/
    private function criterio_busqueda_reporte($filtro_para_pdf)
    {
		$modelo = new consultaGralModel();
		?>
		<style type="text/css">
			.imp_texto {
				font-family: Arial;
				font-size: 12px;
			}
			.imp_criterios_busqueda {
				height: 20px;
				border-left: 2px solid #000;
				border-right: 2px solid #000;
			}
			.imp_criterios_nombre {
				height: 14px;
				padding: 3px 0 3px 15px;
				float: left;
				font-weight: 700;
			}
			.imp_criterios_valor {
				height: 14px;
				padding: 3px 0 3px 15px;
				float: left;
			}
			.imp_criterios_busqueda_margen_inferior {
				height: 5px;
				clear: both;
				font-size: 0;
				border-left: 2px solid #000;
				border-right: 2px solid #000;
			}
		</style>
		
		<div class="imp_criterios_busqueda_margen_inferior"></div>
		<?php 
		// PARA Iniciador - Categoria
		if ( $filtro_para_pdf['c_iniciado_tipo'] || $filtro_para_pdf['c_categoria'] )
		{
		?>
			<div class="imp_texto imp_criterios_busqueda">
				<?php 
				if ( $filtro_para_pdf['c_iniciado_tipo'] )
				{
					$iniciador = $modelo->obtenerNombreIniciador($filtro_para_pdf['c_iniciado_tipo'], $filtro_para_pdf['c_iniciado_codigo']);
				?>
					<div class="imp_criterios_nombre">Iniciado:</div>
					<div class="imp_criterios_valor"><?php echo $iniciador; ?></div>
				<?php
				}
				if ( $filtro_para_pdf['c_categoria'] )
				{
					$categoria = $modelo->obtenerNombreCategoria($filtro_para_pdf['c_categoria']);
				?>
					<div class="imp_criterios_nombre">Categor&iacute;a:</div>
					<div class="imp_criterios_valor"><?php echo $categoria; ?></div>
				<?php
				}
				?>
			</div>
		<?php
		}
		?>

		<?php 
		// PARA Autor - Tema
		if ( $filtro_para_pdf['c_autor_tipo'] || $filtro_para_pdf['c_tema'] )
		{
		?> 
			<div class="imp_texto imp_criterios_busqueda">
				<?php 
				if ( $filtro_para_pdf['c_autor_tipo'] )
				{
					$autor = $modelo->obtenerNombreAutor($filtro_para_pdf['c_autor_tipo'], $filtro_para_pdf['c_autor_codigo']);//SE OBTIENE EL NOMBRE DEL Autor
				?>
					<div class="imp_criterios_nombre">Autor:</div>
					<div class="imp_criterios_valor"><?php echo $autor; ?></div>
				<?php 
				}
				if ( $filtro_para_pdf['c_tema'] )
				{
					$tema = $modelo->obtenerNombreTema($filtro_para_pdf['c_tema']);//SE OBTIENE EL NOMBRE DEL Tema
				?>
					<div class="imp_criterios_nombre">Tema:</div>
					<div class="imp_criterios_valor"><?php echo $tema; ?></div>
				<?php
				}
				?>
			</div>
		<?php
		}
		?>
		
		<?php 
		// PARA Estado - Comision
		if ( $filtro_para_pdf['c_estado'] || $filtro_para_pdf['c_comision_tipo'] )
		{
		?>
			<div class="imp_texto imp_criterios_busqueda">
				<?php 
				if ( $filtro_para_pdf['c_estado'] )
				{
					$estado = $modelo->obtenerNombreEstado($filtro_para_pdf['c_estado']);
				?>
					<div class="imp_criterios_nombre">Estado:</div>
					<div class="imp_criterios_valor"><?php echo $estado; ?></div>
				<?php
				}
				if ( $filtro_para_pdf['c_comision_tipo'] )
				{
					$comision = $modelo->obtenerNombreComision($filtro_para_pdf['c_comision_tipo'], $filtro_para_pdf['c_comision_codigo']);
				?>
					<div class="imp_criterios_nombre">Comisi&oacute;n:</div>
					<div class="imp_criterios_valor" style="width:521px;"><?php echo $comision; ?></div>
				<?php
				}
				?>
			</div>
		<?php
		}
		?>

		<?php 
		// PARA Fecha Desde - Fecha Hasta
		if ( isset($filtro_para_pdf['c_fecha_desde']) || isset($filtro_para_pdf['c_fecha_hasta']) )
		{
		?>
			<div class="imp_texto imp_criterios_busqueda">
				<?php
				if ( isset($filtro_para_pdf['c_fecha_desde']) )
				{
				?>
				  <div class="imp_criterios_nombre">Fecha Desde:</div>
				  <div class="imp_criterios_valor"><?php echo $this->formatearFecha($filtro_para_pdf['c_fecha_desde']); ?></div>
				<?php 
				}
				if ( isset($filtro_para_pdf['c_fecha_hasta']) )
				{
				?>
				  <div class="imp_criterios_nombre">Fecha Hasta:</div>
				  <div class="imp_criterios_valor"><?php echo $this->formatearFecha($filtro_para_pdf['c_fecha_hasta']); ?></div>
				<?php
				}
				?>
			</div>
		<?php
		}
		?>
		<div class="imp_criterios_busqueda_margen_inferior"></div>
    <?php
    }
/******************************************************************************************************************
	PIE DEL REPORTE EN FORMATO PARA IMPRIMIR
******************************************************************************************************************/
    private function pie_reporte()
    {
    ?>
		<style type="text/css">
			.imp_pie{
				height:41px;
				border-left:2px solid #000;
				border-right:2px solid #000;
				border-bottom:2px solid #000;
			}
			.imp_datos_izq{
				width:265px;
				height:37px;
				padding:5px 0 0 5px;
				float:left;
			}
			.imp_datos_der{
				width:265px;
				height:37px;
				padding:5px 5px 0 0;
				float:right;
				text-align:right
			}
			.imp_gral_texto {
				font-family: Arial;
				font-size: 12px;
				color: #000;
			}
		</style>
		<div class="imp_pie imp_gral_texto">
			<div class="imp_datos_izq"><?php echo date("d/m/Y"); ?>&nbsp;&nbsp;<span id="reloj"></span><br>USR. <?php echo $_SESSION['usuario']; ?></div>
			<div class="imp_datos_der"><br>PC: <?php echo gethostbyaddr($_SERVER['REMOTE_ADDR']); ?></div>
		</div>
    <?php  
    }

    /**
     * SE GENERA EL REPORTE DE Consulta General EN FORMATO .doc PARA PROCESAR SU CONTENIDO
     * 
     * @param array $listado_para_pdf
     * @param array $filtro_para_pdf
     */
    public function procesar_texto($listado_para_pdf = '', $filtro_para_pdf = '')
    {
		header('Content-type: application/msword; charset=UTF-8');
		header('Content-Disposition: inline; filename=consulta_general.doc');

		$modelo = new consultaGralModel();
		//  SE COMIENZA A ARMAR EL DOCUMENTO
		echo "<html>";
		echo "\n<body>";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Municipalidad de General Pueyrredon</p>";
		echo chr(9).chr(9).chr(9).chr(9).chr(9).chr(9).chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Sistema de Expedientes</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Honorable Concejo Deliberante</p>";
		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Consulta General</p>";
		 
		if ($filtro_para_pdf['c_iniciado_tipo'])
		{
			$iniciador = $modelo->obtenerNombreIniciador($filtro_para_pdf['c_iniciado_tipo'], $filtro_para_pdf['c_iniciado_codigo']);
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Iniciado: </b>".$this->reemplazarPorHTML($iniciador)."</p>";
		}	
				
		if ($filtro_para_pdf['c_categoria'])
		{
			$categoria = $modelo->obtenerNombreCategoria($filtro_para_pdf['c_categoria']);
			echo chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Categor&iacute;a: </b>".$this->reemplazarPorHTML($categoria)."</p>";
		}
						
		if ($filtro_para_pdf['c_autor_tipo'])
		{
			$autor = $modelo->obtenerNombreAutor($filtro_para_pdf['c_autor_tipo'], $filtro_para_pdf['c_autor_codigo']);
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Autor: </b>".$this->reemplazarPorHTML($autor)."</p>";
		}

		if ($filtro_para_pdf['c_tema'])
		{
			//SE OBTIENE EL Tema DEL CRITERIO DE BUSQUEDA
			$tema = $modelo->obtenerNombreTema($filtro_para_pdf['c_tema']);
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Tema: </b>".$this->reemplazarPorHTML($tema)."</p>";
		}
				
		if ($filtro_para_pdf['c_comision_tipo'])
		{
			$comision = $modelo->obtenerNombreComision($filtro_para_pdf['c_comision_tipo'], $filtro_para_pdf['c_comision_codigo']);
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Comisi&oacute;n: </b>".$this->reemplazarPorHTML($comision)."</p>";
		}

		if ($filtro_para_pdf['c_estado'])
		{
			//SE OBTIENE EL Estado DEL Expediente RESULTANTE
			$estado = $modelo->obtenerNombreEstado($filtro_para_pdf['c_estado']);
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Estado: </b>".$this->reemplazarPorHTML($estado)."</p>";
		}

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Fecha Desde: </b>".$this->formatearFecha($filtro_para_pdf['c_fecha_desde']).chr(9)."<b>Fecha Hasta: </b>".$this->formatearFecha($filtro_para_pdf['c_fecha_hasta'])."</p>";

		echo "\n<hr>";
		// TITULOS PARA LA CLAVE DEL EXPEDIENTE Y SU FECHA DE INGRESO
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Cpo.".chr(9)."Al.</b>".chr(9).chr(9).chr(9)."Fecha Ingreso</p>";

		echo "\n<hr>";

		$cantidad = count($listado_para_pdf);
		for ($f=0; $f < $cantidad; $f++)
		{
			$ficha = &$listado_para_pdf[$f];
			
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$ficha['anio'].chr(9).$ficha['tipo'].chr(9).$ficha['numero'].chr(9).$ficha['cuerpo'].chr(9).$ficha['alcance']."</b>".chr(9).chr(9).chr(9).$this->formatearFecha($ficha['fecha_entrada_expe'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
			
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Car&aacute;tula: </b>".chr(9).$this->reemplazarPorHTML($ficha['caratula'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Iniciador: </b>".chr(9).$this->reemplazarPorHTML($ficha['iniciador'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Categor&iacute;a: </b>".chr(9).$this->reemplazarPorHTML($ficha['categoria'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Tema: </b>";

			$cantidad_temas = count($ficha['temas']);
			for ($t=0; $t < $cantidad_temas; $t++)
			{
				$tema = &$ficha['temas'][$t];

				echo chr(9).chr(9).$this->reemplazarPorHTML($tema['descripcion_tema']); 
				if ( $t != ($cantidad_temas - 1) ) echo '<b>;</b> ';
			}
			echo "\n</p>";
			
			//echo "<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
			
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Autor: </b>";

			$cantidad_autores = count($ficha['autores']);
			for ($a=0; $a < $cantidad_autores; $a++)
			{
				$autor = &$ficha['autores'][$a];

				echo chr(9).chr(9).$this->reemplazarPorHTML($autor['descripcion_grp']); 
				if ( $a != ($cantidad_autores - 1) ) echo '<b>;</b> ';
			}
			echo "\n</p>";
			
			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$ficha['proyectos'][$p];

				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Proyecto de ".chr(9).$this->reemplazarPorHTML($proyecto['descripcion_proyecto'])."</b></p>";
				echo "\n<p style='margin-bottom: 0cm;text-align:justify'><b>Extracto: </b>".chr(9).$this->reemplazarPorHTML($proyecto['extracto'])."</p>";
			}

			if ($ficha['id_codestado'])
			{
				//SE OBTIENE EL Estado DEL Expediente
				$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $ficha['id_codestado']);
				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Estado / Desde</b>".chr(9).$this->reemplazarPorHTML($estado[0]['nombre_estado']).chr(9).chr(9)."/".chr(9).chr(9).$this->formatearFecha($estado[0]['fecha_estado'])."</p>";
			}

			if ($ficha['id_codestado'] == 3 || $ficha['id_codestado'] == 16) //18/11/2011 $estado[0]
			{
				//SE OBTIENE LA Comision DEL Expediente RESULTANTE
				$comision = $modelo->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $ficha['comision_tipo'], $ficha['comision_codigo']);
				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Comisi&oacute;n / Desde</b>".$this->reemplazarPorHTML($comision[0]['comision']).chr(9).chr(9)."/".chr(9).chr(9).$this->formatearFecha($comision[0]['fecha_giro'])."</p>"; 
			}
		   
			echo "\n<hr>";

		}// FIN DEL for	

		// PIE DEL DOCUMENTO A IMPRIMIR CON FECHA, USUARIO Y NOMBRE DE LA PC
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>".date("d/m/Y")."</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>USR. ".$_SESSION['usuario'].chr(9).chr(9).chr(9).chr(9).chr(9)."PC: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."</p>";


		echo "\n</body>";
		echo "\n</html>";
    }
/*********************************************************************************************************************************/
    public function obtenerNombreMarcaComision($marca_comision)
    {
    	switch ($marca_comision)
    	{
			case '0':
				$nombre_marca = "Sin marca";
				break;
			case '1':
				$nombre_marca = "Para tratar";
				break;
			case '2':
				$nombre_marca = "Para su conocimiento";
				break;
			case '3':
				$nombre_marca = "Para archivo";
				break;	
		}
		return $nombre_marca;
    }
/******************************************************************************************************************
     SE REFRESCAN MEDIANTE AJAX LOS COMBOS DEL CRITERIO DE BUSQUEDA AL SETEAR EL CHECK DE Mostrar solo Habilitados o No
******************************************************************************************************************/
	public function comboComisiones($listado, $comision = 0)
	{
	?>
		<select id="c_comision" name="c_comision" class="msc_combo msc_combo_ancho" style="width:128px;">
			<option value="0">0, TODAS</option>
			<?php
			$cant_comisiones = count($listado);
			for ($c=0; $c < $cant_comisiones; $c++)
			{
				$registro = &$listado[$c];
			?>	
				<option value="<?php echo $registro['tipo_grp'].'-'.$registro['codigo_grp']; ?>"><?php echo $registro['codigo_grp'].', '.$registro['descripcion_grp']; ?></option>
			<?php
			}
			?>
		</select>
		&nbsp;
		<a id="imagen_zoom_comisiones_ConsultaGral" href="javascript:modalGaby('consultas/index.php?controlador=consulta_gral&accion=pedirNombreComisionModal&c_solo_habilitado='+$('c_solo_habilitado').value+'');" title="Buscar por Nombre de Comisi&oacute;n"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
		<script type="text/javascript">
			$('c_comision').value = <?php echo ($comision) ? "'".$comision."'" : 0; ?>; 
		</script>
	<?php
	}

	public function comboIniciadores($listado, $iniciador = 0)
	{
	?>
		<select id="c_iniciado" name="c_iniciado" class="msc_combo msc_combo_ancho" style="width:128px;">
			<option value="0">0, TODOS</option>
			<?php
			$cant_iniciadores = count($listado);
			for ($i=0; $i < $cant_iniciadores; $i++)
			{
				$registro = &$listado[$i];
			?>	
				<option value="<?php echo $registro['tipo_grp'].'-'.$registro['codigo_grp']; ?>"><?php echo $registro['codigo_grp'].', '.$registro['descripcion_grp']; ?></option>
			<?php
			}
			?>
		</select>
		&nbsp;
		<a href="javascript:modalGaby('consultas/index.php?controlador=consulta_gral&accion=pedirNombreIniciadorModal&c_solo_habilitado='+$('c_solo_habilitado').value+'');" title="Buscar por Nombre de Iniciador"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
		<script type="text/javascript">
			$('c_iniciado').value = <?php echo ($iniciador) ? "'".$iniciador."'" : 0; ?>; 
		</script>
	<?php
	}

	public function comboCategorias($listado, $categoria = 0)
	{
	?>
		<select id="c_categoria" name="c_categoria" class="msc_combo" style="width:128px;">
			<option value="0">0, TODAS</option>
			<?php
			$cant_categorias = count($listado);
			for ($cat=0; $cat < $cant_categorias; $cat++)
			{
				$registro = &$listado[$cat];
			?>
				<option value="<?php echo $registro['id_codcategoria']; ?>"><?php echo $registro['codigo_categoria'].', '.$registro['descripcion_categoria']; ?></option>
			<?php
			}
			?>
		</select>	
		&nbsp;
		<a href="javascript:modalGaby('abms/index.php?controlador=categorias&accion=pedirNombreModal&c_solo_habilitado='+$('c_solo_habilitado').value+'');" title="Buscar por Nombre de Categor&iacute;a"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
		
		<script type="text/javascript">
			$('c_categoria').value = <?php echo ($categoria) ? "'".$categoria."'" : 0; ?>; 
		</script>
	<?php
	}

	public function comboAutores($listado, $autor = 0)
	{
	?>
		<select id="c_autor" name="c_autor" class="msc_combo msc_combo_ancho" style="width:128px;">
			<option value="0">0, TODOS</option>
			<?php
			$cant_autores = count($listado);
			for ($a=0; $a < $cant_autores; $a++)
			{
				$registro = &$listado[$a];
			?>	
				<option value="<?php echo $registro['autor_tipo'].'-'.$registro['autor_codigo']; ?>"><?php echo $registro['autor_codigo'].', '.$registro['autor_descripcion']; ?></option>
			<?php
			}
			?>
		</select>
		&nbsp;
		<a href="javascript:modalGaby('consultas/index.php?controlador=consulta_gral&accion=pedirNombreAutorModal&c_solo_habilitado='+$('c_solo_habilitado').value+'');" title="Buscar por Nombre de Autor"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
		<script type="text/javascript">
			$('c_autor').value = <?php echo ($autor) ? "'".$autor."'" : 0; ?>; 
		</script>
	<?php
	}

	public function comboTemas($listado, $tema = 0)
	{
	?>
		<select id="c_tema" name="c_tema" class="msc_combo" style="width:128px;">
			<option value="0">0, TODOS</option>
			<?php
			$cant_temas = count($listado);
			for ($t=0; $t < $cant_temas; $t++)
			{
				$registro = &$listado[$t];
			?>	
				<option value="<?php echo $registro['id_codtema']; ?>"><?php echo $registro['codigo_tema'].', '.$registro['descripcion_tema']; ?></option>
			<?php
			}
			?>
		</select>
		&nbsp;
		<a href="javascript:modalGaby('abms/index.php?controlador=codtemas&accion=pedirNombreModal&c_solo_habilitado='+$('c_solo_habilitado').value+'');" title="Buscar por Nombre de Tema"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
		<script type="text/javascript">
			// Se inicializa el objeto MOOdalBox
			Window.onDomReady(MOOdalBox.init.bind(MOOdalBox));
			
			$('c_tema').value = <?php echo ($tema) ? "'".$tema."'" : 0; ?>; 
		</script>
	<?php
	}

	public function comboEstados($listado, $estado = 0)
	{
	?>
		<select id="c_estado" name="c_estado" class="msc_combo" style="width:128px;">
			<option value="0">0, TODOS</option>
			<?php
			$cant_estados = count($listado);
			for ($e=0; $e < $cant_estados; $e++)
			{
				$registro = &$listado[$e];
			?>	
				<option value="<?php echo $registro['id_codestado']; ?>"><?php echo $registro['codigo_estado'].', '.$registro['nombre_estado']; ?></option>
			<?php
			}
			?>
		</select>
		&nbsp;
		<a id="imagen_zoom_estados_ConsultaGral" href="javascript:modalGaby('abms/index.php?controlador=codestados&accion=pedirNombreModal&c_solo_habilitado='+$('c_solo_habilitado').value+'');" title="Buscar por Nombre de Estado"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
		<script type="text/javascript">
			// Se inicializa el objeto MOOdalBox
			Window.onDomReady(MOOdalBox.init.bind(MOOdalBox));
			
			$('c_estado').value = <?php echo ($estado) ? "'".$estado."'" : 0; ?>; 
		</script>
	<?php
	}
	
	public function pedirNombreIniciadorModal($listadoModal)
	{
	?>
		<div class="autosugerido">
			Nombre: <input type="text" id="nombre_sugerido" name="nombre_sugerido" maxlength="100" />
			<div id="sugerencias"><ul></ul></div>
		</div>
		<div class="margen_modal"></div>
		<div class="cerrar_pedirNombreModal">
			<div class="titulo_pedirNombreModal">Buscar por Iniciador.</div>
			<div id="btCerrar_pedirNombreModal" class="btCerrar_pedirNombreModal"></div>
		</div>
		<script type="text/javascript">
		
			ventana_modal = "si";
			
			var iniciadores_a_elegir = new Array(
				<?php
				$cantidad = count($listadoModal);
				for ($i=0; $i < $cantidad; $i++)	
				{
					$iniciadorModal = &$listadoModal[$i];
				
					if ( $i == $cantidad-1 )
						echo "'".$iniciadorModal['tipo_grp'].", ".$iniciadorModal['codigo_grp'].", ".$iniciadorModal['descripcion_grp']."'";
					else
						echo "'".$iniciadorModal['tipo_grp'].", ".$iniciadorModal['codigo_grp'].", ".$iniciadorModal['descripcion_grp']."',";
				}
				?>
			);
			
			new AutoSuggest($('nombre_sugerido'),iniciadores_a_elegir, 'c_iniciado');
			
			setfocus('nombre_sugerido');
			
			$('btCerrar_pedirNombreModal').addEvent('click', function(){
				cerrarModalPedirNombre();
			});	
		</script>
	<?php	
	}
	
	public function pedirNombreAutorModal($listadoModal)
	{
	?>
		<div class="autosugerido">
			Nombre: <input type="text" id="nombre_sugerido" name="nombre_sugerido" maxlength="100" />
			<div id="sugerencias"><ul></ul></div>
		</div>
		<div class="margen_modal"></div>
		<div class="cerrar_pedirNombreModal">
			<div class="titulo_pedirNombreModal">Buscar por Autor.</div>
			<div id="btCerrar_pedirNombreModal" class="btCerrar_pedirNombreModal"></div>
		</div>
		<script type="text/javascript">
		
			ventana_modal = "si";
			
			var autores_a_elegir = new Array(
				<?php
				$cantidad = count($listadoModal);
				for ($a=0; $a < $cantidad; $a++)	
				{
					$autorModal = &$listadoModal[$a];
				
					if ( $a == $cantidad-1 )
						echo "'".$autorModal['autor_tipo'].", ".$autorModal['autor_codigo'].", ".$autorModal['autor_descripcion']."'";
					else
						echo "'".$autorModal['autor_tipo'].", ".$autorModal['autor_codigo'].", ".$autorModal['autor_descripcion']."',";
				}
				?>
			);
			
			new AutoSuggest($('nombre_sugerido'),autores_a_elegir, 'c_autor');
			
			setfocus('nombre_sugerido');
			
			$('btCerrar_pedirNombreModal').addEvent('click', function(){
				cerrarModalPedirNombre();
			});	
		</script>
	<?php	
	}
	
	public function pedirNombreComisionModal($listadoModal)
	{
	?>
		<div class="autosugerido">
			Nombre: <input type="text" id="nombre_sugerido" name="nombre_sugerido" maxlength="100" />
			<div id="sugerencias"><ul></ul></div>
		</div>
		<div class="margen_modal"></div>
		<div class="cerrar_pedirNombreModal">
			<div class="titulo_pedirNombreModal">Buscar por Comisi&oacute;n.</div>
			<div id="btCerrar_pedirNombreModal" class="btCerrar_pedirNombreModal"></div>
		</div>
		<script type="text/javascript">
		
			ventana_modal = "si";
			
			var comisiones_a_elegir = new Array(
				<?php
				$cantidad = count($listadoModal);
				for ($i=0; $i < $cantidad; $i++)	
				{
					$comisionModal = &$listadoModal[$i];
				
					if ( $i == $cantidad-1 )
						echo "'".$comisionModal['tipo_grp'].", ".$comisionModal['codigo_grp'].", ".$comisionModal['descripcion_grp']."'";
					else
						echo "'".$comisionModal['tipo_grp'].", ".$comisionModal['codigo_grp'].", ".$comisionModal['descripcion_grp']."',";
				}
				?>
			);
			
			new AutoSuggest($('nombre_sugerido'),comisiones_a_elegir, 'c_comision');
			
			setfocus('nombre_sugerido');
			
			$('btCerrar_pedirNombreModal').addEvent('click', function(){
				cerrarModalPedirNombre();
			});	
		</script>
	<?php	
	}

	/**
	 * SE GENERA EL REPORTE DE Consulta General EN FORMATO .csv
	 *
	 * @param array $listado_para_csv
	 * @param array $filtro_para_csv
	 */
	public function generarCSV_ConsultaGeneral($listado_para_csv = '', $filtro_para_csv = '')
	{
		header("Cache-Control: must-revalidate");
    	header("Pragma: must-revalidate");
    	header("Content-type: application/vnd.ms-excel");
    	header('Content-Disposition: attachment; filename=consulta_general.csv');
    
    	$modelo = new consultaGralModel();
    	 
    	//  SE COMIENZA A ARMAR EL DOCUMENTO
    	echo "Municipalidad de General Pueyrredon";
    	echo "\nSistema de Expedientes";
    	echo "\nHonorable Concejo Deliberante";
    	echo "\n";
    	echo "\nConsulta General";
			
		if ($filtro_para_csv['c_iniciado_tipo'])
		{
			// Se obtiene el Iniciador seleccionado en el criterio de búsqueda
			$iniciador = $modelo->obtenerNombreIniciador($filtro_para_csv['c_iniciado_tipo'], $filtro_para_csv['c_iniciado_codigo']);
			
			echo "\nIniciador: \"".str_replace('"',"",$iniciador)."\"";
		}
	
		if ($filtro_para_csv['c_categoria'])
		{
			// Se obtiene la Categoría seleccionada en el criterio de búsqueda
			$categoria = $modelo->obtenerNombreCategoria($filtro_para_csv['c_categoria']);
			
			echo "\nCategoría: \"".str_replace('"',"",$categoria)."\"";
		}
	
		if ($filtro_para_csv['c_autor_tipo'])
		{
			// Se obtiene el Autor seleccionado en el criterio de búsqueda
			$autor = $modelo->obtenerNombreAutor($filtro_para_csv['c_autor_tipo'], $filtro_para_csv['c_autor_codigo']);
			
			echo "\nAutor: \"".str_replace('"',"",$autor)."\"";
		}
	
		if ($filtro_para_csv['c_tema'])
		{
			// Se obtiene el Tema seleccionado en el criterio de búsqueda
			$tema = $modelo->obtenerNombreTema($filtro_para_csv['c_tema']);
			
			echo "\nTema: \"".str_replace('"',"",$tema)."\"";
		}
	
		if ($filtro_para_csv['c_comision_tipo'])
		{
			// Se obtiene la Comisión seleccionada en el criterio de búsqueda
			$comision = $modelo->obtenerNombreComision($filtro_para_csv['c_comision_tipo'], $filtro_para_csv['c_comision_codigo']);
			
			echo "\nComisión: \"".str_replace('"',"",$comision)."\"";
		}
	
		if ($filtro_para_csv['c_estado'])
		{
			// Se obtiene el Estado seleccionado en el criterio de búsqueda
			$estado = $modelo->obtenerNombreEstado($filtro_para_csv['c_estado']);
			
			echo "\nEstado: \"".str_replace('"',"",$estado)."\"";
		}
	
		// Rango de Fechas Desde - Hasta
		echo "\nFecha Desde: ".$this->formatearFecha($filtro_para_csv['c_fecha_desde']).",Fecha Hasta:,".$this->formatearFecha($filtro_para_csv['c_fecha_hasta']);

		// TÍTULOS de la Clave del Expediente, su fecha de ingreso y resto de su información, en base al criterio de búsqueda utilizado
		echo "\nAño,Tipo,Número,Cuerpo,Alcance,Fecha Ingreso,Carátula,Iniciador,Categoría,Temas,Autores,Proyectos,Extracto,Estado / Desde,Comisión / Desde";
		 
		$cantidad = count($listado_para_csv);
		for ($f=0; $f < $cantidad; $f++)
		{
			$ficha = &$listado_para_csv[$f];
			
			// Si posee Proyectos
			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$ficha['proyectos'][$p];
			
				// DATOS de la Clave del Expediente, su fecha de ingreso y resto de su información
				echo "\n".$ficha['anio'].",".$ficha['tipo'].",".$ficha['numero'].",".$ficha['cuerpo'].",".$ficha['alcance'].",".$this->formatearFecha($ficha['fecha_entrada_expe']).",\"".str_replace('"',"",$ficha['caratula'])."\",\"".str_replace('"',"",$ficha['iniciador'])."\",\"".str_replace('"',"",$ficha['categoria'])."\"";
		
				// Si posee Temas
				$cantidad_temas = count($ficha['temas']);
				for ($t=0; $t < $cantidad_temas; $t++)
				{
					$tema = &$ficha['temas'][$t];
					
					// Si es el primer (ó único) Tema se pasa a otra celda con la coma y se abre comilla doble
					if ($t == 0)
					{
						echo ",\"";
					}
					// Si NO es el primero ni el último, se agrega la coma para separarlos
					elseif ( $t < $cantidad_temas )
					{
						echo ",";
					}
					
					// Se agrega la descripción del Tema
					echo str_replace('"',"",str_replace(','," ",$tema['descripcion_tema']));
	
					// Si es el último Tema (ó el único) se cierra comilla doble
					if ($t == ($cantidad_temas-1) ) echo "\"";
				}
		
				// Si posee Autores
				$cantidad_autores = count($ficha['autores']);
				for ($a=0; $a < $cantidad_autores; $a++)
				{
					$autor = &$ficha['autores'][$a];
			
					// Si es el primer (ó único) Autor se pasa a otra celda con la coma y se abre comilla doble
					if ($a == 0)
					{
						echo ",\"";
					}
					// Si NO es el primero ni el último, se agrega la coma para separarlos
					elseif ( $a < $cantidad_autores )
					{
						echo ",";
					}
					
					// Se agrega la descripción del Autor
					echo str_replace('"',"",str_replace(','," ",$autor['descripcion_grp']));
					
					// Si es el último Autor (ó el único) se cierra comilla doble
					if ($a == ($cantidad_autores-1) ) echo "\"";
				}
			
				// Se pasa a otra celda, se muestra la Descripción y el Extracto del Proyecto
				echo ",\"".str_replace('"',"",$proyecto['descripcion_proyecto'])."\",\"".str_replace('"'," ",$proyecto['extracto'])."\"";
			
				if ($ficha['id_codestado'])
				{
					// Se obtiene el Estado del expediente respectivo
					$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $ficha['id_codestado']);
	
					echo ",\"".str_replace('"',"",$estado[0]['nombre_estado'])."\"/".$this->formatearFecha($estado[0]['fecha_estado']);
				}
		
				// Si su estado es "Girado a Comisión" ó "Vuelto a Comisión en Sesión"
				if ($ficha['id_codestado'] == 3 || $ficha['id_codestado'] == 16)
				{
					// Se obtiene la Comisión del expediente respectivo
					$comision = $modelo->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $ficha['comision_tipo'], $ficha['comision_codigo']);
					
					echo ",\"".str_replace('"',"",$comision[0]['comision'])."\"/".$this->formatearFecha($comision[0]['fecha_giro']);
				}
			}
		
		}// FIN DEL for
	}
}
