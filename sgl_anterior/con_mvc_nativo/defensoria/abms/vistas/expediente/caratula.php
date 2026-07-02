<?php
if (!isset($_SESSION))
	session_start();

class VistaCaratulaPDF extends VistaBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Se genera la Nota de Pedido en formato pdf, para su impresion y/o descarga
	 * @param array $datos
	 */
	public function mostrar($datos) {

		ob_start();
		?>
		<style type="text/css">
			#pdf_contenedor {
				width: 60%;
				border: 1px solid silver;
				color: #545454;
				font-size: 14px;
			}
			
			p {
				margin: 5px;
			}
			
		</style>
		<page backtop="5mm" backbottom="5mm" backleft="5mm" backright="5mm">
			<div id="pdf_contenedor">
				
				<p style="padding-left: 300px;">
					<b><?= "Expte. N&deg;&nbsp;" . $datos['numero']; ?></b>
				</p>
				<hr>
				<p>
					<b><?= $datos['presentador_apellido'] . ', ' . $datos['presentador_nombre']; ?></b>	
				</p>
				<hr>
				<p>
					<i>CONTRA</i>
				</p>
				<p>
					<b><?=$datos['tipo_proceso_nombre'];?></b>
				</p>
				<hr>
				<p>
					<i>SOBRE</i>
				</p>
				<p>
					<b><?=$datos['texto'];?></b>
					<br><br><br><br><br><br>
				</p>
			</div>
		</page>
	<?php
		$contenido = ob_get_clean();
		try {
			// Se convierte el HTML a PDF
			$html2pdf = new HTML2PDF('P', 'A4', 'es');
			$html2pdf->WriteHTML($contenido);
			$html2pdf->Output("caratula_" . $datos['numero'] . ".pdf");

		} catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}
}