<?php
if (!isset($_SESSION))
	session_start();

class VistaOrdenSesionComboSubsecciones extends VistaBase {

	public function __construct() {

		parent::__construct();

		$this->controlador = 'ordenes_sesion';
	}

	public function mostrar($subsecciones, $cod_seccion) {

		$cant_subsecciones = (isset($subsecciones)) ? count($subsecciones) : 0;
		?>
		<option value="0">seleccione</option>
		<?php for ($i = 0; $i < $cant_subsecciones; $i++) {?>
			<option value="<?=$subsecciones[$i]['codigo'];?>" >
				<?=$subsecciones[$i]['nombre'];?>
			</option>
		<?php }?>
		<script>
			// Se muestra la subsección si ya posee
			$('#cod_seccion').val('<?=(isset($cod_seccion)) ? $cod_seccion : 0; ?>');
		</script>
	<?php }
}