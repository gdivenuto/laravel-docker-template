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
// Criterio de búsqueda utilizado
$criterios = (array_key_exists('criterio_busqueda', $this->vista->data))
    ? $this->vista->data['criterio_busqueda']
    : null;

if (isset($criterios) && $criterios != '') { // && count($criterios) > 0
?>
	<!-- Aquí se muestra el criterio de búsqueda utilizado -->
	<table class="pdf_tabla_cuerpo_reporte">
		<tr>
			<td class="pdf_ficha_expediente pdf_texto_gris">
				<table>
					<tr><td colspan="2"><strong>Criterio de b&uacute;squeda utilizado.</strong></td></tr>
					<tr><td><?php echo implode('<br>', $criterios); ?></td></tr>
				</table>
			</td>
		</tr>
	</table>
<?php
}
?>