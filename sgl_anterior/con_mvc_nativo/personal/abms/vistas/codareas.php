<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaAreas extends VistaBase
{
	private $directorio;
    private $controlador;
    private $formulario;

    public function __construct()
    {
    	$this->directorio = 'abms';
    	$this->controlador = 'codareas';
		$this->formulario = 'formCodAreas';
    }
   
    public function listar($datos, $mensaje = '', $tipo_mensaje = '', $filtro)
    {
		// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);
		?>
		<!-- BARRA DE NAVEGACION SUPERIOR -->
		<div id="p_barra_navegacion" class="p_barra_navegacion">
			<div class="p_titulo_listado">:. Listado de &Aacute;reas</div>	
			
			<!-- PAGINADOR -->
			<?php $this->mostrarPaginador($this->directorio, $this->controlador, 'listar', $filtro); ?>
			
			<div class="p_margen2_boton_edicion"></div>
				
			<div class="p_boton_edicion">
				<a id="btSalir" title="Volver al listado de Personal." href="javascript:refrescar('abms/index.php?controlador=personal&accion=listar', 'contenidoAjaxPrincipal');">
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
					<a id="btAgregar" title="Agregar &Aacute;rea" href="javascript:refrescar('abms/index.php?controlador=codareas&accion=agregar', 'contenidoAjaxPrincipal');">
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
						
			<div class="p_buscador_codigo">
				C&oacute;digo: <input type="text" name="f_codigo" id="f_codigo" value="<?php echo ($_SESSION['filtro_codarea']['codigo']) ? $_SESSION['filtro_codarea']['codigo'] : ''; ?>" onKeyPress="return soloEnteros(event)" />
			</div>
			<div class="p_buscador_nombre">
				Nombre: <input type="text" name="f_nombre" id="f_nombre" value="<?php echo ($_SESSION['filtro_codarea']['nombre']) ? $_SESSION['filtro_codarea']['nombre'] : ''; ?>" onKeyPress="return soloLetras(event)" />
			</div>
			<div class="p_boton_edicion izquierda">
				<a title="Buscar" href="javascript:buscarCodArea();">
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
		if ($datos)
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
						// SÓLO EL PERFIL 1 PUEDE ELIMINAR
						if ( $_SESSION['perfil3'] == 1 )
						{
						?>
							<th class="orden_link" width="32" colspan="2">&nbsp;</th>
						<?php 
						}
						?>	
						<th class="orden_link"><a id="ca_id" title="Ordenar por C&oacute;digo" href="javascript:ordenarColumna('ca_id', 'codareas', <?php echo $filtro['pagina']; ?>);">C&oacute;digo&nbsp;<?php echo ($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'ca_id') ? '<img src="imagenes/s_desc.png" width="11" height="9" align="top" >' : '<img src="imagenes/s_asc.png" width="11" height="9" align="top" >'; ?></a></th>
						<th class="orden_link"><a id="ca_nombre" title="Ordenar por Nombre" href="javascript:ordenarColumna('ca_nombre', 'codareas', <?php echo $filtro['pagina']; ?>);">Nombre&nbsp;<?php echo ($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'ca_nombre') ? '<img src="imagenes/s_desc.png" width="11" height="9" align="top" >' : '<img src="imagenes/s_asc.png" width="11" height="9" align="top" >'; ?></a></th>
						<th class="orden_link"><a id="ca_mail" title="Ordenar por Mail" href="javascript:ordenarColumna('ca_mail', 'codareas', <?php echo $filtro['pagina']; ?>);">Mail&nbsp;<?php echo ($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'ca_mail') ? '<img src="imagenes/s_desc.png" width="11" height="9" align="top" >' : '<img src="imagenes/s_asc.png" width="11" height="9" align="top" >'; ?></a></th>
						<th class="orden_link"><a id="ca_telefono" title="Ordenar por Tel&eacute;fono" href="javascript:ordenarColumna('ca_telefono', 'codareas', <?php echo $filtro['pagina']; ?>);">Tel&eacute;fono&nbsp;<?php echo ($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'ca_telefono') ? '<img src="imagenes/s_desc.png" width="11" height="9" align="top" >' : '<img src="imagenes/s_asc.png" width="11" height="9" align="top" >'; ?></a></th>
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
						<tr id="e_fila<?php echo $i; ?>" <?php echo ($dato['ca_habilitado'] == '0') ? 'style="color: #A6ABAB;"' : ''; ?> onmouseover="javascript:resaltarFila(<?php echo $i; ?>);" onmouseout="javascript:no_resaltarFila(<?php echo $i; ?>);" onclick="javascript:remarcarFila(<?php echo $i; ?>);" onDblClick="javascript:refrescar('abms/index.php?controlador=codareas&accion=editar&ca_id=<?php echo $dato['ca_id']; ?>&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');"> 
							<a name="tr<?php echo $i; ?>" style="display:none;"></a>
							
							<?php //SOLO EL PERFIL 1 Y 2 PUEDE MODIFICAR
							if ( $_SESSION['perfil3'] == 1)
							{
							?>
								<td width="16">
									<a style="width:16px;height:16px;display:block;" title="Editar registro" href="javascript:refrescar('abms/index.php?controlador=codareas&accion=editar&ca_id=<?php echo $dato['ca_id']; ?>&pagina=<?php echo $filtro['pagina']; ?>&mostrar_todos=<?php echo $filtro['mostrar_todos']; ?>', 'contenidoAjaxPrincipal');">
										<img src="imagenes/b_edit.png" width="14" height="14" align="top" />
									</a>
								</td>
								<td width="16">
									<a style="width:16px;height:16px;display:block;" title="Eliminar registro" href="javascript:if (confirm('Desea eliminar el Area?')){refrescar('abms/index.php?controlador=codareas&accion=eliminar&ca_id=<?php echo $dato['ca_id']; ?>', 'contenidoAjaxPrincipal');};">
										<img src="imagenes/b_drop.png" width="14" height="14" align="top" />
									</a>
								</td>
							<?php 
							}
							?>
							<td id="ca_id<?php echo $i; ?>" style="width:87px;text-align:center;"><?php echo ($dato['ca_id']) ? $dato['ca_id'] : '&nbsp;'; ?></td>
							<td id="ca_nombre<?php echo $i; ?>" style="text-align:left;padding:0 3px 0 3px;"><?php echo ($dato['ca_nombre']) ? $dato['ca_nombre'] : '&nbsp;'; ?></td>
							<td id="ca_mail<?php echo $i; ?>" style="text-align:left;padding:0 3px 0 3px;"><?php echo ($dato['ca_mail']) ? $dato['ca_mail'] : '&nbsp;'; ?></td>
							<td id="ca_telefono<?php echo $i; ?>" style="text-align:left;padding:0 3px 0 3px;"><?php echo ($dato['ca_telefono']) ? $dato['ca_telefono'] : '&nbsp;'; ?></td>
							<td id="ca_habilitado<?php echo $i; ?>" style="width:70px;text-align:center;padding:0 3px 0 3px;">
								<input type="hidden" id="bandera_habilitado<?php echo $i; ?>" value="<?php echo $dato['ca_habilitado']; ?>" >
								
								<?php echo ($dato['ca_habilitado'] == '1') ? '<img src="imagenes/barra/ok_16x16.gif" width="12" height="12" align="top" />' : ''; ?>
							</td>
						</tr>
					<?php
					}
					?>	
					<?php 
					$posicion_en_el_listado = $i-1; // POR DEFECTO
					if ($filtro['por_teclado']=='arriba'){ $posicion_en_el_listado = $i-1; } // PARA VER LA PAGINA ANTERIOR
					if ($filtro['por_teclado']=='abajo'){ $posicion_en_el_listado = 0; } // PARA VER LA PAGINA SIGUIENTE
					?>	  
				</tbody>
			</table>
		<?php
		} else
			echo $this->mostrarCartelResultado("Sin resultados", 1);
		?>	
		</div>
		<script type="text/javascript">
			<?php
			if ( $datos ) {
			?>
				//SE MARCA EL ULTIMO REGISTRO DEL LISTADO
				$('e_fila<?php echo $posicion_en_el_listado; ?>').setStyle('background-color','#76A0CD');
				$('e_fila<?php echo $posicion_en_el_listado; ?>').setStyle('color','#fff');
				$('nroFila_elegida').value = <?php echo $posicion_en_el_listado; ?>;
				
				location.href ="#tr<?php echo $posicion_en_el_listado; ?>";
			<?php 
			}
			?>
			
			$('f_codigo').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						if( $('f_codigo').value != '' )
						{
							buscarCodArea();
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
							buscarCodArea();
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
				//SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
				if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
				{
				?>
					<a id="btCancelar" title="Cancelar los cambios realizados" href="javascript:refrescar('abms/index.php?controlador=codareas&accion=listar&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');">
						<img src="imagenes/barra/error_16x16.gif" width="15" height="15" align="top" />&nbsp;Cancelar
					</a>
				<?php
				}
				else // PARA EL PERFIL 3 (Consulta)
				{
				?>
					<a id="btCancelar" title="Volver al listado de &Aacute;reas" href="javascript:refrescar('abms/index.php?controlador=codareas&accion=listar&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');">
						<img src="imagenes/barra/volver.jpeg" width="17" height="17" align="top" />&nbsp;Volver
					</a>
				<?php	
				}	
				?>	
			</div>
			
			<div class="p_margen2_boton_edicion"></div>
			<?php 
			//SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				<div class="p_boton_edicion">
					<a id="btGuardar" title="Guardar" href="javascript:validarCodArea();" >
						<img src="imagenes/barra/ok_16x16.gif" width="15" height="15" align="top" />&nbsp;Guardar
					</a>
				</div>
			<?php
			}
			?>
		</div>
		
		<form action="abms/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>" >
				
			<input type="hidden" id="controlador" name="controlador" value="<?php echo $this->controlador; ?>" />
			
			<input type="hidden" name="accion" value="<?php echo ( isset($datos['ca_id']) ) ? 'modificar' : 'insertar'; ?>" />
			
			<div class="p_edicion_datos">
				<div class="p_edicion_datos_titulo_leyenda"><?php echo ($datos['ca_id']) ? 'Edici&oacute;n' : 'Alta'; ?> de &Aacute;rea</div>
				<div class="p_edicion_formulario p_edicion_datos">	
					<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">C&oacute;digo:</div>
				  		<div class="h_edicion_fila_valor">
				  			<input type="text" name="ca_id" id="ca_id" value="<?php echo ($datos['ca_id']) ? $datos['ca_id'] : ''; ?>" class="adm_inputEditar" style="width:77px;text-align:left;padding-left:3px;" onKeyPress="return soloEnteros(event)" maxlength="8" <?php echo ($datos['ca_id']) ? 'readonly' : ''; ?> />
						</div>
				  	</div>
					<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">Descripci&oacute;n:</div>
				  		<div class="h_edicion_fila_valor">
				  			<input type="text" name="ca_nombre" id="ca_nombre" value="<?php echo $datos['ca_nombre']; ?>" class="adm_inputEditar" style="width:384px;" />
						</div>
				  	</div>
				  	<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">Tipo:</div>
				  		<div class="h_edicion_fila_valor">
				  			<input type="radio" name="ca_tipo" id="op_politico" value="B" />&nbsp;Bloques
							<input type="radio" name="ca_tipo" id="op_planta" value="P" />&nbsp;Planta
						</div>
				  	</div>
				  	<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">Depende de:</div>
				  		<div class="h_edicion_fila_valor">
				  			<input type="text" name="ca_depende_de" id="ca_depende_de" value="<?php echo ($datos['ca_depende_de']) ? $datos['ca_depende_de'] : ''; ?>" onKeyPress="return soloEnteros(event)"  maxlength="8" style="width:77px;text-align:left;padding-left:3px;" />
							<a id="lupaModalIniciador" href="abms/index.php?controlador=codareas&accion=listarModal" rel="moodalbox 397 350" title="Buscar Legajo">
								<img src="imagenes/barra/zoom_16x16.gif" width="16" height="16" align="top" />
							</a>	  
							<input type="text" name="descripcion_area" id="descripcion_area" value="" style="width:270px;" disabled />
						</div>
				  	</div>
				  	<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">Mail:</div>
				  		<div class="h_edicion_fila_valor">
				  			<input type="text" name="ca_mail" id="ca_mail" value="<?php echo $datos['ca_mail']; ?>" class="adm_inputEditar" style="width:384px;" />
						</div>
				  	</div>
				  	<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">Tel&eacute;fono:</div>
				  		<div class="h_edicion_fila_valor">
				  			<input type="text" name="ca_telefono" id="ca_telefono" value="<?php echo $datos['ca_telefono']; ?>" class="adm_inputEditar" style="width:384px;" />
						</div>
				  	</div>
				  	<div class="h_edicion_fila">
				  		<div class="h_edicion_fila_titulo">Habilitado:</div>
				  		<div class="h_edicion_fila_valor">
				  			<input type="hidden" name="ca_habilitado" id="ca_habilitado" value="<?php echo ($datos['ca_habilitado']) ? $datos['ca_habilitado'] : 1; ?>" />
							<input type="checkbox" name="chk_habilitado" id="chk_habilitado" <?php echo ($datos['ca_habilitado'] == '0') ? '' : 'checked'; ?> />
						</div>
				  	</div>
				</div>
			</div>	

		</form>  
		<script>
			Window.onDomReady(MOOdalBox.init.bind(MOOdalBox));
			
			// AL EDITAR NO SE BUSCA
			se_busca = false;
			
			function mostrarNombreAreaDependienteSegunTipo(tipo_area, codigo)
			{
				// SI ESTA TILDADA LA OPCION DE TIPO (BLOQUE POLITICO O PLANTA PERMANENTE)
				if ( $(tipo_area).checked === true )
					// SI EL ELEMENTO 'depende_de' ESTA VACIO
					if ( $('ca_depende_de').value == '' )
						$('ca_depende_de').value = codigo;// SE ASIGNA EL CODIGO DEL AREA CORRESPONDIENTE
			}
			
			function obtenerDescripcionArea()
			{
				//Se envía la petición de la Descripción del Área del cual dependerá
				var miJSON = new Json.Remote('abms/index.php?controlador='+$('controlador').value+'&accion=buscarDescripcionCodArea&ca_depende_de='+$('ca_depende_de').value+'',
				{
					//la petición devuelve un objeto el cual llegara como parametro en el evento onComplete
					onComplete: function(objeto) {
						// SE MUESTRA LA DESCRIPCION DEL AREA
						$('descripcion_area').value = ( objeto.id_area != '' ) ? objeto.descripcion_area : '';
					}
				});
				miJSON.send();
			}
			
			<?php
			// SI ES DE TIPO PLANTA PERMANENTE
			if ( $datos['ca_tipo'] == 'P' )
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
			
			// AL CARGARSE EL ELEMENTO DE OPCION DE TIPO BLOQUE
			$('op_politico').addEvent('domready', function()
			{
				mostrarNombreAreaDependienteSegunTipo('op_politico', '02000000');
			});
			
			// AL CARGARSE EL ELEMENTO DE OPCION DE TIPO PLANTA PERMANENTE
			$('op_planta').addEvent('domready', function()
			{
				mostrarNombreAreaDependienteSegunTipo('op_planta', '01000000');
			});
			
			// AL CLICKEAR LA OPCION DE TIPO BLOQUE
			$('op_politico').addEvent('click', function()
			{
				$('ca_depende_de').value = '02000000';
				obtenerDescripcionArea();
			});
			
			// AL CLICKEAR LA OPCION DE TIPO PLANTA
			$('op_planta').addEvent('click', function()
			{
				$('ca_depende_de').value = '01000000';
				obtenerDescripcionArea();
			});
			
			// AL SOLTAR LA TECLA EN depende_de
			$('ca_depende_de').addEvent('keyup', function()
			{
				if ( $('ca_depende_de').value != '' && $('ca_depende_de').value != $('ca_id').value )
				{
					obtenerDescripcionArea();
				}
				else
				{
					$('descripcion_area').value = '';	
				}	
			});
			
			// AL OCURRIR UN CAMBIO EN depende_de
			$('ca_depende_de').addEvent('change', function()
			{
				if ( $('ca_depende_de').value != '' && $('ca_depende_de').value != $('ca_id').value )
				{
					obtenerDescripcionArea();
				}
				else
				{
					$('descripcion_area').value = '';	
				}	
			});
			
			// AL CARGARSE EL ELEMENTO depende_de
			$('ca_depende_de').addEvent('domready', function()
			{
				if ( $('ca_depende_de').value != '' && $('ca_depende_de').value != $('ca_id').value )
				{
					obtenerDescripcionArea();
				}
				else
				{
					$('descripcion_area').value = '';	
				}	
			});
			
			<?php
			// SI EL AREA TIENE UNA DEPENDENCIA
			if ( $datos['ca_depende_de'] != '' )
			{
			?>	
				obtenerDescripcionArea();
			<?php	
			}
			else
			{
			?>	
				$('descripcion_area').value = '';	
			<?php
			}
			?>
			
			$('chk_habilitado').addEvent('change', function()
			{
				$('ca_habilitado').value = ( $('chk_habilitado').checked === true ) ? 1 : 0; 
			});
			
			var primer_campo_editable = ($('accion').value == 'modificar') ? 'ca_nombre' : 'ca_id';
			setTimeout("$("+primer_campo_editable+").select()", 75);
		</script>	
    <?php
    }
	
    public function listarModal($datos)
    {
    ?>
		<div class="ub_listado">
			<table class="e_tabla_texto">
				<thead class="e_tabla_titulos">
					<tr>
						<th class="orden_link">C&oacute;digo</th>
						<th class="orden_link">Descripci&oacute;n</th>
					</tr>
				</thead>
				<tbody class="e_cuerpo_scrolleable">
					<?php
					$n = count($datos);
					for ($m=0; $m < $n; $m++) {
						$dato = &$datos[$m];
					?>
						<tr id="im_fila<?php echo $m; ?>" onclick="javascript:volverModalCodArea('ca_depende_de', 'descripcion_area', '<?php echo $dato['ca_id']; ?>', '<?php echo $dato['ca_nombre']; ?>');" onmouseover="javascript:$('im_fila<?php echo $m; ?>').setStyle('background-color','#DDD');" onmouseout="javascript:$('im_fila<?php echo $m; ?>').setStyle('background-color','#fff');"> 
							<td style="width:74px;text-align:right;padding:0 3px 0 3px;"><?php echo $dato['ca_id']; ?></td>
							<td style="width:307px;text-align:left;padding:0 3px 0 3px;"><?php echo $dato['ca_nombre']; ?></td>
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