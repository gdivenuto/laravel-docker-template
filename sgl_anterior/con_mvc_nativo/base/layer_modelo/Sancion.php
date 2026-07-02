<?php
/**
 * Clase Sancion
 * 
 * Descripción: expe_sanciones 
 * Layer Datos: DBExpedientes
 * Layer Negocio: NGExpedientes
 *
 * GenerateClass 0.97.4 beta @ 2016-09-01 13:51:00
 */
class Sancion extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio                ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo                ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero              ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo              ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance             ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $orden_proyecto      ; // (Primary Key) PHP: float      MySQL: decimal(2,0)
	protected $fecha_sancion       ; // (Primary Key) PHP: string     MySQL: date
	protected $numero_sancion      ; // PHP: string     MySQL: char(10) [Permite NULL]
	protected $fecha_promulga      ; // PHP: string     MySQL: date [Permite NULL]
	protected $numero_promulga     ; // PHP: string     MySQL: varchar(10) [Permite NULL]
	protected $decreto_promulga    ; // PHP: string     MySQL: varchar(10) [Permite NULL]
	protected $fecha_veto          ; // PHP: string     MySQL: date [Permite NULL]
	protected $decreto_veto        ; // PHP: string     MySQL: varchar(10) [Permite NULL]
	protected $decreto_presidencia ; // PHP: string     MySQL: varchar(10) [Permite NULL]
	protected $fecha_remision_de_comunicacion; // PHP: string     MySQL: date [Permite NULL]
	protected $fecha_1er_vto_comunicacion; // PHP: string     MySQL: date [Permite NULL]
	protected $fecha_2do_vto_comunicacion; // PHP: string     MySQL: date [Permite NULL]
	protected $fecha_rta_comunicacion; // PHP: string     MySQL: date [Permite NULL]
	protected $observaciones_sancion; // PHP: string     MySQL: text [Permite NULL]
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned
	
	// Atributos de sólo lectura
	protected $ro_descripcion_proyecto; // Asociado a orden_proyecto
	
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

	public function getOrden_Proyecto() { return $this->orden_proyecto; }
	public function setOrden_Proyecto($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setOrden_Proyecto(): no se permiten valores nulos para el atributo 'orden_proyecto'.", get_class($this)));
		if ( (!($this->esFloat($value) || $this->esInteger($value))) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setOrden_Proyecto(): el atributo 'orden_proyecto' solo permite valores de tipo float o double.", get_class($this)));
		$this->orden_proyecto = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Sancion() { return $this->fecha_sancion; }
	public function setFecha_Sancion($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setFecha_Sancion(): no se permiten valores nulos para el atributo 'fecha_sancion'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Sancion(): el atributo 'fecha_sancion' solo permite valores de tipo string.", get_class($this)));
		try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Sancion(): el atributo 'fecha_sancion' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_sancion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNumero_Sancion() { return $this->numero_sancion; }
	public function setNumero_Sancion($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setNumero_Sancion(): el atributo 'numero_sancion' solo permite valores de tipo string.", get_class($this)));
		$this->numero_sancion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Promulga() { return $this->fecha_promulga; }
	public function setFecha_Promulga($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Promulga(): el atributo 'fecha_promulga' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Promulga(): el atributo 'fecha_promulga' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_promulga = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNumero_Promulga() { return $this->numero_promulga; }
	public function setNumero_Promulga($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setNumero_Promulga(): el atributo 'numero_promulga' solo permite valores de tipo string.", get_class($this)));
		$this->numero_promulga = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDecreto_Promulga() { return $this->decreto_promulga; }
	public function setDecreto_Promulga($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDecreto_Promulga(): el atributo 'decreto_promulga' solo permite valores de tipo string.", get_class($this)));
		$this->decreto_promulga = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Veto() { return $this->fecha_veto; }
	public function setFecha_Veto($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Veto(): el atributo 'fecha_veto' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Veto(): el atributo 'fecha_veto' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_veto = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDecreto_Veto() { return $this->decreto_veto; }
	public function setDecreto_Veto($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDecreto_Veto(): el atributo 'decreto_veto' solo permite valores de tipo string.", get_class($this)));
		$this->decreto_veto = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDecreto_Presidencia() { return $this->decreto_presidencia; }
	public function setDecreto_Presidencia($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDecreto_Presidencia(): el atributo 'decreto_presidencia' solo permite valores de tipo string.", get_class($this)));
		$this->decreto_presidencia = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Remision_De_Comunicacion() { return $this->fecha_remision_de_comunicacion; }
	public function setFecha_Remision_De_Comunicacion($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Remision_De_Comunicacion(): el atributo 'fecha_remision_de_comunicacion' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Remision_De_Comunicacion(): el atributo 'fecha_remision_de_comunicacion' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_remision_de_comunicacion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_1Er_Vto_Comunicacion() { return $this->fecha_1er_vto_comunicacion; }
	public function setFecha_1Er_Vto_Comunicacion($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_1Er_Vto_Comunicacion(): el atributo 'fecha_1er_vto_comunicacion' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_1Er_Vto_Comunicacion(): el atributo 'fecha_1er_vto_comunicacion' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_1er_vto_comunicacion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_2Do_Vto_Comunicacion() { return $this->fecha_2do_vto_comunicacion; }
	public function setFecha_2Do_Vto_Comunicacion($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_2Do_Vto_Comunicacion(): el atributo 'fecha_2do_vto_comunicacion' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_2Do_Vto_Comunicacion(): el atributo 'fecha_2do_vto_comunicacion' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_2do_vto_comunicacion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Rta_Comunicacion() { return $this->fecha_rta_comunicacion; }
	public function setFecha_Rta_Comunicacion($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Rta_Comunicacion(): el atributo 'fecha_rta_comunicacion' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Rta_Comunicacion(): el atributo 'fecha_rta_comunicacion' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_rta_comunicacion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getObservaciones_Sancion() { return $this->observaciones_sancion; }
	public function setObservaciones_Sancion($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones_Sancion(): el atributo 'observaciones_sancion' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones_sancion = $value;
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

	// Atributos de sólo lectura
	public function getDescripcion_Proyecto() { return $this->ro_descripcion_proyecto; }
	public function setDescripcion_Proyecto($value) { /**/ }

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
	 * @param  float (PK) orden_proyecto
	 * @param  string (PK) fecha_sancion
	 * @param  string numero_sancion
	 * @param  string fecha_promulga
	 * @param  string numero_promulga
	 * @param  string decreto_promulga
	 * @param  string fecha_veto
	 * @param  string decreto_veto
	 * @param  string decreto_presidencia
	 * @param  string fecha_remision_de_comunicacion
	 * @param  string fecha_1er_vto_comunicacion
	 * @param  string fecha_2do_vto_comunicacion
	 * @param  string fecha_rta_comunicacion
	 * @param  string observaciones_sancion
	 * @param  integer id_usuario
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$porden_proyecto = 0.0,
		$pfecha_sancion = '',
		$pnumero_sancion = null,
		$pfecha_promulga = null,
		$pnumero_promulga = null,
		$pdecreto_promulga = null,
		$pfecha_veto = null,
		$pdecreto_veto = null,
		$pdecreto_presidencia = null,
		$pfecha_remision_de_comunicacion = null,
		$pfecha_1er_vto_comunicacion = null,
		$pfecha_2do_vto_comunicacion = null,
		$pfecha_rta_comunicacion = null,
		$pobservaciones_sancion = null,
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
		$this->orden_proyecto       = $porden_proyecto;
		$this->fecha_sancion        = $pfecha_sancion;
		$this->numero_sancion       = $pnumero_sancion;
		$this->fecha_promulga       = $pfecha_promulga;
		$this->numero_promulga      = $pnumero_promulga;
		$this->decreto_promulga     = $pdecreto_promulga;
		$this->fecha_veto           = $pfecha_veto;
		$this->decreto_veto         = $pdecreto_veto;
		$this->decreto_presidencia  = $pdecreto_presidencia;
		$this->fecha_remision_de_comunicacion = $pfecha_remision_de_comunicacion;
		$this->fecha_1er_vto_comunicacion = $pfecha_1er_vto_comunicacion;
		$this->fecha_2do_vto_comunicacion = $pfecha_2do_vto_comunicacion;
		$this->fecha_rta_comunicacion = $pfecha_rta_comunicacion;
		$this->observaciones_sancion = $pobservaciones_sancion;
		$this->id_usuario           = $pid_usuario;	
		
		// Atributos de sólo lectura
		$this->ro_descripcion_proyecto = null;
		
		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>