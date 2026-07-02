<?php
/**
 * Vista de la Verificación de la Digitalización de Expedientes del D.E..
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEVerificarDigitalizacionView extends BaseView {

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
	}

	/**
	 * Vista para el listado del contenido del directorio resources/proyectos/temp/
	 * Ejecuta la acción BEVerificarDigitalizacionViewActionBuscador.
	 */
	public function vistaBuscador() {
		$this->accionVista = new BEVerificarDigitalizacionViewActionBuscador($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
