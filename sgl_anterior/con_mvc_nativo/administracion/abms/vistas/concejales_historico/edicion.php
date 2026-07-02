<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaConcejalesHistoricoEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'concejales_historico';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['ch_id'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['ch_id'])) ? 'modificar' : 'insertar';
		$id_definido = (isset($datos['ch_id'])) ? $datos['ch_id'] : '';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : '';
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
							<?=$titulo_operacion;?> del Hist&oacute;rico de Concejales
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
						<input type="hidden" id="ch_id" name="ch_id" value="<?=$id_definido;?>" />

						<div class="row my-1">
							<div class="col-md-6">
								<div class="form-group row mt-1">
									<label for="ch_apellido_nombre" class="col-sm-4 control-label small text-right pt-1">
										Apellido y Nombres
									</label>
									<div class="col-sm-8">
										<input  type="text" id="ch_apellido_nombre" name="ch_apellido_nombre"
												value="<?=(isset($datos['ch_apellido_nombre'])) ? $this->reemplazarComillaDoble($datos['ch_apellido_nombre']) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="ch_bloque" class="col-sm-4 control-label small text-right pt-1">
										Bloque
									</label>
									<div class="col-sm-8">
										<input  type="text" id="ch_bloque" name="ch_bloque"
												value="<?=(isset($datos['ch_bloque'])) ? $this->reemplazarComillaDoble($datos['ch_bloque']) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								
								<div class="form-group row mt-1">
									<label for="ch_desde" class="col-sm-4 control-label small text-right pt-1">
										Desde
									</label>
									<div class="col-sm-2">
										<input  type="text" id="ch_desde" name="ch_desde"
												value="<?=(isset($datos['ch_desde'])) ? $this->reemplazarComillaDoble($datos['ch_desde']) : '';?>"
												class="form-control form-control-sm" >
									</div>
									<label for="ch_hasta" class="col-sm-4 control-label small text-right pt-1">
										Hasta
									</label>
									<div class="col-sm-2">
										<input  type="text" id="ch_hasta" name="ch_hasta"
												value="<?=(isset($datos['ch_hasta'])) ? $this->reemplazarComillaDoble($datos['ch_hasta']) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row">
									<div class="input-group input-group-sm ml-3">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-clipboard"></i></span>
										</div>
										<textarea id="ch_cargo" name="ch_cargo" class="form-control"
												  rows="4" placeholder="Ingrese aqu&iacute; el Cargo..."
												  aria-label="Texto"><?=($datos['ch_cargo'] != '') ? htmlspecialchars($datos['ch_cargo']) : '';?></textarea>
									</div>
								</div>
								
								<!-- Botones Guardar y Cancelar -->
								<div class="row mt-3">
									<div class="col-sm-12 text-center">
										<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n"><i class="fas fa-check-circle"></i>&nbsp;Guardar</button>
										<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Cancelar operaci&oacute;n"><i class="fas fa-angle-left"></i>&nbsp;Cancelar</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script src="<?=URL_JS;?>concejales_historico/edicion.js?v=<?=date("Ymd_His");?>"></script>

		  	</body>
		</html>
		<?php }
}
?>