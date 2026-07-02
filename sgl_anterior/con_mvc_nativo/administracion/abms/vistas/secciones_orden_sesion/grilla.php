<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaSeccionesOrdenSesionGrilla extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'secciones_orden_sesion';
	}

	/**
	 * Se muestra la grilla
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 * @param  array $filtro       [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '', $filtro) {

		$cant_secciones_padre = (isset($datos['secciones_padre'])) ? count($datos['secciones_padre']) : 0;
		$cantidad = (isset($datos['info'])) ? count($datos['info']) : 0;

		// Se arma el criterio del buscador para la url del paginador
		$criterio_buscador  = "&f_codigo=" . $_SESSION['filtro_od_sesion_seccion']['f_codigo'];
		$criterio_buscador .= "&f_nombre=".$_SESSION['filtro_od_sesion_seccion']['f_nombre'];
		$criterio_buscador .= "&f_seccion_padre=" . $_SESSION['filtro_od_sesion_seccion']['f_seccion_padre'];
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
							Secciones para Orden del D&iacute;a de Sesi&oacute;n
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
							<div class="col-12 col-md-2 mt-1">
								<input type="text" name="f_codigo" id="f_codigo"
										value="<?= ($_SESSION['filtro_od_sesion_seccion']['f_codigo']) ? $_SESSION['filtro_od_sesion_seccion']['f_codigo'] : ''; ?>" 
										class="form-control form-control-sm small"
										placeholder="C&oacute;digo"  
										onKeyPress="return soloEnteros(event)">
							</div>
							<div class="col-12 col-md-2 mt-1">
								<input  type="text" name="f_nombre" id="f_nombre" 
										value="<?= ($_SESSION['filtro_od_sesion_seccion']['f_nombre']) ? $_SESSION['filtro_od_sesion_seccion']['f_nombre'] : '';?>" 
										class="form-control form-control-sm small" 
										placeholder="Nombre" 
										onKeyPress="return soloLetras(event)">
							</div>
							<div class="col-12 col-md-5 mt-1">
								<select id="f_seccion_padre" name="f_seccion_padre" 
										class="form-control form-control-sm">
									<option value="0">Depende de...</option>
									<?php for ($s = 0; $s < $cant_secciones_padre; $s++) {?>
										<option value="<?=$datos['secciones_padre'][$s]['codigo'];?>" >
											<?=$datos['secciones_padre'][$s]['nombre'];?>
										</option>
									<?php }?>
								</select>
							</div>
							<div class="col-12 col-md-3 mt-1 text-center text-md-left">
								<button type="button" id="btBuscar" class="btn btn-info btn-sm" title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar" class="btn btn-info btn-sm" title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>
								<button type="button" id="btNuevo" class="btn btn-success btn-sm" 
										title="Nueva Secci&iocute;n">
									<i class="fas fa-plus"></i>&nbsp;Nueva
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
										      	<th>C&oacute;digo</th>
										      	<th>Nombre</th>
										      	<th class="text-center">Con salto de P&aacute;gina</th>
												<th class="text-center">Iniciador</th>
												<th class="text-center">Autor</th>
												<th class="text-center">Car&aacute;tula en Expedientes</th>
												<th class="text-center">Car&aacute;tula en Notas</th>
												<th class="text-center">Comisiones</th>
												<th class="text-center">Habilitado</th>
											</tr>
										</thead>
										<tbody>
										<?php for ($i = 0; $i < $cantidad; $i++) {$dato = &$datos['info'][$i];?>
											<tr <?=($dato['habilitado'] == '0') ? ' class="text-muted"' : '';?> >
										
										    	<td class="text-center" width="30">
										    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['codigo'];?>&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
										    			<i class="fas fa-edit"></i>
										    		</a>
										    	</td>
										
										    	<td class="text-center" width="30">
										    		<a href="javascript:if(confirm('¿Desea eliminar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['codigo'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
										    			<i class="fas fa-trash"></i>
										    		</a>
										    	</td>
										
										        <td><?=$dato['codigo'];?></td>

												<?php
												// SEGUNDO PAR DE DIGITOS DEL CODIGO DE LA SECCION
												$segundo_par_digitos = substr($dato['codigo'], 2, 2);

												// TERCER PAR DE DIGITOS DEL CODIGO DE LA SECCION
												$tercer_par_digitos = substr($dato['codigo'], 4, 2);

												// CUARTO PAR DE DIGITOS DEL CODIGO DE LA SECCION
												$cuarto_par_digitos = substr($dato['codigo'], 6, 2);

												if ($segundo_par_digitos != '00') {
													$padding_css = 'padding-left:30px;';
												} elseif ($tercer_par_digitos != '00') {
													$padding_css = 'padding-left:50px;';
												} elseif ($cuarto_par_digitos != '00') {
													$padding_css = 'padding-left:70px;';
												} else {
													$padding_css = 'padding-left:10px;';
												}?>
												<td style="<?= $padding_css; ?>">
													<?=$dato['nombre'];?>
												</td>

												<td class="text-center" width="40">
													<?= ($dato['mostrar_con_salto_pagina'] == '1') ? '<i class="fas fa-check"></i>' : '&nbsp;';?>
												</td>
												<td class="text-center" width="40">
													<?= ($dato['mostrar_iniciador'] == '1') ? '<i class="fas fa-check"></i>' : '&nbsp;';?>
												</td>
												<td class="text-center" width="40">
													<?= ($dato['mostrar_autor'] == '1') ? '<i class="fas fa-check"></i>' : '&nbsp;';?>
												</td>
												<td class="text-center" width="40">
													<?= ($dato['mostrar_caratula_en_exped'] == '1') ? '<i class="fas fa-check"></i>' : '&nbsp;';?>
												</td>
												<td class="text-center" width="40">
													<?= ($dato['mostrar_caratula_en_nota'] == '1') ? '<i class="fas fa-check"></i>' : '&nbsp;';?>
												</td>
												<td class="text-center" width="40">
													<?= ($dato['mostrar_comisiones'] == '1') ? '<i class="fas fa-check"></i>' : '&nbsp;';?>
												</td>

												<td class="text-center" width="40">
													<?php if ($dato['habilitado'] == '1') {?>
														<a title="Deshabilitar Secci&oacute;n" href="javascript:if(confirm('¿Desea deshabilitar la Seccion?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['codigo'];?>&habilitado=<?=$dato['habilitado'];?>&pagina=<?=$filtro['pagina'];?>');};">
															<i class="fas fa-check"></i>
														</a>
													<?php } else {?>
														<a title="Habilitar Secci&oacute;n" href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['codigo'];?>&habilitado=<?=$dato['habilitado'];?>&pagina=<?=$filtro['pagina'];?>');">
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
					$('#f_seccion_padre').val('<?=($_SESSION['filtro_od_sesion_seccion']['f_seccion_padre']) ? $_SESSION['filtro_od_sesion_seccion']['f_seccion_padre'] : 0;?>');
				</script>

			    <script src="<?=URL_JS;?>secciones_orden_sesion/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>
