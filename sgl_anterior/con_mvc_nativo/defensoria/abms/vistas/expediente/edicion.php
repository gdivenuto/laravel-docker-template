<?php
if (!isset($_SESSION))
	session_start();

class VistaExpedienteEdicion extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'expediente';
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
							<?=$titulo_operacion;?> del Expediente
							<?= isset($datos['numero']) ? ' N&deg; ' . $datos['numero'] : ''; ?>
						</div>
					</div>
					<ul class="nav nav-tabs mt-1">
						<li class="nav-item">
							<a class="nav-link small active" href="#">Ficha</a>
						</li>
						<li class="nav-item">
							<a  class="nav-link small <?=$css_disabled;?>"
								href="<?=URL_ABMS;?>?controlador=movimiento&accion=listar&numero=<?=$datos['numero'];?>">Movimientos</a>
						</li>
						<li class="nav-item ml-auto">
							<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n">
								<i class="fas fa-check-circle"></i>&nbsp;Guardar Info
							</button>
							<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Volver a Planes">
								<i class="fas fa-angle-left"></i>&nbsp;Volver
							</button>
						</li>
					</ul>

					<?=$this->mostrarFormularioInfo($datos);?>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script>
					$('#presentante_id').val('<?=(isset($datos['presentante_id'])) ? $datos['presentante_id'] : 0;?>');
					$('#tipo_proceso_id').val('<?=(isset($datos['tipo_proceso_id'])) ? $datos['tipo_proceso_id'] : 0;?>');
					$('#estado').val('<?=(isset($datos['estado'])) ? $datos['estado'] : "en trámite";?>');
				</script>

				<script src="<?=URL_JS.$this->controlador;?>/edicion.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
		<?php }

	/**
	 * Se muestra el Formulario para la Info
	 * @param  [type] $datos [description]
	 * @return [type]        [description]
	 */
	public function mostrarFormularioInfo($datos, $mensaje = '', $tipo_mensaje = '') {

		$operacion = (isset($datos['numero'])) ? 'modificar' : 'insertar';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1;

		$cant_presentadores = (isset($datos['presentadores'])) ? count($datos['presentadores']) : 0;
		$cant_tipos_proceso = (isset($datos['tipos_proceso'])) ? count($datos['tipos_proceso']) : 0;
		?>
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
							for="presentante_id" 
							class="col-sm-2 control-label small text-left text-sm-right color_resaltado pt-1">
							* Presentante
						</label>
						<div class="col-sm-5">
							<select 
								name="presentante_id" 
								id="presentante_id" 
								class="form-control form-control-sm"
							>
								<option value="0">---</option>
								<?php for ($i = 0; $i < $cant_presentadores; $i++) {?>
									<option value="<?=$datos['presentadores'][$i]['id'];?>">
										<?=$datos['presentadores'][$i]['nombre'];?>
										<?=isset($datos['presentadores'][$i]['apellido']) 
											? '&nbsp;' . $datos['presentadores'][$i]['apellido'] 
											: '';?>
									</option>
								<?php }?>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label 
							for="tipo_proceso_id" 
							class="col-sm-2 control-label small text-left text-sm-right color_resaltado pt-1">
							* Tipo de Proceso
						</label>
						<div class="col-sm-5">
							<select 
								name="tipo_proceso_id" 
								id="tipo_proceso_id" 
								class="form-control form-control-sm"
							>
								<option value="0">---</option>
								<?php for ($i = 0; $i < $cant_tipos_proceso; $i++) {?>
									<option value="<?=$datos['tipos_proceso'][$i]['id'];?>">
										<?=$datos['tipos_proceso'][$i]['nombre'];?>
									</option>
								<?php }?>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label 
							for="estado" 
							class="col-sm-2 control-label small text-left text-sm-right pt-1">Estado
						</label>
						<div class="col-sm-3">
							<select id="estado" name="estado" class="form-control form-control-sm">
								<option value="en trámite" >En tr&aacute;mite</option>
								<option value="archivado" >Archivado</option>
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
								rows="7" 
								placeholder="Ingrese aqu&iacute; el texto..." 
								aria-label="Texto"
							><?=($datos['texto'] != '') ? htmlspecialchars($datos['texto']) : '';?></textarea>
						</div>
					</div>
				</div>
			</div>
		</form>
	<?php }
}
?>