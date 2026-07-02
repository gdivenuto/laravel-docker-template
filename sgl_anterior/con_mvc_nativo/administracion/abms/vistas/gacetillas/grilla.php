<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaGacetillaGrilla extends VistaBase {

	private $controlador;

	public function __construct() {
		parent::__construct();
		$this->controlador = 'gacetillas';
	}

	public function mostrarTipo($g_tipo) {
		switch ($g_tipo) {
		case 'A':
			return 'Anuncio';
			break;
		case 'P':
			return 'Protocolar';
			break;
		case 'L':
			return 'Legislativa';
			break;
		case 'E':
			return 'Escuela';
			break;
		default:
			return 'Protocolar';
			break;
		}
	}

	public function mostrarActo($g_acto) {
		switch ($g_acto) {
		case 'Cf':
			return 'Conferencias';
			break;
		case 'Cv':
			return 'Convenios';
			break;
		case 'SAP':
			return 'De Sesiones y Actividades de la Presidencia';
			break;
		case 'J':
			return 'Jornadas';
			break;
		case 'Rec':
			return 'Reconocimientos';
			break;
		case 'Reu':
			return 'Reuniones';
			break;
		}
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
		$criterio_buscador = "&f_tipo=" . $filtro['f_tipo'];
		$criterio_buscador .= "&f_acto=" . $filtro['f_acto'];
		$criterio_buscador .= "&f_fecha=" . $filtro['f_fecha'];
		$criterio_buscador .= "&f_titulo=" . $filtro['f_titulo'];
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
							Listado de Gacetillas
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
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<select id="f_tipo" name="f_tipo" class="form-control form-control-sm ">
									<option value="0">Tipo...</option>
									<option value="A">Anuncio</option>
									<option value="E">Escuela</option>
									<option value="L">Legislativa</option>
									<option value="P">Protocolar</option>
								</select>
							</div>
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<select id="f_acto" name="f_acto" class="form-control form-control-sm ">
									<option value="0">Acto...</option>
									<option value="Cf">Conferencias</option>
									<option value="Cv">Convenios</option>
									<option value="SAP">De Sesiones y Actividades de la Presidencia</option>
									<option value="J">Jornadas</option>
									<option value="Rec">Reconocimientos</option>
									<option value="Reu">Reuniones</option>
								</select>
							</div>
							<div class="col-12 col-md-3 mt-1 mt-md-0">
								<input  type="text" name="f_titulo" id="f_titulo"
										value="<?=(isset($filtro['f_titulo'])) ? $filtro['f_titulo'] : '';?>"
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
										title="Nueva Gacetilla">
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
						<?php if ($cantidad > 0) {?>
							<div class="table-responsive">
								<table class="table table-hover table-bordered table-sm small">
									<thead class="thead-light">
										<tr>
											<th width="60" colspan="2">&nbsp;</th>
											<th width="30">&nbsp;</th>
											<th class="text-center">Fecha</th>
									      	<th>T&iacute;tulo</th>
									      	<th>Tipo</th>
									      	<th>Acto</th>
											<th class="text-center">Enviada</th>
									    </tr>
									</thead>
									<tbody>
										<?php for ($i = 0; $i < $cantidad; $i++) {$dato = &$datos[$i];?>

										<tr <?=($dato['g_habilitada'] == '0') ? ' class="text-muted"' : '';?> >

											<td class="text-center" width="30">
									    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['g_codigo'];?>&pagina=<?=$filtro['pagina'];?>');" title="Editar registro">
									    			<i class="fas fa-edit"></i>
									    		</a>
									    	</td>

											<?php if ($dato['g_enviar_por_mail'] == '0') { // Si la Gacetilla NO fue enviada, se permite Eliminar ?>
										    	<td class="text-center" width="30">
										    		<a href="javascript:if(confirm('¿Desea eliminar la Gacetilla?')){redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=eliminar&id=<?=$dato['g_codigo'];?>&pagina=<?=$filtro['pagina'];?>');};" title="Eliminar registro">
										    			<i class="fas fa-trash"></i>
										    		</a>
										    	</td>
										    <?php }if ($dato['g_enviar_por_mail'] == '1') { // Si la Gacetilla fue enviada, se permite Ver sus Suscriptores ?>
												<td class="text-center" width="20">
													<a  href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=verSuscriptores&id_mail=<?=$dato['g_id_mail'];?>&pagina=<?=$filtro['pagina'];?>');" title="Ver suscriptores">
										    			<i class="fas fa-eye"></i>
										    		</a>
												</td>
										    <?php }?>

										    <td class="text-center" width="20">
										    	<a  href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=verVistaPrevia&id=<?=$dato['g_codigo'];?>&pagina=<?=$filtro['pagina'];?>');" title="Ver Gacetilla">
									    			<i class="far fa-envelope"></i>
									    		</a>
											</td>

											<td class="text-center" width="80">
									        	<?=$this->formatearFecha($dato['g_fecha']);?>
									        </td>
									        <td>
									        	<?=($dato['g_titulo']) ? $dato['g_titulo'] : '&nbsp;';?>
									        </td>
									        <td>
									        	<?=$this->mostrarTipo($dato['g_tipo']);?>
									        </td>
									        <td>
									        	<?=$this->mostrarActo($dato['g_acto']);?>
									        </td>
									        <td class="text-center" width="40">
									        	<?=($dato['g_enviar_por_mail'] == '1') ? '<i class="fas fa-check"></i>' : '---';?>
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
					jQuery(document).ready(function() {

						$('#f_tipo').val('<?=(isset($filtro['f_tipo'])) ? $filtro['f_tipo'] : 0;?>');

					    $('#f_acto').val('<?=(isset($filtro['f_acto'])) ? $filtro['f_acto'] : 0;?>');
					});
				</script>
			    <script src="<?=URL_JS;?>gacetillas/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }
}
?>