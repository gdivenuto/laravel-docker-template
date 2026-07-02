<?php
/**
 * Clase Modulo
 * 
 * Descripción: admin_modulos 
 * Layer Datos: DBSeguridad
 * Layer Negocio: NGSeguridad
 *
 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:47
 */
class Modulo extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $id_modulo           ; // (Primary Key) PHP: string     MySQL: char(32)
	protected $nombre_modulo       ; // PHP: string     MySQL: varchar(100)
	protected $descripcion_modulo  ; // PHP: string     MySQL: text
	protected $activo              ; // PHP: bool       MySQL: tinyint(1)
	
	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getId_Modulo() { return $this->id_modulo; }
	public function setId_Modulo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Modulo(): no se permiten valores nulos para el atributo 'id_modulo'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Modulo(): el atributo 'id_modulo' solo permite valores de tipo string.", get_class($this)));
		$this->id_modulo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNombre_Modulo() { return $this->nombre_modulo; }
	public function setNombre_Modulo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setNombre_Modulo(): no se permiten valores nulos para el atributo 'nombre_modulo'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setNombre_Modulo(): el atributo 'nombre_modulo' solo permite valores de tipo string.", get_class($this)));
		$this->nombre_modulo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDescripcion_Modulo() { return $this->descripcion_modulo; }
	public function setDescripcion_Modulo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setDescripcion_Modulo(): no se permiten valores nulos para el atributo 'descripcion_modulo'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDescripcion_Modulo(): el atributo 'descripcion_modulo' solo permite valores de tipo string.", get_class($this)));
		$this->descripcion_modulo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getActivo() { return $this->activo; }
	public function setActivo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setActivo(): no se permiten valores nulos para el atributo 'activo'.", get_class($this)));
		if ( (!is_bool($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setActivo(): el atributo 'activo' solo permite valores de tipo boolean.", get_class($this)));
		$this->activo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 * @param  string (PK) id_modulo
	 * @param  string nombre_modulo
	 * @param  string descripcion_modulo
	 * @param  bool activo
	 */
	public function __construct(
		$pid_modulo = '',
		$pnombre_modulo = '',
		$pdescripcion_modulo = '',
		$pactivo = false)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no 
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se 
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->id_modulo            = $pid_modulo;
		$this->nombre_modulo        = $pnombre_modulo;
		$this->descripcion_modulo   = $pdescripcion_modulo;
		$this->activo               = $pactivo;	
		
		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>