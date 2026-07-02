<?php
// Alto de la imagen
$alto = ( isset($_GET['alto']) && $_GET['alto'] != '' ) ? $_GET['alto'] : '';

// Ancho de la imagen
$ancho = ( isset($_GET['ancho']) && $_GET['ancho'] != '' ) ? $_GET['ancho'] : '';

// Nombre de la imagen
$nombre_imagen_original = ( isset($_GET['imagen']) && $_GET['imagen'] != '' ) ? $_GET['imagen'] : '';

// Si se recibió el nombre de la imagen
if ($nombre_imagen_original != '') {
	// SE OBTIENE INFORMACION DE LA FOTO
	$datos = @getimagesize($nombre_imagen_original);
	
	if ( $datos[2] == 1 ) {
		header ("Content-type: image/gif");
		// CREA UNA NUEVA IMAGEN GIF A PARTIR DE LA IMAGEN ORIGINAL
		$imagen_creada = @imagecreatefromgif($nombre_imagen_original);
	}
	if ( $datos[2] == 2 ) {
		header ("Content-type: image/jpeg");
		// CREA UNA NUEVA IMAGEN JPEG A PARTIR DE LA IMAGEN ORIGINAL
		$imagen_creada = @imagecreatefromjpeg($nombre_imagen_original);
	}
	if ( $datos[2] == 3 ) {
		header ("Content-type: image/png");
		// CREA UNA NUEVA IMAGEN PNG A PARTIR DE LA IMAGEN ORIGINAL
		$imagen_creada = @imagecreatefrompng($nombre_imagen_original);
	}
		
	$ancho_original = $datos[0];// ANCHO DE LA IMAGEN RECIBIDA
	$alto_original = $datos[1];// ALTO DE LA IMAGEN RECIBIDA
	
	// SI EL ANCHO ES MENOR AL ALTO
	if ( $ancho_original < $alto_original ) {
		// SE FIJA EL ALTO DE LA IMAGEN
		$alto_final = $alto;//ALTO_IMAGEN;

		// SE CALCULA LA PROPORCION DEL ANCHO DE LA IMAGEN
		$ancho_final = ($alto_final / $alto_original) * $ancho_original;
	} else {
		// SE FIJA EL ANCHO DE LA IMAGEN
		$ancho_final = $ancho;//ANCHO_IMAGEN;
		
		// SE CALCULA LA PROPORCION DEL ALTO DE LA IMAGEN
		$alto_final = ($ancho_final / $ancho_original) * $alto_original;
	}
	
	// Crea una imagen en negro del tamaño especificado
	$imagen_tamanio_especifico = imagecreatetruecolor($ancho_final, $alto_final);
	
	// Copia y cambia el tamaño de parte de la imagen creada redimensionándola
	imagecopyresampled($imagen_tamanio_especifico, $imagen_creada, 0, 0, 0, 0, $ancho_final, $alto_final, $datos[0], $datos[1]);
	
	if ( $datos[2]==1 )
		// Crea un archivo GIF desde la imagen con tamaño especifico
		@imagegif($imagen_tamanio_especifico, $nombre_foto);

	if ( $datos[2]==2 )
		// Crea un archivo JPEG desde la imagen con tamaño especifico
		@imagejpeg($imagen_tamanio_especifico, $nombre_foto);

	if ( $datos[2]==3 )
		// Crea un archivo PNG desde la imagen con tamaño especifico
		@imagepng($imagen_tamanio_especifico, $nombre_foto);
}
?>