<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaVerificarDigitalizacion extends VistaBase
{
    private $controlador;
    private $formulario;
    
    public function __construct() {
		$this->controlador = 'verificar_digitalizacion';
		$this->formulario = 'formVerificarDigitalizacion';
    }
    
    public function verificar_digitalizacion() {
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>
		
		<div id="precarga_modal" style="display:none"></div>
		<div id="contenidoAjaxVerificacionDigitalizacion" class="msc_gral msc_texto">
			
			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>
			
			<form action="tareas/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">
					
				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="verificar_digitalizacion" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="f_enviado" id="f_enviado" value="enviado" />
				
				<div id="dragger_verificar_digitalizacion" class="msc_titulos degradado">Verificar Digitalizaci&oacute;n</div>
				<div style="height:10px;font-size:0;"></div>
				<div style="height:32px;">
					<div class="mscpa_margen_medio_buscador"></div>
					<div class="mscpa_dato_buscador">
						<strong>A&ntilde;o:</strong> <input type="text" name="vd_f_anio" id="vd_f_anio" value="<?php echo $filtro['vd_f_anio']; ?>" onKeyPress="return soloEnteros(event);" size="4" maxlength="4" tabindex="1" />
					</div>
					
					<div class="mscpa_dato_buscador">
						<strong>N&uacute;mero:</strong> <input type="text" name="vd_f_numero" id="vd_f_numero" value="<?php echo $filtro['vd_f_numero']; ?>" onKeyPress="return soloEnteros(event);" size="4" maxlength="10" tabindex="2" />
					</div>
					
					<div class="mscpa_dato_buscador">
						<strong>D&iacute;gito:</strong> <input type="text" name="vd_f_digito" id="vd_f_digito" value="<?php echo $filtro['vd_f_digito']; ?>" size="2" maxlength="2" tabindex="3" />
					</div>
					<div class="mscpa_margen_medio_buscador"></div>
					<div id="msc_btBuscar_antecedente" class="mscpa_boton degradado" tabindex="4">
						<a href="javascript:buscarExpediente_DE();" >
							<img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Buscar
						</a>
					</div>
					<div class="mscpa_margen_medio_buscador"></div>
					<div class="mscpa_boton degradado" tabindex="5">
						<a href="javascript:cerrarModal('<?php echo $_SESSION['clave_expediente_referenciado']['anio']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['tipo']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['numero']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['cuerpo']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['alcance']; ?>');">
							<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Cerrar
						</a>
					</div> 
				</div>
			</form>
		</div>
		<script>
			
			//$('header').setStyle('display','none');
			//$('p_menu_ocultado').setStyle('display','block');
			
			function buscarExpediente_DE()
			{
				var mensaje = "";
				var error = false;
				
				if ($('vd_f_anio').value == '')
				{
					error = true;
					mensaje += "Debe ingresar el A"+'\u00f1'+"o.<br>";
				}
				
				if ($('vd_f_numero').value == '')
				{
					error = true;
					mensaje += "Debe ingresar el N"+'\u00fa'+"mero.<br>";
				}
				
				if ($('vd_f_digito').value == '')
				{
					error = true;
					mensaje += "Debe ingresar el D"+'\u00ed'+"gito.<br>";
				}
				
				if (error)
				{
					alert(mensaje);
				}
				else
				{	
					enviarForm('formVerificarDigitalizacion', 'tareas', 'contenidoAjaxVerificacionDigitalizacion');
				}	
			}
			
		    var menuDrag = new Drag.Move($('contenidoAjaxVerificacionDigitalizacion'), {
				handle: $('dragger_verificar_digitalizacion')
			});
			
			setfocus('vd_f_anio');
		</script>		
    <?php
    }
	
	public function mostrar_contenido_directorio_ejecutivo($ruta_documentos_ejecutivo, $expediente) {
	?>
	    <div id="contenido_directorio_ejecutivo">
			<div id="dragger_verificar_digitalizacion" class="msc_titulos degradado">
				<?php echo "Documentos digitalizados del Expediente ".$expediente['vd_f_anio']." D ".$expediente['vd_f_numero']; ?>
			</div>
			<div id="tareas_contenido_directorio_expe_ejecutivo">
				<?php    
				$digitalizado = false;
				
				if ( $dir_abierto = @opendir($ruta_documentos_ejecutivo) ) {
					while ( false !== ( $file = readdir($dir_abierto) ) ) {
						if ( $file != '..' && $file != '.' ) 
						{
							$digitalizado = true;
							$extension = substr($file, -3);
							echo "&nbsp;".$this->obtenerImagenExtension($extension);
							echo "&nbsp;".$file;
							echo "<br>";
						}
					}
					closedir($dir_abierto);
				}
				else
					echo "El expediente no posee documentos digitalizados.";
				?>
			</div>
			<div class="tareas_contenido_directorio_expe_ejecutivo_boton_cerrar">
				<div class="mscpa_boton degradado derecha">
					<a href="javascript:cerrarModal('<?php echo $_SESSION['clave_expediente_referenciado']['anio']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['tipo']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['numero']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['cuerpo']; ?>', '<?php echo $_SESSION['clave_expediente_referenciado']['alcance']; ?>');">
						<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;&nbsp;Cerrar
					</a>
				</div>
			</div>
	    </div>
	    <script>
			<?php
			// SI POSEE DOCUMENTOS DIGITALIZADOS
			if ( $digitalizado )
			{
			?>
				$('tareas_contenido_directorio_expe_ejecutivo').setStyles({'color':'#30AD23', 'text-align':'left'});
			<?php
			}
			else
			{
			?>
				$('tareas_contenido_directorio_expe_ejecutivo').setStyles({'color':'#BE1D15', 'text-align':'center', 'font-size':'17px'});
			<?php
			}
			?>
			
		    var menuDrag = new Drag.Move($('contenido_directorio_ejecutivo'), {
				handle: $('dragger_verificar_digitalizacion')
			});
		</script>
	<?php
	}
	
	public function obtenerImagenExtension($extension)
	{
		switch ($extension)
		{
			case 'doc':
			case 'DOC':
				$img_extension = '<img src="imagenes/iconos_office/doc.jpg" width="17" height="17" >';
				break;
			case 'pdf':
			case 'PDF':
				$img_extension = '<img src="imagenes/iconos_office/pdf.jpg" width="17" height="17" >';
				break;
			case 'xls':
			case 'XLS':
			case 'ods':
				$img_extension = '<img src="imagenes/iconos_office/xls.jpg" width="17" height="17" >';
				break;
			case 'ppt':
			case 'PPT':
				$img_extension = '<img src="imagenes/iconos_office/ppt.jpg" width="17" height="17" >';
				break;	
			case 'rar':
			case 'RAR':
				$img_extension = '<img src="imagenes/iconos_office/rar.jpg" width="17" height="17" >';
				break;
			default:
				$img_extension = '<img src="imagenes/iconos_office/archivo.jpeg" width="17" height="17" >';
				break;
		}
		
		return $img_extension;
	}
	
}
?>
