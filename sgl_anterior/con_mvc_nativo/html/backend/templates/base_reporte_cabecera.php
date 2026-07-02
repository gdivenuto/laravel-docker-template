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
<page_header>
	<table id="pdf_encabezado" class="pdf_texto_gris">
		<tr>
			<td id="pdf_encabezado_logo" rowspan="3">
				<img src="<?= URL_KRAKEN_RESOURCES_ASSET_IMAGES; ?>logo_118x55.png">
			</td>
			<td id="pdf_encabezado_nombre_sistema">
                <table align="right">
                    <tr>
                        <td>
                            <!-- 1983 - 40&deg; Aniversario de la Recuperaci&oacute;n Democr&aacute;tica - 2023
                            <br>
                            A&ntilde;o Homenaje al cuadrag&eacute;simo aniversario de vigencia ininterrumpida de la democracia -->
                            &nbsp;
                        </td>
                    </tr>
                    <tr><td><?= $this->vista->dataTitulo; ?></td></tr>
                </table>
            </td>
		</tr>
	</table>
	<div class="pdf_separador"></div>
</page_header>