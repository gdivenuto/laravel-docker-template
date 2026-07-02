<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaNotificacionesGruposEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'notificaciones_grupos';

		// Se crea una instancia del modelo
		$this->modelo = new notificacionesGruposModel();
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  array $listas        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $listas = null, $mensaje = '', $tipo_mensaje = '') {

		// Cantidad de Todas las listas
		$cantidad_listas = (isset($listas)) ? count($listas) : 0;
		// Listas que ya posee asignadas el Grupo
		$listas_asignadas = explode(',', $datos['phplist_ids']);

		//LibreriaGeneral::registrarLog("listas", $listas);
		//LibreriaGeneral::registrarLog("listas_asignadas", $listas_asignadas);
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
							<?= (isset($datos['id'])) ? 'Edici&oacute;n' : 'Alta'; ?> del Grupo
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" action="<?=URL_ABMS;?>" method="POST">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="<?= (isset($datos['id'])) ? 'modificar' : 'insertar'; ?>" />
						<input type="hidden" id="pagina" name="pagina" value="<?= (isset($datos['pagina'])) ? $datos['pagina'] : ''; ?>" />
						
						<div class="row my-1">
							<!-- Info -->
							<div class="col-md-8">
								<div class="form-group row mt-1">
									<label for="id" class="col-sm-2 control-label small text-right pt-1">
										C&oacute;digo
									</label>
									<div class="col-sm-2">
										<input  id="id" name="id" class="form-control form-control-sm"
												value="<?= (isset($datos['id'])) ? $datos['id'] : ''; ?>"
												onKeyPress="return soloEnteros(event)" maxlength="8" 
												<?= ($datos['id']) ? 'readonly' : ''; ?> />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="descripcion" class="col-sm-2 control-label small text-right pt-1">
										Descripci&oacute;n
									</label>
									<div class="col-sm-10">
										<input  type="text" id="descripcion" name="descripcion"
												value="<?=(isset($datos['descripcion'])) ? htmlspecialchars($datos['descripcion']) : ''; ?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label class="col-sm-2 control-label small text-right pt-1">Listas asignadas</label>
									<div class="col-sm-10 overflow-auto alto_300">
										<?php if ($cantidad_listas > 0) {
			?>
											<table class="table table-hover table-sm small">
												<tbody>
													<?php for ($i = 0; $i < $cantidad_listas; $i++) {
				$lista = &$listas[$i];?>
													<tr>
														<td width="20">
															<input  type="checkbox"
																	name="listas_asignadas[]"
																	class="listas_destino"
																	value="<?=$lista['id'];?>"
																	<?=(isset($datos['phplist_ids']) &&
					$datos['phplist_ids'] != null &&
					(in_array($lista['id'], $listas_asignadas) != false)) ? 'checked' : '';?> />
														</td>
														<td><?=$lista['name'];?></td>
													</tr>
													<?php }?>
												</tbody>
											</table>
										<?php }?>
									</div>
								</div>
							</div>
							<div class="col-md-4">
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

				<script src="<?=URL_JS;?>notificaciones_grupos/edicion.js"></script>
		  	</body>
		</html>
		<?php }
}
?>