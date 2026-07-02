<?php
/**
 * Vista de Préstamos.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEPrestamosView extends BaseView {

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

		// Debo mostrar los errores que vuelven del controlador
		$viewData['tipo_cabecera'] = VISTA_CABECERA_MODAL;

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
		$this->accionVista = new BEPrestamosViewActionGrilla($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición del Prestamo.
	 * Ejecuta la acción BEPrestamosViewActionEdicion.
	 */
	public function vistaEdicion() {
		$this->accionVista = new BEPrestamosViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición a estado Prestado.
	 * Ejecuta la acción BEPrestamosViewActionEdicionEstadoPrestado.
	 */
	public function vistaEdicionEstadoPrestado() {
		$this->accionVista = new BEPrestamosViewActionEdicionEstadoPrestado($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición a estado Devuelto.
	 * Ejecuta la acción BEPrestamosViewActionEdicionEstadoDevuelto.
	 */
	public function vistaEdicionEstadoDevuelto() {
		$this->accionVista = new BEPrestamosViewActionEdicionEstadoDevuelto($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición a estado Anulado.
	 * Ejecuta la acción BEPrestamosViewActionEdicionEstadoAnulado.
	 */
	public function vistaEdicionEstadoAnulado() {
		$this->accionVista = new BEPrestamosViewActionEdicionEstadoAnulado($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición de la información del préstamo
	 * Número del Libro, Folio del Libro y Observaciones
	 * Ejecuta la acción BEPrestamosViewActionEdicionInfo.
	 */
	public function vistaEdicionInfo() {
		$this->accionVista = new BEPrestamosViewActionEdicionInfo($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición de un Nuevo Préstamo
	 * Ejecuta la acción BEPrestamosViewActionEdicionNuevo.
	 */
	public function vistaEdicionNuevo() {
		$this->accionVista = new BEPrestamosViewActionEdicionNuevo($this);
		$this->accionVista->entregar('base.php');
	}

	// *************************************************************************************/
	// *** Los métodos siguientes son utilizados desde la grilla general de Préstamos ***
	// ************************************************************************************/

	/**
	 * Vista por defecto para el listado de Prestamos de un expediente determinado.
	 * Ejecuta la acción BEPrestamosViewActionGrillaGeneral.
	 */
	public function vistaListadoGeneral() {
		$this->accionVista = new BEPrestamosViewActionGrillaGeneral($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el formulario de edición de un Nuevo Préstamo
	 * Ejecuta la acción BEPrestamosViewActionEdicionNuevoGeneral.
	 */
	public function vistaEdicionNuevoGeneral() {
		$this->accionVista = new BEPrestamosViewActionEdicionNuevoGeneral($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
