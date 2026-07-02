<?php
if (!isset($_SESSION))
	session_start();

class VistaBanca25Grilla extends VistaBase {

	public function __construct() {
		parent::__construct();
		$this->controlador = 'banca25';
	}
	
	/**
	 * Se muestra la grilla
	 * @param  array  $datos
	 * @param  string $mensaje
	 * @param  string $tipo_mensaje
	 * @param  array  $filtro
	 */
	public function mostrar($datos, $mensaje = '', $tipo_mensaje = '', $filtro) {

		$cantidad = (isset($datos)) ? count($datos) : 0;

		$criterio_buscador  = "&f_fecha_desde=" . $filtro['f_fecha_desde'];
		$criterio_buscador .= "&f_fecha_hasta=" . $filtro['f_fecha_hasta'];
		$criterio_buscador .= "&f_texto=" . $filtro['f_texto'];
		$criterio_buscador .= "&f_tipo=" . $filtro['f_tipo'];

		$url_listado_pdf = URL_ABMS.'?controlador='.$this->controlador.'&accion=generarInforme'.$criterio_buscador;
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
							Solicitudes/Expositores de <strong>Banca 25</strong>
						</div>
					</div>
					<form method="POST" name="formBuscadorGrilla">

						<input type="hidden" id="perfil_usuario" name="perfil_usuario" value="<?=$_SESSION['perfil1'];?>">
						<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>">
				        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>">
						<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
						<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
						<input type="hidden" id="pagina" name="pagina" value="<?=(isset($filtro['pagina'])) ? $filtro['pagina'] : '';?>">

						<div class="row mt-1">
							<div class="col-12 col-md-3 mt-1 mt-md-0">
								<input 
									type="text" 
									name="f_texto" 
									id="f_texto"
									value="<?=(isset($filtro['f_texto'])) ? $filtro['f_texto'] : '';?>"
									class="form-control form-control-sm w-100 small" 
									placeholder="Por Solicitante, DNI o mail...">
							</div>
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<input  id="f_fecha_desde" name="f_fecha_desde"
										class="form-control form-control-sm small" width="150"
										value="<?=(isset($filtro['f_fecha_desde']) 
											? $this->formatearFecha($filtro['f_fecha_desde']) 
											: '');?>"
										placeholder="Desde" />
							</div>
							<div class="col-12 col-md-2 mt-1 mt-md-0 px-0">
								<input  id="f_fecha_hasta" name="f_fecha_hasta"
										class="form-control form-control-sm small" width="150"
										value="<?=(isset($filtro['f_fecha_hasta']) 
											? $this->formatearFecha($filtro['f_fecha_hasta']) 
											: '');?>"
										placeholder="Hasta" />
							</div>
							<div class="col-12 col-md-2 mt-1 mt-md-0">
								<select id="f_tipo" name="f_tipo" class="form-control form-control-sm">
									<option value="0">Todos</option>
									<option value="1" >Solicitantes</option>
									<option value="2" >Expositores</option>
								</select>
							</div>
							<div class="col-12 col-md-3 mt-1 mt-md-0 px-0">
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
								<?php if ($cantidad > 0) {?>
									<a  href="<?=$url_listado_pdf;?>"
										target="_blank" 
										class="btn btn-info btn-sm mt-1 mt-md-0"
										title="Generar listado">
										<i class="far fa-file-pdf"></i>&nbsp;Listado
									</a>
								<?php } ?>
							</div>
						</div>
					</form>

					<?php $this->mostrarPaginador($cantidad, $filtro, $criterio_buscador, $this->controlador);?>

					<div class="row mt-1">
						<div class="col-md-12">
						<?php if ($cantidad > 0) {?>
							<div class="table-responsive">
								<table class="table table-bordered table-sm small">
									<thead class="thead-light">
										<tr class="small">
											<th width="60" colspan="2">&nbsp;</th>
											<th class="text-center">N&uacute;mero</th>
											<th class="text-center">Fecha Solic.</th>
									      	<th>Solicitante</th>
									      	<th>DNI</th>
									      	<th>Tel&eacute;fono</th>
									      	<th>Mail</th>
									      	<th>Entidad</th>
									      	<th>Tema</th>
									      	<th>Fecha Sesi&oacute;n</th>
									      	<th>Expte/Nota</th>
									      	<th>Observaciones</th>
									    </tr>
									</thead>
									<tbody>
										<?php for ($i = 0; $i < $cantidad; $i++) { $dato = &$datos[$i]; ?>
										<tr class="<?=$this->aRevisar($dato);?>">
											<td class="text-center" width="30">
									    		<a href="javascript:redireccionar('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=editar&id=<?=$dato['id'];?>&pagina=<?=$filtro['pagina'];?>');" title="Editar info">
									    			<i class="fas fa-edit"></i>
									    		</a>
									    	</td>
											<td class="text-center">
												<a  href="javascript:refrescarEnModal('<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=mostrarDocumentos&dni=<?=$dato['nrodoc'];?>');" 
													title="Ver documentos">
													<i class="far fa-file-pdf"></i>
												</a>
											</td>
											<td width="50" class="text-right">
									        	<?=$dato['id'];?>
									        </td>
										    <td class="text-center">
									        	<?=$this->formatearFecha($dato['fecha']);?>
									        </td>
									        <td>
									        	<?=empty($dato['b_apellidoynombre'])
									        		? LibreriaGeneral::aMayuscula($dato['apellidoynombre'])
									        		: LibreriaGeneral::aMayuscula($dato['b_apellidoynombre']);
									        	?>
									        </td>
									        <td>
									        	<?=empty($dato['b_nrodoc'])
									        		? empty($dato['nrodoc']) 
									        			? '&nbsp;' 
									        			: htmlspecialchars($dato['nrodoc'])
									        		: htmlspecialchars($dato['b_nrodoc']);
									        	?>
									        </td>
									        <td>
									        	<?=empty($dato['b_telefono'])
									        		? empty($dato['telefono']) 
									        			? '&nbsp;' 
									        			: htmlspecialchars($dato['telefono'])
									        		: htmlspecialchars($dato['b_telefono']);
									        	?>
									        </td>
									        <td>
									        	<?=empty($dato['b_mail'])
									        		? empty($dato['mail']) 
									        			? '&nbsp;' 
									        			: htmlspecialchars($dato['mail'])
									        		: htmlspecialchars($dato['b_mail']);
									        	?>
									        </td>
									        <td>
									        	<?=empty($dato['b_institucion_nombre'])
									        		? empty($dato['institucion_nombre']) 
									        			? '&nbsp;' 
									        			: htmlspecialchars($dato['institucion_nombre'])
									        		: htmlspecialchars($dato['b_institucion_nombre']);
									        	?>
									        </td>
									        <td>
									        	<?=empty($dato['tema'])
									        		? empty($dato['mensaje']) 
									        			? '&nbsp;' 
									        			: htmlspecialchars($dato['mensaje'])
									        		: htmlspecialchars($dato['tema']);
									        	?>
									        </td>
									        <td class="text-center">
									        	<?=(isset($dato['fecha_sesion']) 
									        		? $this->formatearFecha($dato['fecha_sesion']) 
									        		: '&nbsp;');?>
									        </td>
									        <td class="text-center" width="90">
									        	<?=(isset($dato['expe_anio']) 
									        		? $dato['expe_anio'].'-'.$dato['expe_tipo'].'-'. $dato['expe_numero']
									        		: '&nbsp;');?>
									        </td>
									        <td>
									        	<?=($dato['observaciones']) 
									        		? htmlspecialchars($dato['observaciones']) 
									        		: '&nbsp;';
									        	?>
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
						$('#f_tipo').val('<?=($filtro['f_tipo']) ? $filtro['f_tipo'] : 0;?>');
					});
				</script>
			    <script src="<?=URL_JS.$this->controlador;?>/grilla.js?v=<?=date("Ymd_His");?>"></script>
		  	</body>
		</html>
		<?php }

	private function aRevisar($dato) {
		if ($dato['descartada'] === '1')
			return 'fondo_descartado';

		return (empty($dato['b_apellidoynombre']) && 
				empty($dato['tema']) && 
				empty($dato['fecha_sesion']) && 
				empty($dato['expe_anio']) &&
				empty($dato['expe_tipo']) &&
				empty($dato['expe_numero'])
			   )
			? 'bg-warning'
		 	: '';
	}
}
?>