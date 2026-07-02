<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaSeccionesOrdenSesionEdicion extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'secciones_orden_sesion';
	}

	/**
	 * Se muestra el formulario
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $mensaje = '', $tipo_mensaje = '') {

		$titulo_operacion = (isset($datos['codigo'])) ? 'Edici&oacute;n' : 'Alta';

		// 13/07/2022 XXXX
		// Al volver de la obtención de la info de la sección padre
		// mantenemos la operación original (insertar/modificar)
		if (isset($datos['operacion']) && $datos['operacion'] === 'insertar')
			$operacion = 'insertar';
		else
			$operacion = (isset($datos['codigo'])) ? 'modificar' : 'insertar';

		$valor_pagina = (isset($datos['pagina'])) ? $datos['pagina'] : 1;

		// Se define el valor de cada checkbox
		// -----------------------------------
		// if (isset($datos['mostrar_con_salto_pagina']))
		// 	$valor_chk_mostrar_con_salto_pagina = $datos['mostrar_con_salto_pagina'];
		// elseif (isset($datos['padre']['mostrar_con_salto_pagina']))
		// 	$valor_chk_mostrar_con_salto_pagina = $datos['padre']['mostrar_con_salto_pagina'];
		// else
		// 	$valor_chk_mostrar_con_salto_pagina = 0;
		// Descomentar al utilizar PHP 7 en adelante en el servidor
		$valor_chk_mostrar_con_salto_pagina = (isset($datos['mostrar_con_salto_pagina'])) ?? $datos['padre']['mostrar_con_salto_pagina'] ?? 0;

		// if (isset($datos['mostrar_iniciador']))
		// 	$valor_chk_mostrar_iniciador = $datos['mostrar_iniciador'];
		// elseif (isset($datos['padre']['mostrar_iniciador']))
		// 	$valor_chk_mostrar_iniciador = $datos['padre']['mostrar_iniciador'];
		// else
		// 	$valor_chk_mostrar_iniciador = 0;
		// Descomentar al utilizar PHP 7 en adelante en el servidor
		$valor_chk_mostrar_iniciador = $datos['mostrar_iniciador'] ?? $datos['padre']['mostrar_iniciador'] ?? 0;

		// if (isset($datos['mostrar_autor']))
		// 	$valor_chk_mostrar_autor = $datos['mostrar_autor'];
		// elseif (isset($datos['padre']['mostrar_autor']))
		// 	$valor_chk_mostrar_autor = $datos['padre']['mostrar_autor'];
		// else
		// 	$valor_chk_mostrar_autor = 0;
		// Descomentar al utilizar PHP 7 en adelante en el servidor
		$valor_chk_mostrar_autor = $datos['mostrar_autor'] ?? $datos['padre']['mostrar_autor'] ?? 0;

		// if (isset($datos['mostrar_caratula_en_exped']))
		// 	$valor_chk_mostrar_caratula_en_exped = $datos['mostrar_caratula_en_exped'];
		// elseif (isset($datos['padre']['mostrar_caratula_en_exped']))
		// 	$valor_chk_mostrar_caratula_en_exped = $datos['padre']['mostrar_caratula_en_exped'];
		// else
		// 	$valor_chk_mostrar_caratula_en_exped = 0;
		// Descomentar al utilizar PHP 7 en adelante en el servidor
		$valor_chk_mostrar_caratula_en_exped = $datos['mostrar_caratula_en_exped'] ?? $datos['padre']['mostrar_caratula_en_exped'] ?? 0;

		// if (isset($datos['mostrar_caratula_en_nota']))
		// 	$valor_chk_mostrar_caratula_en_nota = $datos['mostrar_caratula_en_nota'];
		// elseif (isset($datos['padre']['mostrar_caratula_en_nota']))
		// 	$valor_chk_mostrar_caratula_en_nota = $datos['padre']['mostrar_caratula_en_nota'];
		// else
		// 	$valor_chk_mostrar_caratula_en_nota = 0;
		// Descomentar al utilizar PHP 7 en adelante en el servidor
		$valor_chk_mostrar_caratula_en_nota = $datos['mostrar_caratula_en_nota'] ?? $datos['padre']['mostrar_caratula_en_nota'] ?? 0;

		// if (isset($datos['mostrar_comisiones']))
		// 	$valor_chk_mostrar_comisiones = $datos['mostrar_comisiones'];
		// elseif (isset($datos['padre']['mostrar_comisiones']))
		// 	$valor_chk_mostrar_comisiones = $datos['padre']['mostrar_comisiones'];
		// else
		// 	$valor_chk_mostrar_comisiones = 0;
		// Descomentar al utilizar PHP 7 en adelante en el servidor
		$valor_chk_mostrar_comisiones = $datos['mostrar_comisiones'] ?? $datos['padre']['mostrar_comisiones'] ?? 0;

		// if (isset($datos['permite_carga_grupal']))
		// 	$valor_chk_permite_carga_grupal = $datos['permite_carga_grupal'];
		// elseif (isset($datos['padre']['permite_carga_grupal']))
		// 	$valor_chk_permite_carga_grupal = $datos['padre']['permite_carga_grupal'];
		// else
		// 	$valor_chk_permite_carga_grupal = 0;
		// Descomentar al utilizar PHP 7 en adelante en el servidor
		$valor_chk_permite_carga_grupal = $datos['permite_carga_grupal'] ?? $datos['padre']['permite_carga_grupal'] ?? 0;

		// Se define si cada checkbox debe estar activo o no
		// ------------------------------------------------
		if (isset($datos['mostrar_con_salto_pagina']) && $datos['mostrar_con_salto_pagina'] == 1)
			$seteo_chk_mostrar_con_salto_pagina = 'checked';
		elseif (isset($datos['padre']['mostrar_con_salto_pagina']) && $datos['padre']['mostrar_con_salto_pagina'] == 1)
			$seteo_chk_mostrar_con_salto_pagina = 'checked';
		else
			$seteo_chk_mostrar_con_salto_pagina = '';

		if (isset($datos['mostrar_iniciador']) && $datos['mostrar_iniciador'] == 1)
			$seteo_chk_mostrar_iniciador = 'checked';
		elseif (isset($datos['padre']['mostrar_iniciador']) && $datos['padre']['mostrar_iniciador'] == 1)
			$seteo_chk_mostrar_iniciador = 'checked';
		else
			$seteo_chk_mostrar_iniciador = '';

		if (isset($datos['mostrar_autor']) && $datos['mostrar_autor'] == 1)
			$seteo_chk_mostrar_autor = 'checked';
		elseif (isset($datos['padre']['mostrar_autor']) && $datos['padre']['mostrar_autor'] == 1)
			$seteo_chk_mostrar_autor = 'checked';
		else
			$seteo_chk_mostrar_autor = '';

		if (isset($datos['mostrar_caratula_en_exped']) && $datos['mostrar_caratula_en_exped'] == 1)
			$seteo_chk_mostrar_caratula_en_exped = 'checked';
		elseif (isset($datos['padre']['mostrar_caratula_en_exped']) && $datos['padre']['mostrar_caratula_en_exped'] == 1)
			$seteo_chk_mostrar_caratula_en_exped = 'checked';
		else
			$seteo_chk_mostrar_caratula_en_exped = '';

		if (isset($datos['mostrar_caratula_en_nota']) && $datos['mostrar_caratula_en_nota'] == 1)
			$seteo_chk_mostrar_caratula_en_nota = 'checked';
		elseif (isset($datos['padre']['mostrar_caratula_en_nota']) && $datos['padre']['mostrar_caratula_en_nota'] == 1)
			$seteo_chk_mostrar_caratula_en_nota = 'checked';
		else
			$seteo_chk_mostrar_caratula_en_nota = '';

		if (isset($datos['mostrar_comisiones']) && $datos['mostrar_comisiones'] == 1)
			$seteo_chk_mostrar_comisiones = 'checked';
		elseif (isset($datos['padre']['mostrar_comisiones']) && $datos['padre']['mostrar_comisiones'] == 1)
			$seteo_chk_mostrar_comisiones = 'checked';
		else
			$seteo_chk_mostrar_comisiones = '';

		if (isset($datos['permite_carga_grupal']) && $datos['permite_carga_grupal'] == 1)
			$seteo_chk_permite_carga_grupal = 'checked';
		elseif (isset($datos['padre']['permite_carga_grupal']) && $datos['padre']['permite_carga_grupal'] == 1)
			$seteo_chk_permite_carga_grupal = 'checked';
		else
			$seteo_chk_permite_carga_grupal = '';
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
							<?=$titulo_operacion;?> de la Secci&oacute;n para Orden del D&iacute;a de Sesi&oacute;n
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

						<div class="row my-1">
							<div class="col-md-6">
								<div class="form-group row mt-1">
									<label for="codigo" class="col-sm-4 control-label small text-right pt-1">* C&oacute;digo</label>
									<div class="col-sm-8">
										<input  type="text" id="codigo" name="codigo"
												value="<?=(isset($datos['codigo'])) ? htmlspecialchars($datos['codigo']) : '';?>"
												class="form-control form-control-sm"
												onKeyPress="return soloEnteros(event)"
												maxlength="8"
												<?= (isset($datos['codigo']) && $datos['codigo']) ? 'readonly' : ''; ?> >
									</div>
								</div>
								<div class="form-group row mt-1">
									<label for="nombre" class="col-sm-4 control-label small text-right pt-1">* Descripci&oacute;n</label>
									<div class="col-sm-8">
										<input  type="text" id="nombre" name="nombre"
												value="<?=(isset($datos['nombre'])) ? htmlspecialchars($datos['nombre']) : '';?>"
												class="form-control form-control-sm" >
									</div>
								</div>
								<div class="form-group row">
									<label for="mostrar_con_salto_pagina" class="col-sm-4 control-label small text-right pt-1">
										Ver con salto de p&aacute;gina
									</label>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input  type="checkbox" id="mostrar_con_salto_pagina" name="mostrar_con_salto_pagina"
											value="<?= $valor_chk_mostrar_con_salto_pagina; ?>"
											<?=$seteo_chk_mostrar_con_salto_pagina;?> >
								</div>
								<div class="form-group row">
									<label for="mostrar_iniciador" class="col-sm-4 control-label small text-right pt-1">
										Ver Iniciador
									</label>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input  type="checkbox" id="mostrar_iniciador" name="mostrar_iniciador"
											value="<?= $valor_chk_mostrar_iniciador; ?>"
											<?=$seteo_chk_mostrar_iniciador;?> >
								</div>
								<div class="form-group row">
									<label for="mostrar_autor" class="col-sm-4 control-label small text-right pt-1">
										Ver Autor
									</label>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input  type="checkbox" id="mostrar_autor" name="mostrar_autor"
											value="<?= $valor_chk_mostrar_autor; ?>"
											<?=$seteo_chk_mostrar_autor;?> >
								</div>
								<div class="form-group row">
									<label for="mostrar_caratula_en_exped" class="col-sm-4 control-label small text-right pt-1">
										Ver Car&aacute;tula en Exped.
									</label>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input  type="checkbox" id="mostrar_caratula_en_exped" name="mostrar_caratula_en_exped"
											value="<?= $valor_chk_mostrar_caratula_en_exped; ?>"
											<?=$seteo_chk_mostrar_caratula_en_exped;?> >
								</div>
								<div class="form-group row">
									<label for="mostrar_caratula_en_nota" class="col-sm-4 control-label small text-right pt-1">
										Ver Car&aacute;tula en Notas
									</label>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input  type="checkbox" id="mostrar_caratula_en_nota" name="mostrar_caratula_en_nota"
											value="<?= $valor_chk_mostrar_caratula_en_nota; ?>"
											<?=$seteo_chk_mostrar_caratula_en_nota;?> >
								</div>
								<div class="form-group row">
									<label for="mostrar_comisiones" class="col-sm-4 control-label small text-right pt-1">
										Ver Comisiones
									</label>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input  type="checkbox" id="mostrar_comisiones" name="mostrar_comisiones"
											value="<?= $valor_chk_mostrar_comisiones;?>"
											<?=$seteo_chk_mostrar_comisiones;?> >
								</div>
								<div class="form-group row">
									<label for="permite_carga_grupal" class="col-sm-4 control-label small text-right pt-1">
										Permite Carga Grupal
									</label>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input  type="checkbox" id="permite_carga_grupal" name="permite_carga_grupal"
											value="<?= $valor_chk_permite_carga_grupal; ?>"
											<?=$seteo_chk_permite_carga_grupal;?> >
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

				<script>

					$('#codigo').blur(function(){
						if ( $('#codigo').val() != '' ) {
							var url  = $('#url_abms').val()+'?controlador='+$('#controlador').val();
								url += '&accion=obtenerDatosSeccionPadre';
								url += '&codigo='+$('#codigo').val();
								url += '&nombre='+$('#nombre').val();
								url += '&operacion='+$('#accion').val(); // Operación original (Alta/Modi)

							redireccionar(url);
						}
					});

					// Si se edita
					if ( $('#codigo').val() != '' )
						$('#nombre').focus();
					else
						$('#codigo').focus();
				</script>

				<script src="<?=URL_JS;?>secciones_orden_sesion/edicion.js"></script>
		  	</body>
		</html>
		<?php }
}
?>
