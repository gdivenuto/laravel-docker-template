<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaNotificacionesPDF extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'notificaciones';
		
		// Se crea una instancia del modelo
		$this->modelo = new notificacionesModel();
	}

	/**
	 * Se muestra el nombre (desc) del Grupo Destino respectivo
	 * @param  [integer] $id_grupo_destino Identificador del Grupo de Distribución
	 * @return [string]                    Nombre del Grupo de Distribución
	 */
	private function mostrarGrupoDestino($id_grupo_destino) {
		// Se obtiene la lista de grupos de distribución
		$lista_grupos = $this->modelo->obtenerListaGruposDistribucion();

		foreach ($lista_grupos as $ng) {
			// Si se encuentra
			if ($ng['id'] == $id_grupo_destino) {
				// devuelve su descripción
				return $ng['descripcion'];
			}
		}
		// Sino, devuelve un espacio vacío
		return '&nbsp;';
	}

	/**
	 * Se genera la lista de precios completa para almacenarla en el directorio respectivo
	 * y ser utilizado por el cliente registrado en el sitio web para su descarga
	 * @param  array $datos Listado de artículos con código, descripción y precio de venta.
	 */
	public function mostrar($datos) {
		// Se empieza a guardar el HTML en un bufer interno
		ob_start();
		?>
		<style>
			.pdf_notificacion_titulo {
				font-size: 20px;
			    font-weight: bold;
			    text-align: left;
			    color: #444;
			    margin: 5px 20px;
			    border-top: 2px solid #8730b3;
			    border-bottom: 1px solid #1ea5c8;
			}
			.pdf_notificacion_fecha {
			    font-size: 14px;
			    color: #666666;
			    padding: 5px 20px;
			    text-align: left;
			}
			.pdf_notificacion_texto {
				padding: 20px;
				font-size: 13px;
				color: #666666;
				text-align: justify;
			}
			.pdf_notificacion_destinatarios_titulo {
				font-size: 14px;
				font-weight: bold;
				text-align: left;
				padding: 5px 5px;
				color: #444;
				border-top: 1px solid #8730b3;
			}
			.pdf_notificacion_destinatarios {
				padding: 10px;
			}
			.pdf_ancho_100_porciento {
				width: 100%;
			}
		</style>
		<!-- Etiqueta propia de la librería, utilizada para el SUMARIO de la Orden del día de Sesión -->
		<page backtop="20mm" backbottom="10mm" backleft="25mm" backright="20mm" style="font-size:12px;color:#484848">
			<page_footer style="font-size:12px;">
				<table style="width:100%;">
					<tr>
						<td style="text-align:center;width: 100%">[[page_cu]]</td>
					</tr>
				</table>
			</page_footer>
			<table class="pdf_ancho_100_porciento">
				<tr>
					<td><img src="<?=URL_IMAGENES;?>plantilla_mail/escudo_y_titulos.gif" align="left" border="0" width="200" /></td>
				</tr>
				<tr>
					<td class="pdf_notificacion_titulo">
						<?=$datos['n_asunto'];?>
					</td>
				</tr>
				<tr>
					<td class="pdf_notificacion_fecha">
						<?=$this->mostrarFechaConNombreDiaCompleto($datos['n_fecha']);?>
					</td>
				</tr>
				<tr>
					<td class="pdf_notificacion_texto">
						<p><?=($datos['n_mensaje'] != '') ? nl2br($datos['n_mensaje']) : '';?></p>
					</td>
				</tr>
				<tr>
					<td class="pdf_notificacion_destinatarios">
						<?php
// Nombre del directorio de adjuntos de la notificación respectiva
		$dir_adjuntos_notificacion = RUTA_ADJUNTOS_NOTIFICACIONES;

		if (!empty($dir_adjuntos_notificacion)) {
			// Si pudo abrirse el directorio de los Adjuntos de la Notificación respectiva
			if ($handle = opendir($dir_adjuntos_notificacion)) {

				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != ".." && $file != "index.html") {
						if (LibreriaGeneral::esAdjuntoDe($datos['n_id'], $file)) {
							$listado_adjuntos[] = $file;
						}
					}
				}

				closedir($handle);

				// Si posee archivos la notificación
				if ($listado_adjuntos) {

					sort($listado_adjuntos);

					echo '<strong>Documentos adjuntos:</strong><br><br>';

					// Se recorre el directorio respectivo y por cada archivo que contiene
					// se muestra su enlace para visualizarlo.
					foreach ($listado_adjuntos as $adjunto) {
						echo '&#8226;&nbsp;' . str_replace($datos['n_id'] . '__', '', $adjunto) . '<br><br>';
					}
				}
			}
		}
		?>
					</td>
				</tr>
				<tr>
					<td class="pdf_notificacion_destinatarios_titulo">Enviada a:</td>
				</tr>
				<tr>
					<td class="pdf_notificacion_destinatarios">
						<?php
// Si se envió a un Grupo, se muestra su nombre
		if ($datos['n_id_grupo_destino'] != null) {
			echo '<strong>Grupo:</strong>&nbsp;' . $this->mostrarGrupoDestino($datos['n_id_grupo_destino']) . '<br>';
		}

		// Si se envió por lo menos a una Lista, se muestra/n su nombre/s
		if (isset($datos['nombre_lista'])) {
			echo '<br><strong>Listas:</strong>';
			foreach ($datos['nombre_lista'] as $lista) {
				echo '<br>&#8226;&nbsp;' . $lista;
			}
		}?>
					</td>
				</tr>
			</table>
		</page>

		<?php
// Se asigna todo el HTML guardado en el bufer interno, para su conversión a PDF
		$contenido_html = ob_get_clean();

		try {
			// conversion HTML => PDF (los métodos utilizados aquí están definidos en el archivo html2pdf.class.php)
			$html2pdf = new HTML2PDF('P', 'Legal', 'es');

			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');

			// Tratamiento del código HTML
			$html2pdf->WriteHTML($contenido_html);

			// Destino donde enviar el documento
			$html2pdf->Output("notificacion_" . $datos['n_fecha'] . ".pdf");

		} catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}
}
?>