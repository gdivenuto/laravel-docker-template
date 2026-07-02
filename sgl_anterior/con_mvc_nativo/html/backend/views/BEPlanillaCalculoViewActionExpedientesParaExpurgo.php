<?php
/**
 * Clase base para la implementación del patrón strategy en las acciones de una vista.
 * Define un comportamiento por defecto para una vista base.
 */
class BEPlanillaCalculoViewActionExpedientesParaExpurgo extends BaseViewActionPlanillaCalculo {
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
	 * Método que renderiza el código html de la cabecera de página para la vista actual dentro del ámbito de la acción.
	 */
	public function generarCabecera()
	{
		// NO heredamos comportamiento de BaseViewActionPlanillaCalculo
		// ya que cada planilla de cálculo debe tener su nombre y título respectivo
		require($this->vista->baseTemplatePath.'planillas_calculo/cabeceras/cabecera_expedientes_para_expurgo.php');
	}

	/**
	 * Método que renderiza el código html del cuerpo de la planilla de cálculo
	 */
	public function generarCuerpo()
	{
		require($this->vista->baseTemplatePath.'planillas_calculo/planilla_calculo_expedientes_para_expurgo.php');
	}
}
?>