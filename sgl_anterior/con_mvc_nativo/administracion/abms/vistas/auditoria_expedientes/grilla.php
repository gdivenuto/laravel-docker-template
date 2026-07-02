<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaAuditoriaExpedientesGrilla extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'auditoria_expedientes';
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
		$criterio_buscador = "&f_anio=" . $filtro['f_anio'];
		$criterio_buscador .= "&f_tipo=" . $filtro['f_tipo'];
		$criterio_buscador .= "&f_numero=" . $filtro['f_numero'];
		$criterio_buscador .= "&f_cuerpo=" . $filtro['f_cuerpo'];
		$criterio_buscador .= "&f_alcance=" . $filtro['f_alcance'];
		$criterio_buscador .= "&f_fecha_desde=" . $this->formatearFecha($filtro['f_fecha_desde']);
		$criterio_buscador .= "&f_fecha_hasta=" . $this->formatearFecha($filtro['f_fecha_hasta']);
		$criterio_buscador .= "&f_usuario=" . $filtro['f_usuario'];
		$criterio_buscador .= "&f_criterio=" . $filtro['f_criterio'];
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
							Auditor&iacute;a del Sistema de Expedientes
						</div>
					</div>
					<!-- Buscador -->
					<form action="" method="POST" name="formBuscadorGrilla">

						<input  type="hidden" id="mensaje" name="mensaje" 
								value="<?= (isset($mensaje)) ? $mensaje : ''; ?>">
				        <input  type="hidden" id="tipo_mensaje" name="tipo_mensaje" 
				        		value="<?= (isset($tipo_mensaje)) ? $tipo_mensaje : ''; ?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?= URL_ABMS; ?>">
						<input type="hidden" id="controlador" name="controlador" value="<?= $this->controlador; ?>">
						<input type="hidden" id="pagina" name="pagina" value="<?= (isset($filtro['pagina'])) ? $filtro['pagina'] : ''; ?>">

						<div class="row m-1">
							<label for="f_criterio" class="col-1 small text-right pt-2">Buscar por</label>
							<select id="f_criterio" name="f_criterio" class="col-1 form-control form-control-sm">
								<option value="1">Clave</option>
								<option value="2">Fechas</option>
								<option value="3">Usuario</option>
							</select>

							<div class="col-12 col-md-1 campo_busqueda_por_clave">
								<input  type="text" name="f_anio" id="f_anio" 
										value="<?= utf8_encode($filtro['f_anio']); ?>" 
										class="form-control form-control-sm small">
							</div>
							<div class="col-12 col-md-1 campo_busqueda_por_clave">
								<select id="f_tipo" name="f_tipo" class="form-control form-control-sm">
									<option value="E">E</option>
									<option value="N">N</option>
									<option value="R">R</option>
								</select>
							</div>
							<div class="col-12 col-md-1 campo_busqueda_por_clave">
								<input  type="text" name="f_numero" id="f_numero" 
										value="<?= utf8_encode($filtro['f_numero']); ?>" 
										class="form-control form-control-sm small" 
										onKeyPress="return soloEnteros(event);">
							</div>
							<div class="col-12 col-md-1 campo_busqueda_por_clave">
								<input  type="text" name="f_cuerpo" id="f_cuerpo" 
										value="<?= utf8_encode($filtro['f_cuerpo']); ?>" 
										class="form-control form-control-sm small"
										onKeyPress="return soloEnteros(event);">
							</div>
							<div class="col-12 col-md-1 campo_busqueda_por_clave">
								<input  type="text" name="f_alcance" id="f_alcance" 
										value="<?= utf8_encode($filtro['f_alcance']); ?>" 
										class="form-control form-control-sm small"
										onKeyPress="return soloEnteros(event);">
							</div>

							<div class="col-12 col-md-2 campo_busqueda_por_fechas">
								<input  id="f_fecha_desde" name="f_fecha_desde" 
										class="form-control form-control-sm small"
										value="<?=(isset($filtro['f_fecha_desde'])) ? $this->formatearFecha($filtro['f_fecha_desde']) : '';?>" 
										placeholder="Desde" width="130" />
							</div>
							<div class="col-12 col-md-2 campo_busqueda_por_fechas">
								<input  id="f_fecha_hasta" name="f_fecha_hasta" 
										class="form-control form-control-sm small" 
										value="<?=(isset($filtro['f_fecha_hasta'])) ? $this->formatearFecha($filtro['f_fecha_hasta']) : '';?>"
										placeholder="Hasta" width="130" />
							</div>

							<div class="col-12 col-md-2 campo_busqueda_por_usuario">
								<input  type="text" name="f_usuario" id="f_usuario" 
										value="<?= utf8_encode($filtro['f_usuario']); ?>" 
										class="form-control form-control-sm small">
							</div>

							<button type="button" id="btBuscar" class="ml-3 btn btn-info btn-sm" title="Buscar">
								<i class="fas fa-search"></i>&nbsp;Buscar
							</button>
							<button type="button" id="btLimpiar" class="ml-3 btn btn-info btn-sm" title="Limpiar criterio de b&uacute;squeda">
								<i class="fas fa-eraser"></i>&nbsp;Limpiar
							</button>
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
									      	<th class="text-center">Fecha Log</th>
									      	<th class="text-center">Hora Log</th>
									      	<th class="text-center">Usuario</th>
											<th class="text-center">PC utilizada</th>
											<th class="text-center">Operaci&oacute;n</th>
											<th class="text-center">Tabla</th>
											<th class="text-center">Exped./Nota</th>
											<th class="text-center">Fecha</th>
									      	<th class="text-center">Orden</th>
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
											<td><?= ($dato['netusername'] != '') ? $dato['netusername'] : '&nbsp;'; ?>
											</td>
											<td>
												<?= ($dato['netpcname'] != '') ? $dato['netpcname'] : '&nbsp;'; ?>
											</td>
											<td>
												<?= ($dato['operacion'] != '') ? $dato['operacion'] : '&nbsp;'; ?>
											</td>
											<td>
												<?= ($dato['tabla'] != 'null') ? $dato['tabla'] : '&nbsp;'; ?>
											</td>
											<td class="text-center" width="120">
												<?= (isset($dato['anio_log']) && $dato['anio_log'] != 0) ? $dato['anio_log'].'-'.$dato['tipo_log'].'-'.$dato['numero_log'].'-'.$dato['cuerpo_log'].'-'.$dato['alcance_log'] : '---';?>
											</td>
											<td class="text-center">
									        	<?= ($dato['fecha_log'] != '') ? $this->formatearFecha($dato['fecha_log']) : '&nbsp;'; ?>
								        	</td>
											<td class="text-center">
												<?= ($dato['orden_log'] != '') ? $dato['orden_log'] : '&nbsp;'; ?>
											</td>
											<td>
												<?= ($dato['observaciones_log'] != '') ? nl2br($dato['observaciones_log']) : '&nbsp;'; ?>
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

				<script>
					jQuery(document).ready(function() {

						$('#f_criterio').val('<?=(isset($filtro['f_criterio'])) ? $filtro['f_criterio'] : 1;?>');

					    $('#f_tipo').val('<?=(isset($filtro['f_tipo'])) ? $filtro['f_tipo'] : E;?>');
					});
				</script>
			    <script src="<?= URL_JS; ?>auditoria_expedientes/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>
