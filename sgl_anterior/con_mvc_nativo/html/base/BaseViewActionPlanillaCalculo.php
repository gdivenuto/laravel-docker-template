<?php
/**
 * Clase base para la implementación del patrón strategy en las acciones de una vista de tipo Planilla de Cálculo.
 */
class BaseViewActionPlanillaCalculo extends BaseViewAction {
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
	public function __construct(BaseView $view) {
		// Constructor base del BaseViewAction
		parent::__construct($view);
	}

	/**
	 * Método que renderiza el código html de la cabecera de página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarCabecera()
	{
		// NO heredo comportamiento de BaseViewAction
		// NO se incluye aquí la cabecera
		//require($this->vista->baseTemplatePath.'base_planilla_calculo_cabecera.php');
	}

	/**
	 * Método que renderiza el código html del cuerpo de la página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarCuerpo()
	{
		// No heredo comportamiento de BaseViewAction
		// NO se incluye aquí el cuerpo
		//require($this->vista->baseTemplatePath.'base_planilla_calculo_cuerpo.php');
	}
}
?>