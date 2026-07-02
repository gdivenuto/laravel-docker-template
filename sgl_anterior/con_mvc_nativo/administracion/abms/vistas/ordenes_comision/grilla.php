<?php
if (!isset($_SESSION))
	session_start();

class VistaOrdenComisionGrilla extends VistaBase {

	public function __construct() {
		
		parent::__construct();
		
		$this->controlador = 'ordenes_comision';

		$this->modelo = new ordenes_comisionModel();
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
		$cant_comisiones = (isset($datos['comisiones_internas'])) ? count($datos['comisiones_internas']) : 0;

		$criterio_buscador  = "&f_comision=" . $filtro['f_comision'];
		$criterio_buscador .= "&f_fecha=".$filtro['f_fecha'];
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
							Ordenes del D&iacute;a de Comisiones
						</div>
					</div>

					<form action="" method="POST" name="formBuscadorGrilla">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input  type="hidden" id="pagina" name="pagina" 
								value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

						<div class="form-row">
							
							<div class="col-12 col-md-5 mt-1">
								<select id="f_comision" name="f_comision" class="form-control form-control-sm">
									<option value="0">Comisi&oacute;n...</option>
									<?php for ($i = 0; $i < $cant_comisiones; $i++) {?>
										<option value="<?=$datos['comisiones_internas'][$i]['codigo_grp'];?>" >
											<?=$datos['comisiones_internas'][$i]['descripcion_grp'];?>
										</option>
									<?php }?>
								</select>
							</div>
							<div class="col-12 col-md-2 mt-1">
								<input  id="f_fecha" name="f_fecha"
										class="form-control form-control-sm small" width="130"
										value="<?= (isset($filtro['f_fecha'])) ? $this->formatearFecha($filtro['f_fecha']) : ''; ?>"
										placeholder="Fecha" />
							</div>
							<div class="col-12 col-md-4 mt-1 text-center text-md-left">
								<button type="button" id="btBuscar" class="btn btn-info btn-sm" title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar" class="btn btn-info btn-sm" title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>

								<button type="button" id="btNuevo" class="btn btn-success btn-sm" 
										title="Nueva Orden de Comisi&oacute;n">
									<i class="fas fa-plus"></i>&nbsp;Nueva
								</button>
							</div>
						</div>
					</form>

					<?php $this->mostrarPaginador($cantidad, $filtro, $criterio_buscador, $this->controlador);?>

					<div class="row mt-1">
						<div class="col-md-12">
							<?php if ($cantidad > 0) {?>
								<div class="table-responsive">
									<table class="table table-hover table-bordered table-sm small">
										<thead class="thead-light">
											<tr>
												<th width="60" colspan="2">&nbsp;</th>
										      	<th>Comisi&oacute;n</th>
										      	<th class="text-center">Fecha</th>
												<th class="text-center">Hora</th>
												<th class="text-center">Publicada</th>
												<th colspan="2">&nbsp;</th>
											</tr>
										</thead>
										<tbody>
										<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos['info'][$i]; ?>
											<tr>
										    	<td class="text-center" width="30">
										    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
										    			<i class="fas fa-edit"></i>
										    		</a>
										    	</td>
										    	<td class="text-center" width="30">
										    		<?php if ($dato['publicada']) {
										    			echo '---';
										    		} else { ?>
											    		<a href="javascript:if(confirm('¿Desea eliminar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
											    			<i class="fas fa-trash"></i>
											    		</a>
											    	<?php } ?>
										    	</td>
												<td>
													<?= $dato['asunto']; ?>
												</td>
												<td class="text-center" width="80">
													<?= $this->formatearFecha($dato['fecha']); ?>
												</td>
												<td class="text-center" width="80">
													<?= $dato['hora']; ?>
												</td>
												<td class="text-center" width="40">
													<?= ($dato['publicada'] == '1') 
														? '<i class="fas fa-check text-success"></i>' 
														: '<i class="fas fa-times"></i>';
													?>
												</td>
												<td class="text-center" width="30">
													<a  href="<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=crearFormatoPdf&id=<?=$dato['id'];?>" 
														target="_blank" 
														title="Generar Orden en PDF"
													>
														<i class="far fa-file-pdf"></i>
													</a>
												</td>
												<td class="text-center" width="30">
													<a  href="<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=crearFormatoHTML&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>" 
														title="Ver Orden para Publicar en el sitio web">
														<i class="fas fa-cloud-upload-alt"></i>
													</a>
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

				<script>
					jQuery(document).ready(function() {
						$('#f_comision').val('<?=($filtro['f_comision']) ? $filtro['f_comision'] : 0;?>');
					});
				</script>

			    <script src="<?=URL_JS.$this->controlador;?>/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>
