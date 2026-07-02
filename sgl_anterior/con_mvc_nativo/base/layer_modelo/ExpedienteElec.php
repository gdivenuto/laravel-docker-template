<?php
/**
 * Clase ExpedienteElec
 * 
 * Descripción: expe_expedientes_elec 
 * Layer Datos: DBExpedientesElec
 * Layer Negocio: NGExpedientesElec
 *
 * GenerateClass 0.97.7 beta @ 2022-09-16 09:33:42
 */
class ExpedienteElec extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio                ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo                ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero              ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo              ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance             ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $orden               ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo_actuacion      ; // PHP: string     MySQL: varchar(50)
	protected $detalle             ; // PHP: string     MySQL: varchar(200)
	protected $documento           ; // PHP: string     MySQL: varchar(512)
	protected $documento_hash      ; // PHP: string     MySQL: varchar(64)
	protected $texto_original      ; // PHP: string     MySQL: longtext [Permite NULL]
	protected $dec1404             ; // PHP: bool       MySQL: tinyint(1)
	protected $embebido            ; // PHP: bool       MySQL: tinyint(1)
	protected $es_caratula         ; // PHP: bool       MySQL: tinyint(1)
	protected $fecha_hora          ; // PHP: string     MySQL: datetime
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned
	protected $observaciones       ; // PHP: string     MySQL: longtext [Permite NULL]
	
	// Atributos de solo lectura
	protected $ro_codigo_usuario;  // Asociado a id_usuario
	protected $ro_nombre_usuario;  // Asociado a id_usuario
	protected $ro_total_firmas;    // Asociado a las firmas del documento
	protected $ro_cant_firmados;   // Asociado a las firmas del documento
	protected $ro_cant_pendientes; // Asociado a las firmas del documento
	protected $ro_cant_cancelados; // Asociado a las firmas del documento

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

	public function getTipo_Actuacion() { return $this->tipo_actuacion; }
	public function setTipo_Actuacion($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setTipo_Actuacion(): no se permiten valores nulos para el atributo 'tipo_actuacion'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setTipo_Actuacion(): el atributo 'tipo_actuacion' solo permite valores de tipo string.", get_class($this)));
		$this->tipo_actuacion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDetalle() { return $this->detalle; }
	public function setDetalle($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setDetalle(): no se permiten valores nulos para el atributo 'detalle'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDetalle(): el atributo 'detalle' solo permite valores de tipo string.", get_class($this)));
		$this->detalle = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDocumento() { return $this->documento; }
	public function setDocumento($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setDocumento(): no se permiten valores nulos para el atributo 'documento'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDocumento(): el atributo 'documento' solo permite valores de tipo string.", get_class($this)));
		$this->documento = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDocumento_Hash() { return $this->documento_hash; }
	public function setDocumento_Hash($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setDocumento_Hash(): no se permiten valores nulos para el atributo 'documento_hash'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDocumento_Hash(): el atributo 'documento_hash' solo permite valores de tipo string.", get_class($this)));
		$this->documento_hash = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getTexto_Original() { return $this->texto_original; }
	public function setTexto_Original($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setTexto_Original(): el atributo 'texto_original' solo permite valores de tipo string.", get_class($this)));
		$this->texto_original = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDec1404() { return $this->dec1404; }
	public function setDec1404($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setDec1404(): no se permiten valores nulos para el atributo 'dec1404'.", get_class($this)));
		if ( (!$this->esBoolean($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDec1404(): el atributo 'dec1404' solo permite valores de tipo boolean.", get_class($this)));
		$this->dec1404 = $this->obtenerBoolean($value);
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getEmbebido() { return $this->embebido; }
	public function setEmbebido($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setEmbebido(): no se permiten valores nulos para el atributo 'embebido'.", get_class($this)));
		if ( (!$this->esBoolean($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setEmbebido(): el atributo 'embebido' solo permite valores de tipo boolean.", get_class($this)));
		$this->embebido = $this->obtenerBoolean($value);
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getEs_Caratula() { return $this->es_caratula; }
	public function setEs_Caratula($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setEs_Caratula(): no se permiten valores nulos para el atributo 'es_caratula'.", get_class($this)));
		if ( (!$this->esBoolean($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setEs_Caratula(): el atributo 'es_caratula' solo permite valores de tipo boolean.", get_class($this)));
		$this->es_caratula = $this->obtenerBoolean($value);
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Hora() { return $this->fecha_hora; }
	public function setFecha_Hora($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setFecha_Hora(): no se permiten valores nulos para el atributo 'fecha_hora'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Hora(): el atributo 'fecha_hora' solo permite valores de tipo string.", get_class($this)));
		try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Hora(): el atributo 'fecha_hora' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_hora = $value;
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

	public function getObservaciones() { return $this->observaciones; }
	public function setObservaciones($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones(): el atributo 'observaciones' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	// Atributos de solo lectura
	public function getCodigo_Usuario() { return $this->ro_codigo_usuario; }
	public function setCodigo_Usuario($value) { /* atributo de solo lectura */ }

	public function getNombre_Usuario() { return $this->ro_nombre_usuario; }
	public function setNombre_Usuario($value) { /* atributo de solo lectura */ }

	public function getTotal_Firmas() { return $this->ro_total_firmas; }
	public function setTotal_Firmas($value) { /* atributo de solo lectura */ }

	public function getCant_Firmados() { return $this->ro_cant_firmados; }
	public function setCant_Firmados($value) { /* atributo de solo lectura */ }

	public function getCant_Pendientes() { return $this->ro_cant_pendientes; }
	public function setCant_Pendientes($value) { /* atributo de solo lectura */ }

	public function getCant_Cancelados() { return $this->ro_cant_cancelados; }
	public function setCant_Cancelados($value) { /* atributo de solo lectura */ }

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
	 * @param  string tipo_actuacion
	 * @param  string detalle
	 * @param  string documento
	 * @param  string documento_hash
	 * @param  string texto_original
	 * @param  bool dec1404
	 * @param  bool es_caratula
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string observaciones
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$porden = 0,
		$ptipo_actuacion = '',
		$pdetalle = '',
		$pdocumento = '',
		$pdocumento_hash = '',
		$ptexto_original = null,
		$pdec1404 = false,
		$pembebido = false,
		$pes_caratula = false,
		$pfecha_hora = 'CURRENT_TIMESTAMP',
		$pid_usuario = 0,
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
		$this->tipo_actuacion       = $ptipo_actuacion;
		$this->detalle              = $pdetalle;
		$this->documento            = $pdocumento;
		$this->documento_hash       = $pdocumento_hash;
		$this->texto_original       = $ptexto_original;
		$this->dec1404              = $pdec1404;
		$this->embebido             = $pembebido;
		$this->es_caratula          = $pes_caratula;
		$this->fecha_hora           = ($pfecha_hora == 'CURRENT_TIMESTAMP') ? date('Y-m-d H:i:s') : $pfecha_hora;
		$this->id_usuario           = $pid_usuario;
		$this->observaciones        = $pobservaciones;	
		
		// Atributos de solo lectura
		$this->ro_codigo_usuario = null;
		$this->ro_nombre_usuario = null;
		$this->ro_total_firmas = 0;
		$this->ro_cant_firmados = 0;
		$this->ro_cant_pendientes = 0;
		$this->ro_cant_cancelados = 0;

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}

	/**
	 * Devuelve la etiqueta de un documento del expediente electrónico.
	 * @param  boolean $pConDetalle Determina si se incluye el detalle en la etiqueta.
	 * @return string               Etiqueta
	 */
	public function obtenerEtiqueta($pConDetalle = false)
	{
		$tipo_str = '';
		
		switch ($this->tipo) {
			case 'E':	$tipo_str = 'Expediente'; break;
			case 'N':	$tipo_str = 'Nota'; break;
			case 'R':	$tipo_str = 'Recomendación'; break;
		}

		$detalle = ($pConDetalle)
			? sprintf(', detalle: %s', $this->detalle)
			: '';

		return sprintf('%s %s-%s-%s cpo %s alc %s, orden: %s%s', 
        	$tipo_str,
        	$this->anio,
			$this->tipo,
			$this->numero,
			$this->cuerpo,
			$this->alcance,
			$this->orden,
			$detalle
		);
	}

	/**
	 * Determina si todas las firmas han sido completadas (firmadas o rechazadas).
	 * @return [type] [description]
	 */
	public function sinFirmasPendientes()
	{
		return $this->total_firmas == ($this->cant_firmados + $this->cant_cancelados);
	}

	/**
	 * Determina si alguna de las firmas han sido rechazadas.
	 * @return [type] [description]
	 */
	public function hayFirmasCanceladas()
	{
		return $this->cant_cancelados > 0;
	}

}
?>