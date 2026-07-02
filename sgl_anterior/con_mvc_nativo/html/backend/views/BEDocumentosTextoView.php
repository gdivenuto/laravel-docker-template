<?php
/**
 * Vista de Documentos de Texto.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEDocumentosTextoView extends BaseView {

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
	 * Vista para el documento de texto del resultado de la Búsqueda Avanzada.
	 * Ejecuta la acción BEDocumentoTextoViewActionBusquedaAvanzada.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_documento_texto.php');
	 */
	public function vistaDocumentoTextoBusquedaAvanzada() {
		$this->accionVista = new BEDocumentoTextoViewActionBusquedaAvanzada($this);
		$this->accionVista->entregar('base_documento_texto.php');
	}

	/**
	 * Vista para el documento de texto de Expedientes en Comisión.
	 * Ejecuta la acción BEDocumentoTextoViewActionExpedientesEnComision.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_documento_texto.php');
	 */
	public function vistaDocumentoTextoExpedientesEnComision() {
		$this->accionVista = new BEDocumentoTextoViewActionExpedientesEnComision($this);
		$this->accionVista->entregar('base_documento_texto.php');
	}

	/**
	 * Vista para el documento de texto de Orden del Dia.
	 * Ejecuta la acción BEDocumentoTextoViewActionOrdenDelDia.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_documento_texto.php');
	 */
	public function vistaDocumentoTextoOrdenDelDia() {
		$this->accionVista = new BEDocumentoTextoViewActionOrdenDelDia($this);
		$this->accionVista->entregar('base_documento_texto.php');
	}

	/**
	 * Vista para el documento de texto de Detalle de Giros.
	 * Ejecuta la acción BEDocumentoTextoViewActionDetalleDeGiros.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_documento_texto.php');
	 */
	public function vistaDocumentoTextoDetalleDeGiros() {
		$this->accionVista = new BEDocumentoTextoViewActionDetalleDeGiros($this);
		$this->accionVista->entregar('base_documento_texto.php');
	}

	/**
	 * Vista para el documento de texto de Informes.
	 * Ejecuta la acción BEDocumentoTextoViewActionInformes.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_documento_texto.php');
	 */
	public function vistaDocumentoTextoInformes() {
		$this->accionVista = new BEDocumentoTextoViewActionInformes($this);
		$this->accionVista->entregar('base_documento_texto.php');
	}

	/**
	 * Vista para el documento de texto de Asuntos Entrados.
	 * Ejecuta la acción BEDocumentoTextoViewActionAsuntosEntrados.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_documento_texto.php');
	 */
	public function vistaDocumentoTextoAsuntosEntrados() {
		$this->accionVista = new BEDocumentoTextoViewActionAsuntosEntrados($this);
		$this->accionVista->entregar('base_documento_texto.php');
	}

	/**
	 * Vista para el documento de texto de Expedientes para Expurgo.
	 * Ejecuta la acción BEDocumentoTextoViewActionExpedientesParaExpurgo.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_documento_texto.php');
	 */
	public function vistaDocumentoTextosExpedientesParaExpurgo() {
		$this->accionVista = new BEDocumentoTextoViewActionExpedientesParaExpurgo($this);
		$this->accionVista->entregar('base_documento_texto.php');
	}

	/**
	 * Vista para el documento de texto de Expedientes en Préstamo.
	 * Ejecuta la acción BEDocumentoTextoViewActionExpedientesEnPrestamo.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_documento_texto.php');
	 */
	public function vistaDocumentoTextosExpedientesEnPrestamo() {
		$this->accionVista = new BEDocumentoTextoViewActionExpedientesEnPrestamo($this);
		$this->accionVista->entregar('base_documento_texto.php');
	}

	/**
	 * Vista para el documento de texto de Expedientes sin documento cargado
	 * Ejecuta la acción BEDocumentoTextoViewActionExpedientesSinDocumentoCargado.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_documento_texto.php');
	 */
	public function vistaDocumentoTextosExpedientesSinDocumentoCargado() {
		$this->accionVista = new BEDocumentoTextoViewActionExpedientesSinDocumentoCargado($this);
		$this->accionVista->entregar('base_documento_texto.php');
	}

	/**
	 * Vista para el documento de texto de Expedientes sin digitalizar
	 * Ejecuta la acción BEDocumentoTextoViewActionExpedientesSinDigitalizar.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_documento_texto.php');
	 */
	public function vistaDocumentoTextosExpedientesSinDigitalizar() {
		$this->accionVista = new BEDocumentoTextoViewActionExpedientesSinDigitalizar($this);
		$this->accionVista->entregar('base_documento_texto.php');
	}
}
?>
