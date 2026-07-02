<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 *
 *  $this->vista->data              Array asociativo que contiene todos los parámetros de la vista para ser
 *                                  utilizados en la plantilla.
 *
 * Además:
 *
 *  $this->vista->dataTitulo        Titulo de la vista
 *  $this->vista->dataSubtitulo     Subtitulo de la vista
 *  $this->vista->dataTexto         Texto introductorio de la vista
 *  $this->vista->dataUsuario       Instancia del usuario actual.
 *  $this->vista->dataMensajeOk     Mensaje de confirmación que debe mostrarse en la vista.
 *  $this->vista->dataMensajeError  Mensaje de error que debe mostrarse en la vista.
 */

// Información de la actuacion
$actuacion = $this->vista->data['actuacion'];
$paso = $actuacion->obtenerPasoActual();
$transaccion_data = (! is_null($this->vista->data['transac_actuacion'])) 
    ? $this->vista->data['transac_actuacion']->data
    : null;

// Modal de espera de carga
?>
<div id="v_modal_espera_paso" class="modal" tabindex="-1" role="dialog" data-delay="0.6" data-open="0.6">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <span class="glyphicon glyphicon-hourglass"></span>&nbsp;Espere
                </h2>
            </div>
            <div class="modal-body">
                <p>Aguarde unos instantes...</p>
            </div>
        </div>
    </div>
</div>

<?php
// Transformación de datos a JSON para tenerlos disponibles en la vista.
// Desde JavaScript no hace falta ninguna transformación, dado que JSON es nativo de JavaScript.
?>
<script type="text/javascript">
    // Parametros
    var base_url = '<?= $this->vista->baseUrl; ?>';

    // Acumulador de errores
    var errores = [];

    // Volcado de datos JSON
    var actuacion = <?= JsonHelper::get()->serializar($actuacion, JSON_HEX_APOS); ?>;
    var paso = <?= JsonHelper::get()->serializar($paso, JSON_HEX_APOS); ?>;
    var transaccion = <?= (is_null($transaccion_data)) ? 'null' : $transaccion_data; ?>;
    var tipo_actuacion = '<?= $actuacion->obtenerTipoDeClaseActuacion(); ?>';
    var actuacion_texto_informativo = '<?= $actuacion->generarTextoInformativo(); ?>';
    
    // Si es true, se advierte de ir al paso anterior (para no perder cambios).
    var flag_advertir_anterior = false;
</script>