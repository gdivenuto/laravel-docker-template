<?php
if (!isset($_SESSION))
	session_start();

class VistaOrdenComisionAgregarItem extends VistaBase {

	public function __construct() {

		parent::__construct();

		$this->controlador = 'ordenes_comision';

		$this->modelo = new ordenes_comisionModel();
	}

	/**
	 * Se muestra el formulario
	 * @param  array 	$info_orden
	 * @param  array 	$marca
	 * @param  array 	$expe_sin_marca
	 * @param  string 	$mensaje
	 * @param  string 	$tipo_mensaje
	 */
	public function mostrar($info_orden, $marca, $expe_sin_marca = null, $mensaje = '', $tipo_mensaje = '') {

		$cant_expedientes = (isset($expe_sin_marca)) ? count($expe_sin_marca) : 0;
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
				<?php $this->mostrarContenidoHead();?>
			</head>
		  	<body>
			    <div class="container-fluid p-0 px-3">

					<?php $this->mostrarMenuPrincipal();?>

					<div class="row mb-3">
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							Orden del d&iacute;a de Comisi&oacute;n :: Seleccione los &iacute;tems que desea <span class="text-info"><?=$this->mostrarNombreMarcaComision($marca);?></span>
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" action="<?=URL_ABMS;?>" method="POST">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="guardarItems" />
						<input type="hidden" id="id_orden_comision" name="id_orden_comision" value="<?=$info_orden['id'];?>" />
						<input type="hidden" id="marca_comision" name="marca_comision" value="<?=$marca;?>" />

						<?php if ($cant_expedientes > 0) {?>

							<div class="small pl-3">
								<input type="checkbox" id="check_todos" name="check_todos" value="1" />
								&nbsp;<strong>Selecionar todos</strong>
							</div>
							<?php
							// Para cada expediente "Sin marcar"
							for ($i=0; $i < $cant_expedientes; $i++) { $item = &$expe_sin_marca[$i];?>
								<div class="small pl-3">
									<input  type="checkbox" name="a_agregar[]" class="elegido" 
											value="<?=$item['anio'].'___'.$item['tipo'].'___'.$item['numero'];?>" />
									&nbsp;
									<strong>
										<?=$item['anio'];?>&nbsp;
										<?=$item['tipo'];?>&nbsp;
										<?=$item['numero'];?>&nbsp;
										<?=$item['iniciador_codigo'];?>&nbsp;
										<?=$item['caratula'];?>
									</strong>
									<?php
									// Si posee extracto el item
									if (isset($item['extracto'])) {
										echo '<p>'.$item['extracto'].'</p>';
									} else {
										// Sino se obtienen los Extractos de los proyectos del expediente respectivo
										$extractos = $this->modelo->obtenerExtractosPorExpediente($item['anio'], $item['tipo'], $item['numero']);
										$cant_extractos = (isset($extractos)) ? count($extractos) : 0;
										// Por cada Extracto del expediente
										for ($e=0; $e < $cant_extractos; $e++) echo '<p>'.$extractos[$e]['extracto'].'</p>';
									}?>
								</div>
							<?php }?>
							<div class="row my-3">
								<div class="col-sm-12 text-center">
									<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n"><i class="fas fa-check-circle"></i>&nbsp;Guardar</button>

									<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Cancelar operaci&oacute;n"><i class="fas fa-angle-left"></i>&nbsp;Cancelar</button>
								</div>
							</div>
						<?php } else {?>
							<div class="alert alert-info">No hay expedientes en la Comisi&oacute;n para agregar.</div>
							<div class="row my-3">
								<div class="col-sm-12 text-center">
									<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Volver">
										<i class="fas fa-angle-left"></i>&nbsp;Volver
									</button>
								</div>
							</div>
						<?php }?>
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script src="<?=URL_JS;?>ordenes_comision/agregar_item.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>