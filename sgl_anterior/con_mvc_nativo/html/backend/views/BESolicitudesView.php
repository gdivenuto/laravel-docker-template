<?php
/**
 * Vista de Solicitudes.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BESolicitudesView extends BaseView {

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
	 * Vista por defecto para el listado de Prestamos de un expediente determinado.
	 * Ejecuta la acción BEPrestamosViewActionListado.
	 */
	public function vistaListado() {
		$this->accionVista = new BESolicitudesViewActionGrilla($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición de la información del préstamo
	 * Número del Libro, Folio del Libro y Observaciones
	 * Ejecuta la acción BEPrestamosViewActionEdicionInfo.
	 */
	public function vistaEdicionInfo() {
		$this->accionVista = new BESolicitudesViewActionEdicionInfo($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición a estado Solicitado al E.E.
	 * Ejecuta la acción BESolicitudesViewActionEdicionEstadoSolicitadoEE.
	 */
	public function vistaEdicionEstadoSolicitadoEE() {
		$this->accionVista = new BESolicitudesViewActionEdicionEstadoSolicitadoEE($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición a estado Ingresado del E.E.
	 * Ejecuta la acción BESolicitudesViewActionEdicionEstadoIngresadoEE.
	 */
	public function vistaEdicionEstadoIngresadoEE() {
		$this->accionVista = new BESolicitudesViewActionEdicionEstadoIngresadoEE($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición a estado Devuelto.
	 * Ejecuta la acción BESolicitudesViewActionEdicionEstadoDevueltoEE.
	 */
	public function vistaEdicionEstadoDevueltoEE() {
		$this->accionVista = new BESolicitudesViewActionEdicionEstadoDevueltoEE($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición a estado Anulado.
	 * Ejecuta la acción BESolicitudesViewActionEdicionEstadoAnulado.
	 */
	public function vistaEdicionEstadoAnulado() {
		$this->accionVista = new BESolicitudesViewActionEdicionEstadoAnulado($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
