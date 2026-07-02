<?php
/**
 * Vista de Codificadora de Estados.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BECodestadosView extends BaseView {

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

		// Asigno rutas por defecto
		$this->baseTemplatePath = PATH_KRAKEN_HTML_BACKEND_TEMPLATES;
		$this->baseUrl = URL_KRAKEN_HTML_BACKEND;
	}

	/**
	 * Vista por defecto para el listado de Codificadora de Estados.
	 * Ejecuta la acción BECodestadosViewActionListado.
	 */
	public function vistaListado() {
		$this->accionVista = new BECodestadosViewActionListado($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición de la Codificadora de Estados.
	 * Ejecuta la acción BECodestadosViewActionEdicion.
	 */
	public function vistaEdicion() {
		$this->accionVista = new BECodestadosViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
