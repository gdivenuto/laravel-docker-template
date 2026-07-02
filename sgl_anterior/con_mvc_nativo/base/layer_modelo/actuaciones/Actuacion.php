<?php
/**
 * Clase Actuacion
 * 
 * Clase Base encargada de contener la configuración de las actuaciones.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class Actuacion extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $nombre;         // [String]  Nombre de fantasía de la Actuación.
	protected $version;        // [String]  Version de la actuacion.
	protected $descripcion;    // [String]  Descripción larga de la actuación.
	protected $paso_actual;    // [Integer] ID del paso actual en la lista de pasos.
	protected $parametros;     // [Array]   Parametros de la actuación.

	protected $pasos;          // [Array]   Lista de pasos de la actuación.

	public $datos;             // [Array]   Array multipropósito para guardar datos de la actuación.
	public $info_auditoria;    // [Array]   Array asociativo con información utilizada en la auditoria.

	protected $id_transaccion; // [Integer] Referencia de PK para acceder a los datos almacenados.
	
	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getTipo() { return get_class($this); }

	public function getNombre() { return $this->nombre; }
	public function setNombre($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setNombre(): no se permiten valores nulos para el atributo 'nombre'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setNombre(): el atributo 'nombre' solo permite valores de tipo string.", get_class($this)));
		$this->nombre = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVersion() { return $this->version; }
	public function setVersion($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setVersion(): no se permiten valores nulos para el atributo 'version'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setVersion(): el atributo 'version' solo permite valores de tipo string.", get_class($this)));
		$this->nombre = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDescripcion() { return $this->descripcion; }
	public function setDescripcion($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setDescripcion(): no se permiten valores nulos para el atributo 'descripcion'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDescripcion(): el atributo 'descripcion' solo permite valores de tipo string.", get_class($this)));
		$this->descripcion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getId_Transaccion() { return $this->id_transaccion; }
	public function setId_Transaccion($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Transaccion(): no se permiten valores nulos para el atributo 'id_transaccion'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Transaccion(): el atributo 'id_transaccion' solo permite valores de tipo integer.", get_class($this)));
		$this->id_transaccion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getPaso_Actual() { return $this->paso_actual; }
	// public function setPaso_Actual($value) { 
	// 	if (is_null($value)) 
	// 		throw new UnexpectedValueException(sprintf("Error en %s.setPaso_Actual(): no se permiten valores nulos para el atributo 'paso_actual'.", get_class($this)));
	// 	if ( (!$this->esInteger($value)) ) 
	// 		throw new InvalidArgumentException(sprintf("Error en %s.setPaso_Actual(): el atributo 'paso_actual' solo permite valores de tipo integer.", get_class($this)));
	// 	$this->paso_actual = $value;
	// 	$this->setInstanceState(IS_MODIFIED);
	// }

	public function getPaso($index) { return $this->pasos[$index]; }

	public function getPasos() { return $this->pasos; }
	public function getParametros() { return $this->parametros; }

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct($pnombre = '', $pdescripcion = '')
	{
		// Asignación de atributos.
		$this->nombre = $pnombre;
		$this->version = '0.0.0.1';
		$this->descripcion = $pdescripcion;
		$this->parametros = [];
		$this->pasos = [];
		$this->paso_actual = (count($this->pasos) > 0) ? 0 : -1;

		// Los datos son vacios
		$this->datos = [];

		// La info de auditoria es vacia
		$this->info_auditoria = [];

		// Valor indefinido para la transaccion
		$this->id_transaccion = -1;
	}

	/**
	 * Agrega un paso a la lista de pasos de la actuación.
	 * @param  string $ptipo     Tipo de paso a agregar (no requiere el prefijo 'Paso')
	 * @param  array  $popciones Opciones del paso a agregar.
	 * @return PasoActuacion     Instancia del paso agregado.
	 */
	public function agregarPaso($ptipo, $popciones = []) 
	{
		$tipo_paso = sprintf('PasoActuacion%s', $ptipo);
		$paso = new $tipo_paso($popciones);

		// el id_paso de la transaccion es igual a la cantidad de elementos (antes de agregarlo a la lista de pasos)
		$paso->id_paso = count($this->pasos);

		$this->pasos[] = $paso;

		// salgo del paso -1 cuando tengo al menos un elemento en la lista de pasos
		if ($this->paso_actual <= 0) $this->paso_actual = 0;

		return $paso;
	}

	/**
	 * Obtiene LA INSTANCIA de tipo PasoActuacion actual segun el id 'paso_actual'
	 * de la instancia de actuación.
	 * @return PasoActuacion Instancia de paso de actuacion, o null si no hay pasos definidos.
	 */
	public function obtenerPasoActual()
	{
		return (array_key_exists($this->paso_actual, $this->pasos))
			? $this->pasos[$this->paso_actual]
			: null;
	}

	/**
	 * Determina si se está en el último paso de la lista de pasos. En caso de una
	 * lista de pasos vacía, siempre devuelve verdadero (cuando paso_actual < 0).
	 * @return Boolean True si se está en el último paso, False en caso contrario.
	 */
	public function enUltimoPaso()
	{
		return ($this->paso_actual >= 0) && ($this->paso_actual == (count($this->pasos)-1));
	}

	/**
	 * Determina si una lista de pasos finalizo siendo recorrida 'un paso mas alla del ultimo paso'
	 * @return [type] [description]
	 */
	public function finalizado() {
		// Cuando "se pasa" por un elemento pasado el ultimo, es que terminó...
		return ($this->paso_actual == count($this->pasos));
	}

	/**
	 * Mueve el paso actual al anterior, y retorna una instancia de dicho paso.
	 * @return PasoActuacion Instancia de paso de actuacion siguiente, o null si no hay mas pasos.
	 */
	public function pasoAnterior()
	{
		$this->paso_actual = ($this->paso_actual > 0)
			? $this->paso_actual - 1
			: $this->paso_actual;
		return $this->obtenerPasoActual();
	}

	/**
	 * Mueve el paso actual al siguiente, y retorna una instancia de dicho paso.
	 * @return PasoActuacion Instancia de paso de actuacion siguiente, o null si no hay mas pasos.
	 */
	public function pasoSiguiente()
	{
		$this->paso_actual = ($this->paso_actual <= count($this->pasos)-1)
			? $this->paso_actual + 1
			: $this->paso_actual;
		return $this->obtenerPasoActual();
	}

	/**
	 * Devuelve un nombre de clase a partir de un tipo de actuacion (utilizado generalmente
	 * como identificador de acción de controlador).
	 * @param  [type] $ptipo [description]
	 * @return [type]        [description]
	 */
	public function obtenerTipoDeClaseActuacion()
	{
		$aux = preg_replace('/^Actuacion(\w+)/', '$1', get_class($this));
		$aux = preg_replace('/([A-Z])/', '_$1', $aux);
		$aux = preg_replace('/^_/', '', $aux);
		return strtolower($aux);
	}

	/**
	 * De una lista de parametros obligatorios, devuelve aquellos que no
	 * estan presentes en la lista de parametros de la actuacion.
	 * @param  [array]  $obligatorios Array de parametros obligatorios.
	 * @return [array]                Listado de errores.
	 */
	protected function obtenerParametrosFaltantes($obligatorios = [])
	{
		$ret = [];
		$faltantes = array_diff($obligatorios, array_keys($this->parametros));
		if (count($faltantes) > 0)
			foreach ($faltantes as $p)
				$ret[] = "Falta el parámetro '$p'.";

		return $ret;
	}

	/**
	 * Verifica la integridad de los parámetros de la actuación. Este método
	 * se sobreescribe en cada actuación que herede esta clase.
	 * @return [Array] Lista de errores encontrados, o un array vacío si no hay errores.
	 */
	public function verificarParametros()
	{
		return [];
	}

	/**
	 * Debido a que la verificacion de parametros de expediente se usará en varias
	 * actuaciones, se mantiene un 'metodo helper' en la clase padre.
	 * @return [type] [description]
	 */
	public function verificarParametrosExpediente()
	{
		// Valido existencia de parametros
		$errores = $this->obtenerParametrosFaltantes(['anio', 'tipo', 'numero', 'cuerpo', 'alcance']);
		if (count($errores) > 0) return $errores;

		// Verifico la integridad de los parametros
		if (! preg_match('/^[0-9]{4,4}$/', $this->parametros['anio']))
			$errores[] = "Formato inválido para 'anio'.";

		if (! preg_match('/^(E|N|R)$/i', $this->parametros['tipo']))
			$errores[] = "Formato inválido para 'tipo'.";

		if (! preg_match('/^[0-9]{1,5}$/', $this->parametros['numero']))
			$errores[] = "Formato inválido para 'numero'.";

		if (! preg_match('/^[0-9]{1,5}$/', $this->parametros['cuerpo']))
			$errores[] = "Formato inválido para 'cuerpo'.";

		if (! preg_match('/^[0-9]{1,5}$/', $this->parametros['alcance']))
			$errores[] = "Formato inválido para 'alcance'.";

		return $errores;
	}

	/**
	 * Genera un texto informativo extra para mostrarse en la cabecera de cada paso.
	 * Este metodo se sobreescribe en todas las actuaciones que lo necesiten.
	 * @return [type] [description]
	 */
	
	/**
	 * Genera un texto informativo extra para mostrarse en la cabecera de cada paso.
	 * Este metodo se sobreescribe en todas las actuaciones que lo necesiten.
	 * @param  boolean $extendido Flag para determinar si se genera el texto "extendido", que aplica segun el caso.
	 * @return [type]             [description]
	 */
	public function generarTextoInformativo($extendido = false) 
	{
		return '';
	}

	/**
	 * Genera los parametros necesarios de retorno al cancelarse o finalizarse
	 * esta actuación. Responde con un controlador, accion y parámeteros según
	 * lo necesita la interfase de usuario.
	 * Este metodo se sobreescribe en todas las actuaciones que lo necesiten.
	 * NOTA: esta forma de trabajo rompe con tener la logica de negocio y modelo
	 * separada de la interfase, pero se hace en pos de tener mayor flexibilidad
	 * en las actuaciones.
	 * @return Array Resultado. Formato [<string>controlador, <string>accion, <array>parametros]
	 */
	public function obtenerRutaRetorno() 
	{
		return [
			'controlador' => 'expedientes',
			'accion' => 'view',
			'parametros' => []
		];
	}

	/**
	 * Genera el texto que será utilizado para las auditorias del sistema.
	 * @return [type] [description]
	 */
	public function obtenerDetalleAuditoria() 
	{
		$aud_data = array_merge([ get_class($this) => $this->version], $this->info_auditoria);

		$log_str = [];
		foreach ($aud_data as $k => $v) {
			$log_str[] = "$k: $v";
		}

		return join(' | ', $log_str);
	}
}
?>