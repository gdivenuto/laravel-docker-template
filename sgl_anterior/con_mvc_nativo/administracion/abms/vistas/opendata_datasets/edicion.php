<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaOpendataDatasetsEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'opendata_datasets';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $catalogos = null, $publicadores = null, $mensaje = '', $tipo_mensaje = '') {

		$cant_catalogos = (isset($catalogos)) ? count($catalogos) : 0;
    	$cant_publicadores = (isset($publicadores)) ? count($publicadores) : 0;
    	$cant_recursos = (isset($datos['recursos'])) ? count($datos['recursos']) : 0;

		$titulo_operacion = (isset($datos['id'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['id'])) ? 'modificar' : 'insertar';
		$id_dataset = (isset($datos['id'])) ? $datos['id'] : '';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1;
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
							<?=$titulo_operacion;?> del DataSet
						</div>
					</div>

					<form   id="formEdicion" name="formEdicion" class="form-horizontal" 
							action="<?=URL_ABMS;?>?controlador=<?= $this->controlador; ?>&accion=guardar" 
							method="POST" 
							enctype="multipart/form-data">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="pagina" name="pagina" value="<?=$valor_pagina;?>" />
						<input type="hidden" id="id" name="id" value="<?=$id_dataset;?>" />
						<input type="hidden" id="prefijo" name="prefijo" value="<?= (isset($datos['prefijo'])) ? $datos['prefijo'] : date("Ymd_His"); ?>" />

						<div class="row my-1">	
							<!-- Info del DataSet -->
							<div class="col-md-7">
								<div class="form-group row mt-1">
									<label for="titulo" class="col-sm-3 control-label small text-right pt-1">T&iacute;tulo</label>
									<div class="col-sm-9">
										<input  type="text" id="titulo" name="titulo" 
												value="<?=(isset($datos['titulo'])) ? htmlspecialchars($datos['titulo']) : '';?>" 
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fecha_emitido" class="col-sm-3 control-label small text-right pt-1">
										Fecha Emisi&oacute;n
									</label>
									<div class="col-sm-8">
										<input id="fecha_emitido" name="fecha_emitido" class="form-control" width="145" value="<?php echo (isset($datos['fecha_emitido'])) ? $this->formatearFecha($datos['fecha_emitido']) : date("d/m/Y"); ?>" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fecha_modificado" class="col-sm-3 control-label small text-right pt-1">
										Fecha Modificaci&oacute;n
									</label>
									<div class="col-sm-8">
										<input id="fecha_modificado" name="fecha_modificado" class="form-control" width="145" value="<?php echo (isset($datos['fecha_modificado'])) ? $this->formatearFecha($datos['fecha_modificado']) : ''; ?>" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="id_catalogo" class="col-sm-3 control-label small text-right pt-1">Cat&aacute;logo</label>
									<div class="col-sm-4">
										<select id="id_catalogo" name="id_catalogo" class="form-control form-control-sm">
											<option value="0">---</option>
											<?php for ($i = 0; $i < $cant_catalogos; $i++) {?>
												<option value="<?=$catalogos[$i]['id'];?>" >
													<?=$catalogos[$i]['titulo'];?>
												</option>
											<?php }?>
										</select>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="id_publicador" class="col-sm-3 control-label small text-right pt-1">Publicador</label>
									<div class="col-sm-4">
										<select id="id_publicador" name="id_publicador" class="form-control form-control-sm">
											<option value="0">---</option>
											<?php for ($i = 0; $i < $cant_publicadores; $i++) {?>
												<option value="<?=$publicadores[$i]['id'];?>" >
													<?=$publicadores[$i]['titulo'];?>
												</option>
											<?php }?>
										</select>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="identificador" class="col-sm-3 control-label small text-right pt-1">Identificador</label>
									<div class="col-sm-8">
										<input  type="text" id="identificador" name="identificador" 
												value="<?=(isset($datos['identificador'])) ? htmlspecialchars($datos['identificador']) : '';?>" 
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="palabras_clave" class="col-sm-3 control-label small text-right pt-1">Palabras clave</label>
									<div class="col-sm-8">
										<input  type="text" id="palabras_clave" name="palabras_clave" 
												value="<?=(isset($datos['palabras_clave'])) ? htmlspecialchars($datos['palabras_clave']) : '';?>" 
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="lenguaje" class="col-sm-3 control-label small text-right pt-1">Lenguaje</label>
									<div class="col-sm-8">
										<input  type="text" id="lenguaje" name="lenguaje" 
												value="<?=(isset($datos['lenguaje'])) ? htmlspecialchars($datos['lenguaje']) : '';?>" 
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="frecuencia" class="col-sm-3 control-label small text-right pt-1">Frecuencia</label>
									<div class="col-sm-4">
										<select id="frecuencia" name="frecuencia" class="form-control form-control-sm">
											<option value="0">---</option>
											<option value="1">Semanal</option>
											<option value="2">Quincenal</option>
											<option value="3">Mensual</option>
											<option value="4">Bimestral</option>
											<option value="5">Trimestral</option>
											<option value="6">Semestral</option>
											<option value="7">Anual</option>
										</select>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="url" class="col-sm-3 control-label small text-right pt-1">URL</label>
									<div class="col-sm-8">
										<input  type="text" id="url" name="url" 
												value="<?=(isset($datos['url'])) ? htmlspecialchars($datos['url']) : '';?>" 
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="licencia" class="col-sm-3 control-label small text-right pt-1">Licencia</label>
									<div class="col-sm-8">
										<input  type="text" id="licencia" name="licencia" 
												value="<?=(isset($datos['licencia'])) ? htmlspecialchars($datos['licencia']) : '';?>" 
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fuente" class="col-sm-3 control-label small text-right pt-1">Fuente</label>
									<div class="col-sm-8">
										<input  type="text" id="fuente" name="fuente" 
												value="<?=(isset($datos['fuente'])) ? htmlspecialchars($datos['fuente']) : '';?>" 
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="nivel_acceso" class="col-sm-3 control-label small text-right pt-1">Nivel Acceso</label>
									<div class="col-sm-4">
										<select id="nivel_acceso" name="nivel_acceso" class="form-control form-control-sm">
											<option value="0">---</option>
											<option value="1">P&uacute;blico</option>
											<option value="2">Privado</option>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<div class="input-group input-group-sm ml-1">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-clipboard"></i></span>
										</div>
										<textarea   id="descripcion" name="descripcion"
													class="form-control" rows="5"
													placeholder="Ingresa aqu&iacute; la descripci&oacute;n..."
													aria-label="Texto"><?=($datos['descripcion'] != '') ? htmlspecialchars($datos['descripcion']) : '';?></textarea>
									</div>
								</div>

								<!-- Botones Guardar y Cancelar -->
								<div class="row mt-3">
									<div class="col-sm-12 text-center">
										<!-- Botón Guardar -->
										<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n"><i class="fas fa-check-circle"></i>&nbsp;Guardar</button>
										<!-- Botón Cancelar -->
										<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Cancelar operaci&oacute;n"><i class="fas fa-angle-left"></i>&nbsp;Cancelar</button>
									</div>
								</div>
							</div>
							<!-- Recursos del DataSet -->
							<div class="col-md-5">
								<!-- Existentes -->
								<div class="row mt-3">
									<div class="col-md-12">
										<?php // Si existe
										if ($id_dataset != '' && $id_dataset != 0) {

											// Nombre del directorio de recursos correspondiente al Dataset
											$dir_recursos_dataset = RUTA_DATASET_RECURSOS.$id_dataset."/";

											if (is_dir($dir_recursos_dataset)) {
												// Si pudo abrirse el directorio de recursos del dataset respectivo
												if ($handle = opendir($dir_recursos_dataset)) {

													// Si posee recursos
													if (isset($datos['recursos'])) { ?>

														<p class="small">Recursos que posee:</p>
														<ul>
														<?php
														// Por cada recurso que posee
														foreach ($datos['recursos'] as $recurso) { ?>
						                                  	<li>
						                                  		<p class="small">
						                                  			<a  title="Eliminar recurso"
																		href="javascript:if (confirm('¿Desea eliminar el recurso <?= $recurso['titulo']; ?>?')) {
																			redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminarRecurso&id=<?= $recurso['id'];?>&id_dataset=<?= $id_dataset;?>&nombre_adjunto=<?= $recurso['titulo'];?>');
																		 };">
																		<i class="fas fa-trash"></i>&nbsp;Eliminar
																	</a>
																	&nbsp;&nbsp;&nbsp;
						                                  			<a  title="Ver recurso"
						                                  				href="<?= $recurso['url_descarga']; ?>"
						                                  				target="_blank">
						                                  				<?= $recurso['titulo']; ?>
						                                  			</a>
						                                  		</p>
						                                  	</li>
						                                <?php }?>
														</ul>
													<?php
													}
												}
											}
										}
										?>
									</div>
								</div>
								<!-- Botón para Subir los recursos -->
								<div class="row my-1">
									<div class="col-sm-5 offset-sm-6 custom-file">
									  	<input  type="file" class="custom-file-input" 
									  			id="adjuntos" name="adjuntos[]" multiple="multiple" lang="es"
									  			onchange="javascript:subirEnTemporal();">

									  	<label class="custom-file-label small" for="adjuntos" data-browse="Buscar">
									  		<i class="far fa-file"></i>&nbsp;Subir recursos
									  	</label>
									</div>
								</div>
								<!-- Archivos Temporales, para su carga final al guardar -->
								<div class="row mt-1">
									<div class="col-md-12">
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
													if ($listado_temporales) { sort($listado_temporales); ?>

														<p class="small">Archivos temporales para cargar:</p>
														<ul>
														<?php
														// Se recorre el directorio respectivo y por cada archivo que contiene se muestra su enlace para visualizarlo.
														foreach ($listado_temporales as $temporal) { ?>
						                                  	<li>
						                                  		<p class="small">
						                                  			<a  title="Eliminar temporal" 
						                                  				href="javascript:eliminarTemporal('<?=$temporal;?>');"
						                                  				title="Eliminar temporal">
																		<i class="fas fa-trash"></i>&nbsp;Eliminar
																	</a>
						                                  			&nbsp;&nbsp;&nbsp;
						                                  			<a  title="Ver archivo a subir" 
						                                  				href="<?= URL_DIRECTORIO_TEMPORAL . $temporal; ?>" 
						                                  				target="_blank">
						                                  				<?= str_replace($datos['prefijo'] . '__', '', $temporal); ?>
						                                  			</a>
						                                  		</p>
						                                  	</li>
							                            <?php }?>
														</ul>
														<?php
													}
												}
											}
										}
										?>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<?php $this->mostrarSpinnerModal();?>

				<script>
					$('#id_catalogo').val('<?=(isset($datos['id_catalogo'])) ? $datos['id_catalogo'] : 0;?>');

					$('#id_publicador').val('<?=(isset($datos['id_publicador'])) ? $datos['id_publicador'] : 0;?>');

					$('#frecuencia').val('<?=(isset($datos['frecuencia'])) ? $datos['frecuencia'] : 0;?>');

					$('#nivel_acceso').val('<?=(isset($datos['nivel_acceso'])) ? $datos['nivel_acceso'] : 0;?>');
				</script>

				<script src="<?=URL_JS;?>opendata_datasets/edicion.js"></script>
		  	</body>
		</html>
		<?php }
}
?>