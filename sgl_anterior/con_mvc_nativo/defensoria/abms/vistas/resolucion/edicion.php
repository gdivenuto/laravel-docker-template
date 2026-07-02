<?php
if (!isset($_SESSION))
	session_start();

class VistaResolucionEdicion extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'resolucion';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['numero'])) ? 'Edici&oacute;n' : 'Alta';
		$css_disabled = (isset($datos['numero'])) ? '' : 'text-light disabled';
		$operacion = (isset($datos['numero'])) ? 'modificar' : 'insertar';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1;

		$cant_remitentes = (isset($datos['remitentes'])) ? count($datos['remitentes']) : 0;
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
				<?php $this->mostrarContenidoHead();?>
			</head>
		  	<body>
			    <div class="container-fluid p-0 px-3">

					<?php $this->mostrarMenuPrincipal();?>

					<div class="row">
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							<?=$titulo_operacion;?> de la Resoluci&oacute;n
							<?= isset($datos['numero']) ? ' N&deg; ' . $datos['numero'] : ''; ?>
						</div>
					</div>
					<form 
						id="formEdicion" 
						name="formEdicion" 
						class="form-horizontal" 
						action="<?=URL_ABMS;?>" 
						method="POST"
					>
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="<?=$operacion;?>" />
						<input 
							type="hidden" 
							id="numero" 
							name="numero" 
							value="<?=(isset($datos['numero']) ? $datos['numero'] : '')?>"
						/>
						<input type="hidden" id="pagina" name="pagina" value="<?=$valor_pagina;?>" />
						
						<div class="row my-1">
							<div class="col-md-8 mx-auto">
								<div class="form-group row">
									<label 
										for="fecha" 
										class="col-sm-2 control-label small text-left text-sm-right pt-1">
										Fecha
									</label>
									<div class="col-sm-2">
										<input 
											id="fecha" 
											name="fecha" 
											class="form-control" 
											width="145" 
											value="<?=(isset($datos['fecha']) 
												? $this->formatearFecha($datos['fecha']) 
												: date("d/m/Y"));?>"
										/>
									</div>
								</div>
								<div class="form-group row">
									<label 
										for="remitente_id" 
										class="col-sm-2 control-label small text-left text-sm-right color_resaltado pt-1">
										* Remitente
									</label>
									<div class="col-sm-5">
										<select 
											name="remitente_id" 
											id="remitente_id" 
											class="form-control form-control-sm"
										>
											<option value="0">---</option>
											<?php for ($i = 0; $i < $cant_remitentes; $i++) {?>
												<option value="<?=$datos['remitentes'][$i]['id'];?>">
													<?=$datos['remitentes'][$i]['nombre'];?>
													<?=isset($datos['remitentes'][$i]['apellido']) 
														? '&nbsp;' . $datos['remitentes'][$i]['apellido'] 
														: '';?>
												</option>
											<?php }?>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<div class="input-group input-group-sm ml-1">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-clipboard"></i></span>
										</div>
										<textarea
											id="texto" 
											name="texto" 
											class="form-control" 
											rows="17" 
											placeholder="Ingrese aqu&iacute; el texto..." 
											aria-label="Texto"
										><?=($datos['texto'] != '') ? htmlspecialchars($datos['texto']) : '';?></textarea>
									</div>
								</div>
								<div class="row mt-3">
									<div class="col-sm-12 text-center">
										<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n">
											<i class="fas fa-check-circle"></i>&nbsp;Guardar
										</button>
										<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Cancelar operaci&oacute;n">
											<i class="fas fa-angle-left"></i>&nbsp;Cancelar
										</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script>
					$('#remitente_id').val('<?=(isset($datos['remitente_id'])) ? $datos['remitente_id'] : 0;?>');
				</script>

				<script src="<?=URL_JS.$this->controlador;?>/edicion.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>