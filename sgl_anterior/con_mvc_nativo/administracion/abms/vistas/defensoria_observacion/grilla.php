<?php
if (!isset($_SESSION))
	session_start();

class VistaObservacionDpGrilla extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'defensoria_observacion';
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

		$criterio_buscador  = "&f_fecha=" . $filtro['f_fecha'];
		$criterio_buscador .= "&f_texto=" . $filtro['f_texto'];
		$criterio_buscador .= "&f_habilitados=" . $filtro['f_habilitados'];

		$url_listado_pdf = URL_ABMS.'?controlador='.$this->controlador.'&accion=generarInforme'.$criterio_buscador;
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
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							Observaciones <strong>a Moderar</strong> para Defensor del Pueblo
						</div>
					</div>
					<form action="" method="POST" name="formBuscadorGrilla">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input type="hidden" id="pagina" name="pagina" value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

						<div class="row mt-1">
							<div class="col-12 col-md-3 mt-1 mt-md-0">
								<input  type="text" name="f_texto" id="f_texto"
										value="<?=(isset($filtro['f_texto'])) ? $filtro['f_texto'] : '';?>"
										class="form-control form-control-sm w-100 small" placeholder="Por candidato, observador, DNI o mail...">
							</div>
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<input  id="f_fecha" name="f_fecha"
										class="form-control form-control-sm small" width="130"
										value="<?=(isset($filtro['f_fecha'])) ? $this->formatearFecha($filtro['f_fecha']) : '';?>"
										placeholder="Fecha" />
							</div>
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<div class="form-group form-control-sm small form-check pt-1">

									<input  type="checkbox" class="form-check-input"
											name="chk_habilitados" id="chk_habilitados" 
											<?= ( $_SESSION['f_defensoria']['f_habilitados'] == 1 ) ? "checked" : ""; ?> />
									<label class="form-check-label" for="chk_habilitados">S&oacute;lo habilitadas</label>

									<input  type="hidden" id="f_habilitados" name="f_habilitados" 
											value="<?= ($_SESSION['f_defensoria']['f_habilitados']) ? $_SESSION['f_defensoria']['f_habilitados'] : ''; ?>" />
								</div>
							</div>
							<div class="col-12 col-md-5 mt-1 mt-md-0">
								<button type="button" id="btBuscar"
										class="btn btn-info btn-sm"
										title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar"
										class="btn btn-info btn-sm"
										title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>
								<?php if ($cantidad > 0) {?>
									<a  href="<?=$url_listado_pdf;?>"
										target="_blank" 
										class="btn btn-info btn-sm mt-1 mt-md-0"
										title="Generar PDF">
										<i class="far fa-file-pdf"></i>&nbsp;Generar PDF
									</a>
								<?php } ?>
								<a  href="<?=URL_ABMS.'?controlador=defensoria&accion=listar';?>"
									class="btn btn-info btn-sm mt-1 mt-md-0"
									title="Volver a inscripciones">
									<i class="fas fa-angle-left"></i>&nbsp;Volver a inscripciones
								</a>
							</div>
						</div>
					</form>

					<?php $this->mostrarPaginador($cantidad, $filtro, $criterio_buscador, $this->controlador);?>

					<div class="row mt-1">
						<div class="col-md-12">
						<?php if ($cantidad > 0) {?>
							<div class="table-responsive">
								<table class="table table-hover table-bordered table-sm small">
									<thead class="thead-light">
										<tr>
											<th width="60" colspan="2">&nbsp;</th>
											<th class="text-center">Fecha</th>
									      	<th>Candidato</th>
									      	<th>Observado por</th>
									      	<th>DNI</th>
									      	<th>Tel&eacute;fono</th>
									      	<th>Mail</th>
											<th class="text-center">Habilitada</th>
											<th class="text-center">Notificar</th>
									    </tr>
									</thead>
									<tbody>
										<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos[$i]; ?>

										<tr <?=($dato['habilitado'] != '1') ? ' class="text-muted"' : '';?> >
											<td class="text-center">
												<a  href="javascript:refrescarEnModal('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=mostrarDocumentos&id=<?=$dato['id'];?>');" 
													title="Ver documentos">
													<i class="far fa-file-pdf"></i>
												</a>
											</td>
											<td class="text-center">
												<a  href="javascript:mostrarDatoEnModal('<?=$dato['mensaje'];?>');" 
													title="Ver mensaje de la observaci&oacute;n">
													<i class="far fa-file-alt"></i>
												</a>
											</td>
										    <td class="text-center">
									        	<?=$this->formatearFecha($dato['fecha']);?>
									        </td>
									        <td>
									        	<?= $dato['candidato_apellido'].', '.$dato['candidato_nombre'];?>
									        </td>
									        <td>
									        	<?= ($dato['tipo_observacion'] == 'personal'
									        		? $dato['apellido'].', '.$dato['nombre']
									        		: $dato['entidad_nombre']);
									        	?>
									        </td>
									        <td>
									        	<?=($dato['dni']) ? $dato['dni'] : '&nbsp;';?>
									        </td>
									        <td>
									        	<?= ($dato['tipo_observacion'] == 'personal'
									        		? $dato['telefono']
									        		: $dato['entidad_telefono']);
									        	?>
									        </td>
									        <td>
									        	<?= ($dato['tipo_observacion'] == 'personal'
									        		? $dato['email']
									        		: $dato['entidad_email']);
									        	?>
									        </td>
									        <td class="text-center">
												<a  href="javascript:refrescarEnModal('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editarHabilitacion&id=<?=$dato['id'];?>');" 
													title="Habilitaci&oacute;n de Observaci&oacute;n">
													<?= ($dato['habilitado'] == '1' 
														? '<i class="fas fa-check"></i>' 
														: '<i class="fas fa-times"></i>');?>
												</a>
											</td>
											<td class="text-center">
											<?php if ($dato['habilitado'] == '1') { ?>
												<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=notificarCandidato&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>');" title="Notificar a candidato">
									    			<i class="far fa-envelope"></i>
									    		</a>
											<?php } else echo '---';?>
											</td>
									    </tr>
										<?php }?>
									</tbody>
								</table>
							</div>
						<?php } else {?>
							<div class="alert alert-info">No posee observaciones a&uacute;n.</div>
						<?php }?>
						</div>
					</div>
			    </div>

				<?php $this->mostrarContenedorModal();?>

			    <script src="<?=URL_JS.$this->controlador;?>/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>