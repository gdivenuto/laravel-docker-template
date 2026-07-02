<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaNotificacionesSuscriptoresEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'notificaciones_suscriptores';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $mensaje = '', $tipo_mensaje = '') {
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
							<?=(isset($datos->id)) ? 'Edici&oacute;n' : 'Alta';?> del Suscriptor
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" action="<?=URL_ABMS;?>" method="POST">

						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="<?=(isset($datos->id)) ? 'modificar' : 'insertar';?>" />
						<input type="hidden" name="id" id="id" value="<?=(isset($datos->id)) ? $datos->id : '';?>" />

						<div class="row mt-1">
							<div class="col-md-8">
								<div class="form-group row mt-3">
									<label for="email" class="col-sm-3 control-label small text-right pt-1">Email</label>
									<div class="col-sm-6">
										<input  type="text" id="email" name="email"
												value="<?=(isset($datos->email)) ? htmlspecialchars($datos->email) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>

							</div>
							<div class="col-md-4">
								<!-- Botones Guardar y Cancelar -->
								<div class="row mt-3">
									<div class="col-sm-12 text-center">
										<!-- Botón Guardar -->
										<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n">
											<i class="fas fa-check-circle"></i>&nbsp;Guardar
										</button>
										<!-- Botón Cancelar -->
										<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Volver a la grilla de Suscriptores">
											<i class="fas fa-angle-left"></i>&nbsp;Cancelar
										</button>
									</div>
								</div>
							</div>
						</div>

					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script src="<?=URL_JS;?>notificaciones_suscriptores/edicion.js"></script>
			</body>
		</html>
		<?php }
}
?>