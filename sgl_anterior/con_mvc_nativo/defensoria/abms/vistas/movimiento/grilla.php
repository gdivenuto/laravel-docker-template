<?php
if (!isset($_SESSION))
	session_start();

class VistaMovimientoGrilla extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'movimiento';
	}

	/**
	 * Se muestra la grilla
	 * 
	 * @param  integer $numero	    	Número del expediente
	 * @param  array   $listado		 	Lista de movimientos del expediente
	 * @param  string  $mensaje      	Mensaje al usuario
	 * @param  string  $tipo_mensaje 	Tipo del mensaje
	 */
	public function mostrar($numero, $listado, $mensaje = '', $tipo_mensaje = '') {?>
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
							Listado de movimientos del <strong>Expediente N&deg; <?=$numero;?></strong>
						</div>
					</div>
					<ul class="nav nav-tabs mt-1">
						<li class="nav-item">
							<a  class="nav-link small"
								href="<?=URL_ABMS;?>?controlador=expediente&accion=editar&numero=<?=$numero;?>"
							>Ficha</a>
						</li>
						<li class="nav-item">
							<a  class="nav-link small active" href="#">Movimientos</a>
						</li>
						<li class="nav-item ml-auto">
							<div class="row mt-1 pr-3 float-right">
								<?php if ($_SESSION['perfil6'] == 1 || $_SESSION['perfil6'] == 2) { ?>
									<button type="button" 
											id="btNuevo" 
											class="btn btn-success btn-sm mx-2 " 
											title="Nuevo movimiento"
									>
										<i class="fas fa-plus"></i>&nbsp;Nuevo movimiento
									</button>
								<?php }?>
								<button 
									type="button" 
									id="btVolver" 
									class="btn btn-info btn-sm" 
									title="Ir al listado de expedientes"
								>
									<i class="fas fa-angle-left"></i>&nbsp;Volver
								</button>
							</div>
						</li>
					</ul>

					<?=$this->mostrarGrilla($numero, $listado, $mensaje, $tipo_mensaje);?>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script src="<?=URL_JS.$this->controlador;?>/grilla.js?v=<?=date("Ymd_Hi");?>"></script>

		  	</body>
		</html>
	<?php }

	/**
	 * Se muestra la Grilla
	 * 
	 * @param  integer $numero	    	Número del expediente
	 * @param  array   $listado		 	Lista de movimientos del expediente
	 * @param  string  $mensaje      	Mensaje al usuario
	 * @param  string  $tipo_mensaje 	Tipo del mensaje
	 */
	public function mostrarGrilla($numero, $listado, $mensaje = '', $tipo_mensaje = '') {

		$cantidad = (isset($listado)) ? count($listado) : 0;
		?>
		<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>"/>
		<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>"/>
		<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
		<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
		<input type="hidden" id="numero" name="numero" value="<?=$numero;?>">

		<?php $_SESSION['mensaje'] = $_SESSION['tipo_mensaje'] = ''; ?>
				
		<div class="row mt-1">
			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-hover table-bordered table-sm small">
						<tbody>
							<tr>
								<td>
									<a  href="<?=URL_ABMS;?>?controlador=expediente&accion=getCaratula&numero=<?=$numero;?>" 
										title="Ver Car&aacute;tula"
										target="_blank"
									>
										Car&aacute;tula
									</a>
								</td>
							</tr>
							<tr>
								<td>
									<a  href="<?=URL_ABMS;?>?controlador=expediente&accion=getDenuncia&numero=<?=$numero;?>" 
										title="Ver Denuncia"
										target="_blank"
									>
										Denuncia
									</a>
								</td>
							</tr>
							<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$listado[$i]; ?>
								<tr>
									<td>
										<a  href="<?=URL_DOCUMENTOS_MOVIMIENTOS.$dato['documento']?>"
											target="_blank"
										><?= str_replace($dato['numero'].'_', '', $dato['documento']);?></a>
										&nbsp;
										<a  href="javascript:if(confirm('¿Desea eliminar el movimiento?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['id'];?>');};" 
											title="Eliminar movimiento">
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
	<?php }
}?>