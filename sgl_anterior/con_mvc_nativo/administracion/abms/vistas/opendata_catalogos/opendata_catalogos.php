<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaOpendataCatalogos extends VistaBase
{
    private $directorio;
    private $controlador;
    private $formulario;
  
    public function __construct()
    {
		$this->directorio = 'abms';
		$this->controlador = 'opendata_catalogos';
		$this->formulario = 'formOpendataCatalogo';
	}
    
    public function mostrarPaginador($directorio, $controlador, $accion, $filtro, $criterio_buscador = '')
    {
	?>
		<div class="p_bnav_contenedor_4bt">
			<?php
			if ( $filtro['pagina'] != 1 )
			{
			?>
				<a id="btPrimero" title="Primer Registro" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=<?php echo $accion; ?>&campo_orden=<?php echo $_SESSION['ultimo_campo']; ?>&pagina=1&sentido=primero<?php echo $criterio_buscador; ?>', 'contenidoAjaxPrincipal');">
					<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="14" height="14" align="top" />
				</a>
			<?php 
			}
			else
			{
			?>
				<a id="btPrimero" href="#">
					<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="top" />
				</a>
			<?php
			}
			
			if ( $filtro['pagina_ant'] != 0 )
			{
			?>
				<a id="btAnterior" title="Registro Anterior" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=<?php echo $accion; ?>&campo_orden=<?php echo $_SESSION['ultimo_campo']; ?>&pagina=<?php echo $filtro['pagina_ant']; ?>&sentido=anterior<?php echo $criterio_buscador; ?>', 'contenidoAjaxPrincipal');">
					<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="14" height="14" align="top" />
				</a>
			<?php 
			}
			else
			{
			?>
				<a id="btAnterior" href="#">
					<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="top" />
				</a>
			<?php
			}
			
			echo "&nbsp;".$filtro['pagina']." de ".$filtro['nro_paginas']."&nbsp;";
			
			if ( $filtro['pagina'] != $filtro['nro_paginas'] )
			{
			?>
				<a id="btSiguiente" title="Registro Siguiente" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=<?php echo $accion; ?>&campo_orden=<?php echo $_SESSION['ultimo_campo']; ?>&pagina=<?php echo $filtro['pagina_sgte']; ?>&sentido=siguiente<?php echo $criterio_buscador; ?>', 'contenidoAjaxPrincipal');">
					<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="14" height="14" align="top" />
				</a>
			<?php
			}
			else
			{
			?>
				<a id="btSiguiente" href="#">
					<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="top" />
				</a>
			<?php
			}
				
			if ( $filtro['pagina'] != $filtro['nro_paginas'] )
			{
			?>
				<a id="btUltimo" title="Ultimo Registro" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=<?php echo $accion; ?>&campo_orden=<?php echo $_SESSION['ultimo_campo']; ?>&pagina=<?php echo $filtro['nro_paginas']; ?>&sentido=ultimo<?php echo $criterio_buscador; ?>', 'contenidoAjaxPrincipal');">
					<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="14" height="14" align="top" />
				</a>
			<?php
			}
			else
			{
			?>
				<a id="btUltimo" href="#">
					<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="top" />
				</a>
			<?php
			}
			?>
		</div>	
	<?php
	}
    
    public function listar($datos = null, $mensaje = '', $tipo_mensaje = '', $filtro = null) {

		// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);
		?>
		<script>
			$("capaFondo").setStyle('visibility','hidden');
			$("capaVentana").setStyle('visibility','hidden');
			
			se_busca = false;
	    </script>
	    	    
		<!-- BARRA DE NAVEGACION SUPERIOR -->
		<div id="p_barra_navegacion" class="p_barra_navegacion">
			
			<div class="hcdh_titulo_listado">Cat&aacute;logos</div>
			<?php
			if ($datos)
			{
				// SE ARMA EL CRITERIO DEL BUSCADOR PARA LA URL DEL PAGINADOR
				$criterio_buscador = "&f_fecha=".$filtro['f_fecha']."&f_titulo=".$filtro['f_titulo']."&f_descripcion=".$filtro['f_descripcion']."&campo_orden=".$_SESSION['ultimo_campo']."&sentido_orden=".$_SESSION['ultimo_sentido']."";
				
				// PAGINADOR
				$this->mostrarPaginador($this->directorio, $this->controlador, 'listar', $filtro, $criterio_buscador);
			}
			?>
			<div class="p_margen2_boton_edicion"></div>
			<div class="p_boton_edicion">
				<a id="btAgregar" title="Agregar" href="javascript:refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=editar', 'contenidoAjaxPrincipal');">
					<img src="imagenes/barra/add_16x16.gif" width="16" height="16" align="top" />&nbsp;Nuevo
				</a>
			</div>
		</div>
		<div class="p_edicion_datos_borde_superior"></div>
		<div class="ub_cont_buscador p_buscador_alto">
			<!--  AQUI SE GUARDA EL NOMBRE DEL CAMPO POR EL CUAL SE ORDENA Y SE BUSCA  -->
			<input type="hidden" name="campo_orden" id="campo_orden" value="<?php echo $filtro['campo_orden']; ?>" />

			<div class="p_campo_buscador">
				Fecha: <input type="text" name="f_fecha" id="f_fecha" value="<?php echo $this->formatearFecha($_SESSION['filtro_opendata_catalogos']['f_fecha']); ?>" style="width:70px;" maxlength="10" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true);" />
				<input type="image" id="img_f_fecha" src="imagenes/calendario/calendario.gif" alt="Presione aqu&iacute; para seleccionar la fecha." align="top" width="16" height="16">
			</div>
			<div class="p_campo_buscador">
				T&iacute;tulo: <input type="text" name="f_titulo" id="f_titulo" value="<?php echo ($_SESSION['filtro_opendata_catalogos']['f_titulo']) ? $_SESSION['filtro_opendata_catalogos']['f_titulo'] : ''; ?>" size="27" />
			</div>
			<div class="p_campo_buscador">
				Descripci&oacute;n: <input type="text" name="f_descripcion" id="f_descripcion" value="<?php echo ($_SESSION['filtro_opendata_catalogos']['f_descripcion']) ? $_SESSION['filtro_opendata_catalogos']['f_descripcion'] : ''; ?>" size="27" />
			</div>
			<div class="p_boton_edicion izquierda">
				<a title="Buscar" href="javascript:buscarOpendataCatalogos();">
					<img src="imagenes/barra/zoom_16x16.gif" width="16" height="16" align="top" />&nbsp;Buscar
				</a>
			</div>
			<div class="p_buscador_margen_boton"></div>
			<div class="p_boton_edicion izquierda">
				<a title="Limpiar" href="javascript:limpiarBuscador('<?php echo $this->directorio; ?>', '<?php echo $this->controlador; ?>', 'listar', '<?php echo $filtro['pagina']; ?>');">
					<img src="imagenes/barra/limpiar.png" width="16" height="16" align="top" />&nbsp;Limpiar
				</a>
			</div>
		</div>
		<div class="p_edicion_datos_borde_superior"></div>
		<div class="ub_listado">
			<?php 
			if ($datos)
			{
			?>
				<input type="hidden" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" id="cantidad" value="<?php echo count($datos); ?>" />
				<input type="hidden" id="pagina" value="<?php echo $filtro['pagina']; ?>" />
				<input type="hidden" id="nroFila_elegida" value="" />
				
				<table border="0" cellpadding="0" cellspacing="0" class="e_tabla_texto" width="100%" >
					<thead class="e_tabltitulos">
						<tr>
							<th class="orden_link" width="32" colspan="2">&nbsp;</th>
							<th class="orden_link">
								<a id="fecha_emitido" title="Ordenar por Fecha de emisi&oacute;n" href="javascript:ordenarColumna('fecha_emitido', '<?php echo $this->controlador; ?>', <?php echo $filtro['pagina']; ?>);">F. EDICI&Oacute;N&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'fecha_emitido'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" align="top" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" align="top" >'; } ?></a>
							</th>
							<th class="orden_link">
								<a id="fecha_modificado" title="Ordenar por Fecha de modificaci&oacute;n" href="javascript:ordenarColumna('fecha_modificado', '<?php echo $this->controlador; ?>', <?php echo $filtro['pagina']; ?>);">F. MODIFICACI&Oacute;N&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'fecha_modificado'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" align="top" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" align="top" >'; } ?></a>
							</th>
							<th class="orden_link">
								<a id="titulo" title="Ordenar por T&iacute;tulo" href="javascript:ordenarColumna('titulo', '<?php echo $this->controlador; ?>', <?php echo $filtro['pagina']; ?>);">T&Iacute;TULO&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'titulo'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" align="top" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" align="top" >'; } ?></a>
							</th>
							<th class="orden_link">DESCRIPCI&Oacute;N</th>
							<th class="orden_link">HABILITADO</th>
						</tr>
					</thead>
					<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
						<?php
						$n = count($datos);
						for ($i=0; $i < $n; $i++)
						{
							$dato = &$datos[$i];
						?>
							<tr id="e_fila<?php echo $i; ?>" <?php echo ($dato['habilitado'] == '0') ? 'style="color: #A6ABAB;"' : ''; ?> onmouseover="javascript:resaltarFila(<?php echo $i; ?>);" onmouseout="javascript:no_resaltarFila(<?php echo $i; ?>);" onclick="javascript:marcarFilaNueva(<?php echo $i; ?>);" ondblclick="javascript:refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=editar&id=<?php echo $dato['id']; ?>&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');"> 
								<a name="tr<?php echo $i; ?>" style="display:none;"></a>
								
								<td width="16">
									<a style="width:16px;height:16px;display:block;" title="Editar Cat&aacute;logo" href="javascript:refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador;?>&accion=editar&id=<?php echo $dato['id']; ?>&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');">
										<img src="imagenes/b_edit.png" width="14" height="14" align="top" />
									</a>
								</td>
								<td width="16">
									<a style="width:16px;height:16px;display:block;" title="Eliminar Cat&aacute;logo" href="javascript:if (confirm('Desea eliminar el Catalogo?')){refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminar&id=<?php echo $dato['id']; ?>', 'contenidoAjaxPrincipal');};">
										<img src="imagenes/b_drop.png" width="14" height="14" align="top" />
									</a>
								</td>
								
								<input type="hidden" id="id<?php echo $i; ?>" value="<?php echo $dato['id']; ?>" >
								
								<td id="fecha_emitido<?php echo $i; ?>" style="width:110px;text-align:center;padding:0 3px 0 3px;"><?php echo $this->formatearFecha($dato['fecha_emitido']); ?></td>
								
								<td id="fecha_modificado<?php echo $i; ?>" style="width:110px;text-align:center;padding:0 3px 0 3px;"><?php echo $this->formatearFecha($dato['fecha_modificado']); ?></td>
								
								<td id="titulo<?php echo $i; ?>" style="text-align:left;padding:0 10px 0 10px;"><?php echo ($dato['titulo']) ? $dato['titulo'] : '&nbsp;'; ?></td>
								
								<td id="descripcion<?php echo $i; ?>" style="text-align:left;padding:0 10px 0 10px;"><?php echo ($dato['descripcion']) ? $dato['descripcion'] : '&nbsp;'; ?></td>
								
								<td id="habilitado<?php echo $i; ?>" style="width:70px;text-align:center;padding:0 3px 0 3px;">
									<input type="hidden" id="bandera_habilitado<?php echo $i; ?>" value="<?php echo $dato['habilitado']; ?>" >
									<?php echo ( $dato['habilitado'] == '1') ? '<img src="imagenes/barra/ok_16x16.gif" width="12" height="12" align="top" />' : ''; ?>
								</td>
							</tr>
						<?php
						}
						
						$posicion_en_el_listado = $i-1; // POR DEFECTO
						if (isset($filtro['por_teclado']) && $filtro['por_teclado'] == 'arriba'){ $posicion_en_el_listado = $i-1; } // PARA VER LA PAGINA ANTERIOR
						if (isset($filtro['por_teclado']) && $filtro['por_teclado'] == 'abajo'){ $posicion_en_el_listado = 0; } // PARA VER LA PAGINA SIGUIENTE
						?>	  
					</tbody>
				</table>
			<?php
			}
			else
			{
				echo $this->mostrarCartelResultado("Sin resultados", 1);
			}
			?>
		</div>
		<script type="text/javascript">
			
			// SE FILTRA EL LISTADO
			function buscarOpendataCatalogos()
			{
				var url = '<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&f_fecha='+$('f_fecha').value+'&f_titulo='+$('f_titulo').value+'&f_descripcion='+$('f_descripcion').value+'';
				
				refrescar(url, 'contenidoAjaxPrincipal');
			}
			
			//CALENDARIO PARA LA FECHA
			var fecha = new Zapatec.Calendar.setup({
				inputField:"f_fecha",
				ifFormat:"%d/%m/%Y",
				button:"img_f_fecha",
				onUpdate :function(){ buscarOpendataCatalogos(); },
				showsTime:false
			});
			
			<?php
			if ( $datos )
			{
			?>
				//SE MARCA EL ULTIMO REGISTRO DEL LISTADO
				$('e_fila<?php echo $posicion_en_el_listado; ?>').setStyle('background-color','#76A0CD');
				$('e_fila<?php echo $posicion_en_el_listado; ?>').setStyle('color','#fff');
				$('nroFila_elegida').value = <?php echo $posicion_en_el_listado; ?>;
				
				location.href ="#tr<?php echo $posicion_en_el_listado; ?>";
			<?php 
			}
			?>
			
			$('f_fecha').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						if( $('f_fecha').value != '' )
						{
							buscarOpendataCatalogos();
						}
					}
				}
			});
			
			$('f_titulo').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						if( $('f_titulo').value != '' )
						{
							buscarOpendataCatalogos();
						}
					}
				}
			});
			
			$('f_descripcion').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						if( $('f_descripcion').value != '' )
						{
							buscarOpendataCatalogos();
						}
					}
				}
			});
			
			resaltarItemMenu('item_informatica');
			
		</script>
    <?php
    }
    
    public function editar($datos = null, $filtro = '') 
    {
    ?>
		<div class="p_cont_botonera_edicion">
		    <div class="p_edicion_titulo_leyenda"><?php echo ($datos['id']) ? 'Edici&oacute;n' : 'Alta'; ?>  del Cat&aacute;logo</div>
			<div class="p_margen2_boton_edicion"></div>
			<div class="p_boton_edicion">
				<a id="btCancelar" title="Cancelar los cambios realizados" href="javascript:refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&pagina=<?php echo $datos['pagina']; ?>&f_fecha=<?php echo $this->formatearFecha($_SESSION['filtro_opendata_catalogos']['f_fecha']); ?>&f_titulo=<?php echo $_SESSION['filtro_opendata_catalogos']['f_titulo']; ?>&f_descripcion=<?php echo $_SESSION['filtro_opendata_catalogos']['f_descripcion']; ?>', 'contenidoAjaxPrincipal');">
					<img id="p_img_titulo_cancelar_volver" src="imagenes/barra/error_16x16.gif" width="15" height="15" align="top" />&nbsp;<span id="p_titulo_cancelar_volver">Cancelar</span>
				</a>
			</div>
			<div class="p_margen2_boton_edicion"></div>
			<div class="p_boton_edicion">
				<a id="btGuardar" title="Guardar" href="javascript:validarCatalogo('<?php echo $this->formulario; ?>', 'abms');" class="boton_en_edicion" tabindex="6" >
					<img src="imagenes/barra/ok_16x16.gif" width="15" height="15" align="top" />&nbsp;Guardar
				</a>
			</div>
		</div>
		<div class="p_edicion_datos_borde_superior"></div>
		<div class="p_edicion_datos ub_listado" >
			<form action="<?php echo $this->directorio; ?>/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>" >
					
				<input type="hidden" name="controlador" value="<?php echo $this->controlador; ?>" />
				
				<input type="hidden" name="accion" value="<?php echo ($datos['id']) ? 'modificar' : 'insertar'; ?>" />
				
				<input type="hidden" name="id" value="<?php echo ($datos['id']) ? $datos['id'] : $datos['siguiente_codigo']; ?>" >

				<div class="p_edicion">	
					<div class="p_edicion_datos">
						<table>
							<tr>
								<td class="p_edicion_titulo_etiqueta">T&iacute;tulo:</td>
								<td>
									<input type="text" name="titulo" id="titulo" value="<?php echo $this->reemplazarComillaDoble($datos['titulo']); ?>" style="width:680px;" tabindex="3"  />
								</td>
							</tr>
							<tr>
								<td class="p_edicion_titulo_etiqueta">Fecha Emisi&oacute;n:</td>
								<td>
									<input type="text" name="fecha_emitido" id="fecha_emitido" value="<?php echo ($datos['fecha_emitido']) ? $this->formatearFecha($datos['fecha_emitido']) : date("d/m/Y"); ?>" style="width:70px;" maxlength="10" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true);" tabindex="1" />
									<input type="image" id="img_fecha_emitido" src="imagenes/calendario/calendario.gif" alt="Presione aqu&iacute; para seleccionar la fecha." align="top" width="16" height="16" >
									(DDMMAAAA)
								</td>
							</tr>
							<tr>
								<td class="p_edicion_titulo_etiqueta">Fecha Modificaci&oacute;n:</td>
								<td>
									<input type="text" name="fecha_modificado" id="fecha_modificado" value="<?php echo ($datos['fecha_modificado']) ? $this->formatearFecha($datos['fecha_modificado']) : date("d/m/Y"); ?>" style="width:70px;" maxlength="10" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true);" tabindex="1" />
									<input type="image" id="img_fecha_modificado" src="imagenes/calendario/calendario.gif" alt="Presione aqu&iacute; para seleccionar la fecha." align="top" width="16" height="16" >
									(DDMMAAAA)
								</td>
							</tr>
							<tr>
								<td class="p_edicion_titulo_etiqueta">Descripci&oacute;n:</td>
								<td>
									<textarea id="descripcion" name="descripcion" tabindex="4"><?php echo $datos['descripcion']; ?></textarea>
								</td>
							</tr>
							<tr>
								<td class="p_edicion_titulo_etiqueta">Mostrar en Sitio Web</td>
								<td>
									<input type="hidden" name="habilitado" id="habilitado" value="<?php echo ($datos['habilitado']) ? $datos['habilitado'] : 1; ?>" />
									<input type="checkbox" name="chk_habilitado" id="chk_habilitado" <?php echo ($datos['habilitado'] == '0') ? '' : 'checked'; ?> tabindex="5" />
								</td>
							</tr>
						</table>
					</div>
				</div>
			</form>
		</div>
		<script>
			window.addEvent('domready', function()
			{
				// AL EDITAR NO SE BUSCA
				se_busca = false;
				
				//CALENDARIO PARA LA FECHA
				var fechaEmitido = new Zapatec.Calendar.setup({
					inputField:"fecha_emitido",
					ifFormat:"%d/%m/%Y",
					button:"img_fecha_emitido",
					showsTime:false
				});
				
				//CALENDARIO PARA LA FECHA
				var fechaModificado = new Zapatec.Calendar.setup({
					inputField:"fecha_modificado",
					ifFormat:"%d/%m/%Y",
					button:"img_fecha_modificado",
					showsTime:false
				});
				
				$('titulo').addEvents({
					keyup: function(){
						// SE HABILITA EL CALENDARIO
						$('img_fecha_emitido').disabled = false;
						$('img_fecha_modificado').disabled = false;
					},
					keydown: function(event){
						if(event.key == 'Enter')
						{
							// SE DESHABILITA EL CALENDARIO
							$('img_fecha_emitido').disabled = true;
							$('img_fecha_modificado').disabled = true;
						}
					}
				});
				
				$('p_img_titulo_cancelar_volver').setProperty('src', 'imagenes/barra/volver.jpeg');
				$('p_titulo_cancelar_volver').innerHTML = 'Volver';
				
				// ANTE UN CAMBIO EN LOS CAMPOS DE ENTRADA SE MUESTRA EL BOTON Cancelar
				$$("input").addEvent('change', function(){
					$('p_img_titulo_cancelar_volver').setProperty('src', 'imagenes/barra/error_16x16.gif');
					$('p_titulo_cancelar_volver').innerHTML = 'Cancelar';
				});
				
				// AL MARCAR O DESMARCAR EL CAMPO HABILITADO
				$('chk_habilitado').addEvent('change', function()
				{
					$('habilitado').value = ( $('chk_habilitado').checked === true ) ? 1 : 0;
				});
				
				resaltarItemMenu('item_informatica');
			
				setTimeout("$('titulo').select()",75);
			});
		</script>
    <?php
    }
}
?>
