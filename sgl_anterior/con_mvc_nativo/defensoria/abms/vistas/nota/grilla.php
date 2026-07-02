<?php
if (!isset($_SESSION))
	session_start();

class VistaNotaGrilla extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'nota';
	}

	/**
	 * Se muestra la grilla
	 * @param  array $datos
	 * @param  string $mensaje
	 * @param  string $tipo_mensaje
	 * @param  array $filtro
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '', $filtro) {

		$cantidad = (isset($datos)) ? count($datos) : 0;
		
		$criterio_buscador  = "&f_numero=" . $filtro['f_numero'];
		$criterio_buscador .= "&f_fecha_desde=" . $filtro['f_fecha_desde'];
		$criterio_buscador .= "&f_fecha_hasta=" . $filtro['f_fecha_hasta'];
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
							Listado de Notas
						</div>
					</div>
					<form action="" method="POST" name="formBuscadorGrilla">

						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input type="hidden" id="pagina" name="pagina" value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

						<div class="row mt-1">
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<input 
									type="number" 
									name="f_numero" 
									id="f_numero"
									value="<?=($filtro['f_numero']) ? $filtro['f_numero'] : '';?>"
									class="form-control form-control-sm small"
									placeholder="N&uacute;mero">
							</div>
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<input  id="f_fecha_desde" name="f_fecha_desde"
										class="form-control form-control-sm small" width="130"
										value="<?=(isset($filtro['f_fecha_desde']) 
											? $this->formatearFecha($filtro['f_fecha_desde']) 
											: '');?>"
										placeholder="Desde" />
							</div>
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<input  id="f_fecha_hasta" name="f_fecha_hasta"
										class="form-control form-control-sm small" width="130"
										value="<?=(isset($filtro['f_fecha_hasta']) 
											? $this->formatearFecha($filtro['f_fecha_hasta']) 
											: '');?>"
										placeholder="Hasta" />
							</div>
							<div class="col-12 col-md-6 text-center">
								<button type="button" id="btBuscar" class="btn btn-info btn-sm" title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar" class="btn btn-info btn-sm" title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>
								<?php if ($_SESSION['perfil6'] != 3) { ?>
									<button type="button" id="btNuevo" class="btn btn-success btn-sm mt-1 mt-md-0" title="Nueva">
										<i class="fas fa-plus"></i>&nbsp;Nueva
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
												<th width="30">&nbsp;</th>
											<?php } ?>
												<th class="text-center">N&uacute;mero</th>
											    <th class="text-center">Fecha y Hora</th>
											    <th>Documento</th>
										    </tr>
										</thead>
										<tbody>
										<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos[$i]; ?>
											<tr>
												<?php if ( in_array($_SESSION['perfil6'], [1,2]) ) { ?>
											    	<td class="text-center" width="30">
											    		<a href="javascript:if(confirm('¿Desea eliminar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&numero=<?=$dato['numero'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
											    			<i class="fas fa-trash"></i>
											    		</a>
											    	</td>
												<?php }?>
												<td width="50" class="text-right">
										        	<?=$dato['numero'];?>
										        </td>
										        <td width="150" class="text-center">
										        	<?=(isset($dato['fecha']) 
										        		? $dato['fecha'] 
										        		: '&nbsp;');
										        	?>
										        </td>
										        <td>
													<a  href="<?=URL_DOCUMENTOS_NOTAS.$dato['documento']?>"
														target="_blank"
													><?= str_replace($dato['numero'].'_', '', $dato['documento']);?></a>
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

			    <script src="<?=URL_JS.$this->controlador;?>/grilla.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>