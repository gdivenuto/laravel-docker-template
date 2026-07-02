<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaEquiposHcdCombo extends VistaBase {

	private $controlador;

	public function __construct() {
		parent::__construct();
		$this->controlador = 'equipos_hcd';
	}

	/**
	 * Se renderiza el combo
	 * @param  array $responsables	Listado de categorías
	 */
	public function mostrar($responsables = null, $cod_responsable = 0) {

		$cant_responsables = (isset($responsables)) ? count($responsables) : 0;?>

		<option value="0">---</option>
		<?php for ($c = 0; $c < $cant_responsables; $c++) {?>
			<option value="<?=$responsables[$c]['cod_responsable'];?>" >
				<?=$responsables[$c]['nombre_responsable'];?>
			</option>
		<?php }?>
		<script>
			$('#cod_responsable').val('<?=(isset($cod_responsable)) ? $cod_responsable : 0;?>');
		</script>
	<?php }
}?>