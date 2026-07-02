<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaNotificacionesListasEdicionSuscriptores extends VistaBase {

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
	public function mostrar($datos = null, $id_asignados = null, $suscriptores = null, $criterio_a_buscar = null, $mensaje = '', $tipo_mensaje = '') {

		//LibreriaGeneral::registrarLog("id_asignados", $id_asignados);

		$cantidad_suscriptores = (isset($suscriptores)) ? count($suscriptores) : 0;
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
							Asignaci&oacute;n de Suscriptores a la Lista <strong><?= $datos['name']; ?></strong>
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" action="<?=URL_ABMS;?>" method="POST">

						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="asignarSuscriptores" />

						<input type="hidden" id="id" name="id" value="<?=(isset($datos['id'])) ? $datos['id'] : '';?>" />

						<?php // 16/11/2020 XXXX, por el momento TODAS son PRIVADAS (cero) ?>
						<input type="hidden" id="active" name="active" value="0" />

						<input type="hidden" id="pagina" name="pagina" value="<?=(isset($datos['pagina'])) ? $datos['pagina'] : '';?>" />

						<div class="row mt-1">
							<div class="col-md-8">
								<div class="form-group row mt-3">
									<label for="nuevo_mail" class="col-sm-2 control-label small text-right pt-1">
										Nuevo Suscriptor
									</label>
									<div class="col-sm-7">
										<input  type="text" id="nuevo_mail" name="nuevo_mail"
												value=""
												class="form-control form-control-sm" >
									</div>
									<button type="button" id="btAgregarSuscriptor" class="btn btn-success btn-sm" title="Agregar Suscriptor">
										<i class="fas fa-plus"></i>&nbsp;Agregar Suscriptor
									</button>
								</div>
								<div class="form-group row mt-1">
									<label for="criterio_a_buscar" class="col-sm-2 control-label small text-right pt-1">
										Buscar Suscriptor
									</label>
									<div class="col-sm-7">
										<input  type="text" id="criterio_a_buscar" name="criterio_a_buscar"
												value="<?= (isset($criterio_a_buscar)) ? $criterio_a_buscar : ''; ?>"
												placeholder="Ingrese un mail o parte de &eacute;l, presione la tecla Enter o el bot&oacute;n Buscar"
												class="form-control form-control-sm" >
									</div>
									<button type="button" id="btBuscar"
											class="btn btn-info btn-sm"
											title="Buscar">
										<i class="fas fa-search"></i>&nbsp;Buscar
									</button>
									&nbsp;
									<button type="button" id="btLimpiar"
											class="btn btn-info btn-sm"
											title="Limpiar criterio de b&uacute;squeda">
										<i class="fas fa-eraser"></i>&nbsp;Limpiar
									</button>
								</div>
								<div class="form-group row mt-1">
									<label class="col-sm-2 control-label small text-right pt-1">Suscriptores</label>
									<div class="col-sm-10 overflow-auto alto_300">
										<?php if ($cantidad_suscriptores > 0) {?>
											<table class="table table-hover table-sm small">
												<tbody>
													<?php for ($i = 0; $i < $cantidad_suscriptores; $i++) {$info_suscriptor = &$suscriptores[$i];?>
													<tr>
														<td width="20">
															<input  type="checkbox"
																	name="suscriptor[]"
																	class="suscriptores"
																	value="<?=$info_suscriptor->id;?>"
																	<?=(isset($id_asignados) && $id_asignados != null &&
																		(in_array($info_suscriptor->id, $id_asignados) != false)) ? 'checked' : '';?> />
												    	</td>
														<td>
															<?=$info_suscriptor->email;?>
														</td>
													</tr>
													<?php }?>
												</tbody>
											</table>
										<?php } else {?>
											<div class="alert alert-info">No se han encontrado suscriptores.</div>
										<?php }?>
									</div>
								</div>
							</div>
						</div>

						<!-- Botones -->
						<div class="row mt-3">
							<div class="col-sm-12 text-center">
								<!-- Botón Guardar -->
								<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n">
									<i class="fas fa-check-circle"></i>&nbsp;Guardar Suscriptores
								</button>
								<!-- Botón Cancelar -->
								<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Volver a la edici&oacute;n de la Lista">
									<i class="fas fa-angle-left"></i>&nbsp;Volver
								</button>
							</div>
						</div>
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script src="<?=URL_JS;?>notificaciones_listas/edicion_suscriptores_lista.js?v=<?=date("Ymd_Hi");?>"></script>
			</body>
		</html>
		<?php }
}
?>
