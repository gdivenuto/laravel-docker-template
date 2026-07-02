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
 *  $this->vista->dataTitulo		Titulo de la vista
 *  $this->vista->dataSubtitulo		Subtitulo de la vista
 *  $this->vista->dataTexto			Texto introductorio de la vista
 *  $this->vista->dataUsuario		Instancia del usuario actual.
 *  $this->vista->dataMensajeOk		Mensaje de confirmación que debe mostrarse en la vista.
 *  $this->vista->dataMensajeError	Mensaje de error que debe mostrarse en la vista.
 */
?>
	<div class="row">
		<div id="login_contenedor" class="col-xs-10 col-xs-offset-1 col-md-6 col-md-offset-3">
			<h1 class="text-center">
				<img src="../../resources/images/escudo_hcd_35x40.png" alt="SGLv2">&nbsp;Sistema de Gesti&oacute;n Legislativa
			</h1>
			<h2 class="text-center">HCD - Municipalidad de General Pueyrredon</h2>
			<br>
			<span>
				Este es el portal de acceso al panel administrativo de SGLv2.
				<br>Haga click <a href="index.php?c=login&a=view">aqu&iacute;</a> para iniciar sesi&oacute;n.
			</span>
		</div>
	</div>
	<script>
		jQuery(document).ready(function() {
			$('body').css({
				"background-image": "url(../../resources/images/fondo_gris_ejemplo.jpg)",
			 	"background-repeat": "no-repeat",
				"background-size": "cover"
			});
			
			$('nav').css("display", "none");
			//$('footer').css("background-color", "transparent !important");
			
			$('.page-header').css({
			    "border-bottom": 0
			});

			$('#login_contenedor').css({
				"margin-top": "5%",
			 	"padding": "20px 30px",
				"background-color": "#4682B4",
				"opacity": ".8",
				"color": "#fff",
				"border-radius": 10,
				"text-align": "center"
			});
			$('#login_contenedor span').css({
				"font-size": "14px"
			});
			$('#login_contenedor span a').css({
				"color": "#fff",
				"font-weight": 700
			});
			$('#login_contenedor span a').hover(function() {
				$(this).css("color", "orange")
			});
		});
	</script>