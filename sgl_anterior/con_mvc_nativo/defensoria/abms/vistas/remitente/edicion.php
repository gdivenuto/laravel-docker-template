<?php
if (!isset($_SESSION))
	session_start();

class VistaRemitenteEdicion extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'remitente';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['id'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['id'])) ? 'modificar' : 'insertar';
		$id_definido = (isset($datos['id'])) ? $datos['id'] : '';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1;
		$cant_provincias = isset($datos['provincias']) ? count($datos['provincias']) : 0;
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
							<?=$titulo_operacion;?> del Remitente
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" action="<?=URL_ABMS;?>" method="POST">

						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="<?=$operacion;?>" />
						<input type="hidden" id="pagina" name="pagina" value="<?=$valor_pagina;?>" />
						<input type="hidden" id="id" name="id" value="<?=$id_definido;?>" />
						
						<div class="row my-1">
							<div class="col-md-8 mx-auto">
								<div class="form-group row mt-1">
									<label for="nombre" class="col-sm-2 control-label small text-left text-sm-right color_resaltado pt-1">* Nombre</label>
									<div class="col-sm-4">
										<input type="text" id="nombre" name="nombre" value="<?=(isset($datos['nombre'])) ? htmlspecialchars($datos['nombre']) : '';?>" class="form-control form-control-sm" >
									</div>
									<label for="apellido" class="col-sm-2 control-label small text-left text-sm-right pt-1">Apellido</label>
									<div class="col-sm-4">
										<input type="text" id="apellido" name="apellido" value="<?=(isset($datos['apellido'])) ? htmlspecialchars($datos['apellido']) : '';?>" class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row">
									<label for="provincia_id" class="col-sm-2 control-label small text-left text-sm-right color_resaltado pt-1">* Provincia</label>
									<div class="col-sm-3">
										<select name="provincia_id" id="provincia_id" class="form-control form-control-sm">
											<option value="0">---</option>
											<?php for ($i = 0; $i < $cant_provincias; $i++) {?>
												<option value="<?=$datos['provincias'][$i]['id'];?>">
													<?=$datos['provincias'][$i]['nombre'];?>
												</option>
											<?php }?>
										</select>
									</div>
									<label for="localidad" class="col-sm-1 control-label small text-left text-sm-right pt-1">Localidad</label>
									<div class="col-sm-3">
										<input type="text" id="localidad" name="localidad" value="<?=(isset($datos['localidad'])) ? htmlspecialchars($datos['localidad']) : '';?>" class="form-control form-control-sm" >
									</div>
									<label for="codigo_postal" class="col-sm-1 control-label small text-left text-sm-right pt-1">Cod.Postal</label>
									<div class="col-sm-2">
										<input 
											type="text" 
											id="codigo_postal" 
											name="codigo_postal" 
											value="<?=(isset($datos['codigo_postal'])) 
												? htmlspecialchars($datos['codigo_postal']) 
												: '';?>" 
											class="form-control form-control-sm" 
										/>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="direccion_calle" class="col-sm-2 control-label small text-left text-sm-right pt-1">Direcci&oacute;n</label>
									<div class="col-sm-6">
										<div class="input-group">
											<div class="input-group-prepend">
										    	<span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
										  	</div>
											<input 
												type="text" 
												id="direccion_calle" 
												name="direccion_calle" 
												value="<?=(isset($datos['direccion_calle'])) 
													? htmlspecialchars($datos['direccion_calle']) 
													: '';?>" 
												class="form-control form-control-sm"
												placeholder="Calle"
											/>
										</div>
									</div>
									<div class="col-sm-2">
										<input 
											type="number" 
											id="direccion_numero" 
											name="direccion_numero" 
											value="<?=(isset($datos['direccion_numero'])) 
												? htmlspecialchars($datos['direccion_numero']) 
												: '';?>" 
											class="form-control form-control-sm"
											placeholder="N&uacute;mero"
										/>
									</div>
									<div class="col-sm-1">
										<input 
											type="text" 
											id="direccion_piso" 
											name="direccion_piso" 
											value="<?=(isset($datos['direccion_piso'])) 
												? htmlspecialchars($datos['direccion_piso']) 
												: '';?>" 
											class="form-control form-control-sm"
											placeholder="Piso"
										/>
									</div>
									<div class="col-sm-1">
										<input 
											type="text" 
											id="direccion_departamento" 
											name="direccion_departamento" 
											value="<?=(isset($datos['direccion_departamento'])) 
												? htmlspecialchars($datos['direccion_departamento']) 
												: '';?>" 
											class="form-control form-control-sm"
											placeholder="Dto."
										/>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="telefono_fijo" class="col-sm-2 control-label small text-left text-sm-right pt-1">Tel&eacute;fono fijo</label>
									<div class="col-sm-4">
										<div class="input-group">
											<div class="input-group-prepend">
										    	<span class="input-group-text"><i class="fas fa-phone"></i></span>
										  	</div>
											<input 
												type="number" 
												id="tel_fijo_cod_area" 
												name="tel_fijo_cod_area" 
												value="<?=(isset($datos['tel_fijo_cod_area'])) ? htmlspecialchars($datos['tel_fijo_cod_area']) : '';?>" 
												class="form-control form-control-sm px-1" 
												onkeypress="return soloParaTelefono(event)" 
												placeholder="cod.&aacute;rea"
											/>
											<input 
												type="number" 
												id="tel_fijo_numero" 
												name="tel_fijo_numero" value="<?=(isset($datos['tel_fijo_numero'])) ? htmlspecialchars($datos['tel_fijo_numero']) : '';?>" 
												class="form-control form-control-sm ml-1" 
												onkeypress="return soloParaTelefono(event)" 
												placeholder="n&uacute;mero"
											/>
										</div>
									</div>
									<label class="col-sm-2 control-label small text-left text-sm-right pt-1">M&oacute;vil</label>
									<div class="col-sm-4">
										<div class="input-group">
											<div class="input-group-prepend">
										    	<span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
										  	</div>
											<input 
												type="number" 
												id="movil_cod_area" 
												name="movil_cod_area" 
												value="<?=(isset($datos['movil_cod_area'])) ? htmlspecialchars($datos['movil_cod_area']) : '';?>" 
												class="form-control form-control-sm px-1" 
												onkeypress="return soloParaTelefono(event)" 
												placeholder="cod.&aacute;rea"
											/>
											<input 
												type="number" 
												id="movil_numero" 
												name="movil_numero" 
												value="<?=(isset($datos['movil_numero'])) ? htmlspecialchars($datos['movil_numero']) : '';?>" 
												class="form-control form-control-sm ml-1" 
												onkeypress="return soloParaTelefono(event)" 
												placeholder="n&uacute;mero"
											/>
										</div>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="mail" class="col-sm-2 control-label small text-left text-sm-right pt-1">Mail</label>
									<div class="col-sm-6">
										<div class="input-group">
											<div class="input-group-prepend">
										    	<span class="input-group-text"><i class="fas fa-envelope"></i></span>
										  	</div>
											<input type="text" id="mail" name="mail" value="<?=(isset($datos['mail'])) ? htmlspecialchars($datos['mail']) : '';?>" class="form-control form-control-sm" >
										</div>
									</div>
									<label for="dni" class="col-sm-2 control-label small text-left text-sm-right pt-1">DNI</label>
									<div class="col-sm-2">
										<input 
											type="number" 
											id="dni" 
											name="dni" 
											value="<?=(isset($datos['dni'])) ? htmlspecialchars($datos['dni']) : '';?>"
											class="form-control form-control-sm" 
											placeholder=" (sin puntos)" 
											onKeyPress="javascript:return soloEnterosyPunto(event)"
										/>
									</div>
								</div>
								<div class="form-group row">
									<label for="fecha_alta" class="col-sm-2 control-label small text-left text-sm-right pt-1">Fecha Alta</label>
									<div class="col-sm-2">
										<input id="fecha_alta" name="fecha_alta" class="form-control" width="145" value="<?=(isset($datos['fecha_alta'])) ? $this->formatearFecha($datos['fecha_alta']) : date("d/m/Y");?>" />
									</div>
								</div>
								<div class="form-group row">
									<div class="input-group input-group-sm ml-1">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-clipboard"></i></span>
										</div>
										<textarea id="observaciones" name="observaciones" class="form-control" rows="7" placeholder="Ingrese aqu&iacute; las observaciones..." aria-label="Texto"><?=($datos['observaciones'] != '') ? htmlspecialchars($datos['observaciones']) : '';?></textarea>
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
					$('#provincia_id').val('<?=(isset($datos['provincia_id'])) ? $datos['provincia_id'] : 0;?>');
				</script>

				<script src="<?=URL_JS.$this->controlador;?>/edicion.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>