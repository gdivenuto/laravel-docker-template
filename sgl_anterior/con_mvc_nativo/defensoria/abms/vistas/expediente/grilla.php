<?php
if (!isset($_SESSION))
	session_start();

class VistaExpedienteGrilla extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'expediente';
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
		$cant_presentadores = (isset($datos['presentadores'])) ? count($datos['presentadores']) : 0;
		$cant_tipos_proceso = (isset($datos['tipos_proceso'])) ? count($datos['tipos_proceso']) : 0;

		$criterio_buscador  = "&f_numero=" . $filtro['f_numero'];
		$criterio_buscador .= "&f_presentacion=" . $filtro['f_presentacion'];
		$criterio_buscador .= "&f_tipo_proceso=" . $filtro['f_tipo_proceso'];
		$criterio_buscador .= "&f_fecha=" . $filtro['f_fecha'];
		$criterio_buscador .= "&f_estado=" . $filtro['f_estado'];
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
							Listado de Expedientes
						</div>
					</div>
					<form action="" method="POST" name="formBuscadorGrilla">

						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input type="hidden" id="pagina" name="pagina" value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

						<div class="row mt-1">
							<div class="col-12 col-md-2 mt-1">
								<input 
									type="number" 
									name="f_numero" 
									id="f_numero"
									value="<?=($filtro['f_numero']) ? $filtro['f_numero'] : '';?>"
									class="form-control form-control-sm small"
									placeholder="N&uacute;mero">
							</div>
							<div class="col-12 col-md-4 mt-1">
								<select id="f_presentante" name="f_presentante" class="form-control form-control-sm">
									<option value="0">Presentante</option>
									<?php for ($i = 0; $i < $cant_presentadores; $i++) {?>
										<option value="<?=$datos['presentadores'][$i]['id'];?>" >
											<?=$datos['presentadores'][$i]['nombre'];?>
											<?=(isset($datos['presentadores'][$i]['apellido']) 
												? $datos['presentadores'][$i]['apellido'] : '');?>
										</option>
									<?php }?>
								</select>
							</div>
							<div class="col-12 col-md-4 mt-1">
								<select id="f_tipo_proceso" name="f_tipo_proceso" class="form-control form-control-sm">
									<option value="0">Tipo de Proceso</option>
									<?php for ($i = 0; $i < $cant_tipos_proceso; $i++) {?>
										<option value="<?=$datos['tipos_proceso'][$i]['id'];?>" >
											<?=$datos['tipos_proceso'][$i]['nombre'];?>
										</option>
									<?php }?>
								</select>
							</div>
						</div>
						<div class="row mt-1">
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<input  id="f_fecha" name="f_fecha"
										class="form-control form-control-sm small" width="130"
										value="<?=(isset($filtro['f_fecha']) 
											? $this->formatearFecha($filtro['f_fecha']) 
											: '');?>"
										placeholder="Fecha" />
							</div>
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<select id="f_estado" name="f_estado" class="form-control form-control-sm">
									<option value="0">Tipo Proceso</option>
									<option value="en trámite" >En tr&aacute;mite</option>
									<option value="archivado" >Archivado</option>
								</select>
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
												<th class="text-center">N&uacute;mero</th>
											    <th>Presentante</th>
											    <th>Tipo de Proceso</th>
											    <th class="text-center">Fecha</th>
											    <th class="text-center">Estado</th>
											    <th class="text-center">Operado por</th>
										    </tr>
										</thead>
										<tbody>

										<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos['info'][$i]; ?>
										
											<tr <?=($dato['habilitado'] == '0') ? ' class="text-muted"' : '';?> >
												<?php if ( in_array($_SESSION['perfil6'], [1,2]) ) { ?>
											    	<td class="text-center" width="30">
											    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&numero=<?=$dato['numero'];?>&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
											    			<i class="fas fa-edit"></i>
											    		</a>
											    	</td>
											    	<td class="text-center" width="30">
											    		<a href="javascript:if(confirm('¿Desea eliminar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&numero=<?=$dato['numero'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
											    			<i class="fas fa-trash"></i>
											    		</a>
											    	</td>
												<?php }?>
												<td class="text-right" width="50">
										        	<?=$dato['numero'];?>
										        </td>
										        <td>
										        	<?=(isset($dato['presentador_nombre']) 
										        		? $dato['presentador_nombre'] 
										        		: '&nbsp;');
										        	?>
										        	<?=(isset($dato['presentador_apellido']) 
										        		? '&nbsp;' . $dato['presentador_apellido'] 
										        		: '');
										        	?>
									        	</td>
									        	<td>
										        	<?=(isset($dato['tipo_proceso_nombre']) 
										        		? $dato['tipo_proceso_nombre'] 
										        		: '&nbsp;');
										        	?>
									        	</td>
										        <td class="text-center" width="80">
										        	<?=(isset($dato['fecha']) 
										        		? $this->formatearFecha($dato['fecha']) 
										        		: '&nbsp;');
										        	?>
										        </td>
										        <td class="text-center" width="90">
										        	<?=($dato['estado']) ? $dato['estado'] : '&nbsp;';?>
										        </td>
										        <td class="text-center" width="120">
										        	<?=(isset($dato['codigo_usuario']) ? $dato['codigo_usuario'] : '&nbsp;');?>
										        </td>
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

				<script>
					jQuery(document).ready(function() {
						$('#f_presentacion').val('<?=($filtro['f_presentacion']) ? $filtro['f_presentacion'] : 0;?>');
						$('#f_tipo_proceso').val('<?=($filtro['f_tipo_proceso']) ? $filtro['f_tipo_proceso'] : 0;?>');
						$('#f_estado').val('<?=($filtro['f_estado']) ? $filtro['f_estado'] : 0;?>');
					});
				</script>

			    <script src="<?=URL_JS.$this->controlador;?>/grilla.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>