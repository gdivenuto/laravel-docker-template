<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaComisionesInternasEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'comisiones_internas';

		$this->modelo = new comisionesInternasModel();
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $mensaje = '', $tipo_mensaje = '') {

		$cant_comisiones_internas = (isset($datos['comisiones_internas'])) ? count($datos['comisiones_internas']) : 0;
		$cant_relatores = (isset($datos['relatores'])) ? count($datos['relatores']) : 0;
		$cant_concejales = (isset($datos['concejales'])) ? count($datos['concejales']) : 0;

		$titulo_operacion = (isset($datos['ci_codigo'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['ci_codigo'])) ? 'modificar' : 'insertar';
		$id_definido = (isset($datos['ci_codigo'])) ? $datos['ci_codigo'] : '';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1;
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
				<?php $this->mostrarContenidoHead();?>
			</head>
		  	<body>
			    <div class="container-fluid p-0 px-3">

					<?php $this->mostrarMenuPrincipal();?>

					<!-- Vista para el formulario -->
					<div class="row">
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							<?=$titulo_operacion;?> de la Comisi&oacute;n Interna
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" action="<?=URL_ABMS;?>" method="POST">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="<?=$operacion;?>" />
						<input type="hidden" id="pagina" name="pagina" value="<?=$valor_pagina;?>" />
						
						<div class="row my-1">
							<div class="col-md-7">
								<div class="form-group row mt-1">
									<label for="ci_codigo" class="col-sm-2 control-label small text-left text-sm-right pt-1">
										Comisi&oacute;n:
									</label>
									<div class="col-sm-10">
										<select id="ci_codigo" name="ci_codigo" class="form-control form-control-sm">
											<option value="0">---</option>
											<?php 
											for ($i=0; $i < $cant_comisiones_internas; $i++)
												echo '<option value="'.$datos['comisiones_internas'][$i]['codigo_grp'].'">'.$datos['comisiones_internas'][$i]['codigo_grp'].' - '.$datos['comisiones_internas'][$i]['descripcion_grp'].'</option>';
											?>
										</select>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="ci_dia" class="col-sm-2 control-label small text-left text-sm-right pt-1">D&iacute;a:</label>
									<div class="col-sm-10">
										<input  type="text" id="ci_dia" name="ci_dia" 
												value="<?=isset($datos['ci_dia']) ? $datos['ci_dia'] : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="ci_horario" class="col-sm-2 control-label small text-left text-sm-right pt-1">Horario:</label>
									<div class="col-sm-10">
										<input  type="text" id="ci_horario" name="ci_horario" 
												value="<?=isset($datos['ci_horario']) ? $datos['ci_horario'] : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="ci_relator" class="col-sm-2 control-label small text-left text-sm-right pt-1">
										Relator/a:
									</label>
									<div class="col-sm-10">
										<select id="ci_relator" name="ci_relator" class="form-control form-control-sm">
											<option value="0">---</option>
											<?php 
											for ($r=0; $r < $cant_relatores; $r++)
												echo '<option value="'.$datos['relatores'][$r]['p_legajo'].'">'.$datos['relatores'][$r]['p_apellido'].', '.$datos['relatores'][$r]['p_nombre'].'</option>';
											?>
										</select>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="ci_incumbencias" class="col-sm-2 control-label small text-left text-sm-right pt-1">
										Incumbencia:
									</label>
									<div class="col-sm-10">
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fas fa-clipboard"></i></span>
											</div>
											<textarea   id="ci_incumbencias" name="ci_incumbencias"
														class="form-control" rows="10"
														placeholder="Ingresa aqu&iacute; el texto de la Incumbencia..."
														aria-label="Texto"><?=(isset($datos['ci_incumbencias']) && $datos['ci_incumbencias'] != '') ? htmlspecialchars($datos['ci_incumbencias']) : '';?></textarea>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group row mt-1">
									<label class="col-sm-2 control-label small text-left text-sm-right pt-1">Integrantes:</label>
									<div class="col-sm-10 overflow-auto alto_430">
										<table class="table table-hover table-sm small">
											<thead class="thead-light">
												<tr>
													<th class="text-center" width="16">&nbsp;</th>
													<th class="text-center">Nombre</th>
													<th class="text-center">Presidente</th>
													<th class="text-center">Vicepresidente</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td width="16">&nbsp;</td>
													<td class="p-1">Sin autoridad asignada.</td>
													<td class="text-center">
														<input type="radio" name="cargo_presidente" value="" checked />
													</td>
													<td class="text-center">
														<input type="radio" name="cargo_vicepresidente" value="" checked />
													</td>
												</tr>
												<?php
												for ($i=0; $i < $cant_concejales; $i++) {
													$concejal = &$datos['concejales'][$i];

													$legajo 	 = $concejal['p_legajo'];
													$nombre_cjal = $concejal['p_apellido'].', '.ucwords(LibreriaGeneral::aMinusculas($concejal['p_nombre']));
													
													// Si se modifica la Comisión Interna
													if ( isset($datos['ci_codigo']) && $datos['ci_codigo'] != '' ) {
														$valor_chk_para_integrante 	   = ( $this->modelo->esIntegrante($legajo, $datos['ci_codigo']) == 1 ) ? 'checked' : '';
														$valor_chk_para_presidente 	   = ( $this->modelo->esPresidente($legajo, $datos['ci_codigo']) == 1 ) ? 'checked' : '';
														$valor_chk_para_vicepresidente = ( $this->modelo->esVicepresidente($legajo, $datos['ci_codigo']) == 1 ) ? 'checked' : '';

													// Si se ingresa una Nueva Comisión Interna
													} else {
														$valor_chk_para_integrante     = '';
														$valor_chk_para_presidente     = '';
														$valor_chk_para_vicepresidente = '';
													}
												?>
													<tr>
														<td width="16">
															<input type="checkbox" name="es_integrante[]" value="<?= $legajo; ?>" <?= $valor_chk_para_integrante; ?> />
														</td>
														<td class="p-1"><?= $nombre_cjal; ?></td>
														<td class="text-center">
															<input type="radio" name="cargo_presidente" value="<?= $legajo; ?>" <?= $valor_chk_para_presidente; ?> />
														</td>
														<td class="text-center">
															<input type="radio" name="cargo_vicepresidente" value="<?= $legajo; ?>" <?= $valor_chk_para_vicepresidente; ?> />
														</td>
													</tr>
												<?php
												}
												?>
											</tbody>
										</table>
									</div>
								</div>
								<!-- Botones Guardar y Cancelar -->
								<div class="row mt-3">
									<div class="col-sm-12 text-center">
										<!-- Botón Guardar -->
										<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n"><i class="fas fa-check-circle"></i>&nbsp;Guardar</button>
										<!-- Botón Cancelar -->
										<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Cancelar operaci&oacute;n"><i class="fas fa-angle-left"></i>&nbsp;Cancelar</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script>
					$('#ci_codigo').val('<?=(isset($datos['ci_codigo'])) ? $datos['ci_codigo'] : '0';?>');

					$('#ci_relator').val('<?=(isset($datos['ci_relator'])) ? $datos['ci_relator'] : '0';?>');
				</script>

				<script src="<?=URL_JS;?>comisiones_internas/edicion.js"></script>
		  	</body>
		</html>
		<?php }
}
?>
