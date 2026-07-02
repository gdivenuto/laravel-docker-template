<?php
if (!isset($_SESSION)) {
	session_start();
}

class VistaParticipacionesGrilla extends VistaBase {

	private $controlador;

	public function __construct() {
		parent::__construct();
		$this->controlador = 'participaciones';
	}

	/**
	 * Se muestra la grilla
	 * @param  array   $datos        [description]
	 * @param  integer $anio
	 * @param  string  $tipo
	 * @param  integer $numero
	 * @param  integer $cuerpo
	 * @param  integer $alcance
	 * @param  string  $mensaje      [description]
	 * @param  string  $tipo_mensaje [description]
	 * @param  array   $filtro       [description]
	 */
	public function mostrar($datos, $anio, $tipo, $numero, $cuerpo, $alcance, $mensaje = '', $tipo_mensaje = '', $f_fecha_desde = '', $f_fecha_hasta = '') {

		$cantidad = (isset($datos)) ? count($datos) : 0;

		if ($tipo == 'E') {
			$titulo_por_tipo = 'del Expediente';
		} elseif ($tipo == 'N') {
			$titulo_por_tipo = 'de la Nota';
		} else {
			$titulo_por_tipo = 'de la Recomendaci&oacute;n';
		}

		// Se arma el criterio del buscador para la url del paginador
		$criterio_buscador = "&anio=" . $anio;
		$criterio_buscador .= "&tipo=" . $tipo;
		$criterio_buscador .= "&numero=" . $numero;
		$criterio_buscador .= "&cuerpo=" . $cuerpo;
		$criterio_buscador .= "&alcance=" . $alcance;
		$criterio_buscador .= "&f_fecha_desde=" . $this->formatearFecha($f_fecha_desde);
		$criterio_buscador .= "&f_fecha_hasta=" . $this->formatearFecha($f_fecha_hasta);
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
							Participaciones A MODERAR <?=$titulo_por_tipo;?> <?=$anio;?> <?=$tipo;?> <?=$numero;?> <?=$alcance;?> <?=$cuerpo;?>
						</div>
					</div>
					<!-- Buscador -->
					<form action="" method="POST" name="formBuscadorGrilla">

						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input type="hidden" id="anio" name="anio" value="<?=$anio;?>">
						<input type="hidden" id="tipo" name="tipo" value="<?=$tipo;?>">
						<input type="hidden" id="numero" name="numero" value="<?=$numero;?>">
						<input type="hidden" id="cuerpo" name="cuerpo" value="<?=$cuerpo;?>">
						<input type="hidden" id="alcance" name="alcance" value="<?=$alcance;?>">
						<input type="hidden" id="pagina" name="pagina" value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

						<div class="row mt-1">
							<div class="col-12">
								<div class="form-group row">
									<label for="f_fecha_desde" class="col-sm-1 control-label text-right pt-1">F. Desde</label>
									<div class="col-sm-1 pl-0">
										<input  id="f_fecha_desde" name="f_fecha_desde"
												class="form-control form-control-sm small" width="130"
												value="<?=(isset($f_fecha_desde)) ? $this->formatearFecha($f_fecha_desde) : '';?>" />
									</div>
									<label for="f_fecha_hasta" class="col-sm-1 control-label text-right pt-1">F. Hasta</label>
									<div class="col-sm-1 pl-0">
										<input  id="f_fecha_hasta" name="f_fecha_hasta"
												class="form-control form-control-sm small" width="130"
												value="<?=(isset($f_fecha_hasta)) ? $this->formatearFecha($f_fecha_hasta) : '';?>" />
									</div>
									<div class="col-sm-2 text-right">
										<button type="button" id="btBuscar"
												class="btn btn-info btn-sm"
												title="Buscar">
											<i class="fas fa-search"></i>&nbsp;Buscar
										</button>
									</div>
									<div class="col-sm-1 pl-0">
										<button type="button" id="btLimpiar"
												class="btn btn-info btn-sm"
												title="Limpiar criterio de b&uacute;squeda">
											<i class="fas fa-eraser"></i>&nbsp;Limpiar
										</button>
									</div>
									<div class="col-sm-2 text-right">
										<button type="button" id="btVolver"
												class="btn btn-info btn-sm"
												title="Volver">
											<i class="fas fa-chevron-left"></i>&nbsp;Volver
										</button>
									</div>
									<div class="col-sm-2">
										<button type="button" id="btnIrExpedientes"
												class="btn btn-info btn-sm"
												title="Ir a Expedientes">
											<i class="fas fa-chevron-left"></i>&nbsp;Ir a Expedientes
										</button>
									</div>
								</div>
							</div>
						</div>
					</form>

					<!-- Paginador -->
					<?php $this->mostrarPaginador($cantidad, $filtro, $criterio_buscador, $this->controlador);?>

					<!-- Grilla -->
					<div class="row mt-1">
						<div class="col-md-12">
							<?php if ($cantidad > 0) {?>
								<div class="table-responsive">
									<table class="table table-hover table-bordered table-sm small">
										<thead class="thead-light">
											<tr>
												<th>&nbsp;</th>
												<th class="text-center">Nro.</th>
										      	<th class="text-center">Fecha</th>
											    <th class="text-center">Participante</th>
											    <th class="text-center">Instituci&oacute;n</th>
											    <th class="text-center">Documentaci&oacute;n</th>
											    <th class="text-center">Mensaje</th>
											    <th class="text-center">Incorporada</th>
											    <th>&nbsp;</th>
										    </tr>
										</thead>
										<tbody>
											<?php for ($i = 0; $i < $cantidad; $i++) {$dato = &$datos[$i];?>
												<tr>
													<td class="text-center" width="30">
											    		<form   id="form_eliminar_ppc_<?=$i;?>"
											    				name="form_eliminar_ppc_<?=$i;?>"
											    				action="<?=URL_ABMS;?>?controlador=participaciones&accion=eliminarParticipacion"
											    				method="post">

											    			<input type="hidden" name="anio" value="<?=$dato['anio'];?>" />
											    			<input type="hidden" name="tipo" value="<?=$dato['tipo'];?>" />
											    			<input type="hidden" name="numero" value="<?=$dato['numero'];?>" />
											    			<input type="hidden" name="cuerpo" value="<?=$dato['cuerpo'];?>" />
											    			<input type="hidden" name="alcance" value="<?=$dato['alcance'];?>" />
											    			<input type="hidden" name="numero_participacion" value="<?=$dato['numero_participacion'];?>" />

												    		<a  href="javascript:if(confirm('¿Desea eliminar la participacion nro. <?=$dato['numero_participacion'];?>?')){ $('#form_eliminar_ppc_<?=$i;?>').submit();};"
												    			title="Eliminar participaci&oacute;n">
												    			<i class="fas fa-trash"></i>
												    		</a>
												    	</form>
											    	</td>
													<td class="text-right">
														<?=$dato['numero_participacion'];?>
													</td>
											    	<td class="text-center">
											        	<?=$this->formatearFecha($dato['fecha']);?>
											        </td>
											        <td>
											        	<?=$dato['apellidoynombre'];?>
											        </td>
											        <td>
											        	<?=(isset($dato['institucion_nombre'])) ? $dato['institucion_nombre'] . ' - ' . $dato['institucion_domicilio'] : '&nbsp;';?>
											        </td>
											        <td class="text-center">
											        	<?php if ($dato['documentacion'] != null) {?>

											        		<a  href="javascript:mostrarDocumentacion('<?=$dato['documentacion'];?>')"
											        			title="Ver Documentaci&oacute;n">

																<img src="data:image/jpg;base64,<?=$dato['documentacion'];?>"
													        		 alt="Documentaci&oacute;n"
													        		 class="mx-auto d-none d-md-block"
													        		 height="60" />
												        	</a>
														<?php } else {echo '&nbsp;';}?>
											        </td>
											        <td>
											        	<?=(isset($dato['texto'])) ? html_entity_decode($dato['texto']) : '&nbsp;';?>
												    </td>
												    <td class="text-center <?=($dato['estado_hcd'] == null) ? 'bg-warning' : '';?>">
												    	<?php if ($dato['estado_hcd'] == null) {?>
												    		<br>Pendiente
												    	<?php } elseif ($dato['estado_hcd'] == '1') {?>
												    		<i class="fas fa-check"></i>
												    	<?php } else {echo '---';}?>
												    </td>
													<td scope="row" class="text-center" width="150">

														<?php if ($dato['estado_hcd'] == null) {?>

												    		<form   name="form_incorporar_ppc"
												    				action="<?=URL_ABMS;?>?controlador=participaciones&accion=modificarEstadoAprobacion"
												    				method="post">

												    			<input type="hidden" name="anio" value="<?=$dato['anio'];?>" />
												    			<input type="hidden" name="tipo" value="<?=$dato['tipo'];?>" />
												    			<input type="hidden" name="numero" value="<?=$dato['numero'];?>" />
												    			<input type="hidden" name="cuerpo" value="<?=$dato['cuerpo'];?>" />
												    			<input type="hidden" name="alcance" value="<?=$dato['alcance'];?>" />
												    			<input type="hidden" name="numero_participacion" value="<?=$dato['numero_participacion'];?>" />
												    			<input type="hidden" name="estado" value="0" />

													    		<button type="submit" name="enviar"
													    				class="btn btn-sm btn-success"
													    				title="Incorporar participaci&oacute;n">
													    			Incorporar
													    		</button>
													    	</form>
													    	<br><br>
													    	<form   name="form_denegar_ppc"
												    				action="<?=URL_ABMS;?>?controlador=participaciones&accion=modificarEstadoAprobacion"
												    				method="post">

												    			<input type="hidden" name="anio" value="<?=$dato['anio'];?>" />
												    			<input type="hidden" name="tipo" value="<?=$dato['tipo'];?>" />
												    			<input type="hidden" name="numero" value="<?=$dato['numero'];?>" />
												    			<input type="hidden" name="cuerpo" value="<?=$dato['cuerpo'];?>" />
												    			<input type="hidden" name="alcance" value="<?=$dato['alcance'];?>" />
												    			<input type="hidden" name="numero_participacion" value="<?=$dato['numero_participacion'];?>" />
												    			<input type="hidden" name="estado" value="1" />

													    		<button type="submit" name="enviar"
													    				class="btn btn-sm btn-danger"
													    				title="Denegar participaci&oacute;n">
													    			Denegar
													    		</button>
														    </form>

													    <?php } else {?>

													    	<form   name="form_switchear_ppc"
												    				action="<?=URL_ABMS;?>?controlador=participaciones&accion=modificarEstadoAprobacion"
												    				method="post">

												    			<input type="hidden" name="anio" value="<?=$dato['anio'];?>" />
												    			<input type="hidden" name="tipo" value="<?=$dato['tipo'];?>" />
												    			<input type="hidden" name="numero" value="<?=$dato['numero'];?>" />
												    			<input type="hidden" name="cuerpo" value="<?=$dato['cuerpo'];?>" />
												    			<input type="hidden" name="alcance" value="<?=$dato['alcance'];?>" />
												    			<input type="hidden" name="numero_participacion" value="<?=$dato['numero_participacion'];?>" />
												    			<input type="hidden" name="estado" value="<?=$dato['estado_hcd'];?>" />

													    		<button type="submit" name="enviar"
													    				class="btn btn-sm <?=($dato['estado_hcd'] == '0') ? 'btn-success' : 'btn-danger';?>"
													    				title="<?=($dato['estado_hcd'] == '0') ? 'Incorporar' : 'Denegar';?> participaci&oacute;n">
													    			<?=($dato['estado_hcd'] == '0') ? 'Incorporar' : 'Denegar';?>
													    		</button>
													    	</form>
													    <?php }?>
											    	</td>
											    </tr>
											<?php }?>
										</tbody>
									</table>
								</div>
							<?php } else {?>
								<div class="alert alert-info">No posee participaciones por el momento.</div>
							<?php }?>
						</div>
					</div>
				</div>

				<div id="modal_documentacion" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_documentacion" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
						        <img src="<?=URL_IMAGENES;?>logo_hcd.png" class="rounded-circle" height="" alt="<?=TITULO_SISTEMA;?>">
						        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
						          <span aria-hidden="true">&times;</span>
						        </button>
					        </div>
							<div class="modal-body">
								<img id="modal_imagen_documentacion"
									 src=""
					        		 alt="Documentaci&oacute;n"
					        		 class="img-fluid" />
							</div>
						</div>
					</div>
				</div>
				<a id="muestra_modal_documentacion" href="#modal_documentacion" data-toggle="modal" class="d-none"></a>

				<?php $this->mostrarContenedorModal();?>

				<script>
					function mostrarDocumentacion(documentacion) {
						$('#modal_imagen_documentacion').attr('src', 'data:image/jpg;base64,'+documentacion);
						$('#muestra_modal_documentacion').click();
					}

					jQuery(document).ready(function() {

					    $('#f_fecha_desde').val('<?=(isset($f_fecha_desde)) ? $f_fecha_desde : '';?>');

						$('#f_fecha_hasta').val('<?=(isset($f_fecha_hasta)) ? $f_fecha_hasta : '';?>');

						$('#btnIrExpedientes').click(function(){
							redireccionar('<?=URL_RAIZ_SGL;?>html/backend/index.php?c=participaciones&a=view&f_anio=<?=$anio;?>&f_tipo=<?=$tipo;?>&f_numero=<?=$numero;?>&f_cuerpo=<?=$cuerpo;?>&f_alcance=<?=$alcance;?>');
						});
					});
				</script>

			    <script src="<?=URL_JS;?>participaciones/grilla_participaciones.js"></script>
		  	</body>
		</html>
		<?php }
}
?>