<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaNotificacionesSuscriptoresGrilla extends VistaBase {

	private $controlador;

	public function __construct() {
		parent::__construct();

		$this->controlador = 'notificaciones_suscriptores';
	}

	/**
	 * Se muestra la grilla
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 * @param  array $filtro       [description]
	 */
	public function mostrar($suscriptores, $mensaje, $tipo_mensaje, $criterio_a_buscar = '') {

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

					<!-- Vista para la grilla -->
					<div class="row">
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							Listado de Suscriptores
						</div>
					</div>
					<!-- Buscador -->
					<form action="" method="POST" name="formSuscriptor">

						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input type="hidden" id="pagina" name="pagina" value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

						<div class="row mt-1">
							<div class="col-12 col-md-9 mt-1 mt-md-0">
								<input  type="text" name="criterio_a_buscar" id="criterio_a_buscar"
										value="<?=(isset($criterio_a_buscar)) ? $criterio_a_buscar : '';?>"
										class="form-control form-control-sm w-100 small"
										placeholder="Buscar Suscriptor...">
							</div>

							<div class="col-12 col-md-3 mt-1 mt-md-0">
								<button type="button" id="btBuscar" class="btn btn-info btn-sm" title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar" class="btn btn-info btn-sm" title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>
								<button type="button" id="btNuevo" class="btn btn-success btn-sm mt-1 mt-md-0" title="Nuevo Suscriptor">
									<i class="fas fa-plus"></i>&nbsp;Nuevo
								</button>
							</div>
						</div>
					</form>
					<div class="row mt-1">
						<div class="col-12 overflow-auto alto_430">
							<?php if ($cantidad_suscriptores > 0) {?>
								<table class="table table-hover table-sm small">
									<tbody>
										<?php for ($i = 0; $i < $cantidad_suscriptores; $i++) {$info_suscriptor = &$suscriptores[$i];?>
										<tr>
											<td class="text-center" width="30">
									    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id_suscriptor=<?=$info_suscriptor->id;?>');" title="Editar Suscriptor">
									    			<i class="fas fa-edit"></i>
									    		</a>
									    	</td>
									    	<td class="text-center" width="30">
									    		<a href="javascript:if(confirm('¿Desea eliminar el Suscriptor?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id_suscriptor=<?=$info_suscriptor->id;?>');};"
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
								<div class="alert alert-info">No se han encontrado suscriptores.</div>
							<?php }?>
						</div>
					</div>
				</div>

				<?php $this->mostrarContenedorModal();?>

			    <script src="<?=URL_JS;?>notificaciones_suscriptores/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>