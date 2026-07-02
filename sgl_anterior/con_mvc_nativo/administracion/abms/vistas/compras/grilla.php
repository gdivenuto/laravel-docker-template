<?php
if (!isset($_SESSION)) {
	session_start();
}

class VistaComprasGrilla extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'compras';
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
		$criterio_buscador = "&f_anio=" . $filtro['f_anio'];
		$criterio_buscador .= "&f_concepto=" . $filtro['f_concepto'];
		$criterio_buscador .= "&f_fecha=" . $filtro['f_fecha'];
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
							&Oacute;rdenes de Compra
						</div>
					</div>
					<!-- Buscador -->
					<form action="" method="POST" name="formBuscadorGrilla">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input  type="hidden" id="pagina" name="pagina"
								value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

						<div class="form-row">
							<div class="col-12 col-md-1 mt-1">
								<select id="f_anio" name="f_anio" class="form-control form-control-sm">
									<?php for ($i=date("Y"); $i > 2006; $i--) { ?>	
										<option value="<?= $i; ?>"><?= $i; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-12 col-md-4 mt-1">
								<input  type="text" name="f_concepto" id="f_concepto"
										value="<?=($filtro['f_concepto']) ? $filtro['f_concepto'] : '';?>"
										class="form-control form-control-sm small"
										placeholder="Concepto o N&uacute;mero">
							</div>
							<div class="col-12 col-md-2 mt-1">
								<input  id="f_fecha" name="f_fecha"
										class="form-control form-control-sm small" width="130"
										value="<?=(isset($filtro['f_fecha'])) ? $this->formatearFecha($filtro['f_fecha']) : '';?>"
										placeholder="Fecha" />
							</div>
							<div class="col-12 col-md-3 mt-1 text-center text-md-left">
								<button type="button" id="btBuscar" class="btn btn-info btn-sm" title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar" class="btn btn-info btn-sm" title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>

								<?php if ($_SESSION['perfil'] != 3) { // Sólo los perfiles 1 y 2 pueden Ingresar un NUEVO registro ?>
								<button type="button" id="btNuevo" class="btn btn-success btn-sm"
										title="Nueva Secci&iocute;n">
									<i class="fas fa-plus"></i>&nbsp;Nueva
								</button>
								<?php }?>
							</div>
						</div>
					</form>

					<!-- Paginador -->
					<?php $this->mostrarPaginadorMostrandoActual($cantidad, $filtro, $criterio_buscador, $this->controlador);?>

					<!-- Grilla -->
					<div class="row mt-1">
						<div class="col-md-12">
							<?php if ($cantidad > 0) { ?>
								<div class="table-responsive">
									<table class="table table-hover table-bordered table-sm small">
										<thead class="thead-light">
											<tr>
												<th width="60" colspan="2">&nbsp;</th>
										      	<th>N&uacute;mero O.C.</th>
										      	<th class="text-center" width="80">Fecha</th>
										      	<th>Concepto</th>
												<th class="text-center">Proveedor</th>
												<th class="text-center">Monto</th>
												<th class="text-center">Habilitado</th>
											</tr>
										</thead>
										<tbody>
										<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos[$i];?>
											<tr <?=($dato['comp_habilitado'] == '0') ? ' class="text-muted"' : '';?> >

										    	<td class="text-center" width="30">
										    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['comp_id'];?>&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
										    			<i class="fas fa-edit"></i>
										    		</a>
										    	</td>

										    	<td class="text-center" width="30">
										    		<a href="javascript:if(confirm('¿Desea eliminar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['comp_id'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
										    			<i class="fas fa-trash"></i>
										    		</a>
										    	</td>

										        <td class="text-right">
										        	<?= ($dato['comp_codigo']) ? $dato['comp_codigo'] : '&nbsp;'; ?>
										        </td>

												<td class="text-center" width="80">
													<?=$this->formatearFecha($dato['comp_fecha']);?>
												</td>

												<td>
													<?= ($dato['comp_concepto']) ? $dato['comp_concepto'] : '&nbsp;'; ?>
												</td>

												<td>
													<?= ($dato['comp_proveedor']) ? $dato['comp_proveedor'] : '&nbsp'; ?>
												</td>

												<td class="text-right">
													<?= ($dato['comp_monto']) ? number_format($dato['comp_monto'], 2, ',', '.') : '&nbsp;'; ?>
												</td>

												<td class="text-center" width="40">
													<?php if ($dato['comp_habilitado'] == '1') {?>
														<a title="Deshabilitar registro" href="javascript:if(confirm('¿Desea deshabilitar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['comp_id'];?>&habilitado=<?=$dato['comp_habilitado'];?>&pagina=<?=$filtro['pagina'];?>');};">
															<i class="fas fa-check"></i>
														</a>
													<?php } else {?>
														<a title="Habilitar registro" href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['comp_id'];?>&habilitado=<?=$dato['comp_habilitado'];?>&pagina=<?=$filtro['pagina'];?>');">
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
					// Se setea en el combo
					$('#f_anio').val('<?=($filtro['f_anio']) ? $filtro['f_anio'] : date("Y");?>');
				</script>

			    <script src="<?=URL_JS;?>compras/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>