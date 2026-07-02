<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaOpendataCatalogosEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'opendata_catalogos';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['id'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['id'])) ? 'modificar' : 'insertar';
		$id_definido = (isset($datos['id'])) ? $datos['id'] : '';
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
							<?=$titulo_operacion;?> del Cat&aacute;logo
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
						<input type="hidden" id="id" name="id" value="<?=$id_definido;?>" />
						
						<div class="row my-1">
							<div class="col-md-6">
								<div class="form-group row mt-1">
									<label for="titulo" class="col-sm-4 control-label small text-right pt-1">* T&iacute;tulo</label>
									<div class="col-sm-8">
										<input  type="text" id="titulo" name="titulo" 
												value="<?=(isset($datos['titulo'])) ? htmlspecialchars($datos['titulo']) : '';?>" 
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="descripcion" class="col-sm-4 control-label small text-right pt-1">
										Descripci&oacute;n
									</label>
									<div class="col-sm-8">
										<input  type="text" id="descripcion" name="descripcion" 
												value="<?=(isset($datos['descripcion'])) ? htmlspecialchars($datos['descripcion']) : '';?>" 
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fecha_emitido" class="col-sm-4 control-label small text-right pt-1">
										Fecha Emisi&oacute;n
									</label>
									<div class="col-sm-8">
										<input id="fecha_emitido" name="fecha_emitido" class="form-control" width="145" value="<?php echo (isset($datos['fecha_emitido'])) ? $this->formatearFecha($datos['fecha_emitido']) : date("d/m/Y"); ?>" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fecha_modificado" class="col-sm-4 control-label small text-right pt-1">
										Fecha Modificaci&oacute;n
									</label>
									<div class="col-sm-8">
										<input id="fecha_modificado" name="fecha_modificado" class="form-control" width="145" value="<?php echo (isset($datos['fecha_modificado'])) ? $this->formatearFecha($datos['fecha_modificado']) : ''; ?>" />
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

				<script src="<?=URL_JS;?>opendata_catalogos/edicion.js"></script>
		  	</body>
		</html>
		<?php }
}
?>