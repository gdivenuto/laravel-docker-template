<?php
	// ALTO DE LA IMAGEN
	$alto = $_GET['alto'];
	
	// ANCHO DE LA IMAGEN
	$ancho = $_GET['ancho'];
	
	// NOMBRE DE LA IMAGEN
	$nombre_imagen_original = $_GET['imagen'];
	
	header ("Content-type: image/jpeg");

	$datos = @getimagesize($nombre_imagen_original);
	
	// Crea una nueva imagen a partir de la imagen original
	$imagen_creada = @imagecreatefromjpeg($nombre_imagen_original);
	
	$ancho_original = $datos[0];// ANCHO DE LA IMAGEN RECIBIDA
	$alto_original = $datos[1];// ALTO DE LA IMAGEN RECIBIDA
	
	// SI EL ANCHO ES MENOR AL ALTO
	if ( $ancho_original < $alto_original )
	{
		// SE FIJA EL ALTO DE LA IMAGEN
		$alto_final = $alto;//ALTO_IMAGEN;
		
		// SE CALCULA LA PROPORCION DEL ANCHO DE LA IMAGEN
		$ancho_final = ($alto_final / $alto_original) * $ancho_original;
	}	
	else
	{
		// SE FIJA EL ANCHO DE LA IMAGEN
		$ancho_final = $ancho;//ANCHO_IMAGEN;
		
		// SE CALCULA LA PROPORCION DEL ALTO DE LA IMAGEN
		$alto_final = ($ancho_final / $ancho_original) * $alto_original;
	}
	
	// Crea una imagen en negro del tamaño especificado
	$imagen_tamanio_especifico = imagecreatetruecolor($ancho_final, $alto_final);
	
	// Copia y cambia el tamaño de parte de la imagen creada redimensionándola
	imagecopyresampled($imagen_tamanio_especifico, $imagen_creada, 0, 0, 0, 0, $ancho_final, $alto_final, $datos[0], $datos[1]);
	
	// Crea un archivo JPEG desde la imagen con tamaño especifico
	imagejpeg($imagen_tamanio_especifico);
?>
