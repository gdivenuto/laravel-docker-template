<?php
/**
 * Vista del Paso para descartar la revision de un documento pendiente.
 *
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEPADescartarRevisionView extends BEPABaseView {

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
	 * Ejecuta la acción BEPADescartarRevisionViewActionEdicion.
	 */
	public function vistaPasoActuacion()
	{
		parent::vistaPasoActuacion();

		$this->accionVista = new BEPADescartarRevisionViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
