<?php
/**
 * Clase PasoActuacionEditorTexto
 * 
 * Paso de actuación donde deba confeccionarse un texto.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacionEditorTexto extends PasoActuacion {

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
			'Confección de Texto', 

			// Opciones por defecto
			[
				// Nombre alternativo; en caso de tener valor, reemplaza el nombre del paso.
				'paso_nombre' => '',

				// Ayuda para el usuario con respecto al paso
				'paso_ayuda' => 'Redacte un texto para la actuación.',
				
				// Texto predefinido
				'texto_placeholder' => 'Ingrese el texto aquí.',
				
				// Es el texto obligatorio?
				'texto_obligatorio' => true,

				// Tiene el texto una longitud minima o máxima? (-1 deshabilita la verificacion)
				'texto_longitud_min' => -1,
				'texto_longitud_max' => -1, 
				
				// Permite título?
				'titulo_permitido' => true,

				// Titulo predefinido
				'titulo_placeholder' => 'Ingrese el título aquí',

				// Es el Titulo obligatorio?
				'titulo_obligatorio' => false,

				// Tiene el titulo una longitud minima o máxima? (-1 deshabilita la verificacion)
				'titulo_longitud_min' => -1,
				'titulo_longitud_max' => -1, 

				// Generar archivo pdf al finalizar el paso (si las validaciones son correctas)
				// Esto dejara en la transaccion un parametro 'archivo_generado' con la ruta
				// del archivo en cuestion.
				'generar_archivo_pdf' => [
					'generar_archivo' => false,
					'ruta_archivo' => PATH_SGL_DOC_TEMPORALES
				],

				// Flag para determinar si el texto será enriquecido o no (plano)
				'texto_enriquecido' => false

			], 

			// Opciones customizadas
			$popciones
		);
	}
}
?>