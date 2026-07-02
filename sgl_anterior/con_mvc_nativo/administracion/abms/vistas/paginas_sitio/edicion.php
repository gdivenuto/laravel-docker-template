<?php
if (!isset($_SESSION))
	session_start();

class VistaPaginasSitioEdicion extends VistaBase {

	public function __construct() {

		parent::__construct();

		$this->controlador = 'paginas_sitio';

		$this->modelo = new paginasSitioModel();
	}

	/**
	 * Se muestra el formulario
	 * @param  array $categorias    Listado de categorías
	 * @param  string $mensaje
	 * @param  string $tipo_mensaje
	 */
	public function mostrar($categorias = null, $mensaje = '', $tipo_mensaje = '') {

		$cant_categorias = (isset($categorias)) ? count($categorias) : 0;
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
							Asignaci&oacute;n del mantenimiento de las P&aacute;ginas del sitio web
						</div>
					</div>

					<form id="formEdicion" name="formEdicion" class="form-horizontal" method="POST">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
						<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
						<input type="hidden" id="accion" name="accion" value="guardar" />
						
						<div id="contenedor_mantenimiento" class="row no-gutters">

							<?php for ($i=0; $i < $cant_categorias; $i++) { ?>
				
						        <div class="col-12 col-sm-6 col-md-2">
						          <ul>
						            <li><?=$categorias[$i]['nombre'];?>
						                <ul class="mt-3">
						                	<?php
						                	// Se obtienen las páginas asociadas a la categoría respectiva
											$paginas = $this->modelo->obtenerPorCategoria($categorias[$i]['id']);

											$cant_paginas = (isset($paginas)) ? count($paginas) : 0;
											for ($p=0; $p < $cant_paginas; $p++) {
											?>
						                    <li>
						                    	<div class="form-group row">
						                    		<input  type="checkbox"
															name="chk_pagina[]"
															value="<?=$paginas[$p]['id'];?>"
															<?=($paginas[$p]['en_mantenimiento']) ? 'checked' : '';?> />

													<label for="" class="control-label small pt-1 ml-3">
														<?=$paginas[$p]['nombre'];?>
														<br>
														<span class="small">
															(<?=$paginas[$p]['url'];?>)
														</span>
													</label>
												</div>
							                </li>
						                    <?php } ?> 
						                </ul>
						            </li>
						          </ul>
						        </div>
						    <?php } ?>
					    </div>
						<div class="row mb-3">
							<div class="col-sm-12 text-center">
								<button type="button" id="btGuardar" class="btn btn-success btn-sm" title="Guardar informaci&oacute;n">
									<i class="fas fa-check-circle"></i>&nbsp;Guardar
								</button>
								<button type="button" id="btCancelar" class="btn btn-info btn-sm" title="Cancelar operaci&oacute;n">
									<i class="fas fa-angle-left"></i>&nbsp;Cancelar
								</button>
								<span class="small">
									<i class="fa fa-exclamation-circle"></i>&nbsp;Las Comisiones Internas se definen 
									<a  class="" 
	                                    href="<?=URL_ABMS;?>?controlador=comisiones_internas&accion=listar">
				                		aqu&iacute;
					            	</a>.
					            </span>
							</div>
						</div>
					</form>
				</div>

				<?php $this->mostrarContenedorModal();?>

				<script src="<?=URL_JS;?>paginas_sitio/edicion.js?v=<?=date("Ymd_Hi");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>