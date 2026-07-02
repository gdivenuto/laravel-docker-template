<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaUsuariosEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'usuarios';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '')
	{
		$titulo_operacion = (isset($datos['id_usuario'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['id_usuario'])) ? 'modificar' : 'insertar';
		$id_definido = (isset($datos['id_usuario'])) ? $datos['id_usuario'] : '';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1;
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
							<?=$titulo_operacion;?> del Usuario
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" action="<?=URL_ABMS;?>" method="POST">

						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="<?=$operacion;?>" />
						<input type="hidden" id="pagina" name="pagina" value="<?=$valor_pagina;?>" />
						<input type="hidden" id="id_usuario" name="id_usuario" value="<?=$id_definido;?>" />
						
						<div class="row mt-1">
							<div class="col-md-8">
								<div class="form-group row mt-1">
									<label for="codigo_usuario" class="col-sm-3 control-label small text-left text-md-right pt-1">
										* Usuario
									</label>
									<div class="col-sm-3">
										<input  type="text" id="codigo_usuario" name="codigo_usuario"
												value="<?=(isset($datos['codigo_usuario'])) ? htmlspecialchars($datos['codigo_usuario']) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="password_usuario" class="col-sm-3 control-label small text-left text-md-right pt-1">Contrase&ntilde;a</label>
									<div class="col-sm-3">
										<div class="input-group">
											<div class="input-group-prepend">
										    	<span class="input-group-text"><i class="fas fa-key"></i></span>
										  	</div>
											<input type="password" id="password_usuario" name="password_usuario" value="" class="form-control form-control-sm">
										</div>
									</div>
									<label for="confirmar_password_usuario" class="col-sm-2 control-label small text-left text-md-right pt-1">Confirmar</label>
									<div class="col-sm-3">
										<div class="input-group">
											<div class="input-group-prepend">
										    	<span class="input-group-text"><i class="fas fa-key"></i></span>
										  	</div>
											<input type="password" id="confirmar_password_usuario" name="confirmar_password_usuario" value="" class="form-control form-control-sm">
										</div>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="nombre_usuario" class="col-sm-3 control-label small text-left text-md-right pt-1">* Nombre Completo</label>
									<div class="col-sm-8">
										<input  
											type="text" 
											id="nombre_usuario" 
											name="nombre_usuario" 
											value="<?=(isset($datos['nombre_usuario'])) 
												? htmlspecialchars($datos['nombre_usuario']) 
												: '';?>" 
											class="form-control form-control-sm"
											placeholder="(hasta treinta caracteres)"
											maxlength="30"
										/>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="iniciales_usuario" class="col-sm-3 control-label small text-left text-md-right pt-1">Iniciales</label>
									<div class="col-sm-2">
										<div class="input-group">
											<input type="text" id="iniciales_usuario" name="iniciales_usuario" value="<?=(isset($datos['iniciales_usuario'])) ? htmlspecialchars($datos['iniciales_usuario']) : '';?>" class="form-control form-control-sm" >
										</div>
									</div>
									<label for="u_mail" class="col-sm-1 control-label small text-left text-md-right pt-1">Email</label>
									<div class="col-sm-5">
										<div class="input-group">
											<input  type="text" id="u_mail" name="u_mail" class="form-control form-control-sm"
													value="<?=(isset($datos['u_mail'])) ? htmlspecialchars($datos['u_mail']) : '';?>" >
										</div>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="u_legajo" class="col-sm-3 control-label small text-left text-md-right pt-1">Legajo</label>
									<div class="col-sm-2">
										<input  type="text" id="u_legajo" name="u_legajo" 
												value="<?=(isset($datos['u_legajo'])) ? htmlspecialchars($datos['u_legajo']) : '';?>" 
												class="form-control form-control-sm" 
												placeholder="(solo numeros)" 
												onKeyPress="return soloEnteros(event);" >
									</div>
									<div id="nombre_apellido_legajo" class="col-sm-5 text-success"></div>
								</div>
								<div class="form-group row">
									<label for="confirma_giros" class="col-sm-4 control-label small text-left text-md-right pt-1">
										Confirma Giros&nbsp;
										<input type="checkbox" id="confirma_giros" name="confirma_giros" value="1" <?=($datos['confirma_giros'] == '1') ? 'checked' : '';?> >
									</label>
									
									<label for="habilitado_usuario" class="col-sm-4 control-label small text-left text-md-right pt-1">
										Habilitado&nbsp;
										<input type="checkbox" id="habilitado_usuario" name="habilitado_usuario" value="1" <?=( ! isset($datos['habilitado_usuario']) || $datos['habilitado_usuario'] == '1') ? 'checked' : '';?> >
									</label>
								</div>
								<div class="form-group row">
									<div class="input-group input-group-sm ml-3">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fas fa-clipboard"></i></span>
										</div>
										<textarea id="observaciones_usuario" name="observaciones_usuario" class="form-control"
												  rows="4" placeholder="Ingrese aqu&iacute; las observaciones..."
												  aria-label="Texto"><?=(isset($datos['observaciones_usuario']) && $datos['observaciones_usuario'] != '') ? htmlspecialchars($datos['observaciones_usuario']) : '';?></textarea>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<!-- Botones Guardar y Cancelar -->
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
						<div class="row mt-1">
							<div class="col">
								<table class="table table-bordered table-sm fuente_07">
									<thead class="thead-light">
										<tr>
											<th rowspan="2" class="text-center">SISTEMAS</th>
											<th colspan="4" class="text-center">PERFILES</th>
											<th colspan="9" class="text-center">&Aacute;REAS</th>
											<th rowspan="2" class="text-center">Sin acceso</th>
										</tr>
										<tr>
											<!-- Perfiles -->
											<th nowrap>Administrador</th>
											<th nowrap>Altas/Modificaciones</th>
											<!-- <th nowrap>S&oacute;lo Giros</th> -->
											<th nowrap>Consulta/Concejales</th>
											<th nowrap>Consulta Web</th>
											<!-- Areas -->
											<th nowrap>Actas</th>
											<th nowrap>Administraci&oacute;n</th>
											<th nowrap>Biblioteca</th>
											<th nowrap>Comisiones</th>
											<th nowrap>Inform&aacute;tica</th>
											<th nowrap>Mesa Entradas</th>
											<th nowrap>Modernizaci&oacute;n</th>
											<th nowrap>Prensa</th>
											<th nowrap>Presidencia</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td class="font-weight-bold">Administraci&oacute;n</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<!-- <td class="text-center">&nbsp;-</td> -->
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">
												<input type="radio" name="perfil_admin" value="23"
														<?=(isset($datos['perfiles']['perfil_admin']) && $datos['perfiles']['perfil_admin'] == '23') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_admin" value="10"
														<?=(isset($datos['perfiles']['perfil_admin']) && $datos['perfiles']['perfil_admin'] == '10') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_admin" value="11"
														<?=(isset($datos['perfiles']['perfil_admin']) && $datos['perfiles']['perfil_admin'] == '11') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_admin" value="12"
														<?=(isset($datos['perfiles']['perfil_admin']) && $datos['perfiles']['perfil_admin'] == '12') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_admin" value="14"
														<?=(isset($datos['perfiles']['perfil_admin']) && $datos['perfiles']['perfil_admin'] == '14') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_admin" value="24"
														<?=(isset($datos['perfiles']['perfil_admin']) && $datos['perfiles']['perfil_admin'] == '24') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_admin" value="26"
														<?=(isset($datos['perfiles']['perfil_admin']) && $datos['perfiles']['perfil_admin'] == '26') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_admin" value="15"
														<?=(isset($datos['perfiles']['perfil_admin']) && $datos['perfiles']['perfil_admin'] == '15') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_admin" value="25"
														<?=(isset($datos['perfiles']['perfil_admin']) && $datos['perfiles']['perfil_admin'] == '25') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_admin" value="0"
														<?=(!isset($datos['perfiles']['perfil_admin'])) ? 'checked' : '';?> />
											</td>
										</tr>
										<tr>
											<td class="font-weight-bold">Expedientes</td>
											<td class="text-center">
												<input type="radio" name="perfil_exped" value="1"
														<?=(isset($datos['perfiles']['perfil_exped']) && $datos['perfiles']['perfil_exped'] == '1') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_exped" value="2"
														<?=(isset($datos['perfiles']['perfil_exped']) && $datos['perfiles']['perfil_exped'] == '2') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_exped" value="3"
														<?=(isset($datos['perfiles']['perfil_exped']) && $datos['perfiles']['perfil_exped'] == '3') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_exped" value="4"
														<?=( ! isset($datos['perfiles']['perfil_exped']) || $datos['perfiles']['perfil_exped'] == '4') ? 'checked' : '';?> />
											</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">
												<input type="radio" name="perfil_exped" value="0" />
											</td>
										</tr>
										<tr>
											<td class="font-weight-bold">Personal</td>
											<td class="text-center">
												<input type="radio" name="perfil_personal" id="perfil_1_personal" value="1"
														<?=(isset($datos['perfiles']['perfil_personal']) && $datos['perfiles']['perfil_personal'] == '1') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_personal" id="perfil_2_personal" value="2"
														<?=(isset($datos['perfiles']['perfil_personal']) && $datos['perfiles']['perfil_personal'] == '2') ? 'checked' : '';?> />
											</td>
											<!-- <td class="text-center">&nbsp;-</td> -->
											<td class="text-center">
												<input type="radio" name="perfil_personal" id="perfil_3_personal" value="3"
														<?=(isset($datos['perfiles']['perfil_personal']) && $datos['perfiles']['perfil_personal'] == '3') ? 'checked' : '';?> />
											</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">
												<input type="radio" name="perfil_personal" id="perfil_0_personal" value="0"
														<?=(!isset($datos['perfiles']['perfil_personal'])) ? 'checked' : '';?> />
											</td>
										</tr>
										<tr>
											<td class="font-weight-bold">Biblioteca</td>
											<td class="text-center">
												<input type="radio" name="perfil_biblioteca" id="perfil_1_biblioteca" value="1"
														<?=(isset($datos['perfiles']['perfil_biblioteca']) && $datos['perfiles']['perfil_biblioteca'] == '1') ? 'checked' : '';?> />
											</td>
											<td class="text-center">&nbsp;-</td>
											<!-- <td class="text-center">&nbsp;-</td> -->
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">
												<input type="radio" name="perfil_biblioteca" id="perfil_0_biblioteca" value="0"
														<?=(!isset($datos['perfiles']['perfil_biblioteca'])) ? 'checked' : '';?> />
											</td>
										</tr>
										<tr>
											<td class="font-weight-bold">Inventario</td>
											<td class="text-center">
												<input type="radio" name="perfil_inventario" id="perfil_1_inventario" value="1"
														<?=(isset($datos['perfiles']['perfil_inventario']) && $datos['perfiles']['perfil_inventario'] == '1') ? 'checked' : '';?> />
											</td>
											<td class="text-center">&nbsp;-</td>
											<!-- <td class="text-center">&nbsp;-</td> -->
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">
												<input type="radio" name="perfil_inventario" id="perfil_0_inventario" value="0"
														<?=(!isset($datos['perfiles']['perfil_inventario'])) ? 'checked' : '';?> />
											</td>
										</tr>
										<tr>
											<td class="font-weight-bold">Defensor&iacute;a</td>
											<td class="text-center">
												<input type="radio" name="perfil_defensoria" id="perfil_1_defensoria" value="1"
														<?=(isset($datos['perfiles']['perfil_defensoria']) && $datos['perfiles']['perfil_defensoria'] == '1') ? 'checked' : '';?> />
											</td>
											<td class="text-center">
												<input type="radio" name="perfil_defensoria" id="perfil_2_defensoria" value="2"
														<?=(isset($datos['perfiles']['perfil_defensoria']) && $datos['perfiles']['perfil_defensoria'] == '2') ? 'checked' : '';?> />
											</td>
											<!-- <td class="text-center">&nbsp;-</td> -->
											<td class="text-center">
												<input type="radio" name="perfil_defensoria" id="perfil_3_defensoria" value="3"
														<?=(isset($datos['perfiles']['perfil_defensoria']) && $datos['perfiles']['perfil_defensoria'] == '3') ? 'checked' : '';?> />
											</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">&nbsp;-</td>
											<td class="text-center">
												<input type="radio" name="perfil_defensoria" id="perfil_0_defensoria" value="0"
														<?=(!isset($datos['perfiles']['perfil_defensoria'])) ? 'checked' : '';?> />
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script src="<?=URL_JS.$this->controlador;?>/edicion.js?v=<?=date("Ymd_Hi");?>"></script>
			</body>
		</html>
		<?php }
}
?>
