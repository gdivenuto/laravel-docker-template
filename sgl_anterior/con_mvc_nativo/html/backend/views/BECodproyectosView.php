<?php
/**
 * Vista de Codificadora de Proyectos.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BECodproyectosView extends BaseView {

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
	 * Vista por defecto para el listado de Codificadora de Temas.
	 * Ejecuta la acción BECodproyectosViewActionListado.
	 */
	public function vistaListado() {
		$this->accionVista = new BECodproyectosViewActionListado($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición de la Codificadora de Temas.
	 * Ejecuta la acción BECodproyectosViewActionEdicion.
	 */
	public function vistaEdicion() {
		$this->accionVista = new BECodproyectosViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
