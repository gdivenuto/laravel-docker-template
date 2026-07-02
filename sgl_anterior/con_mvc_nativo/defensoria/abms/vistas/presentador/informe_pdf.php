<?php
if (!isset($_SESSION))
	session_start();

class VistaSocioInformePDF extends VistaBase {

	public function __construct() {
		parent::__construct();
	}

	public function mostrar($listado) {

		ob_start();

        $cantidad = count($listado);
		?>
        <style type="text/css">
            img {
                border: 0;
                vertical-align: middle;
            }
            table {
                width: 100%;
                padding: 0;
                border-collapse: collapse;
                font-size: 15px;
            }
            .pdf_alineado_izquierda {
                text-align: left;
                padding-left: 5px;
            }
            .pdf_alineado_centrado {
                text-align: center;
            }
            .pdf_alineado_derecha {
                text-align: right;
                padding-right: 5px;
            }
            .pdf_texto_verde {
                color: #7EB03D;
            }
            .pdf_texto_rojo {
                color: #dd3c4c;
            }
            .pdf_lista_titulos th {
                text-align: center;
                background-color: #5e656b;
                color: #e9ecef;
            }
            .pdf_lista_detalle_pedido_info {
                overflow-x: hidden;
            }
            .pdf_lista_detalle_pedido_info tr {
                color: #6C7073;
            }
            .pdf_lista_detalle_pedido_info td {
                border: 1px solid #BEBEBE;
            }
        </style>
        <page backtop="7mm" backbottom="7mm" backleft="15mm" backright="5mm">
            <table>
                <tr>
                    <td style="width:30%;">
                        <img src="<?=URL_IMAGENES;?>logo.png" />
                    </td>
                    <td style="width:70%;font-size:14px;color:#6E6F73;">
                        <br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;Listado de Socios al <?= date("d/m/Y"); ?>.
                    </td>
                </tr>
            </table>
            <table>
                <thead>
                    <tr class="pdf_lista_titulos">
                        <th class="pdf_alineado_centrado">Nro.</th>
                        <th class="pdf_alineado_centrado">Nombre</th>
                        <th class="pdf_alineado_centrado">Saldo</th>
                        <th class="pdf_alineado_centrado">M&oacute;vil</th>
                        <th class="pdf_alineado_centrado">Mail</th>
                    </tr>
                </thead>
                <tbody class="pdf_lista_detalle_pedido_info">
                    <?php for ($i = 0; $i < $cantidad; $i++) { 
                        $dato = &$listado[$i]; 

                        // Si el saldo es cero
                        if ($dato['saldo_actual'] == '0.00') {
                            $css_color_saldo = '';
                            $saldo_a_mostrar = '0.00';
                        }
                        // Si el saldo es negativo, es crédito del Cliente
                        elseif ($dato['saldo_actual'] < '0.00') {
                            $css_color_saldo = 'pdf_texto_verde';
                            $saldo_a_mostrar = number_format($dato['saldo_actual'] * (-1), 2, ',', '.');
                        }
                        // Si el saldo es positivo, es a favor de la empresa
                        else {
                            $css_color_saldo = 'pdf_texto_rojo';
                            $saldo_a_mostrar = number_format($dato['saldo_actual'], 2, ',', '.');
                        }
                    ?>
                        <tr>
                            <td class="pdf_alineado_derecha" width="20">
                                <?=(isset($dato['id'])) ? $dato['id'] : '&nbsp;';?>
                            </td>
                            <td class="pdf_alineado_izquierda" width="150">
                                <?=(isset($dato['nombre'])) ? $dato['nombre'] . ' ' . $dato['apellido'] : '&nbsp;';?>
                            </td>
                            <td class="pdf_alineado_derecha <?=$css_color_saldo; ?>" width="80">
                                $&nbsp;<?= $saldo_a_mostrar; ?>
                            </td>
                            <td class="pdf_alineado_derecha" width="110">
                                <?=(isset($dato['movil_cod_area'])) ? $dato['movil_cod_area'] . ' ' . $dato['movil_numero'] : '&nbsp;';?>
                            </td>
                            <td class="pdf_alineado_izquierda" width="240">
                                <?=(isset($dato['mail'])) ? $dato['mail'] : '&nbsp;';?>
                            </td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </page>
        <?php
        $contenido = ob_get_clean();
		try {
			// conversion HTML => PDF
			$html2pdf = new HTML2PDF('P', 'A4', 'es');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->WriteHTML($contenido);
			$html2pdf->Output("resumen_cta_cte_socios_" . date("dmY") . ".pdf");
		} catch (HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}
}