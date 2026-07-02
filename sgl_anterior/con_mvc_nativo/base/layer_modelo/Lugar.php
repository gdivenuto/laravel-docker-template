<?php
/**
 * Clase Lugar
 * 
 * Descripción: expe_lugares 
 * Layer Datos: DBExpedientesParam
 * Layer Negocio: NGExpedientesParam
 *
 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:11
 */
class Lugar extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $tipo_grp            ; // (Primary Key) PHP: string     MySQL: varchar(3)
	protected $codigo_grp          ; // (Primary Key) PHP: string     MySQL: varchar(10)
	protected $descripcion_grp     ; // PHP: string     MySQL: varchar(60) [Permite NULL]
	protected $abreviatura_grp     ; // PHP: string     MySQL: varchar(100) [Permite NULL]
	protected $bloque_tipo         ; // PHP: string     MySQL: varchar(3) [Permite NULL]
	protected $bloque_codigo       ; // PHP: string     MySQL: varchar(10) [Permite NULL]
	protected $observaciones_grp   ; // PHP: string     MySQL: text [Permite NULL]
	protected $vigente_Desde_grp   ; // PHP: string     MySQL: date [Permite NULL]
	protected $vigente_Hasta_grp   ; // PHP: string     MySQL: date [Permite NULL]
	protected $habilitado_grp      ; // PHP: string     MySQL: char(1)
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned
	
	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getTipo_Grp() { return $this->tipo_grp; }
	public function setTipo_Grp($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setTipo_Grp(): no se permiten valores nulos para el atributo 'tipo_grp'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setTipo_Grp(): el atributo 'tipo_grp' solo permite valores de tipo string.", get_class($this)));
		$this->tipo_grp = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCodigo_Grp() { return $this->codigo_grp; }
	public function setCodigo_Grp($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setCodigo_Grp(): no se permiten valores nulos para el atributo 'codigo_grp'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCodigo_Grp(): el atributo 'codigo_grp' solo permite valores de tipo string.", get_class($this)));
		$this->codigo_grp = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDescripcion_Grp() { return $this->descripcion_grp; }
	public function setDescripcion_Grp($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDescripcion_Grp(): el atributo 'descripcion_grp' solo permite valores de tipo string.", get_class($this)));
		$this->descripcion_grp = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAbreviatura_Grp() { return $this->abreviatura_grp; }
	public function setAbreviatura_Grp($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAbreviatura_Grp(): el atributo 'abreviatura_grp' solo permite valores de tipo string.", get_class($this)));
		$this->abreviatura_grp = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getBloque_Tipo() { return $this->bloque_tipo; }
	public function setBloque_Tipo($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setBloque_Tipo(): el atributo 'bloque_tipo' solo permite valores de tipo string.", get_class($this)));
		$this->bloque_tipo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getBloque_Codigo() { return $this->bloque_codigo; }
	public function setBloque_Codigo($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setBloque_Codigo(): el atributo 'bloque_codigo' solo permite valores de tipo string.", get_class($this)));
		$this->bloque_codigo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getObservaciones_Grp() { return $this->observaciones_grp; }
	public function setObservaciones_Grp($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones_Grp(): el atributo 'observaciones_grp' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones_grp = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVigente_Desde_Grp() { return $this->vigente_Desde_grp; }
	public function setVigente_Desde_Grp($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setVigente_Desde_Grp(): el atributo 'vigente_Desde_grp' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setVigente_Desde_Grp(): el atributo 'vigente_Desde_grp' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->vigente_Desde_grp = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVigente_Hasta_Grp() { return $this->vigente_Hasta_grp; }
	public function setVigente_Hasta_Grp($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setVigente_Hasta_Grp(): el atributo 'vigente_Hasta_grp' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setVigente_Hasta_Grp(): el atributo 'vigente_Hasta_grp' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->vigente_Hasta_grp = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getHabilitado_Grp() { return $this->habilitado_grp; }
	public function setHabilitado_Grp($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setHabilitado_Grp(): no se permiten valores nulos para el atributo 'habilitado_grp'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setHabilitado_Grp(): el atributo 'habilitado_grp' solo permite valores de tipo string.", get_class($this)));
		$this->habilitado_grp = $value;
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

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 * @param  string (PK) tipo_grp
	 * @param  string (PK) codigo_grp
	 * @param  string descripcion_grp
	 * @param  string abreviatura_grp
	 * @param  string bloque_tipo
	 * @param  string bloque_codigo
	 * @param  string observaciones_grp
	 * @param  string vigente_Desde_grp
	 * @param  string vigente_Hasta_grp
	 * @param  string habilitado_grp
	 * @param  integer id_usuario
	 */
	public function __construct(
		$ptipo_grp = '',
		$pcodigo_grp = '',
		$pdescripcion_grp = null,
		$pabreviatura_grp = null,
		$pbloque_tipo = null,
		$pbloque_codigo = null,
		$pobservaciones_grp = null,
		$pvigente_Desde_grp = null,
		$pvigente_Hasta_grp = null,
		$phabilitado_grp = '',
		$pid_usuario = 0)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no 
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se 
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->tipo_grp             = $ptipo_grp;
		$this->codigo_grp           = $pcodigo_grp;
		$this->descripcion_grp      = $pdescripcion_grp;
		$this->abreviatura_grp      = $pabreviatura_grp;
		$this->bloque_tipo          = $pbloque_tipo;
		$this->bloque_codigo        = $pbloque_codigo;
		$this->observaciones_grp    = $pobservaciones_grp;
		$this->vigente_Desde_grp    = $pvigente_Desde_grp;
		$this->vigente_Hasta_grp    = $pvigente_Hasta_grp;
		$this->habilitado_grp       = $phabilitado_grp;
		$this->id_usuario           = $pid_usuario;	
		
		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>