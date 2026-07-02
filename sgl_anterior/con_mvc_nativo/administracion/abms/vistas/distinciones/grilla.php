<?php
if (!isset($_SESSION)) {
	session_start();
}

class VistaDistincionesGrilla extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'distinciones';
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

		// Se arma el criterio del buscador para la url del paginador
		$criterio_buscador = "&f_distincion=" . $filtro['f_distincion'];
		$criterio_buscador .= "&f_acto=" . $filtro['f_acto'];
		$criterio_buscador .= "&f_expediente=" . $filtro['f_expediente'];
		$criterio_buscador .= "&f_fecha=" . $filtro['f_fecha'];
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
							Distinciones
						</div>
					</div>
					<!-- Buscador -->
					<form action="" method="POST" name="formBuscadorGrilla">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input  type="hidden" id="pagina" name="pagina"
								value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

						<div class="form-row">
							<div class="col-12 col-md-2 mt-1">
								<select id="f_distincion" name="f_distincion" class="form-control form-control-sm">
									<option value="0">Distinci&oacute;n</option>
									<option value="CE">Ciudadano Ejemplar</option>
									<option value="CI">Ciudadano Ilustre</option>
									<option value="CM">Ciudadano Marplatense</option>
									<option value="CA">Compromiso Ambiental</option>
									<option value="CS">Compromiso Social</option>
									<option value="DIN">Declaraci&oacute;n de Inter&eacute;s</option>
									<option value="DI">Deportista Insigne</option>
									<option value="HD">Hijo Dilecto</option>
									<option value="MA">M&eacute;rito Acad&eacute;mico</option>
									<option value="MC">M&eacute;rito Ciudadano</option>
									<option value="MD">M&eacute;rito Deportivo</option>
									<option value="RE">Reconocimiento</option>
									<option value="SS">Servicio Solidario</option>
									<option value="VD">Vecino Destacado</option>
									<option value="VI">Visitante Ilustre</option>
									<option value="VN">Visitante Notable</option>
								</select>
							</div>
							<div class="col-12 col-md-2 mt-1">
								<input  type="text" name="f_acto" id="f_acto"
										value="<?=($filtro['f_acto']) ? $filtro['f_acto'] : '';?>"
										class="form-control form-control-sm small"
										placeholder="Acto">
							</div>
							<div class="col-12 col-md-2 mt-1">
								<input  type="text" name="f_expediente" id="f_expediente"
										value="<?=($filtro['f_expediente']) ? $filtro['f_expediente'] : '';?>"
										class="form-control form-control-sm small"
										placeholder="Expediente">
							</div>
							<div class="col-12 col-md-2 mt-1">
								<input  id="f_fecha" name="f_fecha"
										class="form-control form-control-sm small" width="130"
										value="<?=(isset($filtro['f_fecha'])) ? $this->formatearFecha($filtro['f_fecha']) : '';?>"
										placeholder="Fecha" />
							</div>
							<div class="col-12 col-md-3 mt-1 text-center text-md-left">
								<button type="button" id="btBuscar" class="btn btn-info btn-sm" title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar" class="btn btn-info btn-sm" title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>

								<?php if ($_SESSION['perfil'] != 3) { // Sólo los perfiles 1 y 2 pueden Ingresar un NUEVO registro ?>
								<button type="button" id="btNuevo" class="btn btn-success btn-sm"
										title="Nueva">
									<i class="fas fa-plus"></i>&nbsp;Nueva
								</button>
								<?php }?>
							</div>
						</div>
					</form>

					<!-- Paginador -->
					<?php $this->mostrarPaginadorMostrandoActual($cantidad, $filtro, $criterio_buscador, $this->controlador);?>

					<!-- Grilla -->
					<div class="row mt-1">
						<div class="col-md-12">
							<?php if ($cantidad > 0) {?>
								<div class="table-responsive">
									<table class="table table-hover table-bordered table-sm small">
										<thead class="thead-light">
											<tr>
												<th width="60" colspan="2">&nbsp;</th>
										      	<th class="text-center" width="80">Fecha</th>
										      	<th>Distinci&oacute;n</th>
										      	<th>Acto</th>
												<th class="text-center">Expediente</th>
												<th class="text-center">Habilitada</th>
											</tr>
										</thead>
										<tbody>
										<?php for ($i = 0; $i < $cantidad; $i++) {$dato = &$datos[$i];?>
											<tr <?=($dato['d_habilitado'] == '0') ? ' class="text-muted"' : '';?> >

										    	<td class="text-center" width="30">
										    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['d_codigo'];?>&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
										    			<i class="fas fa-edit"></i>
										    		</a>
										    	</td>
										    	<td class="text-center" width="30">
										    		<a href="javascript:if(confirm('¿Desea eliminar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['d_codigo'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
										    			<i class="fas fa-trash"></i>
										    		</a>
										    	</td>
												<td class="text-center" width="80">
													<?=$this->formatearFecha($dato['d_fecha']);?>
												</td>
												<td>
													<?=($dato['d_tipo']) ? $this->obtenerNombreTipo($dato['d_tipo']) : '&nbsp';?>
												</td>
										        <td>
										        	<?=($dato['d_acto']) ? $dato['d_acto'] : '&nbsp;';?>
										        </td>
												<td>
													<?=($dato['d_expediente']) ? $dato['d_expediente'] : '&nbsp;';?>
												</td>
												<td class="text-center" width="40">
													<?php if ($dato['d_habilitado'] == '1') {?>
														<a title="Deshabilitar registro" href="javascript:if(confirm('¿Desea deshabilitar el registro?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['d_codigo'];?>&habilitado=<?=$dato['d_habilitado'];?>&pagina=<?=$filtro['pagina'];?>');};">
															<i class="fas fa-check"></i>
														</a>
													<?php } else {?>
														<a title="Habilitar registro" href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=modificarEstado&id=<?=$dato['d_codigo'];?>&habilitado=<?=$dato['d_habilitado'];?>&pagina=<?=$filtro['pagina'];?>');">
															<i class="fas fa-times"></i>
														</a>
													<?php }?>
												</td>
										    </tr>
										<?php }?>
										</tbody>
									</table>
								</div>
							<?php } else {?>
								<div class="alert alert-info">No se han encontrado resultados.</div>
							<?php }?>
						</div>
					</div>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script>
					// Se setea en el combo
					$('#f_distincion').val('<?=($filtro['f_distincion']) ? $filtro['f_distincion'] : 0;?>');
				</script>

			    <script src="<?=URL_JS;?>distinciones/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }

	public function obtenerNombreTipo($cod_tipo)
	{
		switch ($cod_tipo)
		{
			case "CE":
				$nombre_tipo = "Ciudadano Ejemplar";
				break;
			case "CI":
				$nombre_tipo = "Ciudadano Ilustre";
				break;
			case "CM":
				$nombre_tipo = "Ciudadano Marplatense";
				break;
			case "CA":
				$nombre_tipo = "Compromiso Ambiental";
				break;
			case "CS":
				$nombre_tipo = "Compromiso Social";
				break;
			case "DI":
				$nombre_tipo = "Deportista Insigne";
				break;
			case "HD":
				$nombre_tipo = "Hijo Dilecto";
				break;
			case "MA":
				$nombre_tipo = "M&eacute;rito Acad&eacute;mico";
				break;
			case "MC":
				$nombre_tipo = "M&eacute;rito Ciudadano";
				break;
			case "MD":
				$nombre_tipo = "M&eacute;rito Deportivo";
				break;
			case "RE":
				$nombre_tipo = "Reconocimiento";
				break;
			case "SS":
				$nombre_tipo = "Servicio Solidario";
				break;
			case "VD":
				$nombre_tipo = "Vecino Destacado";
				break;
			case "VI":
				$nombre_tipo = "Visitante Ilustre";
				break;
			case "VN":
				$nombre_tipo = "Visitante Notable";
				break;
		}
		
		return $nombre_tipo;
	}
	
}
?>