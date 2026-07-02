<?php
/**
 * Clase ActuacionExpeElecFirmar
 * 
 * Clase de actuación que se encarga de firmar un documento electrónico
 * perteneciente a un expediente digital.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class ActuacionExpeElecFirmar extends Actuacion {
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
			'Firmar un documento del expediente electrónico',
			'Firmar un documento perteneciente a un expediente electrónico.'
		);

		// Version de la actuacion.
		$this->version = '0.0.1.1';

		// Configuración de pasos.
		$this->agregarPaso('ConfirmarFirma', [
			'paso_ayuda' => '<strong>Usted está a punto de firmar digitalmente un documento.</strong><br/>Esta firma tendrá la misma válidez y tenor que su firma de <i>puño y letra</i>. Además, una vez finalizada la actuación, la firma no podrá deshacerse o revertirse.',
		 	'firma_obligatoria' => true,
			'documento_a_firmar' => [
				'tipo_documento' => 'directo', 
				'url_base_documento_a_firmar' => URL_KRAKEN_RESOURCES_PROYECTOS,
			],
			'path_documento_firmado' => PATH_SGL_DOC_FIRMADOS,
			'conservar_documento_original' => false,
		]);

		$this->agregarPaso('EditorTexto', [
			'paso_nombre' => 'Observaciones',
			'paso_ayuda' => 'Observaciones.',
			'titulo_permitido' => false,
			'titulo_obligatorio' => false,
			'texto_placeholder' => 'Si lo desea, ingrese aquí las observaciones con respecto a la firma del documento.',
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
		// Agrego ademas el orden
		$errores = array_merge(
			$this->verificarParametrosExpediente(), 
			$this->obtenerParametrosFaltantes(['orden', 'id_firma'])
		);
		if (count($errores) > 0) return $errores;

		// Verifico la integridad de los parametros
		if (! preg_match('/^[0-9]{1,5}$/', $this->parametros['orden']))
			$errores[] = "Formato inválido para 'orden'.";
		if (! preg_match('/^[0-9]{1,5}$/', $this->parametros['id_firma']))
			$errores[] = "Formato inválido para 'id_firma'.";

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

		return sprintf('%s: %s-%s-%s cpo.: %s alc.: %s, orden: %s',
			$tipo_txt,
			$this->parametros['anio'],
			$this->parametros['tipo'],
			$this->parametros['numero'],
			$this->parametros['cuerpo'],
			$this->parametros['alcance'],
			$this->parametros['orden']
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
			'controlador' => 'firmas', // 'expedienteselec',
			'accion' => 'view',
			'parametros' => [
				// 'f_anio' => $this->parametros['anio'],
				// 'f_tipo' => $this->parametros['tipo'],
				// 'f_numero' => $this->parametros['numero'],
				// 'f_cuerpo' => $this->parametros['cuerpo'],
				// 'f_alcance' => $this->parametros['alcance']
			]
		];
	}
}
?>