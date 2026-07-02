<?php
if (!isset($_SESSION)) {
	session_start();
}

class VistaFichaWebGrilla extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'ficha_web';

		$this->modelo = new fichaWebModel();
		$this->modelo_personal = new personalModel();
	}

	/**
	 * Se muestra la grilla
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 * @param  array $filtro       [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '', $filtro) {

		$cantidad = (isset($datos)) ? count($datos) : 0;

		// Se arma el criterio del buscador para la url del paginador
		$criterio_buscador = "&f_legajo=" . $filtro['f_legajo'];
		$criterio_buscador .= "&f_apellido_y_nombre=" . $filtro['f_apellido_y_nombre'];
		$criterio_buscador .= "&f_activos=" . $filtro['f_activos'];
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
							Fichas Web de Autoridades del HCD
						</div>
					</div>
					<!-- Buscador -->
					<form action="" method="POST" name="formBuscadorGrilla">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input  type="hidden" id="pagina" name="pagina"
								value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">
						
						<div class="form-row">
							<div class="col-12 col-md-1 mt-1">
								<input  type="text" name="f_legajo" id="f_legajo"
										value="<?= ($_SESSION['f_ficha_web']['f_legajo']) ? $_SESSION['f_ficha_web']['f_legajo'] : ''; ?>"
										class="form-control form-control-sm small"
										placeholder="Legajo">
							</div>
							<div class="col-12 col-md-4 mt-1">
								<input  type="text" name="f_apellido_y_nombre" id="f_apellido_y_nombre"
										value="<?= ($_SESSION['f_ficha_web']['f_apellido_y_nombre']) ? $_SESSION['f_ficha_web']['f_apellido_y_nombre'] : ''; ?>"
										class="form-control form-control-sm small"
										placeholder="Apellido y Nombres">
							</div>
							<div class="col-12 col-md-2 mt-1">
								<div class="form-group form-check pt-1">

									<input  type="checkbox" class="form-check-input"
											name="chk_activos" id="chk_activos" 
											<?= ( $_SESSION['f_ficha_web']['f_activos'] == 1 ) ? "checked" : ""; ?> />
									<label class="form-check-label" for="chk_activos">Activos</label>

									<input  type="hidden" id="f_activos" name="f_activos" 
											value="<?= ($_SESSION['f_ficha_web']['f_activos']) ? $_SESSION['f_ficha_web']['f_activos'] : ''; ?>" />
								</div>
							</div>
							<div class="col-12 col-md-5 mt-1 text-center text-md-left">
								<button type="button" id="btBuscar" class="btn btn-info btn-sm" title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar" class="btn btn-info btn-sm" title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>
							</div>
						</div>
					</form>

					<!-- Paginador -->
					<?php $this->mostrarPaginadorMostrandoActual($cantidad, $filtro, $criterio_buscador, $this->controlador);?>

					<!-- Grilla -->
					<div class="row mt-1">
						<div class="col-md-12">
							<?php if ($cantidad > 0) { ?>
								<div class="table-responsive">
									<table class="table table-hover table-bordered table-sm small">
										<thead class="thead-light">
											<tr>
												<th width="30">&nbsp;</th>
										      	<th>Legajo</th>
										      	<th>Apellido</th>
										      	<th>Nombre</th>
												<th>Funci&oacute;n</th>
												<th>&Aacute;rea</th>
												<th>Per&iacute;odo</th>
												<th>Tel&eacute;fono</th>
												<th>Email</th>
												<th class="text-center">Activo</th>
											</tr>
										</thead>
										<tbody>
										<?php
										for ($i = 0; $i < $cantidad; $i++) { 
											$dato = &$datos[$i];

											$ficha_web = $this->modelo->obtenerRegistro($dato['p_legajo']);

											$marca_presidente_bloque = ($ficha_web['fw_es_presidente_bloque'] == '1') ? ' <strong>(Pte. Bloque)</strong>' : '';

											$area = $this->modelo_personal->obtenerNombreUltimaArea($dato['p_legajo']);
											
											$activo = $this->modelo_personal->estaActivo($dato['p_legajo']);
										?>
											<tr <?=($activo == '0') ? ' class="text-muted"' : '';?> >

										    	<td class="text-center" width="30">
										    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&legajo=<?= $dato['p_legajo']; ?>&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
										    			<i class="fas fa-edit"></i>
										    		</a>
										    	</td>

										        <td class="text-right">
										        	<?= ($dato['p_legajo']) ? number_format($dato['p_legajo'], 0, '', '.') : '&nbsp;'; ?>
										        </td>
												<td>
													<?= ($dato['p_apellido']) ? $dato['p_apellido'] : '&nbsp;'; ?>
												</td>
												<td>
													<?= ($dato['p_nombre']) ? $dato['p_nombre'] : '&nbsp;'; ?>
												</td>
												<td>
													<?= ($ficha_web['fw_funcion']) ? $this->mostrarFuncion($ficha_web['fw_funcion']) : 'Concejal'; ?>
												</td>
												<td>
													<?= ($area['area']) ? $area['area'].$marca_presidente_bloque : '&nbsp;'; ?>
												</td>
												<td width="100" class="text-center">
													<?= (isset($ficha_web['fw_anio_inicio']) && isset($ficha_web['fw_anio_fin'])) ? $ficha_web['fw_anio_inicio'].' - '.$ficha_web['fw_anio_fin'] : '&nbsp;'; ?>
												</td>
												<td width="130">
													<?= (isset($ficha_web['fw_telefono'])) ? $ficha_web['fw_telefono'] : '&nbsp;'; ?>
												</td>
												<td>
													<?= (isset($ficha_web['fw_mail'])) ? $ficha_web['fw_mail'] : '&nbsp;'; ?>
												</td>
												<td class="text-center" width="40">
													<?= ($activo) ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>'; ?>
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

			    <script src="<?=URL_JS;?>ficha_web/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }

    private function mostrarFuncion($funcion = null) {

    	switch ($funcion) {
    		case 1:
    			$nombre = 'Presidente';
    			break;
			case 2:
				$nombre = 'Vicepresidente 1ro.';
				break;
			case 3:
				$nombre = 'Vicepresidente 2do.';
				break;
			case 4:
				$nombre = 'Secretario';
				break;
			case 5:
				$nombre = 'Subsecretario';
				break;
			default:
				$nombre = 'Concejal';
				break;
    	}
    	return $nombre;
    }

}
?>