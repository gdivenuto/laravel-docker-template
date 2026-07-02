<?php
if (!isset($_SESSION)) {
	session_start();
}

class VistaComisionesInternasGrilla extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'comisiones_internas';

		$this->modelo = new comisionesInternasModel();
	
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
		$criterio_buscador = "&f_codigo=" . $filtro['f_codigo'];
		$criterio_buscador .= "&f_nombre=" . $filtro['f_nombre'];
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
							Comisiones Internas
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
						<input  type="hidden" id="estado_mantenimiento" name="estado_mantenimiento" 
								value="<?=$datos[0]['ci_en_mantenimiento'];?>">

						<div class="form-row">
							<div class="col-12 col-md-1 mt-1">
								<input  type="text" name="f_codigo" id="f_codigo"
										value="<?=($filtro['f_codigo']) ? $filtro['f_codigo'] : '';?>"
										class="form-control form-control-sm small"
										placeholder="C&oacute;digo">
							</div>
							<div class="col-12 col-md-4 mt-1">
								<input  type="text" name="f_nombre" id="f_nombre"
										value="<?=($filtro['f_nombre']) ? $filtro['f_nombre'] : '';?>"
										class="form-control form-control-sm small"
										placeholder="Nombre">
							</div>
							
							<div class="col-12 col-md-5 mt-1 text-center text-md-left">
								<button type="button" id="btBuscar" class="btn btn-info btn-sm" title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar" class="btn btn-info btn-sm" title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>

								<button type="button" id="btNuevo" class="btn btn-success btn-sm"
										title="Nueva">
									<i class="fas fa-plus"></i>&nbsp;Nueva
								</button>

								<?php if ($datos[0]['ci_en_mantenimiento'] == 0) { ?>
									<button type="button" id="btSetearMantenimiento" class="btn btn-warning btn-sm"
											title="Definir en Mantenimiento todas las Comisiones Internas">
										<i class="fas fa-wrench"></i>&nbsp;Definir en mantenimiento
									</button>
								<?php } else { ?>
									<button type="button" id="btSetearMantenimiento" class="btn btn-success btn-sm"
											title="Publicar a todas las Comisiones Internas">
										<i class="fas fa-check"></i>&nbsp;Publicar
									</button>
								<?php } ?>
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
										      	<th>C&oacute;digo</th>
										      	<th>Nombre</th>
												<th>Integrantes</th>
												<th>D&iacute;a</th>
												<th>Horario</th>
												<th>Relator/a</th>
												<th class="text-center">Habilitada</th>
											</tr>
										</thead>
										<tbody>
										<?php
										for ($i = 0; $i < $cantidad; $i++) { 
											$dato = &$datos[$i];

											$integrantes = $this->modelo->listarMiembrosComisionInterna($dato['ci_codigo']);
											$cant_integrantes = count($integrantes);

											$nombre_relator = ( $dato['ci_relator'] != 0 ) ? $this->modelo->obtenerNombrePersonal($dato['ci_relator']) : null;
										?>
											<tr <?=($dato['ci_habilitado'] == '0') ? ' class="text-muted"' : '';?> >

										    	<td class="text-center" width="30">
										    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['ci_codigo'];?>&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
										    			<i class="fas fa-edit"></i>
										    		</a>
										    	</td>

										    	<td class="text-center" width="30">
										    		<a href="javascript:if(confirm('¿Desea eliminar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['ci_codigo'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
										    			<i class="fas fa-trash"></i>
										    		</a>
										    	</td>

										        <td class="text-right">
										        	<?= ($dato['ci_codigo']) ? $dato['ci_codigo'] : '&nbsp;'; ?>
										        </td>

												<td <?=($dato['ci_habilitado'] == '1' && $dato['ci_en_mantenimiento'] == '1') ? ' class="text-danger"' : '';?>>
													<?= ($dato['ci_nombre']) ? $dato['ci_nombre'] : '&nbsp;'; ?>
													<?=($dato['ci_habilitado'] == '1' && $dato['ci_en_mantenimiento'] == '1') ? '&nbsp;<i class="fas fa-wrench" title="En Actualizaci&oacute;n"></i>' : '';?>
												</td>

												<td>
													<?php
													for ($m=0; $m < $cant_integrantes; $m++) {
														$miembro = &$integrantes[$m];

														echo LibreriaGeneral::aMayusculas($miembro['p_apellido']).', '.ucwords(LibreriaGeneral::aMinusculas($miembro['p_nombre']));

														// Si es Presidente/a
														if ($miembro['mci_cargo_comision'] == 1) {
															echo (isset($miembro['p_sexo']) && $miembro['p_sexo'] == 'M') ? '&nbsp;&nbsp;&nbsp;<strong>Presidente</strong>' : '&nbsp;&nbsp;&nbsp;<strong>Presidenta</strong>';
														}

														// Si es Vicepresidente/a
														if ($miembro['mci_cargo_comision'] == 2) {
															echo (isset($miembro['p_sexo']) && $miembro['p_sexo'] == 'M') ? '&nbsp;&nbsp;&nbsp;<strong>Vicepresidente</strong>' : '&nbsp;&nbsp;&nbsp;<strong>Vicepresidenta</strong>';
														}
														echo '<br>';
													}
													?>
												</td>
												<td>
													<?= ($dato['ci_dia']) ? $dato['ci_dia'] : '&nbsp;'; ?>
												</td>
												<td class="text-center">
													<?= ($dato['ci_horario']) ? $dato['ci_horario'] : '&nbsp;'; ?>
												</td>
												<td>
													<?= ($nombre_relator) ? $nombre_relator : '&nbsp;'; ?>
												</td>
												<td class="text-center" width="40">
													<?php if ($dato['ci_habilitado'] == '1') {?>
														<a title="Deshabilitar registro" href="javascript:if(confirm('¿Desea deshabilitar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['ci_codigo'];?>&habilitado=<?=$dato['ci_habilitado'];?>&pagina=<?=$filtro['pagina'];?>');};">
															<i class="fas fa-check"></i>
														</a>
													<?php } else {?>
														<a title="Habilitar registro" href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['ci_codigo'];?>&habilitado=<?=$dato['ci_habilitado'];?>&pagina=<?=$filtro['pagina'];?>');">
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

			    <script src="<?=URL_JS;?>comisiones_internas/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>
