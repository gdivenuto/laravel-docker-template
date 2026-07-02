<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaAntecedentes extends VistaBase
{
    private $controlador;
    private $formulario;
	const RUTA_DIRECTORIO = "/var/www/sgl/expedientes/expe-de/";

    public function __construct()
    {
		$this->controlador = 'antecedentes';
		$this->formulario = 'formAntecedentes';
    }

    public function listar($datos = '', $mensaje = '', $tipo_mensaje = '', $filtro = '')
    {
	    // MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);

		$cantidad = count($datos);
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
					<a id="btAgregar" style="width:30px;" title="Agregar Registro" href="javascript:refrescar('abms/index.php?controlador=antecedentes&accion=agregar&anio=<?php echo $filtro['anio']; ?>&tipo=<?php echo $filtro['tipo']; ?>&numero=<?php echo $filtro['numero']; ?>&cuerpo=<?php echo $filtro['cuerpo']; ?>&alcance=<?php echo $filtro['alcance']; ?>&por_boton_Agregar=true', 'contenidoAjaxPrincipal');">
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
						<a title="Buscar" href="javascript:buscarAntecedentes();">
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

	    <div class="p_solapas_titulos p_buscador_texto">

		    <div id="p_solapa_link1" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=expedientes&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&sentido=anterior', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link1').setStyle('color','#315299');" onmouseOut="$('p_solapa_link1').setStyle('color','#000');">Expedientes</div>
		    <div id="p_solapa_link2" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=proyectos&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link2').setStyle('color','#315299');" onmouseOut="$('p_solapa_link2').setStyle('color','#000');">Proyectos</div>
		    <div id="p_solapa_link3" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=giros&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link3').setStyle('color','#315299');" onmouseOut="$('p_solapa_link3').setStyle('color','#000');">Giros</div>
		    <div id="p_solapa_link4" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=sanciones&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link4').setStyle('color','#315299');" onmouseOut="$('p_solapa_link4').setStyle('color','#000');">Sanciones</div>
		    <div id="p_solapa_link5" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=estados&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link5').setStyle('color','#315299');" onmouseOut="$('p_solapa_link5').setStyle('color','#000');">Estados</div>
		    <div id="p_solapa_link6" class="p_solapa_link" style="background-color:silver" onclick="refrescar('abms/index.php?controlador=antecedentes&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link6').setStyle('color','#315299');" onmouseOut="$('p_solapa_link6').setStyle('color','#000');">Antecedentes</div>
		    <?php
			// SOLO PERFIL 1 Y 2 PUEDEN VERIFICAR LOS PRESTAMOS DEL EXPEDIENTE
			//if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 ) {
			?>
		    	<div id="p_solapa_link7" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=prestamos&accion=listarEnSolapa&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link7').setStyle('color','#315299');" onmouseOut="$('p_solapa_link7').setStyle('color','#000');">Pr&eacute;stamos</div>
		    <?php
			//}
			?>
		    <div id="p_solapa_link8" class="p_solapa_link" style="color:silver;" onclick="#">Ruta</div>

	    </div>

	    <div class="p_borde_superior_sin_linea"></div>

	    <div id="ub_listado" class="contenedor_listado">

		    <!-- PARA EL BOTON 'Ir a antecedente' -->
		    <input type="hidden" name="antecedente_anio" id="antecedente_anio" value="" />
		    <input type="hidden" name="antecedente_tipo" id="antecedente_tipo" value="" />
		    <input type="hidden" name="antecedente_numero" id="antecedente_numero" value="" />
		    <input type="hidden" name="antecedente_cuerpo" id="antecedente_cuerpo" value="" />
		    <input type="hidden" name="antecedente_alcance" id="antecedente_alcance" value="" />

			<input type="hidden" id="controlador" name="controlador" value="antecedentes" />
			<input type="hidden" id="cantidad" value="<?php echo $cantidad; ?>" />
			<input type="hidden" id="pagina" value="<?php echo $filtro['pagina']; ?>">
			<input type="hidden" id="nroFila_elegida" value="">

		    <table class="e_tabla_texto">
				<?php
				if ( $cantidad > 0) {
				?>
					<thead class="e_tabla_titulos">
						<tr style="background-color:silver;">
							<th colspan="4" style="border:0">
								<a href="javascript:irAntecedente();" class="e_btAgregadoExpediente">Ir a antecedente</a>
							</th>
							<th colspan="11" style="border:0;">&nbsp;</th>
						</tr>
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
							<th class="orden_link">A&ntilde;o</th>
							<th class="orden_link">Tipo</th>
							<th class="orden_link">N&uacute;mero</th>
							<th class="orden_link">Dig.</th>
							<th class="orden_link">Cpo.</th>
							<th class="orden_link">Alc.</th>
							<th class="orden_link">Cpo.Alc.</th>
							<th class="orden_link">Anex.Alc.</th>
							<th class="orden_link">Cpo.Anex.Alc.</th>
							<th class="orden_link">Anex.</th>
							<th class="orden_link">Cpo.Anex.</th>
							<th class="orden_link">Observaciones</th>

							<?php //SOLO EL PERFIL 1 o 2 PUEDE CARGAR DOCUMENTOS
							if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 )
							{
							?>
								<th class="orden_link">&nbsp;</th>
							<?php
							}
							?>
						</tr>
					</thead>
					<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
						<?php
						for ($i=0; $i < $cantidad; $i++) {
							$dato = &$datos[$i];

							$evento_dobleclick = "";
							//SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN EDITAR
							if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2)
								$evento_dobleclick = "onDblClick=\"javascript:refrescar('abms/index.php?controlador=antecedentes&accion=editar&anio=".$dato['anio']."&tipo=".$dato['tipo']."&numero=".$dato['numero']."&cuerpo=".$dato['cuerpo']."&alcance=".$dato['alcance']."&anio_a=".$dato['anio_a']."&tipo_a=".$dato['tipo_a']."&numero_a=".$dato['numero_a']."&cuerpo_a=".$dato['cuerpo_a']."&alcance_a=".$dato['alcance_a']."&pagina=".$filtro['pagina']."', 'contenidoAjaxPrincipal');\"";
						?>
							<tr id="e_fila<?php echo $i; ?>" onmouseover="javascript:resaltarFila(<?php echo $i; ?>);" onmouseout="javascript:no_resaltarFila(<?php echo $i; ?>);" onclick="javascript:marcarFila(<?php echo $i; ?>);paraIrAntecedente(<?php echo $dato['anio_a']; ?>, '<?php echo $dato['tipo_a']; ?>', <?php echo $dato['numero_a']; ?>, <?php echo $dato['cuerpo_a']; ?>, <?php echo $dato['alcance_a']; ?>);$('modifico_usr').setHTML('Modificado por : <?php echo $dato['codigo_usuario']; ?>');" <?php echo $evento_dobleclick; ?> >
								<?php
								// SOLO EL PERFIL 1 o 2 PUEDE MODIFICAR
								if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) {
								?>
									<td width="16">
										<a style="width:16px;height:16px;display:block;" href="javascript:refrescar('abms/index.php?controlador=antecedentes&accion=editar&anio=<?php echo $dato['anio']; ?>&tipo=<?php echo $dato['tipo']; ?>&numero=<?php echo $dato['numero']; ?>&cuerpo=<?php echo $dato['cuerpo']; ?>&alcance=<?php echo $dato['alcance']; ?>&anio_a=<?php echo $dato['anio_a']; ?>&tipo_a=<?php echo $dato['tipo_a']; ?>&numero_a=<?php echo $dato['numero_a']; ?>&cuerpo_a=<?php echo $dato['cuerpo_a']; ?>&alcance_a=<?php echo $dato['alcance_a']; ?>&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" title="Editar">
											<img src="imagenes/b_edit.png" width="14" height="14" align="top" />
										</a>
									</td>
									<td width="16">
										<a style="width:16px;height:16px;display:block;" href="javascript:if(confirm('Desea eliminar el Antecedente?')){refrescar('abms/index.php?controlador=antecedentes&accion=eliminar&anio=<?php echo $dato['anio']; ?>&tipo=<?php echo $dato['tipo']; ?>&numero=<?php echo $dato['numero']; ?>&cuerpo=<?php echo $dato['cuerpo']; ?>&alcance=<?php echo $dato['alcance']; ?>&anio_a=<?php echo $dato['anio_a']; ?>&tipo_a=<?php echo $dato['tipo_a']; ?>&numero_a=<?php echo $dato['numero_a']; ?>&digito_a=<?php echo $dato['digito_a']; ?>&cuerpo_a=<?php echo $dato['cuerpo_a']; ?>&alcance_a=<?php echo $dato['alcance_a']; ?>&cuerpoalcance_a=<?php echo $dato['cuerpoalcance_a']; ?>&anexoalcance_a=<?php echo $dato['anexoalcance_a']; ?>&cuerpoanexoalcance_a=<?php echo $dato['cuerpoanexoalcance_a']; ?>&anexo_a=<?php echo $dato['anexo_a']; ?>&cuerpoanexo_a=<?php echo $dato['cuerpoanexo_a']; ?>', 'contenidoAjaxPrincipal');};" title="Eliminar">
											<img src="imagenes/b_drop.png" width="14" height="14" align="top" />
										</a>
									</td>
								<?php
								}
								?>
								<td id="i_anio_a<?php echo $i; ?>" style="width:36px;height:17px;text-align:right;padding:0 3px 0 3px;"><?php echo $dato['anio_a']; ?></td>
								<td id="i_tipo_a<?php echo $i; ?>" style="width:32px;height:17px;text-align:left;padding:0 3px 0 3px;"><?php echo $dato['tipo_a']; ?></td>
								<td id="i_numero_a<?php echo $i; ?>" style="width:57px;height:17px;text-align:right;padding:0 3px 0 3px;"><?php echo $dato['numero_a']; ?></td>
								<td id="i_digito_a<?php echo $i; ?>" style="width:43px;height:17px;text-align:right;padding:0 3px 0 3px;"><?php echo $dato['digito_a']; ?></td>
								<td id="i_cuerpo_a<?php echo $i; ?>" style="width:43px;height:17px;text-align:right;padding:0 3px 0 3px;"><?php echo $dato['cuerpo_a']; ?></td>
								<td id="i_alcance_a<?php echo $i; ?>" style="width:42px;height:17px;text-align:right;padding:0 3px 0 3px;"><?php echo $dato['alcance_a']; ?></td>
								<td id="i_cuerpoalcance_a<?php echo $i; ?>" style="width:43px;height:17px;text-align:right;padding:0 3px 0 3px;"><?php echo $dato['cuerpoalcance_a']; ?></td>
								<td id="i_anexoalcance_a<?php echo $i; ?>" style="width:43px;height:17px;text-align:right;padding:0 3px 0 3px;"><?php echo $dato['anexoalcance_a']; ?></td>
								<td id="i_cuerpoanexoalcance_a<?php echo $i; ?>" style="width:43px;height:17px;text-align:right;padding:0 3px 0 3px;"><?php echo $dato['cuerpoanexoalcance_a']; ?></td>
								<td id="i_anexo_a<?php echo $i; ?>" style="width:43px;height:17px;text-align:right;padding:0 3px 0 3px;"><?php echo $dato['anexo_a']; ?></td>
								<td id="i_cuerpoanexo_a<?php echo $i; ?>" style="width:43px;height:17px;text-align:right;padding:0 3px 0 3px;"><?php echo $dato['cuerpoanexo_a']; ?></td>
								<td id="i_observaciones_antecedentes<?php echo $i; ?>" style="width:400px;height:17px;text-align:left;padding:0 3px 0 3px;"><?php echo ($dato['observaciones_antecedentes'] != '') ? $dato['observaciones_antecedentes'] : '&nbsp;' ; ?></td>

								<?php //SOLO EL PERFIL 1 o 2 PUEDE CARGAR DOCUMENTOS DEL EJECUTIVO
								if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 ) {
									// SE ARMA EL NOMBRE DEL DIRECTORIO DONDE SE TOMARÁN LOS DOCUMENTOS DEL EJECUTIVO
									$aux_numero_a = 1000000+$dato['numero_a'];
									$numero_a = substr($aux_numero_a, -6);

									$ruta_documentos_ejecutivo_para_cargar = self::RUTA_DIRECTORIO.$dato['anio_a']."/".$dato['anio_a']."-".$numero_a."-".$dato['digito_a'];

									// SI ES DEL EJECUTIVO Y EL DIRECTORIO EXISTE
									if ( $dato['tipo_a'] == 'D' && is_dir($ruta_documentos_ejecutivo_para_cargar) ) {
								?>
										<td width="16">
											<a style="width:16px;height:16px;display:block;" href="javascript:refrescar('tareas/index.php?controlador=cargar_documentos_depto_ejecutivo&accion=listar&anio=<?php echo $dato['anio']; ?>&tipo=<?php echo $dato['tipo']; ?>&numero=<?php echo $dato['numero']; ?>&cuerpo=<?php echo $dato['cuerpo']; ?>&alcance=<?php echo $dato['alcance']; ?>&anio_a=<?php echo $dato['anio_a']; ?>&tipo_a=<?php echo $dato['tipo_a']; ?>&numero_a=<?php echo $dato['numero_a']; ?>&digito_a=<?php echo $dato['digito_a']; ?>', 'capaVentana');" title="Cargar Documentos">
												<img src="imagenes/barra/upload.jpeg" width="14" height="14" align="top" />
											</a>
										</td>
								<?php
									} else
										echo '<td width="16">&nbsp;</td>';
								}
								?>
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
		    $('btPrint').addEvent('click', function() {
				$('btPrint').setProperty('href','consultas/index.php?controlador=ficha&accion=generar_ficha&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'');
				$('btPrint').setProperty('target','_blank');
		    });
		    // SE SETEA EL HREF DE 'btPrintEtiq' PARA GENERAR LA ETIQUETA EN PDF
		    $('btPrintEtiq').addEvent('click', function() {
				$('btPrintEtiq').setProperty('href','consultas/index.php?controlador=ficha&accion=generarEtiqueta_ficha&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'');
				$('btPrintEtiq').setProperty('target','_blank');
		    });

			function buscarAntecedentes() {
				refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');
			}

			$('f_anio').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
						buscarAntecedentes();
				}
			});

			$('f_numero').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
						buscarAntecedentes();
				}
			});

			$('f_cuerpo').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
						buscarAntecedentes();
				}
			});

			$('f_alcance').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
						buscarAntecedentes();
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
			}

			<?php if ( $datos[$i-1]['anio_a'] != '' ) { ?>
				var anio    = <?php echo $datos[$posicion_en_el_listado]['anio_a']; ?>;
				var tipo    = '<?php echo $datos[$posicion_en_el_listado]['tipo_a']; ?>';
				var numero  = <?php echo $datos[$posicion_en_el_listado]['numero_a']; ?>;
				var cuerpo  = <?php echo $datos[$posicion_en_el_listado]['cuerpo_a']; ?>;
				var alcance = <?php echo $datos[$posicion_en_el_listado]['alcance_a']; ?>;

				paraIrAntecedente(anio, tipo, numero, cuerpo, alcance);
			<?php } ?>
		</script>
    <?php
    }

    public function editar($datos = null, $antecedentesRelacionados = null, $filtro = null)
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
				    <img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="16" height="16" align="top" />
			    </a>
			    <a id="btAnterior" href="#">
				    <img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="16" height="16" align="top" />
			    </a>
			    <a id="btSiguiente" href="#">
				    <img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="16" height="16" align="top" />
			    </a>
			    <a id="btUltimo" href="#">
				    <img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="16" height="16" align="top" />
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
					<a id="btGuardar" title="Aceptar los cambios realizados" href="javascript:validarAntecedente(true);">
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
			    <a id="btCancelar" title="Cancelar los cambios realizados" href="javascript:refrescar('abms/index.php?controlador=antecedentes&accion=listar&anio=<?php echo $filtro['anio']; ?>&tipo=<?php echo $filtro['tipo']; ?>&numero=<?php echo $filtro['numero']; ?>&cuerpo=<?php echo $filtro['cuerpo']; ?>&alcance=<?php echo $filtro['alcance']; ?>', 'contenidoAjaxPrincipal');">
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

				<input type="hidden" name="controlador" value="<?php echo $this->controlador; ?>" />

				<input type="hidden" name="accion" id="accion" value="<?php echo ($datos[0]['tipo']) ? 'modificar' : 'insertar'; ?>" />

				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="por_boton_Agregar" id="por_boton_Agregar" value="<?php echo $filtro['por_btAgregar']; ?>" >

				<!-- CLAVE DEL EXPEDIENTE -->
				<input type="hidden" name="anio" id="anio" value="<?php echo ($filtro['anio']) ? $filtro['anio'] : ''; ?>" />
				<input type="hidden" name="tipo" id="tipo" value="<?php echo ($filtro['tipo']) ? $filtro['tipo'] : ''; ?>" />
				<input type="hidden" name="numero" id="numero" value="<?php echo ($filtro['numero']) ? $filtro['numero'] : ''; ?>" />
				<input type="hidden" name="cuerpo" id="cuerpo" value="<?php echo ($filtro['cuerpo']) ? $filtro['cuerpo'] : ''; ?>" />
				<input type="hidden" name="alcance" id="alcance" value="<?php echo ($filtro['alcance']) ? $filtro['alcance'] : ''; ?>" />

				<div class="e_datos_sup">
					<div class="e_datos_label e_datos_texto">A&ntilde;o:</div>
					<div class="e_datos_valor">
						<input name="d_anio" value="<?php echo ($filtro['anio']) ? $filtro['anio'] : ''; ?>" class="e_datos_texto" onKeyPress="return soloEnteros(event);" size="3" maxlength="4" style="border:0;" disabled />
					</div>
					<div class="e_datos_label e_datos_texto">Tipo:</div>
					<div class="e_datos_valor">
						<input name="d_tipo" value="<?php echo ($filtro['tipo']) ? $filtro['tipo'] : ''; ?>" class="e_datos_texto" size="1" maxlength="1" style="border:0;" disabled />
					</div>
					<div class="e_datos_label e_datos_texto">N&uacute;mero:</div>
					<div class="e_datos_valor e_datos_texto">
						<input name="d_numero" value="<?php echo ($filtro['numero']) ? $filtro['numero'] : ''; ?>" class="e_datos_texto" onKeyPress="return soloEnteros(event);" size="10" maxlength="10" style="border:0;" disabled />
					</div>
					<div class="e_datos_label e_datos_texto">Cuerpo:</div>
					<div class="e_datos_valor e_datos_texto">
						<input name="d_cuerpo" value="<?php echo ($filtro['cuerpo']) ? $filtro['cuerpo'] : '0'; ?>" class="e_datos_texto" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" style="border:0;" disabled />
					</div>
					<div class="e_datos_label e_datos_texto">Alcance:</div>
					<div class="e_datos_valor e_datos_texto">
						<input name="d_alcance" value="<?php echo ($filtro['alcance']) ? $filtro['alcance'] : '0'; ?>" class="e_datos_texto" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" style="border:0;" disabled />
					</div>
				</div>

				<div class="p_borde_superior"></div>

				<div class="e_solapas_titulos p_buscador_texto">
					<div class="e_solapa_al_editar">Expedientes</div>
					<div class="e_solapa_al_editar">Proyectos</div>
					<div class="e_solapa_al_editar">Giros</div>
					<div class="e_solapa_al_editar">Sanciones</div>
					<div class="e_solapa_al_editar">Estados</div>
					<div class="e_solapa_al_editar" style="color:#000;background-color:silver">Antecedentes</div>
					<div class="e_solapa_al_editar">Pr&eacute;stamos</div>
					<div class="e_solapa_al_editar">Ruta</div>
				</div>
				<div class="e_edit_gral">
					<?php
					if ( isset($datos[0]['tipo_a']) && $datos[0]['tipo_a'] == 'D' )
					{
						$deshabilitado = "";
						$color_segun_tipo = "color:#000;";
					}
					else
					{
						$deshabilitado = "disabled";
						$color_segun_tipo = "color:#C0BFBE;";
					}
					?>
					<div class="e_antecedente um_texto_datos">
						&nbsp;A&ntilde;o:&nbsp;
						<input type="text" name="anio_a" id="anio_a" value="<?php if (isset($datos[0]['anio_a'])){ echo $datos[0]['anio_a'];}else{ echo date("Y");} ?>" onKeyPress="return soloEnteros(event);" size="4" maxlength="4" style="border:0;" />
						&nbsp;Tipo:&nbsp;
						<select name="tipo_a" id="tipo_a" style="width:110px;">
							<option value="E" >E&nbsp;&nbsp;Expediente</option>
							<option value="D" >D&nbsp;&nbsp;Departamento Ejecutivo</option>
							<option value="N">N&nbsp;&nbsp;Nota</option>
						</select>
						&nbsp;N&uacute;mero:&nbsp;
						<input type="text" name="numero_a" id="numero_a" value="<?php if (isset($datos[0]['numero_a'])){ echo $datos[0]['numero_a'];}else{ echo '';} ?>" onKeyPress="return soloEnteros(event);" size="5" maxlength="5" style="border:0;" />
						&nbsp;D&iacute;gito:&nbsp;
						<input type="text" name="digito_a" id="digito_a" value="<?php if (isset($datos[0]['digito_a'])){ echo $datos[0]['digito_a'];}else{ echo '';} ?>" size="1" maxlength="4" style="border:0;<?php echo $color_segun_tipo; ?>" <?php echo $deshabilitado; ?> />
						&nbsp;Cuerpo:&nbsp;
						<input type="text" name="cuerpo_a" id="cuerpo_a" value="<?php if (isset($datos[0]['cuerpo_a'])){ echo $datos[0]['cuerpo_a'];}else{ echo '0';} ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" style="border:0;" />
						&nbsp;Alcance:&nbsp;
						<input type="text" name="alcance_a" id="alcance_a" value="<?php if (isset($datos[0]['alcance_a'])){ echo $datos[0]['alcance_a'];}else{ echo '0';} ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" style="border:0;" />
					</div>

					<!-- SOLO SE HABILITAN CIERTOS CAMPOS SI EL TIPO DE EXPEDIENTE ES D - DEPARTAMENTO EJECUTIVO -->
					<div id="datosAntecedente" class="e_antecedente um_texto_datos">
						&nbsp;Anexo:&nbsp;
						<input type="text" name="anexo_a" id="anexo_a" value="<?php if (isset($datos[0]['anexo_a'])){ echo $datos[0]['anexo_a'];}else{ echo '0';} ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" style="border:0;<?php echo $color_segun_tipo; ?>" <?php echo $deshabilitado; ?> />
						&nbsp;Cuerpo:&nbsp;
						<input type="text" name="cuerpoalcance_a" id="cuerpoalcance_a" value="<?php if (isset($datos[0]['cuerpoalcance_a'])){ echo $datos[0]['cuerpoalcance_a'];}else{ echo '0';} ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" style="border:0;<?php echo $color_segun_tipo; ?>" <?php echo $deshabilitado; ?> />
						&nbsp;Anexo Alcance:&nbsp;
						<input type="text" name="anexoalcance_a" id="anexoalcance_a" value="<?php if (isset($datos[0]['anexoalcance_a'])){ echo $datos[0]['anexoalcance_a'];}else{ echo '0';} ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" style="border:0;<?php echo $color_segun_tipo; ?>" <?php echo $deshabilitado; ?> />
						&nbsp;Cuerpo Anexo Alcance:&nbsp;
						<input type="text" name="cuerpoanexoalcance_a" id="cuerpoanexoalcance_a" value="<?php if (isset($datos[0]['cuerpoanexoalcance_a'])){ echo $datos[0]['cuerpoanexoalcance_a'];}else{ echo '0';} ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" style="border:0;<?php echo $color_segun_tipo; ?>" <?php echo $deshabilitado; ?> />
						&nbsp;Cuerpo Anexo:&nbsp;
						<input type="text" name="cuerpoanexo_a" id="cuerpoanexo_a" value="<?php if (isset($datos[0]['cuerpoanexo_a'])){ echo $datos[0]['cuerpoanexo_a'];}else{ echo '0';} ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" style="border:0;<?php echo $color_segun_tipo; ?>" <?php echo $deshabilitado; ?> />
					</div>
					<div class="e_antecedente_observaciones um_texto_datos">
						&nbsp;Observaciones:<br>
						&nbsp;<textarea name="observaciones_antecedentes" id="observaciones_antecedentes"><?php echo ($datos[0]['observaciones_antecedentes']) ? $datos[0]['observaciones_antecedentes'] : ''; ?></textarea>
					</div>
				</div>
			</form>
	    </div>
	    <script>
			$('tipo_a').value = '<?php echo ($datos[0]['tipo_a']) ? $datos[0]['tipo_a'] : 'E'; ?>';

		    $('tipo_a').addEvent('change', function(){

			    if ($('tipo_a').selectedIndex == '1')
			    {
					$('digito_a').disabled = false;
					$('anexo_a').disabled = false;
					$('cuerpoalcance_a').disabled = false;
					$('anexoalcance_a').disabled = false;
					$('cuerpoanexoalcance_a').disabled = false;
					$('cuerpoanexo_a').disabled = false;

					$('digito_a').setStyle('color', '#000');
					$('anexo_a').setStyle('color','#000');
					$('cuerpoalcance_a').setStyle('color','#000');
					$('anexoalcance_a').setStyle('color','#000');
					$('cuerpoanexoalcance_a').setStyle('color','#000');
					$('cuerpoanexo_a').setStyle('color','#000');
			    }
			    else
			    {
					$('digito_a').disabled = true;
					$('anexo_a').disabled = true;
					$('cuerpoalcance_a').disabled = true;
					$('anexoalcance_a').disabled = true;
					$('cuerpoanexoalcance_a').disabled = true;
					$('cuerpoanexo_a').disabled = true;

					$('digito_a').setStyle('color', '#C0BFBE');
					$('anexo_a').setStyle('color','#C0BFBE');
					$('cuerpoalcance_a').setStyle('color','#C0BFBE');
					$('anexoalcance_a').setStyle('color','#C0BFBE');
					$('cuerpoanexoalcance_a').setStyle('color','#C0BFBE');
					$('cuerpoanexo_a').setStyle('color','#C0BFBE');
			    }
		    });

		    setfocus('anio_a');
	    </script>
    <?php
    }

    public function esDistinto($elEditado, $elRelacionado)
    {
        $distinto = true;
	    if ( $elEditado['anio_a'] == $elRelacionado['anio_a'] && $elEditado['numero_a'] == $elRelacionado['numero_a'] && $elEditado['cuerpo_a'] == $elRelacionado['cuerpo_a'] && $elEditado['alcance_a'] == $elRelacionado['alcance_a'] )
	    {
		    $distinto = false;
	    }
	    return $distinto;
    }

}
?>
