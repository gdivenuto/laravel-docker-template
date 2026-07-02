<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaNotificacionesGrilla extends VistaBase {

	private $controlador;

	public function __construct() {
		parent::__construct();
		$this->controlador = 'notificaciones';
		// Se crea una instancia del modelo
		$this->modelo = new notificacionesModel();
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
		$criterio_buscador = '';
		$criterio_buscador .= "&f_fecha=" . $filtro['f_fecha'];
		$criterio_buscador .= "&f_asunto=" . $filtro['f_asunto'];
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
							Listado de Notificaciones
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
							<div class="col-12 col-md-3 mt-1 mt-md-0">
								<input  type="text" name="f_asunto" id="f_asunto"
										value="<?=(isset($filtro['f_asunto'])) ? $filtro['f_asunto'] : '';?>"
										class="form-control form-control-sm w-100 small" placeholder="T&iacute;tulo...">
							</div>
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<input  id="f_fecha" name="f_fecha"
										class="form-control form-control-sm small" width="130"
										value="<?=(isset($filtro['f_fecha'])) ? $this->formatearFecha($filtro['f_fecha']) : '';?>"
										placeholder="Fecha" />
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
										title="Nueva Notificaci&oacute;n">
									<i class="fas fa-plus"></i>&nbsp;Nueva
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
											<th width="30">&nbsp;</th>
											<th class="text-center">Fecha</th>
									      	<th class="text-center">Asunto</th>
									      	<th class="text-center">Enviada a</th>
											<th class="text-center">Enviada</th>
									    </tr>
									</thead>
									<tbody>
									<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos[$i];?>

										<tr <?=($dato['n_habilitada'] == '0') ? ' class="text-muted"' : '';?> >

											<?php if ($dato['n_enviada'] == '0') { // Si la Notificacion NO fue enviada, se permite Editar y Eliminar ?>
										    	<td class="text-center" width="30">
										    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['n_id'];?>&es_fe_erratas=0&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
										    			<i class="fas fa-edit"></i>
										    		</a>
										    	</td>
										    	<td class="text-center" width="30">
										    		<a href="javascript:if(confirm('¿Desea eliminar la Notificacion?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['n_id'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
										    			<i class="fas fa-trash"></i>
										    		</a>
										    	</td>
										    <?php }if ($dato['n_enviada'] == '1') { // Si la Notificacion fue enviada, se permite Ver sus Suscriptores ?>
												<td class="text-center" width="30">
													<a  href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=verSuscriptores&id_mail=<?=$dato['n_id_mail'];?>&pagina=<?=$filtro['pagina'];?>');"
														title="Ver suscriptores">
										    			<i class="fas fa-eye"></i>
										    		</a>
												</td>
												<td class="text-center" width="30">
													<a  href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['n_id'];?>&es_fe_erratas=1&pagina=<?=$filtro['pagina'];?>');"
														title="Enviar Fe de Erratas">
										    			<i class="fas fa-eraser"></i>
										    		</a>
												</td>
										    <?php }?>
										    <td class="text-center" width="30">
										    	<a  href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=verVistaPrevia&id=<?=$dato['n_id'];?>&pagina=<?=$filtro['pagina'];?>');"
													title="Ver Notificaci&oacute;n">
									    			<i class="far fa-envelope"></i>
									    		</a>
											</td>

											<td class="text-center" width="80">
									        	<?=$this->formatearFecha($dato['n_fecha']);?>
									        </td>
									        <td>
									        	<?=($dato['n_asunto']) ? $dato['n_asunto'] : '&nbsp;';?>
									        </td>
									        <td>
											<?php if ($dato['n_id_grupo_destino'] != null && isset($dato['nombre_lista'])) {
												// Se muestra el nombre del Grupo con los nombres de las Listas
												echo $this->mostrarGrupoDestino($dato['n_id_grupo_destino']) . ', ' . implode(', ', $dato['nombre_lista']);
											} else {
												// Si se envió a un Grupo, se muestra su nombre
												echo ($dato['n_id_grupo_destino'] != null) ? $this->mostrarGrupoDestino($dato['n_id_grupo_destino']) : '';
												// Si se envió por lo menos a una Lista, se muestra/n su nombre/s
												echo (isset($dato['nombre_lista'])) ? implode(', ', $dato['nombre_lista']) : '';
											}?>
									        </td>
									        <td class="text-center" width="40">
									        	<?=($dato['n_enviada'] == '1') ? '<i class="fas fa-check"></i>' : '---';?>
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
					// SE SETEA EL TITULO FILTRADO
					$('f_asunto').value = '<?=($_SESSION['f_notificaciones']['f_asunto']) ? $_SESSION['f_notificaciones']['f_asunto'] : '';?>';
				</script>

			    <script src="<?=URL_JS;?>notificaciones/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }

	/**
	 * Se muestra el nombre (desc) del Grupo Destino respectivo
	 * @param  [integer] $id_grupo_destino Identificador del Grupo de Distribución
	 * @return [string]                    Nombre del Grupo de Distribución
	 */
	private function mostrarGrupoDestino($id_grupo_destino) {
		// Se obtiene la lista de grupos de distribución
		$lista_grupos = $this->modelo->obtenerListaGruposDistribucion();

		foreach ($lista_grupos as $ng) {
			// Si se encuentra
			if ($ng['id'] == $id_grupo_destino) {
				// devuelve su descripción
				return $ng['descripcion'];
			}
		}
		// Sino, devuelve un espacio vacío
		return '&nbsp;';
	}

}
?>
