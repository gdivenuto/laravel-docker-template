<?php
/**
 * Vista que renderiza un formulario de confirmacion de firma digital de un documento.
 * 
 * Clase base para la implementación del patrón strategy en las acciones de una vista.
 * Define un comportamiento por defecto para una vista base.
 */
class BEPAConfirmarFirmaLoteViewActionEdicion extends BEPABaseViewActionForm {
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
	 */
	public function generarHtmlHeader()
	{
		// Heredo comportamiento de BaseViewAction
		parent::generarHtmlHeader();
	}

	/**
	 * Método que renderiza el código html dentro de la sección <head>...</head> correspondiente a
	 * la inclusión o definición de código JavaScript, para la vista actual dentro del ámbito de la acción.
	 * Se utiliza para especificar únicamente lo correpondiente a JavaScript, sin necesidad de reescribir 
	 * todo el código del método generarHtmlHeader().
	 */
	public function generarHtmlHeaderJS()
	{
		// Heredo comportamiento de BaseViewAction
		parent::generarHtmlHeaderJS();
		
		// Agrego su propio JS
		echo '<script type="text/javascript" src="'.$this->vista->baseUrl.'js/actuaciones/paso_confirmar_firma_lote.js?v='.SGL_BUILD_NUMBER.'"></script>'."\n";
	}

	/**
	 * Método que renderiza el código html dentro de la sección <head>...</head> correspondiente a
	 * la inclusión o definición de código CSS, para la vista actual dentro del ámbito de la acción.
	 * Se utiliza para especificar únicamente lo correpondiente a CSS, sin necesidad de reescribir 
	 * todo el código del método generarHtmlHeader().
	 */
	public function generarHtmlHeaderCSS()
	{
		// Heredo comportamiento de BaseViewAction
		parent::generarHtmlHeaderCSS();
	}

	/**
	 * Método que renderiza el código html del menú principal para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarMenu()
	{
		// Heredo comportamiento de BaseViewAction
		parent::generarMenu();
	}

	/**
	 * Método que renderiza el código html de la cabecera de página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarCabecera()
	{
		// Heredo comportamiento de BaseViewAction
		parent::generarCabecera();
	}

	/**
	 * Método que renderiza el código html del cuerpo de la página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarCuerpo()
	{
		parent::generarCuerpo();
		echo "\n<!-- ".get_class($this).".generarCuerpo() -->\n";
		require($this->vista->baseTemplatePath.'actuaciones/confirmar_firma_lote_cuerpo.php');
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

	/**
	 * Método que renderiza el código html del cuadro de diálogo modal de la página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarModalDialog()
	{
		// Heredo comportamiento de BaseViewAction
		parent::generarModalDialog();
	}
}
?>