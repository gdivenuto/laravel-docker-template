<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaContenidosWebEdicion extends VistaBase {

	public function __construct() {

		parent::__construct();

		$this->controlador = 'contenidos_web';
	}

	/**
	 * Se muestra el formulario
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 */
	public function mostrar($datos = null, $mensaje = '', $tipo_mensaje = '') {
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
				<?php $this->mostrarContenidoHead();?>

				<!-- Se agrega el CSS del editor Summernote -->
				<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    			<!-- Se agrega el JS del editor Summernote -->
    			<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
			</head>
		  	<body>
			    <div class="container-fluid p-0 px-3">

					<?php $this->mostrarMenuPrincipal();?>

					<div class="row">
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							Edici&oacute;n de la Historia de la Biblioteca
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" method="POST">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="<?=(isset($datos['id'])) ? 'modificar' : 'insertar';?>" />
						<input type="hidden" id="id" name="id" value="<?=(isset($datos['id'])) ? $datos['id'] : '';?>" />
						
						<div class="form-group row mt-1">
							<label for="titulo" class="col-sm-2 control-label small text-right pt-1">
								T&iacute;tulo
							</label>
							<div class="col-sm-6">
								<input  type="text" id="titulo" name="titulo"
										value="<?=(isset($datos['titulo'])) ? $this->reemplazarComillaDoble($datos['titulo']) : '';?>"
										class="form-control form-control-sm" >
							</div>
						</div>
						<div class="form-group row">
							<div class="col-12">
								<textarea id="editor_contenido" name="contenido"><?= ($datos['contenido'] != '') ? html_entity_decode($datos['contenido']) : ''; ?></textarea>
							</div>
						</div>
						
						<div class="row mb-3">
							<div class="col-sm-12 text-center">
								<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n">
									<i class="fas fa-check-circle"></i>&nbsp;Guardar
								</button>
								<?php if (isset($datos['contenido']) && $datos['contenido'] != '') { ?>
									<a  href="<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=generarPdf&id=<?=$datos['id'];?>" target="_blank" class="btn btn-info btn-sm" title="Generar PDF">
										<i class="fas fa-print"></i>&nbsp;Generar PDF
									</a>
								<?php } ?>
								<!-- <button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Cancelar operaci&oacute;n">
									<i class="fas fa-angle-left"></i>&nbsp;Cancelar
								</button> -->
							</div>
						</div>
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script src="<?=URL_JS;?>contenidos_web/edicion.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>