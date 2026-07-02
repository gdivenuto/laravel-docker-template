<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaGacetillaPrevia extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'gacetillas';
	}

	/**
	 * Se muestran los Suscriptores
	 * @param  array $datos
	 */
	public function mostrar($datos = null) {

		$cant_imagenes = (isset($datos['fotos'])) ? count($datos['fotos']) : 0;
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
							Vista previa de la Gacetilla
						</div>
					</div>
					<div class="row mt-1">
						<?php if ($datos['g_enviar_por_mail'] == '0') {?>
							<button type="button" id="btEnviarMail" class="btn btn-info btn-sm ml-auto" title="Enviar por Mail">
								<i class="fas fa-envelope"></i>&nbsp;Enviar Mail
							</button>
						<?php } ?>
						<button type="button" id="btVolver" class="btn btn-info btn-sm ml-auto mr-3" title="Volver al listado">
							<i class="fas fa-angle-left"></i>&nbsp;Volver al listado
						</button>
					</div>
					<div class="row mt-1">
						<div class="col-md-12">
							<form action="" method="POST" name="formVistaPrevia">

								<input type="hidden" id="mensaje" name="mensaje" value="<?=$mensaje;?>" />
						        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?=$tipo_mensaje;?>" />
								<input type="hidden" id="url_abms" name="url_abms" value="<?=URL_ABMS;?>">
								<input type="hidden" id="controlador" name="controlador" value="<?=$this->controlador;?>" />
								<input type="hidden" id="pagina" name="pagina" value="<?=$datos['pagina'];?>" />
								<input type="hidden" id="g_codigo" name="g_codigo" value="<?=$datos['g_codigo'];?>" />

								<!-- Inicio de la vista previa -->
								<div class="vista_previa_gacetilla_contenedor">
									<!-- Encabezado de la plantilla -->
									<table class="ancho_100_porciento">
										<tr>
											<td width="60%">
												<img src="<?=URL_IMAGENES;?>plantilla_mail/escudo_y_titulos.gif" align="left" border="0" />
											</td>
											<td width="40%">&nbsp;</td>
										</tr>
									</table>

									<!-- Línea de 2 colores -->
									<div class="vista_previa_linea_color_A">
										&nbsp;
									</div>

									<!-- [SUBJECT] ASUNTO de la Campaña -->
									<h3 class="vista_previa_gacetilla_titulo">
										<?=$datos['g_titulo'];?>
									</h3>

									<!-- [CONTENT] CONTENIDO de la Campaña -->
									<div class="vista_previa_gacetilla_cuerpo">

										<!-- Fecha en formato gregoriano -->
										<div class="vista_previa_gacetilla_fecha">
											<?=$this->mostrarFechaConNombreDiaCompleto($datos['g_fecha']);?>
										</div>

										<!-- Foto principal -->
										<?php if ( isset($datos['g_foto']) && $datos['g_foto'] != '') { // Si posee la foto principal ?>
											<div>
												<img src="<?= URL_FOTOS_GACETILLAS.'resize.php?ancho=800&imagen='.$datos['g_foto']; ?>" />
											</div>
										<?php } ?>

										<!-- Mensaje -->
										<div class="vista_previa_gacetilla_texto">
											<p><?=($datos['g_texto'] != '') ? nl2br($datos['g_texto']) : '';?></p>
										</div>

										<!-- Fotos restantes -->
										<div class="vista_previa_gacetilla_contenedor_fotos_secundarias">
											<?php
											for ($i=0; $i < $cant_imagenes; $i++) { $info_imagen = &$datos['fotos'][$i]; ?>
												
												<div class="vista_previa_gacetilla_contenedor_foto_secundaria">
													
													<img src="<?= URL_FOTOS_GACETILLAS.$info_imagen['fsg_nombre_foto']; ?>" width="97%" />

												</div>
											<?php } ?>
										</div>
									</div>

									<!-- PIE DE LA CAMPAÑA -->
									<div class="vista_previa_gacetilla_pie">
										[FOOTER]
									</div>
									
									<!-- FIRMA DE LA CAMPAÑA -->
									<div class="vista_previa_gacetilla_firma">
										<p>[SIGNATURE]</p>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>

			    <script src="<?=URL_JS;?>gacetillas/previa.js"></script>
		  	</body>
		</html>
		<?php }

}