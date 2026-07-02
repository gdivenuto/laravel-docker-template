<?php
/**
 * Clase PasoActuacionFormatoExpeElec
 * 
 * Paso de actuación donde se confirma el formato del expediente electronico a descargar.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacionFormatoExpeElec extends PasoActuacion {
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
			'Selección de formato de descarga.', 

			// Opciones por defecto
			[
				// Nombre alternativo; en caso de tener valor, reemplaza el nombre del paso.
				'paso_nombre' => '',

				// Ayuda para el usuario con respecto al paso
				'paso_ayuda' => 'Indique el formato de descarga para el documento electrónico seleccionado.',
			], 

			// Opciones customizadas
			$popciones
		);
	}
}
?>