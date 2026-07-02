<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionReport o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 				Array asociativo que contiene todos los parámetros de la vista para ser
 *  								utilizados en la plantilla.
 *
 * Además:
 * 
 *  $this->vista->dataTituloApp		Titulo de la aplicacion
 *  $this->vista->dataTitulo		Titulo de la vista
 *  $this->vista->dataSubtitulo		Subtitulo de la vista
 *  $this->vista->dataTexto			Texto introductorio de la vista
 *  $this->vista->dataUsuario		Instancia del usuario actual.
 *  $this->vista->dataMensajeOk		Mensaje de confirmación que debe mostrarse en la vista.
 *  $this->vista->dataMensajeError	Mensaje de error que debe mostrarse en la vista.
 */
?>
<page_footer>
	<table id="pdf_pie" class="pdf_texto_gris">
		<tr>
            <td class="pdf_pie_logo" rowspan="3">
                <img src="<?= URL_KRAKEN_RESOURCES_ASSET_IMAGES.'logo_pie_izquierdo.png';?>" width="80" height="80" />
            </td>
            <td id="pdf_pie_info">
                Hip. Yrigoyen 1627 | 2&deg; Piso Palacio Municipal | Ala izquierda
                <br>
                Tel. 223 499 6510 – B7600DOM | Mar del Plata | Prov. de Buenos Aires | Rep. Argentina
                <br>
                info@concejomdp.gob.ar | www.concejomdp.gob.ar
            </td>
            <td class="pdf_pie_logo" rowspan="3">
                <img src="<?= URL_KRAKEN_RESOURCES_ASSET_IMAGES.'logo_pie_derecho.png';?>" width="80" height="80" />
            </td>
        </tr>
	</table>
</page_footer>