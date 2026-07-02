<?php
/**
 * Vista del Menú para elegir un Sistema respectivo.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX
 *
 */
class BEMenuSistemasView extends BaseView {

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
	public function __construct() {
		// Asigno rutas por defecto
		$this->baseTemplatePath = PATH_KRAKEN_HTML_BACKEND_TEMPLATES;
		$this->baseUrl = URL_KRAKEN_HTML_BACKEND;
	}

	/**
	 * Vista por defecto para el Menú de Sistemas, para elegir uno determinado.
	 * Ejecuta la acción BEMenuSistemasViewActionListado.
	 */
	public function vistaMenuSistemas() {
		// Instancio la vista del menú de sistemas a elegir
		$this->accionVista = new BEMenuSistemasViewActionListado($this);
		// Se muestra
		$this->accionVista->entregar('base.php');
	}

}
?>
