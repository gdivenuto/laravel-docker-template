<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaOrdenSesionCargaGrupal extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'ordenes_sesion';

		// Se crea una instancia del modelo
		$this->modelo = new ordenes_sesionModel();
	}

	public function mostrar($listado = null, $filtro = null, $mensaje = '', $tipo_mensaje = '') {

		$cant_listado = (isset($listado)) ? count($listado) : 0;

		$odcg_fecha_desde = LibreriaGeneral::restarDiasFechaActual(20);
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
							Carga Grupal para la Orden del D&iacute;a de Sesi&oacute;n
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" action="<?=URL_ABMS;?>" method="POST">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="listarCargaGrupal" />
					    <input type="hidden" id="id_sesion" name="id_sesion" value="<?= $filtro['id_sesion']; ?>" />
					    <input type="hidden" id="cod_seccion" name="cod_seccion" value="<?= $filtro['cod_seccion']; ?>" />
						<input type="hidden" id="cantidad_listado" name="cantidad_listado" value="<?= $cant_listado; ?>" />

						<div class="row mt-1">
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<input  id="odcg_fecha_desde" name="odcg_fecha_desde"
										class="form-control form-control-sm small" width="130"
										value="<?=($_SESSION['filtro_carga_grupal']['odcg_fecha_desde']) ? $this->formatearFecha($_SESSION['filtro_carga_grupal']['odcg_fecha_desde']) : $this->formatearFecha($odcg_fecha_desde); ?>" />
							</div>
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<input  id="odcg_fecha_hasta" name="odcg_fecha_hasta"
										class="form-control form-control-sm small" width="130"
										value="<?=($_SESSION['filtro_carga_grupal']['odcg_fecha_hasta']) ? $this->formatearFecha($_SESSION['filtro_carga_grupal']['odcg_fecha_hasta']) : date("d/m/Y"); ?>" />
							</div>
							<div class="col-12 col-md-3 mt-1 mt-md-0">
								<button type="button" 
										id="btBuscar"
										class="btn btn-info btn-sm"
										title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<?php if ($cant_listado > 0){ ?>
								<button type="button" 
										id="btCargar"
										class="btn btn-success btn-sm mt-1 mt-md-0"
										title="Cargar">
									<i class="fas fa-plus"></i>&nbsp;Cargar
								</button>
								<?php } ?>
								<button type="button" 
										id="btVolver"
										class="btn btn-info btn-sm"
										title="Volver a la edici&oacute;n del &iacute;tem">
									<i class="fas fa-angle-left"></i>&nbsp;Volver
								</button>
							</div>
						</div>
					
						<!-- Grilla -->
						<div class="row mt-1">
							<div class="col-md-12">
							<?php if ($cant_listado > 0) { ?>
								<div class="table-responsive">
									<table class="table table-hover table-bordered table-sm small">
										<thead class="thead-light">
											<tr>
												<th class="text-center">A&ntilde;o</th>
												<th class="text-center">Tipo</th>
												<th class="text-center">N&uacute;mero</th>
												<th class="text-center">C&oacute;digo</th>
										      	<th class="text-center">Extracto</th>
										      	<th class="text-center">&nbsp;</th>
										    </tr>
										</thead>
										<tbody>
										<?php for ($i = 0; $i < $cant_listado; $i++) { $dato = &$listado[$i];?>

											<tr id="e_fila<?= $i; ?>">
												<td width="15" class="text-muted">
													<input  type="text" 
															name="anio_carga_grupal<?= $i; ?>" 
															id="anio_carga_grupal<?= $i; ?>" 
															value="<?= $dato['anio']; ?>" 
															class="odcg_input" disabled >
												</td>
												<td width="15" class="text-muted">
													<input  type="text" 
															name="tipo_carga_grupal<?= $i; ?>" 
															id="tipo_carga_grupal<?= $i; ?>" 
															value="<?= $dato['tipo']; ?>" 
															class="odcg_input" disabled >
												</td>
												<td width="30" class="text-muted">
													<input  type="text" 
															name="numero_carga_grupal<?= $i; ?>" 
															id="numero_carga_grupal<?= $i; ?>" 
															value="<?= $dato['numero']; ?>" 
															class="odcg_input" disabled >
												</td>
												<td width="15" class="text-muted">
													<input  type="text" 
															name="iniciador_carga_grupal<?= $i; ?>" 
															id="iniciador_carga_grupal<?= $i; ?>" 
															value="<?= $dato['iniciador_codigo']; ?>" 
															class="odcg_input" disabled >
												</td>
												<td class="text-muted" 
													id="extracto_legible_carga_grupal<?= $i; ?>" readonly >

													<?php
													$detalle_proyectos_documento = '';
													
													// Se obtiene la información de los proyectos del exped./nota
													$proyectos = $this->modelo->obtenerProyectosExpedienteItem($dato);
													
													$cant_proyectos = count($proyectos);

													for ($j=0; $j < $cant_proyectos; $j++) {
														$proyecto = &$proyectos[$j];
													
														// Si el proyecto posee extracto, se muestra
														if ( $proyecto['extracto'] != '' && $proyecto['extracto'] != 'null' ) {

															// Si posee más de un proyecto el exped./nota
															if ( $cant_proyectos > 1 ) {

																$num_proyecto = $j + 1;

																// Se muestran numerados, con el formato: 
																// [1]) PROYECTO DE [DESCRIPCION]: [EXTRACTO] 
																// [2]) PROYECTO DE [DESCRIPCION]:[EXTRACTO]...
																$detalle_proyectos_documento .= $num_proyecto.") PROYECTO DE ".$this->reemplazarPorMayusculaAcentuada(strtoupper($proyecto['descripcion_proyecto'])).": ".$proyecto['extracto']." ";
															} else {
																// Sino se muestra sólo el extracto
																$detalle_proyectos_documento = $proyecto['extracto'];
															}
														}
													}
													// Se muestra el detalle de proyectos del exped./nota
													echo $this->cortaCadena($detalle_proyectos_documento, 60);
													?>
												</td>

												<input  type="hidden" 
														name="descripcion_iniciador_carga_grupal<?= $i; ?>" 
														id="descripcion_iniciador_carga_grupal<?= $i; ?>" 
														value="<?= $dato['descripcion_iniciador']; ?>" disabled >
												
												<input  type="hidden" 
														name="caratula_carga_grupal<?= $i; ?>" 
														id="caratula_carga_grupal<?= $i; ?>" 
														value="<?= $dato['caratula']; ?>" disabled >
												
												<td width="5">
													<input  type="checkbox" 
															name="chk_elegido<?= $i; ?>" 
															id="chk_elegido<?= $i; ?>" 
															class="check_carga_grupal"
															onchange="javascript:elegir(<?= $i; ?>);" >
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
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<?php $this->mostrarSpinnerModal();?>

				<script src="<?=URL_JS;?>ordenes_sesion/carga_grupal.js"></script>
				
		  	</body>
		</html>
	<?php }
}