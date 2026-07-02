<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaDistincionesEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'distinciones';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['d_codigo'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['d_codigo'])) ? 'modificar' : 'insertar';
		$id_definido = (isset($datos['d_codigo'])) ? $datos['d_codigo'] : '';
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
							<?=$titulo_operacion;?> de la Distinci&oacute;n
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
						<input type="hidden" id="d_codigo" name="d_codigo" value="<?=$id_definido;?>" />

						<div class="row my-1">
							<div class="col-md-6">
								<div class="form-group row mt-1">
									<label for="d_fecha" class="col-sm-4 control-label small text-right pt-1">
										Fecha
									</label>
									<div class="col-sm-8">
										<input id="d_fecha" name="d_fecha" class="form-control" width="145" value="<?=(isset($datos['d_fecha'])) ? $this->formatearFecha($datos['d_fecha']) : date("d/m/Y");?>" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="d_tipo" class="col-sm-4 control-label small text-right pt-1">
										Tipo
									</label>
									<div class="col-sm-8">
										<select id="d_tipo" name="d_tipo" class="form-control form-control-sm" >
											<option value="0">seleccione</option>
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
								</div>
								<div class="form-group row mt-1">
									<label for="d_acto" class="col-sm-4 control-label small text-right pt-1">
										Acto
									</label>
									<div class="col-sm-8">
										<input  type="text" id="d_acto" name="d_acto"
												value="<?=(isset($datos['d_acto'])) ? $this->reemplazarComillaDoble($datos['d_acto']) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="d_expediente" class="col-sm-4 control-label small text-right pt-1">
										Expediente
									</label>
									<div class="col-sm-8">
										<input  type="text" id="d_expediente" name="d_expediente"
												value="<?=(isset($datos['d_expediente'])) ? $this->reemplazarComillaDoble($datos['d_expediente']) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row">
									<div class="input-group input-group-sm ml-3">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-clipboard"></i></span>
										</div>
										<textarea 
											id="d_contenido" 
											name="d_contenido" 
											class="form-control"
											rows="4" 
											placeholder="Ingrese aqu&iacute; ..."
											aria-label="Texto"><?=($datos['d_contenido'] != '') ? htmlspecialchars($datos['d_contenido']) : '';?></textarea>
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

				<script>
					$('#d_tipo').val('<?=(isset($datos['d_tipo'])) ? $datos['d_tipo'] : '0';?>');
				</script>

				<script src="<?=URL_JS;?>distinciones/edicion.js"></script>
		  	</body>
		</html>
		<?php }
}
?>