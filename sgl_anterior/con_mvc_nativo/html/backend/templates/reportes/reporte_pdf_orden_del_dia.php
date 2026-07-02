<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionReport o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 *
 *  $this->vista->data 	Array asociativo que contiene todos los parámetros de la vista para ser
 *  					utilizados en la plantilla.
 */
// Resultado completo de expedientes
$expedientes = $this->vista->data['resultados'];
?>
<!-- Aquí comienza a mostrarse el resultado completo -->
<table class="pdf_tabla_cuerpo_reporte">
	<tbody>
		<?php
		$cantidad_expedientes = Array();
		$cantidad_notas = Array();
		$marca_auxiliar = 99;

		foreach ($expedientes as $e) {
			// Si posee una marca en comisión
			if ( !is_null($e->marca_comision) ) {
				// Si es diferente a la ya mostrada
				if ( $e->marca_comision != $marca_auxiliar ) {
					$marca_auxiliar = $e->marca_comision;

					switch ($marca_auxiliar) {
						case '0':
							$nombre_marca = "Sin marca";
							break;
						case '1':
							$nombre_marca = "Para tratar";
							break;
						case '2':
							$nombre_marca = "Para su conocimiento";
							break;
						case '3':
							$nombre_marca = "Para archivo";
							break;
						// 07/12/2021 XXXX, se retiró esta Marca.
						// case '4':
						// 	$nombre_marca = "Para pr&oacute;rroga";
						// 	break;
						case '5':
							$nombre_marca = "Para convalidar";
							break;
					}
					// Se muestra el nombre de la marca en comisión respectiva antes de cada listado
					echo '<tr><td class="listado_pdf_titulo_marca_comision">'.$nombre_marca.'</td></tr>';
				}
				// Se contabilizan los Expedientes y las Notas de cada marca
				if ($e->tipo == 'E') {
					$cantidad_expedientes[$e->marca_comision]++;
				}
				elseif ($e->tipo == 'N') {
					$cantidad_notas[$e->marca_comision]++;
				}
				?>
				<tr>
					<td class="pdf_ficha_expediente">
						<table>
							<tr>
								<td class="listados_pdf_ficha_clave_expediente">
									<strong><?= sprintf('%d&nbsp;%s&nbsp;%d&nbsp;%d&nbsp;%d&nbsp;&nbsp;&nbsp;&nbsp;%s&nbsp;&nbsp;&nbsp;&nbsp;%s', $e->anio, $e->tipo, $e->numero, $e->cuerpo, $e->alcance, $e->iniciador_codigo, $e->caratula); ?></strong>
								</td>
								<td class="listados_pdf_ficha_fecha_ingreso_expediente">
									<strong><?= sprintf('%s', Validator::get()->convertirAFechaVista($e->fecha_entrada_expe)); ?></strong>
								</td>
							</tr>
						</table>
						<table>
					<?php
					foreach ($e->proyectos as $p)
						if ( !is_null($p->extracto) && $p->extracto != '' )
							echo '<tr><td class="listados_pdf_ficha_info_extracto_e_informes">'.$p->extracto.'</td></tr>';
					?>
						</table>
					</td>
				</tr>
			<?php
			}
		} // fin del foreach de expedientes

		$cant_expe_marca_1 = (isset($cantidad_expedientes[1])) ? $cantidad_expedientes[1] : 0;
		$cant_expe_marca_2 = (isset($cantidad_expedientes[2])) ? $cantidad_expedientes[2] : 0;
		$cant_expe_marca_3 = (isset($cantidad_expedientes[3])) ? $cantidad_expedientes[3] : 0;
		$cant_expe_marca_5 = (isset($cantidad_expedientes[5])) ? $cantidad_expedientes[5] : 0;

		$cant_total_expe = $cant_expe_marca_1 + $cant_expe_marca_2 + $cant_expe_marca_3 + $cant_expe_marca_5;

		$cant_nota_marca_1 = (isset($cantidad_notas[1])) ? $cantidad_notas[1] : 0;
		$cant_nota_marca_2 = (isset($cantidad_notas[2])) ? $cantidad_notas[2] : 0;
		$cant_nota_marca_3 = (isset($cantidad_notas[3])) ? $cantidad_notas[3] : 0;
		$cant_nota_marca_5 = (isset($cantidad_notas[5])) ? $cantidad_notas[5] : 0;

		$cant_total_notas = $cant_nota_marca_1 + $cant_nota_marca_2 + $cant_nota_marca_3 + $cant_nota_marca_5;
		?>
		<tr>
			<td>
				<table>
					<tr>
						<td>&nbsp;</td>
						<td class="listado_pdf_valor_contador pdf_texto_gris">Expedientes</td>
						<td class="listado_pdf_valor_contador pdf_texto_gris">Notas</td>
					</tr>
					<tr>
						<td class="listado_pdf_titulo_marca pdf_texto_gris">Para Tratar:</td>
						<td class="listado_pdf_valor_contador">
							<?= $cant_expe_marca_1; ?>
						</td>
						<td class="listado_pdf_valor_contador">
							<?= $cant_nota_marca_1; ?>
						</td>
					</tr>
					<tr>
						<td class="listado_pdf_titulo_marca pdf_texto_gris">Para Su Conocimiento:</td>
						<td class="listado_pdf_valor_contador">
							<?= $cant_expe_marca_2; ?>
						</td>
						<td class="listado_pdf_valor_contador">
							<?= $cant_nota_marca_2; ?>
						</td>
					</tr>
					<tr>
						<td class="listado_pdf_titulo_marca pdf_texto_gris">Para Archivo:</td>
						<td class="listado_pdf_valor_contador">
							<?= $cant_expe_marca_3; ?>
						</td>
						<td class="listado_pdf_valor_contador">
							<?= $cant_nota_marca_3; ?>
						</td>
					</tr>
					<tr>
						<td class="listado_pdf_titulo_marca pdf_texto_gris">Para Convalidar:</td>
						<td class="listado_pdf_valor_contador">
							<?= $cant_expe_marca_5; ?>
						</td>
						<td class="listado_pdf_valor_contador">
							<?= $cant_nota_marca_5; ?>
						</td>
					</tr>
					<tr>
						<td class="listado_pdf_titulo_marca pdf_texto_gris"><strong>Total</strong>:</td>
						<td class="listado_pdf_valor_contador">
							<strong><?= $cant_total_expe; ?></strong>
						</td>
						<td class="listado_pdf_valor_contador">
							<strong><?= $cant_total_notas; ?></strong>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>
