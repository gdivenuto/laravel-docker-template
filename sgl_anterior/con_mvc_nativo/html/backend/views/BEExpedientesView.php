<?php
/**
 * Vista de Expedientes.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 * 
 * @author Kaleb
 *
 */
class BEExpedientesView extends BaseView {

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
	 * Vista por defecto para la búsqueda simple.
	 * Ejecuta la acción BEExpedientesViewActionBusquedaSimple.
	 */
	public function vistaBusquedaSimple() {
		$this->accionVista = new BEExpedientesViewActionBusquedaSimple($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para la búsqueda avanzada.
	 * Ejecuta la acción BEExpedientesViewActionBusquedaAvanzada.
	 */
	public function vistaBusquedaAvanzada() {
		$this->accionVista = new BEExpedientesViewActionBusquedaAvanzada($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para la búsqueda por antecedente.
	 * Ejecuta la acción BEExpedientesViewActionBusquedaPorAntecedente.
	 */
	public function vistaBusquedaPorAntecedente() {
		$this->accionVista = new BEExpedientesViewActionBusquedaPorAntecedente($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición del Expediente.
	 * Ejecuta la acción BEExpedientesViewActionEdicion.
	 */
	public function vistaEdicion() {
		$this->accionVista = new BEExpedientesViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}
}
?>