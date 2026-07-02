<?php
/**
 * Vista de CargaDigitalizacionesReservadas.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BECargaDigitalizacionesReservadasView extends BaseView {

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
	 * Vista para el listado del contenido del directorio resources/proyectos/digital/ con la letra "d" (desglose) como sufijo
	 * Ejecuta la acción BECargaDigitalizacionesReservadasViewActionListado.
	 */
	public function vistaListado() {
		$this->accionVista = new BECargaDigitalizacionesReservadasViewActionListado($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
