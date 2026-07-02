<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaNotificacionesEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'notificaciones';
		// Se crea una instancia del modelo
		$this->modelo = new notificacionesModel();
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $listas = null, $mensaje = '', $tipo_mensaje = '') {

		$cantidad_listas = (isset($listas)) ? count($listas) : 0;

		$listas_asignadas = explode(",", $datos['n_phplist_ids_destino']);

		// Si se trata de una Fe de Erratas
		if (isset($datos['es_fe_erratas']) && $datos['es_fe_erratas'] == 1) {

			$titulo = 'Editar Fe de erratas de la Notificaci&oacute;n';

			$asunto = '[Fe de erratas] ' . htmlspecialchars($datos['n_asunto']);
			// Se deja vacío para generar uno nuevo al guardar la Fe de erratas
			$id_notificacion = '';
		} else {
			$titulo = (isset($datos['n_id'])) ? 'Edici&oacute;n de Notificaci&oacute;n Interna' : 'Alta de Notificaci&oacute;n Interna';
			// o de una Notificación
			$asunto = (isset($datos['n_asunto'])) ? htmlspecialchars($datos['n_asunto']) : '';
			// Se setea el Id según su valor
			$id_notificacion = (isset($datos['n_id'])) ? $datos['n_id'] : '';
		}

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
							<?=$titulo;?>
						</div>
					</div>

					<form   id="formEdicion" name="formEdicion" class="form-horizontal"
							action="<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=guardar"
							method="POST"
							enctype="multipart/form-data">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="pagina" name="pagina" value="<?=$valor_pagina;?>" />
						<input type="hidden" id="n_id" name="n_id" value="<?=$id_notificacion;?>" />
						<input type="hidden" id="prefijo" name="prefijo" value="<?=(isset($datos['prefijo'])) ? $datos['prefijo'] : date("Ymd_His");?>" />
						<input type="hidden" id="n_id_mail" name="n_id_mail" value="<?=(isset($datos['n_id_mail'])) ? $datos['n_id_mail'] : 0;?>" />
						<input type="hidden" id="es_fe_erratas" name="es_fe_erratas" value="<?=(isset($datos['es_fe_erratas'])) ? $datos['es_fe_erratas'] : 0;?>" />

						<div class="row my-1">
							<!-- Info -->
							<div class="col-md-6">
								<div class="form-group row mt-1">
									<label for="n_fecha" class="col-sm-2 control-label small text-right pt-1">
										Fecha
									</label>
									<div class="col-sm-10">
										<input id="n_fecha" name="n_fecha" class="form-control" width="145" value="<?=(isset($datos['n_fecha'])) ? $this->formatearFecha($datos['n_fecha']) : date("d/m/Y");?>" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="n_asunto" class="col-sm-2 control-label small text-right pt-1">Asunto</label>
									<div class="col-sm-10">
										<input  type="text" id="n_asunto" name="n_asunto"
												value="<?=$asunto;?>"
												class="form-control form-control-sm" >
									</div>
								</div>

								<div class="form-group row mt-1">
									<label for="n_id_grupo_destino" class="col-sm-2 control-label small text-right pt-1">Grupo</label>
									<div class="col-sm-10">
										<select id="n_id_grupo_destino" name="n_id_grupo_destino" class="form-control form-control-sm">
											<option value="0">seleccione</option>
											<?php // Se obtiene la lista de grupos de distribución
											$grupos = $this->modelo->obtenerListaGruposDistribucion();

											foreach ($grupos as $ng) {
												echo '<option value="' . $ng['id'] . '">' . $ng['descripcion'] . '</option>';
											}?>
										</select>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label class="col-sm-2 control-label small text-right pt-1">Lista</label>
									<div class="col-sm-10 overflow-auto alto_300">
										<?php if ($cantidad_listas > 0) { ?>
											<table class="table table-hover table-sm small">
												<tbody>
													<?php for ($i = 0; $i < $cantidad_listas; $i++) { $lista = &$listas[$i];?>
													<tr>
														<td width="20">
															<input  type="checkbox"
																	name="listas_destino[]"
																	class="listas_destino"
																	value="<?=$lista['id'];?>"
																	<?=(isset($datos['n_phplist_ids_destino']) &&
																		$datos['n_phplist_ids_destino'] != null &&
																		(in_array($lista['id'], $listas_asignadas) != false)) ? 'checked' : '';?> />
														</td>
														<td><?=$lista['name'];?></td>
													</tr>
													<?php }?>
												</tbody>
											</table>
										<?php }?>
									</div>
								</div>
							</div>
							<!-- Mensaje + Archivos Adjuntos + Botones Guardar/Cancelar -->
							<div class="col-md-6">

								<div class="form-group row mt-1">
									<div class="col-sm-12">
										<div class="input-group input-group-sm">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fas fa-clipboard"></i></span>
											</div>
											<textarea   id="n_mensaje" name="n_mensaje"
														class="form-control" rows="15"
														placeholder="Ingresa aqu&iacute; el mensaje de la Notificaci&oacute;n..."
														aria-label="Texto"><?=($datos['n_mensaje'] != '') ? htmlspecialchars($datos['n_mensaje']) : '';?></textarea>
										</div>
									</div>
								</div>

								<!-- Archivos Existentes -->
								<div class="row mt-3">
									<div class="col-md-12">
										<?php // Si existe la Notificación
		if ($id_notificacion != '' && $id_notificacion != 0) {

			// Nombre del directorio de adjuntos
			$dir_adjuntos = RUTA_ADJUNTOS_NOTIFICACIONES;

			if (!empty($dir_adjuntos)) {
				// Si pudo abrirse el directorio de los Adjuntos de la Notificación respectiva
				if ($handle = opendir($dir_adjuntos)) {

					while (false !== ($file = readdir($handle))) {
						// Si el archivo es un adjunto de la Notificación
						if ($file != "." && $file != ".." && $file != "index.html") {
							if (LibreriaGeneral::esAdjuntoDe($id_notificacion, $file)) {
								$listado_adjuntos[] = $file; // Se agrega al listado a mostrar
							}
						}
					}
					closedir($handle);
					// Si posee archivos la notificación
					if ($listado_adjuntos) {sort($listado_adjuntos);?>

										<p class="small">&nbsp;Archivos adjuntos:</p>
										<ul>
										<?php // Se recorre el directorio respectivo y por cada archivo que contiene se muestra su enlace
						foreach ($listado_adjuntos as $adjunto) {?>
		                                  	<li>
		                                  		<p class="small">
		                                  			<a  title="Eliminar adjunto"
														href="javascript:if (confirm('¿Desea eliminar el adjunto?')) {
															redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminarAdjunto&id_notificacion=<?=$id_notificacion;?>&nombre_adjunto=<?=$adjunto;?>');
														 };">
														<i class="fas fa-trash"></i>&nbsp;Eliminar
													</a>
													&nbsp;&nbsp;&nbsp;
		                                  			<a  title="ver Adjunto"
		                                  				href="<?=URL_ADJUNTOS_NOTIFICACIONES . $adjunto;?>"
		                                  				target="_blank">
		                                  				<?=str_replace($id_notificacion . '__', '', $adjunto);?>
		                                  			</a>
		                                  		</p>
		                                  	</li>
		                                <?php }?>
										</ul>
										<?php }}}}?>
									</div>
								</div>
								<!-- Botón para Subir los recursos -->
								<div class="row my-1">
									<div class="col-sm-5 offset-sm-6 custom-file">
									  	<input  type="file" class="custom-file-input"
									  			id="adjuntos" name="adjuntos[]" multiple="multiple" lang="es"
									  			onchange="javascript:subirEnTemporal();">

									  	<label class="custom-file-label small" for="adjuntos" data-browse="Buscar">
									  		<i class="far fa-file"></i>&nbsp;Subir adjuntos
									  	</label>
									</div>
								</div>
								<!-- Archivos Temporales, para su carga final al guardar -->
								<div class="row mt-1">
									<div class="col-md-12">
										<?php
										if (isset($datos['prefijo']) && $datos['prefijo'] != '') {
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
													if ($listado_temporales) {sort($listado_temporales);?>

														<p class="small">Archivos temporales para cargar:</p>
														<ul>
														<?php
														// Se recorre el directorio respectivo y 
														// por cada archivo que contiene se muestra su enlace para visualizarlo.
														foreach ($listado_temporales as $temporal) {?>
						                                  	<li>
						                                  		<p class="small">
						                                  			<a  title="Eliminar temporal"
						                                  				href="javascript:eliminarTemporal('<?=$temporal;?>');"
						                                  				title="Eliminar temporal">
																		<i class="fas fa-trash"></i>&nbsp;Eliminar
																	</a>
						                                  			&nbsp;&nbsp;&nbsp;
						                                  			<a  title="Ver archivo a subir"
						                                  				href="<?=URL_DIRECTORIO_TEMPORAL . $temporal;?>"
						                                  				target="_blank">
						                                  				<?=str_replace($datos['prefijo'] . '__', '', $temporal);?>
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
						</div>
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<?php $this->mostrarSpinnerModal();?>

				<script>
					// Se setea el Grupo destino
					$('#n_id_grupo_destino').val('<?=(isset($datos['n_id_grupo_destino'])) ? $datos['n_id_grupo_destino'] : '0';?>');
				</script>

				<script src="<?=URL_JS;?>notificaciones/edicion.js"></script>
		  	</body>
		</html>
		<?php }
}
?>
