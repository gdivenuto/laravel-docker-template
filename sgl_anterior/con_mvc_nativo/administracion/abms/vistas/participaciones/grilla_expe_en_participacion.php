<?php
if (!isset($_SESSION)) {
	session_start();
}

class VistaExpeEnParticipacionGrilla extends VistaBase {

	private $controlador;

	public function __construct() {
		parent::__construct();
		$this->controlador = 'participaciones';
	}

	/**
	 * Se muestra la grilla
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 * @param  array $filtro       [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '', $filtro = null) {

		$cantidad = (isset($datos)) ? count($datos) : 0;

		// Se arma el criterio del buscador para la url del paginador
		$criterio_buscador = "";
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
							Listado de Expedientes en Participaci&oacute;n
						</div>
					</div>
					
					<!-- Paginador -->
					<?php $this->mostrarPaginador($cantidad, $filtro, $criterio_buscador, $this->controlador);?>

					<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
					<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
					
					<!-- Grilla -->
					<div class="row mt-1">
						<div class="col-md-12">
							<?php if ($cantidad > 0) {?>
								<div class="table-responsive">
									<table class="table table-hover table-bordered table-sm small">
										<thead class="thead-light">
											<tr>
												<th>&nbsp;</th>
										      	<th>EXPEDIENTE</th>
										      	<th>FECHA</th>
												<th>TEXTO</th>
												<th>&nbsp;</th>
										    </tr>
										</thead>
										<tbody>
											<?php for ($i = 0; $i < $cantidad; $i++) {$dato = &$datos[$i];?>
												<tr>
													<td class="text-center" width="30">
											    		<form   id="form_ppc_deshabilitar_<?=$i;?>" 
											    				name="form_ppc_deshabilitar_<?=$i;?>"
											    				action="<?=URL_ABMS;?>?controlador=participaciones&accion=retirarExpeParticipaciones" method="post">

											    			<input type="hidden" name="anio" value="<?= $dato['anio']; ?>" />
											    			<input type="hidden" name="tipo" value="<?= $dato['tipo']; ?>" />
											    			<input type="hidden" name="numero" value="<?= $dato['numero']; ?>" />
											    			<input type="hidden" name="cuerpo" value="<?= $dato['cuerpo']; ?>" />
											    			<input type="hidden" name="alcance" value="<?= $dato['alcance']; ?>" />

												    		<a  href="javascript:if(confirm('¿Desea retirar al expediente <?= $dato['anio']."-".$dato['tipo']."-".$dato['numero']; ?> de las participaciones?')){ $('#form_ppc_deshabilitar_<?=$i;?>').submit();};"
												    			title="Retirar al expediente de las participaciones">
												    			<i class="fas fa-trash"></i>
												    		</a>
												    	</form>
											    	</td>
											        <td class="text-center" width="200">
											        	<?= $dato['anio'].' - '.$dato['tipo'].' - '.$dato['numero'].' - '.$dato['cuerpo'].' - '.$dato['alcance']; ?>
											        </td>
													<td class="text-center" width="80">
														<?= $this->formatearFecha($dato['fecha_inicio']); ?>
													</td>
													<td>
														<?= (isset($dato['extracto'])) ? $dato['extracto'] : '&nbsp;'; ?>
													</td>
													<td scope="row" class="text-center" width="185">
											    		<form   id="form_pepn" name="form_pepn"
											    				action="<?=URL_ABMS;?>?controlador=participaciones&accion=listarParticipaciones" method="post">

											    			<input type="hidden" name="anio" value="<?= $dato['anio']; ?>" />
											    			<input type="hidden" name="tipo" value="<?= $dato['tipo']; ?>" />
											    			<input type="hidden" name="numero" value="<?= $dato['numero']; ?>" />
											    			<input type="hidden" name="cuerpo" value="<?= $dato['cuerpo']; ?>" />
											    			<input type="hidden" name="alcance" value="<?= $dato['alcance']; ?>" />

												    		<button type="submit" name="enviar" class="btn btn-sm btn-info" title="Ver participaciones">
												    			<i class="fas fa-users"></i>&nbsp;Participaciones
												    		</button>
												    	</form>
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

			    <script src="<?=URL_JS;?>participaciones/grilla_expedientes_habilitados.js"></script>
		  	</body>
		</html>
		<?php }
}
?>