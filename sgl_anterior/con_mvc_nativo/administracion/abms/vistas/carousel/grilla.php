<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaCarouselGrilla extends VistaBase {

	private $controlador;

	public function __construct() {
		parent::__construct();
		$this->controlador = 'carousel';
	}

	/**
	 * Se muestra la grilla
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 * @param  array $filtro       [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '', $filtro) {

		$cantidad = (isset($datos['info'])) ? count($datos['info']) : 0;
		// Se arma el criterio del buscador para la url del paginador
		$criterio_buscador .= "&f_fecha=" . $this->formatearFecha($filtro['f_fecha']);
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
							Carousel del sitio web
						</div>
					</div>
					<!-- Buscador -->
					<div class="row mt-1">
						<form class="form-inline" action="" method="POST" name="formBuscadorGrilla">

							<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
					        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
							<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
							<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
							<input type="hidden" id="pagina" name="pagina" value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

							<label for="f_fecha" class="mx-3 small">Fecha</label>
							<input id="f_fecha" name="f_fecha" class="form-control form-control-sm small" width="130" />

							<button type="button" id="btBuscar" class="btn btn-info btn-sm mx-2" title="Buscar">
								<i class="fas fa-search"></i>&nbsp;Buscar
							</button>
							<button type="button" id="btLimpiar" class="btn btn-info btn-sm mx-2" title="Limpiar criterio de b&uacute;squeda">
								<i class="fas fa-eraser"></i>&nbsp;Limpiar
							</button>
							<button type="button" id="btNuevo" class="btn btn-success btn-sm mx-2" title="Nuevo">
								<i class="fas fa-plus"></i>&nbsp;Nuevo
							</button>
						</form>
					</div>

					<!-- Paginador -->
					<?php $this->mostrarPaginador($cantidad, $filtro, $criterio_buscador, $this->controlador);?>

					<!-- Grilla -->
					<div class="row mt-1">
						<div class="col-md-12">
						<?php if ($cantidad > 0) {?>
							<div class="table-responsive">
								<table class="table table-hover table-bordered table-sm small">
									<thead class="thead-light">
										<tr>
											<th width="60" colspan="2">&nbsp;</th>
									      	<th class="text-center">Nro</th>
									      	<th class="text-center">Fecha</th>
									      	<th class="text-center">Recurso</th>
									      	<th class="text-center">Muestra Actividades</th>
											<th>Enlace</th>
											<th class="text-center">Habilitado</th>
											<th class="text-center">Orden</th>
										</tr>
									</thead>
									<tbody>
									<?php for ($i = 0; $i < $cantidad; $i++) {$dato = &$datos['info'][$i];?>

										<?php $foto = (isset($dato['recurso']) && ($dato['recurso'] != '') && (LibreriaGeneral::existeFoto(RUTA_RECURSOS_CAROUSEL . $dato['recurso']))) ? $dato['recurso'] . '?v=' . date("Ymd_His") : 'no_disponible.jpg';?>

										<tr <?=($dato['habilitado'] == '0') ? ' class="text-muted"' : '';?> >
									    	<td class="text-center" width="30">
									    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
									    			<i class="fas fa-edit"></i>
									    		</a>
									    	</td>
									    	<td class="text-center" width="30">
									    		<a href="javascript:if(confirm('¿Desea eliminar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
									    			<i class="fas fa-trash"></i>
									    		</a>
									    	</td>
									        <td class="text-right" width="80">
									        	<?=$dato['id'];?>
									        </td>
									        <td class="text-center" width="110">
									        	<?=$this->formatearFecha($dato['fecha']);?>
									        </td>
									        <td class="text-center grilla_img_miniatura">
									        	<?php if (strpos($dato['enlace'], "www.youtube.com")) { ?>
									        		<i class="fab fa-youtube fa-3x"></i>
									        	<?php } else { ?>
													<img src="<?=URL_RECURSOS_CAROUSEL;?><?=$foto;?>"
														 alt="<?=$dato['recurso'];?>"
														 class="img-thumbnail d-none d-md-block">
												<?php } ?>
									        </td>
									        <td class="text-center" width="100">
												<?php if ($dato['es_actividad'] == '1') {?>
													<a  title="No mostrar Actividades" class="text-success" 
														href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstadoEsActividad&id=<?=$dato['id'];?>&es_actividad=<?=$dato['es_actividad'];?>&pagina=<?=$filtro['pagina'];?>');">
														<strong>Si</strong>
													</a>
												<?php } else {?>
													<a  title="Mostrar Actividades" class="text-danger" 
														href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstadoEsActividad&id=<?=$dato['id'];?>&es_actividad=<?=$dato['es_actividad'];?>&pagina=<?=$filtro['pagina'];?>');">
														<strong>No</strong>
													</a>
												<?php }?>
											</td>
											<td>
									        	<?=(isset($dato['enlace'])) ? $dato['enlace'] : 'No posee';?>
									        </td>
											<td class="text-center" width="40">
													<?php if ($dato['habilitado'] == '1') {?>
														<a  title="Deshabilitar registro" class="text-success" 
															href="javascript:if(confirm('¿Desea deshabilitar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['id'];?>&habilitado=<?=$dato['habilitado'];?>&pagina=<?=$filtro['pagina'];?>');};">
															<i class="fas fa-check"></i>
														</a>
													<?php } else {?>
														<a  title="Habilitar registro" 
															href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['id'];?>&habilitado=<?=$dato['habilitado'];?>&pagina=<?=$filtro['pagina'];?>');">
															<i class="fas fa-times"></i>
														</a>
													<?php }?>
											</td>
											<td class="text-center" width="60">
												<?php if ($i > 0 && $dato['habilitado']) {?>
													<a title="Subir prioridad" href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=subirPrioridad&id=<?=$dato['id'];?>&prioridad=<?=$dato['prioridad'];?>&pagina=<?=$filtro['pagina'];?>');">
														<i class="fas fa-chevron-up"></i>
													</a>
												<?php }?>
												&nbsp;
												<?php if ($i < ($cantidad - 1) && $dato['habilitado'] && isset($datos['info'][$i+1]) && $datos['info'][$i+1]['habilitado']) {?>
													<a title="Bajar prioridad" href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=bajarPrioridad&id=<?=$dato['id'];?>&prioridad=<?=$dato['prioridad'];?>&pagina=<?=$filtro['pagina'];?>');">
														<i class="fas fa-chevron-down"></i>
													</a>
												<?php }?>
											</td>
										</tr>
									<?php }?>
									</tbody>
								</table>
							</div>
						<?php } else {?>
							<div class="alert alert-info">No se han encontrado resultados.</div>
						<?php }?>
						</div>
					</div>
				</div>

				<?php $this->mostrarContenedorModal();?>

			    <script src="<?=URL_JS;?>carousel/grilla.js"></script>
		  	</body>
		</html>
		<?php }

}
?>