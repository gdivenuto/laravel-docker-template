<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaOrdenSesionEdicionItem extends VistaBase {

	public function __construct() {

		parent::__construct();

		$this->controlador = 'ordenes_sesion';

		// Se crea una instancia del modelo
		$this->modelo = new ordenes_sesionModel();
	}

	public function mostrar($datos = null, $filtro = null, $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['id'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['id'])) ? 'modificarItem' : 'insertarItem';
		$id_definido = (isset($datos['id'])) ? $datos['id'] : '';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : '';

		$cant_secciones = (isset($datos['secciones'])) ? count($datos['secciones']) : 0;
		$cant_subsecciones = (isset($datos['subsecciones'])) ? count($datos['subsecciones']) : 0;

		if ( isset($datos['anio']) && $datos['anio'] != '' )
			$anio_item = $datos['anio'];
		elseif ( $filtro['anio'] != '' )
			$anio_item = $filtro['anio'];
		else
			$anio_item = date("Y");

		// Si se edita el ítem
		if ( isset($datos['numero']) && $datos['numero'] ) {
			// Se obtiene el nombre del Iniciador
			$iniciador_para_item = $this->modelo->obtenerIniciadorParaItem(
				$datos['anio'], $datos['tipo'], $datos['numero']
			);
		}

		// Se toma el primer par de dígitos de la sección del ítem
		$primer_par_digitos = substr($datos['cod_seccion'], 0, 2);
		
		$seccion_padre = $primer_par_digitos.'000000';

		if ( isset($datos['tipo']) && $datos['tipo'] != '' )
			$tipo_item = $datos['tipo'];
		elseif ( $filtro['tipo'] != '' )
			$tipo_item = $filtro['tipo'];
		else
			$tipo_item = 0;

		// Si se edita el ítem
		if ( isset($datos['numero']) && $datos['numero'] )
			// Se obtiene el nombre del Autor
			$autor_lectura = $this->modelo->obtenerInfoPrimerAutor($datos['anio'], $datos['tipo'], $datos['numero']);

		// Sólo si se edita el item
		if ( isset($datos['id']) )
		{
			// Si la sección padre del ítem es de:
			// 40: DICTAMEN DE COMISION
			// 50: EXPEDIENTES Y NOTAS CON DICTAMEN DE COMISION
			if ( substr($datos['cod_seccion'], 0, 2) === '40' || substr($datos['cod_seccion'], 0, 2) === '50')
			{
				if ( ($datos['tipo'] == '0') && ( ! is_null($datos['anio_despacho_archivo']) ) ) {
					$doc_elec = $this->modelo->obtenerDocumentosExpedElec(
						$datos['anio_despacho_archivo'], 
						$datos['tipo_despacho_archivo'], 
						$datos['numero_despacho_archivo']
					);
				} else {
					$doc_elec = $this->modelo->obtenerDocumentosExpedElec(
						$datos['anio'], $datos['tipo'], $datos['numero']
					);
				}
				$cant_doc_elec = (isset($doc_elec)) ? count($doc_elec) : 0;

				$despachos_item = $this->modelo->obtenerDespachosItem($datos['id']);
				//LibreriaGeneral::registrarLog("despachos_item", $despachos_item);
				$cant_despachos_item = (isset($despachos_item)) ? count($despachos_item) : 0;
			}
		}
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
						<div class="col-12 col-md-10 fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							<?=$titulo_operacion;?> del Item de la Orden del D&iacute;a de Sesi&oacute;n
						</div>
						<div class="col-12 col-md-2 pt-1 pt-md-0 text-center">
							<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n">
								<i class="fas fa-check-circle"></i>&nbsp;Guardar
							</button>
							<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Cancelar operaci&oacute;n">
								<i class="fas fa-angle-left"></i>&nbsp;
								<?=($operacion == 'modificarItem') ? 'Volver' : 'Cancelar';?>
							</button>
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" action="<?=URL_ABMS;?>" method="POST">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="<?=$operacion;?>" />
						<input type="hidden" id="pagina" name="pagina" value="<?=$valor_pagina;?>" />
						<input type="hidden" id="id" name="id" value="<?=$id_definido;?>" />
						<input type="hidden" id="id_sesion" name="id_sesion" value="<?= $datos['id_sesion']; ?>" />

						<input  type="hidden" id="numero_actual" name="numero_actual" 
								value="<?=(isset($datos['numero'])) ? $datos['numero'] : ''; ?>">

						<input  type="hidden" id="habilitado" name="habilitado" 
								value="<?=(isset($datos['habilitado'])) ? $datos['habilitado'] : ''; ?>">

						<!-- Para saber si se debe guardar o borrar el Nombre de la Comisión -->
						<input  type="hidden" 
								id="giros_edicion_manual_marca_previa" 
								name="giros_edicion_manual_marca_previa" 
								value="<?= (isset($datos['giros_edicion_manual'])) 
									? $datos['giros_edicion_manual'] : 0; ?>"
						/>

						<!-- El valor del nro de Orden del Item -->
						<input  type="hidden" name="orden" 
								value="<?= (isset($datos['orden'])) ? $datos['orden'] : ''; ?>" />

						<div class="row my-1">
							<div class="col-12">
								<div class="form-group row mt-1">
									<label  for="seccion_padre" 
											class="col-sm-2 control-label small text-left text-md-right pt-1">
										Secci&oacute;n padre:
									</label>
									<div class="col-sm-8">
										<select id="seccion_padre" name="seccion_padre" class="form-control form-control-sm" >
											<option value="0">---</option>
											<?php for ($i = 0; $i < $cant_secciones; $i++) {?>
												<option value="<?=$datos['secciones'][$i]['codigo'];?>" >
													<?=$datos['secciones'][$i]['nombre'];?>
												</option>
											<?php }?>
										</select>
									</div>
								</div>
								<div id="ods_subsecciones" class="form-group row mt-1">
									<label for="cod_seccion" class="col-sm-2 control-label small text-left text-md-right pt-1">
										Secci&oacute;n:
									</label>
									<div class="col-sm-8">
										<select id="cod_seccion" name="cod_seccion" class="form-control form-control-sm" >
											<option value="0">---</option>
											<?php for ($i = 0; $i < $cant_subsecciones; $i++) {?>
												<option value="<?=$datos['subsecciones'][$i]['codigo'];?>" >
													<?=$datos['subsecciones'][$i]['nombre'];?>
												</option>
											<?php }?>
										</select>
									</div>
									<div id="contenedoraCargaGrupal" class="col-sm-2 p-1">
										<button type="button" id="btCargaGrupal" class="btn btn-info btn-sm" 
												title="Carga Grupal">
											<i class="fas fa-check-circle"></i>&nbsp;Carga Grupal
										</button>
									</div>
								</div>

								<div id="ods_contenedora_resto_datos_item" class="px-1">

									<!-- Clave del Expediente/Nota -->
									<div class="form-group row">
										<label for="anio" class="col-sm-2 control-label small text-left text-sm-right pt-1">
											A&ntilde;o
										</label>
										<div class="col-sm-1">
											<input  type="text" id="anio" name="anio"
													value="<?=(isset($datos['anio'])) ? $datos['anio'] : date("Y");?>"
													class="form-control form-control-sm p-1"
													onKeyPress="javascript:return soloEnteros(event)">
										</div>
										<label for="tipo" class="col-sm-1 control-label small text-left text-sm-right pt-1">
											Tipo
										</label>
										<div class="col-sm-2">
											<select id="tipo" name="tipo" value="" class="custom-select custom-select-sm p-1">
												<option value="E">Expediente</option>
												<option value="N">Nota</option>
												<option value="D">Decreto</option>
												<option value="0">Otros</option>
											</select>
										</div>
										<label for="numero" class="col-sm-2 control-label small text-left text-sm-right pt-1">
											N&uacute;mero
										</label>
										<div class="col-sm-2">
											<input  type="text" id="numero" name="numero" 
													value="<?= (isset($datos['numero'])) ? $datos['numero'] : ''; ?>" 
													class="form-control form-control-sm p-1"
													onkeypress="return soloEnteros(event);">
										</div>
										<div class="col-sm-2">
											<!-- Mensaje en caso que el documento haya sido registrado previamente -->
											<span id="mensaje_clave_documento_orden_sesion"></span>
										</div>
									</div>

									<!-- Datos del Expediente/Nota -->
									<div id="ods_datos_expediente_nota">

										<div class="form-group row mt-1">
											<label for="iniciador_codigo" class="col-sm-2 control-label small text-left text-md-right pt-1">
												Iniciador
											</label>
											<div class="col-sm-1">
												<input  type="text" id="iniciador_codigo" name="iniciador_codigo"
														value="<?=(isset($iniciador_para_item['codigo_iniciador'])) ? $iniciador_para_item['codigo_iniciador'] : '';?>"
														class="form-control form-control-sm p-1 text-muted" readonly="true">
											</div>
											<div class="col-sm-7">
												<input  type="text" id="descripcion_iniciador" name="descripcion_iniciador" 
														value="<?= (isset($iniciador_para_item['descripcion_iniciador'])) ? $iniciador_para_item['descripcion_iniciador'] : ''; ?>"
														class="form-control form-control-sm text-muted" width="400" readonly="true" />
											</div>
											<div class="col-sm-2 p-1">
												<!-- Para Copiar el Iniciador en el textarea de Iniciador/Autor -->
												<button type="button" id="btCopiarIniciador" class="btn btn-info btn-sm" 
														title="Mostrar el Iniciador debajo en el campo Iniciador/Autor">
													<i class="fas fa-check-circle"></i>&nbsp;Copiar
												</button>
											</div>
										</div>

										<div class="form-group row mt-1">
											<label for="codigo_autor" class="col-sm-2 control-label small text-left text-md-right pt-1">
												Autor
											</label>
											<div class="col-sm-1">
												<input  type="text" id="codigo_autor" name="codigo_autor"
														value="<?=(isset($autor_lectura['codigo_autor'])) ? $autor_lectura['codigo_autor'] : '';?>"
														class="form-control form-control-sm p-1 text-muted" readonly="true">
											</div>
											<div class="col-sm-7">
												<input  type="text" id="descripcion_autor" name="descripcion_autor" 
														value="<?= (isset($autor_lectura['descripcion_autor'])) ? $autor_lectura['descripcion_autor'] : ''; ?>"
														class="form-control form-control-sm text-muted" width="400" readonly="true" />
											</div>
											<div class="col-sm-2 p-1">
												<!-- Para Copiar el Autor en el textarea de Iniciador/Autor -->
												<button type="button" id="btCopiarAutor" class="btn btn-info btn-sm" 
														title="Mostrar el Autor debajo en el campo Iniciador/Autor">
													<i class="fas fa-check-circle"></i>&nbsp;Copiar
												</button>
											</div>
										</div>

										<div class="form-group row mt-1">
											<label for="autor" class="col-sm-2 control-label small text-left text-md-right pt-1">
												Iniciador/Autor:
											</label>
											<div class="col-sm-8">
												<textarea id="autor" name="autor" class="form-control form-control-sm"
														  rows="4" aria-label="Iniciador/Autor"><?=(isset($datos['autor'])) ? $datos['autor'] : '';?></textarea>
											</div>
										</div>

										<div class="form-group row mt-1">
											<label for="caratula" class="col-sm-2 control-label small text-left text-md-right pt-1">
												Car&aacute;tula
											</label>
											<div class="col-sm-8">
												<input  type="text" id="caratula" name="caratula"
														value="<?=(isset($datos['caratula'])) ? $this->reemplazarComillaDoble($datos['caratula']) : '';?>"
														class="form-control form-control-sm" >
											</div>
										</div>

										<div class="form-group row mt-1">
											<label for="extracto" class="col-sm-2 control-label small text-left text-md-right pt-1">
												Extracto:
											</label>
											<div class="col-sm-8">
												<textarea id="extracto" name="extracto" 
														  class="form-control form-control-sm" rows="4" 
														  aria-label="Extracto"><?=(isset($datos['extracto'])) ? preg_replace('~\R~u', "&#13;", $datos['extracto']) : '';?></textarea>
											</div>
											<div class="col-sm-2 p-1">
												<!-- Para actualizar el texto con el extracto original del expediente -->
												<button type="button" id="btActualizarExtracto" class="btn btn-info btn-sm" 
														title="Actualizar Extracto">
													<i class="fas fa-check-circle"></i>&nbsp;Actualizar
												</button>
											</div>
										</div>
									</div>

									<?php if ( isset($datos['id']) ) { ?>
										<!-- Expediente/Nota con Despacho de Archivo (Caso de Uso cuando: Tipo=Otros) -->
										<div class="form-group row">
											<div id="cont_con_despacho_archivo" class="col-12 col-md-3 offset-md-2 d-none">
												<span class="small font-weight-bold">
													¿Expediente con despacho de archivo?
												</span>
												&nbsp;
												<input  type="checkbox" 
														id="con_despacho_archivo" 
														name="con_despacho_archivo" 
														<?=(isset($datos['con_despacho_archivo']) && $datos['con_despacho_archivo'] == 1) ? 'checked' : '';?>
												/>
											</div>
											<div class="col-12 col-md-1">
												<input  type="text" 
														id="anio_despacho_archivo" 
														name="anio_despacho_archivo"
														value="<?= $datos['anio_despacho_archivo'] ?? ''; ?>" 
														placeholder="A&ntilde;o" 
														class="form-control form-control-sm p-1 text-center d-none"
														onKeyPress="javascript:return soloEnteros(event)"
												/>
											</div>
											<div class="col-12 col-md-2">
												<select id="tipo_despacho_archivo" 
														name="tipo_despacho_archivo" 
														class="custom-select custom-select-sm p-1 text-center d-none">
													<option value="E">Expediente</option>
													<option value="N">Nota</option>
												</select>
											</div>
											<div class="col-12 col-md-1">
												<input  type="text" 
														id="numero_despacho_archivo" 
														name="numero_despacho_archivo" 
														value="<?= $datos['numero_despacho_archivo'] ?? ''; ?>" 
														placeholder="N&uacute;mero" 
														class="form-control form-control-sm p-1 text-center d-none"
														onkeypress="return soloEnteros(event);"
												/>
											</div>
											<div class="col-12 col-md-2">
												<button type="button" 
														id="btBuscarDocElec" 
														class="btn btn-info btn-sm d-none" 
														title="Ver Documentos Electr&oacute;nicos">
													<i class="fas fa-search"></i>&nbsp;Ver
												</button>
											</div>
										</div>
									
										<div class="form-group row">
											<div class="col-12 col-md-4 offset-md-2">
												<div class="row">
													<div class="col-12 small">
														Documentos a elegir para <strong>despacho a archivo</strong>:
													</div>
												</div>
												<div class="row overflow-auto">
													<div id="cont_documentos_elec" class="col-12">
														<div class="table-responsive <?=( $cant_doc_elec > 3) ? 'alto_150' : '';?>">
															<table class="table table-hover table-bordered table-sm small">
																<thead class="thead-light">
																	<tr>
																		<th>&nbsp;</th>
																		<th class="text-center">Orden</th>
																      	<th>Detalle</th>
																      	<th class="text-center">Fecha</th>
																		<th width="30">&nbsp;</th>
																	</tr>
																</thead>
																<tbody>
																	<?php for ($i=0; $i < $cant_doc_elec; $i++) { ?>
																		<tr>
																			<td class="text-center">
																				<?= ($doc_elec[$i]['dec1404']) 
																					? '<i class="fa fa-eye-slash text-danger" title="Alcanzado por Art. 11 Dec. 1404"  aria-hidden="true"></i>'
																					: '<i class="fa fa-eye text-success" title="No Alcanzado por Art. 11 Dec. 1404" aria-hidden="true"></i>';?>
																			</td>
																			<td class="text-center">
																				<?= $doc_elec[$i]['orden']; ?>
																			</td>
																			<td>
																				<a  href="<?= URL_PROYECTOS.$doc_elec[$i]['documento'].'?v='.date("Ymd_His");?>" 
																					target="_blank" 
																					title="Ver documento">
																					<?= $doc_elec[$i]['detalle']; ?>
																				</a>
																			</td>
																			<td>
																				<?= $this->extraerFecha($doc_elec[$i]['fecha_hora']); ?>
																			</td>
														    				<td class="text-center" width="30">
														    					<a  href="javascript:asignarDespacho(<?=$doc_elec[$i]['orden'];?>, '<?= $doc_elec[$i]['detalle']; ?>');"
																	    			title="Asignar despacho al item">
																	    			<i class="fas fa-angle-right"></i>
																	    		</a>
																	    	</td>
																		</tr>
																	<?php } ?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
											<div class="col-12 col-md-4" id="cont_despachos">
												<div class="row">
													<div class="col-12 small">
														Despachos a archivo <strong>asignados</strong> al &iacute;tem:
													</div>
												</div>
												<div class="row overflow-auto">
													<div id="cont_despachos_asignados" class="col-12">
														<div class="table-responsive <?=( $cant_despachos_item > 3) ? 'alto_150' : '';?>">
															<table class="table table-hover table-bordered table-sm small">
																<thead class="thead-light">
																	<tr>
																		<th>&nbsp;</th>
																		<th class="text-center">Orden</th>
																      	<th>Detalle</th>
																		<th width="60" colspan="2">&nbsp;</th>
																	</tr>
																</thead>
																<tbody>
																	<?php for ($i=0; $i < $cant_despachos_item; $i++) { 
																		$d = &$despachos_item[$i];
																	?>
																	<tr>
																		<td class="text-center">
																			<?= ($d['dec1404']) 
																				? '<i class="fa fa-eye-slash text-danger" title="Alcanzado por Art. 11 Dec. 1404"  aria-hidden="true"></i>'
																				: '<i class="fa fa-eye text-success" title="No Alcanzado por Art. 11 Dec. 1404" aria-hidden="true"></i>';?>
																		</td>
																		<td class="text-center">
																			<?= $d['orden_actuacion']; ?>
																		</td>
																		<td>
																			<a  href="<?= $d['documento'];?>" 
																				target="_blank" title="Ver documento">
																				<?=(isset($d['detalle'])) ? $d['detalle'] : '';?>
																			</a>
																		</td>
																		<td class="text-center">
																			<a  href="javascript:editarDetalleModal(<?=$d['orden_actuacion'];?>, '<?=$d['detalle'];?>');"
																    			title="Actualizar texto del detalle del despacho">
																    			<i class="fas fa-edit"></i>
																    		</a>
																		</td>
																		<td class="text-center">
																			<a  href="javascript:eliminarDespacho(<?=$d['orden_actuacion'];?>);"
																    			title="Eliminar despacho">
																    			<i class="fas fa-trash"></i>
																    		</a>
																		</td>
															    	</tr>
																	<?php } ?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } else { ?>
										<div class="form-group row">
											<div class="col-8 mx-auto">
												<div class="alert alert-info text-center small">
													Debe guardar el &iacute;tem primero, para cargar los Despachos de Archivo.
												</div>
											</div>
										</div>
									<?php }	?>

									<?php
									// Para editar los giros (los nombres de las Comisiones a las cuales fueron girados, separados por comas)
									// Si la sección permite cargar Giros en el ítem
									if ( $this->modelo->permiteCargaGiros($datos['cod_seccion']) ) {
									?>
										<div class="form-group row mt-1">
											<label for="giros" class="col-sm-2 control-label small text-left text-md-right pt-1">
												Giros
											</label>
											<div class="col-sm-7">
												<input  type="text" id="giros" name="giros"
														value="<?=(isset($datos['giros'])) ? $this->reemplazarComillaDoble($datos['giros']) : '';?>"
														class="form-control form-control-sm" />
											</div>
											<div class="col-sm-1">
												<input 
													type="checkbox" 
													name="chk_giros" 
													id="chk_giros"
													<?=( isset($datos['giros_edicion_manual']) && 
														 $datos['giros_edicion_manual'] === '1'  
														 	? 'checked' 
														 	: '');?>
												/>
											</div>
										</div>
									<?php } ?>

									<div class="form-group row mt-1">
										<label for="detalle" class="col-sm-2 control-label small text-left text-md-right pt-1">
											Detalle
										</label>
										<div class="col-sm-8">
											<input  type="text" id="detalle" name="detalle"
													value="<?=(isset($datos['detalle'])) ? $this->reemplazarComillaDoble($datos['detalle']) : '';?>"
													class="form-control form-control-sm" >
										</div>
									</div>
									<?php if ( isset($datos['orden']) && $datos['orden'] != '' ) { ?>
										<div class="my-1 p-1 small border border-secundary">
											<?=$this->mostrarVistaPreviaItem($datos);?>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>

						<!-- Modal para la edición del detalle del despacho -->
						<div class="modal fade" id="edicionDetalleModal" tabindex="-1" role="dialog">
							<div class="modal-dialog" role="document">
								<div class="modal-content p-3">
									<div class="modal-header p-1">
										<h6 class="modal-title">
											Ingrese el <strong>nuevo detalle</strong> del despacho.
										</h6>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body p-0">

										<input type="hidden" id="orden_actuacion" name="orden_actuacion" value="" />

										<div class="input-group mb-3">
											<input  type="text" class="form-control form-control-sm" 
													id="despacho_asignado" name="despacho_asignado" value=""
													aria-label="Texto del detalle" 
													aria-describedby="btnActualizarDetalleDespacho" />
											<div class="input-group-append">
												<button class="btn btn-sm btn-info" type="button" 
														id="btnActualizarDetalleDespacho">
													<i class="fas fa-check-circle"></i>&nbsp;Actualizar
												</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<?php $this->mostrarSpinnerModal();?>

				<script>
					// Se setea la Sección padre
					$('#seccion_padre').val('<?=$seccion_padre;?>');
					
					// Se setea la Sección a la que pertenece
					$('#cod_seccion').val('<?= (isset($datos['cod_seccion'])) ? $datos['cod_seccion'] : 0; ?>');

					$('#tipo').val('<?= $tipo_item; ?>');

					$('#tipo_despacho_archivo').val('<?= $datos['tipo_despacho_archivo'] ?? 'E'; ?>');
					
					let permite_carga_giros = '<?=$this->modelo->permiteCargaGiros($datos['cod_seccion']);?>';

					// Si "está tildado" en check para editar los Giros
		            if ( $('#chk_giros').prop('checked') ) {
		                // Se habilita el campo 'giros'
		                $('#giros').prop('disabled', false);
		                $('#giros').focus();
		            } else {
		                // sino, se deshabilita el campo 'giros'
		                $('#giros').prop('disabled', true);
		            }
				</script>
				<script src="<?=URL_JS;?>ordenes_sesion/edicion_item.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }

	/**
	 * Se muestra la vista previa de un Item respectivo
	 * Utilizado en el método: listarItemsPorSeccion
	 * @param  [type] $dato [description]
	 * @return [type]       [description]
	 */
	private function mostrarVistaPreviaItem($dato) {

		// Para mostrar o no el valor del campo "autor"
		$iniciador_autor = '';
		// Para mostrar o no la Carátula
		$caratula = '';
		// Para mostrar o no las Comisiones
		$texto_comisiones = '';
		// Para mostrar o no el Detalle
		$detalle_en_vista_previa = '';

		// Si la sección permite mostrar el Iniciador y/o el Autor
		if ( $this->modelo->seMuestraIniciador($dato['cod_seccion']) || $this->modelo->seMuestraAutor($dato['cod_seccion']) )
			$iniciador_autor = ( isset($dato['autor']) && $dato['autor'] != '' ) ? $dato['autor'].': ' : '';

		// Si posee Carátula
		if ( $dato['caratula'] != '' ) {
			// Si la sección permite mostrar la Carátula en Expedientes
			if ( $dato['tipo'] == 'E' && $this->modelo->seMuestraCaratulaEnExpedientes($dato['cod_seccion']) )
				$caratula = $this->reemplazarPorMayusculaAcentuada(strtoupper($dato['caratula'])).': ';

			// Si la sección permite mostrar la Carátula en Notas
			if ( $dato['tipo'] == 'N' && $this->modelo->seMuestraCaratulaEnNotas($dato['cod_seccion']) )	
				$caratula = $this->reemplazarPorMayusculaAcentuada(strtoupper($dato['caratula'])).': ';
		}

		// Si la sección permite mostrar las Comisiones
		if ( $this->modelo->seMuestranComisiones($dato['cod_seccion']) ) {
			// Se resalta el texto de giros si se cargó manualmente
			$resaltado_en_giros = ( isset($dato['giros_edicion_manual']) && $dato['giros_edicion_manual'] === '1' ) ? 'background-color:yellow' : '';
			// Se arma el texto de las Comisiones
			$texto_comisiones = '&nbsp;<strong><span style="'.$resaltado_en_giros.'">'.$this->reemplazarPorMayusculaAcentuada($dato['giros']).'</span></strong>';
		}
		// Si posee Detalle lo muestra
		if ( isset($dato['detalle']) && $dato['detalle'] != '' )
			$detalle_en_vista_previa = ' <strong>'.$this->reemplazarPorMayusculaAcentuada($dato['detalle']).'</strong>';

		// Si el tipo del documento NO es "Otro", se muestra su Descripción, Iniciador/Autor y Carátula 
		$texto_previo_al_extracto = ( $dato['tipo'] != '0' ) ? $this->mostrarDescripcionDocumento($dato).$iniciador_autor.$caratula : '';

		// Se muestra la Vista Previa armada
		echo '<strong>'.$dato['orden'].'.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>'.$texto_previo_al_extracto.nl2br($this->reemplazarPorHTML($dato['extracto'])).$texto_comisiones.$detalle_en_vista_previa.'</strong>';
	}

	private function mostrarDescripcionDocumento($dato) {
		
		// SE OBTIENE EL NOMBRE DEL INICIADOR
		$iniciador_para_item = $this->modelo->obtenerIniciadorParaItem($dato['anio'], $dato['tipo'], $dato['numero']);
		
		switch ($dato['tipo']) {
			case 'E':
				$descripcion = "Expte ".$dato['numero']."-".$iniciador_para_item['codigo_iniciador']."-".substr($dato['anio'], 2, 2).': ';
				break;
			case 'N':
				$descripcion = "Nota ".$dato['numero']."-".$iniciador_para_item['codigo_iniciador']."-".substr($dato['anio'], 2, 2).': ';
				break;
			case 'D':
				$descripcion = "Decreto N&deg; ".$dato['numero'].': ';
				break;
			case '0':
				$descripcion = "";// retirado Expte/Nota el 06/08/2018
				break;
		}
		
		return $descripcion;
	}
	
}
?>
