<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaActividadesEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'actividades';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['a_codigo'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['a_codigo'])) ? 'modificar' : 'insertar';
		$id_definido = (isset($datos['a_codigo'])) ? $datos['a_codigo'] : '';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : '';
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
							<?=$titulo_operacion;?> de la Actividad
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
						<input type="hidden" id="a_codigo" name="a_codigo" value="<?=$id_definido;?>" />

						<div class="row my-1">
							<div class="col-md-6">
								<div class="form-group row mt-1">
									<label for="a_fecha" class="col-sm-4 control-label small text-right pt-1">
										Fecha
									</label>
									<div class="col-sm-8">
										<input id="a_fecha" name="a_fecha" class="form-control" width="145" value="<?=(isset($datos['a_fecha'])) ? $this->formatearFecha($datos['a_fecha']) : date("d/m/Y");?>" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="a_hora" class="col-sm-4 control-label small text-right pt-1">
										Hora
									</label>
									<div class="col-sm-3">
										<input  type="text" name="a_hora" id="a_hora"
												value="<?=$datos['a_hora'];?>"
												class="form-control form-control-sm small" style="width:60px"
												onKeyPress="return soloEnteros(event)"
												onkeyup="mascara(this,':',patron_hora,true);">
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="a_titulo" class="col-sm-4 control-label small text-right pt-1">
										T&iacute;tulo
									</label>
									<div class="col-sm-8">
										<input  type="text" id="a_titulo" name="a_titulo"
												value="<?=(isset($datos['a_titulo'])) ? $this->reemplazarComillaDoble($datos['a_titulo']) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row">
									<div class="input-group input-group-sm ml-3">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-clipboard"></i></span>
										</div>
										<textarea id="a_contenido" name="a_contenido" class="form-control"
												  rows="4" placeholder="Ingrese aqu&iacute; ..."
												  aria-label="Texto"><?=($datos['a_contenido'] != '') ? htmlspecialchars($datos['a_contenido']) : '';?></textarea>
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

				<script src="<?=URL_JS;?>actividades/edicion.js"></script>
		  	</body>
		</html>
		<?php }
}
?>