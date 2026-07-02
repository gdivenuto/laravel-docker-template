<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaGacetillaEdicion extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'gacetillas';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['g_codigo'])) ? 'Edici&oacute;n' : 'Alta';
		$id_definido = (isset($datos['g_codigo'])) ? $datos['g_codigo'] : '';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : '';
		$valor_foto = (isset($datos['g_foto'])) ? $datos['g_foto'] : '';
		$foto = (isset($datos['g_foto']) && $datos['g_foto'] != '') ? $datos['g_foto'] . '?v=' . date("Ymd_His") : 'no_disponible.jpg';
		
		$cant_fotos_secundarias = (isset($datos['fotos'])) ? count($datos['fotos']) : 0;
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
							<?=$titulo_operacion;?> de la Gacetilla
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
						<input type="hidden" id="g_codigo" name="g_codigo" value="<?=$id_definido;?>" />

						<input  type="hidden" id="g_enviar_por_mail" name="g_enviar_por_mail" 
								value="<?=(isset($datos['g_enviar_por_mail'])) ? $datos['g_enviar_por_mail'] : '';?>">

						<input  type="hidden" id="prefijo" name="prefijo"
								value="<?=(isset($datos['prefijo']) && $datos['prefijo'] != '') ? $datos['prefijo'] : date("Ymd_His");?>" />
						
						<input type="hidden" id="g_foto" name="g_foto" value="<?=$valor_foto;?>" />
						<input type="hidden" id="habilitado" name="habilitado" value="<?=(isset($datos['habilitado'])) ? $datos['habilitado'] : '';?>" />

						<div class="row my-1">
							<div class="col-12 col-md-7">
								<div class="form-group row mt-1">
									<label for="g_titulo" class="col-sm-1 control-label small text-left text-md-right pt-1 text-info font-weight-bold">T&iacute;tulo</label>
									<div class="col-sm-11">
										<input  type="text" id="g_titulo" name="g_titulo"
												value="<?=(isset($datos['g_titulo'])) ? $datos['g_titulo'] : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row">
									<label for="g_fecha" class="col-sm-1 control-label small text-left text-md-right pt-1 text-info font-weight-bold">Fecha</label>
									<div class="col-sm-3">
										<input  id="g_fecha" name="g_fecha"
												class="form-control" width="145"
												value="<?=(isset($datos['g_fecha'])) ? $this->formatearFecha($datos['g_fecha']) : date("d/m/Y");?>" />
									</div>
									<label for="g_tipo" class="col-sm-1 control-label small text-left text-md-right pt-1">Tipo</label>
									<div class="col-sm-3">
										<select id="g_tipo" name="g_tipo" class="form-control form-control-sm">
											<option value="A">Anuncio</option>
											<option value="E">Escuela</option>
											<option value="L">Legislativa</option>
											<option value="P">Protocolar</option>
										</select>
									</div>
									<label for="g_acto" class="col-sm-1 control-label small text-left text-md-right pt-1">Acto</label>
									<div class="col-sm-3">
										<select id="g_acto" name="g_acto" class="form-control form-control-sm">
											<option value="">---</option>
											<option value="Cf">Conferencias</option>
											<option value="Cv">Convenios</option>
											<option value="SAP">De Sesiones y Actividades de la Presidencia</option>
											<option value="J">Jornadas</option>
											<option value="Rec">Reconocimientos</option>
											<option value="Reu">Reuniones</option>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<div class="input-group input-group-sm ml-1">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-clipboard"></i></span>
										</div>
										<textarea   id="g_texto" name="g_texto"
													class="form-control" rows="15"
													placeholder="Ingresa aqu&iacute; el texto..."
													aria-label="Texto"><?=($datos['g_texto'] != '') ? htmlentities($datos['g_texto']) : '';?></textarea>
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
							<?php
							// Si ya posee una foto principal, se habilita la carga de fotos restantes
							if ( $datos['g_codigo'] != '' ) {  ?>
								<div class="col-12 col-md-5">
									<div class="row mt-1">
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
												<img src="<?=URL_FOTOS_GACETILLAS . $foto;?>" alt="Foto" class="img-thumbnail" />
											<?php }?>
										</div>
									</div>
									<div class="row my-3">
										<div class="col-md-12 text-center">
											<!-- Botón para mostrar la modal del recorte de la foto -->
											<button type="button" class="btn btn-success btn-sm" 
													data-toggle="modal" data-target="#recorteFotoModal">
												<i class="fas fa-cloud-upload-alt"></i>&nbsp;Buscar foto principal
											</button>
										</div>
									</div>
									<div class="custom-file">
									  	<input type="file" class="custom-file-input" id="fotos" name="fotos[]" multiple="multiple" lang="es" accept="image/*" >
									  	<label class="custom-file-label small" for="fotos" data-browse="Buscar">
									  		<i class="far fa-file"></i>&nbsp;Subir m&aacute;s fotos
									  	</label>
									</div>
								</div>
							<?php }?>
						</div>
					</form>
					<?php if ($cant_fotos_secundarias > 0) { // Si posee fotos secundarias ?>
						<div id="contenedor_fotos_secundarias" class="row">
							<?php for ($i=0; $i < $cant_fotos_secundarias; $i++) { 
								$foto_secundaria = &$datos['fotos'][$i]; ?>
								
								<div class="col-12 col-md-3 mt-3 text-center">
									<img src="<?= URL_FOTOS_GACETILLAS.$foto_secundaria['fsg_nombre_foto']; ?>" alt="..." class="img-thumbnail" />
									<p class="mt-1">
										<a  class="btn btn-danger btn-sm"
                              				href="javascript:eliminarFotoSecundaria(<?=$foto_secundaria['fsg_id'];?>);"
                              				title="Eliminar foto">
											<i class="fas fa-trash"></i>&nbsp;Eliminar foto
										</a>
                              		</p>
								</div>
							<?php } ?>
						</div>
					<?php } ?>

					<!-- Modal para el recorte de la foto -->
					<div class="modal fade" id="recorteFotoModal" tabindex="-1" role="dialog">
					  <div class="modal-dialog modal-lg" role="document">
					      <div class="modal-content">
					          <div class="modal-header p-1">
					              <h5 class="modal-title">Carga de la foto de la Gacetilla</h5>
					              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					                  <span aria-hidden="true">&times;</span>
					              </button>
					          </div>
					          <div class="modal-body p-0">
					              <iframe width="100%" height="450"
					              		  src="<?=URL_LIBRERIAS;?>recorte_foto_gacetilla.php?codigo=<?= $id_definido; ?>&nombre_foto=<?= $foto; ?>" ></iframe>
					          </div>
					      </div>
					  </div>
					</div>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<?php $this->mostrarSpinnerModal();?>

				<script>
					$('#g_tipo').val('<?=(isset($datos['g_tipo'])) ? $datos['g_tipo'] : 'P';?>');

					$('#g_acto').val('<?=(isset($datos['g_acto'])) ? $datos['g_acto'] : '';?>');
				</script>

				<script src="<?=URL_JS;?>gacetillas/edicion.js"></script>
		  	</body>
		</html>
		<?php }
}
?>