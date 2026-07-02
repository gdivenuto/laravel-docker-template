<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaCodProyectos extends VistaBase
{
	private $controlador;
	private $formulario;

	public function __construct()
	{
		$this->controlador = 'codproyectos';
		$this->formulario = 'formCodProyectos';
	}
	
	public function verNombreCampoBuscado($campo_orden)
	{
	    switch ($campo_orden)
	    {
			case 'id_codproyecto':
			  return 'C&oacute;digo';
			  break;
			case 'descripcion_proyecto':
			  return 'Descripci&oacute;n';
			  break;
			case 'vigencia_desde_codproy':
			  return 'Vigencia Desde';
			  break;
			case 'vigencia_hasta_codproy':
			  return 'Vigencia Hasta';
			  break;
	    }
	}

	public function listar($datos, $mensaje = '', $tipo_mensaje = '', $filtro= '')
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
		<?php $this->mostrarBarraNavegacionSuperior_Archivos('abms', 'codproyectos', 'listar', $filtro, $mensaje, $tipo_mensaje); ?>
		
		<div class="p_borde_superior"></div>
	    
		<div class="ub_cont_buscador">
			<span class="archivos_titulo_listado">PROYECTOS&nbsp;</span>&nbsp;
		
			<input type="hidden" id="nro_paginas" value="<?php echo $filtro['nro_paginas']; ?>">
			<!--  AQUI SE GUARDA EL NOMBRE DEL  CAMPO POR EL CUAL SE ORDENA Y SE BUSCA  -->
			<input type="hidden" name="campo_orden" id="campo_orden" value="<?php echo $filtro['campo_orden']; ?>" />
			Buscar por <?php echo $this->verNombreCampoBuscado($filtro['campo_orden']); ?>&nbsp;&nbsp;
			<input type="text" name="valor_buscado" id="valor_buscado" value="<?php echo ($filtro['valor_buscado']) ? $filtro['valor_buscado'] : ''; ?>" class="ub_buscador" />&nbsp;&nbsp;
			&nbsp;&nbsp;
			<input type="button" id="btBuscar" value="Buscar" onclick="javascript:buscar($('campo_orden').value, $('valor_buscado').value, 'codproyectos');" />
			&nbsp;&nbsp;
			<input type="button" id="btRestablecer" value="Restablecer" onclick="javascript:refrescar('abms/index.php?controlador=codproyectos&accion=listar&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');" />
			<?php
			if ( $filtro['mostrar_todos'] != 'si' )
			{
			?>
				<input type="button" id="btVerHistoricos" value="Ver hist&oacute;ricos" onclick="javascript:refrescar('abms/index.php?controlador=codproyectos&accion=listar&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=si', 'contenidoAjaxPrincipal');" />
			<?php
			}
			else
			{
			?>
				<input type="button" id="btVerActivos" value="Ver s&oacute;lo activos" onclick="javascript:refrescar('abms/index.php?controlador=codproyectos&accion=listar&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=no', 'contenidoAjaxPrincipal');" />
			<?php
			}
			?>
		</div>
		
		<div class="p_borde_superior"></div>
	    
		<div class="ub_listado" style="width:590px;height:284px;overflow-x:hidden;overflow-y:auto;font-size:11px;">
			<table class="e_tabla_texto">
		   		<thead class="e_tabla_titulos">
		  			<tr>
						<th class="orden_link" width="32" colspan=2>&nbsp;</th>
		   				<th class="orden_link"><a id="codigo_proyecto" href="javascript:ordenarColumna('id_codproyecto','codproyectos');">Cod.&nbsp;<?php if ($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'codigo_proyecto'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a></th>
		  				<th class="orden_link"><a id="descripcion_proyecto" href="javascript:ordenarColumna('descripcion_proyecto','codproyectos');">Descripci&oacute;n&nbsp;<?php if ($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'descripcion_proyecto'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a></th>
		  				<th class="orden_link"><a id="vigencia_desde_codproy" href="javascript:ordenarColumna('vigencia_desde_codproy','codproyectos');">Vigencia Desde&nbsp;<?php if ($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'vigencia_desde_codproy'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a></th>
						<th class="orden_link"><a id="vigencia_hasta_codproy" href="javascript:ordenarColumna('vigencia_hasta_codproy','codproyectos');">Vigencia Hasta&nbsp;<?php if ($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'vigencia_hasta_codproy'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" >'; } ?></a></th>
						
					</tr>
		  		</thead>
				<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
				
				<input type="hidden" id="controlador" value="codproyectos">
				<input type="hidden" id="cantidad" value="<?php echo count($datos); ?>" />
				<input type="hidden" id="pagina" value="<?php echo $filtro['pagina']; ?>">
				<input type="hidden" id="nroFila_elegida" value="">
				<input type="hidden" id="mostrar_todos" value="<?php echo ($filtro['mostrar_todos']) ? $filtro['mostrar_todos'] : 'no'; ?>">
				<?php
				$n = count($datos);
				for ($i=0; $i < $n; $i++)
				{
					$dato = &$datos[$i];
				?>
					<input type="hidden" id="id_codproyecto<?php echo $i; ?>" value="<?php echo $dato['id_codproyecto']; ?>">
					
					<tr id="e_fila<?php echo $i; ?>" <?php echo ($dato['habilitado_codproy'] == 1)? '' : 'style="color:silver"'; ?> onclick="javascript:marcarFila(<?php echo $i; ?>);" onDblClick="javascript:refrescar('abms/index.php?controlador=codproyectos&accion=editar&id=<?php echo $dato['id_codproyecto']; ?>&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=<?php echo $filtro['mostrar_todos']; ?>', 'contenidoAjaxPrincipal');"> 
						<a name="tr<?php echo $i; ?>" style="display:none;"></a>
						<td width="16">
							<a style="width:16px;height:16px;display:block;" href="javascript:refrescar('abms/index.php?controlador=codproyectos&accion=editar&id=<?php echo $dato['id_codproyecto']; ?>&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=<?php echo $filtro['mostrar_todos']; ?>', 'contenidoAjaxPrincipal');" title="Editar">
								<img src="imagenes/b_edit.png" width="14" height="14" />
							</a>
						</td>
						<td width="16">
							<a style="width:16px;height:16px;display:block;" href="javascript:if(confirm('Desea eliminar el Codigo de Proyecto?')){refrescar('abms/index.php?controlador=codproyectos&accion=eliminar&id=<?php echo $dato['id_codproyecto']; ?>', 'contenidoAjaxPrincipal');};" title="Eliminar">
								<img src="imagenes/b_drop.png" width="14" height="14" />
							</a>
						</td>
						<td id="i_codigo_proyecto<?php echo $i; ?>" style="width:58px;height:17px;text-align:right;padding-right:3px;"><?php echo $dato['codigo_proyecto']; ?></td>
						<td id="i_descripcion_proyecto<?php echo $i; ?>" style="width:200px;height:17px;text-align:left;padding-left:3px;"><?php echo ($dato['descripcion_proyecto'])? $dato['descripcion_proyecto'] : '&nbsp;'; ?></td>
						<td id="i_vigencia_desde_codproy<?php echo $i; ?>" style="width:133px;height:17px;text-align:center;padding-left:3px;"><?php echo $this->formatearFecha($dato['vigencia_desde_codproy']); ?></td>
						<td id="i_vigencia_hasta_codproy<?php echo $i; ?>" style="width:131px;height:17px;text-align:center;padding-left:3px;"><?php echo $this->formatearFecha($dato['vigencia_hasta_codproy']); ?></td>
					</tr>
				<?php
				}
				
				$posicion_en_el_listado = $i-1; // POR DEFECTO
				?>
				</tbody>
			</table>
		</div>
		<script type="text/javascript">
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
			$('i_codigo_proyecto<?php echo $posicion_en_el_listado; ?>').tabindex = 1;
		</script>
	<?php
	
	}
	
	public function editar($datos = null, $filtro = null)// $pagina)
	{
	?>
		<script>
		    $('header').setStyle('display','none');
		    $('p_menu_ocultado').setStyle('display','block');
		</script>
		
		<div class="p_borde_superior"></div>
	    
		<?php 
		$url_btGuardar = "javascript:validarCodigo('codigo_proyecto', 'formCodProyectos', 'abms', 'contenidoAjaxPrincipal');";
		$url_btCancelar = "javascript:refrescar('abms/index.php?controlador=codproyectos&accion=listar&id=".$datos[0]['id_codproyecto']."&boton=cancelar&pagina=".$filtro['pagina']."&mostrar_todos=".$filtro['mostrar_todos']."', 'contenidoAjaxPrincipal');";
		// BARRA DE NAVEGACION SUPERIOR
		$this->mostrarBarraNavegacionSuperiorEnEdicion_Archivos($url_btGuardar, $url_btCancelar, 'Proyecto'); 
		?>		
		
		<div class="p_borde_superior"></div>
	    
		<form action="abms/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">
			
			<input type="hidden" name="controlador" value="<?php echo $this->controlador; ?>" />
		
			<input type="hidden" name="accion" value="<?php echo ($datos[0]['id_codproyecto']) ? 'modificar' : 'insertar'; ?>" />
		
			<input type="hidden" name="id_codproyecto" id="id_codproyecto" value="<?php echo $datos[0]['id_codproyecto']; ?>" />
			<input type="hidden" name="pagina" value="<?php echo $filtro['pagina']; ?>" />
			<input type="hidden" name="habilitado_codproy" id="habilitado_codproy" value="<?php if(isset($datos[0]['habilitado_codproy'])){ echo $datos[0]['habilitado_codproy'];}else{ echo '1';} ?>" />	
			<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
			
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					C&oacute;digo:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="codigo_proyecto" id="codigo_proyecto" value="<?php echo ($datos[0]['codigo_proyecto']) ? $datos[0]['codigo_proyecto'] : ''; ?>" size="2" maxlength="2" />
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Descripci&oacute;n:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="descripcion_proyecto" id="descripcion_proyecto" value="<?php echo ($datos[0]['descripcion_proyecto']) ? $datos[0]['descripcion_proyecto'] : ''; ?>" size="30" />
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Vigencia Desde:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="vigencia_desde_codproy" id="vigencia_desde" value="<?php echo(isset($datos[0]['vigencia_desde_codproy']) && $datos[0]['vigencia_desde_codproy']!='0000-00-00') ? $this->formatearFecha($datos[0]['vigencia_desde_codproy']) : '' ; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="9" maxlength="10" />
					&nbsp;<input type="image" id="codproy_fecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Vigencia Hasta:
				</div>
				<div class="codif_edicion_valor">
					<input type="text" name="vigencia_hasta_codproy" id="vigencia_hasta" value="<?php echo (isset($datos[0]['vigencia_hasta_codproy']) && $datos[0]['vigencia_hasta_codproy']!='0000-00-00') ? $this->formatearFecha($datos[0]['vigencia_hasta_codproy']) : '' ; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="9" maxlength="10" />
					&nbsp;<input type="image" id="codproy_fecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
				</div>
			</div>
			<div class="codif_edicion_fila">
				<div class="codif_edicion_titulo">
					Habilitado:
				</div>
				<div class="codif_edicion_valor">
					<?php
					if ( !isset($datos[0]['id_codproyecto']) )
					{
						//SI ES Nuevo
						$checked = 'checked';
					}
					else
					{
						//SI SE Edita
						if ( $datos[0]['habilitado_codproy'] == 1 )
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
					<input type="checkbox" name="habilitado" id="habilitado" onchange="javascript:chequear('habilitado_codproy');" <?php echo $checked; ?> />
				</div>
			</div>
		</form>
		<script type="text/javascript">
			//CALENDARIO PARA LA FECHA DESDE
			var calDesde = new Zapatec.Calendar.setup({
				
				inputField	:"vigencia_desde",
				ifFormat	:"%d/%m/%Y",
				button		:"codproy_fecha_desde",
				showsTime	:false

			});
			//CALENDARIO PARA LA FECHA HASTA
			var calHasta = new Zapatec.Calendar.setup({
				
				inputField	:"vigencia_hasta",
				ifFormat	:"%d/%m/%Y",
				button		:"codproy_fecha_hasta",
				showsTime	:false

			});
		</script>	
	<?php
	}
	
}
?>
