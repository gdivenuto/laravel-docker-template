<?php
/**
 * Clase FirmaExpedienteElec
 * 
 * Descripción: expe_firmas_expediente_elec 
 * Layer Datos: DBFirmasExpedienteElec
 * Layer Negocio: NGFirmasExpedienteElec
 *
 * GenerateClass 0.97.7 beta @ 2022-09-28 12:37:51
 */
class FirmaExpedienteElec extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio                ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo                ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero              ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo              ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance             ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $orden               ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $id_firma            ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned
	protected $id_usuario_solicitante; // PHP: integer    MySQL: int(10) unsigned
	protected $estado              ; // PHP: string     MySQL: enum('pendiente','firmado','cancelado')
	protected $fecha_hora_entrada  ; // PHP: string     MySQL: datetime
	protected $fecha_hora_salida   ; // PHP: string     MySQL: datetime [Permite NULL]
	protected $usuario_cargo       ; // PHP: string     MySQL: varchar(100)
	protected $usuario_dependencia ; // PHP: string     MySQL: varchar(100)
	protected $observaciones       ; // PHP: string     MySQL: longtext [Permite NULL]

	// Atributos de solo lectura
	protected $ro_detalle;        // Asociado a ExpedienteElec->detalle
	protected $ro_documento;      // Asociado a ExpedienteElec->documento
	protected $ro_embebido;       // Asociado a ExpedienteElec->embebido
	protected $ro_observaciones_ee; // Asociado a ExpedienteElec->observaciones
	protected $ro_codigo_usuario; // Asociado a id_usuario
	protected $ro_nombre_usuario; // Asociado a id_usuario
	protected $ro_mail_usuario;   // Asociado a id_usuario
	protected $ro_codigo_usuario_solicitante; // Asociado a id_usuario_solicitante
	protected $ro_nombre_usuario_solicitante; // Asociado a id_usuario_solicitante
	protected $ro_mail_usuario_solicitante;   // Asociado a id_usuario_solicitante
	protected $ro_cc_nombre;      // Asociado a id_usuario -> nombre de cargo
	protected $ro_ca_nombre;      // Asociado a id_usuario -> nombre de area
	protected $ro_dias_pendiente; // Cantidad de días donde la firma estuvo pendiente.

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

	public function getOrden() { return $this->orden; }
	public function setOrden($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setOrden(): no se permiten valores nulos para el atributo 'orden'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setOrden(): el atributo 'orden' solo permite valores de tipo integer.", get_class($this)));
		$this->orden = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getId_Firma() { return $this->id_firma; }
	public function setId_Firma($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Firma(): no se permiten valores nulos para el atributo 'id_firma'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Firma(): el atributo 'id_firma' solo permite valores de tipo integer.", get_class($this)));
		$this->id_firma = $value;
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

	public function getId_Usuario_Solicitante() { return $this->id_usuario_solicitante; }
	public function setId_Usuario_Solicitante($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Usuario_Solicitante(): no se permiten valores nulos para el atributo 'id_usuario_solicitante'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Usuario_Solicitante(): el atributo 'id_usuario_solicitante' solo permite valores de tipo integer.", get_class($this)));
		$this->id_usuario_solicitante = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getEstado() { return $this->estado; }
	public function setEstado($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setEstado(): no se permiten valores nulos para el atributo 'estado'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setEstado(): el atributo 'estado' solo permite valores de tipo string.", get_class($this)));
		if ( !in_array($value, ['pendiente','firmado','cancelado']) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setEstado(): el atributo 'estado' solo permite los valores 'pendiente', 'firmado' y 'cancelado'.", get_class($this)));
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

	public function getUsuario_Cargo() { return $this->usuario_cargo; }
	public function setUsuario_Cargo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setUsuario_Cargo(): no se permiten valores nulos para el atributo 'usuario_cargo'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setUsuario_Cargo(): el atributo 'usuario_cargo' solo permite valores de tipo string.", get_class($this)));
		$this->usuario_cargo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getUsuario_Dependencia() { return $this->usuario_dependencia; }
	public function setUsuario_Dependencia($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setUsuario_Dependencia(): no se permiten valores nulos para el atributo 'usuario_dependencia'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setUsuario_Dependencia(): el atributo 'usuario_dependencia' solo permite valores de tipo string.", get_class($this)));
		$this->usuario_dependencia = $value;
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
	public function getDetalle() { return $this->ro_detalle; }
	public function setDetalle($value) { /* atributo de solo lectura */ }

	public function getDocumento() { return $this->ro_documento; }
	public function setDocumento($value) { /* atributo de solo lectura */ }

	public function getEmbebido() { return $this->ro_embebido; }
	public function setEmbebido($value) { /* atributo de solo lectura */ }

	public function getObservaciones_Ee() { return $this->ro_observaciones_ee; }
	public function setObservaciones_Ee($value) { /* atributo de solo lectura */ }

	public function getCodigo_Usuario() { return $this->ro_codigo_usuario; }
	public function setCodigo_Usuario($value) { /* atributo de solo lectura */ }

	public function getNombre_Usuario() { return $this->ro_nombre_usuario; }
	public function setNombre_Usuario($value) { /* atributo de solo lectura */ }

	public function getMail_Usuario() { return $this->ro_mail_usuario; }
	public function setMail_Usuario($value) { /* atributo de solo lectura */ }

	public function getCodigo_Usuario_Solicitante() { return $this->ro_codigo_usuario_solicitante; }
	public function setCodigo_Usuario_Solicitante($value) { /* atributo de solo lectura */ }

	public function getNombre_Usuario_Solicitante() { return $this->ro_nombre_usuario_solicitante; }
	public function setNombre_Usuario_Solicitante($value) { /* atributo de solo lectura */ }

	public function getMail_Usuario_Solicitante() { return $this->ro_mail_usuario_solicitante; }
	public function setMail_Usuario_Solicitante($value) { /* atributo de solo lectura */ }

	public function getCc_Nombre() { return $this->ro_cc_nombre; }
	public function setCc_Nombre($value) { /* atributo de solo lectura */ }

	public function getCa_Nombre() { return $this->ro_ca_nombre; }
	public function setCa_Nombre($value) { /* atributo de solo lectura */ }

	public function getDias_Pendiente() { 
		return $this->calcularDiasPendiente();
	}
	public function setDias_Pendiente($value) { /* atributo de solo lectura */ }

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
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_firma
	 * @param  integer id_usuario
	 * @param  integer id_usuario_solicitante
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  string usuario_cargo
	 * @param  string usuario_dependencia
	 * @param  string observaciones
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$porden = 0,
		$pid_firma = 0,
		$pid_usuario = 0,
		$pid_usuario_solicitante = 0,
		$pestado = 'pendiente',
		$pfecha_hora_entrada = 'CURRENT_TIMESTAMP',
		$pfecha_hora_salida = null,
		$pusuario_cargo = '',
		$pusuario_dependencia = '',
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
		$this->orden                = $porden;
		$this->id_firma             = $pid_firma;
		$this->id_usuario           = $pid_usuario;
		$this->id_usuario_solicitante = $pid_usuario_solicitante;
		$this->estado               = $pestado;
		$this->fecha_hora_entrada   = ($pfecha_hora_entrada == 'CURRENT_TIMESTAMP') ? date('Y-m-d H:i:s') : $pfecha_hora_entrada;
		$this->fecha_hora_salida    = $pfecha_hora_salida;
		$this->usuario_cargo        = $pusuario_cargo;
		$this->usuario_dependencia  = $pusuario_dependencia;
		$this->observaciones        = $pobservaciones;	
		
		// Atributos de solo lectura
		$this->ro_detalle = null;
		$this->ro_documento = null;
		$this->ro_embebido = null;
		$this->ro_observaciones_ee = null;
		$this->ro_codigo_usuario = null;
		$this->ro_nombre_usuario = null;
		$this->ro_mail_usuario = null;
		$this->ro_codigo_usuario_solicitante = null;
		$this->ro_nombre_usuario_solicitante = null;
		$this->ro_mail_usuario_solicitante = null;
		$this->ro_cc_nombre = null;
		$this->ro_ca_nombre = null;
		$this->ro_dias_pendiente = null;

		// Actualizo la cantidad de días pendiente de firma
		$this->calcularDiasPendiente();

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}

	/**
	 * Se calculan los Dias Pendiente de Firma
	 * @return [type] [description]
	 */
	public function calcularDiasPendiente()
	{
		$fecha_hasta = ($this->estado == 'pendiente')
			? new DateTime()
			: new DateTime($this->fecha_hora_salida);

		$this->ro_dias_pendiente = (new DateTime($this->fecha_hora_entrada))
			->diff($fecha_hasta)
			->days;
		
		return $this->ro_dias_pendiente;
	}
}
?>