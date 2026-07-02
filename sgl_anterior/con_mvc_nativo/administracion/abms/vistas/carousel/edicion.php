<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaCarouselEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'carousel';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['id'])) ? 'Edici&oacute;n' : 'Alta';
		$id_definido = (isset($datos['id'])) ? $datos['id'] : '';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : '';
		$valor_foto = (isset($datos['recurso'])) ? $datos['recurso'] : '';
		$foto = (isset($datos['recurso']) && $datos['recurso'] != '') ? $datos['recurso'] . '?v=' . date("Ymd_His") : 'no_disponible.jpg';
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
							<?=$titulo_operacion;?> del recurso del carousel
						</div>
					</div>

					<form   id="formEdicion" name="formEdicion" class="form-horizontal"
							action="<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=guardar"
							method="POST"
							enctype="multipart/form-data">

						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="pagina" name="pagina" value="<?=$valor_pagina;?>" />
						<input type="hidden" id="id" name="id" value="<?=$id_definido;?>" />
						<input  type="hidden" id="prefijo" name="prefijo"
								value="<?=(isset($datos['prefijo']) && $datos['prefijo'] != '') ? $datos['prefijo'] : date("Ymd_His");?>" />
						<input type="hidden" id="recurso" name="recurso" value="<?=$valor_foto;?>" />
						<input type="hidden" id="habilitado" name="habilitado" value="<?=(isset($datos['habilitado'])) ? $datos['habilitado'] : '';?>" />

						<div class="row my-1">
							<div class="col-12 col-md-6">
								<div class="custom-file">
								  	<input  type="file" class="custom-file-input"
								  			id="foto" name="foto" lang="es"
								  			onchange="javascript:subirEnTemporal();">

								  	<label class="custom-file-label small" for="foto" data-browse="Buscar">
								  		Seleccionar foto
								  	</label>
								</div>
								<div class="form-group row my-1">
									<div class="col-md-12 text-center">
										<?php
										if ($datos['prefijo'] != '') {
											// Si existe el directorio temporal
											if (is_dir(RUTA_DIRECTORIO_TEMPORAL)) {
												// Si pudo abrirse
												if ($handle = opendir(RUTA_DIRECTORIO_TEMPORAL)) {

													while (false !== ($file = readdir($handle))) {
														if ($file != "." && $file != ".." && $file != "index.html") {
															if (LibreriaGeneral::esAdjuntoDe($datos['prefijo'], $file)) {
																$listado_temporales[] = $file;
															}
														}
													}

													closedir($handle);

													// Si posee archivos
													if ($listado_temporales) {
														sort($listado_temporales);

														foreach ($listado_temporales as $temporal) {?>

															<img src="<?=URL_DIRECTORIO_TEMPORAL . $temporal;?>" alt="Foto temporal" class="img-thumbnail" />
						                                  	<p class="small">
					                                  			<a  class="btn btn-danger btn-sm"
					                                  				href="javascript:eliminarTemporal('<?=$temporal;?>');"
					                                  				title="Eliminar foto temporal">
																	<i class="fas fa-trash"></i>&nbsp;Eliminar foto temporal
																</a>
					                                  		</p>
						                                <?php }
													}
												}
											}
										} else {?>
											<img src="<?=URL_RECURSOS_CAROUSEL . $foto;?>" alt="Foto" class="img-thumbnail" />
											<?php if (isset($datos['recurso']) && $datos['recurso'] != '') { ?>
												<p class="small">
		                                  			<a  class="btn btn-danger btn-sm"
		                                  				href="javascript:eliminarFoto();"
		                                  				title="Eliminar foto">
														<i class="fas fa-trash"></i>&nbsp;Eliminar foto
													</a>
		                                  		</p>
	                                  		<?php } ?>
										<?php }?>
									</div>
								</div>
							</div>
							<div class="col-12 col-md-6">
								<div class="form-group row">
									<label for="enlace" class="col-sm-3 control-label small text-left text-md-right pt-2 pr-0">
										Enlace (opcional):
									</label>
									<div class="col-sm-9">
										<input  type="text" id="enlace" name="enlace"
												value="<?=(isset($datos['enlace'])) ? $datos['enlace'] : '';?>"
												class="form-control form-control-sm"
												placeholder="Ingrese el link o url del video de Youtube..." >
									</div>
								</div>
								<div class="row mt-3">
									<div class="col-12">
										<div class="alert alert-info small">
											Puede ingresar <i class="far fa-hand-point-up"></i> un enlace a un recurso determinado o la url de un video de Youtube (la url completa, la que visualiza en el navegador, la copia y la pega aqu&iacute;)
										</div>
									</div>
								</div>
								<div class="row mt-3">
									<div class="col-sm-12 text-center">
										<!-- Botón Guardar -->
										<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n"><i class="fas fa-check-circle"></i>&nbsp;Guardar</button>
										<!-- Botón Volver -->
										<button type="button" id="btVolver" class="btn btn-info btn-sm" title="Volver al listado">
											<i class="fas fa-angle-left"></i>&nbsp;Volver
										</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<?php $this->mostrarSpinnerModal();?>

				<script src="<?=URL_JS;?>carousel/edicion.js"></script>
		  	</body>
		</html>
		<?php }
}
?>