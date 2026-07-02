<?php
/**
 * Clase base para la implementación del patrón strategy en las acciones de una vista de tipo reporte.
 */
class BaseViewActionReport extends BaseViewAction {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	public $actionParamMarginTop;
	public $actionParamMarginRight;
	public $actionParamMarginBottom;
	public $actionParamMarginLeft;
	public $actionParamOrientation;
	public $actionParamFont;
	public $actionParamDisplayMode; 
	public $actionParamDefaultFont;
	public $actionParamName;
	public $actionParamOutputType;
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

		$this->actionParamMarginTop    = KRAKEN_REPORT_MARGIN_TOP;
		$this->actionParamMarginRight  = KRAKEN_REPORT_MARGIN_RIGHT;
		$this->actionParamMarginBottom = KRAKEN_REPORT_MARGIN_BOTTOM;
		$this->actionParamMarginLeft   = KRAKEN_REPORT_MARGIN_LEFT;

		$this->actionParamMarginBodyTop    = KRAKEN_REPORT_MARGIN_BODY_TOP;
		$this->actionParamMarginBodyRight  = KRAKEN_REPORT_MARGIN_BODY_RIGHT;
		$this->actionParamMarginBodyBottom = KRAKEN_REPORT_MARGIN_BODY_BOTTOM;
		$this->actionParamMarginBodyLeft   = KRAKEN_REPORT_MARGIN_BODY_LEFT;

		$this->actionParamOrientation  = KRAKEN_REPORT_ORIENTATION_VERTICAL;   // Orientación horizontal (L = landscape)
		$this->actionParamFont 		   = KRAKEN_REPORT_HOJA_A4;				   // Tamaño hoja A4
		$this->actionParamDisplayMode  = KRAKEN_REPORT_DISPLAY_MODE_FULL_PAGE; // Modo de salida 
		$this->actionParamDefaultFont  = KRAKEN_REPORT_FONT_ARIAL; 			   // Fuente del documento
		$this->actionParamName		   = 'reporte_'.date("Y_m_d_h_i").'.pdf';  // Nombre del documento por defecto
		$this->actionParamOutputType   = KRAKEN_REPORT_OUTPUT_BROWSER;         // Tipo de salida para el PDF generado. I: browser, F: archivo en disco del server
	}

	/**
	 * Método que renderiza el código html de la sección <head>...</head> para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarHtmlHeader() { }

	/**
	 * Método que renderiza el código html dentro de la sección <head>...</head> correspondiente a
	 * la inclusión o definición de código JavaScript, para la vista actual dentro del ámbito de la acción.
	 * Se utiliza para especificar únicamente lo correpondiente a JavaScript, sin necesidad de reescribir 
	 * todo el código del método generarHtmlHeader().
	 */
	public function generarHtmlHeaderJS() { }

	/**
	 * Método que renderiza el código html dentro de la sección <head>...</head> correspondiente a
	 * la inclusión o definición de código CSS, para la vista actual dentro del ámbito de la acción.
	 * Se utiliza para especificar únicamente lo correpondiente a CSS, sin necesidad de reescribir 
	 * todo el código del método generarHtmlHeader().
	 */
	public function generarHtmlHeaderCSS()
	{
		// No heredo comportamiento de BaseViewAction
		echo "\n<!-- ".get_class($this).".generarHtmlHeaderCSS() -->\n";
		echo '<link rel="stylesheet" type="text/css" href="'.$this->vista->baseUrl.'css/estilo_reporte.css" />'."\n";
	}

	/**
	 * Método que renderiza el código html del menú principal para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarMenu() { }

	/**
	 * Método que renderiza el código html de la cabecera de página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarCabecera()
	{
		// NO heredo comportamiento de BaseViewAction
		echo "\n<!-- ".get_class($this).".generarCabecera() -->\n";
		require($this->vista->baseTemplatePath.'base_reporte_cabecera.php');
	}

	/**
	 * 30/03/2017
	 * Método que renderiza el código html del criterio de búsqueda utilizado para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarCriterioBusqueda()
	{
		// NO heredo comportamiento de BaseViewAction
		echo "\n<!-- ".get_class($this).".generarCriterioBusqueda() -->\n";
		require($this->vista->baseTemplatePath.'base_reporte_criterio_busqueda.php');
	}

	/**
	 * Método que renderiza el código html del cuerpo de la página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarCuerpo()
	{
		// No heredo comportamiento de BaseViewAction
		echo "\n<!-- ".get_class($this).".generarCuerpo() -->\n";
		require($this->vista->baseTemplatePath.'base_reporte_cuerpo.php');
	}

	/**
	 * Método que renderiza el código html del pie de la página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarPie()
	{
		// No heredo comportamiento de BaseViewAction
		echo "\n<!-- ".get_class($this).".generarPie() -->\n";
		require($this->vista->baseTemplatePath.'base_reporte_pie.php');
	}

	/**
	 * Método que renderiza el código html del cuadro de diálogo modal de la página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarModalDialog() { }
}
?>