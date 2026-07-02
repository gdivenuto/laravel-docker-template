<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaComprasEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'compras';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['comp_id'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['comp_id'])) ? 'modificar' : 'insertar';
		$id_definido = (isset($datos['comp_id'])) ? $datos['comp_id'] : '';
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
							<?=$titulo_operacion;?> de la Orden de Compra
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
						<input type="hidden" id="comp_id" name="comp_id" value="<?=$id_definido;?>" />
						
						<div class="row my-1">
							<div class="col-md-6">
								<div class="form-group row mt-1">
									<label for="comp_codigo" class="col-sm-4 control-label small text-right pt-1">
										N&uacute;mero O. C.
									</label>
									<div class="col-sm-3">
										<input  type="text" id="comp_codigo" name="comp_codigo" 
												value="<?=(isset($datos['comp_codigo'])) ? htmlspecialchars($datos['comp_codigo']) : '';?>" 
												class="form-control form-control-sm"
												onkeypress="return soloEnteros(event)" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="comp_fecha" class="col-sm-4 control-label small text-right pt-1">
										Fecha
									</label>
									<div class="col-sm-8">
										<input id="comp_fecha" name="comp_fecha" class="form-control" width="145" value="<?= (isset($datos['comp_fecha'])) ? $this->formatearFecha($datos['comp_fecha']) : date("d/m/Y"); ?>" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="comp_concepto" class="col-sm-4 control-label small text-right pt-1">
										Concepto
									</label>
									<div class="col-sm-8">
										<input  type="text" id="comp_concepto" name="comp_concepto" 
												value="<?=(isset($datos['comp_concepto'])) ? htmlspecialchars($datos['comp_concepto']) : '';?>" 
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="comp_proveedor" class="col-sm-4 control-label small text-right pt-1">
										Proveedor
									</label>
									<div class="col-sm-8">
										<input  type="text" id="comp_proveedor" name="comp_proveedor" 
												value="<?=(isset($datos['comp_proveedor'])) ? htmlspecialchars($datos['comp_proveedor']) : '';?>" 
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="comp_monto" class="col-sm-4 control-label small text-right pt-1">
										Monto
									</label>
									<div class="col-sm-4">
										<div class="input-group">
	                                        <div class="input-group-prepend">
	                                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
	                                        </div>
											<input  type="text" id="comp_monto" name="comp_monto" 
													value="<?=(isset($datos['comp_monto'])) ? htmlspecialchars($datos['comp_monto']) : '';?>" 
													class="form-control form-control-sm"
													onkeypress="return soloEnterosyPunto(event)" 
													onchange="formatearMoneda(this);"
													aria-describedby="text_monto" >
										</div>
									</div>
									<small id="text_monto" class="form-text text-muted pl-3 pl-md-0">
	                                    (punto para decimal)
	                                </small>
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

				<script src="<?=URL_JS;?>compras/edicion.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>