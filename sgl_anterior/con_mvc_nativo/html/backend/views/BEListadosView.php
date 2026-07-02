<?php
/**
 * Vista de los Listados.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX
 *
 */
class BEListadosView extends BaseView {

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
	 * Vista por defecto para el listado de Expedientes en Comisión.
	 * Ejecuta la acción BEListadosViewActionExpedientesEnComision.
	 */
	public function vistaListadoExpedientesEnComision() {
		$this->accionVista = new BEListadosViewActionExpedientesEnComision($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el listado de Ordenes del Día en Comisión.
	 * Ejecuta la acción BEListadosViewActionOrdenDelDia.
	 */
	public function vistaListadoOrdenDelDia() {
		$this->accionVista = new BEListadosViewActionOrdenDelDia($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el listado de Detalle de Giros.
	 * Ejecuta la acción BEListadosViewActionDetalleDeGiros.
	 */
	public function vistaListadoDetalleDeGiros() {
		$this->accionVista = new BEListadosViewActionDetalleDeGiros($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el listado de Informes.
	 * Ejecuta la acción BEListadosViewActionInformes.
	 */
	public function vistaListadoInformes() {
		$this->accionVista = new BEListadosViewActionInformes($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el listado de Asuntos Entrados.
	 * Ejecuta la acción BEListadosViewActionAsuntosEntrados.
	 */
	public function vistaListadoAsuntosEntrados() {
		$this->accionVista = new BEListadosViewActionAsuntosEntrados($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el listado de Expedientes para Expurgo.
	 * Ejecuta la acción BEListadosViewActionExpedientesParaExpurgo.
	 */
	public function vistaListadoExpedientesParaExpurgo() {
		$this->accionVista = new BEListadosViewActionExpedientesParaExpurgo($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el listado de Expedientes en Préstamo.
	 * Ejecuta la acción BEListadosViewActionExpedientesEnPrestamo.
	 */
	public function vistaListadoExpedientesEnPrestamo() {
		$this->accionVista = new BEListadosViewActionExpedientesEnPrestamo($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el listado de Expedientes sin documento cargado.
	 * Ejecuta la acción BEListadosViewActionExpedientesSinDocumentoCargado.
	 */
	public function vistaListadoExpedientesSinDocumentoCargado() {
		$this->accionVista = new BEListadosViewActionExpedientesSinDocumentoCargado($this);
		$this->accionVista->entregar('base.php');
	}

	/**
	 * Vista por defecto para el listado de Expedientes sin documento cargado.
	 * Ejecuta la acción BEListadosViewActionExpedientesSinDocumentoCargado.
	 */
	public function vistaListadoExpedientesSinDigitalizar() {
		$this->accionVista = new BEListadosViewActionExpedientesSinDigitalizar($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
