<?php
/**
 * Clase PasoActuacionConfirmarFirmaLote
 * 
 * Paso de actuación donde el usuario confirma la firma digital de un lote de documentos
 * almacenados en un directorio.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacionConfirmarFirmaLote extends PasoActuacion {
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
			'Confirmar Firma Digital de Lote de Documentos', 

			// Opciones por defecto
			[
				// Nombre alternativo; en caso de tener valor, reemplaza el nombre del paso.
				'paso_nombre' => '',

				// Ayuda para el usuario con respecto al paso
				'paso_ayuda' => 'Confirme la Firma del lote de documentos PDF',

				// La firma es obligatoria o el usuario puede optar por no firmar?
				'firma_obligatoria' => true,

				// Archivo original: se obtiene de un parametro directo o de un paso previo. 
				'lote_a_firmar' => [
					// Puede ser 'directo' o 'desde_paso'
					'tipo_lote' => 'directo', 
					
					// Si 'tipo_lote' es 'directo', estas son las opciones relevantes	
					'ruta_directorio_a_firmar' => '',

					// Si 'tipo_lote' es 'desde_paso', estas son las opciones relevantes
					'id_paso' => 0,         // ID del paso del cual obtener el directorio
					'parametro_paso' => '', // Nombre del parametro del paso del cual se obtiene el directorio
					
					// Opciones generales
					'url_base_directorio_a_firmar' => URL_SGL_DOC_TEMPORALES, // URL donde se encuentra publicado el lote a firmar
				],

				// Ruta del lote firmado (salida)
				'path_lote_firmado' => '',

				// Se conserva el lote original (true) o se elimina (false)?
				'conservar_lote_original' => true,
			], 

			// Opciones customizadas
			$popciones
		);
	}
}
?>