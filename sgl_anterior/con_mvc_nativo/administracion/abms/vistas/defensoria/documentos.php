<?php
class VistaInscripcionesDocumentos extends VistaBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Se renderiza el listado de documentos del DNI respectivo
	 * @param  integer $dni
	 */
	public function mostrar($dni = null) {
		
		if (is_dir(RUTA_INSCRIPCIONDP)) {
			if ($handle = opendir(RUTA_INSCRIPCIONDP)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != ".." && $file != "index.html") {
						if (strpos( $file, $dni ) === 0) {
							$listado[] = $file;
						}
					}
				}
				closedir($handle);

				if ($listado) {
					sort($listado);
					foreach ($listado as $doc) { ?>
						<p class="small mt-1">
							<a  href="<?=URL_INSCRIPCIONDP.$doc.'?v='.date("Ymd_Hi");?>" 
								target="_blank" 
								title="<?=$doc;?>">
								<?= str_replace($dni."_", "", $doc);?>
							</a>
						</p>
					<?php }
				}
			}
		}
	}
}