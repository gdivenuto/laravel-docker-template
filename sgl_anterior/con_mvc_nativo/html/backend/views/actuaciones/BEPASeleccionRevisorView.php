<?php
/**
 * Vista del Paso para la Selección del Revisor.
 *
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEPASeleccionRevisorView extends BEPABaseView {

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
	 * Vista por defecto para el formulario de la selección del Firmante.
	 * Ejecuta la acción BEPASeleccionRevisorViewActionEdicion.
	 */
	public function vistaPasoActuacion()
	{
		parent::vistaPasoActuacion();

		$this->accionVista = new BEPASeleccionRevisorViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
