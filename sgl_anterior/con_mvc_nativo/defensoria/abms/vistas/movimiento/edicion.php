<?php
if (!isset($_SESSION))
	session_start();

class VistaMovimientoEdicion extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'movimiento';
	}

	/**
	 * Se muestra el formulario
	 * 
	 * @param  integer $numero	    	Número del expediente
	 * @param  string  $mensaje      	Mensaje al usuario
	 * @param  string  $tipo_mensaje 	Tipo del mensaje
	 */
	public function mostrar($numero, $mensaje = '', $tipo_mensaje = '') { ?>
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
							Edici&oacute;n del movimiento del <strong>Expediente N&deg; <?=$numero;?></strong>
						</div>
					</div>
					<ul class="nav nav-tabs mt-1">
						<li class="nav-item">
							<a class="nav-link small disabled" tabindex="-1" aria-disabled="true">Ficha</a>
						</li>
						<li class="nav-item">
							<a class="nav-link small active">Movimientos</a>
						</li>
						<li class="nav-item ml-auto">
							<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n">
								<i class="fas fa-check-circle"></i>&nbsp;Guardar
							</button>
							<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Cancelar operaci&oacute;n">
								<i class="fas fa-angle-left"></i>&nbsp;Cancelar
							</button>
						</li>
					</ul>

					<?=$this->mostrarFormulario($numero, $mensaje, $tipo_mensaje);?>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script src="<?=URL_JS.$this->controlador;?>/edicion.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
	<?php }

	/**
	 * Se muestra el formulario
	 * @param  integer $numero	    	Número del expediente
	 * @param  string  $mensaje 		Mensaje al usuario
	 * @param  string  $tipo_mensaje 	Tipo del mensaje
	 */
	public function mostrarFormulario($numero, $mensaje = '', $tipo_mensaje = '') {
		?>
		<form   
			id="formEdicion" 
			name="formEdicion" 
			class="form-horizontal"
			action="<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=guardar"
			method="POST"
			enctype="multipart/form-data"
		>
			<input 
				type="hidden" id="mensaje" name="mensaje" 
				value="<?=isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '';?>"
			/>
			<input 
				type="hidden" id="tipo_mensaje" name="tipo_mensaje" 
				value="<?=isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : '';?>"
			/>
			<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>" />
			<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
			<input type="hidden" id="numero" name="numero" value="<?=$numero;?>" />

			<?php $_SESSION['mensaje'] = $_SESSION['tipo_mensaje'] = ''; ?>
						
			<div class="row mt-1">
				<div class="col col-md-9">
					<div class="row mt-1">
						<div class="col-12 col-md-5 mx-auto">
                            <div class="custom-file">
                                <input 
                                    type="file" 
                                    class="custom-file-input" 
                                    id="documento" 
                                    name="documento" 
                                    lang="es" 
                                    accept=".pdf, .doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                />
                                <label class="custom-file-label small" for="documento" data-browse="Buscar">
                                    Subir documento (.pdf | .doc | .docx)
                                </label>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</form>
	<?php }
}
?>