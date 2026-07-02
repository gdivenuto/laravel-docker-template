<?php
/**
 * Vista del Paso para rechazar la firma digital de un documento.
 *
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 *
 * @author XXXX y XXXX
 *
 */
class BEPARechazarFirmaView extends BEPABaseView {

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
	 * Ejecuta la acción BEPARechazarFirmaViewActionEdicion.
	 */
	public function vistaPasoActuacion()
	{
		parent::vistaPasoActuacion();

		$this->accionVista = new BEPARechazarFirmaViewActionEdicion($this);
		$this->accionVista->entregar('base.php');
	}
}
?>
