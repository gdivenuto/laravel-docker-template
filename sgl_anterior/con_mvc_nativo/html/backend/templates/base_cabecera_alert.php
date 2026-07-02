<?php
/**
 * Este script esta diseñado para ser incluído desde la plantilla base_cabecera.php.
 *
 * Los parámetros disponibles para trabajar con esta plantilla son:
 * 
 *  $this->vista->data              Array asociativo que contiene todos los parámetros de la vista para ser
 *                                  utilizados en la plantilla.
 *
 * Además:
 * 
 *  $this->vista->dataTipoCabecera  De que forma será renderizada la cabecera (VISTA_CABECERA_VACIA | VISTA_CABECERA_ALERT | VISTA_CABECERA_MODAL).
 *  $this->vista->dataTituloApp     Titulo de la aplicacion
 *  $this->vista->dataTitulo        Titulo de la vista
 *  $this->vista->dataSubtitulo     Subtitulo de la vista
 *  $this->vista->dataTexto         Texto introductorio de la vista
 *  $this->vista->dataUsuario       Instancia del usuario actual.
 *  $this->vista->dataMensajeOk     Mensaje de confirmación que debe mostrarse en la vista.
 *  $this->vista->dataMensajeError  Mensaje de error que debe mostrarse en la vista.
 */
?>
<div class="page-header">

<?php 

// if ($this->vista->dataTitulo != '') echo <<<HTML
// 	<h1>{$this->vista->dataTitulo}</h1>
// HTML;

// if ($this->vista->dataSubtitulo != '') echo <<<HTML
// 	<h2>{$this->vista->dataSubtitulo}</h2>
// HTML;

if ($this->vista->dataMensajeOk != '') echo <<<HTML
	<div id="msg_ok" class="alert alert-success" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		{$this->vista->dataMensajeOk}
	</div>
HTML;

if ($this->vista->dataMensajeError != '') echo <<<HTML
	<div id="msg_error" class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		{$this->vista->dataMensajeError}
	</div>
HTML;

// if ($this->vista->dataTexto != '') echo <<<HTML
// 	<p>{$this->vista->dataTexto}</p>
// HTML;

?>

</div>
