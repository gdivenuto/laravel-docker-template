<?php
/**
 * Clase helper para la manipulación de imagenes.
 * 
 * @author kaleb
 */
class ImageHelper {
	
	/**
	 * Constructor de clase. 
	 */
    public function __construct()
    {
    	if (!extension_loaded('gd')) { 
    		throw new Exception('ImageHelper: la librer&iacute;a GD no se encuentra instalada.');
    	}
    }

    /**
     * Resampleo de la imagen manteniendo la relación de aspecto.
     * @param  string  $archivoImagen  Nombre del archivo físico a cambiar de tamaño.
     * @param  integer $maxAncho       Ancho máximo de resampleo (por defecto, 800).
     * @param  integer $maxAlto        Alto máximo de resampleo (por defecto, 60).
     * @param  integer $calidadDestino Calidad del JPEG de destino (por defecto, 90).
     */
    public function resizeAspectRatio($archivoImagen, $maxAncho = 800, $maxAlto = 600, $calidadDestino = 90)
    {
		// Obtengo el alto y ancho del archivo de imagen
		list($imagenAncho, $imagenAlto, $imagenTipo) = getimagesize($archivoImagen);

		// Creo una referencia al archivo origen en base a su tipo (solo gif, jpeg o png)
		switch ($imagenTipo) {
			case IMAGETYPE_GIF:
				$imagenOrigen = imagecreatefromgif($archivoImagen);
				break;
			case IMAGETYPE_JPEG:
				$imagenOrigen = imagecreatefromjpeg($archivoImagen);
				break;
			case IMAGETYPE_PNG:
				$imagenOrigen = imagecreatefrompng($archivoImagen);
				break;
			default:
				$imagenOrigen = false;
				break;
		}

		// Si no es un tipo soportado, lanzo un error
		if ($imagenOrigen === false) 
        	throw new Exception('ImageHelper.resizeAspectRatio: tipo de archivo no soportado.');

		// Si la imagen es mas ancha que alta, mantengo ancho y calculo el alto
		if (($imagenAncho / $imagenAlto) > ($maxAncho / $maxAlto)) {
			$destinoAncho = $maxAncho;
			$destinoAlto = ($maxAncho * $imagenAlto) / $imagenAncho;
		}
		// Si la imagen es mas alta que ancha, mantengo el alto y calculo el ancho
		else if (($imagenAncho / $imagenAlto) < ($maxAncho / $maxAlto)) {
			$destinoAncho = ($maxAlto * $imagenAncho) / $imagenAlto;
			$destinoAlto = $maxAlto;
		}
		// Y por las dudas, si son iguales, mantengo alto y ancho
		else if (($imagenAncho / $imagenAlto) == ($maxAncho / $maxAlto)) {
			$destinoAncho = $maxAncho;
			$destinoAlto = $maxAlto;
		}

		// Creo la imagen destino
		$imagenDestino = imagecreatetruecolor($destinoAncho, $destinoAlto);
		imagecopyresampled($imagenDestino, $imagenOrigen, 0, 0, 0, 0, $destinoAncho, $destinoAlto, $imagenAncho, $imagenAlto);
		$this->aplicarWatermark($imagenDestino); // Aplico marca de agua
		imagejpeg($imagenDestino, $archivoImagen, $calidadDestino);
		
		// Libero memoria
		imagedestroy($imagenOrigen);
		imagedestroy($imagenDestino);
    }

    /**
     * Genera sobre la imagen un rectangulo con bordes redondeados.
     * @param resource &$resourceDestino Referencia al resource (imagen) sobre la cual se dibujará el rectángulo.
     * @param int $x1               Coordenada X inicial.
     * @param int $y1               Coordenada Y inicial.
     * @param int $x2               Coordenada X final.
     * @param int $y2               Coordenada Y final.
     * @param int $radius           Radio del borde redondeado.
     * @param int $color            Color del rectángulo.
     */
    private function ImageRectangleWithRoundedCorners(&$resourceDestino, $x1, $y1, $x2, $y2, $radius, $color) {
		// draw rectangle without corners
		imagefilledrectangle($resourceDestino, $x1+$radius, $y1, $x2-$radius, $y2, $color);
		imagefilledrectangle($resourceDestino, $x1, $y1+$radius, $x2, $y2-$radius, $color);
		// draw circled corners
		imagefilledellipse($resourceDestino, $x1+$radius, $y1+$radius, $radius*2, $radius*2, $color);
		imagefilledellipse($resourceDestino, $x2-$radius, $y1+$radius, $radius*2, $radius*2, $color);
		imagefilledellipse($resourceDestino, $x1+$radius, $y2-$radius, $radius*2, $radius*2, $color);
		imagefilledellipse($resourceDestino, $x2-$radius, $y2-$radius, $radius*2, $radius*2, $color);
	}
    
    /**
     * Aplica una marca de agua a la imágen destino.
     * @param  resource &$resourceDestino Referencia al resource (imagen) sobre la cual se dibujará la marca de agua.
     * @param  string $cadena           Cadena que se dibujará en la marca de agua.
     */
    public function aplicarWatermark(&$resourceDestino, $cadena = 'KrakenSample')
    {
		// Primero creo el watermark utilizando GD
		$watermark = imagecreatetruecolor(100, 16);
		imagefilledrectangle($watermark, 0, 0, 99, 15, 0x999999);
		//$this->ImageRectangleWithRoundedCorners($watermark, 0, 0, 119+4, 19+4, 4, 0x999999);
		imagestring($watermark, 2, 4, 1, '('.date("Y").') '.$cadena, 0x555555);
		imagestring($watermark, 2, 5, 2, '('.date("Y").') '.$cadena, 0x111111);

		// Establecer los márgenes para la watermark y obtener el alto/ancho de la imagen de la watermark
		$margen_dcho = 0;
		$margen_inf = 0;
		$sx = imagesx($watermark);
		$sy = imagesy($watermark);

		// Fusionar la watermark con nuestra foto con una opacidad del 50%
		imagecopymerge($resourceDestino, $watermark, 
			imagesx($resourceDestino) - $sx - $margen_dcho, imagesy($resourceDestino) - $sy - $margen_inf, 
			0, 0, imagesx($watermark), imagesy($watermark), 
			50);

		imagedestroy($watermark);
    }

}

?>