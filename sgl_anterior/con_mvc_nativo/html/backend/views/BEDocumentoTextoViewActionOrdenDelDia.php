<?php
/**
 * Clase base para la implementación del patrón strategy en las acciones de una vista.
 * Define un comportamiento por defecto para una vista base.
 */
class BEDocumentoTextoViewActionOrdenDelDia extends BaseViewActionDocumentoTexto {
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
		// NO heredamos comportamiento de BaseViewActionDocumentoTexto
		// ya que cada documento de texto debe tener su nombre y título respectivo
		require($this->vista->baseTemplatePath.'documentos_texto/cabeceras/cabecera_orden_del_dia.php');
	}

	/**
	 * Método que renderiza el código html del criterio de búsqueda utilizado para la vista actual
	 * dentro del ámbito de la acción.
	 *
	 * EN SGLv1 NO SE MUESTRA EL CRITERIO DE BÚSQUEDA PARA Orden del Día
	 * EN SGLv2 LO DEJAMOS VISIBLE, POR ELLO PERMITIMOS QUE HEREDE EL MÉTODO DE BaseViewActionDocumentoTexto
	 */
	//public function generarCriterioBusqueda() { }

	/**
	 * Método que renderiza el código html del cuerpo del documento de texto
	 */
	public function generarCuerpo()
	{
		require($this->vista->baseTemplatePath.'documentos_texto/documento_texto_orden_del_dia.php');
	}

	/**
	 * Método que renderiza el código html del pie de la página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarPie()
	{
		// Heredo comportamiento de BaseViewAction
		parent::generarPie();
	}
}
?>