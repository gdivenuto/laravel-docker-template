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

$c_anio = Validator::get()->obtenerDefault($this->vista->data['f_anio'], '');
$c_tipo = Validator::get()->obtenerDefault($this->vista->data['f_tipo'], '-');
$c_numero = Validator::get()->obtenerDefault($this->vista->data['f_numero'], '');
$c_cuerpo = Validator::get()->obtenerDefault($this->vista->data['f_cuerpo'], 0);
$c_alcance = Validator::get()->obtenerDefault($this->vista->data['f_alcance'], 0);

$clave = "&f_anio=".$c_anio."&f_tipo=".$c_tipo."&f_numero=".$c_numero."&f_cuerpo=".$c_cuerpo."&f_alcance=".$c_alcance;
?>

<?php $this->generarModalDialog(); ?>

  <div class="row">
        <!-- Criterio de búsqueda simple + Solapas + Botón NUEVO + Grilla -->
        <div class="col-md-9 responsive">
            <div class="row">
                <div class="col-md-12 responsive">
                  El m&oacute;dulo de prestamos no ha sido implementado a&uacute;n.
                </div>
            </div>
        </div>
    </div>