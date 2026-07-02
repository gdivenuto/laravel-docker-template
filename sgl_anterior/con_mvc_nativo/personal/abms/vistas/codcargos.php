<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaCargos extends VistaBase
{
	private $directorio;
    private $controlador;
    private $formulario;

    public function __construct()
    {
    	$this->directorio = 'abms';
		$this->controlador = 'codcargos';
		$this->formulario = 'formCodCargos';
    }
    
    public function mostrarNumeroNomenclador($nomenclador)
    {
		$nomenclador_1 = substr($nomenclador, 0, 2);
		$nomenclador_2 = substr($nomenclador, 2, 2);
		$nomenclador_3 = substr($nomenclador, 4, 2);
		$nomenclador_4 = substr($nomenclador, 6, 2);
		
		return $nomenclador_1.'-'.$nomenclador_2.'-'.$nomenclador_3.'-'.$nomenclador_4;
	}
	
    public function dividirNumeroNomenclador($nomenclador)
    {
		$nomenclador_dividido[0] = substr($nomenclador, 0, 2);
		$nomenclador_dividido[1] = substr($nomenclador, 2, 2);
		$nomenclador_dividido[2] = substr($nomenclador, 4, 2);
		$nomenclador_dividido[3] = substr($nomenclador, 6, 2);
		
		return $nomenclador_dividido;
	}
	
	private function mostrarTipo($tipo)
	{
		return ( $tipo == 'B') ? 'Bloques' : 'Planta';
	}
	
	private function esSuperior($gente_a_cargo)
	{
		return ( $gente_a_cargo == '1') ? 'Si' : 'No';
	}
				
    public function listar($datos, $mensaje = '', $tipo_mensaje = '', $filtro)
	{    
		// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);
		?>
		<!-- BARRA DE NAVEGACION SUPERIOR -->
		<div id="p_barra_navegacion" class="p_barra_navegacion">
			<div class="p_titulo_listado">:. Listado de Cargos</div>
			
			<!-- PAGINADOR -->
			<?php $this->mostrarPaginador($this->directorio, $this->controlador, 'listar', $filtro); ?>
			
			<div class="p_margen2_boton_edicion"></div>
								
			<div class="p_boton_edicion">
				<a title="Volver al listado de Personal" href="javascript:refrescar('<?php echo $this->directorio; ?>/index.php?controlador=personal&accion=listar', 'contenidoAjaxPrincipal');">
					<img src="imagenes/barra/volver.jpeg" width="17" height="17" align="top" />&nbsp;Volver
				</a>
			</div>
			
		    <?php 
			//SÓLO USUARIOS DE PERFIL 1 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 )
			{
			?>
				<div class="p_margen2_boton_edicion"></div>
				
				<div class="p_boton_edicion">
					<a title="Agregar Cargo" href="javascript:refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=agregar', 'contenidoAjaxPrincipal');">
						<img src="imagenes/barra/add_16x16.gif" width="16" height="16" align="top" />&nbsp;Nuevo
					</a>
				</div>
			<?php 
			} 
			?>
		</div>
		<div class="p_edicion_datos_borde_superior"></div>
		<div class="ub_cont_buscador p_buscador_alto">
			
			<input type="hidden" id="nro_paginas" value="<?php echo $filtro['nro_paginas']; ?>" />
			<!--  AQUI SE GUARDA EL NOMBRE DEL CAMPO POR EL CUAL SE ORDENA Y SE BUSCA  -->
			<input type="hidden" name="campo_orden" id="campo_orden" value="<?php echo $filtro['campo_orden']; ?>" />
			
			<div class="p_buscador_nomenclador">
				Nomenclador: <input type="text" name="f_nomenclador" id="f_nomenclador" value="<?php echo ($_SESSION['filtro_codcargo']['nomenclador']) ? $_SESSION['filtro_codcargo']['nomenclador'] : ''; ?>" onKeyPress="return soloEnteros(event)" />
			</div>
			<div class="p_buscador_nombre">
				Nombre: <input type="text" name="f_nombre" id="f_nombre" value="<?php echo ($_SESSION['filtro_codcargo']['nombre']) ? $_SESSION['filtro_codcargo']['nombre'] : ''; ?>" onKeyPress="return soloLetras(event)" />
			</div>
			<div class="p_boton_edicion izquierda">
				<a title="Buscar" href="javascript:buscarCodCargo();">
					<img src="imagenes/barra/zoom_16x16.gif" width="16" height="16" align="top" />&nbsp;Buscar
				</a>
			</div>
			<div class="p_buscador_margen_boton"></div>
			<div class="p_boton_edicion izquierda">
				<a title="Limpiar" href="javascript:refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');">
					<img src="imagenes/barra/limpiar.png" width="16" height="16" align="top" />&nbsp;Limpiar
				</a>
			</div>
		</div>
		<div class="ub_listado">
		<?php 
		if ( $datos )
		{
		?>
			<input type="hidden" id="controlador" value="<?php echo $this->controlador; ?>" />
			<input type="hidden" id="cantidad" value="<?php echo count($datos); ?>" />
			<input type="hidden" id="pagina" value="<?php echo $filtro['pagina']; ?>" />
			<input type="hidden" id="nroFila_elegida" value="" />
			
			<table class="e_tabla_texto">
				<thead class="e_tabla_titulos">
					<tr>
						<?php
						//SÓLO EL PERFIL 1 PUEDE MODIFICAR Y ELIMINAR CODIFICADORA DE CARGOS
						if ( $_SESSION['perfil3'] == 1 )
						{
						?>
							<th class="orden_link" width="32" colspan="2">&nbsp;</th>
						<?php 
						}
						?>	
						<th class="orden_link"><a id="cc_nomenclador" title="Ordenar por Nomenclador" href="javascript:ordenarColumna('cc_nomenclador', <?php echo $this->controlador; ?>, <?php echo $filtro['pagina']; ?>);">Nomenclador&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'cc_nomenclador'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" align="top" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" align="top" >'; } ?></a></th>
						<th class="orden_link"><a id="cc_nombre" title="Ordenar por Nombre" href="javascript:ordenarColumna('cc_nombre', <?php echo $this->controlador; ?>, <?php echo $filtro['pagina']; ?>);">Nombre&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'cc_nombre'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" align="top" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" align="top" >'; } ?></a></th>
						<th class="orden_link">Tipo</th>
						<th class="orden_link">Personal a cargo</th>
						<th class="orden_link">M&oacute;dulo</th>
						<th class="orden_link">Habilitado</th>
					</tr>
				</thead>
				<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
					<?php
					$n = count($datos);
					for ($i=0; $i < $n; $i++)
					{
						$dato = &$datos[$i];
					?>
						<tr id="e_fila<?php echo $i; ?>" <?php echo ($dato['cc_habilitado'] == '0') ? 'style="color: #A6ABAB;"' : ''; ?> onmouseover="javascript:resaltarFila(<?php echo $i; ?>);" onmouseout="javascript:no_resaltarFila(<?php echo $i; ?>);" onclick="javascript:remarcarFila(<?php echo $i; ?>);" onDblClick="javascript:refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=editar&cc_nomenclador=<?php echo $dato['cc_nomenclador']; ?>&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');"> 
							<a name="tr<?php echo $i; ?>" style="display:none;"></a>		    
							
							<?php //SOLO EL PERFIL 1 Y 2 PUEDE MODIFICAR Y ELIMINAR
							if ( $_SESSION['perfil3'] == 1 )
							{
							?>
								<td width="16">
									<a style="width:16px;height:16px;display:block;" title="Editar registro" href="javascript:refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=editar&cc_nomenclador=<?php echo $dato['cc_nomenclador']; ?>&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=<?php echo $filtro['mostrar_todos']; ?>', 'contenidoAjaxPrincipal');">
										<img src="imagenes/b_edit.png" width="14" height="14" align="top" />
									</a>
								</td>
								<td width="16">
									<a style="width:16px;height:16px;display:block;" title="Eliminar registro" href="javascript:if (confirm('Desea eliminar el Cargo?')){refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminar&cc_nomenclador=<?php echo $dato['cc_nomenclador']; ?>', 'contenidoAjaxPrincipal');};">
										<img src="imagenes/b_drop.png" width="14" height="14" align="top" />
									</a>
								</td>
							<?php 
							}
							?>
							<td id="cc_nomenclador<?php echo $i; ?>" style="width:100px;text-align:center;">
								<?php echo ($dato['cc_nomenclador']) ? $this->mostrarNumeroNomenclador($dato['cc_nomenclador']) : '&nbsp;'; ?>
							</td>
							<td id="cc_nombre<?php echo $i; ?>" style="width:270px;text-align:left;padding-left:3px;">
								<?php echo ($dato['cc_nombre']) ? $dato['cc_nombre'] : '&nbsp;'; ?>
							</td>
							<td id="cc_tipo<?php echo $i; ?>" style="width:100px;text-align:left;padding-left:3px;">
								<?php echo ($dato['cc_tipo']) ? $this->mostrarTipo($dato['cc_tipo']) : '&nbsp;'; ?>
							</td>
							<td id="cc_gente_a_cargo<?php echo $i; ?>" style="width:70px;text-align:center;padding:0 3px 0 3px;">
								<?php echo ($dato['cc_gente_a_cargo'] == '1') ? '<img src="imagenes/barra/ok_16x16.gif" width="12" height="12" align="top" />' : ''; ?>
							</td>
							<td id="cc_modulo<?php echo $i; ?>" style="width:100px;text-align:center;">
								<?php echo ($dato['cc_modulo']) ? $dato['cc_modulo'] : '&nbsp;'; ?>
							</td>
							<td id="cc_habilitado<?php echo $i; ?>" style="width:70px;text-align:center;padding:0 3px 0 3px;">
								<input type="hidden" id="bandera_habilitado<?php echo $i; ?>" value="<?php echo $dato['cc_habilitado']; ?>" >
								
								<?php echo ($dato['cc_habilitado'] == '1') ? '<img src="imagenes/barra/ok_16x16.gif" width="12" height="12" align="top" />' : ''; ?>
							</td>
						</tr>
					<?php
					}
					 
					$posicion_en_el_listado = $i-1; // POR DEFECTO
					if ($filtro['por_teclado']=='arriba'){ $posicion_en_el_listado = $i-1; } // PARA VER LA PAGINA ANTERIOR
					if ($filtro['por_teclado']=='abajo'){ $posicion_en_el_listado = 0; } // PARA VER LA PAGINA SIGUIENTE
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
		<script>
			<?php 
			if ($datos)
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
			
			$('f_nomenclador').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						if( $('f_nomenclador').value != '' )
						{
							buscarCodCargo();
						}
					}
				}
			});
			
			$('f_nombre').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						if( $('f_nombre').value != '' )
						{
							buscarCodCargo();
						}
					}
				}
			});
			
		</script>
    <?php
    }  
    
    public function editar($datos = null, $filtro = '')
    {
    ?>
		<div class="p_cont_botonera_edicion">	
		    
			<div class="p_margen2_boton_edicion"></div>
			
			<div class="p_boton_edicion">	
				<?php 
				// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
				if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
				{
				?>
					<a id="btCancelar" title="Cancelar los cambios realizados" href="javascript:refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');">
						<img src="imagenes/barra/error_16x16.gif" width="15" height="15" align="top" />&nbsp;Cancelar
					</a>
				<?php
				}
				else // PARA EL PERFIL 3 (Consulta)
				{
				?>
					<a id="btCancelar" title="Volver al listado de Cargos" href="javascript:refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');">
						<img src="imagenes/barra/volver.jpeg" width="17" height="17" align="top" />&nbsp;Volver
					</a>
				<?php	
				}	
				?>
			</div>
			
			<div class="p_margen2_boton_edicion"></div>
			<?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				<div class="p_boton_edicion">
					<a id="btGuardar" title="Guardar" href="javascript:validarCodCargo();" tabindex="49">
						<img src="imagenes/barra/ok_16x16.gif" width="15" height="15" align="top" />&nbsp;Guardar
					</a>
				</div>
			<?php
			}
			?>
		</div>
		
		<form action="<?php echo $this->directorio; ?>/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>" >
				
			<input type="hidden" name="controlador" value="<?php echo $this->controlador; ?>" />
			<input type="hidden" name="cc_nomenclador" value="<?php echo $datos['cc_nomenclador']; ?>" />
			
			<input type="hidden" id="accion" name="accion" value="<?php echo ($datos['cc_nomenclador']) ? 'modificar' : 'insertar'; ?>" />
			
			<div class="p_edicion_datos">
				<div class="p_edicion_datos_titulo_leyenda"><?php echo ($datos['cc_nomenclador']) ? 'Edici&oacute;n' : 'Alta'; ?> de Cargo</div>
				<div class="p_edicion_formulario p_edicion_datos">
					<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">Nomenclador:</div>
				  		<div class="h_edicion_fila_valor">
				  			<?php
							$parte_nomenclador = $this->dividirNumeroNomenclador($datos['cc_nomenclador']);
							?>
							<input type="text" name="cc_parte_nomenclador_0" id="cc_parte_nomenclador_0" value="<?php echo $parte_nomenclador[0]; ?>" maxlength="2" class="adm_inputEditar" style="width:18px;text-align:center;" onKeyPress="return soloEnteros(event)" <?php echo ($datos['cc_nomenclador']) ? 'readonly' : ''; ?> />
							-
							<input type="text" name="cc_parte_nomenclador_1" id="cc_parte_nomenclador_1" value="<?php echo $parte_nomenclador[1]; ?>" maxlength="2" class="adm_inputEditar" style="width:18px;text-align:center;" onKeyPress="return soloEnteros(event)" <?php echo ($datos['cc_nomenclador']) ? 'readonly' : ''; ?> />
							-
							<input type="text" name="cc_parte_nomenclador_2" id="cc_parte_nomenclador_2" value="<?php echo $parte_nomenclador[2]; ?>" maxlength="2" class="adm_inputEditar" style="width:18px;text-align:center;" onKeyPress="return soloEnteros(event)" <?php echo ($datos['cc_nomenclador']) ? 'readonly' : ''; ?> />
							-
							<input type="text" name="cc_parte_nomenclador_3" id="cc_parte_nomenclador_3" value="<?php echo $parte_nomenclador[3]; ?>" maxlength="2" class="adm_inputEditar" style="width:18px;text-align:center;" onKeyPress="return soloEnteros(event)" <?php echo ($datos['cc_nomenclador']) ? 'readonly' : ''; ?> />
						</div>
				  	</div>
				  	<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">Descripci&oacute;n:</div>
				  		<div class="h_edicion_fila_valor">
				  			<input type="text" name="cc_nombre" id="cc_nombre" value="<?php echo $datos['cc_nombre']; ?>" class="adm_inputEditar" style="width:570px;" />
						</div>
				  	</div>
				  	<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">Tipo:</div>
				  		<div class="h_edicion_fila_valor">
				  			<input type="radio" name="cc_tipo" id="op_politico" value="B" />&nbsp;Bloques
							<input type="radio" name="cc_tipo" id="op_planta" value="P" />&nbsp;Planta
						</div>
				  	</div>
				  	<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">Personal a cargo:</div>
				  		<div class="h_edicion_fila_valor">
				  			<input type="hidden" name="cc_gente_a_cargo" id="cc_gente_a_cargo" value="<?php echo($datos['cc_gente_a_cargo']) ? $datos['cc_gente_a_cargo'] : 0; ?>" />
							<input type="checkbox" name="chk_gente_a_cargo" id="chk_gente_a_cargo" <?php echo ($datos['cc_gente_a_cargo'] == '0') ? '' : 'checked'; ?> />
						</div>
				  	</div>
				  	<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">M&oacute;dulo:</div>
				  		<div class="h_edicion_fila_valor">
				  			<input type="text" name="cc_modulo" id="cc_modulo" value="<?php echo $datos['cc_modulo']; ?>" class="adm_inputEditar" style="width:20px;" />&nbsp;utilice el . (punto) para decimales
						</div>
				  	</div>
				  	<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">Habilitado:</div>
				  		<div class="h_edicion_fila_valor">
				  			<input type="hidden" name="cc_habilitado" id="cc_habilitado" value="<?php echo($datos['cc_habilitado']) ? $datos['cc_habilitado'] : 1; ?>" />
							<input type="checkbox" name="chk_habilitado" id="chk_habilitado" <?php echo ($datos['cc_habilitado'] == '0') ? '' : 'checked'; ?> />
						</div>
				  	</div>
				</div>
			</div>
		</form>  
		<script>
			<?php
			// SI ES DE TIPO PLANTA PERMANENTE
			if ( $datos['cc_tipo'] == 'P' )
			{
			?>
				// SE TILDA LA OPCION DE TIPO PLANTA PERMANENTE
				$('op_planta').checked = true;
				setTimeout("$('op_planta').focus()",75);
			<?php
			}
			else
			{
			?>
				// SE TILDA LA OPCION DE TIPO BLOQUE
				$('op_politico').checked = true;
				setTimeout("$('op_politico').focus()",75);
			<?php
			}
			?>	
			
			$('chk_gente_a_cargo').addEvent('change', function()
			{
				$('cc_gente_a_cargo').value = ( $('chk_gente_a_cargo').checked == true ) ? 1 : 0; 
			});
			
			$('chk_habilitado').addEvent('change', function()
			{
				$('cc_habilitado').value = ( $('chk_habilitado').checked === true ) ? 1 : 0; 
			});
						
			var primer_campo_editable = ($('accion').value == 'modificar') ? 'cc_nombre' : 'cc_parte_nomenclador_0';
			setTimeout("$("+primer_campo_editable+").select()", 75);
		</script>		  
    <?php
    }
	
}
?>
