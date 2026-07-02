<?php
if (!isset($_SESSION)) {
	session_start();
}

class VistaEquiposHcdGrilla extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'equipos_hcd';

		// Se crea una instancia del modelo
		$this->modelo = new equiposHcdModel();
	
	}

	/**
	 * Se muestra la grilla
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 * @param  array $filtro       [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '', $filtro) {

		$cantidad = (isset($datos['listado'])) ? count($datos['listado']) : 0;
		$cant_areas = (isset($datos['areas'])) ? count($datos['areas']) : 0;
		$cant_responsables = (isset($datos['responsables'])) ? count($datos['responsables']) : 0;

		// Se arma el criterio del buscador para la url del paginador
		$criterio_buscador  = "&f_cod_area=".$filtro['f_cod_area'];
		$criterio_buscador .= "&f_cod_responsable=".$filtro['f_cod_responsable'];
		$criterio_buscador .= "&f_nombre_netbios=".$filtro['f_nombre_netbios'];
		$criterio_buscador .= "&f_direccion_mac=" . $filtro['f_direccion_mac'];
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
							Listado de Equipos en red HCD
						</div>
					</div>
					<!-- Buscador -->
					<form action="" method="POST" name="formBuscadorGrilla">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input type="hidden" id="pagina" name="pagina" value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

						<div class="form-row">
							<div class="col-12 col-md-2 mt-1">
								<select id="f_cod_area" name="f_cod_area" class="form-control form-control-sm">
									<option value="0">&Aacute;rea</option>
									<?php for ($i = 0; $i < $cant_areas; $i++) {?>
										<option value="<?=$datos['areas'][$i]['cod_area'];?>" >
											<?=$datos['areas'][$i]['nombre_area'];?>
										</option>
									<?php }?>
								</select>
							</div>
							<div class="col-12 col-md-2 mt-1">
								<select id="f_cod_responsable" name="f_cod_responsable" class="form-control form-control-sm">
									<option value="0">Responsable</option>
									<?php for ($i = 0; $i < $cant_responsables; $i++) {?>
										<option value="<?=$datos['responsables'][$i]['cod_responsable'];?>" >
											<?=$datos['responsables'][$i]['nombre_responsable'];?>
										</option>
									<?php }?>
								</select>
							</div>
							<div class="col-12 col-md-2 mt-1 text-center text-md-left">
								<input type="text" id="f_nombre_netbios" name="f_nombre_netbios" value="<?=utf8_encode($filtro['f_nombre_netbios']);?>" class="form-control form-control-sm small mx-3" placeholder="por Nombre">
							</div>
							<div class="col-12 col-md-2 mt-1 text-center text-md-left">
								<input type="text" id="f_direccion_mac" name="f_direccion_mac" value="<?=utf8_encode($filtro['f_direccion_mac']);?>" class="form-control form-control-sm small mx-3" placeholder="por MAC">
							</div>
							<div class="col-12 col-md-3 mt-1 text-center">
								<button type="button" id="btBuscar" class="btn btn-info btn-sm mx-2" title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar" class="btn btn-info btn-sm mx-2" title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>
								<button type="button" id="btNuevo" class="btn btn-success btn-sm mx-2" title="Nuevo Equipo">
									<i class="fas fa-plus"></i>&nbsp;Nuevo
								</button>
							</div>
						</div>
					</form>

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
												<th class="text-center">Nombre</th>
										      	<th class="text-center">MAC</th>
										      	<th class="text-center">IP</th>
										      	<th class="text-center">NameServer</th>
												<th class="text-center">WINS</th>
												<th class="text-center">Gateway</th>
										      	<th class="text-center">Nro. Inventario</th>
										      	<th class="text-center">F. Alta</th>
										      	<th class="text-center">F.Caducidad</th>
										      	<th class="text-center">Area</th>
										      	<th class="text-center">Responsable</th>
										      	<th class="text-center">Comentario</th>
										      	<th class="text-center">Habilitado</th>
											</tr>
										</thead>
										<tbody>
											<?php for ($i = 0; $i < $cantidad; $i++) {$dato = &$datos['listado'][$i];?>
												<tr <?=($dato['habilitado'] == '0') ? ' class="text-muted"' : '';?> >
											    	<td class="text-center" width="30">
											    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
											    			<i class="fas fa-edit"></i>
											    		</a>
											    	</td>
											    	<td class="text-center" width="30">
											    		<a href="javascript:if(confirm('¿Desea eliminar el Equipo?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
											    			<i class="fas fa-trash"></i>
											    		</a>
											    	</td>
													<td>
											        	<?=$dato['nombre_netbios'];?>
										        	</td>
													<td class="text-center" width="150">
											        	<?=($dato['direccion_mac']) ? $dato['direccion_mac'] : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=($dato['ip']) ? $dato['ip'] : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=($dato['nameserver']) ? $dato['nameserver'] : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=($dato['wins']) ? $dato['wins'] : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=($dato['gateway']) ? $dato['gateway'] : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=($dato['nro_inventario']) ? $dato['nro_inventario'] : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=($dato['fecha_alta']) ? $this->formatearFecha($dato['fecha_alta']) : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=($dato['fecha_caducidad']) ? $this->formatearFecha($dato['fecha_caducidad']) : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=($dato['cod_area']) ? $this->modelo->obtenerNombreArea($dato['cod_area']) : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=( isset($dato['cod_responsable']) ) ? $this->modelo->obtenerNombreResponsable($dato['cod_responsable']) : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=($dato['comentario']) ? $dato['comentario'] : '&nbsp;';?>
											        </td>
											        <td class="text-center" width="40">
														<?php if ($dato['habilitado'] == '1') {?>
															<a title="Deshabilitar Equipo" href="javascript:if(confirm('¿Desea deshabilitar el Equipo?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['id'];?>&habilitado=<?=$dato['habilitado'];?>&pagina=<?=$filtro['pagina'];?>');};">
																<i class="fas fa-check"></i>
															</a>
														<?php } else {?>
															<a title="Habilitar Equipo" href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['id'];?>&habilitado=<?=$dato['habilitado'];?>&pagina=<?=$filtro['pagina'];?>');">
																<i class="fas fa-times"></i>
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

				<script>
					jQuery(document).ready(function() {
						// Se setea el combo de Areas
						$('#f_cod_area').val('<?=($filtro['f_cod_area']) ? $filtro['f_cod_area'] : 0;?>');
						// Se setea el combo de Responsables
						$('#f_cod_responsable').val('<?=($filtro['f_cod_responsable']) ? $filtro['f_cod_responsable'] : 0;?>');
					});
				</script>

			    <script src="<?=URL_JS;?>equipos_hcd/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>
