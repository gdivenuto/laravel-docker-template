<?php
class VistaObservacionDpDocumentos extends VistaBase {

	public function __construct() {
		parent::__construct();
	}

	private function mostrarLink($documento, $titulo) {

		if (is_file(RUTA_OBSERVACIONES_INSCRIPCIONDP.$documento)) {?>
			<p class="small mt-1">
				<a  href="<?=URL_OBSERVACIONES_INSCRIPCIONDP.$documento.'?v='.date("Ymd_Hi");?>" 
					target="_blank" 
					title="<?=$titulo;?>"
				>
					<?=$titulo;?>
				</a>
			</p>
		<?php
		}
	}

	/**
	 * Se renderiza el listado de documentos de la Observación respectiva
	 * 
	 * @param  array $registro
	 */
	public function mostrar($registro = null) {
		
		echo $this->mostrarLink(
			$registro['nombre_acta_designacion_autoridades'],
			"Acta de Designaci&oacute;n de Autoridades"
		);
		echo $this->mostrarLink(
			$registro['nombre_estatuto_social'],
			"Estatuto Social"
		);
		echo $this->mostrarLink(
			$registro['nombre_acta_reunion_comision_directiva'],
			"Acta de Reuni&oacute;n de Comisi&oacute;n Directiva"
		);
		echo $this->mostrarLink(
			$registro['nombre_frente_dni'],
			"Frente de DNI"
		);
		echo $this->mostrarLink(
			$registro['nombre_dorso_dni'],
			"Dorso de DNI"
		);
		echo $this->mostrarLink(
			$registro['nombre_doc_adicional'],
			"Documentaci&oacute;n Adicional"
		);
	}
}