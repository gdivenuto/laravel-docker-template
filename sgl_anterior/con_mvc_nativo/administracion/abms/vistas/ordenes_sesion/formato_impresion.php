<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaFormatoImpresion extends VistaBase {

	private $codigo_seccion_sobre_tablas;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'ordenes_sesion';

		// Se crea una instancia del modelo
		$this->modelo = new ordenes_sesionModel();

		// Para agregarse o no, la sección Sobre Tablas, en el documento de la Orden del Día de Sesión
        $this->codigo_seccion_sobre_tablas = '90000000';
	}

	/**
	 * Se genera el PDF de la Orden del día de Sesión, para su impresión y/o descarga
	 * @param  [array] $datos     			Conjunto de información de la Orden del Día
	 * @param  [array] $secciones 			Conjunto de Secciones de la Orden del Día
	 * @param  [boolean] $con_sobre_tablas 	Si se desea mostrar o no "Sobre Tablas" en el documento
	 *
	 * @return [pdf]	Documento en formato PDF
	 */
	public function mostrarPDF($datos, $secciones, $con_sobre_tablas) {

		// Se empieza a guardar el HTML en un bufer interno
		ob_start();
		?>
		<!-- Etiqueta propia de la librería, utilizada para el SUMARIO de la Orden del día de Sesión -->
		<page backtop="30mm" backbottom="10mm" backleft="25mm" backright="10mm" style="font-size:12px;color:#484848">
			<page_footer style="font-size:12px;">
				<table style="width:100%;">
					<tr>
						<td style="text-align:center;width: 100%">[[page_cu]]</td>
					</tr>
				</table>
			</page_footer>
			<?php
			// Se muestra el HTML correspondiente al Sumario de la Orden del Día de Sesión
			echo $this->formatoHtmlSumarioOrdenDiaSesion($datos, $secciones, $con_sobre_tablas);
			?>
		</page>
		<!-- Etiqueta propia de la librería, utilizada para el CUERPO de la Orden del día de Sesión -->
		<page backtop="30mm" backbottom="10mm" backleft="25mm" backright="10mm" style="font-size:12px;color:#484848">
			<page_footer style="font-size:12px;">
				<table style="width:100%;">
					<tr>
						<td style="text-align:center;width: 100%">[[page_cu]]</td>
					</tr>
				</table>
			</page_footer>
			<?php
			// Se muestra el HTML correspondiente al Cuerpo de la Orden del Día de Sesión
			echo $this->formatoHtmlCuerpoOrdenDiaSesion($datos, $secciones, $con_sobre_tablas);
			?>
		</page>
		<?php
		// Se asigna todo el HTML guardado en el bufer interno, para su conversión a PDF
		$contenido_html = ob_get_clean();

		try {
			// SE DIVIDE LA FECHA PARA ARMAR EL NOMBRE DEL DOCUMENTO PDF
			$partes_fecha = explode('-', $datos['fecha']);

			$anio = $partes_fecha[0];
			$mes = $partes_fecha[1];
			$dia = $partes_fecha[2];

			$nombre_pdf = $datos['reunion'] . " " . strtoupper(LibreriaGeneral::quitarAcentos($datos['sesion'])) . " " . $dia . " de " . $this->obtenerNombreMes($mes) . " de " . $anio . ".pdf";

			// conversion HTML => PDF (los métodos utilizados aquí están definidos en el archivo html2pdf.class.php)
			$html2pdf = new HTML2PDF('P', 'Legal', 'es');

			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');

			//Tratamiento del código HTML
			$html2pdf->WriteHTML($contenido_html);

			//Destino donde enviar el documento
			$html2pdf->Output($nombre_pdf);

		} catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

	/**
	 * Se genera el HTML de la Orden del Día de Sesión, para visualizarla en otra pestaña
	 * @param  [array] $datos     			Conjunto de información de la Orden del Día
	 * @param  [array] $secciones 			Conjunto de Secciones de la Orden del Día
	 * @param  [boolean] $con_sobre_tablas 	Si se desea mostrar o no "Sobre Tablas" en el documento
	 * @return [html]	Documento en formato HTML
	 */
	public function mostrarHTML($datos, $secciones, $con_sobre_tablas) {

		header("Content-Type: text/html; charset=UTF-8");

		// Se muestra el HTML correspondiente al Sumario de la Orden del Día de Sesión
		echo $this->formatoHtmlSumarioOrdenDiaSesion($datos, $secciones, $con_sobre_tablas);

		// Se muestra el HTML correspondiente al Cuerpo de la Orden del Día de Sesión
		echo $this->formatoHtmlCuerpoOrdenDiaSesion($datos, $secciones, $con_sobre_tablas);
	}

	/**
	 * Muestra una Orden del Día de Sesión en formato HTML
	 * @param string $nombre_archivo
	 */
	public function mostrarListado($periodo, $nombre_archivo) {
	?>
		<!-- CSS original de Bootstrap v4.6 -->
    	<link href="<?=URL_CSS;?>bootstrap_4.6.0.css" rel="stylesheet">

		<div class="container-fluid p-0">
			<div class="row no-gutters">
	          	<div class="col-12 ml-3 ml-md-5">
					<div class="text-center p-3">
						<button name="confirmar" type="button" class="btn btn-sm btn-info mb-2"
								onclick="javascript:confirmarPublicacion();">Confirmar</button>
					</div>

					<?php include(RUTA_DIRECTORIO_TEMPORAL.$nombre_archivo.".html"); ?>

					<div class="text-center p-3">
						<button name="confirmar" type="button" class="btn btn-sm btn-info mt-2"
								onclick="javascript:confirmarPublicacion();">Confirmar</button>
					</div>
				</div>
			</div>
		</div>

		<script>
			function confirmarPublicacion() {
				location.href = "<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=confirmarPublicacion&periodo=<?=$periodo;?>&nombre_archivo=<?=$nombre_archivo;?>";
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

	/**
	 * Se define el formato HTML del SUMARIO de la Orden del Día de Sesión
	 * @param  [array] $datos     			Conjunto de información de la Orden del Día
	 * @param  [array] $secciones 			Conjunto de Secciones de la Orden del Día
	 * @param  [boolean] $con_sobre_tablas 	Si se desea mostrar o no "Sobre Tablas" en el documento
	 * @return [html]	Sumario de la Orden del Día en formato HTML
	 */
	public function formatoHtmlSumarioOrdenDiaSesion($datos, $secciones, $con_sobre_tablas) {

		echo $this->estilosCssOrden();

		if ($datos['decreto_y_anexo'] === '1' && isset($datos['texto_decreto_previo_anexo'])) {

			echo '<div class="od_texto_decreto_previo_anexo">';
			echo html_entity_decode($datos['texto_decreto_previo_anexo']);
			echo '</div>';
			echo '<h1 style="text-align:center"><strong>ANEXO</strong></h1><br>';
		}
		?>

		<?php // TITULO DEL ENCABEZADO ?>
		<div class="od_titulo_encabezado">
			MUNICIPALIDAD DEL PARTIDO DE GENERAL PUEYRREDON
		</div>
		<?php // TITULO DEL ENCABEZADO ?>
		<div class="od_titulo_encabezado">
			DEPARTAMENTO DELIBERATIVO
		</div>

		<?php // DATOS DE LA ORDEN DEL DIA ?>
		<div class="od_datos_orden_dia_sesion">
		<?php

		$partes_fecha = explode('-', $datos['fecha']);

		$anio = $partes_fecha[0];
		$mes = $partes_fecha[1];
		$dia = $partes_fecha[2];
		?>
			<table class="orden_dia_sesion_datos_texto">
				<tr>
					<td class="orden_dia_sesion_datos_espacio_izquierdo">&nbsp;</td>
					<td class="orden_dia_sesion_datos_titulo">PERIODO:</td>
					<td class="orden_dia_sesion_datos_valor"><?= $datos['periodo'] . '&deg;'; ?></td>
				</tr>
				<tr>
					<td class="orden_dia_sesion_datos_espacio_izquierdo">&nbsp;</td>
					<td class="orden_dia_sesion_datos_titulo">REUNION:</td>
					<td class="orden_dia_sesion_datos_valor"><?= $datos['reunion'] . '&deg;'; ?></td>
				</tr>
				<tr>
					<td class="orden_dia_sesion_datos_espacio_izquierdo">&nbsp;</td>
					<td class="orden_dia_sesion_datos_titulo">SESION:</td>
					<td class="orden_dia_sesion_datos_valor"><?= $this->reemplazarPorHTML($datos['sesion']); ?></td>
				</tr>
				<tr>
					<td class="orden_dia_sesion_datos_espacio_izquierdo">&nbsp;</td>
					<td class="orden_dia_sesion_datos_titulo">A&Ntilde;O:</td>
					<td class="orden_dia_sesion_datos_valor"><?= $anio; ?></td>
				</tr>
				<tr>
					<td class="orden_dia_sesion_datos_espacio_izquierdo">&nbsp;</td>
					<td class="orden_dia_sesion_datos_titulo">DIA Y MES:</td>
					<td class="orden_dia_sesion_datos_valor"><?= $dia . ' de ' . $this->obtenerNombreMes($mes); ?></td>
				</tr>
				<tr>
					<td class="orden_dia_sesion_datos_espacio_izquierdo">&nbsp;</td>
					<td class="orden_dia_sesion_datos_titulo">HORA:</td>
					<td class="orden_dia_sesion_datos_valor"><?= $datos['hora'] . ' hs.'; ?></td>
				</tr>
			</table>
		</div>

		<?php // TITULO "ORDEN DEL DIA" ?>
		<div class="od_titulo_orden_del_dia">
			ORDEN DEL DIA
		</div>

		<?php // TITULO "SUMARIO" ?>
		<div class="od_titulo_sumario">
			SUMARIO
		</div>
		<?php
		//	SUMARIO DE LA ORDEN DEL DIA DE SESION
		// ---------------------------------------
		$cantidad_secciones = count($secciones);

		if ($cantidad_secciones > 0) {
			// PARA CADA SECCION PADRE
			for ($s = 0; $s < $cantidad_secciones; $s++) {
				$seccion = &$secciones[$s];

				// En base a la decisión del usuario, se muestra o no "Sobre Tablas" en el documento
				// Si la sección NO es "SOBRE TABLAS",
				// ó lo es y se permite agregarla al documento
				if ( ($seccion['codigo'] != $this->codigo_seccion_sobre_tablas) ||
					 ($seccion['codigo'] == $this->codigo_seccion_sobre_tablas && $con_sobre_tablas == 1)) {

					$numero = $s + 1;
					$numero_romano = LibreriaGeneral::convertir_a_numero_romano($numero);

					// SE OBTIENEN SUS SUBSECCIONES, SI POSEE DICHA SECCION
					// 14/07/2022 XXXX, se obtienen todas, aunque estén deshabilitadas, para el histórico
					$subsecciones = $this->modelo->obtenerTodasSubSecciones($seccion['codigo']);

					// SI POSEE SUBSECCIONES
					if ($subsecciones) {
						$ordenes_items_seccion = $this->modelo->obtenerOrdenesItems($datos['id'], $seccion['codigo'], '1');

						// ORDEN INICIAL DE LA SECCION
						$orden_inicial = $ordenes_items_seccion[0]['orden'];

						// ORDEN FINAL DE LA SECCION
						$ultima_posicion = count($ordenes_items_seccion) - 1;
						$orden_final = $ordenes_items_seccion[$ultima_posicion]['orden'];

						if ($orden_inicial != $orden_final) {
							$aclaracion_puntos = " (Del punto " . $orden_inicial . " al punto " . $orden_final . ")";
						} else {
							$aclaracion_puntos = " (Punto " . $orden_inicial . ")";
						}

						// TITULO DE LA SECCION DEL SUMARIO ?>
						<div class="od_titulo_sumario_seccion">
							<span class="od_titulo_sumario_mayuscula">
								<?php
								// 27/06/2022 XXXX retirado
								// $numero_romano . ' - ' . LibreriaGeneral::quitarAcentos($seccion['nombre']);
								echo $numero_romano . ' - ' . $seccion['nombre'];
								?>
							</span>
							<span style="font-size:15px"><?= $aclaracion_puntos; ?></span>
						</div>
						<?php
						$codigo_letra = 0;
						$contador_codigo_letra = 0;

						$cantidad_subsecciones = count($subsecciones);
						// PARA CADA SUBSECCION
						for ($ss = 0; $ss < $cantidad_subsecciones; $ss++) {
							$subseccion = &$subsecciones[$ss];

							// SI LA SUBSECCION POSEE ITEMS PARA DICHA ORDEN
							if ($this->modelo->tieneItems($datos['id'], $subseccion['codigo'])) {
								$codigo_letra = $contador_codigo_letra + 65;
								$contador_codigo_letra++;

								$ordenes_items_subseccion = $this->modelo->obtenerOrdenesItems($datos['id'], $subseccion['codigo']);

								// ORDEN INICIAL DE LA SUBSECCION
								$orden_inicial_subseccion = $ordenes_items_subseccion[0]['orden'];

								// ORDEN FINAL DE LA SUBSECCION
								$ultima_posicion_orden_subseccion = count($ordenes_items_subseccion) - 1;
								$orden_final_subseccion = $ordenes_items_subseccion[$ultima_posicion_orden_subseccion]['orden'];

								if ($orden_inicial_subseccion != $orden_final_subseccion) {
									$aclaracion_puntos = " (Punto " . $orden_inicial_subseccion . " al " . $orden_final_subseccion . ")";
								} else {
									$aclaracion_puntos = " (Punto " . $orden_inicial_subseccion . ")";
								}

								?>
								<?php // TITULO DE LA SUBSECCION DEL SUMARIO ?>
								<div class="od_titulo_sumario_subseccion">
									<?= chr($codigo_letra); ?>)
									<span class="od_titulo_sumario_mayuscula">
										<?php
										// 27/06/2022 XXXX
										// Se retiró el uso de quitarAcentos, porque solicitaron que se visualicen los acentos.
										// LibreriaGeneral::quitarAcentos($subseccion['nombre']);
										echo $subseccion['nombre'];
										?>
									</span>
									<?= $aclaracion_puntos; ?>
								</div>
						<?php
							}
						}
					} else {
						// SI LA SECCION POSEE ITEMS PARA DICHA ORDEN, AUN SIN TENER SUBSECCION
						if ($this->modelo->tieneItems($datos['id'], $seccion['codigo'])) {
							$ordenes_items_seccion = $this->modelo->obtenerOrdenesItems($datos['id'], $seccion['codigo']);

							// ORDEN INICIAL DE LA SECCION
							$orden_inicial = $ordenes_items_seccion[0]['orden'];

							// ORDEN FINAL DE LA SECCION
							$ultima_posicion = count($ordenes_items_seccion) - 1;
							$orden_final = $ordenes_items_seccion[$ultima_posicion]['orden'];

							if ($orden_inicial != $orden_final) {
								$aclaracion_puntos = " (Punto " . $orden_inicial . " al " . $orden_final . ")";
							} else {
								$aclaracion_puntos = " (Punto " . $orden_inicial . ")";
							}

							?>
							<?php // TITULO DE LA SECCION DEL SUMARIO ?>
							<div class="od_titulo_sumario_seccion">
								<span class="od_titulo_sumario_mayuscula">
									<?php
									// 27/06/2022 XXXX
									// Se retiró el uso de quitarAcentos, porque solicitaron que se visualicen los acentos.
									// LibreriaGeneral::quitarAcentos($seccion['nombre']);
									echo $numero_romano . ' - ' . $seccion['nombre'];
									?>
								</span>
								<span style="font-size:15px"><?= $aclaracion_puntos; ?></span>
							</div>
						<?php
						}
					}
				}
			}
		}
	}

	/**
	 * Se define el formato HTML del CUERPO de la Orden del Día de Sesión
	 * @param  [array] $datos     			Conjunto de información de la Orden del Día
	 * @param  [array] $secciones 			Conjunto de Secciones de la Orden del Día
	 * @param  [boolean] $con_sobre_tablas 	Si se desea mostrar o no "Sobre Tablas" en el documento
	 *
	 * @return [html]	Cuerpo de la Orden del Día en formato HTML
	 */
	public function formatoHtmlCuerpoOrdenDiaSesion($datos, $secciones, $con_sobre_tablas) {

		// Se crea una instancia del modelo
		$modelo = new ordenes_sesionModel();

		// CUERPO DE LA ORDEN DEL DIA DE SESION
		// ---------------------------------------
		$cantidad_secciones = count($secciones);

		if ($cantidad_secciones > 0) {
			// Para cada SECCION
			for ($s = 0; $s < $cantidad_secciones; $s++) {
				$seccion = &$secciones[$s];

				// En base a la decisión del usuario, se muestra o no "Sobre Tablas" en el documento
				// Si la sección NO es "SOBRE TABLAS",
				// ó lo es y se permite agregarla al documento
				if (($seccion['codigo'] != $this->codigo_seccion_sobre_tablas) || ($seccion['codigo'] == $this->codigo_seccion_sobre_tablas && $con_sobre_tablas == 1)) {
					// Se genera el salto de página para aquellas Secciones que permitan el Salto de Página
					// ------------------------------------------------------------------------------------
					if ($seccion['mostrar_con_salto_pagina'] === '1') {
						echo '<div style="page-break-after:always;"></div>';
					}

					$numero = $s + 1;
					$numero_romano = LibreriaGeneral::convertir_a_numero_romano($numero);

					$data_seccion['numero_romano'] = $numero_romano;
					$data_seccion['seccion_nombre'] = $seccion['nombre'];

					$this->nombre_seccion_ya_mostrada = false;

					// Se obtienen sus SUBSECCIONES, si posee dicha Sección
					// 14/07/2022 XXXX, se obtienen todas, aunque estén deshabilitadas, para el histórico
					$subsecciones = $this->modelo->obtenerTodasSubSecciones($seccion['codigo']);

					// Si posee SUBSECCIONES
					// ---------------------
					if ($subsecciones) {

						$codigo_letra = 0;
						$contador_codigo_letra = 0;

						$cantidad_subsecciones = count($subsecciones);
						// Para cada Subsección
						for ($ss = 0; $ss < $cantidad_subsecciones; $ss++) {
							$subseccion = &$subsecciones[$ss];

							// Se obtienen sus ITEMS:
							$items = $this->modelo->listarItemsOrdenDiaSesion($datos['id'], $subseccion['codigo']);

							$cantidad_items = (isset($items)) ? count($items) : 0;

							// Si posee ITEMS, se muestran con el nombre de la SUBSECCION como título
							if ($cantidad_items > 0) {

								$codigo_letra = $contador_codigo_letra + 65;
								$contador_codigo_letra++;

								$data_subseccion['codigo_letra'] = $codigo_letra;
								$data_subseccion['subseccion_nombre'] = $subseccion['nombre'];

								for ($i = 0; $i < $cantidad_items; $i++) {
									$dato = &$items[$i];

									// Sólo para el primer Item
									if ($i == 0) {
										// Se muestra el HTML del Item respectivo
										echo $this->mostrarItem($dato, $data_seccion, $data_subseccion);
										// Se marca como ya mostrada
										$this->nombre_seccion_ya_mostrada = true;
									} else {
										// Se muestra el HTML del Item respectivo
										echo $this->mostrarItem($dato);
									}
								}
							}
						}
					} else {
						// PARA CADA SECCION, SE OBTIENEN LOS ITEMS:
						$items = $this->modelo->listarItemsOrdenDiaSesion($datos['id'], $seccion['codigo']);

						$cantidad_items = count($items);

						// SI POSEE ITEMS, SE MUESTRAN CON EL NOMBRE DE LA SECCION COMO TITULO
						if ($cantidad_items > 0) {
							for ($i = 0; $i < $cantidad_items; $i++) {
								$dato = &$items[$i];

								// Sólo para el primer Item
								if ($i == 0) {
									// Se muestra el HTML del Item respectivo, precediéndole el nombre de su Sección
									echo $this->mostrarItem($dato, $data_seccion, null);
								} else {
									// Se muestra el HTML del Item respectivo
									echo $this->mostrarItem($dato);
								}
							}
						}
					}
				}
			}
		}
	}

	public function mostrarDescripcionDocumento($dato) {

		// Se obtiene el nombre del iniciador
		$iniciador_para_item = $this->modelo->obtenerIniciadorParaItem($dato['anio'], $dato['tipo'], $dato['numero']);

		switch ($dato['tipo']) {
		case 'E':
			$descripcion = "Expte " . $dato['numero'] . "-" . $iniciador_para_item['codigo_iniciador'] . "-" . substr($dato['anio'], 2, 2) . ': ';
			break;
		case 'N':
			$descripcion = "Nota " . $dato['numero'] . "-" . $iniciador_para_item['codigo_iniciador'] . "-" . substr($dato['anio'], 2, 2) . ': ';
			break;
		case 'D':
			$descripcion = "Decreto N&deg; " . $dato['numero'] . ': ';
			break;
		case '0':
			$descripcion = ""; // retirado Expte/Nota el 06/08/2018
			break;
		}

		return $descripcion;
	}

	/**
	 * Devuelve el HTML de un Item respectivo, utilizado tanto para el PDF como para el HTML
	 * @param  [array] $dato 			Info del Item
	 * @param  [array] $data_seccion 	Info de la Sección (numero_romano y nombre)
	 * @param  [array] $data_subseccion Info de la Subsección (codigo_letra y nombre)
	 */
	private function mostrarItem($dato, $data_seccion = null, $data_subseccion = null) { ?>
		<nobreak>
			<br>
			<!-- ITEM DE LA SECCION -->
			<table width="90%" border="0">
				<?php
				// Si se recibe la data de la Sección y NO se mostró aún
				if ($data_seccion != null && $this->nombre_seccion_ya_mostrada === false) {
					// Se muestra el nombre de la SECCION sólo con el primer Item
					echo '<tr><td colspan="2" style="font-size: 15px;font-weight: bold;text-align: center;">';
					echo '<span class="od_titulo_sumario_mayuscula">' . $data_seccion['numero_romano'] . ' - ' . $data_seccion['seccion_nombre'] . '</span>';// 27/06/2022 XXXX LibreriaGeneral::quitarAcentos()
					echo '</td></tr>';
					echo '<tr><td colspan="2">&nbsp;</td></tr>';
				}

				// Se muestra el nombre de la SUBSECCION sólo con el primer Item
				if ($data_subseccion != null) {
					// Título de la SUBSECCION
					echo '<tr><td colspan="2" style="font-size: 13px;font-weight: bold;text-align: left;">';
					echo chr($data_subseccion['codigo_letra']) . ') ' . $data_subseccion['subseccion_nombre'];
					// 27/06/2022 XXXX LibreriaGeneral::quitarAcentos()
					echo '</td></tr>';
					echo '<tr><td colspan="2">&nbsp;</td></tr>';
				}?>
				<tr>
					<td style="width: 25px; font-size: 13px; vertical-align: top;">
						<!-- NUMERO DE ORDEN DEL ITEM -->
						<strong><?= $dato['orden']; ?>.</strong>
					</td>
					<td style="width: 625px; font-size: 13px;text-align: justify;">
						<?php
						// Si el tipo del documento NO es "Otro"
						if ($dato['tipo'] != '0') {
							echo $this->mostrarDescripcionDocumento($dato);

							// Si la sección permite mostrar el Iniciador y/o el Autor
							if ($this->modelo->seMuestraIniciador($dato['cod_seccion']) || $this->modelo->seMuestraAutor($dato['cod_seccion'])) {
								// Si posee valor el campo "autor", se muestra, el cual puede ser:
								// Sólo Iniciador
								// Sólo Autor
								// Iniciador / Autor
								echo ($dato['autor'] != '') ? LibreriaGeneral::aMayusculas($dato['autor']) . ': ' : '';
							}

							// Si posee Carátula
							if ($dato['caratula'] != '') {
								// SI LA SECCION PERMITE MOSTRAR LA CARATULA EN EXPEDIENTES
								if ($dato['tipo'] == 'E' && $this->modelo->seMuestraCaratulaEnExpedientes($dato['cod_seccion'])) {
									echo LibreriaGeneral::aMayusculas($dato['caratula']) . ': ';
								}

								// SI LA SECCION PERMITE MOSTRAR LA CARATULA EN NOTAS
								if ($dato['tipo'] == 'N' && $this->modelo->seMuestraCaratulaEnNotas($dato['cod_seccion'])) {
									echo LibreriaGeneral::aMayusculas($dato['caratula']) . ': ';
								}
							}
						}

						// 13/06/2019 XXXX
						// Se eliminan solamente los espacios al final del extracto
						//
						// 08/05/2019 XXXX
						// Se agregó la función nl2br de PHP para convertir los saltos de línea a HTML (etiqueta <br>)
						//
						// Se muestra el Extracto
						echo nl2br(rtrim($dato['extracto']));

						// Si la sección permite mostrar las Comisiones
						if ($this->modelo->seMuestranComisiones($dato['cod_seccion'])) {
							echo '&nbsp;<strong>' . LibreriaGeneral::aMayusculas(trim($dato['giros'])) . '</strong>';
						}

						// Si posee Detalle se muestra
						if ($dato['detalle'] != '') {
							echo '&nbsp;<b>' . LibreriaGeneral::aMayusculas(trim($dato['detalle'])) . '</b>';
						}

						// Si la sección padre del ítem es de:
						// 40: DICTAMEN DE COMISION
						// 50: EXPEDIENTES Y NOTAS CON DICTAMEN DE COMISION
						if ( substr($dato['cod_seccion'], 0, 2) === '40' || substr($dato['cod_seccion'], 0, 2) === '50')
						{
							$despachos = $this->modelo->obtenerDespachosItem($dato['id']);

							$cant_despachos = (isset($despachos)) ? count($despachos) : 0;

							// Si existen despachos que mostrar
							for ($i=0; $i < $cant_despachos; $i++) {

								$link  = '&nbsp;';
								$link .= '<a href="'.$despachos[$i]['documento'].'"';
								$link .= 'target="_blank">Ver despacho ';
								$link .= ($cant_despachos === 1) ? '' : $i+1;
								$link .= '</a>';

								echo $link;
							}
						}
						?>
					</td>
				</tr>
			</table>
		</nobreak>
		<?php }

}
