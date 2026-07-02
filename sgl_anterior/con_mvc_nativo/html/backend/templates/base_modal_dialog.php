<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
 * Representa una ventana modal.
 */
?>
<div id="v_modal_dialog" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h2 id="v_modal_dialog_titulo" class="modal-title">&nbsp;</h2>
			</div>
			<div class="modal-body">
				<p id="v_modal_dialog_texto">&nbsp;</p>
			</div>
			<div class="modal-footer">
				<button id="v_modal_dialog_btn_sobreescribir" type="button" class="btn btn-default" data-dismiss="modal">Sobreescribir</button>
				<button id="v_modal_dialog_btn_agregar" type="button" class="btn btn-default" data-dismiss="modal">Agregar</button>
				<button id="v_modal_dialog_btn_cerrar" type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button id="v_modal_dialog_btn_si" type="button" class="btn btn-default" data-dismiss="modal">Si</button>
				<button id="v_modal_dialog_btn_no" type="button" class="btn btn-default" data-dismiss="modal">No</button>
				<button id="v_modal_dialog_btn_aceptar" type="button" class="btn btn-default" data-dismiss="modal">Aceptar</button>
				<button id="v_modal_dialog_btn_cancelar" type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->