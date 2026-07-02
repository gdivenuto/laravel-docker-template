<?php
/**
 * Clase PasoActuacionSeleccionRevisor
 * 
 * Paso de actuación donde deba seleccionarse uno o mas revisores.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacionSeleccionRevisor extends PasoActuacion {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	

	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************


	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct($popciones = [])
	{
		// Invocación de inicialización de clase padre.
		parent::__construct(
			// Nombre del paso
			'Selección de Revisores', 

			// Opciones por defecto
			[
				// Ayuda para el usuario con respecto al paso 
				'paso_ayuda' => 'Seleccione revisores',

				// Mínimo de personas que pueden revisar el documento
				'cantidad_minima' => 1,

				// Máximo de personas que pueden revisar el documento (-1 sin maximo)
				'cantidad_maxima' => -1,

				// Requiere revisión por defecto?
				'requiere_revision_por_defecto' => false,

				// Revisores por defecto. Estos se preseleccionan al llegar al paso de
				// revisores, si es que no hay nada seleccionado.
				'revisores_default' => [],
				
				// Excluyo el usuario operador como posible revisor?
				'excluir_usuario_actual' => true,
			], 

			// Opciones customizadas
			$popciones
		);
	}
}
?>