<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaEquiposHcdEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'equipos_hcd';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        		Info del registro
	 * @param  array $areas		Listado de áreas
	 * @param  string $mensaje
	 * @param  string $tipo_mensaje
	 */
	public function mostrar($datos = null, $areas = null, $responsables = null, $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['id'])) ? 'Edici&oacute;n' : 'Alta';
		$operacion = (isset($datos['id'])) ? 'modificar' : 'insertar';
		$id_definido = (isset($datos['id'])) ? $datos['id'] : '';
		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : '';

		// Se separa la MAC por cada guión
		$parte_direccion_mac = explode("-", $datos['direccion_mac']);
		// Se separa la IP por cada punto
		$parte_ip = explode(".", $datos['ip']);
		// Se separa el nro Wins por cada punto
		$parte_wins = explode(".", $datos['wins']);
		// Se separa el gateway por cada punto
		$parte_gateway = explode(".", $datos['gateway']);

		$cant_areas = (isset($areas)) ? count($areas) : 0;
		$cant_responsables = (isset($responsables)) ? count($responsables) : 0;
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
							<?=$titulo_operacion;?> de Equipo
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
						<input type="hidden" id="id" name="id" value="<?=$id_definido;?>" />

						<input  type="hidden" name="parte_direccion_mac_0_actual" id="parte_direccion_mac_0_actual" 
								value="<?=(isset($parte_direccion_mac[0])) ? $parte_direccion_mac[0] : '';?>" />
						<input  type="hidden" name="parte_direccion_mac_1_actual" id="parte_direccion_mac_1_actual" 
								value="<?=(isset($parte_direccion_mac[1])) ? $parte_direccion_mac[1] : '';?>" />
						<input  type="hidden" name="parte_direccion_mac_2_actual" id="parte_direccion_mac_2_actual" 
								value="<?=(isset($parte_direccion_mac[2])) ? $parte_direccion_mac[2] : '';?>" />
						<input  type="hidden" name="parte_direccion_mac_3_actual" id="parte_direccion_mac_3_actual" 
								value="<?=(isset($parte_direccion_mac[3])) ? $parte_direccion_mac[3] : '';?>" />
						<input  type="hidden" name="parte_direccion_mac_4_actual" id="parte_direccion_mac_4_actual" 
								value="<?=(isset($parte_direccion_mac[4])) ? $parte_direccion_mac[4] : '';?>" />
						<input  type="hidden" name="parte_direccion_mac_5_actual" id="parte_direccion_mac_5_actual" 
								value="<?=(isset($parte_direccion_mac[5])) ? $parte_direccion_mac[5] : '';?>" />

						<div class="row my-1">
							<div class="col-md-6">
								<div class="form-group row mt-1">
									<label for="nombre_netbios" class="col-sm-4 control-label small text-info text-right pt-1">
										Nombre Netbios
									</label>
									<div class="col-sm-8">
										<input  type="text" id="nombre_netbios" name="nombre_netbios"
												value="<?=(isset($datos['nombre_netbios'])) ? htmlspecialchars($datos['nombre_netbios']) : '';?>"
												class="form-control form-control-sm" tabindex="1" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="direccion_mac" class="col-sm-4 control-label small text-right pt-1">
										Direcci&oacute;n MAC
									</label>
									<div class="col-sm-1 p-1 pl-3">
										<input  type="text" name="parte_direccion_mac_0" id="parte_direccion_mac_0"
												value="<?=(isset($parte_direccion_mac[0])) ? $parte_direccion_mac[0] : '';?>" maxlength="2"
												class="form-control form-control-sm"
												tabindex="2" />
									</div>
									<div class="col-sm-1 p-1 pl-3">
										<input  type="text" name="parte_direccion_mac_1" id="parte_direccion_mac_1"
												value="<?=(isset($parte_direccion_mac[1])) ? $parte_direccion_mac[1] : '';?>" maxlength="2"
												class="form-control form-control-sm"
												tabindex="3" />
									</div>
									<div class="col-sm-1 p-1 pl-3">
										<input  type="text" name="parte_direccion_mac_2" id="parte_direccion_mac_2"
												value="<?=(isset($parte_direccion_mac[2])) ? $parte_direccion_mac[2] : '';?>" maxlength="2"
												class="form-control form-control-sm"
												tabindex="4" />
									</div>
									<div class="col-sm-1 p-1 pl-3">
										<input  type="text" name="parte_direccion_mac_3" id="parte_direccion_mac_3"
												value="<?=(isset($parte_direccion_mac[3])) ? $parte_direccion_mac[3] : '';?>" maxlength="2"
												class="form-control form-control-sm"
												tabindex="5" />
									</div>
									<div class="col-sm-1 p-1 pl-3">
										<input  type="text" name="parte_direccion_mac_4" id="parte_direccion_mac_4"
												value="<?=(isset($parte_direccion_mac[4])) ? $parte_direccion_mac[4] : '';?>" maxlength="2"
												class="form-control form-control-sm"
												tabindex="6" />
									</div>
									<div class="col-sm-1 p-1 pl-3">
										<input  type="text" name="parte_direccion_mac_5" id="parte_direccion_mac_5"
												value="<?=(isset($parte_direccion_mac[5])) ? $parte_direccion_mac[5] : '';?>" maxlength="2"
												class="form-control form-control-sm"
												tabindex="7" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="id" class="col-sm-4 control-label small text-right pt-1">
										IP
									</label>
									<div class="col-sm-1 p-1 pl-2 ml-2">
										<input  type="text" name="parte_ip_0" id="parte_ip_0" 
												value="<?=(isset($parte_ip[0])) ? $parte_ip[0] : '';?>"
												maxlength="3" class="form-control form-control-sm"
												onkeypress="return validarDireccionIP(event, '#parte_ip_', 0);"
												onblur="return validarRangoIP(this.value);" tabindex="8" />
									</div>
									<div class="col-sm-1 p-1 pl-2">
										<input  type="text" name="parte_ip_1" id="parte_ip_1" 
												value="<?=(isset($parte_ip[1])) ? $parte_ip[1] : '';?>"
												maxlength="3" class="form-control form-control-sm"
												onkeypress="eliminarPuntoCadena('#parte_ip_0');return validarDireccionIP(event, '#parte_ip_', 1);"
												onblur="return validarRangoIP(this.value);" tabindex="9" />
									</div>
									<div class="col-sm-1 p-1 pl-2">
										<input  type="text" name="parte_ip_2" id="parte_ip_2" 
												value="<?=(isset($parte_ip[2])) ? $parte_ip[2] : '';?>"
												maxlength="3" class="form-control form-control-sm"
												onkeypress="eliminarPuntoCadena('#parte_ip_1');return validarDireccionIP(event, '#parte_ip_', 2);"
												onblur="return validarRangoIP(this.value);" tabindex="10" />
									</div>
									<div class="col-sm-1 p-1 pl-2">
										<input  type="text" name="parte_ip_3" id="parte_ip_3" 
												value="<?=(isset($parte_ip[3])) ? $parte_ip[3] : '';?>"
												maxlength="3" class="form-control form-control-sm"
												onkeypress="eliminarPuntoCadena('#parte_ip_2');return validarDireccionIP(event, '#parte_ip_', 3);"
												onblur="return validarRangoIP(this.value);" tabindex="11" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="nameserver" class="col-sm-4 control-label small text-right pt-1">
										Nameserver
									</label>
									<div class="col-sm-8">
										<input  type="text" id="nameserver" name="nameserver"
												value="<?=(isset($datos['nameserver'])) ? htmlspecialchars($datos['nameserver']) : '';?>"
												class="form-control form-control-sm" tabindex="12" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="wins" class="col-sm-4 control-label small text-right pt-1">
										Wins
									</label>
									<div class="col-sm-1 p-1 pl-2 ml-2">
										<input  type="text" name="parte_wins_0" id="parte_wins_0" 
												value="<?=(isset($parte_wins[0])) ? $parte_wins[0] : '';?>"
												maxlength="3" class="form-control form-control-sm"
												onkeypress="return validarDireccionIP(event, '#parte_wins_', 0);"
												onblur="return validarRangoIP(this.value);" tabindex="13" />
									</div>
									<div class="col-sm-1 p-1 pl-2">
										<input  type="text" name="parte_wins_1" id="parte_wins_1" 
												value="<?=(isset($parte_wins[1])) ? $parte_wins[1] : '';?>"
												maxlength="3" class="form-control form-control-sm"
												onkeypress="eliminarPuntoCadena('#parte_wins_0');return validarDireccionIP(event, '#parte_wins_', 1);"
												onblur="return validarRangoIP(this.value);" tabindex="14" />
									</div>
									<div class="col-sm-1 p-1 pl-2">
										<input  type="text" name="parte_wins_2" id="parte_wins_2" 
												value="<?=(isset($parte_wins[2])) ? $parte_wins[2] : '';?>"
												maxlength="3" class="form-control form-control-sm"
												onkeypress="eliminarPuntoCadena('#parte_wins_1');return validarDireccionIP(event, '#parte_wins_', 2);"
												onblur="return validarRangoIP(this.value);" tabindex="15" />
									</div>
									<div class="col-sm-1 p-1 pl-2">
										<input  type="text" name="parte_wins_3" id="parte_wins_3" 
												value="<?=(isset($parte_wins[3])) ? $parte_wins[3] : '';?>"
												maxlength="3" class="form-control form-control-sm"
												onkeypress="eliminarPuntoCadena('#parte_wins_2');return validarDireccionIP(event, '#parte_wins_', 3);"
												onblur="return validarRangoIP(this.value);" tabindex="16" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="gateway" class="col-sm-4 control-label small text-right pt-1">
										Gateway
									</label>
									<div class="col-sm-1 p-1 pl-2 ml-2">
										<input  type="text" name="parte_gateway_0" id="parte_gateway_0" 
												value="<?=(isset($parte_gateway[0])) ? $parte_gateway[0] : '';?>"
												maxlength="3" class="form-control form-control-sm"
												onkeypress="return validarDireccionIP(event, '#parte_gateway_', 0);"
												onblur="return validarRangoIP(this.value);" tabindex="17" />
									</div>
									<div class="col-sm-1 p-1 pl-2">
										<input  type="text" name="parte_gateway_1" id="parte_gateway_1" 
												value="<?=(isset($parte_gateway[1])) ? $parte_gateway[1] : '';?>"
												maxlength="3" class="form-control form-control-sm"
												onkeypress="eliminarPuntoCadena('#parte_gateway_0');return validarDireccionIP(event, '#parte_gateway_', 1);"
												onblur="return validarRangoIP(this.value);" tabindex="18" />
									</div>
									<div class="col-sm-1 p-1 pl-2">
										<input  type="text" name="parte_gateway_2" id="parte_gateway_2" 
												value="<?=(isset($parte_gateway[2])) ? $parte_gateway[2] : '';?>"
												maxlength="3" class="form-control form-control-sm"
												onkeypress="eliminarPuntoCadena('#parte_gateway_1');return validarDireccionIP(event, '#parte_gateway_', 2);"
												onblur="return validarRangoIP(this.value);" tabindex="19" />
									</div>
									<div class="col-sm-1 p-1 pl-2">
										<input  type="text" name="parte_gateway_3" id="parte_gateway_3" 
												value="<?=(isset($parte_gateway[3])) ? $parte_gateway[3] : '';?>"
												maxlength="3" class="form-control form-control-sm"
												onkeypress="eliminarPuntoCadena('#parte_gateway_2');return validarDireccionIP(event, '#parte_gateway_', 3);"
												onblur="return validarRangoIP(this.value);" tabindex="20" />
									</div>
								</div>

								<div class="form-group row mt-1">
									<label for="nro_inventario" class="col-sm-4 control-label small text-right pt-1">
										Nro. Inventario
									</label>
									<div class="col-sm-8">
										<input  type="text" id="nro_inventario" name="nro_inventario"
												value="<?=(isset($datos['nro_inventario'])) ? htmlspecialchars($datos['nro_inventario']) : '';?>"
												class="form-control form-control-sm" tabindex="21" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fecha_alta" class="col-sm-4 control-label small text-info text-right pt-1">
										Fecha Alta
									</label>
									<div class="col-sm-8">
										<input id="fecha_alta" name="fecha_alta" class="form-control" width="145" tabindex="22"
												value="<?=(isset($datos['fecha_alta'])) ? $this->formatearFecha($datos['fecha_alta']) : date("d/m/Y");?>" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="fecha_caducidad" class="col-sm-4 control-label small text-right pt-1">
										Fecha Caducidad
									</label>
									<div class="col-sm-8">
										<input id="fecha_caducidad" name="fecha_caducidad" class="form-control" width="145" tabindex="23"
												value="<?=(isset($datos['fecha_caducidad'])) ? $this->formatearFecha($datos['fecha_caducidad']) : '';?>" />
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="comentario" class="col-sm-4 control-label small text-right pt-1">
										Comentario
									</label>
									<div class="col-sm-8">
										<input  type="text" id="comentario" name="comentario"
												value="<?=(isset($datos['comentario'])) ? htmlspecialchars($datos['comentario']) : '';?>"
												class="form-control form-control-sm" tabindex="24" >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="cod_area" class="col-sm-4 control-label small text-right pt-1">
										&Aacute;rea
									</label>
									<div class="col-sm-8">
										<select id="cod_area" name="cod_area" class="form-control form-control-sm" tabindex="25" >
											<option value="0">:: Seleccione un &Aacute;rea</option>
											<?php for ($i = 0; $i < $cant_areas; $i++) {?>
												<option value="<?=$areas[$i]['cod_area'];?>" >
													<?=$areas[$i]['nombre_area'];?>
												</option>
											<?php }?>
										</select>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="cod_responsable" class="col-sm-4 control-label small text-right pt-1">
										Responsable
									</label>
									<div class="col-sm-8" id="contenedor_combo_responsables">
										<select id="cod_responsable" name="cod_responsable" class="form-control form-control-sm" tabindex="26" >
											<option value="0">---</option>
										<?php for ($i = 0; $i < $cant_responsables; $i++) {?>
											<option value="<?=$responsables[$i]['cod_responsable'];?>" >
												<?=$responsables[$i]['nombre_responsable'];?>
											</option>
										<?php }?>
										</select>
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="observaciones" class="col-sm-4 control-label small text-right pt-1">
										Observaciones
									</label>
									<div class="col-sm-8">
										<input  type="text" id="observaciones" name="observaciones"
												value="<?=(isset($datos['observaciones'])) ? htmlspecialchars($datos['observaciones']) : '';?>"
												class="form-control form-control-sm" tabindex="27" >
									</div>
								</div>

								<!-- Botones Guardar y Cancelar -->
								<div class="row my-3">
									<div class="col-sm-12 text-center">
										<!-- Botón Guardar -->
										<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n">
											<i class="fas fa-check-circle"></i>&nbsp;Guardar
										</button>
										<!-- Botón Cancelar -->
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
					$('#cod_area').val('<?=(isset($datos['cod_area'])) ? $datos['cod_area'] : 0;?>');

					$('#cod_responsable').val('<?=(isset($datos['cod_responsable'])) ? $datos['cod_responsable'] : 0;?>');
				</script>
				
				<script src="<?=URL_JS;?>equipos_hcd/edicion.js"></script>
				
		  	</body>
		</html>
		<?php }
}
?>