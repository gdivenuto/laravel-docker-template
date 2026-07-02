<?php
/**
 * Vista de Proyectos.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEProyectosView extends BaseView {

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

		// Debo mostrar los errores que vuelven del controlador
		$viewData['tipo_cabecera'] = VISTA_CABECERA_MODAL;

		// Llamada al constructor del padre
		parent::__construct($viewData);

		// Asigno rutas por defecto
		$this->baseTemplatePath = PATH_KRAKEN_HTML_BACKEND_TEMPLATES;
		$this->baseUrl = URL_KRAKEN_HTML_BACKEND;
	}

	/**
	 * Vista por defecto para el listado de proyectos de un expediente determinado.
	 * Ejecuta la acción BEProyectosViewActionListado.
	 */
	public function vistaListado() {
		$this->accionVista = new BEProyectosViewActionGrilla($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición del Proyecto.
	 * Ejecuta la acción BEProyectosViewActionEdicion.
	 */
	public function vistaEdicion() {
		$this->accionVista = new BEProyectosViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
