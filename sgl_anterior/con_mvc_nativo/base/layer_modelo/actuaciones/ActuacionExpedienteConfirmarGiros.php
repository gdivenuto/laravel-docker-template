<?php
/**
 * Clase ActuacionExpedienteConfirmarGiros
 * 
 * Clase de actuación que modifica (opcional) y confirma la carga de Giros a un expediente.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class ActuacionExpedienteConfirmarGiros extends Actuacion {
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
			'Confirmar Giros para un Expediente',
			'Confirmar giros a comisiones para un expediente.'
		);

		// Version de la actuacion.
		$this->version = '0.0.1.1';

		// Configuración de pasos.
		$this->agregarPaso('SeleccionComisiones', [
			'paso_ayuda' => 'Confirme las comisiones a las que se girará el expediente',
			'generar_providencia_pdf' => [
				'generar_providencia' => true,
				'tipo_providencia' => 'confirmar_giros',
				'ruta_archivo' => PATH_SGL_DOC_TEMPORALES
			],			
		]);		

		$this->agregarPaso('ConfirmarFirma', [
			'paso_ayuda' => '<strong>Usted está a punto de firmar digitalmente un documento.</strong><br/>Esta firma tendrá la misma válidez y tenor que su firma de <i>puño y letra</i>. Además, una vez finalizada la actuación, la firma no podrá deshacerse o revertirse.',
		 	'firma_obligatoria' => true,
			'documento_a_firmar' => [
				'tipo_documento' => 'desde_paso', 
				'id_paso' => 0,                          // paso previo: SeleccionComisiones
				'parametro_paso' => 'archivo_generado',  // nombre del parametro que contiene el nombre del archivo
				'url_base_documento_a_firmar' => URL_SGL_DOC_TEMPORALES,
			],
			'path_documento_firmado' => PATH_SGL_DOC_FIRMADOS,
			'conservar_documento_original' => false,
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
		// Agrego ademas el id_pendiente
		$errores = array_merge(
			$this->verificarParametrosExpediente(), 
			$this->obtenerParametrosFaltantes(['id_pendiente'])
		);
		if (count($errores) > 0) return $errores;

		// Verifico la integridad de los parametros
		if (! preg_match('/^[0-9]{1,5}$/', $this->parametros['id_pendiente']))
			$errores[] = "Formato inválido para 'id_pendiente'.";

		return $errores;
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
			'controlador' => 'giros',
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