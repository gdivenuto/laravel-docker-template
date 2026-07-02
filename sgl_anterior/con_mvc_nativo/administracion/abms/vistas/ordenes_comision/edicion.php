<?php
if (!isset($_SESSION))
	session_start();

class VistaOrdenComisionEdicion extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'ordenes_comision';
		$this->modelo = new ordenes_comisionModel();
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos
	 * @param  array $filtro
	 * @param  string $mensaje
	 * @param  string $tipo_mensaje
	 */
	public function mostrar($datos = null, $filtro = '', $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['id'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['id'])) ? 'modificar' : 'insertar';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : '';

		$cant_comisiones = (isset($datos['comisiones_internas'])) ? count($datos['comisiones_internas']) : 0;
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
				<?php $this->mostrarContenidoHead();?>
			</head>
		  	<body>
			    <div class="container-fluid p-0 px-3">

					<?php $this->mostrarMenuPrincipal();?>

					<form id="formEdicion" name="formEdicion" class="pt-1" action="<?=URL_ABMS;?>" method="POST">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="<?=$operacion;?>" />
						<input type="hidden" id="pagina" name="pagina" value="<?=$valor_pagina;?>" />
						<input 
							type="hidden" id="id" name="id"
							value="<?=(isset($datos['id'])) ? $datos['id'] : '';?>"
						/>
						
						<div class="row">
							<div class="col-12 col-sm-10 fuente_titulos bg-dark text-white small py-1 titulo_entidad">
								<?=$titulo_operacion;?> de la Orden del D&iacute;a de Comisi&oacute;n
							</div>
							<div class="col-12 col-sm-2 text-center text-md-left">

							<?php if (isset($datos['id'])) { ?>

								<button type="button" id="btVolver" class="btn btn-info btn-sm mt-1 mt-sm-0 px-3" title="Volver al listado">
									<i class="fas fa-angle-left"></i>&nbsp;Volver al listado
								</button>

							<?php } else { ?>

								<button type="button" id="btCrear" class="btn btn-success btn-sm mt-1 mt-sm-0" title="Crear Orden de Comisi&oacute;n">
									<i class="fas fa-check-circle"></i>&nbsp;Crear
								</button>
								<button type="button" id="btCancelar" class="btn btn-info btn-sm mt-1 mt-sm-0" title="Cancelar operaci&oacute;n">
									<i class="fas fa-angle-left"></i>&nbsp;Cancelar
								</button>

							<?php } ?>

							</div>
						</div>
						<!-- Cabecera -->
						<div class="form-group row pt-1">

							<?php if (! isset($datos['id'])) { // Si se crea la comisión ?>

								<div class="col-sm-2 pt-1 px-2 small">
									<strong>¿Es una Comisi&oacute;n conjunta?</strong>&nbsp;
									<input type="checkbox" id="es_conjunta" name="es_conjunta" value="1" />
								</div>
								<div id="contenedor_combo_comisiones" class="col-sm-6 pl-0 d-inline">
									<select id="codigo_comision" name="codigo_comision" class="form-control form-control-sm" >
										<option value="0">Comisi&oacute;n...</option>
										<?php for ($i = 0; $i < $cant_comisiones; $i++) {?>
											<option value="<?=$datos['comisiones_internas'][$i]['codigo_grp'];?>" >
												<?=$datos['comisiones_internas'][$i]['descripcion_grp'];?>
											</option>
										<?php }?>
									</select>
								</div>
								<div id="contenedor_comisiones_conjuntas" class="col-sm-6 pl-1 d-none">
									<table class="table table-hover table-sm small">
										<thead class="thead-light">
											<tr>
												<th class="text-center" width="16">&nbsp;</th>
												<th>Seleccione las comisiones conjuntas y elija la que ser&aacute; la principal</th>
												<th class="text-center">Principal</th>
											</tr>
										</thead>
										<tbody>
											<?php
											for ($i=0; $i < $cant_comisiones; $i++) { ?>
												<tr>
													<td width="16">
														<input type="checkbox" name="codigo_conjuntas[]" class="conjuntas"
															   value="<?= $datos['comisiones_internas'][$i]['codigo_grp']; ?>" />
													</td>
													<td class="p-1">
														<?= $datos['comisiones_internas'][$i]['descripcion_grp']; ?>
													</td>
													<td class="text-center">
														<input type="radio" name="principal" class="principal"
															   value="<?= $datos['comisiones_internas'][$i]['codigo_grp']; ?>" />
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
								<label for="fecha" class="control-label small text-right pt-1 px-2">
									Fecha
								</label>
								<div class="col-sm-1 pl-0 mr-3">
									<input  id="fecha" name="fecha"
											class="form-control form-control-sm small" width="135"
											value="<?=(isset($datos['fecha'])) ? $this->formatearFecha($datos['fecha']) : date("d/m/Y");?>" />
								</div>

								<label for="hora" class="control-label small text-right ml-1 pt-1 px-2">
									Hora
								</label>
								<div class="col-sm-1 pl-0">
									<input  type="text" name="hora" id="hora"
											value="<?=(isset($datos['hora'])) ? $datos['hora'] : '';?>"
											class="form-control form-control-sm small" style="width:60px"
											onKeyPress="return soloEnteros(event)"
											onkeyup="mascara(this,':',patron_hora,true);">
								</div>

							<?php } else { // Si se edita la comisión ?>

								<div class="col-12 small">
									<strong>Comisi&oacute;n</strong>: <?=$datos['asunto'];?>
									&nbsp;|&nbsp;
									<strong>Fecha</strong>: <?=$this->formatearFecha($datos['fecha'])?>
									&nbsp;|&nbsp;
									<strong>Hora</strong>: <?=$datos['hora']?>
									&nbsp;|&nbsp;
									<a  title="Editar Cabecera" 
										href="javascript:editarCabecera(<?=$datos['id'];?>);">
										<i class="fas fa-edit"></i>
									</a>
								</div>
							<?php } ?>
						</div>

						<?php if (isset($datos['id'])) { // Si existe, se permite gestionar los expedientes en la Comisión ?>

							<div class="row">
								<div 
									id="mensaje_informativo" 
									class=" col-12 col-md-5 mx-auto alert alert-info small d-none" 
									role="alert"></div>
							</div>
							<!-- Contenedor del Encabezado -->
							<div class="form-group row">
								<div class="col-12">
									<div class="borde_superior_1 pt-1 small">
										<a  id="toggle_encabezado" 
											title="Ver encabezado de la Orden del d&i&acute;a de Comisi&oacute;n"
											data-toggle="collapse" 
											href="#panel_subseccion_encabezado"
										>
											&nbsp;<i class="fas fa-chevron-down"></i>&nbsp;Encabezado
										</a>
										&nbsp;
										<a  title="Editar Encabezado" 
											href="javascript:editarEncabezado(<?=$datos['id'];?>);">
											<i class="fas fa-edit"></i>
										</a>
									</div>
									<div id="panel_subseccion_encabezado" class="collapse">
										<?= ($datos['encabezado'] != '') 
											? html_entity_decode($datos['encabezado'])
											: ''; ?>
									</div>
								</div>
							</div>

							<!-- Contenedor de los Expedientes -->
							<div class="form-group row">
								<div class="col-12">
									<div class="borde_superior_1 pt-1">
										<a  id="toggle_expedientes" 
											title="Ver Expedientes de la Orden del d&i&acute;a de Comisi&oacute;n"
											data-toggle="collapse" 
											href="#panel_subseccion_expedientes"
											class="small"
										>
											&nbsp;<i class="fas fa-chevron-down"></i>&nbsp;Expedientes
										</a>
									</div>
									<div id="panel_subseccion_expedientes" class="collapse">

										<?php $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 1); ?>
										
										<?php $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 2); ?>
										
										<?php $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 3); ?>
																				
										<?php $this->listarExpedientesPorMarca($datos['id'], $datos['principal'], 5); ?>
									</div>
								</div>							
							</div>

							<!-- Contenedor del Pie -->
							<div class="form-group row">
								<div class="col-12">
									<div class="borde_superior_1 pt-1 small">
										<a  id="toggle_pie" 
											title="Ver pie de la Orden del d&i&acute;a de Comisi&oacute;n"
											data-toggle="collapse" 
											href="#panel_subseccion_pie"
										>
											&nbsp;<i class="fas fa-chevron-down"></i>&nbsp;Pie
										</a>
										&nbsp;
										<a  title="Editar Pie" 
											href="javascript:editarPie(<?=$datos['id'];?>);">
											<i class="fas fa-edit"></i>
										</a>
									</div>
									<div id="panel_subseccion_pie" class="collapse">
										<?= ($datos['pie'] != '') 
										? html_entity_decode($datos['pie']) 
										: ''; ?>
									</div>
								</div>
							</div>

							<div class="form-group row">
								<div class="col-12 borde_superior_1 pt-3 text-center">
									<a  href="<?=URL_RAIZ_SGL;?>html/backend/index.php?c=marcacomision&a=mostrar&f_comision=<?=$datos['codigo_comision']?>"
										target="_blank"
										class="btn btn-info btn-sm"
										title="Ir a Marca de Expedientes de la Comisi&oacute;n, para su revisi&oacute;n">
										<i class="fas fa-edit"></i>&nbsp;Revisar la Marca de Expedientes de la Comisi&oacute;n
									</a>
								</div>
							</div>
						<?php } ?>
					</form>
					<?php if (isset($datos['id'])) { ?>
						<hr>
						<form 
							id="formUpload" 
							name="formUpload" 
							class="form-horizontal"
							action="<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=upload"
							method="POST"
							enctype="multipart/form-data"
						>
							<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
							<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
							<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
							<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
							<input type="hidden" id="pagina" name="pagina" value="<?=$valor_pagina;?>" />
							<input  type="hidden" id="id" name="id" 
									value="<?=(isset($datos['id'])) ? $datos['id'] : '';?>" />
							
							<div class="row mt-1">
								<div class="col-12 small">
									Si posees <strong>firmada digitalmente</strong> la Orden del d&iacute;a de la Comisi&oacute;n, puedes subirla a continuaci&oacute;n.
								</div>
							</div>
							<div class="row mt-1">
								<div class="col-12 col-md-11 mx-auto">
	                                <div class="row mt-1">
		                                <div class="col-12 col-sm-2 custom-file">
		                                    <input 
		                                        type="file" 
		                                        class="custom-file-input" 
		                                        id="documento" 
		                                        name="documento" 
		                                        lang="es" 
		                                        accept=".pdf"
		                                    />
		                                    <label 
		                                    	class="custom-file-label small" 
		                                    	for="documento" 
		                                    	data-browse="Buscar"
		                                    >
		                                        Elegir documento
		                                    </label>
		                                </div>
										<div class="col-12 col-sm-3 text-center">
											<button 
												type="button" 
												id="btUpload" 
												class="btn btn-success btn-sm" 
												title="Subir"
											>
												<i class="far fa-file-pdf"></i>&nbsp;Subir documento Firmado
											</button>
										</div>
										<?php
			                        	$nombre_documento = $datos['id'] . '.pdf';
			                        	if (LibreriaGeneral::existeFoto(RUTA_ORDENES_COMISION_FIRMADOS . $nombre_documento)) {
			                        	?>
										<div class="col-12 col-sm-7">
			                                <p class="small pt-2">
			                                    <a  class="text-danger"
			                                        href="javascript:eliminarDocumentoFirmado('<?=$nombre_documento;?>');"
			                                        title="Eliminar documento firmado">
			                                        <i class="fas fa-trash text-danger"></i>
			                                    </a>
			                                    &nbsp;|&nbsp;
			                                    <a 
			                                        href="<?=URL_ORDENES_COMISION_FIRMADOS . $nombre_documento?>?v=<?=date('Ymd_Hi')?>" 
			                                        target="_blank" 
			                                        title="Ver <?=$datos['asunto'].' firmado'?>"
			                                    >
			                                        <?=$datos['asunto']?>&nbsp;<strong>(FIRMADA Digitalmente)</strong>
			                                    </a>
			                                </p>
			                            </div>
										<?php } ?>
									</div>
	                            </div>
	                        </div>
						</form>
					<?php } ?>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<?php $this->mostrarSpinnerModal();?>

				<script src="<?=URL_JS.$this->controlador;?>/edicion.js?v=<?=date("Ymd_Hi");?>"></script>
				<script src="<?=URL_JS.$this->controlador;?>/upload.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
		<?php }

	/**
	 * Se listan los Expedientes de una Marca en Comisión respectiva
	 * @param  integer  $id_orden_comision
	 * @param  string   $codigo_comision
	 * @param  integer  $marca_comision
	 * @return html
	 */
	private function listarExpedientesPorMarca(
		$id_orden_comision, 
		$codigo_comision, 
		$marca_comision = 0
	) {

		$expedientes = $this->modelo->obtenerItemsOrdenComision($id_orden_comision, $marca_comision);
		$cant_expedientes = (isset($expedientes)) ? count($expedientes) : 0;
		?>
		<div class="borde_superior_1 pt-1 pl-3">
			<a  id="toggle_expedientes" 
				title="Ver Expedientes <?=$this->mostrarNombreMarcaComision($marca_comision);?>"
				data-toggle="collapse" 
				href="#panel_marca_<?=$marca_comision?>" >

				&nbsp;<i class="fas fa-chevron-down"></i>&nbsp;
				<strong><?=$this->mostrarNombreMarcaComision($marca_comision);?></strong>
			</a>
			&nbsp;
			<a  title="Ingresar &Iacute;tem" 
				href="javascript:agregarItem(<?= $id_orden_comision;?>, <?=$marca_comision;?>);">
				<i class="fas fa-plus"></i>
			</a>
		</div>
		<div id="panel_marca_<?=$marca_comision?>" class="collapse pt-1">
			<?php
			// Para cada expediente de la Marca
			for ($i=0; $i < $cant_expedientes; $i++) { $item = &$expedientes[$i];?>
				<div class="small pl-3">
					&nbsp;&nbsp;&nbsp;
					<a  title="Editar extracto" 
						href="javascript:editarItem(<?=$item['id'];?>);">
						<i class="fas fa-edit"></i>
					</a>
					&nbsp;
					<a  title="Eliminar &iacute;tem" 
						href="javascript:eliminarItem(<?=$item['id'];?>);">
						<i class="fas fa-trash"></i>
					</a>
					&nbsp;
					<strong>
						<?=$item['anio'];?>&nbsp;
						<?=$item['tipo'];?>&nbsp;
						<?=$item['numero'];?>&nbsp;
						<?=$item['iniciador_codigo'];?>&nbsp;
						<?=$item['caratula'];?>
					</strong>
					<?php
					// Si posee extracto el item
					if (isset($item['extracto'])) {
						echo '<p>'.$item['extracto'].'</p>';
					} else {
						// Sino se obtienen los Extractos de los proyectos del expediente respectivo
						$extractos = $this->modelo->obtenerExtractosPorExpediente($item['anio'], $item['tipo'], $item['numero']);
						$cant_extractos = (isset($extractos)) ? count($extractos) : 0;
						// Por cada Extracto del expediente
						for ($e=0; $e < $cant_extractos; $e++) echo '<p>'.$extractos[$e]['extracto'].'</p>';
					}?>
				</div>
			<?php }?>
		</div>
		<?php }
}
?>
