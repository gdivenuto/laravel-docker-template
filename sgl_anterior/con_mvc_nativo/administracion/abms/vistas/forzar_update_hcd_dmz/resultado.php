<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaResultado extends VistaBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Se muestra el resultado
	 * @param  string 	$mensaje
	 * @param  integer 	$tipo_mensaje
	 */
	public function mostrar($mensaje = '', $tipo_mensaje = '') { ?>

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
							Tarea de actualizaci&oacute;n de bases de datos <strong>hcd</strong> y <strong>dmz</strong>
						</div>
					</div>
					<div class="row mt-1">
						<div class="col-md-12">
							<div class="alert alert-<?=($tipo_mensaje == 1) ? 'success' : 'danger';?>"><?=$mensaje;?></div>
						</div>
					</div>
				</div>
			</body>
		</html>
	<?php }

}