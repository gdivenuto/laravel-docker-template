<?php
/**
 * Clase PasoActuacionConfirmarDec1404
 * 
 * Paso de actuación donde se confirma si el documento es alcanzado por el art.11 decreto 1404.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacionConfirmarDec1404 extends PasoActuacion {
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
			'Confirmación Art. 11 Decreto 1404', 

			// Opciones por defecto
			[
				// Nombre alternativo; en caso de tener valor, reemplaza el nombre del paso.
				'paso_nombre' => '',

				// Ayuda para el usuario con respecto al paso
				'paso_ayuda' => 'Confirme si el documento es alcanzado por el Art. 11 Decreto 1404.',

				// Muestra un preview de un documento, asignado como paso->datos['archivo_preview']
				'permite_preview_documento' => true,

				// Archivo original: se obtiene de un parametro directo o de un paso previo. 
				'documento_a_confirmar' => [
					// Puede ser 'directo' o 'desde_paso'
					'tipo_documento' => 'directo', 
					
					// Si 'tipo_documento' es 'directo', estas son las opciones relevantes	
					'ruta_documento_a_confirmar' => '',

					// Si 'tipo_documento' es 'desde_paso', estas son las opciones relevantes
					'id_paso' => 0,         // ID del paso del cual obtener el archivo
					'parametro_paso' => '', // Nombre del parametro del paso del cual se obtiene el archivo
					
					// Opciones generales
					'url_base_documento_a_confirmar' => URL_SGL_DOC_TEMPORALES, // URL donde se encuentra publicado el archivo a firmar
				],				
			], 

			// Opciones customizadas
			$popciones
		);
	}
}
?>