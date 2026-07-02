<?php
if (!isset($_SESSION))
	session_start();

class VistaBanca25Edicion extends VistaBase {

	public function __construct() {

		parent::__construct();

		$this->controlador = 'banca25';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $mensaje = '', $tipo_mensaje = '') {
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
							Edici&oacute;n de la solicitud N&deg; <?=$datos['id'];?> de Banca 25
						</div>
					</div>
					<form 
						id="formEdicion" 
						name="formEdicion" 
						class="form-horizontal" 
						action="<?=URL_ABMS;?>" 
						method="POST"
					>
						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="guardar" />
						<input type="hidden" id="id" name="id" value="<?=$datos['id'];?>" />
						<input type="hidden" id="fecha" name="fecha" value="<?=$datos['fecha'];?>" />
						<input 
							type="hidden" id="pagina" name="pagina" 
							value="<?=(isset($datos['pagina']) ? $datos['pagina'] : '');?>" />
						
						<div class="row my-1">
							<div class="col-md-6">
								<div class="form-group row mt-1">
									<label for="apellidoynombre" class="col-sm-4 control-label small text-right pt-1">
										Apellido y Nombres
									</label>
									<div class="col-sm-8">
										<input 
											type="text" 
											id="apellidoynombre" 
											name="apellidoynombre"
											value="<?=empty($datos['b_apellidoynombre'])
										        		? LibreriaGeneral::aMayuscula($datos['apellidoynombre'])
										        		: LibreriaGeneral::aMayuscula($datos['b_apellidoynombre']);
										        	?>"
											class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label class="col-sm-4 control-label small text-right pt-1">
										Documento
									</label>
									<div class="col-sm-2">
										<select id="tipodoc" name="tipodoc" class="form-control form-control-sm">
											<option value="DNI">DNI</option>
											<option value="LC">LC</option>
											<option value="LE">LE</option>
										</select>
									</div>
									<div class="col-sm-3">
										<input 
											type="number" name="nrodoc" id="nrodoc"
											value="<?=empty($datos['b_nrodoc'])
										        		? empty($datos['nrodoc']) 
										        			? '' 
										        			: $datos['nrodoc']
										        		: $datos['b_nrodoc'];
										        	?>"
											class="form-control form-control-sm small"
											onKeyPress="return soloEnteros(event)"
											placeholder="N&uacute;mero"
										/>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="domicilio" class="col-sm-4 control-label small text-right pt-1">
										Domicilio
									</label>
									<div class="col-sm-8">
										<input 
											type="text" 
											id="domicilio" 
											name="domicilio"
											value="<?=empty($datos['b_domicilio'])
										        		? empty($datos['domicilio']) 
										        			? '' 
										        			: $datos['domicilio']
										        		: $datos['b_domicilio'];
										        	?>"
											class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="localidad" class="col-sm-4 control-label small text-right pt-1">
										Localidad
									</label>
									<div class="col-sm-8">
										<input 
											type="text" 
											id="localidad" 
											name="localidad"
											value="<?=empty($datos['b_localidad'])
										        		? empty($datos['localidad']) 
										        			? '' 
										        			: $datos['localidad']
										        		: $datos['b_localidad'];
										        	?>"
											class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="telefono" class="col-sm-4 control-label small text-right pt-1">
										Tel&eacute;fono
									</label>
									<div class="col-sm-8">
										<input 
											type="text" 
											id="telefono" 
											name="telefono"
											value="<?=empty($datos['b_telefono'])
										        		? empty($datos['telefono']) 
										        			? '' 
										        			: $datos['telefono']
										        		: $datos['b_telefono'];
										        	?>"
											class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="mail" class="col-sm-4 control-label small text-right pt-1">
										Mail
									</label>
									<div class="col-sm-8">
										<input 
											type="text" 
											id="mail" 
											name="mail"
											value="<?=empty($datos['b_mail'])
										        		? empty($datos['mail']) 
										        			? '' 
										        			: $datos['mail']
										        		: $datos['b_mail'];
										        	?>"
											class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="institucion_nombre" class="col-sm-4 control-label small text-right pt-1">
										Instituci&oacute;n Nombre
									</label>
									<div class="col-sm-8">
										<input 
											type="text" 
											id="institucion_nombre" 
											name="institucion_nombre"
											value="<?=empty($datos['b_institucion_nombre'])
										        		? empty($datos['institucion_nombre']) 
										        			? '' 
										        			: $datos['institucion_nombre']
										        		: $datos['b_institucion_nombre'];
										        	?>"
											class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="institucion_domicilio" class="col-sm-4 control-label small text-right pt-1">
										Instituci&oacute;n Domicilio
									</label>
									<div class="col-sm-8">
										<input 
											type="text" 
											id="institucion_domicilio" 
											name="institucion_domicilio"
											value="<?=empty($datos['b_institucion_domicilio'])
										        		? empty($datos['institucion_domicilio']) 
										        			? '' 
										        			: $datos['institucion_domicilio']
										        		: $datos['b_institucion_domicilio'];
										        	?>"
											class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row">
									<div class="input-group input-group-sm ml-3">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fas fa-clipboard"></i>&nbsp;Tema
											</span>
										</div>
										<textarea 
											id="tema" 
											name="tema" 
											class="form-control"
											rows="4" 
											placeholder="Tema de la exposici&oacute;n solicitada..."
											aria-label="Texto"
										><?=empty($datos['tema'])
								        		? empty($datos['mensaje']) 
								        			? '' 
								        			: $datos['mensaje']
								        		: $datos['tema'];
								        	?></textarea>
									</div>
								</div>
								<hr>
								<div class="form-group row">
									<label for="descartada" class="col-sm-4 control-label small text-left text-sm-right pt-1">DESCARTADA</label>
									<div class="col-sm-1">
										<input 
											type="checkbox" 
											id="descartada" 
											name="descartada" 
											value="1" 
											<?=($datos['descartada'] == '1') ? 'checked' : '';?>
										/>
									</div>
								</div>
								<hr>
								<div class="form-group row">
									<label for="fecha_sesion" class="col-sm-4 control-label small text-right pt-1">
										Fecha de la Sesi&oacute;n
									</label>
									<div class="col-sm-8">
										<input id="fecha_sesion" name="fecha_sesion" class="form-control" width="145" value="<?=(isset($datos['fecha_sesion'])) ? $this->formatearFecha($datos['fecha_sesion']) : date("d/m/Y");?>" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label class="col-sm-4 control-label small text-right pt-1">
										Expediente/Nota
									</label>
									<div class="col-sm-2">
										<input 
											type="number" name="expe_anio" id="expe_anio"
											value="<?=$datos['expe_anio'];?>"
											class="form-control form-control-sm small"
											onKeyPress="return soloEnteros(event)"
											placeholder="A&ntilde;o"
										/>
									</div>
									<div class="col-sm-2">
										<select id="expe_tipo" name="expe_tipo" class="form-control form-control-sm">
											<option value="0">Tipo</option>
											<option value="E">E</option>
											<option value="N">N</option>
										</select>
									</div>
									<div class="col-sm-3">
										<input 
											type="number" name="expe_numero" id="expe_numero"
											value="<?=$datos['expe_numero'];?>"
											class="form-control form-control-sm small"
											onKeyPress="return soloEnteros(event)"
											placeholder="N&uacute;mero"
										/>
									</div>
								</div>
								<div class="form-group row">
									<div class="input-group input-group-sm ml-3">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-clipboard"></i></span>
										</div>
										<textarea 
											id="observaciones" 
											name="observaciones" 
											class="form-control"
											rows="4" 
											placeholder="Ingrese aqu&iacute; las observaciones ..."
											aria-label="Texto"><?=($datos['observaciones'] != '') 
												? htmlspecialchars($datos['observaciones']) : '';?></textarea>
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
					$('#expe_tipo').val('<?=(isset($datos['expe_tipo']) && $datos['expe_tipo'] != '') 
						? $datos['expe_tipo'] 
						: 0;?>'
					);
				</script>
				<script src="<?=URL_JS.$this->controlador;?>/edicion.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>