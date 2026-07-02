<?php
if (!isset($_SESSION))
	session_start();

class VistaDenunciaPDF extends VistaBase {

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
				color: #545454;
				font-size: 14px;
			}
			img {
				border: 0;
				vertical-align: middle;
			}
			p {
				margin: 5px;
			}
		</style>
		<page backtop="5mm" backbottom="5mm" backleft="5mm" backright="5mm">
			<div id="pdf_contenedor">
				<p style="text-align: center;">
					<img src="<?=URL_IMAGENES;?>logo.png" width="250" />
					<br>
					<img src="<?=URL_IMAGENES;?>direccion.png" width="250" />
				</p>
				<p style="padding-left: 500px;">
					<b><?= "N&deg; de actuaci&oacute;n: " . $datos['numero']; ?></b>
				</p>
				<p>
					Fecha: <?= $this->formatearFecha($datos['fecha']); ?>
				</p>
				<p>
					<b>APELLIDO Y NOMBRES:</b> <?= $datos['presentador_apellido'] . ', ' . $datos['presentador_nombre']; ?>
				</p>
				<p>
					<b>Tipo y N&deg; de Doc: : </b>DNI <?= $datos['presentador_dni']; ?>
					&nbsp;&nbsp;
					<b>Tel&eacute;fono: </b>
					<?= $datos['presentador_movil_cod_area'].$datos['presentador_movil_numero']; ?>
					<?php
					if ( isset($datos['presentador_tel_fijo_cod_area'], $datos['presentador_tel_fijo_numero']) )
						echo ' / ' . $datos['presentador_tel_fijo_cod_area'].$datos['presentador_tel_fijo_numero'];
					?>
				</p>
				<p>
					<b>Domicilio: </b>
					<?= $datos['presentador_direccion_calle'] . ' ' . $datos['presentador_direccion_numero']; ?>
					<?php
					if ( isset($datos['presentador_direccion_piso'], $datos['presentador_direccion_departamento']) )
						echo ' ' . $datos['presentador_direccion_piso'] . ' ' . $datos['presentador_direccion_departamento'];
					?>
				</p>
				<p>
					<b>Localidad:</b> <?= $datos['presentador_localidad']; ?>
					&nbsp;&nbsp;
					<b>C&oacute;digo Postal:</b> <?=$datos['presentador_codigo_postal']; ?>
				</p>
				<br><br>
				<p>
					Car&aacute;cter: PROPIO / En nombre de: 
				</p>
				<p>
					MANIFIESTA QUE:<br>
					<?=$datos['texto'];?>
					<br><br><br><br>
				</p>
				<p>
					<b>La presentaci&oacute;n de la queja ante el Defensor del Pueblo no interrumpe los plazos que la ley fija para iniciar acciones judiciales o administrativas, ni para interponer recursos en esas mismas sedes (judicial y administrativa).-</b>
					<br><br><br><br>
				</p>
				<p>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					------------------------------
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					------------------------------
					<br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					Firma en Conformidad
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					Aclaraci&oacute;n
				</p>
				<p>
					<br><br>
					Atendi&oacute; por el Defensor del Pueblo:
					<br><br><br>
				</p>
				<p>
					El suscrito constituye la direcci&oacute;n de correo electr&oacute;nico expresada a continuaci&oacute;n, declar&aacute;ndolo medio v&aacute;lido para las notificaciones que deban practicarse.
				</p>
			</div>
		</page>
	<?php
		$contenido = ob_get_clean();
		try {
			// Se convierte el HTML a PDF
			$html2pdf = new HTML2PDF('P', 'A4', 'es');
			$html2pdf->WriteHTML($contenido);
			$html2pdf->Output("denuncia_" . $datos['numero'] . ".pdf");

		} catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}
}