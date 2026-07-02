<?php
/**
 * Vista de Marcas de los expedientes en una Comisión respectiva.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEMarcaComisionView extends BaseView {

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos que requieren implementación *********************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct($viewData) {
		// Configuración inicial de la vista
		$viewData['titulo'] = "";
		$viewData['subtitulo'] = "";
		$viewData['texto'] = "";

		// Llamada al constructor del padre
		parent::__construct($viewData);
	}

	/**
	 * Vista para el listado de expedientes de una comisión determinada
	 * para definir su marca en dicha comisión
	 * Ejecuta la acción BEMarcaComisionViewActionListado.
	 */
	public function vistaListadoMarcaComision() {
		$this->accionVista = new BEMarcaComisionViewActionListado($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
