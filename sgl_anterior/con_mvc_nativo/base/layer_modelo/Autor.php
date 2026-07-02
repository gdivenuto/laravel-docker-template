<?php
/**
 * Clase Autor
 * 
 * Descripción: expe_autores 
 * Layer Datos: DBExpedientes
 * Layer Negocio: NGExpedientes
 *
 * GenerateClass 0.97.4 beta @ 2016-09-01 13:50:59
 */
class Autor extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio                ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo                ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero              ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo              ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance             ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $autor_tipo          ; // (Primary Key) PHP: string     MySQL: varchar(3)
	protected $autor_codigo        ; // (Primary Key) PHP: string     MySQL: varchar(10)
	protected $autor_bloque_tipo   ; // PHP: string     MySQL: varchar(3) [Permite NULL]
	protected $autor_bloque_codigo ; // PHP: string     MySQL: varchar(10) [Permite NULL]
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned

	// Atributos de solo lectura
	protected $ro_descripcion_grp; // Asociado a autor_tipo y autor_codigo
	
	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getAnio() { return $this->anio; }
	public function setAnio($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAnio(): no se permiten valores nulos para el atributo 'anio'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAnio(): el atributo 'anio' solo permite valores de tipo integer.", get_class($this)));
		$this->anio = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getTipo() { return $this->tipo; }
	public function setTipo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setTipo(): no se permiten valores nulos para el atributo 'tipo'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setTipo(): el atributo 'tipo' solo permite valores de tipo string.", get_class($this)));
		$this->tipo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNumero() { return $this->numero; }
	public function setNumero($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setNumero(): no se permiten valores nulos para el atributo 'numero'.", get_class($this)));
		if ( (!($this->esFloat($value) || $this->esInteger($value))) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setNumero(): el atributo 'numero' solo permite valores de tipo float o double.", get_class($this)));
		$this->numero = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpo() { return $this->cuerpo; }
	public function setCuerpo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setCuerpo(): no se permiten valores nulos para el atributo 'cuerpo'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpo(): el atributo 'cuerpo' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAlcance() { return $this->alcance; }
	public function setAlcance($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAlcance(): no se permiten valores nulos para el atributo 'alcance'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAlcance(): el atributo 'alcance' solo permite valores de tipo integer.", get_class($this)));
		$this->alcance = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAutor_Tipo() { return $this->autor_tipo; }
	public function setAutor_Tipo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAutor_Tipo(): no se permiten valores nulos para el atributo 'autor_tipo'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAutor_Tipo(): el atributo 'autor_tipo' solo permite valores de tipo string.", get_class($this)));
		$this->autor_tipo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAutor_Codigo() { return $this->autor_codigo; }
	public function setAutor_Codigo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAutor_Codigo(): no se permiten valores nulos para el atributo 'autor_codigo'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAutor_Codigo(): el atributo 'autor_codigo' solo permite valores de tipo string.", get_class($this)));
		$this->autor_codigo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAutor_Bloque_Tipo() { return $this->autor_bloque_tipo; }
	public function setAutor_Bloque_Tipo($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAutor_Bloque_Tipo(): el atributo 'autor_bloque_tipo' solo permite valores de tipo string.", get_class($this)));
		$this->autor_bloque_tipo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAutor_Bloque_Codigo() { return $this->autor_bloque_codigo; }
	public function setAutor_Bloque_Codigo($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAutor_Bloque_Codigo(): el atributo 'autor_bloque_codigo' solo permite valores de tipo string.", get_class($this)));
		$this->autor_bloque_codigo = $value;
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

	// Atributos de solo lectura
	public function getDescripcion_Grp() { return $this->ro_descripcion_grp; }
	public function setDescripcion_Grp($value) { /* atributo de solo lectura */ }

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) autor_tipo
	 * @param  string (PK) autor_codigo
	 * @param  string autor_bloque_tipo
	 * @param  string autor_bloque_codigo
	 * @param  integer id_usuario
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$pautor_tipo = '',
		$pautor_codigo = '',
		$pautor_bloque_tipo = null,
		$pautor_bloque_codigo = null,
		$pid_usuario = 0)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no 
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se 
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->anio                 = $panio;
		$this->tipo                 = $ptipo;
		$this->numero               = $pnumero;
		$this->cuerpo               = $pcuerpo;
		$this->alcance              = $palcance;
		$this->autor_tipo           = $pautor_tipo;
		$this->autor_codigo         = $pautor_codigo;
		$this->autor_bloque_tipo    = $pautor_bloque_tipo;
		$this->autor_bloque_codigo  = $pautor_bloque_codigo;
		$this->id_usuario           = $pid_usuario;	

		// Atributos de solo lectura
		$this->ro_descripcion_grp = null;
		
		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>