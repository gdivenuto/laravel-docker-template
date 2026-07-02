<?php
/**
 * Vista del Home.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 * 
 * @author Kaleb
 *
 */
class BEHomeView extends BaseView {

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
	public function __construct($viewData)
	{
		// Configuración inicial de la vista
		$viewData['titulo']    = "Sistema de Gesti&oacute;n Legislativa v2";
		$viewData['subtitulo'] = "Bienvenido!";
		//$viewData['texto']   = "";

		// Llamada al constructor del padre
		parent::__construct($viewData);

		// Asigno rutas por defecto
		$this->baseTemplatePath = PATH_KRAKEN_HTML_BACKEND_TEMPLATES;
		$this->baseUrl = URL_KRAKEN_HTML_BACKEND;
	}

}
?>