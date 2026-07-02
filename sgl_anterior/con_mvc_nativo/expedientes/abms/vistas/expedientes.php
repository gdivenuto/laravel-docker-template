<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaExpedientes extends VistaBase
{
    private $controlador;
    private $formulario;
    private $url_salida;
    private $cantExtractos;	// Cantidad de extractos

    public function __construct()
    {
		$this->controlador = 'expedientes';
		$this->formulario  = 'formExpedientes';
		/* para Producción, momentáneamente */
		$this->url_salida  = '../salir.php';
		/* para Desarrollo *
		$this->url_salida  = URL_KRAKEN_BASE.'html/backend/index.php?c=login&a=logout';
		/**/
	}

    public function listar($datos = '', $mensaje = '', $tipo_mensaje = '', $filtro = '')
	{
		// EN EL LISTADO
	    $_SESSION['agregado_previamente'] = false;

		// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);

		$_SESSION['mensaje'] = "";
		$_SESSION['tipo_mensaje'] = "";
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

			<!-- PAGINADOR -->
			<div class="p_bnav_contenedor_4bt">

				<a id="btPrimero" title="Primer Registro" href="javascript:refrescar('abms/index.php?controlador=expedientes&accion=listar&anio=<?php echo $datos[0]['anio']; ?>&tipo=<?php echo $datos[0]['tipo']; ?>&numero=<?php echo $datos[0]['numero']; ?>&cuerpo=<?php echo $datos[0]['cuerpo']; ?>&alcance=<?php echo $datos[0]['alcance']; ?>&sentido=primero', 'contenidoAjaxPrincipal');">
					<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="14" height="14" align="top" />
				</a>

				<a id="btAnterior" title="Registro Anterior" href="javascript:refrescar('abms/index.php?controlador=expedientes&accion=listar&anio=<?php echo $datos[0]['anio']; ?>&tipo=<?php echo $datos[0]['tipo']; ?>&numero=<?php echo $datos[0]['numero']; ?>&cuerpo=<?php echo $datos[0]['cuerpo']; ?>&alcance=<?php echo $datos[0]['alcance']; ?>&sentido=anterior', 'contenidoAjaxPrincipal');">
					<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="14" height="14" align="top" />
				</a>

				<a id="btSiguiente" title="Registro Siguiente" href="javascript:refrescar('abms/index.php?controlador=expedientes&accion=listar&anio=<?php echo $datos[10]['anio']; ?>&tipo=<?php echo $datos[10]['tipo']; ?>&numero=<?php echo $datos[10]['numero']; ?>&cuerpo=<?php echo $datos[10]['cuerpo']; ?>&alcance=<?php echo $datos[10]['alcance']; ?>&sentido=siguiente', 'contenidoAjaxPrincipal');">
					<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="14" height="14" align="top" />
				</a>

				<a id="btUltimo" title="Ultimo Registro" href="javascript:refrescar('abms/index.php?controlador=expedientes&accion=listar&sentido=ultimo', 'contenidoAjaxPrincipal');">
					<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="14" height="14" align="top" />
				</a>

			</div><!-- FIN DEL PAGINADOR -->

			<div class="p_bnav_contenedor_3bt">
				<?php
				//SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
				if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2)
				{
				?>
					<a id="btAgregar" style="width:30px;" title="Agregar Registro" href="javascript:refrescar('abms/index.php?controlador=expedientes&accion=agregar', 'contenidoAjaxPrincipal');">
						<img id="imgAgregar" src="imagenes/barra/add_16x16.gif" width="16" height="16" />
					</a>
				<?php
				}
				else
				{
				?>
					<a id="btAgregar" style="width:30px;" title="Funci&oacute;n deshabilitada" href="#">
						<img id="imgAgregar" src="imagenes/barra/add_gris_16x16.gif" width="16" height="16"  />
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
				<a id="btPrint" href="#" title="Imprimir el registro seleccionado">
					<img id="imgPrint" src="imagenes/barra/print_16x16.gif" width="16" height="16" />
				</a>
				<?php
				if ( $_SESSION['perfil2'] != 4 )
				{
				?>
					<a id="btPrintEtiq" href="#" title="Imprimir la Etiqueta del Expediente">
						<img id="imgPrintEtiq" src="imagenes/barra/print_etiq_16x16.gif" width="16" height="16" />
					</a>
				<?php
				}
				?>
			</div>

			<?php
			// SI TIENE ACCESO A UN SÓLO SISTEMA SE PERMITE CERRAR LA SESION
			if ( count($_SESSION['accesos']) == 1 )
			{
			?>
				<div class="p_bnav_contenedor_btSalir">
					<a id="btSalir" href="javascript:if (confirm('Desea salir del Sistema?')){ location.href='<?php echo $this->url_salida; ?>'; };" title="Salir del Sistema">
						<img src="imagenes/barra/salir.jpeg" width="58" height="21" />
					</a>
				</div>
			<?php
			}
			?>
		</div>

	    <div class="p_borde_superior"></div>

	    <!-- BUSCADOR POR EXPEDIENTE -->
	    <div class="p_buscador">
		    <form action="abms/index.php" method="post" name="formBuscador" id="formBuscador" class="p_buscador_form">

				<input type="hidden" id="controlador" value="expedientes">
				<input type="hidden" id="pagina" value="<?php echo $filtro['pagina']; ?>">
				<input type="hidden" id="nroFila_elegida" value="">
				<input type="hidden" id="orden" value="<?php echo $_SESSION['ultimo_sentido']; ?>">

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
					<a title="Buscar" href="javascript:buscarExpediente();">
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

	    <div class="p_borde_superior"></div>

	    <!-- SOLAPAS -->
	    <div class="p_solapas_titulos p_buscador_texto">
			<div id="p_solapa_link1" class="p_solapa_link" style="background-color:silver;" onclick="refrescar('abms/index.php?controlador=expedientes&accion=listar', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link1').setStyle('color','#315299');" onmouseOut="$('p_solapa_link1').setStyle('color','#000');">Expedientes</div>
			<div id="p_solapa_link2" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=proyectos&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link2').setStyle('color','#315299');" onmouseOut="$('p_solapa_link2').setStyle('color','black');">Proyectos</div>
			<div id="p_solapa_link3" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=giros&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link3').setStyle('color','#315299');" onmouseOut="$('p_solapa_link3').setStyle('color','black');">Giros</div>
			<div id="p_solapa_link4" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=sanciones&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link4').setStyle('color','#315299');" onmouseOut="$('p_solapa_link4').setStyle('color','black');">Sanciones</div>
			<div id="p_solapa_link5" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=estados&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link5').setStyle('color','#315299');" onmouseOut="$('p_solapa_link5').setStyle('color','black');">Estados</div>
			<div id="p_solapa_link6" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=antecedentes&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link6').setStyle('color','#315299');" onmouseOut="$('p_solapa_link6').setStyle('color','black');">Antecedentes</div>
			<?php
			// SOLO PERFIL 1 Y 2 PUEDEN VERIFICAR LOS PRESTAMOS DEL EXPEDIENTE
			//if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 ) {
			?>
				<div id="p_solapa_link7" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=prestamos&accion=listarEnSolapa&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link7').setStyle('color','#315299');" onmouseOut="$('p_solapa_link7').setStyle('color','#000');">Pr&eacute;stamos</div>
			<?php
			//}
			?>
			<div id="p_solapa_link8" class="p_solapa_link" style="color:silver;" onclick="#">Ruta</div>	<!--  onmouseOver="$('p_solapa_link8').setStyle('color','#315299');" onmouseOut="$('p_solapa_link8').setStyle('color','black');" -->
		</div>

	    <div class="p_borde_superior"></div>

	    <div id="grilla_expedientes" class="ub_listado">
			<!-- ENCABEZADO GRILLA -->
			<table id="tablaListado" class="e_tabla_texto">

				<thead class="e_tabla_titulos">

					<tr style="background-color:silver;">
						<?php
						if ( $_SESSION['perfil2'] == 1 )
							$cant_columnas = 12; // Perfil 1 puede Editar y Eliminar
						elseif ( $_SESSION['perfil2'] == 2 )
							$cant_columnas = 11; // Perfil 2 puede Editar
						else
							$cant_columnas = 10; // Perfil 3 y 4 sólo,pueden visualizar
						?>
						<th colspan="<?php echo $cant_columnas; ?>" style="border:0">&nbsp;</th>
						<th colspan="5" style="border:0;">
							<a href="javascript:verAgregado();" class="e_btAgregadoExpediente">Agregado a</a>
						</th>
					</tr>
					<tr>
						<?php
						if ( $_SESSION['perfil2'] == 1 ) // SÓLO EL PERFIL 1 PUEDE ELIMINAR
							echo '<th class="orden_link" colspan="4">&nbsp;</th>';
						elseif ( $_SESSION['perfil2'] == 2 ) // SÓLO LOS PERFILES 1 Y 2 PUEDEN MODIFICAR
							echo '<th class="orden_link" colspan="3">&nbsp;</th>';
						else
							echo '<th class="orden_link" colspan="2">&nbsp;</th>';
						?>
						<th class="orden_link">A&ntilde;o</th>
						<th class="orden_link">Tipo</th>
						<th class="orden_link">N&uacute;mero</th>
						<th class="orden_link">Cuerpo</th>
						<th class="orden_link">Alcance</th>
						<th class="orden_link">Iniciador</th>
						<th class="orden_link">C&oacute;digo</th>
						<th class="orden_link">Fecha Entrada</th>
						<th class="orden_link">A&ntilde;o</th>
						<th class="orden_link">Tipo</th>
						<th class="orden_link">N&uacute;mero</th>
						<th class="orden_link">Cuerpo</th>
						<th class="orden_link">Alcance</th>
					</tr>
				</thead>
				<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
					<?php
					$cantidad = count($datos);
					for ( $i=0; $i < $cantidad; $i++ ) {
						$dato = &$datos[$i];

						$evento_dobleclick = "";
						//SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN EDITAR
						if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2)
							$evento_dobleclick = "onDblClick=\"javascript:refrescar('abms/index.php?controlador=expedientes&accion=editar&anio=".$dato['anio']."&tipo=".$dato['tipo']."&numero=".$dato['numero']."&cuerpo=".$dato['cuerpo']."&alcance=".$dato['alcance']."&por_btAgregarExped=false', 'contenidoAjaxPrincipal');\"";
					?>
						<tr id="e_fila<?php echo $i; ?>" onmouseover="javascript:resaltarFila(<?php echo $i; ?>);" onmouseout="javascript:no_resaltarFila(<?php echo $i; ?>);" onclick="javascript:marcarFila(<?php echo $i; ?>);cargarBuscador(<?php echo $dato['anio']; ?>, '<?php echo $dato['tipo']; ?>', <?php echo $dato['numero']; ?>, <?php echo $dato['cuerpo']; ?>, <?php echo $dato['alcance']; ?>);cargarAgregado($('i_agregado_anio<?php echo $i; ?>').innerHTML, $('i_agregado_tipo<?php echo $i; ?>').innerHTML, $('i_agregado_numero<?php echo $i; ?>').innerHTML, $('i_agregado_cuerpo<?php echo $i; ?>').innerHTML, $('i_agregado_cuerpo<?php echo $i; ?>').innerHTML);pedirDatos(<?php echo $dato['anio']; ?>, '<?php echo $dato['tipo']; ?>', <?php echo $dato['numero']; ?>, <?php echo $dato['cuerpo']; ?>, <?php echo $dato['alcance']; ?>);" <?php echo $evento_dobleclick; ?> >
							<?php //SOLO EL PERFIL 1 Y 2 PUEDEN MODIFICAR
							if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 ) {
							?>
								<td width="16">
									<a style="width:16px;height:16px;" href="javascript:refrescar('abms/index.php?controlador=expedientes&accion=editar&anio=<?php echo $dato['anio']; ?>&tipo=<?php echo $dato['tipo']; ?>&numero=<?php echo $dato['numero']; ?>&cuerpo=<?php echo $dato['cuerpo']; ?>&alcance=<?php echo $dato['alcance']; ?>&por_btAgregarExped=false', 'contenidoAjaxPrincipal');" title="Editar" >
										<img src="imagenes/b_edit.png" width="14" height="14" />
									</a>
								</td>
							<?php
							}
							// SOLO EL PERFIL 1 PUEDE ELIMINAR
							if ( $_SESSION['perfil2'] == 1 ) {
								if ( $dato['tipo'] == 'E' )
									$titulo_confirm_js = "Desea eliminar el Expediente?";
								elseif ( $dato['tipo'] == 'N' )
									$titulo_confirm_js = "Desea eliminar la Nota?";
								else
									$titulo_confirm_js = "Desea eliminar la Recomendación?";
							?>
								<td width="16">
									<a style="width:16px;height:16px;" href="javascript:if(confirm('<?php echo $titulo_confirm_js; ?>')){refrescar('abms/index.php?controlador=expedientes&accion=eliminar&anio=<?php echo $dato['anio']; ?>&tipo=<?php echo $dato['tipo']; ?>&numero=<?php echo $dato['numero']; ?>&cuerpo=<?php echo $dato['cuerpo']; ?>&alcance=<?php echo $dato['alcance']; ?>&fecha_entrada_expe=<?php echo $dato['fecha_entrada_expe']; ?>', 'contenidoAjaxPrincipal');};" title="Eliminar">
										<img src="imagenes/b_drop.png" width="14" height="14" />
									</a>
								</td>
							<?php
							}
							?>
							<td width="16" align="center">
								<?php echo $this->mostrarEstadoProyecto($dato); ?>
							</td>
							<td width="16" align="center">
								<?php echo $this->mostrarEstadoDigitalizacion($dato); ?>
							</td>
							<td id="i_anio<?php echo $i; ?>" style="width:40px;height:17px;text-align:right;padding-right:3px;"><?php echo $dato['anio']; ?></td>
							<td id="i_tipo<?php echo $i; ?>" style="width:42px;height:17px;text-align:left;padding-left:3px;"><?php echo $dato['tipo']; ?></td>
							<td id="i_numero<?php echo $i; ?>" style="width:50px;height:17px;text-align:right;padding-right:3px;"><?php echo $dato['numero']; ?></td>
							<td id="i_cuerpo<?php echo $i; ?>" style="width:43px;height:17px;text-align:right;padding-right:3px;"><?php echo $dato['cuerpo']; ?></td>
							<td id="i_alcance<?php echo $i; ?>" style="width:42px;height:17px;text-align:right;padding-right:3px;"><?php echo $dato['alcance']; ?></td>
							<td id="i_iniciador_tipo<?php echo $i; ?>" style="width:70px;height:17px;text-align:left;padding-left:3px;"><?php echo $dato['iniciador_tipo']; ?></td>
							<td id="i_iniciador_codigo<?php echo $i; ?>" style="width:70px;height:17px;text-align:left;padding-left:3px;"><?php echo $dato['iniciador_codigo']; ?></td>
							<td id="i_fecha_entrada_expe<?php echo $i; ?>" style="width:80px;height:17px;text-align:center;"><?php echo $this->formatearFecha($dato['fecha_entrada_expe']); ?></td>
							<td id="i_agregado_anio<?php echo $i; ?>" style="width:40px;height:17px;text-align:right;padding-right:3px;"><?php echo ($dato['agregado_anio'] != '') ? $dato['agregado_anio'] : '0' ; ?></td>
							<td id="i_agregado_tipo<?php echo $i; ?>" style="width:42px;height:17px;text-align:left;padding-left:3px;"><?php echo ($dato['agregado_tipo'] != '') ? $dato['agregado_tipo'] : 's/tipo' ; ?></td>
							<td id="i_agregado_numero<?php echo $i; ?>" style="width:50px;height:17px;text-align:right;padding-right:3px;"><?php echo ($dato['agregado_numero'] != '') ? $dato['agregado_numero'] : '0' ; ?></td>
							<td id="i_agregado_cuerpo<?php echo $i; ?>" style="width:43px;height:17px;text-align:right;padding-right:3px;"><?php echo ($dato['agregado_cuerpo'] != '')? $dato['agregado_cuerpo'] : '0'; ?></td>
							<td id="i_agregado_alcance<?php echo $i; ?>" style="width:42px;height:17px;text-align:right;padding-right:3px;"><?php echo ($dato['agregado_alcance'] != '') ? $dato['agregado_alcance'] : '0' ; ?></td>
						</tr>
					<?php
					}
					$posicion_en_el_listado = $i-1; // POR DEFECTO
					if ($filtro['por_teclado']=='arriba'){ $posicion_en_el_listado = $i-1; } // PARA VER LA PAGINA ANTERIOR
					if ($filtro['por_teclado']=='abajo'){ $posicion_en_el_listado = 0; } // PARA VER LA PAGINA SIGUIENTE

					// 04/01/2012: SE GUARDA EN SESION LA CLAVE DEL EXPEDIENTE PARA NO PERDER SU REFERENCIA AL VOLVER DE LAS CONSULTAS Y LISTADOS
					$_SESSION['clave_expediente_referenciado'] = $datos[$posicion_en_el_listado];
					?>
				</tbody>
			</table>
			<!-- SE TIENE UNA REFERENCIA AL ULTIMO REGISTRO DEL LISTADO -->
			<input type="hidden" id="anio_inferior" value="<?php echo $datos[$posicion_en_el_listado]['anio']; ?>">
			<input type="hidden" id="tipo_inferior" value="<?php echo $datos[$posicion_en_el_listado]['tipo']; ?>">
			<input type="hidden" id="numero_inferior" value="<?php echo $datos[$posicion_en_el_listado]['numero']; ?>">
			<input type="hidden" id="cuerpo_inferior" value="<?php echo $datos[$posicion_en_el_listado]['cuerpo']; ?>">
			<input type="hidden" id="alcance_inferior" value="<?php echo $datos[$posicion_en_el_listado]['alcance']; ?>">
			<input type="hidden" id="agregado_anio_inferior" value="<?php echo $datos[$posicion_en_el_listado]['agregado_anio']; ?>">
			<input type="hidden" id="agregado_tipo_inferior" value="<?php echo $datos[$posicion_en_el_listado]['agregado_tipo']; ?>">
			<input type="hidden" id="agregado_numero_inferior" value="<?php echo $datos[$posicion_en_el_listado]['agregado_numero']; ?>">
			<input type="hidden" id="agregado_cuerpo_inferior" value="<?php echo $datos[$posicion_en_el_listado]['agregado_cuerpo']; ?>">
			<input type="hidden" id="agregado_alcance_inferior" value="<?php echo $datos[$posicion_en_el_listado]['agregado_alcance']; ?>">

			<input type="hidden" id="por_teclado" name="por_teclado" value="<?php echo ( isset($filtro['por_teclado']) ) ? $filtro['por_teclado'] : ''; ?>" />
			<input type="hidden" id="por_boton_buscar" name="por_boton_buscar" value="<?php echo ( isset($filtro['por_boton_buscar']) ) ? $filtro['por_boton_buscar'] : ''; ?>" />
		</div>
	    <div class="p_borde_superior"></div>
	    <div id="capa_datos_inferior">
			<!-- AQUI SE VISUALIZAN LOS DATOS DEL EXPEDIENTE SELECCIONADO -->
	    </div>
	    <script type="text/javascript">
	    	var posicion;

			Window.onDomReady(MOOdalBox.init.bind(MOOdalBox));

		    $('f_tipo').value = '<?php echo ($filtro['tipo']) ? $filtro['tipo'] : 'E'; ?>';

			if  ( $('por_teclado').value == "abajo" ) {
				posicion = 0;
				// SE MANTIENE EL SCROLL ARRIBA
				var scroller = new Fx.Scroll($('e_cuerpo_scrolleable'));
			    scroller.toTop();
			} else {
				posicion = '<?php echo $posicion_en_el_listado; ?>';
				// SE BAJA EL SCROLL AL ACTUALIZAR EL LISTADO
				window.setTimeout("bajarScrollListados('e_cuerpo_scrolleable')",3);
			}

			// Si se utilizó el botón Buscar
			if  ( $('por_boton_buscar').value == "si" ) {
				// Se carga la clave del expediente obtenido en los campos del buscador
				cargarBuscador($('i_anio0').innerHTML, $('i_tipo0').innerHTML, $('i_numero0').innerHTML, $('i_cuerpo0').innerHTML, $('i_alcance0').innerHTML);
				// Se visualizan los datos del expediente obtenido (Iniciador, Categoria, Autores, Temas) del expediente seleccionado
				pedirDatos($('i_anio0').innerHTML, $('i_tipo0').innerHTML, $('i_numero0').innerHTML, $('i_cuerpo0').innerHTML, $('i_alcance0').innerHTML);

				if ( $('i_agregado_tipo0').innerHTML == '' )
					$('i_agregado_tipo0').innerHTML = 's/tipo';
				// Se cargan los datos para el botón Agregado en la solapa Expedientes
				cargarAgregado($('i_agregado_anio0').innerHTML, $('i_agregado_tipo0').innerHTML, $('i_agregado_numero0').innerHTML, $('i_agregado_cuerpo0').innerHTML, $('i_agregado_alcance0').innerHTML);
			}
			else // si se recorre la grilla con las teclas del teclado y se cliquea sobre una fila
			{
				// Se carga la clave, del expediente inferior en la grilla, en los campos del buscador
				cargarBuscador($('anio_inferior').value, $('tipo_inferior').value, $('numero_inferior').value, $('cuerpo_inferior').value, $('alcance_inferior').value);
				// Se visualizan los datos, del expediente inferior en la grilla, (Iniciador, Categoria, Autores, Temas) del expediente seleccionado
				pedirDatos($('anio_inferior').value, $('tipo_inferior').value, $('numero_inferior').value, $('cuerpo_inferior').value, $('alcance_inferior').value);

				if ( $('agregado_tipo_inferior').value == '' )
					$('agregado_tipo_inferior').value = 's/tipo';
				// Se cargan los datos para el botón Agregado en la solapa Expedientes
				cargarAgregado($('agregado_anio_inferior').value, $('agregado_tipo_inferior').value, $('agregado_numero_inferior').value, $('agregado_cuerpo_inferior').value, $('agregado_alcance_inferior').value);
			}

			function buscarExpediente() {
				refrescar('abms/index.php?controlador=expedientes&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&sentido=siguiente&por_boton_buscar=si', 'contenidoAjaxPrincipal');
			}

			// SE SETEA EL HREF DE 'btPrint' PARA GENERAR LA FICHA EN PDF
			$('btPrint').addEvent('click', function(){
				$('btPrint').setProperty('href','consultas/index.php?controlador=ficha&accion=generar_ficha&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'');
				$('btPrint').setProperty('target','_blank');
			});

			// SI SE MUESTRA EL BOTON PARA LA ETIQUETA
			if ( $('btPrintEtiq') ) {
				// SE SETEA EL HREF DE 'btPrintEtiq' PARA GENERAR LA ETIQUETA EN PDF
				$('btPrintEtiq').addEvent('click', function(){
					$('btPrintEtiq').setProperty('href','consultas/index.php?controlador=ficha&accion=generarEtiqueta_ficha&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'');
					$('btPrintEtiq').setProperty('target','_blank');
				});
			}

			// SE SETEA LA FILA ACTUAL
			$('nroFila_elegida').value = ( $('por_boton_buscar').value == "si" ) ? 0 : posicion;

			// Al cliquear SE MARCA EL REGISTRO DEL LISTADO
			$('i_anio'+posicion).addEvent('click', function() {
				// Si se utilizó el botón Buscar
				if  ( $('por_boton_buscar').value == "si" )
					$('e_fila0').setStyles({'background-color':'#76A0CD'});
				else
					$('e_fila'+posicion).setStyles({'background-color':'#76A0CD'});

				//setfocus($('i_anio'+posicion));
				$('e_cuerpo_scrolleable').scroll = 'yes';
			});

			$('i_anio'+posicion).fireEvent('click');

			if ( $('msl_btffecha_comision') )
				$('msl_btffecha_comision').setStyle('visibility', 'hidden');

			$('f_anio').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
						buscarExpediente();
				}
			});

			$('f_numero').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
						buscarExpediente();
				}
			});

			$('f_cuerpo').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
						buscarExpediente();
				}
			});

			$('f_alcance').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
						buscarExpediente();
				}
			});
		</script>
    	<?php
    }

    /**
     * SE ARMA EL NOMBRE DEL DOCUMENTO PARA EL RESPECTIVO EXPEDIENTE
     */
    private function obtenerNomenclatura($info) {

		$anio_corto = substr($info['anio'], -2);
		$tipo       = $info['tipo'];
		$aux_numero = 100000+$info['numero'];
		$numero     = substr($aux_numero, -5);

	    return $anio_corto.$tipo.$numero;
    }

    /**
     * Modificado el 20/02/2019
     * Se muestra el estado del Proyecto de un expediente determinado
     * @param  [type] $info [description]
     * @return [type]       [description]
     */
    public function mostrarEstadoProyecto($info) {

    	$nombre_codificado = $this->obtenerNomenclatura($info);

    	switch ($info['estado_doc']) {

    		// Para Cargar
	    	case '1':
	    		// 30/10/2019 XXXX
	    		// Se agregó a la condición que el usuario con Perfil de acceso web
	    		// pueda visualizar que la Digitalización está para cargar (amarillo)
	    		// para usuarios de Bloque ó usuarios de acceso web (concejomdp.gov.ar/sgl)
	    		if ( $_SESSION['perfil2'] == 3 || $_SESSION['perfil2'] == 4 )
	    			$marca_estado_proyecto = '<span class="celda_documento color_documento_para_cargar" title="Proyecto Para Cargar">P</span>';
	    		// Para usuarios Administrativos
	    		elseif ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 ) {
	    			$marca_estado_proyecto  = '<a href="/sgl/expedientes/proyectos/temporal/'.$nombre_codificado.'.doc" target="_blank" title="Proyecto Para Cargar">';
	    			$marca_estado_proyecto .= '<span class="celda_documento color_documento_para_cargar">P</span>';
	    			$marca_estado_proyecto .= '</a>';
	    		}
	    		break;

	    	// Cargado
	    	case '2':
	    		$rel_para_modal = '';

	    		// Se verifica que posea el "original.doc"
				if ( file_exists("../proyectos/".$info['anio'].'/'.$nombre_codificado."/original.doc") )
					$link = "/sgl/expedientes/proyectos/".$info['anio']."/".$nombre_codificado."/original.doc";

				// Se verifica que posea el "original.doc"
				elseif ( file_exists("../proyectos/".$info['anio'].'/'.$nombre_codificado."/deforma.doc") )
					$link = "/sgl/expedientes/proyectos/".$info['anio']."/".$nombre_codificado."/deforma.doc";

				// Se muestra el contenido del directorio
				else {
					$link = "tareas/index.php?controlador=cargar_proyecto&accion=mostrar_contenido_dir&pftp_anio=".$info['anio']."&pftp_tipo=".$info['tipo']."&pftp_numero=".$info['numero'];
					$rel_para_modal = 'rel="moodalbox 430 170"';
				}

	    		$marca_estado_proyecto  = '<a href="'.$link.'" '.$rel_para_modal.' target="_blank" title="Proyecto Cargado">';
	    		$marca_estado_proyecto .= '<span class="celda_documento color_documento_cargado">P</span>';
	    		$marca_estado_proyecto .= '</a>';
	    		break;

	    	// Sin cargar ó por defecto
	    	case '3':
	    	default:
	    		$marca_estado_proyecto = '<span class="celda_documento color_documento_sin_cargar" title="Proyecto Sin Cargar">P</span>';
	    		break;
		}

		return $marca_estado_proyecto;
    }

    /**
     * Se obtiene el nombre real de un documento determinado por su nombre codificado
     * @param  [type] $nombre [description]
     * @return [type]         [description]
     */
    public function obtenerNombreRealDocumento($directorio, $nombre) {

    	// Se obtiene un array de los archivos que contiene el directorio respectivo
	    $archivos = @scandir($directorio);

	    $diccionario = array();

		foreach ($archivos as $a)
			$diccionario[strtolower($a)] = $a;

		return $diccionario[strtolower($nombre.'.pdf')];
    }

    /**
     * Se muestra el estado de la digitalización de un expediente determinado
     * @param  [type] $info [description]
     * @return [type]       [description]
     */
    public function mostrarEstadoDigitalizacion($info) {

    	$nombre = $this->obtenerNomenclatura($info);

    	switch ($info['estado_digitalizacion']) {
	    	case '1':
	    		// 30/10/2019 XXXX
	    		// Se agregó a la condición que el usuario con Perfil de acceso web
	    		// pueda visualizar que la Digitalización está para cargar (amarillo)
	    		// para usuarios de Bloque ó usuarios de acceso web (concejomdp.gov.ar/sgl)
	    		if ( $_SESSION['perfil2'] == 3 || $_SESSION['perfil2'] == 4 )
	    			$marca_estado_digitalizacion = '<span class="celda_documento color_documento_para_cargar" title="Digitalizaci&oacute;n Para Cargar">D</span>';
	    		// Para usuarios Administrativos
	    		elseif ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 ) {

					$nombre_real = $this->obtenerNombreRealDocumento("../proyectos/digital/", $nombre);

	    			$marca_estado_digitalizacion  = '<a href="/sgl/expedientes/proyectos/digital/'.$nombre_real.'" target="_blank" title="Digitalizaci&oacute;n Para Cargar">';
	    			$marca_estado_digitalizacion .= '<span class="celda_documento color_documento_para_cargar">D</span>';
	    			$marca_estado_digitalizacion .= '</a>';
	    		}
	    		break;
	    	case '2':
	    		// 2020/05/13 pduthey
	    		// Se muestra:
	    		// D= Digitalización Cargada
	    		// DC= Digitalización Completa
	    		// El cambio de Cargada a Completa se realiza en la edición del Expediente/Nota

	    		$title = ($info['digi_completa'] == '0') ? 'Digitalizaci&oacute;n Cargada' : 'Digitalizaci&oacute;n Completa';

	    		$marca_estado_digitalizacion  = '<a href="/sgl/expedientes/proyectos/'.$info['anio'].'/'.$nombre.'/'.$nombre.'.pdf" target="_blank" title="'.$title.'">';
	    		$marca_estado_digitalizacion .= '<span class="celda_documento color_documento_cargado">';
	    		$marca_estado_digitalizacion .= ($info['digi_completa'] == '0') ? 'D' : 'DC';
	    		$marca_estado_digitalizacion .= '</span>';
	    		$marca_estado_digitalizacion .= '</a>';
	    		break;
	    	case '3':
	    	default:
	    		$marca_estado_digitalizacion = '<span class="celda_documento color_documento_sin_cargar" title="Digitalizaci&oacute;n Sin Cargar">D</span>';
	    		break;
		}

		return $marca_estado_digitalizacion;
    }

    public function editar($datos = null, $listadoAutores = null, $listadoTemas = null, $mensaje = '', $lista_completa_lugares = null, $lista_completa_categorias = null, $lista_completa_temas = null, $filtro = null, $tipo_mensaje = '')
	{
	    // MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);

		$_SESSION['mensaje'] = "";
		$_SESSION['tipo_mensaje'] = "";

	    if ( $_SESSION['campos_habilitados'] == false )
	    {
		    // SE DESHABILITAN LOS CAMPOS
		    $estado = 'readonly="readonly"';
		    // SE HABILITA LA CLAVE
		    $estado_para_la_clave = '';
	    }
	    else
	    {
		    // SE HABILITAN LOS CAMPOS
		    $estado = '';
		    // SE DESHABILITA LA CLAVE
		    $estado_para_la_clave = 'readonly="readonly"';
	    }

	    if ( $_SESSION['perfil2'] == 4 )
	    {
			// SE DESHABILITAN LOS CAMPOS
		    $estado = 'readonly="readonly"';
		    // SE HABILITA LA CLAVE
		    $estado_para_la_clave = '';
		}
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
		    </div>

		    <div class="p_bnav_contenedor_3bt">
			    <a id="btAgregar" style="width:30px;" title="Funci&oacute;n deshabilitada" href="#">
				    <img id="imgAgregar" src="imagenes/barra/add_gris_16x16.gif" width="16" height="16" />
			    </a>
		    </div>
		    <div class="p_bnav_contenedor_2bt">
				<?php
				//SOLO USUARIOS DE PERFIL 1 Y 2 PUEDEN EDITAR EL REGISTRO
				if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 )
				{
					if (empty($estado))
					{
				?>
						<a id="btGuardar" title="Aceptar los cambios realizados" href="javascript:validarExpediente();" tabindex="24">
							<img id="imgGuardar" src="imagenes/barra/ok_16x16.gif" width="16" height="16" />
						</a>
				<?php
					}
				}
				else
				{
				?>
					<a id="btGuardar" style="width:30px;" title="Funci&oacute;n deshabilitada" href="#">
						<img id="imgAgregar" src="imagenes/barra/ok_gris_16x16.gif" width="16" height="16" />
					</a>
				<?php
				}

				if ($_SESSION['agregado_previamente'])
				{
					$rutaCancelar  = "javascript:refrescar('abms/index.php?controlador=expedientes&accion=eliminar";
					$rutaCancelar .= "&anio=".$datos[0]['anio'];
					$rutaCancelar .= "&tipo=".$datos[0]['tipo'];
					$rutaCancelar .= "&numero=".$datos[0]['numero'];
					$rutaCancelar .= "&cuerpo=".$datos[0]['cuerpo'];
					$rutaCancelar .= "&alcance=".$datos[0]['alcance'];
					$rutaCancelar .= "&por_boton_buscar=si', 'contenidoAjaxPrincipal');";
				}
				else
				{
					$rutaCancelar  = "index.php?anio=".$datos[0]['anio'];
					$rutaCancelar .= "&tipo=".$datos[0]['tipo'];
					$rutaCancelar .= "&numero=".$datos[0]['numero'];
					$rutaCancelar .= "&cuerpo=".$datos[0]['cuerpo'];
					$rutaCancelar .= "&alcance=".$datos[0]['alcance'];
					$rutaCancelar .= "&sentido=siguiente";
					$rutaCancelar .= "&por_boton_buscar=si";
				}
				?>
			    <a id="btCancelar" title="Cancelar los cambios realizados" href="<?php echo $rutaCancelar; ?>" >
				    <img id="imgCancelar" src="imagenes/barra/error_16x16.gif" width="16" height="16" />
			    </a>
		    </div>
		    <div class="p_bnav_contenedor_2bt">
			    <a id="btPrint" title="Funci&oacute;n deshabilitada" href="#">
				    <img id="imgPrint" src="imagenes/barra/print_gris_16x16.gif" width="16" height="16" />
			    </a>
			    <a id="btPrintEtiq" title="Funci&oacute;n deshabilitada" href="#">
				    <img id="imgPrintEtiq" src="imagenes/barra/print_etiq_gris_16x16.gif" width="16" height="16" />
			    </a>
		    </div>
		</div>

	    <div class="p_borde_superior"></div>

		<form action="abms/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

			<input type="hidden" name="controlador" value="<?php echo $this->controlador; ?>" />

			<input type="hidden" name="accion" id="accion" value="<?php echo ($datos[0]['tipo']) ? 'modificar' : 'insertar'; ?>" />

			<input type="hidden" name="marca_comision" id="marca_comision" value="<?php if (isset($datos[0]['marca_comision'])){ echo $datos[0]['marca_comision'];}else{ echo '';} ?>" />

			<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />

			<!-- DATOS SUPERIOR (Año, Tipo, Numero, Cuerpo, Alcance) -->
			<div class="e_datos_sup">
				<div class="e_datos_label e_datos_texto">A&ntilde;o:</div>
				<div class="e_datos_valor">
					<input type="text" name="anio" id="anio" value="<?php echo ($filtro['anio']) ? $filtro['anio'] : date("Y"); ?>" class="e_datos_texto" tabindex="1" onKeyPress="return soloEnteros(event);" size="3" maxlength="4" style="border:0;" <?php echo $estado_para_la_clave; ?> />
				</div>
				<div class="e_datos_label e_datos_texto">Tipo:</div>
				<div class="e_datos_valor">
					<?php
					if ($_SESSION['campos_habilitados'] == true)
					{
					?>
						<input type="text" name="tipo" id="tipo" value="<?php echo $filtro['tipo']; ?>" class="e_datos_texto" size="1" maxlength="1" style="border:0;" tabindex="2" readonly="readonly" />
					<?php
					}
					else
					{
					?>
						<select name="tipo" id="tipo" class="e_datos_texto <?php if (!empty($estado_para_la_clave)){echo 'e_select_deshabilitado';} ?>" size="1" style="border:0;" tabindex="2" <?php if (!empty($estado_para_la_clave)){echo 'disabled';} ?> >
							<option value="-1">...</option>
							<option value="E">E</option>
							<option value="N">N</option>
							<option value="R">R</option>
						</select>
					<?php
					}
					?>
				</div>
				<div class="e_datos_label e_datos_texto">N&uacute;mero:</div>
				<div class="e_datos_valor e_datos_texto">
					<input type="text" id="numero" name="numero" value="<?php echo $filtro['numero']; ?>" class="e_datos_texto" onKeyPress="return soloEnteros(event);" size="5" maxlength="10" style="border:0;" tabindex="3" <?php echo $estado_para_la_clave; ?> />
				</div>
				<div class="e_datos_label e_datos_texto">Cuerpo:</div>
				<div class="e_datos_valor e_datos_texto">
					<input type="text" id="cuerpo" name="cuerpo" value="<?php if (isset($filtro['cuerpo'])){ echo $filtro['cuerpo'];}else{ echo '0';} ?>" class="e_datos_texto" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" style="border:0;" tabindex="4" <?php echo $estado_para_la_clave; ?> />
				</div>
				<div class="e_datos_label e_datos_texto">Alcance:</div>
				<div class="e_datos_valor e_datos_texto">
					<input type="text" id="alcance" name="alcance" value="<?php if (isset($filtro['alcance'])){ echo $filtro['alcance'];}else{ echo '0';} ?>" class="e_datos_texto" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" style="border:0;" tabindex="5" <?php echo $estado_para_la_clave; ?> />
				</div>
				<?php
				if (!isset($datos[0]['tipo']))
				{
				?>
					<div class="btHabilitarDatos">
						<input type="button" id="btHabilitarDatos" value="Ingresar" tabindex="6" />
					</div>
				<?php
				}
				?>
			</div>

			<div class="p_borde_superior"></div>

			<div class="e_solapas_titulos p_buscador_texto">
				<div class="e_solapa_al_editar" style="color:#000;background-color:silver">Expedientes</div>
				<div class="e_solapa_al_editar">Proyectos</div>
				<div class="e_solapa_al_editar">Giros</div>
				<div class="e_solapa_al_editar">Sanciones</div>
				<div class="e_solapa_al_editar">Estados</div>
				<div class="e_solapa_al_editar">Antecedentes</div>
				<div class="e_solapa_al_editar">Pr&eacute;stamos</div>
				<div class="e_solapa_al_editar">Ruta</div>
			</div>
			<div class="e_edit_gral um_texto_datos" >
				<!-- DATOS (Fecha de Ingreso, Iniciador, Categoria, Caratula) -->
				<div class="e_parte_uno">
					<div class="e_edicion_expediente">
						<div class="e_parte_uno_izq">
							<div class="e_label">Fecha de Entrada:</div>
							<div class="e_margen_label"></div>
							<div class="e_label">Iniciador:</div>
							<div class="e_margen_label"></div>
							<div class="e_label">Categor&iacute;a:</div>
							<div class="e_margen_label"></div>
							<div class="e_label">Car&aacute;tula:</div>
						</div>
						<div class="e_parte_uno_der">
							<!-- FECHA ENTRADA EXPEDIENTE -->
							<div class="e_valores">
								<input type="text" name="fecha_entrada_expe" id="fecha_entrada_expe" value="<?php echo ($datos[0]['fecha_entrada_expe']) ? $this->formatearFecha($datos[0]['fecha_entrada_expe']) : date("d/m/Y"); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="9" maxlength="10" tabindex="7" <?php if (!empty($estado)){echo 'disabled';} ?> />
								<?php
								if ( empty($estado) )
								{
								?>
									<input type="image" id="expediente_fecha" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha" width="16" height="16" tabindex="8">
								<?php
								}
								?>
							</div>
							<div class="e_margen_label"></div>
							<!-- INICIADOR -->
							<div class="e_valores">
								<select name="iniciador_tipo" id="iniciador_tipo" tabindex="9" onchange="$('lupaModalIniciador').setProperty('href','abms/index.php?controlador=lugares&accion=listarModalIniciador&iniciador_tipo='+$('iniciador_tipo').value+'');" class="e_cboAutor e_datos_texto <?php if (!empty($estado)){echo 'e_select_deshabilitado';} ?>" <?php if (!empty($estado)){echo 'disabled';} ?> >
									<option value="G" >G&nbsp;-&nbsp;Grupo</option>
									<option value="V" >V&nbsp;-&nbsp;Varios</option>
								</select>
								&nbsp;
								<input type="text" name="iniciador_codigo" id="iniciador_codigo" value="<?php echo ($datos[0]['observaciones_expe'] != 'inicializado') ? $datos[0]['iniciador_codigo'] : ''; ?>" size="5" tabindex="10" <?php echo $estado; ?>  />
								&nbsp;
								<input type="text" name="iniciador_descripcion" id="iniciador_descripcion" class="e_ancho_datos_iniciador_descripcion" value="<?php echo ($datos[0]['observaciones_expe'] != 'inicializado') ? $datos[0]['iniciador_descripcion'] : ''; ?>" maxlength="60" readonly="readonly" />
								&nbsp;
								<?php
								if ( empty($estado) )
								{
								?>
									<a id="lupaModalIniciador" href="abms/index.php?controlador=lugares&accion=listarModalIniciador&iniciador_tipo=G" rel="moodalbox 550 350" title="Buscar Iniciador"><img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" /></a>
								<?php
								}
								else
								{
								?>
									<a href="#" title="Buscar Iniciador"><img src="imagenes/zoom_gris_16x16.gif" width="16" height="16" align="top" /></a>
								<?php
								}
								?>
							</div>
							<div class="e_margen_label"></div>
							<!-- CATEGORIA -->
							<div class="e_valores">
								<input type="hidden" name="id_codcategoria" id="id_codcategoria" value="<?php echo ($datos[0]['id_codcategoria']) ? $datos[0]['id_codcategoria'] : ''; ?>" <?php echo $estado; ?> />
								<input type="text" name="codigo_categoria" id="codigo_categoria" value="<?php echo ($datos[0]['observaciones_expe'] != 'inicializado') ? $datos[0]['codigo_categoria'] : ''; ?>" size="5" tabindex="11" <?php echo $estado; ?> />
								&nbsp;
								<input type="text" name="descripcion_categoria" id="descripcion_categoria" class="e_valores_descripcion_categoria" value="<?php echo ($datos[0]['observaciones_expe'] != 'inicializado') ? $datos[0]['descripcion_categoria'] : ''; ?>" maxlength="60" readonly="readonly" />
								&nbsp;
								<?php
								if (empty($estado))
								{
								?>
									<a href="abms/index.php?controlador=categorias&accion=listarModal" rel="moodalbox 550 350" title="Buscar Categor&iacute;a"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
								<?php
								}
								else
								{
								?>
									<a href="#" title="Buscar Categor&iacute;a"><img src="imagenes/zoom_gris_16x16.gif" width="16" height="16" /></a>
								<?php
								}
								?>
							</div>
							<div class="e_margen_label"></div>
							<!-- CARATULA -->
							<div class="e_valores">
								<input type="text" name="caratula" id="caratula" value="<?php echo ($datos[0]['caratula']) ? $datos[0]['caratula'] : ''; ?>" size="67" maxlength="60" tabindex="12" <?php echo $estado; ?> />
							</div>
						</div>
					</div>
				</div>

				<div class="e_parte_dos">
					<!-- LISTADO DE TEMAS -->
					<div class="e_parte_dos_izq">
						<div class="e_label_izquierda">Temas</div>
						<div class="e_valores">
							<input type="hidden" name="id_codtema" id="id_codtema" value="<?php if (isset($listadoTemas[0]['id_codtema'])){ echo $listadoTemas[0]['id_codtema'];}else{ echo '';} ?>" <?php echo $estado; ?> />
							&nbsp;&nbsp;&nbsp;
							<input type="text" name="codigo_tema" id="codigo_tema" value="" size="3" tabindex="13" <?php echo $estado; ?> />

							<input type="text" name="descripcion_tema" id="descripcion_tema" class="e_valores_descripcion_tema" value="" maxlength="33" readonly="readonly" />
							<?php
							if (empty($estado))
							{
								$href_buscar = "abms/index.php?controlador=codtemas&accion=listarModal";
								$rel = "moodalbox";
								$src_imagen_buscar = "imagenes/zoom_16x16.gif";
								$href_agregar = "javascript:cargarArrayTemasJS($('anio').value, $('tipo').value, $('numero').value, $('cuerpo').value, $('alcance').value, $('id_codtema').value, $('codigo_tema').value, $('descripcion_tema').value);";
								$src_imagen_agregar = "imagenes/barra/add_16x16.gif";
							}
							else
							{
								$href_buscar = "#";
								$rel = "";
								$src_imagen_buscar = "imagenes/zoom_gris_16x16.gif";
								$href_agregar = "#";
								$src_imagen_agregar = "imagenes/barra/add_gris_16x16.gif";
							}
							?>
							<a href="<?php echo $href_buscar; ?>" rel="<?php echo $rel; ?>" title="Buscar Tema">
								<img src="<?php echo $src_imagen_buscar; ?>" width="16" height="16" align="top" />
							</a>
							<a id="btAgregarTema" href="<?php echo $href_agregar; ?>" title="Agregar el Tema al listado" tabindex="14">
								<img id="imgBtAgregarTema" src="<?php echo $src_imagen_agregar; ?>" width="16" height="16" align="top" />
							</a>
						</div>
						<div id="e_lista_temas">
							<script type="text/javascript">
								// AQUI SE MUESTRA EL LISTADO DE LOS TEMAS DEL EXPEDIENTE
								<?php
								$cantidadT = count($listadoTemas);
								for ($t=0; $t < $cantidadT; $t++)
								{
									$tema = &$listadoTemas[$t];
								?>
									cargarArrayTemasJS(<?php echo $tema['anio']; ?>, '<?php echo $tema['tipo']; ?>', <?php echo $tema['numero']; ?>, <?php echo $tema['cuerpo']; ?>, <?php echo $tema['alcance']; ?>, <?php echo $tema['id_codtema']; ?>, '<?php echo $tema['codigo_tema']; ?>', '<?php echo $tema['descripcion_tema']; ?>');
								<?php
								}
								?>
							</script>
						</div>
					</div>
					<!-- LISTADO DE AUTORES -->
					<div class="e_parte_dos_der">
						<div class="e_label_izquierda">Autores</div>
						<div class="e_valores">
							<div class="e_combo_autor_tipo">
								<select id="autor_tipo" name="autor_tipo" class="e_cboAutor e_datos_texto <?php if(!empty($estado)){echo 'e_select_deshabilitado';} ?>" tabindex="15" <?php if(!empty($estado)){echo 'disabled';} ?> >
									<option value="C">C&nbsp;&nbsp;Comisi&oacute;n</option>
									<option value="G">G&nbsp;-&nbsp;Grupo</option>
									<option value="V">V&nbsp;-&nbsp;Varios</option>
								</select>
							</div>
							<div class="e_margen_datos_autor"></div>
							<div class="e_datos_autor">
								<input type="text" name="autor_codigo" id="autor_codigo" value="" size="5" tabindex="16" <?php echo $estado; ?> />

								<input type="text" name="autor_descripcion" id="autor_descripcion" class="e_valores_autor_descripcion" value="" maxlength="33" readonly="readonly" />
								<?php
								if (empty($estado))
								{
									$tipo_autor = ($listadoAutores[0]['autor_tipo']) ? $listadoAutores[0]['autor_tipo'] : 'C';
									$href_buscar = "abms/index.php?controlador=autores&accion=listarModal&autor_tipo=".$tipo_autor."";
									$rel = "moodalbox";
									$src_imagen_buscar = "imagenes/zoom_16x16.gif";
									$href_agregar = "javascript:cargarArrayAutoresJS($('anio').value, $('tipo').value, $('numero').value, $('cuerpo').value, $('alcance').value, $('autor_tipo').value, $('autor_codigo').value, $('autor_descripcion').value);";
									$src_imagen_agregar = "imagenes/barra/add_16x16.gif";
								}
								else
								{
									$href_buscar = "#";
									$rel = "";
									$src_imagen_buscar = "imagenes/zoom_gris_16x16.gif";
									$href_agregar = "#";
									$src_imagen_agregar = "imagenes/barra/add_gris_16x16.gif";
								}
								?>
								<a id="lupaModalAutor" href="<?php echo $href_buscar; ?>" rel="<?php echo $rel; ?>" title="Buscar Autor">
									<img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" />
								</a>
								<a id="btAgregarAutor" href="<?php echo $href_agregar; ?>" title="Agregar el Autor al listado" tabindex="17">
									<img id="imgBtAgregarAutor" src="<?php echo $src_imagen_agregar; ?>" width="16" height="16" align="top" />
								</a>
							</div>
						</div>
						<div id="e_lista_autores">
							<script type="text/javascript">
								// AQUI SE MUESTRA EL LISTADO DE LOS AUTORES DEL EXPEDIENTE
								<?php
								$cantidadA = count($listadoAutores);
								for ($a=0; $a < $cantidadA; $a++)
								{
									$autor = &$listadoAutores[$a];
								?>
									cargarArrayAutoresJS(<?php echo $autor['anio']; ?>, '<?php echo $autor['tipo']; ?>', <?php echo $autor['numero']; ?>, <?php echo $autor['cuerpo']; ?>, <?php echo $autor['alcance']; ?>, '<?php echo $autor['autor_tipo']; ?>', '<?php echo $autor['autor_codigo']; ?>', '<?php echo $autor['autor_descripcion']; ?>');
								<?php
								}
								?>
							</script>
						</div>
					</div>
				</div>

				<!-- AGREGADO -->
				<div class="e_parte_tres">
					<div class="e_parte_tres_izq">
						<div class="e_label">Agregado a Expediente:</div>
						<div class="e_label"><br>Observaciones:</div>
					</div>
					<div class="e_parte_tres_der">
						<div class="e_valores_agregados">
							<div class="e_agregado_anio">
								<input type="text" name="agregado_anio" id="agregado_anio" value="<?php echo ($datos[0]['agregado_anio']) ? $datos[0]['agregado_anio'] : ''; ?>" onKeyPress="return soloEnteros(event);" style="width:40px;" maxlength="4" tabindex="18" <?php echo $estado; ?> />
							</div>
							<div class="e_agregado_margen"></div>
							<div class="e_agregado_tipo">
								<select name="agregado_tipo" id="agregado_tipo" class="e_cboTipo e_datos_texto <?php if (!empty($estado)){echo 'e_select_deshabilitado';} ?>" style="width:90px;text-align:left;" tabindex="19" <?php if (!empty($estado)){echo 'disabled';} ?> >
									<option value="0" >::&nbsp;sin tipo&nbsp;::</option>
									<option value="E" selected >E&nbsp;&nbsp;Expediente</option>
									<option value="N">N&nbsp;&nbsp;Nota</option>
								</select>
							</div>
							<div class="e_agregado_margen"></div>
							<div class="e_agregado_nro_cpo_alc">
								<input type="text" name="agregado_numero" id="agregado_numero" value="<?php echo ($datos[0]['agregado_numero']) ? $datos[0]['agregado_numero'] : ''; ?>" tabindex="20" onKeyPress="return soloEnteros(event);" style="width:40px;" maxlength="10" <?php echo $estado; ?> />
								&nbsp;
								<input type="text" name="agregado_cuerpo" id="agregado_cuerpo" value="<?php echo ($datos[0]['agregado_cuerpo']) ? $datos[0]['agregado_cuerpo'] : '0'; ?>" tabindex="21" onKeyPress="return soloEnteros(event);" style="width:20px;" <?php echo $estado; ?> />
								&nbsp;
								<input type="text" name="agregado_alcance" id="agregado_alcance" value="<?php echo ($datos[0]['agregado_alcance']) ? $datos[0]['agregado_alcance'] : '0'; ?>" tabindex="22" onKeyPress="return soloEnteros(event);" style="width:20px;" <?php echo $estado; ?> />
							</div>
						</div>
						<div class="e_observaciones_expe">
							<textarea name="observaciones_expe" id="observaciones_expe" rows="2" cols="5" style="width:480px;" tabindex="23" <?php echo $estado; ?>><?php if($datos[0]['observaciones_expe'] != 'inicializado'){ echo $datos[0]['observaciones_expe'];}else{ echo '';} ?></textarea>
						</div>
					</div>
				</div>

				<?php
				// 2020/05/07 XXXX
				// Se setea de Digitalización Parcial a Digitalización Completa
				// siempre y cuando se encuentre cargada la Digitalización
				if ($datos[0]['estado_digitalizacion'] == 2) {
				?>
					<div class="e_parte_cuatro">
						<div class="e_parte_cuatro_izq">
							<div class="e_label">¿Digitalizaci&oacute;n Completa?</div>
						</div>
						<div class="e_parte_cuatro_der">
							<div class="e_valores">
								<input type="checkbox" name="digi_completa" <?php echo ($datos[0]['digi_completa'] == 1) ? 'checked' : ''; ?> />
							</div>
						</div>
					</div>
				<?php
				}
				?>
			</div><!-- FIN DE um_texto_datos -->
		</form>
	    <script>
			// PARA LAS VENTANAS MODALES (inicializa el objeto MOOdalBox)
		    Window.onDomReady(MOOdalBox.init.bind(MOOdalBox));

		    //CALENDARIO PARA LA FECHA DESDE
		    var cal = new Zapatec.Calendar.setup({
			    inputField	:"fecha_entrada_expe",
			    ifFormat	:"%d/%m/%Y",
			    button		:"expediente_fecha",
			    showsTime	:false
		    });

		    <?php
		    // SE CARGA UN VECTOR DE CATEGORIAS PARA EL AUTOCOMPLETADO
		    if ( $lista_completa_categorias != null )
		    {
		    ?>
			    var cantidadCategorias = <?php echo count($lista_completa_categorias); ?>;
			    var categorias = new Array(cantidadCategorias);
		    <?php
			    $cant_categorias = count($lista_completa_categorias);
			    for ($c=0; $c < $cant_categorias; $c++)
			    {
			?>
				    categorias[<?php echo $lista_completa_categorias[$c]['id_codcategoria']; ?>] = {
									    id_codcategoria:"<?php echo $lista_completa_categorias[$c]['id_codcategoria']; ?>",
									    codigo_categoria:"<?php echo $lista_completa_categorias[$c]['codigo_categoria']; ?>",
									    descripcion_categoria:"<?php echo $lista_completa_categorias[$c]['descripcion_categoria']; ?>"
								      };
		    <?php
			    }
		    }

		    // SE CARGA UN VECTOR DE TEMAS PARA EL AUTOCOMPLETADO
		    if ( $lista_completa_temas != null )
		    {
			?>
			    var cantidadTemas = <?php echo count($lista_completa_temas); ?>;
			    var temas = new Array(cantidadTemas);
			<?php
			    $cant_temas = count($lista_completa_temas);
			    for ($i=0; $i < $cant_temas; $i++)
			    {
			?>
				    temas[<?php echo $lista_completa_temas[$i]['id_codtema']; ?>] = {
								    id_codtema:"<?php echo $lista_completa_temas[$i]['id_codtema']; ?>",
								    codigo_tema:"<?php echo $lista_completa_temas[$i]['codigo_tema']; ?>",
								    descripcion_tema:"<?php echo $lista_completa_temas[$i]['descripcion_tema']; ?>"
								  };
		    <?php
			    }
		    }
		    ?>

		    $('iniciador_tipo').addEvent('change', function()
		    {
			    $('iniciador_codigo').value = '';
			    $('iniciador_descripcion').value = '';
			    $('lupaModalIniciador').setProperty('href','abms/index.php?controlador=lugares&accion=listarModalIniciador&iniciador_tipo='+$('iniciador_tipo').value+'');
			    setfocus('iniciador_codigo');
		    });

		    $('iniciador_codigo').addEvent('keyup', function()
			{
			    if ( $('iniciador_codigo').value != '' )
			    {
				    //Se envia una peticion
				    //	SE CAMBIO 'buscarNombreIniciador' POR 'buscarCodigoNombreIniciador' EN EL CONTROLADOR PARA OBTENER EL codigo DEL INICIADOR
				    var miJSON = new Json.Remote('abms/index.php?controlador=expedientes&accion=buscarCodigoNombreIniciador&iniciador_tipo='+$('iniciador_tipo').value+'&iniciador_codigo='+$('iniciador_codigo').value+'',
				    {
					    //la peticion devolvera un objeto el cual llegara como parametro en el evento onComplete
					    onComplete: function(objeto)
					    {
						    if ( objeto.codigo != '' )
						    {
							    $('iniciador_codigo').value = objeto.codigo;//AHORA SE MUESTRA EL codigo REAL
							    $('iniciador_descripcion').value = objeto.descripcion;
						    }
						    else
							{
								$('iniciador_descripcion').value = "";
							}
					    }
				    });
				    miJSON.send();
			    }
			    else
			    {
					$('iniciador_descripcion').value = "";
				}
		    });

		    // AL TIPEAR UN CODIGO DE CATEGORIA
		    $('codigo_categoria').addEvent('keyup', function()
		    {
				if ( $('codigo_categoria').value != '' )
				{
					if ( categorias[$('codigo_categoria').value] != undefined )
					{
						$('id_codcategoria').value = categorias[$('codigo_categoria').value].id_codcategoria;
						$('descripcion_categoria').value = categorias[$('codigo_categoria').value].descripcion_categoria;
					}
					else
					{
						$('descripcion_categoria').value = "";
					}
				}
				else
				{
					$('descripcion_categoria').value = "";
				}
		    });

		    // AL TIPEAR UN CODIGO DE TEMA
		    $('codigo_tema').addEvent('keyup', function()
		    {
				if ( $('codigo_tema').value != '' )
				{
					if ( temas[$('codigo_tema').value] != undefined )
					{
						$('id_codtema').value = temas[$('codigo_tema').value].id_codtema;
						$('descripcion_tema').value = temas[$('codigo_tema').value].descripcion_tema;
					}
					else
					{
						$('descripcion_tema').value = "";
					}
				}
				else
				{
					$('descripcion_tema').value = "";
				}
		    });

		    // AL MODIFICAR EL TIPO DE AUTOR
		    $('autor_tipo').addEvent('change', function()
		    {
			    $('autor_codigo').value = '';
			    $('autor_descripcion').value = '';
			    $('lupaModalAutor').setProperty('href','abms/index.php?controlador=autores&accion=listarModal&autor_tipo='+$('autor_tipo').value+'');
			    setfocus('autor_codigo');
		    });

		    // AL TIPEAR UN CODIGO DE AUTOR
		    $('autor_codigo').addEvent('keyup', function()
			{
			    if ( $('autor_codigo').value != '' )
			    {
				    //Se envia una peticion
				    var miJSON = new Json.Remote('abms/index.php?controlador=expedientes&accion=buscarCodigoNombreAutor&autor_tipo='+$('autor_tipo').value+'&autor_codigo='+$('autor_codigo').value+'',
				    {
					    //la peticion nos devolvera un objeto el cual llegara como parametro en el evento onComplete
					    onComplete: function(objeto)
					    {
						    if ( objeto.codigo != '' )
						    {
							    $('autor_codigo').value = objeto.codigo;
							    $('autor_descripcion').value = objeto.descripcion;
						    }
						    else
							{
								$('autor_descripcion').value = "";
							}
					    }
				    });
				    miJSON.send();
			    }
			    else
			    {
					$('autor_descripcion').value = "";
				}
		    });

		    <?php
		    // SI NO SE HABILITARON LOS DATOS DEL EXPEDIENTE
		    if ($_SESSION['campos_habilitados'] == false)
		    {
			?>
			    setfocus('anio');// SE EMPIEZA EDITANDO DESDE EL Año
			    deshabilitarLaCargaCombos();
		    <?php
		    }
		    else
		    {
			?>
			    // SI ESTÁN HABILITADOS SE OFRECE EMPEZAR A EDITAR POR iniciador_tipo
			    setfocus('iniciador_tipo');
			    habilitarLaCargaCombos();
		    <?php
		    }
		    ?>

		    $('iniciador_tipo').value = '<?php echo ($datos[0]['iniciador_tipo']) ? $datos[0]['iniciador_tipo'] : G; ?>';

		    $('autor_tipo').value = '<?php echo ($listadoAutores[0]['autor_tipo']) ? $listadoAutores[0]['autor_tipo'] : C; ?>';

		    $('agregado_tipo').value = '<?php echo ($datos[0]['agregado_tipo']) ? $datos[0]['agregado_tipo'] : 0; ?>';

		    $('btHabilitarDatos').addEvent('click', function(){
			    if ($('tipo').value == '-1')
			    {
				    setfocus('tipo');
				    alert('Por favor, seleccione un valor para Tipo<br>Gracias');
			    }
			    else
			    {
				    refrescar('abms/index.php?controlador=expedientes&accion=habilitarRestoDatos&anio='+$('anio').value+'&tipo='+$('tipo').value+'&numero='+$('numero').value+'&cuerpo='+$('cuerpo').value+'&alcance='+$('alcance').value+'', 'contenidoAjaxPrincipal');
			    }
		    });

		    <?php
		    if ( $tipo_mensaje != '' )
		    {
			?>
				$('tipo').value = '';
				$('numero').value = '';
				$('cuerpo').value = '';
				$('alcance').value = '';
		    <?php
		    }
		    ?>

	    </script>
    <?php
    }

    public function listarTemas($listadoTemas, $mensaje = '')
    {
    	if ($mensaje != ''){ echo '<div style="padding:3px;background-color:#688196;color:#fff;text-align:center;">'.$mensaje.'</div>'; }
		?>
		<script type="text/javascript">
			$('codigo_tema').value='';
			$('descripcion_tema').value='';
			$('autor_tipo').focus();
		</script>
		<table class="e_tabla_texto">
			<thead class="e_tabla_titulos">
				<tr>
					<th class="orden_link" style="background-color:#A09FA4">&nbsp;</th>
					<th class="orden_link" style="background-color:#A09FA4">C&oacute;digo</th>
					<th class="orden_link" style="background-color:#A09FA4">Descripci&oacute;n</th>
				</tr>
			</thead>
			<tbody id="e_cuerpo_scrolleable">
				<?php
				$cantidadT = count($listadoTemas);
				if ( $cantidadT > 3 )
					$tope = $cantidadT;
				else
					$tope = 3;

				for ($t=0; $t < $tope; $t++)
				{
					$tema = &$listadoTemas[$t];
				?>
					<tr id="t_fila<?php echo $t; ?>" onmouseover="javascript:resaltarFila('t_fila<?php echo $t; ?>');" onclick="javascript:resaltarFila('t_fila<?php echo $t; ?>');verTema(<?php echo $tema['codigo_tema']; ?>, '<?php echo $tema['descripcion_tema']; ?>');">
						<td style="width:2px;">
							<?php
							if ( $tema['codigo_tema'] != '')
							{
							?>
								<a style="width:16px;height:16px;display:block;" href="javascript:if(confirm('Desea eliminar el Tema?')){refrescar('abms/index.php?controlador=expedientes&accion=eliminarTema&anio=<?php echo $tema['anio']; ?>&tipo=<?php echo $tema['tipo']; ?>&numero=<?php echo $tema['numero']; ?>&cuerpo=<?php echo $tema['cuerpo']; ?>&alcance=<?php echo $tema['alcance']; ?>&id_codtema=<?php echo $tema['id_codtema']; ?>', 'contenidoAjaxPrincipal');};">
									<img src="imagenes/b_drop.png" width="14" height="14" align="top" />
								</a>
							<?php
							}
							else
							{
								echo '&nbsp;';
							}
							?>
						</td>
						<td><input type="text" name="i_codigo_tema[]" id="lista_temas_codigo_tema<?php echo $t; ?>" value="<?php echo ($tema['codigo_tema']) ? $tema['codigo_tema'] : ''; ?>" style="width:29px;height:17px;" readonly="readonly"></td>
						<td><input type="text" name="i_descripcion_tema[]" value="<?php echo ($tema['descripcion_tema']) ? $tema['descripcion_tema'] : '&nbsp;'; ?>" style="width:235px;height:17px;" readonly="readonly"></td>
					</tr>
					<input type="hidden" name="i_id_codtema[]" value="<?php echo $tema['id_codtema']; ?>" >

					<script type="text/javascript">
						<?php
						if ( $tema['codigo_tema'] )
						{
						?>
							cargarArrayTemasJS(<?php echo $tema['anio']; ?>, '<?php echo $tema['tipo']; ?>', <?php echo $tema['numero']; ?>, <?php echo $tema['cuerpo']; ?>, <?php echo $tema['alcance']; ?>, <?php echo $tema['id_codtema']; ?>, '<?php echo $tema['codigo_tema']; ?>', '<?php echo $tema['descripcion_tema']; ?>');
						<?php
						}
						?>
					</script>
				<?php
				}
				?>
			</tbody>
		</table>
		<input type="hidden" name="contador_temas" value="<?php echo $t; ?>" >
    <?php
    }

    public function listarAutores($listadoAutores, $mensaje = '')
    {
		//fputs(fopen("listadoAutores_listarAutoresV.txt",'w'),print_r($listadoAutores,true));

		if ( $mensaje != '' )
		{
			echo '<div style="padding:3px;background-color:#688196;color:#fff;text-align:center;">'.$mensaje.'</div>';
		}
		?>
		<script type="text/javascript">
			$('autor_codigo').value='';
			$('autor_descripcion').value='';
		</script>
		<table class="e_tabla_texto">
			<thead class="e_tabla_titulos">
				<tr>
					<th class="orden_link" style="background-color:#A09FA4">&nbsp;</th>
					<th class="orden_link" style="background-color:#A09FA4">Grupo</th>
					<th class="orden_link" style="background-color:#A09FA4">C&oacute;digo</th>
					<th class="orden_link" style="background-color:#A09FA4">Descripci&oacute;n</th>
				</tr>
			</thead>
			<tbody id="e_cuerpo_scrolleable">
				<?php
				$cantidadA = count($listadoAutores);
				if ( $cantidadA > 3 )
					$tope = $cantidadA;
				else
					$tope = 3;

				for ($a=0; $a < $tope; $a++)
				{
					$autor = &$listadoAutores[$a];
				?>
					<tr id="a_fila<?php echo $a; ?>" onmouseover="javascript:resaltarFila('a_fila<?php echo $a; ?>');" onclick="javascript:resaltarFila('a_fila<?php echo $a; ?>');verAutor('<?php echo $autor['autor_tipo']; ?>', '<?php echo $autor['autor_codigo']; ?>');">
						<td>
							<?php
							if ( $autor['autor_codigo'] != '')
							{
							?>
								<a style="width:16px;height:16px;display:block;" href="javascript:if(confirm('Desea eliminar el Autor?')){refrescar('abms/index.php?controlador=expedientes&accion=eliminarAutor&anio=<?php echo $autor['anio']; ?>&tipo=<?php echo $autor['tipo']; ?>&numero=<?php echo $autor['numero']; ?>&cuerpo=<?php echo $autor['cuerpo']; ?>&alcance=<?php echo $autor['alcance']; ?>&autor_tipo=<?php echo $autor['autor_tipo']; ?>&autor_codigo=<?php echo $autor['autor_codigo']; ?>', 'contenidoAjaxPrincipal');};">
									<img src="imagenes/b_drop.png" width="14" height="14" align="top" />
								</a>
							<?php
							}
							else
							{
								echo '&nbsp;';
							}
							?>
						</td>
						<td><input type="text" name="i_autor_tipo[]" value="<?php echo ($autor['autor_tipo']) ? $autor['autor_tipo'] : '&nbsp;'; ?>" style="width:47px;height:17px;" readonly="readonly"></td>
						<td><input type="text" name="i_autor_codigo[]" id="lista_autor_autor_codigo<?php echo $a; ?>" value="<?php echo ($autor['autor_codigo']) ? $autor['autor_codigo'] : ''; ?>" style="width:47px;height:17px;" readonly="readonly"></td>
						<td><input type="text" name="i_autor_descripcion[]" value="<?php echo ($autor['autor_descripcion']) ? $autor['autor_descripcion'] : '&nbsp;'; ?>" style="width:260px;height:17px;" readonly="readonly"></td>
					</tr>

					<script type="text/javascript">
						<?php
						if ( $autor['autor_codigo'] )
						{
						?>
							cargarArrayAutoresJS(<?php echo $autor['anio']; ?>, '<?php echo $autor['tipo']; ?>', <?php echo $autor['numero']; ?>, <?php echo $autor['cuerpo']; ?>, <?php echo $autor['alcance']; ?>, '<?php echo $autor['autor_tipo']; ?>', '<?php echo $autor['autor_codigo']; ?>', '<?php echo $autor['autor_descripcion']; ?>');
						<?php
						}
						?>
					</script>
				<?php
				}
				?>
			</tbody>
		</table>
		<input type="hidden" name="contador_autores" value="<?php echo $a; ?>" >
    <?php
    }

    // SE MUESTRAN LOS DATOS DEL EXPEDIENTE DEBAJO DEL LISTADO GENERAL
    public function mostrarDatosExped($datos_expediente)
    {
    ?>
		<input type="hidden" name="h_estado_doc" id="h_estado_doc" value="<?php echo $datos_expediente['estado_doc']; ?>" >
		<!-- 2020/05/07 XXXX -->
		<input type="hidden" name="h_estado_digitalizacion" id="h_estado_digitalizacion" value="<?php echo $datos_expediente['estado_digitalizacion']; ?>" >
		<input type="hidden" name="digi_completa" id="digi_completa" value="<?php echo (isset($datos_expediente['digi_completa'])) ? $datos_expediente['digi_completa'] : 0; ?>" >

		<input type="hidden" name="h_codigo_usuario" id="h_codigo_usuario" value="<?php echo $datos_expediente['codigo_usuario']; ?>" >

		<div class="datos_expediente_contenedor">
			<div class="datos_expediente_izq">
				<span id="p_datos_nombre"><strong><?php echo $datos_expediente['caratula']; ?></strong></span>
				<div style="height:20px;">
					<div class="datos_expediente_titulos">Inicia:</div>
					<div id="p_datos_inicia" class="datos_expediente_valor"><?php echo $datos_expediente['iniciador_codigo'].' - '.$datos_expediente['iniciador_descripcion']; ?></div>
				</div>
				<div style="height:20px;">
					<div class="datos_expediente_titulos">Categor&iacute;a:</div>
					<div id="p_datos_categ" class="datos_expediente_valor"><?php echo $datos_expediente['id_codcategoria'].' - '.$this->cortaCadena($datos_expediente['descripcion_categoria'], 30); ?></div>
				</div>
				<div style="height:20px;">
					<div class="datos_expediente_titulos" style="padding-top:3px;">Autores:</div>
					<div class="datos_expediente_valor">
						<select name="listadoAutores" id="listadoAutores" class="datos_expediente_combos">
							<?php
							$cant_autores = count($datos_expediente['autores']);
							for ($a=0; $a < $cant_autores; $a++)
							{
								$autor = &$datos_expediente['autores'][$a];
							?>
								<option value="<?php echo $autor['autor_codigo']; ?>"><?php echo $autor['autor_codigo'].' - '.$autor['autor_descripcion']; ?></option>
							<?php
							}
							?>
						</select>
					</div>
				</div>
				<div style="height:17px;margin-top:3px">
					<div class="datos_expediente_titulos" style="padding-top:3px;">Temas:</div>
					<div class="datos_expediente_valor">
						<select name="listadoTemas" id="listadoTemas" class="datos_expediente_combos">
							<?php
							$cant_temas = count($datos_expediente['temas']);
							for ($t=0; $t < $cant_temas; $t++)
							{
								$tema = &$datos_expediente['temas'][$t];
							?>
								<option value="<?php echo $tema['codigo_tema']; ?>"><?php echo $tema['codigo_tema'].' - '.$tema['descripcion_tema']; ?></option>
							<?php
							}
							?>
						</select>
					</div>
				</div>
				<div style="height:17px;margin-top:3px">
					<div class="datos_expediente_titulos">Estado:</div>
					<div id="p_datos_estado" class="datos_expediente_valor"><?php echo $datos_expediente['codigo_estado'].' - '.$datos_expediente['nombre_estado']; ?></div>
				</div>
				<?php
				if ( $datos_expediente['codigo_grp'] != '' && $datos_expediente['descripcion_grp'] != '' )
				{
				?>
					<div style="height:40px;">
						<div class="datos_expediente_titulos">Comisi&oacute;n:</div>
						<div id="p_datos_comision" class="datos_expediente_valor" style="height:40px"><?php echo $datos_expediente['codigo_grp'].' - '.$datos_expediente['descripcion_grp']; ?></div>
					</div>
				<?php
				}
				?>
			</div>
			<div id="ext_botoneraScroll" class="datos_expedientes_botonera">
				<?php
				$cant_proyectos = count($datos_expediente['proyectos']);
				// SI EL EXPEDIENTE POSEE MÁS DE UN PROYECTO, SE AGREGAN LOS BOTONES PARA MOVERSE ENTRE LOS PROYECTOS
				if ($cant_proyectos > 1)
				{
				?>
					<div style="height:25px;font-size:0;"></div>
					<div id="btSubir" class="ext_boton ext_btSubir"></div>
					<div style="height:15px;font-size:0;"></div>
					<div id="btBajar" class="ext_boton ext_btBajar"></div>
				<?php
				}
				?>
			</div>
			<div class="datos_expediente_der">
				<div id="p_extractos" class="ext_extractos">
					<div id="p_scrolleable_proyectos" style="width:100%;">
						<?php
						// SI POSEE PROYECTOS EL EXPEDIENTE
						if ($cant_proyectos > 0)
						{
							for ($p=0; $p < $cant_proyectos; $p++)
							{
								$proyecto = &$datos_expediente['proyectos'][$p];
						?>
								<div class='ext_titulo_extracto'><strong>Proyecto N&ordm;&nbsp;<?php echo $proyecto['orden_proyecto']; ?>&nbsp;&nbsp;:&nbsp;<?php echo $proyecto['descripcion_proyecto']; ?></strong></div>
								<div style='height:5px;font-size:0;'></div>
								<div class='ext_detalle_extracto'><span id='extracto'><?php echo convertir_salto_linea($proyecto['extracto']); ?></span></div>
						<?php
							}
						}
						?>
					</div>
				</div>
				<div class="ext_extractos">
					<div style='height:5px;font-size:0;'></div>
					<div class='ext_titulo_extracto'><strong>Observaciones:</strong></div>
					<div id="p_datos_observacion" class='ext_detalle_extracto'><?php echo $datos_expediente['observaciones_expe']; ?></div>
				</div>
			</div>

		</div>
	<?php
    }
}
?>
