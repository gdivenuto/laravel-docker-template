<?php
/**
 * Clase ActuacionExpeElecDescargar
 * 
 * Clase de actuación que se encarga de descargar un expediente electrónico
 * completo en diferentes formatos.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class ActuacionExpeElecDescargar extends Actuacion {
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
			'Descargar un expediente electrónico completo.',
			'Descargar toda la documentación que conforma el expediente electrónico en un único archivo.'
		);

		// Version de la actuacion.
		$this->version = '0.0.1.1';

		// Configuración de pasos.
		$this->agregarPaso('FormatoExpeElec', [
			'paso_nombre' => '',
			'paso_ayuda' => 'Indique el formato de descarga para el documento electrónico seleccionado.',
		]);

		$this->agregarPaso('EditorTexto', [
			'paso_nombre' => 'Observaciones',
			'paso_ayuda' => 'Observaciones.',
			'titulo_permitido' => false,
			'titulo_obligatorio' => false,
			'texto_placeholder' => 'Ingrese aquí el motivo de descarga del expediente electrónico.',
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
		$parametros = [
			'f_anio' => $this->parametros['anio'],
			'f_tipo' => $this->parametros['tipo'],
			'f_numero' => $this->parametros['numero'],
			'f_cuerpo' => $this->parametros['cuerpo'],
			'f_alcance' => $this->parametros['alcance']
		];

		// Si esta presente el archivo de descarga, redirecciono a la descarga
		if (array_key_exists('archivo_descarga', $this->datos) && $this->datos['archivo_descarga'] != '')
			$parametros['f_archivo_descarga'] = $this->datos['archivo_descarga'];

		return [
			'controlador' => 'expedienteselec',
			'accion' => 'view',
			'parametros' => $parametros
		];
	}
}
?>