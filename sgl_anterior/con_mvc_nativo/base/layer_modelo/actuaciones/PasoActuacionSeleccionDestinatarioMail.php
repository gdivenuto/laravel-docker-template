<?php
/**
 * Clase PasoActuacionSeleccionDestinatarioMail
 * 
 * Paso de actuación donde deba confeccionarse un texto.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacionSeleccionDestinatarioMail extends PasoActuacion {
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
			'Selección de destinatario de correo electrónico.', 

			// Opciones por defecto
			[
				// Nombre alternativo; en caso de tener valor, reemplaza el nombre del paso.
				'paso_nombre' => '',

				// Ayuda para el usuario con respecto al paso
				'paso_ayuda' => 'Notifique a los destinatarios sobre la actuación.',

				// Cantidad mínima/máxima de destinatarios?
				'cantidad_minima' => 1,
				'cantidad_maxima' => 10,

				// Permite el ingreso de un destinatario "manual"? (por fuera de la lista de selección)
				'destinatario_manual' => false,

				// Texto del destinatario manual predefinido
				'destinatario_manual_placeholder' => 'Ingrese la dirección de correo electrónico.',
			], 

			// Opciones customizadas
			$popciones
		);
	}
}
?>