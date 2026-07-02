<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaFichaWebEdicion extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'ficha_web';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '') {

		$valor_foto = (isset($datos['fw_foto'])) ? $datos['fw_foto'] : '';
		$foto = (isset($datos['fw_foto']) && $datos['fw_foto'] != '') ? $datos['fw_foto'] . '?v=' . date("Ymd_His") : 'avatar.png';

		$cant_autores = (isset($datos['autores'])) ? count($datos['autores']) : 0;
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
							Edici&oacute;n de Ficha Web de <?= $datos['apellido'].', '.$datos['nombre']; ?> | Legajo: <?= number_format($datos['fw_legajo'], 0, '', '.'); ?> <?= ($datos['bloque'] != '') ? ' | '.$datos['bloque'] : ''; ?>
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
						<input  type="hidden" id="pagina" name="pagina" 
								value="<?=isset($datos['pagina']) ? $datos['pagina'] : '';?>" />
						
						<input type="hidden" name="f_legajo" value="<?= (isset($_SESSION['f_fichas_web']['f_legajo'])) ? $_SESSION['f_fichas_web']['f_legajo'] : ''; ?>" />
						
						<input type="hidden" name="f_apellido_y_nombre" value="<?= (isset($_SESSION['f_fichas_web']['f_apellido_y_nombre'])) ? $_SESSION['f_fichas_web']['f_apellido_y_nombre'] : ''; ?>" />
						
						<input type="hidden" name="f_activos" value="<?= (isset($_SESSION['f_fichas_web']['f_activos'])) ? $_SESSION['f_fichas_web']['f_activos'] : ''; ?>" />
						
						<!-- Legajo -->
						<input type="hidden" id="fw_legajo" name="fw_legajo" value="<?= $datos['fw_legajo']; ?>" />

						<!-- Nombre de la foto -->
						<input type="hidden" id="fw_foto" name="fw_foto" value="<?= $valor_foto; ?>" />

						<input  type="hidden" id="prefijo" name="prefijo"
								value="<?=(isset($datos['prefijo']) && $datos['prefijo'] != '') ? $datos['prefijo'] : date("Ymd_His");?>" />
						
						<div class="row my-1">
							<div class="col-12 col-md-8">
								<div class="form-group row mt-1">
									<label for="g_titulo" class="col-sm-4 control-label small text-left text-md-right pt-1 text-info font-weight-bold">Funci&oacute;n</label>
									<div class="col-sm-8">
										<select id="fw_funcion" name="fw_funcion" class="form-control form-control-sm">
											<option value="0">Concejal</option>
											<option value="1">Presidente</option>
											<option value="2">Vicepresidente 1ro.</option>
											<option value="3">Vicepresidente 2do.</option>
											<option value="4">Secretario</option>
											<option value="5">Subsecretario</option>
										</select>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label class="col-sm-4 control-label small text-left text-md-right pt-1 text-info font-weight-bold">Per&iacute;odo de mandato</label>
									<div class="col-sm-2">
										<input  id="fw_anio_inicio" name="fw_anio_inicio"
												class="form-control form-control-sm"
												value="<?=$datos['fw_anio_inicio'];?>"
												onKeyPress="return soloEnteros(event)" />
									</div>
									<div class="col-sm-1">---</div>
									<div class="col-sm-2">
										<input  id="fw_anio_fin" name="fw_anio_fin"
												class="form-control form-control-sm"
												value="<?=$datos['fw_anio_fin'];?>"
												onKeyPress="return soloEnteros(event)" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fw_es_presidente_bloque" class="col-sm-4 control-label small text-left text-md-right pt-1">
										Presidente de Bloque
									</label>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input  type="checkbox" id="fw_es_presidente_bloque" name="fw_es_presidente_bloque" 
											value="<?= (isset($datos['fw_es_presidente_bloque'])) ? $datos['fw_es_presidente_bloque'] : 0; ?>"
											<?=(isset($datos['fw_es_presidente_bloque']) && $datos['fw_es_presidente_bloque'] == 1) ? 'checked' : '';?> />
								</div>
								<div class="form-group row mt-1">
									<label for="fw_profesion" class="col-sm-4 control-label small text-left text-md-right pt-1">
										Profesi&oacute;n
									</label>
									<div class="col-sm-8">
										<input  type="text" id="fw_profesion" name="fw_profesion"
												value="<?=(isset($datos['fw_profesion'])) ? $this->reemplazarComillaDoble($datos['fw_profesion']) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fw_mail" class="col-sm-4 control-label small text-left text-md-right pt-1">
										Mail
									</label>
									<div class="col-sm-8">
										<div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            </div>
											<input  type="text" id="fw_mail" name="fw_mail"
													value="<?=(isset($datos['fw_mail'])) ? $this->reemplazarComillaDoble($datos['fw_mail']) : '';?>"
													class="form-control form-control-sm" >
										</div>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fw_telefono" class="col-sm-4 control-label small text-left text-md-right pt-1">
										Tel&eacute;fono
									</label>
									<div class="col-sm-8">
										<div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            </div>
                                            <input  type="text" id="fw_telefono" name="fw_telefono" 
                                            		value="<?=(isset($datos['fw_telefono'])) ? $datos['fw_telefono'] : '';?>"
                                            		class="form-control form-control-sm" />
                                        </div>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fw_facebook" class="col-sm-4 control-label small text-left text-md-right pt-1">
										Facebook
									</label>
									<div class="col-sm-8">
										<div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fab fa-facebook-square"></i></span>
                                            </div>
											<input  type="text" id="fw_facebook" name="fw_facebook"
													value="<?=(isset($datos['fw_facebook'])) ? $this->reemplazarComillaDoble($datos['fw_facebook']) : '';?>"
													class="form-control form-control-sm" >
										</div>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fw_instagram" class="col-sm-4 control-label small text-left text-md-right pt-1">
										Instagram
									</label>
									<div class="col-sm-8">
										<div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                            </div>
											<input  type="text" id="fw_instagram" name="fw_instagram"
													value="<?=(isset($datos['fw_instagram'])) ? $this->reemplazarComillaDoble($datos['fw_instagram']) : '';?>"
													class="form-control form-control-sm" >
										</div>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fw_twitter" class="col-sm-4 control-label small text-left text-md-right pt-1">
										Twitter
									</label>
									<div class="col-sm-8">
										<div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                            </div>
											<input  type="text" id="fw_twitter" name="fw_twitter"
													value="<?=(isset($datos['fw_twitter'])) ? $this->reemplazarComillaDoble($datos['fw_twitter']) : '';?>"
													class="form-control form-control-sm" >
										</div>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fw_sitio_web" class="col-sm-4 control-label small text-left text-md-right pt-1">
										Sitio Web
									</label>
									<div class="col-sm-8">
										<input  type="text" id="fw_sitio_web" name="fw_sitio_web"
												value="<?=(isset($datos['fw_sitio_web'])) ? $this->reemplazarComillaDoble($datos['fw_sitio_web']) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fw_autor_codigo" class="col-sm-4 control-label small text-left text-md-right pt-1">
										C&oacute;digo de Autor
									</label>
									<div class="col-sm-8">
										<select id="fw_autor_codigo" name="fw_autor_codigo" class="form-control form-control-sm">
											<option value="0">Concejal</option>
											<?php for ($i=0; $i < $cant_autores; $i++) { $autor = &$datos['autores'][$i]; ?>
												<option value="<?=$autor['codigo_grp'];?>">
													<?=$autor['tipo_grp'].' - '.$autor['codigo_grp'].' - '.$autor['descripcion_grp'];?>
												</option>
											<?php } ?>
										</select>
									</div>
								</div>

								<div class="row my-3">
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
							<div class="col-12 col-md-4">
								<div class="row mt-1">
									<div class="col-md-12 text-center">
										<img src="<?=URL_FOTOS_FICHAS_AUTORIDADES . $foto;?>" alt="Foto" 
											 class="img-thumbnail" />
									</div>
								</div>
								<div class="row my-3">
									<div class="col-md-12 text-center">
										<!-- Botón para mostrar la modal del recorte de la foto -->
										<button type="button" class="btn btn-success btn-sm" 
												data-toggle="modal" data-target="#recorteFotoModal">
											<i class="fas fa-cloud-upload-alt"></i>&nbsp;Buscar foto
										</button>
									</div>
								</div>
							</div>
						</div>
					</form>

					<!-- Modal para el recorte de la foto -->
					<div class="modal fade" id="recorteFotoModal" tabindex="-1" role="dialog">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header p-1">
									<h5 class="modal-title">Carga de la foto para la Ficha Web</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body p-0">
									<iframe width="100%" height="450"
									  		src="<?=URL_LIBRERIAS;?>recorte_foto_ficha_web.php?codigo=<?= $datos['fw_legajo']; ?>&nombre_foto=<?= $foto; ?>" ></iframe>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<?php $this->mostrarSpinnerModal();?>

				<script>
					// Se setea la función
					$('#fw_funcion').val('<?= (isset($datos['fw_funcion'])) ? $datos['fw_funcion'] : 0; ?>');

					// Se setea el código de Autor
					$('#fw_autor_codigo').val('<?=(isset($datos['fw_autor_codigo'])) ? $datos['fw_autor_codigo'] : 0; ?>');
				</script>

				<script src="<?=URL_JS;?>ficha_web/edicion.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>