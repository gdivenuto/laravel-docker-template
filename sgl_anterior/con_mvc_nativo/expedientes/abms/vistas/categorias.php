<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaCategorias extends VistaBase
{
	private $controlador;
	private $formulario;

	public function __construct()
	{
		$this->controlador = 'categorias';
		$this->formulario = 'formCategorias';
	}
	
	public function verNombreCampoBuscado($campo_orden)
	{
	    switch ($campo_orden)
	    {
			case 'codigo_categoria':
			  return 'C&oacute;digo';
			  break;
			case 'descripcion_categoria':
			  return 'Descripci&oacute;n';
			  break;
			case 'vigencia_desde_categoria':
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
		<?php $this->mostrarBarraNavegacionSuperior_Archivos('abms', 'categorias', 'listar', $filtro, $mensaje, $tipo_mensaje); ?>
		
		<div class="p_borde_superior"></div>
	    
		<div class="ub_cont_buscador">
			<span class="archivos_titulo_listado">CATEGOR&Iacute;AS&nbsp;</span>&nbsp;
			
			<input type="hidden" id="nro_paginas" value="<?php echo $filtro['nro_paginas']; ?>">
			
			<!--  AQUI SE GUARDA EL NOMBRE DEL CAMPO POR EL CUAL SE ORDENA Y SE BUSCA  -->
			<input type="hidden" name="campo_orden" id="campo_orden" value="<?php echo $filtro['campo_orden']; ?>" />
			
			Buscar por <?php echo $this->verNombreCampoBuscado($filtro['campo_orden']); ?>&nbsp;&nbsp;
			<input type="text" name="valor_buscado" id="valor_buscado" value="<?php echo ($filtro['valor_buscado']) ? $filtro['valor_buscado'] : ''; ?>" class="ub_buscador" />
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" id="btBuscar" value="Buscar" onclick="javascript:buscar($('campo_orden').value, $('valor_buscado').value, 'categorias');" />
			&nbsp;&nbsp;
			<input type="button" id="btRestablecer" value="Restablecer" onclick="javascript:refrescar('abms/index.php?controlador=categorias&accion=listar&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" />
			<?php
			if ( $filtro['mostrar_todos'] != 'si' )
			{
			?>
				<input type="button" id="btVerHistoricos" value="Ver hist&oacute;ricos" onclick="javascript:refrescar('abms/index.php?controlador=categorias&accion=listar&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=si', 'contenidoAjaxPrincipal');" />
			<?php
			}
			else
			{
			?>
				<input type="button" id="btVerActivos" value="Ver s&oacute;lo activos" onclick="javascript:refrescar('abms/index.php?controlador=categorias&accion=listar&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=no', 'contenidoAjaxPrincipal');" />
			<?php
			}
			?>
		</div>
		
		<div class="p_borde_superior"></div>
	    
		<div style="width:656px;height:14px;background-color:#3760A7;">
			<div class="orden_link_codificadoras" style="width:35px;"></div>
			<div class="orden_link_codificadoras" style="width:64px;">
				<a id="codigo_categoria" title="Ordenar por c&oacute;digo" href="javascript:ordenarColumna('codigo_categoria','categorias');">C&oacute;digo&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'codigo_categoria'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a>
			</div>
			<div class="orden_link_codificadoras" style="width:345px;">
				<a id="descripcion_categoria" title="Ordenar por descripci&oacute;n" href="javascript:ordenarColumna('descripcion_categoria','categorias');">Descripci&oacute;n&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'descripcion_categoria'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a>
			</div>
			<div class="orden_link_codificadoras" style="width:138px;">
				<a id="vigencia_desde_categoria" title="Ordenar por fecha de vigencia" href="javascript:ordenarColumna('vigencia_desde_categoria','categorias');">Vigencia Desde&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'vigencia_desde_categoria'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a>
			</div>
			<div class="orden_link_codificadoras" style="width:73px;">Habilitado</div>
		</div>
		
		<div class="ub_listado" style="width:657px;height:424px;overflow-y:auto;font-size:11px;">
			
			<input type="hidden" id="controlador" value="categorias">
			<input type="hidden" id="cantidad" value="<?php echo count($datos); ?>">
			<input type="hidden" id="pagina" value="<?php echo $filtro['pagina']; ?>">
			<input type="hidden" id="nroFila_elegida" value="">
			<input type="hidden" id="mostrar_todos" value="<?php echo ($filtro['mostrar_todos']) ? $filtro['mostrar_todos'] : 'no'; ?>">
			
			<table class="e_tabla_texto">
				<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
					<?php
					$n = count($datos);
					for ( $i=0; $i < $n; $i++ )
					{
						$dato = &$datos[$i];
					?>
						<input type="hidden" id="id_codcategoria<?php echo $i; ?>" value="<?php echo $dato['id_codcategoria']; ?>">
						
						<tr id="e_fila<?php echo $i; ?>" <?php echo ($dato['habilitado_categoria'] == 1)? '' : 'style="color:silver"'; ?> onclick="javascript:marcarFila(<?php echo $i; ?>);" onDblClick="javascript:refrescar('abms/index.php?controlador=categorias&accion=editar&id=<?php echo $dato['id_codcategoria']; ?>&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=<?php echo $filtro['mostrar_todos']; ?>', 'contenidoAjaxPrincipal');"> 
							
							<a name="tr<?php echo $i; ?>" style="display:none;"></a>
							
							<td width="16">
								<a style="width:16px;height:16px;display:block;" href="javascript:refrescar('abms/index.php?controlador=categorias&accion=editar&id=<?php echo $dato['id_codcategoria']; ?>&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=<?php echo $filtro['mostrar_todos']; ?>', 'contenidoAjaxPrincipal');" title="Editar">
									<img src="imagenes/b_edit.png" width="14" height="14" />
								</a>
							</td>
							<td width="16">
								<a style="width:16px;height:16px;display:block;" href="javascript:if(confirm('Desea eliminar la Categoria?')){refrescar('abms/index.php?controlador=categorias&accion=eliminar&id=<?php echo $dato['id_codcategoria']; ?>', 'contenidoAjaxPrincipal');};" title="Eliminar">
									<img src="imagenes/b_drop.png" width="14" height="14" />
								</a>
							</td>
							<td id="i_codigo_categoria<?php echo $i; ?>" style="width:58px;height:17px;text-align:right;padding-right:3px;"><?php echo $dato['codigo_categoria']; ?></td>
							<td id="i_descripcion_categoria<?php echo $i; ?>" style="width:330px;height:17px;text-align:left;padding-left:3px;"><?php echo $dato['descripcion_categoria']; ?></td>
							<td id="i_vigencia_desde_categoria<?php echo $i; ?>" style="width:133px;height:17px;text-align:center;"><?php echo $this->formatearFecha($dato['vigencia_desde_categoria']); ?></td>
							<td id="i_habilitado_categoria<?php echo $i; ?>" style="width:70px;height:17px;text-align:center;"><?php echo ($dato['habilitado_categoria'] == 1)? '<img src="imagenes/barra/ok_16x16.gif" width="10" height="10" />' : ''; ?></td>
						</tr>
					<?php
					}
					$posicion_en_el_listado = $i-1; // POR DEFECTO
					?>
				</tbody>
		    </table>
		</div>
		<script>
		    $('valor_buscado').addEvent('click', function(){
				$('valor_buscado').value = '';
		    });
		    
			// SE SETEA LA FILA ACTUAL
			$('nroFila_elegida').value = <?php echo $posicion_en_el_listado; ?>;
			
			// SE MARCA EL ULTIMO REGISTRO VISUALIZADO DEL LISTADO
			$('e_fila<?php echo $posicion_en_el_listado; ?>').setStyles({'background-color':'#76A0CD'});//, 'color':'#fff' 21/06/2018
			
			// SE BAJA EL SCROLL AL ACTUALIZAR EL LISTADO
			window.setTimeout("bajarScrollListados('ub_listado')",3);
			
			// SE ESTABLECE EL FOCO EN LA FILA MARCADA
			$('i_codigo_categoria<?php echo $posicion_en_el_listado; ?>').tabindex = 1;
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
		$url_btGuardar = "javascript:validarCodigo('codigo_categoria', 'formCategorias', 'abms', 'contenidoAjaxPrincipal');";
		$url_btCancelar = "javascript:refrescar('abms/index.php?controlador=categorias&accion=listar&id=".$datos[0]['id_codcategoria']."&boton=cancelar&pagina=".$filtro['pagina']."&mostrar_todos=".$filtro['mostrar_todos']."', 'contenidoAjaxPrincipal');";
		
		// BARRA DE NAVEGACION SUPERIOR
		$this->mostrarBarraNavegacionSuperiorEnEdicion_Archivos($url_btGuardar, $url_btCancelar, 'Categor&iacute;a'); 
		?>		
		
		<div class="p_borde_superior"></div>
	    
		<form action="abms/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">
			
			<input type="hidden" name="controlador" value="<?php echo $this->controlador; ?>" />
			
			<input type="hidden" name="accion" value="<?php echo ($datos[0]['id_codcategoria']) ? 'modificar' : 'insertar';?>" />
			
			<input type="hidden" name="id_codcategoria" id="id_codcategoria" value="<?php echo $datos[0]['id_codcategoria']; ?>" />
			<input type="hidden" name="habilitado_categoria" id="habilitado_categoria" value="<?php if (isset($datos[0]['habilitado_categoria'])){ echo $datos[0]['habilitado_categoria'];}else{ echo '1';} ?>" />		
			<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
			<input type="hidden" name="pagina" value="<?php echo $filtro['pagina']; ?>" />
			
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					C&oacute;digo:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="codigo_categoria" id="codigo_categoria" value="<?php echo ($datos[0]['codigo_categoria']) ? $datos[0]['codigo_categoria'] : ''; ?>" size="3" maxlength="3" />
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Descripci&oacute;n:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="descripcion_categoria" id="descripcion_categoria" value="<?php echo ($datos[0]['descripcion_categoria']) ? $datos[0]['descripcion_categoria'] : ''; ?>" size="60" />
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Vigencia Desde:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="vigencia_desde_categoria" id="vigencia_desde" value="<?php echo ($datos[0]['vigencia_desde_categoria']) ? $this->formatearFecha($datos[0]['vigencia_desde_categoria']) : ''; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="9" maxlength="10" />
					&nbsp;<input type="image" id="codcat_fecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Vigencia Hasta:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="vigencia_hasta_categoria" id="vigencia_hasta" value="<?php echo ($datos[0]['vigencia_hasta_categoria']) ? $this->formatearFecha($datos[0]['vigencia_hasta_categoria']) : ''; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="9" maxlength="10" />
					&nbsp;<input type="image" id="codcat_fecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Habilitado:
				</div>
				<div class="codif_edicion_valor">
					<?php
					if ( !isset($datos[0]['id_codcategoria']) )
					{
						//SI ES Nueva
						$checked = 'checked';
					}
					else
					{
						//SI SE Edita
						if ( $datos[0]['habilitado_categoria'] == 1 )
						{
							//SI ESTA Habilitada
							$checked = 'checked';//SE TILDA
						}
						else
						{
							$checked = '';
						}	
					} 
					?>	
					<input type="checkbox" name="habilitado" id="habilitado" onchange="javascript:chequear('habilitado_categoria');" <?php echo $checked; ?> />
				</div>
			</div>
		</form>
		<script type="text/javascript">
			
		    //CALENDARIO PARA LA FECHA DESDE
		    var calDesde = new Zapatec.Calendar.setup({
			    inputField:"vigencia_desde",
				ifFormat:"%d/%m/%Y",
				button:"codcat_fecha_desde",
				showsTime:false
		    });
		    
		    //CALENDARIO PARA LA FECHA HASTA
		    var calHasta = new Zapatec.Calendar.setup({
			    inputField:"vigencia_hasta",
				ifFormat:"%d/%m/%Y",
				button:"codcat_fecha_hasta",
				showsTime:false
		    });
		</script>	
	<?php
	}
	
	public function listarModal($datos)
	{
	?>
		<div class="ub_listado">
			<table width="100%" class="e_tabla_texto">
		   		<thead class="e_tabla_titulos">
		  			<tr>
						<th>Categor&iacute;a</th>
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
						<tr id="am_fila<?php echo $m; ?>" onclick="javascript:volverModal('id_codcategoria', 'codigo_categoria', 'descripcion_categoria', '<?php echo $dato['id_codcategoria']; ?>', '<?php echo $dato['codigo_categoria']; ?>', '<?php echo $dato['descripcion_categoria']; ?>');" onmouseover="javascript:$('am_fila<?php echo $m; ?>').setStyle('background-color','#DDDDDD');" onmouseout="javascript:$('am_fila<?php echo $m; ?>').setStyle('background-color','#fff');"> 
						
							<td style="width:50px;padding-left:10px;"><?php echo $dato['codigo_categoria']; ?></td>
							<td style="width:50px;padding-left:10px;"><?php echo $dato['descripcion_categoria']; ?></td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
	<?php
	}
	
	public function pedirNombreModal($listadoModal)
	{
	?>
		<div class="autosugerido">
			Nombre: <input type="text" id="nombre_sugerido" name="nombre_sugerido" maxlength="100" />
			<div id="sugerencias"><ul></ul></div>
		</div>
		<div class="margen_modal"></div>
		<div class="cerrar_pedirNombreModal">
			<div class="titulo_pedirNombreModal">Buscar por Categor&iacute;a.</div>
			<div id="btCerrar_pedirNombreModal" class="btCerrar_pedirNombreModal"></div>
		</div>
		<script type="text/javascript">
		
			ventana_modal = "si";
			
			var categorias_a_elegir = new Array(
				<?php
				$cantidad = count($listadoModal);
				for ($c=0; $c < $cantidad; $c++)	
				{
					$categoriaModal = &$listadoModal[$c];
				
					if ( $c == $cantidad-1 )
						echo "'".$categoriaModal['codigo_categoria'].", ".$categoriaModal['descripcion_categoria']."'";
					else
						echo "'".$categoriaModal['codigo_categoria'].", ".$categoriaModal['descripcion_categoria']."',";
				}
				?>
			);
			
			new AutoSuggest($('nombre_sugerido'),categorias_a_elegir, 'c_categoria');	 
			
			setfocus('nombre_sugerido');
			
			$('btCerrar_pedirNombreModal').addEvent('click', function(){
				cerrarModalPedirNombre();
			});	
		</script>
	<?php	
	}
	
}
?>
