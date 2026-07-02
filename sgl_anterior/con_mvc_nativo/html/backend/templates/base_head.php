<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
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
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- Los tres metatags anteriores *deben* aparecer primero en head; cualquier otro debe estar *despues* de estos tags -->
	<meta name="description" content="<?php echo $this->vista->dataTituloApp; ?> Backend - <?php echo $this->vista->dataTitulo; ?>">
	<meta name="author" content="<?php echo $this->vista->dataAutorApp; ?>">
	
	<link rel="icon" href="../../resources/images/favicon.ico">
	
	<title><?php echo $this->vista->dataTituloApp; ?> Expedientes</title>
	
	<!-- Bootstrap -->
	<link href="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>bootstrap-backend/css/bootstrap.backend.css" rel="stylesheet">
	
	<!-- Bootstrap core JavaScript -->
	<script type="text/javascript" src="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>jquery/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>bootstrap-backend/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>moment/moment.min.js"></script>
	<script type="text/javascript" src="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>moment/locale/es.js"></script>
	
	<!-- Datatables -->
	<!-- <link rel="stylesheet" type="text/css" href="datatables/css/jquery.dataTables.min.css" />-->	 
	<link rel="stylesheet" type="text/css" href="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>datatables/css/dataTables.bootstrap.min.css" />
	<script type="text/javascript" src="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>datatables/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>datatables/js/dataTables.bootstrap.min.js"></script>

	<!-- CSS y JS para el Autosugerido -->		    
    <link href="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>jquery_ui_1_12_1/jquery-ui.css" rel="stylesheet">
    <script src="<?php echo URL_KRAKEN_HTML_LIBRERIAS; ?>jquery_ui_1_12_1/jquery-ui.js"></script>