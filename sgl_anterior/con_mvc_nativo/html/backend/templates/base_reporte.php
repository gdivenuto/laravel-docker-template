<?php
/**
 * Este script esta diseñado para ser incluído como plantilla desde BaseViewActionReport o alguno de sus 
 * descendientes.
 * A continuación se detallan los métodos de generación de la plantilla (implementados en 
 * BaseViewActionReport o alguno de sus descendientes.
 *
 * 	$this->generarHtmlHeader();		--> No utilizado
 * 	$this->generarHtmlHeaderCSS();	
 * 	$this->generarHtmlHeaderJS();	--> No utilizado
 * 	$this->generarMenu();			--> No utilizado
 * 	$this->generarCabecera();
 * 	$this->generarCuerpo();
 * 	$this->generarPie();
 */

// Se incluye la clase para convertir el HTML en PDF
require_once(PATH_KRAKEN_LIBRERIAS_HTML2PDF.'html2pdf.class.php');

// Se activa el buffering de la salida, significa que se guarda la salida en un bufer interno, 
// en vez de enviarla al cliente (navegador)
ob_start();
?>
<style>
<?php 
	// Incluyo las hojas de estilo
	$this->generarHtmlHeaderCSS();
?>
</style>
<page backtop="<?php echo $this->actionParamMarginBodyTop; ?>" backbottom="<?php echo $this->actionParamMarginBodyBottom; ?>" backleft="<?php echo $this->actionParamMarginBodyLeft; ?>" backright="<?php echo $this->actionParamMarginBodyRight; ?>">
<?php
	// Incluyo la cabecera
	$this->generarCabecera();

	// Incluyo el pie de pagina 
	// NOTA: se define el pie después de la cabecera,
	// porque la etiqueta <page_footer> debe ir a continuación de </page_header>
	$this->generarPie();

	// Incluyo el criterio de búsqueda utilizado
	$this->generarCriterioBusqueda();

	// Incluyo el cuerpo de la vista
	$this->generarCuerpo();
?>
</page>
<?php
	// Se obtiene la salida del buffer, sin mostrar en el navegador, para utilizarlo para la creación del documento PDF.
	$outputBuffer = ob_get_clean();
	try
	{
		// Conversión HTML => PDF (los métodos utilizados aquí están definidos en el archivo html2pdf.class.php)
		// Se instancia un 'html2pdf', con:
		// orientación horizontal (L = landscape), 
		// tamaño hoja A4, 
		// idioma español,
		// márgenes Left, Top, Right y Bottom 
		$html2pdf = new HTML2PDF(
			$this->actionParamOrientation, 
			$this->actionParamFont,
			'es', 
			true,    // unicode? 
			'UTF-8', // tipo de encoding
			array(
				$this->actionParamMarginLeft, 
				$this->actionParamMarginTop, 
				$this->actionParamMarginRight, 
				$this->actionParamMarginBottom) 
		);
		
		$html2pdf->pdf->SetDisplayMode($this->actionParamDisplayMode);	// Se define el modo de visualización a pantalla completa
		$html2pdf->setDefaultFont($this->actionParamDefaultFont);		// Se define la fuente Arial por defecto
		$html2pdf->writeHTML($outputBuffer);							// Se procesa el outputBuffer del buffer almacenado (código HTML)
		
		// Se genera el documento PDF en base a la configuracion de salida
		//  - actionParamName es el nombre o ruta completa de la salida
		//  - actionParamOutputType es el tipo de salida para la libreria PDF
		$html2pdf->Output($this->actionParamName, $this->actionParamOutputType);
	}
	catch(HTML2PDF_exception $e) {
		echo $e;
		exit;
	}
?>