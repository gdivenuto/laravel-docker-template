<?php
if (!isset($_SESSION))
	session_start();

class VistaInscripcionFichaPDF extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'defensoria_observacion';
	}

	/**
	 * Se genera la lista en formato pdf
	 * @param  array $dato
	 */
	public function mostrar($dato) {

		ob_start();
		?>
		<style>
			<?php require_once RUTA_CSS . "reportes_pdf.css";?>
		</style>
		<page backtop="25mm" backbottom="5mm" backleft="0mm" backright="0mm">
			<page_header>
				<table id="pdf_encabezado" class="pdf_texto_gris">
					<tr>
						<td id="pdf_encabezado_logo" rowspan="3">
							<img src="<?=URL_IMAGENES;?>logo_hcd.png">
						</td>
						<td id="pdf_encabezado_mgp_hcd">
							<table>
								<tr><td>Municipalidad de General Pueyrredon</td></tr>
								<tr><td>Honorable Concejo Deliberante</td></tr>
							</table>
						</td>
						<td id="pdf_encabezado_nombre_sistema">
							<table>
								<tr><td>Sistema Gesti&oacute;n Legislativa</td></tr>
								<tr><td>Inscripto para Defensor del Pueblo</td></tr>
							</table>
						</td>
					</tr>
				</table>
				<div class="pdf_separador"></div>
			</page_header>
			<page_footer>
				<table id="pdf_pie" class="pdf_texto_gris">
					<tr>
						<td>Fecha: <?=date("d/m/Y");?></td>
						<td>P&aacute;gina [[page_cu]] de [[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<table class="pdf_tabla_cuerpo_reporte pdf_mt_10 pdf_ml_10">
				<tbody>
					<tr>
						<td class="pdf_ficha_label"><strong>Fecha</strong>:</td>
						<td class="pdf_ficha_valor"><?=$this->formatearFecha($dato['fecha']);?></td>
					</tr>
					<tr>
						<td class="pdf_ficha_label"><strong>Nombre</strong>:</td>
						<td class="pdf_ficha_valor"><?=$dato['apellido'].', '.$dato['nombre'];?></td>
					</tr>
					<tr>
						<td class="pdf_ficha_label"><strong>DNI</strong>:</td>
						<td class="pdf_ficha_valor"><?=$dato['dni'];?></td>
					</tr>
					<tr>
						<td class="pdf_ficha_label"><strong>Domicilio</strong>:</td>
						<td class="pdf_ficha_valor"><?=$dato['domicilio'];?></td>
					</tr>
					<tr>
						<td class="pdf_ficha_label"><strong>Tel&eacute;fono</strong>:</td>
						<td class="pdf_ficha_valor"><?=$dato['telefono'];?></td>
					</tr>
					<tr>
						<td class="pdf_ficha_label"><strong>Mail</strong>:</td>
						<td class="pdf_ficha_valor"><?=$dato['email'];?></td>
					</tr>
					<?php if (isset($dato['entidad_nombre']) && $dato['entidad_nombre'] != null) {?>
						<tr>
							<td class="pdf_ficha_label"><strong>Entidad:</strong></td>
							<td class="pdf_ficha_valor"><?=$dato['entidad_nombre'];?></td>
						</tr>
					<?php }?>
				</tbody>
			</table>
		</page>
        <?php $contenido = ob_get_clean();
		try {
			// conversion HTML => PDF
			$html2pdf = new HTML2PDF('P', 'A4', 'es');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->WriteHTML($contenido);
			$html2pdf->Output("ficha_observacion_candidato.pdf");

		} catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}
}