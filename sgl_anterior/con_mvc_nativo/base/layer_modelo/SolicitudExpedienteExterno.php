<?php
/**
 * Clase SolicitudExpedienteExterno
 * 
 * Descripción: expe_expedientes_externos 
 * Layer Datos: DBPrestamos
 * Layer Negocio: NGPrestamos
 *
 * GenerateClass 0.97.7 beta @ 2017-10-05 09:22:41
 */
class SolicitudExpedienteExterno extends ClaseBase {

	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	// Estados del cicuito de solicitudes de expediente externo.
	// Ver documentación en ./sistema_gestion_legislativa/documentacion/Modulo de Prestamos y Ubicacion/
	const E_SOLICITADO_HCD = "SHCD";		// Solicitado desde HCD
	const E_SOLICITADO_EE = "SEE";		 	// Solicitado al ente externo
	const E_INGRESADO_EE = "IEE";			// Ingresado desde el ente externo
	const E_DEVUELTO_EE = "DEE";			// Devuelto al ente externo
	const E_ANULADO_EE = "AEE";				// Solicitud anulada

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio                ; // (Primary Key) PHP: integer    MySQL: int(11)
	protected $tipo                ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero              ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo              ; // (Primary Key) PHP: integer    MySQL: int(11)
	protected $alcance             ; // (Primary Key) PHP: integer    MySQL: int(11)
	protected $digito              ; // (Primary Key) PHP: string     MySQL: char(2)
	protected $cuerpoalcance       ; // (Primary Key) PHP: integer    MySQL: int(11)
	protected $anexoalcance        ; // (Primary Key) PHP: integer    MySQL: int(11)
	protected $cuerpoanexoalcance  ; // (Primary Key) PHP: integer    MySQL: int(11)
	protected $anexo               ; // (Primary Key) PHP: integer    MySQL: int(11)
	protected $cuerpoanexo         ; // (Primary Key) PHP: integer    MySQL: int(11)
	protected $fecha_solicitud_hcd ; // (Primary Key) PHP: string     MySQL: datetime
	protected $fecha_solicitud_ee  ; // PHP: string     MySQL: datetime [Permite NULL]
	protected $fecha_ingresado_ee  ; // PHP: string     MySQL: datetime [Permite NULL]
	protected $fecha_devuelto_ee   ; // PHP: string     MySQL: datetime [Permite NULL]
	protected $fecha_anulado_ee    ; // PHP: string     MySQL: datetime [Permite NULL]
	protected $estado              ; // PHP: string     MySQL: char(10)
	protected $observaciones       ; // PHP: string     MySQL: text [Permite NULL]
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned
	protected $activo              ; // PHP: string     MySQL: char(1)

	// *** Atributos de solo lectura ***
	// Asociado al conjunto de estados siguientes posibles
	public $ro_estados_siguientes;
	// Valor true o false para indicar si dicha solicitud posee un préstamo pendiente de préstamo
	public $ro_existe_prestamo_pendiente;

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

	public function getDigito() { return $this->digito; }
	public function setDigito($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setDigito(): no se permiten valores nulos para el atributo 'digito'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDigito(): el atributo 'digito' solo permite valores de tipo string.", get_class($this)));
		$this->digito = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpoalcance() { return $this->cuerpoalcance; }
	public function setCuerpoalcance($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setCuerpoalcance(): no se permiten valores nulos para el atributo 'cuerpoalcance'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpoalcance(): el atributo 'cuerpoalcance' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpoalcance = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAnexoalcance() { return $this->anexoalcance; }
	public function setAnexoalcance($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAnexoalcance(): no se permiten valores nulos para el atributo 'anexoalcance'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAnexoalcance(): el atributo 'anexoalcance' solo permite valores de tipo integer.", get_class($this)));
		$this->anexoalcance = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpoanexoalcance() { return $this->cuerpoanexoalcance; }
	public function setCuerpoanexoalcance($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setCuerpoanexoalcance(): no se permiten valores nulos para el atributo 'cuerpoanexoalcance'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpoanexoalcance(): el atributo 'cuerpoanexoalcance' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpoanexoalcance = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAnexo() { return $this->anexo; }
	public function setAnexo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAnexo(): no se permiten valores nulos para el atributo 'anexo'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAnexo(): el atributo 'anexo' solo permite valores de tipo integer.", get_class($this)));
		$this->anexo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpoanexo() { return $this->cuerpoanexo; }
	public function setCuerpoanexo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setCuerpoanexo(): no se permiten valores nulos para el atributo 'cuerpoanexo'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpoanexo(): el atributo 'cuerpoanexo' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpoanexo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Solicitud_Hcd() { return $this->fecha_solicitud_hcd; }
	public function setFecha_Solicitud_Hcd($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setFecha_Solicitud_Hcd(): no se permiten valores nulos para el atributo 'fecha_solicitud_hcd'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Solicitud_Hcd(): el atributo 'fecha_solicitud_hcd' solo permite valores de tipo string.", get_class($this)));
		try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Solicitud_Hcd(): el atributo 'fecha_solicitud_hcd' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_solicitud_hcd = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Solicitud_Ee() { return $this->fecha_solicitud_ee; }
	public function setFecha_Solicitud_Ee($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Solicitud_Ee(): el atributo 'fecha_solicitud_ee' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Solicitud_Ee(): el atributo 'fecha_solicitud_ee' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_solicitud_ee = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Ingresado_Ee() { return $this->fecha_ingresado_ee; }
	public function setFecha_Ingresado_Ee($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Ingresado_Ee(): el atributo 'fecha_ingresado_ee' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Ingresado_Ee(): el atributo 'fecha_ingresado_ee' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_ingresado_ee = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Devuelto_Ee() { return $this->fecha_devuelto_ee; }
	public function setFecha_Devuelto_Ee($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Devuelto_Ee(): el atributo 'fecha_devuelto_ee' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Devuelto_Ee(): el atributo 'fecha_devuelto_ee' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_devuelto_ee = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Anulado_Ee() { return $this->fecha_anulado_ee; }
	public function setFecha_Anulado_Ee($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Anulado_Ee(): el atributo 'fecha_anulado_ee' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Anulado_Ee(): el atributo 'fecha_anulado_ee' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_anulado_ee = $value;
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

	public function getObservaciones() { return $this->observaciones; }
	public function setObservaciones($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones(): el atributo 'observaciones' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones = $value;
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

	public function getActivo() { return $this->activo; }
	public function setActivo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setActivo(): no se permiten valores nulos para el atributo 'activo'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setActivo(): el atributo 'activo' solo permite valores de tipo string.", get_class($this)));
		$this->activo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	// Atributos de solo lectura
	public function getEstados_Siguientes() { return $this->ro_estados_siguientes; }
	public function setEstados_Siguientes($value) { /* atributo de solo lectura */ }

	public function getExistePrestamoPendiente() { return $this->ro_existe_prestamo_pendiente; }
	public function setExistePrestamoPendiente($value) { /* atributo de solo lectura */ }
	
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
	 * @param  string (PK) digito
	 * @param  integer (PK) cuerpoalcance
	 * @param  integer (PK) anexoalcance
	 * @param  integer (PK) cuerpoanexoalcance
	 * @param  integer (PK) anexo
	 * @param  integer (PK) cuerpoanexo
	 * @param  string (PK) fecha_solicitud_hcd
	 * @param  string fecha_solicitud_ee
	 * @param  string fecha_ingresado_ee
	 * @param  string fecha_devuelto_ee
	 * @param  string fecha_anulado_ee
	 * @param  string estado
	 * @param  string observaciones
	 * @param  integer id_usuario
	 * @param  string activo
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$pdigito = '',
		$pcuerpoalcance = 0,
		$panexoalcance = 0,
		$pcuerpoanexoalcance = 0,
		$panexo = 0,
		$pcuerpoanexo = 0,
		$pfecha_solicitud_hcd = '',
		$pfecha_solicitud_ee = null,
		$pfecha_ingresado_ee = null,
		$pfecha_devuelto_ee = null,
		$pfecha_anulado_ee = null,
		$pestado = self::E_SOLICITADO_HCD,
		$pobservaciones = null,
		$pid_usuario = 0,
		$pactivo = '1')
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
		$this->digito               = $pdigito;
		$this->cuerpoalcance        = $pcuerpoalcance;
		$this->anexoalcance         = $panexoalcance;
		$this->cuerpoanexoalcance   = $pcuerpoanexoalcance;
		$this->anexo                = $panexo;
		$this->cuerpoanexo          = $pcuerpoanexo;
		$this->fecha_solicitud_hcd  = $pfecha_solicitud_hcd;
		$this->fecha_solicitud_ee   = $pfecha_solicitud_ee;
		$this->fecha_ingresado_ee   = $pfecha_ingresado_ee;
		$this->fecha_devuelto_ee    = $pfecha_devuelto_ee;
		$this->fecha_anulado_ee     = $pfecha_anulado_ee;
		$this->estado               = $pestado;
		$this->observaciones        = $pobservaciones;
		$this->id_usuario           = $pid_usuario;
		$this->activo               = $pactivo;	
		
		// Atributos de solo lectura
		$this->ro_estados_siguientes = null;
		$this->ro_existe_prestamo_pendiente = null;
		
		// Invocación de inicialización de clase padre.
		parent::__construct();
	}

	/**
	 * Obtiene la fecha_solicitud_hcd como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function get_fecha_solicitud_hcd_AsDateTime()
	{
		return $this->verificarDateTimeDesdeString($this->fecha_solicitud_hcd);
	} 
	
	/**
	 * Obtiene la fecha_solicitud_ee como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function get_fecha_solicitud_ee_AsDateTime()
	{
		return $this->verificarDateTimeDesdeString($this->fecha_solicitud_ee);
	}
	
	/**
	 * Obtiene la fecha_ingresado_ee como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function get_fecha_ingresado_ee_AsDateTime()
	{
		return $this->verificarDateTimeDesdeString($this->fecha_ingresado_ee);
	} 
	
	/**
	 * Obtiene la fecha_devuelto_ee como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function get_fecha_devuelto_ee_AsDateTime()
	{
		return $this->verificarDateTimeDesdeString($this->fecha_devuelto_ee);
	} 
	
	/**
	 * Obtiene la fecha_anulado_ee como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function get_fecha_anulado_ee_AsDateTime()
	{
		return $this->verificarDateTimeDesdeString($this->fecha_anulado_ee);
	} 
	
	/**
	 * Asigna un valor a la fecha de solicitud del hcd a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function set_fecha_solicitud_hcd_FromDateTime(DateTime $fecha)
	{
		$this->fecha_solicitud_hcd = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	} 
	
	/**
	 * Asigna un valor a la fecha de solicitud de expediente externo a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function set_fecha_solicitud_ee_FromDateTime(DateTime $fecha)
	{
		$this->fecha_solicitud_ee = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	} 
	
	/**
	 * Asigna un valor a la fecha de ingresado a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function set_fecha_ingresado_ee_FromDateTime(DateTime $fecha)
	{
		$this->fecha_ingresado_ee = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}
	
	/**
	 * Asigna un valor a la fecha devuelto a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function set_fecha_devuelto_ee_FromDateTime(DateTime $fecha)
	{
		$this->fecha_devuelto_ee = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}
	
	/**
	 * Asigna un valor a la fecha anulado a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function set_fecha_anulado_ee_FromDateTime(DateTime $fecha)
	{
		$this->fecha_anulado_ee = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}
	
	/**
	 * Obtiene de un estado su correspondiente descripción.
	 * @param string $estado
	 * @return string Descripción del estado.
	 * @throws InvalidArgumentException
	 */
	public function estadoToString()
	{
		$cadena = "";
	
		switch ($this->estado)
		{
			case self::E_SOLICITADO_HCD : // Solicitado por el HCD
				$cadena = "Solicitado por el HCD";
				break;
					
			case self::E_SOLICITADO_EE : // Solicitado al Ente Externo
				$cadena = "Solicitado al ente externo";
				break;
		
			case self::E_INGRESADO_EE : // Ingresado desde el Ente Externo
				$cadena = "Ingresado al HCD desde ente externo";
				break;
					
			case self::E_DEVUELTO_EE : // Devuelto al Ente Externo
				$cadena = "Devuelto al ente externo";
				break;
	
			case self::E_ANULADO_EE : // Solicitud anulada
				$cadena = "Solicitud anulada";
				break;
					
			default:
				throw new InvalidArgumentException("El estado es inválido. Estado: ".$this->estado);
		}
		return $cadena;
	}
	
	/**
	 * Dado el estado de la solicitud, devuelve la fecha asociada. Es posible que la fecha sea null.
	 * @param string $estado Estado del cual se desea obtener la fecha.
	 * @return string Fecha del estado. Puede ser null.
	 * @throws InvalidArgumentException
	 */
	public function obtenerFechaSegunEstado($estado)
	{
		$fecha = null;
		
		switch ($estado)
		{
			case self::E_SOLICITADO_HCD : // Solicitado por el HCD
				$fecha = $this->fecha_solicitud_hcd;
				break;
			
			case self::E_SOLICITADO_EE : // Solicitado al Ente Externo
				$fecha = $this->fecha_solicitud_ee;
				break;
			
			case self::E_INGRESADO_EE : // Ingresado desde el Ente Externo
				$fecha = $this->fecha_ingresado_ee;
				break;
					
			case self::E_DEVUELTO_EE : // Devuelto al Ente Externo
				$fecha = $this->fecha_devuelto_ee;
				break;
				
			case self::E_ANULADO_EE : // Solicitud anulada
				$fecha = $this->fecha_anulado_ee;
				break;
					
			default:
				throw new InvalidArgumentException("El estado de la solicitud es inválido. Estado: ".$estado);
		}
		
		return $fecha;
	}
	
	/**
	 * Dado el estado de la solicitud, devuelve la fecha asociada. Es posible que la fecha sea null.
	 * @param string $estado Estado del cual se desea obtener la fecha.
	 * @return DateTime Fecha del estado. Puede ser null.
	 * @throws InvalidArgumentException
	 */
	public function obtenerFechaSegunEstadoAsDateTime($estado)
	{
		$fecha = $this->obtenerFechaSegunEstado($estado);
		
		return $this->verificarDateTimeDesdeString($fecha);
	}
	
	/**
	 * Devuelve la fecha actual de la solicitud (la fecha del estado actual de la solicitud).
	 * @return string La fecha del estado actual de la solicitud.
	 * @throws InvalidArgumentException
	 */
	public function obtenerFechaActual()
	{
		return $this->obtenerFechaSegunEstado($this->estado);
	}
	
	/**
	 * Devuelve la fecha actual de la solicitud (la fecha del estado actual de la solicitud).
	 * @return DateTime La fecha del estado actual de la solicitud.
	 * @throws InvalidArgumentException
	 */
	public function obtenerFechaActualAsDateTime()
	{
		return $this->obtenerFechaSegunEstadoAsDateTime($this->estado);
	}
	
	/**
	 * Este método devuelve el identificador de la solicitud formateada como una cadena.
	 * @return string
	 */
	public function toStringDescription()
	{
		return	$this->anio."-".$this->tipo."-".$this->numero." ".
				$this->cuerpo."-".$this->alcance ." ".
				$this->digito ."-".$this->cuerpoalcance."-".$this->anexoalcance."-".$this->cuerpoanexoalcance ."-".$this->anexo."-".$this->cuerpoanexo;
	}
	
	/**
	 * Este método devuelve una cadena con la observación recortada. Recorta la observación y anexa puntos suspensivos al final 
	 * si la descripción es mayor al largo.
	 * @param number $largo Cantidad de caracteres donde cortar.
	 * @return string Observación resumida.
	 */
	public function obtenerResumenObservacion($largo = 20)
	{
		$salida = (is_null($this->observaciones)) ? '' : $this->observaciones; 
		if (strlen($salida) > $largo)
			$salida = substr($salida, 0, $largo) . "...";
		return $salida;
	}
	
}
?>