<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaNotificacionesSuscriptores extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'notificaciones';
	}

	/**
	 * Se muestran los Suscriptores
	 * @param  array $listado
	 * @param  integer $pagina
	 */
	public function mostrar($listado = null, $pagina = '') {

		$cantidad = (isset($listado)) ? count($listado) : 0;
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
				<?php $this->mostrarContenidoHead();?>
			</head>
		  	<body>
			    <div class="container-fluid p-0 px-3">

					<?php $this->mostrarMenuPrincipal();?>

					<!-- Vista para el formulario -->
					<div class="row">
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							Listado de Suscriptores
						</div>
					</div>
					<div class="row mt-1">
						<button type="button" id="btVolver" class="btn btn-info btn-sm ml-auto mr-3" title="Volver al listado">
							<i class="fas fa-angle-left"></i>&nbsp;Volver al listado
						</button>
					</div>
					<div class="row mt-1">
						<div class="col-md-12">
							<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
							<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
							<input type="hidden" id="pagina" name="pagina" value="<?=(isset($pagina)) ? $pagina : '';?>">

							<div class="table-responsive">
								<table class="table table-hover table-bordered table-sm small">
									<thead class="thead-light">
										<tr>
											<th class="text-center">Fecha</th>
											<th class="text-center">Estado</th>
											<th>Mail</th>
										</tr>
									</thead>
									<tbody>
										<?php for ($i = 0; $i < $cantidad; $i++) {$suscriptor = &$listado[$i];?>
											<tr>
												<td class="text-center" width="90">
													<?=$this->mostrarFechaSuscripcion($suscriptor);?>
												</td>

												<?=$this->setearEstadoVisualizacion($suscriptor->status);?>

												<td>
													<?=$suscriptor->email;?>
												</td>
											</tr>
										<?php }?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

			    <script src="<?=URL_JS;?>notificaciones/suscriptores.js"></script>
		  	</body>
		</html>
		<?php }

	/**
	 * Se setea el Estado de la Visualizacion respectiva
	 * @param  [string] $estado viewed | not_viewed
	 * @return [string]         elemento HTML
	 */
	private function mostrarFechaSuscripcion($suscriptor) {

		if (isset($suscriptor->last_view)) {
			$aux = $suscriptor->last_view;
		} elseif (isset($suscriptor->last_bounce)) {
			$aux = $suscriptor->last_bounce;
		} else {
			$aux = '';
		}

		if ($aux != '') {
			$separacion = explode(' ', $aux);

			$fecha = $separacion[0];

			return $this->formatearFecha($fecha);
		} else {
			return '&nbsp;';
		}
	}

	/**
	 * Se setea el Estado de la Visualizacion respectiva
	 * @param  [string] $estado viewed | not_viewed
	 * @return [string]         elemento HTML
	 */
	private function setearEstadoVisualizacion($estado) {
		if (isset($estado)) {
			if ($estado == 'viewed') {
				return '<td class="text-center bg-success text-white" width="100">VISTO</td>';
			} elseif ($estado == 'not_viewed') {
				return '<td class="text-center bg-danger text-white" width="100">NO VISTO</td>';
			}
		} else {
			return '<td width="100">&nbsp;</td>';
		}
	}
}
?>