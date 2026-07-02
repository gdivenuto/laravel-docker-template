<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaLugares extends VistaBase
{
	private $controlador;
	private $formulario;

	public function __construct()
	{
		$this->controlador = 'lugares';
		$this->formulario = 'formLugares';
	}
	
	public function verNombreCampoBuscado($campo_orden)
	{
	    switch ($campo_orden)
	    {
			case 'tipo_grp':
			  return 'Tipo Grupo';
			  break;
			case 'codigo_grp':
			  return 'C&oacute;digo Grupo';
			  break;
			case 'descripcion_grp':
			  return 'Descripci&oacute;n';
			  break;
			case 'bloque_tipo':
			  return 'Tipo Bloque';
			  break;
			case 'bloque_codigo':
			  return 'C&oacute;digo Bloque';
			  break;
			case 'vigente_Desde_grp':
			  return 'Vigencia Desde';
			  break;
	    }
	}

	public function listar($datos, $mensaje = '', $tipo_mensaje = '', $filtro = '')
	{
		// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);
	?>
		<script>
		    $('header').setStyle('display','block');
		    $('item_consultas_menu_gral').setStyle('display','none');
		    $('item_listados_menu_gral').setStyle('display','none');
		    $('item_tareas_menu_gral').setStyle('display','none');
		    $('item_prestamos_menu_gral').setStyle('display','none');
		    $('p_menu_ocultado').setStyle('display','none');
		</script>	
		
		<div class="p_borde_superior"></div>
	    
		<!-- BARRA DE NAVEGACION SUPERIOR -->
		<?php $this->mostrarBarraNavegacionSuperior_Archivos('abms', 'lugares', 'listar', $filtro, $mensaje, $tipo_mensaje); ?>
		
		<div class="p_borde_superior"></div>
	    
		<div class="ub_cont_buscador">
			<span class="archivos_titulo_listado">LUGARES&nbsp;</span>&nbsp;
			
		    <input type="hidden" id="nro_paginas" value="<?php echo $filtro['nro_paginas']; ?>">
		    <!--  AQUI SE GUARDA EL NOMBRE DEL CAMPO POR EL CUAL SE ORDENA Y SE BUSCA  -->
		    <input type="hidden" name="campo_orden" id="campo_orden" value="<?php echo $filtro['campo_orden']; ?>" />
		    Buscar por <?php echo $this->verNombreCampoBuscado($filtro['campo_orden']); ?>&nbsp;&nbsp;
		    <input type="text" name="valor_buscado" id="valor_buscado" value="<?php echo ($filtro['valor_buscado']) ? $filtro['valor_buscado'] : ''; ?>" class="ub_buscador" />&nbsp;&nbsp;
		    &nbsp;&nbsp;
		    <input type="button" id="btBuscar" value="Buscar" onclick="javascript:buscar($('campo_orden').value, $('valor_buscado').value, 'lugares');" />
		    &nbsp;&nbsp;
		    <input type="button" id="btRestablecer" value="Restablecer" onclick="javascript:refrescar('abms/index.php?controlador=lugares&accion=listar&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" />
		    <?php
		    // Si se desean mostrar todos
			if ( $filtro['mostrar_todos'] === 'si' ) {
			?>
				<input type="button" id="btVerActivos" value="Ver s&oacute;lo activos" onclick="javascript:refrescar('abms/index.php?controlador=lugares&accion=listar&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=no', 'contenidoAjaxPrincipal');" />
			<?php
			} else { // sino, sólo los activos
			?>
				<input type="button" id="btVerHistoricos" value="Ver hist&oacute;ricos" onclick="javascript:refrescar('abms/index.php?controlador=lugares&accion=listar&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=si', 'contenidoAjaxPrincipal');" />
			<?php
			}
			?>
		</div>
		
		<div class="p_borde_superior"></div>
	    
		<div style="width:830px;height:14px;background-color:#3760A7;">
			<div class="orden_link_codificadoras" style="width:35px;"></div>
			<div class="orden_link_codificadoras" style="width:78px;">
				<a id="tipo_grp" href="javascript:ordenarColumna('tipo_grp','lugares');">Tipo Grp.&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'tipo_grp'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a>
			</div>
			<div class="orden_link_codificadoras" style="width:78px;">
				<a id="codigo_grp" href="javascript:ordenarColumna('codigo_grp','lugares');">Cod. Grp.&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'codigo_grp'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a>
			</div>
			<div class="orden_link_codificadoras" style="width:220px;">
				<a id="descripcion_grp" href="javascript:ordenarColumna('descripcion_grp','lugares');">Descripci&oacute;n&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'descripcion_grp'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a>
			</div>
			<div class="orden_link_codificadoras" style="width:89px;">
				<a id="bloque_tipo" href="javascript:ordenarColumna('bloque_tipo','lugares');">Tipo Bloque&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'bloque_tipo'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a>
			</div>
			<div class="orden_link_codificadoras" style="width:89px;">
				<a id="bloque_codigo" href="javascript:ordenarColumna('bloque_codigo','lugares');">Cod. Bloque&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'bloque_codigo'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a>
			</div>
			<div class="orden_link_codificadoras" style="width:150px;">
				<a id="vigente_Desde_grp" href="javascript:ordenarColumna('vigente_Desde_grp','lugares');">Vigencia Desde&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'vigente_Desde_grp'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a>
			</div>
			<div class="orden_link_codificadoras" style="width:72px;">Habilitado</div>
		</div>
		
		<div id="ub_listado" class="ub_listado" style="width:830px;height:287px;overflow-x:hidden;overflow-y:auto;font-size:11px;">
			
			<input type="hidden" id="controlador" value="lugares">
			<input type="hidden" id="cantidad" value="<?php echo count($datos); ?>" />
			<input type="hidden" id="pagina" value="<?php echo $filtro['pagina']; ?>">
			<input type="hidden" id="nroFila_elegida" value="">
			<input type="hidden" id="mostrar_todos" value="<?php echo ($filtro['mostrar_todos']) ? $filtro['mostrar_todos'] : 'si'; ?>">
			
			<table class="e_tabla_texto">
				<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
					<?php
					$n = count($datos);
					for ( $i=0; $i < $n; $i++ )
					{
						$dato = &$datos[$i];
						if ( ( $dato['tipo_grp'] == $filtro['tipo_grp'] ) && ( $dato['codigo_grp'] == $filtro['codigo_grp'] ) )
						{
							$posicion_fila_a_marcar = $i;
						}
					?>
						<input type="hidden" id="tipo_grp<?php echo $i; ?>" value="<?php echo $dato['tipo_grp']; ?>">
						<input type="hidden" id="codigo_grp<?php echo $i; ?>" value="<?php echo $dato['codigo_grp']; ?>">

						<tr id="e_fila<?php echo $i; ?>" <?php echo ($dato['habilitado_grp'] == 1)? '' : 'style="color:silver"'; ?> onmouseover="javascript:refrescarObservaciones('<?php echo addslashes($dato['observaciones_grp']); ?>');" onclick="javascript:marcarFila(<?php echo $i; ?>);refrescarObservaciones('<?php echo addslashes($dato['observaciones_grp']); ?>');" onDblClick="javascript:refrescar('abms/index.php?controlador=lugares&accion=editar&tipo=<?php echo $dato['tipo_grp']; ?>&codigo=<?php echo $dato['codigo_grp']; ?>&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=<?php echo $filtro['mostrar_todos']; ?>', 'contenidoAjaxPrincipal');" >
						
							<td style="width:15px;">
								<a style="width:16px;height:16px;display:block;" href="javascript:refrescar('abms/index.php?controlador=lugares&accion=editar&tipo=<?php echo $dato['tipo_grp']; ?>&codigo=<?php echo $dato['codigo_grp']; ?>&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=<?php echo $filtro['mostrar_todos']; ?>', 'contenidoAjaxPrincipal');" title="Editar">
									<img src="imagenes/b_edit.png" width="14" height="14" />
								</a>
							</td>
							<td style="width:15px;">
								<a style="width:16px;height:16px;display:block;" href="javascript:if(confirm('Desea eliminar el Lugar?')){refrescar('abms/index.php?controlador=lugares&accion=eliminar&tipo=<?php echo $dato['tipo_grp']; ?>&codigo=<?php echo $dato['codigo_grp']; ?>', 'contenidoAjaxPrincipal');};" title="Eliminar">
									<img src="imagenes/b_drop.png" width="14" height="14" />
								</a>
							</td>
							<td id="i_tipo_grp<?php echo $i; ?>" style="width:74px;height:17px;text-align:left;padding-left:3px;" ><?php echo $dato['tipo_grp']; ?></td>
							<td id="i_codigo_grp<?php echo $i; ?>" style="width:74px;height:17px;text-align:left;padding-left:3px;" ><?php echo $dato['codigo_grp']; ?></td>
							<td id="i_descripcion_grp<?php echo $i; ?>" style="width:214px;height:17px;text-align:left;padding-left:3px;" ><?php echo ($dato['descripcion_grp'] != '') ? $dato['descripcion_grp'] : '&nbsp;' ; ?></td>
							<td id="i_bloque_tipo<?php echo $i; ?>" style="width:82px;height:17px;text-align:left;padding-left:3px;" ><?php echo ($dato['bloque_tipo'] != '') ? $dato['bloque_tipo'] : '&nbsp;' ; ?></td>
							<td id="i_bloque_codigo<?php echo $i; ?>" style="width:82px;height:17px;text-align:left;padding-left:3px;" ><?php echo ($dato['bloque_codigo'] != '') ? $dato['bloque_codigo'] : '&nbsp;' ; ?></td>
							<td id="i_vigente_Desde_grp<?php echo $i; ?>" style="width:148px;height:17px;text-align:left;padding-left:3px;" ><?php echo $this->formatearFecha($dato['vigente_Desde_grp']); ?></td>
							<td id="i_habilitado_grp<?php echo $i; ?>" style="width:67px;height:17px;text-align:center;" ><?php echo ($dato['habilitado_grp'] == 1)? '<img src="imagenes/barra/ok_16x16.gif" width="10" height="10" />' : ''; ?></td>
						</tr>
					<?php
					}
					
					$posicion_en_el_listado = $i-1; // POR DEFECTO
					?>
					<a id="enlace_scroll" href="javascript:$('ub_listado').scrollTo(0, 800);return false;"></a>
				</tbody>
			</table>
		</div>
		
		<div class="um_cont_observaciones">
		    <div class="um_titulo_observaciones archivos_observaciones">Observaciones:</div>
		    <div class="um_caja_observaciones">
				<textarea name="observaciones" id="observaciones" class="um_textarea" disabled></textarea>
		    </div>
		</div>
		<script type="text/javascript">
			
		    refrescarObservaciones('<?php echo addslashes($datos[0]['observaciones_grp']); ?>');
		    
		    $('valor_buscado').addEvent('click', function(){
				$('valor_buscado').value = '';
		    });
		    
			// SE SETEA LA FILA ACTUAL
			$('nroFila_elegida').value = <?php echo $posicion_en_el_listado; ?>;
			
			// SE MARCA EL ULTIMO REGISTRO VISUALIZADO DEL LISTADO
			$('e_fila<?php echo $posicion_en_el_listado; ?>').setStyles({'background-color':'#76A0CD'});//, 'color':'#fff' 21/06/2018
			
			// SE ESTABLECE EL FOCO EN LA FILA MARCADA
			$('i_tipo_grp<?php echo $posicion_en_el_listado; ?>').tabindex = 1;
			
		</script>
	<?php
	}
	
	public function editar($datos = null, $filtro = null)
	{
	?>
		<script>
		    $('header').setStyle('display','none');
		    $('p_menu_ocultado').setStyle('display','block');
		</script>
		
		<div class="p_borde_superior"></div>
	    
		<?php 
		$tipo_grp = ($datos[0]['tipo_grp']) ? $datos[0]['tipo_grp'] : '';
		$codigo_grp = ($datos[0]['codigo_grp']) ? $datos[0]['codigo_grp'] : '';
		
		$url_btGuardar = "javascript:validarLugar();";
		$url_btCancelar = "javascript:refrescar('abms/index.php?controlador=lugares&accion=listar&tipo_grp=".$tipo_grp."&codigo_grp=".$codigo_grp."&pagina=".$filtro['pagina']."&mostrar_todos=".$filtro['mostrar_todos']."', 'contenidoAjaxPrincipal');";
		
		// BARRA DE NAVEGACION SUPERIOR
		$this->mostrarBarraNavegacionSuperiorEnEdicion_Archivos($url_btGuardar, $url_btCancelar, 'Lugar'); 
		?>		
		
		<div class="p_borde_superior"></div>
	    
		<form action="abms/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">
			
			<input type="hidden" name="controlador" value="<?php echo $this->controlador; ?>" />
		
			<input type="hidden" name="accion" value="<?php echo ($datos[0]['tipo_grp']) ? 'modificar' : 'insertar'; ?>" />
		
			<input type="hidden" name="pagina" value="<?php echo $filtro['pagina']; ?>" />
			
			<input type="hidden" name="habilitado_grp" id="habilitado_grp" value="<?php echo ($datos[0]['habilitado_grp']) ?  $datos[0]['habilitado_grp'] : '1' ; ?>" />			
			
			<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
			
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Tipo:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="tipo_grp" id="tipo_grp" value="<?php echo ($datos[0]['tipo_grp']) ? $datos[0]['tipo_grp'] : ''; ?>" size="3" maxlength="3" onkeyup="javascript:convertirMayuscula('tipo_grp');" />
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					C&oacute;digo:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="codigo_grp" id="codigo_grp" value="<?php echo ($datos[0]['codigo_grp']) ?  $datos[0]['codigo_grp'] : ''; ?>" size="10" maxlength="10" />
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Descripci&oacute;n:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="descripcion_grp" id="descripcion_grp" value="<?php echo ($datos[0]['descripcion_grp']) ? $datos[0]['descripcion_grp'] : ''; ?>" size="60" />
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Abreviatura p/ Orden del D&iacute;a:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="abreviatura_grp" id="abreviatura_grp" value="<?php echo ($datos[0]['abreviatura_grp']) ? $datos[0]['abreviatura_grp'] : ''; ?>" size="60" />
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Tipo Bloque:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="bloque_tipo" id="bloque_tipo" value="<?php echo ($datos[0]['bloque_tipo']) ? $datos[0]['bloque_tipo'] : ''; ?>" size="3" maxlength="3" />
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					C&oacute;digo Bloque:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="bloque_codigo" id="bloque_codigo" value="<?php echo ($datos[0]['bloque_codigo']) ? $datos[0]['bloque_codigo'] : ''; ?>" size="10" maxlength="10" />
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Vigencia Desde:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="vigente_Desde_grp" id="vigencia_desde" value="<?php echo $this->formatearFecha($datos[0]['vigente_Desde_grp']); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="9" maxlength="10" />
					&nbsp;<input type="image" id="codlug_fecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Vigencia Hasta:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="vigente_Hasta_grp" id="vigencia_hasta" value="<?php echo $this->formatearFecha($datos[0]['vigente_Hasta_grp']); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="9" maxlength="10" />
					&nbsp;<input type="image" id="codlug_fecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Habilitado:
				</div>
				<div class="codif_edicion_valor">
					<?php
					if ( !isset($datos[0]['tipo_grp']) )
					{
						//SI ES Nuevo
						$checked = 'checked';
					}
					else
					{
						//SI SE Edita
						if ( $datos[0]['habilitado_grp'] == 1 )
						{
							//SI ESTA Habilitado
							$checked = 'checked';//SE TILDA
						}
						else
						{
							$checked = '';
						}	
					}
					?>	
					<input type="checkbox" name="habilitado" id="habilitado" onchange="javascript:chequear('habilitado_grp');" <?php echo $checked; ?> />
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">Observaciones:</div>
				<div class="codif_edicion_valor">
					<textarea name="observaciones_grp" id="observaciones_grp" class="um_textarea"><?php echo ($datos[0]['observaciones_grp']) ? $datos[0]['observaciones_grp'] : ''; ?></textarea>
				</div>
			</div>
		</form>
		
		<script type="text/javascript">
			
		    //CALENDARIO PARA LA FECHA DESDE
		    var calDesde = new Zapatec.Calendar.setup({
			    
				inputField:"vigencia_desde",
				ifFormat:"%d/%m/%Y",
				button:"codlug_fecha_desde",
				showsTime:false
		    });
		    
		    //CALENDARIO PARA LA FECHA HASTA
		    var calHasta = new Zapatec.Calendar.setup({
			    
				inputField:"vigencia_hasta",
				ifFormat:"%d/%m/%Y",
				button:"codlug_fecha_hasta",
				showsTime:false
		    });

		    function convertirMayuscula(id)
		    {
		    	$(id).value = $(id).value.toUpperCase();
			}
		</script>
	<?php
	}
	
	public function listarModal($datos, $se_edita = false)
	{
	?>
	    <div class="ub_listado">
			<table width="100%" class="e_tabla_texto">
				<thead class="e_tabla_titulos">
					<tr>
						<th>C&oacute;digo</th>
						<th>Descripci&oacute;n</th>
					</tr>
				</thead>
				<tbody class="e_cuerpo_scrolleable">
					<?php
					$n = count($datos);
					for ( $m=0; $m < $n; $m++ )
					{
						$dato = &$datos[$m];
					?>
						<tr id="im_fila<?php echo $m; ?>" onclick="javascript:volverModal_lugares('<?php echo $dato['tipo_grp']; ?>', '<?php echo $dato['codigo_grp']; ?>', '<?php echo $dato['descripcion_grp']; ?>', '<?php echo $dato['bloque_tipo']; ?>', '<?php echo $dato['bloque_codigo']; ?>');" onmouseover="javascript:$('im_fila<?php echo $m; ?>').setStyle('background-color','#DDDDDD');" onmouseout="javascript:$('im_fila<?php echo $m; ?>').setStyle('background-color','#fff');"> 
							
							<td style="width:50px;padding-left:10px;"><?php echo $dato['codigo_grp']; ?></td>
							<td style="width:50px;padding-left:10px;"><?php echo $dato['descripcion_grp']; ?></td>
												
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
