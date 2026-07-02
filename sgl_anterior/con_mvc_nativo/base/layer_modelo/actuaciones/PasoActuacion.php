<?php
/**
 * Clase PasoActuacion
 * 
 * Clase Base encargada de contener la configuración de un paso de una actuación determinada.
 *
 * NOTA: actualmente la clase no posee persistencia en base de datos.
 * 
 */
class PasoActuacion extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $nombre;   // [String] Nombre de fantasía del paso de actuación.

	protected $opciones_default; // [Array]  Lista de opciones por defecto del paso (modificadores de comportamiento).
	public $opciones;         // [Array]  Lista de opciones del paso (modificadores de comportamiento).

	public $datos;            // [Array]  Array que se completa con los datos que requiere el paso para generar la interfase.

	protected $id_transaccion;   // [Integer] Referencia de PK para acceder a los datos almacenados.
	protected $id_paso;          // [Integer] Referencia de PK para acceder a los datos almacenados.
	
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

	public function getId_Transaccion() { return $this->id_transaccion; }
	public function setId_Transaccion($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Transaccion(): no se permiten valores nulos para el atributo 'id_transaccion'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Transaccion(): el atributo 'id_transaccion' solo permite valores de tipo integer.", get_class($this)));
		$this->id_transaccion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getId_Paso() { return $this->id_paso; }
	public function setId_Paso($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Paso(): no se permiten valores nulos para el atributo 'id_paso'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Paso(): el atributo 'id_paso' solo permite valores de tipo integer.", get_class($this)));
		$this->id_paso = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct($pnombre = '', $popciones_default = [], $popciones = [])
	{
		// Asignación de atributos.
		$this->nombre = $pnombre;
		$this->opciones_default = $popciones_default;
		$this->opciones = $this->generarOpciones($popciones_default, $popciones);

		// Los datos son vacios
		$this->datos = [];

		// Valores indefinidos para la transaccion y el paso
		$this->id_transaccion = -1;
		$this->id_paso = -1;
	}

	/**
	 * Toma las opciones por defecto y les hace un 'merge' con un nuevo array de opciones.
	 * @param  array  $default  Opciones por defecto.
	 * @param  array  $opciones Nuevas opciones customizadas.
	 * @return array            Resultado del merge entre opciones.
	 */
	public function generarOpciones($default = [], $opciones = [])
	{
		// Preset de opciones para este tipo de paso
		$new_opciones = $default;

		// Piso / Agrego opciones a las opciones default
		foreach ($opciones as $key => $value) {
			$new_opciones[$key] = (is_array($value))
				? $this->generarOpciones($new_opciones[$key], $value)
				: $value;
		}

		return $new_opciones;
	}
}
?>