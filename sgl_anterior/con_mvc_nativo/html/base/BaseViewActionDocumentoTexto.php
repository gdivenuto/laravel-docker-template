<?php
/**
 * Clase base para la implementación del patrón strategy en las acciones de una vista de tipo documento de texto.
 */
class BaseViewActionDocumentoTexto extends BaseViewAction {
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
	 * Método que renderiza el código html de la sección <head>...</head> para la vista actual
	 * dentro del ámbito de la acción.
	 *
	 * SE ANULA, no se permite que herede de BaseViewAction 
	 * PORQUE NO SE UTILIZA en los DOCUMENTOS DE TEXTO
	 */
	public function generarHtmlHeader() { }

	/**
	 * Método que renderiza el código html dentro de la sección <head>...</head> correspondiente a
	 * la inclusión o definición de código JavaScript, para la vista actual dentro del ámbito de la acción.
	 * Se utiliza para especificar únicamente lo correpondiente a JavaScript, sin necesidad de reescribir 
	 * todo el código del método generarHtmlHeader().
	 *
	 * SE ANULA, no se permite que herede de BaseViewAction 
	 * PORQUE NO SE UTILIZA en los DOCUMENTOS DE TEXTO
	 */
	public function generarHtmlHeaderJS() { }

	/**
	 * Método que renderiza el código html dentro de la sección <head>...</head> correspondiente a
	 * la inclusión o definición de código CSS, para la vista actual dentro del ámbito de la acción.
	 * Se utiliza para especificar únicamente lo correpondiente a CSS, sin necesidad de reescribir 
	 * todo el código del método generarHtmlHeader().
	 *
	 * SE ANULA, no se permite que herede de BaseViewAction 
	 * PORQUE NO SE UTILIZA en los DOCUMENTOS DE TEXTO
	 */
	public function generarHtmlHeaderCSS() { }

	/**
	 * Método que renderiza el código html del menú principal para la vista actual
	 * dentro del ámbito de la acción.
	 *
	 * SE ANULA, no se permite que herede de BaseViewAction 
	 * PORQUE NO SE UTILIZA en los DOCUMENTOS DE TEXTO
	 */
	public function generarMenu() { }

	/**
	 * Método que renderiza el código html de la cabecera de página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarCabecera()
	{
		// NO heredo comportamiento de BaseViewAction
		require($this->vista->baseTemplatePath.'base_documento_texto_cabecera.php');
	}

	/**
	 * Método que renderiza el código html del criterio de búsqueda utilizado para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarCriterioBusqueda()
	{
		// NO heredo comportamiento de BaseViewAction
		require($this->vista->baseTemplatePath.'base_documento_texto_criterio_busqueda.php');
	}

	/**
	 * Método que renderiza el código html del cuerpo de la página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarCuerpo()
	{
		// No heredo comportamiento de BaseViewAction
		require($this->vista->baseTemplatePath.'base_documento_texto_cuerpo.php');
	}

	/**
	 * Método que renderiza el código html del pie de la página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarPie()
	{
		// No heredo comportamiento de BaseViewAction
		require($this->vista->baseTemplatePath.'base_documento_texto_pie.php');
	}

	/**
	 * Método que renderiza el código html del cuadro de diálogo modal de la página para la vista actual
	 * dentro del ámbito de la acción.
	 *
	 * SE ANULA, no se permite que herede de BaseViewAction 
	 * PORQUE NO SE UTILIZA en los DOCUMENTOS DE TEXTO
	 */
	public function generarModalDialog() { }
}
?>