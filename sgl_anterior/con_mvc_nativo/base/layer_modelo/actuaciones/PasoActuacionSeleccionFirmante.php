<?php
/**
 * Clase PasoActuacionSeleccionFirmante
 * 
 * Paso de actuación donde deba seleccionarse uno o mas firmantes.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacionSeleccionFirmante extends PasoActuacion {
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
			'Selección de Firmantes', 

			// Opciones por defecto
			[
				// Ayuda para el usuario con respecto al paso 
				'paso_ayuda' => 'Seleccione firmantes',

				// Mínimo de personas que pueden firmar el documento
				'cantidad_minima' => 1,

				// Máximo de personas que pueden firmar el documento (-1 sin maximo)
				'cantidad_maxima' => -1,

				// Firmantes por defecto. Estos se preseleccionan al llegar al paso de
				// firmantes, si es que no hay nada seleccionado.
				'firmantes_default' => [],
				
				// Condición de selección de firmantes posibles. Los valores son:
				// todos: todos los usuarios que posean número de legajo y se encuentren habilitados al día de la fecha.
				// giros: usuarios con legajo y cargo equivalente al Secretario HCD o con el valor confirma_giros activo.
				'tipo_firmantes' => 'todos',

				// Excluyo el usuario operador como posible firmante?
				'excluir_usuario_actual' => true,
			], 

			// Opciones customizadas
			$popciones
		);
	}
}
?>