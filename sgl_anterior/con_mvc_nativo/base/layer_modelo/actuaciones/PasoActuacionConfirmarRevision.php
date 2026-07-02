<?php
/**
 * Clase PasoActuacionConfirmarRevision
 * 
 * Paso de actuación donde se confirma si el documento pendiente es confirmado.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacionConfirmarRevision extends PasoActuacion {
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
			'Confirmación de Revisión de Documento', 

			// Opciones por defecto
			[
				// Nombre alternativo; en caso de tener valor, reemplaza el nombre del paso.
				'paso_nombre' => '',

				// Ayuda para el usuario con respecto al paso
				'paso_ayuda' => '<strong>Usted está a punto de revisar un documento pendiente.</strong><br/>En caso de que todos los revisores confirmen el documento, éste pasará a formar parte del expediente electrónico; en caso de que al menos un revisor lo rechace, el documento será descartado y no formará parte del expediente electrónico.',

				// Muestra un preview de un documento, asignado como paso->datos['archivo_preview']
				'permite_preview_documento' => true,

				// Archivo original: se obtiene de un parametro directo o de un paso previo. 
				'documento_a_revisar' => [
					// Puede ser 'directo' o 'desde_paso'
					'tipo_documento' => 'directo', 
					
					// Si 'tipo_documento' es 'directo', estas son las opciones relevantes	
					'ruta_documento_a_revisar' => '',

					// Si 'tipo_documento' es 'desde_paso', estas son las opciones relevantes
					'id_paso' => 0,         // ID del paso del cual obtener el archivo
					'parametro_paso' => '', // Nombre del parametro del paso del cual se obtiene el archivo
					
					// Opciones generales
					'url_base_documento_a_revisar' => URL_SGL_DOC_TEMPORALES, // URL donde se encuentra publicado el archivo a firmar
				],

				// Determina si este paso se puede revisar y firmar en la misma actuacion
				'revisar_y_firmar' => false
			], 

			// Opciones customizadas
			$popciones
		);
	}
}
?>