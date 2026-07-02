<?php
/**
 * Vista de Reportes.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */

class BEReportesView extends BaseView {

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
	 * 26/08/2020 XXXX
	 * Se muestra en formato HTML en otra pestaña, sólo este reporte.
	 *
	 * Vista para el reporte de la Búsqueda Avanzada.
	 * Ejecuta la acción BEReportesViewActionAvanzada.
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'base_reporte_busqueda_avanzada.php');
	 */
	public function vistaReporteBusquedaAvanzada() {
		$this->accionVista = new BEReportesViewActionAvanzada($this);
		$this->accionVista->entregar('base_reporte_busqueda_avanzada.php');
	}

	/**
	 * Vista para el reporte de la Búsqueda por Antecedente.
	 * Ejecuta la acción BEReportesViewActionPorAntecedente.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_busqueda_por_antecedente.php');
	 *
	 */
	public function vistaReporteBusquedaPorAntecedente() {
		$this->accionVista = new BEReportesViewActionPorAntecedente($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el Reporte de Expedientes en Comisión, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionExpedientesEnComision.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_pdf_expedientes_en_comision.php');
	 *
	 */
	public function vistaReportePdfExpedientesEnComision() {
		$this->accionVista = new BEReportesViewActionPdfExpedientesEnComision($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el Reporte de Órdenes del Día en Comisión, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionPdfOrdenDelDia.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_pdf_orden_del_dia.php');
	 *
	 */
	public function vistaReportePdfOrdenDelDia() {
		$this->accionVista = new BEReportesViewActionPdfOrdenDelDia($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el Reporte de Detalle de Giros, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionPdfDetalleDeGiros.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_pdf_detalle_de_giros.php');
	 *
	 */
	public function vistaReportePdfDetalledegiros() {
		$this->accionVista = new BEReportesViewActionPdfDetalleDeGiros($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el Reporte de Informes, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionPdfInformes.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_pdf_informes.php');
	 *
	 */
	public function vistaReportePdfInformes() {
		$this->accionVista = new BEReportesViewActionPdfInformes($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el Reporte de Asuntos Entrados, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionPdfAsuntosEntrados.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_pdf_asuntos_entrados.php');
	 *
	 */
	public function vistaReportePdfAsuntosEntrados() {
		$this->accionVista = new BEReportesViewActionPdfAsuntosEntrados($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el Reporte de Expedientes para Expurgo, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionPdfExpedientesParaExpurgo.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_pdf_expedientes_para_expurgo.php');
	 *
	 */
	public function vistaReportePdfExpedientesParaExpurgo() {
		$this->accionVista = new BEReportesViewActionPdfExpedientesParaExpurgo($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el Reporte de Informes, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionPdfExpedientesEnPrestamo.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_pdf_expedientes_en_prestamo.php');
	 *
	 */
	public function vistaReportePdfExpedientesEnPrestamo() {
		$this->accionVista = new BEReportesViewActionPdfExpedientesEnPrestamo($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el Reporte de Informes, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionPdfSinDocumentoCargado.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_pdf_sin_documentos_cargados.php');
	 *
	 */
	public function vistaReportePdfSinDocumentoCargado() {
		$this->accionVista = new BEReportesViewActionPdfSinDocumentoCargado($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el Reporte de Informes, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionPdfSinDigitalizar.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_pdf_sin_digitalizar.php');
	 *
	 */
	public function vistaReportePdfSinDigitalizar() {
		$this->accionVista = new BEReportesViewActionPdfSinDigitalizar($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para la Ficha de un Expediente determinado
	 * Ejecuta la acción BEReportesViewActionFichaExpediente.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda avanzada, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/pdf_ficha_expediente.php');
	 */
	public function vistaReportePdfFichaExpediente() {
		$this->accionVista = new BEReportesViewActionFichaExpediente($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para la Etiqueta de un Expediente determinado
	 * Ejecuta la acción BEReportesViewActionEtiquetaExpediente.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda avanzada, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/pdf_etiqueta_expediente.php');
	 */
	public function vistaReportePdfEtiquetaExpediente() {
		$this->accionVista = new BEReportesViewActionEtiquetaExpediente($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el listado general de Préstamos, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionPdfPrestamos.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_pdf_prestamos.php');
	 *
	 */
	public function vistaReportePdfPrestamos() {
		$this->accionVista = new BEReportesViewActionPdfPrestamos($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el listado general de Solicitudes, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionPdfSolicitudes.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_pdf_solicitudes.php');
	 *
	 */
	public function vistaReportePdfSolicitudes() {
		$this->accionVista = new BEReportesViewActionPdfSolicitudes($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el listado general de Participaciones, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionPdfParticipaciones.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/reporte_pdf_participaciones.php');
	 *
	 */
	public function vistaReportePdfParticipaciones() {
		$this->accionVista = new BEReportesViewActionPdfParticipaciones($this);
		$this->accionVista->entregar('base_reporte.php');
	}

	/**
	 * Vista para el Documento a firmar, en formato PDF.
	 * Ejecuta la acción BEReportesViewActionPdfAFirmar.
	 *
	 * NO se entrega (incluye) aquí base.php
	 * porque la librería html2pdf NO permite etiquetas <html>, <head> ni <body>
	 * Se incluye el template de la búsqueda por antecedente, la cual utiliza etiquetas propias de dicha librería
	 * <page>, <page_header> y <page_footer>
	 *
	 * Aquí 'entregar' realiza en este caso un:
	 * require($this->vista->baseTemplatePath.'reportes/pdf_documento_a_firmar.php');
	 *
	 */
	public function vistaPdfDocumentoAFirmar() {
		$this->accionVista = new BEReportesViewActionPdfAFirmar($this);
		$this->accionVista->actionParamName = $this->data['archivo_destino'];
		$this->accionVista->actionParamOutputType = KRAKEN_REPORT_OUTPUT_FILE;
		$this->accionVista->entregar('base_reporte.php');
	}
}
?>
