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

if ($this->vista->dataMensajeOk != '') echo <<<HTML
    <div id="v_modal_cabeceraok_dialog" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="v_modal_cabeceraok_dialog_titulo" class="modal-title">Aviso</h2>
                </div>
                <div class="modal-body">
                    <p id="v_modal_cabeceraok_dialog_texto">{$this->vista->dataMensajeOk}</p>
                </div>
                <div class="modal-footer">
                    <button id="v_modal_cabeceraok_dialog_btn_cerrar" type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script type="text/javascript">
        $('#v_modal_cabeceraok_dialog').modal();
    </script>
HTML;

if ($this->vista->dataMensajeError != '') echo <<<HTML
    <div id="v_modal_cabeceraerr_dialog" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="v_modal_cabeceraerr_dialog_titulo" class="modal-title">Error</h2>
                </div>
                <div class="modal-body">
                    <p id="v_modal_cabeceraerr_dialog_texto">{$this->vista->dataMensajeError}</p>
                </div>
                <div class="modal-footer">
                    <button id="v_modal_cabeceraerr_dialog_btn_cerrar" type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script type="text/javascript">
        $('#v_modal_cabeceraerr_dialog').modal();
    </script>
HTML;

?>