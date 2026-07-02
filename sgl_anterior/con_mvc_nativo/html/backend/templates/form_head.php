<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewActionForm o alguno de sus descendientes.
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
	<!-- jQuery Validation -->
	<script type="text/javascript" src="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>jquery-validation/dist/jquery.validate.min.js"></script>
	<script type="text/javascript" src="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>jquery-validation/dist/additional-methods.js"></script>

	<!-- jQueryUI DatePicker -->
	<link rel="stylesheet" type="text/css" href="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>jquery-ui-custom/jquery-ui.min.css">
	<script type="text/javascript" src="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>jquery-ui-custom/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>jquery-ui-custom/i18n/datepicker-es.js"></script>

	<!-- Bootstrap TagsInput -->
	<script type="text/javascript" src="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>bootstrap-tagsinput/bootstrap-tagsinput.css" />
	