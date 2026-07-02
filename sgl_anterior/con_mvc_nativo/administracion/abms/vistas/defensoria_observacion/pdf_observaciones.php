<?php
if (!isset($_SESSION))
	session_start();

class VistaObservacionDpPDF extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'defensoria_observacion';
	}

	/**
	 * Se genera la lista en formato pdf
	 * @param  array $datos
	 */
	public function mostrar($datos) {
		ob_start();
		$cantidad = (isset($datos)) ? count($datos) : 0;
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
								<tr><td>Observaciones a Candidatos a Defensor del Pueblo</td></tr>
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
			<table class="pdf_tabla_cuerpo_reporte pdf_pt_10">
				<thead>
					<tr>
						<th class="pdf_text_center pdf_px_10">Fecha</th>
						<th class="pdf_text_center pdf_px_10">Candidato</th>
						<th class="pdf_text_center pdf_px_10">Observado por</th>
						<th class="pdf_text_center pdf_px_10">DNI</th>
						<th class="pdf_text_center pdf_px_10">Tel&eacute;fono</th>
						<th class="pdf_text_center pdf_px_10">Mail</th>
						<th class="pdf_text_center pdf_px_10">Habilitada</th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos[$i]; ?>
						<tr class="pdf_pt_10">
							<td class="pdf_px_10 pdf_text_center">
								<?=$this->formatearFecha($dato['fecha']);?>
							</td>
							<td class="pdf_px_10">
								<?=$dato['candidato_apellido'].', '.$dato['candidato_nombre'];?>
							</td>
							<td class="pdf_px_10">
					        	<?= ($dato['tipo_observacion'] == 'personal'
					        		? $dato['apellido'].', '.$dato['nombre']
					        		: $dato['entidad_nombre']);
					        	?>
							</td>
							<td class="pdf_px_10 pdf_text_right">
								<?=($dato['dni']) ? $dato['dni'] : '&nbsp;';?>
							</td>
							<td class="pdf_px_10 pdf_text_right">
								<?= ($dato['tipo_observacion'] == 'personal'
					        		? $dato['telefono']
					        		: $dato['entidad_telefono']);
					        	?>
							</td>
							<td class="pdf_px_10">
								<?= ($dato['tipo_observacion'] == 'personal'
					        		? $dato['email']
					        		: $dato['entidad_email']);
					        	?>
							</td>
							<td class="pdf_px_10 pdf_text_center">
								<?=($dato['habilitado']) ? 'Si' : 'No';?>
							</td>
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
			$html2pdf->Output("observaciones_defensor_pueblo.pdf");

		} catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}
}