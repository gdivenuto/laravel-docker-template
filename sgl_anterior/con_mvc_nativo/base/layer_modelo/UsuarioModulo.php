<?php
/**
 * Clase UsuarioModulo
 * 
 * Descripción: admin_usuarios_x_modulo 
 * Layer Datos: DBSeguridad
 * Layer Negocio: NGSeguridad
 *
 * GenerateClass 0.97.5 beta @ 2016-09-21 11:25:02
 */
class UsuarioModulo extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $id_usuario          ; // (Primary Key) PHP: integer    MySQL: int(10) unsigned
	protected $id_modulo           ; // (Primary Key) PHP: string     MySQL: char(32)
	protected $nivel               ; // PHP: integer    MySQL: int(11)
	
	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getId_Usuario() { return $this->id_usuario; }
	public function setId_Usuario($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Usuario(): no se permiten valores nulos para el atributo 'id_usuario'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Usuario(): el atributo 'id_usuario' solo permite valores de tipo integer.", get_class($this)));
		$this->id_usuario = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getId_Modulo() { return $this->id_modulo; }
	public function setId_Modulo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Modulo(): no se permiten valores nulos para el atributo 'id_modulo'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Modulo(): el atributo 'id_modulo' solo permite valores de tipo string.", get_class($this)));
		$this->id_modulo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNivel() { return $this->nivel; }
	public function setNivel($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setNivel(): no se permiten valores nulos para el atributo 'nivel'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setNivel(): el atributo 'nivel' solo permite valores de tipo integer.", get_class($this)));
		$this->nivel = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 * @param  integer (PK) id_usuario
	 * @param  string (PK) id_modulo
	 * @param  integer nivel
	 */
	public function __construct(
		$pid_usuario = 0,
		$pid_modulo = '',
		$pnivel = 0)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no 
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se 
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->id_usuario           = $pid_usuario;
		$this->id_modulo            = $pid_modulo;
		$this->nivel                = $pnivel;	
		
		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>