<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaCargarProyecto 
{
    private $controlador;
    private $formulario;
    
    public function __construct()
    {
		$this->controlador = 'cargar_proyecto';
		$this->formulario = 'formCargarProyecto';
    }
    
    public function consultar_carga_bloque($clave)
    {
    ?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>
		
		<div id="precarga_modal" style="display:none"></div>

		<div id="contenidoAjaxCargarProyecto" class="mst_pasar_proy_gral mst_pasar_proy_texto">
		
			<form action="tareas/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">
				
				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="upload_bloque" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="enviado" id="enviado" value="enviado" />
				
				<!-- PARA EL NOMBRE DEL DIRECTORIO DONDE SE GUARDARA EL DOCUMENTO DEL PROYECTO -->
				<input type="hidden" name="pftp_anio" id="pftp_anio" value="<?php echo $clave['anio']; ?>" />
				<input type="hidden" name="pftp_tipo" id="pftp_tipo" value="<?php echo $clave['tipo']; ?>" />
				<input type="hidden" name="pftp_numero" id="pftp_numero" value="<?php echo $clave['numero']; ?>" />
				
				<div id="dragger_carga_proyectos"  class="msc_titulos degradado">Carga de Proyectos</div>
				<div style="height:5px;font-size:0;"></div>
				&iquest;Cargar Documentos Originales al Expediente
				<br>
				<b>
				<span id="cpp_anio"><?php echo $clave['anio']; ?></span>
				&nbsp;&nbsp;&nbsp;
				<span id="cpp_tipo"><?php echo $clave['tipo']; ?></span>
				&nbsp;&nbsp;&nbsp;
				<span id="cpp_numero"><?php echo $clave['numero']; ?></span>
				&nbsp;&nbsp;&nbsp;
				<span id="cpp_cuerpo"><?php echo $clave['cuerpo']; ?></span>
				&nbsp;&nbsp;&nbsp;
				<span id="cpp_alcance"><?php echo $clave['alcance']; ?></span>
				</b>?
				<div style="height:50px;font-size:0;"></div>
				
				<div class="msc_contenedor_btVolver">
					<div class="mst_margen_lateral_botones"></div>
					<div class="msc_volver degradado">
						<a href="index.php"><img src="imagenes/anterior.gif" width="16" height="16" align="top" />&nbsp;&nbsp;&nbsp;No</a>
					</div>
					<div class="mst_margen_medio_botones"></div>
					<div class="msc_volver degradado">
						<a href="javascript:enviarForm('formCargarProyecto', 'tareas', 'contenidoAjaxCargarProyecto');">
							<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;&nbsp;Si
						</a>
					</div>
				</div>
			</form>
		</div>
		<script>
			window.addEvent('domready', function()
			{
				$('header').setStyle('display','none');
				$('p_menu_ocultado').setStyle('display','block');
								
				var menuDrag = new Drag.Move($('contenidoAjaxCargarProyecto'), {
				   handle: $('dragger_carga_proyectos')
				  }
				);
			});
		</script>
    <?php	
    }

    public function upload_bloque($expediente)
    {
    ?>
		<div id="contenidoAjaxCargarProyecto" class="mst_pasar_proy_gral mst_pasar_proy_texto mst_pasar_proy_gral_upload" style="padding-left:0;padding-right:0;">
			<form action="tareas/index.php" method="POST" enctype="multipart/form-data" name="formUpload" id="formUpload">
				
				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="procesar_upload_bloque" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
				
				<!-- PARA EL NOMBRE DEL DIRECTORIO DONDE SE GUARDARA EL DOCUMENTO -->
				<input type="hidden" name="pftp_anio" id="pftp_anio" value="<?php echo $expediente['pftp_anio']; ?>" />
				<input type="hidden" name="pftp_tipo" id="pftp_tipo" value="<?php echo $expediente['pftp_tipo']; ?>" />
				<input type="hidden" name="pftp_numero" id="pftp_numero" value="<?php echo $expediente['pftp_numero']; ?>" />
				
				Seleccione el archivo:&nbsp;<input name="proyecto_subido" type="file" />
				<br>
				<input type="submit" value="Enviar" />
					
			</form>
			<div style="height:70px;font-size:0;clear:both;"></div>
			<div class="msc_contenedor_btVolver">
				<div class="msc_volver degradado">
					<a href="index.php"><img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;&nbsp;Cerrar</a>
				</div>
			</div>
		</div>
    <?php
    }

    public function retornar($mensaje = '', $tipo_mensaje = '', $clave_expediente)
    {
		$_SESSION['mensaje'] = $mensaje;
		$_SESSION['tipo_mensaje'] = $tipo_mensaje;
    
    	header("Location: ../index.php?anio=".$clave_expediente['pftp_anio']."&tipo=".$clave_expediente['pftp_tipo']."&numero=".$clave_expediente['pftp_numero']."&cuerpo=0&alcance=0&sentido=anterior");
    }

    public function preguntar_usuario_bloque($expediente)
    {
    ?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>
		
		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxCargarProyecto" class="proy_contenedor_cargar_proyecto" style="width:377px;">
			
			<div id="ftp_contenedora_mensaje" style="width:377px;">
				<div id="dragger_carga_proyectos" class="msc_titulos degradado">Carga de Proyectos</div>
				<div style="height:5px;font-size:0;"></div>
				<div class="ftp_mensaje_texto">
					<?php
					if ($expediente['pftp_tipo'] == 'E') $tipo = " el Expediente ";
					else $tipo = " la Nota ";
					
					echo "El documento para".$tipo." <span>".$expediente['pftp_anio']." ".$expediente['pftp_tipo']." ".$expediente['pftp_numero']."</span> ya existe, desea reemplazarlo?";
					?>
				</div>
				<div style="height:70px;font-size:0;"></div>
				<div style="height:20px;font-size:0;">
					<div class="ftp_mensaje_margen"></div>
					<div class="ftp_mensaje_boton degradado">
						<a href="index.php"><img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;No</a>
					</div>
					<div class="ftp_mensaje_margen"></div>
					<div id="ftp_mensaje_btSi" class="ftp_mensaje_boton degradado">
						<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Si
					</div>
				</div>
			</div>
			
		</div>
		<script type="text/javascript">
			$('ftp_mensaje_btSi').addEvent('click', function()
			{
				refrescar('tareas/index.php?controlador=cargar_proyecto&accion=upload_bloque&pftp_anio=<?php echo $expediente['pftp_anio']; ?>&pftp_tipo=<?php echo $expediente['pftp_tipo']; ?>&pftp_numero=<?php echo $expediente['pftp_numero']; ?>&se_vuelve=si' ,'contenidoAjaxCargarProyecto');
			});
										
			var menuDrag = new Drag.Move($('contenidoAjaxCargarProyecto'), {
			   handle: $('dragger_carga_proyectos')
			  }
			);
		</script>
    <?php
    }
	
	// MUESTRA EL CONTENIDO DEL DIRECTORIO temporal, DONDE LOS BLOQUES CARGARON LOS PROYECTOS
    public function mostrar_contenido($directorio_proyectos_para_cargar, $clave) {
    ?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>
		
		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxPasarProyectos" class="proy_contenedor_cargar_proyecto">
			<div id="dragger_documentos_para_cargar" class="proy_titulo_superior_directorio degradado">Documentos para cargar</div>
			
			<form action="tareas/index.php" method="POST" name="formPasarProyectos" id="formPasarProyectos">
				<div class="proy_contenido_directorio">
				
					<input type="hidden" name="controlador" id="controlador" value="cargar_proyecto" />
					<input type="hidden" name="accion" id="accion" value="pasar_proyectos" />
					<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
					
					<input type="hidden" name="f_anio" id="f_anio" value="<?php echo $clave['anio']; ?>" />
					<input type="hidden" name="f_tipo" id="f_tipo" value="<?php echo $clave['tipo']; ?>" />
					<input type="hidden" name="f_numero" id="f_numero" value="<?php echo $clave['numero']; ?>" />
					<input type="hidden" name="f_cuerpo" id="f_cuerpo" value="<?php echo $clave['cuerpo']; ?>" />
					<input type="hidden" name="f_alcance" id="f_alcance" value="<?php echo $clave['alcance']; ?>" />
					
					<table width="95%" border="0" cellpadding="0" cellspacing="0" class="e_tabla_texto">
						<tbody>
							<?php
							if ( $dir_temporal = @opendir($directorio_proyectos_para_cargar) ) {
								// Se crea el array para cargar los archivos
								$listado_archivos = array();

								while ( false !== ( $archivo = readdir($dir_temporal) ) ) {
									if ( $archivo != '..' && $archivo != '.' )
										$listado_archivos[] = $archivo;
								}
								// Se ordena el array
							    sort($listado_archivos);

							    // Se muestra el contenido del array
							    foreach($listado_archivos as $nombre_archivo) {

						        	// Se toma la extensión en minúscula
									$extension = strtolower(substr($nombre_archivo, -4));
									
									if ( $extension === ".doc" ) {
									?>
										<tr>
											<td>&nbsp;<?php echo $nombre_archivo; ?></td>
											<td><input type="checkbox" name="<?php echo $nombre_archivo; ?>" value="<?php echo $nombre_archivo; ?>" /></td>
										</tr>
									<?php
									}
								}
							    // Se cierra el directorio temporal ("proyectos/temporal/")
								closedir($dir_temporal);
							}
							?>
						</tbody>
					</table>
				</div>
				<div class="proy_espacio_cargar_proyectos"></div>
				<div class="proy_margen_inferior_directorio">
					<div id="proy_btVolver" class="proy_boton_cargar_proyectos degradado">
						<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Cerrar
					</div>
					<div id="proy_btPasar" class="proy_boton_cargar_proyectos degradado">
						<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Cargar
					</div>
				</div>
			</form>
		</div>
		<script>
			// Se envía el formulario para finalizar con la carga de la digitalización
			$('proy_btVolver').addEvent('click', function() {
				refrescar('abms/index.php?controlador=expedientes&accion=listar&anio=<?php echo $clave['anio']; ?>&tipo=<?php echo $clave['tipo']; ?>&numero=<?php echo $clave['numero']; ?>&cuerpo=<?php echo $clave['cuerpo']; ?>&alcance=<?php echo $clave['alcance']; ?>&sentido=anterior', 'contenidoAjaxPrincipal');
			});

			$('proy_btPasar').addEvent('click', function(){
				enviarForm('formPasarProyectos', 'tareas', 'contenidoAjaxPasarProyectos');
			});
						
		    var menuDrag = new Drag.Move($('contenidoAjaxPasarProyectos'), {
			   handle: $('dragger_documentos_para_cargar')
			  }
			);
			
		</script>
    <?php
    }
    
    public function preguntar_por_original_doc($directorio_desde, $directorio_destino, $documento, $nombre_original_existente, $anio, $nombre)
    {
    	$tipo        = substr($nombre, 2, 1);
		$nombre_tipo = ( $tipo == 'E' ) ? "el Expediente" : "la Nota";
		$numero      = (int)substr($nombre, 3, 5);
    ?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>
		
		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxPasarProyectos" class="proy_contenedor_cargar_proyecto">
			
			<div id="ftp_contenedora_mensaje">
				<div id="dragger_carga_proyectos" class="msc_titulos degradado">Carga de Proyectos</div>
				<div style="height:5px;font-size:0;"></div>
				<div class="ftp_mensaje_texto">El documento <span>original.doc</span> en <?php echo $nombre_tipo."   <span>".$anio." ".$tipo." ".$numero; ?></span> ya existe, desea reemplazarlo o agregarlo?</div>
				<div style="height:70px;font-size:0;"></div>
				<div style="height:20px;font-size:0;">
					<div class="ftp_mensaje_margen"></div>
					<div id="ftp_mensaje_btCancelar" class="ftp_mensaje_boton degradado">
						<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Cancelar
					</div>
					<div class="ftp_mensaje_margen"></div>
					<div id="ftp_mensaje_btAgregar" class="ftp_mensaje_boton degradado">
						<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Agregar
					</div>
					<div class="ftp_mensaje_margen"></div>
					<div id="ftp_mensaje_btSobreescribir" class="ftp_mensaje_boton degradado">
						<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Reemplazar
					</div>
				</div>
			</div>
		
		</div>
		<script type="text/javascript">
			
			$('ftp_mensaje_btSobreescribir').addEvent('click', function()
			{
				refrescar('tareas/index.php?controlador=cargar_proyecto&accion=sobreescribirOriginal&directorio_desde=<?php echo $directorio_desde; ?>&directorio_destino=<?php echo $directorio_destino; ?>&documento=<?php echo $documento; ?>&nombre_original_existente=<?php echo $nombre_original_existente; ?>&clave_a_separar=<?php echo $nombre; ?>&f_anio='+$('f_anio').value+'&f_tipo='+$('f_tipo').value+'&f_numero='+$('f_numero').value+'&f_cuerpo='+$('f_cuerpo').value+'&f_alcance='+$('f_alcance').value, 'contenidoAjaxPasarProyectos');
			});
			
			$('ftp_mensaje_btAgregar').addEvent('click', function()
			{
				refrescar('tareas/index.php?controlador=cargar_proyecto&accion=renombrarOriginalAnterior&directorio_desde=<?php echo $directorio_desde; ?>&directorio_destino=<?php echo $directorio_destino; ?>&documento=<?php echo $documento; ?>&nombre_original_existente=<?php echo $nombre_original_existente; ?>&clave_a_separar=<?php echo $nombre; ?>&f_anio='+$('f_anio').value+'&f_tipo='+$('f_tipo').value+'&f_numero='+$('f_numero').value+'&f_cuerpo='+$('f_cuerpo').value+'&f_alcance='+$('f_alcance').value, 'contenidoAjaxPasarProyectos');
			});
						
			$('ftp_mensaje_btCancelar').addEvent('click', function()
			{
				refrescar('tareas/index.php?controlador=cargar_proyecto&accion=consultarUsuario&f_anio='+$('f_anio').value+'&f_tipo='+$('f_tipo').value+'&f_numero='+$('f_numero').value+'&f_cuerpo='+$('f_cuerpo').value+'&f_alcance='+$('f_alcance').value, 'contenidoAjaxPasarProyectos');
			});
						
			var menuDrag = new Drag.Move($('contenidoAjaxPasarProyectos'), {
			   handle: $('dragger_carga_proyectos')
			  }
			);
			
		</script>
    <?php
    }

    /**
     * Se vuelve al listado principal de expedientes, informando al usuario del documento no encontrado en el sistema
     *
     * @param array $clave
     * @param string $mensaje
    */
    public function volverListadoPrincipal($clave, $mensaje) {
    ?>
       	<script>
    		// Se vuelve al listado principal de expedientes, informando al usuario del documento no encontrado en el sistema
    		refrescar('abms/index.php?controlador=expedientes&accion=listar&anio=<?php echo $clave['anio']; ?>&tipo=<?php echo $clave['tipo']; ?>&numero=<?php echo $clave['numero']; ?>&cuerpo=<?php echo $clave['cuerpo']; ?>&alcance=<?php echo $clave['alcance']; ?>&sentido=anterior&mensaje=<?php echo $mensaje; ?>&tipo_mensaje=2', 'contenidoAjaxPrincipal');
    	</script>
    <?php
    }
}
?>
