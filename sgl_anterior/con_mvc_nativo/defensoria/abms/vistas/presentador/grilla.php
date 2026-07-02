<?php
if (!isset($_SESSION))
	session_start();

class VistaPresentadorGrilla extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'presentador';
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

					<div class="row">
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							Listado de Presentantes
						</div>
					</div>
					<form action="" method="POST" name="formBuscadorGrilla">

						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input type="hidden" id="pagina" name="pagina" value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

						<div class="row mt-1">
							<div class="col-12 col-md-6">
								<div class="form-group row">
									<input 
										type="text" 
										name="valor_buscado" 
										id="valor_buscado" 
										value="<?=mb_convert_encoding($filtro['valor_buscado'], 'UTF-8');?>"
										class="form-control form-control-sm mx-3 small" 
										placeholder="Busque aqu&iacute;..."
									/>
								</div>
							</div>
							<div class="col-12 col-md-6 text-center">
								<button type="button" id="btBuscar" class="btn btn-info btn-sm" title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar" class="btn btn-info btn-sm" title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>
								<?php if ($_SESSION['perfil6'] != 3) { ?>
									<button type="button" id="btNuevo" class="btn btn-success btn-sm mt-1 mt-md-0" title="Nuevo">
										<i class="fas fa-plus"></i>&nbsp;Nuevo
									</button>
								<?php }?>
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
												<?php if ( in_array($_SESSION['perfil6'], [1,2]) ) { ?>
													<th width="60" colspan="2">&nbsp;</th>
												<?php } ?>
													<th class="text-center">Nombre</th>
											      	<th class="text-right">DNI</th>
											      	<th class="text-left">Localidad</th>
											      	<th class="text-center">Cod. Postal</th>
											      	<th class="text-left">Direcci&oacute;n</th>
											      	<th class="text-center">Tel&eacute;fono</th>
											      	<th class="text-center">M&oacute;vil</th>
											      	<th class="text-center">Mail</th>
												<?php if ( in_array($_SESSION['perfil6'], [1,2]) ) { ?>
													<th class="text-center">Habilitado</th>
												<?php } ?>
										    </tr>
										</thead>
										<tbody>
										<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos['info'][$i]; ?>
											<tr <?=($dato['habilitado'] == '0') ? ' class="text-muted"' : '';?> >
												<?php if ( in_array($_SESSION['perfil6'], [1,2]) ) { ?>
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
												<?php }?>
												<td>
										        	<?=($dato['nombre']) ? $dato['nombre'] . ' ' . $dato['apellido'] : '&nbsp;';?>
										        </td>
										        <td class="text-right">
										        	<?=($dato['dni']) ? $dato['dni'] : '&nbsp;';?>
										        </td>
										        <td>
										        	<?=(isset($dato['localidad'])) ? $dato['localidad'] : '&nbsp;';?>
									        	</td>
									        	<td class="text-center">
										        	<?=(isset($dato['codigo_postal'])) ? $dato['codigo_postal'] : '&nbsp;';?>
									        	</td>
										        <td>
										        	<?=(isset($dato['direccion_calle'])) ? $dato['direccion_calle'] . ' ' . $dato['direccion_numero'] : '&nbsp;';?>
										        	<?=(isset($dato['direccion_piso'])) ? $dato['direccion_piso'] . ' ' . $dato['direccion_departamento'] : '&nbsp;';?>
									        	</td>
										        <td class="text-right" width="110">
										        	<?=(isset($dato['tel_fijo_cod_area'])) ? $dato['tel_fijo_cod_area'] . ' ' . $dato['tel_fijo_numero'] : '&nbsp;';?>
										        </td>
										        <td class="text-right" width="110">
										        	<?=(isset($dato['movil_cod_area'])) ? $dato['movil_cod_area'] . ' ' . $dato['movil_numero'] : '&nbsp;';?>
										        </td>
										        <td width="110">
										        	<?=($dato['mail']) ? $dato['mail'] : '&nbsp;';?>
										        </td>
										    	<?php if ( in_array($_SESSION['perfil6'], [1,2]) ) { ?>
													<td class="text-center" width="40">
														<?php if ($dato['habilitado'] == '1') {?>
															<a title="Deshabilitar Presentante" href="javascript:if(confirm('¿Desea deshabilitar el Presentante?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['id'];?>&habilitado=<?=$dato['habilitado'];?>&pagina=<?=$filtro['pagina'];?>');};">
																<i class="fas fa-check"></i>
															</a>
														<?php } else {?>
															<a title="Habilitar Presentante" href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['id'];?>&habilitado=<?=$dato['habilitado'];?>&pagina=<?=$filtro['pagina'];?>');">
																<i class="fas fa-times"></i>
															</a>
														<?php } ?>
													</td>
												<?php } ?>
										    </tr>
										<?php } ?>
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

			    <script src="<?=URL_JS.$this->controlador;?>/grilla.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>