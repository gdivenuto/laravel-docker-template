<?php
/**
 * Vista de CargaGiros.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BECargaGirosView extends BaseView {

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
	 * Vista para el formulario de carga múltiple de Giros
	 * Ejecuta la acción BECargaGirosViewActionEdicion.
	 */
	public function vistaEdicion() {
		$this->accionVista = new BECargaGirosViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
