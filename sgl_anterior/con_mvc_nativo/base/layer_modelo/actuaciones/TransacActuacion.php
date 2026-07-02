<?php
/**
 * Clase TransacActuacion
 * 
 * Descripción: expe_transac_actuaciones 
 * Layer Datos: DBActuaciones
 * Layer Negocio: NGActuaciones
 *
 * GenerateClass 0.97.7 beta @ 2022-08-10 13:50:42
 */
class TransacActuacion extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $id_transaccion      ; // (Primary Key) PHP: integer    MySQL: int(11) unsigned
	protected $id_paso             ; // (Primary Key) PHP: integer    MySQL: int(11)
	protected $tipo_actuacion      ; // PHP: string     MySQL: varchar(50)
	protected $fecha_hora          ; // PHP: string     MySQL: datetime
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned
	protected $data                ; // PHP: string     MySQL: longtext [Permite NULL]
	
	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
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

	public function getTipo_Actuacion() { return $this->tipo_actuacion; }
	public function setTipo_Actuacion($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setTipo_Actuacion(): no se permiten valores nulos para el atributo 'tipo_actuacion'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setTipo_Actuacion(): el atributo 'tipo_actuacion' solo permite valores de tipo string.", get_class($this)));
		$this->tipo_actuacion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Hora() { return $this->fecha_hora; }
	public function setFecha_Hora($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setFecha_Hora(): no se permiten valores nulos para el atributo 'fecha_hora'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Hora(): el atributo 'fecha_hora' solo permite valores de tipo string.", get_class($this)));
		try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Hora(): el atributo 'fecha_hora' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_hora = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getId_Usuario() { return $this->id_usuario; }
	public function setId_Usuario($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Usuario(): no se permiten valores nulos para el atributo 'id_usuario'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Usuario(): el atributo 'id_usuario' solo permite valores de tipo integer.", get_class($this)));
		$this->id_usuario = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getData() { return $this->data; }
	public function setData($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setData(): el atributo 'data' solo permite valores de tipo string.", get_class($this)));
		$this->data = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 * @param  integer (PK) id_transaccion
	 * @param  integer (PK) id_paso
	 * @param  string tipo_actuacion
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string data
	 */
	public function __construct(
		$pid_transaccion = 0,
		$pid_paso = 0,
		$ptipo_actuacion = '',
		$pfecha_hora = 'CURRENT_TIMESTAMP',
		$pid_usuario = 0,
		$pdata = null)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no 
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se 
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->id_transaccion       = $pid_transaccion;
		$this->id_paso              = $pid_paso;
		$this->tipo_actuacion       = $ptipo_actuacion;
		$this->fecha_hora           = ($pfecha_hora == 'CURRENT_TIMESTAMP') ? date('Y-m-d H:i:s') : $pfecha_hora;
		$this->id_usuario           = $pid_usuario;
		$this->data                 = $pdata;	
		
		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>