<?php
if (!isset($_SESSION)) {
	session_start();
}

class VistaOrdenSesionGrilla extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'ordenes_sesion';

		// Se crea una instancia del modelo
		$this->modelo = new ordenes_sesionModel();
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
		$criterio_buscador = "&f_periodo=" . $filtro['f_periodo'];
		$criterio_buscador .= "&f_reunion=" . $filtro['f_reunion'];
		$criterio_buscador .= "&f_sesion=" . $filtro['f_sesion'];
		$criterio_buscador .= "&f_fecha=" . $filtro['f_fecha'];
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
							Ordenes del D&iacute;a de Sesi&oacute;n
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
								<input type="text" name="f_periodo" id="f_periodo"
										value="<?=($_SESSION['filtro_od_sesion']['f_periodo']) ? $_SESSION['filtro_od_sesion']['f_periodo'] : '';?>"
										class="form-control form-control-sm small"
										placeholder="Per&iacute;odo"
										onKeyPress="return soloEnteros(event)">
							</div>
							<div class="col-12 col-md-1 mt-1">
								<input  type="text" name="f_reunion" id="f_reunion"
										value="<?=($_SESSION['filtro_od_sesion']['f_reunion']) ? $_SESSION['filtro_od_sesion']['f_reunion'] : '';?>"
										class="form-control form-control-sm small"
										placeholder="Reuni&oacute;n"
										onKeyPress="return soloEnteros(event)">
							</div>
							<div class="col-12 col-md-4 mt-1">
								<input  type="text" name="f_sesion" id="f_sesion"
										value="<?=($_SESSION['filtro_od_sesion']['f_sesion']) ? $_SESSION['filtro_od_sesion']['f_sesion'] : '';?>"
										class="form-control form-control-sm small"
										placeholder="Sesi&oacute;n">
							</div>
							<div class="col-12 col-md-2 mt-1">
								<input  id="f_fecha" name="f_fecha"
										class="form-control form-control-sm small" width="130"
										value="<?=(isset($filtro['f_fecha'])) ? $this->formatearFecha($filtro['f_fecha']) : '';?>"
										placeholder="Fecha" />
							</div>
							<div class="col-12 col-md-3 mt-1 text-center text-md-left">
								<button type="button" id="btBuscar" class="btn btn-info btn-sm" title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar" class="btn btn-info btn-sm" title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>
								<button type="button" id="btNuevo" class="btn btn-success btn-sm"
										title="Nueva Secci&iocute;n">
									<i class="fas fa-plus"></i>&nbsp;Nueva
								</button>
							</div>
						</div>
					</form>

					<!-- Paginador -->
					<?php $this->mostrarPaginador($cantidad, $filtro, $criterio_buscador, $this->controlador);?>

					<!-- Grilla -->
					<div class="row mt-1">
						<div class="col-md-12">
							<?php if ($cantidad > 0) { ?>
								<div class="table-responsive">
									<table class="table table-hover table-bordered table-sm small">
										<thead class="thead-light">
											<tr>
												<th width="60" colspan="2">&nbsp;</th>
										      	<th width="80">Per&iacute;odo</th>
										      	<th width="80">Reuni&oacute;n</th>
										      	<th>Sesi&oacute;n</th>
												<th class="text-center">Fecha</th>
												<th class="text-center">Hora</th>
												<th colspan="3">&nbsp;</th>
											</tr>
										</thead>
										<tbody>
										<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos[$i];?>
											<tr>
										    	<td class="text-center" width="30">
										    		<a  href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>');"
										    			title="Editar registro">
										    			<i class="fas fa-edit"></i>
										    		</a>
										    	</td>
										    	<td class="text-center" width="30">
										    		<a  href="javascript:if(confirm('¿Desea eliminar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>');};"
										    			title="Eliminar registro">
										    			<i class="fas fa-trash"></i>
										    		</a>
										    	</td>

										        <td class="text-right"><?=$dato['periodo'];?></td>

												<td class="text-right"><?=$dato['reunion'];?></td>

												<td><?=$dato['sesion'];?></td>

												<td class="text-center" width="80">
													<?=$this->formatearFecha($dato['fecha']);?>
												</td>
												<td class="text-center" width="80">
													<?=$dato['hora'];?>
												</td>
												<td class="text-center" width="30">
													<?php
													if ($this->modelo->tieneItems($dato['id'])) {

														/**
														// TEMPORAL, retirar al solucionar el error:
														// TCPDF ERROR: Could not include font definition file: arial 
														if ($dato['id'] == 225) {
														?>
															<a href="https://www.concejomdp.gov.ar/legislacion/orden_sesion/periodos/110/orden_sesion_extraordinaria_2026_02_27.pdf"
															 target="_blank" 
															 title="Generar Orden en PDF">
																<i class="far fa-file-pdf"></i>
															</a>
														<?php
														} else {
														/**/
															// Se pregunta si se desea agregar o no "Sobre Tablas" en el documento de la orden del Día, desde una modal
														?>
															<a href="javascript:preguntarPorSobreTablasFormatoImpresion('crearFormatoPdfOrden', <?=$dato['id'];?>);" title="Generar Orden en PDF">
																<i class="far fa-file-pdf"></i>
															</a>
														<?php 
														//}
													}
													?>
												</td>
												<td class="text-center" width="30">
													<?php
													if ($this->modelo->tieneItems($dato['id'])) {
													// Se pregunta si se desea agregar o no "Sobre Tablas" en el documento de la orden del Día, desde una modal
													?>
														<a href="javascript:preguntarPorSobreTablasFormatoImpresion('crearFormatoImpresionOrden', <?=$dato['id'];?>);" title="Imprimir Orden">
															<i class="fas fa-print"></i>
														</a>
													<?php }?>
												</td>
												<td class="text-center" width="30">
													<?php // Se permite publicar la Orden del Día de Sesión en el sitio web
													if ($this->modelo->tieneItems($dato['id'])) {
													?>
														<a href="javascript:preguntarPorSobreTablasFormatoImpresion('verOrdenParaPublicar', <?=$dato['id'];?>);" title="Ver Orden para Publicar en el sitio web">
															<i class="fas fa-cloud-upload-alt"></i>
														</a>
													<?php }?>
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

				<!-- Modal para el Despacho 
				<div id="modal_despacho" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_despacho" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
						        <img id="cabecera_logo" src="<?=URL_IMAGENES;?>logo_hcd.png" class="rounded-circle" alt="<?=TITULO_SISTEMA;?>">
						        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
						          <span aria-hidden="true">&times;</span>
						        </button>
					        </div>
							<div class="modal-body">

								<input type="hidden" id="modal_periodo" name="modal_periodo" value="" />
								<input type="hidden" id="modal_reunion" name="modal_reunion" value="" />
								<input type="hidden" id="modal_sesion" name="modal_sesion" value="" />
								<input type="hidden" id="modal_fecha" name="modal_fecha" value="" />

								<div class="row mt-1">
									<div class="col-12 overflow-auto">
										<table class="table table-hover table-sm small">
											<thead class="thead-light">
												<tr>
													<th class="text-center" colspan="2">Despacho</th>
												</tr>
											</thead>
											<tbody>
												<?php
												// Si existe el directorio
												if (is_dir(RUTA_PROYECTOS_DIGITAL)) {
													// Si pudo abrirse
													if ($handle = opendir(RUTA_PROYECTOS_DIGITAL)) {

														while (false !== ($file = readdir($handle))) {
															if ($file != "." && $file != ".." && 
																$file != "index.html" && 
																$file != ".gitkeep" && 
																!is_dir(RUTA_PROYECTOS_DIGITAL.$file)) {

																// un array por si hubiese más de uno
																$archivos[] = $file;
															}
														}
														closedir($handle);
														
														// Si posee archivos
														if ($archivos) {
															// Se consideran solamente aquellos archivos pdf  
															// que NO cumplen con la nomenclatura AATNNNNN
															$despachos = preg_grep('/^[0-9]{2,2}(e|n|r)[0-9]{5,5}/i', $archivos, PREG_GREP_INVERT);
															// Se ordenan
															sort($despachos);

															// Se muestran
															foreach ($despachos as $documento) { ?>
																<tr>
																	<td class="p-1">
							                                  			<a  title="Ver despacho"
							                                  				href="<?= URL_PROYECTOS_DIGITAL.$documento; ?>"
							                                  				target="_blank">
							                                  				<?= $documento; ?>
							                                  			</a>
																	</td>
																	<td width="80" class="p-1">
																		<a  title="Publicar despacho"
																			href="javascript:publicarDespacho('<?=$documento;?>');">
																			<i class="fas fa-cloud-upload-alt"></i>&nbsp;publicar
																		</a>
																	</td>
																</tr>
														<?php }
														}
													}
												}?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<a id="muestra_modal_despacho" href="#modal_despacho" data-toggle="modal" style="display:none"></a>
				-->
				
				<?php $this->mostrarContenedorModal();?>

				<?php $this->mostrarModalConfirmacion();?>

				<script>
					function preguntarPorSobreTablasFormatoImpresion(accion_a_ejecutar, id_orden_sesion) {

						$('#btnModalConfirmacionSi').prop('href', $('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion='+accion_a_ejecutar+'&id='+id_orden_sesion+'&con_sobre_tablas=1');
						$('#btnModalConfirmacionSi').prop('target', '_blank');
						$('#btnModalConfirmacionSi').prop('title', 'Imprimir Orden con Sobre Tablas');

						$('#btnModalConfirmacionNo').prop('href', $('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion='+accion_a_ejecutar+'&id='+id_orden_sesion+'&con_sobre_tablas=0');
						$('#btnModalConfirmacionNo').prop('target', '_blank');
						$('#btnModalConfirmacionNo').prop('title', 'Imprimir Orden sin Sobre Tablas');

						mostrarModalConfirmacion('¿Desea mostrar <strong>Sobre Tablas</strong> a la Orden del D&iacute;a?');
					}
					/**
					function muestraModalDespacho(periodo, reunion, sesion, fecha) {

						$('#modal_periodo').val(periodo);
						$('#modal_reunion').val(reunion);
						$('#modal_sesion').val(sesion);
						$('#modal_fecha').val(fecha);
						// Se muestra la modal
						$('#muestra_modal_despacho').click();
					}

					function publicarDespacho(documento) {

						if (confirm('¿Desea publicar el Despacho para la Orden del d'+i_acentuada+'a de Sesi'+o_acentuada+'n '+$('#modal_sesion').val()+'?')) {
							
							url = $('#url_abms').val()+'?controlador='+$('#controlador').val();
							url += '&accion=publicarDespacho';
							url += '&documento='+documento;
							url += '&periodo='+$('#modal_periodo').val();
							url += '&reunion='+$('#modal_reunion').val();
							url += '&sesion='+$('#modal_sesion').val();
							url += '&fecha='+$('#modal_fecha').val();
							
							redireccionar(url);
						}
					}
					/**/
					$('#btnModalConfirmacionSi').click(function(){
						$('#modal_confirmacion').modal('hide');
					});

					$('#btnModalConfirmacionNo').click(function(){
						$('#modal_confirmacion').modal('hide');
					});

					jQuery(document).ready(function() {
					    $('#f_fecha').val("<?=(isset($filtro['f_fecha'])) ? $filtro['f_fecha'] : '';?>");
					});
				</script>

			    <script src="<?=URL_JS;?>ordenes_sesion/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>
