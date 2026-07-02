<?php
class VistaObservacionDpHabilitacion extends VistaBase {

	public function __construct() {
		parent::__construct();

		$this->controlador = 'defensoria_observacion';
	}

	/**
	 * Se renderiza el listado de documentos del DNI respectivo
	 * @param  integer $dni
	 */
	public function mostrar($datos, $pagina = null) {
	?>
		<form 
			id="formEdicion" 
			name="formEdicion" 
			class="form-horizontal" 
			action="<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado"
			method="POST">
			
			<input type="hidden" id="pagina" name="pagina" value="<?=(isset($pagina)) ? $pagina : '';?>" />
			<input type="hidden" id="observacion_id" name="observacion_id" value="<?=$datos['observacion_id'];?>" />

			<p>A continuaci&oacute;n puede habilitar o no la Observaci&oacute;n.<br>Puede especificar un motivo si lo desea.</p>
			<div class="form-group row mt-1">
				<label for="motivo" class="col-sm-2 control-label small text-right pt-1">
					Motivo
				</label>
				<div class="col-sm-10">
					<input  type="text" id="motivo" name="motivo" 
							value="<?=(isset($datos['motivo'])) ? htmlspecialchars($datos['motivo']) : '';?>" 
							class="form-control form-control-sm" >
				</div>
			</div>
			<div class="form-group row mt-1 mx-auto">
				<div class="form-check form-check-inline mx-auto">
					<input 
						class="form-check-input" 
						type="radio" name="habilitado" id="habilitado1" value="1"
						<?= ($datos['habilitado'] == '1' ? 'checked' : '');?>
					>
					<label class="form-check-label small" for="habilitado1">Habilitado</label>
				</div>
				<div class="form-check form-check-inline mx-auto">
					<input 
						class="form-check-input" 
						type="radio" name="habilitado" id="deshabilitado" value="0"
						<?= (($datos['habilitado'] == '0' || $datos['habilitado'] == '') ? 'checked' : '');?>
					>
					<label class="form-check-label small" for="deshabilitado">Deshabilitado</label>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-sm-12 text-center">
					<button 
						type="button" 
						onclick="javascript:$('#formEdicion').submit();" 
						class="btn btn-success btn-sm" 
						title="Guardar"
					>
						<i class="fas fa-check-circle"></i>&nbsp;Guardar
					</button>
				</div>
			</div>
		</form>
	<?php }
}