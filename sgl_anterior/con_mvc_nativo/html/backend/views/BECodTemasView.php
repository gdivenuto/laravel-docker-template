<?php
/**
 * Vista de Codificadora de Temas.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BECodTemasView extends BaseView {

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
	 * Ejecuta la acción BECodTemasViewActionListado.
	 */
	public function vistaListado() {
		$this->accionVista = new BECodTemasViewActionListado($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición de la Codificadora de Temas.
	 * Ejecuta la acción BECodTemasViewActionEdicion.
	 */
	public function vistaEdicion() {
		$this->accionVista = new BECodTemasViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
