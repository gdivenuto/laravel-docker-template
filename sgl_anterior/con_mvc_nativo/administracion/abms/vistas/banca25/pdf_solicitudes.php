<?php
if (!isset($_SESSION))
	session_start();

class VistaBanca25SolicitudesPDF extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'banca25';
	}

	/**
	 * Se genera la lista en formato pdf
	 * @param  array $datos
	 * @param  array $filtro
	 */
	public function mostrar($datos, $filtro) {
		ob_start();
		$cantidad = (isset($datos)) ? count($datos) : 0;
		?>
		<style>
			<?php require_once RUTA_CSS . "reportes_pdf.css";?>
		</style>
		<page backtop="25mm" backbottom="5mm" backleft="5mm" backright="5mm">
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
								<tr>
									<td>
										<?=$this->getNombreTipo($filtro['f_tipo']);?> de Banca 25
									</td>
								</tr>
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
						<th class="pdf_text_center pdf_px_10">Nro</th>
						<th class="pdf_text_center pdf_px_10">Fecha</th>
						<th class="pdf_text_center pdf_px_10">Solicitante</th>
						<th class="pdf_text_center pdf_px_10">DNI</th>
						<th class="pdf_text_center pdf_px_10">Tel&eacute;fono</th>
						<th class="pdf_text_center pdf_px_10">Mail</th>
						<th class="pdf_px_10">Instituci&oacute;n</th>
						<th class="pdf_px_10">Tema</th>
						<th class="pdf_text_center pdf_px_10">Fecha Sesi&oacute;n</th>
						<th class="pdf_text_center pdf_px_10">Expte/Nota</th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos[$i]; ?>
						<tr class="pdf_pt_10">
							<td class="pdf_px_10 pdf_text_right">
								<?=$dato['id'];?>
							</td>
							<td class="pdf_px_10 pdf_text_center">
								<?=$this->formatearFecha($dato['fecha']);?>
							</td>
							<td class="pdf_px_10" width="100">
								<?=LibreriaGeneral::aMayuscula($dato['apellidoynombre']);?>
							</td>
							<td class="pdf_px_10 pdf_text_right">
								<?=$dato['nrodoc'];?>
							</td>
							<td class="pdf_px_10 pdf_text_right" width="90">
								<?=$dato['telefono'];?>
							</td>
							<td class="pdf_px_10" width="100">
								<?=$dato['mail'];?>
							</td>
							<td class="pdf_px_10" width="100">
								<?=($dato['institucion_nombre']) ? $dato['institucion_nombre'] : '---';?>
							</td>
							<td class="pdf_px_10" width="120">
								<?=($dato['tema']) ? $dato['tema'] : '---';?>
							</td>
							<td class="pdf_px_10 pdf_text_center">
								<?=(isset($dato['fecha_sesion']) 
									? $this->formatearFecha($dato['fecha_sesion'])
									: '&nbsp;');?>
							</td>
							<td class="pdf_px_10 pdf_text_center">
					        	<?=(isset($dato['expe_anio']) 
					        		? $dato['expe_anio'].'-'.$dato['expe_tipo'].'-'. $dato['expe_numero']
					        		: '&nbsp;');?>
					        </td>
						</tr>
					<?php }?>
				</tbody>
			</table>
		</page>
        <?php $contenido = ob_get_clean();
		try {
			// conversion HTML => PDF
			$html2pdf = new HTML2PDF('L', 'A4', 'es');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->WriteHTML($contenido);
			$html2pdf->Output("banca25.pdf");

		} catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

	private function getNombreTipo($tipo) {
		switch ($tipo) {
			case 1:
				$nombre_tipo = 'Solicitudes pendientes';
				break;
			case 2:
				$nombre_tipo = 'Expositores';
				break;
			default:
				$nombre_tipo = 'Solicitudes/Expositores';
				break;
		}
		return $nombre_tipo;
	}
}