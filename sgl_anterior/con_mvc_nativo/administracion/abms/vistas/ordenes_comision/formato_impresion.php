<?php
if (!isset($_SESSION))
	session_start();

class VistaFormatoImpresion extends VistaBase {

	public function __construct() {

		parent::__construct();

		$this->controlador = 'ordenes_comision';

		$this->modelo = new ordenes_comisionModel();
	}

	/**
	 * Se genera el PDF de la Orden del día de Sesión, para su impresión y/o descarga
	 * @param  array $datos     			Conjunto de información de la Orden del Día
	 * @return pdf	Documento en formato PDF
	 */
	public function mostrarPDF($datos) {
		// Se empieza a guardar el HTML en un bufer interno
		ob_start();
		
		// Se embebe el CSS
		echo $this->CssOrdenComision();
		?>
		<!-- Etiqueta propia de la librería html2pdf -->
		<page backtop="20mm" backbottom="10mm" backleft="25mm" backright="10mm" style="font-size:12px;color:#484848">
			<!-- <page_footer style="font-size:12px;">
				<table style="width:100%;">
					<tr>
						<td style="text-align:center;width: 100%">[[page_cu]]</td>
					</tr>
				</table>
			</page_footer> -->
			<?php
			// Se muestra el Encabezado de la Orden del Día de Comisión
			echo (isset($datos['encabezado'])) ? html_entity_decode($datos['encabezado']) : '&nbsp;';

			// Se muestran los expedientes marcados "Para tratar"
			echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 1);

			// Se muestran los expedientes marcados "Para su conocimiento"
			echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 2);

			// Se muestran los expedientes marcados "Para Archivo"
			echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 3);

			// Se muestran los expedientes marcados "Para convalidar"
			echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 5);

			// Se muestra el Pie de la Orden del Día de Comisión
			echo (isset($datos['pie'])) ? html_entity_decode($datos['pie']) : '&nbsp;';
			?>
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

			$nombre_archivo = $this->obtenerNombreArchivoOrdenComision($datos['principal']);

			// Destino del documento
			$html2pdf->Output($nombre_archivo.".pdf");

		} catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

	/**
	 * Se genera el HTML de la Orden del Día de Comisión, para guardar en el directorio respectivo
	 * @param  array $datos 	Conjunto de información de la Orden del Día
	 * @return html				Documento en formato HTML, guardado en el directorio respectivo
	 */
	public function generarHtml($datos) {

		header("Content-Type: text/html; charset=UTF-8");
		
		ob_start();
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
				<?php $this->mostrarContenidoHead();?>

				<?= $this->CssOrdenComision();?>

				<style>
					body {
					  /* Importante, para que la versión de impresión muestre la negrita seteada */
					  font-family: 'Arial' !important;
					}
					@media print {
						/* Para que el pie se mantenga en una sola página de la versión de impresión */
						#pie_contenedor {
							page-break-inside: avoid;
						}
					}
				</style>
			</head>
		  	<body>
			    <div class="container-fluid p-0 px-3">

					<div class="container">
						<div class="row ">
		          			<div class="col-12 mt-2 px-5">
								<?php
								// Se muestra el Encabezado de la Orden del Día de Comisión
								echo (isset($datos['encabezado'])) ? html_entity_decode($datos['encabezado']) : '&nbsp;';

								// Se muestran los expedientes "Para tratar"
								echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 1);

								// Se muestran los expedientes "Para su conocimiento"
								echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 2);

								// Se muestran los expedientes "Para Archivo"
								echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 3);

								// Se muestran los expedientes "Para convalidar"
								echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 5);

								// Se muestra el Pie de la Orden del Día de Comisión
								echo (isset($datos['pie'])) ? html_entity_decode($datos['pie']) : '&nbsp;';
								?>
							</div>
						</div>
					</div>
				</div>
			</body>
		</html>
		<?php
		$contenido_html = ob_get_clean();

		fputs(fopen(RUTA_ORDENES_COMISION.$datos['codigo_comision'].'.html','w'), print_r($contenido_html, true));
	}

	public function convertirHtmlToPdf($datos) {

		$this->generarHtml($datos);
		//LibreriaGeneral::registrarLog("documento_html", RUTA_ORDENES_COMISION.$datos['codigo_comision'].'.html');

		$nombre_pdf = 'orden_'.$this->getNombreComision($datos['asunto']);

		// Se convierte a pdf con el comando 'lowriter'
        $cmd = sprintf('cd %s && wkhtmltopdf -s A4 %s.html %s.pdf', 
        	RUTA_ORDENES_COMISION, 
        	$datos['codigo_comision'],
        	$nombre_pdf
        );
        //LibreriaGeneral::registrarLog("cmd", $cmd);

        $output = shell_exec("( $cmd ) 2>&1");
        //LibreriaGeneral::registrarLog("output", $output);

        header("location:".URL_ORDENES_COMISION.$nombre_pdf.".pdf");
	}

	/**
	 * Se genera el HTML de la Orden del Día de Comisión, para visualizarla en otra pestaña
	 * @param  array $datos 	Conjunto de información de la Orden del Día
	 * @return html				Documento en formato HTML
	 */
	public function mostrarHTMLImpresion($datos) {
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
		  		<?php $this->mostrarContenidoHead();?>

				<?= $this->CssOrdenComision();?>

				<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
			</head>
		  	<body>
				<div id="contenido_pdf" class="container">
					<div class="row ">
	          			<div class="col-12 mt-2 px-3">
							<?php
							// Se muestra el Encabezado de la Orden del Día de Comisión
							echo (isset($datos['encabezado'])) ? html_entity_decode($datos['encabezado']) : '&nbsp;';

							// Se muestran los expedientes "Para tratar"
							echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 1);

							// Se muestran los expedientes "Para su conocimiento"
							echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 2);

							// Se muestran los expedientes "Para Archivo"
							echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 3);

							// Se muestran los expedientes "Para convalidar"
							echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 5);

							// Se muestra el Pie de la Orden del Día de Comisión
							echo (isset($datos['pie'])) ? html_entity_decode($datos['pie']) : '&nbsp;';
							?>
						</div>
					</div>
				</div>
				<script>
					const elemento = document.getElementById("contenido_pdf");

				    const opciones = {
				        margin: 10,
				        filename: 'orden_<?=strtolower(LibreriaGeneral::quitarVirgulillas(strtok(str_replace(',', '', $datos['asunto']), " ")))?>.pdf',
				        image: { type: 'jpeg', quality: 0.98 },
				        html2canvas: { scale: 4, letterRendering: true },
				        jsPDF: { unit: 'mm', format: 'letter', orientation: 'portrait' },
				        pagebreak: { mode: 'avoid-all' }
				    };

				    html2pdf()
				        .set(opciones)
				        .from(elemento)
				        .save()
				        .catch(err => console.log(err));
				</script>
			</body>
		</html>
		<?php
	}

	private function getNombreComision($titulo) {
		return strtolower(
			LibreriaGeneral::quitarVirgulillas(
				strtok(str_replace(',', '', $titulo), " ")
			)
		);
	}

	/**
	 * Se genera el HTML de la Orden del Día de Comisión, para visualizarla en otra pestaña
	 * @param  array $datos 	Conjunto de información de la Orden del Día
	 * @return html				Documento en formato HTML
	 */
	public function mostrarHTMLParaImpresion($datos) {
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
		  		<title>orden_<?=$this->getNombreComision($datos['asunto'])?></title>

		  		<?php $this->mostrarContenidoHead();?>

				<?= $this->CssOrdenComision();?>
				<style>
					body {
					  /* Importante, para que la versión de impresión muestre la negrita seteada */
					  font-family: 'Arial';
					}
					@media print {
						/* Para que el pie se mantenga en una sola página de la versión de impresión */
						#pie_contenedor {
							page-break-inside: avoid;
						}
					}
				</style>
			</head>
		  	<body>
				<div id="contenido_pdf" class="container">
					<div class="row ">
	          			<div class="col-12 mt-2 px-5">
							<?php
							// Se muestra el Encabezado de la Orden del Día de Comisión
							echo (isset($datos['encabezado'])) ? html_entity_decode($datos['encabezado']) : '&nbsp;';
							
							// Se muestran los expedientes "Para tratar"
							echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 1);

							// Se muestran los expedientes "Para su conocimiento"
							echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 2);

							// Se muestran los expedientes "Para Archivo"
							echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 3);

							// Se muestran los expedientes "Para convalidar"
							echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 5);
							
							// Se muestra el Pie de la Orden del Día de Comisión
							echo (isset($datos['pie'])) 
								? '<div id="pie_contenedor">'.html_entity_decode($datos['pie']).'</div>' 
								: '&nbsp;';
							?>
						</div>
					</div>
				</div>
				<script>
					window.print();
				</script>
			</body>
		</html>
		<?php
	}

	/**
	 * Se genera el HTML de la Orden del Día de Comisión, para visualizarla en otra pestaña
	 * @param  array $datos 	Conjunto de información de la Orden del Día
	 * @return html				Documento en formato HTML
	 */
	public function mostrarHTML($datos) {
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
				<?php $this->mostrarContenidoHead();?>

				<?= $this->CssOrdenComision();?>
			</head>
		  	<body>
			    <div class="container-fluid p-0 px-3">

					<?php $this->mostrarMenuPrincipal();?>

					<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>" />
					<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
					<input 
						type="hidden" 
						id="pagina" 
						name="pagina" 
						value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>"
					/>
					<input 
						type="hidden" 
						id="id" 
						name="id" 
						value="<?=(isset($datos['id'])) ? $datos['id'] : '';?>"
					/>

					<div class="row">
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							Ordenes del D&iacute;a de Comisiones
						</div>
					</div>
					<div class="container">
						<div class="row ">
		          			<div class="col-12 mt-2 px-5 bg-light">
								<?php
								// Se muestra el Encabezado de la Orden del Día de Comisión
								echo (isset($datos['encabezado'])) ? html_entity_decode($datos['encabezado']) : '&nbsp;';

								// Se muestran los expedientes "Para tratar"
								echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 1);

								// Se muestran los expedientes "Para su conocimiento"
								echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 2);

								// Se muestran los expedientes "Para Archivo"
								echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 3);

								// Se muestran los expedientes "Para convalidar"
								echo $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 5);

								// Se muestra el Pie de la Orden del Día de Comisión
								echo (isset($datos['pie'])) ? html_entity_decode($datos['pie']) : '&nbsp;';
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-12 text-center p-3">
								<button 
									type="button" 
									id="btVolver" 
									class="btn btn-info btn-sm mt-1 mt-sm-0" 
									title="Volver al listado"
								>
									<i class="fas fa-angle-left"></i>&nbsp;Volver
								</button>
								<button 
									type="button" 
									id="btnConfirmarPublicacion"
									class="btn btn-sm btn-success" 
								>
									<i class="fas fa-cloud-upload-alt"></i>&nbsp;Confirmar Publicaci&oacute;n
								</button>
							</div>
						</div>
					</div>
				</div>
				<script src="<?=URL_JS.$this->controlador;?>/vista_previa.js?v=<?=date("Ymd_His");?>"></script>
			</body>
		</html>
		<?php
	}

	/**
	 * Se listan los Expedientes con una Marca en comisión determinada
	 * @param  integer  $id_orden_comision [description]
	 * @param  [type]   $codigo_comision   [description]
	 * @param  integer  $marca_comision    [description]
	 * @return [type]                     [description]
	 */
	private function listarExpedientesPorMarca($id_orden_comision, $codigo_comision, $marca_comision = 0) {

		$expedientes = $this->modelo->obtenerItemsOrdenComision($id_orden_comision, $marca_comision);
		$cant_expedientes = (isset($expedientes)) ? count($expedientes) : 0;

		if ($cant_expedientes > 0) {
		?>
		<p class="font-weight-bold">
			<?=$this->mostrarNombreMarcaComision($marca_comision);?>
		</p>
		<table width="100%" border="0">
			<?php
			// Para cada expediente de la Marca
			for ($i=0; $i < $cant_expedientes; $i++) { $item = &$expedientes[$i];?>
				<tr>
					<td>
						<strong>
							<?=$item['anio'];?>&nbsp;
							<?=$item['tipo'];?>&nbsp;
							<?=$item['numero'];?>&nbsp;
							<?=$item['iniciador_codigo'];?>&nbsp;
							<?=$item['caratula'];?>
						</strong>
					</td>
				</tr>
				<tr>
					<td>
					<?php
					// Si posee extracto el item
					if (isset($item['extracto'])) {
						echo '<p class="text-justify">'.$item['extracto'].'</p>';
					} else {
						// Sino se obtienen los Extractos de los proyectos del expediente respectivo
						$extractos = $this->modelo->obtenerExtractosPorExpediente($item['anio'], $item['tipo'], $item['numero']);
						$cant_extractos = (isset($extractos)) ? count($extractos) : 0;
						// Por cada Extracto del expediente
						for ($e=0; $e < $cant_extractos; $e++) echo '<p>'.$extractos[$e]['extracto'].'</p>';
					}?>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
			<?php }?>
		</table>
		<br><br>
	<?php
		} 
	}

	/**
	 * Muestra una Orden del Día de Comisión en formato HTML
	 * @param integer $id
	 * @param string  $nombre_archivo
	 */
	public function mostrarListado($id, $nombre_archivo) {
	?>
		<!-- CSS original de Bootstrap v4.6 -->
    	<link href="<?=URL_CSS;?>bootstrap_4.6.0.css" rel="stylesheet">
		
		<div class="container p-0">
			<div class="row no-gutters">
	          	<div class="col-12 ml-3 ml-md-5 px-3 bg-light">
					<div class="text-center p-3">
						<button 
							name="confirmar" 
							type="button" 
							class="btn btn-sm btn-info " 
							onclick="javascript:confirmarPublicacion();">Confirmar</button>
					</div>
					<hr>
					<?php include(RUTA_DIRECTORIO_TEMPORAL.$nombre_archivo.".html"); ?>
					<hr>
					<div class="text-center p-3">
						<button 
							name="confirmar" 
							type="button" 
							class="btn btn-sm btn-info" 
							onclick="javascript:confirmarPublicacion();">Confirmar</button>
					</div>
				</div>
			</div>
		</div>
		<script>
			function confirmarPublicacion() {
				location.href = "<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=confirmarPublicacion&id=<?=$id;?>";
			}
		</script>
	<?php	
	}

	/**
	 * Muestra el resultado de la publicación, de una Orden del Día de Sesión, en el sitio web
	 * @param string $mensaje
	 * @param integer $tipo_mensaje
	 */
	public function mostrarResultadoPublicacion($mensaje, $tipo_mensaje) {
	?>
		<!-- CSS original de Bootstrap v4.6 -->
    	<link href="<?=URL_CSS;?>bootstrap_4.6.0.css" rel="stylesheet">
		
		<div class="container-fluid">
			<div class="row">
	          	<div class="col-12 text-center mt-3">
					<div class="alert alert-<?=($tipo_mensaje == 2) ? 'danger' : 'info'; ?>">
						<strong><?=$mensaje;?></strong><br><br>Puede cerrar esta pesta&ntilde;a del navegador
					</div>
				</div>
			</div>
		</div>
	<?php }

}