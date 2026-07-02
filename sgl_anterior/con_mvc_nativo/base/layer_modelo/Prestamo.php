<?php
/**
 * Clase Prestamo
 * 
 * Descripción: expe_prestamos 
 * Layer Datos: DBPrestamos
 * Layer Negocio: NGPrestamos
 *
 * GenerateClass 0.97.7 beta @ 2017-10-05 09:13:43
 */
class Prestamo extends ClaseBase {

	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	// Estados del cicuito de prestamos.
	// Ver documentación en ./sistema_gestion_legislativa/documentacion/Modulo de Prestamos y Ubicacion/
	const E_SOLICITADO = "S";		 	// Solicitado al HCD
	const E_PRESTADO = "P";				// Prestado desde el HCD
	const E_DEVUELTO = "D";				// Devuelto al HCD
	const E_ANULADO = "A";				// Prestamo anulado

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio                ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo                ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero              ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo              ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance             ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $digito              ; // (Primary Key) PHP: string     MySQL: char(2)
	protected $cuerpoalcance       ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $anexoalcance        ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $cuerpoanexoalcance  ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $anexo               ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $cuerpoanexo         ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $fecha_solicitud     ; // (Primary Key) PHP: string     MySQL: datetime
	protected $fecha_prestado      ; // PHP: string     MySQL: datetime [Permite NULL]
	protected $fecha_devuelto      ; // PHP: string     MySQL: datetime [Permite NULL]
	protected $fecha_anulado       ; // PHP: string     MySQL: datetime [Permite NULL]
	protected $solicitante_tipo    ; // PHP: string     MySQL: varchar(3) [Permite NULL]
	protected $solicitante_codigo  ; // PHP: string     MySQL: varchar(10) [Permite NULL]
	protected $libro_numero        ; // PHP: float      MySQL: decimal(5,0) [Permite NULL]
	protected $libro_folio         ; // PHP: float      MySQL: decimal(5,0) [Permite NULL]
	protected $estado              ; // PHP: string     MySQL: varchar(45) [Permite NULL]
	protected $observaciones_prestamo; // PHP: string     MySQL: text [Permite NULL]
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned
	protected $activo              ; // PHP: string     MySQL: char(1)

	// Atributos de solo lectura
	public $ro_solicitante_nombre;// Asociado a $solicitante_tipo y a $solicitante_codigo
	public $ro_estados_siguientes;// Asociado al conjunto de estados siguientes posibles
	
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

	public function getFecha_Solicitud() { return $this->fecha_solicitud; }
	public function setFecha_Solicitud($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setFecha_Solicitud(): no se permiten valores nulos para el atributo 'fecha_solicitud'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Solicitud(): el atributo 'fecha_solicitud' solo permite valores de tipo string.", get_class($this)));
		try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Solicitud(): el atributo 'fecha_solicitud' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_solicitud = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Prestado() { return $this->fecha_prestado; }
	public function setFecha_Prestado($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Prestado(): el atributo 'fecha_prestado' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Prestado(): el atributo 'fecha_prestado' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_prestado = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Devuelto() { return $this->fecha_devuelto; }
	public function setFecha_Devuelto($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Devuelto(): el atributo 'fecha_devuelto' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Devuelto(): el atributo 'fecha_devuelto' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_devuelto = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Anulado() { return $this->fecha_anulado; }
	public function setFecha_Anulado($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Anulado(): el atributo 'fecha_anulado' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Anulado(): el atributo 'fecha_anulado' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_anulado = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getSolicitante_Tipo() { return $this->solicitante_tipo; }
	public function setSolicitante_Tipo($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setSolicitante_Tipo(): el atributo 'solicitante_tipo' solo permite valores de tipo string.", get_class($this)));
		$this->solicitante_tipo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getSolicitante_Codigo() { return $this->solicitante_codigo; }
	public function setSolicitante_Codigo($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setSolicitante_Codigo(): el atributo 'solicitante_codigo' solo permite valores de tipo string.", get_class($this)));
		$this->solicitante_codigo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getLibro_Numero() { return $this->libro_numero; }
	public function setLibro_Numero($value) { 
		if ( (!is_null($value)) && (!($this->esFloat($value) || $this->esInteger($value))) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setLibro_Numero(): el atributo 'libro_numero' solo permite valores de tipo float o double.", get_class($this)));
		$this->libro_numero = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getLibro_Folio() { return $this->libro_folio; }
	public function setLibro_Folio($value) { 
		if ( (!is_null($value)) && (!($this->esFloat($value) || $this->esInteger($value))) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setLibro_Folio(): el atributo 'libro_folio' solo permite valores de tipo float o double.", get_class($this)));
		$this->libro_folio = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getEstado() { return $this->estado; }
	public function setEstado($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setEstado(): el atributo 'estado' solo permite valores de tipo string.", get_class($this)));
		$this->estado = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getObservaciones_Prestamo() { return $this->observaciones_prestamo; }
	public function setObservaciones_Prestamo($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones_Prestamo(): el atributo 'observaciones_prestamo' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones_prestamo = $value;
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
	public function getSolicitante_Nombre() { return $this->ro_solicitante_nombre; }
	public function setSolicitante_Nombre($value) { /* atributo de solo lectura */ }

	public function getEstados_Siguientes() { return $this->ro_estados_siguientes; }
	public function setEstados_Siguientes($value) { /* atributo de solo lectura */ }

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
	 * @param  string (PK) fecha_solicitud
	 * @param  string fecha_prestado
	 * @param  string fecha_devuelto
	 * @param  string fecha_anulado
	 * @param  string solicitante_tipo
	 * @param  string solicitante_codigo
	 * @param  float libro_numero
	 * @param  float libro_folio
	 * @param  string estado
	 * @param  string observaciones_prestamo
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
		$pfecha_solicitud = '',
		$pfecha_prestado = null,
		$pfecha_devuelto = null,
		$pfecha_anulado = null,
		$psolicitante_tipo = null,
		$psolicitante_codigo = null,
		$plibro_numero = null,
		$plibro_folio = null,
		$pestado = self::E_SOLICITADO,
		$pobservaciones_prestamo = null,
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
		$this->fecha_solicitud      = $pfecha_solicitud;
		$this->fecha_prestado       = $pfecha_prestado;
		$this->fecha_devuelto       = $pfecha_devuelto;
		$this->fecha_anulado        = $pfecha_anulado;
		$this->solicitante_tipo     = $psolicitante_tipo;
		$this->solicitante_codigo   = $psolicitante_codigo;
		$this->libro_numero         = $plibro_numero;
		$this->libro_folio          = $plibro_folio;
		$this->estado               = $pestado;
		$this->observaciones_prestamo = $pobservaciones_prestamo;
		$this->id_usuario           = $pid_usuario;
		$this->activo               = $pactivo;	

		// Atributos de solo lectura
		$this->ro_solicitante_nombre = null;
		$this->ro_estados_siguientes = null;
		
		// Invocación de inicialización de clase padre.
		parent::__construct();
	}


	/**
	 * Obtiene la fecha_solicitud como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function get_fecha_solicitud_AsDateTime()
	{
		return $this->verificarDateTimeDesdeString($this->fecha_solicitud);
	} 
	
	/**
	 * Obtiene la fecha_prestado como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function get_fecha_prestado_AsDateTime()
	{
		return $this->verificarDateTimeDesdeString($this->fecha_prestado);
	} 
	
	/**
	 * Obtiene la fecha_devuelto como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function get_fecha_devuelto_AsDateTime()
	{
		return $this->verificarDateTimeDesdeString($this->fecha_devuelto);
	} 
	
	/**
	 * Obtiene la fecha_anulado como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function get_fecha_anulado_AsDateTime()
	{
		return $this->verificarDateTimeDesdeString($this->fecha_anulado);
	} 
	
	/**
	 * Asigna un valor a la fecha de solicitud a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function set_fecha_solicitud_FromDateTime(DateTime $fecha)
	{
		$this->fecha_solicitud = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	} 
	
	/**
	 * Asigna un valor a la fecha prestado a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function set_fecha_prestado_FromDateTime(DateTime $fecha)
	{
		$this->fecha_prestado = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}
	
	/**
	 * Asigna un valor a la fecha devuelto a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function set_fecha_devuelto_FromDateTime(DateTime $fecha)
	{
		$this->fecha_devuelto = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}
	
	/**
	 * Asigna un valor a la fecha anulado a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function set_fecha_anulado_FromDateTime(DateTime $fecha)
	{
		$this->fecha_anulado = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
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
			case self::E_SOLICITADO : // Solicitado al HCD
				$cadena = "Solicitado al HCD";
				break;
					
			case self::E_PRESTADO : // Prestado desde el HCD
				$cadena = "Prestado desde el HCD";
				break;
					
			case self::E_DEVUELTO : // Devuelto al HCD
				$cadena = "Devuelto al HCD";
				break;
	
			case self::E_ANULADO : // Prestamo anulado
				$cadena = "Préstamo anulado";
				break;
					
			default:
				throw new InvalidArgumentException("El estado es inválido. Estado: ".$this->estado);
		}
		return $cadena;
	}
	
	/**
	 * Dado el estado del préstamo, devuelve la fecha asociada. Es posible que la fecha sea null.
	 * @param string $estado Estado del cual se desea obtener la fecha.
	 * @return string Fecha del estado. Puede ser null.
	 * @throws InvalidArgumentException
	 */
	public function obtenerFechaSegunEstado($estado)
	{
		$fecha = null;
		
		switch ($estado)
		{
			case self::E_SOLICITADO : // Solicitado al HCD
				$fecha = $this->fecha_solicitud;
				break;
			
			case self::E_PRESTADO : // Prestado desde el HCD
				$fecha = $this->fecha_prestado;
				break;
					
			case self::E_DEVUELTO : // Devuelto al HCD
				$fecha = $this->fecha_devuelto;
				break;
				
			case self::E_ANULADO : // Prestamo anulado
				$fecha = $this->fecha_anulado;
				break;
					
			default:
				throw new InvalidArgumentException("El estado del préstamo es inválido. Estado: ".$estado);
		}
		
		return $fecha;
	}
	
	/**
	 * Dado el estado del préstamo, devuelve la fecha asociada. Es posible que la fecha sea null.
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
	 * Devuelve la fecha actual del préstamo (la fecha del estado actual del préstamo).
	 * @return string La fecha del estado actual del préstamo.
	 * @throws InvalidArgumentException
	 */
	public function obtenerFechaActual()
	{
		return $this->obtenerFechaSegunEstado($this->estado);
	}
	
	/**
	 * Devuelve la fecha actual del préstamo (la fecha del estado actual del préstamo).
	 * @return DateTime La fecha del estado actual del préstamo.
	 * @throws InvalidArgumentException
	 */
	public function obtenerFechaActualAsDateTime()
	{
		return $this->obtenerFechaSegunEstadoAsDateTime($this->estado);
	}
	
	/**
	 * Este método devuelve el identificador del prestamo formateado como una cadena.
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
		$salida = (is_null($this->observaciones_prestamo)) ? '' : $this->observaciones_prestamo; 
		if (strlen($salida) > $largo)
			$salida = substr($salida, 0, $largo) . "...";
		return $salida;
	}
}
?>