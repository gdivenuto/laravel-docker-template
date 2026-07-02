<?php
/********************************************************************************************************************
* SCRIPT PARA BUSCAR, ELEGIR Y RECORTAR UNA IMAGEN Y GUARDARLA EN UN DIRECTORIO DETERMINADO Y EN LA BASE DE DATOS
*
* FUENTE: http://www.webmotionuk.com / http://www.webmotionuk.co.uk
*********************************************************************************************************************/
session_start();

// Clase que implementa el patrón singleton para centralización de la lógica de logueo de errores e
// información desde los scripts en PHP.
require_once($_SERVER['DOCUMENT_ROOT']."/sgl/librerias/Logger.php");

// Se incluye el modelo base para MySQL
require '../../librerias/modelo_base_mysqli.php';

// Se incluye el modelo de Personal
require 'modelos/personal.php';
/******************************************************************************************************************
	DEFINICIONES Y RECEPCIÓN DE PARÁMETROS
******************************************************************************************************************/
// Se crea una instancia del modelo de Personal
$modelo = new personalModel();

// Se recibe el legajo para anexar al nombre de la foto
if ($_GET["legajo"] != '')
	$legajo = $_GET["legajo"];

// Se recibe el nombre de la foto actual
if ($_GET["nombre_foto_carnet"] != '')
	$nombre_foto_carnet = $_GET["nombre_foto_carnet"];

// Se recibe la extensión de la foto cargada
if ($_GET["extension"] != '')
	$extension = $_GET["extension"];

$ruta_directorio_fotos        = "../fotos/";	// La ruta del directorio donde se guardan las fotos
$tamanio_maximo_archivo       = "10"; 			// Tamaño máximo del archivo de la foto en MB
$ancho_maximo_foto_a_recortar = "500";			// Ancho máximo permitido para la imagen a recortar
$ancho_miniatura              = "120";			// Ancho de la miniatura
$alto_miniatura               = "120";			// Alto de la miniatura

// Solamente se permiten subir imágenes de tipo: jpg, png y gif
$tipos_imagen_permitidos = array(
	'image/pjpeg' =>"jpg",
	'image/jpeg'  =>"jpg",
	'image/jpg'   =>"jpg",
	'image/png'   =>"png",
	'image/x-png' =>"png",
	'image/gif'   =>"gif"
);

$extensiones_imagen_permitidos = array_unique($tipos_imagen_permitidos);
$extension_imagen 			   = "";

foreach ($extensiones_imagen_permitidos as $mime_type => $ext)
    $extension_imagen .= strtoupper($ext)." ";

/******************************************************************************************************************
	FUNCIONES PARA REDIMENSIONAR LA IMAGEN (NO ES NECESARIO MODIFICARLAS)
******************************************************************************************************************/
function resizeImage($image,$width,$height,$scale)
{
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);
	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);

	switch($imageType)
	{
		case "image/gif":
			$source=imagecreatefromgif($image);
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image);
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image);
			break;
  	}

	imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);

	switch($imageType)
	{
		case "image/gif":
	  		imagegif($newImage,$image);
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$image,90);
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$image);
			break;
    }

	chmod($image, 0777);

	return $image;
}

/**
 * Se genera la foto recortada en base a la imagen original y la información del recorte
 *
 * @param  [type] $nombre_imagen_miniatura Nombre de la imagen recortada para la credencial
 * @param  [type] $image            [description]
 * @param  [type] $width            [description]
 * @param  [type] $height           [description]
 * @param  [type] $start_width      [description]
 * @param  [type] $start_height     [description]
 * @param  [type] $scale            [description]
 * @return [type]                   [description]
 */
function resizeThumbnailImage($nombre_imagen_miniatura, $image, $width, $height, $start_width, $start_height, $scale)
{
	list($imagewidth, $imageheight, $imageType) = getimagesize($image);
	$imageType = image_type_to_mime_type($imageType);

	$newImageWidth = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);
	$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);

	switch($imageType)
	{
		case "image/gif":
			$source=imagecreatefromgif($image);
			break;
	    case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
			$source=imagecreatefromjpeg($image);
			break;
	    case "image/png":
		case "image/x-png":
			$source=imagecreatefrompng($image);
			break;
  	}
	imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);

	switch($imageType)
	{
		case "image/gif":
	  		imagegif($newImage,$nombre_imagen_miniatura);
			break;
      	case "image/pjpeg":
		case "image/jpeg":
		case "image/jpg":
	  		imagejpeg($newImage,$nombre_imagen_miniatura,90);
			break;
		case "image/png":
		case "image/x-png":
			imagepng($newImage,$nombre_imagen_miniatura);
			break;
    }
	chmod($nombre_imagen_miniatura, 0777);

	return $nombre_imagen_miniatura;
}

/**
 * Devuelve el alto de la foto
 * @param  [type] $image Imagen para obtener su alto
 * @return int Devuelve el alto de la imagen
 */
function getHeight($image)
{
	$size = getimagesize($image);
	$height = $size[1];
	return $height;
}

/**
 * Devuelve el ancho de la foto
 * @param  [type] $image Imagen para obtener su ancho
 * @return int Devuelve el ancho de la imagen
 */
function getWidth($image)
{
	$size = getimagesize($image);
	$width = $size[0];
	return $width;
}
/******************************************************************************************************************
	Si NO EXISTE aún el DIRECTORIO de las fotos
******************************************************************************************************************/
if (!is_dir($ruta_directorio_fotos))
{
	// Se crea el directorio de las fotos
	mkdir($ruta_directorio_fotos, 0777);
	// Con los permisos respectivos
	chmod($ruta_directorio_fotos, 0777);
}
/******************************************************************************************************************
	Aquí se SUBE la FOTO ORIGINAL para su futuro recorte
******************************************************************************************************************/
if ( isset($_POST["upload"]) )
{
	$legajo = $_POST['legajo'];

	// Se recibe la información del archivo de la foto
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
	$userfile_size = $_FILES['image']['size'];
	$userfile_type = $_FILES['image']['type'];
	$filename = basename($_FILES['image']['name']);

	$extension_del_archivo = strtolower(substr($filename, strrpos($filename, '.') + 1));

	// Si se ha recibido la imagen
	if( (!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0) )
	{
		foreach ($tipos_imagen_permitidos as $mime_type => $ext)
		{
			// Se procesa solamente si el archivo de la foto es JPG, PNG o GIF
			if($extension_del_archivo == $ext && $userfile_type == $mime_type)
			{
				$mensaje_error = "";
				break;
			}
			else
				$mensaje_error = "S&oacute;lo im&aacute;genes de tipo <strong>".$extension_imagen."</strong> son permitidas<br />";
		}
		// Se verifica si el tamaño de la foto supera el tamaño máximo permitido
		if ($userfile_size > ($tamanio_maximo_archivo * 1048576))
			$mensaje_error .= "El tama&ntilde;o de la imagen debe ser menor a ".$tamanio_maximo_archivo."MB";
	} else
		$mensaje_error = "Debe seleccionar una imagen a cargar";

	// Si no se han encontrado errores, es posible cargar la imagen
	if (strlen($mensaje_error) == 0)
	{
		// Si se ha subido un archivo
		if (isset($_FILES['image']['name']))
		{
			// Se arma la ruta completa de la foto a recortar
			$ubicacion_imagen_a_recortar = $ruta_directorio_fotos."normal_".$legajo.".".$extension_del_archivo;

			move_uploaded_file($userfile_tmp, $ubicacion_imagen_a_recortar);
			chmod($ubicacion_imagen_a_recortar, 0777);

			$width = getWidth($ubicacion_imagen_a_recortar);
			$height = getHeight($ubicacion_imagen_a_recortar);

			//Scale the image if it is greater than the width set above
			if ($width > $ancho_maximo_foto_a_recortar)
			{
				$scale = $ancho_maximo_foto_a_recortar/$width;
				$uploaded = resizeImage($ubicacion_imagen_a_recortar,$width,$height,$scale);
			}
			else
			{
				$scale = 1;
				$uploaded = resizeImage($ubicacion_imagen_a_recortar,$width,$height,$scale);
			}
		}

		// Se carga nuevamente la vista para mostrar la foto para su recorte
		header("location:".$_SERVER["PHP_SELF"]."?legajo=".$legajo."&extension=".$extension_del_archivo );
		exit();
	}
}
/******************************************************************************************************************
	Si se envió la información para RECORTAR LA FOTO
******************************************************************************************************************/
if ( isset($_POST["upload_recorte"]) )
{
	$legajo = $_POST['legajo'];
	$extension = $_POST['extension'];

	// Se obtienen las nuevas coordenadas para recortar la foto
	$x1 = $_POST["x1"];
	$y1 = $_POST["y1"];
	$x2 = $_POST["x2"];
	$y2 = $_POST["y2"];
	$w  = $_POST["w"];
	$h  = $_POST["h"];

	// Escala la imagen según el ancho de la miniatura deseada (la del cuadrado punteado utilizado para el recorte)
	$scale = $ancho_miniatura/$w;

	// Ruta completa de la foto a recortar
	$ruta_completa_foto_a_recortar = $ruta_directorio_fotos."normal_".$legajo.".".$extension;

	$nombre_foto_carnet = "carnet_".$legajo.".".$extension;//."_".date("H_i_s")

	// Ruta donde se guardará la foto carnet
	$ruta_completa_foto_carnet = $ruta_directorio_fotos.$nombre_foto_carnet;

	// Se genera la foto recortada en base a la imagen original y la información del recorte
	$cropped = resizeThumbnailImage($ruta_completa_foto_carnet, $ruta_completa_foto_a_recortar,$w,$h,$x1,$y1,$scale);

	// Se elimina la foto original (la que se recortó)
	if (file_exists($ruta_completa_foto_a_recortar))
		unlink($ruta_completa_foto_a_recortar);

	// Se registra el nombre de la foto del Legajo en la base de datos
	$resultado_carga_foto_carnet = ( $modelo->registrarNombreFoto($legajo, $nombre_foto_carnet) ) ? 1 : 2;
}
?>
<!DOCTYPE html>
<html lang="es" >
	<head>
		<meta charset="UTF-8">
		<!--  Librería jQuery General-->
		<script type="text/javascript" src="../js/jquery-pack.js"></script>
		<!--  Librería jQuery utilizada para el recorte de la foto -->
		<script type="text/javascript" src="../js/jquery.imgareaselect.min.js"></script>
		<!-- CSS General -->
		<link rel="stylesheet" href="../css/general.css" />
	</head>
	<body>
		<div id="dragger_carga_foto" class="i_modal_titulo degradado">Carga de Foto</div>
		<div class="">
			<?php
			$ruta_completa_foto_a_recortar = $ruta_directorio_fotos."normal_".$legajo.".".$extension;

			// Solamente carga el Javascript si la imagen ha sido cargada
			if ( file_exists($ruta_completa_foto_a_recortar) )
			{
				$current_large_image_width = getWidth($ruta_completa_foto_a_recortar);
				$current_large_image_height = getHeight($ruta_completa_foto_a_recortar);
			?>
				<script type="text/javascript">
					function preview(img, selection)
					{
						var scaleX = <?php echo $ancho_miniatura; ?> / selection.width;
						var scaleY = <?php echo $alto_miniatura; ?> / selection.height;

						$('#thumbnail + div > img').css({
							width: Math.round(scaleX * <?php echo $current_large_image_width; ?>) + 'px',
							height: Math.round(scaleY * <?php echo $current_large_image_height; ?>) + 'px',
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
						$('#thumbnail').imgAreaSelect({ aspectRatio: '1:<?php echo $alto_miniatura/$ancho_miniatura;?>', onSelectChange: preview });
					});
				</script>
			<?php
			}

			// Si POSEE foto carnet
			if ( $nombre_foto_carnet != '' )
			{
				// Si se acaba de CARGAR RECIÉN
				if ( isset($resultado_carga_foto_carnet) && $resultado_carga_foto_carnet == 1 )
				{
					$ruta_para_ficha_datos = str_replace("../", "", $ruta_directorio_fotos);
				?>
					<script type="text/javascript">
						$(document).ready(function() {

							// Se muestra la foto en la ficha del legajo respectivo
							window.parent.contenedora_foto_legajo.setProperty('src', '<?php echo $ruta_para_ficha_datos.$nombre_foto_carnet; ?>?'+(new Date()).getTime());

							// Se guarda el nombre de la foto en un campo oculto
							window.parent.p_foto.setProperty('value', '<?php echo $nombre_foto_carnet; ?>');

							// Se muestra el botón Borrar debajo de la foto carnet
							window.parent.btBorrarFotoActual.style.display = 'block';

							//	Se cierra la modal para mostrar la foto en la ficha de datos
							window.parent.ocultarIframeParaSubirFoto();
						});
					</script>
				<?php
				}
				else // 13/02/2020 XXXX, se cambia la foto, se elige y se sube para su recorte.
				{
				?>
					<form id="formCargarFotoParaRecorte" name="photo" enctype="multipart/form-data" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">

						<input type="hidden" id="legajo" name="legajo" value="<?php echo (isset($legajo)) ? $legajo : ''; ?>" />

						<input type="hidden" id="upload" name="upload" value="Subir" />

						<div class="p_edicion_modal_botones">
							<div id="inputFoto" class="input_file_personalizado" style="margin-left:222px">
								<input type="file" name="image" id="image" value="" size="1" class="input_file" onchange="javascript:document.getElementById('formCargarFotoParaRecorte').submit();" />
								&nbsp;<img src="../imagenes/barra/zoom_16x16.gif" width="15" height="15" align="top" />&nbsp;Examinar
							</div>
							<div class="p_boton_en_modal_foto">
								<a id="p_btCerrar">
									&nbsp;<img src="../imagenes/barra/delete_16x16.gif" width="14" height="14" align="top" />&nbsp;Cancelar
								</a>
							</div>
						</div>
					</form>
				<?php
				}
			}
			else // Si NO POSEE foto carnet
			{
				$ruta_completa_foto_a_recortar = $ruta_directorio_fotos."normal_".$legajo.".".$extension;

				//	Si YA SE ELIGIÓ la foto A RECORTAR
				if ( file_exists($ruta_completa_foto_a_recortar) ) {
				?>
					<form id="formRecorte" name="thumbnail" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">

						<input type="hidden" name="x1" value="" id="x1" />
						<input type="hidden" name="y1" value="" id="y1" />
						<input type="hidden" name="x2" value="" id="x2" />
						<input type="hidden" name="y2" value="" id="y2" />
						<input type="hidden" name="w" value="" id="w" />
						<input type="hidden" name="h" value="" id="h" />

						<input type="hidden" id="legajo" name="legajo" value="<?php echo (isset($legajo)) ? $legajo : ''; ?>" />
						<input type="hidden" id="extension" name="extension" value="<?php echo (isset($extension)) ? $extension : ''; ?>" />

						<input type="hidden" name="upload_recorte" value="algo" id="upload_recorte" />

						<div class="p_edicion_modal_botones">
							<div style="width: 220px;height:25px;float:left;"></div>
							<div id="btnRecortar" class="p_boton_en_modal_foto">
								<a href="javascript:document.getElementById('formRecorte').submit();">
									<img src="../imagenes/barra/ok_16x16.gif" width="15" height="15" align="top" />&nbsp;Recortar
								</a>
							</div>
							<div class="p_boton_en_modal_foto">
								<a id="p_btCerrar">
									<img src="../imagenes/barra/delete_16x16.gif" width="14" height="14" align="top" />&nbsp;Cancelar
								</a>
							</div>
						</div>
					</form>
					<!-- Aquí se crea la miniatura para la credencial-->
					<div align="center" style="clear:both;padding-top:5px;">

						<!-- Se muestra la imagen elegida para tomar el recorte -->
						<img src="<?php echo $ruta_completa_foto_a_recortar; ?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Crear Miniatura" />

						<!--  Este es el cuadro punteado para recortar la foto -->
						<div style="border:1px #e5e5e5 solid; float:left; position:relative; overflow:hidden; width:<?php echo $ancho_miniatura; ?>px; height:<?php echo $alto_miniatura; ?>px;">
							<img src="<?php echo $ruta_completa_foto_a_recortar; ?>" style="position: relative;" alt="Miniatura Previa" />
						</div>
					</div>
					<script type="text/javascript">
						$(document).ready(function () {
							// Para mostrar el área de selección sobre la imagen a recortar "thumbnail"
							$('#thumbnail').imgAreaSelect({
								x1: 50,
								y1: 50,
								x2: 100,
								y2: 100,
								onSelectEnd: function (img, selection) {
									$('#btnRecortar').css('display', 'block');
								}
							});

							$('#btnRecortar').css('display', 'none');
						});
					</script>
				<?php
				}
				else // Se elige la foto y se sube para su recorte
				{
				?>
					<form id="formCargarFotoParaRecorte" name="photo" enctype="multipart/form-data" action="<?php echo $_SERVER["PHP_SELF"];?>" method="post">

						<input type="hidden" id="legajo" name="legajo" value="<?php echo (isset($legajo)) ? $legajo : ''; ?>" />

						<input type="hidden" id="upload" name="upload" value="Subir" />

						<div class="p_edicion_modal_botones">
							<div id="inputFoto" class="input_file_personalizado" style="margin-left:222px">
								<input type="file" name="image" id="image" value="" size="1" class="input_file" onchange="javascript:document.getElementById('formCargarFotoParaRecorte').submit();" />
								&nbsp;<img src="../imagenes/barra/zoom_16x16.gif" width="15" height="15" align="top" />&nbsp;Examinar
							</div>

							<div class="p_boton_en_modal_foto">
								<a id="p_btCerrar">
									&nbsp;<img src="../imagenes/barra/delete_16x16.gif" width="14" height="14" align="top" />&nbsp;Cancelar
								</a>
							</div>
						</div>
					</form>
				<?php
				}
			}
			?>
		</div>
		<script type="text/javascript">
			$(document).ready(function() {
				// Se oculta el iframe, la capa de fondo oscuro y la capa de la modal
				$('#p_btCerrar').click(function() {
					window.parent.ocultarIframeParaSubirFoto();
				});
			});
		</script>
	</body>
</html>
