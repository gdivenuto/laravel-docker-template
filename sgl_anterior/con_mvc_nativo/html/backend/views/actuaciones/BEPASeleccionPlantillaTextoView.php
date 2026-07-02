<?php
/**
 * Vista del Paso para la seleccion de una plantilla de texto.
 *
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEPASeleccionPlantillaTextoView extends BEPABaseView {

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
	 * Vista por defecto para utilizar el Editor de texto para la confección de un documento.
	 * Ejecuta la acción BEPASeleccionPlantillaTextoViewActionEdicion.
	 */
	public function vistaPasoActuacion()
	{
		parent::vistaPasoActuacion();

		$this->accionVista = new BEPASeleccionPlantillaTextoViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
