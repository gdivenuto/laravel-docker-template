<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaAuditoriaAdministracionGrilla extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'auditoria_administracion';
	}

	/**
	 * Se muestra la grilla
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 * @param  array $filtro       [description]
	 */
	public function mostrar($datos, $filtro) {

		$cantidad = (isset($datos)) ? count($datos) : 0;
		// Se arma el criterio del buscador para la url del paginador
		$criterio_buscador = "&f_usuario=" . $filtro['f_usuario'];
		$criterio_buscador .= "&f_fecha_desde=" . $this->formatearFecha($filtro['f_fecha_desde']);
		$criterio_buscador .= "&f_fecha_hasta=" . $this->formatearFecha($filtro['f_fecha_hasta']);
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
							Auditor&iacute;a del Sistema de Administraci&oacute;n
						</div>
					</div>
					<!-- Buscador -->
					<div class="row mt-1">
						<form class="form-inline" action="" method="POST" name="formBuscadorGrilla">

							<input  type="hidden" id="mensaje" name="mensaje" 
									value="<?= (isset($mensaje)) ? $mensaje : ''; ?>">
					        <input  type="hidden" id="tipo_mensaje" name="tipo_mensaje" 
					        		value="<?= (isset($tipo_mensaje)) ? $tipo_mensaje : ''; ?>">
							<input type="hidden" id="url_abms" name="url_abms" value="<?= URL_ABMS; ?>">
							<input type="hidden" id="controlador" name="controlador" value="<?= $this->controlador; ?>">
							<input type="hidden" id="pagina" name="pagina" value="<?= (isset($filtro['pagina'])) ? $filtro['pagina'] : ''; ?>">

							<input type="text" name="f_usuario" id="f_usuario" value="<?= utf8_encode($filtro['f_usuario']); ?>" class="form-control form-control-sm small mx-3" placeholder="Usuario...">

							<label for="f_fecha_desde" class="mx-3 small">Fecha Desde</label>
							<input  id="f_fecha_desde" name="f_fecha_desde" 
									value="<?=(isset($filtro['f_fecha_desde'])) ? $this->formatearFecha($filtro['f_fecha_desde']) : '';?>" 
									class="form-control form-control-sm small" width="130" />

							<label for="f_fecha_hasta" class="mx-3 small">Fecha Hasta</label>
							<input  id="f_fecha_hasta" name="f_fecha_hasta" 
									value="<?=(isset($filtro['f_fecha_hasta'])) ? $this->formatearFecha($filtro['f_fecha_hasta']) : '';?>" 
									class="form-control form-control-sm small" width="130" />

							<button type="button" id="btBuscar" class="btn btn-info btn-sm mx-2" title="Buscar">
								<i class="fas fa-search"></i>&nbsp;Buscar
							</button>
							<button type="button" id="btLimpiar" class="btn btn-info btn-sm mx-2" title="Limpiar criterio de b&uacute;squeda">
								<i class="fas fa-eraser"></i>&nbsp;Limpiar
							</button>
						</form>
					</div>

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
									      	<th class="text-center">Fecha</th>
									      	<th class="text-center">Hora</th>
									      	<th class="text-center">Usuario</th>
											<th class="text-center">PC utilizada</th>
											<th class="text-center">Operaci&oacute;n</th>
											<th>Tabla</th>
											<th>Observaciones</th>
									    </tr>
									</thead>
									<tbody>
										<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos[$i];

										$partes_fecha_hora = explode(' ', $dato['fecha_hora_log']);
										$fecha_log = $partes_fecha_hora[0];
										$hora_log = $partes_fecha_hora[1];
										?>
										<tr>
									        <td class="text-center">
									        	<?= ($fecha_log != '') ? $this->formatearFecha($fecha_log) : '&nbsp;'; ?>
								        	</td>
											<td class="text-center">
												<?= ($hora_log != '') ? $hora_log : '&nbsp;'; ?>
											</td>
											<td class="text-left"><?= ($dato['netusername'] != '') ? $dato['netusername'] : '&nbsp;'; ?>
											</td>
											<td class="text-left">
												<?= ($dato['netpcname'] != '') ? $dato['netpcname'] : '&nbsp;'; ?>
											</td>
											<td class="text-left">
												<?= ($dato['operacion'] != '') ? $dato['operacion'] : '&nbsp;'; ?>
											</td>
											<td class="text-left">
												<?= ($dato['tabla'] != 'null') ? $dato['tabla'] : '&nbsp;'; ?>
											</td>
											<td class="text-left">
												<?= ($dato['observaciones_log'] != '') ? $dato['observaciones_log'] : '&nbsp;'; ?>
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

			    <script src="<?= URL_JS; ?>auditoria_administracion/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>
