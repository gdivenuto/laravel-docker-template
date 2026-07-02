<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaParticipacionesPDF extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'participaciones';
	}

	/**
	 * Se genera la lista en formato pdf
	 * @param  array $datos
	 */
	public function mostrar($datos) {

		ob_start();

		$cantidad = (isset($datos)) ? count($datos) : 0;

		if ($datos[0]['tipo'] == 'E') {
			$documento = 'el Expediente';
		} elseif ($datos[0]['tipo'] == 'N') {
			$documento = 'la Nota';
		} elseif ($datos[0]['tipo'] == 'R') {
			$documento = 'la Recomendaci&oacute;n';
		}
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
								<tr><td>Programa de Participaci&oacute;n Ciudadana</td></tr>
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
			<table class="pdf_tabla_cuerpo_reporte">
				<tbody>
					<tr>
						<td colspan="2" class="pdf_ficha_expediente pdf_texto_gris">
							Participaciones en <?=$documento;?>&nbsp;
							<strong><?=sprintf('%d&nbsp;%s&nbsp;%d&nbsp;%d&nbsp;%d', $datos[0]['anio'], $datos[0]['tipo'], $datos[0]['numero'], $datos[0]['cuerpo'], $datos[0]['alcance']);?></strong>
						</td>
					</tr>
					<?php for ($i = 0; $i < $cantidad; $i++) {$dato = &$datos[$i];?>
						<tr>
							<td class="pdf_pt_10">
								<table>
									<tr>
										<td><strong>Nro</strong>:</td>
										<td><?=$dato['numero_participacion'];?></td>
									</tr>
									<tr>
										<td><strong>Fecha</strong>:</td>
										<td><?=$this->formatearFecha($dato['fecha']);?></td>
									</tr>
									<tr>
										<td><strong>Nombre</strong>:</td>
										<td><?=$dato['apellidoynombre'];?></td>
									</tr>
									<tr>
										<td><strong>Documento</strong>:</td>
										<td><?=$dato['tipodoc'] . ':&nbsp;' . $dato['nrodoc'];?></td>
									</tr>
									<tr>
										<td><strong>Domicilio</strong>:</td>
										<td><?=$dato['domicilio'];?></td>
									</tr>
									<tr>
										<td><strong>Tel&eacute;fono</strong>:</td>
										<td><?=$dato['telefono'];?></td>
									</tr>
									<tr>
										<td><strong>Mail</strong>:</td>
										<td><?=$dato['mail'];?></td>
									</tr>
									<?php if (isset($dato['institucion_nombre']) && $dato['institucion_nombre'] != null) {?>
										<tr>
											<td><strong>Instituci&oacute;n:</strong></td>
											<td><?=$dato['institucion_nombre'] . ' - ' . $dato['institucion_domicilio'];?></td>
										</tr>
									<?php }?>
								</table>
							</td>
							<td class="pdf_pt_10">
								<?php if ($dato['documentacion'] != null) {?>
							        <img src="data:image/jpg;base64,<?=$dato['documentacion'];?>" alt="Documentaci&oacute;n" height="120" />
								<?php }?>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="pdf_pt_10 pdf_ficha_expediente">
								<strong>Propuesta:</strong> <?=html_entity_decode($dato['texto']);?>
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

			// Tratamiento del codigo HTML
			$html2pdf->WriteHTML($contenido);

			// Destino donde enviar el documento
			$html2pdf->Output("informe_participaciones.pdf");

		} catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}
}