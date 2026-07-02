<?php
/**
 * Vista base para todos los pasos de actuaciones. El resto de las vistas para
 * pasos de actuación deberían heredar de esta.
 *
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEPABaseView extends BaseView {

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
		// Si no tengo la actuacion en el viewData, hay que lanzar una excepcion
		if ((!array_key_exists('actuacion', $viewData)) || is_null($viewData['actuacion']))
			throw new Exception("ERROR: debe especificar una actuación a la vista", 1);

		$viewData['titulo'] = "";
		$viewData['subtitulo'] = "";
		$viewData['texto'] = "";

		// Debo mostrar los errores que vuelven del controlador
		$viewData['tipo_cabecera'] = VISTA_CABECERA_MODAL; // VISTA_CABECERA_ALERT;

		// Llamada al constructor del padre
		parent::__construct($viewData);

		// Asigno rutas por defecto
		$this->baseTemplatePath = PATH_KRAKEN_HTML_BACKEND_TEMPLATES;
		$this->baseUrl = URL_KRAKEN_HTML_BACKEND;
	}

	/**
	 * Funcionalidad comun a todas las vistas de 'PasoActuacion'.
	 * @return [type] [description]
	 */
	public function vistaPasoActuacion()
	{
	}
}
?>
