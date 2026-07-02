<?php
/**
 * Clase PasoActuacionSubirArchivo
 * 
 * Paso de actuación donde se sube un archivo al servidor.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacionSubirArchivo extends PasoActuacion {
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
			'Subir un archivo', 

			// Opciones por defecto
			[
				// Ayuda para el usuario con respecto al paso
				'paso_ayuda' => 'Seleccione un archivo a subir.',

				// Permite título?
				'titulo_permitido' => true,

				// Titulo predefinido
				'titulo_placeholder' => 'Ingrese la descripción del archivo aquí.',

				// Es el Titulo obligatorio?
				'titulo_obligatorio' => false,

				// Tiene el titulo una longitud minima o máxima? (-1 deshabilita la verificacion)
				'titulo_longitud_min' => -1,
				'titulo_longitud_max' => -1, 

				// Filtro de mime-type permitidos.
				'mimetype_permitidos' => [
					// Si esta vacío, agrega automaticamente esto (ver mas abajo de este constructor):
					// 'pdf' => 'application/pdf'
				],

				// Tamaño máximo en bytes del archivo.
				'tamano_mb_max' => KRAKEN_UPLOAD_MAX_SIZE,

				// Directorio temporal de subida de archivo (requiere barra al final).
				'directorio_destino' => PATH_SGL_DOC_TEMPORALES,
			], 

			// Opciones customizadas
			$popciones
		);

		// Logica especifica para que no haga merge de los mimetype_permitidos
		if ( count($this->opciones['mimetype_permitidos']) == 0) {
			$this->opciones['mimetype_permitidos'] = [
				'pdf' => 'application/pdf'
			];
		}
	}
}
?>