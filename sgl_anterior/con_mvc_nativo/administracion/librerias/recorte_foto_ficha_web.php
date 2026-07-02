<?php
/****************************************************************
* SCRIPT PARA BUSCAR, ELEGIR Y RECORTAR UNA IMAGEN Y 
* GUARDARLA EN UN DIRECTORIO DETERMINADO Y EN LA BASE DE DATOS
*****************************************************************/
if (!isset($_SESSION)) {
	session_start();
}

// Se incluyen las rutas definidas
include_once realpath($_SERVER['DOCUMENT_ROOT']) . '/sgl/administracion/librerias/definiciones.php';

// CLASE CON METODOS ESTATICOS PARA UTILIZAR EN TODO EL SISTEMA
require_once RUTA_LIBRERIAS . "LibreriaGeneral.php";

// CLASE BASE DE LOS Modelos PARA TRABAJAR CON MySQLi en la DB hcd
require_once RUTA_LIBRERIAS_SGL . "modelo_base_mysqli.php";

// Se incluye el modelo
require_once RUTA_MODELOS . "ficha_web.php";

// Se crea una instancia del modelo
$modelo = new fichaWebModel();

// Se recibe el codigo
if ( isset($_GET["codigo"]) && $_GET["codigo"] != '' )
	$codigo = $_GET["codigo"];

// Se recibe el nombre de la foto actual
if ( isset($_GET["nombre_foto"]) && $_GET["nombre_foto"] != '' )
	$nombre_foto = $_GET["nombre_foto"];

// Se recibe la extensión de la foto cargada
if ( isset($_GET["extension"]) && $_GET["extension"] != '' )
	$extension = $_GET["extension"];

// La ruta del directorio donde se guardan las fotos
$ruta_directorio_fotos = RUTA_FOTOS_FICHAS_AUTORIDADES;

// Tamaño máximo del archivo de la foto en MB (1Mb=1048576)
$tamanio_en_mega = TAMANIO_MAXIMO_FOTO / 1048576;

// Extensiones permitidas para las fotos
$extensiones_fotos_permitidas = array("jpeg", "jpg", "png", "gif");

/*************************************************
	Si NO EXISTE aún el DIRECTORIO de las fotos
**************************************************/
if ( !is_dir($ruta_directorio_fotos) ) {
	// Se crea el directorio de las fotos
	mkdir($ruta_directorio_fotos, 0777);
	// Con los permisos respectivos
	chmod($ruta_directorio_fotos, 0777);
}
/*********************************************************
	Aquí se SUBE la FOTO ORIGINAL para su futuro recorte
*********************************************************/
if ( isset($_POST["upload"]) )
{
	$codigo = $_POST['codigo'];
	
	// Se recibe la información del archivo de la foto
	$imagen_nombre = $_FILES['image']['name'];
	$imagen_archivo = $_FILES['image']['tmp_name'];
	$imagen_tamanio = $_FILES['image']['size'];
	
	// Se toma la extensión del archivo y se convierte a minúscula
	$extension = strtolower(pathinfo($imagen_nombre, PATHINFO_EXTENSION));
	
	// Si se ha recibido la imagen
	if ( (!empty($_FILES["image"])) && ($_FILES['image']['error'] === 0) )
	{
		// Si su extensión no es válida
		if (!in_array($extension, $extensiones_fotos_permitidas)) {
			$mensaje_error = "La extensi&oacute;n de " . $imagen_nombre . " no es v&aacute;lida.";
		}
		else {
			// Se verifica si el tamaño de la foto supera el tamaño máximo permitido
			if ($imagen_tamanio > TAMANIO_MAXIMO_FOTO)
				$mensaje_error .= "El tama&ntilde;o de la imagen debe ser menor a ".$tamanio_en_mega."MB";
		}
	}
	else {
		$mensaje_error = "Debe seleccionar una imagen a cargar";
	}

	// Si no se han encontrado errores, es posible cargar la imagen
	if (strlen($mensaje_error) == 0) {
		// Si se ha subido un archivo
		if (isset($_FILES['image']['name'])) {
			// Se arma la ruta completa de la foto a recortar
			$ubicacion_imagen_a_recortar = $ruta_directorio_fotos."normal_".$codigo.".".$extension;

			move_uploaded_file($imagen_archivo, $ubicacion_imagen_a_recortar);

			chmod($ubicacion_imagen_a_recortar, 0777);
			
			$width = LibreriaGeneral::getWidth($ubicacion_imagen_a_recortar);
			$height = LibreriaGeneral::getHeight($ubicacion_imagen_a_recortar);

			// Escala la imagen si es mayor que el ancho establecido
			if ($width > FICHA_WEB_ANCHO_MAXIMO_FOTO_A_RECORTAR) {
				$escala   = FICHA_WEB_ANCHO_MAXIMO_FOTO_A_RECORTAR / $width;
				$uploaded = LibreriaGeneral::resizeImage($ubicacion_imagen_a_recortar, $width, $height, $escala);
			} else {
				$escala   = 1;
				$uploaded = LibreriaGeneral::resizeImage($ubicacion_imagen_a_recortar, $width, $height, $escala);
			}
		}

		// Se carga nuevamente la vista para mostrar la foto para su recorte
		header("location:".$_SERVER["PHP_SELF"]."?codigo=".$codigo."&extension=".$extension);
		exit();
	}
}
// Si se canceló el recorte de la foto
// ************************************************
if ( isset($_POST["cancelar_recorte"]) )
{
	$codigo    = $_POST['codigo'];
	$extension = $_POST['extension'];

	// Ruta completa de la foto a recortar
	$ruta_completa_foto_a_recortar = $ruta_directorio_fotos."normal_".$codigo.".".$extension;

	// Se elimina la foto "normal_" (la que se recortó)
    if (file_exists($ruta_completa_foto_a_recortar))
        unlink($ruta_completa_foto_a_recortar);

	// Se carga nuevamente la vista para mostrar la foto para su recorte
	header("location:".$_SERVER["PHP_SELF"]."?codigo=".$codigo);
	exit();
}
// Si se envió la información para RECORTAR LA FOTO
// ************************************************
if ( isset($_POST["upload_recorte"]) )
{
	$codigo    = $_POST['codigo'];
	$extension = $_POST['extension'];

	// Se obtienen las nuevas coordenadas para recortar la foto
	$x1 = $_POST["x1"];
	$y1 = $_POST["y1"];
	$x2 = $_POST["x2"];
	$y2 = $_POST["y2"];
	$w  = $_POST["w"];
	$h  = $_POST["h"];

	// Escala la imagen según el ancho del recorte punteado utilizado para el recorte
	$escala = ($w != '') ? FICHA_WEB_ANCHO_FOTO_RECORTE / $w : FICHA_WEB_ANCHO_FOTO_RECORTE;

	// Ruta completa de la foto a recortar
	$ruta_completa_foto_a_recortar = $ruta_directorio_fotos."normal_".$codigo.".".$extension;

	$nombre_foto = $codigo.".".$extension;

	// Ruta donde se guardará la foto carnet
	$ruta_completa_foto = $ruta_directorio_fotos.$nombre_foto;

	// Se genera la foto recortada en base a la imagen original y la información del recorte
	$recorte = LibreriaGeneral::resizeThumbnailImage(
		$ruta_completa_foto, 
		$ruta_completa_foto_a_recortar, 
		$w, $h, $x1, $y1, 
		$escala);

	// Se elimina la foto "normal_" (la que se recortó)
	if (file_exists($ruta_completa_foto_a_recortar))
		unlink($ruta_completa_foto_a_recortar);
	
	// Se registra el nombre de la foto en la base de datos
	$resultado_carga_foto = ( $modelo->registrarNombreFoto($codigo, $nombre_foto) ) ? 1 : 2;
}
?>
<!DOCTYPE html>
<html lang="es" >
	<head>
		<meta charset="UTF-8">

		<!--  Librería viejita de jQuery (v1.2.6) -->
		<script type="text/javascript" src="<?=URL_JS_LIBRERIAS;?>jquery-pack.js"></script>

		<!--  Librería jQuery imgAreaSelect v 0.6.1, utilizada para el recorte de la foto -->
		<script type="text/javascript" src="<?=URL_JS_LIBRERIAS;?>jquery.imgareaselect.min.js"></script>

		<!-- CSS para el recorte de la foto -->
		<link rel="stylesheet" href="<?=URL_CSS;?>recorte_foto.css" />
	</head>
	<body>
		<div>
			<?php
			$ruta_completa_foto_a_recortar = $ruta_directorio_fotos."normal_".$codigo.".".$extension;
			$url_completa_foto_a_recortar = URL_FOTOS_FICHAS_AUTORIDADES."normal_".$codigo.".".$extension;
			
			// Solamente carga el Javascript si la imagen ha sido cargada
			if ( file_exists($ruta_completa_foto_a_recortar) )
			{
				$current_large_image_width  = LibreriaGeneral::getWidth($ruta_completa_foto_a_recortar);
				$current_large_image_height = LibreriaGeneral::getHeight($ruta_completa_foto_a_recortar);
				?>
				<script type="text/javascript">

					function preview(img, selection)
					{ 
						var scaleX = <?= FICHA_WEB_ANCHO_FOTO_RECORTE; ?> / selection.width; 
						var scaleY = <?= FICHA_WEB_ALTO_FOTO_RECORTE; ?> / selection.height; 
						
						$('#thumbnail + div > img').css({ 
							width: Math.round(scaleX * <?= $current_large_image_width; ?>) + 'px', 
							height: Math.round(scaleY * <?= $current_large_image_height; ?>) + 'px',
							marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
							marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
						});
						$('#x1').val(selection.x1);
						$('#y1').val(selection.y1);
						$('#x2').val(selection.x2);
						$('#y2').val(selection.y2);
						$('#w').val(selection.width);
						$('#h').val(selection.height);
					} 

					$(document).ready(function () { 
						$('#save_thumb').click(function() {
							var x1 = $('#x1').val();
							var y1 = $('#y1').val();
							var x2 = $('#x2').val();
							var y2 = $('#y2').val();
							var w = $('#w').val();
							var h = $('#h').val();

							if (x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h=="") {
								alert("Debe realizar una seleccion");
								return false;
							} else
								return true;
						});
					}); 

					$(window).load(function () { 
						$('#thumbnail').imgAreaSelect({ 
							aspectRatio: '1',
							onSelectChange: preview 
						}); 
					});
				</script>
				<?php
			}
			
			// Si posee foto
			if ( $nombre_foto != '' )
			{
				// Si se acaba de CARGAR RECIÉN
				if ( isset($resultado_carga_foto) && $resultado_carga_foto == 1 ) { ?>
					<script>
						top.location.href = '<?=URL_ABMS;?>?controlador=ficha_web&accion=editar&legajo=<?=$codigo;?>';
					</script>
				<?php
				}
				else // Se cambia la foto, se elige y se sube para su recorte.
				{ 
				?>
					<form id="formCargarFotoParaRecorte" name="photo" action="<?= $_SERVER["PHP_SELF"];?>" method="post" enctype="multipart/form-data">

						<input type="hidden" id="codigo" name="codigo" value="<?= (isset($codigo)) ? $codigo : ''; ?>" />
						<input type="hidden" id="upload" name="upload" value="Subir" />
						
						<div class="p_edicion_modal_botones">
							<div class="modal_texto_informativo">
								<h5>Presione el bot&oacute;n Examinar para elegir la foto a cargar.</h5>
							</div>
							<div class="input_file_personalizado" id="inputFoto">
								<input type="file" name="image" id="image" value="" size="1" class="input_file" onchange="javascript:$('#formCargarFotoParaRecorte').submit();" />
								&nbsp;Examinar
							</div>
						</div>
					</form>
				<?php
				}
			}
			else // Si NO posee foto
			{
				$ruta_completa_foto_a_recortar = $ruta_directorio_fotos."normal_".$codigo.".".$extension;
				$url_completa_foto_a_recortar = URL_FOTOS_FICHAS_AUTORIDADES."normal_".$codigo.".".$extension;
				
				//	Si YA SE ELIGIÓ la foto A RECORTAR
				if ( file_exists($ruta_completa_foto_a_recortar) ) {
				?>
					<div class="p_edicion_modal_botones">
						<div class="modal_texto_informativo">
							<h5>Verifique la selecci&oacute;n del &aacute;rea de recorte de la foto.</h5>
						</div>
						<div class="p_boton_en_modal_foto">
							<form id="formRecorte" name="formRecorte" action="<?= $_SERVER["PHP_SELF"]; ?>" method="post">

								<input type="hidden" name="x1" value="" id="x1" />
								<input type="hidden" name="y1" value="" id="y1" />
								<input type="hidden" name="x2" value="" id="x2" />
								<input type="hidden" name="y2" value="" id="y2" />
								<input type="hidden" name="w" value="" id="w" />
								<input type="hidden" name="h" value="" id="h" />

								<input type="hidden" id="codigo" name="codigo" value="<?= (isset($codigo)) ? $codigo : ''; ?>" />
								<input type="hidden" id="extension" name="extension" value="<?= (isset($extension)) ? $extension : ''; ?>" />

								<input type="hidden" id="upload_recorte" name="upload_recorte" value="algo" />
								
								<a href="javascript:$('#formRecorte').submit();">
									&nbsp;Recortar
								</a>
							</form>
						</div>
						<div class="p_boton_en_modal_foto">
							<form id="formCancelarRecorte" name="formCancelarRecorte" action="<?= $_SERVER["PHP_SELF"]; ?>" method="post">

								<input type="hidden" id="codigo" name="codigo" value="<?= (isset($codigo)) ? $codigo : ''; ?>" />
								<input type="hidden" id="cancelar_recorte" name="cancelar_recorte" value="algo" />

								<a href="javascript:$('#formCancelarRecorte').submit();">
									&nbsp;Cancelar
								</a>
							</form>
						</div>
					</div>
				
					<!-- Aquí se crea el recorte -->
					<div class="modal_contenedor_foto_para_recorte">
						<!-- Se muestra la imagen elegida para tomar el recorte -->
						<img src="<?= $url_completa_foto_a_recortar; ?>" id="thumbnail" alt="Crear Miniatura" />
					</div>

					<script type="text/javascript">
						
						$(document).ready(function () {

							// Se calcula y se muestra el área de selección sobre la imagen a recortar "thumbnail"
							function addImgAreaSelect( img ) {

						        img.addClass('imgAreaSelect').imgAreaSelect({
					                handles : true,
					                aspectRatio : '1',
					                fadeSpeed : 1,
					                show : true
						        });

						        // Se muestra la selección inicial con la relación de aspecto de 16:9
						        img.load(function() {

						        	// Se definen las coordenadas
		                            var coordenadas = { 
		                            	x1 : 0,
		                            	y1 : 0,
		                            	x2 : 160,
		                            	y2 : 160
		                            };
		                            
				                    // Se establecen las coordenadas de la selección
					                $(this).imgAreaSelect(coordenadas);
						        });
							}

							// Se aplica el área de selección a la foto elegida
							addImgAreaSelect($('#thumbnail'));
						});
					</script>
				<?php
				}
				else // Se elige la foto y se sube para su recorte
				{
				?>
					<form id="formCargarFotoParaRecorte" name="photo" action="<?= $_SERVER["PHP_SELF"];?>" method="post" enctype="multipart/form-data">
						<input type="hidden" id="codigo" name="codigo" value="<?= (isset($codigo)) ? $codigo : ''; ?>" />
						<input type="hidden" id="upload" name="upload" value="Subir" />
						<div class="p_edicion_modal_botones">
							<div class="modal_texto_informativo">
								<h5>Presione en Examinar para elegir la foto a cargar.</h5>
							</div>
							<div class="input_file_personalizado" id="inputFoto">
								<input type="file" name="image" id="image" value="" size="1" class="input_file" onchange="javascript:$('#formCargarFotoParaRecorte').submit();" />
								&nbsp;Examinar
							</div>
						</div>
					</form>
				<?php
				}
			}
			?>
		</div>
	</body>
</html>