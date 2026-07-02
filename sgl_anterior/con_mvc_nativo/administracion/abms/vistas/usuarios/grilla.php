<?php
if (!isset($_SESSION)) {
	session_start();
}

class VistaUsuariosGrilla extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'usuarios';
	}

	/**
	 * Se muestra la grilla
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 * @param  array $filtro       [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '', $filtro) {

		$cantidad = (isset($datos)) ? count($datos) : 0;

		// Se arma el criterio del buscador para la url del paginador
		$criterio_buscador = "&valor_buscado=" . $filtro['valor_buscado'];
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
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">Listado de Usuarios</div>
					</div>
					<!-- Buscador -->
					<div class="row mt-1">
						<form class="form-inline" action="" method="POST" name="formBuscadorGrilla">

							<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
							<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
					        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
							<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
							<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
							<input  type="hidden" id="pagina" name="pagina"
									value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

							<input type="text" id="valor_buscado" name="valor_buscado" value="<?=utf8_encode($filtro['valor_buscado']);?>" class="form-control form-control-sm mx-3 small" placeholder="Busque aqu&iacute;...">

							<button type="button" id="btBuscar" class="btn btn-info btn-sm mx-2" title="Buscar">
								<i class="fas fa-search"></i>&nbsp;Buscar
							</button>
							<button type="button" id="btLimpiar" class="btn btn-info btn-sm mx-2" title="Limpiar criterio de b&uacute;squeda">
								<i class="fas fa-eraser"></i>&nbsp;Limpiar
							</button>
							<button type="button" id="btNuevo" class="btn btn-success btn-sm mx-2" title="Nuevo Usuario">
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
												<th class="text-center">Usuario</th>
										      	<th class="text-center">Nombre Completo</th>
										      	<th class="text-center">Iniciales</th>
										      	<th class="text-center">Legajo</th>
										      	<th class="text-center">Mail</th>
										      	<th class="text-center">Observaciones</th>
												<th class="text-center">Habilitado</th>
											</tr>
										</thead>
										<tbody>
											<?php for ($i = 0; $i < $cantidad; $i++) {$dato = &$datos[$i];?>

												<tr <?=($dato['habilitado_usuario'] == '0') ? ' class="text-muted"' : '';?> >

											    	<td class="text-center" width="30">
											    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['id_usuario'];?>&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
											    			<i class="fas fa-edit"></i>
											    		</a>
											    	</td>
											    	<td class="text-center" width="30">
											    		<a href="javascript:if(confirm('¿Desea eliminar el Usuario?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['id_usuario'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
											    			<i class="fas fa-trash"></i>
											    		</a>
											    	</td>
													<td>
											        	<strong><?=$dato['codigo_usuario'];?></strong>
										        	</td>
													<td>
											        	<?=($dato['nombre_usuario']) ? $dato['nombre_usuario'] : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=($dato['iniciales_usuario']) ? $dato['iniciales_usuario'] : '&nbsp;';?>
											        </td>
											        <td class="text-right">
											        	<?=($dato['u_legajo']) ? $dato['u_legajo'] : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=($dato['u_mail']) ? $dato['u_mail'] : '&nbsp;';?>
											        </td>
											        <td>
											        	<?=($dato['observaciones_usuario']) ? $dato['observaciones_usuario'] : '&nbsp;';?>
											        </td>
											        <td class="text-center" width="40">
														<?php if ($dato['habilitado_usuario'] == '1') {?>
															<a title="Deshabilitar Usuario" href="javascript:if(confirm('¿Desea deshabilitar el Usuario?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['id_usuario'];?>&habilitado=<?=$dato['habilitado_usuario'];?>&pagina=<?=$filtro['pagina'];?>');};">
																<i class="fas fa-check"></i>
															</a>
														<?php } else {?>
															<a title="Habilitar Usuario" href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['id_usuario'];?>&habilitado=<?=$dato['habilitado_usuario'];?>&pagina=<?=$filtro['pagina'];?>');">
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

			    <script src="<?=URL_JS;?>usuarios/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>
