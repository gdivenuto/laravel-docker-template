<?php
/**
 * Clase Auditoria
 * 
 * Descripción: expe_auditoria 
 * Layer Datos: DBAuditoria
 * Layer Negocio: NGAuditoria
 *
 * GenerateClass 0.97.7 beta @ 2018-03-15 09:49:48
 */
class Auditoria extends ClaseBase {
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	// Operaciones "normales" de auditoria de expedientes.
	const OP_ALTA = 'ALTA';
	const OP_ARCHIVO_MOVIDO = 'ARCHIVO MOVIDO';
	const OP_ARCHIVO_SUBIDO = 'ARCHIVO SUBIDO';
	const OP_ARCHIVO_AGREGADO = 'ARCHIVO AGREGADO';
	const OP_ARCHIVO_SOBREESCRITO = 'ARCHIVO SOBREESCRITO';
	const OP_ARCHIVO_ELIMINADO = 'ARCHIVO ELIMINADO';
	const OP_BAJA = 'BAJA';
	const OP_COPIA = 'COPIA';
	const OP_CREA = 'CREA';
	const OP_DESHABILITADO = 'DESHABILITADO';
	const OP_ELIMINA = 'ELIMINA';
	const OP_ERROR = 'ERROR';
	const OP_MARCA = 'MARCA';
	const OP_MODIFICA = 'MODIFICA';
	const OP_SANCIONADO = 'SANCIONADO';
	const OP_PROCESA = 'PROCESA'; // para Actuaciones gralmente.

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $id_log              ; // (Primary Key) PHP: integer    MySQL: int(10) unsigned
	protected $fecha_hora_log      ; // PHP: string     MySQL: timestamp
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned
	protected $operacion           ; // PHP: string     MySQL: varchar(10)
	protected $tabla               ; // PHP: string     MySQL: varchar(20) [Permite NULL]
	protected $anio_log            ; // PHP: integer    MySQL: smallint(6)
	protected $tipo_log            ; // PHP: string     MySQL: char(1)
	protected $numero_log          ; // PHP: float      MySQL: decimal(10,0)
	protected $digito_log          ; // PHP: string     MySQL: char(2) [Permite NULL]
	protected $cuerpo_log          ; // PHP: integer    MySQL: smallint(6)
	protected $alcance_log         ; // PHP: integer    MySQL: smallint(6)
	protected $cuerpoalcance_log   ; // PHP: integer    MySQL: smallint(6) [Permite NULL]
	protected $anexoalcance_log    ; // PHP: integer    MySQL: smallint(6) [Permite NULL]
	protected $cuerpoanexoalcance_log; // PHP: integer    MySQL: smallint(6) [Permite NULL]
	protected $anexo_log           ; // PHP: integer    MySQL: smallint(6) [Permite NULL]
	protected $cuerpoanexo_log     ; // PHP: integer    MySQL: smallint(6) [Permite NULL]
	protected $fecha_log           ; // PHP: string     MySQL: date [Permite NULL]
	protected $orden_log           ; // PHP: float      MySQL: decimal(2,0) [Permite NULL]
	protected $netusername         ; // PHP: string     MySQL: varchar(15) [Permite NULL]
	protected $netpcname           ; // PHP: string     MySQL: varchar(20) [Permite NULL]
	protected $observaciones_log   ; // PHP: string     MySQL: text [Permite NULL]
	
	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getId_Log() { return $this->id_log; }
	public function setId_Log($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Log(): no se permiten valores nulos para el atributo 'id_log'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Log(): el atributo 'id_log' solo permite valores de tipo integer.", get_class($this)));
		$this->id_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Hora_Log() { return $this->fecha_hora_log; }
	public function setFecha_Hora_Log($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setFecha_Hora_Log(): no se permiten valores nulos para el atributo 'fecha_hora_log'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Hora_Log(): el atributo 'fecha_hora_log' solo permite valores de tipo string.", get_class($this)));
		try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Hora_Log(): el atributo 'fecha_hora_log' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_hora_log = $value;
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

	public function getOperacion() { return $this->operacion; }
	public function setOperacion($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setOperacion(): no se permiten valores nulos para el atributo 'operacion'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setOperacion(): el atributo 'operacion' solo permite valores de tipo string.", get_class($this)));
		$this->operacion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getTabla() { return $this->tabla; }
	public function setTabla($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setTabla(): el atributo 'tabla' solo permite valores de tipo string.", get_class($this)));
		$this->tabla = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAnio_Log() { return $this->anio_log; }
	public function setAnio_Log($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAnio_Log(): no se permiten valores nulos para el atributo 'anio_log'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAnio_Log(): el atributo 'anio_log' solo permite valores de tipo integer.", get_class($this)));
		$this->anio_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getTipo_Log() { return $this->tipo_log; }
	public function setTipo_Log($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setTipo_Log(): no se permiten valores nulos para el atributo 'tipo_log'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setTipo_Log(): el atributo 'tipo_log' solo permite valores de tipo string.", get_class($this)));
		$this->tipo_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNumero_Log() { return $this->numero_log; }
	public function setNumero_Log($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setNumero_Log(): no se permiten valores nulos para el atributo 'numero_log'.", get_class($this)));
		if ( (!($this->esFloat($value) || $this->esInteger($value))) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setNumero_Log(): el atributo 'numero_log' solo permite valores de tipo float o double.", get_class($this)));
		$this->numero_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDigito_Log() { return $this->digito_log; }
	public function setDigito_Log($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDigito_Log(): el atributo 'digito_log' solo permite valores de tipo string.", get_class($this)));
		$this->digito_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpo_Log() { return $this->cuerpo_log; }
	public function setCuerpo_Log($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setCuerpo_Log(): no se permiten valores nulos para el atributo 'cuerpo_log'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpo_Log(): el atributo 'cuerpo_log' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpo_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAlcance_Log() { return $this->alcance_log; }
	public function setAlcance_Log($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAlcance_Log(): no se permiten valores nulos para el atributo 'alcance_log'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAlcance_Log(): el atributo 'alcance_log' solo permite valores de tipo integer.", get_class($this)));
		$this->alcance_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpoalcance_Log() { return $this->cuerpoalcance_log; }
	public function setCuerpoalcance_Log($value) { 
		if ( (!is_null($value)) && (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpoalcance_Log(): el atributo 'cuerpoalcance_log' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpoalcance_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAnexoalcance_Log() { return $this->anexoalcance_log; }
	public function setAnexoalcance_Log($value) { 
		if ( (!is_null($value)) && (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAnexoalcance_Log(): el atributo 'anexoalcance_log' solo permite valores de tipo integer.", get_class($this)));
		$this->anexoalcance_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpoanexoalcance_Log() { return $this->cuerpoanexoalcance_log; }
	public function setCuerpoanexoalcance_Log($value) { 
		if ( (!is_null($value)) && (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpoanexoalcance_Log(): el atributo 'cuerpoanexoalcance_log' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpoanexoalcance_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAnexo_Log() { return $this->anexo_log; }
	public function setAnexo_Log($value) { 
		if ( (!is_null($value)) && (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAnexo_Log(): el atributo 'anexo_log' solo permite valores de tipo integer.", get_class($this)));
		$this->anexo_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpoanexo_Log() { return $this->cuerpoanexo_log; }
	public function setCuerpoanexo_Log($value) { 
		if ( (!is_null($value)) && (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpoanexo_Log(): el atributo 'cuerpoanexo_log' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpoanexo_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Log() { return $this->fecha_log; }
	public function setFecha_Log($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Log(): el atributo 'fecha_log' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Log(): el atributo 'fecha_log' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getOrden_Log() { return $this->orden_log; }
	public function setOrden_Log($value) { 
		if ( (!is_null($value)) && (!($this->esFloat($value) || $this->esInteger($value))) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setOrden_Log(): el atributo 'orden_log' solo permite valores de tipo float o double.", get_class($this)));
		$this->orden_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNetusername() { return $this->netusername; }
	public function setNetusername($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setNetusername(): el atributo 'netusername' solo permite valores de tipo string.", get_class($this)));
		$this->netusername = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNetpcname() { return $this->netpcname; }
	public function setNetpcname($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setNetpcname(): el atributo 'netpcname' solo permite valores de tipo string.", get_class($this)));
		$this->netpcname = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getObservaciones_Log() { return $this->observaciones_log; }
	public function setObservaciones_Log($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones_Log(): el atributo 'observaciones_log' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones_log = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 * @param  integer (PK) id_log
	 * @param  string fecha_hora_log
	 * @param  integer id_usuario
	 * @param  string operacion
	 * @param  string tabla
	 * @param  integer anio_log
	 * @param  string tipo_log
	 * @param  float numero_log
	 * @param  string digito_log
	 * @param  integer cuerpo_log
	 * @param  integer alcance_log
	 * @param  integer cuerpoalcance_log
	 * @param  integer anexoalcance_log
	 * @param  integer cuerpoanexoalcance_log
	 * @param  integer anexo_log
	 * @param  integer cuerpoanexo_log
	 * @param  string fecha_log
	 * @param  float orden_log
	 * @param  string netusername
	 * @param  string netpcname
	 * @param  string observaciones_log
	 */
	public function __construct(
		$pid_log = null,
		$pfecha_hora_log = 'CURRENT_TIMESTAMP',
		$pid_usuario = 0,
		$poperacion = '',
		$ptabla = null,
		$panio_log = 0,
		$ptipo_log = '',
		$pnumero_log = 0.0,
		$pdigito_log = null,
		$pcuerpo_log = 0,
		$palcance_log = 0,
		$pcuerpoalcance_log = null,
		$panexoalcance_log = null,
		$pcuerpoanexoalcance_log = null,
		$panexo_log = null,
		$pcuerpoanexo_log = null,
		$pfecha_log = null,
		$porden_log = null,
		$pnetusername = null,
		$pnetpcname = null,
		$pobservaciones_log = null)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no 
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se 
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->id_log               = $pid_log;
		$this->fecha_hora_log       = $pfecha_hora_log;
		$this->id_usuario           = $pid_usuario;
		$this->operacion            = $poperacion;
		$this->tabla                = $ptabla;
		$this->anio_log             = $panio_log;
		$this->tipo_log             = $ptipo_log;
		$this->numero_log           = $pnumero_log;
		$this->digito_log           = $pdigito_log;
		$this->cuerpo_log           = $pcuerpo_log;
		$this->alcance_log          = $palcance_log;
		$this->cuerpoalcance_log    = $pcuerpoalcance_log;
		$this->anexoalcance_log     = $panexoalcance_log;
		$this->cuerpoanexoalcance_log = $pcuerpoanexoalcance_log;
		$this->anexo_log            = $panexo_log;
		$this->cuerpoanexo_log      = $pcuerpoanexo_log;
		$this->fecha_log            = $pfecha_log;
		$this->orden_log            = $porden_log;
		$this->netusername          = $pnetusername;
		$this->netpcname            = $pnetpcname;
		$this->observaciones_log    = $pobservaciones_log;	
		
		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>