<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaCargarDocumentosDeptoEjecutivo
{
    private $controlador;
    private $formulario;

    public function __construct()
    {
		$this->controlador = 'cargar_documentos_depto_ejecutivo';
		$this->formulario  = 'formCargarDocumentos';
    }

    /**
     * Se muestra el contenido del directorio de documentos del DE, de un expediente determinado
     * @param  [string] $ruta_documentos_ejecutivo_para_cargar [description]
     * @param  [string] $directorio_destino                    [description]
     * @param  [string] $f_criterio_busqueda                   [description]
     */
    public function mostrar_contenido($ruta_documentos_ejecutivo_para_cargar, $directorio_destino, $f_criterio_busqueda)
    {
		$extensiones_no_permitidas = Array(".exe", ".pif", ".inf");
    ?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxCargaDocumentosEjecutivo" class="proy_contenedor_cargar_proyecto">

			<div id="dragger_carga_documentos_ejecutivo" class="msc_titulos degradado">Documentos para cargar</div>

			<form action="tareas/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">
				<div class="proy_cont_directorio">

					<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
					<input type="hidden" name="accion" id="accion" value="pasar_documentos_ejecutivo" />
					<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
					<input type="hidden" name="ruta_documentos_ejecutivo_para_cargar" id="ruta_documentos_ejecutivo_para_cargar" value="<?php echo $ruta_documentos_ejecutivo_para_cargar; ?>" />
					<input type="hidden" name="directorio_destino" id="directorio_destino" value="<?php echo $directorio_destino; ?>" />

					<input type="hidden" name="criterio_anio" id="criterio_anio" value="<?php echo $f_criterio_busqueda['criterio_anio']; ?>" />
					<input type="hidden" name="criterio_tipo" id="criterio_tipo" value="<?php echo $f_criterio_busqueda['criterio_tipo']; ?>" />
					<input type="hidden" name="criterio_numero" id="criterio_numero" value="<?php echo $f_criterio_busqueda['criterio_numero']; ?>" />
					<input type="hidden" name="criterio_cuerpo" id="criterio_cuerpo" value="<?php echo $f_criterio_busqueda['criterio_cuerpo']; ?>" />
					<input type="hidden" name="criterio_alcance" id="criterio_alcance" value="<?php echo $f_criterio_busqueda['criterio_alcance']; ?>" />

					<table width="100%" border="0" cellpadding="0" cellspacing="0" class="e_tabla_texto">
						<tbody>
							<?php
							// SE ABRE EL DIRECTORIO DE DOCUMENTOS DEL EJECUTIVO
							if ( $dir_temporal = @opendir($ruta_documentos_ejecutivo_para_cargar) ) {

								// Se crea el array para cargar los archivos
								$listado_archivos = array();

								// SE RECORRE DICHO DIRECTORIO
								while ( false !== ( $archivo = readdir($dir_temporal) ) ) {
									// SI EL ARCHIVO ES UN DIRECTORIO DISTINTO DE . Y .., Y ES PERMITIDA SU EXTENSION
									if ( $archivo != '..' && $archivo != '.' && !in_array(strtolower(substr($archivo, -4)), $extensiones_no_permitidas) )
										$listado_archivos[] = $archivo;
								}
								// Se ordena el array
							    sort($listado_archivos);

							    // Se recorre el listado ordenado
								foreach($listado_archivos as $nombre_archivo) {
									// SE MUESTRA SÓLO SI ES UN ARCHIVO
									if ( is_file($ruta_documentos_ejecutivo_para_cargar.'/'.$nombre_archivo) ) {
									?>
										<tr>
											<td>&nbsp;<?php echo $nombre_archivo; ?></td>
											<td><input type="checkbox" name="documento[]" value="<?php echo $nombre_archivo; ?>" checked /></td>
										</tr>
									<?php
									}

								}
								// SE CIERRA EL DIRECTORIO DE DOCUMENTOS DEL EJECUTIVO
								closedir($dir_temporal);
							}
							?>
						</tbody>
					</table>
				</div>
				<div class="proy_espacio_cargar_proyectos"></div>
				<div class="proy_margen_inferior_directorio">
					<div id="doc_ejecutivo_btVolver" class="proy_boton_cargar_proyectos degradado">
						<a href="#"><img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Cerrar</a>
					</div>
					<div id="proy_btPasar" class="proy_boton_cargar_proyectos degradado">
						<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Cargar
					</div>
				</div>
			</form>
		</div>
		<script>

			$('doc_ejecutivo_btVolver').addEvent('click', function(){
				$("capaFondo").setStyle('visibility','hidden');
				$("capaVentana").setStyle('visibility','hidden');
			});

			$('proy_btPasar').addEvent('click', function(){
				enviarForm('<?php echo $this->formulario; ?>', 'tareas', 'contenidoAjaxCargaDocumentosEjecutivo');
			});

		    var menuDrag = new Drag.Move($('contenidoAjaxCargaDocumentosEjecutivo'), {
			   handle: $('dragger_carga_documentos_ejecutivo')
			});

		</script>
    <?php
    }

    /**
     * Agregado el 19/02/2019 por XXXX
     * Se le pregunta al usuario si desea sobreescribir un documento determinado
     * @param  [string] $directorio_desde   [description]
     * @param  [string] $directorio_destino [description]
     * @param  [string] $documento          [description]
     * @param  [string] $anio               [description]
     */
    public function preguntarPorSobreescritura($directorio_desde, $directorio_destino, $documento, $anio)
    {
		//	$directorio_desde   Ejemplo: /var/www/sgl/expedientes/expe-de/2018/2018-001122-1
		//	$directorio_destino Ejemplo: 18E02180

    	$nombre_tipo = ( $_SESSION['criterio_tipo'] == 'E' ) ? "del Expediente" : "de la Nota";
    ?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxPasarDocumentosDelDE" class="proy_contenedor_cargar_proyecto">

			<input type="hidden" name="ppde_directorio_desde" id="ppde_directorio_desde" value="<?php echo $directorio_desde; ?>" />
			<input type="hidden" name="ppde_directorio_destino" id="ppde_directorio_destino" value="<?php echo $directorio_destino; ?>" />
			<input type="hidden" name="ppde_documento" id="ppde_documento" value="<?php echo $documento; ?>" />
			<input type="hidden" name="ppde_anio" id="ppde_anio" value="<?php echo $anio; ?>" />

			<div id="ftp_contenedora_mensaje">
				<div id="dragger_carga_documentos_ejecutivo" class="msc_titulos degradado">Documentos para cargar</div>
				<div style="height:5px;font-size:0;"></div>
				<div class="ftp_mensaje_texto">
					El documento <span><?php echo $documento; ?></span> <?php echo $nombre_tipo." <span>".$_SESSION['criterio_anio']." ".$_SESSION['criterio_tipo']." ".$_SESSION['criterio_numero']; ?></span> ya existe, desea reemplazarlo?
				</div>
				<div style="height:70px;font-size:0;"></div>
				<div style="height:20px;font-size:0;">
					<div class="ftp_mensaje_margen"></div>
					<div id="ftp_mensaje_btCancelar" class="ftp_mensaje_boton degradado">
						<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Cancelar
					</div>
					<div class="ftp_mensaje_margen"></div>
					<div id="ftp_mensaje_btSobreescribir" class="ftp_mensaje_boton degradado">
						<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Reemplazar
					</div>
				</div>
			</div>

		</div>
		<script type="text/javascript">
			// Al Cancelar
			$('ftp_mensaje_btCancelar').addEvent('click', function() {
				refrescar('tareas/index.php?controlador=<?php echo $this->controlador; ?>&accion=consultarUsuario', 'contenidoAjaxPasarDocumentosDelDE');
			});
			// Al Sobreescribir
			$('ftp_mensaje_btSobreescribir').addEvent('click', function() {
				refrescar('tareas/index.php?controlador=<?php echo $this->controlador; ?>&accion=sobreescribirDocumento&directorio_desde='+$('ppde_directorio_desde').value+'&directorio_destino='+$('ppde_directorio_destino').value+'&documento='+$('ppde_documento').value+'&anio='+$('ppde_anio').value, 'contenidoAjaxPasarDocumentosDelDE');
			});

			var menuDrag = new Drag.Move($('contenidoAjaxPasarDocumentosDelDE'), {
			   	handle: $('dragger_carga_documentos_ejecutivo')
			});
		</script>
    <?php
    }
    /**
    public function retornar($mensaje = '', $tipo_mensaje = '', $clave_expediente)
    {
		$_SESSION['mensaje'] = $mensaje;
		$_SESSION['tipo_mensaje'] = $tipo_mensaje;
    ?>
		<script type="text/javascript">
			// PARA VOLVER AL INICIO
			location.href = "../index.php?anio=<?php echo $clave_expediente['pftp_anio']; ?>&tipo=<?php echo $clave_expediente['pftp_tipo']; ?>&numero=<?php echo $clave_expediente['pftp_numero']; ?>&cuerpo=0&alcance=0&sentido=anterior";
		</script>
    <?php
    }
	/**/
}
?>
