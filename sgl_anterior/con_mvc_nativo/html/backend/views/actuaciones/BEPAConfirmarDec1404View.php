<?php
/**
 * Vista del Paso para confirmar el alcance del art 11 decreto 1404.
 *
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEPAConfirmarDec1404View extends BEPABaseView {

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
	 * Ejecuta la acción BEPAConfirmarDec1404ViewActionEdicion.
	 */
	public function vistaPasoActuacion()
	{
		parent::vistaPasoActuacion();

		$this->accionVista = new BEPAConfirmarDec1404ViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
