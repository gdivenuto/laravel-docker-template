<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaNotificacionesGruposGrilla extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();
		
		$this->controlador = 'notificaciones_grupos';
		
		// Se crea una instancia del modelo
		$this->modelo = new notificacionesGruposModel();
	}

	/**
	 * Se muestra la grilla
	 * @param  array $datos        [description]
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 * @param  array $filtro       [description]
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '', $filtro) {

		$cantidad = (isset($datos)) ? count($datos) : 0;

		// Se arma el criterio del buscador para la url del paginador
		$criterio_buscador .= "&f_descripcion=" . $filtro['f_descripcion'];

		//$url_informe = URL_ABMS . '?controlador=' . $this->controlador . '&accion=generarPDF' . $criterio_buscador;
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
				<?php $this->mostrarContenidoHead();?>
			</head>
		  	<body>
			    <div class="container-fluid p-0 px-3">

					<?php $this->mostrarMenuPrincipal();?>

					<!-- Vista para la grilla -->
					<div class="row">
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							Listado de grupos de Distribuci&oacute;n
						</div>
					</div>
					<!-- Buscador -->
					<form action="" method="POST" name="formBuscadorGrilla">

						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input type="hidden" id="pagina" name="pagina" value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

						<div class="row mt-1">
							<div class="col-12 col-md-8 mt-1 mt-md-0">
								<input  type="text" name="f_descripcion" id="f_descripcion"
										value="<?=(isset($filtro['f_descripcion'])) ? $filtro['f_descripcion'] : '';?>"
										class="form-control form-control-sm w-100 small" 
										placeholder="Por descripci&oacute;n...">
							</div>
							<div class="col-12 col-md-3 mt-1 mt-md-0">
								<button type="button" id="btBuscar"
										class="btn btn-info btn-sm"
										title="Buscar">
									<i class="fas fa-search"></i>&nbsp;Buscar
								</button>
								<button type="button" id="btLimpiar"
										class="btn btn-info btn-sm"
										title="Limpiar criterio de b&uacute;squeda">
									<i class="fas fa-eraser"></i>&nbsp;Limpiar
								</button>
								<button type="button" id="btNuevo"
										class="btn btn-success btn-sm mt-1 mt-md-0"
										title="Nuevo Grupo">
									<i class="fas fa-plus"></i>&nbsp;Nuevo
								</button>
							</div>
						</div>
					</form>

					<!-- Paginador -->
					<?php $this->mostrarPaginador($cantidad, $filtro, $criterio_buscador, $this->controlador);?>

					<!-- Grilla -->
					<div class="row mt-1">
						<div class="col-md-12">
						<?php if ($cantidad > 0) { ?>
							<div class="table-responsive">
								<table class="table table-hover table-bordered table-sm small">
									<thead class="thead-light">
										<tr>
											<th width="60" colspan="2">&nbsp;</th>
											<th class="text-center">C&oacute;digo</th>
									      	<th class="text-center">Descripci&oacute;n</th>
									      	<th class="text-center">Listas</th>
									    </tr>
									</thead>
									<tbody>
									<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos[$i];?>

										<tr>
									    	<td class="text-center" width="30">
									    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
									    			<i class="fas fa-edit"></i>
									    		</a>
									    	</td>
									    	<td class="text-center" width="30">
									    		<a href="javascript:if(confirm('¿Desea eliminar el Grupo?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
									    			<i class="fas fa-trash"></i>
									    		</a>
									    	</td>
										    
											<td class="text-center" width="80">
									        	<?= ($dato['id']) ? $dato['id'] : '&nbsp;'; ?>
									        </td>
									        <td>
									        	<?= ($dato['descripcion']) ? $dato['descripcion'] : '&nbsp;'; ?>
									        </td>
									        <td>
												<?= ($dato['nombre_lista']) ? implode(', <br> ', $dato['nombre_lista']) : '&nbsp;'; ?>
									        </td>
									    </tr>
									<?php }?>
									</tbody>
								</table>
							</div>
						<?php } else {?>
							<div class="alert alert-info">No se han encontrado resultados.</div>
						<?php }?>
						</div>
					</div>
			    </div>

				<?php $this->mostrarContenedorModal();?>

				<script>
					// Se establece el valor buscado si existiese
					$('#f_descripcion').val('<?=($_SESSION['f_notificaciones_grupos']['f_descripcion']) ? $_SESSION['f_notificaciones_grupos']['f_descripcion'] : '';?>');
				</script>

			    <script src="<?=URL_JS;?>notificaciones_grupos/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }

}
?>