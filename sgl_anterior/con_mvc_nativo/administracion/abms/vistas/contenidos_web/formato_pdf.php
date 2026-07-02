<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaFormatoPdf extends VistaBase {

	public function __construct() {

		parent::__construct();

		$this->controlador = 'contenidos_web';
	}

	/**
	 * Se genera un PDF
	 * @param  array $datos     Información a mostrar en el PDF
	 * @return pdf				Documento en formato PDF
	 */
	public function mostrar($datos = null)
	{
		// Se empieza a guardar el HTML en un bufer interno
		ob_start();
		?>
		<page backtop="20mm" backbottom="10mm" backleft="15mm" backright="15mm">
			<?= html_entity_decode($datos['contenido']); ?>
		</page>
		<?php
		// Se asigna todo el HTML guardado en el bufer interno, para su conversión a PDF
		$contenido_html = ob_get_clean();

		try {
			// conversion HTML => PDF (los métodos utilizados aquí están definidos en el archivo html2pdf.class.php)
			$html2pdf = new HTML2PDF('P', 'Legal', 'es');

			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');

			// Se escribe el contenido HTML en el documento a generar
			$html2pdf->WriteHTML($contenido_html);

			// Destino del documento
			$html2pdf->Output("contenido_nro_".$datos['id'].".pdf");

		} catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}
}