<?php
if (!isset($_SESSION))
	session_start();

class VistaDespachosItem extends VistaBase {

	public function __construct() {

		parent::__construct();

		$this->controlador = 'ordenes_sesion';
	}

	public function mostrar($despachos) {

		$cant_despachos = (isset($despachos)) ? count($despachos) : 0;
	?>
		<div class="table-responsive <?=( $cant_despachos > 3) ? 'alto_150' : '';?>">
			<table class="table table-hover table-bordered table-sm small">
				<thead class="thead-light">
					<tr>
						<th>&nbsp;</th>
						<th class="text-center">Orden</th>
				      	<th>Detalle</th>
						<th width="60" colspan="2">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php for ($i = 0; $i < $cant_despachos; $i++) { $d = &$despachos[$i]; ?>
					<tr>
						<td class="text-center">
							<?= ($d['dec1404']) 
								? '<i class="fa fa-eye-slash text-danger" title="Alcanzado por Art. 11 Dec. 1404"  aria-hidden="true"></i>'
								: '<i class="fa fa-eye text-success" title="No Alcanzado por Art. 11 Dec. 1404" aria-hidden="true"></i>';?>
						</td>
						<td class="text-center">
							<?= $d['orden_actuacion']; ?>
						</td>
						<td>
							<a  href="<?= $d['documento'];?>" 
								target="_blank" title="Ver documento">
								<?=(isset($d['detalle'])) ? $d['detalle'] : '';?>
							</a>
						</td>
						<td class="text-center">
							<a  href="javascript:editarDetalleModal(<?=$d['orden_actuacion'];?>, '<?=$d['detalle'];?>');"
				    			title="Actualizar texto del detalle del despacho">
				    			<i class="fas fa-edit"></i>
				    		</a>
						</td>
						<td class="text-center">
							<a  href="javascript:eliminarDespacho(<?=$d['orden_actuacion'];?>);"
				    			title="Eliminar despacho">
				    			<i class="fas fa-trash"></i>
				    		</a>
						</td>
			    	</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	<?php }
}