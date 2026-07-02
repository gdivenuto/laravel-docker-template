<?php
if (!isset($_SESSION))
	session_start();

class VistaDocumentosElec extends VistaBase {

	public function __construct() {

		parent::__construct();

		$this->controlador = 'ordenes_sesion';
	}

	public function mostrar($doc_elec) {

		$cant_doc_elec = (isset($doc_elec)) ? count($doc_elec) : 0;
	?>
		<div class="table-responsive <?=( $cant_doc_elec > 3) ? 'alto_150' : '';?>">
			<table class="table table-hover table-bordered table-sm small">
				<thead class="thead-light">
					<tr>
						<th>&nbsp;</th>
						<th class="text-center">Orden</th>
				      	<th>Detalle</th>
				      	<th class="text-center">Fecha</th>
						<th width="30">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php for ($i=0; $i < $cant_doc_elec; $i++) { ?>
					<tr>
						<td class="text-center">
							<?= ($doc_elec[$i]['dec1404']) 
								? '<i class="fa fa-eye-slash text-danger" title="Alcanzado por Art. 11 Dec. 1404"  aria-hidden="true"></i>'
								: '<i class="fa fa-eye text-success" title="No Alcanzado por Art. 11 Dec. 1404" aria-hidden="true"></i>';?>
						</td>
						<td class="text-center">
							<?= $doc_elec[$i]['orden']; ?>
						</td>
						<td>
							<a  href="<?= URL_PROYECTOS.$doc_elec[$i]['documento'].'?v='.date("Ymd_His");?>" 
								target="_blank" title="Ver documento">
								<?= $doc_elec[$i]['detalle']; ?>
							</a>
						</td>
						<td>
							<?= $this->extraerFecha($doc_elec[$i]['fecha_hora']); ?>
						</td>
	    				<td class="text-center" width="30">
					      	<a  href="javascript:asignarDespacho(<?=$doc_elec[$i]['orden'];?>, '<?= $doc_elec[$i]['detalle']; ?>');"
				    			title="Asignar despacho al item">
				    			<i class="fas fa-angle-right"></i>
				    		</a>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	<?php }
}