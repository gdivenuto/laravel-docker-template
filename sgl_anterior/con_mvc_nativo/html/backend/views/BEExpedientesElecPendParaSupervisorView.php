<?php
/**
 * Vista de Expedientes Elec. Pendientes para los Supervisores.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX
 *
 */
class BEExpedientesElecPendParaSupervisorView extends BaseView {

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
	 * Vista por defecto para el listado de Revisiones pendientes para los supervisores.
	 * Ejecuta la acción BEExpedientesElecPendParaSupervisorViewActionGrilla.
	 */
	public function vistaPendientesRevision() {
		$this->accionVista = new BEExpedientesElecPendParaSupervisorViewActionGrilla($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
