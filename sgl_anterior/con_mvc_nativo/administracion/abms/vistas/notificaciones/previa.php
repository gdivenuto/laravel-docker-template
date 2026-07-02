<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaNotificacionesPrevia extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'notificaciones';
		
		// Se crea una instancia del modelo
		$this->modelo = new notificacionesModel();
	}

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

	/**
	 * Se muestran los Suscriptores
	 * @param  array $datos
	 */
	public function mostrar($datos = null) {

		$cantidad = (isset($datos)) ? count($datos) : 0;
		?>
		<!DOCTYPE html>
		<html lang="es">
		  	<head>
				<?php $this->mostrarContenidoHead();?>
			    <!-- CSS de la vista previa -->
		    	<link href="<?=URL_CSS;?>vista_previa.css" rel="stylesheet">
			</head>
		  	<body>
			    <div class="container-fluid p-0 px-3">

					<?php $this->mostrarMenuPrincipal();?>

					<!-- Vista para el formulario -->
					<div class="row">
						<div class="col fuente_titulos bg-dark text-white small py-1 titulo_entidad">
							Vista previa de la Notificaci&oacute;n
						</div>
					</div>
					<div class="row mt-1">
						<?php if ($datos['n_enviada'] == '0') {?>
							<button type="button" id="btEnviarMail" class="btn btn-info btn-sm ml-auto" title="Enviar por Mail">
								<i class="fas fa-envelope"></i>&nbsp;Enviar Mail
							</button>
						<?php } else {?>
							<a  class="btn btn-info btn-sm ml-auto"
								href="<?=URL_ABMS;?>?controlador=<?=$this->controlador;?>&accion=generarPdf&id=<?=$datos['n_id'];?>&pagina=<?=$datos['pagina'];?>"
								target="_blank"
								title="Imprimir Notificaci&oacute;n">
								<i class="fas fa-print"></i>&nbsp;Imprimir
							</a>
						<?php }?>
						<button type="button" id="btVolver" class="btn btn-info btn-sm ml-auto mr-3" title="Volver al listado">
							<i class="fas fa-angle-left"></i>&nbsp;Volver al listado
						</button>
					</div>
					<div class="row mt-1">
						<div class="col-md-12">
							<form action="" method="POST" name="formVistaPrevia">

								<input  type="hidden" id="mensaje" name="mensaje" 
										value="<?=(isset($mensaje)) ? $mensaje : '';?>">
						        <input  type="hidden" id="tipo_mensaje" name="tipo_mensaje" 
						        		value="<?=(isset($tipo_mensaje)) ? $tipo_mensaje : '';?>">
								<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
								<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>">
								<input type="hidden" id="pagina" name="pagina" value="<?=$datos['pagina'];?>">
								<input type="hidden" id="n_id" name="n_id" value="<?=$datos['n_id'];?>" />

								<!-- Inicio de la vista previa -->
								<div class="vista_previa_gacetilla_contenedor">
									<!-- Encabezado de la plantilla -->
									<table class="ancho_100_porciento">
										<tr>
											<td width="60%">
												<img src="<?=URL_IMAGENES;?>plantilla_mail/escudo_y_titulos.gif" align="left" border="0" />
											</td>
											<td width="40%">

											</td>
										</tr>
									</table>

									<!-- Línea de 2 colores -->
									<div class="vista_previa_linea_color_A">
										&nbsp;
									</div>

									<!-- [SUBJECT] ASUNTO de la Campaña -->
									<h3 class="vista_previa_gacetilla_titulo">
										<?=$datos['n_asunto'];?>
									</h3>

									<!-- [CONTENT] CONTENIDO de la Campaña -->
									<div class="vista_previa_gacetilla_cuerpo">

										<!-- Fecha en formato gregoriano -->
										<div class="vista_previa_gacetilla_fecha">
											<?=$this->mostrarFechaConNombreDiaCompleto($datos['n_fecha']);?>
										</div>

										<!-- Mensaje -->
										<div class="vista_previa_gacetilla_texto">
											<p><?=($datos['n_mensaje'] != '') ? nl2br($datos['n_mensaje']) : '';?></p>
										</div>

										<!-- Adjuntos -->
										<div class="vista_previa_notificaciones_contenedor_adjuntos">
											<?php
											// Nombre del directorio de adjuntos de la notificación respectiva
											$dir_adjuntos_notificacion = RUTA_ADJUNTOS_NOTIFICACIONES;

											if (!empty($dir_adjuntos_notificacion)) {
												// Si pudo abrirse el directorio de los Adjuntos de la Notificación respectiva
												if ($handle = opendir($dir_adjuntos_notificacion)) {

													while (false !== ($file = readdir($handle))) {
														if ($file != "." && $file != ".." && $file != "index.html") {
															if (LibreriaGeneral::esAdjuntoDe($datos['n_id'], $file)) {
																$listado_adjuntos[] = $file;
															}
														}
													}

													closedir($handle);

													// Si posee archivos la notificación
													if (isset($listado_adjuntos)) {

														sort($listado_adjuntos);

														echo '<strong>Documentos adjuntos:</strong><br><br>';

														// Se recorre el directorio respectivo y por cada archivo que contiene
														// se muestra su enlace para visualizarlo.
														foreach ($listado_adjuntos as $adjunto) {?>
																	&#8226;&nbsp;
											              			<a href="<?=URL_ADJUNTOS_NOTIFICACIONES . $adjunto;?>" title="ver Adjunto" target="_blank">
											              				<?=str_replace($datos['n_id'] . '__', '', $adjunto);?>
											              			</a><br><br>
																<?php }
													}
												}
											} ?>
										</div>
									</div>
									<div class="vista_previa_notificacion_destinatarios_titulo">
										<?=($datos['n_enviada'] == '0') ? "Para enviar a:" : "Enviada a:";?>
									</div>
									<div class="vista_previa_notificacion_destinatarios">
										<?php
										// Si se envió a un Grupo, se muestra su nombre
										if ($datos['n_id_grupo_destino'] != null) {
											echo '<strong>Grupo:</strong>&nbsp;' . $this->mostrarGrupoDestino($datos['n_id_grupo_destino']) . '<br>';
										}

										// Si se envió por lo menos a una Lista, se muestra/n su nombre/s
										if (isset($datos['nombre_lista'])) {
											echo '<br><strong>Listas:</strong>';
											foreach ($datos['nombre_lista'] as $lista) {
												echo '<br>&#8226;&nbsp;' . $lista;
											}

										}?>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>

			    <script src="<?=URL_JS;?>notificaciones/previa.js"></script>
		  	</body>
		</html>
		<?php }

}
