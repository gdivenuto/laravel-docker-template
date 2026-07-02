<?php
/**
 * Clase GiroPendiente
 * 
 * Descripción: expe_giros_pendientes 
 * Layer Datos: DBGirosPendientes
 * Layer Negocio: NGGirosPendientes
 *
 * GenerateClass 0.97.7 beta @ 2022-10-31 10:21:36
 */
class GiroPendiente extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio                ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo                ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero              ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo              ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance             ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $id_pendiente        ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $giros_pendientes    ; // PHP: string     MySQL: longtext
	protected $estado              ; // PHP: string     MySQL: enum('pendiente','confirmado','rechazado')
	protected $fecha_hora_entrada  ; // PHP: string     MySQL: datetime
	protected $fecha_hora_salida   ; // PHP: string     MySQL: datetime [Permite NULL]
	protected $id_usuario_firmante ; // PHP: integer    MySQL: int(10) unsigned
	protected $id_usuario_solicitante; // PHP: integer    MySQL: int(10) unsigned
	protected $observaciones       ; // PHP: string     MySQL: longtext [Permite NULL]

	// Atributos de solo lectura
	protected $ro_codigo_usuario_firmante; // Asociado a id_usuario_firmante
	protected $ro_nombre_usuario_firmante; // Asociado a id_usuario_firmante
	protected $ro_mail_usuario_firmante;   // Asociado a id_usuario_firmante
	protected $ro_codigo_usuario_solicitante; // Asociado a id_usuario_solicitante
	protected $ro_nombre_usuario_solicitante; // Asociado a id_usuario_solicitante
	protected $ro_mail_usuario_solicitante;   // Asociado a id_usuario_solicitante

	
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

	public function getId_Pendiente() { return $this->id_pendiente; }
	public function setId_Pendiente($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Pendiente(): no se permiten valores nulos para el atributo 'id_pendiente'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Pendiente(): el atributo 'id_pendiente' solo permite valores de tipo integer.", get_class($this)));
		$this->id_pendiente = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getGiros_Pendientes() { return $this->giros_pendientes; }
	public function setGiros_Pendientes($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setGiros_Pendientes(): no se permiten valores nulos para el atributo 'giros_pendientes'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setGiros_Pendientes(): el atributo 'giros_pendientes' solo permite valores de tipo string.", get_class($this)));
		$this->giros_pendientes = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getEstado() { return $this->estado; }
	public function setEstado($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setEstado(): no se permiten valores nulos para el atributo 'estado'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setEstado(): el atributo 'estado' solo permite valores de tipo string.", get_class($this)));
		$this->estado = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Hora_Entrada() { return $this->fecha_hora_entrada; }
	public function setFecha_Hora_Entrada($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setFecha_Hora_Entrada(): no se permiten valores nulos para el atributo 'fecha_hora_entrada'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Hora_Entrada(): el atributo 'fecha_hora_entrada' solo permite valores de tipo string.", get_class($this)));
		try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Hora_Entrada(): el atributo 'fecha_hora_entrada' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_hora_entrada = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Hora_Salida() { return $this->fecha_hora_salida; }
	public function setFecha_Hora_Salida($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Hora_Salida(): el atributo 'fecha_hora_salida' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Hora_Salida(): el atributo 'fecha_hora_salida' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_hora_salida = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getId_Usuario_Firmante() { return $this->id_usuario_firmante; }
	public function setId_Usuario_Firmante($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Usuario_Firmante(): no se permiten valores nulos para el atributo 'id_usuario_firmante'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Usuario_Firmante(): el atributo 'id_usuario_firmante' solo permite valores de tipo integer.", get_class($this)));
		$this->id_usuario_firmante = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getId_Usuario_Solicitante() { return $this->id_usuario_solicitante; }
	public function setId_Usuario_Solicitante($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Usuario_Solicitante(): no se permiten valores nulos para el atributo 'id_usuario_solicitante'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Usuario_Solicitante(): el atributo 'id_usuario_solicitante' solo permite valores de tipo integer.", get_class($this)));
		$this->id_usuario_solicitante = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getObservaciones() { return $this->observaciones; }
	public function setObservaciones($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones(): el atributo 'observaciones' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	// Atributos de solo lectura
	public function getCodigo_Usuario_Firmante() { return $this->ro_codigo_usuario_firmante; }
	public function setCodigo_Usuario_Firmante($value) { /* atributo de solo lectura */ }

	public function getNombre_Usuario_Firmante() { return $this->ro_nombre_usuario_firmante; }
	public function setNombre_Usuario_Firmante($value) { /* atributo de solo lectura */ }

	public function getMail_Usuario_Firmante() { return $this->ro_mail_usuario_firmante; }
	public function setMail_Usuario_Firmante($value) { /* atributo de solo lectura */ }

	public function getCodigo_Usuario_Solicitante() { return $this->ro_codigo_usuario_solicitante; }
	public function setCodigo_Usuario_Solicitante($value) { /* atributo de solo lectura */ }

	public function getNombre_Usuario_Solicitante() { return $this->ro_nombre_usuario_solicitante; }
	public function setNombre_Usuario_Solicitante($value) { /* atributo de solo lectura */ }

	public function getMail_Usuario_Solicitante() { return $this->ro_mail_usuario_solicitante; }
	public function setMail_Usuario_Solicitante($value) { /* atributo de solo lectura */ }

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
	 * @param  integer (PK) id_pendiente
	 * @param  string giros_pendientes
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  integer id_usuario_firmante
	 * @param  integer id_usuario_solicitante
	 * @param  string observaciones
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$pid_pendiente = 0,
		$pgiros_pendientes = '',
		$pestado = 'pendiente',
		$pfecha_hora_entrada = 'CURRENT_TIMESTAMP',
		$pfecha_hora_salida = null,
		$pid_usuario_firmante = 0,
		$pid_usuario_solicitante = 0,
		$pobservaciones = null)
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
		$this->id_pendiente         = $pid_pendiente;
		$this->giros_pendientes     = $pgiros_pendientes;
		$this->estado               = $pestado;
		$this->fecha_hora_entrada   = ($pfecha_hora_entrada == 'CURRENT_TIMESTAMP') ? date('Y-m-d H:i:s') : $pfecha_hora_entrada;
		$this->fecha_hora_salida    = $pfecha_hora_salida;
		$this->id_usuario_firmante  = $pid_usuario_firmante;
		$this->id_usuario_solicitante = $pid_usuario_solicitante;
		$this->observaciones        = $pobservaciones;	
		
		// Atributos de solo lectura
		$this->ro_codigo_usuario_firmante = null;
		$this->ro_nombre_usuario_firmante = null;
		$this->ro_mail_usuario_firmante = null;
		$this->ro_codigo_usuario_solicitante = null;
		$this->ro_nombre_usuario_solicitante = null;
		$this->ro_mail_usuario_solicitante = null;

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}

	/**
	 * Devuelve la etiqueta de un expediente electrónico.
	 * @return string               Etiqueta
	 */
	public function obtenerEtiqueta()
	{
		$tipo_str = '';
		
		switch ($this->tipo) {
			case 'E':	$tipo_str = 'Expediente'; break;
			case 'N':	$tipo_str = 'Nota'; break;
			case 'R':	$tipo_str = 'Recomendación'; break;
		}

		return sprintf('%s %s-%s-%s cpo %s alc %s', 
        	$tipo_str,
        	$this->anio,
			$this->tipo,
			$this->numero,
			$this->cuerpo,
			$this->alcance
		);
	}	
}
?>