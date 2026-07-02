<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaNotificacionesListasEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'notificaciones_listas';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $mensaje = '', $tipo_mensaje = '') {

		$cantidad_suscriptores = (isset($datos['suscriptores'])) ? count($datos['suscriptores']) : 0;
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
							<?=(isset($datos['id'])) ? 'Edici&oacute;n' : 'Alta';?> del Suscriptor
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" action="<?=URL_ABMS;?>" method="POST">

						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="<?=(isset($datos['id'])) ? 'modificar' : 'insertar';?>" />
						<input type="hidden" id="id" name="id" value="<?=(isset($datos['id'])) ? $datos['id'] : '';?>" />
						<?php // 16/11/2020 XXXX, por el momento TODAS son PRIVADAS (cero),
		// se quitó en el atributo value: echo (isset($datos['active'])) ? $datos['active'] : 0;?>
						<input type="hidden" id="active" name="active" value="0" />

						<input type="hidden" id="pagina" name="pagina" value="<?=(isset($datos['pagina'])) ? $datos['pagina'] : '';?>" />

						<div class="row mt-1">
							<div class="col-md-8">
								<div class="form-group row mt-3">
									<label for="name" class="col-sm-2 control-label small text-right pt-1">Nombre</label>
									<div class="col-sm-6">
										<input  type="text" id="name" name="name"
												value="<?=(isset($datos['name'])) ? htmlspecialchars($datos['name']) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="description" class="col-sm-2 control-label small text-right pt-1">Descripci&oacute;n</label>
									<div class="col-sm-6">
										<input  type="text" id="description" name="description"
												value="<?=(isset($datos['description'])) ? htmlspecialchars($datos['description']) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label class="col-sm-2 control-label small text-right pt-1">Suscriptores</label>
									<div class="col-sm-10 overflow-auto alto_300">
										<?php if ($cantidad_suscriptores > 0) {?>
											<table class="table table-hover table-sm small">
												<tbody>
													<?php for ($i = 0; $i < $cantidad_suscriptores; $i++) {$info_suscriptor = &$datos['suscriptores'][$i];?>
													<tr>
														<td class="text-center" width="30">
												    		<a href="javascript:if(confirm('¿Desea eliminar el Suscriptor de la Lista?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminarSuscriptorDeLista&id_lista=<?=$datos['id'];?>&id_suscriptor=<?=$info_suscriptor->userid;?>');};"
												    			title="Eliminar Suscriptor">
												    			<i class="fas fa-trash"></i>
												    		</a>
												    	</td>
														<td>
															<?=$info_suscriptor->email;?>
														</td>
													</tr>
													<?php }?>
												</tbody>
											</table>
										<?php } else {?>
											<div class="alert alert-info">No posee a&uacute;n.</div>
										<?php }?>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<!-- Botones -->
								<div class="row mt-3">
									<div class="col-sm-12 text-center">
										<!-- Botón Guardar -->
										<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n">
											<i class="fas fa-check-circle"></i>&nbsp;Guardar
										</button>
										<?php if (isset($datos['id'])) {?>
											<button type="button" id="btAgregarSuscriptores" class="btn btn-success btn-sm" title="Agregar Suscriptores">
												<i class="fas fa-plus"></i>&nbsp;Agregar Suscriptores
											</button>
										<?php }?>
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

				<script src="<?=URL_JS;?>notificaciones_listas/edicion.js?v=<?=date("Ymd_Hi");?>"></script>
			</body>
		</html>
		<?php }
}
?>
