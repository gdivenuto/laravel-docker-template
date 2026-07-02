<?php
/**
 * Vista de Planillas de Cálculo.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEPlanillasCalculoView extends BaseView {

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
	 * Vista para la Planilla de Cálculo del resultado de la Búsqueda Avanzada.
	 * Ejecuta la acción BEPlanillaCalculoViewActionBusquedaAvanzada.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_planilla_calculo.php');
	 */
	public function vistaPlanillaCalculoBusquedaAvanzada() {
		$this->accionVista = new BEPlanillaCalculoViewActionBusquedaAvanzada($this);
		$this->accionVista->entregar('base_planilla_calculo.php');
	}

	/**
	 * Vista para la Planilla de Cálculo de Expedientes para Expurgo.
	 * Ejecuta la acción BEPlanillaCalculoViewActionExpedientesParaExpurgo.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_planilla_calculo.php');
	 */
	public function vistaPlanillaCalculoExpedientesParaExpurgo() {
		$this->accionVista = new BEPlanillaCalculoViewActionExpedientesParaExpurgo($this);
		$this->accionVista->entregar('base_planilla_calculo.php');
	}
}
?>
