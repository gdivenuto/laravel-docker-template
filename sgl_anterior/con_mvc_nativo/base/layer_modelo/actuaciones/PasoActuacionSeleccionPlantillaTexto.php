<?php
/**
 * Clase PasoActuacionSeleccionPlantillaTexto
 * 
 * Paso de actuación donde deba seleccionarse una plantilla de texto para un documento.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacionSeleccionPlantillaTexto extends PasoActuacion {
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
			'Selección de plantilla de documento.', 

			// Opciones por defecto
			[
				// Nombre alternativo; en caso de tener valor, reemplaza el nombre del paso.
				'paso_nombre' => '',

				// Ayuda para el usuario con respecto al paso
				'paso_ayuda' => 'Seleccione una plantilla para la generación de un documento.',

				// Directorio del cual se obtendrá la lista de plantillas
				'path_plantillas' => PATH_SGL_DOC_PLANTILLAS,

				// Filtro de plantillas mostradas por 'destino'-
				// Puede ser 'todos', 'expe_elec', 'firma_online'
				'destino_plantillas' => 'expe_elec',

				// Lista blanca de plantillas permitidas
				'whitelist_plantillas' => [],

				// Lista negra de plantillas permitidas
				'blacklist_plantillas' => [],

				// Lugar donde se almacenará el texto generado de la plantilla
				'destino_texto' => [
					// Puede ser 'ninguno' o 'paso_editor_texto'
					'destino' => 'ninguno',

					// Si 'destino' es 'paso_editor_texto', estas son las opciones relevantes
					'id_paso' => 0,           // ID del paso donde guardar el texto
					'parametro_titulo' => '', // Nombre del dato del paso donde se almacenará el titulo
					'parametro_texto' => '',  // Nombre del dato del paso donde se almacenará el texto
				]
			], 

			// Opciones customizadas
			$popciones
		);
	}
}
?>