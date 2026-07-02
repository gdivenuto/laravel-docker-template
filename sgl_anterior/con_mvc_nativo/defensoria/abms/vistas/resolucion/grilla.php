<?php
if (!isset($_SESSION))
	session_start();

class VistaResolucionGrilla extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'resolucion';
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
		$cant_remitentes = (isset($datos['remitentes'])) ? count($datos['remitentes']) : 0;

		$criterio_buscador  = "&f_numero=" . $filtro['f_numero'];
		$criterio_buscador .= "&f_remitente=" . $filtro['f_remitente'];
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

					<div class="row">
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							Listado de Resoluciones
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
								<select id="f_remitente" name="f_remitente" class="form-control form-control-sm">
									<option value="0">Remitente</option>
									<?php for ($i = 0; $i < $cant_remitentes; $i++) {?>
										<option value="<?=$datos['remitentes'][$i]['id'];?>" >
											<?=$datos['remitentes'][$i]['nombre'];?>
											<?=(isset($datos['remitentes'][$i]['apellido']) 
												? $datos['remitentes'][$i]['apellido'] : '');?>
										</option>
									<?php }?>
								</select>
							</div>
							<div class="col-12 col-md-2 mt-1">
								<input  id="f_fecha" name="f_fecha"
										class="form-control form-control-sm small" width="130"
										value="<?=(isset($filtro['f_fecha']) 
											? $this->formatearFecha($filtro['f_fecha']) 
											: '');?>"
										placeholder="Fecha" />
							</div>
							<div class="col-12 col-md-4 text-center">
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
											    <th>Remitente</th>
											    <th class="text-center">Fecha</th>
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
										        	<?=(isset($dato['remitente_nombre']) 
										        		? $dato['remitente_nombre'] 
										        		: '&nbsp;');
										        	?>
										        	<?=(isset($dato['remitente_apellido']) 
										        		? '&nbsp;' . $dato['remitente_apellido'] 
										        		: '');
										        	?>
									        	</td>
										        <td class="text-center" width="80">
										        	<?=(isset($dato['fecha']) 
										        		? $this->formatearFecha($dato['fecha']) 
										        		: '&nbsp;');
										        	?>
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
						$('#f_remitente').val('<?=($filtro['f_remitente']) ? $filtro['f_remitente'] : 0;?>');
					});
				</script>

			    <script src="<?=URL_JS.$this->controlador;?>/grilla.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>