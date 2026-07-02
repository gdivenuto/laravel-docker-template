<?php
/**
 * Vista del Paso para la Selección de Comisiones.
 *
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEPASeleccionComisionesView extends BEPABaseView {

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
	 * Vista por defecto para el formulario de la selección de Comisiones.
	 * Ejecuta la acción BEPASeleccionComisionesViewActionEdicion.
	 */
	public function vistaPasoActuacion()
	{
		parent::vistaPasoActuacion();

		$this->accionVista = new BEPASeleccionComisionesViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
