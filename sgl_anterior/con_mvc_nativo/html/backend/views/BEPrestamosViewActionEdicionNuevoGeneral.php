<?php
/**
 * Clase base para la implementación del patrón strategy en las acciones de una vista.
 * Define un comportamiento por defecto para una vista base.
 */
class BEPrestamosViewActionEdicionNuevoGeneral extends BaseViewActionForm {
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

		echo "\n<!-- ".get_class($this).".generarHtmlHeaderJS() -->\n";

		// Para este caso particular, necesito el DataTables y los scripts de ayuda en grillas
		echo '<!-- Datatables -->'."\n";
		echo '<link rel="stylesheet" type="text/css" href="'.URL_KRAKEN_HTML_LIBRERIAS.'datatables/css/dataTables.bootstrap.min.css" />'."\n";
		echo '<script type="text/javascript" src="'.URL_KRAKEN_HTML_LIBRERIAS.'datatables/js/jquery.dataTables.min.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.URL_KRAKEN_HTML_LIBRERIAS.'datatables/js/dataTables.bootstrap.min.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.$this->vista->baseUrl.'js/grid-control.js?v='.SGL_BUILD_NUMBER.'"></script>'."\n";

		// Agrego su propio JS
		echo '<script type="text/javascript" src="'.$this->vista->baseUrl.'js/prestamos/prestamos-formulario-edicion-nuevo-general.js?v='.SGL_BUILD_NUMBER.'"></script>'."\n";
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
		echo "\n<!-- ".get_class($this).".generarCuerpo() -->\n";
		require($this->vista->baseTemplatePath.'prestamos/prestamos_cuerpo_formulario_edicion_nuevo_general.php');
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