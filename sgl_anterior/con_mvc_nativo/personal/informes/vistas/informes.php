<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaInformes extends VistaBase
{
	private $modeloPersonal;
	private $controlador;
	private $formulario;
	private $directorio_fotos;
	private $nroPagina;
	private $url_sitio_web_real;

	public function __construct()
	{
		//Se crea una instancia del modelo de Informes
		$this->modelo = new informesModel();
		//Se crea una instancia del modelo de Personal
		$this->modeloPersonal = new personalModel();

		$this->controlador = 'informes';
		$this->formulario = 'formInformes';

		// Ruta física donde se encuentran las fotos de cada legajo
		$this->directorio_fotos = '/sgl/personal/fotos/';

		// 06/03/2020 XXXX
		// Url del Sitio real
		$this->url_sitio_web_real = 'http://concejomdp.gov.ar/';
	}

    public function listar($listado = '', $listadoConcejales = '', $listadoAreas = '', $listadoCargos = '', $filtro = '')
    {
		$cantidad        = (isset($listado)) ? count($listado) : 0;
		$cant_areas      = (isset($listadoAreas)) ? count($listadoAreas) : 0;
		$cant_cargos     = (isset($listadoCargos)) ? count($listadoCargos) : 0;
		$cant_concejales = (isset($listadoConcejales)) ? count($listadoConcejales) : 0;
    	?>
	    <script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxResultadoInformes" class="i_modal_contenedora i_modal_texto">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="<?php echo $this->controlador; ?>/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

			    <input type="hidden" name="controlador" id="controlador_modal" value="<?php echo $this->controlador; ?>" />
			    <input type="hidden" name="accion" id="accion" value="listar" />
			    <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
			    <input type="hidden" name="i_enviado" id="i_enviado" value="enviado" />
				<input type="hidden" name="i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha" id="i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha" value="2" >
				<input type="hidden" name="i_pagina" id="i_pagina" value="<?php echo $filtro['i_pagina']; ?>" />
				<input type="hidden" name="i_nro_paginas" id="i_nro_paginas" value="<?php echo $filtro['i_nro_paginas']; ?>" />
				<!-- 03/10/2019 XXXX -->
				<input type="hidden" name="cantidad_listado" id="cantidad_listado" value="<?php echo $cantidad; ?>" />

			    <div id="dragger_consulta_general" class="i_modal_titulo degradado">Consulta de Empleados</div>
			    <div style="height:10px;font-size:0;"></div>

			    <div class="inf_contenedora">
					<div class="inf_lado_uno">

						<div class="inf_fila">
							<div id="txt_a_la_fecha" class="inf_label_nombre">A la fecha:</div>
							<div class="inf_calendario">
								<input type="text" id="i_a_la_fecha" name="i_a_la_fecha" value="<?php echo($_SESSION['filtro_informes']['i_a_la_fecha']) ? $this->formatearFecha($_SESSION['filtro_informes']['i_a_la_fecha']) : date("d/m/Y") ; ?>" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />

								<input type="image" id="img_hasta_la_fecha" src="imagenes/calendario/calendario.gif" align="top" width="16" height="16" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta." >

								<img id="img_hasta_la_fecha_gris" src="imagenes/calendario/calendario_gris.gif" align="top" width="16" height="16" >
							</div>
							<div style="width:137px;height:22px;float:left;"></div>
							<!-- Para habilitar "A la fecha" -->
						    <div class="inf_radio">
								<input type="radio" id="op_habilitar_fecha_hasta" name="habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha" value="1" />
							</div>
						</div>
						<div class="inf_espacio_entre_filas"></div>
						<div class="inf_fila">
							<div id="txt_por_fecha_de_baja" class="inf_label_nombre">Por fecha de baja:</div>
							<div class="inf_calendario">
								<input type="text" id="i_por_fecha_de_baja" name="i_por_fecha_de_baja" value="<?php echo($_SESSION['filtro_informes']['i_por_fecha_de_baja']) ? $this->formatearFecha($_SESSION['filtro_informes']['i_por_fecha_de_baja']) : date("d/m/Y") ; ?>" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />

								<input type="image" id="img_por_fecha_de_baja" src="imagenes/calendario/calendario.gif" align="top" width="16" height="16" alt="Calendario, presione aqu&iacute; para seleccionar la fecha de baja." >

								<img id="img_por_fecha_de_baja_gris" src="imagenes/calendario/calendario_gris.gif" align="top" width="16" height="16" >
							</div>
							<div style="width:137px;height:22px;float:left;padding: 5px 0 0 0;text-align: left;">(con Decreto de Baja)</div>
							<!-- Para habilitar "Por fecha de baja" -->
						    <div class="inf_radio">
								<input type="radio" id="op_habilitar_por_fecha_de_baja" name="habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha" value="2" />
							</div>
						</div>
						<div class="inf_espacio_entre_filas"></div>
						<div class="inf_fila">
							<div id="txt_fecha_desde" class="inf_label_nombre">Desde:</div>
							<div class="inf_calendario">
								<?php $i_fecha_desde = date("d/m/").( date("Y")-10 ); ?>
							    <input type="text" id="i_fecha_desde" name="i_fecha_desde" value="<?php echo($_SESSION['filtro_informes']['i_fecha_desde']) ? $this->formatearFecha($_SESSION['filtro_informes']['i_fecha_desde']) : $i_fecha_desde ; ?>" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />

							    <input type="image" id="img_fecha_desde" src="imagenes/calendario/calendario.gif" align="top" width="16" height="16" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" >

							    <img id="img_fecha_desde_gris" src="imagenes/calendario/calendario_gris.gif" align="top" width="16" height="16" >
							</div>
						    <div id="txt_fecha_hasta" class="inf_label_nombre" style="width:35px">&nbsp;Hasta:</div>
						    <div class="inf_calendario">
								<input type="text" id="i_fecha_hasta" name="i_fecha_hasta" value="<?php echo($_SESSION['filtro_informes']['i_fecha_hasta']) ? $this->formatearFecha($_SESSION['filtro_informes']['i_fecha_hasta']) : date("d/m/Y") ; ?>" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />

							    <input type="image" id="img_fecha_hasta" src="imagenes/calendario/calendario.gif" align="top" width="16" height="16" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" >

							    <img id="img_fecha_hasta_gris" src="imagenes/calendario/calendario_gris.gif" align="top" width="16" height="16" >
						    </div>
						    <!-- Para habilitar el rango de fechas -->
						    <div class="inf_radio">
								<input type="radio" id="op_habilitar_rango_fecha" name="habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha" value="3" />
							</div>
						</div>

					  	<div class="inf_espacio_entre_filas"></div>

						<div class="inf_fila">
						   <div class="inf_label_nombre">Por &Aacute;rea :</div>
						   <div class="inf_combo">
							  <select id="i_area" name="i_area">
								  	<option value="0">Seleccione un &Aacute;rea</option>
								  	<?php
								  	for ($b=0; $b < $cant_areas; $b++) {
									  	$area = &$listadoAreas[$b];

										$aclaracion_area = ($area['ca_habilitado'] == 0) ? ' (retirado)' : '';

									  	echo '<option value="'.$area['ca_id'].'">'.$area['ca_nombre'].$aclaracion_area.'</option>';
								  	}
								  	?>
							  </select>
							  &nbsp;<a id="imagen_zoom_comisiones_Listados" href="javascript:modalGaby('informes/index.php?controlador=informes&accion=pedirNombreAreaModal');" title="Buscar por Nombre de Area"><img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" /></a>
						   </div>
						</div>

						<div class="inf_espacio_entre_filas"></div>

						<div class="inf_fila">
							<div class="inf_label_nombre">Por Cargo :</div>
							<div id="informes_contenedora_comboCargos" class="inf_combo">
								<select id="i_cargo" name="i_cargo">
									<option value="0">Seleccione un Cargo</option>
									<?php
									for ($c=0; $c < $cant_cargos; $c++) {
										$cargo = &$listadoCargos[$c];

										$aclaracion_cargo = ($cargo['cc_habilitado'] == 0) ? ' (retirado)' : '';

										echo '<option value="'.$cargo['cc_nomenclador'].'">'.$cargo['cc_nombre'].$aclaracion_cargo.'</option>';
									}
									?>
								</select>
								&nbsp;<a id="imagen_zoom_comisiones_Listados" href="javascript:modalGaby('informes/index.php?controlador=informes&accion=pedirNombreCargoModal');" title="Buscar por Nombre de Cargo"><img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" /></a>
							</div>
						</div>

						<div class="inf_espacio_entre_filas"></div>

						<div class="inf_fila">
							<div class="inf_label_nombre">Por Concejal :</div>
							<div id="informes_contenedora_comboConcejales" class="inf_combo">
								<select id="i_concejal" name="i_concejal">
									<option value="0">Seleccione un Concejal</option>
									<?php
									for ($c=0; $c < $cant_concejales; $c++) {
									    $concejal = &$listadoConcejales[$c];
										echo '<option value="'.$concejal['p_legajo'].'">'.$concejal['p_apellido'].', '.$concejal['p_nombre'].'</option>';
									}
									?>
								</select>
								&nbsp;<a id="imagen_zoom_comisiones_Listados" href="javascript:modalGaby('informes/index.php?controlador=informes&accion=pedirNombreConcejalModal&id_combo=i_concejal');" title="Buscar por Nombre de Concejal"><img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" /></a>
							</div>
						</div>

						<div class="inf_espacio_entre_filas"></div>

						<div class="inf_fila">
						    <div class="inf_label_nombre">Ordenado por:</div>
						    <div class="inf_combo">
								<input type="radio" name="i_orden" id="op_orden_por_legajo" value="1" />&nbsp;Legajo
								<input type="radio" name="i_orden" id="op_orden_por_apellido" value="2" />&nbsp;Apellido
								<input type="hidden" name="filtro_opcion_orden" id="filtro_opcion_orden" value="<?php echo $_SESSION['filtro_informes']['i_orden']; ?>" />
							</div>
						</div>
					</div>
					<div class="inf_espacio_criterio_y_botones"></div>
					<div class="inf_lado_botones">
						<?php
						if ( $filtro['i_enviado'] != '' && $listado != '' ) {
						?>
							<div class="p_boton_edicion">
								<a id="i_btImpresion" target="_blank" >
									<img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;Imprimir
								</a>
							</div>
						<?php
						} else {
						?>
							<div class="p_boton_edicion">
								<a style="cursor:pointer;color:silver" title="Sin resultados para imprimir">
									<img src="imagenes/barra/print_gris_16x16.gif" width="16" height="16" align="left" />&nbsp;Imprimir
								</a>
							</div>
						<?php
						}
						?>
						<div style="height:15px;font-size:0;"></div>
						<div class="p_boton_edicion">
							<a id="i_btBuscar" >
								<img src="imagenes/barra/zoom_16x16.gif" width="16" height="16" align="left" />Buscar
							</a>
						</div>
						<div style="height:15px;font-size:0;"></div>
						<div class="p_boton_edicion">
							<a id="i_btCerrar">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="left" />Cerrar
							</a>
						</div>
					</div>
			    </div>

			    <?php
				// PAGINADOR
				if ( $filtro['i_enviado'] != '' ) {
				?>
					<div class="msc_paginador">
						<div class="msc_margen_paginador"></div>
						<div class="msc_flechas_paginador">
							<a id="cg_btPrimero" href="#" title="Primer Registro" >
								<?php
								if ( $filtro['i_pagina'] != 1 )
									echo '<img src="imagenes/barra/b_firstpage.png" width="14" height="14" align="top" />';
								else
									echo '<img src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="top" />';
								?>
							</a>
							<a id="cg_btAnterior" href="#" title="Registro Anterior" >
								<?php
								if ( $filtro['i_pagina_ant'] != 0 )
									echo '<img src="imagenes/barra/b_prevpage.png" width="14" height="14" align="top" />';
								else
									echo '<img src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="top" />';
								?>
							</a>
						</div>
						<div class="msc_detalle_paginador">
							<?php echo $filtro['i_pagina'].' de '.$filtro['i_nro_paginas']; ?>
						</div>
						<div class="msc_flechas_paginador">
							<a id="cg_btSiguiente" href="#" title="Registro Siguiente" >
								<?php
								if ( $filtro['i_pagina'] != $filtro['i_nro_paginas'] )
									echo '<img src="imagenes/barra/b_nextpage.png" width="14" height="14" align="top" />';
								else
									echo '<img src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="top" />';
								?>
							</a>
							<a id="cg_btUltimo" href="#" title="Ultimo Registro" >
								<?php
								if ( $filtro['i_pagina'] != $filtro['i_nro_paginas'] )
									echo '<img src="imagenes/barra/b_lastpage.png" width="14" height="14" align="top" />';
								else
									echo '<img src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="top" />';
								?>
							</a>
						</div>
					</div>
				<?php
				} // FIN DEL PAGINADOR
				?>

			  	<div class="i_borde1">
				  <div id="i_borde2" class="i_borde2">
					<?php
					if ( !$filtro['i_enviado'] ) {
					?>
						<!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->
						<div class="i_fondo_item"><br>Listado de Personal</div>
						<div class="i_fondo_item"><br>Listado de Personal</div>
						<div class="i_fondo_item"><br>Listado de Personal</div>
						<div class="i_fondo_item"><br>Listado de Personal</div>
						<div class="i_fondo_item"><br>Listado de Personal</div>
					<?php
					} else {
					    if ( $listado == null ) {
							echo '<br><h3>Sin resultados</h3>';
					    } else {
					    	// SE LISTAN LAS FICHAS DE LOS EMPLEADOS
						    for ($e=0; $e < $cantidad; $e++) {
							    $ficha = &$listado[$e];

								$area = $this->modeloPersonal->obtenerNombreUltimaArea($ficha['p_legajo']);

								$cargo = $this->modeloPersonal->obtenerNombreUltimoCargo($ficha['p_legajo']);

								$depende_de = $this->modeloPersonal->obtenerDependeDe($ficha['p_legajo']);
							?>
							    <div class="i_ficha" onmouseover="javascript:$(this),setStyle('background-color','#E7E7E7');"  onmouseout="javascript:$(this),setStyle('background-color','#DEDEDE');">
									<div class="i_ficha_datos <?php echo ($cargo['c_fecha_baja'] != '') ? 'i_ficha_datos_legajo_con_baja' : ''; ?>">
										<div class="i_ficha_datos_valor">
											<strong>Legajo:</strong>&nbsp;<?php echo number_format($ficha['p_legajo'], 0, '', '.')."/".$cargo['c_digito']; ?>
										</div>
										<div class="i_ficha_datos_valor">
											<strong><?php echo $ficha['p_apellido'].', '.$ficha['p_nombre']; ?></strong>
										</div>
										<div class="i_ficha_datos_valor">
											<strong>&Aacute;rea <?php echo ($cargo['c_fecha_baja'] != '') ? '' : 'actual'; ?>:</strong>&nbsp;<?php echo $area['area']; ?>
										</div>
										<div class="i_ficha_datos_valor">
											<?php
											// Si tiene fecha de baja
											if ($cargo['c_fecha_baja'] != '') {
											?>
												<strong>Cargo:</strong>&nbsp;<?php echo $cargo['cargo']; ?>&nbsp;&nbsp;
												<span>(hasta el <?php echo $this->formatearFecha($cargo['c_fecha_baja']); ?>)</span>
											<?php
											} else {
											?>
												<strong>Cargo actual:</strong>&nbsp;<?php echo $cargo['cargo']; ?>
											<?php
											}
											?>
										</div>
										<?php
										// Si depende o dependía de un concejal, se muestra su nombre
										if ( $depende_de[0]['p_apellido'] ) {
										?>
											<div class="i_ficha_datos_valor">
												<strong><?php echo ($cargo['c_fecha_baja'] != '') ? 'Depend&iacute;a de:' : 'Depende actualmente de:'; ?></strong>
												&nbsp;<?php echo $depende_de[0]['p_apellido'].", ".$depende_de[0]['p_nombre']; ?>
											</div>
										<?php
										}
										?>
									</div>
									<div class="i_ficha_foto_mini">
										<a>
											<span class="centrar_imagen"><img src="<?php echo $this->directorio_fotos; ?>resize.php?ancho=107&alto=107&imagen=<?php echo ($ficha['p_foto']) ? $ficha['p_foto'] : 'avatar.jpg'; ?>" ></span>
										</a>
									</div>
									<div class="i_ficha_ver_legajo">
										<br><br><br><br><br>
										<a href="javascript:refrescar('abms/index.php?controlador=personal&accion=editar&legajo=<?php echo $ficha['p_legajo']; ?>&pagina=<?php echo $filtro['i_pagina']; ?>', 'contenidoAjaxPrincipal');" title="Ver la Ficha del Empleado.">Ver Ficha</a>
									</div>
							    </div>
							<?php
							} // FIN DEL for
							?>
							<script>
								var scroller = new Fx.Scroll($('i_borde2'));
								scroller.toTop();
							</script>
					<?php
						} //FIN DEL SEGUNDO else
					} //FIN DEL PRIMER else
					?>
				  </div><!-- FIN DE i_borde2 -->
			    </div><!-- FIN DE i_borde1 -->

	        </form>
		</div>
	    <script>
			// PARA FILTRAR POR FECHA HASTA O POR UN RANGO DE FECHAS
			var filtro_fecha;

			// 03/10/2019 XXXX
			var js_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha = '<?php echo $_SESSION['filtro_informes']['i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha']; ?>';
			// 03/10/2019 XXXX
			var js_orden = '<?php echo $_SESSION['filtro_informes']['i_orden']; ?>';

			$('i_area').value = '<?php echo ($_SESSION['filtro_informes']['i_area']) ? $_SESSION['filtro_informes']['i_area'] : 0 ; ?>';

			function habilitarCalendario_a_LaFecha()
			{
				$('i_a_la_fecha').disabled = false;
				$('img_hasta_la_fecha').setStyle('display', 'inline');
				$('img_hasta_la_fecha_gris').setStyle('display', 'none');
				$('txt_a_la_fecha').setStyle('color', '#000000');

				$('i_por_fecha_de_baja').disabled = true;
				$('img_por_fecha_de_baja').setStyle('display', 'none');
				$('img_por_fecha_de_baja_gris').setStyle('display', 'inline');
				$('txt_por_fecha_de_baja').setStyle('color', '#484848');

				$('i_fecha_desde').disabled = true;
				$('img_fecha_desde').setStyle('display', 'none');
				$('img_fecha_desde_gris').setStyle('display', 'inline');
				$('txt_fecha_desde').setStyle('color', '#484848');

				$('i_fecha_hasta').disabled = true;
				$('img_fecha_hasta').setStyle('display', 'none');
				$('img_fecha_hasta_gris').setStyle('display', 'inline');
				$('txt_fecha_hasta').setStyle('color', '#484848');

				//CALENDARIO PARA LA FECHA ESPECÍFICA
				var cal_a_la_fecha = new Zapatec.Calendar.setup({
					inputField:"i_a_la_fecha",
					ifFormat:"%d/%m/%Y",
					button:"img_hasta_la_fecha",
					showsTime:false
				});
			}

			function habilitarCalendarioPorFechaBaja()
			{
				$('i_a_la_fecha').disabled = true;
				$('img_hasta_la_fecha').setStyle('display', 'none');
				$('img_hasta_la_fecha_gris').setStyle('display', 'inline');
				$('txt_a_la_fecha').setStyle('color', '#484848');

				$('i_por_fecha_de_baja').disabled = false;
				$('img_por_fecha_de_baja').setStyle('display', 'inline');
				$('img_por_fecha_de_baja_gris').setStyle('display', 'none');
				$('txt_por_fecha_de_baja').setStyle('color', '#000000');

				$('i_fecha_desde').disabled = true;
				$('img_fecha_desde').setStyle('display', 'none');
				$('img_fecha_desde_gris').setStyle('display', 'inline');
				$('txt_fecha_desde').setStyle('color', '#484848');

				$('i_fecha_hasta').disabled = true;
				$('img_fecha_hasta').setStyle('display', 'none');
				$('img_fecha_hasta_gris').setStyle('display', 'inline');
				$('txt_fecha_hasta').setStyle('color', '#484848');

				//CALENDARIO DE LA FECHA DE BAJA
				var calPorFechaBaja = new Zapatec.Calendar.setup({
					inputField:"i_por_fecha_de_baja",
					ifFormat:"%d/%m/%Y",
					button:"img_por_fecha_de_baja",
					showsTime:false
				});
			}

			function habilitarCalendariosDelRango()
			{
				$('i_a_la_fecha').disabled = true;
				$('img_hasta_la_fecha').setStyle('display', 'none');
				$('img_hasta_la_fecha_gris').setStyle('display', 'inline');
				$('txt_a_la_fecha').setStyle('color', '#484848');

				$('i_por_fecha_de_baja').disabled = true;
				$('img_por_fecha_de_baja').setStyle('display', 'none');
				$('img_por_fecha_de_baja_gris').setStyle('display', 'inline');
				$('txt_por_fecha_de_baja').setStyle('color', '#484848');

				$('i_fecha_desde').disabled = false;
				$('img_fecha_desde').setStyle('display', 'inline');
				$('img_fecha_desde_gris').setStyle('display', 'none');
				$('txt_fecha_desde').setStyle('color', '#000000');

				$('i_fecha_hasta').disabled = false;
				$('img_fecha_hasta').setStyle('display', 'inline');
				$('img_fecha_hasta_gris').setStyle('display', 'none');
				$('txt_fecha_hasta').setStyle('color', '#000000');

				// CALENDARIO PARA LA FECHA DESDE
				var calDesde = new Zapatec.Calendar.setup({
					inputField:"i_fecha_desde",
					ifFormat:"%d/%m/%Y",
					button:"img_fecha_desde",
					showsTime:false
				});

				// CALENDARIO PARA LA FECHA HASTA
				var calHasta = new Zapatec.Calendar.setup({
					inputField:"i_fecha_hasta",
					ifFormat:"%d/%m/%Y",
					button:"img_fecha_hasta",
					showsTime:false
				});
			}

			$('op_habilitar_fecha_hasta').addEvent('click', function() {
				$('op_habilitar_fecha_hasta').checked = true;
				setTimeout("$('op_habilitar_fecha_hasta').focus()",75);
				$('i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha').value = 1;
				habilitarCalendario_a_LaFecha();
			});

			$('op_habilitar_por_fecha_de_baja').addEvent('click', function() {
				$('op_habilitar_por_fecha_de_baja').checked = true;
				setTimeout("$('op_habilitar_por_fecha_de_baja').focus()",75);
				$('i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha').value = 2;
				habilitarCalendarioPorFechaBaja();
			});

			$('op_habilitar_rango_fecha').addEvent('click', function() {
				$('op_habilitar_rango_fecha').checked = true;
				setTimeout("$('op_habilitar_rango_fecha').focus()",75);
				$('i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha').value = 3;
				habilitarCalendariosDelRango();
			});

			// 03/10/2019 XXXX
			// SI SE HABIA UTILIZADO LA OPCION "A LA FECHA"
			if ( js_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha == '1' )
			{
				// SE TILDA LA OPCION DE "_fecha_hasta"
				$('op_habilitar_fecha_hasta').checked = true;
				setTimeout("$('op_habilitar_fecha_hasta').focus()",75);
				$('i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha').value = 1;
				habilitarCalendario_a_LaFecha();
			}
			// SI SE HABIA UTILIZADO LA OPCION "POR FECHA DE BAJA"
			else if ( js_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha == '2' )
			{
				// SE TILDA LA OPCION "_por_fecha_de_baja"
				$('op_habilitar_por_fecha_de_baja').checked = true;
				setTimeout("$('op_habilitar_por_fecha_de_baja').focus()",75);
				$('i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha').value = 2;
				habilitarCalendarioPorFechaBaja();
			}
			else
			{
				// SINO SE TILDA LA OPCION DEL RANGO DE FECHAS
				$('op_habilitar_rango_fecha').checked = true;
				setTimeout("$('op_habilitar_rango_fecha').focus()",75);
				$('i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha').value = 3;
				habilitarCalendariosDelRango();
			}

			// AL MOSTRARSE EL COMBO DE AREAS
			$('i_area').addEvent('domready', function() {
				// SI SE ELIGIO UN AREA
				if ( $('i_area').value != '0' ) {
					// SE REFREZCA EL COMBO DE CARGOS, SEGUN EL TIPO DE AREA (PERMANENTE O POLITICA)
					refrescar('informes/index.php?controlador='+$('controlador_modal').value+'&accion=refrescarComboCargos&id_area='+$('i_area').value+'', 'informes_contenedora_comboCargos');

					// SI EL AREA ES POLITICA
					if ( $('i_area').value.substring(0,2) == '02' ) {
						// SE REFREZCA EL COMBO DE CONCEJALES
						refrescar('informes/index.php?controlador='+$('controlador_modal').value+'&accion=refrescarComboConcejales&i_bloque='+$('i_area').value+'&i_concejal=<?php echo $_SESSION['filtro_informes']['i_concejal']; ?>', 'informes_contenedora_comboConcejales');
					} else {
						// SI EL AREA ES DE PLANTA PERMANENTE SE LIMPIA EL COMBO DE CONCEJALES
						$('i_concejal').options.length = 0;
					}
				}
			});

			// SI SURGE UN CAMBIO EN EL COMBO DE AREAS
			$('i_area').addEvent('change', function() {
				// SI SE ELIGIO UN AREA
				if ( $('i_area').value != '0' ) {
					// 03/10/2019 XXXX: SE AGREGÓ DENTRO DEL if
					// Se refrezca el combo de Cargos, según el tipo de área (Permanente ó Política)
					refrescar('informes/index.php?controlador='+$('controlador_modal').value+'&accion=refrescarComboCargos&id_area='+$('i_area').value, 'informes_contenedora_comboCargos');

					// SI EL AREA ES POLITICA
					if ( $('i_area').value.substring(0,2) == '02' ) {
						// SE REFREZCA EL COMBO DE CONCEJALES
						refrescar('informes/index.php?controlador='+$('controlador_modal').value+'&accion=refrescarComboConcejales&i_bloque='+$('i_area').value, 'informes_contenedora_comboConcejales');
					} else {
						// SI EL AREA ES DE PLANTA PERMANENTE SE LIMPIA EL COMBO DE CONCEJALES
						$('i_concejal').options.length = 0;
					}
				}
			});

			$('i_cargo').value = '<?php echo ($_SESSION['filtro_informes']['i_cargo']) ? $_SESSION['filtro_informes']['i_cargo'] : 0 ; ?>';

			$('i_concejal').value = '<?php echo ($_SESSION['filtro_informes']['i_concejal']) ? $_SESSION['filtro_informes']['i_concejal'] : 0 ; ?>';

			$('op_orden_por_legajo').addEvent('click', function(){
				$('filtro_opcion_orden').value = 1;
				$('op_orden_por_legajo').checked = true;
				setTimeout("$('op_orden_por_legajo').focus()",75);
			});

			$('op_orden_por_apellido').addEvent('click', function(){
				$('filtro_opcion_orden').value = 2;
				$('op_orden_por_apellido').checked = true;
				setTimeout("$('op_orden_por_apellido').focus()",75);
			});

			if ( js_orden == '1' ) {
				$('op_orden_por_legajo').checked = true;// ORDENA POR LEGAJO
				setTimeout("$('op_orden_por_legajo').focus()",75);
			} else {
				$('op_orden_por_apellido').checked = true;// ORDENA POR APELLIDO
				setTimeout("$('op_orden_por_apellido').focus()",75);
			}

	        $('i_btBuscar').addEvent('click', function() {
				var mensaje = '';
				var error = false;

				if ( $('i_fecha_desde') && $('i_fecha_hasta') ) {

					if ($('i_fecha_desde').value == '') {
						error = true;
						mensaje = "Debe ingresar una fecha Desde.\n";
					}

					if ($('i_fecha_hasta').value == '') {
						error = true;
						mensaje = "Debe ingresar una fecha Hasta.\n";
					}

					if ( esLaFechaMayor($('i_fecha_desde').value, $('i_fecha_hasta').value) ) {
						error = true;
						mensaje = "La fecha Desde debe ser menor a la fecha Hasta en el criterio de busqueda.";
					}
				}

				if (error)
					alert(mensaje);
				else
					enviarForm('formInformes', 'informes', 'contenidoAjaxResultadoInformes');
	        });

			// PARA CERRAR LA VENTANA MODAL
			$('i_btCerrar').addEvent('click', function() {
				$("capaFondo").setStyle('visibility','hidden');
				$("capaVentana").setStyle('visibility','hidden');
			});

			// 03/10/2019 XXXX
			if ( $('cantidad_listado').value > 0 ) {

				function armarFiltroFecha()
				{
					var filtro_fecha = '';

					// Si la fecha 'A la fecha' Y 'Por fecha de Baja' están deshabilitadas
					if ( $('i_a_la_fecha').disabled && $('i_por_fecha_de_baja').disabled )
					{
						// Se filtra por el rango [Desde-Hasta]
						filtro_fecha = '&i_fecha_desde='+$('i_fecha_desde').value+'&i_fecha_hasta='+$('i_fecha_hasta').value+'&i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha=3';
					}
					// Si la fecha 'A la fecha' y el rango [Desde-Hasta] están deshabilitadas
					else if ( $('i_a_la_fecha').disabled && $('i_fecha_desde').disabled && $('i_fecha_hasta').disabled )
					{
						// Se filtra por la fecha 'Por fecha de Baja'
						filtro_fecha = '&i_por_fecha_de_baja='+$('i_por_fecha_de_baja').value+'&i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha=2';
					}
					else
					{
						// Si la fecha 'Por fecha de Baja' y el rango [Desde-Hasta] están deshabilitadas
						if ( $('i_por_fecha_de_baja').disabled && $('i_fecha_desde').disabled && $('i_fecha_hasta').disabled )
						{
							// Se filtra por la fecha 'A la fecha'
							filtro_fecha = '&i_a_la_fecha='+$('i_a_la_fecha').value+'&i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha=3';
						}
					}

					return filtro_fecha;
				}

				// AL CLIKEAR EL BOTON DE IMPRIMIR
				$('i_btImpresion').addEvent('click', function() {
					var filtro_fecha = armarFiltroFecha();

					$('i_btImpresion').setProperty('href', 'informes/index.php?controlador='+$('controlador_modal').value+'&accion=crear_formato_impresion'+filtro_fecha+'&i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha='+$('i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha').value+'&i_area='+$('i_area').value+'&i_cargo='+$('i_cargo').value+'&i_concejal='+$('i_concejal').value+'&i_orden='+$('filtro_opcion_orden').value);
				});

				// AL CLIKEAR EL BOTON DE PRIMER REGISTRO DEL PAGINADOR
				$('cg_btPrimero').addEvent('click', function() {
					if ( $('i_pagina').value != 1 )
					{
						var filtro_fecha = armarFiltroFecha();

						var valor_href_primero = "javascript:refrescar('informes/index.php?controlador="+$('controlador_modal').value+"&accion=listar"+filtro_fecha+"&i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha="+$('i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha').value+"&i_area="+$('i_area').value+"&i_cargo="+$('i_cargo').value+"&i_concejal="+$('i_concejal').value+"&i_orden="+$('filtro_opcion_orden').value+"&i_enviado="+$('i_enviado').value+"&i_pagina=1', 'contenidoAjaxResultadoInformes');";

						$('cg_btPrimero').setProperty('href', valor_href_primero);
					}
				});

				// AL CLIKEAR EL BOTON DE REGISTRO ANTERIOR DEL PAGINADOR
				$('cg_btAnterior').addEvent('click', function() {
					if ( $('i_pagina').value > 1 )
					{
						var filtro_fecha = armarFiltroFecha();

						var valor_href_anterior = "javascript:refrescar('informes/index.php?controlador="+$('controlador_modal').value+"&accion=listar"+filtro_fecha+"&i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha="+$('i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha').value+"&i_area="+$('i_area').value+"&i_cargo="+$('i_cargo').value+"&i_concejal="+$('i_concejal').value+"&i_orden="+$('filtro_opcion_orden').value+"&i_enviado="+$('i_enviado').value+"&i_pagina=<?php echo $filtro['i_pagina_ant']; ?>', 'contenidoAjaxResultadoInformes');";

						$('cg_btAnterior').setProperty('href', valor_href_anterior);
					}
				});

				// AL CLIKEAR EL BOTON DE REGISTRO SIGUIENTE DEL PAGINADOR
				$('cg_btSiguiente').addEvent('click', function() {

					// SI LA PAGINA ACTUAL NO ES LA ULTIMA
					if ( $('i_pagina').value != $('i_nro_paginas').value )
					{
						var filtro_fecha = armarFiltroFecha();

						var valor_href_sgte = "javascript:refrescar('informes/index.php?controlador="+$('controlador_modal').value+"&accion=listar"+filtro_fecha+"&i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha="+$('i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha').value+"&i_area="+$('i_area').value+"&i_cargo="+$('i_cargo').value+"&i_concejal="+$('i_concejal').value+"&i_orden="+$('filtro_opcion_orden').value+"&i_enviado="+$('i_enviado').value+"&i_pagina=<?php echo $filtro['i_pagina_sgte']; ?>', 'contenidoAjaxResultadoInformes');";

						$('cg_btSiguiente').setProperty('href', valor_href_sgte);
					}
				});

				// AL CLIKEAR EL BOTON DE ULTIMO REGISTRO DEL PAGINADOR
				$('cg_btUltimo').addEvent('click', function() {
					// SI LA PAGINA ACTUAL NO ES LA ULTIMA
					if ( $('i_pagina').value != $('i_nro_paginas').value )
					{
						var filtro_fecha = armarFiltroFecha();

						var valor_href_ultimo = "javascript:refrescar('informes/index.php?controlador="+$('controlador_modal').value+"&accion=listar"+filtro_fecha+"&i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha="+$('i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha').value+"&i_area="+$('i_area').value+"&i_cargo="+$('i_cargo').value+"&i_concejal="+$('i_concejal').value+"&i_orden="+$('filtro_opcion_orden').value+"&i_enviado="+$('i_enviado').value+"&i_pagina=<?php echo $filtro['i_nro_paginas']; ?>', 'contenidoAjaxResultadoInformes');";

						$('cg_btUltimo').setProperty('href', valor_href_ultimo);
					}
				});
			}

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoInformes'), {
			   handle: $('dragger_consulta_general')
			});
		</script>
    	<?php
    }

    /**
     * 2019/08/09 XXXX
     * Se lista el personal de Planta Política
     * @param  string $listado [description]
     * @param  string $filtro  [description]
     */
    public function listarPersonalBloques($listado = '', $filtro = '')
    {
    	$cantidad 		  = (isset($listado)) ? count($listado) : 0;
    	$accion           = "listarPersonalBloques";
		$titulo           = "Personal Planta Pol&iacute;tica";
		$filtro_en_sesion = "filtro_informes_personal_bloques_politicos";
		?>
	    <script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxResultadoInformes" class="i_modal_contenedora i_modal_texto">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="<?php echo $this->controlador; ?>/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

			    <input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
			    <input type="hidden" name="accion" id="accion" value="<?php echo $accion; ?>" />
			    <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
			    <input type="hidden" name="i_enviado" id="i_enviado" value="enviado" />

			    <div id="dragger_personal_bloques" class="i_modal_titulo degradado"><?php echo $titulo; ?></div>
			    <div style="height:10px;font-size:0;"></div>

			    <div style="height:107px;">
					<div style="width:430px;height:107px;float:left;">
					    <div style="height:22px;">
							<div class="inf_fila">
								<div class="inf_label_nombre">A la fecha:</div>
								<div class="inf_combo" style="text-align:left;">
									<input type="text" id="i_a_la_fecha" name="i_a_la_fecha" value="<?php echo($_SESSION[$filtro_en_sesion]['i_a_la_fecha']) ? $this->formatearFecha($_SESSION[$filtro_en_sesion]['i_a_la_fecha']) : date("d/m/Y") ; ?>" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
									<input type="image" id="i_btfecha_personal" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha." align="top" width="16" height="16">
								</div>
							</div>
						</div>
						<div style="height:5px;font-size:0;clear:both;"></div>
					  	<div style="height:22px;">
							<div class="inf_fila">
								<div class="inf_label_nombre">Ordenado por:</div>
								<div class="inf_combo" style="text-align:left;">
									<input type="radio" name="i_orden" id="op_orden_por_legajo" value="1" <?php echo ($_SESSION[$filtro_en_sesion]['i_orden'] == 1 || $_SESSION[$filtro_en_sesion]['i_orden'] == '') ? 'checked' : ''; ?> />&nbsp;Legajo
									<input type="radio" name="i_orden" id="op_orden_por_apellido" value="2" <?php echo ($_SESSION[$filtro_en_sesion]['i_orden'] == 2) ? 'checked' : ''; ?> />&nbsp;Apellido
									<input type="hidden" name="filtro_opcion_orden" id="filtro_opcion_orden" value="<?php echo $_SESSION[$filtro_en_sesion]['i_orden']; ?>" />
								</div>
							</div>
						</div>
					</div>
					<div style="width:35px;height:107px;float:right;"></div>
					<div style="width:100px;height:107px;float:right;">
						<div class="checkbox_con_texto">
							<label><input type="checkbox" name="habilitado" id="habilitado" onchange="javascript:chequear('i_solo_activos');" <?php echo($_SESSION[$filtro_en_sesion]['i_solo_activos'] == '1' ) ? 'checked' : ''; ?> >&nbsp;S&oacute;lo Activos</label>
							<input type="hidden" name="i_solo_activos" id="i_solo_activos" value="<?php echo($_SESSION[$filtro_en_sesion]['i_solo_activos']) ? $_SESSION[$filtro_en_sesion]['i_solo_activos'] : 0; ?>" />
						</div>
						<div class="p_boton_edicion" style="display:<?php echo ($filtro['i_enviado'] == '' ) ? 'none' : 'inline'; ?>" >
							<a id="i_btPublicar" target="_blank" >
								<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="left" />&nbsp;Publicar
							</a>
						</div>
						<div style="height:15px;font-size:0;"></div>
						<div class="p_boton_edicion">
							<a id="i_btBuscar" >
								<img src="imagenes/barra/zoom_16x16.gif" width="16" height="16" align="left" />Buscar
							</a>
						</div>
						<div style="height:15px;font-size:0;"></div>
						<div class="p_boton_edicion">
							<a id="i_btCerrar">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="left" />Cerrar
							</a>
						</div>
					</div>
			    </div>
			    <?php
			    // Se muestra el paginador sólo habiendo buscado previamente
				if ( $filtro['i_enviado'] != '' ) {
				?>
					<!-- PAGINADOR -->
					<div class="msc_paginador">
						<div class="msc_margen_paginador"></div>
						<div class="msc_flechas_paginador">
							<?php
							if ( $filtro['i_pagina'] != 1 ) {
							?>
								<a id="btPrimero" title="Primer Registro" href="javascript:refrescar('informes/index.php?controlador=informes&accion=<?php echo $accion; ?>&i_a_la_fecha='+$('i_a_la_fecha').value+'&i_solo_activos='+$('i_solo_activos').value+'&i_orden='+$('filtro_opcion_orden').value+'&i_enviado=enviado&i_pagina=1', 'contenidoAjaxResultadoInformes');">
									<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							} else {
							?>
								<a id="btPrimero" href="#">
									<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							}

							if ( $filtro['i_pagina_ant'] != 0 ) {
							?>
								<a id="btAnterior" title="Registro Anterior" href="javascript:refrescar('informes/index.php?controlador=informes&accion=<?php echo $accion; ?>&i_a_la_fecha='+$('i_a_la_fecha').value+'&i_solo_activos='+$('i_solo_activos').value+'&i_orden='+$('filtro_opcion_orden').value+'&i_enviado=enviado&i_pagina=<?php echo $filtro['i_pagina_ant']; ?>', 'contenidoAjaxResultadoInformes');">
									<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							} else {
							?>
								<a id="btAnterior" href="#">
									<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							}
							?>
						</div>
						<div class="msc_detalle_paginador"><?php echo $filtro['i_pagina'].' de '.$filtro['i_nro_paginas']; ?></div>
						<div class="msc_flechas_paginador">
							<?php
							if ( $filtro['i_pagina'] != $filtro['i_nro_paginas'] ) {
							?>
								<a id="btSiguiente" title="Registro Siguiente" href="javascript:refrescar('informes/index.php?controlador=informes&accion=<?php echo $accion; ?>&i_a_la_fecha='+$('i_a_la_fecha').value+'&i_solo_activos='+$('i_solo_activos').value+'&i_orden='+$('filtro_opcion_orden').value+'&i_enviado=enviado&i_pagina=<?php echo $filtro['i_pagina_sgte']; ?>', 'contenidoAjaxResultadoInformes');">
									<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							} else {
							?>
								<a id="btSiguiente" href="#">
									<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							}

							if ( $filtro['i_pagina'] != $filtro['i_nro_paginas'] ) {
							?>
								<a id="btUltimo" title="Ultimo Registro" href="javascript:refrescar('informes/index.php?controlador=informes&accion=<?php echo $accion; ?>&i_a_la_fecha='+$('i_a_la_fecha').value+'&i_solo_activos='+$('i_solo_activos').value+'&i_orden='+$('filtro_opcion_orden').value+'&i_enviado=enviado&i_pagina=<?php echo $filtro['i_nro_paginas']; ?>', 'contenidoAjaxResultadoInformes');">
									<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							} else {
							?>
								<a id="btUltimo" href="#">
									<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							}
							?>
						</div>
					</div>
				<?php
				}
				?>
				<div class="i_borde1">
					<div id="i_borde2" class="i_borde2">
						<?php
						if ( $filtro['i_enviado'] == '' ) {
						?>
							  <!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
						<?php
						} else {
						    if ($cantidad < 0)
							  echo '<br><h1>Sin resultados</h1>';
						    else {
							    // SE LISTAN LAS FICHAS DE LOS EMPLEADOS DE BLOQUES
							    for ($e=0; $e < $cantidad; $e++) {

									$ficha = &$listado[$e];

									$nombre_area = ($filtro['i_a_la_fecha'] != '') ? $this->modelo->obtenerNombreAreaSegunFecha($ficha['p_legajo'], $filtro['i_a_la_fecha']) : '';

									$cargo       = ($filtro['i_a_la_fecha'] != '') ? $this->modelo->obtenerNombreCargo($ficha['p_legajo'], $filtro['i_a_la_fecha']) : '';

									$depende_de  = ($filtro['i_a_la_fecha'] != '') ? $this->modelo->obtenerNombreDependeDe($ficha['p_legajo'], $filtro['i_a_la_fecha']) : '';
							?>
									<div class="i_ficha" onmouseover="javascript:$(this).setStyle('background-color','#E7E7E7');" onmouseout="javascript:$(this).setStyle('background-color','#DEDEDE');">
										<div class="i_ficha_datos">
										    <div class="i_ficha_datos_valor"><strong>Legajo:</strong>&nbsp;<?php echo number_format($ficha['p_legajo'], 0, '', '.').'/'.$cargo['digito']; ?></div>
										    <div class="i_ficha_datos_valor"><strong><?php echo $ficha['p_apellido'].', '.$ficha['p_nombre']; ?></strong></div>
										    <div class="i_ficha_datos_valor"><strong>&Aacute;rea:</strong>&nbsp;<?php echo ($nombre_area != '') ? $nombre_area : ''; ?></div>
										    <div class="i_ficha_datos_valor"><strong>Cargo:</strong>&nbsp;<?php echo ($cargo['cargo'] != '') ? $cargo['cargo'] : ''; ?></div>
										    <?php
										    // Si depende de alguien
										    if ( $depende_de != '' )
										    	// Se muestra de quien depende
												echo '<div class="i_ficha_datos_valor"><strong>Depende de:</strong>&nbsp;'.$depende_de['p_apellido'].', '.$depende_de['p_nombre'].'</div>';
											?>
										</div>
										<div class="i_ficha_foto_mini">
											<a>
												<span class="centrar_imagen"><img src="<?php echo $this->directorio_fotos; ?>resize.php?ancho=107&alto=107&imagen=<?php echo ($ficha['p_foto']) ? $ficha['p_foto'] : 'avatar.jpg'; ?>" ></span>
											</a>
										</div>
										<div class="i_ficha_ver_legajo">
											<br><br><br><br><br>
											<a href="javascript:refrescar('abms/index.php?controlador=personal&accion=editar&legajo=<?php echo $ficha['p_legajo']; ?>&pagina=<?php echo $filtro['i_pagina']; ?>', 'contenidoAjaxPrincipal');" title="Ver la Ficha del Empleado.">Ver Ficha</a>
										</div>
									</div>
									<div style="height:7px;font-size:0;clear:both;"></div>
						<?php
							    } // FIN DEL for
						?>
							    <script>
							  		var scroller = new Fx.Scroll($('i_borde2'));
									scroller.toTop();
							    </script>
						<?php
							} //FIN DEL SEGUNDO else
						} //FIN DEL PRIMER else
						?>
					</div><!-- FIN DE i_borde2 -->
				</div><!-- FIN DE i_borde1 -->
	        </form>
		</div>
		<script>
	        // Calendario para la fecha
	        var calDesde = new Zapatec.Calendar.setup({
			    inputField:"i_a_la_fecha",
			    ifFormat:"%d/%m/%Y",
			    button:"i_btfecha_personal",
			    showsTime:false
	        });

			// Para obtener el listado de personal
	        $('i_btBuscar').addEvent('click', function() {
				// Si no se eligió una fecha
				if ( $('i_a_la_fecha').value == '' )
					alert("Debe ingresar una fecha.");
				else
					enviarForm('formInformes', 'informes', 'contenidoAjaxResultadoInformes');
	        });

			// Para cerrar la modal
			$('i_btCerrar').addEvent('click', function() {
				$("capaFondo").setStyle('visibility','hidden');
				$("capaVentana").setStyle('visibility','hidden');
			});

			// Para publicar el listado en formato HTML
			$('i_btPublicar').addEvent('click', function() {
				$('i_btPublicar').setProperty('href', 'informes/index.php?controlador=informes&accion=crearFormatoHTMLPersonalBloquesPoliticos&i_a_la_fecha='+$('i_a_la_fecha').value+'&i_solo_activos='+$('i_solo_activos').value+'&i_orden='+$('filtro_opcion_orden').value);
			});

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoInformes'), {
			   handle: $('dragger_personal_bloques')
			});
		</script>
    	<?php
    }

    /**
     * 2019/08/09 XXXX
     * Se lista el personal de Planta Permanente
     * @param  string $listado [description]
     * @param  string $filtro  [description]
     */
    public function listarPersonalPlantaPermanente($listado = '', $filtro = '')
    {
    	$cantidad         = (isset($listado)) ? count($listado) : 0;
		$titulo           = "Personal Planta Permanente";
		$accion           = "listarPersonalPlantaPermanente";
		$filtro_en_sesion = "filtro_informes_personal_planta_permanente";
		?>
	    <script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxResultadoInformes" class="i_modal_contenedora i_modal_texto">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="<?php echo $this->controlador; ?>/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

			    <input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
			    <input type="hidden" name="accion" id="accion" value="<?php echo $accion; ?>" />
			    <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
			    <input type="hidden" name="i_enviado" id="i_enviado" value="enviado" />

			    <div id="dragger_personal_bloques" class="i_modal_titulo degradado"><?php echo $titulo; ?></div>
			    <div style="height:10px;font-size:0;"></div>

			    <div style="height:107px;">
					<div style="width:430px;height:107px;float:left;">
					    <div style="height:22px;">
							<div class="inf_fila">
								<div class="inf_label_nombre">A la fecha:</div>
								<div class="inf_combo" style="text-align:left;">
									<input type="text" id="i_a_la_fecha" name="i_a_la_fecha" value="<?php echo($_SESSION[$filtro_en_sesion]['i_a_la_fecha']) ? $this->formatearFecha($_SESSION[$filtro_en_sesion]['i_a_la_fecha']) : date("d/m/Y") ; ?>" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
									<input type="image" id="i_btfecha_personal" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha." align="top" width="16" height="16">
								</div>
							</div>
						</div>
						<div style="height:5px;font-size:0;clear:both;"></div>
					  	<div style="height:22px;">
							<div class="inf_fila">
								<div class="inf_label_nombre">Pertenecientes a:</div>
								<div class="inf_combo" style="text-align:left;">
									<input type="radio" name="i_perteneciente_a" id="op_pertenecientes_HCD" value="1" <?php echo ($_SESSION[$filtro_en_sesion]['i_perteneciente_a'] == 1 || $_SESSION[$filtro_en_sesion]['i_perteneciente_a'] == '') ? 'checked' : ''; ?> />&nbsp;HCD
									<input type="radio" name="i_perteneciente_a" id="op_pertenecientes_defensoria" value="2" <?php echo ($_SESSION[$filtro_en_sesion]['i_perteneciente_a'] == 2) ? 'checked' : ''; ?> />&nbsp;Defensor&iacute;a del Pueblo
									<input type="hidden" name="filtro_perteneciente_a" id="filtro_perteneciente_a" value="<?php echo $_SESSION[$filtro_en_sesion]['i_perteneciente_a']; ?>" />
								</div>
							</div>
						</div>
					</div>
					<div style="width:35px;height:107px;float:right;"></div>
					<div style="width:100px;height:107px;float:right;">
						<div class="checkbox_con_texto">
							<label><input type="checkbox" name="habilitado" id="habilitado" onchange="javascript:chequear('i_solo_activos');" <?php echo ($_SESSION[$filtro_en_sesion]['i_solo_activos'] == 1 ) ? 'checked' : ''; ?> >&nbsp;S&oacute;lo Activos</label>
							<input type="hidden" name="i_solo_activos" id="i_solo_activos" value="<?php echo($_SESSION[$filtro_en_sesion]['i_solo_activos']) ? $_SESSION[$filtro_en_sesion]['i_solo_activos'] : 0; ?>" />
						</div>
						<div class="p_boton_edicion" style="display:<?php echo ($filtro['i_enviado'] == '' ) ? 'none' : 'inline'; ?>" >
							<a id="i_btPublicar" target="_blank" >
								<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="left" />&nbsp;Publicar
							</a>
						</div>
						<div style="height:15px;font-size:0;"></div>
						<div class="p_boton_edicion">
							<a id="i_btBuscar" >
								<img src="imagenes/barra/zoom_16x16.gif" width="16" height="16" align="left" />Buscar
							</a>
						</div>
						<div style="height:15px;font-size:0;"></div>
						<div class="p_boton_edicion">
							<a id="i_btCerrar">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="left" />Cerrar
							</a>
						</div>
					</div>
			    </div>
			    <?php
			    // Se muestra el paginador sólo habiendo buscado previamente
				if ( $filtro['i_enviado'] != '' ) {
				?>
					<!-- PAGINADOR -->
					<div class="msc_paginador">
						<div class="msc_margen_paginador"></div>
						<div class="msc_flechas_paginador">
							<?php
							if ( $filtro['i_pagina'] != 1 ) {
							?>
								<a id="btPrimero" title="Primer Registro" href="javascript:refrescar('informes/index.php?controlador=informes&accion=<?php echo $accion; ?>&i_a_la_fecha='+$('i_a_la_fecha').value+'&i_solo_activos='+$('i_solo_activos').value+'&i_perteneciente_a='+$('filtro_perteneciente_a').value+'&i_enviado=enviado&i_pagina=1', 'contenidoAjaxResultadoInformes');">
									<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btPrimero" href="#">
									<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							}

							if ( $filtro['i_pagina_ant'] != 0 ) {
							?>
								<a id="btAnterior" title="Registro Anterior" href="javascript:refrescar('informes/index.php?controlador=informes&accion=<?php echo $accion; ?>&i_a_la_fecha='+$('i_a_la_fecha').value+'&i_solo_activos='+$('i_solo_activos').value+'&i_perteneciente_a='+$('filtro_perteneciente_a').value+'&i_enviado=enviado&i_pagina=<?php echo $filtro['i_pagina_ant']; ?>', 'contenidoAjaxResultadoInformes');">
									<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btAnterior" href="#">
									<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							}
							?>
						</div>
						<div class="msc_detalle_paginador"><?php echo $filtro['i_pagina'].' de '.$filtro['i_nro_paginas']; ?></div>
						<div class="msc_flechas_paginador">
							<?php
							if ( $filtro['i_pagina'] != $filtro['i_nro_paginas'] ) {
							?>
								<a id="btSiguiente" title="Registro Siguiente" href="javascript:refrescar('informes/index.php?controlador=informes&accion=<?php echo $accion; ?>&i_a_la_fecha='+$('i_a_la_fecha').value+'&i_solo_activos='+$('i_solo_activos').value+'&i_perteneciente_a='+$('filtro_perteneciente_a').value+'&i_enviado=enviado&i_pagina=<?php echo $filtro['i_pagina_sgte']; ?>', 'contenidoAjaxResultadoInformes');">
									<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btSiguiente" href="#">
									<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							}

							if ( $filtro['i_pagina'] != $filtro['i_nro_paginas'] ) {
							?>
								<a id="btUltimo" title="Ultimo Registro" href="javascript:refrescar('informes/index.php?controlador=informes&accion=<?php echo $accion; ?>&i_a_la_fecha='+$('i_a_la_fecha').value+'&i_solo_activos='+$('i_solo_activos').value+'&i_perteneciente_a='+$('filtro_perteneciente_a').value+'&i_enviado=enviado&i_pagina=<?php echo $filtro['i_nro_paginas']; ?>', 'contenidoAjaxResultadoInformes');">
									<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btUltimo" href="#">
									<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="16" height="16" align="center" />
								</a>
							<?php
							}
							?>
						</div>
					</div>
				<?php
				}
				?>
				<div class="i_borde1">
					<div id="i_borde2" class="i_borde2">
						<?php
						if ( $filtro['i_enviado'] == '' ) {
						?>
							  <!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
						<?php
						} else {
						    if ($cantidad < 0)
							  echo '<br><h1>Sin resultados</h1>';
						    else {
							    // SE LISTAN LAS FICHAS DE LOS EMPLEADOS DE PLANTA PERMANENTE
							    for ($e=0; $e < $cantidad; $e++) {

									$ficha = &$listado[$e];

									$nombre_area = ($filtro['i_a_la_fecha'] != '') ? $this->modelo->obtenerNombreAreaSegunFecha($ficha['p_legajo'], $filtro['i_a_la_fecha']) : '';

									$cargo       = ($filtro['i_a_la_fecha'] != '') ? $this->modelo->obtenerNombreCargo($ficha['p_legajo'], $filtro['i_a_la_fecha']) : '';

									$depende_de  = ($filtro['i_a_la_fecha'] != '') ? $this->modelo->obtenerNombreDependeDe($ficha['p_legajo'], $filtro['i_a_la_fecha']) : '';
							?>
									<div class="i_ficha" onmouseover="javascript:$(this).setStyle('background-color','#E7E7E7');" onmouseout="javascript:$(this).setStyle('background-color','#DEDEDE');">
										<div class="i_ficha_datos">
										    <div class="i_ficha_datos_valor"><strong>Legajo:</strong>&nbsp;<?php echo number_format($ficha['p_legajo'], 0, '', '.').'/'.$cargo['digito']; ?></div>
										    <div class="i_ficha_datos_valor"><strong><?php echo $ficha['p_apellido'].', '.$ficha['p_nombre']; ?></strong></div>
										    <div class="i_ficha_datos_valor"><strong>&Aacute;rea:</strong>&nbsp;<?php echo ($nombre_area != '') ? $nombre_area : ''; ?></div>
										    <div class="i_ficha_datos_valor"><strong>Cargo:</strong>&nbsp;<?php echo ($cargo['cargo'] != '') ? $cargo['cargo'] : ''; ?></div>
										    <?php
										    // Si depende de alguien
										    if ( $depende_de != '' )
										    	// Se muestra de quien depende
												echo '<div class="i_ficha_datos_valor"><strong>Depende de:</strong>&nbsp;'.$depende_de['p_apellido'].', '.$depende_de['p_nombre'].'</div>';
											?>
										</div>
										<div class="i_ficha_foto_mini">
											<a>
												<span class="centrar_imagen"><img src="<?php echo $this->directorio_fotos; ?>resize.php?ancho=107&alto=107&imagen=<?php echo ($ficha['p_foto']) ? $ficha['p_foto'] : 'avatar.jpg'; ?>" ></span>
											</a>
										</div>
										<div class="i_ficha_ver_legajo">
											<br><br><br><br><br>
											<a href="javascript:refrescar('abms/index.php?controlador=personal&accion=editar&legajo=<?php echo $ficha['p_legajo']; ?>&pagina=<?php echo $filtro['i_pagina']; ?>', 'contenidoAjaxPrincipal');" title="Ver la Ficha del Empleado.">Ver Ficha</a>
										</div>
									</div>
									<div style="height:7px;font-size:0;clear:both;"></div>
						<?php
							    } // FIN DEL for
						?>
							    <script>
							  		var scroller = new Fx.Scroll($('i_borde2'));
									scroller.toTop();
							    </script>
						<?php
							} //FIN DEL SEGUNDO else
						} //FIN DEL PRIMER else
						?>
					</div><!-- FIN DE i_borde2 -->
				</div><!-- FIN DE i_borde1 -->
	        </form>
		</div>
		<script>
	        // Calendario para la fecha
	        var calDesde = new Zapatec.Calendar.setup({
			    inputField:"i_a_la_fecha",
			    ifFormat:"%d/%m/%Y",
			    button:"i_btfecha_personal",
			    showsTime:false
	        });

			// Para obtener el listado de personal
	        $('i_btBuscar').addEvent('click', function() {
				// Si no se eligió una fecha
				if ( $('i_a_la_fecha').value == '' )
					alert("Debe ingresar una fecha.");
				else
					enviarForm('formInformes', 'informes', 'contenidoAjaxResultadoInformes');
	        });

			// Para cerrar la modal
			$('i_btCerrar').addEvent('click', function() {
				$("capaFondo").setStyle('visibility','hidden');
				$("capaVentana").setStyle('visibility','hidden');
			});

			// Para publicar el listado en formato HTML
			$('i_btPublicar').addEvent('click', function() {
				$('i_btPublicar').setProperty('href', 'informes/index.php?controlador=informes&accion=crearFormatoHTMLPersonalPlantaPermanente&i_a_la_fecha='+$('i_a_la_fecha').value+'&i_solo_activos='+$('i_solo_activos').value+'&i_perteneciente_a='+$('filtro_perteneciente_a').value);
			});

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoInformes'), {
			   handle: $('dragger_personal_bloques')
			});
		</script>
    	<?php
    }

	public function crear_formato_impresion($listado = '', $filtro = '')
	{
		ob_start();
	?>
		<style type="text/css">
			h4, h5 {
				font-family: Arial;
			}
			table {
				padding: 0;
				border-collapse: collapse;
			}
			.e_tabla_titulos {
				padding:2px 0 2px 0;
				text-align:center;
			}
			.e_tabla_titulos th {
				font-family: Arial;
				font-size: 11px;
				font-weight: 500;
				color: #fff;
				background-color: #004E65;
				border: 1px solid #BEBEBE;
				padding: 3px;
			}
			.e_cuerpo_scrolleable {
				overflow: auto;
				padding-right: 15px;
				background-color: #fff;
			}
			.e_cuerpo_scrolleable tr {
				height: 16px;
				font-family:Arial;
				font-size:10px;
			}
			.e_cuerpo_scrolleable td {
				padding-left: 10px;
				border: 1px solid #BEBEBE;
			}
		</style>
		<page backtop="31mm" backbottom="7mm" backleft="5mm" backright="1mm" style="font-size: 10px">
			<page_header>
				<table style="width: 100%;">
					<tr>
						<td style="width: 10%;" rowspan="5">
							<img src="../imagenes/escudo_cuatro_colores.gif" width="80" height="100" align="center" >
						</td>
						<td style="width:90%;">&nbsp;</td>
					</tr>
					<tr>
						<td style="width:90%;text-align:left;">Municipalidad de General Pueyrredon</td>
					</tr>
					<tr>
						<td style="width:90%;text-align:left;">Sistema de Personal</td>
					</tr>
					<tr>
						<td style="width:90%;text-align:left;">Honorable Concejo Deliberante</td>
					</tr>
					<tr>
						<td style="width:90%;">&nbsp;</td>
					</tr>
				</table>
			</page_header>
			<page_footer>
				<table style="width:100%;border:solid 1px black;">
					<tr>
						<td style="text-align: left;width: 50%">Fecha: <?php echo date("d/m/Y"); ?></td>
						<td style="text-align: right;width: 50%">P&aacute;gina [[page_cu]] de [[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<table width="100%">
				<thead class="e_tabla_titulos">
					<tr>
						<th>Legajo</th>
						<th>Apellido y Nombre</th>
						<th>Fecha Ingreso D.E.</th>
						<th>Fecha Ingreso HCD</th>
						<th>Fecha Egreso</th>
						<th>&Aacute;rea</th>
						<th>Cargo</th>
						<th>Depende de</th>
					</tr>
				</thead>
				<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
					<?php
					$cantidad = (isset($listado)) ? count($listado) : 0;
					for ($p=0; $p < $cantidad; $p++)
					{
						$dato = &$listado[$p];

						$area = $this->modeloPersonal->obtenerNombreUltimaArea($dato['p_legajo']);

						$cargo = $this->modeloPersonal->obtenerNombreUltimoCargo($dato['p_legajo']);

						$depende_de = $this->modeloPersonal->obtenerDependeDe($dato['p_legajo']);
					?>
						<tr class="e_tabla_titulos">
							<td style="width:30px;padding-right:3px;text-align:right;"><?php echo number_format($dato['p_legajo'], 0, '', '.'); ?>&nbsp;/&nbsp;<?php echo ($cargo['c_digito']) ? $cargo['c_digito'] : '&nbsp;'; ?></td>
							<td style="text-align:left;padding:0 3px 0 3px;"><?php echo $dato['p_apellido'].', '.$dato['p_nombre']; ?></td>
							<td style="width:70px;text-align:center;padding:0 3px 0 3px;"><?php echo ($dato['p_fecha_ingreso_planta_politica']) ? $this->formatearFecha($dato['p_fecha_ingreso_planta_politica']) : '&nbsp;'; ?></td>
							<td style="width:70px;text-align:center;padding:0 3px 0 3px;"><?php echo ($dato['c_fecha_alta']) ? $this->formatearFecha($dato['c_fecha_alta']) : '&nbsp;'; ?></td>
							<td style="width:70px;text-align:center;padding:0 3px 0 3px;"><?php echo ($cargo['c_fecha_baja']) ? $this->formatearFecha($cargo['c_fecha_baja']) : '&nbsp;'; ?></td>
							<td style="width:150px;text-align:left;padding:0 3px 0 3px;"><?php echo ($area['area']) ? $area['area'] : '&nbsp;'; ?></td>
							<td style="width:150px;text-align:left;padding:0 3px 0 3px;"><?php echo ($cargo['cargo']) ? $cargo['cargo'] : '&nbsp;' ?></td>
							<td style="width:200px;text-align:left;padding-left:3px;"><?php echo ($depende_de[0]['p_apellido']) ? $depende_de[0]['p_apellido'].", ".$depende_de[0]['p_nombre'] : '&nbsp;'; ?></td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</page>
		<?php
		$content = ob_get_clean();
		try
		{
			// conversion HTML => PDF
			//los métodos utilizados aquí están definidos en el archivo html2pdf.class.php
			$html2pdf = new HTML2PDF('L','LEGAL','es');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');
			//Tratamiento del código HTML
			$html2pdf->WriteHTML($content);
			//Destino donde enviar el documento
			$html2pdf->Output('listado_consulta_general.pdf');//, 'D'
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

	public function esDireccion($id_area)
	{
		// SE EXTRAE EL PRIMER PAR DE DIGITOS DEL ID DEL AREA
		$primer_par_digitos = substr($id_area, 0, 2);

		// SE EXTRAE EL TERCER PAR DE DIGITOS DEL ID DEL AREA
		$tercer_par_digitos = substr($id_area, 4, 6);

		// SI LOS PARES CORRESPONDEN AL FORMATO PARA LAS DIRECCIONES
		return ( $primer_par_digitos == '01' && $tercer_par_digitos == '00' );
	}

	/**
	 * 06/03/2020 XXXX
	 * Se utiliza Bootstrap 4 a partir de la fecha, para el nuevo sitio web.
	 *
	 * [crearFormatoHTMLPersonalBloquesPoliticos description]
	 * @param  string $bloques               [description]
	 * @param  string $concejales_sin_bloque [description]
	 * @param  string $filtro                [description]
	 * @return [type]                        [description]
	 */
	public function crearFormatoHTMLPersonalBloquesPoliticos($bloques = '', $concejales_sin_bloque = '', $filtro = '')
	{
		header("Content-Type: text/html; charset=UTF-8");

		ob_start();

		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

		$cantidad_bloques = count($bloques);

		// PARA CADA BLOQUE
		for ($b=0; $b < $cantidad_bloques; $b++) {
			$bloque = &$bloques[$b];

			$posicion = 0;
			$secretaria_bloque = null;// 15/01/2014

			// SE OBTIENEN LOS CONCEJALES DE DICHO BLOQUE
			// 25/04/2013: SE AGREGO EL ORDEN COMO PARAMETRO
			$concejales = $this->modelo->obtenerConcejalesPorBloque($bloque['ca_id'], $filtro['i_a_la_fecha'], $filtro['i_orden']);

			$cantidad_concejales = count($concejales);

			// SI EL BLOQUE POSEE CONCEJALES
			if ( $cantidad_concejales > 0 ) {
				// SE MUESTRA EL NOMBRE DE DICHO BLOQUE
				?>
				<h6 class="font-weight-bold mt-3 propio_nombre_bloque"><?php echo $this->reemplazarPorHTML($bloque['ca_nombre']); ?></h6>
				<?php
				// PARA CADA CONCEJAL
				for ($c=0; $c < $cantidad_concejales; $c++)
				{
					$concejal = &$concejales[$c];
					// SE MUESTRA EL NOMBRE DEL CONCEJAL
					?>
					<h6 class="font-weight-bold mt-3 ml-md-3 propio_nombre_concejales"><?php echo "Concejal ".$this->reemplazarPorHTML($concejal['p_apellido']).", ".$this->reemplazarPorHTML($concejal['p_nombre']); ?></h6>
					<?php
					// SE OBTIENEN LOS DEPENDIENTES DE DICHO CONCEJAL
					$dependientes = $this->modelo->obtenerDependientes($concejal['p_legajo'], $filtro['i_a_la_fecha'], $filtro['i_orden']);

					$cantidad_dependientes = count($dependientes);
					// PARA CADA DEPENDIENTE
					for ($d=0; $d < $cantidad_dependientes; $d++)
					{
						$dependiente = &$dependientes[$d];

						// SI NO PERTENECE A SECRETARIA SE MUESTRA
						if ( $dependiente['c_pertenece_secretaria_bloque'] == 0 ) {
						?>
							<div class="row">
								<div class="col-12 col-md-3 mt-3 ml-md-5"><?php echo $this->reemplazarPorHTML($dependiente['p_apellido']).", ".$this->reemplazarPorHTML($dependiente['p_nombre']); ?></div>
								<div class="col-12 col-md-3 mt-md-3"><?php echo number_format($dependiente['p_legajo'], 0, '', '.')." / ".$dependiente['c_digito']; ?></div>
							</div>
						<?php
						}
						else // SINO SE GUARDA EN UN VECTOR PARA MOSTRARLO COMO MIEMBRO DE LA SECRETARIA
						{
							$secretaria_bloque[$posicion]['p_apellido'] = $dependiente['p_apellido'];
							$secretaria_bloque[$posicion]['p_nombre'] = $dependiente['p_nombre'];
							$secretaria_bloque[$posicion]['p_legajo'] = $dependiente['p_legajo'];
							$secretaria_bloque[$posicion]['c_digito'] = $dependiente['c_digito'];

							$posicion++;
						}
					}
				}
				// AL FINALIZAR CON LOS CONCEJALES, SI DICHO BLOQUE POSEE SECRETARIA SE MUESTRAN SUS MIEMBROS
				if ( $secretaria_bloque[0]['p_apellido'] )
				{
				?>
					<h6 class="font-weight-bold mt-3 ml-md-3 propio_nombre_concejales">Secretar&iacute;a de Bloque</h6>
					<?php
					for ($s=0; $s < $posicion; $s++) {
					?>
						<div class="row">
							<div class="col-12 col-md-3 mt-3 ml-md-5"><?php echo $this->reemplazarPorHTML($secretaria_bloque[$s]['p_apellido']).", ".$this->reemplazarPorHTML($secretaria_bloque[$s]['p_nombre']); ?></div>
							<div class="col-12 col-md-3 mt-md-3"><?php echo number_format($secretaria_bloque[$s]['p_legajo'], 0, '', '.')." / ".$secretaria_bloque[$s]['c_digito']; ?></div>
						</div>
					<?php
					}
				}
			}
		}

		$cantidad_concejales_sin_bloque = count($concejales_sin_bloque);

		// SI EXISTEN CONCEJALES SIN PERTENECER A UN BLOQUE
		if ( $cantidad_concejales_sin_bloque > 0 )
		{
			?>
			<h6 class="font-weight-bold mt-3 propio_nombre_bloque">Concejales</h6>
			<?php
			// PARA CADA CONCEJAL
			for ($j=0; $j < $cantidad_concejales_sin_bloque; $j++)
			{
				$concejal_sin_bloque = &$concejales_sin_bloque[$j];
				// SE MUESTRA EL NOMBRE DEL CONCEJAL
				?>
				<h6 class="font-weight-bold mt-3 ml-md-3 propio_nombre_concejales"><?php echo "Concejal ".$this->reemplazarPorHTML($concejal_sin_bloque['p_apellido']).", ".$this->reemplazarPorHTML($concejal_sin_bloque['p_nombre']); ?></h6>
				<?php
				$posicion = 0;
				$secretaria = null;

				// SE OBTIENEN LOS DEPENDIENTES DE DICHO CONCEJAL
				$dependientes = $this->modelo->obtenerDependientes($concejal_sin_bloque['p_legajo'], $filtro['i_a_la_fecha'], $filtro['i_orden']);

				$cantidad_dependientes = count($dependientes);
				// PARA CADA DEPENDIENTE
				for ($d=0; $d < $cantidad_dependientes; $d++) {
					$dependiente = &$dependientes[$d];

					// SI NO PERTENECE A SECRETARIA SE MUESTRA
					if ( $dependiente['c_pertenece_secretaria_bloque'] == 0 ) {
						?>
						<div class="row">
							<div class="col-12 col-md-3 mt-3 ml-md-5"><?php echo $this->reemplazarPorHTML($dependiente['p_apellido']).", ".$this->reemplazarPorHTML($dependiente['p_nombre']); ?></div>
							<div class="col-12 col-md-3 mt-md-3"><?php echo number_format($dependiente['p_legajo'], 0, '', '.')." / ".$dependiente['c_digito']; ?></div>
						</div>
						<?php
					}
					else // SINO SE GUARDA EN UN VECTOR PARA MOSTRARLO COMO MIEMBRO DE LA SECRETARIA
					{
						$secretaria[$posicion]['p_apellido'] = $dependiente['p_apellido'];
						$secretaria[$posicion]['p_nombre']   = $dependiente['p_nombre'];
						$secretaria[$posicion]['p_legajo']   = $dependiente['p_legajo'];
						$secretaria[$posicion]['c_digito']   = $dependiente['c_digito'];

						$posicion++;
					}
				}
				// SI POSEE SECRETARIA SE MUESTRAN SUS MIEMBROS
				if ( $secretaria[0]['p_apellido'] ) {
					?>
					<h6 class="font-weight-bold mt-3 ml-md-3 propio_nombre_concejales">Secretar&iacute;a</h6>
					<?php
					for ($s=0; $s < $posicion; $s++) {
					?>
						<div class="row">
							<div class="col-12 col-md-3 mt-3 ml-md-5"><?php echo $this->reemplazarPorHTML($secretaria[$s]['p_apellido']).", ".$this->reemplazarPorHTML($secretaria[$s]['p_nombre']); ?></div>
							<div class="col-12 col-md-3 mt-md-3"><?php echo number_format($secretaria[$s]['p_legajo'], 0, '', '.')." / ".$secretaria[$s]['c_digito']; ?></div>
						</div>
					<?php
					}
				}
			}
		}
		?>

		<p class="mt-5 font-weight-bold"><?php echo "Actualizado al ".date('d')." de ".$meses[date('n')-1]. " de ".date('Y') ; ?></p>

		<?php
		// CONTENIDO DEL HTML A ESCRIBIR
		$contenido = ob_get_clean();

		// SE ESCRIBE EL CONTENIDO EN ppolitica.html
		fputs(fopen('temporal/ppolitica.html','w'), print_r($contenido, true));

		// SE MUESTRA EL LISTADO TEMPORAL PARA CONFIRMAR SU PUBLICACION
		$this->mostrarListado('ppolitica');
	}

	/**
	 * Se genera el HTML del listado de Planta Permanente (HCD / Defensoría del Pueblo)
	 * @param  string $areas_planta
	 * @param  string $filtro
	 * @return HTML
	 */
	public function crearFormatoHTMLPersonalPlantaPermanente($areas_planta = '', $filtro = '')
	{
		header("Content-Type: text/html; charset=UTF-8");

		ob_start();

		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

		$cantidad_areas_planta = count($areas_planta);
		for ($ap=0; $ap < $cantidad_areas_planta; $ap++) {

			$area = &$areas_planta[$ap];

			// Si es una Dirección, se muestra sin sangría
			$sangria = ($this->esDireccion($area['ca_id'])) ? '' : ' ml-md-3';
			?>
			<h6 class="font-weight-bold mt-3 <?= $sangria; ?>">
				<?= $this->reemplazarPorHTML($area['ca_nombre']); ?>
			</h6>
			<?php
			$legajos = $this->modelo->obtenerPersonalPorArea($area['ca_id'], $filtro['i_a_la_fecha']);
			$cantidad_legajos = (isset($legajos)) ? count($legajos) : 0;

			if ( $cantidad_legajos > 0 ) {

				for ($l=0; $l < $cantidad_legajos; $l++) {
					$legajo = &$legajos[$l];
					$cargo = $this->modelo->obtenerNombreCargo($legajo['p_legajo'], $filtro['i_a_la_fecha']);
				?>
					<div class="row">
						<div class="col-12 col-md-3 mt-1 ml-md-5">
							<?= $this->reemplazarPorHTML($legajo['p_apellido']).", ".$this->reemplazarPorHTML($legajo['p_nombre']); ?>
						</div>
						<div class="col-12 col-md-3 mt-md-1 mb-2 mb-md-0">
							<?= $this->reemplazarPorHTML($cargo['cargo']); ?>
						</div>
					</div>
				<?php
				}
			}
		}
		?>
		<p class="mt-5 font-weight-bold">
			<?= "Actualizado al ".date('d')." de ".$meses[date('n')-1]. " de ".date('Y') ;?>
		</p>
		<?php
		$contenido = ob_get_clean();

		// Si el área es Defensoria del Pueblo
		if ( $area['ca_id'] == '01100000' ) {
			fputs(fopen('temporal/ppermanente_defensoria.html','w'), print_r($contenido, true));
			$this->mostrarListado('ppermanente_defensoria');
		} else {
			fputs(fopen('temporal/ppermanente.html','w'), print_r($contenido, true));
			$this->mostrarListado('ppermanente');
		}
	}

	/**
	 * 06/03/2020 XXXX
	 * Se modifica el HTML de la visualización para la confirmación
	 *
	 * Muestra el html del listado de empleados
	 * @param string $nombre_archivo
	 */
	public function mostrarListado($nombre_archivo) {
	?>
		<!-- CSS de Bootstrap 4.4.1 del sitio web -->
		<link rel="stylesheet" type="text/css" href="<?php echo $this->url_sitio_web_real; ?>css/bootstrap_4.4.1.css">
		<!-- CSS propio del sitio web -->
		<link rel="stylesheet" type="text/css" href="<?php echo $this->url_sitio_web_real; ?>css/propio.css">

		<div class="container-fluid p-0">
			<div class="row no-gutters">
	          	<div class="col-12 ml-3 ml-md-5">
					<div class="text-center p-3">
						<button name="confirmar" type="button" class="btn btn-sm btn-info mb-2" onclick="javascript:confirmarPublicacion();">Confirmar</button>
					</div>

					<?php include("../informes/temporal/".$nombre_archivo.".html"); ?>

					<div class="text-center p-3">
						<button name="confirmar" type="button" class="btn btn-sm btn-info mt-2" onclick="javascript:confirmarPublicacion();">Confirmar</button>
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript">
			function confirmarPublicacion() {
				location.href = "index.php?controlador=informes&accion=confirmarPublicacion&nombre_archivo=<?php echo $nombre_archivo; ?>";
			}
		</script>
	<?php
	}

    /**
     * Se recarga el combo de Concejales
     *
     * @param array $listadoConcejales
     * @param integer $cod_concejal
     */
	public function comboConcejales($listadoConcejales, $cod_concejal = 0) {
    ?>
		<select id="i_concejal" name="i_concejal" class="mslexp_combo">
			<option value="0">Seleccione un Concejal</option>
			<?php
			$cant_concejales = count($listadoConcejales);
			for ($c=0; $c < $cant_concejales; $c++)
			{
				$concejal = &$listadoConcejales[$c];
			?>
				<option value="<?php echo $concejal['p_legajo']; ?>"><?php echo $concejal['p_apellido'].', '.$concejal['p_nombre']; ?></option>
			<?php
			}
			?>
		</select>
		&nbsp;
		<a id="imagen_zoom_comisiones_Listados" href="javascript:modalGaby('informes/index.php?controlador=informes&accion=pedirNombreConcejalModal&id_combo=i_concejal');" title="Buscar por Nombre de Concejal"><img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" /></a>

		<script type="text/javascript">
			$('i_concejal').value = '<?php echo ($cod_concejal != 0) ? $cod_concejal : 0; ?>';

			if ( $('i_retira') ) {
				// AL CAMBIAR EL CONCEJAL SE REFREZCAN LOS DEPENDIENTES
				$('i_concejal').addEvent('change', function() {
					refrescar('informes/index.php?controlador=informes&accion=refrescarComboRetira&i_bloque='+$('i_bloque').value+'&i_concejal='+$('i_concejal').value+'', 'informes_contenedora_comboRetira');
				});
			}
		</script>
    <?php
    }

	public function comboAreas($listadoAreas, $area = 0) {
    ?>
		<select id="i_area" name="i_area" class="mslexp_combo">
			<option value="0">Seleccione un &Aacute;rea</option>
			<?php
			$cant_areas = count($listadoAreas);
			for ($b=0; $b < $cant_areas; $b++) {
				$fila_area = &$listadoAreas[$b];
			?>
				<option value="<?php echo $fila_area['ca_id']; ?>"><?php echo $fila_area['ca_nombre']; ?></option>
			<?php
			}
			?>
	    </select>
		&nbsp;
		<a id="imagen_zoom_comisiones_Listados" href="javascript:modalGaby('informes/index.php?controlador=informes&accion=pedirNombreAreaModal');" title="Buscar por Nombre de Area"><img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" /></a>

		<script type="text/javascript">
			$('i_area').value = <?php echo ($area) ? "'".$area."'" : 0; ?>;
		</script>
    <?php
    }

	public function comboCargos($listadoCargos) {
    ?>
		<select id="i_cargo" name="i_cargo" class="mslexp_combo">
			<option value="0">Seleccione un Cargo</option>
			<?php
			$cant_cargos = count($listadoCargos);
			for ($c=0; $c < $cant_cargos; $c++) {
				$fila_cargo = &$listadoCargos[$c];
			?>
				<option value="<?php echo $fila_cargo['cc_nomenclador']; ?>"><?php echo $fila_cargo['cc_nombre']; ?></option>
			<?php
			}
			?>
		</select>
		&nbsp;
		<a id="imagen_zoom_comisiones_Listados" href="javascript:modalGaby('informes/index.php?controlador=informes&accion=pedirNombreCargoModal');" title="Buscar por Nombre de Cargo"><img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" /></a>

		<script type="text/javascript">
			$('i_cargo').value = <?php echo ($_SESSION['filtro_informes']['i_cargo']) ? "'".$_SESSION['filtro_informes']['i_cargo']."'" : 0; ?>;
		</script>
    <?php
    }

	public function comboRetira($listado, $cod_retira = 0, $id_area) {
	?>
		<select id="i_retira" name="i_retira">
			<option value="0">---</option>
			<?php
			$cant_retira = (isset($listado)) ? count($listado) : 0;
			for ($r=0; $r < $cant_retira; $r++) {
				$retira = &$listado[$r];
			?>
				<option value="<?php echo $retira['p_legajo']; ?>"><?php echo $retira['p_apellido'].', '.$retira['p_nombre']; ?></option>
			<?php
			}
		  ?>
		</select>
		&nbsp;
		<a id="imagen_zoom_comisiones_Listados" href="javascript:modalGaby('informes/index.php?controlador=informes&accion=pedirNombreRetiraModal&id_area=<?php echo $id_area; ?>');" title="Buscar integrantes del Area.">
			<img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" />
		</a>

		<script type="text/javascript">
			// Si se conoce el legajo que retira, se selecciona, sino por defecto queda vacío
			$('i_retira').value = '<?php echo $cod_retira; ?>';
		</script>
	<?php
    }

	public function pedirNombreConcejalModal($listadoModal, $id_combo) {
	?>
		<div class="autosugerido">
			Nombre: <input type="text" id="nombre_sugerido" name="nombre_sugerido" maxlength="100" />
			<div id="sugerencias"><ul></ul></div>
		</div>
		<div class="margen_modal"></div>
		<div class="cerrar_pedirNombreModal">
			<div class="titulo_pedirNombreModal">Buscar por Concejal.</div>
			<div id="btCerrar_pedirNombreModal" class="btCerrar_pedirNombreModal"></div>
		</div>
		<script type="text/javascript">

			ventana_modal = "si";

			var concejales_a_elegir = new Array(
				<?php
				$cantidad = count($listadoModal);
				for ($i=0; $i < $cantidad; $i++) {
					$concejalModal = &$listadoModal[$i];

					if ( $i == $cantidad-1 )
					{
						echo "'".$concejalModal['p_legajo'].", ".$concejalModal['p_apellido'].", ".$concejalModal['p_nombre']."'";
					}
					else
					{
						echo "'".$concejalModal['p_legajo'].", ".$concejalModal['p_apellido'].", ".$concejalModal['p_nombre']."',";
					}
				}
				?>
			);

			new AutoSuggest($('nombre_sugerido'),concejales_a_elegir, '<?php echo $id_combo; ?>', 'doble');

			$('btCerrar_pedirNombreModal').addEvent('click', function(){
				cerrarModalPedirNombre();
			});
		</script>
	<?php
	}

	public function pedirNombreAreaModal($listadoModal) {
	?>
		<div class="autosugerido">
			Nombre: <input type="text" id="nombre_sugerido" name="nombre_sugerido" maxlength="100" />
			<div id="sugerencias"><ul></ul></div>
		</div>
		<div class="margen_modal"></div>
		<div class="cerrar_pedirNombreModal">
			<div class="titulo_pedirNombreModal">Buscar por &Aacute;rea.</div>
			<div id="btCerrar_pedirNombreModal" class="btCerrar_pedirNombreModal"></div>
		</div>
		<script type="text/javascript">

			ventana_modal = "si";

			var areas_a_elegir = new Array(
				<?php
				$cantidad = count($listadoModal);
				for ($i=0; $i < $cantidad; $i++)
				{
					$areaModal = &$listadoModal[$i];

					if ( $i == $cantidad-1 )
					{
						echo "'".$areaModal['ca_id'].", ".$areaModal['ca_nombre']."'";
					}
					else
					{
						echo "'".$areaModal['ca_id'].", ".$areaModal['ca_nombre']."',";
					}
				}
				?>
			);

			new AutoSuggest($('nombre_sugerido'), areas_a_elegir, 'i_area', 'simple');

			$('btCerrar_pedirNombreModal').addEvent('click', function(){
				cerrarModalPedirNombre();
			});
		</script>
	<?php
	}

	public function pedirNombreCargoModal($listadoModal)
	{
		$cantidad = count($listadoModal);
	?>
		<div class="autosugerido">
			Nombre: <input type="text" id="nombre_sugerido" name="nombre_sugerido" maxlength="100" />
			<div id="sugerencias"><ul></ul></div>
		</div>
		<div class="margen_modal"></div>
		<div class="cerrar_pedirNombreModal">
			<div class="titulo_pedirNombreModal">Buscar por Cargo.</div>
			<div id="btCerrar_pedirNombreModal" class="btCerrar_pedirNombreModal"></div>
		</div>
		<script type="text/javascript">

			//ventana_modal = "si";

			$('btCerrar_pedirNombreModal').addEvent('click', function(){
				cerrarModalPedirNombre();
			});

			var cargos_a_elegir = new Array(
				<?php
				for ($i=0; $i < $cantidad; $i++) {
					$cargoModal = &$listadoModal[$i];

					if ( $i == $cantidad-1 )
						echo "'".$cargoModal['cc_nomenclador'].", ".$cargoModal['cc_nombre']."'";
					else
						echo "'".$cargoModal['cc_nomenclador'].", ".$cargoModal['cc_nombre']."',";
				}
				?>
			);

			new AutoSuggest($('nombre_sugerido'), cargos_a_elegir, 'i_cargo', 'simple');

		</script>
	<?php
	}

	public function pedirNombreAreaParaLiquidacionesModal($listadoModal)
	{
	?>
		<div class="autosugerido">
			Nombre: <input type="text" id="nombre_sugerido" name="nombre_sugerido" maxlength="100" />
			<div id="sugerencias"><ul></ul></div>
		</div>
		<div class="margen_modal"></div>
		<div class="cerrar_pedirNombreModal">
			<div class="titulo_pedirNombreModal">Buscar por &Aacute;rea.</div>
			<div id="btCerrar_pedirNombreModal" class="btCerrar_pedirNombreModal"></div>
		</div>
		<script type="text/javascript">

			ventana_modal = "si";

			var bloques_a_elegir = new Array(
				<?php
				$cantidad = count($listadoModal);
				for ($i=0; $i < $cantidad; $i++)
				{
					$bloqueModal = &$listadoModal[$i];

					if ( $i == $cantidad-1 )
					{
						echo "'".$bloqueModal['ca_id'].", ".$bloqueModal['ca_nombre']."'";
					}
					else
					{
						echo "'".$bloqueModal['ca_id'].", ".$bloqueModal['ca_nombre']."',";
					}
				}
				?>
			);

			new AutoSuggest($('nombre_sugerido'), bloques_a_elegir, 'i_area', 'simple');

			$('btCerrar_pedirNombreModal').addEvent('click', function(){
				cerrarModalPedirNombre();
			});

		</script>
	<?php
	}

	public function pedirNombreRetiraModal($listadoModal)
	{
	?>
		<div class="autosugerido">
			Nombre: <input type="text" id="nombre_sugerido" name="nombre_sugerido" maxlength="100" />
			<div id="sugerencias"><ul></ul></div>
		</div>
		<div class="margen_modal"></div>
		<div class="cerrar_pedirNombreModal">
			<div class="titulo_pedirNombreModal">Buscar Personal del Area.</div>
			<div id="btCerrar_pedirNombreModal" class="btCerrar_pedirNombreModal"></div>
		</div>
		<script type="text/javascript">

			ventana_modal = "si";

			var personal_bloque_a_elegir = new Array(
				<?php
				$cantidad = count($listadoModal);
				for ($i=0; $i < $cantidad; $i++)
				{
					$personalBloqueModal = &$listadoModal[$i];

					if ( $i == $cantidad-1 )
					{
						echo "'".$personalBloqueModal['p_legajo'].", ".$personalBloqueModal['p_apellido'].", ".$personalBloqueModal['p_nombre']."'";
					}
					else
					{
						echo "'".$personalBloqueModal['p_legajo'].", ".$personalBloqueModal['p_apellido'].", ".$personalBloqueModal['p_nombre']."',";
					}
				}
				?>
			);

			new AutoSuggest($('nombre_sugerido'),personal_bloque_a_elegir, 'i_retira', 'doble');

			$('btCerrar_pedirNombreModal').addEvent('click', function(){
				cerrarModalPedirNombre();
			});
		</script>
	<?php
	}

    public function listarParaCertificado($bloques = '')
    {
	?>
	    <script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxResultadoInformes" class="i_modal_contenedora i_modal_texto">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="informes/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

			    <input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
			    <input type="hidden" name="accion" id="accion" value="crearFormatoImpresionListadoParaCertificado" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="i_enviado" id="i_enviado" value="enviado" />

				<div id="dragger_certificado" class="i_modal_titulo degradado">Certificado Mensual</div>
				<div style="height:10px;font-size:0;"></div>

				<div style="height:360px;">
					<div style="width:430px;height:360px;float:left;">

						<div class="inf_fila">
							<div class="inf_label_nombre">Mes :</div>
							<div class="inf_combo" style="width:100px;">
								<select id="i_mes" name="i_mes" style="width:100px;">
									<option value="0">---</option>
									<option value="1">Enero</option>
									<option value="2">Febrero</option>
									<option value="3">Marzo</option>
									<option value="4">Abril</option>
									<option value="5">Mayo</option>
									<option value="6">Junio</option>
									<option value="7">Julio</option>
									<option value="8">Agosto</option>
									<option value="9">Septiembre</option>
									<option value="10">Octubre</option>
									<option value="11">Noviembre</option>
									<option value="12">Diciembre</option>
								</select>
							</div>
							<div class="inf_label_nombre" style="width:52px;">A&ntilde;o :</div>
							<div class="inf_combo" style="width:100px;">
								<select id="i_anio" name="i_anio" style="width:100px;">
									<option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
									<option value="<?php echo date("Y")-1; ?>"><?php echo date("Y")-1; ?></option>
								</select>
							</div>
						</div>

						<div style="height:5px;font-size:0;clear:both;"></div>

						<div class="inf_fila">
							<div class="inf_label_nombre">Bloque :</div>
							<div class="inf_combo">
							  	<select id="i_bloque" name="i_bloque">
									  <option value="0">Seleccione un Bloque</option>
									  <?php
									  $cant_bloques = count($bloques);
									  for ($b=0; $b < $cant_bloques; $b++)
									  {
										  $bloque = &$bloques[$b];
									  ?>
										  <option value="<?php echo $bloque['ca_id']; ?>"><?php echo $bloque['ca_nombre']; ?></option>
									  <?php
									  }
									  ?>
								</select>
								&nbsp;
								<a id="imagen_zoom_comisiones_Listados" href="javascript:modalGaby('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=pedirNombreBloqueModal');" title="Buscar por Nombre de Bloque"><img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" /></a>
							</div>
						</div>

						<div style="height:5px;font-size:0;clear:both;"></div>

						<div class="informe_contenedor_concejales">
							<div class="informe_contenedor_concejales_nombre">Concejales :</div>
							<div class="informe_contenedor_concejales_listado">
								<div id="informes_listado_concejales" class="informes_listado_concejales checkbox_con_texto">

								</div>
							</div>
						</div>
						<div style="padding:0 0 3px 103px;text-align:left;clear:both;">
							Formato de P&aacute;gina:&nbsp;
							<input type="radio" name="i_opcion_formato_pagina" id="i_opcion_formato_A4" value="A4" />&nbsp;A4&nbsp;&nbsp;&nbsp;
							<input type="radio" name="i_opcion_formato_pagina" id="i_opcion_formato_Legal" value="Legal" checked />&nbsp;Legal (Oficio)
							<input type="hidden" name="filtro_i_opcion_formato_pagina" id="filtro_i_opcion_formato_pagina" value="<?php echo $_SESSION['filtro_listado_para_liquidaciones']['filtro_i_opcion_formato_pagina']; ?>" />
						</div>
					</div>
					<div style="width:35px;height:360px;float:right;">

					</div>
					<div style="width:100px;height:360px;float:right;">
						<div class="p_boton_edicion">
							<a id="i_btImpresion" href="javascript:generarCertificado();">
								<img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;Generar
							</a>
						</div>
						<div style="height:15px;font-size:0;"></div>
						<div class="p_boton_edicion">
							<a id="i_btCerrar">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="left" />Cerrar
							</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	    <script>
	        $('i_btCerrar').addEvent('click', function()
		    {
				$("capaFondo").setStyle('visibility','hidden');
				$("capaVentana").setStyle('visibility','hidden');
			});

			function generarCertificado()
			{
				var mensaje = '';
				var error = false;

				if ($('i_mes').value == 0)
				{
					error = true;
					mensaje += "Debe seleccionar un Mes.\n";
				}

				if ($('i_anio').value == 0)
				{
					error = true;
					mensaje += "Debe seleccionar un A"+'\u00f1'+"o.\n";
				}

				if ( !verificarCheckbox('<?php echo $this->formulario; ?>') )
				{
					error = true;
					mensaje += "Debe seleccionar un Concejal.";
				}

				if (error)
				{
					alert(mensaje);
				}
				else
				{
					$('<?php echo $this->formulario; ?>').submit();
				}
			}

			function refrezcarListadoConcejales()
			{
				// SI HAY UN BLOQUE SELECCIONADO
				if ( $('i_bloque').value != 0 )
				{
					refrescar('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=refrescarConcejalesParaCertificado&i_mes='+$('i_mes').value+'&i_anio='+$('i_anio').value+'&i_bloque='+$('i_bloque').value, 'informes_listado_concejales');
				}
				else
				{
					document.getElementById("informes_listado_concejales").innerHTML = "";
				}
			}

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoInformes'), {
			   handle: $('dragger_certificado')
			});

			$('i_mes').value = <?php echo ($_SESSION['filtro_listado_para_certificados']['i_mes']) ? "'".$_SESSION['filtro_listado_para_certificados']['i_mes']."'" : date("n") ; ?>;

			$('i_anio').value = <?php echo ($_SESSION['filtro_listado_para_certificados']['i_anio']) ? "'".$_SESSION['filtro_listado_para_certificados']['i_anio']."'" : date("Y") ; ?>;

			$('i_bloque').value = <?php echo ($_SESSION['filtro_listado_para_certificados']['i_bloque']) ? "'".$_SESSION['filtro_listado_para_certificados']['i_bloque']."'" : 0 ; ?>;

			// Al cambiar de Mes
			$('i_mes').addEvent('change', function()
			{
				refrezcarListadoConcejales();
			});

			// Al cambiar de Año
			$('i_anio').addEvent('change', function()
			{
				refrezcarListadoConcejales();
			});

			// Al cargarse el Bloque
			$('i_bloque').addEvent('domready', function()
		    {
				// Se refrezca el listado de Concejales
				refrezcarListadoConcejales();
			});

			// Al cambiar de Bloque
			$('i_bloque').addEvent('change', function()
		    {
			    // Se refrezca el listado de Concejales
				refrezcarListadoConcejales();
			});

			// SI SE ELIGE EL FORMATO DE PAGINA A4
			$('i_opcion_formato_A4').addEvent('click', function(){
				$('filtro_i_opcion_formato_pagina').value = 1;
				$('i_opcion_formato_A4').checked = true;
			});

			// SI SE ELIGE EL FORMATO DE PAGINA LEGAL
			$('i_opcion_formato_Legal').addEvent('click', function(){
				$('filtro_i_opcion_formato_pagina').value = 2;
				$('i_opcion_formato_Legal').checked = true;
			});

			// SI SE LIQUIDA UN AGUINALDO SE MARCA SU OPCION
			if ( $('filtro_i_opcion_formato_pagina').value == 'A4' )
			{
				$('i_opcion_formato_A4').checked = true;
			}
			else
			{
				// SI SE LIQUIDA UN ADICIONAL SE MARCA SU OPCION
				if ( $('filtro_i_opcion_formato_pagina').value == 'Legal' )
				{
					$('i_opcion_formato_Legal').checked = true;
				}
			}
		</script>
    <?php
    }

   	/**
   	 * CORREGIDO EL 09/11/2017 XXXX, XXXX
   	 *
   	 * [crearFormatoImpresionListadoParaCertificado description]
   	 * @param  string $filtro                    [description]
   	 * @param  [type] $listado_activos_en_el_mes [description]
   	 * @return [type]                            [description]
   	 */
	public function crearFormatoImpresionListadoParaCertificado($filtro = '', $listado_activos_en_el_mes = null)
	{
		ob_start();

		$nombre_archivo_certificados = "certificados_por_concejal_".$this->nombreMes($filtro['i_mes'])."_".$filtro['i_anio'].".pdf";

		$posicion = 0;
		$secretaria_bloque = null;

		$cantidad_concejales = ($filtro['i_concejales']) ? count($filtro['i_concejales']) : 0;

		$cantidad_listado_activos = ($listado_activos_en_el_mes) ? count($listado_activos_en_el_mes) : 0;
	?>
		<style type="text/css">
			h4 {
				font-family: Arial;
			}
			table {
				border-collapse: collapse;
				border: 0;
			}
			.imp_texto {
				font-family: Arial;
				font-size: 13px;
				font-weight: 500;
				color: #000;
				text-align: left;
			}
			.imp_alineado_derecha {
				text-align: right;
			}
			.primeralinea {
				text-indent: 100px;
			}
			.imp_datos_dependientes td {
				padding: 5px 5px 5px 10px;
				border-collapse: collapse;
			}
		</style>

		<?php
		// Por cada Concejal
		for ($c=0; $c < $cantidad_concejales; $c++)
		{
			// Se toma su Legajo
			$legajo_concejal = &$filtro['i_concejales'][$c];

			// Se obtiene su información
			$info_concejal = $this->modelo->obtenerInformacionLegajo($legajo_concejal);
		?>
			<page backtop="30mm" backbottom="7mm" backleft="10mm" backright="10mm" style="font-size: 12px">
				<page_header>
					<p class="imp_texto imp_alineado_derecha">
						Mar del Plata, <?php echo $this->nombreMes(date("n")); ?> de <?php echo $filtro['i_anio']; ?>
					</p>
					<p class="imp_texto primeralinea">
						Certifico que las personas que m&aacute;s abajo se detallan, han prestado servicios en este Bloque Pol&iacute;tico, cumpliendo funciones como colaboradores del suscripto, <b>durante el mes de <?php echo $this->nombreMes($filtro['i_mes']); ?> de <?php echo $filtro['i_anio']; ?>.</b>
					</p>
				</page_header>
				<table width="100%" class="imp_texto">
					<?php
					// Se recorre el listado de personal activo
					for ($l=0; $l < $cantidad_listado_activos; $l++)
					{
						$info_legajo_activo = &$listado_activos_en_el_mes[$l];

 						// Si el Agente depende del Concejal respectivo
 						if ($legajo_concejal == $info_legajo_activo['c_depende_de']) {

							// Si pertenece a una Secretaría, NO se muestra aún
							if ( $info_legajo_activo['c_pertenece_secretaria_bloque'] != 0 )
							{
								// Se asigna su información en un array para mostrarla al final del listado, como integrante de la Secretaría
								$secretaria_bloque[$posicion]['p_apellido'] = $info_legajo_activo['p_apellido'];
								$secretaria_bloque[$posicion]['p_nombre'] = $info_legajo_activo['p_nombre'];
								$secretaria_bloque[$posicion]['c_legajo'] = $info_legajo_activo['c_legajo'];
								$secretaria_bloque[$posicion]['c_digito'] = $info_legajo_activo['c_digito'];

								$posicion++;
							}
							else // Si NO pertenece a una Secretaría, se muestra su información
							{
							?>
								<tr class="imp_datos_dependientes">
									<td><?php echo $info_legajo_activo['p_apellido'].", ".$info_legajo_activo['p_nombre']; ?></td>
									<td>Leg. <?php echo number_format($info_legajo_activo['c_legajo'], 0, '', '.')."/".$info_legajo_activo['c_digito']; ?></td>
								</tr>
							<?php
							}
						}
					}
					?>
				</table>
				<h4><?php echo 'CONCEJAL '.$info_concejal['p_apellido'].', '.$info_concejal['p_nombre']; ?></h4>
			</page>
		<?php
		}

		// Si el Bloque posee Secretaría, se muestran sus integrantes en otra página
		if ( $secretaria_bloque[0]['p_apellido'] )
		{
		?>
			<page pageset="old">
				<table width="100%" class="imp_texto">
					<?php
					for ($s=0; $s < $posicion; $s++)
					{
					?>
						<tr class="imp_datos_dependientes">
							<td><?php echo $secretaria_bloque[$s]['p_apellido'].", ".$secretaria_bloque[$s]['p_nombre']; ?></td>
							<td>Leg. <?php echo number_format($secretaria_bloque[$s]['c_legajo'], 0, '', '.')."/".$secretaria_bloque[$s]['c_digito']; ?></td>
						</tr>
					<?php
					}
					?>
				</table>
				<?php
				// Se obtiene el nombre del Bloque respectivo
				$nombre_bloque = ( $filtro['i_bloque'] == '0' ) ? $this->modelo->obtenerNombreArea($secretaria_bloque[0]['c_legajo']) : $filtro['nombre_bloque'];
				?>
				<h4>Secretar&iacute;a de <?php echo $nombre_bloque; ?></h4>
			</page>
		<?php
		}

		$content = ob_get_clean();

		try
		{
			// conversion HTML => PDF
			//los métodos utilizados aquí están definidos en el archivo html2pdf.class.php
			$html2pdf = new HTML2PDF('P', $filtro['i_opcion_formato_pagina'],'es', array(mL, mT, mR, mB));
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');

			//Tratamiento del código HTML
			$html2pdf->WriteHTML($content);

			//Destino donde enviar el documento
			$html2pdf->Output($nombre_archivo_certificados, 'D');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

	/**
	 * Se visualiza una ventana modal con un criterio de búsqueda determinado, para generar el Listado de Personal dependiente de un Concejal en una fecha específica
	 *
	 * @param string $listadoBloques_y_Direcciones
	 */
    public function listarPorConcejal($bloques = '')
    {
	?>
	    <script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxResultadoInformes" class="i_modal_contenedora i_modal_texto">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="informes/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

			    <input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
			    <input type="hidden" name="accion" id="accion" value="crearFormatoImpresionListadoPorConcejal" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="i_enviado" id="i_enviado" value="enviado" />

				<div id="dragger_listado_por_concejal" class="i_modal_titulo degradado">Listado por Concejal</div>
				<div style="height:10px;font-size:0;"></div>

				<div style="height:360px;">
					<div style="width:430px;height:360px;float:left;">

						<div class="informe_contenedor_rango_fechas">
						    <div class="informe_criterio_titulo">A la fecha :</div>
						    <div class="informe_criterio_fecha_calendario">
								<input type="text" class="informe_criterio_fecha" id="i_a_la_fecha" name="i_a_la_fecha" value="<?php echo($_SESSION['filtro_listado_por_concejal_interno']['i_a_la_fecha']) ? $this->formatearFecha($_SESSION['filtro_listado_por_concejal_interno']['i_a_la_fecha']) : date("d/m/Y") ; ?>" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" />
							    <input type="image" id="i_bt_a_la_fecha" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar hasta que fecha listar." align="top" width="16" height="16">
						    </div>
						</div>

						<div style="height:5px;font-size:0;clear:both;"></div>

						<div class="inf_fila">
						    <div class="inf_label_nombre">Bloque :</div>
						    <div class="inf_combo">
							    <select id="i_bloque" name="i_bloque">
									<option value="0">Seleccione un Bloque</option>
									<?php
									$cant_bloques = count($bloques);
									for ($b=0; $b < $cant_bloques; $b++)
									{
										$bloque = &$bloques[$b];
									?>
										<option value="<?php echo $bloque['ca_id']; ?>"><?php echo $bloque['ca_nombre']; ?></option>
									<?php
									}
									?>
							    </select>
							    &nbsp;
							    <a id="imagen_zoom_comisiones_Listados" href="javascript:modalGaby('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=pedirNombreBloqueModal');" title="Buscar por Nombre de Bloque"><img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" /></a>
						    </div>
						</div>

						<div style="height:5px;font-size:0;clear:both;"></div>

						<div class="informe_contenedor_concejales">
							<div class="informe_contenedor_concejales_nombre">Concejales :</div>
							<div class="informe_contenedor_concejales_listado">
								<div id="informes_listado_concejales" class="informes_listado_concejales checkbox_con_texto">

								</div>
							</div>
						</div>
						<div style="padding:0 0 3px 103px;text-align:left;clear:both;">
							Formato de P&aacute;gina:&nbsp;
							<input type="radio" name="i_opcion_formato_pagina" id="i_opcion_formato_A4" value="A4" />&nbsp;A4&nbsp;&nbsp;&nbsp;
							<input type="radio" name="i_opcion_formato_pagina" id="i_op_formato_Legal" value="Legal" checked />&nbsp;Legal (Oficio)
							<input type="hidden" name="filtro_i_opcion_formato_pagina" id="filtro_i_opcion_formato_pagina" value="<?php echo $_SESSION['filtro_listado_para_liquidaciones']['filtro_i_opcion_formato_pagina']; ?>" />
						</div>
					</div>
					<div style="width:35px;height:360px;float:right;"></div>
					<div style="width:100px;height:360px;float:right;">
						<div class="p_boton_edicion">
							<a id="i_btImpresion" href="javascript:generarListadoInterno();">
								<img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;Generar
							</a>
						</div>
						<div style="height:15px;font-size:0;"></div>
						<div class="p_boton_edicion">
							<a id="i_btCerrar">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="left" />Cerrar
							</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	    <script>
			//CALENDARIO
			var calDesde = new Zapatec.Calendar.setup({
				inputField:"i_a_la_fecha",
				ifFormat:"%d/%m/%Y",
				button:"i_bt_a_la_fecha",
				showsTime:false
			});

	        $('i_btCerrar').addEvent('click', function() {
				$("capaFondo").setStyle('visibility','hidden');
				$("capaVentana").setStyle('visibility','hidden');
			});

			function generarListadoInterno()
			{
				var mensaje = '';
				var error = false;

				if ($('i_bloque').value == 0)
				{
					error = true;
					mensaje += "Debe seleccionar un Bloque.\n";
				}

				if ( !verificarCheckbox('<?php echo $this->formulario; ?>') )
				{
					error = true;
					mensaje += "Debe seleccionar un Concejal.";
				}

				if (error)
				{
					alert(mensaje);
				}
				else
				{
					$('<?php echo $this->formulario; ?>').submit();
				}
			}

			function refrezcarListadoConcejales()
			{
				if ( $('i_bloque').value != '0' ) {
					refrescar('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=refrescarConcejalesParaUnaFecha&i_a_la_fecha='+$('i_a_la_fecha').value+'&i_bloque='+$('i_bloque').value, 'informes_listado_concejales');
			    } else {
					document.getElementById("informes_listado_concejales").innerHTML = "";
				}
			}

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoInformes'), {
			   handle: $('dragger_listado_por_concejal')
			});

			// SE ASIGNA EL BLOQUE
			$('i_bloque').value = <?php echo ($_SESSION['filtro_listado_por_concejal_interno']['i_bloque']) ? "'".$_SESSION['filtro_listado_por_concejal_interno']['i_bloque']."'" : 0 ; ?>;

			// AL CARGARSE EL BLOQUE SE REFREZCAN LOS CONCEJALES
			$('i_bloque').addEvent('domready', function()
		    {
				refrezcarListadoConcejales();
			});

			// AL CAMBIAR DE BLOQUE SE REFREZCAN LOS CONCEJALES
			$('i_bloque').addEvent('change', function()
		    {
				refrezcarListadoConcejales();
			});

			// AL CAMBIAR DE FECHA SE REFREZCAN LOS CONCEJALES
			$('i_a_la_fecha').addEvent('change', function()
		    {
				refrezcarListadoConcejales();
			});

			// SI SE ELIGE EL FORMATO DE PAGINA A4
			$('i_opcion_formato_A4').addEvent('click', function(){
				$('filtro_i_opcion_formato_pagina').value = 1;
				$('i_opcion_formato_A4').checked = true;
			});

			// SI SE ELIGE EL FORMATO DE PAGINA LEGAL
			$('i_opcion_formato_Legal').addEvent('click', function(){
				$('filtro_i_opcion_formato_pagina').value = 2;
				$('i_opcion_formato_Legal').checked = true;
			});

			// SI SE LIQUIDA UN AGUINALDO SE MARCA SU OPCION
			if ( $('filtro_i_opcion_formato_pagina').value == 'A4' )
			{
				$('i_opcion_formato_A4').checked = true;
			}
			else
			{
				// SI SE LIQUIDA UN ADICIONAL SE MARCA SU OPCION
				if ( $('filtro_i_opcion_formato_pagina').value == 'Legal' )
				{
					$('i_opcion_formato_Legal').checked = true;
				}
			}
		</script>
    <?php
    }

    /**
     * Se genera el listado del Personal dependiente de los Concejales, en una fecha específica
     *
     * @param string $filtro
     */
	public function crearFormatoImpresionListadoPorConcejal($filtro = '')
	{
		ob_start();
	?>
		<style type="text/css">
			h4, h5 {
				font-family: Arial;
			}
			table {
				border-collapse: collapse;
			}
			.imp_texto {
				font-family: Arial;
				font-size: 13px;
				font-weight: 500;
				color: #000;
				text-align: left;
			}
			.imp_titulos td {
				padding: 5px 10px 5px 10px;
				background-color: silver;
				text-align: center;
			}
			.imp_datos_concejal td {
				padding: 5px 5px 5px 10px;
				font-weight: 700;
			}
			.imp_datos_dependientes td {
				padding: 5px 5px 5px 10px;
				border-collapse: collapse;
			}
			.imp_alineacion_centrado {
				text-align: center;
			}
			.imp_alineacion_derecha {
				text-align: right;
			}
			.imp_total_modulos {
				padding: 5px 5px 5px 10px;
			}
		</style>
		<?php
		$posicion = 0;
		$secretaria_bloque = null;

		// PARA CADA CONCEJAL SELECCIONADO
		$cantidad_concejales = count($filtro['i_concejales']);
		for ($c=0; $c < $cantidad_concejales; $c++)
		{
			$filtro['i_concejal'] = &$filtro['i_concejales'][$c];

			$informacion_concejal = $this->modelo->obtenerInformacionLegajo($filtro['i_concejal']);

			// 24/04/2013
			$digito_actual_concejal = $this->modeloPersonal->obtenerDigitoActual($filtro['i_concejal']);

			$this->modelo->setFiltro($filtro);

			// SE OBTIENEN LOS DEPENDIENTES DEL CONCEJAL A UNA FECHA DETERMINADA
			$listado = $this->modelo->listarPorConcejal();
		?>
			<page backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm" style="font-size: 12px">
				<h5>A la fecha: <?php echo $this->formatearFecha($filtro['i_a_la_fecha']); ?></h5>
				<h4><?php echo $filtro['nombre_bloque']; ?></h4>

				<table width="100%" class="imp_texto" border=1 >
					<tr class="imp_titulos">
						<td>
							APELLIDO Y NOMBRE
						</td>
						<td>
							N&deg; LEGAJO
						</td>
						<td colspan="2">
							<?=$informacion_concejal['cc_nombre'];?>
						</td>
					</tr>
					<tr class="imp_datos_concejal">
						<td>
							<?php echo $informacion_concejal['p_apellido'].', '.$informacion_concejal['p_nombre']; ?>
						</td>
						<td class="imp_alineacion_derecha">
							<?php echo number_format($informacion_concejal['p_legajo'], 0, '', '.').'/'.$digito_actual_concejal; ?>
						</td>
						<td colspan="2">
							<?php echo 'D.N.I. '.number_format($informacion_concejal['p_nro_documento'], 0, '', '.'); ?>
						</td>
					</tr>
					<tr class="imp_titulos">
						<td>APELLIDO Y NOMBRE</td>
						<td>N&deg; LEGAJO</td>
						<td>CARGO</td>
						<td>M&Oacute;DULO</td>
					</tr>
					<?php
					$total_modulos = 0;

					$cantidad_listado = (isset($listado)) ? count($listado) : 0;
					for ($l=0; $l < $cantidad_listado; $l++)
					{
						$dependiente = &$listado[$l];

						// SI NO PERTENECE A SECRETARIA SE MUESTRA
						if ( $dependiente['c_pertenece_secretaria_bloque'] == 0 )
						{
					?>
							<tr class="imp_datos_dependientes">
								<td><?php echo $dependiente['p_apellido'].", ".$dependiente['p_nombre']; ?></td>
								<td class="imp_alineacion_derecha"><?php echo number_format($dependiente['p_legajo'], 0, '', '.')."/".$dependiente['c_digito']; ?></td>
								<td><?php echo $dependiente['cc_nombre']; ?></td>
								<td class="imp_alineacion_derecha"><?php echo $dependiente['cc_modulo']; ?>&nbsp;&nbsp;</td>
							</tr>
					<?php
							$total_modulos = $total_modulos + $dependiente['cc_modulo'];
						}
						else // SINO SE GUARDA EN UN VECTOR PARA MOSTRARLO COMO MIEMBRO DE LA SECRETARIA
						{
							$secretaria_bloque[$posicion]['p_apellido'] = $dependiente['p_apellido'];
							$secretaria_bloque[$posicion]['p_nombre'] = $dependiente['p_nombre'];
							$secretaria_bloque[$posicion]['p_legajo'] = $dependiente['p_legajo'];
							$secretaria_bloque[$posicion]['c_digito'] = $dependiente['c_digito'];
							$secretaria_bloque[$posicion]['cc_nombre'] = $dependiente['cc_nombre'];
							$secretaria_bloque[$posicion]['cc_modulo'] = $dependiente['cc_modulo'];

							$posicion++;
						}
					}
					?>
					<tr class="imp_titulos">
						<td colspan="3" class="imp_alineacion_derecha">TOTAL&nbsp;:&nbsp;</td>
						<td class="imp_alineacion_derecha imp_total_modulos"><?php printf("%01.1f",$total_modulos); ; ?>&nbsp;&nbsp;</td>
					</tr>
				</table>
			</page>
		<?php
		}

		// 	20/03/2013
		// SI POSEE SECRETARIA, SE MUESTRAN SUS MIEMBROS EN OTRA PAGINA
		if ( $secretaria_bloque[0]['p_apellido'] )
		{
			$total_modulos_secretaria = 0;
		?>
			<page pageset="old">
				<h4><?php echo 'Secretar&iacute;a de '.$filtro['nombre_bloque']; ?></h4>

				<table width="100%" class="imp_texto" border=1 >
					<tr class="imp_titulos">
						<td>APELLIDO Y NOMBRE</td>
						<td>N&deg; LEGAJO</td>
						<td>CARGO</td>
						<td>M&Oacute;DULO</td>
					</tr>

					<?php
					for ($s=0; $s < $posicion; $s++)
					{
					?>
						<tr class="imp_datos_dependientes">
							<td><?php echo $secretaria_bloque[$s]['p_apellido'].", ".$secretaria_bloque[$s]['p_nombre']; ?></td>
							<td class="imp_alineacion_derecha"><?php echo number_format($secretaria_bloque[$s]['p_legajo'], 0, '', '.')."/".$secretaria_bloque[$s]['c_digito']; ?></td>
							<td><?php echo $secretaria_bloque[$s]['cc_nombre']; ?></td>
							<td class="imp_alineacion_derecha"><?php echo $secretaria_bloque[$s]['cc_modulo']; ?>&nbsp;&nbsp;</td>
						</tr>
					<?php
						$total_modulos_secretaria = $total_modulos_secretaria + $secretaria_bloque[$s]['cc_modulo'];
					}
					?>

					<tr class="imp_titulos">
						<td colspan="3" class="imp_alineacion_derecha">TOTAL&nbsp;:&nbsp;</td>
						<td class="imp_alineacion_derecha imp_total_modulos"><?php printf("%01.1f",$total_modulos_secretaria); ; ?>&nbsp;&nbsp;</td>
					</tr>
				</table>
			</page>
		<?php
		}

		$content = ob_get_clean();
		try
		{
			// conversion HTML => PDF
			//los métodos utilizados aquí están definidos en el archivo html2pdf.class.php
			$html2pdf = new HTML2PDF('L', $filtro['i_opcion_formato_pagina'],'es', array(mL, mT, mR, mB));
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');

			//Tratamiento del código HTML
			$html2pdf->WriteHTML($content);

			//Destino donde enviar el documento
			$html2pdf->Output('listado_por_concejal.pdf', 'D');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

	/**
	 * Se muestra el listado de Concejales o Personal Administrativo de un Área, Mes y Año determinados
	 *
	 * @param array $listado
	 * @param string $id_area
	 * @param string $i_mes
	 * @param string $i_anio
	 */
	public function mostrarPersonalParaLiquidaciones($listado, $id_area, $i_mes = '', $i_anio = '')
    {
    	$cant_listado = ($listado) ? count($listado) : 0;
	?>
		<div id="informes_listado_concejales" class="informes_listado_concejales checkbox_con_texto">
			<?php
			if ( $cant_listado > 0 )
			{
			?>
				<label><input type="checkbox" name="i_concejales_todos" id="i_concejales_todos" value="" checked onClick="javascript:marcar_desmarcar_checkbox('i_concejales_todos', '<?php echo $this->formulario; ?>');">&nbsp;Todos</label>
				<?php
				for ($i=0; $i < $cant_listado; $i++)
				{
					$registro = &$listado[$i];
				?>
					<label><input type="checkbox" name="i_concejales[]" id="i_concejal_<?php echo $registro['p_legajo']; ?>" value="<?php echo $registro['p_legajo']; ?>" checked >&nbsp;<?php echo $registro['p_apellido'].', '.$registro['p_nombre']; ?></label>
				<?php
				}
    		}
    		else
    		{
    			echo "<h4 style='text-align: center;'>No existe Personal en dicha Área.</h4>";
    		}
			?>
		</div>
		<script>
			// AL CARGARSE LOS CONCEJALES O AGENTES
			$('informes_listado_concejales_agentes').addEvent('domready', function()
		    {
				$('labelRetira').setHTML('Retira: ');

				// SE REFREZCAN SUS DEPENDIENTES
				refrescar('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=refrescarComboRetira&id_area=<?php echo $id_area; ?>&i_retira=<?php echo ($_SESSION['filtro_listado_para_liquidaciones']['i_retira']) ? $_SESSION['filtro_listado_para_liquidaciones']['i_retira'] : 0 ; ?>', 'informes_contenedora_comboRetira');
			});
		</script>
	<?php
    }

    /**
     * Se muestra el listado de Concejales de un Bloque determinado
     *
     * @param array $listadoConcejales
     */
	public function listadoConcejalesParaElegir($listadoConcejales)
    {
    	$cant_listado = ($listadoConcejales) ? count($listadoConcejales) : 0;
	?>
		<div id="informes_listado_concejales" class="informes_listado_concejales checkbox_con_texto">
			<?php
			if ( $cant_listado > 0 ) {
			?>
				<label><input type="checkbox" name="i_concejales_todos" id="i_concejales_todos" value="" checked onClick="javascript:marcar_desmarcar_checkbox('i_concejales_todos', '<?php echo $this->formulario; ?>');">&nbsp;Todos</label>
				<?php
				for ($c=0; $c < $cant_listado; $c++) {
					$concejal = &$listadoConcejales[$c];
					echo '<label><input type="checkbox" name="i_concejales[]" id="i_concejal_'.$concejal['p_legajo'].'" value="'.$concejal['p_legajo'].'" checked >&nbsp;'.$concejal['p_apellido'].', '.$concejal['p_nombre'].'</label>';
				}
			}
			else
				echo "<h4 style='text-align: center;'>No existen Concejales en dicho bloque.</h4>";
			?>
		</div>
	<?php
    }

    /**
     * Se visualiza una ventana modal con un criterio de búsqueda determinado para generar el Listado de Liquidaciones
     *
     * @param string $listadoBloques_y_Direcciones
     */
    public function listarParaLiquidaciones($listadoBloques_y_Direcciones = '')
    {
	?>
	    <script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxResultadoInformes" class="i_modal_contenedora i_modal_texto">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="<?php echo $this->controlador; ?>/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

			    <input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
			    <input type="hidden" name="accion" id="accion" value="crearFormatoImpresionListadoParaLiquidaciones" />
			    <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
			    <input type="hidden" name="i_enviado" id="i_enviado" value="enviado" />

			    <div id="dragger_liquidaciones" class="i_modal_titulo degradado">Liquidaciones</div>
			    <div style="height:400px;margin-top:10px;">
					<div id="l_contenedor_buscador" style="width:430px;height:400px;float:left;">

						<div class="inf_fila">
							<div class="inf_label_nombre">Mes :</div>
							<div class="inf_combo" style="width:100px;">
								<select id="i_mes" name="i_mes" style="width:100px;">
									<option value="0">---</option>
								  	<option value="1">Enero</option>
								  	<option value="2">Febrero</option>
								  	<option value="3">Marzo</option>
								  	<option value="4">Abril</option>
									<option value="5">Mayo</option>
								  	<option value="6">Junio</option>
								  	<option value="7">Julio</option>
								  	<option value="8">Agosto</option>
								  	<option value="9">Septiembre</option>
								  	<option value="10">Octubre</option>
								  	<option value="11">Noviembre</option>
								  	<option value="12">Diciembre</option>
								</select>
							</div>
							<div class="inf_label_nombre" style="width:52px;">A&ntilde;o :</div>
							<div class="inf_combo" style="width:100px;">
								<select id="i_anio" name="i_anio" style="width:100px;">
									<option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
									<option value="<?php echo date("Y")-1; ?>"><?php echo date("Y")-1; ?></option>
								</select>
							</div>
						</div>
						<div style="padding:3px 0 3px 103px;text-align:left;clear:both;">
							<input type="radio" name="i_opcion_haberes_aguinaldo_adicional" id="i_op_haberes" value="1" checked />&nbsp;Haberes&nbsp;&nbsp;&nbsp;
							<input type="radio" name="i_opcion_haberes_aguinaldo_adicional" id="i_op_aguinaldo" value="2" disabled />&nbsp;Aguinaldo (SAC)
							<input type="radio" name="i_opcion_haberes_aguinaldo_adicional" id="i_op_adicional" value="3" />&nbsp;Ajuste
							<input type="hidden" name="filtro_i_opcion_haberes_aguinaldo_adicional" id="filtro_i_opcion_haberes_aguinaldo_adicional" value="<?php echo $_SESSION['filtro_listado_para_liquidaciones']['i_opcion_haberes_aguinaldo_adicional']; ?>" />
						</div>
						<div class="inf_fila">
							<div class="inf_label_nombre">&Aacute;rea :</div>
							<div class="inf_combo">
								<select id="i_area" name="i_area">
									<option value="0">Seleccione un &Aacute;rea</option>
									<?php
									$cant_areas = count($listadoBloques_y_Direcciones);
									for ($i=0; $i < $cant_areas; $i++)
									{
										$area = &$listadoBloques_y_Direcciones[$i];
									?>
										<option value="<?php echo $area['ca_id']; ?>"><?php echo $area['ca_nombre']; ?></option>
									<?php
									}
									?>
								</select>
								&nbsp;
								<a id="imagen_zoom_comisiones_Listados" href="javascript:modalGaby('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=pedirNombreAreaParaLiquidacionesModal');" title="Buscar por Nombre de Area"><img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" /></a>
							</div>
						</div>
						<div style="height:5px;font-size:0;clear:both;"></div>
						<div class="informe_contenedor_concejales">
							<div id="labelIntegranteArea" class="informe_contenedor_concejales_nombre"></div>
							<div class="informe_contenedor_concejales_listado">
								<div id="informes_listado_concejales_agentes" class="informes_listado_concejales checkbox_con_texto">

								</div>
							</div>
						</div>
						<div style="height:5px;font-size:0;clear:both;"></div>
						<div class="inf_fila">
							<div id="labelRetira" class="inf_label_nombre"></div>
							<div id="informes_contenedora_comboRetira" class="inf_combo">

							</div>
						</div>
						<div style="padding:0 0 3px 103px;text-align:left;clear:both;">
							Formato de P&aacute;gina:&nbsp;
							<input type="radio" name="i_opcion_formato_pagina" id="i_opcion_formato_A4" value="A4" />&nbsp;A4&nbsp;&nbsp;&nbsp;
							<input type="radio" name="i_opcion_formato_pagina" id="i_op_formato_Legal" value="Legal" checked />&nbsp;Legal (Oficio)
							<input type="hidden" name="filtro_i_opcion_formato_pagina" id="filtro_i_opcion_formato_pagina" value="<?php echo $_SESSION['filtro_listado_para_liquidaciones']['filtro_i_opcion_formato_pagina']; ?>" />
						</div>
					</div>
					<div style="width:35px;height:400px;float:right;">
					</div>
					<div style="width:100px;height:400px;float:right;">
						<div id="i_contenedor_btImprimir" class="p_boton_edicion">
							<a id="i_btImprimir" href="javascript:generarListadoParaLiquidacion();">
								<img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;Generar
							</a>
						</div>
						<div id="i_contenedor_btCerrar" class="p_boton_edicion">
							<a id="i_btCerrar">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="left" />Cerrar
							</a>
						</div>
					</div>
			    </div>
			</form>
		</div>
	    <script>
			function setearOpcionesAguinaldo_Adicional()
			{
				// SI EL MES ES JUNIO O DICIEMBRE
				if ( $('i_mes').value == '6' || $('i_mes').value == '12' )
				{
					// SE HABILITA EL RADIOBUTTON DE AGUINALDO
					$('i_op_aguinaldo').disabled = false;
				}
				else
				{
					$('i_op_haberes').checked = true;
					// SE DESHABILITA EL RADIOBUTTON DE AGUINALDO
					$('i_op_aguinaldo').disabled = true;
				}
			}

			function refrezcarListados()
			{
				// SI HAY UN AREA SELECCIONADA
				if ( $('i_area').value != 0 )
				{
					// SI ES DE PLANTA POLITICA
					if( $('i_area').value.substring(0, 2) == '02' )
					{
						$('labelIntegranteArea').setHTML('Concejales: ');
					}
					else // SI ES DE PLANTA PERMANENTE
					{
						$('labelIntegranteArea').setHTML('Agentes: ');
					}

					// SE REFREZCAN LOS INTEGRANTES DEL AREA SELECCIONADA
					refrescar('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=refrescarIntegrantesAreaParaLiquidaciones&i_mes='+$('i_mes').value+'&i_anio='+$('i_anio').value+'&id_area='+$('i_area').value, 'informes_listado_concejales_agentes');
				}
				else
				{
					document.getElementById("informes_listado_concejales_agentes").innerHTML = "";
				}
			}

			function generarListadoParaLiquidacion()
			{
				var mensaje = '';
				var error = false;

				if ( $('i_mes').value == 0 )
				{
					error = true;
					mensaje += "Debe seleccionar un Mes.\n";
				}
				if ( $('i_anio').value == 0 )
				{
					error = true;
					mensaje += "Debe seleccionar un A"+'\u00f1'+"o.\n";
				}
				if ( $('i_area').value == 0 )
				{
					error = true;
					mensaje += "Debe seleccionar un "+'\u00c1'+"rea.\n";
				}

				if ( !verificarCheckbox('<?php echo $this->formulario; ?>') )
				{
					error = true;
					mensaje += "Debe seleccionar un Concejal o Agente.";
				}

				if (error)
				{
					alert(mensaje);
				}
				else
				{
					$('<?php echo $this->formulario; ?>').submit();
				}
			}

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoInformes'), {
			   handle: $('dragger_liquidaciones')
			});

			$('i_mes').value = <?php echo ($_SESSION['filtro_listado_para_liquidaciones']['i_mes']) ? "'".$_SESSION['filtro_listado_para_liquidaciones']['i_mes']."'" : date("n") ; ?>;

			$('i_anio').value = <?php echo ($_SESSION['filtro_listado_para_liquidaciones']['i_anio']) ? "'".$_SESSION['filtro_listado_para_liquidaciones']['i_anio']."'" : date("Y") ; ?>;

			$('i_mes').addEvent('domready', function()
			{
				setearOpcionesAguinaldo_Adicional();
			});

			$('i_mes').addEvent('change', function()
			{
				setearOpcionesAguinaldo_Adicional();

				refrezcarListados();
			});

			$('i_anio').addEvent('change', function()
			{
				refrezcarListados();
			});

			$('i_op_haberes').addEvent('click', function(){
				$('filtro_i_opcion_haberes_aguinaldo_adicional').value = 1;
				$('i_op_haberes').checked = true;
			});
			$('i_op_aguinaldo').addEvent('click', function(){
				$('filtro_i_opcion_haberes_aguinaldo_adicional').value = 2;
				$('i_op_aguinaldo').checked = true;
			});
			$('i_op_adicional').addEvent('click', function(){
				$('filtro_i_opcion_haberes_aguinaldo_adicional').value = 3;
				$('i_op_adicional').checked = true;
			});

			// SI SE LIQUIDA UN AGUINALDO SE MARCA SU OPCION
			if ( $('filtro_i_opcion_haberes_aguinaldo_adicional').value == '2' )
			{
				$('i_op_aguinaldo').checked = true;
			}
			else
			{
				// SI SE LIQUIDA UN ADICIONAL SE MARCA SU OPCION
				if ( $('filtro_i_opcion_haberes_aguinaldo_adicional').value == '3' )
				{
					$('i_op_adicional').checked = true;
				}
			}

			// SE ASIGNA EL AREA
			$('i_area').value = <?php echo ($_SESSION['filtro_listado_para_liquidaciones']['i_area']) ? "'".$_SESSION['filtro_listado_para_liquidaciones']['i_area']."'" : 0 ; ?>;

			// AL CARGARSE EL AREA SE REFREZCAN SUS INTEGRANTES
			$('i_area').addEvent('domready', function()
		    {
				refrezcarListados();
			});

			// AL CAMBIAR DE AREA SE REFREZCAN SUS INTEGRANTES
			$('i_area').addEvent('change', function()
		    {
				refrezcarListados();
			});

			// AL CARGARSE LOS CONCEJALES O AGENTES SE REFREZCAN SUS DEPENDIENTES
			$('informes_listado_concejales_agentes').addEvent('domready', function()
		    {
				if ( $('i_area').value != 0 )
				{
					refrescar('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=refrescarComboRetira&i_bloque='+$('i_area').value+'&i_retira=<?php echo ($_SESSION['filtro_listado_para_liquidaciones']['i_retira']) ? $_SESSION['filtro_listado_para_liquidaciones']['i_retira'] : 0 ; ?>', 'informes_contenedora_comboRetira');
				}
			});

			if( $('i_retira') )
			{
				// SE DEFINE EL EVENTO VACIO POR DEFECTO
				definirEventoPorDefecto('i_retira', 'change');
			}

			$('i_btCerrar').addEvent('click', function()
			{
				$("capaFondo").setStyle('visibility','hidden');
				$("capaVentana").setStyle('visibility','hidden');
			});

			// SI SE ELIGE EL FORMATO DE PAGINA A4
			$('i_opcion_formato_A4').addEvent('click', function(){
				$('filtro_i_opcion_formato_pagina').value = 1;
				$('i_opcion_formato_A4').checked = true;
			});

			// SI SE ELIGE EL FORMATO DE PAGINA LEGAL
			$('i_opcion_formato_Legal').addEvent('click', function(){
				$('filtro_i_opcion_formato_pagina').value = 2;
				$('i_opcion_formato_Legal').checked = true;
			});

			// SI SE LIQUIDA UN AGUINALDO SE MARCA SU OPCION
			if ( $('filtro_i_opcion_formato_pagina').value == 'A4' )
			{
				$('i_opcion_formato_A4').checked = true;
			}
			else
			{
				// SI SE LIQUIDA UN ADICIONAL SE MARCA SU OPCION
				if ( $('filtro_i_opcion_formato_pagina').value == 'Legal' )
				{
					$('i_opcion_formato_Legal').checked = true;
				}
			}

		</script>
    <?php
    }

    /**
     * Se genera el Listado para Liquidaciones, en formato PDF para un Mes, Año y Área determinados, es opcional la elección de uno o varios Concejales
     *
     * @param array $filtro
     * @param array $listado_activos_en_el_mes
     */
    public function crearFormatoImpresionListadoParaLiquidaciones($filtro = '', $listado_activos_en_el_mes = null)
    {
    	ob_start();

    	$nombre_archivo_liquidaciones = "liquidaciones_".$this->nombreMes($filtro['i_mes'])."_".$filtro['i_anio'].".pdf";
    ?>
    	<style type="text/css">
    		h4, h5 {
    			font-family: Arial;
    		}
    		table {
    			border-collapse: collapse;
    		}
    		.imp_texto {
    			font-family: Arial;
    			font-size: 12px;
    			font-weight: 500;
    			color: #000;
    			text-align: left;
    		}
    		.primeralinea {
    			text-indent: 100px;
    		}
    		.imp_datos_dependientes td {
    			padding: 5px 5px 5px 10px;
    			border-collapse: collapse;
    		}
    		.imp_alineado_derecha {
    			text-align: right;
    		}
    	</style>

    	<page backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm" style="font-size: 12px">
    		<p class="imp_texto">MAR DEL PLATA,&nbsp;&nbsp;&nbsp;<?php echo date("d/m/Y"); ?></p>

    		<?php
    		if ( $filtro['i_opcion_haberes_aguinaldo_adicional'] == 2 )
    			$concepto = "Aguinaldo ";
    		elseif ( $filtro['i_opcion_haberes_aguinaldo_adicional'] == 3 )
    			$concepto = "Ajuste ";
    		else
    			$concepto = "";

    		// Se toman los 2 primeros dígitos del código del Área
    		$tipo_area = substr($filtro['i_area'], 0, 2);

    		// Si el área es de Planta Política
    		if ( $tipo_area == '02' ) {
    		?>
    			<p class="imp_texto primeralinea">
    				En el d&iacute;a de la fecha, recib&iacute; las liquidaciones de <?php echo $concepto.''.$this->nombreMes($filtro['i_mes']); ?> de <?php echo $filtro['i_anio']; ?> del personal administrativo de este Bloque, que a continuaci&oacute;n se detallan:
    			</p>

    			<h5>CONCEJALES</h5>
    			<table width="100%" class="imp_texto" border=0 >
    				<?php
    				$cantidad_concejales = count($filtro['i_concejales']);

    				// Se muestran los Concejales
    				for ($c=0; $c < $cantidad_concejales; $c++) {
    					$legajo_concejal = &$filtro['i_concejales'][$c];

    					// Se obtienen datos del Concejal
    					$ficha_concejal = $this->modelo->obtenerInformacionLegajo($legajo_concejal);
    				?>
    					<tr class="imp_datos_dependientes">
    						<td><?php echo $ficha_concejal['p_apellido'].", ".$ficha_concejal['p_nombre']; ?></td>
    						<td>Leg. <?php echo number_format($ficha_concejal['p_legajo'], 0, '', '.').'/'.$ficha_concejal['c_digito']; ?></td>
    					</tr>
    				<?php
    				}
    				?>
    			</table>

    			<h5>PERSONAL ADMINISTRATIVO</h5>
    			<table width="100%" class="imp_texto" border=0 >
    				<?php
    				$cantidad_activos_en_el_mes = count($listado_activos_en_el_mes);

    				// Se muestran sólo los Asesores dependientes de los Concejales elegidos
    				for ($a = 0; $a < $cantidad_activos_en_el_mes; $a++) {
    					$registro_asesor_activo = &$listado_activos_en_el_mes[$a];

    					// SE MUESTRAN SOLAMENTE AQUELLOS ASESORES QUE DEPENDAN DE ALGUNO DE LOS CONCEJALES ELEGIDOS PREVIAMENTE
    					// Si el Asesor depende de alguno de los Concejales elegidos
    					if ( in_array($registro_asesor_activo['c_depende_de'], $filtro['i_concejales']) ) {
						?>
	    					<tr class="imp_datos_dependientes">
	    						<td><?php echo $registro_asesor_activo['p_apellido'].", ".$registro_asesor_activo['p_nombre']; ?></td>
	    						<td>Leg. <?php echo number_format($registro_asesor_activo['c_legajo'], 0, '', '.').'/'.$registro_asesor_activo['c_digito']; ?></td>
	    					</tr>
    					<?php
    					}
    				}
    				?>
    			</table>
    		<?php
    		}
    		else // o si es de Planta Permanente
    		{
    		?>
    			<p class="imp_texto primeralinea">
    				En el d&iacute;a de la fecha, recib&iacute; las liquidaciones de <?php echo $concepto.''.$this->nombreMes($filtro['i_mes']); ?> de <?php echo $filtro['i_anio']; ?> del personal administrativo de <?php echo $filtro['nombre_area']; ?>, que a continuaci&oacute;n se detallan:
    			</p>
    			<table width="100%" class="imp_texto" border=0 >
    				<?php
    				$listado = $this->modelo->obtenerPersonalAdministrativoParaLiquidaciones($filtro['i_area']);

    				// SE LISTAN LAS FICHAS DEL PERSONAL ADMINISTRATIVO DEL BLOQUE
    				$cantidad_listado = (isset($listado)) ? count($listado) : 0;
    				for ($i=0; $i < $cantidad_listado; $i++) {
    					$integrante = &$listado[$i];
    				?>
    					<tr class="imp_datos_dependientes">
    						<td><?php echo $integrante['p_apellido'].", ".$integrante['p_nombre']; ?></td>
    						<td>Leg. <?php echo number_format($integrante['p_legajo'], 0, '', '.').'/'.$integrante['digito']; ?></td>
    					</tr>
    				<?php
    				}
    				?>
    			</table>
    		<?php
    		}
    		?>
    	<nobreak>

    	<h4><?php echo $filtro['nombre_area']; ?></h4>

    	<p class="imp_texto imp_alineado_derecha">
    		.....................................................
    		<br>
    		<h5>(FIRMA Y ACLARACI&Oacute;N)&nbsp;&nbsp;&nbsp;</h5>
    	</p>

    	<h5>RETIRA: <?php echo $filtro['datos_retira']['p_nombre'].' '.$filtro['datos_retira']['p_apellido']; ?></h5>
    	</nobreak>
    </page>
    <?php
    	$content = ob_get_clean();
    	try
    	{
    		// conversion HTML => PDF
    		//los métodos utilizados aquí están definidos en el archivo html2pdf.class.php
    		$html2pdf = new HTML2PDF('P', $filtro['i_opcion_formato_pagina'],'es', array(mL, mT, mR, mB));
    		$html2pdf->pdf->SetDisplayMode('fullpage');
    		$html2pdf->setDefaultFont('Arial');

    		//Tratamiento del código HTML
    		$html2pdf->WriteHTML($content);

    		//Destino donde enviar el documento
    		$html2pdf->Output($nombre_archivo_liquidaciones, 'D');
    	}
    	catch(HTML2PDF_exception $e) {
    		echo $e;
    		exit;
    	}
    }

	public function listadoDependientesParaReasignacion($listadoDependientes)
    {
		if ( $listadoDependientes ) {
    ?>
			<label><input type="checkbox" name="i_dependientes_todos" id="i_dependientes_todos" value="" checked onClick="javascript:marcar_desmarcar_dependientes();">&nbsp;Todos</label>
			<?php
			$cant_dependientes = count($listadoDependientes);
			for ($d=0; $d < $cant_dependientes; $d++) {
				$dependiente = &$listadoDependientes[$d];
			?>
				<label><input type="checkbox" name="i_dependientes[]" id="i_dependiente_<?php echo $dependiente['p_legajo']; ?>" value="<?php echo $dependiente['p_legajo']; ?>" checked >&nbsp;<?php echo $dependiente['p_apellido'].', '.$dependiente['p_nombre']; ?></label>
			<?php
			}
		}
		else
			echo '<br>&nbsp;SIN DEPENDIENTES';
	}

	public function listadoDependientes($listadoDependientes)
    {
		if ( $listadoDependientes ) {
    		$cant_dependientes = count($listadoDependientes);
			for ($d=0; $d < $cant_dependientes; $d++) {
				$dependiente = &$listadoDependientes[$d];
			?>
				<div class="t_reasignacion_detalle_dependientes">
					&nbsp;<?php echo $dependiente['p_apellido'].', '.$dependiente['p_nombre']; ?>
				</div>
			<?php
			}
		}
		else
			echo '<br>&nbsp;SIN DEPENDIENTES';
    }

    public function reasignarDependientes($listadoConcejales = '', $mensaje = '', $tipo_mensaje = '')
    {
		$accion_formulario = 'guardarReasignacionDependientes';

		// Mensaje del resultado de la operación realizada
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);
	?>
	    <script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxResultadoInformes" class="t_reasignacion_modal_contenedora i_modal_texto">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="<?php echo $this->controlador; ?>/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

			    <input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
			    <input type="hidden" name="accion" id="accion" value="<?php echo $accion_formulario; ?>" />
			    <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
			    <input type="hidden" name="i_enviado" id="i_enviado" value="enviado" />

			    <div id="dragger_reasignacion_dependientes" class="i_modal_titulo degradado">Reasignaci&oacute;n de Personal</div>
			    <div style="height:5px;font-size:0;"></div>
		  	    <div class="t_reasignacion_general">
					<div class="t_reasignacion_lado_combos">
						<div class="t_reasignacion_lado1_concejales">
							<div class="t_reasignacion_titulo">Concejal or&iacute;gen:</div>
							<div id="t_reasignacion_combo_concejal_origen" class="t_reasignacion_combo">
								<select id="i_concejal_origen" name="i_concejal_origen">
									<option value="0">Seleccione un Concejal origen</option>
									<?php
									$cant_concejales = count($listadoConcejales);
									for ($c=0; $c < $cant_concejales; $c++)
									{
										$concejal = &$listadoConcejales[$c];
									?>
										<option value="<?php echo $concejal['p_legajo']; ?>"><?php echo $concejal['p_apellido'].', '.$concejal['p_nombre']; ?></option>
									<?php
									}
									?>
							    </select>
							    &nbsp;
							    <a href="javascript:modalGaby('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=pedirNombreConcejalModal&id_combo=i_concejal_origen');" title="Buscar por Nombre de Concejal">
									<img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" />
								</a>
							</div>
						</div>
						<div style="height:5px;font-size:0;clear:both;"></div>
						<div class="t_reasignacion_contenedor_dependientes_listado">
							<div class="t_reasignacion_titulo">Seleccione los Dependientes a reasignar:</div>
							<div id="t_reasignacion_listado_dependientes_origen" class="t_reasignacion_listado_dependientes checkbox_con_texto">

							</div>
						</div>
					</div>
					<div class="t_reasignacion_lado_botones">
						<div style="height:70px;font-size:0;"></div>
						<div class="t_reasignacion_titulo">En la Fecha:</div>
						<div class="t_reasignacion_combo">
							<input type="text" id="i_fecha_reasignacion" name="i_fecha_reasignacion" value="<?php echo($_SESSION['filtro_reasignacion_dependientes']['i_fecha_reasignacion']) ? $this->formatearFecha($_SESSION['filtro_reasignacion_dependientes']['i_fecha_reasignacion']) : date("d/m/Y") ; ?>" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="i_btfecha_reasignacion" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha." align="top" width="16" height="16">
						</div>
						<div style="height:20px;font-size:0;clear:both;"></div>
						<div class="p_boton_edicion p_boton_nuevo">
							<a id="i_btReasignar">
								<img src="imagenes/barra/sgte.gif" width="16" height="16" align="left" />&nbsp;Reasignar
							</a>
						</div>
						<div style="height:15px;font-size:0;"></div>
						<div class="p_boton_edicion p_boton_nuevo">
							<a id="i_btCerrar">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="left" />Cerrar
							</a>
						</div>
					</div>
					<div class="t_reasignacion_lado_combos">
						<div class="t_reasignacion_lado1_concejales">
							<div class="t_reasignacion_titulo">Concejal destino:</div>
							<div class="t_reasignacion_combo">
							    <select id="i_concejal_destino" name="i_concejal_destino">
									<option value="0">Seleccione un Concejal destino</option>
									<?php
									$cant_concejales = count($listadoConcejales);
									for ($c=0; $c < $cant_concejales; $c++)
									{
									    $concejal = &$listadoConcejales[$c];
									?>
										<option value="<?php echo $concejal['p_legajo']; ?>"><?php echo $concejal['p_apellido'].', '.$concejal['p_nombre']; ?></option>
									<?php
									}
									?>
							    </select>
							    &nbsp;
							    <a href="javascript:modalGaby('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=pedirNombreConcejalModal&id_combo=i_concejal_destino');" title="Buscar por Nombre de Concejal">
									<img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" />
								</a>
							</div>
						</div>
						<div style="height:5px;font-size:0;clear:both;"></div>
						<div class="t_reasignacion_contenedor_dependientes_listado">
							<div class="t_reasignacion_titulo">Dependientes actuales:</div>
							<div id="t_reasignacion_listado_dependientes_destino" class="t_reasignacion_listado_dependientes checkbox_con_texto">

							</div>
						</div>
					</div>
				</div>
				<div style="height:5px;font-size:0;"></div>
			</form>
		</div>
		<script>
			//CALENDARIO PARA LA FECHA DE REASIGNACION
			var calReasignacion = new Zapatec.Calendar.setup({

				inputField:"i_fecha_reasignacion",
				ifFormat:"%d/%m/%Y",
				button:"i_btfecha_reasignacion",
				showsTime:false
			});

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoInformes'), {
			   handle: $('dragger_reasignacion_dependientes')
			});

			function marcar_desmarcar_dependientes()
			{
				if ( $('i_dependientes_todos').checked )
				{
					marcarTodosCheckbox('<?php echo $this->formulario; ?>');
				}
				else
				{
					desmarcarTodosCheckbox('<?php echo $this->formulario; ?>');
				}
			}

			// SE ASIGNA EL CONCEJAL ORIGEN
			$('i_concejal_origen').value = <?php echo ($_SESSION['filtro_reasignacion_dependientes']['i_concejal_origen']) ? "'".$_SESSION['filtro_reasignacion_dependientes']['i_concejal_origen']."'" : 0 ; ?>;

			// SE ASIGNA EL CONCEJAL DESTINO
			$('i_concejal_destino').value = <?php echo ($_SESSION['filtro_reasignacion_dependientes']['i_concejal_destino']) ? "'".$_SESSION['filtro_reasignacion_dependientes']['i_concejal_destino']."'" : 0 ; ?>;

			// AL CARGARSE EL CONCEJAL SE REFREZCAN SUS DEPENDIENTES
			$('i_concejal_origen').addEvent('domready', function()
		    {
				refrescar('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=refrescarDependientesParaReasignacion&i_concejal='+$('i_concejal_origen').value+'&i_listado=origen', 't_reasignacion_listado_dependientes_origen');
			});

			// AL CAMBIAR DE CONCEJAL SE REFREZCAN SUS DEPENDIENTES
			$('i_concejal_origen').addEvent('change', function()
		    {
				refrescar('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=refrescarDependientesParaReasignacion&i_concejal='+$('i_concejal_origen').value+'&i_listado=origen', 't_reasignacion_listado_dependientes_origen');
			});

			// AL CARGARSE EL CONCEJAL SE REFREZCAN SUS DEPENDIENTES
			$('i_concejal_destino').addEvent('domready', function()
		    {
				refrescar('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=refrescarDependientesParaReasignacion&i_concejal='+$('i_concejal_destino').value+'&i_listado=destino', 't_reasignacion_listado_dependientes_destino');
			});

			// AL CAMBIAR DE CONCEJAL SE REFREZCAN SUS DEPENDIENTES
			$('i_concejal_destino').addEvent('change', function()
		    {
				refrescar('informes/index.php?controlador=<?php echo $this->controlador; ?>&accion=refrescarDependientesParaReasignacion&i_concejal='+$('i_concejal_destino').value+'&i_listado=destino', 't_reasignacion_listado_dependientes_destino');
			});

			// SE DEFINE EL EVENTO VACIO POR DEFECTO
			definirEventoPorDefecto('t_reasignacion_listado_dependientes_origen', 'change');

			$('i_btReasignar').addEvent('click', function()
			{
				var mensaje = '';
				var error = false;

				if ( $('i_concejal_origen').value == 0 )
				{
					error = true;
					mensaje += "Debe seleccionar un Concejal origen.<br>";
				}

				if ( !verificarCheckbox('<?php echo $this->formulario; ?>') )
				{
					error = true;
					mensaje += "Debe seleccionar un Dependiente.<br>";
				}

				if ( $('i_concejal_destino').value == 0 )
				{
					error = true;
					mensaje += "Debe seleccionar un Concejal destino.<br>";
				}

				if ( $('i_concejal_origen').value == $('i_concejal_destino').value )
				{
					error = true;
					mensaje += "Debe seleccionar Concejales diferentes para origen y destino.<br>";
				}

				if (error)
				{
					alert(mensaje);
				}
				else
				{
					enviarForm('<?php echo $this->formulario; ?>', '<?php echo $this->controlador; ?>', 'contenidoAjaxResultadoInformes');
				}
			});

			$('i_btCerrar').addEvent('click', function()
			{
				$("capaFondo").setStyle('visibility','hidden');
				$("capaVentana").setStyle('visibility','hidden');
			});

			$('i_fecha_reasignacion').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('i_btfecha_reasignacion').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('i_btfecha_reasignacion').disabled = true;
					}
				}
			});

		</script>
	<?php
	}

    /**
     * 17/12/2020 XXXX
     * Se listan sólo los Activos a una fecha determinada, con su Cargo respectivo
     * @param  string $listado [description]
     * @param  string $filtro  [description]
     */
    public function listarParaSitioMGP($listado = '', $filtro = '')
    {
    	$cantidad 		  = (isset($listado)) ? count($listado) : 0;
    	$accion           = "listarParaSitioMGP";
		$titulo           = "Para sitio MGP";
		$filtro_en_sesion = "filtro_informes_para_sitio_mgp";
		?>
	    <script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxResultadoInformes" class="i_modal_contenedora i_modal_texto">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="<?php echo $this->controlador; ?>/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

			    <input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
			    <input type="hidden" name="accion" id="accion" value="<?php echo $accion; ?>" />
			    <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
			    <input type="hidden" name="i_enviado" id="i_enviado" value="enviado" />

			    <div id="dragger_para_sitio_mgp" class="i_modal_titulo degradado"><?php echo $titulo; ?></div>
			    <div style="height:10px;font-size:0;"></div>

			    <div style="height:107px;">
					<div style="width:430px;height:107px;float:left;">
					    <div style="height:22px;">
							<div class="inf_fila">
								<div class="inf_label_nombre">A la fecha:</div>
								<div class="inf_combo" style="text-align:left;">
									<input type="text" id="i_a_la_fecha" name="i_a_la_fecha" value="<?php echo($_SESSION[$filtro_en_sesion]['i_a_la_fecha']) ? $this->formatearFecha($_SESSION[$filtro_en_sesion]['i_a_la_fecha']) : date("d/m/Y") ; ?>" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
									<input type="image" id="i_btfecha_personal" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha." align="top" width="16" height="16">
								</div>
							</div>
						</div>
					</div>
					<div style="width:35px;height:107px;float:right;"></div>
					<div style="width:100px;height:107px;float:right;">

						<?php
						if ( $filtro['i_enviado'] != '' && $listado != '' ) {
						?>
							<div class="p_boton_edicion">
								<a id="i_btImpresion" target="_blank" >
									<img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;Imprimir
								</a>
							</div>
						<?php
						} else {
						?>
							<div class="p_boton_edicion">
								<a style="cursor:pointer;color:silver" title="Sin resultados para imprimir">
									<img src="imagenes/barra/print_gris_16x16.gif" width="16" height="16" align="left" />&nbsp;Imprimir
								</a>
							</div>
						<?php
						}
						?>
						<div style="height:15px;font-size:0;"></div>
						<div class="p_boton_edicion">
							<a id="i_btBuscar" >
								<img src="imagenes/barra/zoom_16x16.gif" width="16" height="16" align="left" />Buscar
							</a>
						</div>
						<div style="height:15px;font-size:0;"></div>
						<div class="p_boton_edicion">
							<a id="i_btCerrar">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="left" />Cerrar
							</a>
						</div>
					</div>
			    </div>

				<div class="i_borde1">
					<div id="i_borde2" class="i_borde2">
						<?php
						if ( $filtro['i_enviado'] == '' ) {
						?>
							  <!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
							  <div class="i_fondo_item"><?php echo $titulo; ?></div>
						<?php
						} else {
						    if ($cantidad < 0)
							  echo '<br><h1>Sin resultados</h1>';
						    else {
							    // SE LISTAN LAS FICHAS DE LOS EMPLEADOS DE BLOQUES
							    for ($e=0; $e < $cantidad; $e++) {

									$ficha = &$listado[$e];

									$nombre_area = ($filtro['i_a_la_fecha'] != '') ? $this->modelo->obtenerNombreAreaSegunFecha($ficha['p_legajo'], $filtro['i_a_la_fecha']) : '';

									$cargo       = ($filtro['i_a_la_fecha'] != '') ? $this->modelo->obtenerNombreCargo($ficha['p_legajo'], $filtro['i_a_la_fecha']) : '';

									$depende_de  = ($filtro['i_a_la_fecha'] != '') ? $this->modelo->obtenerNombreDependeDe($ficha['p_legajo'], $filtro['i_a_la_fecha']) : '';
							?>
									<div class="i_ficha" onmouseover="javascript:$(this).setStyle('background-color','#E7E7E7');" onmouseout="javascript:$(this).setStyle('background-color','#DEDEDE');">
										<div class="i_ficha_datos">
										    <div class="i_ficha_datos_valor"><strong>Legajo:</strong>&nbsp;<?php echo number_format($ficha['p_legajo'], 0, '', '.').'/'.$cargo['digito']; ?></div>
										    <div class="i_ficha_datos_valor"><strong><?php echo $ficha['p_apellido'].', '.$ficha['p_nombre']; ?></strong></div>
										    <div class="i_ficha_datos_valor"><strong>&Aacute;rea:</strong>&nbsp;<?php echo ($nombre_area != '') ? $nombre_area : ''; ?></div>
										    <div class="i_ficha_datos_valor"><strong>Cargo:</strong>&nbsp;<?php echo ($cargo['cargo'] != '') ? $cargo['cargo'] : ''; ?></div>
										    <?php
										    // Si depende de alguien
										    if ( $depende_de != '' )
										    	// Se muestra de quien depende
												echo '<div class="i_ficha_datos_valor"><strong>Depende de:</strong>&nbsp;'.$depende_de['p_apellido'].', '.$depende_de['p_nombre'].'</div>';
											?>
										</div>
										<div class="i_ficha_foto_mini">
											<a>
												<span class="centrar_imagen"><img src="<?php echo $this->directorio_fotos; ?>resize.php?ancho=107&alto=107&imagen=<?php echo ($ficha['p_foto']) ? $ficha['p_foto'] : 'avatar.jpg'; ?>" ></span>
											</a>
										</div>
									</div>
									<div style="height:7px;font-size:0;clear:both;"></div>
						<?php
							    } // FIN DEL for
						?>
							    <script>
							  		var scroller = new Fx.Scroll($('i_borde2'));
									scroller.toTop();
							    </script>
						<?php
							} //FIN DEL SEGUNDO else
						} //FIN DEL PRIMER else
						?>
					</div><!-- FIN DE i_borde2 -->
				</div><!-- FIN DE i_borde1 -->
	        </form>
		</div>
		<script>
	        // Calendario para la fecha
	        var calDesde = new Zapatec.Calendar.setup({
			    inputField:"i_a_la_fecha",
			    ifFormat:"%d/%m/%Y",
			    button:"i_btfecha_personal",
			    showsTime:false
	        });

			// Para obtener el listado de personal
	        $('i_btBuscar').addEvent('click', function() {
				// Si no se eligió una fecha
				if ( $('i_a_la_fecha').value == '' )
					alert("Debe ingresar una fecha.");
				else
					enviarForm('formInformes', 'informes', 'contenidoAjaxResultadoInformes');
	        });

			// Para cerrar la modal
			$('i_btCerrar').addEvent('click', function() {
				$("capaFondo").setStyle('visibility','hidden');
				$("capaVentana").setStyle('visibility','hidden');
			});

			// Al elegir el botón de Imprimir
			$('i_btImpresion').addEvent('click', function() {

				$('i_btImpresion').setProperty('href', 'informes/index.php?controlador=informes&accion=crearFormatoImpresionParaSitioMGP&i_a_la_fecha='+$('i_a_la_fecha').value);
			});

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoInformes'), {
			   handle: $('dragger_para_sitio_mgp')
			});
		</script>
    	<?php
    }

    /**
     * 17/12/2020 XXXX
     * Se crea el formato de impresión, de sólo los Activos a una fecha determinada, con su Cargo respectivo
     * @param  string $listado [description]
     */
	public function crearFormatoImpresionParaSitioMGP($listado = '')
	{
		ob_start();

		$cantidad = (isset($listado)) ? count($listado) : 0;
	?>
		<style type="text/css">
			h4, h5 {
				font-family: Arial;
			}
			table {
				padding: 0;
				border-collapse: collapse;
			}
			.e_tabla_titulos {
				padding:2px 0 2px 0;
				text-align:center;
			}
			.e_tabla_titulos th {
				font-family: Arial;
				font-size: 11px;
				font-weight: 500;
				color: #fff;
				background-color: #2da4c6;
				border: 1px solid #BEBEBE;
				padding: 3px;
			}
			.e_cuerpo_scrolleable {
				overflow: auto;
				padding-right: 15px;
				background-color: #fff;
			}
			.e_cuerpo_scrolleable tr {
				height: 16px;
				font-family:Arial;
				font-size:10px;
			}
			.e_cuerpo_scrolleable td {
				padding-left: 10px;
				border: 1px solid #BEBEBE;
			}
		</style>
		<page backtop="31mm" backbottom="7mm" backleft="25mm" backright="1mm" style="font-size: 10px">
			<page_header>
				<table style="width: 100%;">
					<tr>
						<td style="width: 10%;" rowspan="5">
							<img src="../imagenes/logo.png" width="200" align="center" >
						</td>
						<td style="width:90%;">&nbsp;</td>
					</tr>
					<tr>
						<td style="width:90%;text-align:left;padding-left: 20px;font-size:12px;">Municipalidad de General Pueyrredon</td>
					</tr>
					<tr>
						<td style="width:90%;text-align:left;padding-left: 20px;font-size:12px;">Honorable Concejo Deliberante</td>
					</tr>
					<tr>
						<td style="width:90%;text-align:left;padding-left: 20px;font-size:12px;">Planta de Personal del Honorable Concejo Deliberante</td>
					</tr>
					<tr>
						<td style="width:90%;text-align:left;padding-left: 20px;font-size:12px;">Actualizada al <?=date("d/m/Y");?></td>
					</tr>
				</table>
			</page_header>
			<page_footer>
				<table style="width:100%;border:solid 1px black;">
					<tr>
						<td style="text-align: left;width: 50%">Fecha: <?php echo date("d/m/Y"); ?></td>
						<td style="text-align: right;width: 50%">P&aacute;gina [[page_cu]] de [[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<table width="100%">
				<thead class="e_tabla_titulos">
					<tr>
						<th>Legajo</th>
						<th>D&iacute;gito</th>
						<th>Apellido y Nombre</th>
						<th>Cargo</th>
					</tr>
				</thead>
				<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
					<?php
					for ($p=0; $p < $cantidad; $p++) {
						$dato = &$listado[$p];

						$cargo = $this->modeloPersonal->obtenerNombreUltimoCargo($dato['p_legajo']);
					?>
						<tr class="e_tabla_titulos">
							<td style="width:30px;padding-right:3px;text-align:right;">
								<?php echo number_format($dato['p_legajo'], 0, '', '.'); ?>
							</td>
							<td style="width:30px;padding-right:3px;text-align:right;">
								<?php echo ($cargo['c_digito']) ? $cargo['c_digito'] : '&nbsp;'; ?>
							</td>
							<td style="text-align:left;padding:0 3px 0 3px;">
								<?php echo $dato['p_apellido'].', '.$dato['p_nombre']; ?>
							</td>
							<td style="width:150px;text-align:left;padding:0 3px 0 3px;">
								<?php echo ($cargo['cargo']) ? $cargo['cargo'] : '&nbsp;' ?>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</page>
		<?php
		$content = ob_get_clean();
		try
		{
			// conversion HTML => PDF
			//los métodos utilizados aquí están definidos en el archivo html2pdf.class.php
			$html2pdf = new HTML2PDF('P','LEGAL','es');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');
			//Tratamiento del código HTML
			$html2pdf->WriteHTML($content);
			//Destino donde enviar el documento
			$html2pdf->Output('listado_personal_hcd.pdf');//, 'D'
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

}
?>
