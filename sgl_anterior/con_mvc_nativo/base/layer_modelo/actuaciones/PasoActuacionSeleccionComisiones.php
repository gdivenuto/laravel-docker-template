<?php
/**
 * Clase PasoActuacionSeleccionComisiones
 * 
 * Paso de actuación donde deba seleccionarse una o más comisiones.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacionSeleccionComisiones extends PasoActuacion {
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
			'Selección de Comisiones', 

			// Opciones por defecto
			[
				// Ayuda para el usuario con respecto al paso 
				'paso_ayuda' => 'Seleccione Comisiones',

				// Mínimo de Comisiones que pueden seleccionarse
				'cantidad_minima' => 1,

				// Generar providencia en pdf al finalizar el paso (si las validaciones son correctas)
				// Esto dejara en la transaccion un parametro 'archivo_generado' con la ruta
				// del archivo en cuestion.
				'generar_providencia_pdf' => [
					'generar_providencia' => false,
					'tipo_providencia' => 'confirmar_giros', // Define el tipo de providencia. Puede ser 'confirmar_giros' o 'convalidar_giros'.
					'ruta_archivo' => PATH_SGL_DOC_TEMPORALES
				],

			], 

			// Opciones customizadas
			$popciones
		);
	}
}
?>