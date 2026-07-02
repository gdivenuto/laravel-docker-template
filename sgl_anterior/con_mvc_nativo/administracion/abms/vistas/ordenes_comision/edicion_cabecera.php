<?php
if (!isset($_SESSION))
	session_start();

class VistaOrdenComisionEdicionCabecera extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'ordenes_comision';
	}

	/**
	 * Se muestra el formulario
	 * @param array  $datos
	 * @param string $mensaje
	 * @param string $tipo_mensaje
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '') {
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
							Edici&oacute;n de la cabecera de la Orden.
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" action="<?=URL_ABMS;?>" method="POST">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="modificarCabecera" />
						<input type="hidden" id="id" name="id" value="<?=$datos['id'];?>" />
						
						<div class="row my-1">
							<div class="col-11 mx-auto">
								<div class="form-group row">
									<label for="asunto" class="col-sm-1 control-label small text-right pt-1 px-2">
										Comisi&oacute;n
									</label>
									<div class="col-sm-8 pl-1 mr-3">
										<input 
											id="asunto" 
											name="asunto"
											class="form-control form-control-sm small"
											value="<?=(isset($datos['asunto'])) 
												? $datos['asunto'] : '';?>"
										/>
									</div>
								</div>
								<div class="form-group row">
									<label for="fecha" class="col-sm-1 control-label small text-right pt-1 px-2">
										Fecha
									</label>
									<div class="col-sm-1 pl-0 mr-3">
										<input 
											id="fecha" 
											name="fecha"
											class="form-control form-control-sm small" 
											width="135"
											value="<?=(isset($datos['fecha'])) 
												? $this->formatearFecha($datos['fecha']) 
												: date("d/m/Y");?>"
										/>
									</div>
								</div>
								<div class="form-group row">
									<label for="hora" class="col-sm-1 control-label small text-right ml-1 pt-1 px-2">
										Hora
									</label>
									<div class="col-sm-1 pl-0">
										<input 
											type="text" 
											name="hora" 
											id="hora"
											value="<?=(isset($datos['hora'])) ? $datos['hora'] : '';?>"
											class="form-control form-control-sm small" 
											style="width:60px"
											onKeyPress="return soloEnteros(event)"
											onkeyup="mascara(this,':',patron_hora,true);"
										/>
									</div>
								</div>
								<div class="row mt-3">
									<div class="col-sm-12 text-center">
										<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n">
											<i class="fas fa-check-circle"></i>&nbsp;Guardar
										</button>

										<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Cancelar operaci&oacute;n">
											<i class="fas fa-angle-left"></i>&nbsp;Cancelar
										</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script src="<?=URL_JS.$this->controlador;?>/edicion_cabecera.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>