<?php
/**
 * Clase ActuacionExpedienteSubirArchivoTrabajo
 * 
 * Clase de actuación que anexa un documento como "archivo de trabajo" como 
 * documento en solapa "proyectos" de un Expediente. Si el archivo es un .zip,
 * lo descompacta y genera una única salida en .pdf.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class ActuacionExpedienteSubirArchivoTrabajo extends Actuacion {
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
	public function __construct()
	{
		// Invocación de inicialización de clase padre.
		parent::__construct(
			// Asignación de atributos.
			'Cargar archivo de trabajo en un Expediente',
			'Agregar un documento como archivo de trabajo a un expediente.'
		);

		// Version de la actuacion.
		$this->version = '0.0.1.1';

		// Configuración de pasos.
		$this->agregarPaso('SubirArchivo', [
			'paso_ayuda' => 'Seleccione un documento para agregar al expediente como archivo de trabajo.',
			'titulo_permitido' => false,
			'titulo_obligatorio' => false,
			'mimetype_permitidos' => [
					'doc' => 'application/msword',
					'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'odt' => 'application/vnd.oasis.opendocument.text',
					'pdf' => 'application/pdf',
					'txt' => 'text/plain',
					'xls' => 'application/vnd.ms-excel',
					'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'zip' => 'application/zip'
				],
			'directorio_destino' => PATH_SGL_DOC_TEMPORALES,
		]);

		$this->agregarPaso('ConfirmarDec1404', [
			'permite_preview_documento' => false
		]);

		$this->agregarPaso('EditorTexto', [
			'paso_nombre' => 'Observaciones',
			'paso_ayuda' => 'Observaciones.',
			'titulo_permitido' => false,
			'titulo_obligatorio' => false,
			'texto_placeholder' => 'Si lo desea, ingrese aquí las observaciones de la actuación.',
			'texto_obligatorio' => false,
			'generar_archivo_pdf' => [
				'generar_archivo' => false
			],
			'texto_enriquecido' => false,
		]);
	}

	/**
	 * Verifica la integridad de los parámetros de la actuación.
	 * @return [Array] Lista de errores encontrados, o un array vacío si no hay errores.
	 */
	public function verificarParametros()
	{
		// En vez de hacer una validacion manual, uso el 'helper' para ID de expediente.
		return $this->verificarParametrosExpediente();
	}

	/**
	 * Genera un texto informativo extra para mostrarse en la cabecera de cada paso.
	 * Este metodo se sobreescribe en todas las actuaciones que lo necesiten.
	 * @param  boolean $extendido Flag para determinar si se genera el texto "extendido", que aplica segun el caso.
	 * @return [type]             [description]
	 */
	public function generarTextoInformativo($extendido = false) 
	{
		$tipo_txt = '';
		switch ($this->parametros['tipo']) {
			case 'E': $tipo_txt = (($extendido) ? 'el ' : '') . 'Expediente'; break;
			case 'N': $tipo_txt = (($extendido) ? 'la ' : '') . 'Nota'; break;
			case 'R': $tipo_txt = (($extendido) ? 'la ' : '') . 'Recomendación'; break;
		}

		return sprintf('%s: %s-%s-%s cpo.: %s alc.: %s',
			$tipo_txt,
			$this->parametros['anio'],
			$this->parametros['tipo'],
			$this->parametros['numero'],
			$this->parametros['cuerpo'],
			$this->parametros['alcance']
		);
	}

	/**
	 * Genera los parametros necesarios de retorno al cancelarse o finalizarse
	 * esta actuación. Responde con un controlador, accion y parámeteros según
	 * lo necesita la interfase de usuario.
	 * NOTA: esta forma de trabajo rompe con tener la logica de negocio y modelo
	 * separada de la interfase, pero se hace en pos de tener mayor flexibilidad
	 * en las actuaciones.
	 * @return Array Resultado. Formato [<string>controlador, <string>accion, <array>parametros]
	 */
	public function obtenerRutaRetorno() 
	{
		return [
			'controlador' => 'proyectos',
			'accion' => 'view',
			'parametros' => [
				'f_anio' => $this->parametros['anio'],
				'f_tipo' => $this->parametros['tipo'],
				'f_numero' => $this->parametros['numero'],
				'f_cuerpo' => $this->parametros['cuerpo'],
				'f_alcance' => $this->parametros['alcance']
			]
		];
	}
}
?>