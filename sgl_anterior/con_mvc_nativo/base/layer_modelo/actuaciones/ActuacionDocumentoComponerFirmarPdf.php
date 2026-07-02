<?php
/**
 * Clase ActuacionDocumentoComponerFirmarPdf
 * 
 * Clase de actuación que compone un archivo PDF, lo firma y posteriormente
 * lo comparte.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class ActuacionDocumentoComponerFirmarPdf extends Actuacion {
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
			'Componer y firmar un archivo PDF',
			'Componer un archivo PDF para ser firmado digitalmente y luego compartido.'
		);

		// Version de la actuacion.
		$this->version = '0.0.1.1';

		// Configuración de pasos.
		$this->agregarPaso('SeleccionPlantillaTexto',[
			'paso_ayuda' => 'Seleccione una plantilla para la generación de un documento.',
			'destino_plantillas' => 'firma_online', // Solo se visualizan las plantillas con destino 'todos' o 'firma_online'
			'destino_texto' => [
				'destino' => 'paso_editor_texto',
				'id_paso' => 1,                   // ID del paso donde guardar el texto
				'parametro_titulo' => 'f_titulo', // Nombre del dato del paso donde se almacenará el titulo
				'parametro_texto' => 'f_texto',   // Nombre del dato del paso donde se almacenará el texto
			]
		]);

		$this->agregarPaso('EditorTexto', [
			'paso_ayuda' => 'Redacte un texto.',
			'titulo_permitido' => true,
			'titulo_placeholder' => 'Ingrese el título aquí',
			'titulo_obligatorio' => true,
			'titulo_longitud_max' => 200,
			'texto_placeholder' => 'Ingrese el texto aquí.',
			'texto_obligatorio' => true,
			'generar_archivo_pdf' => [
				'generar_archivo' => true,
				'ruta_archivo' => PATH_SGL_DOC_TEMPORALES
			],
			'texto_enriquecido' => true,
		]);

		$this->agregarPaso('ConfirmarFirma', [
			'paso_ayuda' => '<strong>Usted está a punto de firmar digitalmente un documento.</strong><br/>Esta firma tendrá la misma válidez y tenor que su firma de <i>puño y letra</i>. Además, una vez finalizada la actuación, la firma no podrá deshacerse o revertirse.',
		 	'firma_obligatoria' => true,
			'documento_a_firmar' => [
				'tipo_documento' => 'desde_paso', 
				'id_paso' => 1,                          // paso previo: EditorTexto
				'parametro_paso' => 'archivo_generado',  // nombre del parametro que contiene el nombre del archivo
				'url_base_documento_a_firmar' => URL_SGL_DOC_TEMPORALES,
			],
			'path_documento_firmado' => PATH_SGL_DOC_TEMPORALES,
			'conservar_documento_original' => false,
		]);

		$this->agregarPaso('SeleccionDestinatarioMail', [
			'paso_nombre' => '',
			'paso_ayuda' => 'Una copia del documento electrónico será enviada a su dirección de correo personal; además podrá seleccionar otros destinatarios para el envío del documento.',
			'cantidad_minima' => 0,
			'cantidad_maxima' => 10,
			'destinatario_manual' => true,
		]);

		$this->agregarPaso('EditorTexto', [
			'paso_nombre' => 'Observaciones',
			'paso_ayuda' => 'Observaciones.',
			'titulo_permitido' => false,
			'titulo_obligatorio' => false,
			'texto_placeholder' => 'Ingrese aquí las observaciones del documento; este texto será incluído en el correo electrónico donde estará adjunta la documentación generada.',
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
		return [];
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
		$parametros = [];

		// Si esta presente el archivo de descarga, redirecciono a la descarga
		if (array_key_exists('archivo_descarga', $this->datos) && $this->datos['archivo_descarga'] != '')
			$parametros['f_archivo_descarga'] = $this->datos['archivo_descarga'];

		return [
			'controlador' => (isset($parametros['f_archivo_descarga'])) ? 'firmas' : 'expedientes',
			'accion' => (isset($parametros['f_archivo_descarga'])) ? 'firmadordescarga' : 'view',
			'parametros' => $parametros
		];
	}
}
?>