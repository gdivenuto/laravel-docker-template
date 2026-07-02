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
 *  $this->vista->dataTipoCabecera  De que forma será renderizada la cabecera (VISTA_CABECERA_VACIA | VISTA_CABECERA_ALERT | VISTA_CABECERA_MODAL).
 *  $this->vista->dataTituloApp		Titulo de la aplicacion
 *  $this->vista->dataTitulo		Titulo de la vista
 *  $this->vista->dataSubtitulo		Subtitulo de la vista
 *  $this->vista->dataTexto			Texto introductorio de la vista
 *  $this->vista->dataUsuario		Instancia del usuario actual.
 *  $this->vista->dataMensajeOk		Mensaje de confirmación que debe mostrarse en la vista.
 *  $this->vista->dataMensajeError	Mensaje de error que debe mostrarse en la vista.
 */

// requerimientos del header
$requiereHeader = 	($this->vista->dataTitulo != '') 
				|| 	($this->vista->dataSubtitulo != '')
				|| 	($this->vista->dataMensajeOk != '')
				|| 	($this->vista->dataMensajeError != '')
				|| 	($this->vista->dataTexto != '');

if ($requiereHeader) {
    switch ($this->vista->dataTipoCabecera) {
        case VISTA_CABECERA_VACIA:
            echo "<!-- Sin cabecera -->";
            break;

        case VISTA_CABECERA_ALERT:
            require($this->vista->baseTemplatePath . 'base_cabecera_alert.php');
            break;
        
        case VISTA_CABECERA_MODAL:
            require($this->vista->baseTemplatePath . 'base_cabecera_modal.php');
            break;
    }
}
?>