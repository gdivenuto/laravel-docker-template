<?php
/**
 * Clase PasoActuacionConfirmarFirma
 * 
 * Paso de actuación donde el usuario confirma la firma digital de un documento.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacionConfirmarFirma extends PasoActuacion {
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
			'Confirmar Firma Digital de Documento', 

			// Opciones por defecto
			[
				// Nombre alternativo; en caso de tener valor, reemplaza el nombre del paso.
				'paso_nombre' => '',

				// Ayuda para el usuario con respecto al paso
				'paso_ayuda' => 'Confirme la Firma del documento PDF',

				// La firma es obligatoria o el usuario puede optar por no firmar?
				'firma_obligatoria' => true,

				// Archivo original: se obtiene de un parametro directo o de un paso previo. 
				'documento_a_firmar' => [
					// Puede ser 'directo' o 'desde_paso'
					'tipo_documento' => 'directo', 
					
					// Si 'tipo_documento' es 'directo', estas son las opciones relevantes	
					'ruta_documento_a_firmar' => '',

					// Si 'tipo_documento' es 'desde_paso', estas son las opciones relevantes
					'id_paso' => 0,         // ID del paso del cual obtener el archivo
					'parametro_paso' => '', // Nombre del parametro del paso del cual se obtiene el archivo
					
					// Opciones generales
					'url_base_documento_a_firmar' => URL_SGL_DOC_TEMPORALES, // URL donde se encuentra publicado el archivo a firmar
				],

				// Ruta del documento firmado (salida)
				'path_documento_firmado' => '',

				// Se conserva el documento original (true) o se elimina (false)?
				'conservar_documento_original' => true,
			], 

			// Opciones customizadas
			$popciones
		);
	}
}
?>