<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaEstados extends VistaBase
{
	private $controlador;
	private $formulario;

	public function __construct()
	{
		$this->controlador = 'estados';
		$this->formulario = 'formEstados';
	}

	public function listar($datos = '', $mensaje = '', $tipo_mensaje = '', $filtro = '')
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
					<a id="btAgregar" style="width:30px;" title="Agregar Registro" href="javascript:refrescar('abms/index.php?controlador=estados&accion=agregar&anio=<?php echo $filtro['anio']; ?>&tipo=<?php echo $filtro['tipo']; ?>&numero=<?php echo $filtro['numero']; ?>&cuerpo=<?php echo $filtro['cuerpo']; ?>&alcance=<?php echo $filtro['alcance']; ?>', 'contenidoAjaxPrincipal');">
						<img id="imgAgregar" src="imagenes/barra/add_16x16.gif" width="16" height="16" />
					</a>
				<?php
				}
				else
				{
				?>
					<a id="btAgregar" style="width:30px;" title="Agregar Registro" href="#">
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
						<a title="Buscar" href="javascript:buscarEstados();">
							<img src="imagenes/zoom_16x16.gif" width="16" height="16" />&nbsp;Buscar
						</a>
					</div>

					<div class="p_buscador_margen_datos"></div>

					<div class="p_boton_edicion">
						<a title="Restablecer" href="javascript:location.href='index.php';">
							<img src="imagenes/limpiar.png" width="16" height="16" />&nbsp;Restablecer
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

		<div class="p_solapas_titulos p_buscador_texto">

			<div id="p_solapa_link1" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=expedientes&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&sentido=anterior', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link1').setStyle('color','#315299');" onmouseOut="$('p_solapa_link1').setStyle('color','#000');">Expedientes</div>
			<div id="p_solapa_link2" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=proyectos&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link2').setStyle('color','#315299');" onmouseOut="$('p_solapa_link2').setStyle('color','#000');">Proyectos</div>
			<div id="p_solapa_link3" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=giros&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link3').setStyle('color','#315299');" onmouseOut="$('p_solapa_link3').setStyle('color','#000');">Giros</div>
			<div id="p_solapa_link4" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=sanciones&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link4').setStyle('color','#315299');" onmouseOut="$('p_solapa_link4').setStyle('color','#000');">Sanciones</div>
			<div id="p_solapa_link5" class="p_solapa_link" style="background-color:silver" onclick="refrescar('abms/index.php?controlador=estados&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link5').setStyle('color','#315299');" onmouseOut="$('p_solapa_link5').setStyle('color','#000');">Estados</div>
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

		<div id="ub_listado" class="contenedor_listado">

			<input type="hidden" id="controlador" value="estados" />
			<input type="hidden" id="cantidad" value="<?php echo count($datos); ?>" />
			<input type="hidden" id="pagina" value="<?php echo $filtro['pagina']; ?>">
			<input type="hidden" id="nroFila_elegida" value="">

			<table class="e_tabla_texto">
				<?php
				$cantidad = count($datos);
				if ( $cantidad > 0 )
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
							<th class="orden_link">Fecha</th>
							<th class="orden_link">Orden</th>
							<th class="orden_link">C&oacute;digo</th>
							<th class="orden_link">Estado</th>
							<th class="orden_link">Observaciones</th>
						</tr>
					</thead>
					<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">

						<?php
						$cantidad = count($datos);
						for ($i=0; $i < $cantidad; $i++)
						{
							$dato = &$datos[$i];

							$evento_dobleclick = "";
							//SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN EDITAR
							if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2)
							{
								$evento_dobleclick = "onDblClick=\"javascript:refrescar('abms/index.php?controlador=estados&accion=editar&anio=".$dato['anio']."&tipo=".$dato['tipo']."&numero=".$dato['numero']."&cuerpo=".$dato['cuerpo']."&alcance=".$dato['alcance']."&fecha_estado=".$dato['fecha_estado']."&orden_estado=".$dato['orden_estado']."&pagina=".$filtro['pagina']."', 'contenidoAjaxPrincipal');\"";
							}
						?>
							<input type="hidden" id="fecha_estado_hidden<?php echo $i; ?>" value="<?php echo $dato['fecha_estado']; ?>" />

							<tr id="e_fila<?php echo $i; ?>" onmouseover="javascript:resaltarFila(<?php echo $i; ?>);" onmouseout="javascript:no_resaltarFila(<?php echo $i; ?>);" onclick="javascript:marcarFila(<?php echo $i; ?>);$('modifico_usr').setHTML('Modificado por : <?php echo $dato['codigo_usuario']; ?>');" <?php echo $evento_dobleclick; ?> >
								<?php
								// SOLO EL PERFIL 1 o 2 PUEDE MODIFICAR
								if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 )
								{
								?>
									<td width="16">
										<a style="width:16px;height:16px;display:block;" href="javascript:refrescar('abms/index.php?controlador=estados&accion=editar&anio=<?php echo $dato['anio']; ?>&tipo=<?php echo $dato['tipo']; ?>&numero=<?php echo $dato['numero']; ?>&cuerpo=<?php echo $dato['cuerpo']; ?>&alcance=<?php echo $dato['alcance']; ?>&fecha_estado=<?php echo $dato['fecha_estado']; ?>&orden_estado=<?php echo $dato['orden_estado']; ?>&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" title="Editar">
											<img src="imagenes/b_edit.png" width="14" height="14" />
										</a>
									</td>
									<td width="16">
										<a style="width:16px;height:16px;display:block;" href="javascript:if(confirm('Desea eliminar el Estado?')){refrescar('abms/index.php?controlador=estados&accion=eliminar&anio=<?php echo $dato['anio']; ?>&tipo=<?php echo $dato['tipo']; ?>&numero=<?php echo $dato['numero']; ?>&cuerpo=<?php echo $dato['cuerpo']; ?>&alcance=<?php echo $dato['alcance']; ?>&fecha_estado=<?php echo $dato['fecha_estado']; ?>&orden_estado=<?php echo $dato['orden_estado']; ?>', 'contenidoAjaxPrincipal');};" title="Eliminar">
											<img src="imagenes/b_drop.png" width="14" height="14" />
										</a>
									</td>
								<?php
								}
								?>
								<td nowrap id="i_fecha_estado<?php echo $i; ?>" style="width:70px;height:17px;text-align:left;padding:0 3px 0 3px;"><?php echo $this->formatearFecha($dato['fecha_estado']); ?></td>
								<td nowrap id="i_orden_estado<?php echo $i; ?>" style="width:45px;height:17px;text-align:right;padding:0 3px 0 3px;"><?php echo ($dato['orden_estado'] != '') ? $dato['orden_estado'] : '&nbsp;'; ?></td>
								<td nowrap id="i_codigo_estado<?php echo $i; ?>" style="width:87px;height:17px;text-align:right;padding:0 3px 0 3px;"><?php echo ($dato['codigo_estado'] != '') ? $dato['codigo_estado'] : '&nbsp;' ; ?></td>
								<td nowrap id="i_nombre_estado<?php echo $i; ?>" style="width:470px;height:17px;text-align:left;padding:0 3px 0 3px;"><?php echo ($dato['nombre_estado'] != '') ? $dato['nombre_estado'] : '&nbsp;' ; ?></td>
								<td nowrap id="i_observaciones_estado<?php echo $i; ?>" style="width:400px;height:17px;text-align:left;padding:0 3px 0 3px;"><?php echo ($dato['observaciones_estado'] != '') ? $dato['observaciones_estado'] : '&nbsp;' ; ?></td>
							</tr>
						<?php
						}

						$posicion_en_el_listado = $i-1; // POR DEFECTO
						if ($filtro['por_teclado']=='arriba'){ $posicion_en_el_listado = $i-1; } // PARA VER LA PAGINA ANTERIOR
						if ($filtro['por_teclado']=='abajo'){ $posicion_en_el_listado = 0; } // PARA VER LA PAGINA SIGUIENTE
						?>
					</tbody>
				<?php
				}
				?>
			</table>

		</div>

	    <div class="p_borde_superior"></div>

		<div id="capa_datos_inferior">
			<!-- AQUI SE VISUALIZAN LOS DATOS DEL EXPEDIENTE SELECCIONADO -->
		</div>

		<script>
		    Window.onDomReady(MOOdalBox.init.bind(MOOdalBox));

		    $('f_tipo').value = '<?php echo ($filtro['tipo']) ? $filtro['tipo'] : 'E'; ?>';

		    //SE CARGAN (VISUALIZAN) LOS DATOS EN EL BUSCADOR SUPERIOR
		    cargarBuscador($('f_anio').value, $('f_tipo').value, $('f_numero').value, $('f_cuerpo').value, $('f_alcance').value);

		    //SE VISUALIZAN LOS DATOS(Iniciador, Categoria, Autores, Temas) DEL EXPEDIENTE SELECCIONADO
		    pedirDatos($('f_anio').value, $('f_tipo').value, $('f_numero').value, $('f_cuerpo').value, $('f_alcance').value);

		    // SE SETEA EL HREF DE 'btPrint' PARA GENERAR LA FICHA EN PDF
		    $('btPrint').addEvent('click', function(){
				$('btPrint').setProperty('href','consultas/index.php?controlador=ficha&accion=generar_ficha&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'');
				$('btPrint').setProperty('target','_blank');
		    });
		    // SE SETEA EL HREF DE 'btPrintEtiq' PARA GENERAR LA ETIQUETA EN PDF
		    $('btPrintEtiq').addEvent('click', function(){
				$('btPrintEtiq').setProperty('href','consultas/index.php?controlador=ficha&accion=generarEtiqueta_ficha&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'');
				$('btPrintEtiq').setProperty('target','_blank');
		    });

		    // Si hay registros
		    if ( $('cantidad').value > 0 )
		    {
				// SE SETEA LA FILA ACTUAL
				$('nroFila_elegida').value = <?php echo $posicion_en_el_listado; ?>;

				// SE MARCA EL ULTIMO REGISTRO DEL LISTADO
				$('e_fila<?php echo $posicion_en_el_listado; ?>').setStyles({'background-color':'#76A0CD'});//, 'color':'#fff' 21/06/2018

				// SE BAJA EL SCROLL AL ACTUALIZAR EL LISTADO
				window.setTimeout("bajarScrollListados('ub_listado')",3);
		    }

			function buscarEstados()
			{
				refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');
			}

			$('f_anio').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						buscarEstados();
					}
				}
			});

			$('f_numero').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						buscarEstados();
					}
				}
			});

			$('f_cuerpo').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						buscarEstados();
					}
				}
			});

			$('f_alcance').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						buscarEstados();
					}
				}
			});
		</script>
	<?php
	}

	public function editar($datos = null, $filtro = null, $listadoCodEstados = null)
	{
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
					<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="16" height="16" />
				</a>
				<a id="btAnterior" href="#">
					<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="16" height="16" />
				</a>
				<a id="btSiguiente" href="#">
					<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="16" height="16" />
				</a>
				<a id="btUltimo" href="#">
					<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="16" height="16" />
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
					<a id="btGuardar" title="Aceptar los cambios realizados" href="javascript:validarEstado();">
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
				<a id="btCancelar" title="Cancelar los cambios realizados" href="javascript:refrescar('abms/index.php?controlador=estados&accion=listar&anio=<?php echo $filtro['anio']; ?>&tipo=<?php echo $filtro['tipo']; ?>&numero=<?php echo $filtro['numero']; ?>&cuerpo=<?php echo $filtro['cuerpo']; ?>&alcance=<?php echo $filtro['alcance']; ?>', 'contenidoAjaxPrincipal');">
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
					<div class="e_solapa_al_editar">Proyectos</div>
					<div class="e_solapa_al_editar">Giros</div>
					<div class="e_solapa_al_editar">Sanciones</div>
					<div class="e_solapa_al_editar" style="color:#000;background-color:silver">Estados</div>
					<div class="e_solapa_al_editar">Antecedentes</div>
					<div class="e_solapa_al_editar">Pr&eacute;stamos</div>
					<div class="e_solapa_al_editar">Ruta</div>
				</div>
				<div class="e_edit_gral">
					<div class="e_edit_margen_izq"></div>
					<div class="e_edit_nombres">
						<div class="e_edit_nombre">Fecha:</div>
						<div class="e_edit_nombre_margen"></div>
						<div class="e_edit_nombre">Orden:</div>
						<div class="e_edit_nombre_margen"></div>
						<div class="e_edit_nombre">Estado:</div>
						<div class="e_edit_nombre_margen"></div>
						<div class="e_edit_nombre">Observaciones:</div>
					</div>
					<div class="e_edit_valores">
						<div class="e_edit_valor">
							<?php
							// SI SE ESTÁ EDITANDO UN ESTADO, DE DESHABILITA LA FECHA Y SE OCULTA EL CALENDARIO
							if ( isset($datos[0]['fecha_estado']) )
							{
							?>
								<input type="text" name="fecha_estado" id="fecha_estado" value="<?php echo ($datos[0]['fecha_estado']) ?  $this->formatearFecha($datos[0]['fecha_estado']) : date("d/m/Y"); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="9" maxlength="10" readonly="readonly" />
							<?php
							}
							else // AL INGRESAR UN NUEVO ESTADO SE HABILITA LA FECHA Y SE MUESTRA EL CALENDARIO
							{
							?>
								<input type="text" name="fecha_estado" id="fecha_estado" value="<?php echo ($datos[0]['fecha_estado']) ? $this->formatearFecha($datos[0]['fecha_estado']) : date("d/m/Y"); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="9" maxlength="10" />
								<input type="image" id="img_fecha_estado" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha." width="16" height="16">
							<?php
							}
							?>
						</div>
						<div class="e_edit_nombre_margen"></div>
						<div class="e_edit_valor">
							<input type="text" name="orden_estado" id="orden_estado" value="<?php echo ($datos[0]['orden_estado']) ? $datos[0]['orden_estado'] : $filtro['ultimoOrden'] + 1; ?>" size="1" maxlength="2" readonly="readonly" />
						</div>
						<div class="e_edit_nombre_margen"></div>
						<div class="e_edit_valor">
							<input type="hidden" name="id_codestado" id="id_codestado" value="<?php echo ($datos[0]['id_codestado']) ? $datos[0]['id_codestado'] : ''; ?>" />

							<input type="text" name="codigo_estado" id="codigo_estado" value="<?php echo ($datos[0]['codigo_estado']) ? $datos[0]['codigo_estado'] : ''; ?>" size="3" maxlength="3" />
							&nbsp;
							<a href="abms/index.php?controlador=codestados&accion=listarModal" rel="moodalbox 550 350" title="Buscar Iniciador"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
							&nbsp;
							<input type="text" name="nombre_estado" id="nombre_estado" value="<?php echo ($datos[0]['nombre_estado']) ? $datos[0]['nombre_estado'] : ''; ?>" style="width:310px" maxlength="60" readonly="readonly"  />
						</div>
						<div class="e_edit_nombre_margen"></div>
						<div class="e_edit_valor">
							<textarea name="observaciones_estado" id="observaciones_estado" style="width:384px;height:54px;"><?php echo ($datos[0]['observaciones_estado']) ? $datos[0]['observaciones_estado'] : ''; ?></textarea>
						</div>
					</div>
				</div>
			</form>
		</div>
		<script type="text/javascript">
			//	PARA LA VENTANA MODAL (inicializa el objeto MOOdalBox)
			Window.onDomReady(MOOdalBox.init.bind(MOOdalBox));

			<?php
			if ( isset($datos[0]['fecha_estado']) )
			{
			?>
				$('fecha_estado').setStyles({ 'background-color':'#EBEBE4', 'color':'#A09FA4' });

				setfocus('codigo_estado');
			<?php
			}
			else
			{
			?>
				//CALENDARIO PARA LA FECHA DESDE
				var calDesde = new Zapatec.Calendar.setup({

					inputField	:"fecha_estado",
					ifFormat	:"%d/%m/%Y",
					button		:"img_fecha_estado",
					showsTime	:false
				});

				$('fecha_estado').setStyles({ 'background-color':'#fff', 'color':'#000' });

				setfocus('fecha_estado');
			<?php
			}

			if ($listadoCodEstados != null)
			{
			?>
				var cantidadCodEstados = <?php echo count($listadoCodEstados); ?>;
				// SE CREA EL ARRAY JS DE ESTADOS
				var estados = new Array(cantidadCodEstados);

			<?php
				$cant_estados = count($listadoCodEstados);
				for ($i=0; $i < $cant_estados; $i++)
				{
			?>
					// EL INDICE ES EL CODIGO DEL ESTADO
					estados[<?php echo $listadoCodEstados[$i]['codigo_estado']; ?>] = {
											id_codestado:"<?php echo $listadoCodEstados[$i]['id_codestado']; ?>",
											codigo_estado:"<?php echo $listadoCodEstados[$i]['codigo_estado']; ?>",
											nombre_estado:"<?php echo $listadoCodEstados[$i]['nombre_estado']; ?>"
										 };
			<?php
				}
			}
			?>

			if( $('fecha_estado') )
			{
				$('fecha_estado').addEvents({
					keyup: function(){
						if ($('img_fecha_estado'))
						{
							// SE HABILITA EL CALENDARIO
							$('img_fecha_estado').disabled = false;
						}
					},
					keydown: function(event){
						if(event.key == 'Enter')
						{
							if ($('img_fecha_estado'))
							{
								// SE DESHABILITA EL CALENDARIO
								$('img_fecha_estado').disabled = true;
							}
						}
					}
				});
			}

			$('codigo_estado').addEvents({
				keyup: function(){

					if ($('img_fecha_estado'))
					{
						// SE HABILITA EL CALENDARIO
						$('img_fecha_estado').disabled = false;
					}

					if ( $('codigo_estado').value != '' )
					{

						if ( estados[$('codigo_estado').value] != undefined )
						{
							$('id_codestado').value = estados[$('codigo_estado').value].id_codestado;
							$('nombre_estado').value = estados[$('codigo_estado').value].nombre_estado;
						}
						else
						{
							$('nombre_estado').value = "";
						}
					}
					else
					{
						$('nombre_estado').value = "";
					}
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						if ($('img_fecha_estado'))
						{
							// SE DESHABILITA EL CALENDARIO
							$('img_fecha_estado').disabled = true;
						}
					}
				}
			});

			$('nombre_estado').addEvents({
				keyup: function(){
					if ($('img_fecha_estado'))
					{
						// SE HABILITA EL CALENDARIO
						$('img_fecha_estado').disabled = false;
					}
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						if ($('img_fecha_estado'))
						{
							// SE DESHABILITA EL CALENDARIO
							$('img_fecha_estado').disabled = true;
						}
					}
				}
			});

		</script>
	<?php
	}

}
?>
