<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaOrdenSesionEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'ordenes_sesion';

		// Se crea una instancia del modelo
		$this->modelo = new ordenes_sesionModel();
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos         [description]
	 * @param  array $secciones     [description]
	 * @param  array $filtro        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $secciones = null, $filtro = '', $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['id'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['id'])) ? 'modificar' : 'insertar';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : '';

		$cantidad_secciones = (isset($secciones)) ? count($secciones) : 0;
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
				<?php $this->mostrarContenidoHead();?>

				<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

    			<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
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
						<input type="hidden" id="id" name="id" value="<?=$datos['id'];?>" />
						<input  type="hidden" id="cod_seccion_editada" name="cod_seccion_editada"
								value="<?=(isset($filtro['cod_seccion_editada'])) ? $filtro['cod_seccion_editada'] : '';?>" />

						<div class="row">
							<div class="col-12 <?=($datos['id']) ? 'col-sm-8' : 'col-sm-10';?> fuente_titulos bg-dark text-white small py-1 titulo_entidad">
								<?=$titulo_operacion;?> de la Orden del D&iacute;a de Sesi&oacute;n
							</div>
							<div class="col-12 <?=($datos['id']) ? 'col-sm-4' : 'col-sm-2';?> text-center text-md-left">
									
								<?php if ($datos['id']) {?>
									<?php if ($this->modelo->tieneItems($datos['id'])) { // Si la Orden del Día posee ítems ?>
										<button type="button" id="btCargarGirosItems" class="btn btn-info btn-sm mt-1 mt-sm-0" title="Cargar Giros">
											<i class="fas fa-plus"></i>&nbsp;Cargar Giros
										</button>
									<?php }?>
										<button type="button" id="btNuevoItem" class="btn btn-info btn-sm mt-1 mt-sm-0"
												title="Ingresar &Iacute;tem">
											<i class="fas fa-plus"></i>&nbsp;&Iacute;tem
										</button>
								<?php }?>

								<button type="button" id="btGuardar" class="btn btn-success btn-sm mt-1 mt-sm-0" title="Guardar informaci&oacute;n">
									<i class="fas fa-check-circle"></i>&nbsp;Guardar
								</button>
								<button type="button" id="btCancelar" class="btn btn-info btn-sm mt-1 mt-sm-0" title="Volver al listado">
									<i class="fas fa-angle-left"></i>&nbsp;Volver
								</button>
							</div>
						</div>

						<div class="form-group row pt-1">
							<label for="periodo" class="control-label small text-right pt-1 px-2">Per&iacute;odo:</label>
							<div class="col-sm-1 pl-0">
								<input type="text" name="periodo" id="periodo"
										value="<?=($datos['periodo']) ? $datos['periodo'] : $filtro['periodo'];?>"
										class="form-control form-control-sm small w-50"
										onKeyPress="return soloEnteros(event)">
							</div>

							<label for="reunion" class="control-label small text-right pt-1 px-2">Reuni&oacute;n:</label>
							<div class="col-sm-1 pl-0">
								<input  type="text" name="reunion" id="reunion"
										value="<?=($datos['reunion']) ? $datos['reunion'] : $filtro['reunion'] + 1;?>"
										class="form-control form-control-sm small w-50"
										onKeyPress="return soloEnteros(event)">
							</div>

							<label for="sesion" class="control-label small text-right pt-1 px-2">Sesi&oacute;n:</label>
							<div class="col-sm-4 pl-0">
								<input  type="text" name="sesion" id="sesion"
										value="<?=$datos['sesion'];?>"
										class="form-control form-control-sm small w-100" />
							</div>

							<label for="fecha" class="control-label small text-right pt-1 px-2">Fecha</label>
							<div class="col-sm-1 pl-0 mr-3">
								<input  id="fecha" name="fecha"
										class="form-control form-control-sm small" width="130"
										value="<?=($datos['fecha']) ? $this->formatearFecha($datos['fecha']) : date("d/m/Y");?>" />
							</div>

							<label for="hora" class="control-label small text-right pt-1 px-2">Hora</label>
							<div class="col-sm-1 pl-0">
								<input  type="text" name="hora" id="hora"
										value="<?=$datos['hora'];?>"
										class="form-control form-control-sm small" style="width:60px"
										onKeyPress="return soloEnteros(event)"
										onkeyup="mascara(this,':',patron_hora,true);">
							</div>
						</div>
						<?php if ($datos['id']) {?>
							<div class="form-group row mb-0">
								<div class="col-12 borde_superior_1 pt-1 small">
									<p class="mb-1">
										<input 
											type="radio" 
											id="sin_decreto_y_anexo" 
											name="decreto_y_anexo"
											value="0"
											<?=$datos['decreto_y_anexo'] === '0' ? 'checked' : '';?>
										/>&nbsp;Sin Decreto y Anexo
									</p>
									<p class="mb-1">
										<input 
											type="radio" 
											id="con_decreto_y_anexo" 
											name="decreto_y_anexo"
											value="1"
											<?=$datos['decreto_y_anexo'] === '1' ? 'checked' : '';?>
										/>&nbsp;Con Decreto y Anexo
									</p>
								</div>
							</div>
							<div id="contenedor_texto_decreto_previo_anexo" class="form-group row d-none w-100">
								<div class="col-12">
									<div>
										<a  id="toggle_decreto_previo_anexo" 
											title="Ver/editar texto del Decreto previo al Anexo"
											class="small" 
											data-toggle="collapse" 
											href="#panel_decreto_previo_anexo" >

											&nbsp;<i class="fas fa-chevron-down"></i>&nbsp;DECRETO PREVIO AL ANEXO
										</a>
									</div>
									<div id="panel_decreto_previo_anexo" class="collapse">
										<textarea 
											id="editor_texto_decreto_previo_anexo" 
											name="texto_decreto_previo_anexo"
											class="editor_ep"
										><?= ($datos['texto_decreto_previo_anexo'] != '') 
												? html_entity_decode($datos['texto_decreto_previo_anexo']) 
												: ''; ?></textarea>
									</div>
								</div>
							</div>
						<?php } ?>
					</form>

					<?php if ($datos['id']) {?>
						<div class="row mb-5">
							<div class="col-12">
								<?php
								// Para cada sección
								for ($s=0; $s < $cantidad_secciones; $s++) { 
									$seccion = &$secciones[$s];
								
									// Se obtienen sus subsecciones
									$subsecciones = $this->modelo->obtenerTodasSubSecciones($seccion['codigo']);
									$cantidad_subsecciones = (isset($subsecciones)) ? count($subsecciones) : 0;

									// Si posee subsecciones
									if ($cantidad_subsecciones > 0) {
									?>
										<!-- Se muestra el nombre de la sección padre -->
										<div class="borde_superior_1 small pt-1">
											<?php if ($seccion['habilitado'] == 1) { // permite agregar sólo si está hbilitada ?>
												<a  id="btAgregar" title="Ingresar &Iacute;tem" 
													href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?= $this->controlador; ?>&accion=agregarItem&id_sesion=<?= $datos['id']; ?>&cod_seccion=<?= $seccion['codigo']; ?>');">
													<i class="fas fa-plus"></i>
												</a>
											<?php } ?>
											&nbsp;<?= $seccion['nombre']; ?>
											<?=($seccion['habilitado'] == 0) ? '&nbsp;(deshabilitada)' : ''; ?>
										</div>
									<?php
										// Para cada Subsección
										for ($ss=0; $ss < $cantidad_subsecciones; $ss++) {
											$subseccion = &$subsecciones[$ss];
											
											// Se listan los ítems de la Subsección
											$this->listarItemsPorSeccion($datos['id'], $subseccion['codigo'], $subseccion['nombre'], $subseccion['habilitado'], $filtro);
										}
									} else
										// Se listan los ítems de la Sección
										$this->listarItemsPorSeccion($datos['id'], $seccion['codigo'], $seccion['nombre'], $seccion['habilitado'], $filtro);
								}?>
							</div>							
						</div>
					<?php } ?>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script>
					let cod_seccion_editada = '<?=(isset($filtro['cod_seccion_editada'])) ? $filtro['cod_seccion_editada'] : 0;?>';

					if (cod_seccion_editada != 0) {
						// Se abre o cierra el contenedor de los ítems de la sección respectiva
						$("#toggle_"+cod_seccion_editada).trigger('click');
					}
				</script>

				<script src="<?=URL_JS;?>ordenes_sesion/edicion.js"></script>
		  	</body>
		</html>
		<?php }


    private function listarItemsPorSeccion($id, $codigo_seccion, $nombre_seccion, $estado_seccion, $filtro) {
		
		// Se obtienen los items
		$items = $this->modelo->listarItemsOrdenDiaSesion($id, $codigo_seccion);
		$cantidad_items = (isset($items)) ? count($items) : 0;
		
		// Si posee items
		if ( $cantidad_items > 0 ) {
			// Se arma el código de la sección padre
			$codigo_seccion_padre = substr($codigo_seccion, 0, 2).'000000';
		?>
			<!-- SE MUESTRA EL NOMBRE DE LA SECCION -->
			<div class="<?=($codigo_seccion == $codigo_seccion_padre) ? 'borde_superior_1' : '';?> small pt-1">

				<?php if ($estado_seccion == 1) { // permite agregar sólo si está hbilitada ?>
					<a  id="btAgregar" title="Ingresar &Iacute;tem" 
						href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?= $this->controlador; ?>&accion=agregarItem&id_sesion=<?= $id; ?>&cod_seccion=<?= $codigo_seccion; ?>');">
						<i class="fas fa-plus"></i>
					</a>
				<?php } ?>
				&nbsp;
				<a  id="toggle_<?= $codigo_seccion; ?>" 
					title="Ver &iacute;tems de la secci&oacute;n"
					data-toggle="collapse" 
					href="#panel_subseccion_<?= $codigo_seccion; ?>" >

					&nbsp;<i class="fas fa-chevron-down"></i>&nbsp;<?= $nombre_seccion; ?>
				</a><?=($estado_seccion == 0) ? '&nbsp;(deshabilitada)' : ''; ?>
			</div>
			<div id="panel_subseccion_<?= $codigo_seccion; ?>" class="collapse">

				<table class="table table-hover table-bordered table-sm small">
					<thead class="thead-light">
						<tr>
							<?php if ($estado_seccion == 1) { // permite agregar sólo si está hbilitada ?>
								<th width="60" colspan="2">&nbsp;</th>
							<?php } ?>
							<th>Ord.</th>
							<th>Documento</th>
							<th>Car&aacute;tula</th>
							<th>Iniciador / Autor</th>
							<th>Texto</th>
						</tr>
					</thead>
					<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
						<input type="hidden" id="nroFila_elegida_<?php echo $codigo_seccion; ?>" value="">
						
						<?php for ($i=0; $i < $cantidad_items; $i++) { $dato = &$items[$i]; ?>
							<tr id="e_fila<?= $i; ?>" > 
								<a name="tr<?= $i; ?>" style="display:none;"></a>
								
								<?php if ($estado_seccion == 1) { // permite agregar sólo si está hbilitada ?>
									<td class="text-center" width="30">
										<a  title="Editar &iacute;tem" 
											href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?= $this->controlador; ?>&accion=editarItem&id=<?= $dato['id']; ?>&pagina=<?= (isset($filtro['pagina'])) ? $filtro['pagina'] : ''; ?>');">
											<i class="fas fa-edit"></i>
										</a>
									</td>
									<td class="text-center" width="30">
										<a  title="Eliminar &iacute;tem" 
											href="javascript:if (confirm('Desea eliminar el item?')){redireccionar('<?=URL_ABMS;?>?controlador=<?= $this->controlador; ?>&accion=eliminarItem&id=<?= $dato['id']; ?>&id_sesion=<?= $dato['id_sesion']; ?>&cod_seccion=<?= $codigo_seccion; ?>');};">
											<i class="fas fa-trash"></i>
										</a>
									</td>
								<?php } ?>
								<td id="orden<?php echo $i; ?>" class="text-right">
									<?= $dato['orden']; ?></td>
								<td id="documento<?= $i; ?>">
									<?= ( $dato['tipo'] == '0' ) ? "Otro N&deg; ".$dato['numero'] : $this->mostrarDescripcionDocumento($dato); ?></td>
								<td id="caratula<?= $i; ?>">
									<?= ($dato['caratula']) ? $dato['caratula'] : '&nbsp;'; ?></td>
								<td id="autor<?= $i; ?>">
									<?= ($dato['autor']) ? $dato['autor'] : '&nbsp;'; ?></td>
								<td>
									<?php $this->mostrarVistaPreviaItem($dato); // Se muestra la vista previa del Item. ?>
								</td>
							</tr>
						<?php }?>
					</tbody>
				</table>
			</div>
		<?php
		}
	}
	
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
			$texto_comisiones = '&nbsp;<strong><span style="'.$resaltado_en_giros.'">'.$this->reemplazarPorMayusculaAcentuada(strtoupper($dato['giros'])).'</span></strong>';
		}
		// Si posee Detalle lo muestra
		if ( isset($dato['detalle']) && $dato['detalle'] != '' )
			$detalle_en_vista_previa = ' <strong>'.$this->reemplazarPorMayusculaAcentuada(strtoupper($dato['detalle'])).'</strong>';

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
