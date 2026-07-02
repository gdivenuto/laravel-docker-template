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
$c_ultima_accion_realizada = new DateTime();

if (KRAKEN_DEBUG_MODE) {
	echo "\n<!-- DEBUG DATA ------------------------------------------------------------ -->\n";
	echo '<div class="container">';
	echo '<div class="row">';
	echo '<div id="msg_debug" class="alert alert-success" role="alert">';
	echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	echo '<pre>';
	print_r($this->vista->data);
	echo '</pre>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo "\n<!-- END DEBUG DATA -------------------------------------------------------- -->\n";
}
/*************************************************
	Obtenemos datos del usuario
*************************************************/
// Usuario (codigo_usuario)
$c_usuario = ( isset($this->vista->data['usuario']) ) ? $this->vista->data['usuario']->codigo_usuario : 'invitado';

// IP de la PC utilizada
$c_ip  = $_SERVER['REMOTE_ADDR'];
?>
<footer style="margin-top:0" class="fixed-bottom">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<p class="text-muted text-left small">2015 - <?php echo date("Y");?> | <strong><?php echo $this->vista->dataTituloApp; ?></strong> por <u><?php echo $this->vista->dataAutorApp; ?></u> | Usuario: <?php echo $c_usuario.'@'.$c_ip; ?> | &Uacute;ltima acci&oacute;n realizada: <?php echo $c_ultima_accion_realizada->format('d/m/Y H:i:s'); ?></p>
			</div>
		</div>
	</div>
</footer>